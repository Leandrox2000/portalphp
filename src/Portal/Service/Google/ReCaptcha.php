<?php

namespace Portal\Service;

/**
 * Realiza a validação do ReCaptcha do google.
 */
class ReCaptcha
{
    /**
     * URL do Google que irá realizar a verificação.
     * 
     * @var string
     */
    private static $url_google = 'https://www.google.com/recaptcha/api/siteverify';
    
    /**
     * Chave secreta.
     * 
     * @var string
     */
    private static $secret_key = '6LcIKEUUAAAAANzw1T6p3zuM_PozgW3vcH40auwj';

    /**
     * Envia uma requisição perguntando ao google se o 
     * ReCaptcha é válido.
     * 
     * @param string $recaptcha_response
     * @return boolean
     */
    public static function verificar($recaptcha_response) {
        $post_data = http_build_query(
            array(
                'secret' => self::$secret_key,
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        );
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            )
        );
        $context  = stream_context_create($opts);
        $response = file_get_contents(self::$url_google, false, $context);
        $result = json_decode($response);
        
        return !empty($result->success);
    }
    
}
