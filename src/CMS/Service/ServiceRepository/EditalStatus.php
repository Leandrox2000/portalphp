<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\EditalStatus as EditalStatusEntity;
use Helpers\Session;

/**
 * Description of EditalStatus
 *
 * @author Luciano
 */
class EditalStatus extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\PublicacaoCategoria $entity
     */
    public function __construct(EntityManager $em, EditalStatusEntity $entity, Session $session)
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
    public function save($nome, $id = 0, $column = 1)
    {
        $response = 0;
        $error = array();
        $success = "";
        $repository = $this->getEm()->getRepository($this->getNameEntity());
        $dados = array(
            'nome' => $nome,
            "id" => $id,
            'column' => $column
        );

        if ($repository->verificaStatusExiste($dados)) {
            $action = empty($id) ? "inserido" : "alterado";
            try {
                parent::save($dados);
                $response = 1;
                $success = "Registro $action com sucesso!";
            } catch (\Exception $exc) {
                $action = empty($id) ? "inserir" : "alterar";
                //$error[] = "Erro ao {$action} registro";
                $error[] = $exc->getMessage();
                
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
        
        $editalVinculado = $this->getEm()
                ->getRepository('Entity\Edital')
                ->getEditalVinculado($id);
        
        if ($editalVinculado) {
            $response = 2;
            $error = 'Não é possivel excluir este "Status de Edital" porque existem os seguintes Editais relacionados: ';
            foreach($editalVinculado as $edital)
            {
                $error .= $edital['nome'].'; ';
            }
        } else {
            try {
                $this->getEm()->remove($status);
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
