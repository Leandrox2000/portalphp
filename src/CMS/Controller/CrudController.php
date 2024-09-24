<?php

namespace CMS\Controller;

use LibraryController\AbstractController;
use Helpers\String as StringHelper;
use CMS\Service\ServiceRepository\SolrAwareInterface;

/**
 *
 */
abstract class CrudController extends AbstractController {

    abstract public function getService();

    /**
     *
     * @var type
     */
    protected $helperString;

    /**
     *
     * @var \CMS\Service\ServiceRepository\Hash
     */
    protected $hashService;

    /**
     *
     * @return StringHelper
     */
    public function getHelperString() {
        if (empty($this->helperString)) {
            $this->setHelperString(new StringHelper());
        }
        return $this->helperString;
    }

    public function setHelperString(StringHelper $helperString) {
        $this->helperString = $helperString;
    }

    /**
     *
     * @return \CMS\Service\ServiceRepository\Hash
     */
    public function getHashService() {
        if (!isset($this->hashService)) {
            $this->setHashService(new \CMS\Service\ServiceRepository\Hash($this->getEm(), new \Entity\Hash(), $this->getSession()));
        }
        return $this->hashService;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\Hash $hashService
     */
    public function setHashService(\CMS\Service\ServiceRepository\Hash $hashService) {
        $this->hashService = $hashService;
    }

    /**
     *
     * @param string $ids
     * @return string
     */
    public function getHtmlImagens($ids = "") {
        if (!empty($ids)) {
            //Organiza os ids em imagens
            $ids = explode(',', $ids);

            //Busca as imagens
            $imagens = $this->getEm()->getRepository('Entity\Imagem')->getImagemIds($ids);
			
            //Cria o html com as imagens
            $html = "";

            $html .= "<div class='gallerywrapper'>";
            $html .= "<ul  class='imagelist'>";

            //Percorre as imagens e monta o HTML
            foreach ($imagens as $img) {
                $html .= "<li id='img{$img->getId()}' name='".$img->getId()."'>";
                $html .= "<img src='uploads/ckfinder/images/{$this->getHelperString()->removeSpecial($img->getPasta()->getCategoria()->getNome())}/{$img->getPasta()->getCaminho()}/{$img->getImagem()}' />";
                $html .= "<span><a class='delete' href='javascript:excluirImagem({$img->getId()})'></a></span>";
                $html .= "</li>";
            }

            $html .= "</ul>";
            $html .= "</div>";


            return $html;
        } else {
            return '';
        }
    }

    /**
     *
     * @param string $ids
     * @return string
     */
    public function getHtmlImagenJcrop($ids = "") {
        if (!empty($ids)) {
            //Organiza os ids em imagens
            $ids = explode(',', $ids);

            //Busca as imagens
            $imagens = $this->getEm()->getRepository('Entity\Imagem')->getImagemIds($ids);
			
            //Cria o html com as imagens
            $html = "";

            $html .= "<div class='gallerywrapper'>";
            $html .= "<ul style='list-style: none;'>";

            //Percorre as imagens e monta o HTML
            foreach ($imagens as $img) {
                $html .= "<li id='img{$img->getId()}' name='".$img->getId()."'>";
                $html .= "<img src='uploads/ckfinder/images/{$this->getHelperString()->removeSpecial($img->getPasta()->getCategoria()->getNome())}/{$img->getPasta()->getCaminho()}/{$img->getImagem()}'/>";
                $html .= "<span><a class='delete' href='javascript:excluirImagem({$img->getId()})'></a></span>";
                $html .= "</li>";
            }

            $html .= "</ul>";
            $html .= "</div>";


            return $html;
        } else {
            return '';
        }
    }
    /**
     *
     * @param string $ids
     * @return string
     */
    public function getHtmlImagensArray($ids) {
        if (!empty($ids)) {

            //Cria o html com as imagens
            $html = "";

            $html .= "<div class='gallerywrapper'>";
            $html .= "<ul  class='imagelist'>";

            //Percorre as imagens e monta o HTML
            foreach ($ids as $id) {
            	$img = $this->getEm()->getRepository('Entity\Imagem')->find($id);
                $html .= "<li id='img{$img->getId()}' name='".$img->getId()."'>";
                $html .= "<img src='uploads/ckfinder/images/{$this->getHelperString()->removeSpecial($img->getPasta()->getCategoria()->getNome())}/{$img->getPasta()->getCaminho()}/{$img->getImagem()}' />";
                $html .= "<span><a class='delete' href='javascript:excluirImagem({$img->getId()})'></a></span>";
                $html .= "</li>";
            }

            $html .= "</ul>";
            $html .= "</div>";


            return $html;
        } else {
            return '';
        }
    }
    
    /**
     *
     * @param string $ids
     * @return string
     */
    public function getHtmlImagensGaleria($id = "") {
    	return $this->getEm()->getRepository('Entity\Imagem')->getImagemIdsGaleria($id);
    }
    
    /**
     *
     * @return JSON
     */
    public function alterarStatus($array = null, $status = null) {
        $array = ($array == null) ? $this->getParam()->getArray("sel") : $array;
        $status = ($status == null) ? $this->getParam()->getInt("status") : $status;
        $retorno = $this->getService()->alterarStatus($array, $status);

        if ($this->getService() instanceof SolrAwareInterface) {
            foreach ($array as $id) {
                /* Atualiza índice do Solr */
                $className = $this->getService()->getNameEntity();
                $entityName = $this->getEm()->getClassMetadata($className)->getName();
                $entity = $this->getEm()
                        ->getRepository($entityName)
                        ->find($id);
                $dadosSolr = $this->getService()->getDadosSolr($entity);
                $solrManager = new \Helpers\SolrManager();
                $solrManager->save($dadosSolr);
            }
        }

        return json_encode($retorno);
    }

    public function alterarStatusValidacao($array = null, $status = null, $table = null, $entity = null, $responderJson = true) {
        
        $array = ($array == null) ? $this->getParam()->getArray("sel") : $array;
        $status = ($status == null) ? $this->getParam()->getInt("status") : $status;
        $table = ($table == null) ? $this->getParam()->getString("table") : $table;
        $entity = ($entity == null) ? $this->getParam()->getString("entity") : $entity;
        $retorno = $this->getService()->alterarStatusValidacao($array, $status, $table, $entity);

        if ($this->getService() instanceof SolrAwareInterface) {
            foreach ($array as $id) {
                /* Atualiza índice do Solr */
                $className = $this->getService()->getNameEntity();
                $entityName = $this->getEm()->getClassMetadata($className)->getName();
                $entity = $this->getEm()
                    ->getRepository($entityName)
                    ->find($id);
                $dadosSolr = $this->getService()->getDadosSolr($entity);
                $solrManager = new \Helpers\SolrManager();
                $solrManager->save($dadosSolr);
            }
        }
        
        if($responderJson) {
            return json_encode($retorno);
        } else {
            return $retorno;
        }
    }

    public function alterarStatusGaleria($array = null, $status = null) {
        $array = ($array == null) ? $this->getParam()->getArray("sel") : $array;
        $status = ($status == null) ? $this->getParam()->getInt("status") : $status;
        $retorno = $this->getService()->alterarStatusGaleria($array, $status);

        if ($this->getService() instanceof SolrAwareInterface) {
            foreach ($array as $id) {
                /* Atualiza índice do Solr */
                $className = $this->getService()->getNameEntity();
                $entityName = $this->getEm()->getClassMetadata($className)->getName();
                $entity = $this->getEm()
                    ->getRepository($entityName)
                    ->find($id);
                $dadosSolr = $this->getService()->getDadosSolr($entity);
                $solrManager = new \Helpers\SolrManager();
                $solrManager->save($dadosSolr);
            }
        }

        return json_encode($retorno);
    }
    
    public function desvinculaRegistros($array)
    {
        foreach ($array as $value) {
            $resultDelete = $this->getService()->delete($arrayDelete);
        }
    }

    /**
     *
     * @return jSON
     */
    public function delete() {
        $resp = array(
            'error' => array(),
            'response' => 0,
            'success' => ''
        );

        try {
            $ids = implode(',', $_REQUEST['sel']);

            //Pega os ids que vieram por parâmetro
            $array = $this->getParam()->getArray("sel");

            if(empty($array)) {
                throw new \Exception('Nenhum registro foi selecionado');
            }

            //Instancia o repository
            $repository = $this->getEm()->getRepository($this->getService()->getNameEntity());

            //Busca os registros que irá deletar e que irá despublicar
            $arrayNaoPublicar = $repository->getIdsPublicacao($array, 1);
            $arrayDelete = $repository->getIdsPublicacao($array, 0);

            // Verifica se o registro é indexável
            if ($this->getService() instanceof SolrAwareInterface) {
                //Deleta os registros da instância do SOLR
                /* Remove do índice do Solr */
                $entityName = $this->getService()->getNameEntity();
                $solrManager = new \Helpers\SolrManager();
                $solrManager->bulkDelete($entityName, $arrayDelete);
            }

            //Apenas se for entidade menu
            if($this->getService()->getNameEntity() == 'Entity\Menu') {
                //Valida se  o subsite logado é do registro
                $resultDelete = $this->getService()->delete($array);
            } else {
                $aux = explode('\\', $entityName);
                if($aux[1] == "PaginaEstatica") {
                    $entity_atributo    = "pagina_estatica";
                }else{
                    $entity_atributo    = strtolower($aux[1]);
                }
                $entity_tabela      = 'tb_'.$entity_atributo.'_site';
                
                //Deleta os registros
                if($this->getService()->validaVinculo($ids,$entity_tabela,$entity_atributo)) {
                    if ($this->getService()->validaCompartilhamentos()) {
                        if(count($arrayDelete)) {
                            $resultDelete = $this->getService()->delete($arrayDelete, $arrayDelete);
                        }
                        if(count($arrayNaoPublicar) && (!isset($resultDelete['response']) || $resultDelete['response'])) {
                            $resultDelete = $this->alterarStatusValidacao($arrayNaoPublicar, 0, $entity_atributo, $entityName, false);
                            $resultDelete['success'] = 'Este(s) registro(s) foi(foram) despublicado(s). É necessário realizar a ação de exclusão novamente para sua exclusão definitiva.';
                        }
                    } else {
                        if (!$this->getService()->verificarStatus($array)) {
                            $resultDelete = $this->getService()->delete($arrayDelete);
                        } else {
                            $resp['response'] = 1;
                            $resp['success'] = 'Não é possível remover um registro publicado.';
                            $this->getEm()->rollback();
                        }
                    }
                } else {
                    $resp['response'] = 1;
                    $resp['success']  = 'Não foi possível executar esta ação.';
                    $this->getEm()->rollback();
                }
            }

            if ($resultDelete == null) {
                $resultDelete = array();
            }

            //Verificação para bular a gamba de sempre dar o response Positivo
//            if (key_exists("error", $resultDelete) && key_exists("response", $resultDelete) && key_exists("success", $resultDelete)) {
//                return json_encode($resultDelete);
//            }
            
            if(!isset($resultDelete['response'])) {
                //$resp['publicados'] = $arrayNaoPublicar;
                $resp['response'] = 1;
                $resp['success'] = 'Ação realizada com sucesso!';
            } else {
                $resp = $resultDelete;
            }
        }
        catch(\Exception $e) {

            $resp['error'][] = $e->getMessage();
        }
        return json_encode($resp);
    }

    /**
     *
     * @param string $sigla
     * @return boolean
     */
    public function verifyPermission($sigla) {
        $session = $this->getSession()->get('user');
        return isset($session['permissoesUser'][$sigla]);
    }

    /**
     *
     * @return array
     */
    public function getUserSession() {
        if ($this->getSession()->get('user')) {
            return $this->getSession()->get('user');
        } else {
            return array();
        }
    }

    /**
     *
     * @return String
     */
    public function getHash() {
        //Gera o hash do repositpory
        $hash = md5(rand(1, 1000000));

        //Salva o hash
        $this->getHashService()->save($hash);

        //retorn o hash
        return $hash;
    }

    /**
     *
     * @param integer $id
     */
    public function visualizar($id) {
        // Coloca www se necessário
        $www = preg_match('/^www/', $_SERVER['SERVER_NAME']) ? 'www.' : '';
        $routeParam = $this->getParam()->get('route');
        $route = !empty($routeParam) ? $routeParam : '/';

        //Gera o hash
        $hash = $this->getHash();

        //Redireciona para a página
        $url = "http://" . $www . URL_PORTAL . $route;
        $url = str_replace(array('#id#', '#hash#'), array($id, $hash), $url);

        return json_encode(array('url' => $url));
    }
}
