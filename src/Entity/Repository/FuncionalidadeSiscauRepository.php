<?php

namespace Entity\Repository;

/**
 * Description of FuncionalidadeSiscauRepository
 *
 * @author join-ti
 */
class FuncionalidadeSiscauRepository extends BaseRepository
{

    /**
     * 
     * @return array
     */
    public function getFuncionalidadesSessao()
    {
        //Busca, organiza e retorna todas as permissões do sistema organizadas em um array, que irá para a sessão
        $dql = "SELECT f FROM Entity\FuncionalidadeSiscau f ";
        $query = $this->getEntityManager()->createQuery($dql);
        $array = array();

        foreach ($query->getResult() as $result) {
            $array[$result->getController()][$result->getAcao()] = $result->getSigla();
        }

        return $array;
    }

    /**
     * 
     * @return array
     */
    public function getArraySiglas()
    {
        $dql = "SELECT f.sigla FROM Entity\FuncionalidadeSiscau f  ";
        $query = $this->getEntityManager()->createQuery($dql);
        $array = array();

        //Percorre e organiza os sites pela sigla
        foreach ($query->getResult() as $site) {
            $array[$site['sigla']] = $site['sigla'];
        }

        return $array;
    }

}
