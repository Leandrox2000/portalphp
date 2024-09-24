<?php
namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;
use \Doctrine\ORM\Query\ResultSetMapping;

/**
 * Classe ImagemCategoriaRepository
 *
 * Responsável por todas as consultas a tabela Imagem
 * @author Eduardo
 */
class ImagemCategoriaRepository extends EntityRepository
{

    /**
     * Metodo countAll
     * 
     * Conta todas as categorias de imagens 
     * @return int
     */
    public function countAll()
    {
        $dql = "SELECT count(imc) FROM Entity\ImagemCategoria imc";
        $query = $this->getEntityManager()->createQuery($dql);

        return $query->getSingleScalarResult();
    }

    /**
     * Metodo getImagem
     * 
     * Busca as imagens com um limit e offset passados por parametro
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getImagem($limit, $offset)
    {
        $dql = "SELECT imc FROM Entity\ImagemCategoria imc ORDER BY imc.dataCadastro DESC";

        $query = $this->getEntityManager()->createQuery($dql);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }

        return $query->getResult();
    }

}
