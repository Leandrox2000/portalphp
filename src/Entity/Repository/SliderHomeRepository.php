<?php

namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of SliderHomeRepository
 *
 * @author Join-ti
 */
class SliderHomeRepository extends BaseRepository
{

    /**
     * 
     * @param \Entity\Site $site
     * @param integer $limit
     * @return array
     */
    public function getConteudoHome($site = NULL, $limit = 20)
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

        return $query->orderBy('n.dataInicial', 'DESC')
                     ->setParameter('today', new \DateTime('now'))
                     ->setMaxResults($limit)
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME)
                     ->getResult();
    }

    /**
     * 
     * @return int
     */
    public function countAll($user)
    {
        //monta o dql
        $dql = "SELECT COUNT(DISTINCT sld) FROM Entity\SliderHome sld  JOIN sld.sites sit ";

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
    public function getBuscarSliderHome($limit, $offset, $filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT sld FROM Entity\SliderHome sld JOIN sld.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND Unaccent(LOWER(sld.nome)) LIKE Unaccent(:nome) ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND sld.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (sld.dataCadastro BETWEEN :data_inicial AND :data_final  )";
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


        //Ordena os dados
        $dql .= " ORDER BY sld.dataCadastro DESC ";

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
    public function getTotalBuscaSliderHome($filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT COUNT(DISTINCT sld) total FROM Entity\SliderHome sld JOIN sld.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(sld.nome) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND sld.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (sld.dataCadastro BETWEEN :data_inicial AND :data_final  )";
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

    public function getSliderOrdemSite($site)
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('s.idSliderHome', 's.ordem')
                ->from('Entity\SliderHomeSite', "s")
        		->where('s.idSite = '.$site)
        		->orderBy('s.ordem', 'asc');

        return $query->getQuery()->getResult();
    }

    public function getSliderOrdem($id)
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('s.idSite', 's.ordem')
                ->from('Entity\SliderHomeSite', "s")
        		->where('s.idSliderHome = '.$id);

        return $query->getQuery()->getResult();
    }
    
    public function setOrdemSite($site, $slider, $ordem)
    {
    	$this->getEntityManager()
			->createQueryBuilder()
			->update('Entity\SliderHomeSite', 's')
			->set('s.ordem', $ordem)
			->where('s.idSliderHome = '.$slider)
			->andWhere('s.idSite = '.$site)
			->getQuery()
			->execute();
    }
    
     public function getSiteVisualizacao($idBanner){
        $dql = "SELECT s.sigla FROM Entity\BannerGeral b JOIN Entity\Site s WHERE s.publicado = 1 AND b.id = {$idBanner} AND s.sede = 0 ";
        $query = $this->getEntityManager()->createQuery($dql)->setMaxResults(1);
        $dados = $query->execute();
        
        return !empty($dados) ? strtolower($dados[0]['sigla']) : "" ; 
    }

}
