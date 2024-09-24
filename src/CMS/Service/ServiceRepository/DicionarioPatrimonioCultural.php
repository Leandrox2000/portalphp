<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\DicionarioPatrimonioCultural as DicionarioEntity;
use Helpers\Session;

/**
 * Dicionário do Patrimônio Cultural
 */
class DicionarioPatrimonioCultural extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\DicionarioPatrimonioCultural $entity
     */
    public function __construct(EntityManager $em, DicionarioEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    public function getDadosSolr($entity)
    {
        return array(
            'entity_name'       => $this->getNameEntity(),
            'entity_id'         => $entity->getId(),
            'title'             => $entity->getTitulo(),
            'description'       => $entity->getDescricao(),
            'publish'           => $entity->getPublicado(),
            'publish_date'      => $entity->getDataInicial(),
            'unpublish_date'    => $entity->getDataFinal(),
            'author'            => $entity->getVerbete(),
            'url'               => \Helpers\Url::generateRoute('dicionarioPatrimonioCultural', 'detalhes', $entity),
        );
    }

    /**
     *
     * @param array $dados
     */
    public function save(array $dados)
    {
        $success = "";
        $error = array();
        $response = 0;

        try {
            $action = !empty($dados['id']) ? "alterado" : "inserido";

            //Inicia a transação
            $this->getEm()->beginTransaction();

            //Salva o registro
            $entity = parent::save($dados);

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);

            //Commita a transação
            $this->getEm()->commit();

            $success = "Registro {$action} com sucesso!";
            $response = 1;
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $error[] = $ex->getMessage();
        }

        return array("success" => $success, "error" => $error, "response" => $response);
    }

    /**
     *
     * @param array $ids
     * @return array
     */
    public function delete(array $ids)
    {
        $success = "";
        $error = array();
        $response = 0;

        try {
            $success = "Ação executada com sucesso";
            parent::delete($ids);

            $response = 1;
        } catch (\Exception $ex) {
            $error[] = $ex->getMessage();
        }

        return array("success" => $success, "error" => $error, "response" => $response);
    }

}
