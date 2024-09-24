<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\EditalCategoria as EditalCategoriaEntity;
use Helpers\Session;

/**
 * Description of EditalCategoria
 *
 * @author Luciano
 */
class EditalCategoria extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\PublicacaoCategoria $entity
     */
    public function __construct(EntityManager $em, EditalCategoriaEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     * 
     * @param string $nome
     * @param int $id
     * @return array
     */
    public function save($nome, $id = 0)
    {
        $response = 0;
        $error = array();
        $success = "";
        $repository = $this->getEm()->getRepository($this->getNameEntity());
        $dados = array(
            'nome' => $nome,
            "id" => $id,
        );

        if ($repository->verificaCategoriaExiste($dados)) {
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

        $categoria = $this->getEm()->getReference($this->getNameEntity(), $id);

        if ($categoria->getEditais()->count() > 0) {
            $error[] = "Existem registros vinculados a essa categoria.";
        } else {
            try {
                $this->getEm()->remove($categoria);
                $this->getEm()->flush();
                $response = 1;
                $success = "Ação executada com sucesso";
            } catch (\Exception $exc) {
                $this->getLogger()->error($exc->getMessage());
                $error[] = "Erro ao excluir registro.";
            }
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
