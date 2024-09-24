<?php
namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of VideoRelacionadoRepository
 *
 * @author Barbara
 */
class VideoRelacionadoRepository extends BaseRepository
{       
    /**
     * Consulta os vídeos relacionados ao vídeo pai.
     * 
     * @param integer $id Id do video pai
     * @return object[]
     */
    public function getVideosRelacionadosByVideo($id, $filtrarPublicados = true) {
        
        $dql  = 'SELECT vr ';
        $dql .= 'FROM Entity\VideoRelacionado vr ';
        $dql .= 'JOIN vr.relacionado v ';
        $dql .= 'WHERE vr.video = :id ';
        if($filtrarPublicados) {
            $dql .= 'AND v.publicado = 1 ';
        }
        $dql .= 'ORDER BY vr.ordem ASC ';
        
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('id', $id);
        
        $relacionados = $query->getResult();
        $videos = array();
                
        foreach($relacionados as $relacionado) {
            /* @var $relacionado \Entity\VideoRelacionado */
            $videos[] = $relacionado->getRelacionado();
        }
        return $videos;
    }
        
}