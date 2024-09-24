<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Diretoria as DiretoriaEntity;
use Helpers\Session;

/**
 * Diretoria
 */
class Diretoria extends BaseService
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Diretoria $entity
     */
    public function __construct(EntityManager $em, DiretoriaEntity $entity, Session $session)
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

    /**
     * Seta o novo Status dos Registros
     *
     * @param string $ids
     * @param int $status
     * @throws Exception
     */
    public function setaStatus($ids, $status)
    {
        if (!empty($ids)) {
            try {
                $query = $this->getEm()->createQueryBuilder();
                $query->update('Entity\Diretoria', "D")
                        ->set("D.publicado", $status)
                        ->andWhere($query->expr()->in("D.funcionario", $ids));
                $query->getQuery()->execute();
            } catch (Exception $exc) {
                $this->getLogger()->error($exc->getTraceAsString());
                throw new \Exception("Não foi possível alterar o Status");
            }
        }
    }

}
