<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager as EntityManager;
use Entity\PaginaEstatica as PaginaEstaticaEntity;
use Helpers\Param;
use Helpers\Session;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe PaginaEstatica
 *
 * Responsável pelas ações na entidade PaginaEstatica
 * @author join-ti
 */
class PaginaEstatica extends BaseService implements SolrAwareInterface {

    public function __construct(EntityManager $em, PaginaEstaticaEntity $entity, Session $session) {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    public function getDadosSolr($entity) {
        return array(
            'entity_name' => $this->getNameEntity(),
            'entity_id' => $entity->getId(),
            'title' => $entity->getTitulo(),
            'description' => $entity->getConteudo(),
            'publish' => $entity->getPublicado(),
            'publish_date' => $entity->getDataInicial(),
            'unpublish_date' => $entity->getDataFinal(),
            'url' => \Helpers\Url::generateRoute('pagina', 'detalhes', $entity),
        );
    }

    /**
     *
     * @param array $dados
     * @return array
     */
    public function save(array $dados) {
        $error = array();
        $response = 0;
        $success = "";
        $param = new Param();

        try {
            $action = empty($dados['id']) ? "inserido" : "alterado";

            $sites = $dados['sites'];

            //Armazena os ids das galerias em array
            $arrayGalerias = !empty($dados['idsGalerias']) ? explode(',', $dados['idsGalerias']) : array();
            unset($dados['idsGalerias']);

            //Inicia a transação
            $this->getEm()->beginTransaction();

            //Verifica se for update deleta vinculos e pais não selecionado
            if($action == 'alterado') {

                $sitesSession = $_SESSION['user']['subsites'];

                $connection = $this->getEm()->getConnection();

                foreach ($sitesSession as $site) {
                    $connection->query("DELETE FROM tb_pagina_estatica_site WHERE id_pagina_estatica = {$dados['id']} AND id_site = {$site}");

                    $connection->query("DELETE FROM tb_pai_pagina_estatica_site WHERE id_pagina_estatica = {$dados['id']} AND id_site = {$site}");
                }

                $paginaEstatica = $this->getEm()->getRepository('Entity\PaginaEstatica')->find($dados['id']);

                $sitesArrPai = $paginaEstatica->getPaiSites();

                $sitesArr = $paginaEstatica->getSites();

                foreach ($sites as $site) {
                    $rSite = $this->getEm()->getReference('Entity\Site', $site);
                    $sitesArr->add($rSite);
                    $sitesArrPai->add($rSite);
                }

                $dados['paiSites'] = $sitesArrPai;

                $dados['sites'] = $sitesArr;

            } else {
                $sitesArr = new ArrayCollection();

                foreach ($sites as $site) {
                    $rSite = $this->getEm()->getReference('Entity\Site', $site);
                    $sitesArr->add($rSite);
                }

                //Salva subsites e pai do registro
                $dados['paiSites'] = $sitesArr;

                $dados['sites'] = $sitesArr;
            }

            $entity = parent::save($dados);

            //Verifica se a ação é de exclusão ou edição
            if (!empty($dados['id'])) {
                //Seleciona as relações dessa página estática com alguma galeria
                $galerias = $this->getEm()->createQuery("SELECT peg FROM Entity\PaginaEstaticaGaleria peg JOIN peg.paginaEstatica pa WHERE pa.id = {$dados['id']}")->execute();

                //Percorre e deleta a relação com as galerias
                foreach ($galerias as $galeria) {
                    $this->getEm()->remove($galeria);
                    $this->getEm()->flush();
                }

                //Armazena o id da página estática
                $idPaginaEstatica = $dados['id'];
            } else {
                //Armazena o id da página estática
                $idPaginaEstatica = $entity->getId();
            }

            //Insere os registros na tabela PaginaEstaticaGaleria e monta a collection com as relações
            $paginaEstaticaGaleria = new \Doctrine\Common\Collections\ArrayCollection();

            foreach ($arrayGalerias as $idGaleria) {
                $paginaEstaticaGaleria = new \Entity\PaginaEstaticaGaleria();
                $paginaEstaticaGaleria->setGaleria($this->getEm()->getReference('Entity\Galeria', $idGaleria));
                $paginaEstaticaGaleria->setPaginaEstatica($this->getEm()->getReference('Entity\PaginaEstatica', $idPaginaEstatica));
                $paginaEstaticaGaleria->setPosicaoPagina($param->get('posicao' . $idGaleria));
                $this->getEm()->persist($paginaEstaticaGaleria);
                $this->getEm()->flush();
            }

            //Commita a transação
            $this->getEm()->commit();

            /* Atualiza o índice do Solr */
            $this->updateSolr($entity);

            $action = !empty($dados['id']) ? "alterado" : "inserido";
            $success = "Registro $action com sucesso!";
            $response = 1;
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $error[] = $ex->getMessage();
        }



        //Retorna os dados
        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     * Metodo delete
     *
     * Deleta um grupo de registros e seus respectivos arquivos
     * @param array $ids
     * @param array $forceIds  Não foi possível descobrir o porque do parâmetro $ids ser ignorado,
     *                         por isso o ideal foi criar um novo parâmetro para ser respeitado
     *                         quando necessário.
     * @return boolean
     */
    public function delete(array $ids, $forceIds = array()) {
        if(is_array($forceIds) && count($forceIds)) {
            $ids = $forceIds;
        } else {
            $ids = $_REQUEST['sel'];
        }
        $response = 1;
        $error = array();
        $success = "Ação executada com sucesso";

        try {
            $this->getEm()->beginTransaction();

            foreach ($ids as $id) {
                if ($this->verificarStatus($id)) {
                    throw new \Exception;
                }

                $pagina = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($pagina);
                $this->getEm()->flush();
            }
            $this->getEm()->commit();
            $this->getLogger()->info("Páginas " . implode(",", $ids) . " foram excluidas.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $response = 0;
            $error[] = "Não foi possível executar esta ação.";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     *  Atualiza o indice do solr 
     * @param type $entity
     */
    public function updateSolr($entity) {
        try {
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);
        } catch (\Exception $ex) {
            $this->getLogger()->error("Erro ao registrar indice: " . $ex->getMessage());
        }
    }

}
