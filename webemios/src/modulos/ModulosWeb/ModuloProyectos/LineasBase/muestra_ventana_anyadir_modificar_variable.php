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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_VARIABLE_LINEA_BASE, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_linea_base = $_POST["id_linea_base"];
    $id_variable = $_POST["id_variable"];
    if ($id_variable === NULL)
    {
        $id_variable = ID_NINGUNO;
    }

    // Añadir o modificar variable
    $anyadir_variable = ($id_variable == ID_NINGUNO);
    if ($anyadir_variable == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_proyectos_anyadir_modificar_variable_linea_base">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("variable");
    $error = rellena_contenido_ventana_anyadir_modificar_variable_linea_base(
        $anyadir_variable,
        $id_variable,
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

    // Se añaden los parámetros (no visibles) en un 'div' oculto
    $contenido .= '
        <div id="parametros_ventana_anyadir_modificar_variable_linea_base"
            anyadir_variable="'.$anyadir_variable.'"
            id_linea_base="'.$id_linea_base.'"
            id_variable="'.$id_variable.'"
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
	// Función para mostrar el contenido de la ventana de anyadir/modificar variable
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar variable de línea base
	function rellena_contenido_ventana_anyadir_modificar_variable_linea_base(
        $anyadir_variable,
        $id_variable,
        &$contenido)
	{
        $idiomas = new Idiomas();

		// Si hay que modificar la variable, se recupera la información actual de la base de datos
		if ($anyadir_variable == false)
		{
			$fila_variable_linea_base = dame_fila_variable_linea_base($id_variable);

            $nombre = $fila_variable_linea_base["nombre"];
            $clase_sensor = $fila_variable_linea_base["clase_sensor"];
            $id_sensor = $fila_variable_linea_base["sensor"];
            $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $fila_variable_linea_base["campo_parametros_extra"]);
            $campo = $campo_parametros_extra[0];
            $parametros_extra_campo = $campo_parametros_extra[1];
        }
        else
        {
            $clase_sensor = CLASE_NINGUNA;
        }

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_variable_linea_base'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_variable_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_variable_linea_base' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_variable_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_campos_clase_sensor_parametros_extra($clase_sensor, $campo);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_variable_linea_base' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_variable_linea_base' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_variable_linea_base'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        return ("OK");
    }
?>
