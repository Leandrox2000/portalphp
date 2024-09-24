<?php

namespace Helpers;

/**
 * Classe DatetimeFormat
 * 
 * Helper responsável pela formatação de data e hora
 */
class DatetimeFormat
{

    /**
     * Metodo format
     * 
     * Faz a formatação de uma data 
     * @param String $date
     * @param String $format
     * @return String
     */
    public function format($date, $format)
    {

        try {
            //Faz a formatação da data utilizando a classe datetime
            $datetime = new \DateTime($date);
            return $datetime->format($format);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Metodo formatUs
     * 
     * Retorna um objeto datetime de uma data no formato brasileiro para o formato americano
     * @param String $date
     * @return String|boolean
     */
    public function formatUs($date)
    {
        try {
            //Verifica se a data possui 10 caracteres
            if (strlen($date) == 10) {
                //Explode a data 
                $arrayData = explode("/", $date);

                //Verifica se a data está no formato brasileiro
                if (count($arrayData) == 3) {
                    return $arrayData[2] . "-" . $arrayData[1] . "-" . $arrayData[0];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Metodo diff
     * 
     * Calcula e retorna a diferença e data e hora
     * Formata as datas
     * @param String $d1
     * @param String $d2
     */
    public function diff($d1, $d2)
    {
        try {
            //Calcula a diferença entre duas data/hora utulizando a classe datetime
            $datetime = new \DateTime($d2);
            return $datetime->diff($d1);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    
    
     public function formatDataExtenso($data)
    {
        try {
            setlocale( LC_ALL, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese' );
            date_default_timezone_set( 'America/Sao_Paulo' );
            return utf8_encode(strftime( '%d de %B de %Y', strtotime($data)));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
