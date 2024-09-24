<?php

namespace CMS\Controller;

use Entity\SliderHome as SliderHomeEntity;
use Entity\Type;
use CMS\Service\ServiceRepository\SliderHome as SliderHomeService;
use Helpers\Param;
use LibraryController\CrudControllerInterface;

/**
 * SliderHomeController
 *
 * @author join-ti
 */
class SliderHomeController extends CrudController implements CrudControllerInterface {

    const PAGE_TITLE = "Destaque de subsites";
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
     * @var SliderHomeEntity
     */
    protected $entity;

    /**
     *
     * @var SliderHomeService
     */
    protected $service;

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
    public function __construct(\Template\TemplateInterface $tpl, \Helpers\Session $session) {
        parent::__construct($tpl, $session);
        $this->setUser($this->getUserSession());
    }

    /**
     *
     * @return array
     */
    public function getUser() {
        return $this->user;
    }

    /**
     *
     * @param array $user
     */
    public function setUser(array $user) {
        $this->user = $user;
    }

    /**
     *
     * @return String
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     *
     * @param String $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     *
     * @return String
     */
    public function getDefaultAction() {
        return $this->defaultAction;
    }

    /**
     *
     * @return SliderHomeEntity
     */
    public function getEntity() {
        if (!isset($this->entity)) {
            $this->entity = new SliderHomeEntity();
        }
        return $this->entity;
    }

    /**
     *
     * @param SliderHomeEntity $entity
     */
    public function setEntity(SliderHomeEntity $entity) {
        $this->entity = $entity;
    }

    /**
     *
     * @return SliderHomeService
     */
    public function getService() {
        if (!isset($this->service)) {
            $this->service = new SliderHomeService($this->getEm(), $this->getEntity(), $this->getSession());
        }
        return $this->service;
    }

    /**
     *
     * @param SliderHomeService $service
     */
    public function setService(SliderHomeService $service) {
        $this->service = $service;
    }

    /**
     *
     * @return string
     */
    public function lista() {
        //Cria os objetos status
        $naoPublicado = new Type("0", "Não publicado");
        $publicado = new Type("1", "Publicado");


        if ($this->user['sede']) {
            $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
        } else {
            $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
        }

        //Busca os anos
        $this->tpl->renderView(array(
            'status' => array($naoPublicado, $publicado),
            'titlePage' => $this->getTitle(),
            'subTitlePage' => "",
            'sites' => $sites
                )
        );

        return $this->tpl->output();
    }

    /**
     *
     * @param type $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0) {
        //Busca dados do slider
        if ($this->user['sede']) {
            $slider = $this->getEm()->getRepository("Entity\SliderHome")->find($id);
        } else {
            $slider = $this->getEm()->getRepository("Entity\SliderHome")->findByIdSite($id, $this->user['subsites']);
        }

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        //Organiza os ids das imagens
        $idImg = "";
        if ($slider) {
            if ($slider->getImagem()) {
                $idImg = $slider->getImagem()->getId();
            }
        }

        //Adiciona o css e o js da seleção de imagens
//        $this->tpl->addJS("/imagem/imagens.js");
//        $this->tpl->addCSS("/imagem/imagens.css");

        //$sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
        $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);

        $this->tpl->renderView(array(
            "data" => new \DateTime('now'),
            "slider" => $slider,
            "sites" => $sites,
            "idImg" => $idImg,
            "imagem" => $this->getHtmlImagenJcrop($idImg),
            "titlePage" => $this->getTitle(),
            "method" => "POST",
            )
        );

        return $this->tpl->output();
    }

    /**
     *
     * @return string
     */
    public function salvar() {
        $id = $this->getParam()->getInt('id');
        $dados = array(
            'id' => $id,
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('data_inicial')) . " " . $this->getParam()->get('hora_inicial')),
            'nome' => $this->getParam()->get('nome'),
            'descricao' => $this->getParam()->getString('descricao'),
            'imagem' => $this->getEm()->getReference("Entity\Imagem", $this->getParam()->get('imagemBanco')),
            'x1' => $this->getParam()->getString('x1'),
            'x2' => $this->getParam()->getString('x2'),
            'y1' => $this->getParam()->getString('y1'),
            'y2' => $this->getParam()->getString('y2'),
            'w' => $this->getParam()->getString('w'),
            'h' => $this->getParam()->getString('h')
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

        if ($id)
            $sliderHomeOrdem = $this->getEm()->getRepository("Entity\SliderHome")->getSliderOrdem($id);

        $return = $this->getService()->save($dados);

        if ($id) {
            foreach ($sliderHomeOrdem as $sliderHome) {
                if ($sliderHome['ordem'])
                    $this->getEm()->getRepository("Entity\SliderHome")->setOrdemSite($sliderHome['idSite'], $id, $sliderHome['ordem']);
            }
        }

        return json_encode($return);
    }

    /**
     *
     * @return String
     */
    public function pagination() {
        //Instancia o repository
        $em = $this->getEm();
        $repoSliderHome = $em->getRepository("Entity\SliderHome");

        //Armazena o parâmetro
        $param = $this->getParam();

        $paramSite = $param->get('site');

        //Busca o total
        $total = $repoSliderHome->countAll($this->getSession()->get('user'));

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status'),
            "site" => $paramSite
        );

        //Faz a busca e armazena o total de registros
        $sliderHome = $repoSliderHome->getBuscarSliderHome($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
        $totalFiltro = $repoSliderHome->getTotalBuscaSliderHome($filtros, $this->getSession()->get('user'));

        if ($paramSite) {
            $arraySliderOrdem = $repoSliderHome->getSliderOrdemSite($paramSite);
            foreach ($arraySliderOrdem as $slider) {
                $arraySlider[$slider['idSliderHome']] = $slider['ordem'];
            }
        }

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($sliderHome as $slider) {
            $linha = array();

            $linha[] = $this->getFields()->checkbox("sliderHome[]", $slider->getId());


            if ($this->verifyPermission('DESTSUB_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($slider->getLabel()), array("href" => "sliderHome/form/" . $slider->getId())) . $slider->getDataCadastro()->format('d/m/Y') . " as " . $slider->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($slider->getLabel()) . $slider->getDataCadastro()->format('d/m/Y') . " as " . $slider->getDataCadastro()->format('H:i');
            }

            $linha[] = "<input data-id='{$slider->getId()}' value='{$arraySlider[$slider->getId()]}' name='slideHomeOrdem' type='text' style='float:right;text-align:center;width:100px;' onfocus='verificaFiltros();'>";

            $linha[] = $slider->getPublicado() ? $tag->span("Publicado", array('class' => 'publicado')) : $tag->span("Não Publicado", array('class' => 'naoPublicado'));

            $linha[] = "<a href='javascript:visualizar({$slider->getId()})' class='btn btn3 btn_search' ></a>";
            $dados[] = $linha;
        }


        //Organiza o retorno e retorna via json
        $retorno['sEcho'] = Param::getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $totalFiltro;
        $retorno['iTotalRecords'] = $total;
        $retorno['aaData'] = $dados;
        $retorno['paramSite'] = $paramSite;

        return json_encode($retorno);
    }

    /**
     *
     * @return String
     */
    public function delete() {
        //Busca o entityManager e o service
        $service = $this->getService();

        //Pega os Ids Enviados
        $ids = $this->getParam()->getArray("sel");

        //Faz a exclusão
        $resultado = $service->delete($ids);

        //Retorna para o js
        return json_encode($resultado);
    }

    public function ajaxAtualizarOrdenacao() {
        if (!$this->verifyPermission('DESTSUB_ALTPOS')) {
            die('Acesso negado');
        }

        $paramSite = $this->getParam()->get('site');
        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach ($paramOrdenation as $item) {
            $newOrdenation[$item['id']] = $item['ordenacao'];
        }
        $repository = $this->getEm()->getRepository('Entity\SliderHome');

        // Atualiza as entidades
        foreach ($newOrdenation as $id => $ordenacao) {
            // Se vazio
            if (empty($ordenacao)) {
                $ordenacao = NULL;
            }
            $repository->setOrdemSite($paramSite, $id, $ordenacao);
        }

        // Salva
        $this->getEm()->flush();

        return json_encode(array(
            'resultado' => 'ok',
        ));
    }

    public function visualizar($id) {
        // Coloca www se necessário
        $www = preg_match('/^www/', $_SERVER['SERVER_NAME']) ? 'www.' : '';
        $routeParam = $this->getParam()->get('route');
        $route = !empty($routeParam) ? $routeParam : '/';

        //Busca um site a qual o banner pertence
        //$idSite = $this->getEm()->getRepository('Entity\SliderHome')->getSiteVisualizacao($id);
        $sliderHome = $this->getEm()->getRepository('Entity\SliderHome')->find($id);
        foreach ($sliderHome->getSites() as $site)
        {
            $idSite = (int)$site->getId();
        }
        $subsite = $this->getEm()->getRepository('Entity\Site')->find($idSite);
        $idSite = strtolower($subsite->getSigla());
        $siteUrl = !empty($idSite) ? "/" . $idSite : "";
        
        //Gera o hash
        $hash = $this->getHash();

        //Redireciona para a página
        $url = "http://" . $www  . URL_PORTAL . $siteUrl . $route;
        $url = str_replace(array('#id#', '#hash#'), array($id, $hash), $url);

        return json_encode(array('url' => $url));
    }

}
