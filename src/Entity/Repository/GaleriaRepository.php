<?php

namespace Entity\Repository;

/**
 * Description of GaleriaRepository
 *
 * @author Eduardo
 */
class GaleriaRepository extends BaseRepository
{

    public function getPublicado($id)
    {
        $query = $this->createQueryBuilder('e')
                      ->distinct()
                      ->andWhere('e.publicado = 1')
                      ->andWhere('e.dataInicial < :today')
                      ->andWhere('e.dataFinal > :today OR e.dataFinal IS NULL')
                      ->andWhere('e.id = :id');

        return $query->setParameter('today', new \DateTime('now'))
                     ->setParameter('id', $id)
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME)
                     ->getSingleResult();
    }

    public function getConteudoInterna($site = NULL, $pagNumero, $pagMaximo)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->join('a.sites', 's')
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.sigla = :sigla")
                  ->setParameter('sigla', $site->getSigla());
        } else {
            $query->andWhere("s.sigla IN ('SEDE')");
        }

        return $query->orderBy('a.dataInicial', 'DESC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    
    
    public function getConteudoInternaOrder($site = NULL, $pagNumero, $pagMaximo)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->join('a.sites', 's')
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.sigla = :sigla")
                  ->setParameter('sigla', $site->getSigla());
        } else {
            $query->andWhere("s.sigla IN ('SEDE')");
        }

        return $query->orderBy('a.ordem', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
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
        $dql = "SELECT COUNT(DISTINCT ga) FROM Entity\Galeria ga  JOIN ga.sites sit ";

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

    public function getBuscaGaleriaSubsite($site, $limit = 3)
    {
        $dql = "SELECT ga FROM Entity\Galeria ga JOIN ga.sites sit JOIN ga.galeriasSite gs WHERE 1 = 1 ";
        $dql .= " AND sit.id = :id_site and ga.publicado = 1 ";
        $dql .= " ORDER BY gs.ordem ASC ";

        $query = $this->getEntityManager()->createQuery($dql);
        $parametros['id_site'] = $site;
        $query->setParameters($parametros);

        return $query->getResult();
    }
    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param \Entity\Usuario $user
     * @return array
     */
    public function getBuscaGaleria($limit, $offset, $filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT ga FROM Entity\Galeria ga JOIN ga.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(CONCAT(ga.titulo, COALESCE(ga.descricao, ''))) LIKE :titulo ";
            $parametros['titulo'] = "%" . $filtro['busca'] . "%";
        }

        if (isset($filtro['status'])) {
            if ($filtro['status'] !== "") {
                $dql .= " AND ga.publicado = :publicado ";
                $parametros['publicado'] = $filtro['status'];
            }
        }
        
        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (ga.dataCadastro BETWEEN :data_inicial AND :data_final)";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Caso o usuário for sede, poderá buscar por sites
        if (!empty($filtro['site']) and $filtro['site'] != '') {
            $dql .= " AND sit.id = :id_site ";
            $parametros['id_site'] = $filtro['site'];
        }
        
        //Verifica se o usuário logado é da sede
        if (!$user['sede']) {
            $dql .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }
        
        //Ordena os dados
        $dql .= " ORDER BY ga.dataCadastro DESC ";
        
        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        
        if(($limit + $offset) > 0){
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
//        die($query->getSql());
        return $query->getResult();
    }

     /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param \Entity\Usuario $user
     * @return array
     */
    public function getBuscaGaleriaSede($limit, $offset, $filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT ga FROM Entity\Galeria ga JOIN ga.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(CONCAT(ga.titulo, ga.descricao)) LIKE :titulo ";
            $parametros['titulo'] = "%" . $filtro['busca'] . "%";
        }

        if (isset($filtro['status'])) {
            if ($filtro['status'] !== "") {
                $dql .= " AND ga.publicado = :publicado ";
                $parametros['publicado'] = $filtro['status'];
            }
        }
        
        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (ga.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        
        $dql .= " AND sit.sigla = :sigla ";
        $parametros['silga'] = "SEDE";
        
        //Ordena os dados
        $dql .= " ORDER BY ga.dataCadastro DESC ";
        
        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        
        if(($limit + $offset) > 0){
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
//        die($query->getSql());
        return $query->getResult();
    }

    /**
     *
     * @param array $ids
     * @return array
     */
    public function getGaleriaIds($ids)
    {
        $dql = "SELECT gal FROM Entity\Galeria gal WHERE gal.id IN ({$ids}) ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    /**
     *
     * @param array $ids
     * @return array
     */
    public function findIdsImgs($id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        $query->select('g.imagem')
                ->from('Entity\GaleriaImagem', 'g')
                ->where('g.galeria = '.$id)
                ->orderBy('g.ordem', 'ASC');

        $result = $query->getQuery()->getResult();
        return $result;
    }

    /**
     * 
     * @param $id
     * @return integer
     */
    public function setOrdemFototeca($idFototeca, $idGaleria, $ordem)
    {
    	$this->getEntityManager()
        	->createQueryBuilder()
           	->update('Entity\FototecaGaleria', 'c')
            ->set('c.ordem', $ordem)
            ->where('c.fototeca = '.$idFototeca.' and c.galeria = '.$idGaleria)
            ->getQuery()
            ->execute();
    }
    
    /**
     * 
     * @param array $ids
     * @return array
     */
    public function getGaleriasIdsFototeca($id)
    {
    	return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('e.idGaleria')
                ->from('Entity\FototecaGaleria', 'e')
                ->where('e.idFototeca = '. $id)
                ->orderBy('e.ordem', 'asc')
                ->getQuery()
                ->getResult();
    }
    
    
    
     public function getCompartilhadosById($id) 
    {
        $em = $this->getEntityManager();
        $compartilhado = 0;
        
        $sitesVinculados = $em->find("Entity\Galeria", $id)->getSites();
        $sitesPai = $em->find("Entity\Galeria", $id)->getPaiSites();
        
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
            $galeriaVinculadas = $em->find("Entity\Site", $sitesUser)->getGalerias();
            $galeriaPai = $em->find("Entity\Site", $sitesUser)->getPaiGalerias();
            
            if($galeriaVinculadas){
                foreach ($galeriaVinculadas as $vinculada) {
                    $vinculados[] = $vinculada->getId();
                }
            }
            if($galeriaPai){
                foreach ($galeriaPai as $pai) {
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
        
        $galeriasPai = $em->find("Entity\Site", $_REQUEST['site'])->getGalerias();
        $vinculados = $this->getCompartilhados();
        
        if (empty($vinculados)) {
            return 0;
        }
        
        foreach ($galeriasPai as $pai) {
            if (in_array($pai->getId(), $vinculados)) {
                $retorno[] = $pai->getId();
            }
        }
        
        return $retorno;
    }

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param \Entity\Usuario $user
     * @return array
     */
    public function getTotalBuscaGaleria($filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT COUNT(DISTINCT ga) total FROM Entity\Galeria ga JOIN ga.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(CONCAT(ga.titulo, ga.descricao)) LIKE :titulo ";
            $parametros['titulo'] = "%" . $filtro['busca'] . "%";
        }

        if (isset($filtro['status'])) {
            if ($filtro['status'] !== "") {
                $dql .= " AND ga.publicado = :publicado ";
                $parametros['publicado'] = $filtro['status'];
            }
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (ga.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Caso o usuário for sede, poderá buscar por sites
        if (!empty($filtro['site'])) {
            $dql .= " AND sit.id = :id_site ";
            $parametros['id_site'] = $filtro['site'];
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

}
