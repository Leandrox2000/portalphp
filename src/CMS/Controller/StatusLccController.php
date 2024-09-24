<?php

namespace CMS\Controller;

use Entity\StatusLcc as StatusEntity;
use CMS\Service\ServiceRepository\StatusLcc as StatusService;
use Helpers\Param;


/**
 * Description of StatusLccController
 *
 * @author HOLANDA
 */
class StatusLccController extends CrudController
{

    const PAGE_TITLE = "Status licitações";
    const DEFAULT_ACTION = "status";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var StatusService
     */
    protected $service;

    /**
     *
     * @var StatusEntity
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
     * @return StatusService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new StatusService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return StatusEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new StatusEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param StatusService $service
     */
    public function setService(StatusService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param StatusEntity $entity
     */
    public function setEntity(StatusEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Retorna os statuss em um formato JSON
     *
     * @return JSON
     */
    public function getStatus()
    {
        $status = $this->getEm()->getRepository($this->getService()->getNameEntity())->findBy(array(), array('nome' => 'ASC'));
        $dados = array();

        foreach ($status as $status) {
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
    public function getTableStatus()
    {
        $button = $this->getButton();
        $tag = $this->getTag();
        
        $repository = $this->getEm()->getRepository($this->getService()->getNameEntity());
        $status = $repository->findBy(array(), array('ordem' => 'ASC'));
        
        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()
                        ->setId("tableStatus")
                        ->setNumColumns(3)
                        ->addColumnHeader("Nome", "60%")
                        ->addColumnHeader("Ordenação", "30%")
                        ->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()
                        ->setId("tableStatus")
                        ->setNumColumns(2)
                        ->addColumnHeader("Nome", "65%")
                        ->addColumnHeader("Ordenação", "35%");
        }

        foreach ($status as $st) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                    $tag::link($st->getLabel(), array('href' => "#", 'data-id' => $st->getId(), 'data-column' => $st->getColumn()))
                );
            } else {
                $table->addData($st->getLabel());
            }
            
            $table->addData($repository->getColumnLabel($st->getColumn()));

            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData(
                        $button->icon("trash", "javascript:excluirStatus({$st->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Salva um status
     *
     * @return JSON
     */
    public function salvarStatus()
    {
        $param = $this->getParam();
        $dados = array(
            "id" => $param->getInt("id"),
            "nome" => $param->get("nome")       
        );
        
        $dados['column'] = $param->get("column");
        
        if(empty($dados['column'])) $dados['column'] = 1;

        $result = $this->getService()->save($dados);

        return json_encode($result);
    }

    /**
     * Exclui um status
     *
     * @return JSON
     */
    public function excluiStatus()
    {
        $id = $this->getParam()->getInt("id");
        $result = $this->getService()->delete($id);
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
        $this->setTitle('Status de licitação');
        $this->tpl->addJS('/statusLcc/status.js');

        $this->getTpl()->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'status' => $this->getTableStatus(),
                    'columns' => $this->getEm()->getRepository($this->getService()->getNameEntity())->getColumnLabel()
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
        
        $return = $this->getEm()
            ->getRepository($this->getService()->getNameEntity())
            ->setOrdem($newOrdenation);
        
        return json_encode(array(
            'resultado' => 'ok'
        ));
    }
}
