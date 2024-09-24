<?php

namespace Html;

use \Template\TemplateAmanda;

/**
 * Description of Fields
 *
 * @author Luciano
 */
class Fields
{

    /**
     * 
     * @param string $name NOme do Input
     * @param type $value
     * @param type $checked
     * @param type $label
     * @return type
     */
    public static function checkbox($name, $value, $checked=false, $label="")
    {
        return self::getHtml("checkbox", array(
            "name"      => $name,
            "value"     => $value,
            "checked"   => $checked,
            "label"     => $label,
        ));
    }
    
    /**
     * Retorna a Tabela REndeizada em HTML
     * 
     * @return HTML
     */
    private static function getHtml($field, array $attrs = array())
    {
        $tpl = new TemplateAmanda();
        $twig = $tpl->getTwig();
        $twig->addExtension(new \Twig_Extension_Escaper('html'));

        return $twig->render("fields.html.twig", array(
            $field => $attrs,
        ));
    }

}
