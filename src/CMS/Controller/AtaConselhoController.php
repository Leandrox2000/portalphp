<?php
namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\Ata as AtaSerice;
use Entity\Ata as AtaEntity;
use Entity\Type;

/**
 * Description of AtaController
 * hgh
 * @author Luciano
 */
class AtaConselhoController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Atas do Conselho";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var AtaSerice
     */
    private $service;

    /**
     *
     * @var AtaEntity
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
     * @return \CMS\Service\ServiceRepository\Ata
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new AtaSerice($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return \Entity\Ata
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new AtaEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Ata $service
     */
    public function setService(AtaSerice $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param \Entity\Ata $entity
     */
    public function setEntity(AtaEntity $entity)
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

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        $ata = $this->getEm()
                ->getRepository($this->getService()->getNameEntity())
                ->find($id);
        $this->getTpl()->renderView(
                array(
                    "data" => new \DateTime("now"),
                    "ata" => $ata,
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

        $repAtas = $this->getEm()
                ->getRepository($this->getService()->getNameEntity());

        $atas = $repAtas->getAtas(
                $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array(
            'status' => $param->get("status"),
            "busca" => $param->get("sSearch"),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get("data_inicial")),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get("data_final")),
                )
        );

        foreach ($atas as $ata) {
            $linha = array();

            $linha[] = $this->getFields()->checkbox("sel[]", $ata->getId());

            if ($this->verifyPermission('ATAS_ALTERAR')) {
                $linha[] = $tag->link(
                        $tag->h4($ata->getLabel()).$ata->getDataCadastro()->format('d/m/Y \\-\\ \\à\\s\\ h:i'), array("href" => "ataConselho/form/" . $ata->getId())
                );
            } else {
                $linha[] =  $tag->h4($ata->getLabel()).$ata->getDataCadastro()->format('d/m/Y \\-\\ \\à\\s\\ h:i');
            }


            $linha[] = $ata->getPublicado() ? $tag->span("Publicado", array('class' => 'publicado')) : $tag->span("Não publicado", array('class' => 'naoPublicado'));

            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $repAtas->getMaxResult();
        $retorno['iTotalRecords'] = $repAtas->countAll();
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
        $descricao = $this->getParam()->getString("descricao");

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'nome' => $this->getParam()->get("nome"),
            'arquivo' => $this->getParam()->getString("arquivoNome"),
            'arquivoAtual' => $this->getParam()->getString("arquivoAtual"),
            'arquivoExcluido' => $this->getParam()->getString("arquivoExcluido"),
            'descricao' => !empty($descricao) ? $descricao : "",
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
            'dataReuniao' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataReuniao')) . " 12:00:00"),
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
