<?php

namespace Entity\Repository;

/**
 * Description of PaginaEstaticaRepository
 *
 * @author Eduardo
 */
class PaginaEstaticaRepository extends BaseRepository
{

    /**
     * 
     * @param type $id
     * @return \Entity\Pagina
     */
    public function getQueryPortal($id, $publicacao = true)
    {
        try {
            //Cria a query
            $query = $this->createQueryBuilder('p')
                    ->leftJoin('p.galerias', 'g')
                    ->where('p.id = :id');

            //Se publicado
            if ($publicacao) {
                $query->andWhere('p.publicado = 1')
                        ->andWhere(':now >= p.dataInicial')
                        ->andWhere(':now <= p.dataFinal OR p.dataFinal IS NULL')
                        ->setParameter('now', new \DateTime('now'));
            }

            try {
                return $query->setParameter('id', $id)
                                ->getQuery()
                                //   ->useQueryCache(TRUE)
                                // ->useResultCache(TRUE, CACHE_LIFE_TIME)
                                ->getSingleResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                return FALSE;
            }
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * 
     * @return int
     */
    public function countAll($user)
    {
        //monta o dql
        $dql = "SELECT COUNT(DISTINCT pa) FROM Entity\PaginaEstatica pa  JOIN pa.sites sit ";

        //Verifica se o usuário é da sede
        $parametros = array();
        if (!$user['sede']) {
            $dql .= " WHERE sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }


        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        return $query->getSingleScalarResult();
    }

    
    public function getConteudosEstaticos($site,$limit, $offset)
    {
        //Estrutura o sql da busca
        $dql = "SELECT pa FROM Entity\PaginaEstatica pa JOIN pa.sites sit WHERE 1 = 1 AND pa.publicado = 1";
        $dql .= " AND sit.id = :sites ";
        $dql .= " ORDER BY pa.dataCadastro DESC ";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("sites", $site);
        
        
        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
        
        
        #Tratar 
        $aux = $query->getResult();
        
        foreach ($aux as $pagina){
            $pagina->setConteudo(html_entity_decode(strip_tags($pagina->getConteudo()),ENT_COMPAT, 'UTF-8'));
            $result[] = $pagina;
        }
        
        return $result;
    }
    
    /**
     * 
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param array $user
     * @return array
     */
    public function getBuscaPaginaEstatica($limit, $offset, $filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT pa FROM Entity\PaginaEstatica pa JOIN pa.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND Unaccent(LOWER(pa.titulo)) LIKE Unaccent(LOWER(:titulo)) ";
            $parametros['titulo'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND pa.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (pa.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }


        //Verifica se o usuário logado é da sede
        if (!$user['sede']) {
            $dql .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }

        //Caso o usuário for sede, poderá buscar por sites
        if (!empty($filtro['site'])) {
            $dql .= " AND sit.id = :id_site ";
            $parametros['id_site'] = $filtro['site'];
        }


        //Ordena os dados
        //$dql .= " ORDER BY pa.dataCadastro DESC ";
        $dql .= " ORDER BY pa.titulo ASC ";
        

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
//        die($query->getSql());
        return $query->getResult();
    }

    /**
     * 
     * @param array $filtros
     * @param array $user
     * @return array
     */
    public function getTotalBuscaPaginaEstatica($filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT COUNT(DISTINCT pa) total FROM Entity\PaginaEstatica pa JOIN pa.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(CONCAT(pa.titulo, pa.conteudo)) LIKE :titulo ";
            $parametros['titulo'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND pa.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (pa.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }


        //Verifica se o usuário logado é da sede
        if (!$user['sede']) {
            $dql .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }

        //Caso o usuário for sede, poderá buscar por sites
        if (!empty($filtro['site'])) {
            $dql .= " AND sit.id = :id_site ";
            $parametros['id_site'] = $filtro['site'];
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $resultado = $query->getResult();
        return $resultado[0]['total'];
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    public function getPaginaIds($ids)
    {
        $dql = "SELECT pag FROM Entity\PaginaEstatica pag WHERE pag.id IN ({$ids}) ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    public function getCompartilhadosById($id) 
    {
        $em = $this->getEntityManager();
        $compartilhado = 0;
        
        $sitesVinculados = $em->find("Entity\PaginaEstatica", $id)->getSites();
        $sitesPai = $em->find("Entity\PaginaEstatica", $id)->getPaiSites();
        
        if (count($sitesVinculados) > 0 && count($sitesPai) > 0) {
            
            foreach ($sitesVinculados as $vinculado) {
                $idsVinculados[] = $vinculado->getId();
            }
            
            foreach ($sitesPai as $pai) {
                $idsPai[] = $pai->getId();
            }
            
            foreach ($idsVinculados as $vinculado) {
                if (!in_array($vinculado, $idsPai) && in_array($vinculado, $_SESSION['user']['subsites'])) {
                    $compartilhado = 1;
                }
            }
        }
        
        return $compartilhado;
    }
    
    public function getCompartilhados() {
        
        $em = $this->getEntityManager();
        
        foreach ($_SESSION['user']['subsites'] as $sitesUser) {
            
            $agendasVinculadas = $em->find("Entity\Site", $sitesUser)->getPaginasEstaticas();
            $agendasPai = $em->find("Entity\Site", $sitesUser)->getPaiPaginas();
            
            foreach ($agendasVinculadas as $vinculada) {
                $vinculados[] = $vinculada->getId();
            }

            foreach ($agendasPai as $pai) {
                $pais[] = $pai->getId();
            }
            
        }
       
        $idsVinculados = array_unique($vinculados);
        $idsPai = array_unique($pais);
        
        foreach ($idsVinculados as $vinculado) {
            if (!in_array($vinculado, $idsPai)) {
                $retorno[] = $vinculado;
            }
        }
        
        if (!empty($retorno)) {
            $ret = array_unique($retorno);
        } else {
            $ret = 0;
        }
        
        return $ret;
        
    }
    
    // BUSCA OS REGISTROS COMPARTILHADOS
    public function getRegistrosCompartilhados()
    {
        $em = $this->getEntityManager();
        
        $agendasPai = $em->find("Entity\Site", $_REQUEST['site'])->getPaginasEstaticas();
        $vinculados = $this->getCompartilhados();
        
        if (empty($vinculados)) {
            return 0;
        }
        
        foreach ($agendasPai as $pai) {
            if (in_array($pai->getId(), $vinculados)) {
                $retorno[] = $pai->getId();
            }
        }
        
        return $retorno;
    }
}
