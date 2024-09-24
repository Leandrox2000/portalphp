<?php

namespace Entity\Repository;

use Helpers\Session;

/**
 * Description of AgendaRepository
 *
 * @author Luciano
 */
class AgendaRepository extends BaseRepository
{

    /**
     *
     * @var array
     */
    private $where;

    /**
     *
     * @var Session
     */
    private $session;
    
    public function getRegisterByAgenda($idagenda)
    {
        
        $query = $this->createQueryBuilder('v')
                        ->where('v.id_agenda = :id')
                        ->setParameter('id', $idagenda);
        
        $retorno = $query->getQuery()->getResult();
        
        return $retorno;
    }

    public function getConteudoInterna($site = NULL, $data = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->join('a.sites', 's')
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL');

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
            $query->andWhere('a.periodoInicial >= :dataInicioDia')
                  ->andWhere('a.periodoInicial <= :dataFinalDia')
                  ->setParameter('dataInicioDia', $dateStartDay)
                  ->setParameter('dataFinalDia', $dateEndDay);
        }

        return $query->orderBy('a.dataInicial', 'ASC')
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME);
    }
    
    /**
     *
     * @param \Entity\Site $site
     * @return array
     */
    public function getConteudoHome($site = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->join('a.sites', 's')
                      ->andWhere('a.publicado = 1')
                      // ->andWhere('a.periodoInicial >= :today')
                      ->andWhere('a.periodoFinal >= :today OR a.periodoFinal IS NULL')
                      ->andWhere('a.dataFinal >= :today OR a.dataFinal IS NULL');

                      // Alterada a data base para cálculo de prazo para mostrar os eventos da agenda
                      // ->andWhere('a.dataInicial < :today')

        if ($site instanceof \Entity\Site) 
             $query->andWhere("LOWER(s.sigla) = LOWER(:sigla)")->setParameter('sigla', $site->getSigla());
        // } else {
        //     $query->andWhere("s.sigla IN ('SEDE')");
        // }

        return $query->orderBy('a.periodoInicial', 'ASC')
                     ->setMaxResults(4)
                     ->setParameter('today', new \DateTime('now'))
                     ->getQuery()
                     ->useQueryCache(TRUE)
                     ->useResultCache(TRUE, CACHE_LIFE_TIME)
                     ->getResult();
    }

    /**
     * Retorna a Query da Agenda
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryAgenda()
    {
        $query = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select("A")
                        ->distinct()
                        ->from($this->getEntityName(), "A")
                        ->join("A.sites", "sites");


        return $query;
    }


    /**
     * Seta  o Where da Busca
     *
     * @param \Doctrine\ORM\QueryBuilder
     * @param array $where
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function setWhere(\Doctrine\ORM\QueryBuilder $query, array $where = array())
    {
        if (empty($this->where)) {
            $this->where = $where;
        }

        $user           = $this->session->get("user");
        $busca          = mb_strtolower($this->where['busca'], "UTF-8");
        $status         = $this->where['status'];
        $site           = $this->where['site'];
        $data_inicial   = $this->where['data_inicial'];
        $data_final     = $this->where['data_final'];


        if (!empty($busca)) {
            $query->andWhere(
                    $this->getEntityManager()
                            ->createQueryBuilder()
                            ->orWhere($query->expr()->like($query->expr()->lower("A.titulo"), ":busca"))
                            ->orWhere($query->expr()->like($query->expr()->lower("A.descricao"), ":busca"))
                            ->orWhere($query->expr()->like($query->expr()->lower("A.local"), ":busca"))
                            ->orWhere($query->expr()->like($query->expr()->lower("A.cidade"), ":busca"))
                            ->getDQLPart("where")
            );
            $query->setParameter("busca", "%{$busca}%");
        }

        if ($status !== "") {
            $query->andWhere($query->expr()->eq("A.publicado", ":status"));
            $query->setParameter("status", $status);
        }

        if (!empty($data_inicial) && !empty($data_final)) {
            $query->andWhere($query->expr()->between("A.dataCadastro", "'{$data_inicial} 00:00'", "'{$data_final} 23:59'"));
        }

        if ($user['sede']) {
            if ($site>0) {
                $query->andWhere($query->expr()->eq("sites.id", ":site"));
                $query->setParameter("site", $site);
            }
        } else {
            //$query->andWhere($query->expr()->eq("sites.id", ":site"));
            $query->andWhere("sites.id IN(:sites)");
            $query->setParameter("sites", $user['subsites']);
        }

        return $query;
    }


    /**
     *
     * @param type $limit
     * @param type $offset
     * @param array $filtro
     * @param \Helpers\Session $session
     * @return type
     */
    public function getAgenda($limit, $offset, array $filtro, Session $session)
    {
        $this->session = $session;

        $query = $this->getQueryAgenda();

        $query = $this->setWhere($query, $filtro);

        $query->orderBy('A.id', 'DESC');
        
        if($limit>0){
        $query->setMaxResults($limit);
        }
        $query->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    /**
     * Número total de Conselheiros com filtro
     *
     * @return int
     */
    public function getMaxResult()
    {
        $query = $this->getQueryAgenda();

        $query->select($query->expr()->countDistinct("A"));

        $query = $this->setWhere($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Numero total de Conselheiros do Banco
     *
     * @return int
     */
    public function countAll($user)
    {
        //monta o dql
        $dql = "SELECT COUNT(DISTINCT ga) FROM Entity\Agenda ga  JOIN ga.sites sit ";

        //Verifica se o usuário é da sede
        $parametros = array();
        if (!$user['sede']) {
            $dql .= " WHERE sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }


        //Executa a query e retorna o resultado
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        return $query->getSingleScalarResult();
    }

    public function listaMarcada($site = NULL)
    {
        $query = $this->createQueryBuilder('a')
                      ->distinct()
                      ->andWhere('a.publicado = 1')
                      ->andWhere('a.dataInicial < :today')
                      ->andWhere('a.dataFinal > :today OR a.dataFinal IS NULL')
                      ->select('a.periodoInicial')
                      ->join('a.sites', 's');

        if ($site instanceof \Entity\Site) {
            $query->andWhere("s.sigla = :sigla")
                  ->setParameter('sigla', $site->getSigla());
        } else {
            $query->andWhere("s.sigla IN ('SEDE')");
        }

        return $query
                ->setParameter('today', new \DateTime('now'))
                ->groupBy('a.periodoInicial')->getQuery()->getResult();
    }

    public function getCompartilhadosById($id) 
    {
        $em = $this->getEntityManager();
        $compartilhado = 0;
        
        $sitesVinculados = $em->find("Entity\Agenda", $id)->getSites();
        $sitesPai = $em->find("Entity\Agenda", $id)->getPaiSites();
        
        if (count($sitesVinculados) > 0 && count($sitesPai) > 0) {
            
            foreach ($sitesVinculados as $vinculado) {
                $idsVinculados[] = $vinculado->getId();
            }
            
            foreach ($sitesPai as $pai) {
                $idsPai[] = $pai->getId();
            }
            
            foreach ($idsVinculados as $vinculado) {
                if (!in_array($vinculado, $idsPai) && in_array($vinculado, $_SESSION['user']['subsites'])) {
                    $compartilhado = 1;
                }
            }
        }
        
        return $compartilhado;
    }
    
    public function getCompartilhados() {
        
        $em = $this->getEntityManager();
        
        foreach ($_SESSION['user']['subsites'] as $sitesUser) {
            
            $agendasVinculadas = $em->find("Entity\Site", $sitesUser)->getAgendas();
            $agendasPai = $em->find("Entity\Site", $sitesUser)->getPaiAgendas();
            
            foreach ($agendasVinculadas as $vinculada) {
                $vinculados[] = $vinculada->getId();
            }

            foreach ($agendasPai as $pai) {
                $pais[] = $pai->getId();
            }
            
        }
       
        $idsVinculados = array_unique($vinculados);
        $idsPai = array_unique($pais);
        
        foreach ($idsVinculados as $vinculado) {
            if (!in_array($vinculado, $idsPai)) {
                $retorno[] = $vinculado;
            }
        }
        
        if (!empty($retorno)) {
            $ret = array_unique($retorno);
        } else {
            $ret = 0;
        }
        
        return $ret;
        
    }
    
    // BUSCA OS REGISTROS COMPARTILHADOS
    public function getRegistrosCompartilhados()
    {
        $em = $this->getEntityManager();
        
        $agendasPai = $em->find("Entity\Site", $_REQUEST['site'])->getAgendas();
        $vinculados = $this->getCompartilhados();
        
        if (empty($vinculados)) {
            return 0;
        }
        
        foreach ($agendasPai as $pai) {
            if (in_array($pai->getId(), $vinculados)) {
                $retorno[] = $pai->getId();
            }
        }
        
        return $retorno;
    }
}
