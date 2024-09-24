<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\BackgroundHome as BackgroundHomeService;
use Entity\BackgroundHome as BackgroundHomeEntity;
use Entity\Type;

/**
 * Description of DicionarioPatrimonioCulturalController
 */
class BackgroundHomeController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = 'Gerenciamento de background do Portal';
    const DEFAULT_ACTION = 'lista';

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * Rota para o controller.
     */
    protected $controllerRoute = 'backgroundHome';

    /**
     * Nome da entidade
     */
    protected $entityNamespace = 'Entity\BackgroundHome';

    /**
     * Name do checkbox utilizado para seleção multipla.
     */
    protected $selectionCheckboxName = 'backgroundHome';

    /**
     *
     * @var BackgroundHomeService
     */
    private $service;

    /**
     *
     * @var BackgroundHomeEntity
     */
    private $entity;

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
     * @return \BackgroundHomeService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new BackgroundHomeService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return BackgroundHomeEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new BackgroundHomeEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param BackgroundHomeService $service
     */
    public function setService(BackgroundHomeService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param BackgroundHomeEntity $entity
     */
    public function setEntity(BackgroundHomeEntity $entity)
    {
        $this->entity = $entity;
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

            if ($this->verifyPermission('BGPORT_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($registro->getLabel()), array('href' => "{$this->controllerRoute}/form/" . $registro->getId())) . $registro->getDataCadastro()->format('d/m/Y') . ' as ' . $registro->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($registro->getLabel()) . $registro->getDataCadastro()->format('d/m/Y') . ' as ' . $registro->getDataCadastro()->format('H:i');
            }

            $linha[] = $registro->getPublicado() ? $tag->span('Publicado', array('class' => 'publicado')) : $tag->span('Não publicado', array('class' => 'naoPublicado'));
            $linha[] = "<a href='javascript:visualizar({$registro->getId()})' class='btn btn3 btn_search' ></a>";

            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt('sEcho');
        $retorno['iTotalDisplayRecords'] = $repository->getTotal($filtros);
        $retorno['iTotalRecords'] = $repository->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        // Adiciona o CSS e o JS para seleção de imagem
//        $this->tpl->addJS("/imagem/imagens.js");
//        $this->tpl->addCSS("/imagem/imagens.css");

        $entity = $this->getEm()
                ->getRepository($this->getService()->getNameEntity())
                ->find($id);

        // Verifica o Id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . ' - Inserir');
        } else {
            $this->setTitle(self::PAGE_TITLE . ' - Alterar');
        }

        $this->getTpl()->renderView(
                array(
                    'data' => new \DateTime('now'),
                    'entity' => $entity,
                    'method' => 'POST',
                    'titlePage' => $this->getTitle(),
                    'imagem' => $entity ? $this->getHtmlImagens($entity->getImagem()->getId()) : "",
                    'idImg' => $entity ? $entity->getImagem()->getId() : ""
                )
        );

        return $this->getTpl()->output();
    }

    protected function getDados()
    {
        $dataInicial = $this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial'));
        $horaInicial = $this->getParam()->get('horaInicial');
        $dataFinal = $this->getParam()->get('dataFinal');
        $horaFinal = $this->getParam()->get('horaFinal');

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'nome' => $this->getParam()->get('nome'),
            'dataInicial' => new \DateTime($dataInicial . " " . $horaInicial),
            'imagem' => $this->getEm()->getReference('Entity\Imagem', $this->getParam()->getInt('imagemBanco')),
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

}
