<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\ImagemPasta as ImagemPastaEntity;
use CMS\Service\ServiceUpload\ImagemUpload;

/**
 * ImagemPasta
 */
class ImagemPasta extends BaseService
{

    /**
     *
     * @var ImagemUpload 
     */
    private $upload;

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\ImagemPasta $entity
     */
    public function __construct(EntityManager $em, ImagemPastaEntity $entity, \Helpers\Session $session, \Helpers\String $stringHelper)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
        $this->setStringHelper($stringHelper);
    }

    /**
     * 
     * @return ImagemUpload
     */
    public function getUpload()
    {
        if (!isset($this->upload)) {
            $this->setUpload(new ImagemUpload());
        }
        return $this->upload;
    }

    /**
     * 
     * @param \CMS\Service\ServiceUpload\ImagemUpload $upload
     */
    public function setUpload(ImagemUpload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * 
     * @param array $dados
     * @return array
     */
    public function save($dados)
    {
        //Declara as variáveis de retorno
        $response = 0;
        $error = array();
        $success = "";

        //Armazena o diretório base
        $baseDir = getcwd() . "/uploads/ckfinder/images/";

        //Armazena o nome da categoria e deleta do array
        $nomeCategoria = $dados['nomeCategoria'];
        unset($dados['nomeCategoria']);

        //Cria a variável que armazenará os dados da pasta existênte, caso se trate de um formulário de edição
        $nomePastaAntiga = "";

        if (!empty($dados['id'])) {
            //Busca os dados da pasta existente
            $pasta = $this->getEm()->getRepository($this->getNameEntity())->find($dados['id']);
            $nomePastaAntiga = $pasta->getCaminho();
            $nomeCategoria = $pasta->getCategoria()->getNome();
        }

        //Cria a pasta da categoria caso ela exista
        $this->getUpload()->createDir($baseDir, $nomeCategoria);

        //Verifica a existência de uma pasta com o mesmo nome da que será salva
        if (!$this->getUpload()->fileExist($baseDir . $nomeCategoria . "/" . $dados['caminho'])) {
            //Armazena a action e o nome da pasta da categoria caso exista
            $action = empty($dados['id']) ? "inserido" : "alterado";

            try {
                //Faz o salvamento
                parent::save($dados);

                //Verifica se o salvamento é uma inclusão
                if (empty($dados['id'])) {
                    $this->getUpload()->createDir($baseDir . $nomeCategoria . "/", $dados['caminho']);
                } else {
                    //Cria o caminho da pasta atual e do novo nome da pasta
                    $pastaAntiga = $baseDir . $this->getStringHelper()->removeSpecial($nomeCategoria) . "/" . $nomePastaAntiga;
                    $pastaNova = $baseDir . $this->getStringHelper()->removeSpecial($nomeCategoria) . "/" . $dados['caminho'];

                    //Renomeia o diretório
                    $this->getUpload()->renameDir($pastaAntiga, $pastaNova);
                }

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
     * Seta os Dados na Entidade com o Padrão definido
     * @param array $dados
     * @param string $entity
     * @return mixed
     */
    protected function setEntityDados(array $dados, $entity)
    {
        foreach ($dados as $key => $value) {
            if (!empty($value)) {
                $set = "set" . ucfirst($key);
                $entity->$set($value);
            }
        }

        return $entity;
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
        $baseDir = getcwd() . "/uploads/ckfinder/images/";

        $imagemVinculado = $this->getEm()
                            ->getRepository("Entity\Imagem")
                            ->getImagemVinculado($id);
		
        if ($imagemVinculado) {
            $response = 2;
            $error = 'Não é possivel excluir esta "Pasta" porque existem as seguintes Imagens relacionadas: ';
            foreach($imagemVinculado as $imagem)
            {
                $error .= $imagem['nome'].'; ';
            }
        } else {

            try {
                $dadosPasta = $this->getEm()->getRepository($this->getNameEntity())->find($id);
                $pastaAntiga = $baseDir . $this->getStringHelper()->removeSpecial($dadosPasta->getCategoria()->getNome()) . "/" . $dadosPasta->getCaminho();

                $pasta = $this->getEm()->getReference($this->getNameEntity(), $id);
                $this->getEm()->remove($pasta);
                $this->getEm()->flush();

                $this->getUpload()->removeDir($pastaAntiga);

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
