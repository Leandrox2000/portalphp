<?php

namespace CMS\Controller;

use LibraryController\AbstractController;
use LibraryController\CrudControllerInterface;
use Entity\Agenda as AgendaEntity;
use Helpers\Param;
use CMS\Service\ServiceRepository\Agenda as AgendaService;
use Entity\Type;

/**
 * Description of AgendaController
 *
 * @author Luciano
 */
class AgendaController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Agenda de Eventos";
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
     * @var AgendaEntity
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
     * @return \Entity\Agenda
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new AgendaEntity());
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
     * @param \Entity\Agenda $entity
     */
    public function setEntity(AgendaEntity $entity)
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
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form($id = 0)
    {
        $repository = $this->getEm()->getRepository("Entity\Site");
        $estados = array();
        $estadosArray = array("AC" => "Acre", "AL" => "Alagoas", "AM" => "Amazonas", "AP" => "Amapá", "BA" => "Bahia", "CE" => "Ceará", "DF" => "Distrito Federal", "ES" => "Espírito Santo", "GO" => "Goiás", "MA" => "Maranhão", "MT" => "Mato Grosso", "MS" => "Mato Grosso do Sul", "MG" => "Minas Gerais", "PA" => "Pará", "PB" => "Paraíba", "PR" => "Paraná", "PE" => "Pernambuco", "PI" => "Piauí", "RJ" => "Rio de Janeiro", "RN" => "Rio Grande do Norte", "RO" => "Rondônia", "RS" => "Rio Grande do Sul", "RR" => "Roraima", "SC" => "Santa Catarina", "SE" => "Sergipe", "SP" => "São Paulo", "TO" => "Tocantins");

        //Verifica o id para passar o título
        if($id == 0){
            $this->setTitle("Agenda de Eventos - Inserir");
        }else{
            $this->setTitle("Agenda de Eventos - Alterar");
        }

        foreach ($estadosArray as $key => $value) {
            $estados[] = new \Entity\Type($key, $value);
        }
        
        #REGRA DE PERMISS�O
//        if($this->user['sede']){
//            $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
//        } else {
//            $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
//        }
        
        $sites = $repository->findIn($this->user['subsites']);
        
        #REGRA DE PERMISS�O
//        $evento = $this->user['sede'] ? $this->getEm()->getRepository($this->getService()->getNameEntity())->find($id) : $this->getEm()->getRepository($this->getService()->getNameEntity())->findByIdSite($id, $this->user['subsites']);

        $evento = $this->getEm()->getRepository($this->getService()->getNameEntity())->findByIdSite($id);

        #REGRA DE PERMISS�O

        if ($evento) {
            foreach ($estados as $e) {
                if ($e->getId() == $evento->getUf()) {
                    $estado = new \Entity\Type($e->getId(), $e->getNome());
                }
            }
        }


        $repository = $this->getEm()->getRepository('Entity\Agenda');

        $permissao = $repository->validaVinculo($id, 'agenda');


        $this->getTpl()->renderView(
                array(
                    "data" => new \DateTime("now"),
                    "hora" => new \DateTime("now"),
                    "evento" => $evento,
                    "method" => "POST",
                    "sites" => $sites,
                    "estados" => $estados,
                    "estado" => $estado,
                    "titlePage" => $this->getTitle(),
                    "compartilhado" => $permissao
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
        $status[] = new Type("2", "Compartilhado");
        
        #REGRA DE PERMISS�O
//        if ($this->user['sede']) {
//            $sites = $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC'));
//        } else {
//            $sites = $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
//        }
        $sites = $this->getEm()->getRepository("Entity\Site")->findAll();
        
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
        $repoAgenda = $em->getRepository("Entity\Agenda");

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
        if(!$param->get('site') || in_array($param->get('site'), $_SESSION['user']['subsites'])){
            $agendas = $repoAgenda->buscaRegistroByLogin('Entity\Agenda', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'));
            $totalFiltro = $repoAgenda->buscaRegistroByLogin('Entity\Agenda', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $this->getSession()->get('user'), true);
            
        }else{
            $agendas = $repoAgenda->getDataBySubsite('Entity\Agenda', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'));
            $totalFiltro = $repoAgenda->getDataBySubsite('Entity\Agenda', $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"), $filtros, $param->get('site'), true);
        }

        $totalFiltro = $totalFiltro[0]['total'];
        //$totalFiltro = $repoAgenda->getTotalBusca('Entity\Agenda', $filtros, $this->getSession()->get('user'));

        //Percorre e organiza o HTML da listagem
        $dados = array();
        $tag = $this->getTag();

        foreach ($agendas as $agenda) {
            $linha = array();
            $pais = array();

            $linha[] = $this->getFields()->checkbox("agenda[]", $agenda->getId());
            
            $sitesPai = $this->getEm()->find("Entity\Agenda", $agenda->getId())->getPaiSites();
            foreach ($sitesPai as $pai) {
                $pais[] = $pai->getSigla();
            }
            $siglas = implode(", ", $pais);

            if ($this->verifyPermission('AGEND_ALTERAR')) {
                $linha[] = $tag->link($tag->h4($agenda->getLabel()), array("href" => "agenda/form/" . $agenda->getId())) . 'Criado por ' . $agenda->getLogin() . ' - '.$siglas.' - ' . ' em ' . $agenda->getDataCadastro()->format('d/m/Y') . " as " . $agenda->getDataCadastro()->format('H:i');

            } else {
                $linha[] = $tag->h4($agenda->getLabel()) . $agenda->getDataCadastro()->format('d/m/Y') . " as " . $agenda->getDataCadastro()->format('H:i');
            }
            
            if ($repoAgenda->getCompartilhadosById($agenda->getId()) == 1) {
                $linha[] = "<span class='compartilhado'>Compartilhado de $siglas</span>";
            } else {
                $linha[] = $agenda->getPublicado() ? "<span class='publicado'>Publicado</span>" : "<span class='naoPublicado'>Não publicado</span>";
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
    
    public function validaSubsiteVinculadoAgenda()
    {
        $repository = $this->getEm()->getRepository('Entity\Agenda');

        $retorno = $repository->validaVinculo($_REQUEST['id'], 'agenda');
        
        return json_encode(array('permissao' => $retorno));
    }

    /**
     *
     * @return JSON
     */
    public function salvar()
    {
        $param = $this->getParam();
        $id = $this->getParam()->getInt("id");
        $t = new \DateTime();

        $dados = array(
            'id' => $this->getParam()->getInt('id'),
            'titulo' => $this->getParam()->get("titulo"),
            'login' => $this->user['dadosUser']['login'],
            'periodoInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataPeriodoInicial')) . " " . $this->getParam()->get('horaPeriodoInicial')),
            'periodoFinal' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataPeriodoFinal')) . " " . $this->getParam()->get('horaPeriodoFinal')),
            'ingresso' => $this->getParam()->get("ingresso"),
            'local' => $this->getParam()->get("local"),
            'uf' => $this->getParam()->get("estado"),
            'cidade' => $this->getParam()->get("cidade"),
            'endereco' => $this->getParam()->get("endereco"),
            'bairro' => $this->getParam()->get("bairro"),
            'numero' => $this->getParam()->get("numero"),
            'complemento' => $this->getParam()->get("complemento"),
            'cep' => $this->getParam()->get("cep"),
            'telefone' => $this->getParam()->get("telefone"),
            'celular' => $this->getParam()->get("celular"),
            'site' => $this->getParam()->get("site"),
            'email' => $this->getParam()->get("email"),
            'descricao' => $this->getParam()->getString("descricao"),
            'dataInicial' => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
            'sites' => $this->getParam()->get('sites')
        );

//        if (empty($this->getParam()->getInt('id'))) {
//            $dados['login'] = $this->user['dadosUser']['login'];
//        }

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

    //Compartilha registro com subsites selecionados
    public function compartilhar()
    {
        $error = array();

        try {
            if (!empty($_REQUEST['sites'])) {

                $sites = $_REQUEST['sites'];

                $connection = $this->getEm()->getConnection();

                foreach ($_SESSION['user']['subsites'] as $site) {
                    $connection->query("DELETE FROM tb_agenda_site WHERE id_agenda = {$_REQUEST['id']} AND id_site = {$site}");
                }

                foreach ($sites as $site) {
                    $statment = $connection->prepare("INSERT INTO tb_agenda_site (id_agenda, id_site) VALUES({$_REQUEST['id']}, $site)");

                    $statment->execute();
                }
                $success = "Registro compartilhado com sucesso";
                $response = 1;
            } else {
                $response = 1;
                $success = "Registro descompartilhado com sucesso";
                $this->getService()->deletaCompartilhadosById($_REQUEST['id'], 'agenda');
            }


        } catch(\Exception $exc) {
            $error[] = "Não foi possível executar esta ação";
        }

        return json_encode(array('error' => $error, 'response' => $response, 'success' => $success));
    }

}
