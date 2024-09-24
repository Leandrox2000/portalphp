<?php

namespace CMS\Controller;

use Entity\Unidade as UnidadeEntity;
use Entity\Type;
use CMS\Service\ServiceRepository\Unidade as UnidadeService;
use Helpers\Param;
use LibraryController\CrudControllerInterface;

/**
 * UnidadeController
 *
 * @author join-ti
 */
class UnidadeController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Unidades";
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
     * @var UnidadeEntity
     */
    protected $entity;

    /**
     *
     * @var UnidadeService
     */
    protected $service;

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
     * @return UnidadeEntity
     */
    public function getEntity()
    {
        if (!isset($this->entity)) {
            $this->entity = new UnidadeEntity();
        }
        return $this->entity;
    }

    /**
     *
     * @param UnidadeEntity $entity
     */
    public function setEntity(UnidadeEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return UnidadeService
     */
    public function getService()
    {
        if (!isset($this->service)) {
            $this->service = new UnidadeService($this->getEm(), $this->getEntity(), $this->getSession());
        }
        return $this->service;
    }

    /**
     *
     * @param UnidadeService $service
     */
    public function setService(UnidadeService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @return string
     */
    public function lista()
    {

        //Busca os anos
        $this->tpl->renderView(array(
            'titlePage' => $this->getTitle(),
            'subTitlePage' => "",
        ));

        return $this->tpl->output();
    }

    private function getEstados()
    {
        return array(
            "AC" => "Acre",
            "AL" => "Alagoas",
            "AM" => "Amazonas",
            "AP" => "Amapá",
            "BA" => "Bahia",
            "CE" => "Ceará",
            "DF" => "Distrito Federal",
            "ES" => "Espírito Santo",
            "GO" => "Goiás",
            "MA" => "Maranhão",
            "MT" => "Mato Grosso",
            "MS" => "Mato Grosso do Sul",
            "MG" => "Minas Gerais",
            "PA" => "Pará",
            "PB" => "Paraíba",
            "PR" => "Paraná",
            "PE" => "Pernambuco",
            "PI" => "Piauí",
            "RJ" => "Rio de Janeiro",
            "RN" => "Rio Grande do Norte",
            "RO" => "Rondônia",
            "RS" => "Rio Grande do Sul",
            "RR" => "Roraima",
            "SC" => "Santa Catarina",
            "SE" => "Sergipe",
            "SP" => "São Paulo",
            "TO" => "Tocantins"
        );
    }

    /**
     *
     * @param type $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        // Busca dados da legislação
        $unidade = $this->getEm()->getRepository("Entity\Unidade")->find($id);
        $estadosArray = $this->getEstados();
        $estados = array();

        foreach ($estadosArray as $key => $value) {
            $estados[] = new Type($key, $value);
        }

        // Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
            $uf = new Type($unidade->getUf(), $estadosArray[$unidade->getUf()]);
        }

        $this->tpl->renderView(array(
            "data" => new \DateTime('now'),
            "unidade" => $unidade,
            "titlePage" => $this->getTitle(),
            "method" => "POST",
            "estados" => $estados,
            "uf" => isset($uf) ? $uf : ""
        ));

        return $this->tpl->output();
    }

    /**
     *
     * @return string
     */
    public function salvar()
    {
        // Armazena o parâmetro
        $param = $this->getParam();
        $estado = $this->getEstados();
        $dados = array(
            'id' => $param->getInt('id'),
            'nome' => $param->get('nome'),
            'endereco' => $param->get('endereco'),
            'cep' => $param->get('cep'),
            'cidade' => $param->get('cidade'),
            'uf' => $param->get('estado'),
            'bairro' => $param->get('bairro'),
            'numero' => $param->get('numero'),
            'complemento' => $param->get('complemento'),
            'telefone' => $param->get('telefone'),
            'celular' => $param->get('celular'),
            'email' => $param->get('email'),
            'site' => $param->get('site'),
            'estado' => $estado[$param->get('estado')],
            'ordem' =>  $param->getInt('id') ? $this->getParam()->getInt("ordem") : $this->getEm()->getRepository($this->getService()->getNameEntity())->buscarUltimaOrdem(),
        );

        return json_encode($this->getService()->save($dados));
    }

    /**
     *
     * @return String
     */
    public function pagination()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoUnidade = $em->getRepository("Entity\Unidade");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repoUnidade->countAll($this->getSession()->get('user'));

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final'))
        );

        //Faz a busca e armazena o total de registros
        //$unidades = $repoUnidade->getBuscaUnidade($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros);
        $unidades = $repoUnidade->getBuscaUnidadeOrder($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros);
        $totalFiltro = $repoUnidade->getTotalBuscaUnidade($filtros);

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($unidades as $unidade) {
            $linha = array();
            
            $linha[] = $this->getFields()->checkbox("unidade[]", $unidade->getId());
            if ($this->verifyPermission('UNIDA_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($unidade->getLabel()), array("href" => "unidade/form/" . $unidade->getId()))
                        . $unidade->getDataCadastro()->format('d/m/Y') . " as " . $unidade->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($unidade->getLabel())
                        . $unidade->getDataCadastro()->format('d/m/Y') . " as " . $unidade->getDataCadastro()->format('H:i');
            }
            
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
        $resultado = $service->delete($ids);

        //Retorna para o js
        return json_encode($resultado);
    }
    
    
     public function ajaxAtualizarOrdenacaoUnidade()
    {
        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach($paramOrdenation as $item){
            $newOrdenation[$item['id']] = (int)$item['ordenacao'];
        }
       
        return json_encode($this->getService()->updateOrdem($newOrdenation));
    }
    

}
