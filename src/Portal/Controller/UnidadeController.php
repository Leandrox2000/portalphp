<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Contato / Fale Conosco
 *
 */
class UnidadeController extends PortalController
{

    protected $defaultAction = 'index';
    
    /**
     * Página de contato.
     *
     * @return string
     */
    public function index()
    {
        $pagMaximo = 5;
        $pagNumero = $this->getParam()->getString('pagina', 1);
        $pesquisa = $this->getParam()->getString('pesquisa');
        
        $repository = $this->getEm()->getRepository('Entity\Unidade');
        
        //$unidade = $repository->getUnidade($pagMaximo, $pagNumero, $pesquisa);
        $unidade = $repository->getUnidadeOrder($pagMaximo, $pagNumero, $pesquisa);
//        var_dump($unidade);
        $pagination = new Pagination($unidade['pagina'], $pagMaximo);
        
        $this->getTpl()->setTitle('Unidade');
        $this->getTpl()->setView('unidade/index.html.twig');
        
        foreach($unidade['unidade'] as $i => $u){
            if($u->getComplemento())
                $u->setComplemento(', '.$u->getComplemento());
        }
        
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("", null, "unidade");
        
        $this->getTpl()->renderView(array(
            'unidade' => $unidade['unidade'],
            'pesquisa' => $pesquisa,
            'results' => $pagination->results(),
            'pagination' => $pagination->render(),
            'paginationObject' => $pagination->getPagerfanta(),
            'bread' => $bread,
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
