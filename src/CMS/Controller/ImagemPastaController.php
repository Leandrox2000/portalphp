<?php

namespace CMS\Controller;

use Entity\ImagemPasta as ImagemPastaEntity;
use CMS\Service\ServiceRepository\ImagemPasta as ImagemPastaService;

/**
 * Description of ImagemPastaController
 *
 * @author HOLANDA
 */
class ImagemPastaController extends CrudController {

    const PAGE_TITLE = "Pastas"; 
    const DEFAULT_ACTION = "pastas";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var ImagemPastaService
     */
    protected $service;

    /**
     *
     * @var ImagemPastaEntity
     */
    protected $entity;

    /**
     * 
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * 
     * @return String
     */
    public function getDefaultAction() {
        return $this->defaultAction;
    }

    /**
     * 
     * @return String
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * 
     * @return ImagemPastaService
     */
    public function getService() {
        if (empty($this->service)) {
            $this->setService(new ImagemPastaService($this->getEm(), $this->getEntity(), $this->getSession(), $this->getHelperString()));
        }
        return $this->service;
    }

    /**
     * 
     * @return ImagemPastaEntity
     */
    public function getEntity() {
        if (empty($this->entity)) {
            $this->setEntity(new ImagemPastaEntity());
        }
        return $this->entity;
    }

    /**
     * 
     * @param ImagemPastaService $service
     */
    public function setService(ImagemPastaService $service) {
        $this->service = $service;
    }

    /**
     * 
     * @param ImagemPastaEntity $entity
     */
    public function setEntity(ImagemPastaEntity $entity) {
        $this->entity = $entity;
    }

    /**
     * Retorna as pastas em um formato JSON
     * 
     * @return JSON
     */
    public function getPasta($categoria = null) {
        if (is_null($categoria)) {
            $pastas = $this->getEm()->getRepository($this->getService()->getNameEntity())->findBy(array(), array('nome' => 'ASC'));
        } else {
            $pastas = $this->getEm()->getRepository($this->getService()->getNameEntity())->findBy(array('categoria' => $categoria),array('nome' => 'ASC'));
        }

        $dados = array();

        foreach ($pastas as $pasta) {
            $linha = array();
            $linha['id'] = $pasta->getId();
            $linha['label'] = $pasta->getLabel();

            $dados[] = $linha;
        }

        return json_encode($dados);
    }

    /**
     * Retorna a tabela de Categoria
     * 
     * @return \Html\Table
     */
    public function getTablePastas() {
        $button = $this->getButton();
        $tag = $this->getTag();
        $pastas = $this->getEm()->getRepository($this->getService()->getNameEntity())->findBy(array(), array('nome' => 'ASC'));

        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tablePastas")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()->setId("tablePastas")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }

        foreach ($pastas as $pasta) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                        $tag::link(
                                $pasta->getLabel(), array('href' => "javascript:editaPasta({$pasta->getId()})", 'id' => "pasta{$pasta->getId()}")
                        )
                );
            } else {
                $table->addData($pasta->getLabel());
            }

            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData(
                        $button->icon("trash", "javascript:excluirPasta({$pasta->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Salva uma categoria
     * 
     * @return JSON
     */
    public function salvarPasta() {
        $param = $this->getParam();
        $idCategoria = $param->get("categoria") ? $param->get("categoria") : 0;
        $categoria = $this->getEm()->getRepository('Entity\ImagemCategoria')->find($idCategoria);

        $dados = array(
            "id" => $param->getInt("id"),
            "nome" => $param->get("nome"),
            "caminho" => $this->getHelperString()->removeSpecial($param->get("nome")),
            "categoria" => $param->get("categoria") ? $this->getEm()->getReference('Entity\ImagemCategoria', $param->get("categoria")) : "",
            "nomeCategoria" => $param->get("categoria") ? $this->getHelperString()->removeSpecial($categoria->getNome()) : ""
        );

        $result = $this->getService()->save($dados);
        return json_encode($result);
    }

    /**
     * Exclui uma pasta
     * 
     * @return JSON
     */
    public function excluiPasta() {
        $id = $this->getParam()->getInt("id");
        $result = $this->getService()->delete($id);
        return json_encode($result);
    }

    /**
     * 
     * @return \Template\TemplateAmanda
     */
    public function pastas() {
        $this->setTitle('Pastas');
        $this->tpl->addJS('/imagemPasta/pastas.js');


        $this->getTpl()->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'pastas' => $this->getTablePastas(),
                    'categorias' => $this->getEm()->getRepository('Entity\ImagemCategoria')->findBy(array(), array('nome' => 'ASC'))
                )
        );

        return $this->tpl->output();
    }

    /**
     * 
     * @return \Template\TemplateAmanda
     */
    public function pastasColorbox() {

        return $this->getTpl()->renderView(
                        array(
                            'titlePage' => $this->getTitle(),
                            'pastas' => $this->getTablePastas(),
                            'categorias' => $this->getEm()->getRepository('Entity\ImagemCategoria')->findBy(array(), array('nome' => 'ASC'))
                        )
        );
    }

}
