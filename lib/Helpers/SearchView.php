<?php

namespace Helpers;

/**
 * Funções diversas para utilização na view da busca.
 */
class SearchView
{

    const ASC_SPACE = 32;
    const ASC_DOT = 46;

    /**
     * Retorna um campo com as palavras-chave destacadas
     *
     * @param string $fieldName
     * @param object $document
     * @param object $highlighting
     * @return string
     */
    public function getHightlightedField($fieldName, $document, $highlighting)
    {
        $highlightedDocument = $highlighting->getResult($document->id);
        $highlightedField = implode(' (...) ', $highlightedDocument->getField($fieldName));
        $fieldValue = $this->removeFromStart(strip_tags($highlightedField, '<strong>'));

        if (!$this->isStartPharse($fieldValue)) {
            $fieldValue = '...' . $fieldValue;
        }

        return $fieldValue;
    }

    /**
     *
     * @param object $results
     * @return object|null
     */
    public function getResultSet($results)
    {
        // $results é uma instância iterável de Pagerfanta
        if (count($results) > 0) {
            // Pega o resultset
            return $results->getAdapter()->getResultSet(
                $results->getCurrentPageOffsetStart() - 1,
                $results->getMaxPerPage()
            );
        }

        return null;
    }

    public function timestampToDate($timestamp, $format = 'd/m/Y')
    {
        return \DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $timestamp)
                        ->format($format);
    }

    public function resultsMessage($results = null)
    {
        if ($results) {
            $numResults = $results->getNumFound();

            if ($numResults == 1) {
                return 'Foi encontrado 1 resultado para';
            } elseif ($numResults == 0) {
                return 'Nenhum resultado foi encontrado para';
            }

            return "Foram encontrados $numResults resultados para";
        }
    }

    public function selectOption($label, $value, $selected = null)
    {
        if ($value == $selected) {
            return "<option value=\"{$value}\" selected=\"selected\">{$label}</option>";
        }

        return "<option value=\"{$value}\">{$label}</option>";
    }

    /**
     * Verifica se é início de frase (começa com letra maíscula)
     *
     * @param string $str
     * @return boolean
     */
    public function isStartPharse($str)
    {
        $first = ord(substr(trim($str), 0, 1));

        // A-Z e A-Z com acentuação
        if (($first >= 65 && $first <= 90) || ($first >= 192 && $first <= 221)) {
            return true;
        }

        return false;
    }

    /**
     * Remove caracteres desnecessários do início da string
     *
     * @param string $str
     * @return string
     */
    public function removeFromStart($str)
    {
        $char = ord(substr($str, 0, 1));
        if (in_array($char, array(self::ASC_DOT, self::ASC_SPACE))) {
            return substr_replace($str, '', 0, 1);
        }

        return $str;
    }

    /**
     * Corta uma string, caso seja maior que o limite especificado.
     *
     * @param string $string
     * @param integer $limit
     * @param string $break
     * @param string $pad
     * @return string
     */
    public function truncateText($string, $limit, $break = " ", $pad = "...")
    {
        if (strlen($string) <= $limit) {
            return $string;
        }

        $string = substr($string, 0, $limit);
        if (false !== ($breakpoint = strrpos($string, $break))) {
            $string = substr($string, 0, $breakpoint);
        }

        return $string . $pad;
    }

    public function entityToDescription($entity)
    {
        $fromTo = array(
            'Entity\Ata' => 'Atas',
            'Entity\Agenda' => 'Agenda',
            'Entity\Bibliografia' => 'Bibliografia Geral do Patrimônio',
            'Entity\Biblioteca' => 'Bibliotecas do IPHAN',
            'Entity\DicionarioPatrimonioCultural' => 'Dicionário do Patrimônio Cultural',
            'Entity\Edital' => 'Editais',
            'Entity\Fototeca' => 'Fototecas',
            'Entity\Galeria' => 'Galerias',
            'Entity\Legislacao' => 'Legislação',
            'Entity\LicitacaoConvenioContrato' => 'Licitações, Convênios e Contratos',
            'Entity\Noticia' => 'Notícias',
            'Entity\PaginaEstatica' => 'Páginas internas',
            'Entity\Pergunta' => 'Perguntas Frequentes',
            'Entity\Publicacao' => 'Publicações',
            'Entity\Video' => 'Vídeos',
        );

        return strtr($entity, $fromTo);
    }

    public function entityToId($entity)
    {
        $fromTo = array(
            'Entity\Pergunta' => '?id=',
        );

        return strtr($entity, $fromTo);
    }
    
    /**
     * Para os registros de publicações onde o usuário não anexou um arquivo, 
     * o sistema deve redirecionar o usuário a livraria utilizando a mesma
     * query de busca.
     * 
     * @param \Solarium\QueryType\Select\Result\Document $document
     * @return string
     */
    public function resolverUrl($document) {
        $caminho = getcwd() .DS. $document->url;

        //echo $document->entity_name;
        //die();  
        
        if($document->entity_name == 'Entity\Pergunta' ){
            $id = str_replace('Entity\Pergunta#', '', $document->id);
            return $document->url . '/detalhes/' . $id;

        }elseif($document->entity_name != 'Entity\Publicacao' || (file_exists($caminho) && is_file($caminho))) {
            return $document->url;
        }

        return 'livrariaVirtual?' . htmlentities('busca=' . urlencode($document->title));
    }
}
