<?php
	if (session_status() === PHP_SESSION_NONE) { session_start(); }

    include_once($_SESSION["directorio"].'/comun/log/log.php');


    // Formatea un número (según el idioma actual)
	function formatea_numero($numero, $numero_decimales, $eliminar_ceros_finales = true)
	{
        // Caracteres de separación
        $separador_miles = $_SESSION["separador_miles"];
        $punto_decimal = $_SESSION["punto_decimal"];

        // Nota: Se convierte el número a cadena y se eliminan los ceros finales no significativos (trailing zeros)
        $numero_formateado = number_format($numero, $numero_decimales, $punto_decimal, $separador_miles);
        if ($eliminar_ceros_finales == true)
        {
            $posicion_punto_decimal = strpos($numero_formateado, $punto_decimal);
            if ($posicion_punto_decimal !== false)
            {
                $numero_caracteres_numero_formateado = strlen($numero_formateado);
                for ($i = $numero_caracteres_numero_formateado - 1; $i >= $posicion_punto_decimal; $i--)
                {
                    if ($numero_formateado[$i] == "0")
                    {
                        $numero_formateado = substr($numero_formateado, 0, -1);
                        $numero_caracteres_numero_formateado -= 1;
                    }
                    else
                    {
                        break;
                    }
                }
                if ($numero_caracteres_numero_formateado == $posicion_punto_decimal + 1)
                {
                    $numero_formateado = substr($numero_formateado, 0, -1);
                }
            }
        }
        return ($numero_formateado);
    }


    // Convierte una cadena a un número (según el idioma actual)
    function convierte_cadena_a_numero($cadena_numero)
    {
        // Caracteres de separación
        $separador_miles = $_SESSION["separador_miles"];
        $punto_decimal = $_SESSION["punto_decimal"];

        $numero = str_replace($separador_miles, "", $cadena_numero);
        $numero = str_replace($punto_decimal, ".", $numero);
        $numero = (float) $numero;
        return ($numero);
    }


    // Máximo común divisor
    function mcd($a, $b)
    {
        while (($a % $b) != 0)
        {
            $c = $b;
            $b = $a % $b;
            $a = $c;
        }
        $mcd = $b;
        return ($mcd);
    }


    // Mínimo común múltiplo
    function mcm($a, $b)
    {
        $mcm = ($a * $b) / mcd($a, $b);
        return ($mcm);
    }
?>
