<?php

namespace CMS\Service\ServiceRepository;

use \Doctrine\ORM\EntityManager;
use \Entity\SliderHome as SliderHomeEntity;
use Helpers\Session;

/**
 * Description of SliderHome
 *
 * @author Join-ti
 */
class SliderHome extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\SliderHomeEntity $entity
     */
    public function __construct(EntityManager $em, SliderHomeEntity $entity, Session $session)
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
            parent::save($dados, true);
            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $action = empty($dados['id']) ? "inserir" : "alterar";
            $error[] = "Erro ao {$action} registro";
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
                if ($this->verificarStatus($id)) {
                    throw new \Exception;
                }
                $slider = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($slider);
                $this->getEm()->flush();
            }

            $success = "Ação executada com sucesso";
            $response = 1;

            $this->getEm()->commit();
            $this->getLogger()->info("sliders da home " . implode(",", $ids) . " foram excluidos.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
