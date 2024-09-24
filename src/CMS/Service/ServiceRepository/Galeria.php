<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager as EntityManager;
use Entity\Galeria as GaleriaEntity;
use Entity\GaleriaSite;
use Entity\Repository\BaseRepository;
use Helpers\Session;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe Galeria
 *
 * Responsável pelas ações na entidade Galeria
 * @author join-ti
 */
class Galeria extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Galeria $entity
     */
    public function __construct(EntityManager $em, GaleriaEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    public function getDadosSolr($entity)
    {
        return array(
            'entity_name'       => $this->getNameEntity(),
            'entity_id'         => $entity->getId(),
            'title'             => $entity->getTitulo(),
            'description'       => $entity->getDescricao(),
            'publish'           => $entity->getPublicado(),
            'publish_date'      => $entity->getDataInicial(),
            'unpublish_date'    => $entity->getDataFinal(),
            'url'               => \Helpers\Url::generateRoute('galeria', 'detalhes', $entity),
        );
    }

    /**
     *
     * @param array $dados
     * @return mixed
     */
    public function save(array $dados, $ordem = null)
    {
        $error = array();
        $response = 0;
        $success = "";

        try {
            //Armazena a ação
            $action = empty($dados['id']) ? "inserido" : "alterado";

            $sites = $dados['sites'];

            //Verifica se for update deleta vinculos e pais não selecionado
            if($action == 'alterado') {

                $sitesSession = $_SESSION['user']['subsites'];

                $connection = $this->getEm()->getConnection();

                foreach ($sitesSession as $site) {
                    $connection->query("DELETE FROM tb_galeria_site WHERE id_galeria = {$dados['id']} AND id_site = {$site}");
                    $connection->query("DELETE FROM tb_pai_galeria_site WHERE id_galeria = {$dados['id']} AND id_site = {$site}");
                    $connection->query("DELETE FROM tb_galeria_ordem WHERE id_galeria = {$dados['id']} AND id_site = {$site}");
                }

                $galeria = $this->getEm()->getRepository('Entity\Galeria')->find($dados['id']);

                $sitesArrPai = $galeria->getPaiSites();

                $sitesArr = $galeria->getSites();

                foreach ($sites as $site) {
                    $rSite = $this->getEm()->getReference('Entity\Site', $site);
                    $sitesArr->add($rSite);
                    $sitesArrPai->add($rSite);
                }

                $dados['paiSites'] = $sitesArrPai;

                $dados['sites'] = $sitesArr;

            } else {
                $sitesArr = new ArrayCollection();

                foreach ($sites as $site) {
                    $rSite = $this->getEm()->getReference('Entity\Site', $site);
                    $sitesArr->add($rSite);
                }

                //Salva subsites e pai do registro
                $dados['paiSites'] = $sitesArr;

                $dados['sites'] = $sitesArr;
            }

            //Inseri a relação com imagens
            $imagens = new ArrayCollection();
            $arrayImagens = explode(",", $dados['imagens']);

            foreach ($arrayImagens as $img) {
                $imagens->add($this->getEm()->getReference("Entity\Imagem", $img));
            }

            $dados['imagens'] = $imagens;

            //Faz a edição dos registros
            $entity = parent::save($dados);

            if($ordem){
	            $arrayOrdem = explode(",", $ordem);
	            $i = 1;
	            foreach($arrayOrdem as $id){
	            	$this->getEm()->getRepository('Entity\Imagem')->setOrdemGaleria($entity->getId(),$id, $i);
	            	$i++;
	            }
            }

            $ordensAtuais = $this->getOrdensAtuais($entity->getSites(), $entity->getId());

            #se tem "id" deleta dos sites que não estão aqui
            if($dados['id']) $this->deleteVinculoGaleriaSite($entity->getSites(),$entity->getId());

            $sites = $entity->getSites();
            for($i = 0;$i < count($sites);$i++){
                $array = "";
                $array[] = $sites[$i];
                $this->insertOrdem($array, $entity->getId(),empty($dados['id']) ? "insert" : "update", $ordensAtuais);
            }

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);

            //Monta a mensagem de sucesso
            $success = "Registro $action com sucesso!";
            $response = 1;
        } catch (\Exception $e) {
            $error[] = $e->getMessage();
        }

        //Retorna os resultados
        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    public function getOrdensAtuais($sites, $galeria){
        $arrSites = array();

        foreach($sites as $site){
            $arrSites[] = $site->getId();
        }

        if(count($arrSites) > 0){
            $sitesIn = implode(',', $arrSites);

            $queryBuilder = $this->getEm()->createQueryBuilder();
            $queryBuilder->select('GA')
                ->from('Entity\GaleriaSite', 'GA')
                ->where("GA.site IN (".$sitesIn.")")
                ->andWhere("GA.galeria = {$galeria} ");

            $result = $queryBuilder->getQuery()->getResult();

            $array = array();

            foreach($result as $res){
                $array[$res->getSite()->getId()] = $res->getOrdem();
            }

        }
        return $array;
    }


    /**
     * Metodo delete
     *
     * Deleta um grupo de registros e seus respectivos arquivos
     * @param array $ids
     * @return boolean
     */
    public function delete(array $ids)
    {
        $response = 0;
        $error = array();
        $success = "";

        try {
            $this->getEm()->beginTransaction();
            $permissao = false;
            foreach ($ids as $id) {
                if($this->validaVinculoGalerias($ids)) {
                    $galeria = $this->getEm()->find($this->getNameEntity(), $id);
                    $this->getEm()->remove($galeria);
                    $this->getEm()->flush();
                    $permissao = true;
                }
            }
            if($permissao) {
                $this->getEm()->commit();
                $success = "Ação executada com sucesso";
                $response = 1;
                $this->getLogger()->info("Galeria " . implode(",", $ids) . " foram excluidas.");
            } else {
                //$response = 1;
                $error[] = "Não é possível realizar a exclusão de galerias vinculadas a notícias ou novas páginas.";
                $this->getEm()->rollback();
            }

        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)<br><br>".$exc->getMessage();
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

     public function deleteGaleriaSite($site)
    {
         /*try {
             $this->getEm()->beginTransaction();
             $ids[] = 79;
             foreach ($ids as $id) {
               $galeriaSite = $this->getEm()->find('Entity\GaleriaSite', $id);
               $this->getEm()->remove($galeriaSite);
               $this->getEm()->flush();
            }

            $this->getEm()->commit();
            $this->getLogger()->info("Galerias Site foram excluidas.");
         } catch (Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";;
         }*/

        $this->getEm()->beginTransaction();

        $queryBuilder = $this->getEm()->createQueryBuilder();
        $queryBuilder
            ->delete('Entity\GaleriaSite', 'GS')
            ->where($queryBuilder->expr()->eq('GS.site', ':site'))
            ->setParameter('site', $site);
        $this->getEm()->flush();
        $this->getEm()->commit();


        return $queryBuilder->getQuery()->getResult();
    }


    public function deleteVinculoGaleriaSite($array_sites,$galeria)
    {
       /*try {
             $this->getEm()->beginTransaction();
             $ids[] = 79;
             foreach ($ids as $id) {
               $galeriaSite = $this->getEm()->find('Entity\GaleriaSite', $id);
               $this->getEm()->remove($galeriaSite);
               $this->getEm()->flush();
            }

            $this->getEm()->commit();
            $this->getLogger()->info("Galerias Site foram excluidas.");
         } catch (Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";;
         }*/

        $this->getEm()->beginTransaction();

        $queryBuilder = $this->getEm()->createQueryBuilder();

        if ($array_sites[0] instanceof \Entity\Site) {
            foreach ($array_sites as $site){
                $sites[] = $site->getId();
            }
        }

        for($i = 0;$i < count($sites); $i++){
            $notInSite .= $i == 0 ? $sites[$i] : ",".$sites[$i];
        }

        $queryBuilder = $this->getEm()->createQueryBuilder();
        $queryBuilder
            ->delete('Entity\GaleriaSite', 'GS')
            //->where($queryBuilder->expr()->notIn("GS.site", ":site"))
            ->where("GS.site IN ({$notInSite})")
            ->andWhere("GS.galeria = {$galeria} ");
            //->andWhere($queryBuilder->expr()->eq("GS.galeria", ":galeria"))
            //->setParameter('site', $notInSite)
            //->setParameter('galeria', $galeria);

        $queryBuilder->getQuery()->execute();

        $this->getEm()->flush();
        $this->getEm()->commit();



    }



     /*public function insertVinculoGaleriaSite($sites,$galeria)
    {

        for($i = 0;$i < count($sites); $i++){
            $notInSite .= $i = 0 ? $sites[$i] : ",".$sites[$i];
        }

       $queryBuilder = $this->getEm()->createQueryBuilder();
        $queryBuilder
            ->delete('Entity\GaleriaSite', 'GS')
            ->where($queryBuilder->expr()->notIn("GS.site", ":site"))
            ->andWhere($queryBuilder->expr()->eq("GS.galeria", ":galeria"))
            ->setParameter('site', $notInSite)
            ->setParameter('galeria', $galeria);

        return $queryBuilder->getQuery()->getResult();
    }*/

    public function insertOrdem($array_sites,$id_galeria,$action, $ordensAtuais = array()){
        try{

            if ($array_sites[0] instanceof \Entity\Site) {
                foreach ($array_sites as $site){
                    $sites[] = $site->getId();
                }
            }

            $this->getEm()->beginTransaction();
            foreach($sites as $id => $site){
                //$this->getEm()->createQuery("INSERT INTO Entity\GaleriaSite g  g.ordem = {$ordem}, g.site = {$id_site}")->execute();
                $g = new GaleriaSite;

                if(isset($ordensAtuais[$site])){
                    $g->setOrdem($ordensAtuais[$site]);
                }else{
                    $g->setOrdem($this->getEm()->getRepository('Entity\GaleriaSite')->buscarUltimaOrdem($site));
                }

                $g->setSite($this->getEm()->getRepository('Entity\Site')->find($site));
                $g->setGaleria($this->getEm()->getRepository('Entity\Galeria')->find($id_galeria));
                $this->getEm()->persist($g);

            }
            $this->getEm()->flush();
            $this->getEm()->commit();
         } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $this->getLogger()->error($ex->getMessage());
            $error[] = "Erro ao atualizar registro";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }


    public function updateOrdem(array $dados,$id_site){
        try{
            //$this->deleteGaleriaSite($id_site);
            $this->getEm()->beginTransaction();

            foreach($dados as $id => $ordem){
                //$this->getEm()->createQuery("INSERT INTO Entity\GaleriaSite g  g.ordem = {$ordem}, g.site = {$id_site}")->execute();
                $site = $this->getEm()->getRepository('Entity\Site')->find($id_site);
                $galeria = $this->getEm()->getRepository('Entity\Galeria')->find($id);
                $reference = $this->getEm()->getRepository('Entity\GaleriaSite')->findBy(array('site' => $site,'galeria' => $galeria));

                #MODO 1
                /*
                $gs = new GaleriaSite;
                $gs->setId($reference[0]->getId());
                $gs->setOrdem($ordem);
                $gs->setSite($reference[0]->getSite());
                $gs->setGaleria($reference[0]->getGaleria());
                */

                 #MODO 2
               /* $queryBuilder = $this->getEm()->createQueryBuilder();
                $queryBuilder->update('Entity\GaleriaSite', 'GS')
                ->set("GS.ordem", $ordem)
                ->Where($queryBuilder->expr()->eq("GS.site", ":site"))
                ->andWhere($queryBuilder->expr()->eq("GS.galeria", ":galeria"))
                ->setParameter('site', $this->getEm()->getRepository('Entity\Site')->find($id_site))
                ->setParameter('galeria', $this->getEm()->getRepository('Entity\Galeria')->find($id));

                $this->getEm()->flush();*/


                //Faz a edição dos registros
                $aux["id"] = $reference[0]->getId();
                $aux["ordem"] = $ordem;
                $aux["site"] = $reference[0]->getSite();
                $aux["galeria"] = $reference[0]->getGaleria();

                //Busca a referência da entidade
                $entity = $this->getEm()->getReference('Entity\GaleriaSite', $reference[0]->getId());
                //Seta os dados
                $this->setEntityDados($aux, $entity);
                $this->getEm()->persist($entity);
                $this->getEm()->flush();

                //$this->getEm()->persist($gs);
                //$this->getEm()->flush();
            }

            $this->getEm()->commit();
            $success = "Ação executada com sucesso";
            $response = 1;
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $this->getLogger()->error($ex->getMessage());
            $error[] = "Erro ao atualizar registro";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     *
     * @param int $id
     * @return array
     */
    public function deleteVinculados($id)
    {
        $status = "";
        $fototecaVinculada = $this->getEm()
	                            ->getRepository("Entity\Fototeca")
	                            ->getVinculadoGaleria($id);
        if ($vinculadoGaleria) {
            $response = 2;
            foreach($fototecaVinculada as $fototeca)
            {
                $status .= $fototeca['objeto'].'; ';
            }
        } else {
            $tipo = $this->getEm()->getReference($this->getNameEntity(), $id);
            try {
                $this->getEm()->remove($tipo);
                $this->getEm()->flush();

                $response = 1;
                $success = "Ação executada com sucesso";
            } catch (\Exception $exc) {
                $this->logger->error($exc->getMessage());
                $error[] = "Erro ao excluir registro.";
            }
        }

        return $status;
    }

    public function validaVinculoGalerias($ids) {

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
}
