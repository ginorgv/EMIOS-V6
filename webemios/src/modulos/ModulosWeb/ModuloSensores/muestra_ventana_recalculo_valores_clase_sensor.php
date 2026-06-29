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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_RECALCULO_VALORES_CLASE_SENSOR, $_POST);

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
    $pie .= '<button class="btn btn-success boton_sensores_recalcular_valores_clase_sensor">'.$idiomas->_("Recalcular").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Recalcular valores de clase");
    $error = rellena_contenido_ventana_recalculo_valores_clase_sensor($clase_sensor, $id_sensor, $contenido);
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
	// Funciones para mostrar el contenido de la ventana de recálculo de valores de clase de un sensor
	//


	// Función que rellena el contenido de la ventana de recálculo de valores de clase de un sensor
	function rellena_contenido_ventana_recalculo_valores_clase_sensor($clase_sensor, $id_sensor, &$contenido)
	{
        $idiomas = new Idiomas();

        // Lista de clases de sensor con valores de clase
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_sensor_recalculo_valores_clase_sensor' class='select-administracion'>";
        $contenido .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, NULL);
        $clases_sensor_usuario_actual = dame_clases_sensor_usuario_actual(false);
        foreach ($clases_sensor_usuario_actual as $clase_sensor_usuario_actual)
        {
            $caracteristicas_clase_sensor_usuario_actual = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor_usuario_actual);
            if ($caracteristicas_clase_sensor_usuario_actual["valores_clase"] == true)
            {
                $contenido .= dame_opcion_valor_lista_simple(
                    NodoSensor::dame_descripcion_clase_sensor($clase_sensor_usuario_actual),
                    $clase_sensor_usuario_actual,
                    $clase_sensor);
            }
        }
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_recalculo_valores_clase_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_recalculo_valores_clase_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                </div>
            </div>";

        return ("OK");
	}
?>
