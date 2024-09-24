<?php

namespace CMS\Controller;

use CMS\Service\ServiceRepository\PublicacaoIntroducao as PublicacaoIntroducaoService;
use Entity\PublicacaoIntroducao as PublicacaoIntroducaoEntity;

/**
 * PublicacaoController
 */
class PublicacaoIntroducaoController extends CrudController
{

    const PAGE_TITLE = 'Publicações - Inserir texto introdutório';
    const DEFAULT_ACTION = 'form';

    protected $title = self::PAGE_TITLE;
    protected $defaultAction = self::DEFAULT_ACTION;

    /**
     *
     * @var PublicacaoService
     */
    private $service;

    /**
     *
     * @var PublicacaoIntroducaoEntity
     */
    private $entity;

    /**
     *
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @var array
     */
    protected $user;

    /**
     *
     * @param \Template\TemplateInterface $tpl
     * @param \Helpers\Session $session
     */
    public function __construct(
            \Template\TemplateInterface $tpl,
            \Helpers\Session $session
        )
    {
        parent::__construct($tpl, $session);
        $this->setUser($this->getUserSession());
    }

    /**
     *
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param array $user
     */
    public function setUser(array $user)
    {
        $this->user = $user;
    }

    /**
     *
     * @return \CMS\Service\ServiceRepository\PublicacaoIntroducao
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->setService(
                new PublicacaoIntroducaoService(
                    $this->getEm(),
                    $this->getEntity(),
                    $this->getSession()
                )
            );
        }
        return $this->service;
    }

    /**
     *
     * @return \Entity\PublicacaoIntroducao
     */
    public function getEntity()
    {
        if (empty($this->entity)) {
            $this->setEntity(new PublicacaoIntroducaoEntity());
        }
        return $this->entity;
    }

    /**
     *
     * @param \CMS\Service\ServiceRepository\PublicacaoIntroducao $service
     */
    public function setService(PublicacaoIntroducaoService $service)
    {
        $this->service = $service;
    }

    /**
     *
     * @param \Entity\PublicacaoIntroducao $entity
     */
    public function setEntity(PublicacaoIntroducaoEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @param int $id
     * @return \Template\TemplateAmanda
     */
    public function form()
    {
        try {
            $introducao = $this->getEm()
                               ->getRepository($this->getService()->getNameEntity())
                               ->createQueryBuilder('e')
                               ->getQuery()
                               ->setMaxResults(1)
                               ->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $introducao = null;
        }

        $this->getTpl()->renderView(
            array(
                'data'          => new \DateTime('now'),
                'hora'          => new \DateTime('now'),
                'introducao'    => $introducao,
                'method'        => 'POST',
                'titlePage'     => $this->getTitle()
            )
        );

        return $this->getTpl()->output();
    }

    /**
     *
     * @return string
     */
    public function salvar()
    {
        $dados = array(
            'id'        => $this->getParam()->getInt('id'),
            'conteudo'  => $this->getParam()->getString('conteudo'),
        );

        return json_encode($this->getService()->save($dados));
    }

}
