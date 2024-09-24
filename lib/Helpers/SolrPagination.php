<?php

namespace Helpers;

use Pagerfanta\Adapter\SolariumAdapter;
use Pagerfanta\Pagerfanta;

class SolrPagination extends Pagination
{

    /**
     *
     * @param object $client Solarium client
     * @param object $query Query
     * @param integer $perPage Itens por página
     */
    public function __construct($client, $query, $perPage = NULL)
    {
        $this->pagerfanta = new Pagerfanta(new SolariumAdapter($client, $query));
        $this->initPagerfanta($perPage);
        $this->initHelper();
    }

}