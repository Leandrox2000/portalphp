<?php
namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use Entity\Funcionario as FuncionarioEntity;
use Entity\Cargo as CargoEntity;
use Entity\Vinculo as VinculoEntity;
use CMS\Service\ServiceRepository\Funcionario as FuncionarioService;
use CMS\Service\ServiceRepository\Cargo as CargoService;
use CMS\Service\ServiceRepository\Vinculo as VinculoService;
use Entity\Type;
use Helpers\Param;


/**
 * Description of FuncionarioController
 *
 * @author Join
 */
class FuncionarioController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Funcionários";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     * @var FuncionarioService
     */
    protected $service;

    /**
     * @var CargoService
     */
    protected $serviceCargo;

    /**
     *
     * @var  VinculoService
     */
    protected $serviceVinculo;

    /**
     * @var FuncionarioEntity
     */
    protected $entity;

    /**
     * @var CargoEntity
     */
    protected $entityCargo;

    /**
     *
     * @var VinculoEntity
     */
    protected $entityVinculo;

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
     * @return FuncionarioService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new FuncionarioService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return CargoService
     */
    public function getServiceCargo()
    {
        if (empty($this->serviceCargo)) {
            $this->setServiceCargo(new CargoService($this->getEm(), $this->getEntityCargo(), $this->getSession()));
        }
        return $this->serviceCargo;
    }

    /**
     *
     * @return FuncionarioEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new FuncionarioEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @return CargoEntity
     */
    public function getEntityCargo()
    {
        if (empty($this->entityCargo)) {
            $this->setEntityCargo(new CargoEntity());
        }
        return $this->entityCargo;
    }

    /**
     *
     * @param \CMS\Controller\FuncionarioService $service
     */
    public function setService(FuncionarioService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param \CMS\Controller\CargoService $serviceCargo
     */
    public function setServiceCargo(CargoService $serviceCargo)
    {
        $this->serviceCargo = $serviceCargo;
    }

    /**
     *
     * @return VinculoService
     */
    public function getServiceVinculo()
    {
        if (empty($this->serviceVinculo)) {
            $this->setServiceVinculo(new VinculoService($this->getEm(), $this->getEntityVinculo(), $this->getSession()));
        }

        return $this->serviceVinculo;
    }

    /**
     *
     * @return VinculoEntity
     */
    public function getEntityVinculo()
    {
        if (empty($this->entityVinculo)) {
            $this->setEntityVinculo(new VinculoEntity());
        }
        return $this->entityVinculo;
    }

    /**
     *
     * @param VinculoService $serviceVinculo
     */
    public function setServiceVinculo(VinculoService $serviceVinculo)
    {

        $this->serviceVinculo = $serviceVinculo;
    }

    /**
     *
     * @param VinculoEntity $entityVinculo
     */
    public function setEntityVinculo(VinculoEntity $entityVinculo)
    {
        $this->entityVinculo = $entityVinculo;
    }

    /**
     *
     * @param \CMS\Controller\FuncionarioEntity $entity
     */
    public function setEntity(FuncionarioEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @param \CMS\Controller\CargoEntity $entityCargo
     */
    public function setEntityCargo(CargoEntity $entityCargo)
    {
        $this->entityCargo = $entityCargo;
    }

    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        $cargos = $this->getEm()->getRepository('Entity\Cargo')->findBy(array(), array('cargo' => 'ASC'));
        $unidades = $this->getEm()->getRepository('Entity\Unidade')->findBy(array(), array('nome' => 'ASC'));
        $vinculos = $this->getEm()->getRepository('Entity\Vinculo')->findBy(array(), array('nome' => 'ASC'));
        $funcionario = $this->getEm()->find($this->getService()->getNameEntity(), $id);

        $tiposDiretoria = array();

        $tiposDiretoria[] = new Type(1, "Sim");
        $tiposDiretoria[] = new Type(0, "Não");

        $this->tpl->addJS("/funcionario/cargos.js");
        $this->tpl->addJS("/funcionario/vinculos.js");

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        $this->tpl->renderView(array(
            "data" => new \DateTime('now'),
            "cargos" => $cargos,
            "unidades" => $unidades,
            "vinculos" => $vinculos,
            "funcionario" => $funcionario,
            "method" => "POST",
            "titlePage" => $this->getTitle(),
            "tiposDiretoria" => $tiposDiretoria,
        ));

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
        $repoFuncionario = $em->getRepository("Entity\Funcionario");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repoFuncionario->countAll();

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status')
        );

        //Faz a busca e armazena o total de registros
        $funcionarios = $repoFuncionario->getBuscaFuncionario($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros);
        $totalFiltro = $repoFuncionario->getTotalBuscaFuncionario($filtros);

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($funcionarios as $funcionario) {
            $linha = array();
            $linha[] = $this->getFields()->checkbox("funcionario[]", $funcionario->getId());

            if ($this->verifyPermission('FUNCI_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($funcionario->getLabel()), array("href" => "funcionario/form/" . $funcionario->getId())) . $funcionario->getDataCadastro()->format('d/m/Y') . " as " . $funcionario->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($funcionario->getLabel()) . $funcionario->getDataCadastro()->format('d/m/Y') . " as " . $funcionario->getDataCadastro()->format('H:i');
            }

            $linha[] = $funcionario->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
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
        $idCargo = $param->get("cargo");

        $dados = array(
            'id' => $id,
            'nome' => $param->get("nome"),
            'email' => $param->get("email"),
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($param->get('data_inicial'), 'Y-m-d') . " " . $param->get('hora_inicial')),
            'unidade' => $param->get("unidade"),
            'cargo' => !empty($idCargo) ? $this->getEm()->getReference('Entity\Cargo', $idCargo) : null,
            'unidade' => $this->getEm()->getReference('Entity\Unidade', $param->get("unidade")),
            'vinculo' => $this->getEm()->getReference('Entity\Vinculo', $param->get("vinculo")),
            'imagem' => $param->getString("imagemNome"),
            'exibirPortal' => $param->getInt("exibir_portal"),
            'exibirIntranet' => $param->getInt("exibir_intranet"),
            'imagemExcluida' => $param->getString("imagemExcluida"),
            'imagemAtual' => $param->getString("imagemAtual"),
            'curriculo' => $param->getString("curriculo"),
            'diretoria' => $param->getInt("diretoria"),
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
     * Retorna os cargos em um formato JSON
     *
     * @return JSON
     */
    public function getCargos()
    {
        $cargos = $this->getEm()
                ->getRepository($this->getServiceCargo()->getNameEntity())
                ->getCargos();
        $dados = array();

        foreach ($cargos as $cargo) {
            $linha = array();

            $linha['id'] = $cargo->getId();
            $linha['label'] = $cargo->getLabel();

            $dados[] = $linha;
        }

        return json_encode($dados);
    }

    /**
     * Retorna a tabela de Cargos
     *
     * @return \Html\Table
     */
    public function getTableCargos()
    {
        $button = $this->getButton();
        $tag = $this->getTag();
        $cargos = $this->getEm()
                ->getRepository($this->getServiceCargo()->getNameEntity())
                ->getCargos();

        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tableCargos")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()->setId("tableCargos")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }

        foreach ($cargos as $cargo) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                        $tag::link(
                                $cargo->getLabel(), array('href' => "javascript:editaCargo({$cargo->getId()})", 'id' => "cargo{$cargo->getId()}")
                        )
                );
            } else {
                $table->addData($cargo->getLabel());
            }

            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData(
                        $button->icon("trash", "javascript:excluirCargo({$cargo->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Salva um cargo
     *
     * @return JSON
     */
    public function salvarCargo()
    {
        $param = $this->getParam();
        $id = $param->getInt("id");
        $nome = $param->get("nome");

        $result = $this->getServiceCargo()
                ->save($nome, $id);

        return json_encode($result);
    }

    /**
     * Exclui um Cargo
     *
     * @return JSON
     */
    public function excluiCargo()
    {
        $id = $this->getParam()->getInt("id");

        $result = $this->getServiceCargo()
                ->delete($id);

        return json_encode($result);
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function cargos()
    {
        return $this->getTpl()->renderView(array("cargos" => $this->getTableCargos()));
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function adminCargos()
    {
        $this->tpl->addJS('/funcionario/cargos.js');

        $this->getTpl()->renderView(
                array(
                    'titlePage' => 'Cargo/Função',
                    'status' => $this->getTableCargos(),
                )
        );

        return $this->tpl->output();
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function adminVinculos()
    {
        $this->tpl->addJS('/funcionario/vinculos.js');
        $this->getTpl()->renderView(
                array(
                    'titlePage' => 'Vínculo funcionários',
                    'status' => $this->getTableVinculo(),
                )
        );

        return $this->tpl->output();
    }

    /**
     * Retorna os cargos em um formato JSON
     *
     * @return JSON
     */
    public function getVinculos()
    {
        $vinculos = $this->getEm()
                ->getRepository($this->getServiceVinculo()->getNameEntity())
                ->getVinculos();
        $dados = array();

        foreach ($vinculos as $vinculo) {
            $linha = array();

            $linha['id'] = $vinculo->getId();
            $linha['label'] = $vinculo->getLabel();

            $dados[] = $linha;
        }

        return json_encode($dados);
    }

    /**
     * Retorna a tabela de Vínculo
     *
     * @return \Html\Table
     */
    public function getTableVinculo()
    {
        $button = $this->getButton();
        $tag = $this->getTag();
        $vinculos = $this->getEm()->getRepository($this->getServiceVinculo()->getNameEntity())->getVinculos();

        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tableVinculos")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        }else{
            $table = $this->getTable()->setId("tableVinculos")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }

        foreach ($vinculos as $vinculo) {
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                        $tag::link(
                                $vinculo->getLabel(), array('href' => "javascript:editaVinculo({$vinculo->getId()})", 'id' => "vinculo{$vinculo->getId()}")
                        )
                );
            } else {
                $table->addData($vinculo->getLabel());
            }

            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData(
                        $button->icon("trash", "javascript:excluirVinculo({$vinculo->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Salva um cargo
     *
     * @return JSON
     */
    public function salvarVinculo()
    {
        $param = $this->getParam();
        $id = $param->getInt("id");
        $nome = $param->get("nome");

        $result = $this->getServiceVinculo()->save($nome, $id);

        return json_encode($result);
    }

    /**
     * Exclui um Cargo
     *
     * @return JSON
     */
    public function excluiVinculo()
    {
        $id = $this->getParam()->getInt("id");
        $result = $this->getServiceVinculo()->delete($id);
        return json_encode($result);
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function vinculos()
    {
        return $this->getTpl()->renderView(array("vinculos" => $this->getTableVinculo()));
    }

    /**
     * Retorna os cargos em um formato JSON
     *
     * @return JSON
     */
    public function getUnidades()
    {
        $unidades = $this->getEm()->getRepository('Entity\Unidade')->findAll();
        $dados = array();

        foreach ($unidades as $unidade) {
            $linha = array();

            $linha['id'] = $unidade->getId();
            $linha['label'] = $unidade->getLabel();

            $dados[] = $linha;
        }

        return json_encode($dados);
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
    
}
