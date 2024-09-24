<?php

namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of BannerHomeRepository
 *
 * @author Eduardo
 */
class BannerHomeRepository extends BaseRepository
{

    /**
     * 
     * @return int
     */
    public function countAll($user)
    {
        //monta o dql
        $dql = "SELECT COUNT(DISTINCT bh) FROM Entity\BannerHome bh  JOIN bh.sites sit ";

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
    public function getBuscarBannerHome($limit, $offset, $filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT bh FROM Entity\BannerHome bh JOIN bh.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(bh.nome) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND bh.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (bh.dataCadastro BETWEEN :data_inicial AND :data_final  )";
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
        $dql .= " ORDER BY bh.dataCadastro DESC ";

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
    public function getTotalBuscaBannerHome($filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT COUNT(DISTINCT bh) total FROM Entity\BannerHome bh JOIN bh.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(bh.nome) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND bh.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (bh.dataCadastro BETWEEN :data_inicial AND :data_final  )";
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
