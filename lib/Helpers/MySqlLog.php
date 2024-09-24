<?php

namespace Helpers;


/**
 * Gera  o Log do Sql
 *
 * @author Luciano
 */
class MySqlLog implements \Doctrine\DBAL\Logging\SQLLogger
{
    
    
    /**
     *
     * @var \Logger
     */
    private $log;

    /**
     *
     * @var type 
     */
    private $start;
 
    /**
     *
     * @var type 
     */
    private $sql;

    /**
     * 
     * 
     */
    public function __construct()
    {
        \Logger::configure(getcwd()."/config/log.xml");
        $this->log = \Logger::getLogger('Sql');
    }
    
    /**
     * Acessada no ìnício da Query começa 
     * contar o tempo de execução 
     * armazena a Query que foi feita.
     * 
     * @param type $sql
     * @param array $params
     * @param array $types
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->start    = microtime(true);
        $this->sql      = $sql;
    }

    /**
     * Executada ao termino da Query
     * Calcula o Tempo de Execução
     * Saída doLog
     * 
     */
    public function stopQuery()
    {
        $time = microtime(true) - $this->start;
        $this->log->debug("[{$time}}] {$this->sql}}"); // Not logged because DEBUG < WARN
    }

}
