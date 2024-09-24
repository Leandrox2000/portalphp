<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Pergunta as PerguntaEntity;
use Helpers\Session;

/**
 * PerguntaService
 *
 * @author join-ti
 */
class Pergunta extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Pergunta $entity
     */
    public function __construct(EntityManager $em, PerguntaEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    public function getDadosSolr($entity)
    {
        return array(
            'entity_name' => $this->getNameEntity(),
            'entity_id' => $entity->getId(),
            'title' => $entity->getPergunta(),
            'description' => $entity->getResposta(),
            'publish' => $entity->getPublicado(),
            'publish_date' => $entity->getDataInicial(),
            'unpublish_date' => $entity->getDataFinal(),
            'url' => \Helpers\Url::generateRoute('perguntasFrequentes'),
        );
    }

    /**
     *
     * @param array $dados
     * @return array
     */
    public function save(array $dados)
    {
        $response = 0;
        $error = array();
        $success = "";

        try {
            $action = empty($dados['id']) ? "inserido" : "alterado";
            $entity = parent::save($dados);

            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);

            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $action = empty($dados['id']) ? "inserir" : "alterar";
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Erro ao {$action} registro";
        }


        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     *
     * @param array $ids
     * @return array
     */
    public function delete(array $ids)
    {
        $response = 0;
        $error = array();
        $success = "";

        try {
            parent::delete($ids, "id");

            $success = "Ação executada com sucesso";
            $response = 1;
            $this->getLogger()->info("Perguntas " . implode(",", $ids) . " foram excluidas.");
        } catch (\Exception $exc) {
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    
     public function updateOrdem(array $dados){
        try{
            $this->getEm()->beginTransaction();
            
            foreach($dados as $id => $ordem){
                $this->getEm()->createQuery("UPDATE Entity\Pergunta p SET p.ordem = {$ordem} WHERE p.id = {$id} ")->execute();
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
