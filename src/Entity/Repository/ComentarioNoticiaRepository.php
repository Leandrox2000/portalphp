<?php

namespace Entity\Repository;

class ComentarioNoticiaRepository extends BaseRepository
{

    protected $entity = 'Entity\ComentarioNoticia';
    protected $search_column = 'comentario';
    
    public function countComentariosPublicados($id)
    {
        return $this->createQueryBuilder('cn')
                    ->select('count(cn.id)')
                    ->where('cn.noticia = :noticia')
                    ->andWhere('cn.publicado = 1')
                    ->setParameter('noticia', $id)
                    ->getQuery()
                    ->getSingleScalarResult();
    }
    
    public function listarComentarios($id)
    {
        return $this->findBy(
            array(
                'publicado' => 1,
                'noticia' => $id,
            ),
            array('dataInicial' => 'ASC')
        );
    }

    /**
     * 
     * @return int
     */
    public function countAll()
    {
        //monta o dql
        $dql = "SELECT DISTINCT COUNT(e) total FROM {$this->entity} e ";

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getSingleScalarResult();
    }
    
    /**
     * 
     * @return string
     */
    private function getWhereBusca($filtro)
    {
        $dql = " ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER( CONCAT(e.comentario, n.titulo) ) LIKE :busca ";
            $parametros['busca'] = "%" . $filtro['busca'] . "%";
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (e.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }
        
        return array(
            'dql' => $dql,
            'parametros' => $parametros,
        );
    }

    /**
     * 
     * @param int $limit
     * @param int $offset
     * @param array $filtro
     * @return array
     */
    public function getBusca($limit, $offset, $filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT e, n FROM Entity\ComentarioNoticia e JOIN e.noticia n WHERE 1=1 ";

        $whereBusca = $this->getWhereBusca($filtro);
        $dql .= $whereBusca['dql'];

        //Ordena os dados
        $dql .= " ORDER BY e.dataCadastro DESC ";

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($whereBusca['parametros']);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     * 
     * @param array $filtro
     * @return integer
     */
    public function getTotal($filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT COUNT(e) as total FROM Entity\ComentarioNoticia e JOIN e.noticia n WHERE 1=1 ";

        $whereBusca = $this->getWhereBusca($filtro);
        $dql .= $whereBusca['dql'];

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($whereBusca['parametros']);

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
        $dql = "SELECT DISTINCT count(dic) total FROM {$this->entity} dic JOIN dic.categoria cat WHERE cat.id = :id_categoria  ";
        $parametros['id_categoria'] = $categoria;

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }

}
