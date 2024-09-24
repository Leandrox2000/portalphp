<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\ComentarioNoticia as ComentarioService;
use Entity\ComentarioNoticia as ComentarioEntity;
use Entity\Type;


/**
 * Description of DicionarioPatrimonioCulturalController
 */
class ComentarioNoticiaController extends CrudController
{

    const PAGE_TITLE = 'Comentários de notícias';
    const DEFAULT_ACTION = 'lista';

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * Rota para o controller.
     */
    protected $controllerRoute = 'comentarioNoticia';

    /**
     * Nome da entidade
     */
    protected $entityNamespace = 'Entity\ComentarioNoticia';

    /**
     * Name do checkbox utilizado para seleção multipla.
     */
    protected $selectionCheckboxName = 'comentarioNoticia';

    /**
     *
     * @var ComentarioService
     */
    private $service;

    /**
     *
     * @var ComentarioEntity
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
     * @return \ComentarioService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new ComentarioService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return ComentarioEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new ComentarioEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param ComentarioService $service
     */
    public function setService(ComentarioService $service)
    {
        $this->service = $service;
    }


    /**
     *
     * @param ComentarioEntity $entity
     */
    public function setEntity(ComentarioEntity $entity)
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
            $linha[] = $registro->getLabel();
            $linha[] = $registro->getAutor();
            $linha[] = $registro->getEmail();
            $linha[] = $registro->getDataCadastro()->format('d/m/Y');
            $linha[] = $registro->getPublicado() ? $tag->span("Publicado", array('class' => 'publicado')) : $tag->span("Não publicado", array('class' => 'naoPublicado'));

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
     * @return JSON
     */
    public function salvar()
    {
        $dados = array();

        return json_encode($this->getService()->save($dados));
    }

}
