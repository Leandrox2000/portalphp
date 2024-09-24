<?php

namespace CMS\Service\ServiceRepository;

use Entity\PerguntaCategoria as PerguntaCategoriaEntity;
use Helpers\Session;

/**
 * Description of PerguntaCategoria
 *
 * @author Join-ti
 */
class PerguntaCategoria extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\PerguntaCategoria $entity
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, PerguntaCategoriaEntity $entity, Session $session)
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
    public function save($categoria, $id = 0)
    {
        $response = 0;
        $error = array();
        $success = "";
        $repository = $this->getEm()->getRepository($this->getNameEntity());
        $dados = array(
            'categoria' => $categoria,
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
        $perguntaVinculado = $this->getEm()
                            ->getRepository("Entity\Pergunta")
                            ->getPerguntaVinculado($id);
        if ($perguntaVinculado) {
            $response = 2;
            $error = 'Não é possivel excluir esta "Categoria Perguntas Frequentes" porque existem as seguintes Perguntas relacionadas: ';
            foreach($perguntaVinculado as $pergunta)
            {
                $error .= $pergunta['pergunta'].'; ';
            }
        } else {
        	$pergunta = $this->getEm()->getReference($this->getNameEntity(), $id);
            try {
                $this->getEm()->remove($pergunta);
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
