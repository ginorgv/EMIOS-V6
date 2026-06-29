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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/util_ratios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_RATIO, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_ratio = $_POST["id_ratio"];
    if ($id_ratio === NULL)
    {
        $id_ratio = ID_NINGUNO;
    }

    // Flag que indica si se puede realizar la operación
    $operacion_permitida = True;

    // Añadir o modificar ratio
    if ($operacion_permitida == true)
    {
        $anyadir_ratio = ($id_ratio == ID_NINGUNO);
        if ($anyadir_ratio == true)
        {
            $titulo .= $idiomas->_("Añadir");
        }
        else
        {
            $titulo .= $idiomas->_("Modificar");
        }
        $pie .= '<button class="btn btn-success boton_localizaciones_anyadir_modificar_ratio">'.$titulo.'</button>';
        $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

        // Título
        $titulo .= " ".$idiomas->_("ratio");

        // Se recupera el contenido de la ventana
        $error = rellena_contenido_ventana_anyadir_modificar_ratio($anyadir_ratio, $id_ratio, $contenido);
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
            <div id="parametros_ventana_anyadir_modificar_ratio"
                anyadir_ratio="'.$anyadir_ratio.'"
                id_ratio="'.$id_ratio.'"
                hidden>
            </div>';
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar ratio
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar ratio
	function rellena_contenido_ventana_anyadir_modificar_ratio($anyadir_ratio, $id_ratio, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar el ratio (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_ratio != ID_NINGUNO)
		{
			$fila_ratio = dame_fila_ratio($id_ratio);

			$nombre = $fila_ratio["nombre"];
            $descripcion = $fila_ratio["descripcion"];
            $sustituir_unidad_medida_sensor = $fila_ratio["sustituir_unidad_medida_sensor"];
            $unidad_medida = $fila_ratio["unidad_medida"];
            $tipo = $fila_ratio["tipo"];
            $clase_sensor = $fila_ratio["clase_sensor"];
            $campo_sensor = $fila_ratio["campo_sensor"];
            $valor_defecto = $fila_ratio["valor_defecto"];
            $id_sensor_defecto = $fila_ratio["sensor_defecto"];
		}
        else
        {
            $nombre = "";
            $descripcion = "";
            $sustituir_unidad_medida_sensor = VALOR_NO;
            $unidad_medida = "";
            $tipo = TIPO_RATIO_FIJO;
            $clase_sensor = CLASE_NINGUNA;
            $campo_sensor = "";
            $valor_defecto = "";
            $id_sensor_defecto = ID_NINGUNO;
        }

        // Contenido de ventana de ratio
        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_ratio'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

        // Descripción
        $numero_caracteres_actuales = dame_numero_caracteres($descripcion);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_DESCRIPCION;
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Descripción').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='descripcion_ratio'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

		$contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sustituir unidad de medida del sensor").": "."</span><br/>
					<select id='sustituir_unidad_medida_sensor_ratio' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($sustituir_unidad_medida_sensor);
		$contenido .= "
					</select>
				</div>
			</div>

			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Unidad de medida').": "."</span><br/>
					<input type='text' id='unidad_medida_ratio'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($unidad_medida, ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
					<select id='tipo_ratio' class='select-administracion'>";
        $contenido .= dame_lista_tipos_ratio($tipo);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_clase_sensor_ratio'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_ratio' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor_ratio_variable($clase_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_campo_sensor_ratio'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo de sensor").": "."</span><br/>
                    <select id='campo_sensor_ratio' class='select-administracion'>";
        $contenido .= dame_lista_campos_sensor_ratio_variable($clase_sensor, $campo_sensor);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_valor_defecto_ratio'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Valor por defecto').": "."</span><br/>
					<input type='text' id='valor_defecto_ratio'
						class='TLNT_input_float input-administracion' value='".$valor_defecto."'>
				</div>
			</div>

            <div class='row-fluid' id='control_id_sensor_defecto_ratio'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor por defecto").": "."</span><br/>
                    <select id='id_sensor_defecto_ratio' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor_defecto), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
                </div>
            </div>";

        return ("OK");
	}
?>
