<?php

namespace CMS\Controller;

use Entity\AmbitoLcc as AmbitoEntity;
use CMS\Service\ServiceRepository\AmbitoLcc as AmbitoService;
use Helpers\Param;


/**
 * Description of AmbitoLccController
 *
 * @author HOLANDA
 */
class AmbitoLccController extends CrudController
{

    const PAGE_TITLE = "";
    const DEFAULT_ACTION = "ambito";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var AmbitoService
     */
    protected $service;

    /**
     *
     * @var AmbitoEntity
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
     * @return AmbitoService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new AmbitoService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return AmbitoEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new AmbitoEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param AmbitoService $service
     */
    public function setService(AmbitoService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param AmbitoEntity $entity
     */
    public function setEntity(AmbitoEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Retorna os âmbitos em um formato JSON
     *
     * @return JSON
     */
    public function getAmbitos()
    {
        $ambitos = $this->getEm()->getRepository($this->getService()->getNameEntity())->findBy(array(),array('nome' => 'ASC'));
        $dados = array();

        foreach ($ambitos as $ambito) {
            $linha = array();
            $linha['id'] = $ambito->getId();
            $linha['label'] = $ambito->getLabel();

            $dados[] = $linha;
        }

        return json_encode($dados);
    }

    /**
     * Retorna a tabela de Ambitos
     *
     * @return \Html\Table
     */
    public function getTableAmbitos()
    {
        $button = $this->getButton();
        $tag = $this->getTag();
        $ambitos = $this->getEm()->getRepository($this->getService()->getNameEntity())->findAll();

        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tableAmbitos")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()->setId("tableAmbitos")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }

        foreach ($ambitos as $ambito) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                        $tag::link(
                                $ambito->getLabel(), array('href' => "javascript:editaAmbito({$ambito->getId()})", 'id' => "ambito{$ambito->getId()}")
                        )
                );
            } else {
                $table->addData($ambito->getLabel());
            }

            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData(
                        $button->icon("trash", "javascript:excluirAmbito({$ambito->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Salva um âmbito
     *
     * @return JSON
     */
    public function salvarAmbito()
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
     * Exclui um âmbito
     *
     * @return JSON
     */
    public function excluiAmbito()
    {
        $id = $this->getParam()->getInt("id");
        $result = $this->getService()->delete($id);
        return json_encode($result);
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function ambitos()
    {
        return $this->getTpl()->renderView(array("ambitos" => $this->getTableAmbitos()));
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function adminAmbitos()
    {
        $this->setTitle('Âmbitos de licitação');
        $this->tpl->addJS('/ambitoLcc/ambitos.js');

        $this->getTpl()->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'ambitos' => $this->getTableAmbitos()
                )
        );

        return $this->tpl->output();
    }

}
