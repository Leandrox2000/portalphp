<?php

namespace CMS\Service\ServiceRepository;

use \Doctrine\ORM\EntityManager;
use \CMS\Service\ServiceUpload\LogoRodapeUpload;
use \Entity\LogoRodape as LogoRodapeEntity;
use Helpers\Session;

/**
 * Description of LogoRodape
 *
 * @author Luciano
 */
class LogoRodape extends BaseService
{

    private $upload;

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\LogoRodape $entity
     */
    public function __construct(EntityManager $em, LogoRodapeEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     * 
     * @return LogoRodapeUpload
     */
    public function getUpload()
    {
        if (empty($this->upload)) {
            $this->setUpload(new LogoRodapeUpload);
        }
        return $this->upload;
    }

    /**
     * 
     * @param LogoRodapeUpload $upload
     */
    public function setUpload($upload)
    {
        $this->upload = $upload;
    }

    /**
     * 
     * @return int
     */
    public function getProximaOrdem()
    {
        $repository = $this->getEm()->getRepository($this->getNameEntity());

        return (int) $repository->getMaxOrder() + 1;
    }

    public function salvaImagem($imagem, $imagemExcluida, $id = 0)
    {
        $action = empty($id) ? "Inserir" : "Alterar";
        try {
            $this->getUpload()->setFile(getcwd() . "/uploads/temp/" . $imagem);
            if ($id > 0) {
                if ($imagemExcluida) {
                    $this->getUpload()->rename(getcwd() . "/uploads/logoRodape/" . $imagem);
                    $this->getUpload()->setFile(getcwd() . "/uploads/logoRodape/" . $imagemExcluida);
                    $this->getUpload()->delete();
                }
            } else {
                $this->getUpload()->rename(getcwd() . "/uploads/logoRodape/" . $imagem);
            }
        } catch (\Exception $exc) {
            $error[] = "Ocorreu um error ao $action o Logo";
            $this->getLogger()->error($exc->getMessage());
            return array('error' => $error);
        }
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

        if ($dados['temLink'] == 0) {
            $dados['link'] = "";
        }

        $result = $this->salvaImagem($dados['imagem'], $dados['imagemExcluida'], $dados['id']);

        if (count($result['error']) > 0) {
            return array("error" => $result['error'], "response", 0);
        }

        unset($dados['imagemExcluida']);
        unset($dados['temLink']);

        if (empty($dados['id'])) {
            $dados['ordem'] = $this->getProximaOrdem();
            $action = "inserido";
        } else {
            $action = "alterado";
        }

        try {
            parent::save($dados);
            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $action = empty($dados['id']) ? "inserir" : "alterar";
            $this->getLogger()->error($exc->getMessage());
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
        $success = "Ação executada com sucesso";


        try {
            foreach ($ids as $id) {
                $logo = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getUpload()->setFile(getcwd() . "/uploads/logoRodape/" . $logo->getImagem());
                $this->getUpload()->delete();
                $this->getEm()->remove($logo);
                $this->getEm()->flush();
            }

            $response = 1;
            $this->getLogger()->info("logos " . implode(",", $ids) . " foram excluidos.");
        } catch (\Exception $exc) {
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
