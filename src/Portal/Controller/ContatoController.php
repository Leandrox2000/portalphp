<?php

namespace Portal\Controller;

use Helpers\Mail;
use Portal\Service\ReCaptcha;

/**
 * Contato / Fale Conosco
 *
 */
class ContatoController extends PortalController
{

    protected $defaultAction = 'index';

    /**
     * Página de contato.
     *
     * @return string
     */
    public function index()
    {
        $this->getTpl()->setTitle('Fale Conosco');
        $this->getTpl()->renderView();

        $bread = $this->getEm()->getRepository('Entity\Menu')->getBreadCrumbs("", null, "contato");
        $oque = $this->oqueEoqueE();

        $this->getTpl()->renderView(array(
            'bread' => $bread,
            'oque' => $oque
        ));

        return $this->getTpl()->output();
    }

    /**
     * HTML da mensagem.
     *
     * @return string
     */
    private function getMensagem()
    {
        $params = $this->getParam();
        $this->getTpl()->setView('contato/email.html.twig');

        return $this->getTpl()->renderView(array(
            'assunto' => $params->get('assunto'),
            'nome' => $params->get('nome'),
            'mensagem' => $params->get('mensagem'),
            'email' => $params->get('email'),
            'telefone' => $params->get('telefone'),
            'cidade' => $params->get('cidade'),
            'estado' => $params->get('estado'),
        ));
    }

    /**
     * Faz o envio do formulário.
     *
     */
    public function enviar()
    {


        if (empty($_POST['g-recaptcha-response']) || !ReCaptcha::verificar($_POST['g-recaptcha-response'])) {
            return json_encode(array('success' => false, 'response' => 'Captcha inválido.'));
        }

        if(isset($_REQUEST['obrigatorio']) && $_REQUEST['obrigatorio']!="" ) {
            return json_encode(array('success' => false, 'response' => 'Bot detected.'));
        }

        if(!$this->oque($_REQUEST)) {
            return json_encode(array('success' => false, 'response' => 'Cálculo errado. Tente novamente.'));
        }



          $config = require BASE_PATH . 'config/mail.php';
          $config['to'] = $config['faleconosco'];
          $mensagem = $this->getMensagem();
          $mailer = new Mail($config);
          $nome = $this->getParam()->get('nome');
          $mailer->send('Contato: Portal do IPHAN - Nome: ' . $nome, $mensagem);

          return json_encode(array('success' => true));
        }

    public function oqueEoqueE() {
        $u = rand(1,5);
        $d = rand(1,5);
        $m = "Quanto é " . $u . " + " . $d . " ?";
        //$t = $u + $d;
        $oque = array("u"=>$u,"d" => $d, "m"=>$m);
        return $oque;
    }
    public function oque($oque) {
        $u = (int)$oque['u'];
        $d = (int)$oque['d'];
        $t = $u + $d;
        $e = (int)$oque['m'];
        //var_dump($u,$d,$e,$t);exit;
        if($t !== $e) {
            return false;
        }
        return true;
    }
}
