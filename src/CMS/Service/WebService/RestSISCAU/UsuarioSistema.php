<?php
namespace CMS\Service\WebService\RestSISCAU;

class UsuarioSistema extends RestSISCAU {
    
    /**
     * Consulta os usuários do SISCAU filtrando por funcionalidade.
     * 
     * @param string $funcionalidade
     * @return boolean|array
     */
    public function getUsuariosPorSistema($funcionalidade = null) {
        try {

            $resposta = $this->request('listarUsuarios', self::METHOD_GET, array(
                'sistema' => $this->getSistema(),
                'funcionalidade' => $funcionalidade,
            ));
            
        } catch (\Exception $exc) {
            $this->getLogger()->error($exc->getMessage());
            return false;
        }        
        if(empty($resposta['result'])) {
            $this->getLogger()->error('SISCAU WS Error: ' . $resposta['error']);
            return false;            
        }
        
        $dados = json_decode($resposta['result']);
        $this->ordenarAsc($dados, 'nome');
        
        return $dados;
    }
    
}