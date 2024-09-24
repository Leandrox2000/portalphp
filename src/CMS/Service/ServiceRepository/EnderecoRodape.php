<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\EnderecoRodape as EnderecoRodapeEntity;
use Helpers\Session;

/**
 * Description of Bibliografia
 *
 * @author Luciano
 */
class EnderecoRodape extends BaseService
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Bibliografia $entity
     */
    public function __construct(EntityManager $em, EnderecoRodapeEntity $entity, Session $session)
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
        $response = 0;
        $error = array();
        $success = "";

        try {
            $action = empty($dados['id']) ? "inserido" : "alterado";
            parent::save($dados);
            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $error[] = $exc->getMessage();
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
            $this->getEm()->beginTransaction();

            foreach ($ids as $id) {
                $endereco = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($endereco);
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
