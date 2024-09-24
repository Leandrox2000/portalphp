<?php

namespace CMS\Controller;

use LibraryController\CrudControllerInterface;
use CMS\Service\ServiceRepository\EnderecoRodape as EnderecoRodapeService;
use Entity\EnderecoRodape as EnderecoRodapeEntity;
use Entity\Type;

/**
 * Description of EnderecoRodapeController
 *
 * @author Luciano
 */
class EnderecoRodapeController extends CrudController implements CrudControllerInterface
{

    const PAGE_TITLE = "Endereços Rodapé";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var EnderecoRodapeEntity
     */
    private $entity;

    /**
     *
     * @var EnderecoRodapeService
     */
    private $service;

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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return \EnderecoRodapeEntity
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new EnderecoRodapeEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @return \EnderecoRodapeService
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new EnderecoRodapeService($this->getEm(), $this->getEntity(), $this->getSession()));
        }
        return $this->service;
    }

    /**
     *
     * @param \Entity\Bibliografia $entity
     */
    public function setEntity(EnderecoRodapeEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Bibliografia $service
     */
    public function setService(EnderecoRodapeService $service)
    {
        $this->service = $service;
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
     * @return JSON
     */
    public function delete()
    {
        $array = $this->getParam()->getArray("sel");

        $retorno = $this->getService()->delete($array);

        return json_encode($retorno);
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

        $sites      = $this->user['sede'] ? $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC')) : $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);
        $endereco   = $this->user['sede'] ? $this->getEm()->getRepository($this->getService()->getNameEntity())->find($id) : $this->getEm()->getRepository($this->getService()->getNameEntity())->findByIdSite($id, $this->user['subsites']);

        $this->getTpl()->renderView(
            array(
                "data"      => new \DateTime("now"),
                "hora"      => new \DateTime("now"),
                "endereco"  => $endereco,
                "method"    => "POST",
                "sites"     => $sites,
                "titlePage" => $this->getTitle(),
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
        $sites = $this->user['sede'] ? $this->getEm()->getRepository("Entity\Site")->findBy(array(), array('nome' => 'ASC')) : $this->getEm()->getRepository("Entity\Site")->findIn($this->user['subsites']);



        $this->tpl->renderView(
                array(
                    'titlePage' => $this->getTitle(),
                    'status'    => $status,
                    'sites'     => $sites,
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

        $repEnderecos = $this->getEm()
                                ->getRepository($this->getService()->getNameEntity());

        $enderecos = $repEnderecos->getEnderecos(
                            $param->getInt("iDisplayLength"),
                            $param->getInt("iDisplayStart"),
                            array(
                                'status'        => $param->get("status"),
                                "busca"         => $param->get("sSearch"),
                                "data_inicial"  => $this->getDatetimeFomat()->formatUs($param->get("data_inicial")),
                                "data_final"    => $this->getDatetimeFomat()->formatUs($param->get("data_final")),
                                "site"          => $param->getInt("site"),
                            ),
                            $this->getSession()
                        );

        foreach ($enderecos as $endereco) {
            $linha = array();
            $linha[] = $this->getFields()->checkbox("sel[]", $endereco->getId());
            $label = \Helpers\String::truncateText(strip_tags($endereco->getEndereco()), 100);

            if ($this->verifyPermission('ENDEREC_ALTERAR')) {
                $linha[] = $tag->link(
                    $tag->h4($label),
                    array("href" => "enderecoRodape/form/" . $endereco->getId())
                );
            } else {
                $linha[] = $tag->h4($label);
            }

            $linha[] = $endereco->getPublicado() ? $tag->span("Publicado", array('class' => 'publicado')) : $tag->span("Não publicado", array('class' => 'naoPublicado'));
            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $repEnderecos->getMaxResult();
        $retorno['iTotalRecords'] = $repEnderecos->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
    }

    /**
     *
     * @return JSON
     */
    public function salvar()
    {
        $dados = array(
            'id'            => $this->getParam()->getInt('id'),
            'endereco'      => $this->getParam()->getString("endereco"),
            'dataInicial'   => new \DateTime($this->getDatetimeFomat()->formatUs($this->getParam()->get('dataInicial')) . " " . $this->getParam()->get('horaInicial')),
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
    
    
    
    public function alterarStatusEnderecoRodape()
    {
        $status = $this->getParam()->getInt('status');
        
        //Busca endereço que esteja em vigência
        $temEndereco = $this->getEm()->getRepository($this->getService()->getNameEntity())->getEndereco();
        
        
        if(!$temEndereco or $status == '0'){
        

            $array = ($array == null) ? $this->getParam()->getArray("sel") : $array;
            $status = ($status == null) ? $this->getParam()->getInt("status") : $status;
            $retorno = $this->getService()->alterarStatus($array, $status);

            if ($this->getService() instanceof SolrAwareInterface) {
                foreach ($array as $id) {
                    /* Atualiza índice do Solr */
                    $className = $this->getService()->getNameEntity();
                    $entityName = $this->getEm()->getClassMetadata($className)->getName();
                    $entity = $this->getEm()
                            ->getRepository($entityName)
                            ->find($id);
                    $dadosSolr = $this->getService()->getDadosSolr($entity);
                    $solrManager = new \Helpers\SolrManager();
                    $solrManager->save($dadosSolr);
                }
            }
        }else{
           $error[] = "Já existe um endereço em vigência";
           $retorno = array('error' =>$error , 'response' => null, 'success' => null);
        }

        return json_encode($retorno);
        
    }
    
}
