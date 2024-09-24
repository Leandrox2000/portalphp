<?php

namespace Portal\Controller;

use Helpers\Pagination;

/**
 * Atas do Conselho
 *
 */
class AtasConselhoController extends PortalController {

    protected $defaultAction = 'lista';

    /**
     *
     * @return \Entity\Repository\AtaRepository
     */
    private function getRepository()
    {
        return $this->getEm()->getRepository('Entity\Ata');
    }

    /**
     * Gera os intervalos de datas utilizados no filtro da listagem.
     *
     * @return array
     */
    private function geraIntervaloDatas()
    {
        $datas = array();
        $datas['1938/1940'] = 'De 1938 até 1940';
        $ano = 1941;
        $atual = date('Y');

        while ($ano < $atual) {
            $intervalo = $ano + 9;

            if ($intervalo > $atual) {
                $intervalo = $atual;
            }

            $datas[$ano . '/' . $intervalo] = 'De ' . $ano . ' até ' . $intervalo;
            $ano = $ano + 10;
        }

        return $datas;
    }

    /**
     * Lista de Atas do Conselho.
     *
     * @return string
     */
    public function lista()
    {
        $paramData = $this->getParam()->getString('data');
        $repository = $this->getRepository();
        $datas = $this->geraIntervaloDatas();
        $noFilter = false;

        if (!empty($paramData)) {
            $resultado = explode('/', $paramData);
            $results = $repository->getConteudoInterna($resultado[0], $resultado[1]);
            $pagination = new Pagination($results);
        } else {
            end($datas);
            $resultado = explode('/', key($datas));
            $results = $repository->getConteudoInterna($resultado[0], $resultado[1]);
            $pagination = new Pagination($results, 10);
            $noFilter = true;
        }

        $dataObjects = array();
        foreach ($datas as $key => $value) {
            $dataObjects[] = new \Entity\Type($key, $value);
        }
        
        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("Entity\Ata", null, null, $this->getSubsite());


        $this->getTpl()->setTitle('Atas do Conselho');
        $this->getTpl()->renderView(array(
            'paramData' => $paramData,
            'results' => is_object($pagination) ? $pagination->results() : null,
            'pagination' => is_object($pagination) ? $pagination->render() : null,
            'paginationObject' => $pagination->getPagerfanta(), // Classe da paginação
            'datas' => $dataObjects,
            'data' => $paramData,
            'noFilter' => $noFilter,
            'bread' => $bread,
            'site' => $this->getSubsite(),
        ));

        return $this->getTpl()->output();
    }
    
    /**
     * Redireciona para o arquivo da ata.
     * 
     * @param integer $id
     */
    public function detalhes($id = null) {
        $ata = $this->getRepository()->find($id);
        if ($ata == null) {
            throw new \Exception\NotFoundException();
        }
        
        \Helpers\Http::redirect('/uploads/atas/' . $ata->getArquivo());
    }

}
