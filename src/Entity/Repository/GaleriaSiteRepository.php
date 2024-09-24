<?php
namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of GaleriaSiteRepository
 *
 * @author Rafael
 */
class GaleriaSiteRepository extends BaseRepository
{

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param \Entity\Usuario $user
     * @return array
     */
    public function getBuscaGaleriaOrder($limit, $offset, $filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT DISTINCT gs FROM Entity\GaleriaSite gs JOIN gs.site sit JOIN gs.galeria g  WHERE 1 = 1 AND g.publicado = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND LOWER(CONCAT(g.titulo, g.descricao)) LIKE :titulo ";
            $parametros['titulo'] = "%" . $filtro['busca'] . "%";
        }

        if (isset($filtro['status'])) {
            if ($filtro['status'] !== "") {
                $dql .= " AND g.publicado = :publicado ";
                $parametros['publicado'] = $filtro['status'];
            }
        }
        
        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (g.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        }

        //Caso o usuário for sede, poderá buscar por sites
        if (!empty($filtro['site']) and $filtro['site'] != '') {
            $dql .= " AND sit.id = :id_site ";
            $parametros['id_site'] = $filtro['site'];
        }
        
        if(empty($filtro['site'])) {
            $dql .= " AND sit.sigla in(:sigla)";
            $parametros['sigla'] = "SEDE";
        }
        
        //Ordena os dados
        $dql .= " ORDER BY gs.ordem ASC";
        
        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        
        if(($limit + $offset) > 0){
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
        //die($query->getSql());
        //echo "_>>>".$query->getSql();
        $galeriasSite = $query->getResult();
       
        foreach ($galeriasSite as $galeria) {
            $retorno[] = $this->getEntityManager()->getRepository('Entity\Galeria')->find($galeria->getGaleria());
        }
                
        return $retorno ;
    }

    
     public function getConteudoInternaOrder($site = NULL, $pagNumero, $pagMaximo)
    {
        
         $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->join('a.site', 's')
                      ->join('a.galeria', 'g')
                      ->andWhere('g.publicado = 1')
                      ->andWhere('g.dataInicial < :today')
                      ->andWhere('g.dataFinal > :today OR g.dataFinal IS NULL');

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.id = :id")
                  ->setParameter('id', $site->getId());
        } else {
            $query->andWhere("s.sigla IN ('SEDE')");
        }
    
        
        return $query->orderBy('a.ordem', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
        
    }
    
    
    
     public function buscarUltimaOrdem($site){
        
        $dql = "SELECT (max(g.ordem)+1) as ordem FROM Entity\GaleriaSite g WHERE g.site = :site";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("site",$site);
        $aux = $query->getResult();
        
        foreach ($aux as $a){
            $ordem = !$a["ordem"] ? 1 : $a["ordem"];
        }
        
       return $ordem;
    }
    
    
}
