<?php

namespace Entity\Repository;

use Helpers\Session;

/**
 * Description of EditalRepository
 *
 * @author Luciano
 */
class EditalRepository extends BaseRepository
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
     * Retorna a Query dos Editais
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryEditais()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select("E")
                ->distinct()
                ->from($this->getEntityName(), "E")
                ->join("E.status", "S")
                ->join("E.categoria", "C")
                ->join("E.sites", "sites");


        return $query;
    }

    public function getBuscaEditalSubsite($site,$limit = 4){
        $dql = "SELECT DISTINCT e FROM Entity\Edital e JOIN e.sites sit WHERE 1 = 1 AND e.publicado = 1";
        $dql .= " AND sit.id = :site";
        
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('site' , $site);
        
        
        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
        
         #Tratar 
        $aux = $query->getResult();
        
        foreach ($aux as $edital){
            $edital->setConteudo(html_entity_decode(strip_tags($edital->getConteudo()),ENT_COMPAT, 'UTF-8'));
            $result[] = $edital;
        }
        
        return $result;
        
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
        //$_SESSION['user']['subsites']
        
        //$user = $this->session->get("user");
        $user = $_SESSION['user'];
        $busca = mb_strtolower($this->where['busca'], "UTF-8");
        $categoria = $this->where['categoria'];
        $editalStatus = $this->where['editalStatus'];
        $status = $this->where['status'];
        $site = $this->where['site'];
        $data_inicial = $this->where['data_inicial'];
        $data_final = $this->where['data_final'];

        if (!empty($busca)) {
            $query->andWhere(
                    $this->getEntityManager()
                            ->createQueryBuilder()
                            ->orWhere($query->expr()->like($query->expr()->lower("E.nome"), ":busca"))
                            ->orWhere($query->expr()->like($query->expr()->lower("E.conteudo"), ":busca"))
                            ->orWhere($query->expr()->like($query->expr()->lower("S.nome"), ":busca"))
                            ->orWhere($query->expr()->like($query->expr()->lower("C.nome"), ":busca"))
                            ->getDQLPart("where")
            );
            $query->setParameter("busca", "%{$busca}%");
        }

        if (!empty($categoria)) {
            $query->andWhere($query->expr()->eq("E.categoria", ":categoria"));
            $query->setParameter("categoria", $categoria);
        }

        if (!empty($editalStatus)) {
            $query->andWhere($query->expr()->eq("E.status", ":editalStatus"));
            $query->setParameter("editalStatus", $editalStatus);
        }

        if ($status !== "") {
            $query->andWhere($query->expr()->eq("E.publicado", ":status"));
            $query->setParameter("status", $status);
        }

        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere($query->expr()->between("E.dataCadastro", "'{$data_inicial} 00:00'", "'{$data_final} 23:59'"));
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
    public function getEditais($limit, $offset, array $filtro, Session $session)
    {
        $this->session = $session;

        $query = $this->getQueryEditais();

        $query = $this->setWhere($query, $filtro);
        $query->orderBy('E.dataCadastro', 'DESC');
        if($limit>0){
        $query->setMaxResults($limit);
        }
        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    /**
     * Número total de Editais com filtro
     *
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryEditais();

        $query->select($query->expr()->countDistinct("E"));

        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Numero total de Editais do Banco
     *
     * @return int
     */
    public function countAll()
    {
        //$user = $this->session->get("user");
        $user = $_SESSION['user'];
        $query = $this->getQueryEditais();

        $query->select($query->expr()->countDistinct("E"));

        if (!$user['sede']) {
            $query->andWhere("sites.id IN(:sites)");
            $query->setParameter("sites", $user['subsites']);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getConteudoInterna(\Entity\Site $site = null, $status = null, $categoria = null){
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->join('a.sites', 's')
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        if (!empty($status)) {
            $query->andWhere('a.status = :status')
                  ->setParameter('status', $status);
        }

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.id = :site")
                  ->setParameter('site', $site->getId());
        } else {
            $query->andWhere("s.sigla = 'SEDE'");
        }

        if (!empty($categoria)) {
            $query->andWhere('a.categoria = :categoria')
                  ->setParameter('categoria', $categoria);
        }

        return $query->orderBy('a.dataInicial', 'DESC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(true)
                     ->useResultCache(true, CACHE_LIFE_TIME);
    }
    
    /**
     * 
     * @param $id
     * @return integer
     */
    public function getEditalVinculado($id)
    {
        try {
            $query = $this->createQueryBuilder('e')
                        ->select('e.nome')
                        ->distinct()
                        ->andWhere('e.status = :id');

            return $query->setParameter('id', $id)
                        ->getQuery()
                        ->getResult();
        } catch (\Doctrine\ORM\NoResultExceptionption $e) {
            return false;
        }
    }
    
    public function getEditaisCount($filter){
        
        $query = $this->getEntityManager()
                    ->createQueryBuilder()
                    ->select('COUNT(E.id) AS length')
                    ->from($this->getEntityName(), 'E');
        
        $query->andWhere('E.publicado = 1');
        
        if(array_key_exists('subsite', $filter)) {
            
            $query->join('Entity\EditalSite', 'ES', 'WITH', 'E.id = ES.edital')
                ->andWhere('ES.site IN(:sites)')
                  ->setParameter('sites', $filter['subsite']);
        }
        
        if(array_key_exists('categoria', $filter)) {
            
            $query->andWhere('E.categoria = '.$filter['categoria']);
        }
        
        if(array_key_exists('status', $filter)) {
            
            $query->andWhere('E.status = '.$filter['status']);
        }
        
        return $query->getQuery()
                    ->useQueryCache(true)
                    ->useResultCache(true, CACHE_LIFE_TIME)
                    ->getSingleScalarResult();
        
    }
    
    public function getEditaisRaw($filter = array(), $start = 0, $limit = 10){

        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('E')
                        ->from($this->getEntityName(), 'E')
                        ->orderBy('E.dataCadastro', 'DESC')
                        ->setFirstResult($start)
                        ->setMaxResults($limit);
        
        $query->andWhere('E.publicado = 1');
        
        if(array_key_exists('subsite', $filter)) {
            
            $query->join('Entity\EditalSite', 'ES', 'WITH', 'E.id = ES.edital')
                ->andWhere('ES.site IN(:sites)')
                  ->setParameter('sites', $filter['subsite']);
        }
        
        if(array_key_exists('categoria', $filter)) {

            $query->andWhere('E.categoria = '.$filter['categoria']);
        }

        if(array_key_exists('status', $filter)) {
            
            /**
             * Era para ser algo mais ou menos assim :)
             * Se a coluna nu_ordem_column for 1, a ordenação é feita pela data de cadastro
             * Se a column nu_ordem_column for 2, a ordenção é feita pela data de final
             * Essa coluna fica em outra tabela, então a tabela estrangéria ganha ordenação
             * conforme o valor da tabela primária
             * 
             *  SELECT * FROM tb_edital AS e
             *  INNER JOIN tb_edital_status AS s
             *  ON e.id_edital_status = s.id_status_categoria
             *  ORDER BY CASE 
             *      WHEN s.nu_ordem_column = 2 THEN e.dt_final
             *      ELSE e.dt_cadastro
             *  END ASC
             */
            $query->join('Entity\EditalStatus', 'S', 'WITH', 'E.status = S.id')
                ->andWhere('E.status = '.$filter['status'])
                ->addSelect('(CASE WHEN S.column = 2 THEN E.dataFinal ELSE E.dataCadastro END) AS HIDDEN ORD')
                ->orderBy('ORD', 'DESC');
        }

        return $query->getQuery()->getResult(); 
    }
    
    
      public function getCompartilhadosById($id) 
    {
        $em = $this->getEntityManager();
        $compartilhado = 0;
        
        $sitesVinculados = $em->find("Entity\Edital", $id)->getSites();
        $sitesPai = $em->find("Entity\Edital", $id)->getPaiSites();
        
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
            $editalVinculadas = $em->find("Entity\Site", $sitesUser)->getEditais();
            $editalPai = $em->find("Entity\Site", $sitesUser)->getPaiEditais();
            
            if($editalVinculadas){
                foreach ($editalVinculadas as $vinculada) {
                    $vinculados[] = $vinculada->getId();
                }
            }
            if($editalPai){
                foreach ($editalPai as $pai) {
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
        
        $editaisPai = $em->find("Entity\Site", $_REQUEST['site'])->getEditais();
        $vinculados = $this->getCompartilhados();
        
        if (empty($vinculados)) {
            return 0;
        }
        
        foreach ($editaisPai as $pai) {
            if (in_array($pai->getId(), $vinculados)) {
                $retorno[] = $pai->getId();
            }
        }
        
        return $retorno;
    }
    
    
}
