<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of CategoriaFototecaRepository
 *
 * @author Join
 */
class CategoriaFototecaRepository extends EntityRepository
{

    /**
     * 
     * @param string $nome
     * @return boolean
     */
    public function verificaNomeCategoria($nome)
    {
        //Monta a dql, executa e verifica se algum registro foi encontrado
        $dql = "SELECT count(cat) total FROM Entity\CategoriaFototeca cat WHERE cat.nome = :nome ";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters(array('nome' => $nome));
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }
    
    
    public function getBuscaCategoriaFototeca()
    {
        //Monta a dql, executa e verifica se algum registro foi encontrado
        $dql = "SELECT cat FROM Entity\CategoriaFototeca cat ";
        $dql .= " ORDER BY cat.nome ASC ";

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }

        return $query->getResult();
    }

}
