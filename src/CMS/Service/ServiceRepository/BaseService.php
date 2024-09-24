<?php

namespace CMS\Service\ServiceRepository;

use Entity\EntityInterface;
use Doctrine\ORM\EntityManager;
use Logger;
use CMS\StaticMethods\StaticMethods;
use Doctrine\Common\Collections\ArrayCollection;
use Helpers\Param;
use Helpers\String as StringHelper;
use Helpers\Mail;

/**
 * Classe BaseService
 *
 * Abstrai as operações de insert, update e delete
 * @author Luciano
 */
abstract class BaseService
{

    /**
     *
     * @var EntityManager
     */
    protected $em;

    /**
     *
     * @var \Helpers\SolrManager
     */
    protected $solrManager;

    /**
     *
     * @var EntityInterface
     */
    protected $entity;

    /**
     *
     * @var Logger Description
     */
    protected $logger;

    /**
     *
     * @var StaticMethods
     */
    protected $staticMethods;

    /**
     *
     * @var \Helpers\Session
     */
    protected $session;

    /**
     *
     * @var StringHelper
     */
    protected $stringHelper;

    /**
     * Metodo __construct
     *
     * Necessário um Entity Manager para começar
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->setEm($em);
    }

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Retorna EntityManager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     * @return Logger
     */
    public function getLogger()
    {
        if (!isset($this->logger))
            $this->logger = $this->getStaticMethods()->getLoggerFactory();
        return $this->logger;
    }

    /**
     *
     * @param EntityInterface $entity
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     *
     * @param StaticMethods $staticMethods
     */
    public function setStaticMethods(StaticMethods $staticMethods)
    {
        $this->staticMethods = $staticMethods;
    }

    /**
     *
     * @return type new \CMS\StaticMethods\StaticMethods()
     */
    public function getStaticMethods()
    {
        if (!isset($this->staticMethods))
            $this->staticMethods = new StaticMethods();
        return $this->staticMethods;
    }

    /**
     * Metodo getNameEntity
     *
     * Retorna o nome da entity
     * @return String
     */
    public function getNameEntity()
    {
        return get_class($this->getEntity());
    }

    /**
     *
     * @return \Helpers\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     *
     * @param \Helpers\Session $session
     */
    public function setSession(\Helpers\Session $session)
    {
        $this->session = $session;
    }

    /**
     *
     * @return StringHelper
     */
    public function getStringHelper()
    {
        if (!isset($this->stringHelper)) {
            $this->setStringHelper(new StringHelper());
        }
        return $this->stringHelper;
    }

    /**
     *
     * @param \Helpers\String $stringHelper
     */
    public function setStringHelper(StringHelper $stringHelper)
    {
        $this->stringHelper = $stringHelper;
    }

    /**
     *
     * @return string
     */
    public function getNameUser()
    {
        $user = $this->getSession()->get('user');
        return $user['dadosUser']['login'];
    }

    /**
     *
     * @return string
     */
    public function getIpUser()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public function getSolrManager()
    {
        if ($this->solrManager === null) {
            $this->solrManager = new \Helpers\SolrManager();
        }

        return $this->solrManager;
    }

    /**
     * Metodo save
     *
     * Salva ou atualiza os dados de um registro.
     * @param array $dados
     * @return mixed
     */
    public function save(array $dados, $sites = false)
    {
        //Busca o id
        $id = isset($dados['id']) ? $dados['id'] : 0;
        unset($dados['id']);
        //Se o id foi encontrado atualiza, se não, insere
        if ($id > 0) {
            $retuen = $this->update($dados, $id, $sites);
        } else {
            $retuen = $this->insert($dados, $sites);
        }

        //Retorna o objeto
        return $retuen;
    }

    /**
     * Metodo insert
     *
     * Insere dados na tabela
     * @param array $dados
     * @return mixed
     */
    protected function insert(array $dados, $sites = false)
    {
        //Armazena o objeto da entidade
        $entity = $this->getEntity();

        //Seta os dados da entity
        $this->setEntityDados($dados, $entity);

        //Inseri os Sites
        if ($sites) {
            $entity = $this->inseriSites($entity);
        }

        //Faz o insert
        try {
            $this->getEm()->persist($entity);
            $this->getEm()->flush();
            $this->getLogger()->info("[{$this->getNameEntity()}] Inserido novo Registro ID " . $entity->getId() . " - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");
        } catch (\Exception $ex) {
            $this->getLogger()->error($ex->getMessage());

            throw $ex;
            //throw new \Exception("Não foi possível Inserir o registro");
        }

        //Retorna a intância da entidade
        return $entity;
    }

    /**
     * Metodo update
     *
     * Atualiza um registro no banco
     * @param array $dados
     * @param type $id
     * @return mixed
     */
    protected function update(array $dados, $id, $sites = false)
    {
        //Busca a referência da entidade
        $entity = $this->getEm()->getReference($this->getNameEntity(), $id);

        //Seta os dados
        $this->setEntityDados($dados, $entity);

        //Inseri os Sites
        if ($sites) {
            $entity = $this->inseriSites($entity, $id);
        }

        try {
            $this->getEm()->persist($entity);
            $this->getEm()->flush();
            $this->getLogger()->info("[{$this->getNameEntity()}] - Registro Alterado ID " . $entity->getId() . " - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");
        } catch (\Exception $exc) {
            $this->getLogger()->error($exc->getMessage());
            throw new \Exception("Não foi possível Atualizar o registro");
        }

        //Retorna a referência
        return $entity;
    }

    /**
     * Metodo delete
     *
     * Delete um registro da tabela
     * @param array $ids
     * @param String $campo
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function delete(array $ids, $campo = 'id')
    {
        foreach ($ids as $id) {
            if (!is_numeric($id)) {
                throw new \InvalidArgumentException("Id deve ser Númerico");
            }
        }
        $query = $this->getEm()
            ->createQueryBuilder();

        $query->delete($this->getNameEntity() . " tbl")
            ->andWhere($query->expr()->in("tbl." . $campo, implode(",", $ids)));

        $this->getLogger()->info("[{$this->getNameEntity()}] - Registros deletados ID " . implode(",", $ids) . " - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");

        try {
            $query->getQuery()->execute();
        } catch (\Exception $exc) {
            $this->getLogger()->error($exc->getMessage());
            throw new \Exception("Não foi possível excluir os registros.");
        }
        return TRUE;
    }

    /**
     * Apaga as entidades juntamente com seus relacionamentos.
     *
     * @param array $ids
     * @return boolean
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function deleteWithRelations(array $ids)
    {
        foreach ($ids as $id) {
            if (!is_numeric($id)) {
                throw new \InvalidArgumentException("Id deve ser Númerico");
            }
        }

        try {
            $this->getEm()->beginTransaction();

            foreach ($ids as $id) {
                $entity = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($entity);
                $this->getEm()->flush();
            }

            $this->getEm()->commit();

            $this->getLogger()->info("[{$this->getNameEntity()}] - Registros deletados ID " . implode(",", $ids) . " - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            throw new \Exception("Não foi possível excluir os registros.");
        }

        return TRUE;
    }

    /**
     * Seta os Dados na Entidade com o Padrão definido
     * @param array $dados
     * @param string $entity
     * @return mixed
     */
    protected function setEntityDados(array $dados, $entity)
    {
        foreach ($dados as $key => $value) {
            $set = "set" . ucfirst($key);
            $entity->$set($value);
        }

        return $entity;
    }

    /**
     * Zera a Data final de publicação
     *
     * @param type $ids
     */
    protected function zeraDataFinal($ids)
    {
        if (!empty($ids)) {
            try {
                $query = $this->getEm()->createQueryBuilder();
                $query->update($this->getNameEntity(), "E")
                    ->set("E.dataFinal", "NULL")
                    ->andWhere($query->expr()->in("E.id", $ids))
                    ->andWhere($query->expr()->gte("CURRENT_TIMESTAMP()", "E.dataFinal"));
                $query->getQuery()->execute();
            } catch (Exception $exc) {
                $this->getLogger()->error($exc->getTraceAsString());
            }
        }
    }

    /**
     * Seta o novo Status dos Registros
     *
     * @param string $ids
     * @param int $status
     * @throws Exception
     */
    public function setaStatus($ids, $status)
    {
        if (!empty($ids)) {
            try {
                $query = $this->getEm()->createQueryBuilder();
                $query->update($this->getNameEntity(), "P")
                    ->set("P.publicado", $status)
                    ->andWhere($query->expr()->in("P.id", $ids));
                $query->getQuery()->execute();
            } catch (Exception $exc) {
                $this->getLogger()->error($exc->getTraceAsString());
                throw new \Exception("Não foi possível alterar o Status");
            }
        }
    }

    protected function desvinculaRegistro($idsParam, $entity, $table)
    {
        $replace = str_replace(" ", "", $idsParam);
        $ids = explode(",", $replace);

        foreach ($ids as $key => $id) {

            $sitesVinculados = $this->getEm()->find($entity, $id)->getSites();
            $sitesPai = $this->getEm()->find($entity, $id)->getPaiSites();

            foreach ($sitesVinculados as $vinculado) {
                if (!empty($vinculado)) {
                    $igual = 0;
                    foreach ($sitesPai as $pai) {
                        if (!empty($pai)) {
                            if ($vinculado->getId() == $pai->getId()) {
                                $igual = 1;
                            }
                        }
                    }
                    if ($igual == 0) {
                        $this->getEm()->getConnection()->query("DELETE FROM tb_{$table}_site WHERE id_{$table} = $id AND id_site = " . $vinculado->getId());
                    }
                }
            }
        }
    }


    /**
     * Seta o novo Status dos Registros
     *
     * @param string $ids
     * @param int $status
     * @throws Exception
     */
    protected function setaStatusNoticia($ids, $status)
    {
        if (!empty($ids)) {
            try {
                $query = $this->getEm()->createQueryBuilder();
                $query->update($this->getNameEntity(), "P")
                    ->set("P.publicado", $status)
                    ->set("P.flagNoticia", 0)
                    ->andWhere($query->expr()->in("P.id", $ids));
                $query->getQuery()->execute();
            } catch (Exception $exc) {
                $this->getLogger()->error($exc->getTraceAsString());
                throw new \Exception("Não foi possível alterar o Status");
            }
        }
    }

    public function deletaCompartilhadosById($id, $tipo)
    {
        $ids = implode(",", $_SESSION['user']['subsites']);
        $this->getEm()->getConnection()->query("DELETE FROM tb_{$tipo}_site WHERE id_site IN ({$ids}) AND id_{$tipo} = {$id}");
        $this->getEm()->getConnection()->query("DELETE FROM tb_{$tipo}_ordem WHERE id_site IN ({$ids}) AND id_{$tipo} = {$id}");
    }

    /**
     *
     * @param type $entity
     * @throws Exception
     */
    protected function inseriSites($entity, $id = 0)
    {
        $param = new Param();
        $sites = new ArrayCollection();
        $user = $this->getSession()->get('user');

        if ($id != 0 && !$user['sede']) {
            $registro = $this->getEm()->getRepository($this->getNameEntity())->find($id);

            foreach ($registro->getSites() as $site) {
                if (!in_array($site->getId(), $user['subsites'])) {
                    $sites->add($this->getEm()->getReference("Entity\Site", $site->getId()));
                }
            }
        }


        foreach ($param->getArray('sites') as $site) {
            $sites->add($this->getEm()->getReference("Entity\Site", $site));
        }

        // $entity->setPropriedadeSede(1);
        /*      } else {
          if ($entity->getPropriedadeSede() == 1) {
          throw new \Exception("Este registro Pertence a Sede. Não é permitido Alteração.");
          }
          $sites->add($this->getEm()->getReference("Entity\Site", $user->getSite()->getId()));
          } */

        $entity->setSites($sites);

        return $entity;
    }

    /**
     *
     * @param array $ids
     * @param int $status
     * @return type
     */
    public function alterarStatus(array $ids, $status, $tipo = null)
    {
        $response = 0;
        $error = array();
        $success = "Ação executada com sucesso";

        try {
            $ids = implode(",", $ids);
            if ($tipo == 'noticia') {
                $this->setaStatusNoticia($ids, $status);
            } else {
                $this->setaStatus($ids, $status);
            }

            $response = 1;
            $this->getLogger()->info("[{$this->getNameEntity()}] Alterado status IDs " . $ids . " - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");
        } catch (\Exception $exc) {
            $error[] = "Não foi possível executar esta ação";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
    
    public function enviaEmailNoticia($ids)
    {
        $ids = implode(",", $ids);
        $dados_noticia = $this->getEm()->getRepository("Entity\Noticia")->getNoticiaIds($ids);

        $links = array();

        foreach($dados_noticia as $dados){
            foreach($dados->getSites() as $site){
                $siglas[] = $site->getSigla();
            }
            $links[] = array(
                "id"     => $dados->getId(),
                "titulo" => $dados->getTitulo(),
                "siglas" => $siglas
            );
        } 

        $domainCfg = include BASE_PATH . 'config/domain.php';
        //Define URL do portal e cms
        define('URL_PORTAL', $domainCfg['portal']);
        define('URL_CMS', $domainCfg['cms']);

        $mensagem = "A(s) seguinte(s) notícia(s) foi(foram) publicada(s): <br /><br /> \n\n";
        foreach ($links as $link) :
            $mensagem .= "\n Notícia: ". $link['titulo'] ."<br /><br /> \n\n";
            foreach ($link['siglas'] as $sigla) :
                $mensagem .= "\n Subsite: ". $sigla."<br /> \n";
                if ($sigla == "SEDE") :
                    $mensagem .= "Link:";
                    $mensagem .= "<a href='".$domainCfg['portal']."/noticias/detalhes/".$link['id']."'>".$link['titulo']."</a><br /> \n";
                else :
                    $mensagem .= "Link:";
                    $mensagem .= "<a href='".$domainCfg['portal']."/".strtolower($sigla)."/noticias/detalhes/".$link['id']."'>".$link['titulo']."</a><br /> \n";
                endif;
                $mensagem .= "<br /> \n \n";
            endforeach;
        endforeach;
        $mensagem .= "\n\n<br /><br />Email gerado automaticamente pelo sistema Iphan (CMS).";


        $config = require BASE_PATH . 'config/mail.php';
        Mail::phpMailer($config, $mensagem, 'Portal do IPHAN - NOTICIA PUBLICADA');
    }

    public function alterarStatusValidacao(array $ids, $status, $tipo = null, $entity)
    {
        if ($status == 1 && $tipo == "noticia") {
            $this->enviaEmailNoticia($ids);
        }
        
        $aux = explode('\\', $entity);
        if($aux[1] == "PaginaEstatica") {
            $entity_atributo    = "pagina_estatica";
        } else if($aux[1] == "AgendaDirecao") {
            $entity_atributo    = "agenda_direcao";
        }else{
            $entity_atributo    = strtolower($aux[1]);
        }
        $entity_tabela      = 'tb_'.$entity_atributo.'_site';

        $response = 0;
        $error = array();
        $success = "Ação executada com sucesso";

        try {
            if($aux[1] == "Galeria" && !$this->validaVinculoGalerias($ids)) {
                //$response = 1;
                //$success = "Não é possível realizar a despublicação/exclusão de galerias vinculadas a outros subsites.";
                throw new \Exception("Não é possível realizar a despublicação de galerias vinculadas a notícias ou novas páginas.");
            }

            $ids = implode(",", $ids);

            if ($tipo == 'noticia') {
                 if ($this->validaVinculoPai($ids,$entity_tabela,$entity_atributo)) {
                    if ($status == 0) {
                        $this->setaStatusNoticia($ids, $status);
                        $this->desvinculaRegistro($ids, $entity, $tipo);
                    }
                    $this->setaStatusNoticia($ids, $status);
                } else {
                    throw new \Exception('Não foi possível executar esta ação.');
                }
            } else {
                $ret  = $this->validaVinculoPai($ids,$entity_tabela,$entity_atributo);
                if ($ret) {
                    if ($status == 0) {
                        $this->setaStatus($ids, $status);
                        $this->desvinculaRegistro($ids, $entity, $tipo);
                    }
                    $this->setaStatus($ids, $status);
                } else {
                    throw new \Exception('Não foi possível executar esta ação.');
                }
            }

            $response = 1;
            $this->getLogger()->info("[{$this->getNameEntity()}] Alterado status IDs " . $ids . " - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");
        } catch (\Exception $exc) {
            $error[] = $exc->getMessage();
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    public function alterarStatusGaleria(array $ids, $status, $tipo = null)
    {
        $response = 0;
        $error = array();
        $success = "Ação executada com sucesso";

        try {
            $permissao = false;
            $vinculos = array();
            foreach ($ids as $id) {
                if ($this->verificaVinculoNoticia($id) && $this->verificaVinculoPagina($id)) {
                    $permissao = true;
                } else {
                    $vinculos[] = $this->validaVinculo($id);
                    throw new \Exception();
                }
            }

            if ($permissao) {
                $ids = implode(",", $ids);
                if ($tipo == 'noticia') {
                    $this->setaStatusNoticia($ids, $status);
                } else {
                    $this->setaStatus($ids, $status);
                }

                $response = 1;
            }
            $this->getLogger()->info("[{$this->getNameEntity()}] Alterado status IDs " . $ids . " - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");
        } catch (\Exception $exc) {
            $error[] = "Não é possível realizar a despublicação/exclusão pois o SUBSITE NÃO LOGADO X vinculou o registro em questão.";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     *
     * @param array $ids
     * @param int $status
     * @return type
     */
    public function alterarVisivel(array $ids, $status)
    {
        $response = 0;
        $error = array();
        $success = "Ação executada com sucesso";

        try {
            $ids = implode(",", $ids);
            $this->setaVisivel($ids, $status);

            $response = 1;
            $this->getLogger()->info("[{$this->getNameEntity()}] Alterado visibilidade IDs " . $ids . " - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");
        } catch (\Exception $exc) {
            $error[] = "Não foi possível executar esta ação";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     * Seta notícia como Visível Internamente
     *
     * @param string $ids
     * @param int $status
     * @throws Exception
     */
    protected function setaVisivel($ids, $status)
    {
        if (!empty($ids)) {
            try {
                $query = $this->getEm()->createQueryBuilder();
                $query->update($this->getNameEntity(), "P")
                    ->set("P.publicado", $status)
                    ->set("P.flagNoticia", "1")
                    ->andWhere($query->expr()->in("P.id", $ids));
                $query->getQuery()->execute();
            } catch (Exception $exc) {
                $this->getLogger()->error($exc->getTraceAsString());
                throw new \Exception("Não foi possível alterar o Status");
            }
        }
    }

    //Verifica se galeria esta vinculada a notícia
    public function verificaVinculoNoticia($id)
    {
        $connection = $this->getEm()->getConnection();

        $statment = $connection->query("SELECT id_noticia_galeria FROM tb_noticia_galeria ga WHERE ga.id_galeria = {$id}");

        $statment->execute();

        $result = $statment->fetchAll();

        if (empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    //Verifica se galeria esta vinculada a novas páginas
    public function verificaVinculoPagina($id)
    {
        $connection = $this->getEm()->getConnection();

        $statment = $connection->query("SELECT id_pagina_estatica_galeria FROM tb_pagina_estatica_galeria ga WHERE ga.id_galeria = {$id}");

        $statment->execute();

        $result = $statment->fetchAll();

        if (empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    //Valida se subsites vinculados ao usuario são pais do registro
    public function validaVinculo($ids,$entity_tabela,$entity_atributo)
    {
        if (!$this->validaCompartilhamentos()) {
            return TRUE;
        }
        
        $mySubsitesId = $_SESSION['user']['subsites'];
        $idSubsites = array();
        $connection = $this->getEm()->getConnection();
        $statment = $connection->query("SELECT id_site FROM {$entity_tabela} WHERE id_{$entity_atributo} IN ({$ids})");
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
     * Valida se na entidade passada existe a funcionalidade de compartilhamento
     * 
     * @param String $entity
     * 
     * @return bool
     */
    public function validaCompartilhamentos()
    {
        $permissao = FALSE;
        
        $array = array(
            "Entity\Agenda",
            "Entity\Edital",
            "Entity\Galeria",
            "Entity\Legislacao",
            "Entity\PaginaEstatica",
            "Entity\Video",
            "Entity\Noticia"
        );
        
        if (in_array($this->getNameEntity(), $array)) {
            $permissao = TRUE;
        }

        return $permissao;
    }
    
    public function stmtToArray($stmt)
    {
        foreach ($stmt as $row) {
            $retorno[] = $row['id_site'];
        }
        
        return $retorno;
    }
    
    public function varificaPaiSessao($idSubsites, $mySubsitesId)
    {
        $permissao = FALSE;
        foreach ($idSubsites as $value) {
            if (in_array($value, $mySubsitesId)) {
                $permissao = TRUE;
                break;
            }
        }
        
        return $permissao;
    }
    
    //Valida se subsites vinculados ao usuario são pais do registro
    public function validaVinculoPai($ids,$entity_tabela,$entity_atributo)
    {
        $tbpai = str_replace("tb_", "tb_pai_", $entity_tabela);
        
        $mySubsitesId = $_SESSION['user']['subsites'];
        $connection = $this->getEm()->getConnection();
        $registros = explode(",", $ids);
        
        if (count($registros) > 1) {
            foreach ($registros as $id) {
                $idSubsites = array();
                $statment = $connection->query("SELECT id_site FROM {$tbpai} WHERE id_{$entity_atributo} = {$id}");
                $rows = $statment->fetchAll();
                
                if(!empty($rows)) {
                    $idSubsites = $this->stmtToArray($rows);
                }
                
                $permissao = $this->varificaPaiSessao($idSubsites, $mySubsitesId);
                
                if ($permissao == FALSE) {
                    break;
                }
            }
        } else {
            $statment = $connection->query("SELECT id_site FROM {$tbpai} WHERE id_{$entity_atributo} IN({$ids})");
            $rows = $statment->fetchAll();

            if(!empty($rows)) {
                $idSubsites = $this->stmtToArray($rows);
            }
            
            $permissao = $this->varificaPaiSessao($idSubsites, $mySubsitesId);
        }

        return $permissao;
    }
    
    public function validaVinculoGalerias(array $ids) 
    {
        $dbal = $this->getEm()->getConnection();
        $permissao = TRUE;

        foreach ($ids as $id) {
            $noticia = $dbal->query("SELECT * FROM tb_noticia_galeria WHERE id_galeria = $id");
            $paginaEstatica = $dbal->query("SELECT * FROM tb_pagina_estatica_galeria WHERE id_galeria = $id");

            if (count($paginaEstatica->fetchAll()) > 0 || count($noticia->fetchAll()) > 0) {
                $permissao = FALSE;
            }
        }
        
        return $permissao;
    }

    public function verificarStatus($ids)
    {
        if (!is_array($ids)) {
            $ids = explode("-", $ids);
        }
        
        foreach ($ids as $id) {
            if ($this->getEm()->find($this->getNameEntity(), $id)->getPublicado()) {
                return TRUE;
            }   
        }

        return FALSE;
    }
}
