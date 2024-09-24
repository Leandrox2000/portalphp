<?php
namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Vinculo as VinculoEntity;
use Helpers\Session;

/**
 * Description of Vinculo
 *
 * @author Join
 */
class Vinculo extends BaseService
{
    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param VinculoEntity $entity
     */
    public function __construct(EntityManager $em, VinculoEntity $entity, Session $session)
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
    public function save($nome, $id=0)
    {
        $response   = 0;
        $error      = array();
        $success    = "";
        $repository = $this->getEm()->getRepository($this->getNameEntity());
        $dados = array(
                    'nome' => $nome,
                    'id'    => $id,
        );
        
        if ($repository->verificaVinculoExiste($dados)) {  
            $action = empty($id) ? "inserido" : "alterado";
            try {
                parent::save($dados);
                $response   = 1;
                $success = "Registro $action com sucesso!";
            } catch (\Exception $exc) {
                $action = empty($id) ? "inserir" : "alterar";
                $error[] = "Erro ao {$action} registro";
                $this->getLogger()->error($exc->getMessage());
            }
        } else {
            $error[] = "Já existe um registro com esse nome cadastrado.";
        }
        
        return array('error'=>$error, 'response'=>$response, 'success'=>$success);
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
        
        $funcionarioVinculado = $this->getEm()
                                ->getRepository("Entity\Funcionario")
                                ->getFuncionarioVinculado($id);
        
        if ($funcionarioVinculado) {
            $response = 2;
            $error = 'Não é possivel excluir este "Vínculo Funcionários" porque existem os seguintes Funcionários relacionados: ';
            foreach($funcionarioVinculado as $funcionario)
            {
                $error .= $funcionario['nome'].'; ';
            }
        } else {
            $vinculo = $this->getEm()->getReference($this->getNameEntity(), $id);
            try {
                $this->getEm()->remove($vinculo);
                $this->getEm()->flush();
                $response   = 1;
                $success = "Ação executada com sucesso";
            } catch (\Exception $exc) {
                $this->logger->error($exc->getMessage());
                $error[] = "Erro ao excluir registro.";
            }
        }
        
        return array('error'=>$error, 'response'=>$response, 'success'=>$success);
    }

    /**
     * 
     * @param int $id
     * @return array
     */
    public function getAllRelations($id)
    {
        $response   = 0;
        $error      = array();
        $success    = "";
        
        $repository = $this->getEm()->getRepository("Entity\Vinculo");
        
        return $repository->getAllRelationsVinculo($id);
        
        if (!$repository->verificaVinculo($id)) {
            $error[] = "Existem registros vinculados a esse vínculo.";
        } else {
            $vinculo = $this->getEm()->getReference($this->getNameEntity(), $id);
            try {
                $this->getEm()->remove($vinculo);
                $this->getEm()->flush();
                $response   = 1;
                $success = "Ação executada com sucesso";
            } catch (\Exception $exc) {
                $this->logger->error($exc->getMessage());
                $error[] = "Erro ao excluir registro.";
            }
        }
        
        return array('error'=>$error, 'response'=>$response, 'success'=>$success);
    }
}
