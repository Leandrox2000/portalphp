<?php

namespace Helpers;

use Logger;

/**
 * Responsável por manter o índice do Apache Solr.
 */
class SolrManager
{

    /**
     * Cliente do Apache Solr
     * @var \Solarium\Client
     */
    protected $client;

    /**
     *
     * @var \Logger
     */
    protected $logger;

    /**
     *
     * @return Logger
     */
    public function getLogger()
    {
        if ($this->logger === null) {
            $this->logger = Logger::getLogger('Error');
        }

        return $this->logger;
    }

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/solrConfig.php';
        $this->client = new \Solarium\Client($config);
    }

    /**
     * Gera um ID com base no tipo de conteúdo e ID do conteúdo.
     *
     * @param string $entity_name
     * @param integer $entity_id
     * @return string
     */
    private function getId($entity_name, $entity_id)
    {
        return $entity_name . '#' . $entity_id;
    }

    /**
     * Cria ou atualiza um registro no índice do Apache Solr.
     *
     * @param array $dados
     */
    public function save(array $dados)
    {
        $update = $this->client->createUpdate();

        $document = $update->createDocument();
        $document->id = $this->getId($dados['entity_name'], $dados['entity_id']);
        $document->entity_id = $dados['entity_id'];
        $document->entity_name = $dados['entity_name'];
        $document->title = $dados['title'];
        $document->description = $dados['description'];
        if (!empty($dados['autor'])) {
            $document->autor = $dados['autor'];
        }
        $document->publish_date = $dados['publish_date']->format('Y-m-d\TH:i:s\Z');
        $document->publish = $dados['publish'];
        $document->url = $dados['url'];

        if (!empty($dados['unpublish_date'])) {
            $document->unpublish_date = $dados['unpublish_date']->format('Y-m-d\TH:i:s\Z');
        }

        $update->addDocument($document);
        $update->addCommit();

        try {
            return $this->client->update($update);
        } catch (\Exception $e) {
            $message = 'Entity: ' . $dados['entity_name'] . ' Id: ' . $dados['entity_id'];
            $this->getLogger()->error($message . "\n" . $e);
        }
    }

    /**
     * Apaga um registro no índice do Apache Solr.
     *
     * @param string $entity_name
     * @param integer $entity_id
     * @return array
     */
    public function delete($entity_name, $entity_id)
    {
        $id = $this->getId($entity_name, $entity_id);

        $update = $this->client->createUpdate();
        $update->addDeleteById($id);
        $update->addCommit();

        try {
            return $this->client->update($update);
        } catch (\Exception $e) {
            $this->getLogger()->error($e);
        }
    }

    /**
     * Apaga diversos registros no índice do Apache Solr.
     *
     * @param string $entity_name
     * @param array $ids
     */
    public function bulkDelete($entity_name, array $ids)
    {
        foreach ($ids as $id) {
            $this->delete($entity_name, $id);
        }
    }

}
