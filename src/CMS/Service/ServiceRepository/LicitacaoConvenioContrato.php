<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\LicitacaoConvenioContrato as LicitacaoConvenioContratoEntity;
use Entity\ArquivoLcc as ArquivoEntity;
use CMS\Service\ServiceUpload\LicitacaoConvenioContratoUpload;
use Helpers\Upload;
use Helpers\Session;

/**
 * Description of LicitacaoConvenioContrato
 *
 * @author Join
 */
class LicitacaoConvenioContrato extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @var LicitacaoConvenioContratoUpload
     */
    private $upload;

    /**
     *
     * @var ArquivoLcc
     */
    private $serviceArquivo;

    /**
     *
     * @var ArquivoEntity
     */
    private $entityArquivo;

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\LicitacaoConvenioContratoEntity $entity
     */
    public function __construct(EntityManager $em, LicitacaoConvenioContratoEntity $entity, Session $session)
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
            'title'             => $entity->getObjeto(),
            'description'       => $entity->getObservacoes(),
            'publish'           => $entity->getPublicado(),
            'publish_date'      => $entity->getDataInicial(),
            'unpublish_date'    => $entity->getDataFinal(),
            'url'               => \Helpers\Url::generateRoute('licitacoesConveniosContratos', 'detalhes', $entity),
        );
    }

    /**
     *
     * @return \CMS\Service\ServiceUpload\LicitacaoConvenioContratoUpload
     */
    public function getUpload()
    {
        if (empty($this->upload)) {
            $this->setUpload(new LicitacaoConvenioContratoUpload());
        }
        return $this->upload;
    }

    /**
     *
     * @param \CMS\Service\ServiceUpload\LicitacaoConvenioContratoUpload $upload
     */
    public function setUpload(LicitacaoConvenioContratoUpload $upload)
    {
        $this->upload = $upload;
    }

    /**
     *
     * @return ArquivoLcc
     */
    public function getServiceArquivo()
    {
        if (empty($this->serviceArquivo)) {
            $this->setServiceArquivo(new ArquivoLcc($this->getEm(), $this->getEntityArquivo(), $this->getSession()));
        }
        return $this->serviceArquivo;
    }

    /**
     *
     * @param ArquivoLcc $serviceArquivo
     */
    public function setServiceArquivo(ArquivoLcc $serviceArquivo)
    {
        $this->serviceArquivo = $serviceArquivo;
    }

    /**
     *
     * @return ArquivoEntity
     */
    public function getEntityArquivo()
    {
        if (empty($this->entityArquivo)) {
            $this->setEntityArquivo(new ArquivoEntity());
        }
        return $this->entityArquivo;
    }

    /**
     *
     * @param \Entity\ArquivoLcc $entityArquivo
     */
    public function setEntityArquivo(ArquivoEntity $entityArquivo)
    {

        $this->entityArquivo = $entityArquivo;
    }

    /**
     *
     * @param getArquivosIds $arrayIds
     * @return array
     */
    public function getArquivosIds($arrayIds)
    {
        //Busca e organiza os arquivos a serem excluídos
        $arquivos = $this->getEm()->getRepository('Entity\ArquivoLcc')->getArquivosLcc($arrayIds);
        $arrayArquivos = array();

        foreach ($arquivos as $arq) {
            $arrayArquivos[] = $arq['nome'];
        }

        return $arrayArquivos;
    }

    /**
     *
     * @param String $arquivo
     */
    public function uploadArquivo($arquivo)
    {
        $upload = $this->getUpload();
        $upload->setFile(getcwd() . "/uploads/temp/" . $arquivo);
        $nome = Upload::testaNome($arquivo, getcwd() . "/uploads/licitacaoConvenioContrato");
        $upload->rename(getcwd() . "/uploads/licitacaoConvenioContrato/" . $nome);
        return $nome;
    }

    /**
     *
     * @param String $arquivos
     * @param integer $lcc
     */
    public function uploadArquivos($arquivos, $arquivosAntigos, $lcc)
    {
        //Agrupa os arquivos novos em uma array
        $arrayArquivos = explode("|", $arquivos);

        //Agrupa os arquivos antigos em array
        $arquivosAntigos = array();

        if (!empty($arquivosAntigos)) {
            foreach (explode("|", $arquivosAntigos) as $arq) {
                $arquivosAntigos[$arq] = $arq;
            }
        }

        //Percorre os arquivos novos
        foreach ($arrayArquivos as $arq) {

            //Verifica a existência do arquivo, se é vazio e se já não está cadastrado
            if (!empty($arq)) {
                $arrayArq = explode(";;", $arq);
                $nomeArq = $arrayArq[0];
                $nomeOrig = $arrayArq[1];

                if (!isset($arquivosAntigos[$nomeArq])) {
                    //Verifica se o arquivo existe e o insere no FTP e no banco de dados
                    if ($this->getUpload()->fileExist(getcwd() . "/uploads/temp/" . $nomeArq)) {
                        $nome = $this->uploadArquivo($nomeArq);

                        $entity = new \Entity\ArquivoLcc();
                        $entity->setLicitacaoConvenioContrato($this->getEm()->getReference('Entity\LicitacaoConvenioContrato', $lcc));
                        $entity->setNome($nome);
                        $entity->setNomeOriginal($nomeOrig);

                        $this->getEm()->persist($entity);
                        $this->getEm()->flush();
                    }
                }
            }
        }
    }

    /**
     *
     * @param Array $ids
     */
    public function deleteArquivosFtp($arquivos)
    {

        foreach ($arquivos as $arquivo) {
            if ($this->getUpload()->fileExist(getcwd() . "/uploads/licitacaoConvenioContrato/" . $arquivo)) {
                $this->getUpload()->setFile(getcwd() . "/uploads/licitacaoConvenioContrato/" . $arquivo);
                $this->getUpload()->delete();
            }
        }
    }

    /**
     *
     * @param String $ids
     */
    public function deleteArquivosForm($ids)
    {
        if (!empty($ids)) {
            //Quebra os ids em array
            $arrayIds = explode(',', $ids);
            array_pop($arrayIds);

            //Busca os arquivos do banco pelo array de ids
            $arrayArquivos = $this->getArquivosIds($arrayIds);

            $this->getServiceArquivo()->delete($arrayIds);
            $this->deleteArquivosFtp($arrayArquivos);
        }
    }

    /**
     *
     * @param String $arquivosNovos
     * @param String $arquivosAntigos
     * @param String $arquivosExcluidos
     * @param integer $lcc
     * @throws \Exception
     */
    public function upload($arquivosNovos, $arquivosAntigos, $arquivosExcluidos, $lcc)
    {
        try {
            //Deleta os arquivos requisitados
            $this->deleteArquivosForm($arquivosExcluidos);

            //Envia os arquivos
            $this->uploadArquivos($arquivosNovos, $arquivosAntigos, $lcc);
        } catch (\Exception $ex) {
            $this->getLogger()->error($ex->getMessage());
            throw new \Exception("Erro ao salvar arquivo");
        }
    }

    /**
     *
     * @param array $dados
     * @return array
     */
    public function save(array $dados)
    {
        //Cria as variáveis que armazenarão os retornos
        $response = 0;
        $error = array();
        $success = "";

        try {
            //Inicia a transação
            $this->getEm()->beginTransaction();

            //Formata a mensagem
            $action = empty($dados['id']) ? "inserido" : "alterado";

            //armazena os dados dos arquivos e salva o formulário
            $arquivosNovos = $dados['arquivosNovos'];
            $arquivosAntigos = $dados['arquivosAntigos'];
            $arquivosExcluidos = $dados['arquivosExcluidos'];
            unset($dados['arquivosExcluidos']);
            unset($dados['arquivosAntigos']);
            unset($dados['arquivosNovos']);

            $entity = parent::save($dados);

            /* Atualiza índice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);

            //Faz o upload dos arquivos
            $this->upload($arquivosNovos, $arquivosAntigos, $arquivosExcluidos, $entity->getId());

            //Commita a transação
            $this->getEm()->commit();
            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
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
            //Inicia a transação
            $this->getEm()->beginTransaction();

            //Busca os arquivos no banco
            $arquivos = $this->getEm()->getRepository('Entity\ArquivoLcc')->getArquivosLccIds($ids);

            //Organiza os ids dos arquivos
            $idsArquivos = "";

            foreach ($arquivos as $arq) {
                $idsArquivos .= $arq->getId() . ",";
            }
            $idsArquivos = substr($idsArquivos, 0, -1);

            //Deleta os arquivos do banco
            $this->getEm()->createQuery("DELETE FROM Entity\ArquivoLcc arq WHERE arq.id IN ({$idsArquivos})")->execute();

            //Deleta as lccs
            parent::delete($ids);

            //Percorre e deleta os arquivos do FTP
            foreach ($arquivos as $arq) {
                //Deleta os arquivos do ftp
                if ($this->getUpload()->fileExist(getcwd() . "/uploads/licitacaoConvenioContrato/" . $arq->getNome())) {
                    $this->getUpload()->setFile(getcwd() . "/uploads/licitacaoConvenioContrato/" . $arq->getNome());
                    $this->getUpload()->delete();
                }
            }

            $this->getEm()->commit();
            $success = "Ação executada com sucesso";
            $response = 1;

            $this->getLogger()->info("Licitações, convênios e contrator " . implode(",", $ids) . " foram excluidos.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error[] = "Não foi possível excluir o(s) registro(s) selecionado(s)";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
