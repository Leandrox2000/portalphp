<?php

namespace Entity\Repository;

class MenuRepository extends BaseRepository
{

    protected $entity = 'Entity\Menu';
    protected $search_column = 'titulo';

    /**
     *
     * @return int
     */
    public function countAll()
    {
        //monta o dql
        $dql = "SELECT DISTINCT COUNT(e) total FROM {$this->entity} e ";

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getSingleScalarResult();
    }

    /**
     *
     * @param integer $site ID do subsite.
     * @return array
     */
    public function getQueryPortal($site = NULL, $tipo = NULL)
    {
        $query = $this->createQueryBuilder('e')
                      ->distinct()
                      ->join('e.site', 's')
                      ->where('e.publicado = 1')
                      ->andWhere('e.dataInicial < :today')
                      ->andWhere('e.dataFinal > :today OR e.dataFinal IS NULL');

        if (!empty($site)) {
            $query->andWhere("s.id = :siteId")
                  ->setParameter('siteId', $site);
        } else {
            $query->andWhere("s.sigla = 'SEDE'");
        }

        if (!empty($tipo)) {
            $query->andWhere('e.tipoMenu IN (:tipoMenu)')
                  ->setParameter('tipoMenu', $tipo);
        }

        return $query->orderBy('e.ordem', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME)
                     ->getResult();

    }
    
    /**
     *
     * @return string
     */
    private function getWhereBusca($filtro)
    {
        $dql = " ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(e.{$this->search_column}) LIKE :{$this->search_column} ";
            $parametros[$this->search_column] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND e.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['tipoMenu'])) {
            $dql .= " AND e.tipoMenu = :tipoMenu ";
            $parametros['tipoMenu'] = $filtro['tipoMenu'];
        }

        if (!empty($filtro['subsite'])) {
            $dql .= " AND e.site IN ({$filtro['subsite']}) ";
//            $parametros['site'] = $filtro['subsite'];
        }

        if (!empty($filtro['vinculoPai'])) {
            $dql .= " AND e.vinculoPai = :vinculoPai";
            $parametros['vinculoPai'] = $filtro['vinculoPai'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (e.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        return array(
            'dql' => $dql,
            'parametros' => $parametros,
        );
    }

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtro
     * @return array
     */
    public function getBusca($limit, $offset, $filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT e FROM {$this->entity} e WHERE 1 = 1 ";

        $whereBusca = $this->getWhereBusca($filtro);
        $dql .= $whereBusca['dql'];

        //Ordena os dados
        $dql .= " ORDER BY e.ordem, e.titulo ASC ";

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($whereBusca['parametros']);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     *
     * @param array $filtro
     * @return integer
     */
    public function getTotal($filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT COUNT(e) total FROM {$this->entity} e WHERE 1 = 1 ";

        $whereBusca = $this->getWhereBusca($filtro);
        $dql .= $whereBusca['dql'];

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($whereBusca['parametros']);

        $resultado = $query->getResult();
        return $resultado[0]['total'];
    }

    /**
     *
     * @param integer $categoria
     * @return type
     */
    public function verificaVinculoCategoria($categoria)
    {
        //monta o dql
        $dql = "SELECT count(dic) total FROM {$this->entity} dic JOIN dic.categoria cat WHERE cat.id = :id_categoria  ";
        $parametros['id_categoria'] = $categoria;

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }
    
    /**
     * Retorna o Menu para BrearCrumbs fazendo join na entidade \Entity\FuncionalidadeMenu
     * 
     * @param type $entidade é a coluna \Entity\FuncionalidadeMenu.no_entidade a ser filtrada
     * @param type $id
     * @return type
     */
    public function getBreadCrumbs($entidade, $id = null, $url = null, $site = null) 
    {
        //$this->getEntityManager()->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        $query = $this->createQueryBuilder("M")
                    ->leftJoin("M.funcionalidadeMenu", "FM")
                    ->leftJoin("M.site", "S")
                    ->leftJoin("M.vinculoPai", "Mp")
                    ->leftJoin("Mp.vinculoPai", "Mp2")
                    ->andWhere("M.publicado = 1")
                    ->andWhere("(Mp.publicado = 1 OR Mp.publicado is null)")
                    ->andWhere("(Mp2.publicado = 1 OR Mp2.publicado is null)")
                    ->addOrderBy("Mp2.ordem")
                    ->addOrderBy("Mp.ordem")
                    ->addOrderBy("M.ordem")
                    ->addOrderBy("Mp2.titulo")
                    ->addOrderBy("Mp.titulo")
                    ->addOrderBy("M.titulo");
                if (!empty($entidade)) {
                    $query->andWhere("FM.entidade = :entidade")
                        ->setParameter("entidade", $entidade);
                }
                if ($id !== null) {
                    $query->andWhere("M.idEntidade = :id")
                        ->setParameter("id", $id);
                }
                if ($url !== null) {
                    $query->andWhere("FM.url = :url")
                        ->setParameter("url", $url);
                }
                $query->andWhere("S.id = :site");
                if ($site instanceof \Entity\Site) {
                    $query->setParameter("site", $site->getId());
                } else {
                    $query->setParameter("site", 1);
                }
                
                try {
                    return $query
                        ->getQuery()
                        ->useQueryCache(TRUE)
                        ->useResultCache(TRUE, CACHE_LIFE_TIME)
                        ->getSingleResult();
                } catch (\Doctrine\ORM\NonUniqueResultException $exc) {
                    return $query
                        ->setMaxResults(1)
                        ->getQuery()
                        ->useQueryCache(TRUE)
                        ->useResultCache(TRUE, CACHE_LIFE_TIME)
                        ->getSingleResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                    return null;;
                }

        
    }

    
    /**
     *
     * @param array $ids
     * @return array
     */
    public function findIn(array $ids)
    {
        if (count($ids) > 0) {
            $dql = "SELECT t FROM {$this->getEntityName()} t WHERE t.id IN (:ids)";
            $query = $this->getEntityManager()
                    ->createQuery($dql)
                    ->setParameter('ids', $ids);
            return $query->getResult();
        } else {
            return array();
        }
    }

    
}