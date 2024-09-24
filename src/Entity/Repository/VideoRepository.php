<?php

namespace Entity\Repository;

/**
 * Description of VideoRepository
 *
 * @author Eduardo
 */
class VideoRepository extends BaseRepository
{
    public function getUltimosVideos($site = null, $pagNumero = 1, $pagMaximo = 20)
    {
        $query = $this->createQueryBuilder('v')
                    ->distinct()
                    ->join('v.sites', 'sites')
                    ->where('v.publicado = 1')
                    ->andWhere('v.dataInicial < :today')
                    ->andWhere('v.dataFinal > :today OR v.dataFinal IS NULL')
                    ->orderBy('v.dataInicial', 'DESC')
                    ->setParameter('today', new \DateTime('now'));

        if (!empty($site)) {
            $query->andWhere('sites.id = :siteId')
                  ->setParameter('siteId', $site->getId());
        } else {
            $query->andWhere("sites.sigla = 'SEDE'");
        }
        $query->setFirstResult(($pagNumero * $pagMaximo) - $pagMaximo)
            ->setMaxResults($pagMaximo);
        try {
            $return['videos'] = $query->getQuery()->getResult();
            $return['pagina'] = $query->getQuery();
            return $return;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna o vídeo em destaque, por subsite.
     *
     * @param \Entity\Site $site
     * @return \Entity\Video
     */
    public function getVideoDestaque($site = NULL)
    {
        
        $query = $this->createQueryBuilder('v')
                      ->join('v.videosSite', 'videoOrdem')
                      ->join('videoOrdem.site', 'site')
                      ->where('v.publicado = 1')
                      ->andWhere('v.dataInicial < :today')
                      ->andWhere('v.dataFinal > :today OR v.dataFinal IS NULL')
                      ->setParameter('today', new \DateTime('now'));
        
        if (!empty($site)) {
            $query->andWhere('site.id = :siteId')
                  ->setParameter('siteId', $site->getId());
        } else {
            $query->andWhere("site.sigla = 'SEDE'");
        }
        
        $query->orderBy('videoOrdem.ordem', 'ASC');

        try {
            return $query->getQuery()
                  ->setMaxResults(1)
                  ->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param \Entity\Site $site
     * @param integer $limit
     * @return array
     */
    public function getConteudoHome($site = NULL)
    {
        $query = $this->createQueryBuilder('n')
                      ->distinct()
                      ->join('n.sites', 's')
                      ->where('n.publicado = 1')
                      ->andWhere('n.dataInicial < :today')
                      ->andWhere('n.dataFinal > :today OR n.dataFinal IS NULL');

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.sigla = :sigla")
                  ->setParameter('sigla', $site->getSigla());
        } else {
            $query->andWhere("s.sigla IN ('SEDE')");
        }

        try {
            return $query->orderBy('n.dataInicial', 'DESC')
                         ->setMaxResults(1)
                         ->setParameter('today', new \DateTime('now'))
                         ->getQuery()
                         ->useQueryCache(TRUE)
                         ->useResultCache(TRUE, CACHE_LIFE_TIME)
                         ->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return FALSE;
        }
    }

    /**
     *
     * @return int
     */
    public function countAll($user)
    {
        //monta o dql
        $dql = "SELECT COUNT(DISTINCT vid) FROM Entity\Video vid  JOIN vid.sites sit ";

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

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param array $user
     * @return array
     */
    public function getBuscaVideo($limit, $offset, $filtro, array $user)
    {
        //Estrutura o dql da busca
        $dql = "SELECT DISTINCT vid FROM Entity\Video vid JOIN vid.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND (LOWER(vid.nome) LIKE :nome OR LOWER(vid.link) LIKE :link)";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
            $parametros['link'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND vid.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (vid.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_final'])) {
            $dql .= " AND vid.dataCadastro < :data_final ";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_inicial'])) {
            $dql .= " AND (vid.dataCadastro > :data_inicial ) ";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
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
        $dql .= " ORDER BY vid.dataCadastro DESC ";

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
    public function getTotalBuscaVideo($filtro, array $user)
    {
        //Estrutura o dql da busca
        $dql = "SELECT COUNT(DISTINCT vid) total FROM Entity\Video vid JOIN vid.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND (LOWER(vid.nome) LIKE :nome OR LOWER(vid.link) LIKE :link)";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
            $parametros['link'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND vid.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (vid.dataCadastro BETWEEN :data_inicial AND :data_final  )";
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
     * @param string $nome
     * @param array $user
     * @return boolean
     */
    public function validarNome($nome, array $user, $id = 0)
    {
        //Monta a dql
        $dql = "SELECT vid FROM Entity\Video vid JOIN vid.sites sit WHERE vid.nome = :nome ";
        $parametros["nome"] = $nome;

        //Verifica se existe excessão
        if ($id != 0) {
            $dql .= " AND vid.id != :id ";
            $parametros["id"] = $id;
        }

       //Verifica se o usuário logado é da sede
        if (!$user['sede']) {
            $dql .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }

        //Passa os parâmetros e executa a dql
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        //Verifica se algum resultado foi encontrado
        if ($query->getResult()) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Retorna o video publicado, apenas com os relacionados que também estão publicados.
     *
     * @param integer $id
     * @return object|boolean
     */
    /*
     
    MOTIVO:
     O método abaixo foi comentado, pois não existe mais a necessidade de
     consultar o vídeo com seus vídeos relacionados publicados. Agora o 
     método deve apenas retornar o vídeo (sem envolver os relacionados) e
     os relacionados >>publicados<< serão consultados em outro método.
    
    public function getPublicado($id, $verificarPublicado = true)
    {
        try {
            $query = $this->createQueryBuilder('v')
                            ->select('v, vr')
                            ->distinct()
                            ->leftJoin("v.relacionados", 
                                    "vr", 
                                    \Doctrine\ORM\Query\Expr\Join::WITH, 
                                    "vr.publicado = 1 "
                                    . "AND vr.dataInicial <= :today "
                                    . "AND (vr.dataFinal >= :today OR vr.dataFinal IS NULL)")
                            ->andWhere('v.id = :id');

            if ($verificarPublicado) {
                $query->andWhere('v.publicado = 1')
                      ->andWhere('v.dataInicial <= :today')
                      ->andWhere('v.dataFinal >= :today OR v.dataFinal IS NULL')
//                      ->andWhere('vr.publicado = 1 OR vr.publicado IS NULL')
//                      ->andWhere('vr.dataInicial <= :today')
//                      ->andWhere('vr.dataFinal >= :today OR vr.dataFinal IS NULL')
                      ->setParameter('today', new \DateTime('now'));
            }

            return $query->setParameter('id', $id)
                         ->getQuery()
                         ->useQueryCache(true)
                         ->useResultCache(true, CACHE_LIFE_TIME)
                         ->getSingleResult();
        } catch (NoResultException $e) {
            return false;
        }
    }*/
    
     
     public function getCompartilhadosById($id) 
    {
        $em = $this->getEntityManager();
        $compartilhado = 0;
        
        $sitesVinculados = $em->find("Entity\Video", $id)->getSites();
        $sitesPai = $em->find("Entity\Video", $id)->getPaiSites();
        
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
            $videosVinculadas = $em->find("Entity\Site", $sitesUser)->getVideos();
            $videosPai = $em->find("Entity\Site", $sitesUser)->getPaiVideos();
            
            if($videosVinculadas){
                foreach ($videosVinculadas as $vinculada) {
                    $vinculados[] = $vinculada->getId();
                }
            }
            if($videosPai){
                foreach ($videosPai as $pai) {
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
        
        $videosPai = $em->find("Entity\Site", $_REQUEST['site'])->getVideos();
        $vinculados = $this->getCompartilhados();
        
        if (empty($vinculados)) {
            return 0;
        }
        
        foreach ($videosPai as $pai) {
            if (in_array($pai->getId(), $vinculados)) {
                $retorno[] = $pai->getId();
            }
        }
        
        return $retorno;
    }
}
