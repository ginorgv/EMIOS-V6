<?php
session_start();

include_once($_SESSION["directorio"].'/src/lib/BasesDatos/ResultadoConsulta.php');


class ResultadoConsultaMySql extends ResultadoConsulta
{
    // Devuelve el número de filas
    function dame_numero_filas()
	{
        return ($this->res->num_rows);
	}


    // Devuelve la siguiente fila ('false' si ya no hay filas)
    function dame_siguiente_fila()
	{
        return ($this->res->fetch_assoc());
	}


    // Reinicia el contador para iterar por las filas (para poder 'reutilizar' resultados de consultas)
    function reinicia_contador_iteracion_filas()
	{
        if ($this->dame_numero_filas() > 0)
        {
            $this->res->data_seek(0);
        }
	}
}
?>