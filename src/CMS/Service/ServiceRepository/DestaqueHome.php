<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager as EntityManager;
use Entity\DestaqueHome as DestaqueHomeEntity;
use Helpers\Session;

/**
 * Classe DestaqueHome
 * 
 * Responsável pelas ações na entidade DestaqueHome
 * @author join-ti
 */
class DestaqueHome extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\DestaqueHome $entity
     */
    public function __construct(EntityManager $em, DestaqueHomeEntity $entity, Session $session)
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
            //Inseri a relação com imagens
            $imagens = new \Doctrine\Common\Collections\ArrayCollection();
            $arrayImagens = explode(",", $dados['imagens']);

            foreach ($arrayImagens as $img) {
                $imagens->add($this->getEm()->getReference("Entity\Imagem", $img));
            }

            $dados['imagens'] = $imagens;

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
        $success = "Ação executada com sucesso";

        try {
            $this->getEm()->beginTransaction();

            foreach ($ids as $id) {
                $destaque = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($destaque);
                $this->getEm()->flush();
            }
            $this->getEm()->commit();
            $response = 1;
            $this->getLogger()->info("Destaque da home " . implode(",", $ids) . " foram excluidos.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
