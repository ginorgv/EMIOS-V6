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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_BORRADO_ACCIONES_ENVIADAS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $clase_actuador = $_POST["clase_actuador"];
    if ($clase_actuador == "")
    {
        $clase_actuador = CLASE_NINGUNA;
    }
    $destino = $_POST["destino"];
    $id_destino = $_POST["id_destino"];

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_actuadores_borrar_acciones_enviadas">'.$idiomas->_("Borrar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Borrar acciones enviadas");
    $error = rellena_contenido_ventana_borrado_acciones_enviadas($clase_actuador, $destino, $id_destino, $contenido);
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
	// Funciones para mostrar el contenido de la ventana de borrado de acciones enviadas
	//


	// Función que rellena el contenido de la ventana de borrado de acciones enviadas
	function rellena_contenido_ventana_borrado_acciones_enviadas($clase_actuador, $destino, $id_destino, &$contenido)
	{
        $idiomas = new Idiomas();

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_actuador_borrado_acciones_enviadas' class='select-administracion'>";
        $contenido .= dame_lista_clases_actuador($clase_actuador, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de destino").": "."</span><br/>
                    <select id='destino_accion_borrado_acciones_enviadas' class='select-administracion'>";
        $contenido .= dame_lista_destinos_accion($destino);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Destino").": "."</span><br/>
                    <select id='id_destino_accion_borrado_acciones_enviadas' class='chosen-select-administracion'>";
        $contenido .= dame_lista_ids_destinos_accion($clase_actuador, $destino, $id_destino);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_borrado_acciones_enviadas' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_inicio_borrado_acciones_enviadas' class='selector-hora timepicker'
                            readonly='readonly' value='00:00'>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de fin").": "."</span><br/>
                    <input size='10' type='text' id='fecha_fin_borrado_acciones_enviadas' class='selector-fecha datepicker'
                        readonly='readonly' value='".date($_SESSION["formato_fecha_local"])."'>
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_fin_borrado_acciones_enviadas' class='selector-hora timepicker'
                            readonly='readonly' value='23:59'>
                    </span>
                </div>
            </div>";

        return ("OK");
	}
?>
