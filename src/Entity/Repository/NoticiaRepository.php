<?php

namespace Entity\Repository;

use Helpers\Session;

/**
 * Description of NoticiaRepository
 * 
 * @author Luciano
 */
class NoticiaRepository extends BaseRepository
{

    /**
     *
     * @var array
     */
    private $where;

    /**
     *
     * @var Session
     */
    private $session;
    
    

    /**
     *
     * @param integer $categoria ID da categoria
     * @param string $busca Query de busca.
     * @return string
     */
    public function getQueryPortal($site = NULL, $limit = NULL, $notInIds = NULL, $filters = NULL, $featured = FALSE)
    {
        $query = $this->createQueryBuilder('e')
                      ->distinct()
                      ->join('e.sites', 's')
                      ->andWhere('e.publicado = 1')
                      ->andWhere('e.flagNoticia IS NULL OR e.flagNoticia = :parametro')
                      ->andWhere('e.dataInicial < :today')
                      ->andWhere('e.dataFinal > :today OR e.dataFinal IS NULL')
                      ->setParameter('parametro', "0");

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.id = :site")
                  ->setParameter('site', $site->getId());
        } else {
            $query->andWhere("s.sigla = 'SEDE'");
        }

        if (!empty($notInIds)) {
            $query->andWhere('e.id NOT IN (:ids)')
                  ->setParameter('ids', $notInIds);
        }

        if ($featured === true) {
            $query->andWhere('e.imagem IS NOT NULL');
        }

        if (!empty($filters['data'])) {
            $date = new \DateTime($filters['data']);
            $dateStartDay = $date->format('Y-m-d') . ' 00:00:00';
            $dateEndDay = $date->format('Y-m-d') . ' 23:59:59';
            $query->andWhere('e.dataInicial >= :dataInicioDia')
                  ->andWhere('e.dataInicial <= :dataFinalDia')
                  ->setParameter('dataInicioDia', $dateStartDay)
                  ->setParameter('dataFinalDia', $dateEndDay);
        }

        if (!empty($filters['palavraChave'])) {
            $conditions = $query->expr()->orX(
                'LOWER(e.conteudo) LIKE LOWER(:palavraChave)',
                'LOWER(e.titulo) LIKE LOWER(:palavraChave)'
            );
            $query->andWhere($conditions)
                  ->setParameter('palavraChave', '%' . $filters['palavraChave'] . '%');
        }

        if ($limit != null) {
            $query->setMaxResults($limit);
        }

        $today = new \DateTime('now');
        return $query->orderBy('e.dataInicial', 'DESC')
                     ->setParameter('today', $today)
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    /**
     *
     * @param \Entity\Site $site
     * @return array
     */
    public function getConteudoHome($site = NULL, $limit = 4, $notInIds = null)
    {
        $query = $this->createQueryBuilder('n')
                      ->distinct()
                      ->join('n.sites', 's')
                      ->where('n.publicado = 1')
                      ->andWhere('n.flagNoticia IS NULL OR n.flagNoticia = :parametro')
                      ->andWhere('n.dataInicial < :today')
                      ->andWhere('n.dataFinal > :today OR n.dataFinal IS NULL')
                      ->setParameter('parametro', "0");

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.sigla = :sigla")
                    ->andWhere($query->expr()->isNotNull("n.imagem"))
                  ->setParameter('sigla', $site->getSigla());
        } else {
            $query->andWhere("s.sigla IN ('SEDE')");
        }
        
        if(!is_null($notInIds) and !empty($notInIds)){
             $query->andWhere("n.id NOT IN ({$notInIds})");
        }

        return $query->orderBy('n.dataInicial', 'DESC')
                     ->setParameter('today', new \DateTime('now'))
                     ->setMaxResults($limit)
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME)
                     ->getResult();
    }

    /**
     * Retorna a Query dos Conselheiros
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryNoticias()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->distinct()
                ->select("N")
                ->from($this->getEntityName(), "N")
                ->join("N.sites", "sites");


        return $query;
    }

    /**
     * Seta  o Where da Busca
     *
     * @param \Doctrine\ORM\QueryBuilder
     * @param array $where
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function setWhere(\Doctrine\ORM\QueryBuilder $query, array $where = array())
    {
        if (empty($this->where)) {
            $this->where = $where;
        }

        //$user = $this->session->get("user");
        $user = $_SESSION['user'];
        $busca = mb_strtolower($this->where['busca'], "UTF-8");
        $status = $this->where['status'];
        $site = $this->where['site'];
        $data_inicial = $this->where['data_inicial'];
        $data_final = $this->where['data_final'];

        if (!empty($busca)) {
            $query->andWhere(
                    $this->getEntityManager()
                            ->createQueryBuilder()
                            ->orWhere($query->expr()->like($query->expr()->lower("N.titulo"), ":busca"))
                            ->orWhere($query->expr()->like($query->expr()->lower("N.conteudo"), ":busca"))
                            ->getDQLPart("where")
            );
            $query->setParameter("busca", "%{$busca}%");
        }

        if ($status !== "") {
            if ($status === "1" || $status === "0") {
                $query->andWhere($query->expr()->eq("N.publicado", ":status"));
                $query->setParameter("status", $status);
                $query->andWhere('N.flagNoticia IS NULL OR N.flagNoticia = :parametro');
                $query->setParameter('parametro', "0");
            // else status === 2 query de flag invisivel
            } elseif ($status === "2") {
                $st = "1";
                $query->andWhere('N.flagNoticia = :parametro');
                $query->setParameter("parametro", "1");
                $query->andWhere($query->expr()->eq("N.publicado", ":status"));
                $query->setParameter("status", $st);
            }
        }

        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere($query->expr()->between("N.dataCadastro", "'{$data_inicial} 00:00'", "'{$data_final} 23:59'"));
        }


        if ($user['sede']) {
            if ($site > 0) {
                $query->andWhere($query->expr()->eq("sites.id", ":site"));
                $query->setParameter("site", $site);
            }
        } else {
            //$query->andWhere($query->expr()->eq("sites.id", ":site"));
            $query->andWhere("sites.id IN(:sites)");
            $query->setParameter("sites", $user['subsites']);
        }

        return $query;
    }

    /**
     *
     * @param type $limit
     * @param type $offset
     * @param array $filtro
     * @param \Helpers\Session $session
     * @return type
     */
    public function getNoticias($limit, $offset, array $filtro, Session $session)
    {
        $this->session = $session;

        $query = $this->getQueryNoticias();

        $query = $this->setWhere($query, $filtro);
        $query->orderBy('N.dataCadastro', 'DESC');
        if ($limit > 0) {
            $query->setMaxResults($limit);
        }
        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    /**
     * Número total de Notícias com filtro
     *
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryNoticias();
        
        $query->select($query->expr()->countDistinct("N"));

        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Numero total de Notícias do Banco
     *
     * @return int
     */
    public function countAll()
    {
        //$user = $this->session->get("user");
        $user = $_SESSION['user'];
        $query = $this->getQueryNoticias();

        $query->select($query->expr()->countDistinct("N"));

        if (!$user['sede']) {
            $query->andWhere("sites.id IN(:sites)");
            $query->setParameter("sites", $user['subsites']);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    public function getNoticiaIds($ids)
    {
        // $dql = "SELECT pag FROM Entity\PaginaEstatica pag WHERE pag.id IN ({$ids}) ";
        $dql = "SELECT pag FROM Entity\Noticia pag WHERE pag.id IN ({$ids}) ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    public function getCompartilhadosById($id) 
    {
        $em = $this->getEntityManager();
        $compartilhado = 0;
        
        $sitesVinculados = $em->find("Entity\Noticia", $id)->getSites();
        $sitesPai = $em->find("Entity\Noticia", $id)->getPaiSites();

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
            $noticiaVinculadas = $em->find("Entity\Site", $sitesUser)->getNoticias();
            $noticiaPai = $em->find("Entity\Site", $sitesUser)->getPaiNoticias();
            
            if($noticiaVinculadas){
                foreach ($noticiaVinculadas as $vinculada) {
                    $vinculados[] = $vinculada->getId();
                }
            }
            if($noticiaPai){
                foreach ($noticiaPai as $pai) {
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
        
        $noticiasPai = $em->find("Entity\Site", $_REQUEST['site'])->getNoticias();
        $vinculados = $this->getCompartilhados();
        
        if (empty($vinculados)) {
            return 0;
        }
        
        foreach ($noticiasPai as $pai) {
            if (in_array($pai->getId(), $vinculados)) {
                $retorno[] = $pai->getId();
            }
        }
        
        return $retorno;
    }
    
    
    

}
