<?php
namespace Entity\Repository;

class CategoriaDicionarioRepository extends BaseRepository
{

    /**
     * 
     * @param string $nome
     * @return boolean
     */
    public function verificaNomeCategoria($nome)
    {
        //Monta a dql, executa e verifica se algum registro foi encontrado
        $dql = "SELECT count(cd) total FROM Entity\CategoriaDicionario cd WHERE cd.nome = :nome ";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters(array('nome' => $nome));
        $result = $query->getResult();

        return $result[0]['total'] == 0 ? true : false;
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

}
