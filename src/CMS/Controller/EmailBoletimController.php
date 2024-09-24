<?php

namespace CMS\Controller;

use LibraryController\AbstractController;
use Helpers\Param;
use Entity\EmailBoletim as EmailBoletimEntity;
use CMS\StaticMethods\StaticMethods;
use CMS\Service\ServiceRepository\EmailBoletim as EmailBoletimService;
use CMS\Service\ServiceExcel\EmailBoletim as EmailBoletimExcel;

/**
 * EmailBoletimController
 *
 * @author join-ti
 */
class EmailBoletimController extends AbstractController
{

    const PAGE_TITLE = "Banco de emails";
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
     * @var EmailBoletimService
     */
    protected $service;

    /**
     *
     * @var String
     */
    protected $excel;

    /**
     *
     * @var StaticMethods
     */
    protected $staticMethods;

    /**
     *
     * @var EmailBoletim
     */
    protected $entity;

    /**
     *
     * @param EmailBoletimService $service
     */
    public function setService(EmailBoletimService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @return EmailBoletimService
     */
    public function getService()
    {
        if ($this->service == null) {
            $this->service = new EmailBoletimService($this->getEm(), $this->getEntity(), $this->getSession());
        }
        return $this->service;
    }

    /**
     *
     * @param EmailBoletimExcel $excel
     */
    public function setExcel(String $excel)
    {
        $this->excel = $excel;
    }

    /**
     *
     * @return EmailBoletimExcel
     */
    public function getExcel()
    {
        if (!isset($this->excel))
            $this->excel = new EmailBoletimExcel;

        return $this->excel;
    }

    /**
     *
     * @param StaticMethods $staticMethods
     */
    public function setStaticMethods(StaticMethods $staticMethods)
    {
        $this->staticMethods = $staticMethods;
    }

    /**
     *
     * @return StaticMethods
     */
    public function getStaticMethods()
    {
        if ($this->staticMethods == null)
            $this->staticMethods = new StaticMethods();

        return $this->staticMethods;
    }

    /**
     *
     * @param EmailBoletim $entity
     */
    public function setEntity(EmailBoletim $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Metodo getDefaultAction
     *
     * Retorna a default action
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
     * @return EmailBoletimEntity
     */
    public function getEntity()
    {
        if (!isset($this->entity))
            $this->entity = new EmailBoletimEntity();
        return $this->entity;
    }

    /**
     * Metodo lista
     *
     * Utilizado para exibir a página de listagem
     * @return string
     */
    public function lista()
    {
        $this->tpl->renderView(array(
            'titlePage' => $this->getTitle(),
            'subTitlePage' => "")
        );
        return $this->tpl->output();
    }

    /**
     *
     * Resposável por retornar os registros paginados
     * @return String
     */
    public function paginacao()
    {
        //Instancia o repository
        $em = $this->getEm();
        $repoEmail = $em->getRepository("Entity\EmailBoletim");

        //Busca o total
        $total = $repoEmail->countAll();

        //Verifica se é busca ou paginação
        if ($this->getParam()->get('sSearch')) {
            $emails = $repoEmail->getPesquisaEmail($this->getParam()->get('iDisplayLength'), $this->getParam()->get('iDisplayStart'), $this->getParam()->get('sSearch'));
            $totalFiltro = $total;
        } else {
            $emails = $repoEmail->getEmailBoletim($this->getParam()->get('iDisplayLength'), $this->getParam()->get('iDisplayStart'));
            $totalFiltro = $total;
        }

        //Percorre e organiza o HTML da listagem
        $dados = array();
        foreach ($emails as $email) {
            $linha = array();
            $linha[] = "<input type='checkbox' name='email[]' value=" . $email->getId() . " class='marcar' />";
            $linha[] = $email->getLabel() . " <i>(" . $email->getNome() . ")</i>";
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
     * Metodo responsável pela exportação de dados
     */
    public function exportar()
    {
        //Intancia o repository
        $repoEmails = $this->getEm()->getRepository("Entity\EmailBoletim");

        //Faz a exportação
        $this->getExcel()->exportarEmails($repoEmails, $this->getStaticMethods(), $this->getParam()->get('ids'));
    }

    /**
     *
     * Requisita a exclusão de registros
     * @return String
     */
    public function excluir()
    {
        //Busca o entityManager e o service
        $em = $this->getEm();
        $service = $this->getService();

        //Realiza a exclusão e retorna o array de resultados via json
        $retorno = array();
        if ($service->delete($_POST['sel'])) {
            $retorno['response'] = 1;
            $retorno['sucess'] = "Registro(s) excluído(s) com sucesso";
            $retorno['error'] = array();
        } else {
            $retorno['response'] = 0;
            $retorno['error'] = array("Erro ao excluir registro(s)");
        }

        return json_encode($retorno);
    }

}
