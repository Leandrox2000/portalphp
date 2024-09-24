<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\Biblioteca as BibliotecaService;
use Entity\Biblioteca as BibliotecaEntity;
use Entity\Imagem as ImagemEntity;
use Entity\Type;


/**
 * Description of BibliotecaController
 *
 * @author Luciano
 */
class BibliotecaController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Bibliotecas do IPHAN";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var BibliotecaService
     */
    private $service;

    /**
     *
     * @var BibliotecaEntity
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
     * @return \CMS\Service\ServiceRepository\Biblioteca
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new BibliotecaService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return \Entity\Biblioteca
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new BibliotecaEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Biblioteca $service
     */
    public function setService(BibliotecaService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param BibliotecaEntity $entity
     */
    public function setEntity(BibliotecaEntity $entity)
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
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {

        $redes = array();
        $redes[] = new Type("facebook", "Facebook");
        $redes[] = new Type("twitter", "Twitter");
        $redes[] = new Type("instagram", "Instagram");
        $redes[] = new Type("googleplus", "Google+");
        $redes[] = new Type("outros", "Outros");

        $estados = array();
        $estadosArray = array(
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

        foreach ($estadosArray as $key => $value) {
            $estados[] = new \Entity\Type($key, $value);
        }

        $biblioteca = $this->getEm()->getRepository($this->getService()->getNameEntity())->find($id);

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
            $uf = new Type($biblioteca->getUf(), $estadosArray[$biblioteca->getUf()]);
        }


        //Adiciona o css e o js da seleção de imagens
//        $this->tpl->addJS("/imagem/imagens.js");
//        $this->tpl->addCSS("/imagem/imagens.css");

        $this->getTpl()->renderView(
                array(
                    "data" => new \DateTime("now"),
                    "biblioteca" => $biblioteca,
                    "method" => "POST",
                    "titlePage" => $this->getTitle(),
                    "redesSociais" => $redes,
                    'estados' => $estados,
                    "uf" => isset($uf) ? $uf : "",
                    "imagem" => $biblioteca ? $this->getHtmlImagens($biblioteca->getImagem()->getId()) : "",
                    "idImg" => $biblioteca ? $biblioteca->getImagem()->getId() : ""
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

        $repoBiblioteca = $this->getEm()->getRepository("Entity\Biblioteca");

        $filtros = array(
            "status" => $param->get("status"),
            "busca" => $param->get("sSearch"),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get("data_inicial")),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get("data_final")),
        );

        $bibliotecas = $repoBiblioteca->getBuscarBibliotecaOrder($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros);

        foreach ($bibliotecas as $biblioteca) {
            $linha = array();

            $linha[] = $this->getFields()->checkbox("biblioteca[]", $biblioteca->getId());

            if ($this->verifyPermission('BIPHAN_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($biblioteca->getLabel()), array("href" => "biblioteca/form/" . $biblioteca->getId())) . $biblioteca->getDataCadastro()->format('d/m/Y') . " as " . $biblioteca->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($biblioteca->getLabel()) . $biblioteca->getDataCadastro()->format('d/m/Y') . " as " . $biblioteca->getDataCadastro()->format('H:i');
            }



            $linha[] = $biblioteca->getPublicado() ? $tag->span("Publicado", array('class' => 'publicado')) : $tag->span("Não publicado", array('class' => 'naoPublicado'));

            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $repoBiblioteca->getTotalBiblioteca($filtros);
        $retorno['iTotalRecords'] = $repoBiblioteca->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @return JSON
     */
    public function salvar()
    {
        $dataInicial = $this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial'));
        $horaInicial = $this->getParam()->get('horaInicial');

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'nome' => $this->getParam()->get('nome'),
            'responsavel' => $this->getParam()->getString('responsavel'),
            'cidade' => $this->getParam()->getString('cidade'),
            'uf' => $this->getParam()->getString('uf'),
            'cep' => $this->getParam()->getString('cep'),
            'endereco' => $this->getParam()->getString('endereco'),
            'numero' => $this->getParam()->getString('numero'),
            'complemento' => $this->getParam()->getString('complemento'),
            'bairro' => $this->getParam()->getString('bairro'),
            'telefone' => $this->getParam()->getString('telefone'),
            'celular' => $this->getParam()->getString('celular'),
            'email' => $this->getParam()->getString('email'),
            'horarioFuncionamento' => $this->getParam()->getString('horarioFuncionamento'),
            'descricao' => $this->getParam()->getString('descricao'),
            'dataInicial' => new \DateTime($dataInicial . " " . $horaInicial),
            'redeSocial' => $this->getParam()->getArray('redeSocial'),
            'url' => $this->getParam()->getArray('url'),
            'imagem' => $this->getEm()->getReference('Entity\Imagem', $this->getParam()->getInt('imagemBanco')),
            'ordem' => $this->getParam()->getInt('id') ? $this->getParam()->getInt("ordem") : $this->getEm()->getRepository($this->getService()->getNameEntity())->buscarUltimaOrdem()
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
    
      public function ajaxAtualizarOrdenacaoBiblioteca()
    {
        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach($paramOrdenation as $item){
            $newOrdenation[$item['id']] = (int)$item['ordenacao'];
        }
       
        return json_encode($this->getService()->updateOrdem($newOrdenation));
    }
    

}
