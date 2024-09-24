<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager as EntityManager;
use Entity\Fototeca as FototecaEntity;
use Helpers\Param;
use Helpers\Session;

/**
 * Classe Fototeca
 *
 * Responsável pelas ações na entidade Fototeca
 * @author join-ti
 */
class Fototeca extends BaseService implements SolrAwareInterface
{

    public function __construct(EntityManager $em, FototecaEntity $entity, Session $session)
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
            'url'               => \Helpers\Url::generateRoute('fototeca', 'detalhes', $entity),
        );
    }

    /**
     *
     * @param array $dados
     * @return array
     */
    public function save($dados, $ordem = null)
    {
        $error = array();
        $success = "";
        $response = 0;

        try {
            //Busca as galerias
            $arrayGalerias = explode(",", $dados['galerias']);
            $galerias = new \Doctrine\Common\Collections\ArrayCollection();

            foreach ($arrayGalerias as $galeria) {
                $reference = $this->getEm()->getReference('Entity\Galeria', $galeria);
                $galerias->add($reference);
            }
            
            if (!empty($dados['fototecasFilhas'])) {
                $relacionados = explode(',', $dados['fototecasFilhas']);
            }
            unset($dados['fototecasFilhas']);

            $dados['galerias'] = $galerias;
            $entity = parent::save($dados);
        
            $arrayOrdem = explode(",", $ordem);
            $i = 1;
            foreach($arrayOrdem as $id){
            	$this->getEm()->getRepository('Entity\Galeria')->setOrdemFototeca($entity->getId(),$id, $i);
            	$i++;
            }
            
            if (!empty($relacionados)) {
                $this->addFototecasRelacionadas($entity, $relacionados);
            } else {
                $entity->getFototecasFilhas()->clear();
            }

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);
            
            $this->getEm()->persist($entity);
            $this->getEm()->flush();

            $action = !empty($dados['id']) ? "alterado" : "inserido";
            $success = "Registro $action com sucesso!";
            $response = 1;
        } catch (\Exception $ex) {
            $error[] = $ex->getMessage();
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     * Metodo delete
     *
     * Deleta um grupo de registros e seus respectivos arquivos
     * @param array $ids
     * @return boolean
     */
    public function delete(array $ids)
    {
        $response = 0;
        $error = array();
        $success = "";

        try {
            $this->getEm()->beginTransaction();

            foreach ($ids as $id) {
                $fototeca = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($fototeca);
                $this->getEm()->flush();
            }
            $this->getEm()->commit();
            $success = "Ação executada com sucesso";
            $response = 1;
            $this->getLogger()->info("Fototecas " . implode(",", $ids) . " foram excluidas.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
    
    private function addFototecasRelacionadas($entity, $relacionados)
    {
        // Limpa a collection
        $entity->getFototecasFilhas()->clear();

        // Para cada ID de vídeo relacionado
        foreach ($relacionados as $relacionado) {
            // Pega a referência
            $relacionado = $this->getEm()->getReference('Entity\Fototeca', $relacionado);

            // Adiciona à collection
            $entity->getFototecasFilhas()->add($relacionado);
        }
    }


}
