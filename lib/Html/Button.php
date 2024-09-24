<?php

namespace Html;

use \Template\TemplateAmanda;

/**
 * Description of Button
 *
 * @author Luciano
 */
class Button
{
   
    public static function icon($icon, $link="#", $cor="", $radius=0)
    {
        $dados = array('icon'=>$icon, 'link'=>$link, 'cor'=>$cor, 'radius'=>$radius);

        return self::getHtml('icon', $dados);
    }
    
    public static function iconButton($label, $link="#", $icon="no_icon", $cor="", $radius="0")
    {
        $dados = array('label'=>$label, 'link'=>$link, 'icon'=>$icon, 'cor'=>$cor, 'radius'=>$radius);
        
        return self::getHtml('iconButton', $dados);
    }
    
    public static function submit($label)
    {
        $dados = array('label'=>$label);
        
        return self::getHtml('submit', $dados);
    }
    
    /**
     * Retorna a Tabela REndeizada em HTML
     * 
     * @return HTML
     */
    private static function getHtml($button, $dados)
    {
            $tpl    = new TemplateAmanda();
            
            $loader = $tpl->getLoader();
            
            $twig   = new \Twig_Environment($loader);
            
            $twig->addExtension(new \Twig_Extension_Escaper('html'));

            return $twig->render("buttons.html.twig", array(
                                                        $button => $dados
                                                      )
                                );
    }
}
