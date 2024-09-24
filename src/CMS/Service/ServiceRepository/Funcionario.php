<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Funcionario as FuncionarioEntity;
use CMS\Service\ServiceUpload\FuncionarioUpload;
use Helpers\Upload;
use Helpers\Session;

/**
 * Description of Funcionario
 *
 * @author Join
 */
class Funcionario extends BaseService
{

    /**
     *
     * @var FuncionarioUpload
     */
    private $upload;

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Funcionario $entity
     */
    public function __construct(EntityManager $em, FuncionarioEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     *
     * @return \CMS\Service\ServiceUpload\FuncionarioUpload
     */
    public function getUpload()
    {
        if (empty($this->upload)) {
            $this->setUpload(new FuncionarioUpload());
        }
        return $this->upload;
    }

    /**
     *
     * @param \CMS\Service\ServiceUpload\FuncionarioUpload $upload
     */
    public function setUpload(FuncionarioUpload $upload)
    {
        $this->upload = $upload;
    }

    public function salvaImagem($imagem = "", $imagemExcluida = "", $id = 0, $imagemAtual = "")
    {
        $action = empty($id) ? "inserir" : "alterar";
        $nome = $imagem;

        try {
            if ($id > 0) {
                if (!empty($imagemExcluida)) {
                    $this->getUpload()->setFile(getcwd() . "/uploads/funcionario/" . $imagemExcluida);
                    $this->getUpload()->delete();
                }

                if (!empty($imagem) && $imagem !== $imagemAtual) {
                    $this->getUpload()->setFile(getcwd() . "/uploads/temp/" . $imagem);
                    $nome = Upload::testaNome($imagem, getcwd() . "/uploads/funcionario");
                    $this->getUpload()->rename(getcwd() . "/uploads/funcionario/" . $nome);
                }
            } else {
                if (!empty($imagem)) {
                    $this->getUpload()->setFile(getcwd() . "/uploads/temp/" . $imagem);
                    $nome = Upload::testaNome($imagem, getcwd() . "/uploads/funcionario");
                    $this->getUpload()->rename(getcwd() . "/uploads/funcionario/" . $nome);
                }
            }
        } catch (\Exception $exc) {
            $error[] = "Ocorreu um erro ao $action o registro";
            $this->getLogger()->error($exc->getMessage());
            return array('error' => $error);
        }

        return array('error' => array(), 'nome' => $nome);
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

        $result = $this->salvaImagem($dados['imagem'], $dados['imagemExcluida'], $dados['id'], $dados['imagemAtual']);
        unset($dados['imagemExcluida']);
        unset($dados['imagemAtual']);

        if (count($result['error']) > 0) {
            return array("error" => $result['error'], "response", 0);
        }

        try {
            $dados['imagem'] = $result['nome'];
            $action = empty($dados['id']) ? "inserido" : "alterado";
            $entity = parent::save($dados);

            if ($action == 'inserido') {
                // Cria registro na diretoria
                $diretoria = new \Entity\Diretoria();
                $diretoria->setFuncionario($entity);
                $this->getEm()->persist($diretoria);
                $this->getEm()->flush();
            }

            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $action = empty($dados['id']) ? "inserir" : "alterar";
            $error[] = "Erro ao {$action} registro";
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
                $funcionario = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getUpload()->setFile(getcwd() . "/uploads/funcionario/" . $funcionario->getImagem());
                if ($funcionario->getImagem()) {
                    $this->getUpload()->delete();
                }
                $this->getEm()->remove($funcionario);
                $this->getEm()->flush();
            }
            $this->getEm()->commit();
            $success = "Ação executada com sucesso";
            $response = 1;
            $this->getLogger()->info("Funcionário " . implode(",", $ids) . " foram excluidos.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
