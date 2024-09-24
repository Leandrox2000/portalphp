<?php

namespace Portal\Controller;

/**
 * Home da SEDE e de subsite
 *
 */
class IndexController extends PortalController
{

    const PAGE_TITLE = "Home";
    const DEFAULT_ACTION = "index";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @return type
     */
    private function getBannersSlider()
    {
        $hash = $this->getParam()->get('hash');
        $previewType = $this->getParam()->get('preview');

        if ($previewType == 'slider-subsite') {
            if ($this->verifyHash($hash)) {
                $this->getTpl()->addGlobal('pre_visualizacao', true);
                $entity = $this->getEm()
                               ->getRepository('Entity\SliderHome')
                               ->find($this->getParam()->get('id'));
                return array($entity);
            } else {
                throw new \Exception\NotFoundException();
            }
        }
        
        return $this->getEm()
					->getRepository('Entity\SliderHome')
					->getConteudoHome($this->getSubsite());
    }

    private function getBannersSliderOrdem()
    {
        if ($this->getParam()->get('preview') != 'slider-subsite') {
        	$site = $this->getSubsite();
        	return $this->getEm()
					->getRepository('Entity\SliderHome')
					->getSliderOrdemSite($site->getId());
        }
    }
    
    /**
     *
     * @return type
     */
    private function getVideo()
    {
        return $this->getEm()
                        ->getRepository('Entity\Video')
                        ->getConteudoHome($this->getSubsite());
    }
    
    private function getVideosSubsites()
    {
        return $this->getEm()
                        ->getRepository('Entity\VideoSite')
                        ->getUltimosVideosOrder($this->getSubsite(),1,4);
    }
    
    
    private function getConteudosEstaticos()
    {
        return $this->getEm()
                        ->getRepository('Entity\PaginaEstatica')
                        ->getConteudosEstaticos($this->getSubsite(),4,0);
    }
    
    
    
    /**
     *
     * @return array
     */
    private function getBanners($id_banner = null, $hash = null)
    {
        //Busca o subsite
        $subsite = $this->getSubsite();

        //Verifica
        if ($hash) {
            if ($this->verifyHash($hash)) {
                $destaque = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(null, 'Destaque Home', 4, $id_banner);
                $livros = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(null, 'Livros', 8, $id_banner);
                $unidades = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(null, 'Unidades descentralizadas', null, $id_banner);
                $divulgacao = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(null, 'Divulgação', null, $id_banner);
                $rodape = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(null, 'Rodapé', null, $id_banner);
            } else {
                throw new \Exception\NotFoundException;
            }

        } else {
            $destaque = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(null, 'Destaque Home', 4);
            $livros = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(null, 'Livros', 8);
            $unidades = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(null, 'Unidades descentralizadas');
            //$divulgacao = $this->getBannersDivulgacao();
            $divulgacao = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(null, 'Divulgação', 4, null);
            $rodape = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(null, 'Rodapé');

        }


        return array(
            'destaque_home' => $destaque,
            'livros' => $livros,
            'unidades_descentralizadas' => $unidades,
            'divulgacao' => $divulgacao,
            'rodape' => $rodape,
        );
    }

    /**
     *
     * @return array
     */
    private function getNoticias($limit, $notInIds = null)
    {
        return $this->getEm()
                        ->getRepository('Entity\Noticia')
                        ->getConteudoHome($this->getSubsite(), $limit, $notInIds);
    }
    
    /**
     * 
     * @param integer $limit
     * @return array
     */
    public function getNoticiasHome($limit){
        return $this->getEm()->getRepository('Entity\Noticia')->getQueryPortal($this->getSubsite(), $limit, null, null, true)->getResult();
    }
    
    /**
     *
     * @return array
     */
    private function getLegislacao()
    {
        return $this->getEm()
                        ->getRepository('Entity\Legislacao')
                        ->getBuscaLegislacaoSubsite($this->getSubsite());
    }
    
    /**
     *
     * @return array
     */
    private function getEditais()
    {
        return $this->getEm()
                        ->getRepository('Entity\Edital')
                        ->getBuscaEditalSubsite($this->getSubsite());
    }
    
    /**
     *
     * @return array
     */
    private function getGalerias()
    {
        return $this->getEm()
                        ->getRepository('Entity\Galeria')
                        ->getBuscaGaleriaSubsite($this->getSubsite());
    }

    /**
     *
     * @return array
     */
    private function getEventos()
    {
        return $this->getEm()
                        ->getRepository('Entity\Agenda')
                        ->getConteudoHome($this->getSubsite());
    }

    /**
     *
     * @return \Entity\BackgroundHome
     */
    public function getBackgroundHome()
    {
        $hash = $this->getParam()->get('hash');
        $backgroundId = $this->getParam()->getInt('id');
        $isBackgroundPreview = ($this->getParam()->get('preview') == 'background');

        if ($isBackgroundPreview) {
            if ($this->verifyHash($hash)) {
                $this->getTpl()->addGlobal('pre_visualizacao_bg', true);

                return $this->getEm()
                            ->getRepository('Entity\BackgroundHome')
                            ->find($backgroundId);
            } else {
                throw new \Exception\NotFoundException;
            }
        }

        return $this->getEm()
                    ->getRepository('Entity\BackgroundHome')
                    ->getBackgroundHomeFunc();
        
        // return $this->getEm()
        //             ->getRepository('Entity\BackgroundHome')
        //             // ->getBackgroundHomeFunc(1);
        //             ->findOneBy(array('publicado' => 1));
    }

    /**
     *
     * @return string
     */
    public function index($id_banner = null, $hash = null)
    {
        $objSite = $this->getEm()->getRepository('Entity\Site')->getSiteByName($this->getSubsite());
        
        $subsite = $this->getSubsite();

        // Define o background da home
        $backgroundHome = $this->getBackgroundHome();
        $this->getTpl()->addGlobal('background_home', $backgroundHome);

        $previewTypes = array('slider-subsite', 'subsite');
        $previewParam = $this->getParam()->get('preview');
     
        $isPreview = in_array($previewParam, $previewTypes);
        $isSubsite = !empty($subsite);
        
        // Carrega subsite caso seja subsite ou pré-visualização
        if ($isPreview || $isSubsite) {
            // Se é preview de subsite
            
            if ($this->getParam()->get('preview') == 'subsite') {
                $hash = $this->getParam()->get('hash');
                
                if ($this->verifyHash($hash)) {
                    $idSite = $this->getParam()->getInt('id');
                    $subsite = $this->getEm()->getRepository('Entity\Site')->find($idSite);
                    // Carrega o website pré-visualizado
                    $this->setSubsite($subsite);
                } else {
                    throw new \Exception\NotFoundException;
                }
            }
            elseif($this->getParam()->get('preview') == 'slider-subsite')
            {
                $sliderHome = $this->getEm()->getRepository('Entity\SliderHome')->find($this->getParam()->getInt('id'));
                foreach ($sliderHome->getSites() as $site)
                {
                    $idSite = (int)$site->getId();
                }
                $subsite = $this->getEm()->getRepository('Entity\Site')->find($idSite);
                $this->setSubsite($subsite);
            }
            
            $id_subsite = $idSite ? $idSite : $objSite->getId();
            
            //Buscar lista de funcionalidades
            $subsite = $this->getEm()->getRepository('Entity\FuncionalidadeSite')->getFuncionalidades($id_subsite);
            
            $render = array();
            
            $render['bannersSlider'] = $this->getBannersSlider();
            $bannersSliderOrdem = $this->getBannersSliderOrdem();
            
//            var_dump($bannersSliderOrdem);
            foreach ($subsite as $func) {
                switch($func->getFuncionalidade()->getLabel()){
                    case 'Vídeos':
                        $arrayVideos = $this->getVideosSubsites();
                        $render['videos'] = $arrayVideos['videos'];
                    break;
                    case "Conteúdos estáticos":
                        $render['conteudosEstaticos'] = $this->getConteudosEstaticos();
                    break;
                    case "Páginas Estáticas":
                        $render['conteudosEstaticos'] = $this->getConteudosEstaticos();
                    break;
                    case "Novas páginas":
                        $render['conteudosEstaticos'] = $this->getConteudosEstaticos();
                    break;
                    case "Galeria":
                        $render['galeria'] = $this->getGalerias();
                    break;
                    case "Agenda":
                        $render['eventos'] = $this->getEventos();
                    break;
                    case "Legislação":
                        $render['legislacao'] = $this->getLegislacao();
                    break;
                    case "Editais":
                        $render['edital'] = $this->getEditais();
                    break;
                    case "Notícias":
                        $render['noticias'] = $this->getNoticias(3);
                    break;
                }
            }
            
            //$bannersSlider = $this->getBannersSlider();
            $bannersDivulgacao = $this->getBannersDivulgacao();
            
            $noticias = $this->getNoticias(3);
            $video = $this->getVideo();
            $eventos = $this->getEventos();

            #REDES SOCIAIS
            $facebook = $objSite->getFacebook();
            $twitter = $objSite->getTwitter();
            $youtube = $objSite->getYoutube();
            $flickr = $objSite->getFlickr();
            
           $this->getTpl()->renderView(array(
                    'render' =>$render,
                    'bannersSliderOrdem' => $bannersSliderOrdem,
                    'eventos' =>$noticias,
                    'noticias' =>$eventos,
                    'bannersDivulgacao' =>$bannersDivulgacao,
                    'preVisualizacao' => $hash ? true : false,
                    'facebook' => $facebook,
                    'twitter' => $twitter,
                    'youtube' => $youtube,
                    'flickr' => $flickr,
            ), 'index/subsite.html.twig');
            
            /*$this->getTpl()->renderView(array(
                'video' => $video,
                'noticias' => $noticias,
                'bannersSlider' => $bannersSlider,
                'bannersDivulgacao' => $bannersDivulgacao,
                'eventos' => $eventos,
                'preVisualizacao' => $hash ? true : false,
            ), 'index/subsite.html.twig');*/
        }
        // Carrega site da SEDE
        else {
            $banners = $this->getBanners($id_banner, $hash);
            $noticiasHome = $this->getNoticiasHome(4);
            $eventos = $this->getEventos();
            
            //Organiza os ids das notícias que não poderão se encontradas nessa busca
            $notInIds = array();
            
            foreach($noticiasHome as $noticiaHome){
                $notInIds[] = $noticiaHome->getId();
            }
            
            $notInIds = implode(",", $notInIds);

            //Busca as demais notícias
            $noticias = $this->getNoticias(4, $notInIds);
     
            $this->getTpl()->renderView(array(
                'banners' => $banners,
                'noticias' => $noticias,
                'noticiasHome' => $noticiasHome,
                'eventos' => $eventos,
                'preVisualizacao' => $hash ? true : false
            ));
        }

        return $this->tpl->output();
    }

}

