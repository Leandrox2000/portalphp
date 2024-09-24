<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\BannerGeral as BannerEntity;
use Helpers\Session;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * BannerGeral do Gerenciador de BannerGerals
 */
class BannerGeral extends BaseService
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\BannerGeral $entity
     */
    public function __construct(EntityManager $em, BannerEntity $entity, Session $session)
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
        $success = "";
        $error = array();
        $response = 0;

        try {
            $action = !empty($dados['id']) ? "alterado" : "inserido";

            $dados['categoria'] = $this->getEm()
                ->getReference('Entity\BannerGeralCategoria', $dados['categoria']);
            $dados['imagem'] = $this->getEm()
                ->getReference('Entity\Imagem', $dados['imagem']);

           
            
            if (!empty($dados['funcionalidadeMenu'])) {
               $dados['funcionalidadeMenu'] = $this->getEm()->getReference('Entity\FuncionalidadeMenu', $dados['funcionalidadeMenu']);
            } else {
                $dados['funcionalidadeMenu'] = null;
            }

            if (empty($dados['abrirEm'])) {
                unset($dados['abrirEm']);
            }
                        
            // Inicia a transação
            $this->getEm()->beginTransaction();
            
            $user = $this->getSession()->get('user');
            if($user["dadosUser"]["login"] != "teste"){
                $cox  = $this->getEm()->getConnection();
                $sites_da_sessao = $dados['sitesSessao'];
                if($dados['id']){
                    foreach ($sites_da_sessao as $site) {
                        #deleta
                        $cox->query("DELETE FROM tb_banner_geral_site WHERE id_banner_geral = {$dados['id']} AND id_site = {$site->getId()}");
                    }
                }

                unset($dados['sitesSessao']);

                if($dados['id']){
                    $bannerGeral = $this->getEm()->getRepository("Entity\BannerGeral")->find($dados['id']);
                    $sites = $bannerGeral->getSites();
                }

                $collection = new ArrayCollection();
                #adicionar os que estavam no banco
                if($sites){
                    foreach ($sites as $site) {
                        $collection->add($site);
                    }
                }
                #adicionar os enviados do form
                if($dados["sites"]){
                    foreach ($dados["sites"] as $site) {
                        $rSite = $this->getEm()->getReference('Entity\Site', $site);
                        $collection->add($rSite);
                    }
                }
                 $dados["sites"] = $collection;

                // Salva o registro
                parent::save($dados, FALSE);
            }else{
                unset($dados['sitesSessao']);
                unset($dados['sites']);
                // Salva o registro
                parent::save($dados, TRUE);
            }
            
            
            // Commita a transação
            $this->getEm()->commit();

            $success = "Registro {$action} com sucesso!";
            $response = 1;
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $error[] = $ex->getMessage();
        }

        return array(
            "success" => $success,
            "error" => $error,
            "response" => $response,
        );
    }

    /**
     *
     * @param array $ids
     * @return array
     */
    public function delete(array $ids)
    {
        $ids = $_REQUEST['sel'];
        $response = 1;
        $error = array();
        $success = "Ação executada com sucesso";

        try {
            foreach ($ids as $id) {
                if ($this->verificarStatus($id)) {
                    throw new \Exception;
                }
            }
            parent::deleteWithRelations($ids);

        } catch (\Exception $ex) {
            $this->getLogger()->error($ex->getMessage());
            $error[] = "Não foi possível executar esta ação.";
            $response = 0;
        }

        return array("success" => $success, "error" => $error, "response" => $response);
    }

}
