<?php

namespace Console;

use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Driver\PDOSqlsrv\Connection;
use Helpers\Http;

require_once __DIR__.'/../vendor/autoload.php';
date_default_timezone_set('America/Sao_Paulo');
/**
 * Author RainGrave
 * Mapeia tabelas referente as entidades e indexa no solr.
 */

class SolrSeed
{
    protected $dbConfig;
    protected $documents;
    protected $client;
    protected $solManager;

    public function __construct()
    {
        $this->dbConfig = require __DIR__ . '/../config/db.php';
        $config = require __DIR__ . '/../config/solrConfig.php';
        //$this->documents = require __DIR__ . '/../config/solrSeed.php';
        $this->client = new \Solarium\Client($config);
    }

    private function getId($entity_name, $entity_id)
    {
        return $entity_name . '#' . $entity_id;
    }

    public function connection()
    {
        try {
            return new Connection("pgsql:host=" . $this->dbConfig['host'] . ";port=" . $this->dbConfig['port'] . ";dbname=" . $this->dbConfig['dbname'] . ";user=" . $this->dbConfig['user'] . ";password=" . $this->dbConfig['password']."");
        } catch(PDOException $errors) {
            echo $errors;
        }
    }

    public function run()
    {
        $conn = $this->connection();

        //Upgrade php memory
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '512M');

        //Insert tb_ata

        $table = "tb_ata";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Ata', $value['id_ata']);
                $doc->entity_id = $value['id_ata'];
                $doc->entity_name = "Entity\\Ata";
                $doc->title = $value['no_ata'];
                $doc->description = $value['ds_ata'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "atasConselho/detalhes/" . $value['id_ata'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "Foram indexados:<br>" . " <b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert tb_agenda

        $table = "tb_agenda";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Agenda', $value['id_agenda']);
                $doc->entity_id = $value['id_agenda'];
                $doc->entity_name = "Entity\\Agenda";
                $doc->title = $value['no_titulo'];
                $doc->description = $value['ds_evento'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "agendaEventos/detalhes/" . $value['id_agenda'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert tb_bibliografia

        $table = "tb_bibliografia";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Bibliografia', $value['id_bibliografia']);
                $doc->entity_id = $value['id_bibliografia'];
                $doc->entity_name = "Entity\\Bibliografia";
                $doc->title = 'Bibliografia Geral do Patrimônio';
                $doc->description = $value['ds_conteudo'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "bibliografiaPatrimonio";

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_biblioteca

        $table = "tb_biblioteca";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Biblioteca', $value['id_biblioteca']);
                $doc->entity_id = $value['id_biblioteca'];
                $doc->entity_name = "Entity\\Biblioteca";
                $doc->title = $value['no_biblioteca'];
                $doc->description = $value['ds_descricao'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "bibliotecasIphan";

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_dicionario_patrimonio

        $table = "tb_dicionario_patrimonio";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\DicionarioPatrimonioCultural', $value['id_dicionario_patrimonio']);
                $doc->entity_id = $value['id_dicionario_patrimonio'];
                $doc->entity_name = "Entity\\DicionarioPatrimonioCultural";
                $doc->title = $value['no_titulo'];
                $doc->description = $value['ds_descricao'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "dicionarioPatrimonioCultural/detalhes/" . $value['id_dicionario_patrimonio'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_edital

        $table = "tb_edital";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Edital', $value['id_edital']);
                $doc->entity_id = $value['id_edital'];
                $doc->entity_name = "Entity\\Edital";
                $doc->title = $value['no_edital'];
                $doc->description = $value['ds_conteudo'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "editais/detalhes/" . $value['id_edital'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_fototeca

        $table = "tb_fototeca";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Fototeca', $value['id_fototeca']);
                $doc->entity_id = $value['id_fototeca'];
                $doc->entity_name = "Entity\\Fototeca";
                $doc->title = $value['no_nome'];
                $doc->description = $value['ds_descricao'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "fototeca/detalhes/" . $value['id_fototeca'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_galeria

        $table = "tb_galeria";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Galeria', $value['id_galeria']);
                $doc->entity_id = $value['id_galeria'];
                $doc->entity_name = "Entity\\Galeria";
                $doc->title = $value['no_titulo'];
                $doc->description = $value['ds_descricao'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "galeria/detalhes/" . $value['id_galeria'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_legislacao

        $table = "tb_legislacao";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Legislacao', $value['id_legislacao']);
                $doc->entity_id = $value['id_legislacao'];
                $doc->entity_name = "Entity\\Legislacao";
                $doc->title = $value['no_titulo'];
                $doc->description = $value['ds_descricao'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];

                if(!empty($value['no_arquivo'])) {
                    $doc->url = '/uploads/legislacao/' . $value['no_arquivo'];
                } else {
                    $doc->url = $value['ds_url'];
                }



                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_licitacao_convenio_contrato

        $table = "tb_licitacao_convenio_contrato";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\LicitacaoConvenioContrato', $value['id_licitacao_convenio_contrato']);
                $doc->entity_id = $value['id_licitacao_convenio_contrato'];
                $doc->entity_name = "Entity\\LicitacaoConvenioContrato";
                $doc->title = $value['ds_objeto'];
                $doc->description = $value['ds_objeto'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "licitacaoConvenioContrato/detalhes/" . $value['id_licitacao_convenio_contrato'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_noticia

        $table = "tb_noticia";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Noticia', $value['id_noticia']);
                $doc->entity_id = $value['id_noticia'];
                $doc->entity_name = "Entity\\Noticia";
                $doc->title = $value['no_titulo'];
                $doc->description = $value['ds_conteudo'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "noticias/detalhes/" . $value['id_noticia'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_pergunta

        $table = "tb_pergunta";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Pergunta', $value['id_pergunta']);
                $doc->entity_id = $value['id_pergunta'];
                $doc->entity_name = "Entity\\Pergunta";
                $doc->title = $value['ds_pergunta'];
                $doc->description = $value['ds_resposta'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "perguntasFrequentes";

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_publicacao

        $table = "tb_publicacao";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Publicacao', $value['id_publicacao']);
                $doc->entity_id = $value['id_publicacao'];
                $doc->entity_name = "Entity\\Publicacao";
                $doc->title = $value['no_titulo'];
                $doc->description = $value['ds_conteudo'];
                $doc->author = $value['no_autor'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "uploads/publicacao/" . $value['ds_arquivo'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_video

        $table = "tb_video";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\Video', $value['id_video']);
                $doc->entity_id = $value['id_video'];
                $doc->entity_name = "Entity\\Video";
                $doc->title = $value['no_video'];
                $doc->description = $value['ds_resumo'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "videos/detalhes/" . $value['id_video'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

        //Insert table tb_pagina_estatica

        $table = "tb_pagina_estatica";

        $stmt = $conn->prepare("SELECT * FROM $table");

        $stmt->execute();

        $values = $stmt->fetchAll();

        $quantity = 0;

        if (!empty($values)) {
            foreach ($values as $i => $value) {
                $quantity = $i;

                $update = $this->client->createUpdate();

                $doc = $update->createDocument();

                $doc->id = $this->getId('Entity\\PaginaEstatica', $value['id_pagina_estatica']);
                $doc->entity_id = $value['id_pagina_estatica'];
                $doc->entity_name = "Entity\\PaginaEstatica";
                $doc->title = $value['no_titulo'];
                $doc->description = $value['ds_conteudo'];
                $doc->publish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['dt_cadastro']));
                $doc->publish = $value['st_publicado'];
                $doc->url = "pagina/detalhes/" . $value['id_pagina_estatica'];

                if (!empty($value['unpublish_date'])) {
                    $doc->unpublish_date = date("Y-m-d\TH:i:s\Z", strtotime($value['unpublish_date']));
                }

                $update->addDocument($doc);

                $update->addCommit();

                $result = $this->client->update($update);
            }

            if ($result == true) {
                $total = $quantity + 1;
                echo "<b style='color: red;'>$total</b> indices da tabela " . "<b style='color: red;'>$table</b><br>";
            } else {
                echo "Dados nao indexados";
            }
        }

    }

}

$seed = new SolrSeed();
$seed->run();
