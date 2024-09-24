<?php

namespace Helpers;

/**
 * Helper que utiliza o SwiftMailer para envio de e-mails.
 */
class Mail
{

    protected $config;
    protected $transport;
    protected $message;

    /**
     *
     * @param array $cfg Parâmetros do arquivo de configuração.
     */
    public function __construct(array $cfg)
    {
        $this->config = $cfg;
        $this->transport = $this->getTransport();
        $this->message = $this->getMessage();
    }
    
    public static function phpMailer($config, $msg, $assunto)
    {
        $mail = new \PHPMailer();
        
        $emails = explode(";", $config['to']);

//        $mail->SMTPDebug = 2;

        $mail->isSMTP();
		$mail->CharSet = 'UTF-8';
        $mail->Host = $config['auth']['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['auth']['username'];
        $mail->Password = $config['auth']['password'];
        $mail->SMTPSecure = $config['auth']['security'];      
        $mail->Port = $config['auth']['port'];              

        $mail->setFrom($config['from'], 'Portal Iphan');
        
        foreach ($emails as $email) {
            $mail->addAddress($email);
        }
        
        $mail->isHTML(true);

        $mail->Subject = $assunto;
        $mail->Body    = $msg;

        $mail->send();
    }

    /**
     *
     * @return \Swift_Message
     */
    public function getMessage()
    {
        $message = \Swift_Message::newInstance();
        $message->setTo($this->config['to']);
        $message->setFrom($this->config['from']);
        $message->setContentType('text/html');

        return $message;
    }

    /**
     *
     * @return \Swift_Transport
     */
    public function getTransport()
    {
        $config = $this->config['auth'];
        $transport = \Swift_SmtpTransport::newInstance(
            $config['host'],
            $config['port'],
            $config['security']
        );
        $transport->setUsername($config['username']);
        $transport->setPassword($config['password']);

        return $transport;
    }

    /**
     *
     * @param \Swift_Transport $transport
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
    }

    /**
     *
     * @param \Swift_Message $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Enviar a mensagem.
     *
     * @param string $subject
     * @param string $message
     * @return int
     */
    public function send($subject, $message)
    {
        $this->message->setSubject($subject);
        $this->message->setBody($message);
	
        $mailer = \Swift_Mailer::newInstance($this->transport);
        return $mailer->send($this->message);
    }

}
