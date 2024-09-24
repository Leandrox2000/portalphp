<?php

namespace CMS\Controller;

use CMS\Service\ServiceRepository\Diretoria as DiretoriaService;
use Entity\Diretoria as DiretoriaEntity;
use Entity\Type;

/**
 * Description of Diretoria
 */
class DiretoriaController extends CrudController
{

    const PAGE_TITLE = "Diretoria";
    const DEFAULT_ACTION = "lista";

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var DiretoriaService
     */
    private $service;

    /**
     *
     * @var DiretoriaEntity
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
     * @return \CMS\Service\ServiceRepository\Diretoria
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(new DiretoriaService($this->getEm(), $this->getEntity(), $this->getSession()));
        }

        return $this->service;
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
     * @return \Entity\Diretoria
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new DiretoriaEntity());
        }

        return $this->entity;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Diretoria $service
     */
    public function setService(DiretoriaService $service)
    {
        $this->service = $service;
        $this->service->setSession($this->getSession());
    }

    /**
     *
     * @param \Entity\Diretoria $entity
     */
    public function setEntity(DiretoriaEntity $entity)
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
     * @return JSON
     */
    public function alterarStatus()
    {
        $array = $this->getParam()->getArray("sel");
        $status = $this->getParam()->getInt("status");
        $retorno = $this->getService()->alterarStatus($array, $status);

        return json_encode($retorno);
    }

    /**
     * @return string JSON
     */
    public function ajaxAtualizarOrdenacaoOld()
    {
        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach ($paramOrdenation as $item) {
            $newOrdenation[$item['id']] = $item['ordenacao'];
        }
        $oldOrdenation = $this->getEm()->getRepository('Entity\Funcionario')->findBy(array(), array('nome' => 'ASC'));
        $repository = $this->getEm()->getRepository('Entity\Funcionario');
        $resultado = $this->validateOrdenation($newOrdenation, $oldOrdenation);

        if ($resultado === TRUE) {
            // ** Atualiza as entidades **

            //Inicia a transação
            $this->getEm()->beginTransaction();
            foreach ($newOrdenation as $id => $ordenacao) {
                // Se vazio
                if (empty($ordenacao)) {
                    continue;
                }
                $funcionario = $repository->find($id);
                if ($funcionario->getDiretor()) {
                    $funcionario->getDiretor()->setOrdem($ordenacao);
                } else {
                    $diretor = new \Entity\Diretoria();
                    $diretor->setOrdem($ordenacao);
                    $diretor->setFuncionario($funcionario);
                    $this->getEm()->persist($diretor);

                    $funcionario->setDiretor($diretor);
                }
                $this->getEm()->persist($funcionario);
            }

            // Salva
            $this->getEm()->flush();

            // Finaliza a transação
            $this->getEm()->commit();

            return json_encode(array(
                'resultado' => 'ok',
            ));
        } else {
            return json_encode(array(
                'resultado' => 'erro',
                'equals' => $resultado,
            ));
        }
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
        $repository = $this->getEm()->getRepository($this->getService()->getNameEntity());

        $results = $repository->getDiretores(
            $param->getInt("iDisplayLength"), $param->getInt("iDisplayStart"),
            array(
                'status' => $param->get("status"),
                "busca" => $param->get("sSearch"),
            ),
            $this->getSession()
        );

        foreach ($results as $result) {
            $linha = array();
            $linha[] = $this->getFields()->checkbox("sel[]", $result->getId());
            $linha[] = $tag->h4($result->getLabel() . ' - ' . $result->getCargo());
            $numOrdem = is_object($result->getDiretor()) ? $result->getDiretor()->getOrdem() : null;
            $publicado = is_object($result->getDiretor()) ? $result->getDiretor()->getPublicado() : 0;
//            $linha[] = '<input type="text" value="' . $numOrdem . '" class="ordenacao_registro sonumero" name="ordenacao_registro" data-id="' . $result->getId() . '">';

            if ($publicado) {
                $linha[] = $tag->span("Publicado", array('class' => 'publicado'));
            } else {
                $linha[] = $tag->span("Não publicado", array('class' => 'naoPublicado'));
            }

            $dados[] = $linha;
        }

        $retorno['sEcho'] = $this->getParam()->getInt("sEcho");
        $retorno['iTotalDisplayRecords'] = $repository->getMaxResult();
        $retorno['iTotalRecords'] = $repository->countAll();
        $retorno['aaData'] = $dados;

        return json_encode($retorno);
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

    /**
     * @return string JSON
     */
    public function ajaxAtualizarOrdenacao()
    {
        if(!$this->verifyPermission('DIRET_ALTPOS')){
            die('Acesso negado');
        }

        $paramOrdenation = $this->getParam()->get('ordenacao');
        $newOrdenation = array();
        foreach ($paramOrdenation as $item) {
            $newOrdenation[$item['id']] = $item['ordenacao'];
        }
        $repository = $this->getEm()->getRepository('Entity\Funcionario');
        foreach ($newOrdenation as $id => $ordenacao) {
            if (empty($ordenacao)) {
                $ordenacao = NULL;
            }
            $funcionario = $repository->find($id);
            if ($funcionario->getDiretor()) {
                $funcionario->getDiretor()->setOrdem($ordenacao);
            } else {
                $diretor = new \Entity\Diretoria();
                $diretor->setOrdem($ordenacao);
                $diretor->setFuncionario($funcionario);
                $this->getEm()->persist($diretor);

                $funcionario->setDiretor($diretor);
            }
            $this->getEm()->persist($funcionario);
        }

        $this->getEm()->flush();

        return json_encode(array(
            'resultado' => 'ok',
        ));
    }

}