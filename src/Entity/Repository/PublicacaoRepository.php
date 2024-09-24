<?php

namespace Entity\Repository;

/**
 * Description of PublicacaoRepository
 *
 * @author Luciano
 */
class PublicacaoRepository extends BaseRepository
{

    /**
     *
     * @var array
     */
    private $where;
    public function getConteudoInternaOrder($categoria = NULL, $busca = NULL, $tipo = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        
        if (!empty($categoria)) {
            $query->andWhere('a.categoria = :cat')
                  ->setParameter('cat', $categoria);
        }

        if (!empty($tipo)) {
            if ($tipo == 'publicacao') {
                $query->andWhere('a.tipoPublicacao = 1');
            }
            if ($tipo == 'livraria') {
                $query->andWhere('a.tipoLivraria = 1');
            }
        }

        if (!empty($busca)) {
            $conditions = $query->expr()->orX(
                'semAcento(lower(a.titulo)) LIKE Unaccent(lower(:busca))',
                'semAcento(lower(a.conteudo)) LIKE Unaccent(lower(:busca))',
                'semAcento(lower(a.autor)) LIKE Unaccent(lower(:busca))'
            );
            $query->andWhere($conditions)
                  ->setParameter('busca', '%' . $busca . '%');
        }

        return $query->orderBy('a.ordem', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }
    
    
    public function getConteudoInterna($categoria = NULL, $busca = NULL, $tipo = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

        
        if (!empty($categoria)) {
            $query->andWhere('a.categoria = :cat')
                  ->setParameter('cat', $categoria);
        }

        if (!empty($tipo)) {
            if ($tipo == 'publicacao') {
                $query->andWhere('a.tipoPublicacao = 1');
            }
            if ($tipo == 'livraria') {
                $query->andWhere('a.tipoLivraria = 1');
            }
        }

        if (!empty($busca)) {
            $conditions = $query->expr()->orX(
                'semAcento(lower(a.titulo)) LIKE Unaccent(lower(:busca))',
                'semAcento(lower(a.conteudo)) LIKE Unaccent(lower(:busca))',
                'semAcento(lower(a.autor)) LIKE Unaccent(lower(:busca))'
            );
            $query->andWhere($conditions)
                  ->setParameter('busca', '%' . $busca . '%');
        }

        return $query->orderBy('a.dataInicial', 'DESC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }

    /**
     * Retorna a Query das Publicações
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryPublicacoes()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select("P")
                        ->from($this->getEntityName(), "P")
                        ->join("P.categoria", "C");


        return $query;
    }

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtro
     * @return array
     */
    public function getPublicacoes($limit, $offset, array $filtro = array())
    {
        $query = $this->getQueryPublicacoes();

        $query = $this->setWhere($query, $filtro);
        //$query->orderBy('P.dataCadastro', 'DESC');
        $query->orderBy('P.ordem', 'ASC');
        if($limit>0){
        $query->setMaxResults($limit);
        }
        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }
    
    
    public function buscarUltimaOrdem(){
        
        $dql = "SELECT (max(p.ordem)+1) as ordem FROM Entity\Publicacao p";
        $query = $this->getEntityManager()->createQuery($dql);
        $aux = $query->getResult();
        
        foreach ($aux as $a){
            $ordem = $a["ordem"];
        }
        
       return $ordem;
    }



    /**
     * Número total de Publicações com filtro
     *
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryPublicacoes();

        $query->select($query->expr()->count("P"));

        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Numero total de publicações do Banco
     *
     * @return int
     */
    public function countAll()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder();

        $query->select($query->expr()->count("P"))
                ->from($this->getEntityName(), "P");

        return $query->getQuery()->getSingleScalarResult();
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

        $busca          = mb_strtolower($this->where['busca'], "UTF-8");
        $categoria      = $this->where['categoria'];
        $status         = $this->where['status'];
        $data_inicial   = $this->where['data_inicial'];
        $data_final     = $this->where['data_final'];

        if (!empty($busca)) {
            $query->andWhere(
                    $this->getEntityManager()
                            ->createQueryBuilder()
                            ->orWhere($query->expr()->like($query->expr()->lower("Unaccent(P.titulo)"), "Unaccent(:busca)"))
                            ->orWhere($query->expr()->like($query->expr()->lower("Unaccent(P.autor)"), "Unaccent(:busca)"))
                            ->orWhere($query->expr()->like($query->expr()->lower("Unaccent(C.nome)"), "Unaccent(:busca)"))
                            ->orWhere($query->expr()->like($query->expr()->lower("Unaccent(P.conteudo)"), "Unaccent(:busca)"))
                            ->getDQLPart("where")
            );
            $query->setParameter("busca", "%{$busca}%");
        }
        if (!empty($categoria)) {
            $query->andWhere($query->expr()->eq("P.categoria", ":categoria"));
            $query->setParameter("categoria", $categoria);
        }
        if ($status !== "") {
            $query->andWhere($query->expr()->eq("P.publicado", ":status"));
            $query->setParameter("status", $status);
        }

        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere($query->expr()->between("P.dataCadastro", "'$data_inicial 00:00'", "'$data_final 23:59'"));
        }

        return $query;
    }

    /**
     * 
     * @param $id
     * @return integer
     */
    public function getPublicacaoVinculado($id)
    {
        try {
            $query = $this->createQueryBuilder('e')
                        ->select('e.titulo')
                        ->distinct()
                        ->andWhere('e.categoria = :id');

            return $query->setParameter('id', $id)
                        ->getQuery()
                        ->getResult();
        } catch (\Doctrine\ORM\NoResultExceptionption $e) {
            return false;
        }
    }
}
