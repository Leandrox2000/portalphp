<?php

namespace CMS\Service\ServiceRepository;

use Doctrine\ORM\EntityManager;
use Entity\PublicacaoCategoria as PublicacaoCategoriaEntity;
use Helpers\Session;

/**
 * Description of PublicacaoCategoria
 *
 * @author Luciano
 */
class PublicacaoCategoria extends BaseService
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Entity\PublicacaoCategoria $entity
     */
    public function __construct(EntityManager $em, PublicacaoCategoriaEntity $entity, Session $session)
    {
        parent::__construct($em);
        $this->setEntity($entity);
        $this->setSession($session);
    }

    /**
     *
     * @param string $nome
     * @param int $id
     * @return array
     */
    public function save($nome, $descricao, $id = 0)
    {
        $response = 0;
        $error = array();
        $success = "";
        $repository = $this->getEm()->getRepository($this->getNameEntity());
        $dados = array(
            'nome' => $nome,
            'descricao' => $descricao,
            'id' => $id,
        );

        if ($repository->verificaCategoriaExiste($dados)) {
            $action = empty($id) ? "Inserida" : "Alterada";
            try {
                parent::save($dados);
                $response = 1;
                $success = "Categoria $nome $action com sucesso";
            } catch (\Exception $exc) {
                $action = empty($id) ? "Inserir" : "Alterar";
                $error[] = "Não é possível $action esta Categoria.";
                $this->getLogger()->error($exc->getMessage());
            }
        } else {
            $error[] = "Já existe uma categoria com esse nome cadastrado.";
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

    /**
     *
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        $response = 0;
        $error = array();
        $success = "";

        $publicacaoVinculado = $this->getEm()
                            ->getRepository("Entity\Publicacao")
                            ->getPublicacaoVinculado($id);
        
        if ($publicacaoVinculado) {
            $response = 2;
            $error = 'Não é possivel excluir esta "Categoria Publicações" porque existem as seguintes Publicações relacionados: ';
            foreach($publicacaoVinculado as $publicacao)
            {
                $error .= $publicacao['titulo'].'; ';
            }
        } else {
            $categoria = $this->getEm()->getReference($this->getNameEntity(), $id);
            try {
                $this->getEm()->remove($categoria);
                $this->getEm()->flush();
                $response = 1;
                $success = "Categoria excluída com sucesso";
            } catch (\Exception $exc) {
                $this->getLogger()->error($exc->getMessage());
                $error[] = "Erro ao excluir essa categoria.";
            }
        }

        return array('error' => $error, 'response' => $response, 'success' => $success);
    }

}
