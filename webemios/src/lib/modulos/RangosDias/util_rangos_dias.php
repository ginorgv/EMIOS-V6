<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    // Devuelve la fila del rango de días
    function dame_fila_rango_dias($id_rango_dias)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_rango_dias = "
            SELECT *
            FROM rangos_dias
            WHERE
                id = '".$bd_red->_($id_rango_dias)."'";
        $res_rango_dias = $bd_red->ejecuta_consulta($consulta_rango_dias);
        if (($res_rango_dias == false) || ($res_rango_dias->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_rango_dias."'");
        }
        $fila_rango_dias = $res_rango_dias->dame_siguiente_fila();
        return ($fila_rango_dias);
    }
?>
