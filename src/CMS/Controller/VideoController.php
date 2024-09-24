<?php

namespace CMS\Controller;

use Entity\Video as VideoEntity;
use Entity\Type;
use CMS\Service\ServiceRepository\Video as VideoService;
use Helpers\Param;
use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceYoutube\ServiceYoutube;

/**
 * VideoController
 *
 * @author join-ti
 */
class VideoController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Vídeos";
    const DEFAULT_ACTION = "lista";

    /**
     * @var String
     */
    protected $title = self::PAGE_TITLE;

    /**
     * @var String
     */
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var VideoEntity
     */
    protected $entity;

    /**
     *
     * @var VideoService
     */
    protected $service;

    /**
     *
     * @var ServiceYoutube
     */
    protected $serviceYoutube;

    /**
     *
     * @var array
     */
    protected $user;

    /**
     *
     * @param \Template\TemplateInterface $tpl
     * @param \Helpers\Session $session
     */
    public function __construct(\Template\TemplateInterface $tpl, \Helpers\Session $session)
    {
        parent::__construct($tpl, $session);
        $this->setUser($this->getUserSession());
    }

    /**
     *
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param array $user
     */
    public function setUser(array $user)
    {
        $this->user = $user;
    }

    /**
     *
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param String $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return String
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     *
     * @return VideoEntity
     */
    public function getEntity()
    {
        if (!isset($this->entity)) {
            $this->entity = new VideoEntity();
        }
        return $this->entity;
    }

    /**
     *
     * @param VideoEntity $entity
     */
    public function setEntity(VideoEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return VideoService
     */
    public function getService()
    {
        if (!isset($this->service)) {
            $this->service = new VideoService($this->getEm(), $this->getEntity(), $this->getSession());
        }
        return $this->service;
    }

    /**
     *
     * @param VideoService $service
     */
    public function setService(VideoService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @return ServiceYoutube
     */
    public function getServiceYoutube()
    {
        if (!isset($this->serviceYoutube)) {
            $this->serviceYoutube = new ServiceYoutube();
        }

        return $this->serviceYoutube;
    }

    /**
     *
     * @param ServiceYoutube $serviceYoutube
     */
    public function setServiceYoutube(ServiceYoutube $serviceYoutube)
    {
        $this->serviceYoutube = $serviceYoutube;
    }

    /**
     *
     * @return string
     */
    public function lista()
    {
        //Cria os objetos status
        $status = array();
        $status[] = new Type("0", "Não publicado");
        $status[] = new Type("1", "Publicado");
        $status[] = new Type("2", "Compartilhado");

        #REGRA DE PERMISS�O
//        if($this->user['sede']){
//            $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
//        } else {
//            $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
//        }
        #Jeito Antigo
        //$sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
        $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
        //Busca os anos
        $this->tpl->renderView(array(
                'status' => $status,
                'titlePage' => $this->getTitle(),
                'subTitlePage' => "",
                'sites' => $sites,
            )
        );

        return $this->tpl->output();
    }

    /**
     *
     * @param type $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        $this->getTpl()->addJS('/video/videos.js');

        //Busca dados do video
        #REGRA DE PERMISS�O
//        if($this->user['sede']){
//            $video = $this->getEm()->getRepository("Entity\Video")->find($id);
//        } else {
//            $video = $this->getEm()->getRepository("Entity\Video")->findByIdSite($id, $this->user['subsites']);
//        }
        $repository = $this->getEm()->getRepository("Entity\Video");
        $video = $repository->find($id);

        $embed = '';
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");

            //Pega o embed do vídeo
            $dadosYoutube = $this->getServiceYoutube()->getVideoLink($video->getLink());
            $embed = $dadosYoutube['embed'];
        }
        #REGRA DE PERMISS�O
//        if($this->user['sede']){
//            $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
//        } else {
//            $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
//        }

        $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);

        //Organiza os ids das imagens
        $idsImgs = '';

        //Verifica se a galeria foi encontrada
        $videoRelacionados = array();
        if ($video) {
            $videoRelacionados = $this->getEm()->getRepository("Entity\VideoRelacionado")->getVideosRelacionadosByVideo($id, false);

            foreach ($videoRelacionados as $relacionado) {
                $idsImgs[] = $relacionado->getId();
            }

            $idsImgs = implode(',', $idsImgs);
        }

        $this->tpl->renderView(array(
            "data" => new \DateTime('now'),
            "video" => $video,
            "sites" => $sites,
            "titlePage" => $this->getTitle(),
            "method" => "POST",
            "embed" => $embed,
            "videosRelacionados" => $this->getHtmlVideos($idsImgs, $videoRelacionados),
            "idsVideosRelacionados" => $idsImgs,
        ));

        return $this->tpl->output();
    }

    /**
     *
     * @param string $ids
     * @param \Entity\Video[] $videos
     * @return string
     */
    public function getHtmlVideos($ids = "", $videos = null)
    {
        if (!empty($ids)) {
            //Organiza os ids em imagens
            $ids = explode(',', $ids);

            //Busca as imagens
            if(is_array($videos) && count($videos)) {
                $registros = $videos;
            } else {
                $registros = $this->getEm()->getRepository('Entity\Video')->findById($ids);
            }

            //Cria o html com as imagens
            $html = "";

            $html .= "<div class='gallerywrapper'>";
            $html .= "<ul  class='imagelist' id='list-videos-relacionados'>";

            //Percorre as imagens e monta o HTML
            foreach ($registros as $registro) {
                $html .= "<li id='video-relacionado-{$registro->getId()}' id-video='{$registro->getId()}'>";
                $html .= "<span>{$registro->getNome()}</span>";
                $html .= "<span><a class='delete' href='javascript:videosModalObject.removerRelacionado({$registro->getId()})'></a></span>";
                $html .= "</li>";
            }

            $html .= "</ul>";
            $html .= "</div>";


            return $html;
        } else {
            return '';
        }
    }

    /**
     *
     * @return string
     */
    public function salvar()
    {
        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'login' => $this->user['dadosUser']['login'],
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('data_inicial')) . " " . $this->getParam()->get('hora_inicial')),
            'nome' => $this->getParam()->get('nome'),
            'link' => $this->getParam()->getString("link"),
            'nomeYoutube' => $this->getParam()->getString("tituloYoutube"),
            'autor' => $this->getParam()->getString("autor"),
            'resumo' => $this->getParam()->getString("resumo"),
            'relacionados' => $this->getParam()->getString('videosBanco'),
            'sites' => $this->getParam()->get('sites'),
        );

        $dataFinal = $this->getParam()->get('data_final');
        $horaFinal = $this->getParam()->get('hora_final');

        // se a dataFinal não estiver setada ou receber o valor vazio
        // a variável deve ser setada como NULA
        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal) . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }

        return json_encode($this->getService()->save($dados, $this->getParam()->get('ordemVideosRelacionados')));
    }

    public function videos()
    {
        return $this->getTpl()->renderView();
    }

    /**
     *
     * Resposável por retornar os registros paginados
     * @return String
     */
    public function pagination()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoVideo = $em->getRepository("Entity\Video");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repoVideo->countAll($this->getSession()->get('user'));

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status'),
            "site" => $param->get('site')
        );

        //Faz a busca e armazena o total de registros
        //$videos = $repoVideo->getBuscaVideo($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
        //$videos = $repoVideo->getBuscaVideoOrder($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
#Jeito Antigo
//        //        if(!$param->get('site'))
//        {
//        	$videos = $repoVideo->getBuscaVideo($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
//	        $totalFiltro = $repoVideo->getTotalBuscaVideo($filtros, $this->getSession()->get('user'));
//        }
//        else
//        {
//        	$videos = $em->getRepository("Entity\VideoSite")->getBuscaVideoOrder($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
//	        $totalFiltro = $em->getRepository("Entity\VideoSite")->getTotalBuscaVideo($filtros, $this->getSession()->get('user'));
//        }
//Faz a busca e armazena o total de registros
        #INSER��O
        if(!$param->get('site') || in_array($param->get('site'), $_SESSION['user']['subsites'])){
            $videos = $repoVideo->buscaRegistroByLogin('Entity\Video', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
            $totalFiltro = $repoVideo->buscaRegistroByLogin('Entity\Video', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), true);
            
        }else{
            $videos = $repoVideo->getDataBySubsite('Entity\Video', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'));
            $totalFiltro = $repoVideo->getDataBySubsite('Entity\Video', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'), true);
        }


//        $videos = $em->getRepository("Entity\VideoSite")->getBuscaVideoOrder($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
//        $totalFiltro = $em->getRepository("Entity\VideoSite")->getTotalBuscaVideo($filtros, $this->getSession()->get('user'));

        #INSER��O
        //$totalFiltro = $repoVideo->getTotalBusca('Entity\Video', $filtros, $this->getSession()->get('user'));
        $totalFiltro = $totalFiltro[0]['total'];

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        if($videos)
        {
            foreach ($videos as $video) {
                $linha = array();
                $pais = array();
                $linha[] = $this->getFields()->checkbox("video[]", $video->getId());

                #INSER��O
                $sitesPai = $this->getEm()->find("Entity\Video", $video->getId())->getPaiSites();
                if($sitesPai){
                    foreach ($sitesPai as $pai) {
                        $pais[] = $pai->getSigla();
                    }
                }
                $siglas = implode(", ", $pais);

                if ($this->verifyPermission('VIDEO_ALTERAR')) {
                    //$linha[] = $tag->link($tag->h4($video->getLabel()), array("href" => "video/form/" . $video->getId())) . $video->getDataCadastro()->format('d/m/Y') . " as " . $video->getDataCadastro()->format('H:i');
                    $linha[] = $tag->link($tag->h4($video->getLabel()), array("href" => "video/form/" . $video->getId())) . 'Criado por ' . $video->getLogin() . ' - '.$siglas.' - ' . ' em ' . $video->getDataCadastro()->format('d/m/Y') . " as " . $video->getDataCadastro()->format('H:i');
                } else {
                    $linha[] = $tag->link($tag->h4($video->getLabel()), array("href" => "video/form/" . $video->getId())) . $video->getDataCadastro()->format('d/m/Y') . " as " . $video->getDataCadastro()->format('H:i');
                }

                #INSER��O
//                    echo "<pre>";
//                    \Doctrine\Common\Util\Debug::dump($video->getId());
//                    echo "</pre>";
//                    die();

                if ($repoVideo->getCompartilhadosById($video->getId()) == 1) {
                    $linha[] = "<span class='compartilhado'>Compartilhado de $siglas</span>";
                } else {
                    $linha[] = $video->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
                }
                #Jeito Antigo
                //$linha[] = $video->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
                $dados[] = $linha;
            }
        }

        //Organiza o retorno e retorna via json
        $retorno['sEcho'] = Param::getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $totalFiltro;
        $retorno['iTotalRecords'] = $total;
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @param String $link
     * @return String
     */
    public function getDadosVideo()
    {
        $dados = $this->getServiceYoutube()->getVideoLink($this->getParam()->get('link'));
        return json_encode($dados);
    }

    /**
     *
     * @return string
     */
    public function validacaoNome($id = 0)
    {
        //Valida o nome
        $resultado = $this->getEm()->getRepository("Entity\Video")->validarNome($this->getParam()->get('nome'), $this->getSession()->get('user'), $id);

        //Verifica o resultado
        if ($resultado) {
            return "true";
        } else {
            return "false";
        }
    }

    /**
     *
     * @return boolean
     */
    public function validacaoVideo()
    {
        //Requisita a validação
        $validacao = $this->getServiceYoutube()->validarLink($this->getParam()->getString('link'));

        if ($validacao) {
            return "true";
        } else {
            return "false";
        }
    }


    public function ajaxAtualizarOrdenacaoVideo()
    {
        $paramOrdenation = $this->getParam()->get('ordenacao');
        $id_site = $this->getParam()->get('site');
        $newOrdenation = array();
        foreach($paramOrdenation as $item){
            $newOrdenation[$item['id']] = (int)$item['ordenacao'];
        }

        return json_encode($this->getService()->updateOrdem($newOrdenation,$id_site));
    }

    //Compartilha registro com subsites selecionados
    public function compartilhar()
    {
        $error = array();

        try {
            if (!empty($_REQUEST['sites'])) {

                $sites = $_REQUEST['sites'];

                $connection = $this->getEm()->getConnection();

                
                //$aux = $connection->executeQuery("SELECT * FROM tb_video_site WHERE id_video = {$_REQUEST['id']}");
                
                $video = $this->getEm()
                ->getRepository('Entity\Video')
                ->find($_REQUEST['id']);
                $ordem = $video->getVideosSite();
                
                
                foreach ($ordem as $valor) {
                    $v = $valor->getVideo();
                    $s = $valor->getSite();
                    $order = $valor->getOrdem();
                    
                    $bkp_ordenacao[$s->getId()][$v->getId()] = $order;
                }
                
                
                foreach ($_SESSION['user']['subsites'] as $site) {
                    $connection->query("DELETE FROM tb_video_site WHERE id_video = {$_REQUEST['id']} AND id_site = {$site}");
                    $connection->query("DELETE FROM tb_video_ordem WHERE id_video = {$_REQUEST['id']} AND id_site = {$site}");
                }
                
                foreach ($sites as $site) {
                    $cont++;
                    $statment = $connection->prepare("INSERT INTO tb_video_site (id_video, id_site) VALUES({$_REQUEST['id']}, $site)");
                    $statment->execute();
                    
                    $ordem = $bkp_ordenacao[$site][$_REQUEST['id']];
                    
                    if(!$ordem){
                        $entidade_site = $this->getEm()->getRepository('Entity\Site')->find($site);
                        $ordem = $this->getEm()->getRepository("Entity\VideoSite")->buscarUltimaOrdem($entidade_site);
                    }
                    
                    $statment = $connection->prepare("INSERT INTO tb_video_ordem (id_video, id_site, nu_ordem) VALUES({$_REQUEST['id']}, $site, {$ordem})");
                    $statment->execute();
                }
                $response = 1;
                $success = "Registro compartilhado com sucesso";
                
//                    $sites = $entity->getSites();
//
//                    for($i = 0;$i < count($sites);$i++){
//                        $array = "";
//                        $array[] = $sites[$i];
//                        $this->insertOrdem($array, $entity->getId(),empty($dados['id']) ? "insert" : "update");
//                    }
                
            } else {
                $response = 1;
                $success = "Registro descompartilhado com sucesso";
                $this->getService()->deletaCompartilhadosById($_REQUEST['id'], 'video');
            }

        } catch(\Exception $exc) {
            $error[] = "Não foi possível executar esta ação";
        }

        return json_encode(array('error' => $error, 'response' => $response, 'success' => $success));
    }

    public function validaSubsiteVinculadoVideo()
    {
        $repository = $this->getEm()->getRepository('Entity\Video');

        $retorno = $repository->validaVinculo($_REQUEST['id'], 'video');

        return json_encode(array('permissao' => $retorno));
    }

}
