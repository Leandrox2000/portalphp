<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Cargo as CargoEntity;
use Helpers\Session;

/**
 * Description of Cargo
 *
 * @author Join
 */
class Cargo extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Cargo $entity
     */
    public function __construct(EntityManager $em, CargoEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     * 
     * @param string $cargo
     * @param int $id
     * @return array
     */
    public function save($cargo, $id = 0)
    {
        $response = 0;
        $error = array();
        $success = "";
        $repository = $this->getEm()->getRepository($this->getNameEntity());
        $dados = array(
            'cargo' => $cargo,
            "id" => $id,
        );

        if ($repository->verificaCargoExiste($dados)) {
            $action = empty($id) ? "inserido" : "alterado";
            try {
                parent::save($dados);
                $response = 1;
                $success = "Registro $action com sucesso!";
            } catch (\Exception $exc) {
                $action = empty($id) ? "inserir" : "alterar";
                $error[] = "Erro ao {$action} registro";
                $this->getLogger()->error($exc->getMessage());
            }
        } else {
            $error[] = "Já existe um registro com esse nome cadastrado.";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     * 
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        $response = 0;
        $error = array();
        $success = "";

        $repository = $this->getEm()->getRepository("Entity\\Funcionario");

        if (!$repository->verificaVinculoCargo($id)) {
            $error[] = "Existem registros vinculados a esse cargo.";
        } else {
            $cargo = $this->getEm()->getReference($this->getNameEntity(), $id);
            try {
                $this->getEm()->remove($cargo);
                $this->getEm()->flush();
                $response = 1;
                $success = "Ação executada com sucesso";
            } catch (\Exception $exc) {
                $this->logger->error($exc->getMessage());
                $error[] = "Erro ao excluir registro.";
            }
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
