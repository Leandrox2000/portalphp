<?php

namespace Console;

$dbConfig = require __DIR__ . '/../config/db.php';

$dbName = $dbConfig['dbname'];
$dbHost = $dbConfig['host'];
$dbUsername = $dbConfig['user'];
$dbPassword = $dbConfig['password'];
$conn = new \PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);

/**
 * Corrige o problema de duplicação nos subsites
 */
class FixIphan {

    /**
     * Instância do PDO
     */
    private $pdo;
    /**
     * Tabelas que devem ser verificadas em busca de erros
     */
    private $tables = array(
        'tb_agenda_site',
        'tb_banner_geral_site',
        'tb_conselheiro_site',
        'tb_edital_site',
        'tb_funcionalidade_site',
        'tb_galeria_site',
        'tb_legislacao_site',
        'tb_noticia_site',
        'tb_pagina_estatica_site',
        'tb_slider_home_site',
        'tb_video_site',
        'tb_menu',
    );
    private $sqlLog = NULL;
    private $idCorreto = NULL;
    private $idDuplicado = NULL;

    /**
     * 
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo, $idCorreto, $idDuplicado)
    {
        $this->idCorreto = $idCorreto;
        $this->idDuplicado = $idDuplicado;
        $this->pdo = $pdo;
    }

    /**
     * Obtém o nome do campo ID da tabela
     */
    private function getIdField($table)
    {
        return 'id_' . str_replace(array('tb_', '_site'), '', $table);
    }
    
    /**
     * Adiciona um comando SQL ao log
     * @param string $sql
     */
    private function logSql($sql)
    {
        $this->sqlLog .= $sql . "\n\n";
    }

    /**
     * Lança uma exceção
     */
    private function throwSqlException($sql = NULL)
    {
        $sqlError = print_r($this->pdo->errorInfo(), TRUE);
        $sql = ($sql) ? ' "' . $sql . '"' : null;

        throw new \Exception("Erro na consulta SQL" . $sql . ".\n\n" . $sqlError . "\n" );
    }

    /**
    * Retorna os IDs dos registros com ID da Sede duplicado.
    */
    private function getIds($table)
    {
        // Remove o prefixo e sufixo da tabela, obtendo assim o campo ID
        $idField = $this->getIdField($table);
        $sql = "SELECT $idField FROM $table WHERE id_site = " . $this->idDuplicado;
        $this->logSql($sql);
        $query = $this->pdo->prepare($sql);

        // Tratamento de erros
        if (!$query->execute()) {
            $this->throwSqlException();
        }

        return $query->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Corrige um ID problemático.
     */
    private function updateId($table, $id)
    {
        $idField = $this->getIdField($table);

        // Verifica se o registro correto já não existe
        // (isso é necessário devido as tabelas pivô)
        $sql = "SELECT count(*) FROM $table WHERE $idField = $id AND id_site = "
               . $this->idCorreto;
        $this->logSql($sql);
        $correctAlreadyExist = $this->pdo->prepare($sql);
        $correctAlreadyExist->execute();
        $numberOfRows = $correctAlreadyExist->fetchColumn();

        // Se não há registro equivalente
        if ($numberOfRows == 0) {
            // Atualiza a FK id_site para o ID SEDE CORRETO onde igual a PK passada por parâmetro
            $sql = "UPDATE $table SET id_site = " . $this->idCorreto
                   . " WHERE $idField = $id AND id_site = " . $this->idDuplicado;
            $this->logSql($sql);
            $query = $this->pdo->prepare($sql);

            // Tratamento de erros
            if (!$query->execute()) {
                $this->throwSqlException($sql);
            }
        }
        // Se há registro equivalente
        else {
            // Apaga o registro problemático
            $sql = "DELETE FROM $table WHERE $idField = $id AND id_site = "
                   . $this->idDuplicado;
            $this->logSql($sql);
            $query = $this->pdo->prepare($sql);

            // Tratamento de erros
            if (!$query->execute()) {
                $this->throwSqlException();
            }
        }

        return true;
    }

    /**
     * Faz a correção dos registros, caso necessário.
     */
    public function doFix()
    {
        // Para cada tabela
        foreach ($this->tables as $table) {
            // Pega os IDs dos registros com problema
            $ids = $this->getids($table);

            // Se houver IDs
            if (count($ids) > 0) {
                echo "Foram identificados e corrigidos registros com problema na tabela [ $table ]:\n";
                echo 'IDs: ' . implode(', ', array_values($ids));
                echo "\n\n";

                foreach ($ids as $id) {
                    $this->updateId($table, $id);
                }
            } else {
                echo "Nao foram encontrados problema na tabela [ $table ]\n\n";
            }
        }
        
        echo "Queries executadas:\n\n";
        echo $this->sqlLog;
    }

}

$fix = new FixIphan($conn, 1, 5);
$fix->doFix();

