<?php

namespace CMS\Service\ServiceRepository;

use \Doctrine\ORM\EntityManager as EntityManager;
use \Entity\BoletimEletronico as BoletimEletronicoEntity;
use \CMS\Service\ServiceUpload\BoletimEletronicoUpload;
use Helpers\Session;

/**
 * Classe BoletimEletronico
 *
 * Responsável pelas ações na entidade BoletimEletronico
 * @author join-ti
 */
class BoletimEletronico extends BaseService
{

    /**
     *
     * @var BoletimEletronicoUpload
     */
    protected $upload;

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $entity
     */
    public function __construct(EntityManager $em, BoletimEletronicoEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     *
     * @return BoletimEletronicoUpload
     */
    public function getUpload()
    {
        if (!isset($this->upload))
            $this->upload = new BoletimEletronicoUpload();
        return $this->upload;
    }

    /**
     *
     * @param BoletimEletronicoUpload $upload
     */
    public function setUpload(BoletimEletronicoUpload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Metodo save
     *
     * Faz o upload do arquivo e aalva ou atualiza os dados de um registro.
     * @param array $dados
     * @return mixed
     */
    public function save(array $dados)
    {
        $error = array();
        $response = 0;
        $success = "";

        try {
            //Armazena a ação
            $action = empty($dados['id']) ? "inserido" : "alterado";

            //Verifica se o arquivo foi modificado
            if (!empty($dados['arquivo']) && $dados['arquivo'] !== $dados['arquivoAtual']) {
                $upload = $this->getUpload();
                $upload->setFile(getcwd() . "/uploads/temp/" . $dados['arquivo']);
                $upload->rename(getcwd() . "/uploads/boletimeletronico/" . $dados['arquivo']);
            }

            //Verifica se o arquivo anterior foi excluido
            if (!empty($dados['arquivoExcluido'])) {
                $upload = $this->getUpload();
                $upload->setFile(getcwd() . "/uploads/boletimeletronico/" . $dados['arquivoExcluido']);
                $upload->delete();
            }

            //Apaga os elementos de arquivo excluido e atual
            unset($dados['arquivoExcluido']);
            unset($dados['arquivoAtual']);

            //Busca o id
            $id = isset($dados['id']) ? $dados['id'] : 0;
            unset($dados['id']);

            //Se o id foi encontrado atualiza, se não, insere
            if ($id > 0) {
                $this->update($dados, $id);
            } else {
                $this->insert($dados);
            }

            //Monta a mensagem de sucesso
            $success = "Registro $action com sucesso!";
            $response = 1;
        } catch (\Exception $e) {
            $action = empty($dados['id']) ? "inserir" : "alterar";
            $this->getLogger()->error($e->getMessage());
            $error[] = "Erro ao {$action} registro";
        }

        //Retorna os resultados
        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     * Metodo delete
     *
     * Deleta um grupo de registros e seus respectivos arquivos
     * @param array $ids
     * @return boolean
     */
    public function delete(array $ids)
    {
        $error = array();
        $response = 0;
        $success = "Ação executada com sucesso";

        try {
            //Inicia a transação
            $this->getEm()->beginTransaction();

            foreach ($ids as $id) {
                $entity = $this->getEm()->getReference("Entity\BoletimEletronico", $id);
                $this->getEm()->remove($entity);
                $this->getEm()->flush();
            }

            $this->getEm()->commit();

            //Busca os arquivos
            $arquivos = $this->getEm()->getRepository("Entity\BoletimEletronico")->getArquivosBoletins($ids);

            //Percorre os arquivos e os exclui
            foreach ($arquivos as $arq) {
                $this->getUpload()->setFile(getcwd() . "/uploads/boletimeletronico/" . $arq['arquivo']);
                $this->getUpload()->delete();
            }

            $response = 1;
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            $this->getLogger()->error($e->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
