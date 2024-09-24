<?php
namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Compromisso as CompromissoEntity;
use Helpers\Session;
use Doctrine\Common\Collections\ArrayCollection;

class Compromisso extends BaseService
{
    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Compromisso $entity
     */
    public function __construct(EntityManager $em, CompromissoEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
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

            $agendas = $dados['agendasDirecao'];
            
            $this->getEm()->beginTransaction();
         
            if($action == 'alterado') {

                $compromisso = $this->getEm()->getRepository('Entity\Compromisso')->find($dados['id']);
                
                $agendasVinculados = $compromisso->getAgendasDirecao();

                // Limpa as relações
                $agendasVinculados->clear();

                // E adiciona novamente
                foreach ($agendas as $id_agenda_direcao) {
                    $AgendaDirecao = $this->getEm()->getReference('Entity\AgendaDirecao', $id_agenda_direcao);
                    $agendasVinculados->add($AgendaDirecao);
                }

                $dados['agendasDirecao'] = $agendasVinculados;

            } else {
                $agendasVinculados = new ArrayCollection();

                // Vincula as agendas
                foreach ($agendas as $id_agenda_direcao) {
                    $AgendaDirecao = $this->getEm()->getReference('Entity\AgendaDirecao', $id_agenda_direcao);
                    $agendasVinculados->add($AgendaDirecao);
                }
                
                $dados['agendasDirecao'] = $agendasVinculados;
            }

            $entity = parent::save($dados);
            
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
                $compromisso = $this->getEm()->find($this->getNameEntity(), $id);
                
                // Se o registro está publicado, então apenas despublica
                if ($this->verificarStatus($id)) {
                    $compromisso->setPublicado(0);
                    
                    $this->getEm()->persist($compromisso);
                    $this->getEm()->flush();
                    
                    $this->alterarStatus([$id], 0);
                    
                    $despublicados = true;
                    continue;
                } 
                
                $this->getEm()->remove($compromisso);
                $this->getEm()->flush();
                
                $removidos = true;
            }
            $this->getEm()->commit();
            $this->getLogger()->info("Os Compromissos " . implode(",", $ids) . " foram excluidos.");
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
}
