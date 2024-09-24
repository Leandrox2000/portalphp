<?php

namespace CMS\Service\ServiceExcel;

use Entity\Repository\EmailBoletimRepository;
use CMS\StaticMethods\StaticMethods;

/**
 * Classe EmailBoletim
 *
 * Utilizada para a exportação de dados
 */
class EmailBoletim
{

    /**
     * Metodo exportarEmails
     *
     * Faz a exportação de dados especificos da tabela emailsBoletim
     * 
     * @param EmailBoletimRepository $repoEmails
     * @param StaticMethods $methodFactory
     * @param type $ids
     */
    public function exportarEmails(EmailBoletimRepository $repoEmails, StaticMethods $staticMethods, $ids)
    {
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=file.csv");
        header("Pragma: no-cache");

        echo "Nome;E-mail";
        echo "\n";
        foreach ($repoEmails->getEmailsIds($ids) as $email) {
            echo $email['nome'] . ";" . $email['email'];
            echo "\n";
        }
    }

}
