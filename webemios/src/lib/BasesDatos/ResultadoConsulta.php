<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }


class ResultadoConsulta
{
    // Constructor
    function __construct($res)
	{
        $this->res = $res;
	}


    // Devuelve el número de filas
    function dame_numero_filas()
	{
        return (-1);
	}


    // Devuelve la siguiente fila ('false' si ya no hay filas)
    function dame_siguiente_fila()
	{
        return (NULL);
	}


    // Reinicia el contador para iterar por las filas (para poder 'reutilizar' resultados de consultas)
    function reinicia_contador_iteracion_filas()
	{
	}
}
?>