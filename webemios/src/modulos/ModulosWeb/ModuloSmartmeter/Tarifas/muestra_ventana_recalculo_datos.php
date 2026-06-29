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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_RECALCULO_DATOS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $medicion = $_POST["medicion"];

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_smartmeter_recalcular_datos">'.$idiomas->_("Recalcular").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Recalcular datos");
    $error = rellena_contenido_ventana_recalculo_datos($medicion, $contenido);
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
	// Funciones para mostrar el contenido de la ventana de recálculo de datos
	//


	// Función que rellena el contenido de la ventana de recálculo de datos
	function rellena_contenido_ventana_recalculo_datos($medicion, &$contenido)
	{
        $idiomas = new Idiomas();

        // Se muestran las pestañas
        $contenido = "
            <div id='tabs-recalculo-datos-tarifas' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-recalcular-datos-tarifas' id='titulo-tab-recalcular-datos-tarifas'>".$idiomas->_("Tarifas")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-recalcular-datos-grupos-tarifas' id='titulo-tab-recalcular-datos-grupos-tarifas'>".$idiomas->_("Grupos de tarifas")."</a></li>
                </ul>
                <div id='tabs-content-administracion-elemento-plantillas-informes' class='tab-content'>";

        // Contenido de pestaña de tarifas
        $contenido .= "
                    <div class='tab-pane active' id='tab-recalcular-datos-tarifas'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifas").": "."</span><br/>
                    <div id='select_tarifas_recalculo_datos_no_visible' hidden></div>
					<select id='ids_tarifas_recalculo_datos'
                        name='ids_tarifas_recalculo_datos'
                        max_selected='".ID_NINGUNO."' multiple='multiple'
						class='select-administracion' hidden>";
        $contenido .= dame_lista_tarifas($medicion, array(), OPCIONES_EXTRA_LISTA_TARIFAS_SIN_NINGUNA);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de grupos de tarifas
        $contenido .= "
                    <div class='tab-pane' id='tab-recalcular-datos-grupos-tarifas'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupos de tarifas").": "."</span><br/>
                    <div id='select_grupos_tarifas_recalculo_datos_no_visible' hidden></div>
					<select id='ids_grupos_tarifas_recalculo_datos'
                        name='ids_grupos_tarifas_recalculo_datos'
                        max_selected='".ID_NINGUNO."' multiple='multiple'
						class='select-administracion' hidden>";
        $contenido .= dame_lista_grupos_tarifas($medicion, ID_NINGUNO, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_SIN_OPCIONES_EXTRA);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_recalculo_datos' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                </div>
            </div>";

        return ("OK");
	}
?>
