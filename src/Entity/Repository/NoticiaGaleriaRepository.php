<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of NoticiaGaleriaRepository
 *
 * @author Eduardo
 */
class NoticiaGaleriaRepository extends BaseRepository {

    /**
     * 
     * @param array $ids
     * @return array
     */
    public function getNoticiaGaleriaIdsPaginas($id) {
        $dql = "SELECT ng FROM Entity\NoticiaGaleria ng JOIN ng.noticia nt WHERE nt.id = {$id} ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function getGaleriasNoticia($noticia) {
        $dql = "SELECT ng FROM Entity\NoticiaGaleria ng JOIN ng.galeria ga WHERE ng.noticia = {$noticia} AND ga.publicado = 1 ORDER BY ng.ordemGaleria ASC ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    public function getGaleriasNoticiaPublicadas($idsGaleria, $idNoticia) {
        $dql = "SELECT ng FROM Entity\NoticiaGaleria ng JOIN ng.galeria ga WHERE ng.noticia = {$idNoticia} AND ng.galeria IN ({$idsGaleria}) AND ga.publicado = 1 ORDER BY ng.ordemGaleria ASC ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

}
