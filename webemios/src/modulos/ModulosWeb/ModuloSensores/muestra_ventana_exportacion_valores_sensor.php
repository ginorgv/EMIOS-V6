<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_EXPORTACION_VALORES_SENSOR, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $clase_sensor = $_POST["clase_sensor"];
    if ($clase_sensor === NULL)
    {
        $clase_sensor = CLASE_NINGUNA;
    }
    $id_sensor = $_POST["id_sensor"];
    if ($id_sensor === NULL)
    {
        $id_sensor = ID_NINGUNO;
    }

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_sensores_exportar_valores_sensor">'.$idiomas->_("Exportar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Exportar valores");
    $error = rellena_contenido_ventana_exportacion_valores_sensor($clase_sensor, $id_sensor, $contenido);
    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funciones para mostrar el contenido de la ventana de exportación de valores de un sensor
	//


	// Función que rellena el contenido de la ventana de exportación de valores de un sensor
	function rellena_contenido_ventana_exportacion_valores_sensor($clase_sensor, $id_sensor, &$contenido)
	{
        $idiomas = new Idiomas();

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_sensor_exportacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_exportacion_valores_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
				</div>
			</div>";

        // Intervalo de valores
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_exportacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_exportacion_clase_sensor($clase_sensor, INTERVALO_VALORES_HORA);
		$contenido .= "
                    </select>
				</div>
			</div>";

        // Tipo de incrementos de valores
        $contenido .= "
            <div class='row-fluid' id='control_tipo_incrementos_valores_exportacion_valores_sensor' hidden>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de incrementos").": "."</span><br/>
                    <select id='tipo_incrementos_valores_exportacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_tipos_incrementos_valores_sensor(TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL);
		$contenido .= "
                    </select>
				</div>
			</div>";

        // Valores de clase de sensor
        $contenido .= "
            <div class='row-fluid' id='control_valores_clase_sensor_exportacion_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valores de clase").": "."</span><br/>
                    <select id='valores_clase_sensor_exportacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no(VALOR_NO);
        $contenido .= "
                    </select>
                    <span id='boton_sensores_ayuda_valores_clase_exportacion' class='clickable boton-ayuda-select-administracion'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_exportacion_valores_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_inicio_exportacion_valores_sensor' class='selector-hora timepicker'
                            readonly='readonly' value='00:00'>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de fin").": "."</span><br/>
                    <input size='10' type='text' id='fecha_fin_exportacion_valores_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_fin_exportacion_valores_sensor' class='selector-hora timepicker'
                            readonly='readonly' value='23:59'>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Punto decimal").": "."</span><br/>
					<select id='id_punto_decimal_exportacion_valores_sensor' class='select-administracion'>";
        # Se obtiene el punto decimal configurado en la red
        # de la sesion, si no tuviera configurado (imposible)
        # por defecto coma para evitar errores con Excel
        $punto_decimal = $_SESSION["punto_decimal"];

        if ($punto_decimal == ",")
        {
            $punto_decimal = ID_PUNTO_DECIMAL_COMA;
        }
        elseif ($punto_decimal == ".")
        {
            $punto_decimal = ID_PUNTO_DECIMAL_PUNTO;
        }
        else
        {
            $punto_decimal = ID_PUNTO_DECIMAL_COMA;
        }

        $contenido .= dame_lista_valores(
            array(
                array(ID_PUNTO_DECIMAL_COMA, dame_descripcion_id_punto_decimal(ID_PUNTO_DECIMAL_COMA)),
                array(ID_PUNTO_DECIMAL_PUNTO, dame_descripcion_id_punto_decimal(ID_PUNTO_DECIMAL_PUNTO))),
            array($punto_decimal));
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Zona horaria").": "."</span><br/>
                    <select id='zona_horaria_exportacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_zonas_horarias($_SESSION["zona_horaria"]);
        $contenido .= "
                    </select>
                </div>
            </div>";

        return ("OK");
	}
?>
