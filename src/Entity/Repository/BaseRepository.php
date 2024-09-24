<?php

namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Description of BaseRepository
 *
 * @author Luciano
 */
class BaseRepository extends EntityRepository
{

    /**
     * Retorna um item, caso o mesmo esteja publicado.
     *
     * @param integer $id
     * @return object|boolean
     */
    public function getPublicado($id, $verificarPublicado = true)
    {
        try {
            $query = $this->createQueryBuilder('e')
                          ->distinct()
                          ->andWhere('e.id = :id');

            if ($verificarPublicado) {
                $query->andWhere('e.publicado = 1')
                      ->andWhere('e.dataInicial <= :today')
                      ->andWhere('e.dataFinal >= :today OR e.dataFinal IS NULL')
                      ->setParameter('today', new \DateTime('now'));
            }

            return $query->setParameter('id', $id)
                         ->getQuery()
                         ->useQueryCache(true)
                         ->useResultCache(true, CACHE_LIFE_TIME)
                         ->getSingleResult();
        } catch (NoResultException $e) {
            return false;
        }
    }

    /**
     *
     * @param String $ids
     */
    public function getRegistrosPublicacao(array $ids)
    {
        $arrayIds = array();
        $query = $this->getEntityManager()->createQueryBuilder();

        $query->select("E")
                ->from($this->getEntityName(), "E")
                ->andWhere("E.id IN(" . implode(',', $ids) . ")")
                ->andWhere($query->expr()->gte("CURRENT_TIMESTAMP()", "E.dataInicial"));

        $result = $query->getQuery()->getResult();

        foreach ($result as $value) {
            $arrayIds[] = $value->getId();
        }

        return implode(',', $arrayIds);
    }

    /**
     *
     * @param array $ids
     * @return boolean
     */
    public function verificaPeriodoRegistro(array $ids)
    {
        if (count($ids) > 0) {
            $query = $this->getEntityManager()->createQueryBuilder();

            $query->select($query->expr()->count("E"))
                    ->from($this->getEntityName(), "E")
                    ->andWhere("E.id IN(" . implode(',', $ids) . ")")
                    ->andWhere($query->expr()->gte("E.dataInicial", "CURRENT_TIMESTAMP()"));

            return $query->getQuery()->getSingleScalarResult() > 0;
        } else {
            return false;
        }
    }
    
    /**
     *
     * Verifica se o usu�rio possui permiss�o de cadastros
     * 
     * @return boolean
     */
    public function verificaCadastros()
    {
        $permissoes = $_SESSION['user']['permissoesUser'];
        
        if (!empty($permissoes['CADAS_EXCLUIR']) || !empty($permissoes['CADAS_CONSULT']) || !empty($permissoes['CADAS_SALVAR'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     *
     * @param array $ids
     * @return array
     */
    public function findIn(array $ids)
    {
        if (count($ids) > 0) {
            $dql = "SELECT t FROM {$this->getEntityName()} t WHERE t.id IN (:sites) ORDER BY t.nome ASC";
            $query = $this->getEntityManager()
                    ->createQuery($dql)
                    ->setParameter('sites', $ids);

            return $query->getResult();
        } else {
            return array();
        }
    }

    /**
     *
     * @param integer $id
     * @param array $sites
     * @return array
     */
    public function findByIdSite($id, array $sites = NULL)
    {
        $condicao = "";
        $parametros = array('id' => $id);

        if ($sites != NULL) {
            $condicao = " AND sit.id IN(:sites)";
            $parametros['sites'] = $sites;
        }
        $dql = "SELECT DISTINCT t FROM {$this->getEntityName()} t JOIN t.sites sit WHERE t.id = :id $condicao";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $result = $query->getResult();

        return isset($result[0]) ? $result[0] : false;
    }

    /**
     *
     * @param integer $id
     * @param array $sites
     * @return array
     */
    public function findByIdSiteCountEntity($id)
    {
        $dql = "SELECT COUNT(t.id) FROM {$this->getEntityName()} t JOIN t.sites sit WHERE sit.id IN(:id) GROUP BY t.id";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('id', $id);
        $result = $query->getResult();

        return isset($result[0]) ? $result[0] : false;
    }

    /**
     *
     * @param array $id
     * @param integer $publicado
     * @return array
     */
    public function getIdsPublicacao(array $id, $publicado){
        $dql = "SELECT t.id FROM {$this->getEntityName()} t WHERE t.publicado = {$publicado} AND t.id IN(:id) ";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters(array('id' => $id));
        $result = $query->getResult();
        $array = array();

        foreach($result as $valor){
            $array[] = $valor['id'];
        }

        return $array;

    }

    /**
     * Retorna dados de subsites vinculados a um usuário
     */
    public function getDataByUser($entity, $site, $limit = 3)
    {
        $dql = "SELECT ga FROM $entity ga JOIN ga.sites sit WHERE 1 = 1 ";
        $dql .= " AND sit.id = :id_site and ga.publicado = 1 ";
        $dql .= " ORDER BY ga.dataCadastro DESC ";

        $query = $this->getEntityManager()->createQuery($dql);
        $parametros['id_site'] = $site;
        $query->setParameters($parametros);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     * @param \Entity\Usuario $user
     * @return array
     */
    public function buscaRegistroByLogin($entity, $limit, $offset, $filtro, array $user, $countPagination = false)
    {
        // VERIFICA SE PRECISA LISTAR EM ORDEM DE POSICIONAMENTO OU CADASTRO
        if ($entity == 'Entity\Galeria' || $entity == 'Entity\Video') {
            if ($entity == 'Entity\Galeria') {
                $ent = "galeriasSite";
            } else {
                $ent = "videosSite";
            }
            $join = "JOIN ga.$ent gs";
            $order = " ORDER BY gs.ordem ASC ";
        } else {
            $join = "";
            $order = " ORDER BY ga.dataCadastro DESC ";
        }

        //Estrutura o sql da busca
        if(!$countPagination){
            $dql = "SELECT ga FROM $entity ga JOIN ga.sites sit $join WHERE 1 = 1 ";
        }else{
            $dql = "SELECT COUNT(DISTINCT ga) total FROM $entity ga JOIN ga.sites sit $join WHERE 1 = 1 ";
        }
        
        $parametros = array();

//        $dql .= " AND sit.id IN (:sites) ";
//        $parametros['sites'] = $user['subsites'];

        if ($entity == "Entity\Video") {
            if (!empty($filtro['busca'])) {
                $dql .= " AND (semAcento(LOWER(ga.nome)) LIKE semAcento(:nome) OR semAcento(LOWER(ga.link)) LIKE semAcento(:link))";
                $parametros['nome'] = "%" . $filtro['busca'] . "%";
                $parametros['link'] = "%" . $filtro['busca'] . "%";
            }
        } elseif ($entity == "Entity\PaginaEstatica" || $entity == "Entity\Noticia") {
            if (!empty($filtro['busca'])) {
                $dql .= " AND semAcento(LOWER(CONCAT(ga.titulo, ga.conteudo))) LIKE semAcento(:titulo) ";
                $parametros['titulo'] = "%" . $filtro['busca'] . "%";
            }
        } elseif ($entity == "Entity\Edital") {
            if (!empty($filtro['busca'])) {
                $dql .= " AND (semAcento(LOWER(ga.nome)) LIKE semAcento(:nome) OR semAcento(LOWER(ga.conteudo)) LIKE semAcento(:conteudo))";
                $parametros['nome'] = "%" . $filtro['busca'] . "%";
                $parametros['conteudo'] = "%" . $filtro['busca'] . "%";
            }
        } elseif ($entity == "Entity\Galeria") {
            if (!empty($filtro['busca'])) {
                $dql .= " AND (semAcento(LOWER(ga.titulo)) LIKE semAcento(:nome) OR semAcento(LOWER(ga.descricao)) LIKE semAcento(:conteudo))";
                $parametros['nome'] = "%" . $filtro['busca'] . "%";
                $parametros['conteudo'] = "%" . $filtro['busca'] . "%";
            }
        } else {
            if (!empty($filtro['busca'])) {
                $dql .= " AND Unaccent(LOWER(CONCAT(ga.titulo, ga.descricao))) LIKE Unaccent(lower(:titulo)) ";
                $parametros['titulo'] = "%" . $filtro['busca'] . "%";
            }
        }

        if (isset($filtro['status'])) {
            if ($filtro['status'] !== "") {
                $ids = $this->getEntityManager()->getRepository($entity)->getCompartilhados();
                if ($filtro['status'] == 2) {
                    $dql .= " AND ga.id IN (:idsCompartilhados) ";
                    $parametros['idsCompartilhados'] = $ids;
                } else if ($filtro['status'] == 1) {
                    $dql .= " AND ga.publicado = :publicado AND ga.id NOT IN (:idsPublicados)";
                    $parametros['publicado'] = 1;
                    $parametros['idsPublicados'] = $ids;
				} else if ($filtro['status'] == 3) {
                    $dql .= " AND ga.flagNoticia = :flagNoticia ";
                    $parametros['flagNoticia'] = 1;
                } else {
                    $dql .= " AND ga.publicado = :publicado ";
                    $parametros['publicado'] = 0;
                }
            }
        }

        #EDITAL
        if (isset($filtro['editalStatus']) and $filtro['editalStatus'] != 0) {
            $dql .= " AND ga.status = :statusEdital ";
            $parametros['statusEdital'] = $filtro['editalStatus'];
        }
        if (isset($filtro['categoria']) and $filtro['categoria'] != 0) {
            $dql .= " AND ga.categoria = :categoria ";
            $parametros['categoria'] = $filtro['categoria'];
        }

        #VIDEO
        if($entity == "Entity\Video"){
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
        } else if ($entity == "Entity\Agenda" || $entity == "Entity\Edital" || $entity == "Entity\Legislacao" || $entity == "Entity\PaginaEstatica" || $entity == "Entity\Noticia") {
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
        } else {
            #GERAL
            if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
                $dql .= " AND (ga.dataCadastro BETWEEN :data_inicial AND :data_final)";
                $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
                $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
            }
        }
        
        //Caso o usuário for sede, poderá buscar por sites
        if ($entity == 'Entity\Galeria' || $entity == 'Entity\Video') {
            if (!empty($filtro['site']) and $filtro['site'] != '') {
                $dql .= " AND sit.id = :id_site AND gs.site = :id_ordem ";
                $parametros['id_site'] = $filtro['site'];
                $parametros['id_ordem'] = $filtro['site'];
            } else {
                $dql .= " AND sit.id IN (:sites) ";
                $parametros['sites'] = $user['subsites'];
            }
        } else {
            if (!empty($filtro['site']) and $filtro['site'] != '') {
                $dql .= " AND sit.id = :id_site ";
                $parametros['id_site'] = $filtro['site'];
            } else {
                $dql .= " AND sit.id IN (:sites) ";
                $parametros['sites'] = $user['subsites'];
            }
        }

        if(!$countPagination){
            //Ordena os dados
            $dql .= $order;
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        if(($limit + $offset) > 0 && !$countPagination){
            $query->setFirstResult($offset)
                  ->setMaxResults($limit);
        }
        
        if(!$countPagination){
           $paginator = new Paginator($query, $fetchJoinCollection = true);
           return $paginator->getIterator(); 
           
        }else{
           return $query->getResult(); 
           
        }
        
    }

    public function getDataBySubsite($entity, $limit, $offset, $filtro, $subsite, $countPagination = false)
    {
        // VERIFICA SE PRECISA LISTAR EM ORDEM DE POSICIONAMENTO OU CADASTRO
        if ($entity == 'Entity\Galeria' || $entity == 'Entity\Video') {
            if ($entity == 'Entity\Galeria') {
                $ent = "galeriasSite";
            } else {
                $ent = "videosSite";
            }
            $join = "JOIN ga.$ent gs";
            $order = " ORDER BY gs.ordem ASC ";
        } else {
            $join = "";
            $order = " ORDER BY ga.dataCadastro DESC ";
        }

        if(!$countPagination){
             //Estrutura o sql da busca
            $dql = "SELECT ga FROM $entity ga JOIN ga.sites sit $join WHERE 1 = 1";
            
        }else{
            //Estrutura o sql da busca
            $dql = "SELECT COUNT(DISTINCT ga) total  FROM $entity ga JOIN ga.sites sit $join WHERE 1 = 1";
        }
       
        $parametros = array();

        if ($entity == "Entity\Video") {
            if (!empty($filtro['busca'])) {
                $dql .= " AND (semAcento(LOWER(ga.nome)) LIKE semAcento(:nome) OR semAcento(LOWER(ga.link)) LIKE semAcento(:link))";
                $parametros['nome'] = "%" . $filtro['busca'] . "%";
                $parametros['link'] = "%" . $filtro['busca'] . "%";
            }
        } elseif ($entity == "Entity\PaginaEstatica" || $entity == "Entity\Noticia") {
            if (!empty($filtro['busca'])) {
                $dql .= " AND semAcento(LOWER(CONCAT(ga.titulo, ga.conteudo))) LIKE semAcento(:titulo) ";
                $parametros['titulo'] = "%" . $filtro['busca'] . "%";
            }
        } elseif ($entity == "Entity\Edital") {
            if (!empty($filtro['busca'])) {
                $dql .= " AND (semAcento(LOWER(ga.nome)) LIKE semAcento(:nome) OR semAcento(LOWER(ga.conteudo)) LIKE semAcento(:conteudo))";
                $parametros['nome'] = "%" . $filtro['busca'] . "%";
                $parametros['conteudo'] = "%" . $filtro['busca'] . "%";
            }
        } else {
            if (!empty($filtro['busca'])) {
                $dql .= " AND Unaccent(LOWER(CONCAT(ga.titulo, ga.descricao))) LIKE Unaccent(lower(:titulo)) ";
//                $dql .= " AND LOWER(CONCAT(ga.titulo, ga.descricao)) LIKE :titulo ";
                $parametros['titulo'] = "%" . $filtro['busca'] . "%";
            }
        }

        if (isset($filtro['status'])) {
            if ($filtro['status'] !== "") {
                $ids = $this->getEntityManager()->getRepository($entity)->getRegistrosCompartilhados();
                if ($filtro['status'] == 2) {
                    $dql .= " AND ga.id IN (:idsCompartilhados) ";
                    $parametros['idsCompartilhados'] = $ids;
                } else if ($filtro['status'] == 1) {
                    $dql .= " AND ga.publicado = :publicado AND ga.id NOT IN (:idsPublicados)";
                    $parametros['publicado'] = 1;
                    $parametros['idsPublicados'] = $ids;
				} else if ($filtro['status'] == 3) {
                    $dql .= " AND ga.flagNoticia = :flagNoticia ";
                    $parametros['flagNoticia'] = 1;
                } else {
                    $dql .= " AND ga.publicado = :publicado ";
                    $parametros['publicado'] = 5;
                }
            } else {
                $dql .= " AND ga.publicado = :publicado ";
                $parametros['publicado'] = 1;
            }
        }

        #VIDEO
        if($entity == "Entity\Video"){
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
        } else if ($entity == "Entity\Edital" || $entity == "Entity\Legislacao" || $entity == "Entity\PaginaEstatica" || $entity == "Entity\Noticia") {
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
        } else {
            #GERAL
            if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
                $dql .= " AND (ga.dataCadastro BETWEEN :data_inicial AND :data_final)";
                $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
                $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
            }
        }


        //Caso o usuário for sede, poderá buscar por sites
        if (!empty($filtro['site']) and $filtro['site'] != '') {
            $dql .= " AND sit.id = :id_site ";
//            $parametros['id_site'] = $filtro['site'];
        }

        //Verifica se o usuário logado é da sede
        if (!$user['sede']) {
            $dql .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $subsite;
        }


        #EDITAL
        if (isset($filtro['editalStatus']) and $filtro['editalStatus'] != 0) {
            $dql .= " AND ga.status = :statusEdital ";
            $parametros['statusEdital'] = $filtro['editalStatus'];
        }
        if (isset($filtro['categoria']) and $filtro['categoria'] != 0) {
            $dql .= " AND ga.categoria = :categoria ";
            $parametros['categoria'] = $filtro['categoria'];
        }

        if(!$countPagination){
            //Ordena os dados
            $dql .= $order;
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $parametros['id_site'] = $subsite;
        $query->setParameters($parametros);

        if(($limit + $offset) > 0 && !$countPagination){
            $query->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        if(!$countPagination){
           $paginator = new Paginator($query, $fetchJoinCollection = true);
           return $paginator->getIterator(); 
           
        }else{
            return $query->getResult();
        }
    }

    //Pega sites, siglas de um registro
    public function getSubsiteFromRegister()
    {
        $this->getEntityManager()->getConnection()->query("SELECT * FROM");
    }

    //Valida se usua é vinculado ao subsite do registro
    public function validaSubsiteVinculado($entity, $id, $json = TRUE)
    {
        $subsiteUser = $_SESSION['user']['subsites'];

        $repository = $this->getEntityManager()->getRepository($entity);

        $subsitesVinculados = $repository->find($id)->getSites();

        $permissao = false;

        foreach ($subsitesVinculados as $subsiteVinculado) {
            if(in_array($subsiteVinculado->getId(), $subsiteUser)) {
                $permissao = true;
            }
        }

        if ($json == FALSE) {
            return $permissao;
        } else {
            return json_encode(array('permissao' => $permissao));
        }
    }

    // COMPARTILHA OS SITES SELECIONADOS
    public function compartilhaRegistro($entity, $id, $table)
    {
        $repository = $this->getEntityManager()->getRepository($entity);

        $subsitesVinculados = $repository->find($id)->getSites();

        foreach ($subsitesVinculados as $subsiteVinculado) {
            if (!in_array($subsiteVinculado->getId(), $_REQUEST['sites'])) {
                $arrDelete[] = $subsiteVinculado->getId();
            }
        }

        $this->getEntityManager()->getConnection()->query("DELETE FROM tb_{$table}_site WHERE id_{$table} = ".$id." AND id_site IN (".implode(",", $arrDelete).")");
    }

    //Valida se subsites vinculados ao usuario são pais do registro
    public function validaVinculo($ids, $table)
    {
        $mySubsitesId = $_SESSION['user']['subsites'];
        $idSubsites = array();
        $connection = $this->getEntityManager()->getConnection();
        $statment = $connection->query("SELECT id_site FROM tb_pai_{$table}_site WHERE id_{$table} IN( {$ids})");
        $rows = $statment->fetchAll();

        if (!empty($rows)) {
            foreach ($rows as $row) {

                $idSubsites[] = $row['id_site'];
            }
        }

        $permissao = false;
        foreach ($mySubsitesId as $value) {

            if (in_array($value, $idSubsites)) {
                $permissao = true;
                break;
            }
        }

        return $permissao;
    }

    /**
     * Verifica se existem registros compartilhados e retorna true ou false
     *
     * @param int $id
     * @return int
     */
    public function getCompartilhadosById($id, $entity)
    {
        $em = $this->getEntityManager();
        $compartilhado = 0;

        $sitesVinculados = $em->find($entity, $id)->getSites();
        $sitesPai = $em->find($entity, $id)->getPaiSites();

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

    public function getTotalBusca($entity, $filtro, array $user)
    {
        //Estrutura o sql da busca
        $dql = "SELECT COUNT(DISTINCT ga) total FROM $entity ga JOIN ga.sites sit WHERE 1 = 1 ";
        $parametros = array();

        if ($entity == "Entity\Video") {
            if (!empty($filtro['busca'])) {
                $dql .= " AND (LOWER(ga.nome) LIKE :nome OR LOWER(ga.link) LIKE :link)";
                $parametros['nome'] = "%" . $filtro['busca'] . "%";
                $parametros['link'] = "%" . $filtro['busca'] . "%";
            }
        } elseif ($entity == "Entity\PaginaEstatica" || $entity == "Entity\Noticia") {
            if (!empty($filtro['busca'])) {
                $dql .= " AND LOWER(CONCAT(ga.titulo, ga.conteudo)) LIKE :titulo ";
                $parametros['titulo'] = "%" . $filtro['busca'] . "%";
            }
        } elseif ($entity == "Entity\Edital") {
            if (!empty($filtro['busca'])) {
                $dql .= " AND (LOWER(ga.nome) LIKE :nome OR LOWER(ga.conteudo) LIKE :conteudo)";
                $parametros['nome'] = "%" . $filtro['busca'] . "%";
                $parametros['conteudo'] = "%" . $filtro['busca'] . "%";
            }
        } else {
            if (!empty($filtro['busca'])) {
                $dql .= " AND LOWER(CONCAT(ga.titulo, ga.descricao)) LIKE :titulo ";
                $parametros['titulo'] = "%" . $filtro['busca'] . "%";
            }
        }

        if (isset($filtro['status'])) {
            if ($filtro['status'] !== "") {
                $dql .= " AND ga.publicado = :publicado ";
                $parametros['publicado'] = $filtro['status'];
            }
        }

        #VIDEO
        if($entity == "Entity\Video"){
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
        } else if ($entity == "Entity\Edital" || $entity == "Entity\Legislacao" || $entity == "Entity\PaginaEstatica" || $entity == "Entity\Noticia") {
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
        } else {
            #GERAL
            if (!empty($filtro['data_inicial']) && !empty($filtro['data_final'])) {
                $dql .= " AND (ga.dataCadastro BETWEEN :data_inicial AND :data_final)";
                $parametros['data_inicial'] = $filtro['data_inicial'] . " 00:00:00";
                $parametros['data_final'] = $filtro['data_final'] . " 23:59:59";
            }
        }

        //Caso o usuário for sede, poderá buscar por sites
        if (!empty($filtro['site'])) {
            $dql .= " AND sit.id = :id_site ";
            $parametros['id_site'] = $filtro['site'];
        }

        //Verifica se o usuário logado é da sede
        if (!$user['sede']) {
            $dql .= " AND sit.id IN (:sites) ";
            $parametros['sites'] = $user['subsites'];
        }

        //Executa aquery e passa os parâmetros
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $resultado = $query->getResult();
        return $resultado[0]['total'];
    }
}
