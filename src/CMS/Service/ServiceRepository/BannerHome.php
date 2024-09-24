<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager as EntityManager;
use Entity\BannerHome as BannerHomeEntity;
use Helpers\Session;

/**
 * Classe BannerHome
 * 
 * Responsável pelas ações na entidade BannerHome
 * @author join-ti
 */
class BannerHome extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\BannerHome $entity
     */
    public function __construct(EntityManager $em, BannerHomeEntity $entity, Session $session)
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
    public function save(array $dados)
    {
        $error = array();
        $response = 0;
        $success = "";

        try {
            parent::save($dados, true);
            $action = !empty($dados['id']) ? "alterado" : "inserido";
            $success = "Registro $action com sucesso!";
            $response = 1;
        } catch (\Exception $ex) {
            $error[] = $ex->getMessage();
        }

        //Retorna os dados
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
                $banner = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($banner);
                $this->getEm()->flush();
            }

            $this->getEm()->commit();
            $success = "Ação executada com sucesso";
            $response = 1;
            $this->getLogger()->info("[{$this->getNameEntity()}] - Registros deletados ID " . implode(",", $ids) . " - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
