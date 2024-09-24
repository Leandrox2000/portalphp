<?php

namespace Security;

use CMS\Service\ServiceUser\User as UserService;
use Helpers\Session;
use CMS\Service\WebService\SISCAU\UsuarioSistema;
use Factory\EntityManagerFactory as EM;
use Doctrine\ORM\EntityManager;
use Helpers\Param;
use CMS\Service\WebService\RestSISCAU\UsuarioSistema as UsuarioSistemaRest;

/**
 * User
 *
 * @author Luciano
 */
class User {

    /**
     *
     * @var \Helpers\Session
     */
    private $session;

    /**
     *
     * @var string
     */
    private $error;

    /**
     *
     * @var EntityManager
     */
    private $em;

    /**
     *
     * @var Param
     */
    private $param;

    /**
     * Constructor
     *
     * @param \Helpers\Session $session
     */
    public function __construct(Session $session) {
        $this->session = $session;
    }

    /**
     *
     * @return type
     */
    public function getSession() {
        return $this->session;
    }

    /**
     *
     * @param \Helpers\Session $session
     */
    public function setSession(Session $session) {
        $this->session = $session;
    }

    /**
     *
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     *
     * @param  string $error
     */
    public function setError($error) {
        $this->error = $error;
    }

    /**
     *
     * @return Param
     */
    public function getParam() {
        if (is_null($this->param)) {
            $this->setParam(new Param());
        }
        return $this->param;
    }

    /**
     *
     * @param \Helpers\Param $param
     */
    public function setParam(Param $param) {
        $this->param = $param;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm() {
        if (is_null($this->em)) {
            $this->setEm(EM::getEntityManger());
        }
        return $this->em;
    }

    /**
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEm(EntityManager $em) {
        $this->em = $em;
    }

    public function loginSiscau($user, $pass) {
        //Verifica se a sessão existe
        if ($this->getSession()->exists('user')) {
            $this->getSession()->destroy('user');
        }

        //Instancia o web service SISCAU
        $siscau = new UsuarioSistema('webservice.php', 'UsuarioSistema', $this->getEm());

        //Faz a autenticação
        $result = $siscau->auth($user, $pass);

        //Faz a autenticação
        if ($result['success'] && !empty($result['permissoes'])) {
            
            //Monta o array da sessão
            $sessao = array(
                'permissoesUser' => $result['permissoes'],
                'subsites' => $result['subsites'],
                'dadosUser' => array('login' => $user),
                'funcionalidades' => $this->getEm()->getRepository('Entity\FuncionalidadeSiscau')->getFuncionalidadesSessao(),
                'sede' => $result['sede'],
                'banners'=> $result['banners']
            );

            //Armazena os dados do usuário na sessão
            $this->getSession()->set('user', $sessao);
            
            // Consulta os usuários que possuem permissão para criar compromissos
            $usuarioWS = new UsuarioSistemaRest();
            $sessao = array(
                'SEDE_COMPR_INSERIR' => $usuarioWS->getUsuariosPorSistema('SEDE_COMPR_INSERIR'),
            );
            
            $this->getSession()->set('usuariosPorPermissao', $sessao);

            //Retorna true
            return true;
        } else if(empty($result['permissoes'])){
            $this->setError(array('Usuário e/ou senha inválidos.'));
            
            return false;
        } else {
            //Seta o erro
            $this->setError($result['error']);

            //Retorna o erro
            return false;
        }
    }

    /**
     * @param string $user
     * @param string $pass
     * @return boolean
     */
    public function verifyUser($user, $pass) {
        return $this->loginSiscau($user, $pass);
    }

    /**
     *
     * @return UserService
     */
    public function getUser() {
        return $this->getSession()->get('user');
    }

    /**
     * Define o usuário.
     */
    public function setUser() {
        $user = new UserService();
        $this->getSession()->set('user', $user->getUser());
    }

    /**
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function verifyPermissions($controller, $action, $basePath, array $params) {

        //Busca a session de usuários e as funcionálidades do sistema
        $user = $this->getSession()->get("user");

        //Caso não tenha encontrado, e o controller não for de login, redireciona para a página de login
        if (empty($user) && $controller != "login") {
            \Helpers\Http::redirect("{$basePath}login");
        } else {
            //Verifica a ação
            switch ($action) {
                case 'form' :
                    $action = count($params) == 0 ? 'inserir' : 'alterar';
                    break;
                case 'alterarStatus' :
                    $action = $this->getParam()->get('status') == 1 ? 'publicar' : 'naopublicar';
                    break;
            }

            //Verifica a existência da funcionálidade passada
            if (isset($user['funcionalidades'][$controller][$action])) {
                //Armazena a sigla funcionálidade
                $sigla = $user['funcionalidades'][$controller][$action];

                //Verifica se o usuário possui permissão na funcionálidade passada
                if (isset($user['permissoesUser'][$sigla])) {
                    return true;
                } else {
                    \Helpers\Http::redirect("{$basePath}index");
                }
            }
            
            //echo "Aqui<br/>";
//            echo "<pre>";
//                var_dump($user['funcionalidades'][$controller]);
//            echo "</pre>";
            
            if ($controller != "gerenciadorBanner") {
                if (isset($user['funcionalidades'][$controller][$action])) {
                   //Armazena a sigla funcionálidade
                   $sigla = $user['funcionalidades'][$controller][$action];

                   //Verifica se o usuário possui permissão na funcionálidade passada
                   if (isset($user['permissoesUser'][$sigla])) {
                       return true;
                   } else {
                       \Helpers\Http::redirect("{$basePath}index");
                   }
               }
            }else{
                
//                echo $action;
//                echo "<pre>";
//                    var_dump($user['funcionalidades']);
//                echo "</pre>";
//                die();
//                if($action == "pagination") return true;
                
                if (isset($user['funcionalidades'][$controller][$action])) {
                    foreach ($user['banners'] as $sub => $s) {
                        foreach ($s as $categoria => $c) {
                            if($c[$user['funcionalidades'][$controller][$action]] == "1" or $c['inserir'] == true) $possuiPermissao = true;
                        }
                    } 
                    
                    if($possuiPermissao == true){
                        return true;
                    }else{
                        \Helpers\Http::redirect("{$basePath}index");
                    }
                }else{
                    return true;
                }
               
             
                
                
            }
            
//            echo "chegou aquiiiiiiiiiiiiiii2";
//            die();
            
        }
        return true;
    }

}
