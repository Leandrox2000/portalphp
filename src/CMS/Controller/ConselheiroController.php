<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\Conselheiro as ConselheiroService;
use Entity\Conselheiro as ConselheiroEntity;
use Entity\Type;

/**
 * Description of ConselheiroController
 *
 * @author Luciano
 */
class ConselheiroController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Conselheiros";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var ConselheiroService
     */
    private $service;

    /**
     *
     * @var ConselheiroEntity
     */
    private $entity;

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
     * @return \CMS\Service\ServiceRepository\Conselheiro
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new ConselheiroService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return \Entity\Conselheiro
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new ConselheiroEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Conselheiro $service
     */
    public function setService(ConselheiroService $service)
    {
        $this->service = $service;
        $this->service->setSession($this->getSession());
    }

    /**
     *
     * @param \Entity\Conselheiro $entity
     */
    public function setEntity(ConselheiroEntity $entity)
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
        $tipos = array();

        //Verifica o id para passar o título
        if ($id == 0) {
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }

        $tipos[] = new \Entity\Type("E", "Efetivo");
        $tipos[] = new \Entity\Type("S", "Suplente");

        $conselheiro = $this->getEm()
                ->getRepository($this->getService()->getNameEntity())
                ->find($id);
        $this->getTpl()->renderView(
                array(
                    "data" => new \DateTime("now"),
                    "hora" => new \DateTime("now"),
                    "conselheiro" => $conselheiro,
                    "method" => "POST",
                    "tipos" => $tipos,
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

        $repConselheiros = $this->getEm()
                ->getRepository($this->getService()->getNameEntity());

        $conselheiros = $repConselheiros->getConselheiros(
            $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), array(
                'status' => $param->get("status"),
                "busca" => $param->get("sSearch"),
                "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get("data_inicial")),
                "data_final" => $this->getDatetimeFomat()->formatUs($param->get("data_final")),
            )
        );

        foreach ($conselheiros as $conselheiro) {
            $linha = array();
            $linha[] = $this->getFields()->checkbox("sel[]", $conselheiro->getId());
            
            if ($this->verifyPermission('MEMBR_ALTERAR')) {
                $linha[] = $tag->link(
                    $tag->h4($conselheiro->getLabel()), array("href" => "conselheiro/form/" . $conselheiro->getId())
                ).'<input type="hidden" class="ordenacao_registro" name="ordenacao_registro" data-id="'.$conselheiro->getId().'">';
            } else {
                $linha[] = $tag->h4($conselheiro->getLabel());
            }
            
            if ($conselheiro->getPublicado()) {
                $linha[] = $tag->span("Publicado", array('class' => 'publicado'));
            } else {
                $linha[] = $tag->span("Não publicado", array('class' => 'naoPublicado'));
            }

            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $repConselheiros->getMaxResult();
        $retorno['iTotalRecords'] = $repConselheiros->countAll();
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

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'nome' => $this->getParam()->get("nome"),
            'instituicao' => $this->getParam()->get("instituicao"),
            'tipo' => $this->getParam()->get("tipo"),
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
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
     * @return string JSON
     */
    public function ajaxAtualizarOrdenacao()
    {
        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach($paramOrdenation as $item){
            $newOrdenation[$item['id']] = (int)$item['ordenacao'];
        }
        
        $return = $this->getEm()
            ->getRepository($this->getService()->getNameEntity())
            ->setOrdem($newOrdenation);
        
        return json_encode(array(
            'resultado' => 'ok'
        ));
    }
    
    /**
     * Valida a ordenação com base em duas listas de ordenações
     *
     * @param arrray $newOrdenation Somente os itens que tiveram sua ordenação alterada (info. POST/GET).
     * @param array $oldOrdenation Itens que estavam armazenados no banco de dados.
     * @return array|boolean Itens inválidos ou verdadeiro se válido.
     */
    public function validateOrdenation($newOrdenation, $oldOrdenation)
    {
        $equal_ids = array();
        $oldArray = array();

        // Converte oldOrdenation para o mesmo formato de newOrdenation
        foreach ($oldOrdenation as $oldOrdenationItem) {
            if ($oldOrdenationItem->getDiretor()) {
                $oldArray[$oldOrdenationItem->getId()] = $oldOrdenationItem->getDiretor()->getOrdem();
            }
        }
        $oldOrdenation = $oldArray;

        // Compara nova ordenação com itens no banco de dados
        foreach ($newOrdenation as $nkey => $newOrdenationItem) {
            foreach ($oldOrdenation as  $okey => $oldOrdenationItem) {
                // Se é o mesmo item pula para a próxima iteração
                if ($nkey == $okey) {
                    continue;
                }

                // Se foi modificado não é necessário comparar nesse primeiro momento
                // pois vai ser comparado com os itens irmãos
                if(in_array($okey, array_keys($newOrdenation))) {
                    continue;
                }

                // Se é a mesma ordenação
                if ($newOrdenationItem == $oldOrdenationItem) {
                    $equal_ids[] = $okey;
                    $equal_ids[] = $nkey;
                }
            }
        }

        // Compara os itens irmãos
        foreach ($newOrdenation as $nkey => $newOrdenationItem) {
            foreach ($newOrdenation as $nbkey => $newBrotherOrdenationItem) {
                // Se é o mesmo item ou veio sem ordenação preenchida
                if ($nkey == $nbkey || empty($newOrdenationItem)) {
                    continue;
                }

                // Se é a mesma ordenação
                if ($newOrdenationItem == $newBrotherOrdenationItem) {
                    $equal_ids[] = $nkey;
                    $equal_ids[] = $nbkey;
                }
            }
        }

        if (count($equal_ids) > 0) {
            return $equal_ids;
        } else {
            return true;
        }
    }

}
