<?php

namespace Helpers;

/**
 * Helper utilizado para gerar a saída HTML do menu.
 *
 * @author Igor Cemim
 */
class MenuLinksRapidos extends Menu {

    /**
     * Método recursivo para montar o HTML do menu.
     * @param array $tree Array de objetos, itens do menu.
     * @param int $parent
     * @return string HTML do menu.
     */
    public static function recursive($tree, $parent = 0, $class = 'links-rapidos')
    {
        
        $has_elements = FALSE;
        $output = '<ul ' . ( ($parent == 0) ? 'class="' . $class . '"' : NULL) . '>' . "\n";

        // Itera todos os elementos da árvore
        foreach ($tree as $item) {
            // Verifica se o elemento tem pai
            $item_parent = self::getParent($item);

            // Imprime somente os filhos de $parent
            if ($item_parent == $parent) {
                $output .= '<li>' . "\n";
                $output .= self::getLink($item);
                $output .= self::recursive($tree, $item->getId());
                $output .= '</li>' . "\n";

                $has_elements = TRUE; // Marca a flag
            }
        }

        $output .= '</ul>' . "\n";

        // Se $parent tem filhos
        if ($has_elements) {
            return $output;
        }
    }

}