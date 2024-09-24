<?php
namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Publicacao as PublicacaoEntity;
use CMS\Service\ServiceUpload\PublicacaoUpload;
use Helpers\Session;
use Helpers\Upload;

/**
 * Description of Publicacao
 *
 * @author Luciano
 */
class Publicacao extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @var PublicacaoUpload
     */
    protected $upload;

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Publicacao $entity
     */
    public function __construct(EntityManager $em, PublicacaoEntity $entity, Session $session)
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
            'title'             => $entity->getTitulo(),
            'description'       => $entity->getConteudo(),
            'publish'           => $entity->getPublicado(),
            'publish_date'      => $entity->getDataInicial(),
            'unpublish_date'    => $entity->getDataFinal(),
            'author'            => $entity->getAutor(), 
            'url'               => $_SERVER['HOST'] . '/uploads/publicacao/' . $this->getEm()->find('Entity\Publicacao', $entity->getId())->getArquivo(),
        );
    }

    /**
     *
     * @return PublicacaoUpload
     */
    public function getUpload()
    {
        if (empty($this->upload)) {
            $this->upload = new PublicacaoUpload();
        }

        return $this->upload;
    }

    /**
     *
     * @param PublicacaoUpload $upload
     */
    public function setUpload(PublicacaoUpload $upload)
    {
        $this->upload = $upload;
    }

    /**
     *
     * @param array $dados
     * @return array
     */
    protected function doFileUpload(array $dados)
    {
        //Verifica se o arquivo anterior foi excluido
        if (!empty($dados['arquivoExcluido'])) {
            $upload = $this->getUpload();
            $upload->setFile(getcwd() . "/uploads/publicacao/" . $dados['arquivoExcluido']);
            $upload->delete();
        }

        //Verifica se o arquivo foi modificado
        if (!empty($dados['arquivo']) && $dados['arquivo'] !== $dados['arquivoAtual']) {
            $upload = $this->getUpload();
            $upload->setFile(getcwd() . "/uploads/temp/" . $dados['arquivo']);
            $nome = Upload::testaNome($dados['arquivo'], getcwd() . "/uploads/publicacao");
            $upload->rename(getcwd() . "/uploads/publicacao/" . $nome);
            $dados['arquivo'] = $nome;
        }

        //Apaga os elementos de arquivo excluido e atual
        unset($dados['arquivoExcluido']);
        unset($dados['arquivoAtual']);

        return $dados;
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
        $dados = $this->doFileUpload($dados);

        try {
            $action = empty($dados['id']) ? "inserido" : "alterado";
            $entity = parent::save($dados);

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);

            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $action = empty($dados['id']) ? "inserir" : "alterar";
            $this->getLogger()->error($exc->getMessage());
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
                $servidor = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($servidor);
                $this->getEm()->flush();

                /* Remove no índice do Solr */
                $this->getSolrManager()->delete('Entity\Publicacao', $id);
            }
            $this->getEm()->commit();
            $success = "Ação executada com sucesso";
            $response = 1;
            $this->getLogger()->info("Publicações " . implode(",", $ids) . " foram excluidas.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
    
    public function updateOrdem(array $dados){
        try{
            $this->getEm()->beginTransaction();
            
            foreach($dados as $id => $ordem){
                $this->getEm()->createQuery("UPDATE Entity\Publicacao p SET p.ordem = {$ordem} WHERE p.id = {$id} ")->execute();
                /*$entity = $this->getEm()->find($this->getNameEntity(), $id);
                $entity->setOrdem($ordem);
                $this->getEm()->persist($entity);*/
               //$entity = parent::save(array('id' =>$id ,'ordem' => $ordem));
            }
            
            $this->getEm()->commit();
            $success = "Ação executada com sucesso";
            $response = 1;
        } catch (\Exception $ex) {
            $this->getEm()->rollback();
            $this->getLogger()->error($ex->getMessage());
            $error[] = "Erro ao atualizar registro";
        }
        
        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
        
}
