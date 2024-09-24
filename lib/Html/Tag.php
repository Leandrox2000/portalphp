<?php

namespace Html;

use \Template\TemplateAmanda;


/**
 * Description of Tag
 *
 * @author Luciano
 */
class Tag
{
    
    /**
     * Retorna o Elemento com tag de Negrito
     * <strong></strong>
     * 
     * @param string $data
     * @param array $attrs Atributos dad TAG
     * @return HTML
     */
    public static function bold($data, array $attrs = array())
    {
        return self::getHtml("bold", $valor, $attrs);
    }
    
    /**
     * Retorna  o Elemento com tag de itálico
     * <em></em>
     * 
     * @param string $data
     * @param array $attrs atributos da Tag
     * @return HTML
     */
    public static function italic($data, array $attrs = array())
    {
        return self::getHtml("italic", $valor, $attrs);
    }
    
    /**
     * Retorna  o Elemento com tag de link
     * <a></a>
     * 
     * @param string $data
     * @param array $attrs atributos da Tag
     * @return HTML
     */
    public static function link($data, array $attrs = array())
    {
        return self::getHtml("link", $data, $attrs);
    }
    
    
    /**
     * Retorna o Elemento com a tag h1
     * <h1></h1>
     * 
     * @param string $data
     * @param array $attrs atributos da Tag
     * @return HTML
     */
    public static function h1($data, array $attrs = array())
    {
        return self::getHtml("h1", $data, $attrs);
    }
    
    /**
     * Retorna o Elemento com a tag h2
     * <h2></h2>
     * 
     * @param string $data
     * @param array $attrs atributos da Tag
     * @return HTML
     */
    public static function h2($data, array $attrs = array())
    {
        return self::getHtml("h2", $data, $attrs);
    }
    
    /**
     * Retorna o Elemento com a tag h3
     * <h3></h3>
     * 
     * @param string $data
     * @param array $attrs atributos da Tag
     * @return HTML
     */
    public static function h3($data, array $attrs = array())
    {
        return self::getHtml("h3", $data, $attrs);
    }
    
    /**
     * Retorna o Elemento com a tag h4
     * <h4></h4>
     * 
     * @param string $data
     * @param array $attrs atributos da Tag
     * @return HTML
     */
    public static function h4($data, array $attrs = array())
    {
        return self::getHtml("h4", $data, $attrs);
    }
    
    /**
     * Retorna o Elemento com a tag img
     * <img/>
     * 
     * @param string $src
     * @param array $attrs atributos da Tag
     * @return HTML
     */
    public static function img($src, array $attrs = array())
    {
        return self::getHtml("img", $src, $attrs);
    }
    
    /**
     * Retorna o Elemento com a tag div
     * <div></div>
     * 
     * @param string $data
     * @param array $attrs atributos da Tag
     * @return HTML
     */
    public static function div($data, array $attrs = array())
    {
        return self::getHtml("div", $data, $attrs);
    }
    
    /**
     * Retorna o Elemento com a tag span
     * <span></span>
     * 
     * @param string $data
     * @param array $attrs atributos da Tag
     * @return HTML
     */
    public static function span($data, array $attrs = array())
    {
        return self::getHtml("span", $data, $attrs);
    }
    
    
    /**
     * Retorna a Tabela REndeizada em HTML
     * 
     * @return HTML
     */
    private static function getHtml($tag, $data, array $attrs = array())
    {
            $tpl    = new TemplateAmanda();
            
            $loader = $tpl->getLoader();
            
            $twig   = new \Twig_Environment($loader);
            
            $twig->addExtension(new \Twig_Extension_Escaper('html'));

            return $twig->render("tags.html.twig", array(
                                                        $tag => array(
                                                                        'data'  => $data,
                                                                        'attrs' => $attrs,   
                                                                    )
                                                    )
                                );
    }

    
}
