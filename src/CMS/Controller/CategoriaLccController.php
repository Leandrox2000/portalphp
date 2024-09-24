<?php

namespace CMS\Controller;

use Entity\CategoriaLcc as CategoriaEntity;
use CMS\Service\ServiceRepository\CategoriaLcc as CategoriaService;
use Helpers\Param;


/**
 * Description of CategoriaLccController
 *
 * @author HOLANDA
 */
class CategoriaLccController extends CrudController
{

    const PAGE_TITLE = "Categoria licitações";
    const DEFAULT_ACTION = "categorias";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var CategoriaService
     */
    protected $service;

    /**
     *
     * @var CategoriaEntity
     */
    protected $entity;

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
     * @return String
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
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
     * @return CategoriaService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new CategoriaService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     * 
     * @return CategoriaEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new CategoriaEntity());
        }
        return $this->entity;
    }

    /**
     * 
     * @param CategoriaService $service
     */
    public function setService(CategoriaService $service)
    {
        $this->service = $service;
    }

    /**
     * 
     * @param CategoriaEntity $entity
     */
    public function setEntity(CategoriaEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Retorna as categorias em um formato JSON
     * 
     * @return JSON
     */
    public function getCategorias()
    {
        $categorias = $this->getEm()->getRepository($this->getService()->getNameEntity())->findBy(array(),array("nome" => "ASC"));
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
     * Retorna a tabela de Categoria
     * 
     * @return \Html\Table
     */
    public function getTableCategorias()
    {
        $button = $this->getButton();
        $tag = $this->getTag();
        $categorias = $this->getEm()->getRepository($this->getService()->getNameEntity())->getCategoriasPermitidas();


        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }

        foreach ($categorias as $categoria) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                        $tag::link(
                                $categoria->getLabel(), array('href' => "javascript:editaCategoria({$categoria->getId()})", 'id' => "categoria{$categoria->getId()}")
                        )
                );
            } else {
                $table->addData($categoria->getLabel());
            }

            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData(
                        $button->icon("trash", "javascript:excluirCategoria({$categoria->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Salva uma categoria
     * 
     * @return JSON
     */
    public function salvarCategoria()
    {
        $param = $this->getParam();
        $dados = array(
            "id" => $param->getInt("id"),
            "nome" => $param->get("nome")
        );

        $result = $this->getService()->save($dados);

        return json_encode($result);
    }

    /**
     * Exclui um Cargo
     * 
     * @return JSON
     */
    public function excluiCategoria()
    {
        $id = $this->getParam()->getInt("id");
        $result = $this->getService()->delete($id);
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
     * 
     * @return \Template\TemplateAmanda
     */
    public function adminCategorias()
    {
        $this->setTitle('Categorias de licitação');
        $this->tpl->addJS('/categoriaLcc/categorias.js');

        $this->getTpl()->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'categorias' => $this->getTableCategorias()
                )
        );

        return $this->tpl->output();
    }

}
