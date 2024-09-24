<?php

namespace Helpers;

/**
 * Classe que retorna Parêtros do HTTP Request
 *
 */
class Param {

    /**
     *
     * @param string $paramName Nome do Parâmetro a ser resgatado
     * @param boolean $escape Irá escapar de tags e sql words. Default TRUE
     * @return mixed Retorna uma String com o Conteúdo do Parâmetro
     */
    public static function get($paramName, $escape = TRUE)
    {
        $valor = self::getParam($paramName);

        if (is_array($valor)) {
            return $valor;
        }

        if (!is_numeric($valor) && $escape) {
            $valor = strip_tags($valor);

            //$valor = preg_replace(sql_regcase("/(from|select|insert|update|delete|where|drop table|show tables|#|\*|--|\\\\)/"), "", $valor);

            $valor = trim($valor);

            if (!get_magic_quotes_gpc()) {
//                $valor = str_replace("'", "\'", $valor);
            }
        }

        return $valor;
    }

    /**
     * Retorna Sempre uma Array.
     * Se o parâmeto não for uma Array, retorna um array() vazio.
     *
     * @param string $paramName no do paraêmtro Array
     * @return array
     */
    public static function getArray($paramName)
    {
        $valor = self::getParam($paramName);

        if (is_array($valor)) {
            return $valor;
        } else {
            return array();
        }
    }

    /**
     * Transforma o Array em uma String sepera por ','
     *
     * @param string $paramName
     * @return string
     */
    public static function getStringFromArray($paramName)
    {
        return implode(",", self::getArray($paramName));
    }

    /**
     * Retorna uma Array de ua string separa por virgula
     *
     * @param string $paramName
     * @return array
     */
    public static function getArrayFromString($paramName)
    {
        return explode(",", self::getString($paramName));
    }

    /**
     *
     * @param string $paramName
     * @param mixed $default Valor default à se retornado caso o parâmetro Seja vazio. Default NULL
     * @return string
     */
    public static function getString($paramName, $default = NULL)
    {
        $valor = self::getParam($paramName);

        if (is_array($valor)) {
            return self::getStringFromArray($paramName);
        }

        if (strlen($valor) == 0) {
            return $default;
        }

        return $valor;
    }

    /**
     * Retorna um inteiro do Parâmtro
     *
     * @param string $paramName
     * @param int $default Valor default a ser retornado quando o  parâmro for vazio. Default 0
     * @return int Retorna sempre um inteiro ou o Valor Default
     */
    public static function getInt($paramName, $default = 0)
    {
        $valor = intval(self::getParam($paramName));

        if ($valor == 0) {
            return $default;
        }

        return $valor;
    }

    /**
     * Busca o Parâmetro pelo Request
     *
     * @param string $paramName
     * @return mixed
     */
    private static function getParam($paramName)
    {
        return isset($_REQUEST[$paramName]) ? $_REQUEST[$paramName] : NULL;
    }

    /**
     * Seta o Parâmetro pelo Request
     *
     * @param string $paramName
     * @param string $paramValue
     * @return mixed
     */
    public static function setRequestValue($paramName, $paramValue = null)
    {
        $_REQUEST[$paramName] = $paramValue;
    }
}