<?php

namespace CMS\Controller;

use Entity\TipoLcc as TipoEntity;
use CMS\Service\ServiceRepository\TipoLcc as TipoService;
use Helpers\Param;


/**
 * Description of TipoLccController
 *
 * @author HOLANDA
 */
class TipoLccController extends CrudController
{

    const PAGE_TITLE = "Tipo de Categoria Licitações";
    const DEFAULT_ACTION = "tipos";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var TipoService
     */
    protected $service;

    /**
     *
     * @var TipoEntity
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
     * @return TipoService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new TipoService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return TipoEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new TipoEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param TipoService $service
     */
    public function setService(TipoService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param TipoEntity $entity
     */
    public function setEntity(TipoEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Retorna os tipos em um formato JSON
     *
     * @return JSON
     */
    public function getTipos()
    {
        $tipos = $this->getEm()->getRepository($this->getService()->getNameEntity())->findBy(array(), array('nome' => 'ASC'));
        $dados = array();

        foreach ($tipos as $tipo) {
            $linha = array();
            $linha['id'] = $tipo->getId();
            $linha['label'] = $tipo->getLabel();

            $dados[] = $linha;
        }

        return json_encode($dados);
    }

    /**
     * Retorna a tabela de Tipos
     *
     * @return \Html\Table
     */
    public function getTableTipos()
    {
        $button = $this->getButton();
        $tag = $this->getTag();
        $tipos = $this->getEm()->getRepository($this->getService()->getNameEntity())->findBy(array(), array('nome' => 'ASC'));

        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tableTipos")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()->setId("tableTipos")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }

        foreach ($tipos as $tipo) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                        $tag::link(
                                $tipo->getLabel(), array('href' => "javascript:editaTipo({$tipo->getId()})", 'id' => "tipo{$tipo->getId()}")
                        )
                );
            } else {
                $table->addData($tipo->getLabel());
            }

            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData(
                        $button->icon("trash", "javascript:excluirTipo({$tipo->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Salva um tipo
     *
     * @return JSON
     */
    public function salvarTipo()
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
     * Exclui um tipo
     *
     * @return JSON
     */
    public function excluiTipo()
    {
        $id = $this->getParam()->getInt("id");
        $result = $this->getService()->delete($id);
        return json_encode($result);
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function tipos()
    {
        return $this->getTpl()->renderView(array("tipos" => $this->getTableTipos()));
    }

    /**
     * @return \Template\TemplateAmanda
     */
    public function adminTipos()
    {
        $this->tpl->addJS('/tipoLcc/tipos.js');
        $this->tpl->renderView(array('titlePage' => $this->getTitle(), "tipos" => $this->getTableTipos()));

        return $this->getTpl()->output();
    }

}
