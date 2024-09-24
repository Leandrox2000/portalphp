<?php
namespace Entity\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * Classe EmailBoletimRepository
 *
 * Responsável por todas as consultas a tabela EmailBoletim
 * @author Eduardo
 */
class EmailBoletimRepository extends EntityRepository
{
    /**
     * Metodo getEmailsIds
     *
     * Retorna todos os emails de um grupo de ids
     * @param array $ids
     */
    public function getEmailsIds($ids)
    {
        $sql = "SELECT eb.nome, eb.email FROM Entity\EmailBoletim eb WHERE eb.id IN(" . $ids . ")";
        $query = $this->getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * Metodo getEmailboletim
     *
     * Retorna os dados da tabela com o limit e offset passados por parâmetro
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getEmailboletim($limit, $offset)
    {
        $sql = "SELECT eb FROM Entity\EmailBoletim eb ORDER BY eb.dataCadastro DESC";
        $query = $this->getEntityManager()->createQuery($sql);

        if ($offset > 0) {
            $query->setFirstResult($offset);
        }

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     * Metodo countAll
     *
     * Conta todos os Emails de boletim eletrônico
     * @return int
     */
    public function countAll()
    {
        $sql = "SELECT count(eb) FROM Entity\EmailBoletim eb";
        $query = $this->getEntityManager()->createQuery($sql);

        return $query->getSingleScalarResult();
    }


    /**
     * Metodo getPesquisaEmail
     *
     * Faz uma pesquisa por e-mail na tabela tb_email_boletim
     * @param int $limit
     * @param int $offset
     * @param string $email
     * @return array
     */
    public function getPesquisaEmail($limit, $offset, $email)
    {
        $sql = "SELECT eb FROM Entity\EmailBoletim eb WHERE eb.email LIKE '%" . $email . "%' ";
        $query = $this->getEntityManager()->createQuery($sql);

        if (($limit + $offset) > 0) {
            $query->setFirstResult($offset)
                    ->setMaxResults($limit);
        }

        return $query->getResult();
    }

    /**
     * Metodo getTotalPesquisaEmail
     *
     * Retorna o total de registro de uma pesquisa na tabela tb_email_boletim
     * @param string $email
     * @return int
     */
    public function getTotalPesquisaEmail($email)
    {
        $sql = "SELECT count(eb) FROM Entity\EmailBoletim eb WHERE eb.email LIKE '%" . $email . "%' ";
        $query = $this->getEntityManager()->createQuery($sql);

        return $query->getSingleScalarResult();
    }

}
