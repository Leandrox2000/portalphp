<?php

namespace Console;

$dbConfig = require __DIR__ . '/../config/db.php';

$dbName = $dbConfig['dbname'];
$dbHost = $dbConfig['host'];
$dbUsername = $dbConfig['user'];
$dbPassword = $dbConfig['password'];
$conn = new \PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);

class Console
{
    /**
     * Instância do PDO
     */
    protected $pdo;
    
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function criaTabelaAuxiliar()
    {
        $sql = "CREATE TABLE tb_galeria_ordem_copia
                (
                  id_galeria_site SERIAL,
                  id_site BIGINT NOT NULL,
                  id_galeria BIGINT NOT NULL,
                  nu_ordem BIGINT
                )";
        $query = $this->pdo->prepare($sql);
        
        try {
            if ($query->execute())
                return TRUE;
            else
                return FALSE;
        } catch (\Exception $e) {
            echo $e;
        }
    }
    
    public function getSites()
    {
        $sql = "SELECT DISTINCT id_site FROM tb_galeria_ordem";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $array[] = $row;
        }
        
        try {
            if (!empty($array))
                return $array;
            else
                die("Não foi possível buscar os sites");
        } catch (\Exception $e) {
            echo $e;
        }
    }
    
    public function populaTabelaAuxiliar()
    {
        $sites = $this->getSites();
        
        foreach ($sites as $site) {
            $sql = "INSERT INTO tb_galeria_ordem_copia
                    (id_galeria_site, id_site, id_galeria, nu_ordem)
                      SELECT id_galeria_site, id_site, id_galeria, ROW_NUMBER() OVER( ORDER BY nu_ordem) as nu_ordem FROM tb_galeria_ordem WHERE id_site = ".implode("", $site);
            $query = $this->pdo->prepare($sql);
            $query->execute();
        }
    }
    
    public function migraParaTabelaPrincipal()
    {
        $sql = "UPDATE tb_galeria_ordem
                SET nu_ordem = copia.nu_ordem
                FROM (SELECT * FROM tb_galeria_ordem_copia) AS copia
                WHERE tb_galeria_ordem.id_galeria_site = copia.id_galeria_site AND
                      tb_galeria_ordem.id_galeria = copia.id_galeria AND
                      tb_galeria_ordem.id_site = copia.id_site";
        
        $query = $this->pdo->prepare($sql);
        $query->execute();
    }

    public function run()
    {
        $this->criaTabelaAuxiliar();
        $this->populaTabelaAuxiliar();
        $this->migraParaTabelaPrincipal();
    }

}

$console = new Console($conn);
$console->run();