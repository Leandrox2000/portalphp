<?php

namespace CMS\Controller;

use Entity\Galeria as GaleriaEntity;
use Entity\Type;
use CMS\Service\ServiceRepository\Galeria as GaleriaService;
use Helpers\Param;
use LibraryController\CrudControllerInterface;


/** 
 * GaleriaController
 *
 * @author join-ti
 */
class GaleriaController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Galerias";
    const DEFAULT_ACTION = "lista";

    protected $id;

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
     * @var GaleriaEntity
     */
    protected $entity;

    /**
     *
     * @var GaleriaService
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
     * @return GaleriaEntity
     */
    public function getEntity()
    {
        if (!isset($this->entity)) {
            $this->entity = new GaleriaEntity();
        }
        
        return $this->entity;
    }

    /**
     *
     * @param GaleriaEntity $entity
     */
    public function setEntity(GaleriaEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return GaleriaService
     */
    public function getService()
    {
        if (!isset($this->service)) {
            $this->service = new GaleriaService($this->getEm(), $this->getEntity(), $this->getSession());
        }
        return $this->service;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



    /**
     *
     * @param GaleriaService $service
     */
    public function setService(GaleriaService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * Utilizado para exibir a página de listagem
     * @return string
     */
    public function lista()
    {
        //Cria os objetos status
        #Jeito Antigo
//        $naoPublicado = new Type("0", "Não publicado");
//        $publicado = new Type("1", "Publicado");
//        $compartilhado = new Type("2", "Compartilhados");
        
        $status = array();
        $status[] = new Type("0", "Não publicado");
        $status[] = new Type("1", "Publicado");
        $status[] = new Type("2", "Compartilhado");

        #REGRA DE PERMISS�O
//      $sites = $this->user['sede'] ? $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC')) : $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
//        $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
        //Lista todos subsites
        #Jeito Antigo
        //$sites = $this->getEm()->getRepository("Entity\Site")->findAll();
        $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
        //Busca os anos
        $this->tpl->renderView(array(
            'status' => $status,
            'titlePage' => $this->getTitle(),
            'subTitlePage' => "",
            'sites' => $sites
        ));

        return $this->tpl->output();
    }

    /**
     *
     * @param type $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        $this->setId($id);
        //Busca dados do boletim
        $repository = $this->getEm()->getRepository("Entity\Galeria");

        //Valida subsite vinculado com registro x vinculado com usuário
//        $repository->validaSubsiteVinculado('Entity\Galeria', $id);

        $galeria = $repository->find($id);
        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }
        //Organiza os ids das imagens
        $idsImgs = "";
        
        //Verifica se a galeria foi encontrada
        if ($galeria) {
        	$ids = $this->getHtmlImagensGaleria($id);
            $idsImgs = array();
           
            foreach ($ids as $idd) {
                $idsImgs[] = $idd['imagemId'];
            }
           
            $idsImgsString = implode(',', $idsImgs);
        }


        $repository = $this->getEm()->getRepository('Entity\Galeria');

        $permissao = $repository->validaVinculo($id, 'galeria');
        
        #REGRA DE PERMISS�O
//      $sites = $this->user['sede'] ? $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC')) : $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
        $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);

        $this->tpl->renderView(array(
            "data" => new \DateTime('now'),
            "galeria" => $galeria,
            "sites" => $sites,
            "idsImgs" => $idsImgsString,
            "imagens" => $this->getHtmlImagensArray($idsImgs),
            "titlePage" => $this->getTitle(),
            "method" => "POST",
            "compartilhado" => $permissao
        ));

        return $this->tpl->output();
    }

    /**
     *
     * @return string
     */
    public function salvar()
    {
        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('data_inicial')) . " " . $this->getParam()->get('hora_inicial')),
            'titulo' => $this->getParam()->get('titulo'),
            'descricao' => $this->getParam()->getString("descricao"),
            'imagens' => $this->getParam()->get('imagemBanco'),
            'login' => $_SESSION['user']['dadosUser']['login'],
            'sites' => $this->getParam()->get('sites')
        );

        $ordem = $this->getParam()->get('imagemOrdem');

        //'ordem' => $this->getParam()->getInt('id') ? $this->getParam()->getInt("ordem") : $this->getEm()->getRepository($this->getService()->getNameEntity())->buscarUltimaOrdem()
        //if($this->getParam()->getInt('id')) $this->getService()->insertOrdem($this->getParam()->get('sites'),$this->getParam()->getInt('id'));
        
        $dataFinal = $this->getParam()->get('data_final');
        $horaFinal = $this->getParam()->get('hora_final');

        // se a dataFinal não estiver setada ou receber o valor vazio
        // a variável deve ser setada como NULA
        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal) . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }

        return json_encode($this->getService()->save($dados, $ordem));
    }
    
    /**
     * Verifica se a galeria esta presente em alguma Noticia, Nova pagina ou Fotetecas.
     * Não é nenhuma brastemp, mas foi o que deu para fazer.
     * 
     * @param int $id Código da galeria
     * @throws Exception
     * @return null
     */
    private function exists($id = 0){
        
        $errors = array();
        
        // Doctrine\ORM\EntityManager
        $em = $this->getEm();
        
        // Doctrine\DBAL\Connection
        $dbal = $em->getConnection();
        
        $mappers = array(
            array(
                'primary' => 'tb_fototeca',
                'foreign' => 'tb_fototeca_galeria',
                'title' => 'no_nome',
                'id' => 'id_fototeca',
                'desc' => 'Fototeca'
            ),
            array(
                'primary' => 'tb_noticia',
                'foreign' => 'tb_noticia_galeria',
                'title' => 'no_titulo',
                'id' => 'id_noticia',
                'desc' => 'Notícia'
            ),
            array(
                'primary' => 'tb_pagina_estatica',
                'foreign' => 'tb_pagina_estatica_galeria',
                'title' => 'no_titulo',
                'id' => 'id_pagina_estatica',
                'desc' => 'Nova página'
            )
        );

        foreach($mappers as $mapper) {
        
            // Possui algum vínculo com alguma Fototeca, Notícia ou Nova página
            if(intval($dbal->fetchColumn('SELECT COUNT(*) FROM '.$mapper['foreign'].' WHERE id_galeria = '.$id)) > 0) {

                // Acha os títulos que estão vinculados a esta categoria
                $statement = $dbal->query("SELECT {$mapper['title']}  FROM {$mapper['primary']} WHERE {$mapper['id']} IN(SELECT {$mapper['id']} FROM {$mapper['foreign']} WHERE id_galeria= $id)");
                $titulos = implode(', ', $statement->fetchAll(\PDO::FETCH_COLUMN));
                
                $errors[] = sprintf('- %s : %s', $mapper['desc'], $titulos);  
            }
        }
        
        if(!empty($errors)) {
            
            // Acha o título desta categoria
            $titulo = $dbal->fetchColumn('SELECT no_titulo FROM tb_galeria WHERE id_galeria = '. $id);
            
            $message  = sprintf('Não é possível excluir a galeria "%s", pois esta esta sendo utilizada em:', $titulo).PHP_EOL;
            $message .= implode(PHP_EOL, $errors);
            
            throw new \Exception(nl2br($message));
        }
        

        //throw new \Exception(sprintf('Não caiu em nenhum')); // Debug para não excluir todos os registros :)
    }

//    public function delete() {
//        
//        $resp = array(
//            'error' => array(), 
//            'response' => 0, 
//            'success' => ''
//        );
//         
//        try {
//                
//            //Pega os ids que vieram por parâmetro
//            $array = $this->getParam()->getArray("sel");
//            
//            if(empty($array)) {
//                throw new \Exception('Nenhum registro foi selecionado');
//            }
//            
////            foreach($array as $id) {
////
////                $this->exists($id);
////            }
//            
//            // Verifica se o registro é indexável
//            if ($this->getService() instanceof SolrAwareInterface) {
//                $entityName = $this->getService()->getNameEntity();
//                $solrManager = new \Helpers\SolrManager();
//                $solrManager->bulkDelete($entityName, $array);
//            }
//
//            //Deleta os registros
//            if (count($array) > 0) {
//                $resultDelete = $this->getService()->delete($array);
//            }
//
//            if ($resultDelete == null) {
//                $resultDelete = array();
//            }
//
//            //Verificação para bular a gamba de sempre dar o response Positivo
//            if (key_exists("error", $resultDelete) && key_exists("response", $resultDelete) && key_exists("success", $resultDelete)) {
//                return json_encode($resultDelete);
//            }
//
//            $resp['response'] = 1;
//            $resp['success'] = 'Registros deletados com sucesso!';
//        } 
//        catch(\Exception $e) {
//            
//            $resp['error'][] = $e->getMessage();
//        }
//        return json_encode($resp);
//    }
    /**
     *
     * Resposável por retornar os registros paginados
     * @return String
     */
    public function pagination()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoGaleria = $em->getRepository("Entity\Galeria");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repoGaleria->countAll($this->getSession()->get('user'));

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status'),
            "site" => $param->get('site')
        );
        
        //Faz a busca e armazena o total de registros
//        echo "<pre>";
//        \Doctrine\Common\Util\Debug::dump($_SESSION['user']['subsites']);
//        echo "</pre>";
        
        if(!$param->get('site') || in_array($param->get('site'), $_SESSION['user']['subsites'])){
            //Busca registros por subsites vinculados ao usuário
            $galerias = $repoGaleria->buscaRegistroByLogin('Entity\Galeria', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
            $totalFiltro = $repoGaleria->buscaRegistroByLogin('Entity\Galeria', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), true);
            
        }else{
            
            //Busca registros por subsites vinculados
            $galerias = $repoGaleria->getDataBySubsite('Entity\Galeria', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'));
            $totalFiltro = $repoGaleria->getDataBySubsite('Entity\Galeria', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'), true);
        }
     
        $totalFiltro = $totalFiltro[0]['total'];
        //$totalFiltro = $repoGaleria->getTotalBusca('Entity\Galeria', $filtros, $this->getSession()->get('user'));
 
        //$totalFiltro = count($galerias);
        
        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        
        

        
        foreach ($galerias as $galeria) {
            $linha = array();
            $pais = array();
            
            #INSER��O
            $sitesPai = $this->getEm()->find("Entity\Galeria", $galeria->getId())->getPaiSites();
            if($sitesPai){
                foreach ($sitesPai as $pai) {
                    $pais[] = $pai->getSigla();
                }
            }
            $siglas = implode(", ", $pais);
            
            $linha[] = $this->getFields()->checkbox("galeria[]", $galeria->getId());

            if ($this->verifyPermission('GALER_ALTERAR')) {
                //$linha[] = $tag->link($tag->h4($galeria->getLabel()), array("href" => "galeria/form/" . $galeria->getId())) . 'Criado por ' . $galeria->getLogin() . ' em ' . $galeria->getDataCadastro()->format('d/m/Y') . " as " . $galeria->getDataCadastro()->format('H:i');
                $linha[] = $tag->link($tag->h4($galeria->getLabel()), array("href" => "galeria/form/" . $galeria->getId())) . 'Criado por ' . $galeria->getLogin() . ' - '.$siglas.' - ' . ' em ' . $galeria->getDataCadastro()->format('d/m/Y') . " as " . $galeria->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($galeria->getLabel()) . $galeria->getDataCadastro()->format('d/m/Y') . " as " . $galeria->getDataCadastro()->format('H:i');
            }

            
            #INSER��O
            if ($repoGaleria->getCompartilhadosById($galeria->getId()) == 1) {
                $linha[] = "<span class='compartilhado'>Compartilhado de $siglas</span>";
            } else {
                $linha[] = $galeria->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
            }
            
            //$linha[] = $galeria->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
            $dados[] = $linha;
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
     * @param int $categoria
     * @return \Template\TemplateAmanda
     */
    public function galerias($tipo)
    {
        $categoria = $_GET['categoria'];
        $idsMarcados = $_GET['idsMarcados'];
        
        if ($categoria == "noticia") {
            return $this->getTpl()->renderView(array('tipo' => $tipo, 'categoria' => $categoria, 'idsMarcados' => $idsMarcados));
        } else {
            return $this->getTpl()->renderView(array('tipo' => $tipo, 'idsMarcados' => $idsMarcados));
        }
    }

    /**
     *
     * @return Método de paginação do colorbox
     */
    public function paginacaoColorbox()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoGaleria = $em->getRepository("Entity\Galeria");

        //Armazena a busca de parametro
        $param = $this->getParam();

        //Busca o total
        $total = $repoGaleria->countAll($this->getSession()->get('user'));
        
         $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8')
        );

        //Faz a busca
        $galerias = $repoGaleria->getBuscaGaleria($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
        //$galerias = $em->getRepository("Entity\GaleriaSite")->getBuscaGaleriaOrder($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array(), $this->getSession()->get('user'));
        
        //Busca o total de registros
        $totalFiltro = $repoGaleria->getTotalBuscaGaleria($filtros, $this->getSession()->get('user'));

        //Organiza os ids em array
        $ids = explode(',', $param->get('ids'));
        $arrayIds = array();

        foreach ($ids as $id) {
            $arrayIds[$id] = $id;
        }

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();
        if (!empty($galerias)) {
            foreach ($galerias as $galeria) {
                if ($galeria->getPublicado() == 1) {
                    $linha = array();
                    $checked = isset($arrayIds[$galeria->getId()]) ? "checked" : "";

                    if ($param->get('tipo') == 'radio') {
                        $linha[] = "<input type='radio' name='galeria' value={$galeria->getId()} class='marcar' {$checked} />";
                    } else {
                        $linha[] = "<input type='checkbox' name='galeria[]' value={$galeria->getId()} class='marcar marcar-galeria-colorbox' {$checked} />";
                    }

                    $linha[] = $tag->h4($galeria->getLabel()) . $galeria->getDataCadastro()->format('d/m/Y') . " as " . $galeria->getDataCadastro()->format('H:i');
                    $linha[] = $galeria->getPublicado() ? "Publicado" : "Não Publicado";

                    $dados[] = $linha;
                }
            }
        }

        //Organiza o retorno e retorna via json
        $retorno['sEcho'] = Param::getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $totalFiltro;
        $retorno['iTotalRecords'] = $total;
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }
    
     
     public function ajaxAtualizarOrdenacaoGaleria()
    {

        $paramOrdenation = $this->getParam()->get('ordenacao');
        $id_site = $this->getParam()->get('site');
        $newOrdenation = array();
        foreach($paramOrdenation as $item){
            $newOrdenation[$item['id']] = (int)$item['ordenacao'];
        }
       
        return json_encode($this->getService()->updateOrdem($newOrdenation,$id_site));
    }

    public function validaSubsiteVinculadoGaleria()
    {
        $repository = $this->getEm()->getRepository('Entity\Galeria');

        $retorno = $repository->validaVinculo($_REQUEST['id'], 'galeria');

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
                
                $galeria = $this->getEm()->getRepository('Entity\Galeria')->find($_REQUEST['id']);

                $ordem = $galeria->getGaleriasSite();

                foreach ($ordem as $valor) {
                    $g = $valor->getGaleria();
                    $s = $valor->getSite();
                    $order = $valor->getOrdem();
                    
                    $bkp_ordenacao[$s->getId()][$g->getId()] = $order;
                }
                
                foreach ($_SESSION['user']['subsites'] as $site) {
                    $connection->query("DELETE FROM tb_galeria_site WHERE id_galeria = {$_REQUEST['id']} AND id_site = {$site}");
                    $connection->query("DELETE FROM tb_galeria_ordem WHERE id_galeria = {$_REQUEST['id']} AND id_site = {$site}");
                }

                foreach ($sites as $site) {
                    $statment = $connection->prepare("INSERT INTO tb_galeria_site (id_galeria, id_site) VALUES({$_REQUEST['id']}, $site)");
                    $statment->execute();
                    
                    $ordem = $bkp_ordenacao[$site][$_REQUEST['id']];
                    
                    if(!isset($ordem)){
                        $entidade_site = $this->getEm()->getRepository('Entity\Site')->find($site);
                        $ordem = $this->getEm()->getRepository("Entity\GaleriaSite")->buscarUltimaOrdem($entidade_site);
                    }
                    
                    $statment = $connection->prepare("INSERT INTO tb_galeria_ordem (id_galeria, id_site, nu_ordem) VALUES({$_REQUEST['id']}, $site, {$ordem})");
                    $statment->execute();
                }
                $response = 1;
                $success = "Registro compartilhado com sucesso";
            } else {
                $response = 1;
                $success = "Registro descompartilhado com sucesso";
                $this->getService()->deletaCompartilhadosById($_REQUEST['id'], 'galeria');
            }

        } catch(\Exception $exc) {
            $error[] = "Não foi possível executar esta ação";
        }

        return json_encode(array('error' => $error, 'response' => $response, 'success' => $success));
    }
    
    /**
     *
     * @param string $ids
     * @return string
     */
    public function getHtmlImagens($ids = "") {
        if (!empty($ids)) {
            //Organiza os ids em imagens
            $ids = explode(',', $ids);

            //Busca as imagens
            $imagens = $this->getEm()->getRepository('Entity\Imagem')->getImagemIds($ids);
            $arrImagens = array();
            
            foreach($imagens as $img){
                $arrImagens[$img->getId()] = $img;
            }
            
            //Cria o html com as imagens
            $html = "";

            $html .= "<div class='gallerywrapper'>";
            $html .= "<ul  class='imagelist'>";

            //Percorre as imagens e monta o HTML
            foreach ($ids as $idImagem) {
                if(isset($arrImagens[$idImagem])){
                    $img = $arrImagens[$idImagem];
                    $html .= "<li id='img{$img->getId()}' name='".$img->getId()."'>";
                    $html .= "<img src='uploads/ckfinder/images/{$this->getHelperString()->removeSpecial($img->getPasta()->getCategoria()->getNome())}/{$img->getPasta()->getCaminho()}/{$img->getImagem()}' />";
                    $html .= "<span><a class='delete' href='javascript:excluirImagem({$img->getId()})'></a></span>";
                    $html .= "</li>";
                }
            }

            $html .= "</ul>";
            $html .= "</div>";


            return $html;
        } else {
            return '';
        }
    }
    
}
