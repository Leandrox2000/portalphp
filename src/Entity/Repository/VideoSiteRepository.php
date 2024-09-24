<?php
namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of VideoSiteRepository
 *
 * @author Rafael
 */
class VideoSiteRepository extends BaseRepository
{
    
    public function getUltimoVideo($site)
    {
        if ($site instanceof \Entity\Site) {
           $id = $site->getId();
        } else {
            $id = 1;
        }
        
        $query = $this->createQueryBuilder('vo')
                      ->join('vo.video', 'v')
                      ->where('v.publicado = 1')
                      ->andWhere('vo.site = :id')
                      ->orderBy('vo.ordem', 'ASC')
                      ->setParameter('id', $id);
        
        try {
            if (!$query->getQuery()->getResult()) {
                return 0;
            } else {
                return $query->getQuery()
                             ->setMaxResults(1)
                             ->getSingleResult();
            }
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        
    }

    public function getBuscaVideoOrder($limit, $offset, $filtro, array $user)
    {
        //Estrutura o dql da busca
        $dql = "SELECT DISTINCT v FROM Entity\VideoSite v JOIN v.site sit JOIN v.video vid WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND (LOWER(vid.nome) LIKE :nome OR LOWER(vid.link) LIKE :link)";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
            $parametros['link'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND vid.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (vid.dataCadastro BETWEEN :data_inicial AND :data_final)";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_final'])) {
            $dql .= " AND vid.dataCadastro < :data_final ";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_inicial'])) {
            $dql .= " AND (vid.dataCadastro > :data_inicial ) ";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
        }

        //Verifica se o usuário logado é da sede
        if (!$user['sede']) {
            $dql .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }
        
        //Caso o usuário for sede, poderá buscar por sites
        if (!empty($filtro['site'])) {
            $dql .= " AND sit.id = :id_site ";
            $parametros['id_site'] = $filtro['site'];
        }


        //Ordena os dados
        $dql .= " ORDER BY v.ordem ASC";

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }
        
//        die($query->getSQL());
        
        $videosSite = $query->getResult();
        
        foreach ($videosSite as $video) {
            $retorno[] = $this->getEntityManager()->getRepository('Entity\Video')->find($video->getVideo());
        }
                
        return $retorno;
    }

    
   
    
    
    public function getUltimosVideosOrder($site = null, $pagNumero = 1, $pagMaximo = 20, $notIn = null)
    {
        $query = $this->createQueryBuilder('v')
                    ->distinct()
                    ->join('v.site', 'sites')
                    ->join('v.video', 'videos')
                    ->where('videos.publicado = 1')
                    ->andWhere('videos.dataInicial < :today')
                    ->andWhere('videos.dataFinal > :today OR videos.dataFinal IS NULL')
                    ->orderBy('v.ordem', 'ASC')
                    ->setParameter('today', new \DateTime('now'));

        if (!empty($site)) {
            $query->andWhere('sites.id = :siteId')
                  ->setParameter('siteId', $site->getId());
        } else {
            $query->andWhere("sites.sigla = 'SEDE'");
        }
        if(!empty($notIn)) {
            $query->andWhere("videos.id NOT IN (:videosNotIn)");
            $query->setParameter('videosNotIn', $notIn);
        }
        $query->setFirstResult(($pagNumero * $pagMaximo) - $pagMaximo)
            ->setMaxResults($pagMaximo);
        try {
            
            $array_videos = $query->getQuery()->getResult();
            
            foreach ($array_videos as $video) {
                $videos[] = $this->getEntityManager()->getRepository('Entity\Video')->find($video->getVideo());
            }
            $return['videos'] = $videos;
            $return['pagina'] = $query->getQuery();
           return $return;
            
            
           // return $query->getQuery()->getSQL();
            
//             echo "<div style='display:block'>";
//                 echo "<pre>";
//                     var_dump($return['videos']); 
//                 echo "</pre>";
//             echo "</div>"; 

//             echo "<pre>";
//             exit(\Doctrine\Common\Util\Debug::dump($return));
//             echo "</pre>";
//             die();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    
     public function buscarUltimaOrdem($site){
        
        $dql = "SELECT (max(g.ordem)+1) as ordem FROM Entity\VideoSite g WHERE g.site = :site";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("site",$site);
        $aux = $query->getResult();
        
        foreach ($aux as $a){
            $ordem = !$a["ordem"] ? 1 : $a["ordem"];
        }
        
       return $ordem;
    }
    
	public function getTotalBuscaVideo($filtro, array $user)
    {
        //Estrutura o dql da busca
        $dql = "SELECT COUNT(DISTINCT v) total FROM Entity\VideoSite v JOIN v.site sit JOIN v.video vid WHERE 1 = 1 ";
        $parametros = array();

        if (!empty($filtro['busca'])) {
            $dql .= " AND (LOWER(vid.nome) LIKE :nome OR LOWER(vid.link) LIKE :link)";
            $parametros['nome'] = "%" . $filtro['busca'] . "%";
            $parametros['link'] = "%" . $filtro['busca'] . "%";
        }

        if ($filtro['status'] !== "") {
            $dql .= " AND vid.publicado = :publicado ";
            $parametros['publicado'] = $filtro['status'];
        }

        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (vid.dataCadastro BETWEEN :data_inicial AND :data_final)";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_final'])) {
            $dql .= " AND vid.dataCadastro < :data_final ";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_inicial'])) {
            $dql .= " AND (vid.dataCadastro > :data_inicial ) ";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
        }

        //Verifica se o usuário logado é da sede
        if (!$user['sede']) {
            $dql .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }
        
        //Caso o usuário for sede, poderá buscar por sites
        if (!empty($filtro['site'])) {
            $dql .= " AND sit.id = :id_site ";
            $parametros['id_site'] = $filtro['site'];
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        
        $resultado = $query->getResult();
        return $resultado[0]['total'];
    }
    
}
