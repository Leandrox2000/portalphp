<?php

namespace Entity\Repository;

/**
 * Description of LegislacaoRepository
 *
 * @author Eduardo
 */
class LegislacaoRepository extends BaseRepository
{

    /**
     *
     * @param integer $categoria ID da categoria
     * @param string $busca Query de busca.
     * @return string
     */
    public function getQueryPortal($site = NULL, $categoria = NULL, $busca = NULL, $deData = NULL, $ateData = NULL)
    {
        $date = new \Helpers\DatetimeFormat();
        $query = $this->createQueryBuilder('e')
                      ->distinct()
                      ->join('e.sites', 's')
                      ->andWhere('e.publicado = 1')
                      ->andWhere('e.dataInicial < :today')
                      ->andWhere('e.dataFinal > :today OR e.dataFinal IS NULL');

        if (!empty($categoria)) {
            $query->andWhere('e.categoriaLegislacao = :categoria')
                  ->setParameter('categoria', $categoria);
        }

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.id = :site")
                  ->setParameter('site', $site->getId());
        } else {
            $query->andWhere("s.sigla = 'SEDE'");
        }

        if (!empty($busca)) {
            $query->andWhere('LOWER(CONCAT(e.descricao, e.titulo)) LIKE LOWER(:busca)')
                  ->setParameter('busca', '%' . $busca . '%');
        }

        if (!empty($deData)) {
            $deData = $date->formatUs($deData);
            $query->andWhere('e.dataLegislacao >= :deData')
                  ->setParameter('deData', new \DateTime($deData));
        }

        if (!empty($ateData)) {
            $ateData = $date->formatUs($ateData);
            $query->andWhere('e.dataLegislacao <= :ateData')
                  ->setParameter('ateData', new \DateTime($ateData));
        }

        $today = new \DateTime('now');
        return $query->orderBy('e.dataInicial', 'DESC')
                     ->setParameter('today', $today)
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }
    
    
    
    public function getQueryPortalASC($site = NULL, $categoria = NULL, $busca = NULL, $deData = NULL, $ateData = NULL)
    {
        $date = new \Helpers\DatetimeFormat();
        $query = $this->createQueryBuilder('e')
                      ->distinct()
                      ->join('e.sites', 's')
                      ->andWhere('e.publicado = 1')
                      ->andWhere('e.dataInicial < :today')
                      ->andWhere('e.dataFinal > :today OR e.dataFinal IS NULL');

        if (!empty($categoria)) {
            $query->andWhere('e.categoriaLegislacao = :categoria')
                  ->setParameter('categoria', $categoria);
        }

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.id = :site")
                  ->setParameter('site', $site->getId());
        } else {
            $query->andWhere("s.sigla = 'SEDE'");
        }

        if (!empty($busca)) {
            $query->andWhere('LOWER(CONCAT(e.descricao, e.titulo)) LIKE LOWER(:busca)')
                  ->setParameter('busca', '%' . $busca . '%');
        }

        if (!empty($deData)) {
            $deData = $date->formatUs($deData);
            $query->andWhere('e.dataLegislacao >= :deData')
                  ->setParameter('deData', new \DateTime($deData));
        }

        if (!empty($ateData)) {
            $ateData = $date->formatUs($ateData);
            $query->andWhere('e.dataLegislacao <= :ateData')
                  ->setParameter('ateData', new \DateTime($ateData));
        }

        $today = new \DateTime('now');
        // return $query->orderBy('e.dataInicial', 'ASC')
        // deve mostrar da mais recente para a mais antiga
        return $query->orderBy('e.dataLegislacao', 'DESC')
                     ->setParameter('today', $today)
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }


    /**
     *
     * @return int
     */
    public function countAll($user)
    {
        //monta o dql
        $dql = "SELECT COUNT(DISTINCT leg) FROM Entity\Legislacao leg  JOIN leg.sites sit ";

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

    public function getBuscaLegislacaoSubsite($site, $limit = 3)
    {
        $dql = "SELECT DISTINCT leg FROM Entity\Legislacao leg JOIN leg.sites sit WHERE 1 = 1 AND leg.publicado = 1";
        $dql .= " AND sit.id = :site";
        // $dql .= " ORDER BY leg.dataLegislacao ASC";
        // deve mostrar da mais recente para a mais antiga
        $dql .= " ORDER BY leg.dataLegislacao DESC";
                
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('site' , $site);
        
        
        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
        
        return $query->getResult();
    }
    
    
    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param array $user
     * @return array
     */
    public function getBuscaLegislacao($limit, $offset, $filtro, array $user)
    {
        //Estrutura o dql da busca
        $dql = "SELECT DISTINCT leg FROM Entity\Legislacao leg JOIN leg.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND (LOWER(CONCAT(leg.titulo, leg.descricao)) LIKE :titulo OR LOWER(leg.url) LIKE :url)";
            $parametros['titulo'] = "%" . $filtro['busca'] . "%";
            $parametros['url'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND leg.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if ($filtro['site'] !== "") {
            $dql .= " AND sit.id = :site";
            $parametros['site'] = $filtro['site'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (leg.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Verifica se o usuário logado é da sede
        if (!$user['sede']) {
            $dql .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }

        //Ordena os dados
        // $dql .= " ORDER BY leg.dataCadastro DESC ";
        $dql .= " ORDER BY leg.dataLegislacao DESC ";

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param array $user
     * @return array
     */
    public function getTotalBuscaLegislacao($filtro, array $user)
    {
        //Estrutura o dql da busca
        $dql = "SELECT COUNT(DISTINCT leg) total FROM Entity\Legislacao leg JOIN leg.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND (LOWER(CONCAT(leg.titulo, leg.descricao)) LIKE :titulo OR LOWER(leg.url) LIKE :url)";
            $parametros['titulo'] = "%" . $filtro['busca'] . "%";
            $parametros['url'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND leg.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if ($filtro['site'] !== "") {
            $dql .= " AND sit.id = :site";
            $parametros['site'] = $filtro['site'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (leg.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }


        //Verifica se o usuário logado é da sede
        if (!$user['sede']) {
            $dql .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

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
        $dql = "SELECT count(leg) total FROM Entity\Legislacao leg JOIN leg.categoriaLegislacao cl WHERE cl.id = :id_categoria  ";
        $parametros['id_categoria'] = $categoria;

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }

    /**
     *
     * @return array
     */
    public function getArquivosLegislacao($ids)
    {
        $dql = "SELECT leg.arquivo FROM Entity\Legislacao leg WHERE leg.id IN(" . implode(',', $ids) . ")";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    
     public function getCompartilhadosById($id) 
    {
        $em = $this->getEntityManager();
        $compartilhado = 0;
        
        $sitesVinculados = $em->find("Entity\Legislacao", $id)->getSites();
        $sitesPai = $em->find("Entity\Legislacao", $id)->getPaiSites();
        
//        echo "<pre>";
//           \Doctrine\Common\Util\Debug::dump($sitesPai);
//
//        echo "</pre>";
//        die();
        
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
            $legislacaoVinculadas = $em->find("Entity\Site", $sitesUser)->getLegislacoes();
            $legislacaoPai = $em->find("Entity\Site", $sitesUser)->getPaiLegislacao();
            
            if($legislacaoVinculadas){
                foreach ($legislacaoVinculadas as $vinculada) {
                    $vinculados[] = $vinculada->getId();
                }
            }
            if($legislacaoPai){
                foreach ($legislacaoPai as $pai) {
                    $pais[] = $pai->getId();
                }
            }
            
        }

        if($vinculados) $idsVinculados = array_unique($vinculados);
        if($pais)       $idsPai = array_unique($pais);
        
        if($idsVinculados){
            foreach ($idsVinculados as $vinculado) {
                if(!$idsPai){
                    $retorno[] = $vinculado;
                }else{
                    if (!in_array($vinculado, $idsPai)) {
                        $retorno[] = $vinculado;
                    }
                }
            }
        }

        if (!empty($retorno)) {
            //$ret = array_unique($retorno[0]);
            $ret = $retorno;
        } else {
            $ret = 0;
        }
        
        return $ret;
        
    }
    
    // BUSCA OS REGISTROS COMPARTILHADOS
    public function getRegistrosCompartilhados()
    {
        $em = $this->getEntityManager();
        
        $legislacoesPai = $em->find("Entity\Site", $_REQUEST['site'])->getLegislacoes();
        $vinculados = $this->getCompartilhados();
        
        if (empty($vinculados)) {
            return 0;
        }
        
        foreach ($legislacoesPai as $pai) {
            if (in_array($pai->getId(), $vinculados)) {
                $retorno[] = $pai->getId();
            }
        }
        
        return $retorno;
    }
    
    

}
