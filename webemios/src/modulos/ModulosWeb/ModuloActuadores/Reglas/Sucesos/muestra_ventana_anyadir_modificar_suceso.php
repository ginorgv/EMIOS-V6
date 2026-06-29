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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/SucesoRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/util_sucesos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_SUCESO_REGLA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_regla = $_POST["id_regla"];
    $id_suceso = $_POST["id_suceso"];
    if ($id_suceso === NULL)
    {
        $id_suceso = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar suceso
    $anyadir_suceso = (($id_suceso == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_suceso == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_actuadores_anyadir_modificar_suceso_regla">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("suceso");
    if (($anyadir_suceso == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se muestra el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_suceso(
        $anyadir_suceso,
        $id_regla,
        $id_suceso,
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar suceso
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar suceso
	function rellena_contenido_ventana_anyadir_modificar_suceso(
        $anyadir_suceso,
        $id_regla,
        $id_suceso,
        &$contenido)
	{
        $idiomas = new Idiomas();

		// Si hay que modificar el suceso (o es un duplicado), se recupera la información actual de la base de datos
        $numero_horas_activacion = 1;
        $periodo_tiempo_activacion = PERIODO_TIEMPO_HORA;
        $numero_repeticiones_activacion = 1;
		if ($id_suceso != ID_NINGUNO)
		{
            $fila_suceso_regla = dame_fila_suceso_regla($id_suceso);

            $nombre = $fila_suceso_regla['nombre'];
            $causa = $fila_suceso_regla['causa'];
            $id_causa = $fila_suceso_regla['id_causa'];
            $origen = $fila_suceso_regla['origen'];
            $id_origen = $fila_suceso_regla['id_origen'];
            $modo_activacion = $fila_suceso_regla['modo_activacion'];
            $parametros_modo_activacion = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_suceso_regla['parametros_modo_activacion']);
            switch ($modo_activacion)
            {
                case MODO_ACTIVACION_SUCESO_TIEMPO_MINIMO:
                {
                    $numero_horas_activacion = $parametros_modo_activacion[0];
                    break;
                }
                case MODO_ACTIVACION_SUCESO_REPETICIONES_MINIMAS_PERIODO_TIEMPO:
                {
                    $periodo_tiempo_activacion = $parametros_modo_activacion[0];
                    $numero_repeticiones_activacion = $parametros_modo_activacion[1];
                    break;
                }
            }
            $numero_activaciones = $fila_suceso_regla['numero_activaciones'];
		}
        else
        {
            $causa = ID_NINGUNO;
            $id_causa = ID_NINGUNO;
            $origen = ID_NINGUNO;
            $id_origen = ID_NINGUNO;
            $numero_activaciones = 1;
        }

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_suceso_regla'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de causa").": "."</span><br/>
                    <select id='causa_suceso_regla' class='select-administracion'>";
        $contenido .= dame_lista_causas_suceso_regla($causa);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Causa").": "."</span><br/>
                    <select id='id_causa_suceso_regla' class='chosen-select-administracion'>";
        $contenido .= dame_lista_ids_causas_suceso_regla($causa, $id_causa, $id_regla);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de origen").": "."</span><br/>
                    <select id='origen_suceso_regla' class='select-administracion'>";
        $contenido .= dame_lista_origenes_suceso_regla($causa, $id_causa, $origen);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Origen").": "."</span><br/>
                    <select id='id_origen_suceso_regla' class='chosen-select-administracion'>";
        $contenido .= dame_lista_ids_origenes_suceso_regla($causa, $id_causa, $origen, $id_origen);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modo de activación").": "."</span><br/>
                    <select id='modo_activacion_suceso_regla' class='select-administracion'>";
        $contenido .= dame_lista_modos_activacion_suceso_regla($modo_activacion);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_numero_horas_activacion_suceso_regla'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Horas de activación").": "."</span><br/>
                    <input type='text' id='numero_horas_activacion_suceso_regla'
						class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$numero_horas_activacion."'>
                </div>
            </div>

            <div class='row-fluid' id='control_periodo_tiempo_activacion_suceso_regla'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo de activación").": "."</span><br/>
                    <select id='periodo_tiempo_activacion_suceso_regla' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_activacion_suceso_regla($periodo_tiempo_activacion);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_numero_repeticiones_activacion_suceso_regla'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Repeticiones de activación").": "."</span><br/>
                    <input type='text' id='numero_repeticiones_activacion_suceso_regla'
						class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".$numero_repeticiones_activacion."'>
                </div>
            </div>";

        // Se deshabilita el numero de activaciones si el origen no es un grupo de sensores
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de activaciones").": "."</span><br/>
                    <select id='numero_activaciones_suceso_regla' class='select-administracion'";
        if ($origen != ORIGEN_SUCESO_GRUPO_SENSORES)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_numero_activaciones_suceso_regla($origen, $id_origen, $numero_activaciones);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Se añaden los parámetros (no visibles) en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_suceso_regla"
                anyadir_suceso="'.$anyadir_suceso.'"
                id_regla="'.$id_regla.'"
                id_suceso="'.$id_suceso.'"
                causa="'.$causa.'"
                id_causa="'.$id_causa.'"
                hidden>
            </div>';

        return ("OK");
    }
?>