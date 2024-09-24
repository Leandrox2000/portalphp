<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\PerguntaCategoria as PerguntaCategoriaService;
use CMS\Service\ServiceRepository\Pergunta as PerguntaService;
use Entity\Pergunta as PerguntaEntity;
use Entity\PerguntaCategoria as PerguntaCategoriaEntity;
use Entity\Type;

/**
 * PerguntaController
 *
 * @author join-ti
 */
class PerguntaController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Perguntas frequentes";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * @var PerguntaService
     */
    protected $service;

    /**
     * @var PerguntaCategoriaService
     */
    protected $serviceCategoria;

    /**
     * @var PerguntaEntity
     */
    protected $entity;

    /**
     * @var PerguntaCategoriaEntity
     */
    protected $entityCategoria;

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
     * @return PerguntaEntity
     */
    public function getEntity()
    {
        if (is_null($this->entity)) {
            $this->setEntity(new PerguntaEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @return PerguntaCategoriaEntity
     */
    public function getEntityCategoria()
    {
        if (is_null($this->entityCategoria)) {
            $this->setEntityCategoria(new PerguntaCategoriaEntity());
        }
        return $this->entityCategoria;
    }

    /**
     *
     * @return PerguntaService
     */
    public function getService()
    {
        if (is_null($this->service)) {
            $this->setService(new PerguntaService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return PerguntaCategoriaService
     */
    public function getServiceCategoria()
    {
        if (is_null($this->serviceCategoria)) {
            $this->setServiceCategoria(new PerguntaCategoriaService($this->getEm(), $this->getEntityCategoria(), $this->getSession()));
        }
        return $this->serviceCategoria;
    }

    /**
     *
     * @param PerguntaService $service
     */
    public function setService(PerguntaService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param PerguntaCategoriaService $serviceCategoria
     */
    public function setServiceCategoria(PerguntaCategoriaService $serviceCategoria)
    {
        $this->serviceCategoria = $serviceCategoria;
    }

    /**
     *
     * @param \Entity\Pergunta $entity
     */
    public function setEntity(PerguntaEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @param \Entity\PerguntaCategoria $entityCategoria
     */
    public function setEntityCategoria(PerguntaCategoriaEntity $entityCategoria)
    {
        $this->entityCategoria = $entityCategoria;
    }

    /**
     *
     * @param type $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {

        /*$categorias = $this->getEm()
                ->getRepository("Entity\PerguntaCategoria")
                ->findAll();*/

        $categorias = $this->getEm()
                ->getRepository("Entity\PerguntaCategoria")
                ->findBy(array(),array("categoria" => "ASC"));
        
        $pergunta = $this->getEm()
                ->getRepository("Entity\Pergunta")
                ->find($id);


        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        $this->tpl->addJS("/pergunta/categorias.js");
        $this->tpl->renderView(array(
            "data" => date("d/m/Y"),
            "hora" => date("H:i"),
            "categorias" => $categorias,
            "pergunta" => $pergunta,
            "method" => "POST",
            "titlePage" => $this->getTitle()
        ));
        return $this->tpl->output();
    }

    public function lista()
    {
        $categoroas = $this->getEm()->getRepository("Entity\\PerguntaCategoria")->findAll();

        $this->tpl->renderView(
                array(
                    'titlePage' => $this->title,
                    'categorias' => $categoroas,
                )
        );
        return $this->tpl->output();
    }

    public function pagination()
    {
        $tag = $this->getTag();
        $param = $this->getParam();
        $dados = array();
        $repPerguntas = $this->getEm()
                ->getRepository("Entity\\Pergunta");

        /*$perguntas = $repPerguntas->getPerguntas(
                $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array(
            'categoria' => $param->getInt('categoria'),
            'busca' => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            'data_inicial' => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            'data_final' => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
                )
        );*/
        
        $perguntas = $repPerguntas->getPerguntasOrder(
                $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array(
            'categoria' => $param->getInt('categoria'),
            'busca' => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            'data_inicial' => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            'data_final' => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
                )
        );
        

        foreach ($perguntas as $pergunta) {
            $linha = array();

            $linha[] = $this->getFields()->checkbox("sel[]", $pergunta->getId());

            if ($this->verifyPermission('PERGU_ALTERAR')) {
                $linha[] = $tag->link(
                                $tag->h4($pergunta->getLabel()), array("href" => "pergunta/form/" . $pergunta->getId())
                        )
                        . $pergunta->getCategoria()->getCategoria();
            } else {
                $linha[] = $tag->h4($pergunta->getLabel()) . $pergunta->getCategoria()->getCategoria();
            }

            $linha[] = $pergunta->getPublicado() ? $tag->span("Publicado", array('class' => 'publicado')) : $tag->span("Não publicado", array('class' => 'naoPublicado'));

            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $repPerguntas->getMaxResult();
        $retorno['iTotalRecords'] = $repPerguntas->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    public function salvar()
    {
        $param = $this->getParam();
        $id = $param->getInt("id");
        $service = $this->getService();

        $dados = array(
            'id' => $id,
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('data_inicial'), 'Y-m-d') . " " . $param->get('hora_inicial')),
            'pergunta' => $param->get("pergunta"),
            'resposta' => $param->get("resposta"),
            'categoria' => $this->getEm()->getReference("Entity\\PerguntaCategoria", $param->get("categoria")),
            'ordem' => $id ? $this->getParam()->getInt("ordem") : $this->getEm()->getRepository($this->getService()->getNameEntity())->buscarUltimaOrdem()
        );

        $dataFinal = $param->get('data_final');
        $horaFinal = $param->get('hora_final');

        // se a dataFinal não estiver setada ou receber o valor vazio
        // a variável deve ser setada como NULA
        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal, 'Y-m-d') . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }

        $retorno = $service->save($dados);
        return json_encode($retorno);
    }

    /**
     *
     * @return String
     */
    public function categorias()
    {
        return $this->getTpl()->renderView(
                        array(
                            'categorias' => $this->getTableCategorias()
                        )
        );
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function adminCategorias()
    {
        $this->setTitle('Categoria Perguntas Frequentes');
        $this->tpl->addJS('/pergunta/categorias.js');
        $this->getTpl()->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'categorias' => $this->getTableCategorias()
                )
        );

        return $this->tpl->output();
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

        $service = $this->getServiceCategoria();

        $result = $service->save($nome, $id);

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
        $result = $this->getServiceCategoria()->delete($id);
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
        $categorias = $this->getEm()
                ->getRepository("Entity\PerguntaCategoria")
                ->getCategorias();

        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }

        foreach ($categorias as $key => $categoria) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                        $tag::link(
                                $categoria->getCategoria(), array('href' => "javascript:editaCategoria({$categoria->getId()})", 'id' => "categoria{$categoria->getId()}")
                        )
                );
            } else {
                 $table->addData($categoria->getCategoria());
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
                ->getRepository("Entity\PerguntaCategoria")
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

    public function ajaxAtualizarOrdenacaoPergunta()
    {
        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach($paramOrdenation as $item){
            $newOrdenation[$item['id']] = (int)$item['ordenacao'];
        }
       
        return json_encode($this->getService()->updateOrdem($newOrdenation));
    }
    
    
}
