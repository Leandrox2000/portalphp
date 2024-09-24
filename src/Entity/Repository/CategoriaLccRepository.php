<?php

namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Description of CategoriaLccRepository
 *
 * @author Join
 */
class CategoriaLccRepository extends EntityRepository
{
    /*
     * @param string $nome
     * @return boolean
     */

    public function verificaNomeCategoria($nome)
    {
        //Monta a dql, executa e verifica se algum registro foi encontrado
        $dql = "SELECT count(cat) total FROM Entity\CategoriaLcc cat WHERE cat.nome = :nome ";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters(array('nome' => $nome));
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
    }
    
    
    /**
     * 
     * @return array
     */
    public function getCategoriasPermitidas()
    {
        //Monta a dql, executa e verifica se algum registro foi encontrado
        $dql = "SELECT cat FROM Entity\CategoriaLcc cat WHERE cat.permiteExcluir = 1 ";
        $dql .= " ORDER BY cat.nome ASC ";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    
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
     * @return array
     */
    public function getCategoriasFixas()
    {
        return array(
            "Licitações",
            "Contratações diretas",
            "Ata de registro de preços",
            "Contratos",
            "Tranferências Voluntárias (convênios)",
        );
    }
    
    

}
