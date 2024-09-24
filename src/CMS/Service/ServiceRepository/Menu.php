<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Menu as MenuEntity;

/**
 * Menu
 */
class Menu extends BaseService
{

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Menu $entity
     */
    public function __construct(EntityManager $em, MenuEntity $entity, \Helpers\Session $session)
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
        $success = "";
        $error = array();
        $response = 0;
        $action = !empty($dados['id']) ? "alterado" : "inserido";

        // Inicializa a entidade Entity\Menu
        if (!empty($dados['vinculoPai'])) {
            $vinculoPai = $this->getEm()->getReference('Entity\Menu', $dados['vinculoPai']);
            $dados['vinculoPai'] = $vinculoPai;
        } else {
            $dados['vinculoPai'] = NULL;
        }

        // Inicializa a entidade FuncionalidadeMenu
        if (!empty($dados['funcionalidadeMenu'])) {
            $funcMenu = $this->getEm()->getReference('Entity\FuncionalidadeMenu', $dados['funcionalidadeMenu']);
            $dados['funcionalidadeMenu'] = $funcMenu;
        } else {
            $dados['funcionalidadeMenu'] = NULL;
        }

        // Inicializa a entidade Site
        if (!empty($dados['site'])) {
            $eSite = $this->getEm()->getReference('Entity\Site', $dados['site']);
            $dados['site'] = $eSite;
        } else {
            $dados['site'] = NULL;
        }

        try {
            // Inicia a transação
            $this->getEm()->beginTransaction();

            // Salva o registro
            parent::save($dados);

            // Finaliza a transação
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
            'response' => $response
        );
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
        $permitiExclusão = 0;
        
        $rep = $this->getEm()->getRepository($this->getNameEntity());

        $menus = $rep->findIn($ids);
        
        foreach ($menus as $menu) {
            if ($menu->getFilhos()->count()>0) {
                $error[] = "Existem menus com níveis inferiores associados";
                break;
            }
            if ($this->verificarStatus($ids)) {
                $error[] = "Não foi possível executar esta ação.";
                break;
            }
            $permitiExclusão = 1;
        }
        
        if ($permitiExclusão) {
            try {
                $success = "Registros deletados com sucesso";
                parent::delete($ids);
                $response = 1;
            } catch (\Exception $ex) {
                $response = 0;
                $error[] = $ex->getMessage();
            }
        }

        return array("success" => $success, "error" => $error, "response" => $response);
    }
    
  
}
