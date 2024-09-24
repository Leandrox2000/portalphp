<?php

namespace Console;

$dbConfig = require __DIR__ . '/../config/db.php';

$dbName = $dbConfig['dbname'];
$dbHost = $dbConfig['host'];
$dbUsername = $dbConfig['user'];
$dbPassword = $dbConfig['password'];
$conn = new \PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);

class CronBackground
{
    /**
     * Instância do PDO
     */
    protected $pdo;
    
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function buscaBackground()
    {
        $sql = "select count(*) total from tb_background_home WHERE dt_inicial >= NOW() AND dt_inicial < NOW() + interval'2 minutes'";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        
        $result = $query->fetch();
        
        try {
            if ($result['total'] > 0)
                return TRUE;
            else
                return FALSE;
        } catch (\Exception $e) {
            echo $e;
        }
    }
    
    public function updatePublicado()
    {
        $sqlDespublica = "UPDATE tb_background_home SET st_publicado = 0";
        $queryDespublica = $this->pdo->prepare($sqlDespublica);
        $queryDespublica->execute();
        
        $sqlPublica = "UPDATE tb_background_home SET st_publicado = 1 WHERE dt_inicial >= NOW() AND dt_inicial < NOW() + interval'2 minutes'";
        $queryPublica = $this->pdo->prepare($sqlPublica);
        $queryPublica->execute();
    }
    
    public function updateDespublicado()
    {
        $sql = "UPDATE tb_background_home SET st_publicado = 0 WHERE dt_final <= NOW()";
        $query = $this->pdo->prepare($sql);
        $query->execute();
    }

    public function run()
    {
        if ($this->buscaBackground() == TRUE || $this->buscaBackground() == 1)
            $this->updatePublicado();
        $this->updateDespublicado();
    }

}

$seed = new CronBackground($conn);
$seed->run();