<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\BannerGeralCategoria as BannerCategoriaEntity;
use Helpers\Session;

/**
 * Categoria de Banner
 */
class BannerGeralCategoria extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\BannerGeralCategoria $entity
     */
    public function __construct(EntityManager $em, BannerCategoriaEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
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

            //Inicia a transação
            $this->getEm()->beginTransaction();

            //Salva o registro
            parent::save($dados);

            //Commita a transação
            $this->getEm()->commit();

            $success = "Registro {$action} com sucesso!";
            $response = 1;
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $error[] = $ex->getMessage();
        }

        return array(
            "success" => $success,
            "error" => $error,
            "response" => $response,
        );
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    public function delete(array $ids)
    {
        $success = "";
        $error = array();
        $response = 0;

        try {
            $success = "Ação executada com sucesso";
            parent::delete($ids);
            $response = 1;
        } catch (\Exception $ex) {
            $error[] = $ex->getMessage();
        }

        return array(
            "success" => $success,
            "error" => $error,
            "response" => $response,
        );
    }

}
