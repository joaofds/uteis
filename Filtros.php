<?php


namespace App\Helpers;


use Carbon\Carbon;
use Illuminate\Support\Str;

abstract class Filtros
{


    /**
     * Recebe string e envia para metodos correspondente.
     *
     * @param $filtro
     * @param $valor
     * @return false|mixed
     * @throws \Exception
     */
    public static function filtrar($filtro, $valor)
    {

        $filtros = explode('|', $filtro);

        $aux = $valor;

        foreach ($filtros as $f) {

            $aux2 = explode(':', $f);

            if (!isset($aux2[0])) {
                throw new \Exception("Filtro inválido");
            }

            $filtroName = $aux2[0];
            $filtroArgs = isset($aux2[1]) ? explode(',', $aux2[1]) : [];

            if (method_exists(__CLASS__, $filtroName)) {
                $aux = call_user_func_array(array(__CLASS__, $filtroName), array_merge([$aux], $filtroArgs));
            } else {
                throw new \Exception("Filtro \"{$filtroName}\" não existe");
            }

        }

        return $aux;

    }

    /**
     * Trata vazios para null
     *
     * @param $val
     * @return mixed|null
     */
    public static function null($val)
    {
        return (trim($val) === '') ? NULL : $val;
    }

    /**
     * Trata arrays vazios
     *
     * @param $dados
     * @return mixed
     */
    public static function null_array($dados)
    {
        foreach ($dados as $i => $d) {
            if (is_array($d)) {
                $dados[$i] = self::null_array($d);
            } else {
                $dados[$i] = self::null($d);
            }
        }
        return $dados;
    }

    /**
     * Tudo para minusculo
     *
     * @param $valor
     * @return string
     */
    public static function minusculo($valor)
    {
        return Str::lower($valor);
    }

    /**
     * Tudo para maiusculo
     *
     * @param $valor
     * @return string
     */
    public static function maiusculo($valor)
    {
        return Str::upper($valor);
    }

    /**
     * Remover espaços
     *
     * @param $value
     * @return string
     */
    public static function trim($value)
    {
        return trim($value);
    }

    /**
     * Remove espaços nos dados do array
     *
     * @param array $dados
     * @return array
     */
    public static function trim_array(array $dados)
    {
        foreach ($dados as $i => $d) {

            if (is_array($d)) {
                $dados[$i] = self::trim_array($d);
            } else if ($d instanceof \stdClass) {
                $dados[$i] = self::trim_array((array)$d);
            } else {
                $dados[$i] = self::trim($d);
            }
        }
        return $dados;
    }

    /**
     * Formata data
     *
     * @param $data
     * @param string $formatoEntrada
     * @param string $formatoSida
     * @return string|null
     */
    public static function formata_data($data, $formatoEntrada = 'Y-m-d', $formatoSida = 'd/m/Y')
    {

        try {
            return Carbon::createFromFormat($formatoEntrada, trim((string)$data))->format($formatoSida);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Somente numeros
     *
     * @param $value
     * @return array|string|string[]|null
     */
    public static function digitos($value)
    {
        return preg_replace('/[^0-9]/', '', (string)$value);
    }

    /**
     * Somente letras e numeros
     *
     * @param $value
     * @return array|string|string[]|null
     */
    public static function alfanumericos($value)
    {
        return preg_replace("/[^A-Za-z0-9 ]/", '', $value);
    }

    /**
     * Trata decimais
     *
     * @param $valor
     * @param int $casas
     * @return string
     */
    public static function decimal($valor, $casas = 2)
    {
        $aux = self::digitos($valor);

        $aux = $aux / pow(10, $casas);

        return number_format($aux, $casas, '.', '');

    }

    /**
     * Somente letras
     *
     * @param $value
     * @return array|string|string[]|null
     */
    public static function letras($value)
    {
        return preg_replace("/[^A-Za-z ]/", '', $value);
    }

    /**
     * @param $val
     * @param $mask
     * @return string
     */
    public static function mascara($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    /**
     * Moeda para ponto flutuante
     *
     * @param $valor
     * @return array|string|string[]
     */
    public static function moedaParaFloat($valor)
    {
        return str_replace(',', '.', str_replace('.', '', $valor));
    }

    /**
     * Decimais para formato de moeda com duas casas decimais
     *
     * @param $valor
     * @return string
     */
    public static function dinheiro($valor)
    {
        return number_format($valor, 2, ',', '.');
    }

    /**
     * Remove parte de uma string
     *
     * @param $string
     * @param $limite
     * @return false|string
     */
    public static function limitar($string, $limite)
    {

        return substr($string, 0, $limite);
    }

    /**
     * Remove acentos
     *
     * @param $string
     * @return array|string|string[]|null
     */
    public static function removeAcentos($string){
        return preg_replace([
            "/(á|à|ã|â|ä)/",
            "/(Á|À|Ã|Â|Ä)/",
            "/(é|è|ê|ë)/",
            "/(É|È|Ê|Ë)/",
            "/(í|ì|î|ï)/",
            "/(Í|Ì|Î|Ï)/",
            "/(ó|ò|õ|ô|ö)/",
            "/(Ó|Ò|Õ|Ô|Ö)/",
            "/(ú|ù|û|ü)/",
            "/(Ú|Ù|Û|Ü)/",
            "/(ñ)/",
            "/(Ñ)/",
            "/(ç)/",
            "/(Ç)/",
            "/(°)/",
            "/(ª)/",
            "/(º)/",
            "//u" // Qualquer que não seja ASCII
        ],
            explode(";","a;A;e;E;i;I;o;O;u;U;n;N;c;C;.;.;.;"),$string);
    }

    /**
     * Trata caracteres invalidos de email
     *
     * @param $string
     * @return mixed|null
     */
    public static function emailValidator($string)
    {
        if (strlen($string) == 0) {
            // no email address given
            return null;
        } else if ( !preg_match('/^(?:[\w\d]+\.?)+@(?:(?:[\w\d]\-?)+\.)+\w{2,4}$/i', $string)) {
            // invalid email format
            return null;
        }

        return $string;
    }

    /**
     * Converte as keys de um array para o formato indicado no callback.
     *
     * @param array $array
     * @param string $callback
     * @return array
     */
    public static function array_convert_key_case(array $array, $callback = 'strtolower')
    {
        return array_combine(
            array_map($callback, array_keys($array)),
            array_values($array)
        );
    }

    /**
     * Retorna um array de palavas contidas numa string.
     * @param string $v
     * @return array
     */
    public static function palavras($v) {
        return preg_split('/\W/', $v, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Remove espaços, tabs e quebras de linhas duplicadas, substituindo por um espaço simples " "
     * @param string $str
     * @return string
     * */
    public static function revomeEspacoDuplicado($str)
    {
        return preg_replace('/\s+/', ' ', $str);
    }
}
