<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of ImagemRepository
 *
 * @author Eduardo
 */
class ImagemRepository extends EntityRepository
{

    /**
     * 
     * @return int
     */
    public function countAll()
    {
        $dql = "SELECT count(img) FROM Entity\Imagem img";
        $query = $this->getEntityManager()->createQuery($dql);

        return $query->getSingleScalarResult();
    }

    /**
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getImagem($limit, $offset)
    {
        $dql = "SELECT img FROM Entity\Imagem img ORDER BY img.dataCadastro DESC";

        $query = $this->getEntityManager()->createQuery($dql);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     * 
     * @return array
     */
    public function getBuscaImagem($limit, $offset, $filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT img FROM Entity\Imagem img JOIN img.pasta p WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND (LOWER(img.nome) LIKE :nome  OR LOWER(img.palavrasChave) LIKE :palavrasChave)";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
            $parametros['palavrasChave'] = "%" . $filtro['busca'] . "%";
        }
   
        if (!empty($filtro['pasta'])) {
            $dql .= " AND p.id = :pasta ";
            $parametros['pasta'] = $filtro['pasta'];
        }

        if (!empty($filtro['categoria'])) {
            $dql .= " AND p.categoria = :categoria ";
            $parametros['categoria'] = $filtro['categoria'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (img.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Ordena os dados
        $dql .= " ORDER BY img.dataCadastro DESC ";

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
     * @return array
     */
    public function getTotalBuscaImagem($filtro)
    {
        //Estrutura o sql da busca
        $dql = "SELECT count(img) total FROM Entity\Imagem img JOIN img.pasta p  WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(img.nome) LIKE :nome ";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
        }

        if (!empty($filtro['categoria'])) {
            $dql .= " AND p.categoria = :categoria ";
            $parametros['categoria'] = $filtro['categoria'];
        }

        if (!empty($filtro['pasta'])) {
            $dql .= " AND p.id = :pasta ";
            $parametros['pasta'] = $filtro['pasta'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (img.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $resultado = $query->getResult();

        return $resultado[0]['total'];
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    public function getImagemIdsGaleria($id)
    {
    	return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('img.imagemId')
                ->from('Entity\GaleriaImagem', 'img')
                ->where('img.galeria = '. $id)
                ->orderBy('img.ordem', 'asc')
                ->getQuery()
                ->getResult();
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    public function getImagemIds(array $ids)
    {
        $dql = "SELECT img FROM Entity\Imagem img WHERE img.id IN (" . implode(',', $ids) . ") ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    /**
     * 
     * @param mixed $categoria
     * @return array
     */
    public function getImagens($categoria)
    {
        $dql = "SELECT img.id, img.nome, img.imagem FROM Entity\Imagem img ";

        if ($categoria != 0) {
            $dql .= " JOIN img.categoria cat WHERE cat.id = " . $categoria . " ";
        }

        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    /**
     * 
     * @param integer $pasta_id
     * @return boolean
     */
    public function verificaVinculoPasta($pasta_id)
    {
        //monta o dql
        $dql = "SELECT count(entity) total FROM Entity\Imagem entity JOIN entity.pasta pasta WHERE pasta.id = :id  ";
        $parametros['id'] = $pasta_id;

        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }

    /**
     * 
     * @param $id
     * @return integer
     */
    public function getImagemVinculado($id)
    {
        try {
            $query = $this->createQueryBuilder('i')
                        ->select('i.nome')
                        ->distinct()
                        ->andWhere('i.pasta = :id');

            return $query->setParameter('id', $id)
                        ->getQuery()
                        ->getResult();
        } catch (\Doctrine\ORM\NoResultExceptionption $e) {
            return false;
        }
    }

    /**
     * 
     * @param $id
     * @return integer
     */
    public function setOrdemGaleria($idGaleria, $idImagem, $ordem)
    {
    	$this->getEntityManager()
        	->createQueryBuilder()
           	->update('Entity\GaleriaImagem', 'c')
            ->set('c.ordem', $ordem)
            ->where('c.galeria = '.$idGaleria.' and c.imagem = '.$idImagem)
            ->getQuery()
            ->execute();
    }
    
}
