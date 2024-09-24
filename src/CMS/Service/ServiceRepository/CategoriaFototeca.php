<?php

namespace CMS\Service\ServiceRepository;

use Entity\CategoriaFototeca as CategoriaFototecaEntity;
use Helpers\Session;

/**
 * Description of CategoriaFototeca
 *
 * @author Join-ti
 */
class CategoriaFototeca extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\CategoriaFototeca $entity
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, CategoriaFototecaEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     * 
     * @param string $categoria
     * @param int $id
     * @return array
     */
    public function save($dados)
    {
        $response = 0;
        $error = array();
        $success = "";

        $repository = $this->getEm()->getRepository($this->getNameEntity());

        if ($repository->verificaNomeCategoria($dados['nome'])) {
            $action = empty($dados['id']) ? "inserido" : "alterado";
            try {
                parent::save($dados);
                $response = 1;
                $success = "Registro $action com sucesso!";
            } catch (\Exception $exc) {
                $action = empty($dados['id']) ? "inserir" : "alterar";
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
        $repository = $this->getEm()->getRepository("Entity\Fototeca");

        if (!$repository->verificaVinculoCategoria($id)) {
            $error[] = "Existem registros vinculados a essa categoria.";
        } else {
            $categoria = $this->getEm()->getReference($this->getNameEntity(), $id);
            try {
                $this->getEm()->remove($categoria);
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
