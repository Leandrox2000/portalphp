<?php
namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\PublicacaoIntroducao as PublicacaoIntroducaoEntity;
use Helpers\Session;

/**
 * Description of Publicacao
 *
 * @author Luciano
 */
class PublicacaoIntroducao extends BaseService
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\PublicacaoIntroducao $entity
     */
    public function __construct(EntityManager $em, PublicacaoIntroducaoEntity $entity, Session $session)
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
            $entity = parent::save($dados);

            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $action = empty($dados['id']) ? "inserir" : "alterar";
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Erro ao {$action} registro";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
