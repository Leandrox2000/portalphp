<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Entity\Repository;

/**
 * Description of EditalCategoriaRepository
 *
 * @author Luciano
 */
class EditalCategoriaRepository extends BaseRepository
{
    /**
     *
     * @return array
     */
    public function getCategorias()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select("C")
                        ->from($this->getEntityName(), "C")
                        ->orderBy('C.nome', 'ASC');

        return $query->getQuery()->getResult();
    }


    /**
     *
     * @param array $findBy
     * @return boolean
     */
    public function verificaCategoriaExiste(array $findBy)
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder();

        $query->select("C.id")
                ->from($this->getEntityName(), "C")
                ->andWhere($query->expr()->eq("C.nome", ":categoria"))
                ->andWhere($query->expr()->notIn("C.id", ":id"));

        $query->setParameter("categoria", $findBy['nome']);
        $query->setParameter("id", $findBy['id']);

        $result = $query->getQuery()->getResult();

        return empty($result);
    }

}
