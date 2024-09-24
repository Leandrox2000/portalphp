<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Edital as EditalEntity;
use Helpers\Session;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Edital
 *
 * @author Luciano
 */
class Edital extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Edital $entity
     */
    public function __construct(EntityManager $em, EditalEntity $entity, Session $session)
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
            'title'             => $entity->getNome(),
            'description'       => $entity->getConteudo(),
            'publish'           => $entity->getPublicado(),
            'publish_date'      => $entity->getDataInicial(),
            'unpublish_date'    => $entity->getDataFinal(),
            'url'               => \Helpers\Url::generateRoute('editais', 'detalhes', $entity),
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

            $sites = $dados['sites'];

            //Verifica se for update deleta vinculos e pais não selecionado
            if($action == 'alterado') {

                $sitesSession = $_SESSION['user']['subsites'];

                $connection = $this->getEm()->getConnection();

                foreach ($sitesSession as $site) {
                    $connection->query("DELETE FROM tb_edital_site WHERE id_edital = {$dados['id']} AND id_site = {$site}");

                    $connection->query("DELETE FROM tb_pai_edital_site WHERE id_edital = {$dados['id']} AND id_site = {$site}");
                }

                $edital = $this->getEm()->getRepository('Entity\Edital')->find($dados['id']);

                $sitesArrPai = $edital->getPaiSites();

                $sitesArr = $edital->getSites();

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
     * @param array $forceIds  Não foi possível descobrir o porque do parâmetro $ids ser ignorado,
     *                         por isso o ideal foi criar um novo parâmetro para ser respeitado
     *                         quando necessário.
     * @return array
     */
    public function delete(array $ids, $forceIds = array())
    {
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
                $edital = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($edital);
                $this->getEm()->flush();
            }
            $this->getEm()->commit();
            $this->getLogger()->info("Editais " . implode(",", $ids) . " foram excluidos.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $response = 0;
            $error[] = "Não foi possível executar esta ação.";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
