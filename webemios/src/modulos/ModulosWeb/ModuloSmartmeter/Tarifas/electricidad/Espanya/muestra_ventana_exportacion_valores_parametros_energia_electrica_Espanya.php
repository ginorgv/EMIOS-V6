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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_EXPORTACION_VALORES_PARAMETROS_ENERGIA_ELECTRICA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_smartmeter_exportar_valores_parametros_energia_electrica_Espanya">'.$idiomas->_("Exportar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Exportar valores de parámetros de energía eléctrica");
    $error = rellena_contenido_ventana_exportacion_valores_parametros_energia_electrica($contenido);
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
	// Funciones para mostrar el contenido de la ventana de exportación de valores de parámetros de energía eléctrica
	//


	// Función que rellena el contenido de la ventana de exportación de valores de parámetros de energía eléctrica
	function rellena_contenido_ventana_exportacion_valores_parametros_energia_electrica(&$contenido)
	{
        $idiomas = new Idiomas();

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_exportacion_valores_parametros_energia_electrica' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_inicio_exportacion_valores_parametros_energia_electrica' class='selector-hora timepicker'
                            readonly='readonly' value='00:00'>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de fin").": "."</span><br/>
                    <input size='10' type='text' id='fecha_fin_exportacion_valores_parametros_energia_electrica' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_fin_exportacion_valores_parametros_energia_electrica' class='selector-hora timepicker'
                            readonly='readonly' value='23:59'>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Punto decimal").": "."</span><br/>
					<select id='id_punto_decimal_exportacion_valores_parametros_energia_electrica' class='select-administracion'>";
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
