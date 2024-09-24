<?php

namespace Helpers;

use Pagerfanta\Pagerfanta;
use Pagerfanta\View\DefaultView;
use Pagination\CustomTwitterBoostrapView;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;

/**
 * Paginação
 *
 * @author Igor Cemim
 */
class Pagination {

    /**
     *
     * @var Pagerfanta
     */
    protected $pagerfanta;
    /**
     *
     * @var callable
     */
    protected $routeGenerator;
    /**
     *
     * @var DefaultView
     */
    protected $view;
    /**
     *
     * @var array
     */
    protected $viewOptions;

    /**
     *
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        return $this->pagerfanta;
    }

    /**
     *
     * @return callable
     */
    public function getRouteGenerator()
    {
        return $this->routeGenerator;
    }

    /**
     *
     * @return DefaultView
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     *
     * @return array
     */
    public function getViewOptions()
    {
        return $this->viewOptions;
    }

    /**
     *
     * @param \Pagerfanta\Pagerfanta $pagerfanta
     * @return \Helpers\Pagination
     */
    public function setPagerfanta(Pagerfanta $pagerfanta)
    {
        $this->pagerfanta = $pagerfanta;
        return $this;
    }

    /**
     *
     * @param callable $routeGenerator
     * @return \Helpers\Pagination
     */
    public function setRouteGenerator(callable $routeGenerator)
    {
        $this->routeGenerator = $routeGenerator;
        return $this;
    }

    /**
     *
     * @param \Pagerfanta\View\DefaultView $view
     * @return \Helpers\Pagination
     */
    public function setView(DefaultView $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     *
     * @param array $viewOptions
     * @return \Helpers\Pagination
     */
    public function setViewOptions($viewOptions)
    {
        $this->viewOptions = $viewOptions;
        return $this;
    }

    protected function initPagerfanta($perPage = NULL)
    {
        $page = (!empty($_GET['pagina'])) ? $_GET['pagina'] : 1;

        if ($perPage === NULL) {
            $this->pagerfanta->setMaxPerPage(10);
        } else {
            $this->pagerfanta->setMaxPerPage($perPage);
        }
        

        try {
            $this->pagerfanta->setCurrentPage($page);
        } catch (OutOfRangeCurrentPageException $e) {
            // Se a página não existir
            $this->pagerfanta->setCurrentPage(1);
        }

    }

    protected function initHelper()
    {
        $this->view = new CustomTwitterBoostrapView();
        $this->viewOptions = array(
            'prev_message' => 'Anterior',
            'next_message' => 'Próxima',
            'proximity' => 2,
        );

        $this->routeGenerator = function($page) {
            $params = array('pagina' => $page);
            $query = array_merge($_GET, $params);
            $urlComponents = parse_url($_SERVER['REQUEST_URI']);
            $queryString = http_build_query($query);

            return $urlComponents['path'] . '?' . $queryString;
        };
    }

    /**
     *
     * @param \Doctrine\ORM\Query $queryBuilder Query
     * @param integer $perPage Itens por página
     */
    public function __construct($queryBuilder, $perPage = NULL)
    {
        if($queryBuilder instanceof \Pagerfanta\Adapter\DoctrineDbalAdapter) { 
            $adapter = $queryBuilder;
        }
        else {
            
            $adapter = new DoctrineORMAdapter($queryBuilder);
        }
                
        $this->pagerfanta = new Pagerfanta($adapter);
        $this->initPagerfanta($perPage);
        $this->initHelper();
    }

    /**
     * Retorna os resultados
     * @return Pagerfanta
     */
    public function results()
    {
        return $this->pagerfanta;
    }

    /**
     * Retorna o HTML da paginação
     * @return string
     */
    public function render()
    {
        return $this->view->render(
            $this->pagerfanta,
            $this->routeGenerator,
            $this->viewOptions
        );
    }

}