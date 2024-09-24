<?php

namespace CMS\Service\ServiceRepository;

/**
 * Conteúdos a ser indexados pelo Solr precisam implementar esta interface.
 */
interface SolrAwareInterface
{

    /**
     * Deve retornar um array com os dados a serem indexados pelo Apache Solr.
     * @param object $entity Entidade a ser indexada.
     */
    public function getDadosSolr($entity);

}
