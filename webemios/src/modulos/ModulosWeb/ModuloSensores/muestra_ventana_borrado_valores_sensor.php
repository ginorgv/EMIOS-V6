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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_BORRADO_VALORES_SENSOR, $_POST);

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
    $pie .= '<button class="btn btn-success boton_sensores_borrar_valores_sensor">'.$idiomas->_("Borrar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Borrar valores de un sensor");
    $error = rellena_contenido_ventana_borrado_valores_sensor($clase_sensor, $id_sensor, $contenido);
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
	// Funciones para mostrar el contenido de la ventana de borrado de valores de un sensor
	//


	// Función que rellena el contenido de la ventana de borrado de valores de un sensor
	function rellena_contenido_ventana_borrado_valores_sensor($clase_sensor, $id_sensor, &$contenido)
	{
        $idiomas = new Idiomas();

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_sensor_borrado_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_borrado_valores_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_borrado_valores_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_inicio_borrado_valores_sensor' class='selector-hora timepicker'
                            readonly='readonly' value='00:00'>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de fin").": "."</span><br/>
                    <input size='10' type='text' id='fecha_fin_borrado_valores_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_fin_borrado_valores_sensor' class='selector-hora timepicker'
                            readonly='readonly' value='23:59'>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Borrar valores en tiempo real").": "."</span><br/>
					<select id='borrar_valores_tiempo_real_borrado_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no(VALOR_SI);
		$contenido .= "
					</select>
				</div>
			</div>";

        return ("OK");
	}
?>
