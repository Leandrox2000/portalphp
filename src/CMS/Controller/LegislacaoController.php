<?php

namespace CMS\Controller;

use Entity\Legislacao as LegislacaoEntity;
use Entity\CategoriaLegislacao as CategoriaEntity;
use Entity\Type;
use CMS\Service\ServiceRepository\Legislacao as LegislacaoService;
use CMS\Service\ServiceRepository\CategoriaLegislacao as CategoriaLegislacaoService;
use Helpers\Param;
use LibraryController\CrudControllerInterface;

/**
 * LegislacaoController
 *
 * @author join-ti
 */
class LegislacaoController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Legislação";
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
     * @var LegislacaoEntity
     */
    protected $entity;

    /**
     *
     * @var CategoriaEntity
     */
    protected $categoriaEntity;

    /**
     *
     * @var LegislacaoService
     */
    protected $service;

    /**
     *
     * @var CategoriaLegislacaoService
     */
    protected $serviceCategoria;

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
     * @return LegislacaoEntity
     */
    public function getEntity()
    {
        if (!isset($this->entity)) {
            $this->entity = new LegislacaoEntity();
        }
        return $this->entity;
    }

    /**
     *
     * @param LegislacaoEntity $entity
     */
    public function setEntity(LegislacaoEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return LegislacaoService
     */
    public function getService()
    {
        if (!isset($this->service)) {
            $this->service = new LegislacaoService($this->getEm(), $this->getEntity(), $this->getSession());
        }
        return $this->service;
    }

    /**
     *
     * @param LegislacaoService $service
     */
    public function setService(LegislacaoService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @return CategoriaLegislacaoService
     */
    public function getServiceCategoria()
    {
        if (!isset($this->serviceCategoria)) {
            $this->serviceCategoria = new CategoriaLegislacaoService($this->getEm(), $this->getCategoriaEntity(), $this->getSession());
        }
        return $this->serviceCategoria;
    }

    /**
     *
     * @param CategoriaLegislacaoService $serviceCategoria
     */
    public function setServiceCategoria(CategoriaLegislacaoService $serviceCategoria)
    {
        $this->serviceCategoria = $serviceCategoria;
    }

    /**
     *
     * @return categoriaEntity
     */
    public function getCategoriaEntity()
    {
        if (!isset($this->categoriaEntity)) {
            $this->categoriaEntity = new CategoriaEntity();
        }
        return $this->categoriaEntity;
    }

    /**
     *
     * @param \Entity\CategoriaLegislacao $categoriaEntity
     */
    public function setCategoriaEntity(CategoriaEntity $categoriaEntity)
    {
        $this->categoriaEntity = $categoriaEntity;
    }

    /**
     *
     * @return string
     */
    public function lista()
    {
        //Cria os objetos status
#Jeito Antigo
//        $naoPublicado = new Type("0", "Não publicado");
//        $publicado = new Type("1", "Publicado");

        $status = array();
        $status[] = new Type("0", "Não publicado");
        $status[] = new Type("1", "Publicado");
        $status[] = new Type("2", "Compartilhado");

#Jeito Antigo
//        $sites = $this->getEm()
//                      ->getRepository('Entity\Site')
//                      ->getQueryIndex();

        $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));

        //Busca os anos
        $this->tpl->renderView(array(
            'status' => $status,
            'titlePage' => $this->getTitle(),
            'subTitlePage' => "",
            'sites' => $sites,
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
        //Busca dados da legislação
        $legislacao = $this->getEm()->getRepository("Entity\Legislacao")->find($id);

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        //Adiciona o js de categorias de legislação
        $this->tpl->addJS("/legislacao/categorias.js");

        $repository = $this->getEm()->getRepository('Entity\Legislacao');

        $permissao = $repository->validaVinculo($id, 'legislacao');

        #REGRA DE PERMISS�O
        //$sites = $this->user['sede'] ? $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC')) : $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);

        $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);

        $this->tpl->renderView(array(
                "data" => new \DateTime('now'),
                "legislacao" => $legislacao,
                "sites" => $sites,
                "categorias" => $this->getEm()->getRepository("Entity\CategoriaLegislacao")->findBy(array(), array('nome' => 'ASC')),
                "titlePage" => $this->getTitle(),
                "method" => "POST",
                "compartilhado" => $permissao
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
        $this->setTitle('Categoria Legislação');
        $this->tpl->addJS('/legislacao/categorias.js');
        $this->getTpl()->renderView(
            array(
                'titlePage' => $this->getTitle(),
                'categorias' => $this->getTableCategorias()
            )
        );

        return $this->tpl->output();
    }

    /**
     *
     * @return string
     */
    public function salvar()
    {
        //Armazena o parâmetro
        $param = $this->getParam();

        $dados = array(
            'id' => $param->getInt('id'),
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('data_inicial')) . " " . $param->get('hora_inicial')),
            'titulo' => $param->get('titulo'),
            'login' => $this->user['dadosUser']['login'],
            'dataLegislacao' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('data_legislacao')) . " 12:00"),
            'url' => $param->getString('url'),
            'descricao' => $param->get('descricao'),
            'categoriaLegislacao' => $this->getEm()->getReference('Entity\CategoriaLegislacao', $param->get('categoria')),
            'arquivo' => $param->getString('arquivoNome'),
            'arquivoExcluido' => $param->getString('arquivoExcluido'),
            'arquivoAtual' => $param->getString('arquivoAtual'),
            'sites' => $this->getParam()->get('sites')
        );

        $dataFinal = $param->get('data_final');
        $horaFinal = $param->get('hora_final');

        // se a dataFinal não estiver setada ou receber o valor vazio
        // a variável deve ser setada como NULA
        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal) . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }

        $sites = $param->get('sites');

        if(!$param->getInt('id') and !in_array(1, $sites))
        {
            $sites[] = '1';
            $param->setRequestValue('sites', $sites);
        }

        return json_encode($this->getService()->save($dados));
    }

    /**
     *
     * @return String
     */
    public function pagination()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoLegislacao = $em->getRepository("Entity\Legislacao");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repoLegislacao->countAll($this->getSession()->get('user'));

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status'),
            "site" => $param->get('site')
        );


        //Faz a busca e armazena o total de registros
        if(!$param->get('site') || in_array($param->get('site'), $_SESSION['user']['subsites'])){
            $legislacoes = $repoLegislacao->buscaRegistroByLogin('Entity\Legislacao', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
            $totalFiltro = $repoLegislacao->buscaRegistroByLogin('Entity\Legislacao', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), true);
            
        }else{
            $legislacoes = $repoLegislacao->getDataBySubsite('Entity\Legislacao', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'));
            $totalFiltro = $repoLegislacao->getDataBySubsite('Entity\Legislacao', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'), true);
            
        }

        #JEITO ANTIGO
        //Faz a busca e armazena o total de registros
        //$legislacoes = $repoLegislacao->getBuscaLegislacao($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
        //$totalFiltro = $repoLegislacao->getTotalBuscaLegislacao($filtros, $this->getSession()->get('user'));

        $totalFiltro = $totalFiltro[0]['total'];
        
        #INSER��O
        //$totalFiltro = $repoLegislacao->getTotalBusca('Entity\Legislacao', $filtros, $this->getSession()->get('user'));
        
        

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($legislacoes as $legislacao) {
            $linha = array();
            $pais = array();

            $linha[] = $this->getFields()->checkbox("legislacao[]", $legislacao->getId());

            #INSER��O NOVA
            $sitesPai = $this->getEm()->find("Entity\Legislacao", $legislacao->getId())->getPaiSites();
            if($sitesPai){
                foreach ($sitesPai as $pai) {
                    $pais[] = $pai->getSigla();
                }
            }
            $siglas = implode(", ", $pais);


            if ($this->verifyPermission('LEGIS_ALTERAR')) {
                #JEITO ANTIGO
                //$linha[] = $tag->link($tag->h4($legislacao->getLabel()), array("href" => "legislacao/form/" . $legislacao->getId())) . $legislacao->getDataCadastro()->format('d/m/Y') . " as " . $legislacao->getDataCadastro()->format('H:i');
                $linha[] = $tag->link($tag->h4($legislacao->getLabel()), array("href" => "legislacao/form/" . $legislacao->getId())) . 'Criado por ' . $legislacao->getLogin() . ' - '.$siglas.' - ' . ' em ' . $legislacao->getDataCadastro()->format('d/m/Y') . " as " . $legislacao->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($legislacao->getLabel()) . $legislacao->getDataCadastro()->format('d/m/Y') . " as " . $legislacao->getDataCadastro()->format('H:i');
            }

            #INSER��O
            if ($repoLegislacao->getCompartilhadosById($legislacao->getId()) == 1) {
                $linha[] = "<span class='compartilhado'>Compartilhado de $siglas</span>";
            } else {
                $linha[] = $legislacao->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
            }

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
     *
     * @return \Html\Table
     */
    public function getTableCategorias()
    {
        //Instancia os elementos html
        $button = $this->getButton();
        $tag = $this->getTag();

        //Busca todas as categorias de legislação
        //$categorias = $this->getEm()->getRepository("Entity\CategoriaLegislacao")->findAll();
        $categorias = $this->getEm()->getRepository("Entity\CategoriaLegislacao")->getBuscaCategoriaLegislacao();

        //Monta a table
        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }


        foreach ($categorias as $categoria) {
            if ($this->verifyPermission('CADAS_EXCLUIR')) {
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
     *
     * @return JSON
     */
    public function salvarCategoria()
    {
        //Instancia o parâmetro
        $param = $this->getParam();

        //Organiza os dados
        $dados = array(
            'id' => $param->getInt('id'),
            'nome' => $param->get('nome'),
        );

        //Instancia o service e realiza o salvamento
        $service = $this->getServiceCategoria();
        $result = $service->save($dados);
        return json_encode($result);
    }

    /**
     *
     * @return type
     */
    public function excluiCategoria()
    {
        $id = $this->getParam()->getInt("id");

        $result = $this->getServiceCategoria()
            ->delete($id);

        return json_encode($result);
    }

    /**
     *
     * @return JSON
     */
    public function getCategorias()
    {
        $categorias = $this->getEm()->getRepository("Entity\CategoriaLegislacao")->findBy(array(),array("nome" => "ASC"));
        $dados = array();

        foreach ($categorias as $categoria) {
            $linha = array();

            $linha['id'] = $categoria->getId();
            $linha['label'] = $categoria->getLabel();

            $dados[] = $linha;
        }

        return json_encode($dados);
    }

    public function validaSubsiteVinculadoLegislacao()
    {
        $repository = $this->getEm()->getRepository('Entity\Legislacao');

        $retorno = $repository->validaVinculo($_REQUEST['id'], 'legislacao');

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
                    $connection->query("DELETE FROM tb_legislacao_site WHERE id_legislacao = {$_REQUEST['id']} AND id_site = {$site}");
                }

                foreach ($sites as $site) {
                    $statment = $connection->prepare("INSERT INTO tb_legislacao_site (id_legislacao, id_site) VALUES({$_REQUEST['id']}, $site)");

                    $statment->execute();
                }
                $response = 1;
                $success = "Registro compartilhado com sucesso";
            } else {
                $response = 1;
                $success = "Registro descompartilhado com sucesso";
                $this->getService()->deletaCompartilhadosById($_REQUEST['id'], 'legislacao');
            }

        } catch(\Exception $exc) {
            $error[] = "Não foi possível executar esta ação";
        }

        return json_encode(array('error' => $error, 'response' => $response, 'success' => $success));
    }

}
