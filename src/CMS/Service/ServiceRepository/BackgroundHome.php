<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\BackgroundHome as BackgroundHomeEntity;
use Helpers\Session;

/**
 * Dicionário do Patrimônio Cultural
 */
class BackgroundHome extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param BackgroundHomeEntity $entity
     */
    public function __construct(EntityManager $em, BackgroundHomeEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     * 
     * @param array $dados
     */
    public function save(array $dados)
    {
        $success = '';
        $error = array();
        $response = 0;

        try {
            $action = !empty($dados['id']) ? 'alterado' : 'inserido';

            //Inicia a transação
            $this->getEm()->beginTransaction();

            //Salva o registro
            parent::save($dados);

            //Commita a transação
            $this->getEm()->commit();

            $success = "Registro {$action} com sucesso!";
            $response = 1;
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $error[] = $ex->getMessage();
        }

        return array(
            'success' => $success,
            'error' => $error,
            'response' => $response,
        );
    }

    /**
     * 
     * @param array $ids
     * @return array
     */
    public function delete(array $ids)
    {
        $success = '';
        $error = array();
        $response = 0;

        try {
            $success = 'Ação executada com sucesso';
            parent::delete($ids);
            $response = 1;
        } catch (\Exception $ex) {
            $error[] = $ex->getMessage();
        }

        return array(
            'success' => $success,
            'error' => $error,
            'response' => $response
        );
    }

    /**
     * 
     * @param array $ids
     * @param string $status
     * @return array
     */
    public function alterarStatus(array $ids, $status)
    {
        $response = 0;
        $error = array();
        $success = "Ação executada com sucesso";
        $naoPermitido = false;

        // Obtem a contagem de backgrounds publicados
        $query = $this->getEm()
                ->createQuery('SELECT COUNT(e.id) as total FROM Entity\BackgroundHome e WHERE e.publicado = 1')
                ->getResult(\Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);

        try {
            // Bloqueia a publicação de mais de um background
            // quando mais de um item é selecionado, ou caso já exista
            // um background publicado
            if ($status == 1 && ( count($ids) > 1 || $query > 0 ) ) {
                $naoPermitido = true;
                throw new \Exception();
            }
            $ids = implode(",", $ids);
            $this->setaStatus($ids, $status);
            
            $this->getLogger()->info("[{$this->getNameEntity()}] Alterado status IDs " . $ids." - usuario: {$this->getNameUser()} - IP {$this->getIpUser()}");
            $response = 1;
        } catch (\Exception $exc) {
            if ($naoPermitido) {
                $error[] = "Só é permitido publicar um background por vez.";
            } else {
                $error[] = "Não foi possível executar esta ação";
            }
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
