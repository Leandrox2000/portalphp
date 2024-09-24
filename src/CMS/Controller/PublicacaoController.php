<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\PublicacaoCategoria as PublicacaoCategoriaService;
use CMS\Service\ServiceRepository\Publicacao as PublicacaoService;
use Entity\Publicacao as PublicacaoEntity;
use Entity\PublicacaoCategoria as PublicacaoCategoriaEntity;
use Entity\Type;

/**
 * PublicacaoController
 *
 * @author Luciano
 */
class PublicacaoController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Publicações";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var PublicacaoService
     */
    private $service;

    /**
     *
     * @var PublicacaoCategoriaService
     */
    private $serviceCategoria;

    /**
     *
     * @var PublicacaoEntity
     */
    private $entity;

    /**
     *
     * @var PublicacaoCategoriaEntity
     */
    private $entityCategoria;

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
     * @var array
     */
    protected $user;

    /**
     *
     * @param \Template\TemplateInterface $tpl
     * @param \Helpers\Session $session
     */
    public function __construct(\Template\TemplateInterface $tpl, \Helpers\Session $session)
    {
        parent::__construct($tpl, $session);
        $this->setUser($this->getUserSession());
    }

    /**
     *
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param array $user
     */
    public function setUser(array $user)
    {
        $this->user = $user;
    }

    /**
     *
     * @return \CMS\Service\ServiceRepository\Publicacao
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new PublicacaoService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return \CMS\Service\ServiceRepository\PublicacaoCategoria
     */
    public function getServiceCategoria()
    {
        if (empty($this->serviceCategoria)) {
            $this->setServiceCategoria(new PublicacaoCategoriaService($this->getEm(), $this->getEntityCategoria(), $this->getSession()));
        }
        return $this->serviceCategoria;
    }

    /**
     *
     * @return \Entity\Publicacao
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new PublicacaoEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @return \Entity\PublicacaoCategoria
     */
    public function getEntityCategoria()
    {
        if (empty($this->entityCategoria)) {
            $this->setEntityCategoria(new PublicacaoCategoriaEntity());
        }
        return $this->entityCategoria;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Publicacao $service
     */
    public function setService(PublicacaoService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\PublicacaoCategoria $serviceCategoria
     */
    public function setServiceCategoria(PublicacaoCategoriaService $serviceCategoria)
    {
        $this->serviceCategoria = $serviceCategoria;
    }

    /**
     *
     * @param \Entity\Publicacao $entity
     */
    public function setEntity(PublicacaoEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @param \Entity\PublicacaoCategoria $entityCategoria
     */
    public function setEntityCategoria(PublicacaoCategoriaEntity $entityCategoria)
    {
        $this->entityCategoria = $entityCategoria;
    }

    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        $categorias = $this->getEm()
                ->getRepository($this->getServiceCategoria()->getNameEntity())
                ->getCategorias();

        $publicacao = $this->getEm()
                ->getRepository($this->getService()->getNameEntity())
                ->find($id);

        $idImg = "";
        if ($publicacao) {
            $imagem = $this->getHtmlImagens($publicacao->getImagem()->getId());
            $idImg = $publicacao->getImagem()->getId();
        }

        $this->getTpl()->addJS("/publicacao/categorias.js");
//        $this->getTpl()->addCSS("/imagem/imagens.css");
        $this->tpl->addCSS('/publicacao/categorias.css');
//        $this->getTpl()->addJS("/imagem/imagens.js");

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        $this->getTpl()->renderView(
                array(
                    "data" => new \DateTime("now"),
                    "hora" => new \DateTime("now"),
                    "categorias" => $categorias,
                    "publicacao" => $publicacao,
                    "imagem" => isset($imagem) ? $imagem : "",
                    "idImg" => $idImg,
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
    public function adminCategorias()
    {
        $this->setTitle('Categoria Publicações');
        $this->tpl->addJS('/publicacao/categorias.js');
        $this->tpl->addCSS('/publicacao/categorias.css');
        $this->getTpl()->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'categorias' => $this->getTableCategorias()
                )
        );

        return $this->tpl->output();
    }

    /**
     *
     * @return type
     */
    public function lista()
    {
        $status = array();
        $status[] = new Type("0", "Não publicado");
        $status[] = new Type("1", "Publicado");

        $categorias = $this->getEm()
                ->getRepository($this->getServiceCategoria()->getNameEntity())
                ->getCategorias();


        $sites = $this->user['sede'] ? $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC')) : $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);

        $this->tpl->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'status' => $status,
                    'categorias' => $categorias,
                    'sites' => $sites
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

        $repPublicacoes = $this->getEm()
                ->getRepository($this->getService()->getNameEntity());

        $publicacoes = $repPublicacoes->getPublicacoes(
                $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array(
            'categoria' => $param->getInt("categoria"),
            'status' => $param->get("status"),
            "busca" => $param->get("sSearch"),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get("data_inicial")),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get("data_final")),
            "site" => $param->get('site')
                )
        );

        foreach ($publicacoes as $publicacao) {
            $linha = array();

            $linha[] = $this->getFields()->checkbox("sel[]", $publicacao->getId());
            
            if ($this->verifyPermission('PUBLI_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($publicacao->getLabel()), array("href" => "publicacao/form/" . $publicacao->getId())).$publicacao->getCategoria()->getNome();
            } else {
                $linha[] = $tag->h4($publicacao->getLabel()).$publicacao->getCategoria()->getNome();
            }
            
            $linha[] = $publicacao->getPublicado() ? $tag->span("Publicado", array('class' => 'publicado')) : $tag->span("Não publicado", array('class' => 'naoPublicado'));

            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $repPublicacoes->getMaxResult();
        $retorno['iTotalRecords'] = $repPublicacoes->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @return type
     */
    public function salvar()
    {
        $id = $this->getParam()->getInt("id");
        $t = new \DateTime();
        
        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'titulo' => $this->getParam()->get("titulo"),
            'autor' => $this->getParam()->get("autor"),
            'edicao' => $this->getParam()->get("edicao"),
            'paginas' => $this->getParam()->get("paginas"),
            'tipoPublicacao' => $this->getParam()->getInt("tipo_publicacao"),
            'tipoLivraria' => $this->getParam()->getInt("tipo_livraria"),
            'preco' => $this->getParam()->get("preco"),
            'arquivo' => $this->getParam()->getString("arquivoNome"),
            'arquivoAtual' => $this->getParam()->getString("arquivoAtual"),
            'arquivoExcluido' => $this->getParam()->getString("arquivoExcluido"),
            'dataPublicacao' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataPublicacao'))),
            'conteudo' => $this->getParam()->getString("conteudo"),
            'categoria' => $this->getEm()->getReference($this->getServiceCategoria()->getNameEntity(), $this->getParam()->get("categoria")),
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
            'imagem' => $this->getEm()->getReference('Entity\Imagem', $this->getParam()->get('imagemBanco')),
            'ordem' => $id ? $this->getParam()->getInt("ordem") : $this->getEm()->getRepository($this->getService()->getNameEntity())->buscarUltimaOrdem()
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

    /**
     * Retorna as categorias em um formato JSON
     *
     * @return JSON
     */
    public function getCategorias()
    {
        $categorias = $this->getEm()
                ->getRepository($this->getServiceCategoria()->getNameEntity())
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

    /**
     * Retorna a tabela de Categorias
     *
     * @return \Html\Table
     */
    public function getTableCategorias()
    {
        $categorias = $this->getEm()
                ->getRepository($this->getServiceCategoria()->getNameEntity())
                ->getCategorias();

        if ($this->verifyPermission('CADAS_EXCLUIR')) {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(2)->addColumnHeader("Nome", "90%")->addColumnHeader("Excluir", "10%");
        } else {
            $table = $this->getTable()->setId("tableCategorias")->setNumColumns(1)->addColumnHeader("Nome", "100%");
        }

        foreach ($categorias as $categoria) {
            $hidden = "<input type='hidden' name='publicacaoCategoriaId' value='".$categoria->getId()."'>";
            if ($this->verifyPermission('CADAS_SALVAR')) {
                $table->addData(
                        $this->getTag()->link(
                                $categoria->getLabel().$hidden, array('href' => "javascript:editaCategoria({$categoria->getId()})", 'id' => "categoria{$categoria->getId()}")
                        )
                );
            } else {
                $table->addData($categoria->getLabel().$hidden);
            }

            if ($this->verifyPermission('CADAS_EXCLUIR')) {
                $table->addData($this->getButton()->icon("trash", "javascript:excluirCategoria({$categoria->getId()})"), "center");
            }
        }

        return $table;
    }

    /**
     * Salva uma Categoria
     *
     * @return JSON
     */
    public function salvarCategoria()
    {
        $id = $this->getParam()->getInt("id");
        $nome = $this->getParam()->get("nome");
        $descricao = $this->getParam()->get("descricao");

        $result = $this->getServiceCategoria()
                ->save($nome, $descricao, $id);

        return json_encode($result);
    }

    public function obterCategoria($id)
    {
        $categoria = $this->getEm()
                          ->getRepository($this->getServiceCategoria()->getNameEntity())
                          ->find($id);

        if ($categoria) {
            $result = array(
                'id' => $categoria->getId(),
                'nome' => $categoria->getNome(),
                'descricao' => $categoria->getDescricao(),
                'status' => 1,
            );
        } else {
            $result = array('status' => 0);
        }

        return json_encode($result);
    }

    /**
     * Exclui um Categoria
     *
     * @return JSON
     */
    public function excluiCategoria()
    {
        $id = $this->getParam()->getInt("id");
        $result = $this->getServiceCategoria()->delete($id);
        return json_encode($result);
    }

    /**
     *
     * @return \Template\TemplateAmanda
     */
    public function categorias()
    {
        return $this->getTpl()->renderView(array("categorias" => $this->getTableCategorias()));
    }
    
    /**
     *
     * @return JSON
     */
    public function ajaxAtualizarOrdenacao()
    {
        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach($paramOrdenation as $item){
            $newOrdenation[$item['id']] = (int)$item['ordenacao'];
        }
        
        $return = $this->getEm()
            ->getRepository('Entity\PublicacaoCategoria')
            ->setOrdem($newOrdenation);
        
        return json_encode(array(
            'resultado' => 'ok'
        ));
    }
    
    
    
     public function ajaxAtualizarOrdenacaoPublicacao()
    {
        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach($paramOrdenation as $item){
            $newOrdenation[$item['id']] = (int)$item['ordenacao'];
        }
       
        return json_encode($this->getService()->updateOrdem($newOrdenation));
    }
}
