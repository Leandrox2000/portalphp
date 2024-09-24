<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Bibliografia as BibliografiaEntity;
use Helpers\Session;

/**
 * Description of Bibliografia
 *
 * @author Luciano
 */
class Bibliografia extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Bibliografia $entity
     */
    public function __construct(EntityManager $em, BibliografiaEntity $entity, Session $session)
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
            'title'             => 'Bibliografia Geral do Patrimônio',
            'description'       => $entity->getConteudo(),
            'publish'           => $entity->getPublicado(),
            'publish_date'      => $entity->getDataInicial(),
            'unpublish_date'    => $entity->getDataFinal(),
            'url'               => \Helpers\Url::generateRoute('bibliografiaPatrimonio'),
        );
    }

    /**
     *
     * @param array $dados
     * @return array
     */
    public function save(array $dados)
    {
        $response = 0;
        $error = array();
        $success = "";

        try {
            $action = empty($dados['id']) ? "inserido" : "alterado";
            $entity = parent::save($dados);

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);

            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $error[] = $exc->getMessage();
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     *
     * @param array $ids
     * @return array
     */
    public function delete(array $ids)
    {
        $response = 0;
        $error = array();
        $success = "";

        try {
            $this->getEm()->beginTransaction();

            foreach ($ids as $id) {
                $bibliografia = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($bibliografia);
                $this->getEm()->flush();
            }

            $this->getEm()->commit();
            $success = "Ação executada com sucesso";
            $response = 1;
            $this->getLogger()->info("[{$this->getNameEntity()}] - Registros deletados ID " . implode(",", $ids) . " - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
