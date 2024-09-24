<?php

namespace Portal\Controller;

use LibraryController\AbstractController;
use Helpers\Menu;
use Helpers\MenuRodape;
use Helpers\MenuLinksRapidos;
use Factory\DebugBarFactory;

abstract class PortalController extends AbstractController
{

    /**
     *
     * @var type
     */
    protected $subsite;

    /**
     *
     * @var \DebugBar\StandardDebugBar
     */
    protected $debugbar;

    /**
     *
     * @var \Helpers\Session
     */
    protected $session;

    /**
     *
     * @var \CMS\Service\ServiceRepository\Hash
     */
    protected $hashService;

    /**
     *
     * @param \Template\TemplateInterface $tpl
     * @param \Helpers\Session $session
     */
    public function __construct(\Template\TemplateInterface $tpl, \Helpers\Session $session = NULL)
    {
        parent::__construct($tpl, $session);
        $this->setDebugbar(DebugBarFactory::getInstance());
        $this->setSession($session);
    }

    /**
     *
     * @param string $message
     * @param string $label
     */
    public function log($message, $label = NULL)
    {
        $this->debugbar['messages']->addMessage($message, $label);
    }

    /**
     *
     * @return \Entity\Site|null
     */
    public function getSubsite()
    {
        return $this->subsite;
    }

    /**
     *
     * @param string $subsite
     */
    public function setSubsite($subsite)
    {
        $this->subsite = $subsite;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    public function getRedesSociais()
    {
        $redes = array();
        $subsite = (is_object($this->getSubsite())) ? $this->getSubsite()->getId() : 1;
        if($this->getSubsite()){
            $redes['facebook'] = $this->getSubsite()->getFacebook();
            $redes['youtube'] = $this->getSubsite()->getYoutube();
            $redes['flickr'] =  $this->getSubsite()->getFlickr();
            $redes['twitter'] = $this->getSubsite()->getTwitter();
            $redes['subsite'] = true;
        }
        return $redes;
    }
    
    
    public function getBannersComunicacao()
    {
        //$subsite = $this->getSubsite();
        $bannersComunicacao["bannersComunicacao"] = $this->getEm()
               ->getRepository('Entity\BannerGeral')
               ->getBannersComunicacao(null, 'Comunicação', 2);
        return $bannersComunicacao;
    }
    
    /**
     * Método utilizado pelo FrontControllerPortal para injetar os itens
     * de menu no TemplatePortal.
     *
     * @return string
     */
    public function getMenu()
    {
       $menu = array();
        $subsite = ( is_object($this->getSubsite()) ) ? $this->getSubsite()->getId() : 1;

        // Menu Auxiliar
        $menuAuxTree = $this->getEm()
                ->getRepository('Entity\Menu')
                ->getQueryPortal(NULL, 'aux');

        // Menu Links RÃ¡pidos
        $menuLinks = $this->getEm()
                ->getRepository('Entity\Menu')
                ->getQueryPortal(NULL, 'lr');

        // Menus de NÃ­vel 1, NÃ­vel 2 e NÃ­vel 3
        $menuTree = $this->getEm()
                ->getRepository('Entity\Menu')
                ->getQueryPortal(NULL, array('n1', 'n2', 'n3'));
        
 
        if($this->getParam()->get('preview') == "subsite"){
            $subsite = $this->getEm()
                    ->getRepository('Entity\Site')
                    ->find($this->getParam()->get('id'));
        }

        // Menus de Subsite
        $subsiteTree = $this->getEm()
                ->getRepository('Entity\Menu')
                ->getQueryPortal($subsite, array('n1', 'n2', 'n3'));
      
        $baseLink = ($this->getSubsite() instanceof \Entity\Site) ? mb_strtolower($this->getSubsite()->getSigla()) . '/' : null;
        $menu['auxiliar'] = Menu::recursive($menuAuxTree, 0, 'clearfix float-left');
        $menu['principal'] = Menu::recursive($menuTree, 0, 'dropdown clearfix');
        $menu['principal_rodape'] = MenuRodape::recursive($menuTree);
        $menu['links_rapidos'] = MenuLinksRapidos::recursive($menuLinks);
        //$menu['subsite'] = MenuLinksRapidos::recursive($subsiteTree, 0, 'subsite-m', $baseLink);
        //$menu['subsite'] = Menu::recursive($subsiteTree, 0, 'subsite-m');
        $menu['subsite'] = Menu::recursiveSubsite($subsiteTree, 0, 'subsite-m');
         
        if($this->getParam()->get('preview') == "subsite") $this->setSubsite($subsite);
        
        return $menu;
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
     * @return \DebugBar\StandardDebugBar
     */
    public function getDebugbar()
    {
        return $this->debugbar;
    }

    /**
     *
     * @param \DebugBar\StandardDebugBar $debugbar
     */
    public function setDebugbar(\DebugBar\StandardDebugBar $debugbar)
    {
        $this->debugbar = $debugbar;
    }

    /**
     *
     * @return \CMS\Service\ServiceRepository\Hash
     */
    public function getHashService()
    {
        if (!isset($this->hashService)) {
            $this->setHashService(new \CMS\Service\ServiceRepository\Hash($this->getEm(), new \Entity\Hash(), $this->getSession()));
        }
        return $this->hashService;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Hash $hashService
     */
    public function setHashService(\CMS\Service\ServiceRepository\Hash $hashService)
    {
        $this->hashService = $hashService;
    }

    /**
     *
     * @param string $hash
     * @return boolean
     */
    public function verifyHash($hash)
    {
        //Instancia o repository
        $repoHash = $this->getEm()->getRepository('Entity\Hash');
        
        //Busca o registro com o hashPassado
        $registro = $repoHash->findBy(array('value' => $hash));
        
        if ($registro) {
            //Deleta o hash
            if ($this->getHashService()->deleteHash($hash)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     *
     * @return array
     * @throws \Exception\NotFoundException
     */
    public function getBannersLaterais($hash, $id_categoria_banner, $id_banner)
    {
        //Busca o subsite
        $subsite = $this->getSubsite();
        
        //Verifica hash
        if ($hash) {
            if ($this->verifyHash($hash)) {
                //Busca os banners
                $bannersLateral = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome($subsite, 'Lateral', 3, $id_banner);

                if ($id_categoria_banner != 6) {
                    $bannersLateral[0] = $this->getEm()->getRepository('Entity\BannerGeral')->find($id_banner);
                }

                if ($id_categoria_banner != 7) {
                    $bannersLateralDestaque = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome($subsite, 'Lateral Destaque', 1, $id_banner);
                } else {
                    $bannersLateralDestaque[0] = $this->getEm()->getRepository('Entity\BannerGeral')->find($id_banner);
                }
            } else {
                throw new \Exception\NotFoundException;
            }
        } else {
            //Busca os banners
            
            if(!$subsite){
                $subsite = $this->getEm()->getRepository('Entity\Site')->find("1");
                
            }
            $bannersLateral = $this->getEm()->getRepository('Entity\BannerGeral')->getBannersLateral($subsite, 'Lateral', 3);
            $bannersLateralDestaque = $this->getEm()->getRepository('Entity\BannerGeral')->getBannersLateral($subsite, 'Lateral Destaque', 1);
            
        }
        if($subsite)
        {
            $subsiteFunc = $this->getEm()->getRepository('Entity\FuncionalidadeSite')->getFuncionalidades($subsite->getId());
            foreach ($subsiteFunc as $func) {
                switch($func->getFuncionalidade()->getLabel()){
                    case 'Vídeos':
                        $arrayFunc[] = 'videos';
                    break;
                    case "Legislacão":
                        $arrayFunc[] = 'legislacao';
                    break;
                    case "Agenda":
                        $arrayFunc[] = 'agendaEventos';
                    break;
                    case "Notícias":
                        $arrayFunc[] = 'noticias';
                    break;           
                    case "Galeria":
                        $arrayFunc[] = 'galeria';
                    break;
                }
            }
            foreach ($bannersLateral as $banner){
                $banner->setUrlCompletaSite($subsite->getSigla(), $arrayFunc);
            }
            foreach ($bannersLateralDestaque as $banner){
                $banner->setUrlCompletaSite($subsite->getSigla(), $arrayFunc);
            }
            $this->getTpl()->setBannersLaterias($bannersLateral);
            $this->getTpl()->setBannersLateraisDestaques($bannersLateralDestaque);
        }
    }

    /**
     *
     * @return array
     */
    public function getBannersDivulgacao()
    {
        //Busca o subsite
        $subsite = $this->getSubsite();
        if($subsite){
            $bannersDivulgacao = $this->getEm()
                    ->getRepository('Entity\BannerGeral')
                    ->getConteudoHome($sigla, 'Divulgação', 4);
                
        }else{
            $bannersDivulgacao = $this->getEm()
                ->getRepository('Entity\BannerGeral')
                ->getConteudoHome(1, 'Divulgação', 4);
        }
        
        //$site = NULL, $nomeCategoria = NULL, $limit = NULL, $excessao = null
        return $bannersDivulgacao;
    }

    /**
     *
     * Busca os banners de rodape
     * @return array
     */
    public function getBannersRodape()
    {
        //Busca os banners de rodape
        $bannerRodape = $this->getEm()->getRepository('Entity\BannerGeral')->getConteudoHome(1, 'Rodapé', 3);

        //Retorna os banners
        return $bannerRodape;
    }

    /**
     *
     * Busca os banners de rodape
     * @return array
     */
    public function getEnderecoRodape()
    {
        //Busca os banners de rodape
        $enderecoRodape = $this->getEm()->getRepository('Entity\EnderecoRodape')->getEndereco();

        //Retorna os banners
        return $enderecoRodape;
    }

}