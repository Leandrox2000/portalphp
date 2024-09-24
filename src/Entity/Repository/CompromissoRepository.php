<?php
namespace Entity\Repository;

use Helpers\Session;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CompromissoRepository extends BaseRepository
{    
    /**
     * Retorna a quantidade de agendas que pertencem aos sites
     * que o usuário logado pode ver.
     *
     * @param boolean $ver_todos
     * @return int
     */
    public function countAll($user, $ver_todos = false)
    {
        $dql = "
            SELECT COUNT(DISTINCT ga)
            FROM Entity\Compromisso ga ";
        
        $parametros = array();
        if(!$ver_todos) {
            $dql .= "
            JOIN ga.agendasDirecao ad
            JOIN ad.responsaveis resp
            WHERE resp.responsavel = :responsavel";
                    
            $parametros = array(
                'responsavel' => $user['dadosUser']['login'],
            );
        }
        
        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        if(!empty($parametros)) {
            $query->setParameters($parametros);
        }
        return $query->getSingleScalarResult();
    }
    
    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtro
     * @param \Entity\Usuario $user
     * @param boolean $countPagination
     * @param boolean $ver_todos
     * @return array
     */
    public function buscarRegistros($limit, $offset, $filtro, array $user, $countPagination = false, $ver_todos = false)
    {
        //Estrutura o sql da busca
        if(!$countPagination){
            $dql = "SELECT ga FROM Entity\Compromisso ga JOIN ga.agendasDirecao ad JOIN ad.responsaveis resp WHERE 1 = 1 ";
        }else{
            $dql = "SELECT COUNT(DISTINCT ga) total FROM Entity\Compromisso ga JOIN ga.agendasDirecao ad JOIN ad.responsaveis resp WHERE 1 = 1 ";
        }
        
        $parametros = array();        
        if (!empty($filtro['busca'])) {
            $dql .= " AND (";
            $dql .= " Unaccent(LOWER(ga.titulo)) LIKE Unaccent(LOWER(:busca)) ";
            $dql .= " OR Unaccent(LOWER(ga.local)) LIKE Unaccent(LOWER(:busca)) ";
            $dql .= " OR Unaccent(LOWER(ga.participantes)) LIKE Unaccent(LOWER(:busca)) ";
            $dql .= " OR Unaccent(LOWER(ad.titulo)) LIKE Unaccent(LOWER(:busca)) ";
            $dql .= " ) ";
            $parametros['busca'] = "%" . $filtro['busca'] . "%";
        }

        if (isset($filtro['status']) && $filtro['status'] != null) {
            $dql .= " AND ga.publicado = :publicado ";
            $parametros['publicado'] = (int)$filtro['status'];
        }
        
        if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
            $dql .= " AND (ga.dataCadastro BETWEEN :data_inicial AND :data_final  )";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_final'])) {
            $dql .= " AND ga.dataCadastro < :data_final ";
            $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
        } else if (!empty($filtro['data_inicial'])) {
            $dql .= " AND (ga.dataCadastro > :data_inicial ) ";
            $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
        }        
        
        if (!empty($filtro['agenda']) && $filtro['agenda'] != '') {
            $dql .= " AND ad.id = :agenda ";
            $parametros['agenda'] = $filtro['agenda'];
        } elseif(!$ver_todos) {
            $dql .= " AND resp.responsavel = :responsavel";
            $parametros['responsavel'] = $user['dadosUser']['login'];
        }

        if(!$countPagination){
            //Ordena os dados
            $dql .= " ORDER BY ga.dataCadastro DESC ";
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        if(($limit + $offset) > 0 && !$countPagination){
            $query->setFirstResult($offset)
                  ->setMaxResults($limit);
        }
        
        if(!$countPagination) {
           $paginator = new Paginator($query, $fetchJoinCollection = true);
           return $paginator->getIterator();
        } else {
           return $query->getResult();
        }
        
    }
    
    public function getCompromissos($id_agenda, $site = NULL, $data = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->join('a.agendasDirecao', 'ga')
                      ->join('ga.sites', 's')
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL')
                      ->andWhere('ga.id = :id');

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.sigla = :sigla")
                  ->setParameter('sigla', $site->getSigla());
        } else {
            $query->andWhere("s.sigla IN ('SEDE')");
        }

        if (!empty($data)) {
            $date = new \DateTime($data);
            $dateStartDay = $date->format('Y-m-d') . ' 00:00:00';
            $dateEndDay = $date->format('Y-m-d') . ' 23:59:59';
            $query->andWhere('(a.compromissoInicial >= :dataInicioDia AND a.compromissoInicial <= :dataFinalDia) OR (:data BETWEEN a.compromissoInicial AND a.compromissoFinal)')
                  ->setParameter('data', $data)
                  ->setParameter('dataInicioDia', $dateStartDay)
                  ->setParameter('dataFinalDia', $dateEndDay);
        }
        
        return $query->orderBy('a.dataInicial', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->setParameter('id', $id_agenda)
                     ->getQuery();
    }
    
    public function listaMarcada($id_agenda, $site = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL')
                      ->select('a.compromissoInicial, a.compromissoFinal')
                      ->join('a.agendasDirecao', 'ga')
                      ->join('ga.sites', 's')
                      ->andWhere('ga.id = :id');

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.sigla = :sigla")
                  ->setParameter('sigla', $site->getSigla());
        } else {
            $query->andWhere("s.sigla IN ('SEDE')");
        }
        
        $resultado = $query
            ->setParameter('today', new \DateTime('now'))
            ->setParameter('id', $id_agenda)
            ->groupBy('a.compromissoInicial, a.compromissoFinal')
            ->orderBy('a.compromissoInicial', 'ASC')
            ->getQuery()->getResult();
        
        $compromissos = array();
        foreach($resultado as $compromisso) {
            // Compromissos de um dia só
            if(empty($compromisso['compromissoFinal'])) {
                $compromissos[] = array(
                    'compromissoInicial' => array(
                        'date' => $compromisso['compromissoInicial']->format('Y-m-d H:i:s')
                    ),
                    'compromissoFinal' => null
                );
                continue;
            }
            
            // Passa as datas ignorando as horas
            $periodo = new \DatePeriod(
                new \DateTime($compromisso['compromissoInicial']->format('Y-m-d')),
                new \DateInterval('P1D'),
                new \DateTime($compromisso['compromissoFinal']->format('Y-m-d') . ' +1 day')
            );
            
            // Monta o range de datas
            foreach ($periodo as $data) {     
                $compromissos[] = array(
                    'compromissoInicial' => array(
                        'date' => $data->format('Y-m-d') .' '. $compromisso['compromissoInicial']->format('H:i:s')
                    ),
                    'compromissoFinal' => array(
                        'date' => $data->format('Y-m-d') .' '. $compromisso['compromissoFinal']->format('H:i:s')
                    ),
                );
            }
        }

        return $compromissos;
    }

}
