<?php
namespace Entity\Repository;

use Helpers\Session;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AgendaDirecaoRepository extends BaseRepository
{    
    /**
     * Retorna a quantidade de agendas que pertencem aos sites
     * que o usuário logado pode ver.
     *
     * @return int
     */
    public function countAll($user)
    {
        //monta o dql
        $dql = "SELECT COUNT(DISTINCT ga) FROM Entity\AgendaDirecao ga JOIN ga.sites sit";

        $parametros = array();        
        $dql .= " WHERE sit.id IN (:sites) ";
        $parametros['sites'] = $user['subsites'];

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        return $query->getSingleScalarResult();
    }
    
    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param \Entity\Usuario $user
     * @return array
     */
    public function buscarRegistros($limit, $offset, $filtro, array $user, $countPagination = false)
    {
        $select = 'ga';
        $where = '';
        $join = '';
        $ordem = '';
        $parametros = array();        
        if (!empty($filtro['busca'])) {
            $where .= " AND (";
            $where .= " Unaccent(LOWER(ga.titulo)) LIKE Unaccent(LOWER(:busca)) ";
            $where .= " OR Unaccent(LOWER(sit.nome)) LIKE Unaccent(LOWER(:busca)) ";
            $where .= " OR Unaccent(LOWER(sit.sigla)) LIKE Unaccent(LOWER(:busca)) ";
            $where .= " OR Unaccent(LOWER(resp.responsavel)) LIKE Unaccent(LOWER(:busca)) ";
            $where .= " ) ";
            $parametros['busca'] = "%" . $filtro['busca'] . "%";
            $join = ' JOIN ga.responsaveis resp ';
        }

        if (isset($filtro['status']) && $filtro['status'] != null) {
            $where .= " AND ga.publicado = :publicado ";
            $parametros['publicado'] = (int)$filtro['status'];
        }
        
        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $where .= " AND (ga.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_final'])) {
            $where .= " AND ga.dataCadastro < :data_final ";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_inicial'])) {
            $where .= " AND (ga.dataCadastro > :data_inicial ) ";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
        }
        
        if (!empty($filtro['site'])) {
            $where .= " AND sit.id = :id_site ";
            $parametros['id_site'] = $filtro['site'];
            
            $select = 'ga, ags';
            $join .= ' JOIN ga.agendaDirecaoSite ags ';
            $join .= ' JOIN ags.site sit ';
            $ordem .= ' ORDER BY ags.ordem ASC ';
        } else {
            $where .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
            $join .= ' JOIN ga.sites sit ';
            $ordem .= ' ORDER BY ga.dataCadastro DESC ';
        }

        //Estrutura o sql da busca
        if(!$countPagination){
            $dql = "SELECT $select
                    FROM Entity\AgendaDirecao ga
                         $join 
                    WHERE 1 = 1 $where $ordem";
        } else {
            $dql = "SELECT COUNT(DISTINCT ga) total 
                    FROM Entity\AgendaDirecao ga
                         $join 
                    WHERE 1 = 1 $where ";
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        if(($limit + $offset) > 0 && !$countPagination){
            $query->setFirstResult($offset)
                  ->setMaxResults($limit);
        }
        
        if(!$countPagination) {            
            $paginator = new Paginator($query, true);
            return $paginator->getIterator();
        } else {
            return $query->getResult();
        }
        
    }
    
    /**
     * Retorna as agendas que o usuário logado é responsável.
     *
     * @return int
     */
    public function agendasByLogin($user)
    {
        $dql = "
            SELECT ga 
            FROM Entity\AgendaDirecao ga 
            JOIN ga.responsaveis resp 
            WHERE resp.responsavel = :responsavel
            ORDER BY ga.titulo ASC";
        
        $parametros = array(
            'responsavel' => $user['dadosUser']['login'],
        );

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        return $query->getResult();
    }
    
    /**
     * Retorna as agendas que estão publicadas.
     *
     * @param \Entity\Site|null $site
     * @return int
     */
    public function agendasPublicadas($site = null) {         
        $parametros = array(
            'publicado' => 1,
            'today' => new \DateTime('now'),
        );
        
        if ($site instanceof \Entity\Site) {
            $where = "s.sigla = :sigla";
            $parametros['sigla'] = $site->getSigla();
        } else {
            $where = "s.sigla IN ('SEDE')";
        }
        
        $dql = "
            SELECT ga
            FROM Entity\AgendaDirecao ga                  
                 JOIN ga.agendaDirecaoSite ads
                 JOIN ads.site s
            WHERE ga.publicado = :publicado
              AND (ga.dataFinal > :today OR ga.dataFinal IS NULL)
              AND $where
            ORDER BY ads.ordem ASC";

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        return $query->getResult();
    }

}
