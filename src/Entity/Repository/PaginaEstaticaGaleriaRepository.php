<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of PaginaEstaticaGaleriaRepository
 *
 * @author Eduardo
 */
class PaginaEstaticaGaleriaRepository extends BaseRepository
{

    /**
     * 
     * @param array $ids
     * @return array
     */
    public function getPaginaEstaticaGaleriaIdsPaginas($id)
    {
        $dql = "SELECT peg FROM Entity\PaginaEstaticaGaleria peg JOIN peg.paginaEstatica pag WHERE pag.id = {$id} ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

}
