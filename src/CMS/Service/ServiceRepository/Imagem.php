<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager as EntityManager;
use Entity\Imagem as ImagemEntity;
use CMS\Service\ServiceUpload\ImagemUpload;
use Helpers\Upload;
use Helpers\Session;

/**
 * Classe Imagem
 * 
 * Responsável pelas ações na entidade Imagem
 * @author join-ti
 */
class Imagem extends BaseService {

    /**
     *
     * @var ImagemUpload 
     */
    protected $upload;

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param ImagemEntity $entity
     */
    public function __construct(EntityManager $em, ImagemEntity $entity, Session $session) {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     * 
     * @return ImagemUpload
     */
    public function getUpload() {
        if (!isset($this->upload)) {
            $this->upload = new ImagemUpload();
        }
        return $this->upload;
    }

    /**
     * 
     * @param ImagemUpload $upload
     */
    public function setUpload(ImagemUpload $upload) {
        $this->upload = $upload;
    }

    /**
     * 
     * @param string $arquivoNovo
     * @param string $arquivoAtual
     * @param string $arquivoExcluido
     * @param string $caminho
     * @param string $caminhoAntigo
     * @param array $coords Array com as coordenadas para Crop
     * @return type
     * @throws \Exception
     */
    public function salvarImagem($arquivoNovo, $arquivoAtual, $arquivoExcluido, $caminho, $caminhoAntigo, $coords = array()) {
        $upload = $this->getUpload();
        $canvas = $upload->getCanvas();
        $nome   = $arquivoNovo;

        try {
            //Verifica se o arquivo anterior foi excluido
            if (!empty($arquivoExcluido)) {
                $upload = $this->getUpload();
                $upload->setFile(getcwd() . "/uploads/ckfinder/images/" . $caminhoAntigo . $arquivoExcluido);
                $upload->delete();
            }

            //Verifica se o arquivo foi modificado
            if ((!empty($arquivoNovo) && $arquivoNovo !== $arquivoAtual) || $upload->fileExist(getcwd() . "/uploads/temp/" . $arquivoNovo)) {
                $nome   = Upload::testaNome($arquivoNovo, getcwd() . "/uploads/ckfinder/images/" . $caminho);
                if ($coords[2]>0 && $coords[3]>0) {
                    $canvas->carrega(getcwd() . "/uploads/temp/" . $arquivoNovo)
                            ->posicaoCrop($coords[0], $coords[1], $coords[2], $coords[3])
                            ->redimensiona($coords[2], $coords[3], 'crop')
                            ->grava(getcwd() . "/uploads/ckfinder/images/" . $caminho . $nome);
                    $upload->setFile(getcwd() . "/uploads/temp/" . $arquivoNovo);
                    $upload->delete();
                } else {
                    $upload->setFile(getcwd() . "/uploads/temp/" . $arquivoNovo);
                    $upload->rename(getcwd() . "/uploads/ckfinder/images/" . $caminho . $nome);
                }
            } else {
                if ($caminhoAntigo !== $caminho) {
                    $nome   = Upload::testaNome($arquivoAtual, getcwd() . "/uploads/ckfinder/images/" . $caminho);
                    if ($coords[2]>0 && $coords[3]>0) {
                        $canvas->carrega(getcwd() ."/uploads/ckfinder/images/". $caminhoAntigo.$arquivoAtual)
                                ->posicaoCrop($coords[0], $coords[1], $coords[2], $coords[3])
                                ->redimensiona($coords[2], $coords[3], 'crop')
                                ->grava(getcwd() ."/uploads/ckfinder/images/".$caminho.$nome);
                        $upload->setFile(getcwd() ."/uploads/ckfinder/images/". $caminhoAntigo.$arquivoAtual);
                        $upload->delete();
                    } else {
                        $this->getUpload()->cut(getcwd() ."/uploads/ckfinder/images/". $caminhoAntigo.$arquivoAtual, getcwd() ."/uploads/ckfinder/images/".$caminho.$nome);
                    }
                } else {
                    if ($coords[2]>0 && $coords[3]>0) {
                        $nome = $arquivoAtual;
                        $canvas->carrega(getcwd() ."/uploads/ckfinder/images/".$caminhoAntigo.$arquivoAtual)
                            ->posicaoCrop($coords[0], $coords[1], $coords[2], $coords[3])
                            ->redimensiona($coords[2], $coords[3], 'crop')
                            ->grava(getcwd() ."/uploads/ckfinder/images/".$caminhoAntigo.$arquivoAtual);
                    }
                }
            }
            
        } catch (\Exception $ex) {
            $this->getLogger()->error($ex->getMessage());
            throw new \Exception("Erro ao salvar arquivo");
        }

        return $nome;
    }

    /**
     * 
     * @param array $dados
     * @return mixed
     */
    public function save(array $dados) {
        $error = array();
        $response = 0;
        $success = "";

        try {
            //Armazena a ação
            $action = empty($dados['id']) ? "inserido" : "alterado";
            $pasta = $dados['dadosPasta'];

            //Faz o salvamento
            $dados['imagem'] = $this->salvarImagem($dados['imagem'], $dados['arquivoAtual'], $dados['arquivoExcluido'], $this->getStringHelper()->removeSpecial($pasta->getCategoria()->getNome()) . "/" . $pasta->getCaminho() . "/", $dados['caminhoAntigo'], $dados['coords']);

            //Apaga os elementos de arquivo excluido e atual
            unset($dados['arquivoExcluido']);
            unset($dados['arquivoAtual']);
            unset($dados['dadosPasta']);
            unset($dados['caminhoAntigo']);
            unset($dados['coords']);
            
            //Busca o id
            $id = isset($dados['id']) ? $dados['id'] : 0;
            unset($dados['id']);

            //Se o id foi encontrado atualiza, se não, insere
            if ($id > 0) {
                $this->update($dados, $id);
            } else {
                $this->insert($dados);
            }

            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $ex) {
            $action = empty($dados['id']) ? "inserir" : "alterar";
            $this->getLogger()->error($ex->getMessage());
            $error[] = "Erro ao {$action} registro";
        }

        //Retorna o resultado
        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     * Metodo delete
     * 
     * Deleta um grupo de registros e suas respectivas imagens
     * @param array $ids
     * @return boolean
     */
    public function delete(array $ids) {
        $error = array();
        $success = "Ação executada com sucesso";
        $response = 0;

        try {
            //Inicia a transação
            $this->getEm()->beginTransaction();

            //Busca os arquivos
            $arquivos = $this->getEm()->getRepository("Entity\Imagem")->getImagemIds($ids);

            foreach ($ids as $id) {
                $entity = $this->getEm()->getReference("Entity\Imagem", $id);
                $this->getEm()->remove($entity);
                $this->getEm()->flush();
            }

            //Percorre os arquivos e os exclui
            foreach ($arquivos as $arq) {
                $this->getUpload()->setFile(getcwd() . "/uploads/ckfinder/images/" . $this->getStringHelper()->removeSpecial($arq->getPasta()->getCategoria()->getNome()) . "/" . $arq->getPasta()->getCaminho() . "/" . $arq->getImagem());
                $this->getUpload()->delete();
            }

            $this->getEm()->commit();
            $response = 1;
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            $this->getLogger()->error($e->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s). Imagens que possuem registros vinculados, não podem ser excluídas";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
