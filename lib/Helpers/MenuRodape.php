<?php

namespace Helpers;

class MenuRodape extends Menu
{

    protected static $count = 0;

    /**
     * Método recursivo para montar o HTML do menu.
     * @param array $tree Array de objetos, itens do menu.
     * @param integer $parent
     * @param integer $numberOfColumns Número de colunas.
     * @return string HTML do menu.
     */
    public static function recursive($tree, $parent = 0, $numberOfColumns = 4)
    {
        $has_elements = FALSE;
        // Calcula a quantidade de itens por coluna
        $itemsPerColumn = count($tree) / $numberOfColumns;

        if ($parent === 0) {
            $output = '<nav class="first" role="menu"><ul>' . "\n";
        }

	// Itera todos os elementos da árvore
	foreach($tree as $item){
            $nivel1 = ($item->getTipoMenu() == 'n1');

            // Verifica se o elemento tem pai
            $item_parent = self::getParent($item);

            // Imprime somente os filhos de $parent
            if( $item_parent == $parent ) {
                if (self::$count >= $itemsPerColumn) {
                    $output .= '</ul></nav><nav role="menu"><ul>' . "\n";
                    self::$count = 0;
                }

                self::$count = self::$count + 1;
                $class = ($nivel1) ? 'link-yellow' : NULL;
                // Adiciona o tipo de menu ao final da classe do menu
                $class .= ' ' . $item->getTipoMenu();
                $output .= '<li role="menuitem">' . "\n";
                $output .= self::getLink($item, $class);
                $output .= self::recursive($tree, $item->getId());
                $output .= '</li>' . "\n";

                $has_elements = TRUE; // Marca a flag
            }
	}

        if ($parent === 0) {
            $output .= '</ul></nav>' . "\n";
        }

	// Se $parent tem filhos
	if ($has_elements) {
            return $output;
	}
    }

}
