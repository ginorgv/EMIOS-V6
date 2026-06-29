<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_EXCEPCION_PROGRAMACION, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_programacion = $_POST["id_programacion"];
    $id_excepcion = $_POST["id_excepcion"];
    if ($id_excepcion === NULL)
    {
        $id_excepcion = ID_NINGUNO;
    }

    // Añadir o modificar excepción
    $anyadir_excepcion = ($id_excepcion == ID_NINGUNO);
    if ($anyadir_excepcion == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_actuadores_anyadir_modificar_excepcion_programacion">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("excepción");
    $error = rellena_contenido_ventana_anyadir_modificar_excepcion($anyadir_excepcion, $id_excepcion, $contenido);
    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    // Se añaden los parámetros (no visibles) en un 'div' oculto
    $contenido .= '
        <div id="parametros_ventana_anyadir_modificar_excepcion_programacion"
            anyadir_excepcion="'.$anyadir_excepcion.'"
            clase_actuador="'.$clase_actuador.'"
            id_programacion="'.$id_programacion.'"
            id_excepcion="'.$id_excepcion.'"
            hidden>
        </div>';

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar excepción
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar excepción
	function rellena_contenido_ventana_anyadir_modificar_excepcion($anyadir_excepcion, $id_excepcion, &$contenido)
	{
		// Si hay que modificar la excepción, se recupera la información actual de la base de datos
		if ($anyadir_excepcion == false)
		{
			$fila_excepcion_programacion = dame_fila_excepcion_programacion($id_excepcion);

            $nombre_excepcion = $fila_excepcion_programacion["nombre"];
            $tipo_excepcion = $fila_excepcion_programacion["tipo"];
			$cadena_fecha_excepcion_base_datos_local = $fila_excepcion_programacion["fecha"];
            $cadena_fecha_inicio_excepcion_base_datos_local = $fila_excepcion_programacion["fecha_inicio"];
            $cadena_fecha_fin_excepcion_base_datos_local = $fila_excepcion_programacion["fecha_fin"];
            $cadena_dia_anyo_excepcion_base_datos_local = $fila_excepcion_programacion["dia_anyo"];
            $cadena_dia_anyo_inicio_excepcion_base_datos_local = $fila_excepcion_programacion["dia_anyo_inicio"];
            $cadena_dia_anyo_fin_excepcion_base_datos_local = $fila_excepcion_programacion["dia_anyo_fin"];
            $dia_semana_excepcion = $fila_excepcion_programacion["dia_semana"];
		}

        // Se recuperan los controles de la excepción
		$contenido .= dame_controles_excepcion(
            $nombre_excepcion,
            $tipo_excepcion,
            $cadena_fecha_excepcion_base_datos_local,
            $cadena_fecha_inicio_excepcion_base_datos_local,
            $cadena_fecha_fin_excepcion_base_datos_local,
            $cadena_dia_anyo_excepcion_base_datos_local,
            $cadena_dia_anyo_inicio_excepcion_base_datos_local,
            $cadena_dia_anyo_fin_excepcion_base_datos_local,
            $dia_semana_excepcion);

        return ("OK");
	}


    function dame_controles_excepcion(
        $nombre_excepcion,
        $tipo_excepcion,
        $cadena_fecha_excepcion_base_datos_local,
        $cadena_fecha_inicio_excepcion_base_datos_local,
        $cadena_fecha_fin_excepcion_base_datos_local,
        $cadena_dia_anyo_excepcion_base_datos_local,
        $cadena_dia_anyo_inicio_excepcion_base_datos_local,
        $cadena_dia_anyo_fin_excepcion_base_datos_local,
        $dia_semana_excepcion)
    {
        $idiomas = new Idiomas();

        // Controles de excepción
        $controles_excepcion = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
                    <input type='text' id='nombre_excepcion_programacion'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre_excepcion, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_excepcion_programacion' class='select-administracion'>";
        $controles_excepcion .= dame_lista_tipos_excepciones_programacion($tipo_excepcion);
        $controles_excepcion .= "
                    </select>
                </div>
            </div>";
        switch ($tipo_excepcion)
        {
            case TIPO_EXCEPCION_PROGRAMACION_FECHA:
            {
                $cadena_fecha_inicio_excepcion_base_datos_local = NULL;
                $cadena_fecha_fin_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_inicio_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_fin_excepcion_base_datos_local = NULL;
                $dia_semana_excepcion = NULL;
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS:
            {
                $cadena_fecha_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_inicio_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_fin_excepcion_base_datos_local = NULL;
                $dia_semana_excepcion = NULL;
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO:
            {
                $cadena_fecha_excepcion_base_datos_local = NULL;
                $cadena_fecha_inicio_excepcion_base_datos_local = NULL;
                $cadena_fecha_fin_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_inicio_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_fin_excepcion_base_datos_local = NULL;
                $dia_semana_excepcion = NULL;
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO:
            {
                $cadena_fecha_excepcion_base_datos_local = NULL;
                $cadena_fecha_inicio_excepcion_base_datos_local = NULL;
                $cadena_fecha_fin_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_excepcion_base_datos_local = NULL;
                $dia_semana_excepcion = NULL;
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA:
            {
                $cadena_fecha_excepcion_base_datos_local = NULL;
                $cadena_fecha_inicio_excepcion_base_datos_local = NULL;
                $cadena_fecha_fin_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_inicio_excepcion_base_datos_local = NULL;
                $cadena_dia_anyo_fin_excepcion_base_datos_local = NULL;
                break;
            }
        }

        $fecha_hora_actual_local = dame_fecha_hora_actual_local();
        if ($cadena_fecha_excepcion_base_datos_local === NULL)
        {
            $cadena_fecha_excepcion_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_fecha_local"]);
        }
        else
        {
            $cadena_fecha_excepcion_local_local = convierte_formato_fecha($cadena_fecha_excepcion_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        }
        if ($cadena_fecha_inicio_excepcion_base_datos_local === NULL)
        {
            $cadena_fecha_inicio_excepcion_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_fecha_local"]);
        }
        else
        {
            $cadena_fecha_inicio_excepcion_local_local = convierte_formato_fecha($cadena_fecha_inicio_excepcion_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        }
        if ($cadena_fecha_fin_excepcion_base_datos_local === NULL)
        {
            $cadena_fecha_fin_excepcion_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_fecha_local"]);
        }
        else
        {
            $cadena_fecha_fin_excepcion_local_local = convierte_formato_fecha($cadena_fecha_fin_excepcion_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        }

        if ($cadena_dia_anyo_excepcion_base_datos_local === NULL)
        {
            $cadena_dia_anyo_excepcion_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_dia_anyo_local"]);
        }
        else
        {
            $cadena_dia_anyo_excepcion_local_local = convierte_formato_dia_anyo($cadena_dia_anyo_excepcion_base_datos_local, FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
        }
        if ($cadena_dia_anyo_inicio_excepcion_base_datos_local === NULL)
        {
            $cadena_dia_anyo_inicio_excepcion_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_dia_anyo_local"]);
        }
        else
        {
            $cadena_dia_anyo_inicio_excepcion_local_local = convierte_formato_dia_anyo($cadena_dia_anyo_inicio_excepcion_base_datos_local, FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
        }
        if ($cadena_dia_anyo_fin_excepcion_base_datos_local === NULL)
        {
            $cadena_dia_anyo_fin_excepcion_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_dia_anyo_local"]);
        }
        else
        {
            $cadena_dia_anyo_fin_excepcion_local_local = convierte_formato_dia_anyo($cadena_dia_anyo_fin_excepcion_base_datos_local, FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
        }

        if ($dia_semana_excepcion === NULL)
        {
            $dia_semana_excepcion = 1;
        }

        $controles_excepcion .= "
            <div class='row-fluid'>
				<div id='contenedor_fecha_excepcion_programacion' class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha").": "."</span><br/>
                    <input size='10' type='text' id='fecha_excepcion_programacion' class='datepicker selector-fechas-administracion'
                        readonly='readonly' value='".$cadena_fecha_excepcion_local_local."' hidden>
                </div>
			</div>

            <div class='row-fluid'>
                <div id='contenedor_rango_fechas_excepcion_programacion' class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fechas de inicio y fin").": "."</span><br/>
                    <input type='text' id='fecha_inicio_excepcion_programacion' class='datepicker selector-fechas-administracion-50-izda'
                        readonly='readonly' value='".$cadena_fecha_inicio_excepcion_local_local."' hidden>
                    <input type='text' id='fecha_fin_excepcion_programacion' class='datepicker selector-fechas-administracion-50-dcha'
                        readonly='readonly' value='".$cadena_fecha_fin_excepcion_local_local."' hidden>
                </div>
			</div>

            <div class='row-fluid'>
				<div id='contenedor_dia_anyo_excepcion_programacion' class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Día anual").": "."</span><br/>
                    <input size='10' type='text' id='dia_anyo_excepcion_programacion' class='monthdaypicker selector-fechas-administracion-50-izda'
                        readonly='readonly' value='".$cadena_dia_anyo_excepcion_local_local."' hidden>
                </div>
			</div>

            <div class='row-fluid'>
                <div id='contenedor_rango_dias_anyo_excepcion_programacion' class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Días anuales de inicio y fin").": "."</span><br/>
                    <input type='text' id='dia_anyo_inicio_excepcion_programacion' class='monthdaypicker selector-fechas-administracion-50-izda'
                        readonly='readonly' value='".$cadena_dia_anyo_inicio_excepcion_local_local."' hidden>
                    <input type='text' id='dia_anyo_fin_excepcion_programacion' class='monthdaypicker selector-fechas-administracion-50-dcha'
                        readonly='readonly' value='".$cadena_dia_anyo_fin_excepcion_local_local."' hidden>
                </div>
			</div>

            <div class='row-fluid'>
                <div id='contenedor_dia_semana_excepcion_programacion' class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Día de semana").": "."</span><br/>
                    <select id='dia_semana_excepcion_programacion'
                        class='select-administracion' hidden>";
        for ($i = 1; $i <= 7; $i++)
        {
            $controles_excepcion .= dame_opcion_dia_semana($i, $dia_semana_excepcion);
        }
        $controles_excepcion .= "
                    </select>
                </div>
            </div>";

        return ($controles_excepcion);
    }


    function dame_opcion_dia_semana($dia_semana, $dia_semana_seleccionado)
    {
        $opcion .= "<option value='".$dia_semana."'";
        if ($dia_semana == $dia_semana_seleccionado)
        {
            $opcion .= " selected";
        }
        $opcion .= ">".dame_nombre_dia_semana($dia_semana)."</option>";

        return ($opcion);
    }
?>
