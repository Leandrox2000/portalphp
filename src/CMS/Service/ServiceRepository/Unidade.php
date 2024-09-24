<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager as EntityManager;
use Entity\Unidade as UnidadeEntity;
use Helpers\Session;

/**
 * Classe Unidade
 * 
 * Responsável pelas ações na entidade Unidade
 * @author join-ti
 */
class Unidade extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param UnidadeEntity $entity
     */
    public function __construct(EntityManager $em, UnidadeEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     * 
     * @param array $dados
     * @return array
     */
    public function save($dados)
    {
        $error = array();
        $success = "";
        $response = 0;

        try {

            parent::save($dados);
            $action = empty($dados['id']) ? "inserido" : "alterado";
            $success = "Registro $action com sucesso!";
            $response = 1;
        } catch (\Exception $e) {
            $error[] = $e->getMessage();
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     * Metodo delete
     * 
     * Deleta um grupo de registros
     * @param array $ids
     * @return boolean
     */
    public function delete(array $ids)
    {
        $error = array();
        $response = 0;
        $success = "Ação executada com sucesso";

        try {

            //Inicia a transação
            $this->getEm()->beginTransaction();
            
            $funcionarioRep = $this->getEm()->getRepository('Entity\Funcionario');            
            if($funcionarioRep->verificaVinculoByRelation($ids, 'unidade')) {
                throw new \Exception('Não foi possível excluir o(s) registro(s) selecionado(s), pois existem funcionários vinculados.');
            }

            foreach ($ids as $id) {
                $entity = $this->getEm()->getReference("Entity\Unidade", $id);
                $this->getEm()->remove($entity);
                $this->getEm()->flush();
            }

            $this->getEm()->commit();
            $response = 1;
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            $this->getLogger()->error($e->getMessage());
            $error[] = $e->getMessage();
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
    
    
    
     public function updateOrdem(array $dados){
        try{
            $this->getEm()->beginTransaction();
            
            foreach($dados as $id => $ordem){
                $this->getEm()->createQuery("UPDATE Entity\Unidade p SET p.ordem = {$ordem} WHERE p.id = {$id} ")->execute();
                /*$entity = $this->getEm()->find($this->getNameEntity(), $id);
                $entity->setOrdem($ordem);
                $this->getEm()->persist($entity);*/
               //$entity = parent::save(array('id' =>$id ,'ordem' => $ordem));
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
