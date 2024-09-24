<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\StatusLcc as StatusEntity;
use Helpers\Session;

/**
 * Description of StatusLcc
 *
 * @author Join
 */
class StatusLcc extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\StatusLcc $entity
     */
    public function __construct(EntityManager $em, StatusEntity $entity, Session $session)
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

        if ($repository->verificaNomeStatus($dados['nome'], $dados['id'])) {
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
        $success = "";
        $lccVinculado = $this->getEm()
                            ->getRepository("Entity\LicitacaoConvenioContrato")
                            ->getLccVinculado($id, 'status');
        
        if ($lccVinculado) {
            $response = 2;
            $error = 'Não é possivel excluir este "Status de Licitação" porque existem as seguintes Licitações relacionadas: ';
            foreach($lccVinculado as $lcc)
            {
                $error .= $lcc['objeto'].'; ';
            }
        } else {
            $status = $this->getEm()->getReference($this->getNameEntity(), $id);
            try {
                $this->getEm()->remove($status);
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
