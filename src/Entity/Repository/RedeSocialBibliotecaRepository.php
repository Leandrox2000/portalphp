<?php

namespace Entity\Repository;

/**
 * Description of RedeSocialBibliotecaRepository
 *
 * @author Luciano
 */
class RedeSocialBibliotecaRepository extends BaseRepository
{

    /**
     * 
     * @param array $ids
     * @return array
     */
    public function getIdRedesSociaisRelacionadas(array $ids)
    {
        $dql = "SELECT r.id FROM Entity\RedeSocialBiblioteca r JOIN r.biblioteca b WHERE b.id IN (".implode(', ', $ids).") ";
        $query = $this->getEntityManager()->createQuery($dql);
        $array = array();

        foreach ($query->getResult() as $res) {
            $array[] = $res['id'];
        }

        return $array;
    }

}
