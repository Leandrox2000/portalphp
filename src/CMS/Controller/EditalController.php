<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\Edital as EditalService;
use CMS\Service\ServiceRepository\EditalCategoria as EditalCategoriaService;
use CMS\Service\ServiceRepository\EditalStatus as EditalStatusService;
use Entity\Edital as EditalEntity;
use Entity\EditalCategoria as EditalCategoriaEntity;
use Entity\EditalStatus as EditalStatusEntity;
use Entity\Type;

/**
 * Description of EditalController
 *
 * @author Luciano
 */
class EditalController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Editais";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var EditalService
     */
    private $service;

    /**
     *
     * @var EditalCategoriaService
     */
    private $serviceCategoria;

    /**
     *
     * @var EditalStatusService
     */
    private $serviceStatus;

    /**
     *
     * @var EditalEntity
     */
    private $entity;

    /**
     *
     * @var EditalCategoriaEntity
     */
    private $entityCategoria;

    /**
     *
     * @var EditalStatusEntity
     */
    private $entityStatus;

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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return \CMS\Service\ServiceRepository\Edital
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new EditalService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return \CMS\Service\ServiceRepository\EditalCategoria
     */
    public function getServiceCategoria()
    {
        if (empty($this->serviceCategoria)) {
            $this->setServiceCategoria(new EditalCategoriaService($this->getEm(), $this->getEntityCategoria(), $this->getSession()));
        }
        return $this->serviceCategoria;
    }

    /**
     *
     * @return \CMS\Service\ServiceRepository\EditalStatus
     */
    public function getServiceStatus()
    {
        if (empty($this->serviceStatus)) {
            $this->setServiceStatus(new EditalStatusService($this->getEm(), $this->getEntityStatus(), $this->getSession()));
        }
        return $this->serviceStatus;
    }

    /**
     *
     * @return \Entity\Edital
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new EditalEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @return \Entity\EditalCategoria
     */
    public function getEntityCategoria()
    {
        if (empty($this->entityCategoria)) {
            $this->setEntityCategoria(new EditalCategoriaEntity());
        }
        return $this->entityCategoria;
    }

    /**
     *
     * @return \Entity\EditalStatus
     */
    public function getEntityStatus()
    {
        if (empty($this->entityStatus)) {
            $this->setEntityStatus(new EditalStatusEntity());
        }
        return $this->entityStatus;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Edital $service
     */
    public function setService(EditalService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\EditalCategoria $serviceCategoria
     */
    public function setServiceCategoria(EditalCategoriaService $serviceCategoria)
    {
        $this->serviceCategoria = $serviceCategoria;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\EditalStatus $serviceStatus
     */
    public function setServiceStatus(EditalStatusService $serviceStatus)
    {
        $this->serviceStatus = $serviceStatus;
    }

    /**
     *
     * @param \Entity\Edital $entity
     */
    public function setEntity(EditalEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @param \Entity\EditalCategoria $entityCategoria
     */
    public function setEntityCategoria(EditalCategoriaEntity $entityCategoria)
    {
        $this->entityCategoria = $entityCategoria;
    }

    /**
     *
     * @param \Entity\EditalStatus $entityStatus
     */
    public function setEntityStatus(EditalStatusEntity $entityStatus)
    {
        $this->entityStatus = $entityStatus;
    }

    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        $categorias = $this->getEm()
            ->getRepository($this->getServiceCategoria()->getNameEntity())
            ->getCategorias();

        $status = $this->getEm()
            ->getRepository($this->getServiceStatus()->getNameEntity())
            ->getStatus();

        #Jeito Antigo
//        $sites = $this->getEm()
//                ->getRepository("Entity\Site")
//                ->findBy(array(), array('nome' => 'ASC'));
        $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);

        $edital = $this->getEm()
            ->getRepository($this->getService()->getNameEntity())
            ->find($id);

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        $repository = $this->getEm()->getRepository('Entity\Edital');

        $permissao = $repository->validaVinculo($id, 'edital');

        $this->getTpl()->addJS("/edital/categorias.js");
        $this->getTpl()->addJS("/edital/status.js");
        $this->getTpl()->renderView(
            array(
                "data" => new \DateTime("now"),
                "hora" => new \DateTime("now"),
                "categorias" => $categorias,
                "status" => $status,
                "edital" => $edital,
                "method" => "POST",
                "sites" => $sites,
                "titlePage" => $this->getTitle(),
                "compartilhado" => $permissao,
                "permissaoCadastros" => $repository->verificaCadastros()
            )
        );

        return $this->getTpl()->output();
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function adminCategorias()
    {
        $this->setTitle('Categoria editais');
        $this->tpl->addJS('/edital/categorias.js');
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
     * @return type
     */
    public function lista()
    {
        $status = array();
        $status[] = new Type("0", "Não publicado");
        $status[] = new Type("1", "Publicado");
        $status[] = new Type("2", "Compartilhado");

        $categorias = $this->getEm()->getRepository($this->getServiceCategoria()->getNameEntity())->getCategorias();
        $statusEdital = $this->getEm()->getRepository($this->getServiceStatus()->getNameEntity())->getStatus();

//busca os sites
//        if($this->user['sede']){
//            $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
//        } else {
//           $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
//        }
        $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));


        $this->tpl->renderView(
            array(
                'titlePage' => $this->getTitle(),
                'status' => $status,
                'categorias' => $categorias,
                'statusEdital' => $statusEdital,
                'sites' => $sites,
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

        $repEditais = $this->getEm()
            ->getRepository($this->getService()->getNameEntity());
#jeito antigo
//        $editais = $repEditais->getEditais(
//            $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array(
//                'categoria' => $param->getInt("categoria"),
//                'status' => $param->get("status"),
//                'editalStatus' => $param->getInt("statusEdital"),
//                'site' => $param->getInt("site"),
//                "busca" => $param->get("sSearch"),
//                "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get("data_inicial")),
//                "data_final" => $this->getDatetimeFomat()->formatUs($param->get("data_final")),
//            ), $this->getSession()
//        );


        //INSER��O
        $filtros = array(
            'categoria' => $param->getInt("categoria"),
            'status' => $param->get("status"),
            'editalStatus' => $param->getInt("statusEdital"),
            'site' => $param->getInt("site"),
            "busca" => $param->get("sSearch"),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get("data_inicial")),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get("data_final")),
        );


        //Faz a busca e armazena o total de registros
        if(!$param->get('site') || in_array($param->get('site'), $_SESSION['user']['subsites'])){
            $editais = $repEditais->buscaRegistroByLogin('Entity\Edital', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
            $totalFiltro = $repEditais->buscaRegistroByLogin('Entity\Edital', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), true);
            
        }else{
            $editais = $repEditais->getDataBySubsite('Entity\Edital', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'));
            $totalFiltro = $repEditais->buscaRegistroByLogin('Entity\Edital', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), true);
            
        }
        
        $totalFiltro = $totalFiltro[0]['total'];

        #INSER��O
        //$totalFiltro = $repEditais->getTotalBusca('Entity\Edital', $filtros, $this->getSession()->get('user'));

        foreach ($editais as $edital) {
            $linha = array();
            $pais = array();

            $linha[] = $this->getFields()->checkbox("sel[]", $edital->getId());

            #INSER��O NOVA
            $sitesPai = $this->getEm()->find("Entity\Edital", $edital->getId())->getPaiSites();
            if($sitesPai){
                foreach ($sitesPai as $pai) {
                    $pais[] = $pai->getSigla();
                }
            }
            $siglas = implode(", ", $pais);

            if ($this->verifyPermission('EDITA_ALTERAR')) {
                #Jeito Antigo
//                $linha[] = $tag->link(
//                                $tag->h4($edital->getLabel()), array("href" => "edital/form/" . $edital->getId())
//                        ) . $edital->getCategoria()->getNome();

                $linha[] = $tag->link($tag->h4($edital->getLabel()), array("href" => "edital/form/" . $edital->getId())) . 'Criado por ' . $edital->getLogin() . ' - '.$siglas.' - ' . ' em ' . $edital->getDataCadastro()->format('d/m/Y') . " as " . $edital->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($edital->getLabel()) . $edital->getDataCadastro()->format('d/m/Y') . " as " . $edital->getDataCadastro()->format('H:i');
            }

            #INSER��O
            if ($repEditais->getCompartilhadosById($edital->getId()) == 1) {
                $linha[] = "<span class='compartilhado'>Compartilhado de $siglas</span>";
            } else {
                $linha[] = $edital->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
            }

            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $totalFiltro;
        $retorno['iTotalRecords'] = $repEditais->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @return type
     */
    public function salvar()
    {
        $param = $this->getParam();
        $id = $this->getParam()->getInt("id");
        $t = new \DateTime();

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'nome' => $this->getParam()->get("nome"),
            'login' => $this->user['dadosUser']['login'],
            'conteudo' => $this->getParam()->getString("conteudo"),
            'arquivo' => $this->getParam()->getString("arquivo"),
            'categoria' => $this->getEm()->getReference($this->getServiceCategoria()->getNameEntity(), $this->getParam()->get("categoria")),
            'status' => $this->getEm()->getReference($this->getServiceStatus()->getNameEntity(), $this->getParam()->get("status")),
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
            'sites' => $this->getParam()->get('sites')
        );

        $dataFinal = $this->getParam()->get('dataFinal');
        $horaFinal = $this->getParam()->get('horaFinal');

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
     * Retorna as categorias em um formato JSON
     *
     * @return JSON
     */
    public function getCategorias()
    {
        $categorias = $this->getEm()
            ->getRepository($this->getServiceCategoria()->getNameEntity())
            ->getCategorias();
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
     * Retorna a tabela de Categorias
     *
     * @return \Html\Table
     */
    public function getTableCategorias()
    {
        $categorias = $this->getEm()
            ->getRepository($this->getServiceCategoria()->getNameEntity())
            ->getCategorias();

        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()
                ->setId("tableCategorias")
                ->setNumColumns(2)
                ->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");

        } else {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(1)->addColumnHeader("Nome", "100%");

        }

        foreach ($categorias as $categoria) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                    $this->getTag()->link(
//                        $categoria->getLabel(), array('href' => "#", 'data-id' => "categoria{$categoria->getId()}", 'data-column' => $categoria->getColumn())
                        $categoria->getLabel(), array('href' => "#", 'data-id' => "categoria{$categoria->getId()}")
                    )
                );
            } else {
                $table->addData($categoria->getLabel());
            }


            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData($this->getButton()->icon("trash", "javascript:excluirCategoria({$categoria->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Salva uma Categoria
     *
     * @return JSON
     */
    public function salvarCategoria()
    {
        $id = $this->getParam()->getInt("id");
        $nome = $this->getParam()->get("nome");

        $result = $this->getServiceCategoria()
            ->save($nome, $id);

        return json_encode($result);
    }

    /**
     * Exclui um Categoria
     *
     * @return JSON
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
     * @return \Template\TemplateAmanda
     */
    public function categorias()
    {
        return $this->getTpl()->renderView(array("categorias" => $this->getTableCategorias()));
    }

    /**
     * Retorna os Status em um formato JSON
     *
     * @return JSON
     */
    public function getStatus()
    {
        $statusEdital = $this->getEm()
            ->getRepository($this->getServiceStatus()->getNameEntity())
            ->getStatus();
        $dados = array();

        foreach ($statusEdital as $status) {
            $linha = array();

            $linha['id'] = $status->getId();
            $linha['label'] = $status->getLabel();

            $dados[] = $linha;
        }

        return json_encode($dados);
    }

    /**
     * Retorna a tabela de Status
     *
     * @return \Html\Table
     */
    public function getTableStatus() {

        $repository = $this->getEm()->getRepository($this->getServiceStatus()->getNameEntity());

        $statusEdital = $repository->findBy(array(), array('nome' => 'ASC'));
        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()
                ->setId("tableStatus")
                ->setNumColumns(3)
                ->addColumnHeader("Nome", "60%")
                ->addColumnHeader("Ordenação", "30%")
                ->addColumnHeader("Ação", "10%");
        }
        else {
            $table = $this->getTable()
                ->setId("tableStatus")
                ->setNumColumns(2)
                ->addColumnHeader("Nome", "65%")
                ->addColumnHeader("Ordenação", "35%");
        }

        foreach ($statusEdital as $status) {

            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                    $this->getTag()->link(
                        $status->getLabel(), array('href' => "#", 'data-id' => $status->getId(), 'data-column' => $status->getColumn())
                    )
                );
            } else {
                $table->addData($status->getLabel());
            }

            $table->addData($repository->getColumnLabel($status->getColumn()));

            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData($this->getButton()->icon("trash", "javascript:excluirStatus({$status->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Salva um Status
     *
     * @return JSON
     */
    public function salvarStatus()
    {
        $id = $this->getParam()->getInt("id");
        $nome = $this->getParam()->get("nome");
        $column = $this->getParam()->get("column");

        if(empty($column)) $column = 1;

        $result = $this->getServiceStatus()
            ->save($nome, $id, $column);

        return json_encode($result);
    }

    /**
     * Exclui um Status
     *
     * @return JSON
     */
    public function excluiStatus()
    {
        $id = $this->getParam()->getInt("id");
        $result = $this->getServiceStatus()->delete($id);
        return json_encode($result);
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function status()
    {
        return $this->getTpl()->renderView(array("status" => $this->getTableStatus()));
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function adminStatus()
    {
        $this->tpl->addJS('/edital/status.js');

        $this->getTpl()->renderView(
            array(
                'titlePage' => 'Status editais',
                'status' => $this->getTableStatus(),
                'columns' => $this->getEm()->getRepository($this->getServiceStatus()->getNameEntity())->getColumnLabel()
            )
        );

        return $this->tpl->output();
    }

    /**
     *
     * @return JSON
     */
    public function ajaxAtualizarOrdenacao()
    {
        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach($paramOrdenation as $item){
            $newOrdenation[$item['id']] = (int)$item['ordenacao'];
        }

        $status = $this->getEm()
            ->getRepository($this->getServiceStatus()->getNameEntity())
            ->setOrdem($newOrdenation);

        return json_encode(array(
            'resultado' => 'ok'
        ));
    }

    public function validaSubsiteVinculadoEdital()
    {
        $repository = $this->getEm()->getRepository('Entity\Edital');

        $retorno = $repository->validaVinculo($_REQUEST['id'], 'edital');

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
                    $connection->query("DELETE FROM tb_edital_site WHERE id_edital = {$_REQUEST['id']} AND id_site = {$site}");
                }

                foreach ($sites as $site) {
                    $statment = $connection->prepare("INSERT INTO tb_edital_site (id_edital, id_site) VALUES({$_REQUEST['id']}, $site)");

                    $statment->execute();
                }

                $response = 1;
                $success = "Registro compartilhado com sucesso";
            } else {
                $response = 1;
                $success = "Registro descompartilhado com sucesso";
                $this->getService()->deletaCompartilhadosById($_REQUEST['id'], 'edital');
            }

        } catch(\Exception $exc) {
            $error[] = "Não foi possível executar esta ação";
        }

        return json_encode(array('error' => $error, 'response' => $response, 'success' => $success));
    }
}
