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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_EXPORTACION_COSTES_CONCEPTOS_CONSUMO_SENSOR_ELECTRICIDAD_ESPANYA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_sensor = $_POST["id_sensor"];
    if ($id_sensor === NULL)
    {
        $id_sensor = ID_NINGUNO;
    }

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_smartmeter_exportar_costes_conceptos_consumo_sensor_electricidad_Espanya">'.$idiomas->_("Exportar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Exportar costes de conceptos de consumo de un sensor");
    $error = rellena_contenido_ventana_exportacion_costes_conceptos_consumo_sensor($id_sensor, $contenido);
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
	// Funciones para mostrar el contenido de la ventana de exportación de costes de conceptos de consumo
	//


	// Función que rellena el contenido de la ventana de exportación de costes de conceptos de consumo del sensor
	function rellena_contenido_ventana_exportacion_costes_conceptos_consumo_sensor($id_sensor, &$contenido)
	{
        $idiomas = new Idiomas();

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_exportacion_costes_conceptos_consumo_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores(CLASE_SENSOR_ENERGIA_ACTIVA, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_exportacion_costes_conceptos_consumo_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de fin").": "."</span><br/>
                    <input size='10' type='text' id='fecha_fin_exportacion_costes_conceptos_consumo_sensor' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Punto decimal").": "."</span><br/>
					<select id='id_punto_decimal_exportacion_costes_conceptos_consumo_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(ID_PUNTO_DECIMAL_COMA, dame_descripcion_id_punto_decimal(ID_PUNTO_DECIMAL_COMA)),
                array(ID_PUNTO_DECIMAL_PUNTO, dame_descripcion_id_punto_decimal(ID_PUNTO_DECIMAL_PUNTO))),
            array(ID_PUNTO_DECIMAL_PUNTO));
        $contenido .= "
					</select>
				</div>
			</div>";

        return ("OK");
	}
?>
