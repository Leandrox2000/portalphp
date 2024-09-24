<?php
namespace LibraryController;

use Template\TemplateInterface;
use Doctrine\ORM\EntityManager;
use Factory\EntityManagerFactory as EM;
use Html\Button;
use Html\Table;
use Html\Tag;
use Html\Fields;
use Helpers\Param;
use Helpers\DatetimeFormat;
use Helpers\Session;

/**
 * Classse Pai de todos os Controllers
 *
 * @author Luciano
 */
abstract class AbstractController
{

    /**
     *
     * @var \Template\TemplateInterface
     */
    protected $tpl;

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var Button
     */
    protected $button;

    /**
     * @var Tag
     */
    protected $tag;

    /**
     * @var Fields
     */
    protected $fields;

    /**
     * @var Param
     */
    protected $param;

    /**
     *
     * @var DatetimeFormat
     */
    protected $datetimeFomat;

    /**
     *
     * @var Session 
     */
    protected $session;

    /**
     * 
     * @param \Template\TemplateInterface $tpl
     * @param \Helpers\Session $session
     */
    public function __construct(TemplateInterface $tpl, Session $session = NULL)
    {
        $this->setTpl($tpl);
        $this->setSession($session);
    }
 
    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm()
    {
        if (is_null($this->em)) {
            $this->setEm(EM::getEntityManger());
        }
        return $this->em;
    }

    /**
     * 
     * @return \Template\TemplateInterface
     */
    public function getTpl()
    {
        return $this->tpl;
    }

    /**
     * 
     * @return Table
     */
    protected function getTable()
    {
        if (is_null($this->table)) {
            $this->setTable(new Table());
        }
        return $this->table;
    }

    /**
     * 
     * @return Button
     */
    protected function getButton()
    {
        if (is_null($this->button)) {
            $this->setButton(new Button());
        }
        return $this->button;
    }

    /**
     * 
     * @return Tag
     */
    protected function getTag()
    {
        if (is_null($this->tag)) {
            $this->setTag(new Tag);
        }
        return $this->tag;
    }

    /**
     * 
     * @return Fields
     */
    protected function getFields()
    {
        if (is_null($this->fields)) {
            $this->setFields(new Fields());
        }
        return $this->fields;
    }

    /**
     * 
     * @return Tag
     */
    protected function getParam()
    {
        if (is_null($this->param)) {
            $this->setParam(new Param);
        }
        return $this->param;
    }

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEm(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * 
     * @param \Template\TemplateInterface $tpl
     */
    public function setTpl(\Template\TemplateInterface $tpl)
    {
        $this->tpl = $tpl;
    }

    /**
     * 
     * @param Table $table
     */
    protected function setTable(Table $table)
    {
        $this->table = $table;
    }

    /**
     * 
     * @param Button $button
     */
    protected function setButton(Button $button)
    {
        $this->button = $button;
    }

    /**
     * 
     * @param Tag $tag
     */
    protected function setTag(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * 
     * @param Fields $fields
     */
    protected function setFields(Fields $fields)
    {
        $this->fields = $fields;
    }

    /**
     * 
     * @param Param $param
     */
    protected function setParam(Param $param)
    {
        $this->param = $param;
    }

    /**
     * Seta os Arquivos
     * 
     */
    final public function setLayoutPage($file)
    {
        $fileView = $file . ".html.twig";
        $fileJS = $file . ".js";
        $fileCSS = $file . ".css";

        if ($this->viewExists($fileView)) {
            $this->setView($fileView);
        }
        if ($this->jsExists($fileJS)) {
            $this->setJS($fileJS);
        }
        if ($this->cssExists($fileCSS)) {
            $this->setCSS($fileCSS);
        }
    }

    /**
     * Seta  a pagina que será carregada.
     * Se não for setada carrega a páigna default do Tempalte
     * 
     * @param type $view
     */
    public function setView($view)
    {
        $this->tpl->setView($view);
    }

    /**
     * 
     * @param type $js
     */
    public function setJS($js)
    {
        $js = "/" . trim($js, "/");
        $this->tpl->addJS($js);
    }

    /**
     * 
     * @param type $css
     */
    public function setCSS($css)
    {
        $css = "/" . trim($css, "/");
        $this->tpl->addCSS($css);
    }

    /**
     * 
     * @return DatetimeFormat
     */
    public function getDatetimeFomat()
    {
        if (!isset($this->datetimeFomat))
            $this->datetimeFomat = new DatetimeFormat();
        return $this->datetimeFomat;
    }

    /**
     * 
     * @return \Helpers\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * 
     * @param \Helpers\Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * 
     * @param DatetimeFormat $datetimeFomat
     */
    public function setDatetimeFomat(DatetimeFormat $datetimeFomat)
    {
        $this->datetimeFomat = $datetimeFomat;
    }

    /**
     * Verifica se a Página tem View própria
     * 
     * @param string $view Caminha da VIEW
     * @return Boolean
     */
    public function viewExists($view)
    {
        return $this->tpl->viewExists($view);
    }

    /**
     * Verifica se a Página tem JS próprio
     * 
     * @param string $js Caminha do JS
     * @return Boolean
     */
    public function jsExists($js)
    {
        return $this->tpl->jsExists($js);
    }

    /**
     * Verifica se a página tem CSS Próprio
     * 
     * @param string $css Caminha do CSS
     * @return Boolean
     */
    public function cssExists($css)
    {
        return $this->tpl->cssExists($css);
    }

    /**
     * 
     * @param type $name
     * @param type $arguments
     */
    public function __call($name, $arguments)
    {
        echo $name;
    }

    /**
     * 
     * @param string $ids
     * @return string
     */
    public function getHtmlImagens($ids = "")
    {
        if (!empty($ids)) {
            //Organiza os ids em imagens
            $ids = explode(',', $ids);

            //Busca as imagens
            $imagens = $this->getEm()->getRepository('Entity\Imagem')->getImagemIds($ids);

            //Cria o html com as imagens
            $html = "";

            $html .= "<div class='gallerywrapper'>";
            $html .= "<ul  class='imagelist'>";

            //Percorre as imagens e monta o HTML
            foreach ($imagens as $img) {
                $html .= "<li id='img{$img->getId()}' >";
                $html .= "<img src='uploads/ckfinder/images/{$img->getImagem()}' />";
                $html .= "<span><a class='delete' href='javascript:excluirImagem({$img->getId()})'></a></span>";
                $html .= "</li>";
            }

            $html .= "</ul>";
            $html .= "</div>";


            return $html;
        } else {
            return '';
        }
    }

    abstract public function getDefaultAction();

    abstract public function getTitle();
}
