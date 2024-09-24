<?php
namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\Noticia as NoticiaEntity;
use Helpers\Param;
use Helpers\Session;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Noticia 
 *
 * @author Luciano
 */
class Noticia extends BaseService implements SolrAwareInterface
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\Noticia $entity
     */
    public function __construct(EntityManager $em, NoticiaEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    public function getDadosSolr($entity)
    {
        return array(
            'entity_name' => $this->getNameEntity(),
            'entity_id' => $entity->getId(),
            'title' => $entity->getTitulo(),
            'description' => $entity->getConteudo(),
            'publish' => $entity->getPublicado(),
            'publish_date' => $entity->getDataInicial(),
            'unpublish_date' => $entity->getDataFinal(),
            'url' => \Helpers\Url::generateRoute('noticias', 'detalhes', $entity),
        );
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
        $param = new Param();
//        $flag = $dados['flagNoticia'][0];
//        $dados['flagNoticia'] = $flag;
        $ordem = !empty($dados['ordemGalerias']) ? explode(',', $dados['ordemGalerias']) : array();
        unset($dados['ordemGalerias']);
        $cont = 1;
        foreach ($ordem as $index => $value) {
            $ordemGaleria[$value] = $cont;
            $cont++;
        }


        try {
            if (empty($dados['id'])) {
                $action = "inserido";
                $dados['slug'] = \Helpers\Url::slugify($dados['titulo']);
            } else {
                $action = "alterado";
            }

            //Inicia a transaÃ§Ã£o
            $this->getEm()->beginTransaction();

            //Armazena os ids das galerias em array
            $arrayGalerias = !empty($dados['idsGalerias']) ? explode(',', $dados['idsGalerias']) : array();
            unset($dados['idsGalerias']);

            //Salva a noticia
            $action = empty($dados['id']) ? "inserido" : "alterado";

            $sites = $dados['sites'];

            //Verifica se for update deleta vinculos e pais nÃ£o selecionado
            if ($action == 'alterado') {

                $sitesSession = $_SESSION['user']['subsites'];

                $connection = $this->getEm()->getConnection();

                foreach ($sitesSession as $site) {
                    $connection->query("DELETE FROM tb_noticia_site WHERE id_noticia = {$dados['id']} AND id_site = {$site}");

                    $connection->query("DELETE FROM tb_pai_noticia_site WHERE id_noticia = {$dados['id']} AND id_site = {$site}");
                }

                $agenda = $this->getEm()->getRepository('Entity\Noticia')->find($dados['id']);

                $sitesArrPai = $agenda->getPaiSites();

                $sitesArr = $agenda->getSites();

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

            //Verifica se a aÃ§Ã£o Ã© de exclusÃ£o ou ediÃ§Ã£o
            if (!empty($dados['id'])) {
                //Seleciona as relaÃ§Ãµes dessa pÃ¡gina estÃ¡tica com alguma galeria
                // $galerias = $this->getEm()->createQuery("SELECT peg FROM Entity\PaginaEstaticaGaleria peg JOIN peg.paginaEstatica pa WHERE pa.id = {$dados['id']}")->execute();
                $galerias = $this->getEm()->createQuery("SELECT ng FROM Entity\NoticiaGaleria ng JOIN ng.noticia no WHERE no.id = {$dados['id']}")->execute();

                //Percorre e deleta a relaÃ§Ã£o com as galerias
                foreach ($galerias as $galeria) {
                    $this->getEm()->remove($galeria);
                    $this->getEm()->flush();
                }

                //Armazena o id da pÃ¡gina estÃ¡tica
                $idNoticia = $dados['id'];
            } else {
                //Armazena o id da pÃ¡gina estÃ¡tica
                $idNoticia = $entity->getId();
            }

            //Insere os registros na tabela NoticiaGaleria e monta a collection com as relaÃ§Ãµes
            $noticiaGaleria = new \Doctrine\Common\Collections\ArrayCollection();

            foreach ($arrayGalerias as $idGaleria) {
                $pos = null;
                $noticiaGaleria = new \Entity\NoticiaGaleria();
                $noticiaGaleria->setGaleria($this->getEm()->getReference('Entity\Galeria', $idGaleria));
                $noticiaGaleria->setNoticia($this->getEm()->getReference('Entity\Noticia', $idNoticia));
                $pos = $param->get('posicao' . $idGaleria);
                if ($pos) {
                    $noticiaGaleria->setPosicaoPagina($param->get('posicao' . $idGaleria));
                } else {
                    $noticiaGaleria->setPosicaoPagina(2);
                }
                $noticiaGaleria->setOrdemGaleria($ordemGaleria[$idGaleria]);
                $this->getEm()->persist($noticiaGaleria);
                $this->getEm()->flush();
            }

            //Commita a transaÃ§Ã£o
            $this->getEm()->commit();

            /* Atualiza Ã­ndice do Solr */
            $dadosSolr = $this->getDadosSolr($entity);
            $this->getSolrManager()->save($dadosSolr);


            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (\Exception $exc) {
            die($exc->getMessage());
            $action = empty($dados['id']) ? "Inserir" : "Alterar";
            $error[] = $exc->getMessage();
        }

        return array('error' => $error, 'response' => $response, 'success' => $success, 'action' => $action, 'idNoticia' => $idNoticia);
    }

    /**
     *
     * @param array $ids
     * @param array $forceIds  Não foi possível descobrir o porque do parâmetro $ids ser ignorado,
     *                         por isso o ideal foi criar um novo parâmetro para ser respeitado
     *                         quando necessário.
     * @return array
     */
    public function delete(array $ids, $forceIds = array())
    {
        if(is_array($forceIds) && count($forceIds)) {
            $ids = $forceIds;
        } else {
            $ids = $_REQUEST['sel'];
        }
        $response = 1;
        $error = array();
        $success = "Ação executada com sucesso!";

        try {
            $this->getEm()->beginTransaction();

            foreach ($ids as $id) {
                if ($this->verificarStatus($id)) {
                    throw new \Exception;
                }
                $noticia = $this->getEm()->find($this->getNameEntity(), $id);
                $this->getEm()->remove($noticia);
                $this->getEm()->flush();
            }
            $this->getEm()->commit();
            $this->getLogger()->info("Notícias " . implode(",", $ids) . " foram excluidos.");
        } catch (\Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $response = 0;
            $error[] = "Não foi possível executar essa ação.";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }
}
