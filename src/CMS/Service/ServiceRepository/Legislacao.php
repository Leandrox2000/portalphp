<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager as EntityManager;
use Entity\Legislacao as LegislacaoEntity;
use CMS\Service\ServiceUpload\LegislacaoUpload;
use Helpers\Upload;
use Helpers\Session;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe Legislacao
 *
 * Responsável pelas ações na entidade Legislacao
 * @author join-ti
 */
class Legislacao extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @var LegislacaoUpload
     */
    protected $upload;

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Legislacao $entity
     */
    public function __construct(EntityManager $em, LegislacaoEntity $entity, Session $session)
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
            'description'       => $entity->getDescricao(),
            'publish'           => $entity->getPublicado(),
            'publish_date'      => $entity->getDataInicial(),
            'unpublish_date'    => $entity->getDataFinal(),
            'url'               => \Helpers\Url::generateRoute('legislacao'),
        );
    }

    /**
     *
     * @return BoletimEletronicoUpload
     */
    public function getUpload()
    {
        if (!isset($this->upload)) {
            $this->upload = new LegislacaoUpload();
        }
        return $this->upload;
    }

    /**
     *
     * @param LegislacaoUpload $upload
     */
    public function setUpload(LegislacaoUpload $upload)
    {
        $this->upload = $upload;
    }

    /**
     *
     * @param string $arquivoNovo
     * @param string $arquivoAtual
     * @param string $arquivoExcluido
     * @throws \Exception
     */
    public function uploadArquivo($arquivoNovo, $arquivoAtual, $arquivoExcluido)
    {
        $nome = $arquivoNovo;

        try {
            //Verifica se o arquivo foi modificado
            if (!empty($arquivoNovo) && $arquivoNovo !== $arquivoAtual) {
                $upload = $this->getUpload();
                $upload->setFile(getcwd() . "/uploads/temp/" . $arquivoNovo);
                $nome = Upload::testaNome($arquivoNovo, getcwd() . "/uploads/legislacao");
                $upload->rename(getcwd() . "/uploads/legislacao/" . $nome);
            }

            //Verifica se o arquivo anterior foi excluido
            if (!empty($arquivoExcluido)) {
                $upload = $this->getUpload();
                $upload->setFile(getcwd() . "/uploads/legislacao/" . $arquivoExcluido);
                $upload->delete();
            }
        } catch (\Exception $ex) {
            $this->getLogger()->error($ex->getMessage());
            throw new \Exception("Erro ao salvar arquivo");
        }

        return $nome;
    }

    /**
     *
     * @param array $dados
     * @return array
     */
    public function save($dados)
    {
        $error = array();
        $success = "";
        $response = 0;

        try {
            $action = !empty($dados['id']) ? "alterado" : "inserido";

            //Faz inserção ou edição do arquivo
            $dados['arquivo'] = $this->uploadArquivo($dados['arquivo'], $dados['arquivoAtual'], $dados['arquivoExcluido']);

            //Apaga os elementos de arquivo excluido e atual
            unset($dados['arquivoExcluido']);
            unset($dados['arquivoAtual']);

            $sites = $dados['sites'];

            //Verifica se for update deleta vinculos e pais não selecionado
            if($action == 'alterado') {

                $sitesSession = $_SESSION['user']['subsites'];

                $connection = $this->getEm()->getConnection();

                foreach ($sitesSession as $site) {
                    $connection->query("DELETE FROM tb_legislacao_site WHERE id_legislacao = {$dados['id']} AND id_site = {$site}");

                    $connection->query("DELETE FROM tb_pai_legislacao_site WHERE id_legislacao = {$dados['id']} AND id_site = {$site}");
                }

                $legislacao = $this->getEm()->getRepository('Entity\Legislacao')->find($dados['id']);

                $sitesArrPai = $legislacao->getPaiSites();

                $sitesArr = $legislacao->getSites();

                foreach ($sites as $site) {
                    $rSite = $this->getEm()->getReference('Entity\Site', $site);
                    $sitesArr->add($rSite);
                    $sitesArrPai->add($rSite);
                }

                $dados['paiSites'] = $sitesArrPai;

                $dados['sites'] = $sitesArr;

            } else {
                $sitesArr = new ArrayCollection();

                foreach ($sites as $site) {
                    $rSite = $this->getEm()->getReference('Entity\Site', $site);
                    $sitesArr->add($rSite);
                }

                //Salva subsites e pai do registro
                $dados['paiSites'] = $sitesArr;

                $dados['sites'] = $sitesArr;
            }

            $entity = parent::save($dados);

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);

            if (empty($dados['arquivo'])) {
                $dadosSolr['url'] = $entity->getUrl();
            } else {
                $dadosSolr['url'] = $_SERVER["HOST_NAME"]."/uploads/legislacao/".$dados['arquivo'];
            }

            /* --------------------------------------------------- */

            $success = "Registro $action com sucesso!";
            $response = 1;
        } catch (\Exception $e) {
            $error[] = $e->getMessage();
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     * Metodo delete
     *
     * Deleta um grupo de registros e seus respectivos arquivos
     * @param array $ids
     * @return boolean
     */
    public function delete(array $ids)
    {
        $error = array();
        $response = 0;
        $success = "Ação executada com sucesso";

        try {
            //Inicia a transação
            $this->getEm()->beginTransaction();

            foreach ($ids as $id) {
                $entity = $this->getEm()->getReference("Entity\Legislacao", $id);
                $this->getEm()->remove($entity);
                $this->getEm()->flush();
            }

            $this->getEm()->commit();

            //Busca os arquivos
            $arquivos = $this->getEm()->getRepository("Entity\Legislacao")->getArquivosLegislacao($ids);

            //Percorre os arquivos e os exclui
            foreach ($arquivos as $arq) {
                $this->getUpload()->setFile(getcwd() . "/uploads/legislacao/" . $arq['arquivo']);
                $this->getUpload()->delete();
            }

            $response = 1;
        } catch (\Exception $e) {
            $this->getEm()->rollback();
            $this->getLogger()->error($e->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
}
