<?php

namespace Entity\Repository;

use Helpers\Session;

/**
 * Description of BannerGeralRepository
 */
class BannerGeralRepository extends BaseRepository {

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

    public function getBannersDivulgacaoSubsite($site = NULL, $nomeCategoria = NULL, $limit = NULL) {
        //$dql = "SELECT arq.nome FROM Entity\BannerGeral b WHERE arq.id IN(".implode(',', $ids).") ";
        $dql = "SELECT DISTINCT b FROM Entity\BannerGeral b JOIN b.sites sit JOIN b.categoria c WHERE 1=1";

        $dql .= " AND sit.id = :site";
        $dql .= " AND c.nome = :nomeCategoria";

        $query = $this->getEntityManager()->createQuery($dql);

        $query->setParameter('site', $site);
        $query->setParameter('nomeCategoria', $nomeCategoria);

        if($limit !== NULL)
            $query->setMaxResults($limit);
        
        return $query->getResult();
    }

    public function getBannersLateral($site = NULL, $nomeCategoria = NULL, $limit = NULL) {
        //$dql = "SELECT arq.nome FROM Entity\BannerGeral b WHERE arq.id IN(".implode(',', $ids).") ";
        $dql = "SELECT DISTINCT b FROM Entity\BannerGeral b JOIN b.sites sit JOIN b.categoria c WHERE 1=1 AND b.publicado = 1";

        $dql .= " AND sit.id = :site";
        $dql .= " AND c.nome = :nomeCategoria";
        $dql .= " ORDER BY b.ordem ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('site', $site);
        $query->setParameter('nomeCategoria', $nomeCategoria);

        if($limit !== NULL)
            $query->setMaxResults($limit);
        
        return $query->getResult();
    }

       public function getBannersComunicacao($site = NULL, $nomeCategoria = NULL, $limit = NULL) {
        //$dql = "SELECT arq.nome FROM Entity\BannerGeral b WHERE arq.id IN(".implode(',', $ids).") ";
        $dql = "SELECT DISTINCT b FROM Entity\BannerGeral b JOIN b.sites sit JOIN b.categoria c WHERE 1=1 AND b.publicado = 1";

       // $dql .= " AND sit.id = :site";
        $dql .= " AND c.nome = :nomeCategoria";
        $dql .= " ORDER BY b.ordem ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        //$query->setParameter('site', $site);
        $query->setParameter('nomeCategoria', $nomeCategoria);

        if($limit !== NULL)
            $query->setMaxResults($limit);
        
        return $query->getResult();
    }
    
    
    /**
     * 
     * @param \Entity\Site $site
     * @param string $nomeCategoria
     * @return array
     */
    public function getConteudoHome($site = NULL, $nomeCategoria = NULL, $limit = NULL, $excessao = null) {
        $query = $this->createQueryBuilder('b')
                ->distinct()
                ->join('b.sites', 's')
                ->join('b.categoria', 'c')
                ->where('b.publicado = 1')
                ->andWhere('b.dataInicial < :today')
                ->andWhere('b.dataFinal > :today OR b.dataFinal IS NULL');

        if (!is_null($excessao)) {
            $query->orWhere('b.id = :id_excessao')
                    ->setParameter('id_excessao', $excessao);
        }

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.sigla = :sigla")
                    ->setParameter('sigla', $site->getSigla());
        } else {
            $query->andWhere("s.sigla IN ('SEDE')");
        }

        if ($nomeCategoria !== NULL) {
            $query->andWhere('c.nome = :nomeCategoria')
                    ->setParameter('nomeCategoria', $nomeCategoria);
        }

        if ($limit !== NULL) {
            $query->setMaxResults($limit);
        }

        return $query->orderBy('b.ordem', 'ASC')
                        ->setParameter('today', new \DateTime('now'))
                        ->getQuery()
                        ->useQueryCache(TRUE)
                        ->useResultCache(TRUE, CACHE_LIFE_TIME)
                        ->getResult();
    }

    /**
     * Retorna a query
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQuery() {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select("A")
                ->distinct(TRUE)
                ->from($this->getEntityName(), "A")
                ->join("A.sites", "sites")
                ->join("A.categoria", "cat");

        return $query;
    }

    /**
     * Define as condições da busca
     * 
     * @param \Doctrine\ORM\QueryBuilder
     * @param array $where
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function setWhere(\Doctrine\ORM\QueryBuilder $query, array $where = array()) {
        if (empty($this->where)) {
            $this->where = $where;
        }

        $user = $this->session->get("user");
        $busca = mb_strtolower($this->where['busca'], "UTF-8");
        $categoria = $this->where['categoria'];
        $status = $this->where['status'];
        $site = $this->where['site'];
        $data_inicial = $this->where['data_inicial'];
        $data_final = $this->where['data_final'];

        // Filtro por campo de busca
        if (!empty($busca)) {
            $query->andWhere(
                    $this->getEntityManager()
                            ->createQueryBuilder()
                            ->orWhere($query->expr()->like($query->expr()->lower("A.nome"), ":busca"))
                            ->getDQLPart("where")
            );
            $query->setParameter("busca", "%{$busca}%");
        }

        // Filtro por status
        if ($status !== "") {
            $query->andWhere($query->expr()->eq("A.publicado", ":status"));
            $query->setParameter("status", $status);
        }

        // Filtro por categoria
        if (!empty($categoria)) {
            $query->andWhere("A.categoria = :categoria");
            $query->setParameter("categoria", $categoria);
        }

        // Filtro por intervalo de data inicial/final
        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere($query->expr()->between("A.dataCadastro", "'{$data_inicial} 00:00'", "'{$data_final} 23:59'"));
        }

        // Filtro por permissão
        if ($user['sede']) {
            if (!empty($site)) {
                $query->andWhere($query->expr()->eq("sites.id", ":site"));
                $query->setParameter("site", $site);
            }
        } else {
            // Se o filtro 'site' não é vazio e o usuário tem acesso ao conteúdo
            if (!empty($site) && in_array($site, $user['subsites'])) {
                // Aplica filtro
                $query->andWhere($query->expr()->eq("sites.id", ":site"));
                $query->setParameter("site", $site);
            } else {
                // Pega conteúdo dos sites que o usuário tem acesso
                $query->andWhere("sites.id IN(:sites)");
                $query->setParameter("sites", $user['subsites']);
            }
        }

        return $query;
    }

    /**
     * Retorna os registros, de acordo com os filtros.
     * @param type $limit
     * @param type $offset
     * @param array $filtro
     * @param \Helpers\Session $session
     * @return type
     */
    public function getResults($limit, $offset, array $filtro, Session $session) {
        $this->session = $session;

        $query = $this->getQuery();
        $query = $this->setWhere($query, $filtro);
        $query->orderBy('A.ordem', 'ASC');
        if ($limit != '-1') {
            $query->setMaxResults($limit);
        }
        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    /**
     * Número total de registros com filtro
     * 
     * @return int
     */
    public function getMaxResult() {
        $query = $this->getQuery();
        $query->select($query->expr()->countDistinct("A"));
        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Numero total geral de registros
     * 
     * @return int
     */
    public function countAll() {
        $user = $this->session->get("user");
        $query = $this->getQuery();
        $query->select($query->expr()->countDistinct("A"));

        if (!$user['sede']) {
            $query->andWhere("sites.id IN(:sites)");
            $query->setParameter("sites", $user['subsites']);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * 
     * @return 
     */
    public function getBannerRodape() {
        //Monta o dql
        $dql = "SELECT b FROM Entity\BannerGeral b JOIN Entity\BannerGeralCategoria c WHERE c.id = :id_categoria";

        //Faz a ordenação
        $dql .= " ORDER BY b.dataCadastro DESC ";

        //Monta e executa a query
        $query = $this->getEntityManager()->createQuery($dql)->setMaxResults(3);

        //Retorna o resultado
        return $query->getResult();
    }
    
     public function getBuscaSites($id) {
            $query = $this->createQueryBuilder('b')
                ->distinct()
                ->join('b.sites', 's');
            $query->where('b.id = :id')
                    ->setParameter('id', $id);
            
            return $query->getQuery()
                    ->getResult();
          
    }
    
   
    
}
