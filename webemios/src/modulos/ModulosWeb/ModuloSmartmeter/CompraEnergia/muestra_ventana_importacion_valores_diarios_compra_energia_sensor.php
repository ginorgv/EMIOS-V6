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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_IMPORTACION_VALORES_DIARIOS_COMPRA_ENERGIA_SENSOR, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_smartmeter_importar_valores_diarios_compra_energia_sensor">'.$idiomas->_("Importar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= $idiomas->_("Importar valores diarios de compra de energía");

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_importacion_valores_diarios_compra_energia_sensor($contenido);
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
	// Funciones para mostrar el contenido de la ventana de importación de valores diarios de compra de energía de un sensor
	//


	// Función que rellena el contenido de la ventana de importación de valores diarios de compra de energía de un sensor
	function rellena_contenido_ventana_importacion_valores_diarios_compra_energia_sensor(&$contenido)
	{
        $idiomas = new Idiomas();

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_importacion_valores_diarios_compra_energia_sensor' class='chosen-select-administracion' hidden>";
        $contenido .= dame_lista_sensores(CLASE_SENSOR_COMPRA_ENERGIA, array(), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de valores diarios").": "."</span><br/>
                    <input type='file' id='fichero_importacion_valores_diarios_compra_energia_sensor_file'>
                    <input type='text' id='fichero_importacion_valores_diarios_compra_energia_sensor_text'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_importacion_valores_diarios_compra_energia_sensor_seleccionar_fichero' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>
				</div>
			</div>";

        return ("OK");
	}
?>
