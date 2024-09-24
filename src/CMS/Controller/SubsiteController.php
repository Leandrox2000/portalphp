<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use Entity\Site as SiteEntity;
use CMS\Service\ServiceRepository\Site as SiteService;
use Entity\Type;
use Helpers\Param;

/**
 * Description of SubsiteController
 *
 * @author Join
 */
class SubsiteController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Cadastro subsites";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * @var SiteService
     */
    protected $service;

    /**
     * @var SiteEntity
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
     * @param \CMS\Service\ServiceRepository\Site $service
     */
    public function setService(SiteService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @return SiteService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new SiteService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return SiteEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new SiteEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param \CMS\Controller\SiteEntity $entity
     */
    public function setEntity(SiteEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return JSON
     */
    public function delete()
    {
        $array = $this->getParam()->getArray("sel");
        $retorno = $this->getService()->delete($array);
        return json_encode($retorno);
    }

    private function getTypeCollection($entityCollection)
    {
        $typeCollection = array();

        foreach ($entityCollection as $e) {
            $typeCollection[] = new Type($e->getId(), $e->getNome());
        }

        return $typeCollection;
    }

    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        //Busca os dados do subsite
        $subsite = $this->getEm()->getRepository('Entity\Site')->find($id);
        $funcMarcadas = array();

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        $fc = $this->getEm()
                ->getRepository('Entity\Funcionalidade')
                ->findBy(array(), array('nome' => 'ASC'));
        $funcionalidades = $this->getTypeCollection($fc);

        if ($id != 0) {
            //$funcs = $subsite->getFuncionalidades();
            $funcs = $this->getEm()->getRepository('Entity\FuncionalidadeSite')->getFuncionalidades($subsite->getId());
            foreach ($funcs as $func) {
                $marcadas_array_id[] = $func->getFuncionalidade()->getId();
                $funcMarcadas[] = $this->getEm()->getRepository('Entity\Funcionalidade')->find($func->getFuncionalidade());
            }
            
            
            
            
            foreach ($funcionalidades as $func) {
                if(!$marcadas_array_id){
                    $funcionalidadesNaoVinculadas[] = $func;
                }elseif($marcadas_array_id and !in_array($func->getId(), $marcadas_array_id) ){
                    $funcionalidadesNaoVinculadas[] = $func;
                }
                
                /*$valida = false;
                for($i = 0; $i <= count($marcadas_array_id); $i++){
                    if($func->getId() == $marcadas_array_id[$i]) $valida = true;
                }
                if($valida == false) $funcionalidadesNaoVinculadas[] = $func;*/
                
            }
        }else{
            foreach ($funcionalidades as $func) {
                $funcionalidadesNaoVinculadas[] = $func;
            }
        }

        $this->tpl->renderView(
                array(
                    "funcionalidades" => $funcionalidadesNaoVinculadas,
                    "funcionalidadesMarcadas" => $funcMarcadas,
                    "data" => new \DateTime('now'),
                    "subsite" => $subsite,
                    "method" => "POST",
                    "titlePage" => $this->getTitle(),
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

        $this->tpl->renderView(
                array(
                    'titlePage' => $this->title,
                    'status' => $status
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
        $repoSite = $em->getRepository("Entity\Site");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repoSite->countAll();

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status')
        );

        //Faz a busca e armazena o total de registros
        $subsites = $repoSite->getBuscaSite($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros);
        $totalFiltro = $repoSite->getTotalBuscaSite($filtros);

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($subsites as $site) {
            $linha = array();
            $linha[] = $this->getFields()->checkbox("subsite[]", $site->getId());

            if ($this->verifyPermission('SUBSIT_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($site->getLabel()), array("href" => "subsite/form/" . $site->getId())) . $site->getDataCadastro()->format('d/m/Y') . " as " . $site->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($site->getLabel()) . $site->getDataCadastro()->format('d/m/Y') . " as " . $site->getDataCadastro()->format('H:i');
            }

            $linha[] = $site->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
            $linha[] = "<a href='javascript:visualizar({$site->getId()})' class='btn btn3 btn_search' ></a>";

            //$function = "javascript:visualizar('{$site->getSigla()}')";

          //  $linha[] = "<a href={$function} class='btn btn3 btn_search' ></a>";
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
        $param = $this->getParam();
        $id = $param->getInt("id");
        $service = $this->getService();

        $funcionalidades = json_decode($param->get("hidden_funcionalidade"));
        
        $dados = array(
            'id' => $id,
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('data_inicial'), 'Y-m-d') . " " . $param->get('hora_inicial')),
            'nome' => $param->get("nome"),
            'titulo' => $param->get("titulo"),
            'descricao' => $param->getString("descricao"),
            'funcionalidade' => $funcionalidades,
            'sigla' => $param->get("sigla"),
            'facebook' => $param->get("facebook"),
            'twitter' => $param->get("twitter"),
            'youtube' => $param->get("youtube"),
            'flickr' => $param->get("flickr"),
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

}
