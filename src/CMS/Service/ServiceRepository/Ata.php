<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Ata as AtaEntity;
use CMS\Service\ServiceUpload\AtaUpload;
use Helpers\Upload;
use Helpers\Session;

/**
 * Description of Ata
 *
 * @author Luciano
 */
class Ata extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @var AtaUpload
     */
    protected $upload;

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Ata $entity
     */
    public function __construct(EntityManager $em, AtaEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    public function getDadosSolr($entity)
    {
        return array(
            'entity_name'       => $this->getNameEntity(),
            'entity_id'         => $entity->getId(),
            'title'             => $entity->getNome(),
            'description'       => $entity->getDescricao(),
            'publish'           => $entity->getPublicado(),
            'publish_date'      => $entity->getDataInicial(),
            'unpublish_date'    => $entity->getDataFinal(),
            'url'               => \Helpers\Url::generateRoute('atasConselho'),
        );
    }

    /**
     *
     * @return ImagemUpload
     */
    public function getUpload()
    {
        if (empty($this->upload)) {
            $this->upload = new AtaUpload();
        }

        return $this->upload;
    }

    /**
     *
     * @param ImagemUpload $upload
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
    public function save(array $dados)
    {
        $response = 0;
        $error = array();
        $success = "";

        //Verifica se o arquivo anterior foi excluido
        if (!empty($dados['arquivoExcluido'])) {
            $upload = $this->getUpload();
            $upload->setFile(getcwd() . "/uploads/atas/" . $dados['arquivoExcluido']);
            $upload->delete();
        }

        //Verifica se o arquivo foi modificado
        if (!empty($dados['arquivo']) && $dados['arquivo'] !== $dados['arquivoAtual']) {
            $upload = $this->getUpload();
            $upload->setFile(getcwd() . "/uploads/temp/" . $dados['arquivo']);
            $nome = Upload::testaNome($dados['arquivo'], getcwd() . "/uploads/atas");
            $upload->rename(getcwd() . "/uploads/atas/" . $nome);
            $dados['arquivo'] = $nome;
        }

        //Apaga os elementos de arquivo excluido e atual
        unset($dados['arquivoExcluido']);
        unset($dados['arquivoAtual']);


        try {
            $action = empty($dados['id']) ? "inserido" : "alterado";
            $entity = parent::save($dados);

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);

            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $action = empty($dados['id']) ? "inserido" : "alterado";
            $error[] = $exc->getMessage();
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
            parent::delete($ids);

            /* Remove no índice do Solr */
            $this->getSolrManager()->bulkDelete('Entity\Ata', $ids);

            $this->getEm()->commit();
            $success = "Ação executada com sucesso";
            $response = 1;
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
