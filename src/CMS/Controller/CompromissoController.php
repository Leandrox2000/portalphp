<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use Entity\Compromisso as CompromissoEntity;
use Helpers\Param;
use CMS\Service\ServiceRepository\Compromisso as CompromissoService;
use Entity\Type;

class CompromissoController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Cadastro de Compromissos";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var CompromissoService
     */
    private $service;

    /**
     *
     * @var CompromissoEntity
     */
    private $entity;

    /**
     *
     * @var array
     */
    private $user;

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
     * @return \CMS\Controller\CompromissoService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new CompromissoService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return \Entity\Compromisso
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new CompromissoEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param \CMS\Controller\CompromissoService $service
     */
    public function setService(CompromissoService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param \Entity\Compromisso $entity
     */
    public function setEntity(CompromissoEntity $entity)
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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
        
        if($this->verifyPermission('COMPR_VERTODAS')) {
            $agendas = $this->getEm()->getRepository("Entity\AgendaDirecao")->findBy(array(), array('titulo' => 'ASC'));
        } else {
            $agendas = $this->getEm()->getRepository("Entity\AgendaDirecao")->agendasByLogin($this->getSession()->get('user'));            
        }        
        
        $this->tpl->renderView(
            array(
                'titlePage' => $this->getTitle(),
                'status' => $status,
                'agendas' => $agendas
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
        //Instancia o repository
        $em = $this->getEm();
        $repository = $em->getRepository("Entity\Compromisso");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repository->countAll($this->getSession()->get('user'), $this->verifyPermission('COMPR_VERTODAS'));

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status'),
            "agenda" => $param->get('agenda')
        );
        
        //Faz a busca e armazena o total de registros        
        $compromissos = $repository->buscarRegistros($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), false, $this->verifyPermission('COMPR_VERTODAS'));
        $totalFiltro = $repository->buscarRegistros($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), true, $this->verifyPermission('COMPR_VERTODAS'));
        $totalFiltro = $totalFiltro[0]['total'];

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($compromissos as $compromisso) {
            $linha = array();

            $linha[] = $this->getFields()->checkbox("compromisso[]", $compromisso->getId());
            
            if ($this->verifyPermission('COMPR_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($compromisso->getLabel()), array("href" => "compromisso/form/" . $compromisso->getId())) . 'Criado por ' . $compromisso->getLogin() . ' - em ' . $compromisso->getDataCadastro()->format('d/m/Y') . " as " . $compromisso->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($compromisso->getLabel()) . $compromisso->getDataCadastro()->format('d/m/Y') . " as " . $compromisso->getDataCadastro()->format('H:i');
            }            
            
            $linha[] = $compromisso->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
            
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
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        //Verifica o id para passar o título
        if($id == 0){
            $this->setTitle(self::PAGE_TITLE . " - Inserir");
        } else {
            $this->setTitle(self::PAGE_TITLE . " - Alterar");
        }
        
        if($this->verifyPermission('COMPR_VERTODAS')) {
            $agendas = $this->getEm()->getRepository("Entity\AgendaDirecao")->findBy(array(), array('titulo' => 'ASC'));
        } else {
            $agendas = $this->getEm()->getRepository("Entity\AgendaDirecao")->agendasByLogin($this->getSession()->get('user'));
        }       
        $compromisso = $this->getEm()->getRepository($this->getService()->getNameEntity())->find($id);

        $this->getTpl()->renderView(
            array(
                "data" => new \DateTime("now"),
                "hora" => new \DateTime("now"),
                "compromisso" => $compromisso,
                "method" => "POST",
                "agendas" => $agendas,
                "titlePage" => $this->getTitle(),
            )
        );

        return $this->getTpl()->output();
    }

    /**
     * @return \Template\TemplateAmanda
     */
    public function salvar() {
        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'titulo' => $this->getParam()->get("titulo"),
            'local' => $this->getParam()->get("local"),
            'participantes' => $this->getParam()->get("participantes"),
            'observacoes' => $this->getParam()->get("observacoes"),
            'login' => $this->user['dadosUser']['login'],
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
            'compromissoInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('compromissoDtInicial')) . " " . $this->getParam()->get('compromissoHrInicial')),
            'agendasDirecao' => $this->getParam()->get('agendas'),
        );

        $dataFinal = $this->getParam()->get('dataFinal');
        $horaFinal = $this->getParam()->get('horaFinal');
        
        $compromissoDtFinal = $this->getParam()->get('compromissoDtFinal');
        $compromissoHrFinal = $this->getParam()->get('compromissoHrFinal');

        // se a dataFinal não estiver setada ou receber o valor vazio
        // a variável deve ser setada como NULA
        if (!empty($dataFinal) && !empty($horaFinal)) {
            $dados['dataFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($dataFinal) . " " . $horaFinal);
        } else {
            $dados['dataFinal'] = null;
        }
        if (!empty($compromissoDtFinal) && !empty($compromissoHrFinal)) {
            $dados['compromissoFinal'] = new \DateTime($this->getDatetimeFomat()->formatUs($compromissoDtFinal) . " " . $compromissoHrFinal);
        } else {
            $dados['compromissoFinal'] = null;
        }

        return json_encode($this->getService()->save($dados));
    }
    
    /**
     * @return string
     */
    public function delete() {
        $resp = array(
            'error' => array(),
            'response' => 0,
            'success' => ''
        );

        try {
            $ids = $this->getParam()->getArray("sel");

            if(empty($ids)) {
                throw new \Exception('Nenhum registro foi selecionado');
            }

            $resultado = $this->getService()->delete($ids);
            
            if(!isset($resultado['response'])) {
                $resp['response'] = 1;
                $resp['success'] = 'Ação realizada com sucesso!';
            } else {
                $resp = $resultado;
            }
        } catch(\Exception $e) {
            $resp['error'][] = $e->getMessage();
        }
        
        return json_encode($resp);
    }

}
