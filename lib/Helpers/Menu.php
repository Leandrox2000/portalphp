<?php

namespace Helpers;
use Helpers\Http;

/**
 * Helper utilizado para gerar a saída HTML do menu.
 *
 * @author Igor Cemim
 */
class Menu {

    /**
     *
     * @param object $item
     * @return int
     */
    protected static function getParent($item)
    {
        if (is_object($item->getVinculoPai())) {
            $item_parent = $item->getVinculoPai()->getId();
        } else {
            $item_parent = 0;
        }

        return $item_parent;
    }
 
    /**
     *
     * @param \Entity\Menu $item
     * @return string
     */
    protected static function getLink(\Entity\Menu $item, $class = null, $baseLink = null)
    {
        $titulo = $item->getTitulo();
        $url = Http::generateUrl($item, $baseLink);
        $targetAttr = ($item->getAbrirEm() == 'm') ? null : ' target="_blank"';
        $classAttr = ( ( $class !== null ) ? ' class="' . $class . '"' : null );
        if($url and (!substr_count($url, 'www') and !substr_count($url, 'http://') and !substr_count($url, 'https://')))
            $url = ' href="http://'.Http::getServerName()."/". $url . '"' . $targetAttr;
        elseif(substr_count($url, 'www') and (!substr_count($url, 'http://') and !substr_count($url, 'https://')))
            $url = ' href="http://'. $url . '"' . $targetAttr;
        elseif(substr_count($url, 'http://') or substr_count($url, 'https://'))
            $url = ' href="'. $url . '"' . $targetAttr;
        return '<a ' . $classAttr . $url. '><span class="hidden-span">Link de menu: </span>' . $titulo . '</a>' . "\n";
    }

    /**
     * Método recursivo para montar o HTML do menu.
     * @param array $tree Array de objetos, itens do menu.
     * @param int $parent
     * @return string HTML do menu.
     */
    public static function recursive($tree, $parent = 0, $class = 'dropdown clearfix', $preffix = null)
    {
        $has_elements = false;
        $output = '<ul ' . ( ($parent == 0) ? 'class="menu ' . $class . '"' : null) . '>' . "\n";

        // Itera todos os elementos da árvore
        foreach ($tree as $item) {
            // Verifica se o elemento tem pai
            $item_parent = self::getParent($item); 

            // Imprime somente os filhos de $parent
            if ($item_parent == $parent) {
                $output .= '<li role="menuitem">' . "\n";
                $output .= self::getLink($item, null, $preffix);
                $output .= self::recursive($tree, $item->getId(), $class, $preffix);
                $output .= '</li>' . "\n";

                $has_elements = true; // Marca a flag
            }
        }

        $output .= '</ul>' . "\n";

        // Se $parent tem filhos
        if ($has_elements) {
            return $output;
        }
    }
    
     public static function recursiveSubsite($tree, $parent = 0, $class = 'dropdown clearfix', $preffix = null)
    {
        $has_elements = false;
        
        $output = '<ul ' . ( ($parent == 0) ? 'class="menu ' . $class . '"' : 'class="' . $class . '"') . '>' . "\n";

        // Itera todos os elementos da árvore
        foreach ($tree as $item) {
            // Verifica se o elemento tem pai
            $item_parent = self::getParent($item); 

            // Imprime somente os filhos de $parent
            if ($item_parent == $parent) {
                $output .= '<li role="menuitem" >' . "\n";
                $output .= self::getLink($item, null, $preffix);
                $output .= self::recursiveSubsite($tree, $item->getId(), $item->getTipoMenu(), $preffix);
                $output .= '</li>' . "\n";

                $has_elements = true; // Marca a flag
            }
        }

        $output .= '</ul>' . "\n";

        // Se $parent tem filhos
        if ($has_elements) {
            return $output;
        }
    }
    
    
    /**
     * @param array $tree Array de objetos, itens do menu.
     * @param int $parent
     * @return string HTML do menu.
     */
    public static function menuRecursiveThree($tree, $parent = 0, $class = '', $preffix = null, $colocaDiv = true)
    {
        $has_elements = false;
        
        $output .= "<ul class='.$class.'>" . "\n";

        // Itera todos os elementos da árvore
        foreach ($tree as $item) {
            // Verifica se o elemento tem pai
            $item_parent = self::getParent($item); 

            // Imprime somente os filhos de $parent
            if ($item_parent == $parent) {
                $output .= '<li class='.$class.' id='.$item->getId().'>' . "\n";
                $output .= self::getLinkThree($item, null, $preffix);
                $output .= self::menuRecursiveThree($tree, $item->getId(), $class, $preffix,false);
                $output .= '</li>' . "\n";

                $has_elements = true; // Marca a flag
            }
        }

        $output .= '</ul>' . "\n";
        // Se $parent tem filhos
        if ($has_elements) {
            return $output;
        }
    }
    
     /**
     *
     * @param \Entity\Menu $item
     * @return string
     */
    protected static function getLinkThree(\Entity\Menu $item, $class = null, $baseLink = null)
    {
        $titulo = $item->getTitulo();
        $url = Http::generateUrl($item, $baseLink);
        $targetAttr = ($item->getAbrirEm() == 'm') ? null : ' target="_blank"';
        $classAttr = ( ( $class !== null ) ? ' class="' . $class . '"' : null );
        if($url and (!substr_count($url, 'www') and !substr_count($url, 'http://') and !substr_count($url, 'https://')))
            $url = ' href="http://'.Http::getServerName()."/". $url . '"' . $targetAttr;
        elseif(substr_count($url, 'www') and (!substr_count($url, 'http://') and !substr_count($url, 'https://')))
            $url = ' href="http://'. $url . '"' . $targetAttr;
        elseif(substr_count($url, 'http://') or substr_count($url, 'https://'))
            $url = ' href="'. $url . '"' . $targetAttr;
        
       //$aux = ' data-src="'. $item->getId() . '"';
       //return '<a '.$url.'>'.$titulo . '</a>' . "\n";
        
       return $titulo;
    }
    
    
    
    

}