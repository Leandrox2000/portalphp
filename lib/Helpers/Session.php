<?php

namespace Helpers;

/**
 * Description of Session
 *
 * @author Join-ti
 */
class Session
{

    public static $started = FALSE;

    /**
     * Construtor
     */
    public function __construct($sessionLifeTime)
    {
        $this->sessionLifeTime = $sessionLifeTime;
        $this->start();
    }

    /**
     * 
     * Inicia a Sessão
     */
    private function start()
    {           
        
        session_name('iphan-portal');
        session_start();
        $sessionLastActivity = $this->get('LAST_ACTIVITY');
        $sessionIsNotValid = ((time() - $sessionLastActivity) > $this->sessionLifeTime);
        


        if ($sessionLastActivity && $sessionIsNotValid) {
            // Última requisição foi a mais de X minutos, então...
            session_unset(); // Remove a variável $_SESSION do run-time
            session_destroy(); // Destroí a sessão
        } else {
            $this->set('LAST_ACTIVITY', time()); // atualiza a data da última requisição
        }
    }

    /**
     * 
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * 
     * @param string $nome
     * @return mixed
     */
    public function get($nome)
    {
        if ($this->exists($nome)) {
            return $_SESSION[$nome];
        } else {
            return false;
        }
    }

    /**
     * 
     * @param string $nome
     * @return boolean
     */
    public function exists($nome)
    {
        return isset($_SESSION[$nome]);
    }

    /**
     * 
     * @param string $nome
     */
    public function destroy($nome)
    {
        if ($this->exists($nome)) {
            unset($_SESSION[$nome]);
        }
    }

}
