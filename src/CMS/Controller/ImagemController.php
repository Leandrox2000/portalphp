<?php
namespace CMS\Controller;

use Helpers\Param;
use Entity\Imagem as ImagemEntity;
use CMS\Service\ServiceRepository\Imagem as ImagemService;
use LibraryController\CrudControllerInterface;

/**
 * ImagemController
 *
 * @author join-ti
 */
class ImagemController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Imagens";
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
     * @var ImagemService
     */
    protected $service;

    /**
     *
     * @var ImagemEntity
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
     * @return ImagemService
     */
    public function getService()
    {
        if (!isset($this->service)) {
            $this->service = new ImagemService($this->getEm(), $this->getEntity(), $this->getSession());
        }
        return $this->service;
    }

    /**
     *
     * @param ImagemService $service
     */
    public function setService(ImagemService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @return ImagemEntity
     */
    public function getEntity()
    {
        if (!isset($this->entity))
            $this->entity = new ImagemEntity();
        return $this->entity;
    }

    /**
     *
     * @param ImagemEntity $entity
     */
    public function setEntity(ImagemEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * Utilizado para exibir a página de listagem
     * @return string
     */
    public function lista()
    {
        $categorias = $this->getEm()->getRepository("Entity\ImagemCategoria")->findBy(array(), array('nome' => 'ASC'));
        $pasta = $this->getEm()->getRepository("Entity\ImagemPasta")->findBy(array(), array('nome' => 'ASC'));
        //Busca os anos
        $this->tpl->renderView(array(
            'titlePage' => $this->getTitle(),
            'subTitlePage' => "",
            'categorias' => $categorias,
            'pasta' => $pasta,
            'sites' => $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'))
        ));

        return $this->tpl->output();
    }

    public function collectionToArray($collection)
    {
        $array = array();

        foreach ($collection as $entity) {
            $array[] = new Type($entity->getId(), $entity->getLabel());
        }

        return $array;
    }

    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function formModal($id = 0, $idsImgs = null)
    {
        $this->tpl->addGlobal('modal', true);
        $this->tpl->addGlobal('idsImgs', $idsImgs);
        return $this->form();
    }

    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        //Busca dados da imagem
        $imagem = $this->getEm()->getRepository("Entity\Imagem")->find($id);

        //Verifica o id para passar o título
        if (empty($id)) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
            $pastas = array();
            
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
            $caminhoAtual = $this->getHelperString()->removeSpecial($imagem->getPasta()->getCategoria()->getNome())."/".$imagem->getPasta()->getCaminho()."/";            
            $pastas = $this->getEm()->getRepository('Entity\ImagemPasta')->findBy(array('categoria' => $imagem->getPasta()->getCategoria()->getId()), array('nome' => 'ASC'));            
        }
        
        $this->tpl->addJS('/imagemPasta/pastas.js');


        $this->tpl->renderView(array(
            "data" => date('d/m/Y'),
            "hora" => date('H:i'),
            "titlePage" => $this->getTitle(),
            "imagem" => $imagem,
            "method" => "POST",
            "categorias" => $this->getEm()->getRepository("Entity\ImagemCategoria")->findBy(array(), array('nome' => 'ASC')),
            "pastas" => isset($pastas) ? $pastas : "",
            "caminhoAtual" => isset($caminhoAtual) ? $caminhoAtual : "",
        ));
        return $this->tpl->output();
    }

    public function getImagem() {

        $id = $this->getParam()->get('id');
        $em = $this->getEm();
        $repository = $em->getRepository("Entity\Imagem");
        $imagem = $repository->find($id);

        if ($imagem) {
            $caminho = \Helpers\Image::completePath($imagem);
            return json_encode(array(
                'status' => 1,
                'caminho' => $caminho,
                'credito' => $imagem->getLegenda(),
                'legenda' => $imagem->getCredito(),
            ));
        } else {
            return json_encode(array(
                'status' => 0
            ));
        }
    }

    /**
     *
     * @return String
     */
    public function pagination()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoImagem = $em->getRepository("Entity\Imagem");

        //Busca os parâmetros
        $param = $this->getParam();

        //Busca o total
        $total = $repoImagem->countAll();

        //Organiza o array de filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "categoria" => $param->get('categoria'),
            "pasta" => $param->get('pasta'),
            "data_inicial" => $param->get('data_inicial') != "" ? $this->getDatetimeFomat()->formatUs($param->get('data_inicial')) : "",
            "data_final" => $param->get('data_final') != "" ? $this->getDatetimeFomat()->formatUs($param->get('data_final')) : "",
        );

        //Faz a busca e armazena o total
        $imagens = $repoImagem->getBuscaImagem($param->get('iDisplayLength'), $param->get('iDisplayStart'), $filtros);

        //Busca o total de registros
        $totalFiltro = $repoImagem->getTotalBuscaImagem($filtros);

        //Percorre e organiza o HTML da listagem
        $dados = array();
        foreach ($imagens as $img) {
            $caminho = $this->getHelperString()->removeSpecial($img->getPasta()->getCategoria()->getNome()) . "/" . $img->getPasta()->getCaminho();

            $linha = array();
            $linha[] = "<input type='checkbox' name='imagem[]' value=" . $img->getId() . " class='marcar' />";
            $link = "<div class='photo'><img height='50px' src='uploads/ckfinder/images/{$caminho}/{$img->getImagem()}?date=".mktime()."' /></div>";
            
            if ($this->verifyPermission('IMAGE_ALTERAR')) {
                $link .= "<a href='imagem/form/" . $img->getId() . "'><div class='linksImagem'><strong>Título: </strong>" . $img->getNome()
                        . "<br /><strong>Crédito: </strong>" . $img->getCredito()
                        . "<br /><strong>Categoria: </strong>" . $img->getPasta()->getCategoria()->getNome()
                        . "<br /><strong>Pasta: </strong>" . $img->getPasta()->getNome()
                        . "<br /><div></a>";
            } else {
                $link .= "<div class='linksImagem'><strong>Título: </strong>" . $img->getNome()
                        . "<br /><strong>Crédito: </strong>" . $img->getCredito()
                        . "<br /><strong>Categoria: </strong>" . $img->getPasta()->getCategoria()->getNome()
                        . "<br /><strong>Pasta: </strong>" . $img->getPasta()->getNome()
                        . "<br /><div>";
            }
            $linha[] = $link;

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
     * Requisita a exclusão de registros
     * @return String
     */
    public function delete()
    {
        //Busca o entityManager e o service
        $service = $this->getService();

        //Pega os Ids Enviados
        $ids = $this->getParam()->getArray("sel");

        //Deleta os registros
        $retorno = $service->delete($ids);

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

        $coords = array($param->get('x1'), $param->get('y1'), $param->get('w'), $param->get('h'));

        $dados = array(
            'id' => $id,
            'nome' => $param->get('nome'),
            'credito' => $param->get('credito'),
            'legenda' => $param->get('legenda'),
            'imagem' => $param->getString('imagemNome'),
            'arquivoExcluido' => $param->getString('imagemExcluida'),
            'arquivoAtual' => $param->getString('imagemAtual'),
            'palavrasChave' => $param->getString('palavrasChave'),
            'pasta' => $this->getEm()->getReference('Entity\ImagemPasta', $param->get('pasta')),
            'dadosPasta' => $this->getEm()->getRepository('Entity\ImagemPasta')->find($param->get('pasta')),
            'caminhoAntigo' => $param->getString('caminhoAtual'),
            'coords' => $coords,
        );


        //Faz o insert ou o update e retorna o json
        $retorno = $this->getService()->save($dados);
        return json_encode($retorno);
    }

    /**
     *
     * @param int $categoria
     * @return \Template\TemplateAmanda
     */
    public function imagens($tipo, $categoria, $imgSelecionadas = "")
    {
        $pastas = $this->getEm()->getRepository("Entity\ImagemPasta")->findBy(array(), array('nome' => 'ASC'));
        $categorias = $this->getEm()->getRepository("Entity\ImagemCategoria")->findBy(array(), array('nome' => 'ASC'));

        return $this->getTpl()->renderView(array(
                    'tipo' => $tipo,
                    'categorias' => $categorias,
                    'categoria' => $categoria,
                    'pastas' => $pastas,
                    'imgSelecionadas' => $imgSelecionadas == 0 ? "" : $imgSelecionadas
        ));
    }

    /**
     *
     * @return Método de paginação do colorbox
     */
    public function paginacaoColorbox()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoImagem = $em->getRepository("Entity\Imagem");

        //Armazena a busca de parametro
        $param = $this->getParam();

        //Busca o total
        $total = $repoImagem->countAll();

        //Faz a busca
        $imagens = $repoImagem->getBuscaImagem($param->get('iDisplayLength'), $param->get('iDisplayStart'), array(
            'busca' => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            'pasta' => $param->get('pasta'),
        ));

        //Busca o total de registros
        $totalFiltro = $repoImagem->getTotalBuscaImagem(array(
            'busca' => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            'pasta' => $param->get('pasta'),
        ));

        //Organiza os ids em array
        $ids = explode(',', $param->get('ids'));
        $arrayIds = array();

        foreach ($ids as $id) {
            $arrayIds[$id] = $id;
        }

        //Percorre e organiza o HTML da listagem
        $dados = array();
        foreach ($imagens as $img) {
            $caminho = $this->getHelperString()->removeSpecial($img->getPasta()->getCategoria()->getNome()) . "/" . $img->getPasta()->getCaminho();

            $linha = array();
            $checked = isset($arrayIds[$img->getId()]) ? "checked" : "";

            if ($param->get('tipo') == 'radio') {
                $linha[] = "<input type='radio' name='imagem' value={$img->getId()} class='marcar' {$checked} />";
            } else {
                $linha[] = "<input type='checkbox' name='imagem[]' value={$img->getId()} class='marcar marcar-checkbox' {$checked} />";
            }

            $link = "<div class='photo'><img height='50px' src='uploads/ckfinder/images/{$caminho}/{$img->getImagem()}' /></div>";
            $link .= "<div class='linksImagem'><strong>Título: </strong>{$img->getNome()}<br /><strong>Crédito: </strong>{$img->getCredito()}<br /><strong>Categoria: </strong>{$img->getPasta()->getCategoria()->getNome()}<br /><div>";
            $linha[] = $link;

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
     * @param integer $categoria
     * @return string
     */
    public function carregaPastasSelect($categoria = 0)
    {
        $pastas = $this->getEm()->getRepository('Entity\ImagemPasta')->findBy(array('categoria' => $categoria), array('nome' => 'ASC'));

        $array = array();

        foreach ($pastas as $i => $pasta) {
            $array[$i]['nome'] = $pasta->getNome();
            $array[$i]['id'] = $pasta->getId();
        }

        return json_encode($array);
    }

}

