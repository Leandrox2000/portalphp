<?php

namespace Helpers;

/**
 *
 */
class Http {

    /**
     *
     * @param type $page
     */
    public static function redirect($page) {
        header("Location: " . $page);
        die();
    }

    /**
     *
     * @param \Entity\Menu $entity
     * @return string
     */
    public static function generateUrl(\Entity\Menu $entity, $baseLink = NULL)
    {
        $em = \Factory\EntityManagerFactory::getEntityManger();
       
        $url = $baseLink;
        $urlExterna = $entity->getUrlExterna();
        $sigla = mb_strtolower($entity->getSite()->getSigla());
//        
//        if($entity->getId() == "218"){
//            echo "->>>>".$entity->getIdEntidade()."<br>";
//
//            echo "->>>>".$entity->getFuncionalidadeMenu()->getEntidade()."<br>";
//            $id_entidade = $entity->getIdEntidade();
//            $entidade = $entity->getFuncionalidadeMenu()->getEntidade();
//            
//            $retorno =  $em->find($entidade, $id_entidade);
//            
//
//            
//            echo "->>>>>".$retorno->getSigla()."<br>";
//        
//            echo $entity->getIdEntidade(). "-----".$id_entidade;
//            
//            
//            $teste = $em->find('Entity\Site', $entity->getIdEntidade());
//            
//            die();
//        }
//        
        
        
        
        
                
        if ($entity->getFuncionalidadeMenu() and $entity->getFuncionalidadeMenu()->getEntidade() == "Entity\Site" ){
            $subsite = "";
            $subsite = $em->find('Entity\Site', $entity->getIdEntidade());
            if($subsite) $sigla = mb_strtolower($subsite->getSigla());
        }
        
        if ($sigla != 'sede') {
            $url .= $sigla . '/';
        }
        
        if (empty($urlExterna) and method_exists($entity->getFuncionalidadeMenu(), 'getEntidade')) {

            $funcionalidade = $entity->getFuncionalidadeMenu()->getEntidade();

            // Se é uma Página define URL da página
            if ($funcionalidade == 'Entity\PaginaEstatica') {
                $url .= $entity->getFuncionalidadeMenu()->getUrl() . '/' . $entity->getIdEntidade();
            }
            // Caso contrário define URL da listagem de conteúdos da funcionalidade
            elseif(!$entity->getFuncionalidadeMenu()->getUrl()) {
                $url = $baseLink.$sigla;
            }
            else {
                $url .= $entity->getFuncionalidadeMenu()->getUrl();
            }
        } else {
            $url = $entity->getUrlExterna();
        }

        return $url;
    }

    
    /**
     *
     * @param \Entity\Menu $entity
     * @return string
     */
    public static function generateUrlSubsite(\Entity\Menu $entity, $baseLink = NULL, $teste)
    {
        $url = $baseLink;
        $urlExterna = $entity->getUrlExterna();
        $sigla = mb_strtolower($entity->getSite()->getSigla());

        if ($sigla != 'sede') {
            $teste;
            //$url .= $sigla . '/';
        }
        
        if (empty($urlExterna) and method_exists($entity->getFuncionalidadeMenu(), 'getEntidade')) {
            if(!$entity->getFuncionalidadeMenu()->getUrl()) {
                $url = $baseLink.$sigla;
            }else {
                $url .= $entity->getFuncionalidadeMenu()->getUrl();
            }
        } else {
            $url = $entity->getUrlExterna();
        }

        return $url;
    }
    /**
     * Retorna o naked domain
     * @return string
     */
    public static function getServerName()
    {
        // Remove www do início da string
        $serverName = preg_replace('/^(www\.)/', '', $_SERVER['HTTP_X_FORWARDED_HOST'], 1);

        return $serverName;
    }

}