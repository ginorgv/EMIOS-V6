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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoSensor.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ENVIO_VALORES_MANUALES_SENSOR, $_POST);

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
    $origen_envio_valores_manuales = $_POST["origen_envio_valores_manuales"];
    $id_origen_envio_valores_manuales = $_POST["id_origen_envio_valores_manuales"];

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_sensores_enviar_valores_manuales_sensor">'.$idiomas->_("Enviar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Enviar valores manuales a un sensor");
    $error = rellena_contenido_ventana_envio_valores_manuales_sensor(
        $clase_sensor,
        $id_sensor,
        $origen_envio_valores_manuales,
        $id_origen_envio_valores_manuales,
        $contenido);
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
	// Funciones para mostrar el contenido de la ventana de envío de valores manuales a un sensor
	//


	// Función que rellena el contenido de la ventana de envío de valores manuales a un sensor
	function rellena_contenido_ventana_envio_valores_manuales_sensor(
        $clase_sensor,
        $id_sensor,
        $origen_envio_valores_manuales,
        $id_origen_envio_valores_manuales,
        &$contenido)
	{
        $idiomas = new Idiomas();

        // Lista de clases y de sensores
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_sensor_envio_valores_manuales_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_envio_valores_manuales_sensor' class='chosen-select-administracion' hidden>";
        $contenido .= dame_lista_sensores_externos($clase_sensor, CLASE_SENSOR_EXTERNO_NINGUNA, array($id_sensor));
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='controles_fecha_hora_envio_valores_manuales_sensor' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha").": "."</span><br/>
                    <input size='10' type='text' id='fecha_envio_valores_manuales_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_envio_valores_manuales_sensor' class='selector-hora timepicker' readonly='readonly'>
                    </span>
                </div>
            </div>

            <div class='row-fluid' id='control_valores_envio_valores_manuales_sensor' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Valores').": "."</span><br/>
                    <input type='text' id='valores_envio_valores_manuales_sensor'
                        class='TLNT_input_valid_characters input-administracion' value=''>
                </div>
            </div>

            <div class='row-fluid' id='control_incrementos_envio_valores_manuales_sensor' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Incrementos').": "."</span><br/>
                    <input type='text' id='incrementos_envio_valores_manuales_sensor'
                        class='TLNT_input_valid_characters input-administracion' value=''>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_horas_incrementos_envio_valores_manuales_sensor' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Horas de incrementos").": "."</span><br/>
                    <select id='tipo_horas_incrementos_envio_valores_manuales_sensor' class='select-administracion'>";
        $contenido .= dame_lista_tipos_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_horas_incrementos_envio_valores_manuales_sensor' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Número de horas de incrementos').": "."</span><br/>
                    <input type='text' id='horas_incrementos_envio_valores_manuales_sensor'
                        class='TLNT_input_float input-administracion' value='0'>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_incrementos_envio_valores_manuales_sensor' hidden>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de incrementos").": "."</span><br/>
                    <select id='tipo_incrementos_envio_valores_manuales_sensor' class='select-administracion'>";
        $contenido .= dame_lista_tipos_incrementos_valores_sensor(TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Parámetros no visibles (en un 'div' oculto)
        $contenido .= '
            <div id="parametros_ventana_envio_valores_manuales"
                origen_envio_valores_manuales="'.$origen_envio_valores_manuales.'"
                id_origen_envio_valores_manuales="'.$id_origen_envio_valores_manuales.'"
                hidden>
            </div>';
        return ("OK");
	}
?>
