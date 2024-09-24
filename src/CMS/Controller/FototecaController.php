<?php

namespace CMS\Controller;


use Entity\Fototeca as FototecaEntity;
use Entity\CategoriaFototeca as CategoriaFototecaEntity;
use Entity\Type;
use CMS\Service\ServiceRepository\Fototeca as FototecaService;
use CMS\Service\ServiceRepository\CategoriaFototeca as CategoriaFototecaService;
use Helpers\Param;
use LibraryController\CrudControllerInterface;

/**
 * FototecaController
 *
 * @author join-ti
 */
class FototecaController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Fototecas";
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
     * @var FototecaEntity 
     */
    protected $entity;

    /**
     *
     * @var FototecaService 
     */
    protected $service;

    /**
     *
     * @var CategoriaFototecaService
     */
    protected $categoriaFototecaService;

    /**
     *
     * @var CategoriaFototecaEntity 
     */
    protected $categoriaFototecaEntity;

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
     * @return FototecaEntity
     */
    public function getEntity()
    {
        if (!isset($this->entity)) {
            $this->entity = new FototecaEntity();
        }
        return $this->entity;
    }

    /**
     * 
     * @param FototecaEntity $entity
     */
    public function setEntity(FototecaEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * 
     * @return FototecaService
     */
    public function getService()
    {
        if (!isset($this->service)) {
            $this->service = new FototecaService($this->getEm(), $this->getEntity(), $this->getSession());
        }
        return $this->service;
    }

    /**
     * 
     * @param FototecaService $service
     */
    public function setService(FototecaService $service)
    {
        $this->service = $service;
    }

    /**
     * 
     * @return CategoriaFototecaService
     */
    public function getCategoriaFototecaService()
    {
        if (!isset($this->categoriaFototecaService)) {
            $this->categoriaFototecaService = new CategoriaFototecaService($this->getEm(), $this->getCategoriaFototecaEntity(), $this->getSession());
        }
        return $this->categoriaFototecaService;
    }

    /**
     * 
     * @param \CMS\Service\ServiceRepository\CategoriaFototeca $categoriaFototecaService
     */
    public function setCategoriaFototecaService(CategoriaFototecaService $categoriaFototecaService)
    {
        $this->categoriaFototecaService = $categoriaFototecaService;
    }

    /**
     * 
     * @return \Entity\CategoriaFototeca
     */
    public function getCategoriaFototecaEntity()
    {
        if (!isset($this->categoriaFototecaEntity)) {
            $this->categoriaFototecaEntity = new CategoriaFototecaEntity();
        }
        return $this->categoriaFototecaEntity;
    }

    /**
     * 
     * @param \Entity\CategoriaFototeca $categoriaFototecaEntity
     */
    public function setCategoriaFototecaEntity(CategoriaFototecaEntity $categoriaFototecaEntity)
    {
        $this->categoriaFototecaEntity = $categoriaFototecaEntity;
    }

    /**
     * 
     * @return string
     */
    public function lista()
    {
        //Cria os objetos status
        $naoPublicado = new Type("0", "Não publicado");
        $publicado = new Type("1", "Publicado");

        //Busca os anos
        $this->tpl->renderView(array(
            'status' => array($naoPublicado, $publicado),
            'titlePage' => $this->getTitle(),
            'subTitlePage' => "",
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
        $this->getTpl()->addJS('/fototeca/fototecas.js');
        //Busca dados da fototeca
        $fototeca = $this->getEm()->getRepository("Entity\Fototeca")->find($id);

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }


        //Adiciona os js de categoria e galerias
        $this->tpl->addJS("/fototeca/categorias.js");
        $this->tpl->addJS("/galeria/galerias.js");

        //Busca os sites e as categorias de fototeca
        $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
        $categorias = $this->getEm()->getRepository("Entity\CategoriaFototeca")->findBy(array(), array('nome' => 'ASC'));


        //Busca os ids das galeria relacionadas
        $idsGalerias = "";
        $htmlGalerias = "";

        $idsFototecas = '';
        
        if ($fototeca) {
        	
            $arrayIdsGalerias = array();
        	$arrayIdsGalerias = $this->getEm()->getRepository("Entity\Galeria")->getGaleriasIdsFototeca($id);
            
            foreach ($arrayIdsGalerias as $idg) {
                $idsGalerias[] = $idg['idGaleria'];
            }
			$htmlGalerias = $this->getHtmlGaleriasIds($idsGalerias);
        	$idsGalerias = implode(',', $idsGalerias);
           
            $idsFototecas = array();

            foreach ($fototeca->getFototecasFilhas() as $relacionado) {
                $idsFototecas[] = $relacionado->getId();
            }
            $idsFototecas = implode(',', $idsFototecas);
        }

        $this->tpl->renderView(array(
            "data" => new \DateTime('now'),
            "fototeca" => $fototeca,
            "sites" => $sites,
            "categorias" => $categorias,
            "titlePage" => $this->getTitle(),
            "fototecasRelacionadas" => $this->getHtmlVideos($idsFototecas),
            "method" => "POST",
            "idsFototecasRelacionadas" => $idsFototecas,
            "idsGalerias" => $idsGalerias,
            "htmlGalerias" => $htmlGalerias
                )
        );

        return $this->tpl->output();
    }

    /**
     * 
     * @return \Template\TemplateAmanda
     */
    public function adminCategorias()
    {
        $this->setTitle('Categoria fototeca');
        $this->tpl->addJS('/fototeca/categorias.js');
        $render = array(
            'titlePage' => $this->getTitle(),
            'categorias' => $this->getTableCategorias()
        );
        
        $this->getTpl()->renderView($render);


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
            'nome' => $this->getParam()->get('nome'),
            'descricao' => $this->getParam()->getString("descricao"),
            'galerias' => $this->getParam()->getString("galeriaBanco"),
            'fototecasFilhas' => $this->getParam()->getString('fototecasBanco'),
            'categoria' => $this->getEm()->getReference('Entity\CategoriaFototeca', $this->getParam()->getInt('categoria'))
        );

        $ordem = $this->getParam()->get('galeriaOrdem');
        
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
     *
     * Resposável por retornar os registros paginados
     * @return String
     */
    public function pagination()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoFototeca = $em->getRepository("Entity\Fototeca");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repoFototeca->countAll($this->getSession()->get('user'));

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status'),
            "site" => $param->get('site')
        );

        //Faz a busca e armazena o total de registros
        $fototecas = $repoFototeca->getBuscaFototeca($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
        $totalFiltro = $repoFototeca->getTotalBuscaFototeca($filtros, $this->getSession()->get('user'));

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($fototecas as $fototeca) {
            $linha = array();

            $linha[] = $this->getFields()->checkbox("fototeca[]", $fototeca->getId());

            if ($this->verifyPermission('FOTOT_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($fototeca->getLabel()), array("href" => "fototeca/form/" . $fototeca->getId())) . $fototeca->getDataCadastro()->format('d/m/Y') . " as " . $fototeca->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($fototeca->getLabel()) . $fototeca->getDataCadastro()->format('d/m/Y') . " as " . $fototeca->getDataCadastro()->format('H:i');
            }

            $linha[] = $fototeca->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
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
     * @return \Template\TemplateAmanda
     */
    public function categorias()
    {
        return $this->getTpl()->renderView(array("categorias" => $this->getTableCategorias()));
    }

    /**
     * Salva uma categoria
     * 
     * @return JSON
     */
    public function salvarCategoria()
    {
        $param = $this->getParam();
        $id = $param->getInt("id");
        $nome = $param->get("nome");

        $service = $this->getCategoriaFototecaService();

        $result = $service->save(array('nome' => $nome, 'id' => $id));

        return json_encode($result);
    }

    /**
     * Exclui uma categoria
     * 
     * @return type
     */
    public function excluiCategoria()
    {
        $id = $this->getParam()->getInt("id");
        $result = $this->getCategoriaFototecaService()->delete($id);
        return json_encode($result);
    }

    /**
     * Retorna atabela de Categorias
     * 
     * @return \Html\Table
     */
    public function getTableCategorias()
    {
        $button = $this->getButton();
        $tag = $this->getTag();
        $categorias = $this->getEm()->getRepository("Entity\CategoriaFototeca")->getBuscaCategoriaFototeca();

        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }

        foreach ($categorias as $categoria) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                        $tag::link(
                                $categoria->getNome(), array('href' => "javascript:editaCategoria({$categoria->getId()})", 'id' => "categoria{$categoria->getId()}")
                        )
                );
            } else {
                $table->addData($categoria->getNome());
            }


            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData(
                        $button->icon("trash", "javascript:excluirCategoria({$categoria->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Retorna as categorias de Pergunta em um formato JSON
     * 
     * @return JSON
     */
    public function getCategorias()
    {
        $categorias = $this->getEm()
                ->getRepository("Entity\CategoriaFototeca")
                ->findBy(array(),array('nome' => 'ASC'));
        $dados = array();

        foreach ($categorias as $categoria) {
            $linha = array();

            $linha['id'] = $categoria->getId();
            $linha['label'] = $categoria->getLabel();

            $dados[] = $linha;
        }

        return json_encode($dados);
    }

    /**
     * 
     * @param type $ids
     * @param \Doctrine\Common\Collections\Collection $galerias
     * @return string
     */
    public function getHtmlGalerias($ids, \Doctrine\Common\Collections\Collection $galerias = null)
    {
        //Verifica se os ids foram passados
        if (!empty($ids)) {

            //Verifica se um objeto com as galerias já foi passado
            if (is_null($galerias)) {
                //Busca as galerias
                $galerias = $this->getEm()->getRepository("Entity\Galeria")->getGaleriaIds($ids);
            }
            
            //Cria um array ordenado de acordo com os ids passados por parâmetro
            $arrGalerias = array();
            $arrIds = explode(',', $ids);
            
            foreach($arrIds as $idGaleria){
                $arrGalerias[$idGaleria] = "";
            }
            
            
            foreach($galerias as $gal){
                $arrGalerias[$gal->getId()] = $gal ;
            }
            
            $galerias = $arrGalerias;

            //Monta e retorna o html
            $tag = $this->getTag();
            $html = "<div class='gallerywrapper'>";
                        
			$html .= "<ul class='imagelist galSelecionadas ui-sortable' id='galSelecionadas'>";
            foreach ($galerias as $galeria) {
            	$html .= "<li id='galeria{$galeria->getId()}' name='{$galeria->getId()}'>
					   		<h4>{$galeria->getLabel()}</h4>
							<br>
						    <a href='javascript:excluirGaleria({$galeria->getId()})'>Excluir Galeria</a>
					    </li>";
            }
            $html .= "</ul></div>";

            return $html;
        } else {
            return '';
        }
    }

    /**
     * 
     * @param type $ids
     * @param \Doctrine\Common\Collections\Collection $galerias
     * @return string
     */
    public function getHtmlGaleriasNoticias($ids, \Doctrine\Common\Collections\Collection $galerias = null)
    {
        //Verifica se os ids foram passados
        if (!empty($ids)) {

            //Verifica se um objeto com as galerias já foi passado
            if (is_null($galerias)) {
                //Busca as galerias
                $galerias = $this->getEm()->getRepository("Entity\Galeria")->getGaleriaIds($ids);
            }

            //Monta e retorna o html
            $tag = $this->getTag();
            // $html = "<div class='gallerywrapper'>";
            $html="";
                        
			// $html .= "<ul class='imagelist galSelecionadas' id='galSelecionadas'>";

            $c = count($galerias);

            foreach ($galerias as $galeria) {
                if ($c == 1) {

            	$html .= "<div id='galeria{$galeria->getId()}' data-id='{$galeria->getId()}' class='photo'>
					   		<h4>{$galeria->getLabel()}</h4>
							<br>
                            <label style='display: block; float: left;' ><input type='radio' name='posicao{$galeria->getId()}' value='1' checked /> No início da página  </label>
                            <label style='display: block; float: left;' ><input type='radio' name='posicao{$galeria->getId()}' value='2' /> Ao final da página  </label><br />
						    <a href='javascript:excluirGaleria({$galeria->getId()})' class='btn btn3 btn_trash'></a>
					    </div>";
                } else {
            	$html .= "<div id='galeria{$galeria->getId()}' data-id='{$galeria->getId()}' class='photo'>
					   		<h4>{$galeria->getLabel()}</h4>
							<br>
                            <label style='display: block; float: left;'><input type='radio' name='posicao{$galeria->getId()}' value='1' disabled /> No início da página  </label>
                            <label style='display: block; float: left;'><input type='radio' name='posicao{$galeria->getId()}' value='2' checked /> Ao final da página  </label><br />
						    <a href='javascript:excluirGaleria({$galeria->getId()})' class='btn btn3 btn_trash'></a>
					    </div>";
                }
            }
            // $html .= "</ul></div>";

            return $html;
        } else {
            return '';
        }
    }
    
    public function fototecas()
    {
        return $this->getTpl()->renderView();
    }
    
    public function getHtmlVideos($ids = "")
    {
        if (!empty($ids)) {
            //Organiza os ids em imagens
            $ids = explode(',', $ids);

            //Busca as imagens
            $registros = $this->getEm()->getRepository('Entity\Fototeca')->findById($ids);

            //Cria o html com as imagens
            $html = "";

            $html .= "<div class='gallerywrapper'>";
            $html .= "<ul  class='imagelist'>";

            //Percorre as imagens e monta o HTML
            foreach ($registros as $registro) {
                $html .= "<li id='fototeca-relacionada-{$registro->getId()}' >";
                $html .= "<span>{$registro->getLabel()}</span>";
                $html .= "<span><a class='delete' href='javascript:fototecasModalObject.removerRelacionado({$registro->getId()})'></a></span>";
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
     * @return string JSON
     */
    public function ajaxAtualizarOrdenacao()
    {
        if(!$this->verifyPermission('FOTOT_ALTPOS')){
            die('Acesso negado');
        }

        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach ($paramOrdenation as $item) {
            $newOrdenation[$item['id']] = $item['ordenacao'];
        }
        $repository = $this->getEm()->getRepository('Entity\Fototeca');
        foreach ($newOrdenation as $id => $ordenacao) {
            if (empty($ordenacao)) {
                $ordenacao = NULL;
            }
            $entity = $repository->find($id);
            $entity->setOrdem($ordenacao);
            $this->getEm()->persist($entity);
        }

        $this->getEm()->flush();

        return json_encode(array(
            'resultado' => 'ok',
        ));
    }

    /**
     * 
     * @param type $ids
     * @param \Doctrine\Common\Collections\Collection $galerias
     * @return string
     */
    public function getHtmlGaleriasIds($ids)
    {
        //Verifica se os ids foram passados
        if (!empty($ids)) {

            //Monta e retorna o html
            $tag = $this->getTag();
            $html = "<div class='gallerywrapper'>";
                        
			$html .= "<ul class='imagelist galSelecionadas' id='galSelecionadas'>";
            foreach ($ids as $id) {
            	$galeria = $this->getEm()->getRepository("Entity\Galeria")->find($id);
            	$html .= "<li id='galeria{$galeria->getId()}' name='{$galeria->getId()}'>
					   		<h4>{$galeria->getLabel()}</h4>
							<br>
						    <a href='javascript:excluirGaleria({$galeria->getId()})'>Excluir Galeria</a>
					    </li>";
            }
            $html .= "</ul></div>";

            return $html;
        } else {
            return '';
        }
    }
}
