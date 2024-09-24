<?php
namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\AgendaDirecao as AgendaDirecaoEntity;
use Helpers\Session;
use Doctrine\Common\Collections\ArrayCollection;

class AgendaDirecao extends BaseService
{
    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\AgendaDirecao $entity
     */
    public function __construct(EntityManager $em, AgendaDirecaoEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
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
            $responsaveis = $dados['responsaveis'];
            
            $this->getEm()->beginTransaction();
         
            if($action == 'alterado') {

                $agenda = $this->getEm()->getRepository('Entity\AgendaDirecao')->find($dados['id']);
                
                $sitesVinculados = $agenda->getSites();
                $sitesPaiVinculados = $agenda->getPaiSites();

                // Limpa as relações
                $sitesPaiVinculados->clear();
                
                // Remove os itens 
                // A lista de sites da tb_agenda_direcao_site possui a coluna ordem
                // que precisa ser mantida, por isso não é possível utilizar clear nela
                foreach ($sitesVinculados as $i => $Site) {
                    if(!in_array($Site->getId(), $sites)) {
                        $sitesVinculados->remove($i);
                    }
                }

                // E adiciona novamente
                foreach ($sites as $id_site) {
                    $Site = $this->getEm()->getReference('Entity\Site', $id_site);
                    $sitesPaiVinculados->add($Site);
                    
                    if(!$sitesVinculados->contains($Site)) {
                        $sitesVinculados->add($Site);
                    }
                }

                $dados['paiSites'] = $sitesPaiVinculados;
                $dados['sites'] = $sitesVinculados;

            } else {
                $sitesVinculados = new ArrayCollection();

                // Vincula os sites e responsáveis
                foreach ($sites as $id_site) {
                    $Site = $this->getEm()->getReference('Entity\Site', $id_site);
                    $sitesVinculados->add($Site);
                }
                
                $dados['paiSites'] = $sitesVinculados;
                $dados['sites'] = $sitesVinculados;
            }
            unset($dados['responsaveis']);

            $entity = parent::save($dados);
            
            $this->addResponsaveis($entity, $responsaveis, !$dados['id']);
            
            $this->getEm()->commit();

            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $response = 0;
            $error[] = $exc->getMessage();            
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
        
    /**
     * 
     * @param Entity\AgendaDirecao $agenda
     * @param array $responsaveis
     * @param boolean $is_new
     */
    private function addResponsaveis($agenda, $responsaveis, $is_new = true) {
        
        if(!$is_new) {
            $this->deleteResponsaveis($agenda);
        }

        foreach ($responsaveis as $responsavel) {
            $novoResponsavel = new \Entity\AgendaDirecaoResponsavel();
            $novoResponsavel->setResponsavel($responsavel);
            $novoResponsavel->setAgendaDirecao($agenda);

            // Faz a persistência
            $this->getEm()->persist($novoResponsavel);
            $this->getEm()->flush();
        }
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
        $removidos = false;
        $despublicados = false;

        try {
            $this->getEm()->beginTransaction();

            foreach ($ids as $id) {
                $agenda = $this->getEm()->find($this->getNameEntity(), $id);
                
                // Se o registro está publicado, então apenas despublica
                if ($this->verificarStatus($id)) {
                    $agenda->setPublicado(0);
                    
                    $this->getEm()->persist($agenda);
                    $this->getEm()->flush();
                    
                    $this->alterarStatusValidacao([$id], 0, null, 'Entity\AgendaDirecao');
                    
                    $despublicados = true;
                    continue;
                } 
                
                // Remove o registro
                $this->deleteResponsaveis($agenda);
                
                $this->getEm()->remove($agenda);
                $this->getEm()->flush();
                
                $removidos = true;
            }
            $this->getEm()->commit();
            $this->getLogger()->info("As Agendas da Direção " . implode(",", $ids) . " foram excluidas.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $response = 0;
            $error[] = "Não foi possível executar esta ação.";
        }
        
        $success = "Ação executada com sucesso";
        if ($despublicados && $removidos) {
            $success = "Algum(Alguns) registro(s) foi(foram) despublicado(s). É necessário realizar a ação de exclusão novamente para sua exclusão definitiva.";
        } else if($despublicados) {
            $success = "Este(s) registro(s) foi(foram) despublicado(s). É necessário realizar a ação de exclusão novamente para sua exclusão definitiva.";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
    
    /**
     * Remove os responsáveis pela agenda.
     * 
     * @param \Entity\AgendaDirecao $agenda
     * @return boolean
     */
    private function deleteResponsaveis($agenda)
    {         
        $queryBuilder = $this->getEm()->createQueryBuilder();
        $queryBuilder
            ->delete('Entity\AgendaDirecaoResponsavel', 'resp')
            ->where($queryBuilder->expr()->eq('resp.agendaDirecao', ':agenda'))
            ->setParameter('agenda', $agenda);
        
        $this->getEm()->flush();
        
        return $queryBuilder->getQuery()->getResult();
    }
}
