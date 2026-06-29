<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    function dame_lista_tipos_excepciones_programacion($tipo_seleccionado)
    {
        $idiomas = new Idiomas();
        $lista_tipos_excepciones = "";
        $tipos_excepcion = array(
            array(TIPO_EXCEPCION_PROGRAMACION_FECHA, $idiomas->_("Fecha")),
            array(TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS, $idiomas->_("Rango de fechas")),
            array(TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO, $idiomas->_("Día anual")),
            array(TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO, $idiomas->_("Rango de días anual")),
            array(TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA, $idiomas->_("Día de semana"))
        );
        foreach ($tipos_excepcion as $tipo_excepcion)
        {
            $lista_tipos_excepciones .= "<option value='".$tipo_excepcion[0]."'";
			if ($tipo_excepcion[0] == $tipo_seleccionado)
			{
				$lista_tipos_excepciones .= " selected";
			}
			$lista_tipos_excepciones .= ">".$tipo_excepcion[1]."</option>";
        }
        return ($lista_tipos_excepciones);
    }


    function dame_descripcion_tipo_excepcion_programacion($tipo)
    {
        $idiomas = new Idiomas();
        switch ($tipo)
        {
            case TIPO_EXCEPCION_PROGRAMACION_FECHA:
            {
                $descripcion = "Fecha";
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS:
            {
                $descripcion = "Rango de fechas";
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO:
            {
                $descripcion = "Día anual";
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO:
            {
                $descripcion = "Rango de días anual";
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA:
            {
                $descripcion = "Día de semana";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }
        $descripcion = $idiomas->_($descripcion);
        return ($descripcion);
    }


    //
    // Funciones de obtención de información de programaciones
    //


    function dame_fila_programacion($id_programacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_programacion = "
            SELECT *
            FROM programaciones
            WHERE
                id = '".$bd_red->_($id_programacion)."'";
        $res_programacion = $bd_red->ejecuta_consulta($consulta_programacion);
        if (($res_programacion == false) || ($res_programacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_programacion."'");
        }
        $fila_programacion = $res_programacion->dame_siguiente_fila();
        return ($fila_programacion);
    }


    function dame_nombre_programacion($id_programacion)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($id_programacion)
        {
            case ID_NINGUNO:
            {
                $nombre_programacion = $idiomas->_("Ninguna");
                break;
            }
            default:
            {
                $consulta_programacion = "
                    SELECT nombre
                    FROM programaciones
                    WHERE
                        id = '".$bd_red->_($id_programacion)."'";
                $res_programacion = $bd_red->ejecuta_consulta($consulta_programacion);
                if (($res_programacion == false) || ($res_programacion->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_programacion."'");
                }
                $fila_programacion = $res_programacion->dame_siguiente_fila();
                $nombre_programacion = $fila_programacion["nombre"];
                break;
            }
        }
        return ($nombre_programacion);
    }


    function dame_fila_accion_programacion($id_accion_programacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_accion_programacion = "
            SELECT *
            FROM acciones_programaciones
            WHERE
                id = '".$bd_red->_($id_accion_programacion)."'";
        $res_accion_programacion = $bd_red->ejecuta_consulta($consulta_accion_programacion);
        if (($res_accion_programacion == false) || ($res_accion_programacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_accion_programacion."'");
        }
        $fila_accion_programacion = $res_accion_programacion->dame_siguiente_fila();
        return ($fila_accion_programacion);
    }


    function dame_fila_excepcion_programacion($id_excepcion_programacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_excepcion_programacion = "
            SELECT *
            FROM excepciones_programaciones
            WHERE
                id = '".$bd_red->_($id_excepcion_programacion)."'";
        $res_excepcion_programacion = $bd_red->ejecuta_consulta($consulta_excepcion_programacion);
        if (($res_excepcion_programacion == false) || ($res_excepcion_programacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_excepcion_programacion."'");
        }
        $fila_excepcion_programacion = $res_excepcion_programacion->dame_siguiente_fila();
        return ($fila_excepcion_programacion);
    }
?>