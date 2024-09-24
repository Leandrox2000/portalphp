<?php

namespace CMS\Service\ServiceRepository;

use Entity\CategoriaDicionario as CategoriaDicionarioEntity;
use Helpers\Session;

/**
 * Description of CategoriaDicionario
 *
 * @author Join-ti
 */
class CategoriaDicionario extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\CategoriaDicionario $entity
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, CategoriaDicionarioEntity $entity, Session $session)
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
        //Cria as variáveis de retorno
        $response = 0;
        $error = array();
        $success = "";

        //Instancia o repository e verifica se existe uma categoria com o mesmo nome
        $repository = $this->getEm()->getRepository($this->getNameEntity());

        if ($repository->verificaNomeCategoria($dados['nome'])) {

            //Armazena o nome da 
            $action = empty($dados['id']) ? "inserido" : "alterado";

            try {
                parent::save($dados);
                $response = 1;
                $success = "Registro $action com sucesso!";
            } catch (\Exception $exc) {
                $action = empty($dados['id']) ? "inserir" : "alterar";
                $error[] = "Erro ao {$action} registro";
            }
        } else {
            $error[] = "Já existe uma categoria com esse nome cadastrada.";
        }

        //Retorna o resultado
        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     * 
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        //Instancia as variáveis de retorno
        $response = 0;
        $error = array();
        $success = "";

        //Intancia o repository e verifica se existem legislações vinculadas com essa categoria
        $repository = $this->getEm()->getRepository("Entity\DicionarioPatrimonioCultural");

        if ($repository->verificaVinculoCategoria($id) == false) {
            $error[] = "Existem registros vinculados a essa categoria.";
        } else {
            //Remove a categoria
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

        //Retorna o resultado
        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
