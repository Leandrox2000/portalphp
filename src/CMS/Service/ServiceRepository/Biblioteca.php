<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Biblioteca as BibliotecaEntity;
use Entity\RedeSocialBiblioteca as RedeSocialBibliotecaEntity;
use Helpers\Session;

/**
 * Description of Biblioteca
 *
 */
class Biblioteca extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Biblioteca $entity
     */
    public function __construct(EntityManager $em, BibliotecaEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    public function getDadosSolr($entity)
    {
        return array(
            'entity_name'       => $this->getNameEntity(),
            'entity_id'         => $entity->getId(),
            'title'             => $entity->getNome(),
            'description'       => $entity->getDescricao(),
            'publish'           => $entity->getPublicado(),
            'publish_date'      => $entity->getDataInicial(),
            'unpublish_date'    => $entity->getDataFinal(),
            'url'               => \Helpers\Url::generateRoute('bibliotecasIphan'),
        );
    }

    /**
     *
     * @param array $dados
     */
    public function save(array $dados)
    {
        $success = "";
        $error = array();
        $response = 0;

        try {
            $action = !empty($dados['id']) ? "alterado" : "inserido";
            $redeSocial = $dados['redeSocial'];
            $url = $dados['url'];

            unset($dados['redeSocial']);
            unset($dados['url']);

            //Inicia a transação
            $this->getEm()->beginTransaction();

            //Salva o registro
            $entity = parent::save($dados);
            $this->salvaRedesSociais($redeSocial, $url, $entity);

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);

            //Commita a transação
            $this->getEm()->commit();

            $success = "Registro {$action} com sucesso!";
            $response = 1;
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $error[] = $ex->getMessage();
        }

        return array("success" => $success, "error" => $error, "response" => $response);
    }

    private function salvaRedesSociais(array $redeSocial, array $url, $biblioteca, $update = FALSE)
    {
        // Se é atualização
        if ($biblioteca->getId() > 0) {
            $redes = $this->getEm()
                    ->createQuery("SELECT rsb FROM Entity\RedeSocialBiblioteca rsb JOIN rsb.biblioteca b WHERE b.id = {$biblioteca->getId()} ")
                    ->getResult();

            // Apaga todas as redes sociais
            foreach ($redes as $red) {
                $entity = $this->getEm()->getReference('Entity\RedeSocialBiblioteca', $red->getId());
                $this->getEm()->remove($entity);
                $this->getEm()->flush();
            }
        }

        // Verifica se possuem redes sociais
        if (count($redeSocial) > 0 && count($url) > 0) {
            // Percorre as redes sociais
            foreach ($redeSocial as $key => $rede) {
                // Se algum dos campos não estiver preenchido ignora o índice
                if (empty($url[$key]) || empty($rede)) {
                    continue;
                }

                $entity = new RedeSocialBibliotecaEntity();
                $entity->setRedeSocial($rede);
                $entity->setUrl($url[$key]);
                $entity->setBiblioteca($biblioteca);

                // Faz a persistência
                $this->getEm()->persist($entity);
                $this->getEm()->flush();
            }
        }
    }

    /**
     *
     * @param array $ids
     * @return array
     */
    public function delete(array $ids)
    {
        $success = "Ação executada com sucesso";
        $error = array();
        $response = 0;

        try {
            //Inicia a transação
            $this->getEm()->beginTransaction();

            //Busca todos as redes sociais relacionadas
            $idsRedes = $this->getEm()->getRepository('Entity\RedeSocialBiblioteca')->getIdRedesSociaisRelacionadas($ids);

            if (!empty($idsRedes)) {
                //Deleta as redes sociais relacionadas
                $this->getEm()->createQuery("DELETE FROM Entity\RedeSocialBiblioteca r WHERE r.id IN(" . implode(',', $idsRedes) . ") ")->execute();
            }

            //Deleta
            parent::delete($ids);

            /* Remove no índice do Solr */
            $this->getSolrManager()->bulkDelete('Entity\Biblioteca', $ids);

            //Commita
            $this->getEm()->commit();
            $response = 1;
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $this->getLogger()->error($ex->getMessage());
            $error[] = "Erro ao excluir registros! ";
        }

        return array("success" => $success, "error" => $error, "response" => $response);
    }
    
    
    public function updateOrdem(array $dados){
        try{
            $this->getEm()->beginTransaction();
            
            foreach($dados as $id => $ordem){
                $this->getEm()->createQuery("UPDATE Entity\Biblioteca b SET b.ordem = {$ordem} WHERE b.id = {$id} ")->execute();
            }
            
            $this->getEm()->commit();
            $success = "Ação executada com sucesso";
            $response = 1;
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $this->getLogger()->error($ex->getMessage());
            $error[] = "Erro ao atualizar registro";
        }
        
        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
    
    

}
