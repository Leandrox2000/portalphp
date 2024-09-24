<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\DicionarioPatrimonioCultural as DicionarioService;
use CMS\Service\ServiceRepository\CategoriaDicionario as CategoriaDicionarioService;
use Entity\DicionarioPatrimonioCultural as DicionarioPatrimonioCulturalEntity;
use Entity\CategoriaDicionario as CategoriaDicionarioEntity;
use Entity\Type;


/**
 * Description of DicionarioPatrimonioCulturalController
 */
class DicionarioPatrimonioCulturalController extends CrudController implements CrudControllerInterface
{

    //const PAGE_TITLE = 'Dicionário do Patrimônio Cultural';
    const PAGE_TITLE = 'Dicionário Iphan de Patrimônio Cultural';
    const DEFAULT_ACTION = 'lista';

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * Rota para o controller.
     */
    protected $controllerRoute = 'dicionarioPatrimonioCultural';

    /**
     * Nome da entidade
     */
    protected $entityNamespace = 'Entity\DicionarioPatrimonioCultural';

    /**
     * Name do checkbox utilizado para seleção multipla.
     */
    protected $selectionCheckboxName = 'dicionario';

    /**
     *
     * @var DicionarioPatrimonioCulturalService
     */
    private $service;

    /**
     *
     * @var DicionarioPatrimonioCulturalEntity
     */
    private $entity;

    /**
     *
     * @var CategoriaDicionarioEntity
     */
    private $entityCategoria;

    /**
     *
     * @var CategoriaDicionarioService
     */
    private $serviceCategoria;

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
     * @return \DicionarioService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new DicionarioService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return \DicionarioService
     */
    public function getServiceCategoria()
    {
        if (empty($this->serviceCategoria)) {
            $this->setServiceCategoria(new CategoriaDicionarioService($this->getEm(), $this->getEntityCategoria(), $this->getSession()));
        }
        return $this->serviceCategoria;
    }

    /**
     *
     * @return \Entity\DicionarioPatrimonioCultural
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new DicionarioPatrimonioCulturalEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @return CategoriaDicionarioEntity
     */
    public function getEntityCategoria()
    {
        if (empty($this->entityCategoria)) {
            $this->setEntityCategoria(new CategoriaDicionarioEntity());
        }
        return $this->entityCategoria;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\DicionarioService $service
     */
    public function setService(DicionarioService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\CategoriaDicionario $serviceCategoria
     */
    public function setServiceCategoria(CategoriaDicionarioService $serviceCategoria)
    {
        $this->serviceCategoria = $serviceCategoria;
    }

    /**
     *
     * @param DicionarioPatrimonioCulturalEntity $entity
     */
    public function setEntity(DicionarioPatrimonioCulturalEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @param \Entity\CategoriaDicionario $entityCategoria
     */
    public function setEntityCategoria(CategoriaDicionarioEntity $entityCategoria)
    {
        $this->entityCategoria = $entityCategoria;
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
        $entity = $this->getEm()
                ->getRepository($this->getService()->getNameEntity())
                ->find($id);

        // Verifica o Id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . ' - Inserir');
        } else {
            $this->setTitle(self::PAGE_TITLE . ' - Alterar');
        }

        // Inclui o js de categorias
        $this->tpl->addJS("/dicionarioPatrimonioCultural/categorias.js");

        $this->getTpl()->renderView(
                array(
                    'data' => new \DateTime('now'),
                    'entity' => $entity,
                    'method' => 'POST',
                    'titlePage' => $this->getTitle(),
                    'categorias' => $this->getEm()->getRepository("Entity\CategoriaDicionario")->findBy(array(), array('nome' => 'ASC')),
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
        $status = array();
        $status[] = new Type('0', 'Não publicado');
        $status[] = new Type('1', 'Publicado');



        $this->tpl->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'status' => $status,
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
        $repository = $this->getEm()->getRepository($this->entityNamespace);
        $filtros = array(
            'status' => $param->get('status'),
            'busca' => $param->get('sSearch'),
            'data_inicial' => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            'data_final' => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
        );
        $registros = $repository->getBusca($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros);

        foreach ($registros as $registro) {
            $linha = array();

            $linha[] = $this->getFields()->checkbox("{$this->selectionCheckboxName}[]", $registro->getId());

            if ($this->verifyPermission('DICIO_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($registro->getLabel()), array('href' => "{$this->controllerRoute}/form/" . $registro->getId())) . $registro->getDataCadastro()->format('d/m/Y') . ' as ' . $registro->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->link($tag->h4($registro->getLabel()), array('href' => "{$this->controllerRoute}/form/" . $registro->getId())) . $registro->getDataCadastro()->format('d/m/Y') . ' as ' . $registro->getDataCadastro()->format('H:i');
            }

            $linha[] = $registro->getPublicado() ? $tag->span('Publicado', array('class' => 'publicado')) : $tag->span('Não publicado', array('class' => 'naoPublicado'));

            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt('sEcho');
        $retorno['iTotalDisplayRecords'] = $repository->getTotal($filtros);
        $retorno['iTotalRecords'] = $repository->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    protected function getDados()
    {
        $dataInicial = $this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial'));
        $horaInicial = $this->getParam()->get('horaInicial');
        $dataFinal = $this->getParam()->get('dataFinal');
        $horaFinal = $this->getParam()->get('horaFinal');
        $categoriaId = $this->getParam()->getInt('categoria');

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'titulo' => $this->getParam()->get('titulo'),
            'verbete' => $this->getParam()->getString('verbete'),
            'descricao' => $this->getParam()->getString('descricao'),
            'colaborador' => $this->getParam()->getString('colaborador'),
            'funcao' => $this->getParam()->getString('funcao'),
            'link' => $this->getParam()->getString('link'),
            'fichaTecnica' => $this->getParam()->getString('fichaTecnica'),
            'dataInicial' => new \DateTime($dataInicial . " " . $horaInicial),
            'categoria' => $this->getEm()->getReference('\Entity\CategoriaDicionario', $categoriaId),
        );

        // se a dataFinal não estiver setada ou receber o valor vazio
        // a variável deve ser setada como NULA
        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal) . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }

        return $dados;
    }

    /**
     *
     * @return JSON
     */
    public function salvar()
    {
        $dados = $this->getDados();

        return json_encode($this->getService()->save($dados));
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
        $categorias = $this->getEm()->getRepository("Entity\CategoriaDicionario")->findBy(array(), array('nome' => 'ASC'));

        //Monta a table
        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }


        foreach ($categorias as $categoria) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                        $tag::link($categoria->getNome(), array('href' => "javascript:editaCategoria({$categoria->getId()})", 'id' => "categoria{$categoria->getId()}")
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
        $categorias = $this->getEm()->getRepository("Entity\CategoriaDicionario")->findBy(array(), array('nome' => 'ASC'));
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
     * @return \Template\TemplateAmanda
     */
    public function adminCategorias()
    {
        $this->setTitle('Categoria Verbetes');
        $this->tpl->addJS('/dicionarioPatrimonioCultural/categorias.js');
        $this->getTpl()->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'categorias' => $this->getTableCategorias()
                )
        );

        return $this->tpl->output();
    }

}
