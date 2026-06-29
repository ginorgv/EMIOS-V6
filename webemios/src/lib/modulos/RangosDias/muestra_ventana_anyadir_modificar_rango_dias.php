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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/RangosDias/util_rangos_dias.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_RANGO_DIAS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $origen = $_POST["origen"];
    $id_origen = $_POST["id_origen"];
    $id_rango_dias = $_POST["id_rango_dias"];
    if ($id_rango_dias === NULL)
    {
        $id_rango_dias = ID_NINGUNO;
    }

    // Añadir o modificar periodo
    $anyadir_rango_dias = ($id_rango_dias == ID_NINGUNO);
    if ($anyadir_rango_dias == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_anyadir_modificar_rango_dias">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("rango de días");
    $error = rellena_contenido_ventana_anyadir_modificar_rango_dias($anyadir_rango_dias, $id_rango_dias, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_rango_dias"
            anyadir_rango_dias="'.$anyadir_rango_dias.'"
            origen="'.$origen.'"
            id_origen="'.$id_origen.'"
            id_rango_dias="'.$id_rango_dias.'"
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar rango de días
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar rango de días
	function rellena_contenido_ventana_anyadir_modificar_rango_dias($anyadir_rango_dias, $id_rango_dias, &$contenido)
	{
		// Si hay que modificar el rango de días, se recupera la información actual de la base de datos
		if ($anyadir_rango_dias == false)
		{
			$fila_rango_dias = dame_fila_rango_dias($id_rango_dias);

            $cadena_dia_anyo_inicio_base_datos_local = $fila_rango_dias["dia_anyo_inicio"];
            $cadena_dia_anyo_fin_base_datos_local = $fila_rango_dias["dia_anyo_fin"];
		}

        // Se recuperan los controles del rango de días
		$contenido .= dame_controles_rango_dias($cadena_dia_anyo_inicio_base_datos_local, $cadena_dia_anyo_fin_base_datos_local);

        return ("OK");
	}


    function dame_controles_rango_dias($cadena_dia_anyo_inicio_base_datos_local, $cadena_dia_anyo_fin_base_datos_local)
    {
        $idiomas = new Idiomas();

        if (($cadena_dia_anyo_inicio_base_datos_local === NULL) || ($cadena_dia_anyo_fin_base_datos_local === NULL))
        {
            $fecha_hora_actual_local = dame_fecha_hora_actual_local();
            $cadena_dia_anyo_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_dia_anyo_local"]);
            $cadena_dia_anyo_fin_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_dia_anyo_local"]);
        }
        else
        {
            $cadena_dia_anyo_inicio_local_local = convierte_formato_dia_anyo($cadena_dia_anyo_inicio_base_datos_local, FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
            $cadena_dia_anyo_fin_local_local = convierte_formato_dia_anyo($cadena_dia_anyo_fin_base_datos_local, FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
        }

        // Controles del rango de días
        $controles_rango_dias = "
            <div class='row-fluid'>
                <div id='contenedor_rango_dias_anyo_rango_dias' class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Días de inicio y fin").": "."</span><br/>
                    <input type='text' id='dia_anyo_inicio_rango_dias' class='monthdaypicker selector-fechas-administracion-50-izda'
                        readonly='readonly' value='".$cadena_dia_anyo_inicio_local_local."' hidden>
                    <input type='text' id='dia_anyo_fin_rango_dias' class='monthdaypicker selector-fechas-administracion-50-dcha'
                        readonly='readonly' value='".$cadena_dia_anyo_fin_local_local."' hidden>
                </div>
			</div>";

        return ($controles_rango_dias);
    }
?>
