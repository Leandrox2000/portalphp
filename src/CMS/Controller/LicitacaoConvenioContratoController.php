<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use Entity\LicitacaoConvenioContrato as LicitacaoConvenioContratoEntity;
use Entity\Type;
use CMS\Service\ServiceRepository\LicitacaoConvenioContrato as LicitacaoConvenioContratoService;
use Helpers\Param;


/**
 * Description of LicitacaoConvenioContratoController
 *
 * @author Join
 */
class LicitacaoConvenioContratoController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Licitações e Convênios";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * @var FuncionarioService
     */
    protected $service;

    /**
     * @var FuncionarioEntity
     */
    protected $entity;

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
     * @param String $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @return LicitacaoConvenioContratoService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new LicitacaoConvenioContratoService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return LicitacaoConvenioContratoEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new LicitacaoConvenioContratoEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param LicitacaoConvenioContratoService $service
     */
    public function setService(LicitacaoConvenioContratoService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param LicitacaoConvenioContratoEntity $entity
     */
    public function setEntity(LicitacaoConvenioContratoEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        //Busca os dados da licitação, convênio ou contrato
        $lcc = $this->getEm()->getRepository('Entity\LicitacaoConvenioContrato')->find($id);

        //Busca os dados dos subcruds
        $ambitos = $this->getEm()->getRepository('Entity\AmbitoLcc')->findBy(array(), array('nome' => 'ASC'));
        $categorias = $this->getEm()->getRepository('Entity\CategoriaLcc')->findBy(array(), array('nome' => 'ASC'));
        $tipos = $this->getEm()->getRepository('Entity\TipoLcc')->findBy(array(), array('nome' => 'ASC'));
        $status = $this->getEm()->getRepository('Entity\StatusLcc')->findBy(array(), array('nome' => 'ASC'));

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        $this->getTpl()->addJS('/categoriaLcc/categorias.js');
        $this->getTpl()->addJS('/ambitoLcc/ambitos.js');
        $this->getTpl()->addJS('/tipoLcc/tipos.js');
        $this->getTpl()->addJS('/statusLcc/status.js');


        //Manda os dados para view
        $this->tpl->renderView(array(
            "data" => new \DateTime('now'),
            "method" => "POST",
            "titlePage" => $this->getTitle(),
            "ambitos" => $ambitos,
            "categorias" => $categorias,
            "tipos" => $tipos,
            "status" => $status,
            "lcc" => $lcc,
            "htmlArquivos" => $lcc ? $this->getHtmlArquivos($lcc->getArquivos()) : "",
            "arquivosAntigos" => $lcc ? $this->getArquivosAntigos($lcc->getArquivos()) : "",
            "arquivosNovos" => $lcc ? $this->getArquivosNovos($lcc->getArquivos()) : "",
                )
        );

        return $this->tpl->output();
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

        $categorias = $this->getEm()->getRepository('Entity\CategoriaLcc')->findBy(array(), array('nome' => 'ASC'));
        $tipo = $this->getEm()->getRepository('Entity\TipoLcc')->findBy(array(), array('nome' => 'ASC'));

        $this->tpl->renderView(
                array(
                    'titlePage' => $this->title,
                    'status' => $status,
                    'categorias' => $categorias,
                    'tipos' => $tipo
                )
        );

        return $this->getTpl()->output();
    }

    /**
     *
     * @return String
     */
    public function pagination()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoLcc = $em->getRepository("Entity\LicitacaoConvenioContrato");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repoLcc->countAll();

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status'),
            "categoria" => $param->get('categoria'),
            "tipo" => $param->get('tipo')
        );

        //Faz a busca e armazena o total de registros
        $lcc = $repoLcc->getBuscaLcc($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros);
        $totalFiltro = $repoLcc->getTotalBuscaLcc($filtros);

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($lcc as $l) {
            $linha = array();
            $linha[] = $this->getFields()->checkbox("lcc[]", $l->getId());

            if ($this->verifyPermission('LICIT_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($l->getLabel()), array("href" => "licitacaoConvenioContrato/form/" . $l->getId())) . $l->getDataCadastro()->format('d/m/Y') . " as " . $l->getDataCadastro()->format('H:i');
            }else{
                $linha[] = $tag->h4($l->getLabel()) . $l->getDataCadastro()->format('d/m/Y') . " as " . $l->getDataCadastro()->format('H:i');
            }

            $linha[] = $l->getCategoria()->getNome();
            $linha[] = $l->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
            $dados[] = $linha;
        }

        //Organiza o retorno e retorna via json
        $retorno['sEcho'] = Param::getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $totalFiltro;
        $retorno['iTotalRecords'] = $total;
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @return JSON
     */
    public function salvar()
    {
        //Armazena o Param
        $param = $this->getParam();

        //Organiza os dados
        $dados = array(
            'id' => $param->getInt('id'),
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('data_inicial'), 'Y-m-d') . " " . $param->get('hora_inicial')),
            'tipo' => $this->getEm()->getReference('Entity\TipoLcc', $param->get("tipo")),
            'ambito' => $this->getEm()->getReference('Entity\AmbitoLcc', $param->get("ambitoExecucao")),
            'categoria' => $this->getEm()->getReference('Entity\CategoriaLcc', $param->get("categoria")),
            'status' => $this->getEm()->getReference('Entity\StatusLcc', $param->get("status")),
            'dataPublicacaoDou' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('dataPublicacaoDou'), 'Y-m-d')),
            'dataAberturaProposta' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('dataAberturaProposta'), 'Y-m-d') . " " . $param->get('horaAberturaProposta')),
            'dataVigenciaInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('prazoVigenciaInicial'))),
            'dataVigenciaFinal' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('prazoVigenciaFinal'))),
            'contratada' => $param->get('contratada'),
            'objeto' => $param->get('objeto'),
            'edital' => $param->get('edital'),
            'valorEstimado' => $param->get('valorEstimado') ? $param->get('valorEstimado') : null,
            'uasg' => $param->get('uasg'),
            'ano' => $param->get('ano') ? $param->get('ano') : null,
            'processo' => $param->get('processo'),
            'observacoes' => $param->getString('observacao'),
            'arquivosNovos' => $param->get('arquivos'),
            'arquivosExcluidos' => $param->get('arquivosExcluidos'),
            'arquivosAntigos' => $param->get('arquivosAntigos'),
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

        $retorno = $this->getService()->save($dados);
        return json_encode($retorno);
    }

    /**
     *
     * @param \Doctrine\Common\Collections $arquivos
     * @return String
     */
    public function getHtmlArquivos(\Doctrine\Common\Collections\Collection $arquivos)
    {
        //Cria a variável que recebe o HTML
        $html = "";

        foreach ($arquivos as $arq) {
            $funcao = 'javascript:removerArquivo('.$arq->getId().', "'.$arq->getNome().'", "'.$arq->getNomeOriginal().'")';
            $html .= "<div id='divArquivo{$arq->getId()}'><a href='uploads/licitacaoConvenioContrato/{$arq->getNome()}' target='_blank'><i>{$arq->getNomeOriginal()}</i></a><a href='{$funcao}'>&nbsp;&nbsp;<strong>X</strong></a></div>";
        }

        //Retorna o html dos arquivos
        return $html;
    }

    /**
     *
     * @param \Doctrine\Common\Collections $arquivos
     * @return String
     */
    public function getArquivosAntigos(\Doctrine\Common\Collections\Collection $arquivos)
    {
        //Cria a variável que recebe os ids
        $arquivosAntigos = "";

        foreach ($arquivos as $arq) {
            $arquivosAntigos .= $arq->getNome() . "|";
        }

        //Retorna o html dos arquivos
        return $arquivosAntigos;
    }

        /**
     *
     * @param \Doctrine\Common\Collections $arquivos
     * @return String
     */
    public function getArquivosNovos(\Doctrine\Common\Collections\Collection $arquivos)
    {
        //Cria a variável que recebe os ids
        $arquivosAntigos = "";

        foreach ($arquivos as $arq) {
            $arquivosAntigos .= "|" . $arq->getNome() . ";;" .$arq->getNomeOriginal() ;
        }

        //Retorna o html dos arquivos
        return $arquivosAntigos;
    }


}
