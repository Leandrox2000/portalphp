<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Diretoria / Quem é Quem
 *
 */
class QuemEQuemController extends PortalController
{

    protected $defaultAction = 'lista';

    /**
     * Listam de membros da Diretoria.
     *
     * @return string
     */
    public function lista()
    {
        $repository = $this->getEm()->getRepository('Entity\Diretoria');
        $results = $repository->getConteudoInterna();
        $pagination = new Pagination($results);
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("", null, "quemEQuem", $this->getSubsite());
        $this->getTpl()->setTitle('Quem é Quem');
        $this->getTpl()->renderView(array(
            'pagination' => $pagination,
            'results' => $pagination->results(),
            'bread' => $bread,
            'site' => $this->getSubsite()
        ));

        return $this->getTpl()->output();

    }

    /**
     * Detalhes de um membro da Diretoria.
     *
     * @param integer $id
     * @return string
     */
    public function detalhes($id = NULL)
    {
        $repository = $this->getEm()->getRepository('Entity\Funcionario');
        $result = $repository->getPublicadoDetalhe($id);

        if (!$result)
            throw new \Exception\NotFoundException();
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("", null, "quemEQuem", $this->getSubsite());
        $this->getTpl()->setTitle('Quem é quem: ' . $result->getNome());
        $this->getTpl()->renderView(array(
            'result' => $result,
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }
    
    private function getEstados()
    {
        return array(
            "AC" => "Acre",
            "AL" => "Alagoas",
            "AM" => "Amazonas",
            "AP" => "Amapá",
            "BA" => "Bahia",
            "CE" => "Ceará",
            "DF" => "Distrito Federal",
            "ES" => "Espírito Santo",
            "GO" => "Goiás",
            "MA" => "Maranhão",
            "MT" => "Mato Grosso",
            "MS" => "Mato Grosso do Sul",
            "MG" => "Minas Gerais",
            "PA" => "Pará",
            "PB" => "Paraíba",
            "PR" => "Paraná",
            "PE" => "Pernambuco",
            "PI" => "Piauí",
            "RJ" => "Rio de Janeiro",
            "RN" => "Rio Grande do Norte",
            "RO" => "Rondônia",
            "RS" => "Rio Grande do Sul",
            "RR" => "Roraima",
            "SC" => "Santa Catarina",
            "SE" => "Sergipe",
            "SP" => "São Paulo",
            "TO" => "Tocantins"
        );
    }
}
