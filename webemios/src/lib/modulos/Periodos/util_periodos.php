<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    // Devuelve la fila del periodo
    function dame_fila_periodo($id_periodo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_periodo = "
            SELECT *
            FROM periodos
            WHERE
                id = '".$bd_red->_($id_periodo)."'";
        $res_periodo = $bd_red->ejecuta_consulta($consulta_periodo);
        if (($res_periodo == false) || ($res_periodo->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_periodo."'");
        }
        $fila_periodo = $res_periodo->dame_siguiente_fila();
        return ($fila_periodo);
    }
?>
