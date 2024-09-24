<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\AmbitoLcc as AmbitoEntity;
use Helpers\Session;

/**
 * Description of AmbitoLcc
 *
 * @author Join
 */
class AmbitoLcc extends BaseService
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\AmbitoLcc $entity
     */
    public function __construct(EntityManager $em, AmbitoEntity $entity, Session $session)
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
        $response = 0;
        $error = array();
        $success = "";

        $repository = $this->getEm()->getRepository($this->getNameEntity());

        if ($repository->verificaNomeAmbito($dados['nome'], $dados['id']))  {
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
        $repository = $this->getEm()->getRepository("Entity\LicitacaoConvenioContrato");

        if (!$repository->verificaVinculo($id, 'ambito')) {
            $error[] = "Existem registros vinculados a esse âmbito.";
        } else {
            $ambito = $this->getEm()->getReference($this->getNameEntity(), $id);
            try {
                $this->getEm()->remove($ambito);
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
