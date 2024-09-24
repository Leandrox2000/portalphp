<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;
use \Doctrine\ORM\Query\ResultSetMapping;
use Helpers\Session;

/**
 * Description of SiteRepository
 *
 * @author Eduardo
 */
class SiteRepository extends BaseRepository
{
    
    public function getQueryIndex()
    {
        return $this->createQueryBuilder('e')
                    ->distinct()
                    ->andWhere('e.publicado = 1')
                    ->andWhere('e.dataInicial < :today')
                    ->andWhere('e.dataFinal > :today OR e.dataFinal IS NULL')
                    ->orderBy('e.nome', 'ASC')
                    ->getQuery()
                    ->setParameter('today', new \DateTime('now'))
                    ->useQueryCache(TRUE)
                    ->useResultCache(TRUE, CACHE_LIFE_TIME)
                    ->getResult();
    }

    public function getSites(Session $session)
    {
        $user = $session->get('user');
        $dql = 'SELECT sit FROM Entity\Site sit';
        
        //Se não é sede restringe os subsites
        if (!$user['sede']) {
            $dql .= ' WHERE sit.id IN (:sites)';
        }

        $dql .= ' ORDER BY sit.nome ASC ';
        
        $query = $this->getEntityManager()
            ->createQuery($dql);

        if (!$user['sede']) {
            $query->setParameter('sites', $user['subsites']);
        }
            
        return $query->getResult();
    }

    /**
     * 
     * @return array
     */
    
    public function getSiteByName($nome)
    {
        $query = $this->createQueryBuilder('a');
        $query->andWhere($query->expr()->like("a.nome", ":nome"))
                ->setParameter('nome', "%{$nome}%");
        $query->setMaxResults(1);
        $result = $query->getQuery()->getSingleResult();
        
        return $result;   
    }
    
    public function getSiteBySigla($sigla)
    {
          
        $query = $this->createQueryBuilder('a');
        $query->andWhere($query->expr()->like("UPPER(a.sigla)", ":sigla"))
                ->setParameter('sigla', "%".strtoupper($sigla)."%");
        $query->setMaxResults(1);
        $result = $query->getQuery()->getOneOrNullResult();
        
        return $result;   
    }
    
    public function getArraySites()
    {
        $dql = "SELECT sit.id, sit.sigla FROM Entity\Site sit ";
        $query = $this->getEntityManager()->createQuery($dql);
        $array = array();

        //Percorre e organiza os sites pela sigla
        foreach ($query->getResult() as $site) {
            $array[$site['sigla']] = $site['id'];
        }
        
        return $array;
    }
    
    /**
     * 
     * Retorna o id do site sede
     */
    public function getIdSiteSede()
    {
        //Monta a dql
        $dql = "SELECT sit.id FROM \Entity\Site sit WHERE sit.sede ";
        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();

        return $result[0]['id'];
    }

    /**
     * 
     * @param array|string $ids
     * @return boolean
     */
    public function verificaSitesSede($ids)
    {
        //Monta a dql
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $dql = "SELECT sit FROM \Entity\Site sit WHERE sit.sede = 1 AND sit.id IN ({$ids})";
        $query = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();

        return count($result) > 0 ? false : true;
    }
    
    /**
     * 
     * @param array|string $ids
     * @return boolean
     */
    public function getSiteSede()
    {
        $query = $this->createQueryBuilder('s')
        		->select(
        			's.id',
        			's.nome'
        		)
                ->where('s.sede = 1');

        return $query->getQuery()
						->getSingleResult();
    }
    
    public function verificaFuncionalidades($ids)
    {
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $query = $this->createQueryBuilder("S");
        $query->select($query->expr()->count("FS.id"))
                ->leftJoin("S.funcionalidadesSite", "FS")
                ->where($query->expr()->in("S.id", $ids));
        return $query->getQuery()->getSingleScalarResult() == 0;
    }

    /**
     * 
     * @return int
     */
    public function countAll()
    {
        //monta o dql
        $dql = "SELECT COUNT(sit) FROM Entity\Site sit WHERE sit.sede = 0";

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getSingleScalarResult();
    }

    /**
     * 
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @return array
     */
    public function getBuscaSite($limit, $offset, $filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT sit FROM Entity\Site sit WHERE 1 = 1 AND sit.sede = 0 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND semAcento(LOWER(sit.nome)) LIKE semAcento(:nome) ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND sit.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (sit.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Ordena os dados
        //$dql .= " ORDER BY sit.dataCadastro DESC ";
        $dql .= " ORDER BY sit.nome ASC ";

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
     * @return array
     */
    public function getTotalBuscaSite($filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT COUNT(sit) total FROM Entity\Site sit WHERE 1 = 1 AND sit.sede = 0 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(sit.nome) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND sit.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (sit.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $resultado = $query->getResult();
        return $resultado[0]['total'];
    }
    
    public function getSiteIds($ids)
    {
        $dql = "SELECT s FROM Entity\Site s WHERE s.id IN ({$ids}) ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    /**
     * @param Session $session
     */
    public function getSitesPublicados(Session $session)
    {
        $user = $session->get('user');
        $dql = 'SELECT sit FROM Entity\Site sit';        
        $dql .= ' WHERE sit.id IN (:sites)';
        $dql .= ' AND sit.publicado = :publicado';
        $dql .= ' ORDER BY sit.nome ASC ';
        
        $query = $this->getEntityManager()
            ->createQuery($dql);
        $query->setParameter('sites', $user['subsites']);
        $query->setParameter('publicado', 1);
            
        return $query->getResult();
    }
}
