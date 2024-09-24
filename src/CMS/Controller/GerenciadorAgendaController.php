<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use Entity\AgendaDirecao as AgendaDirecaoEntity;
use Helpers\Param;
use CMS\Service\ServiceRepository\AgendaDirecao as AgendaService;
use Entity\Type;
use CMS\Service\WebService\RestSISCAU\UsuarioSistema;

class GerenciadorAgendaController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Gerenciador de Agenda";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var AgendaService
     */
    private $service;

    /**
     *
     * @var AgendaDirecaoEntity
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
     * @return \CMS\Controller\AgendaService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new AgendaService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @return \Entity\AgendaDirecao
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new AgendaDirecaoEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param \CMS\Controller\AgendaService $service
     */
    public function setService(AgendaService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param \Entity\AgendaDirecao $entity
     */
    public function setEntity(AgendaDirecaoEntity $entity)
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
        
        $sites = $this->getEm()->getRepository("Entity\Site")->getSitesPublicados($this->getSession());
        
        $this->tpl->renderView(
            array(
                'titlePage' => $this->getTitle(),
                'status' => $status,
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
        //Instancia o repository
        $em = $this->getEm();
        $repoAgenda = $em->getRepository("Entity\AgendaDirecao");

        //Armazena o parâmetro
        $param = $this->getParam();

        //Busca o total
        $total = $repoAgenda->countAll($this->getSession()->get('user'));

        //Cria os filtros
        $filtros = array(
            "busca" => mb_strtolower($param->get('sSearch'), 'UTF-8'),
            "data_inicial" => $this->getDatetimeFomat()->formatUs($param->get('data_inicial')),
            "data_final" => $this->getDatetimeFomat()->formatUs($param->get('data_final')),
            "status" => $param->get('status'),
            "site" => $param->get('site')
        );
        
        //Faz a busca e armazena o total de registros        
        $agendas = $repoAgenda->buscarRegistros($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
        $totalFiltro = $repoAgenda->buscarRegistros($param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), true);
        $totalFiltro = $totalFiltro[0]['total'];

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($agendas as $agenda) {
            $linha = array();
            $pais = array();

            $linha[] = $this->getFields()->checkbox("agenda[]", $agenda->getId());
            
            $sitesPai = $this->getEm()->find('Entity\AgendaDirecao', $agenda->getId())->getPaiSites();
            foreach ($sitesPai as $pai) {
                $pais[] = $pai->getSigla();
            }
            $siglas = implode(", ", $pais);

            if ($this->verifyPermission('GERAGEND_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($agenda->getLabel()), array("href" => "gerenciadorAgenda/form/" . $agenda->getId())) . 'Criado por ' . $agenda->getLogin() . ' - '.$siglas.' - ' . ' em ' . $agenda->getDataCadastro()->format('d/m/Y') . " as " . $agenda->getDataCadastro()->format('H:i');
            } else {
                $linha[] = $tag->h4($agenda->getLabel()) . $agenda->getDataCadastro()->format('d/m/Y') . " as " . $agenda->getDataCadastro()->format('H:i');
            }            
            
            $linha[] = $agenda->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
            
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
               
        // Consulta os sites disponíveis
        $sites = $this->getEm()->getRepository("Entity\Site")->getSitesPublicados($this->getSession());
        
        // Consulta os usuários do SISCAU        
        $responsaveis = $this->getSession()->get('usuariosPorPermissao');
        if(isset($responsaveis['SEDE_COMPR_INSERIR'])) {
            $responsaveis = $responsaveis['SEDE_COMPR_INSERIR'];
        }
        $tipoResponsaveis = array();
        if(is_array($responsaveis)) {
            foreach ($responsaveis as $item) {
                $tipoResponsaveis[] = new \Entity\Type($item->login, $item->nome);
            }
        }
        
        $agenda = $this->getEm()->getRepository($this->getService()->getNameEntity())->find($id);

        $this->getTpl()->renderView(
            array(
                "data" => new \DateTime("now"),
                "hora" => new \DateTime("now"),
                "agenda" => $agenda,
                "method" => "POST",
                "sites" => $sites,
                "responsaveis" => $tipoResponsaveis,
                "titlePage" => $this->getTitle(),
            )
        );

        return $this->getTpl()->output();
    }

    /**
     * @return \Template\TemplateAmanda
     */
    public function salvar() {        
        $param = $this->getParam();
        $id = $this->getParam()->getInt("id");
        $t = new \DateTime();

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'titulo' => $this->getParam()->get("titulo"),
            'login' => $this->user['dadosUser']['login'],
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
            'sites' => $this->getParam()->get('sites'),
            'responsaveis' => $this->getParam()->get('responsaveis'),
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

        $sites = $param->get('sites');
		
        if(!$param->getInt('id') and !in_array(1, $sites))
        {
            $sites[] = '1';
            $param->setRequestValue('sites', $sites);
        }

        return json_encode($this->getService()->save($dados));
    }
    
    /**
     * @return string
     */
    public function validaSubsiteVinculadoAgenda()
    {
        $repository = $this->getEm()->getRepository('Entity\AgendaDirecao');

        $retorno = $repository->validaVinculo($_REQUEST['id'], 'agenda_direcao');
        
        return json_encode(array('permissao' => $retorno));
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
    
    /**
     * Salva a ordenação das agendas.
     * 
     * @return JSON
     */
    public function salvarOrdenacao()
    {
        $paramOrdenacao = $this->getParam()->get('ordenacao');
        $paramSite = $this->getParam()->get('site');
        
        $novaOrdenacao = array();
        foreach($paramOrdenacao as $item){
            $novaOrdenacao[$item['id']] = (int)$item['ordenacao'];
        }
        
        $return = $this->getEm()
            ->getRepository('Entity\AgendaDirecaoSite')
            ->setOrdem($novaOrdenacao, $paramSite);
        
        return json_encode(array(
            'resultado' => 'ok'
        ));
    }

}
