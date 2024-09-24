<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Entity\FuncionalidadeSite;
use Entity\Site as SiteEntity;
use Exception;
use Helpers\Session;

/**
 * Description of Site
 *
 * @author Join-ti
 */
class Site extends BaseService
{

    /**
     * 
     * @param EntityManager $em
     * @param SiteEntity $entity
     */
    public function __construct(EntityManager $em, SiteEntity $entity, Session $session)
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
            $funcionalidades = $dados['funcionalidade'];
            $naoPermitido = false;
            $contagemSigla = $this->getEm()
                    ->createQuery('SELECT COUNT(e.sigla) FROM Entity\Site e WHERE LOWER(e.sigla) = LOWER(:sigla)')
                    ->setParameter('sigla', $dados['sigla'])
                    ->getResult(Query::HYDRATE_SINGLE_SCALAR);
            $site = $this->getEm()
                    ->getRepository('Entity\Site')
                    ->find($dados['id']);

            unset($dados['funcionalidade']);

            // Inicia a transação
            $this->getEm()->beginTransaction();

            // Verifica se a sigla informada está em uso
            if (( empty($dados['id']) && $contagemSigla > 0 ) || ( $contagemSigla > 0 && $site->getSigla() != $dados['sigla'] )) {
                $naoPermitido = true;
                throw new Exception("Erro: a sigla informada já está em uso.");
            }

            // Define as funcionalidades
            

           // $dados['funcionalidades'] = $funcionlidadesCollection;

            // Salva a entidade
            $site = parent::save($dados);
            
            $this->deleteFuncionalidadeSite($site);
            
            //$funcionlidadesCollection = new \Doctrine\Common\Collections\ArrayCollection();
            $cont = 1;
            
            foreach ($funcionalidades as $f2) {
                if($f2){
                    $reference = new FuncionalidadeSite;
                    $reference->setFuncionalidade($this->getEm()->getRepository('Entity\Funcionalidade')->find($f2));
                    $reference->setSite($site);
                    $reference->setOrdem($cont);
                    $this->getEm()->persist($reference);
                    $this->getEm()->flush();
                    $cont++;
                }
                //$funcionlidadesCollection->add($reference);
            }
            //
            
            // Finaliza a transação
            $this->getEm()->commit();

            $response = 1;
            $success = "Registro $action com sucesso!";
        } catch (Exception $exc) {
            $this->getEm()->rollback();

            $action = empty($dados['id']) ? "inserir" : "alterar";
            if ($naoPermitido) {
                $error[] = $exc->getMessage();
            } else {
                $error[] = "Erro ao {$action} registro {$exc->getMessage()} ";
            }
        }

        return array(
            'error' => $error,
            'response' => $response,
            'success' => $success,
        );
    }

     public function deleteFuncionalidadeSite($site)
    {
        $queryBuilder = $this->getEm()->createQueryBuilder();
        $queryBuilder
            ->delete('Entity\FuncionalidadeSite', 'FS')
            ->where($queryBuilder->expr()->eq('FS.site', ':site'))
            ->setParameter('site', $site);

        return $queryBuilder->getQuery()->getResult();
    }
    /**
     * 
     * @param array $ids
     * @return array
     */
    public function delete(array $ids)
    {
        $response = 0;
        $success = '';
        $error = 'Descrição do status da exclusão dos Subsites selecionados:</br>';	

        try {
        	$response = 2;
        	$sede = $this->getEm()->getRepository('Entity\Site')->getSiteSede();
			$this->getEm()->beginTransaction();
        	foreach ($ids as $id) {
                    
                    if ($this->verificarStatus($id)) {
                        throw new \Exception;
                    }
                    
                    if($id != $sede['id'])
                    {
                        $status = '';
                        $site = $this->getEm()->find($this->getNameEntity(), $id);
                        $sliderHome = $this->getEm()->getRepository('Entity\SliderHome')->findByIdSiteCountEntity($id);
                        $agenda = $this->getEm()->getRepository('Entity\Agenda')->findByIdSiteCountEntity($id);
                        $galeria = $this->getEm()->getRepository('Entity\Galeria')->findByIdSiteCountEntity($id);
                        $legislacao = $this->getEm()->getRepository('Entity\Legislacao')->findByIdSiteCountEntity($id);
                        $noticia = $this->getEm()->getRepository('Entity\Noticia')->findByIdSiteCountEntity($id);
                        $paginaEstatica = $this->getEm()->getRepository('Entity\PaginaEstatica')->findByIdSiteCountEntity($id);
                        $videos = $this->getEm()->getRepository('Entity\Video')->findByIdSiteCountEntity($id);
                        if($sliderHome)
                                $status .= 'Slider Home</br>';
                        if($agenda)
                                $status .= 'Agenda</br>';
                        if($galeria)
                                $status .= 'Galeria</br>';
                        if($legislacao)
                                $status .= 'Legislação</br>';
                        if($noticia)
                                $status .= 'Noticia</br>';
                        if($paginaEstatica)
                                $status .= 'Pagina Estatica</br>';
                        if($videos)
                                $status .= 'Videos</br>';
                        if($status)
                                $error .= '<b>'.$site->getNome().'</b> - Impossível ser excluído, contém registros vinculados nas seguintes funcionalidades:</br>'.$status;
                        else
                        {
                                $error .= '<b>'.$site->getNome().'</b> - Excluído com sucesso!</br>';
                                $this->getEm()->remove($site);
                                $this->getEm()->flush();
                        }
                    }
                    else
        			$error .= $sede['nome'].': Não é posivel escluir o site sede!</br>';
            }
            $this->getEm()->commit();
        } catch (Exception $exc) {
            $this->getEm()->rollback();
            $this->getLogger()->error($exc->getMessage());
            $error = "Não foi possível excluir o(s) registro(s) selecionado(s). Algum erro foi identificado.";
        }

        return array(
            'error' => $error,
            'response' => $response,
            'success' => $success,
        );
    }

}
