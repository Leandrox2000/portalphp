<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\Noticia as NoticiaService;
use Entity\Noticia as NoticiaEntity;
use Entity\Type;
use Helpers\Mail;

/**
 * Description of Noticia
 * 
 * @author Luciano
 */
class NoticiaController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Notícias";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var NoticiaService
     */
    private $service;

    /**
     *
     * @var NoticiaEntity
     */
    private $entity;

    /**
     *
     * @var array
     */
    private $user;

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
     * @return \CMS\Service\ServiceRepository\Noticia
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new NoticiaService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return \Entity\Noticia
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new NoticiaEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Noticia $service
     */
    public function setService(NoticiaService $service)
    {
        $this->service = $service;
        $this->service->setSession($this->getSession());
    }

    /**
     *
     * @param \Entity\Noticia $entity
     */
    public function setEntity(NoticiaEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        //Adiciona o css e o js da seleção de imagens
//        $this->tpl->addJS("/imagem/imagens.js");
//        $this->tpl->addCSS("/imagem/imagens.css");
        
        $this->tpl->addJS("/galeria/galerias.js");

        $this->tpl->addJS("/plugins/EasyTree/jquery.easytree.js");
        $this->tpl->addJS("/plugins/EasyTree/jquery.easytree.min.js");
        $this->tpl->addCSS("/plugins/skin-win8/ui.easytree.css");

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }
        
        #REGRA DE PERMISS�O
//      $sites = $this->user['sede'] ? $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC')) : $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
//      $noticia = $this->user['sede'] ? $this->getEm()->getRepository($this->getService()->getNameEntity())->find($id) : $this->getEm()->getRepository($this->getService()->getNameEntity())->findByIdSite($id, $this->user['subsites']);
        $sites =  $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
        $noticia = $this->getEm()->getRepository($this->getService()->getNameEntity())->find($id);
        // ->findBy(array('age' => 20), array('name' => 'ASC'), 10, 0);
        // $noticia = $this->user['sede'] ? $this->getEm()->getRepository($this->getService()->getNameEntity())->find($id) : $this->getEm()->getRepository($this->getService()->getNameEntity())->findByIdSite($id, $this->user['subsites']);


        //Busca os ids das galeria relacionadas
        $idsGalerias = "";
        $htmlGalerias = "";

        if ($noticia) {
            $arrayIdsGalerias = array();
            foreach ($noticia->getGalerias() as $gal) {
                $arrayIdsGalerias[] = $gal->getGaleria()->getId();
            } 
            // ksort($arrayIdsGalerias);
            $idsGalerias = implode(',', $arrayIdsGalerias);
            $htmlGalerias = $this->getHtmlGalerias($idsGalerias, $id);
        }

        $repository = $this->getEm()->getRepository('Entity\Noticia');

        $permissao = $repository->validaVinculo($id, 'noticia');

        $this->getTpl()->renderView(
            array(
                "data" => new \DateTime("now"),
                "hora" => new \DateTime("now"),
                "noticia" => $noticia,
                "method" => "POST",
                "sites" => $sites,
                "titlePage" => $this->getTitle(),
                "imagem" => $noticia && $noticia->getImagem() ? $this->getHtmlImagens($noticia->getImagem()->getId()) : "",
                "idImg" => $noticia && $noticia->getImagem() ? $noticia->getImagem()->getId() : "",
                "idsGalerias" => $idsGalerias,
                "htmlGalerias" => $htmlGalerias,
                "compartilhado" => $permissao
            )
        );

        return $this->getTpl()->output();
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function lista()
    {
        $this->tpl->addCSS("/noticia/lista.css");
        $sites = array();
//        $status = array();
//        $status[] = new Type("0", "Não publicado");
//        $status[] = new Type("1", "Publicado");
//        $status[] = new Type("2", "Visível Internamente");
        
        $status = array();
        $status[] = new Type("0", "Não publicado");
        $status[] = new Type("1", "Publicado");
        $status[] = new Type("2", "Compartilhado");
        $status[] = new Type("3", "Visível Internamente"); // 2
 
       
        
        #REGRA DE PERMISS�O
//      $sites = $this->user['sede'] ? $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC')) : $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
        //$sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
        $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
        
        $this->tpl->renderView(
            array(
                'titlePage' => $this->getTitle(),
                'status' => $status,
                'sites' => $sites
            )
        );

        return $this->getTpl()->output();
    }

    /**
     *
     * @return JSON
     */
    public function pagination()
    {
        $tag = $this->getTag();
        $param = $this->getParam();
        $dados = array();
        $repNoticias = $this->getEm()->getRepository($this->getService()->getNameEntity());
        $total = $repNoticias->countAll($this->getSession()->get('user'));

        $filtros = array(
                'status' => $param->get("status"),
                'site' => $param->getInt("site"),
                "busca" => $param->get("sSearch"),
                "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get("data_inicial")),
                "data_final" => $this->getDatetimeFomat()->formatUs($param->get("data_final")),
            );
        
        //Faz a busca e armazena o total de registros
        //Jeito Antigo
        //$noticias = $repNoticias->getNoticias($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"),$filtros,$this->getSession());
        if(!$param->get('site') || in_array($param->get('site'), $_SESSION['user']['subsites'])){
            $noticias = $repNoticias->buscaRegistroByLogin('Entity\Noticia', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
            $totalFiltro = $repNoticias->buscaRegistroByLogin('Entity\Noticia', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), true);
            
        }else{
            $noticias = $repNoticias->getDataBySubsite('Entity\Noticia', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'));
            $totalFiltro = $repNoticias->getDataBySubsite('Entity\Noticia', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'), true);
            
        }
        
        $totalFiltro = $totalFiltro[0]['total'];
        
         #INSER��O
       // $totalFiltro = $repNoticias->getTotalBusca('Entity\Noticia', $filtros, $this->getSession()->get('user'));
       

        foreach ($noticias as $noticia) {
            $linha = array();
            $pais = array();
            $linha[] = $this->getFields()->checkbox("sel[]", $noticia->getId());

            #INSER��O
            $sitesPai = $this->getEm()->find("Entity\Noticia", $noticia->getId())->getPaiSites();
            if($sitesPai){
                foreach ($sitesPai as $pai) {
                    $pais[] = $pai->getSigla();
                }
            }
            $siglas = implode(", ", $pais);
            
            if ($this->verifyPermission('NOTIC_ALTERAR')) {
                //$linha[] = $tag->link($tag->h4($noticia->getLabel()), array("href" => "noticia/form/" . $noticia->getId())) . $noticia->getDataCadastro()->format('d/m/Y') . " as " . $noticia->getDataCadastro()->format('H:i');
                $linha[] = $tag->link($tag->h4($noticia->getLabel()), array("href" => "noticia/form/" . $noticia->getId())) . 'Criado por ' . $noticia->getLogin() . ' - '.$siglas.' - ' . ' em ' . $noticia->getDataCadastro()->format('d/m/Y') . " as " . $noticia->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($noticia->getLabel()) . $noticia->getDataCadastro()->format('d/m/Y') . " as " . $noticia->getDataCadastro()->format('H:i');
            }

           #INSER��O
            if ($repNoticias->getCompartilhadosById($noticia->getId()) == 1) {
                $linha[] = "<span class='compartilhado'>Compartilhado de $siglas</span>";
            } else {
                if ($noticia->getFlagNoticia() == 1) {
                    $linha[] = '<span class="glyphicon glyphicon-eye-open cms-olho" aria-hidden="true"></span>'.$tag->span("Visível Internamente", array('class' => 'interno'));
                }else{
                    $linha[] = $noticia->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
                }
            }
            
            
            
            $linha[] = "<a href='javascript:visualizar({$noticia->getId()})' class='btn btn3 btn_search' ></a>";
            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $totalFiltro;
        $retorno['iTotalRecords'] = $total;
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @return JSON
     */
    public function salvar()
    {
        $imgId = $this->getParam()->getString('imagemBanco');
        $respository = $this->getEm()->getRepository("Entity\Noticia");

        if (!empty($imgId)) {
            $img = $this->getEm()->getReference('Entity\Imagem', $imgId);
        } else {
            $img = null;
        }

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'login' => $this->user['dadosUser']['login'],
            'titulo' => $this->getParam()->get("titulo"),
            'conteudo' => $this->getParam()->getString("conteudo"),
            'palavrasChave' => $this->getParam()->getString("palavrasChave"),
            'imagem' => $img,
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
            'idsGalerias' => $this->getParam()->get('galeriaBanco'),
            'ordemGalerias' => $this->getParam()->get('galeriaOrdem'),
            'sites' => $this->getParam()->get('sites')
        );



        $dataFinal = $this->getParam()->get('dataFinal');
        $horaFinal = $this->getParam()->get('horaFinal');


        // Caso a data final seja preenchida, preenche a variável no formato YYYY-MM-DD HH:MM:SS
        // ao contrário, preenche a variável com NULL para poder apagar no banco caso haja alguma data setada na coluna da tabela.
        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal) . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }
           
        if ($this->getParam()->getInt('id') > 0) {
            
            $repository = $this->getEm()->getRepository($this->getService()->getNameEntity())->find($this->getParam()->getInt('id'));
            
            if ($repository->getFlagNoticia() == 1) {
                $dados['flagNoticia'] = 1;
            }
        } else {
            $dados['flagNoticia'] = 0;
        }
        
//        $dados['flagNoticia'] = $this->getParam()->get('flagNoticia');
//        if ($dados['flagNoticia'] == "") {
//            $dados['flagNoticia'] = null;
//        }
        
        //se possui mais de uma galeria, obrigatoriamente vai setar como posição de baixo da página
//        $pos = strpos($dados['idsGalerias'],",");
//        if ($pos==true) {
//            $dados['posicaoPagina'] = 2;
//        } else {
//            $dados['posicaoPagina'] = 1;
//        }

        $retorno = $this->getService()->save($dados);
        return json_encode($retorno);
    }


    /**
     *
     * @param string $ids
     * @return string
     */
    public function getHtmlGalerias($ids, $idNoticia = null)
    {

        if (!empty($ids)) {

            //Busca e organiza as relações da galeria com a notícia
            $arrayRelacoes = array();
            if (!is_null($idNoticia)) {
                $relacoes = $this->getEm()->getRepository('Entity\NoticiaGaleria')->getNoticiaGaleriaIdsPaginas($idNoticia);
                foreach ($relacoes as $relacao) {
                    $arrayRelacoes[$relacao->getGaleria()->getId()] = $relacao->getPosicaoPagina();
                }
            }
            
            //Busca as galerias
//            $galerias = $this->getEm()->getRepository('Entity\NoticiaGaleria')->findBy(array('galeria' => explode(',', $ids), 'noticia' => $idNoticia, 'publicado' => 1), array('ordemGaleria' => 'ASC'));
            $galerias = $this->getEm()->getRepository('Entity\NoticiaGaleria')->getGaleriasNoticiaPublicadas($ids, $idNoticia);

            //Percorre e organiza o HTML da listagem
            $tag = $this->getTag();
            $html = "";

            $c = count($galerias);
            

            foreach ($galerias as $galeria) {
                $galeria = $galeria->getGaleria();
                $checked = isset($arrayRelacoes[$galeria->getId()]) ? $arrayRelacoes[$galeria->getId()] : 1;

                $html .= "<div id='galeria{$galeria->getId()}' class='photo' data-id='{$galeria->getId()}'>";
                $html .= $tag->h4($galeria->getLabel());
                $html .= "<br />";
                // $html .= "<strong>Posicionar galeria: </strong><br />";

                if ($c == 1) {
                    if ($checked == 1 ) {
                        $html .= "<label><input type='radio' name='posicao{$galeria->getId()}' value='1' checked /> No início da página  </label> ";
                        $html .= "<label><input type='radio' name='posicao{$galeria->getId()}' value='2' /> Ao final da página  </label><br />";
                    } else {
                        $html .= "<label><input type='radio' name='posicao{$galeria->getId()}' value='1' /> No início da página  </label> ";
                        $html .= "<label><input type='radio' name='posicao{$galeria->getId()}' value='2' checked /> Ao final da página  </label><br />";
                    }
                } else {
                    $html .= "<label><input type='radio' name='posicao{$galeria->getId()}' value='1' disabled /> No início da página  </label> ";
                    $html .= "<label><input type='radio' name='posicao{$galeria->getId()}' value='2' checked /> Ao final da página  </label><br />";
                }
                $html .= "<a href='javascript:excluirGaleria({$galeria->getId()})' class='btn btn3 btn_trash'></a>";
                $html .= "</div>";
                
            }
            return $html;
        } else {
            return '';
        }
    }

    public function alterarStatus($array = null, $status = null) {
        $array = ($array == null) ? $this->getParam()->getArray("sel") : $array;
        $status = ($status == null) ? $this->getParam()->getInt("status") : $status;
        $tipo = 'noticia';

        $retorno = $this->getService()->alterarStatus($array, $status, $tipo);

        if ($this->getService() instanceof SolrAwareInterface) {
            foreach ($array as $id) {
                /* Atualiza índice do Solr */
                $className = $this->getService()->getNameEntity();
                $entityName = $this->getEm()->getClassMetadata($className)->getName();
                $entity = $this->getEm()
                        ->getRepository($entityName)
                        ->find($id);
                $dadosSolr = $this->getService()->getDadosSolr($entity);
                $solrManager = new \Helpers\SolrManager();
                $solrManager->save($dadosSolr);
            }
        }

        if ($status==1) 
        {
            $ids = implode(",", $array);
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
                $mensagem .= "\n Notícia: ". $link['titulo'] ."<br /> \n";
                $mensagem .= "Link(s): <br /> \n \n";
                foreach ($link['siglas'] as $sigla) :
                    if ($sigla == "SEDE") {
                        $mensagem .= "<a href='".$domainCfg['portal']."/noticias/detalhes/".$link['id']."'>".$link['titulo']."</a><br /> \n";
                    } else{
                        $mensagem .= "<a href='".$domainCfg['portal']."/".strtolower($sigla)."/noticias/detalhes/".$link['id']."'>".$link['titulo']."</a><br /> \n";
                    }
                endforeach;
            endforeach;
            $mensagem .= "\n\n<br /><br />Email gerado automaticamente pelo sistema Iphan (CMS).";


            $config = require BASE_PATH . 'config/mail.php';
            $mailer = new Mail($config);
            $mailer->send('Portal do IPHAN - NOTICIA PUBLICADA', $mensagem);
        }


        return json_encode($retorno);
    }

    public function alterarVisivel($array = null, $status = null) {
        $array = ($array == null) ? $this->getParam()->getArray("sel") : $array;
        $status = ($status == null) ? $this->getParam()->getInt("status") : $status;

        // $retorno = $this->getService()->alterarStatus($array, $status);
        $retorno = $this->getService()->alterarVisivel($array, $status);

        if ($this->getService() instanceof SolrAwareInterface) {
            foreach ($array as $id) {
                /* Atualiza índice do Solr */
                $className = $this->getService()->getNameEntity();
                $entityName = $this->getEm()->getClassMetadata($className)->getName();
                $entity = $this->getEm()
                        ->getRepository($entityName)
                        ->find($id);
                $dadosSolr = $this->getService()->getDadosSolr($entity);
                $solrManager = new \Helpers\SolrManager();
                $solrManager->save($dadosSolr);
            }
        }

        return json_encode($retorno);
    }

    public function validaSubsiteVinculadoNoticia()
    {
        $repository = $this->getEm()->getRepository('Entity\Noticia');

        $retorno = $repository->validaVinculo($_REQUEST['id'], 'noticia');

        return json_encode(array('permissao' => $retorno));
    }

    //Compartilha registro com subsites selecionados
    public function compartilhar()
    {
        $error = array();

        try {
            if (!empty($_REQUEST['sites'])) {

                $sites = $_REQUEST['sites'];

                $connection = $this->getEm()->getConnection();

                foreach ($_SESSION['user']['subsites'] as $site) {
                    $connection->query("DELETE FROM tb_noticia_site WHERE id_noticia = {$_REQUEST['id']} AND id_site = {$site}");
                }

                foreach ($sites as $site) {
                    $statment = $connection->prepare("INSERT INTO tb_noticia_site (id_noticia, id_site) VALUES({$_REQUEST['id']}, $site)");

                    $statment->execute();
                }
                $response = 1;
                $success = "Registro compartilhado com sucesso";
            } else {
                $response = 1;
                $success = "Registro descompartilhado com sucesso";
                $this->getService()->deletaCompartilhadosById($_REQUEST['id'], 'noticia');
            }

        } catch(\Exception $exc) {
            $error[] = "Não foi possível executar esta ação";
        }

        return json_encode(array('error' => $error, 'response' => $response, 'success' => $success));
    }
}
