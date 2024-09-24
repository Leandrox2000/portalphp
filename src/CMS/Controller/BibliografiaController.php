<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\Bibliografia as BibliografiaService;
use Entity\Bibliografia as BibliografiaEntity;
use Entity\Type;

/**
 * Description of BibliografiaController
 *
 * @author Luciano
 */
class BibliografiaController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Bibliografia";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var BibliografiaEntity
     */
    private $entity;

    /**
     *
     * @var BibliografiaService
     */
    private $service;

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
     * @return \Entity\Bibliografia
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new BibliografiaEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @return \CMS\Service\ServiceRepository\Bibliografia
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new BibliografiaService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @param \Entity\Bibliografia $entity
     */
    public function setEntity(BibliografiaEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Bibliografia $service
     */
    public function setService(BibliografiaService $service)
    {
        $this->service = $service;
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
        $bibliografia = $this->getEm()
                ->getRepository($this->getService()->getNameEntity())
                ->find($id);

        $idImg = "";
        if ($bibliografia) {
            if ($bibliografia->getImagem()) {
                $imagem = $this->getHtmlImagens($bibliografia->getImagem()->getId());
                $idImg = $bibliografia->getImagem()->getId();
            }
        }

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

//        $this->getTpl()->addCSS("/imagem/imagens.css");
//        $this->getTpl()->addJS("/imagem/imagens.js");

        $this->getTpl()->renderView(
                array(
                    "data" => new \DateTime("now"),
                    "hora" => new \DateTime("now"),
                    "imagem" => isset($imagem) ? $imagem : "",
                    "idImg" => isset($idImg) ? $idImg : "",
                    "bibliografia" => $bibliografia,
                    "method" => "POST",
                    "titlePage" => $this->getTitle()
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
        $status[] = new Type("0", "Não publicado");
        $status[] = new Type("1", "Publicado");


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

        $repBibliografias = $this->getEm()
                ->getRepository($this->getService()->getNameEntity());

        $bibliografias = $repBibliografias->getBibliografias(
            $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array(
            'status' => $param->get("status"),
            "busca" => $param->get("sSearch"),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get("data_inicial")),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get("data_final")),
        ));

        foreach ($bibliografias as $bibliografia) {
            $linha = array();
            $linha[] = $this->getFields()->checkbox("sel[]", $bibliografia->getId());
            $label = \Helpers\String::truncateText(strip_tags($bibliografia->getConteudo()), 100);

            if ($this->verifyPermission('BIBLIO_ALTERAR')) {
                $linha[] = $tag->link(
                    $tag->h4($label),
                    array("href" => "bibliografia/form/" . $bibliografia->getId())
//                ) . $bibliografia->getImagem();
                );
            } else {
                $linha[] = $tag->h4($label) . $bibliografia->getImagem();
            }

            $linha[] = $bibliografia->getPublicado() ? $tag->span("Publicado", array('class' => 'publicado')) : $tag->span("Não publicado", array('class' => 'naoPublicado'));
            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $repBibliografias->getMaxResult();
        $retorno['iTotalRecords'] = $repBibliografias->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @return JSON
     */
    public function salvar()
    {
        $id = $this->getParam()->getInt("id");
        $t = new \DateTime();
        $imagem = $this->getParam()->get('imagemBanco') !== "" ? $this->getEm()->getReference('Entity\Imagem', $this->getParam()->get('imagemBanco')) : null;

        if($id) $bibliografia = $this->getEm()->getRepository($this->getService()->getNameEntity())->find($id);
        
        $publicado = $id ? $bibliografia->getPublicado() : '0';
        
        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'titulo' => '...',
//            'titulo' => $this->getParam()->get("titulo"),
            'conteudo' => $this->getParam()->getString("conteudo"),
            'imagem' => $imagem,
            'publicado' => $publicado,
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
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

        return json_encode($this->getService()->save($dados));
    }

}
