<?php

namespace CMS\Controller;

use Helpers\Param;
use Entity\BoletimEletronico as BoletimEletronicoEntity;
use Entity\Type;
use CMS\Service\ServiceRepository\BoletimEletronico as BoletimEletronicoService;
use LibraryController\CrudControllerInterface;

/**
 * BoletimEletronicoController
 *
 * @author join-ti
 */
class BoletimEletronicoController  extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Boletim eletrônico";
    const DEFAULT_ACTION = "lista";

    /**
     * @var String 
     */
    protected $title = self::PAGE_TITLE;

    /**
     * @var String 
     */
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var EmailBoletimService 
     */
    protected $service;

    /**
     * 
     * @var BoletimEletronico 
     */
    protected $entity;

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
     * @param String $title
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
     * @return BoletimEletronicoService
     */
    public function getService()
    {
        if (!isset($this->service))
            $this->service = new BoletimEletronicoService($this->getEm(), $this->getEntity(), $this->getSession());
        return $this->service;
    }

    /**
     * 
     * @param BoletimEletronicoService $service
     */
    public function setService(BoletimEletronicoService $service)
    {
        $this->service = $service;
    }

    /**
     * 
     * @return BoletimEletronicoEntity
     */
    public function getEntity()
    {
        if (!isset($this->entity))
            $this->entity = new BoletimEletronicoEntity();
        return $this->entity;
    }

    /**
     * 
     * @param BoletimEletronicoEntity $entity
     */
    public function setEntity(BoletimEletronicoEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * 
     * @return string
     */
    public function lista()
    {
        //Cria os objetos status
        $naoPublicado = new Type("0", "Não publicado");
        $publicado = new Type("1", "Publicado");

        //Busca e organiza todos os anos
        $anos = $this->getEm()->getRepository("Entity\BoletimEletronico")->getAnos();
        $arrayAnos = array();

        foreach ($anos as $ano) {
            $arrayAnos[] = new Type($ano['ano'], $ano['ano']);
        }

        //Busca os anos
        $this->tpl->renderView(array(
            'anos' => $arrayAnos,
            'status' => array($naoPublicado, $publicado),
            'titlePage' => $this->getTitle(),
            'subTitlePage' => "")
        );
        return $this->tpl->output();
    }

    /**
     * 
     * @param type $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        //Busca dados do boletim
        $boletim = $this->getEm()
                ->getRepository("Entity\BoletimEletronico")
                ->find($id);

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        $this->tpl->renderView(array(
            "data" => new \DateTime('now'),
            "titlePage" => $this->getTitle(),
            "boletim" => $boletim,
            "method" => "POST",
                )
        );
        return $this->tpl->output();
    }

    /**
     *
     * @return String
     */
    public function pagination()
    {
        $tag = $this->getTag();
        $param = $this->getParam();

        //Instancia o repository
        $em = $this->getEm();
        $repoBoletim = $em->getRepository("Entity\BoletimEletronico");

        //Busca o total
        $total = $repoBoletim->countAll();

        //Monta o array de filtros
        $filtros = array(
            "busca" => $param->get('sSearch'),
            "ano" => $param->get('anos'),
            "data_inicial" => $param->get('data_inicial') != "" ? $this->getDatetimeFomat()->formatUs($param->get('data_inicial')) : "",
            "data_final" => $param->get('data_final') != "" ? $this->getDatetimeFomat()->formatUs($param->get('data_final')) : "",
            "status" => $param->get('status')
        );

        //Faz a busca e armazena o total
        $boletins = $repoBoletim->getBuscaBoletimEletronico($this->getParam()->get('iDisplayLength'), $this->getParam()->get('iDisplayStart'), $filtros);
        $totalFiltro = $repoBoletim->getTotalBuscaBoletimEletronico($filtros);

        //Percorre e organiza o HTML da listagem
        $dados = array();
        foreach ($boletins as $boletim) {
            $linha = array();
            $linha[] = "<input type='checkbox' name='boletimletronico[]' value=" . $boletim->getId() . " class='marcar' />";
            
            if ($this->verifyPermission('BOLELET_ALTERAR')) {
                $linha[] = $tag->link($tag->h4("n° ".$boletim->getLabel()), array("href" => "boletimEletronico/form/" . $boletim->getId())).$boletim->getPeriodoInicial()->format("d/m/Y") . " a " . $boletim->getLabel();
            } else {
                $linha[] = $tag->h4($boletim->getPeriodoInicial()->format("d/m/Y") . " - " . $boletim->getLabel());
            }
            
            $linha[] = $boletim->getPublicado() == 1 ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
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
     * @return String
     */
    public function delete()
    {
        //Busca o entityManager e o service
        $service = $this->getService();

        //Pega os Ids Enviados
        $ids = $this->getParam()->getArray("sel");

        //Faz a exclusão
        $retorno = $service->delete($ids);

        //Retorna o json
        return json_encode($retorno);
    }

    /**
     * 
     * @return string
     */
    public function salvar()
    {
        //Busca o organiza os parâmetros
        $param = $this->getParam();
        $id = $param->getInt("id") != 0 && $param->getInt("id") != "" ? $param->getInt("id") : "";
        //$edicao = explode("/", (substr($param->get('edicao'), 3)));//old

        $numero = $param->get('numero');
        $ano = $param->get('ano');
        //var_dump($numero, $ano);

        $dados = array(
            'id' => $id,
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('data_inicial'), 'Y-m-d') . " " . $param->get('hora_inicial')),
            'periodoInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('periodo_inicial'), 'Y-m-d')),
            'periodoFinal' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('periodo_final'), 'Y-m-d')),
            'numero' => $numero,
            'ano' => $ano,
            'arquivo' => $param->getString('arquivoNome'),
            'arquivoExcluido' => $param->getString('arquivoExcluido'),
            'arquivoAtual' => $param->getString('arquivoAtual'),
            'publicado' => 0
        );

        //Verifica se a data e hora final de publicação foram informadas
        $dataFinal = $param->get('data_final');
        $horaFinal = $param->get('hora_final');

        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal, 'Y-m-d') . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }
        //Faz o insert ou o update e retorna o json
        $retorno = $this->getService()->save($dados);
        return json_encode($retorno);
    }


}
