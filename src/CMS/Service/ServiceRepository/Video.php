<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager as EntityManager;
use Entity\Video as VideoEntity;
use Entity\VideoSite;
use Helpers\Session;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe Video
 *
 * Responsável pelas ações na entidade Video
 * @author join-ti
 */
class Video extends BaseService implements SolrAwareInterface
{

    public function __construct(EntityManager $em, VideoEntity $entity, Session $session)
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
            'title'             => $entity->getNome(),
            'description'       => $entity->getResumo(),
            'publish'           => $entity->getPublicado(),
            'publish_date'      => $entity->getDataInicial(),
            'unpublish_date'    => $entity->getDataFinal(),
            'url'               => \Helpers\Url::generateRoute('videos', 'detalhes', $entity),
        );
   }

    /**
     *
     * @param array $dados
     * @return array
     */
    public function save($dados, $ordem_videos_relacionados = array())
    {
        $error = array();
        $success = "";
        $response = 0;
		
        try {
            $action = empty($dados['id']) ? "inserido" : "alterado";

            $sites = $dados['sites'];

            //Verifica se for update deleta vinculos e pais não selecionado
            if($action == 'alterado') {

                $sitesSession = $_SESSION['user']['subsites'];

                $connection = $this->getEm()->getConnection();

                foreach ($sitesSession as $site) {
                    $connection->query("DELETE FROM tb_video_site WHERE id_video = {$dados['id']} AND id_site = {$site}");

                    $connection->query("DELETE FROM tb_pai_video_site WHERE id_video = {$dados['id']} AND id_site = {$site}");
                }

                $agenda = $this->getEm()->getRepository('Entity\Video')->find($dados['id']);

                $sitesArrPai = $agenda->getPaiSites();

                $sitesArr = $agenda->getSites();

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

            //Open Old
            if (!empty($dados['relacionados'])) {
                $relacionados = explode(',', $dados['relacionados']);
            }
            unset($dados['relacionados']);

            $entity = parent::save($dados);
            
            if (!empty($relacionados)) {
                $this->addVideosRelacionados($entity, $relacionados, $ordem_videos_relacionados);
            } else {
                $this->deleteVideoRelacionado($entity);
            }

            if($dados['id']){
                $aux = $this->getEm()->getRepository('Entity\Video')->find($dados['id']);
                $sites = $aux->getSites();
            }

            $ordensAtuais = $this->getOrdensAtuais($dados['sites'], $entity->getId());

            #se tem "id" deleta dos sites que não estão aqui
            if($dados['id']) $this->deleteVinculoVideoSite($sites,$entity->getId());

            $sites = $entity->getSites();
            
            for($i = 0;$i < count($sites);$i++){
                $array = "";
                $array[] = $sites[$i];
                $this->insertOrdem($array, $entity->getId(),empty($dados['id']) ? "insert" : "update", $ordensAtuais);
            }
            //Closed Old

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);

            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $ex) {
            $error[] = $ex->getMessage();
        }

        return array(
            'error' => $error,
            'response' => $response,
            'success' => $success,
        );
    }

    public function deleteVinculoVideoSite($array_sites,$video)
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
            ->delete('Entity\VideoSite', 'VS')
            //->where($queryBuilder->expr()->notIn("GS.site", ":site"))
            ->where("VS.site IN ({$notInSite})")
            ->andWhere("VS.video = {$video} ");
            //->andWhere($queryBuilder->expr()->eq("VS.video", ":video"))
            //->setParameter('site', $notInSite)
            //->setParameter('video', $video);
     
        $queryBuilder->getQuery()->execute();
        
        $this->getEm()->flush();
        $this->getEm()->commit();
        
        
    }
    
    public function getOrdensAtuais($sites, $video){
        $arrSites = array();
        
        foreach($sites as $site){
            $arrSites[] = $site->getId();
        }
        
        if(count($arrSites) > 0){
            $sitesIn = implode(',', $arrSites);
            
            $queryBuilder = $this->getEm()->createQueryBuilder();
            $queryBuilder->select('VS')
                ->from('Entity\VideoSite', 'VS')
                ->where("VS.site IN (".$sitesIn.")")
                ->andWhere("VS.video = {$video} ");

            $result = $queryBuilder->getQuery()->getResult();

            $array = array();

            foreach($result as $res){
                $array[$res->getSite()->getId()] = $res->getOrdem();
            }
       
        }
        return $array;
    }
        
    private function addVideosRelacionados($videoPai, $relacionados, $ordem)
    {
        // Limpa os videos relacionados
        $this->deleteVideoRelacionado($videoPai);

        // Para cada ID de vídeo relacionado
        foreach ($relacionados as $relacionado) {
            $videoRelacionado = new \Entity\VideoRelacionado();
            $videoRelacionado->setRelacionado($this->getEm()->getReference('Entity\Video', $relacionado));
            $videoRelacionado->setOrdem($ordem[$relacionado]);
            $videoRelacionado->setVideo($videoPai);

            // Faz a persistência
            $this->getEm()->persist($videoRelacionado);
            $this->getEm()->flush();
        }
    }

    /**
     * Metodo delete
     *
     * Deleta um grupo de registros e seus respectivos arquivos
     * @param array $ids
     * @param array $forceIds  Não foi possível descobrir o porque do parâmetro $ids ser ignorado,
     *                         por isso o ideal foi criar um novo parâmetro para ser respeitado
     *                         quando necessário.
     * @return boolean
     */
    public function delete(array $ids, $forceIds = array())
    {
        if(is_array($forceIds) && count($forceIds)) {
            $ids = $forceIds;
        } else {
            $ids = $_REQUEST['sel'];
        }
        $response = 1;
        $error = array();
        $success = "Ação executada com sucesso";

        try {
            $this->getEm()->beginTransaction();
            
            foreach ($ids as $id) {
                if ($this->verificarStatus($id)) {
                    throw new \Exception;
                }

                $video = $this->getEm()->find($this->getNameEntity(), $id);
                $video_ordem = $video->getVideosSite();

                if($video_ordem){
                    foreach ($video_ordem as $id){
                        $aux = "";
                        $aux = $this->getEm()->getRepository("Entity\VideoSite")->find($id->getId());
                        if($aux) $this->getEm()->remove($aux);
                    }
                }
                
                // Remove os vídeos relacionados
                $this->deleteVideoRelacionado($video);
                
                // Remove o vídeo dos vídeos relacionados de outros vídeos
                $this->deleteRelacionadoVideo($video);
                
                // Remove o vídeo
                $this->getEm()->remove($video);
                $this->getEm()->flush();
            }
            $this->getEm()->commit();
            $this->getLogger()->info("Vídeos " . implode(",", $ids) . " foram excluidos.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $response = 0;
            $error[] = "Não foi possível executar esta ação.";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
    
    
    
     public function deleteVideoSite($site)
    {
        $this->getEm()->beginTransaction();
         
        $queryBuilder = $this->getEm()->createQueryBuilder();
        $queryBuilder
            ->delete('Entity\VideoSite', 'VS')
            ->where($queryBuilder->expr()->eq('VS.site', ':site'))
            ->setParameter('site', $site);
        $this->getEm()->flush();
        $this->getEm()->commit();


        return $queryBuilder->getQuery()->getResult();
    }
    
    /**
     * Remove os vídeos relacionados ao vídeo.
     * 
     * @param \Entity\Video $video
     * @return boolean
     */
    public function deleteVideoRelacionado($video)
    {
        $this->getEm()->beginTransaction();
         
        $queryBuilder = $this->getEm()->createQueryBuilder();
        $queryBuilder
            ->delete('Entity\VideoRelacionado', 'VR')
            ->where($queryBuilder->expr()->eq('VR.video', ':video'))
            ->setParameter('video', $video);
        
        $this->getEm()->flush();
        $this->getEm()->commit();
        
        return $queryBuilder->getQuery()->getResult();
    }
    
    /**
     * Remove o vídeo dos relacionados de outros vídeos.
     * 
     * @param \Entity\Video $video
     * @return boolean
     */
    public function deleteRelacionadoVideo($video)
    {
        $this->getEm()->beginTransaction();
         
        $queryBuilder = $this->getEm()->createQueryBuilder();
        $queryBuilder
            ->delete('Entity\VideoRelacionado', 'VR')
            ->where($queryBuilder->expr()->eq('VR.relacionado', ':video'))
            ->setParameter('video', $video);
        
        $this->getEm()->flush();
        $this->getEm()->commit();

        return $queryBuilder->getQuery()->getResult();
    }
    
    public function insertOrdem($array_sites,$id_video,$action, $ordensAtuais = array()){
        try{
            
            if ($array_sites[0] instanceof \Entity\Site) {
                foreach ($array_sites as $site){
                    $sites[] = $site->getId();
                }
            }
            
            $this->getEm()->beginTransaction();    
            foreach($sites as $id => $site){
                //$this->getEm()->createQuery("INSERT INTO Entity\GaleriaSite g  g.ordem = {$ordem}, g.site = {$id_site}")->execute();
                $v = new VideoSite;
                
                if(isset($ordensAtuais[$site])){
                    $v->setOrdem($ordensAtuais[$site]);
                }else{
                    $v->setOrdem($this->getEm()->getRepository('Entity\VideoSite')->buscarUltimaOrdem($site));
                }
                
                $v->setSite($this->getEm()->getRepository('Entity\Site')->find($site));
                $v->setVideo($this->getEm()->getRepository('Entity\Video')->find($id_video));
                $this->getEm()->persist($v);
                
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
            //$this->deleteVideoSite($id_site);
            
            $this->getEm()->beginTransaction();
            
            foreach($dados as $id => $ordem){
                //$this->getEm()->createQuery("INSERT INTO Entity\GaleriaSite g  g.ordem = {$ordem}, g.site = {$id_site}")->execute();
                
                /*$reference = new VideoSite;
                $reference->setOrdem($ordem);
                $reference->setSite($this->getEm()->getRepository('Entity\Site')->find($id_site));
                $reference->setVideo($this->getEm()->getRepository('Entity\Video')->find($id));
                $this->getEm()->persist($reference);
                $this->getEm()->flush();*/
                
                
                $site = $this->getEm()->getRepository('Entity\Site')->find($id_site);
                $video = $this->getEm()->getRepository('Entity\Video')->find($id);
                $reference = $this->getEm()->getRepository('Entity\VideoSite')->findBy(array('site' => $site,'video' => $video));
                //Faz a edição dos registros
                $aux["id"] = $reference[0]->getId();
                $aux["ordem"] = $ordem;
                $aux["site"] = $reference[0]->getSite();
                $aux["video"] = $reference[0]->getVideo();
                
                //Busca a referência da entidade
                $entity = $this->getEm()->getReference('Entity\VideoSite', $reference[0]->getId());
                //Seta os dados
                $this->setEntityDados($aux, $entity);
                $this->getEm()->persist($entity);
                $this->getEm()->flush();
                
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

    

}
