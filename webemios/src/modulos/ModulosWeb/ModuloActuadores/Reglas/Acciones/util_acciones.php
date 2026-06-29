<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/AccionRegla.php');


    //
    // Funciones de listas
    //


    function dame_lista_causas_ejecucion_accion_regla(&$causa_seleccionada)
    {
        $causas_ejecucion_accion = AccionRegla::dame_causas_ejecucion_accion();
        foreach ($causas_ejecucion_accion as $causa_ejecucion_accion)
        {
            if ($causa_seleccionada == "")
            {
                $causa_seleccionada = $causa_ejecucion_accion;
            }
            $lista .= "<option value='".$causa_ejecucion_accion."'";
			if ($causa_ejecucion_accion == $causa_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".AccionRegla::dame_descripcion_causa_ejecucion_accion($causa_ejecucion_accion)."</option>";
        }

        return ($lista);
    }


    //
    // Funciones de obtención de información de acciones
    //


    function dame_fila_accion_regla($id_accion_regla)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_accion_regla = "
            SELECT *
            FROM acciones_reglas
            WHERE
                id = '".$bd_red->_($id_accion_regla)."'";
        $res_accion_regla = $bd_red->ejecuta_consulta($consulta_accion_regla);
        if (($res_accion_regla == false) || ($res_accion_regla->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_accion_regla."'");
        }
        $fila_accion_regla = $res_accion_regla->dame_siguiente_fila();
        return ($fila_accion_regla);
    }
?>