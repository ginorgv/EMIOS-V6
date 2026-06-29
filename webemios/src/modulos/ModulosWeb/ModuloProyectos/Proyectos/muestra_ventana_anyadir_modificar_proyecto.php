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
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/Proyecto.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_PROYECTO, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_proyecto = $_POST["id_proyecto"];
    if ($id_proyecto === NULL)
    {
        $id_proyecto = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Flag que indica si se puede realizar la operación
    $operacion_permitida = True;

    // Añadir o modificar proyecto
    if ($operacion_permitida == true)
    {
        $anyadir_proyecto = (($id_proyecto == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
        if ($anyadir_proyecto == true)
        {
            $titulo .= $idiomas->_("Añadir");
        }
        else
        {
            $titulo .= $idiomas->_("Modificar");
        }
        $pie .= '<button class="btn btn-success boton_proyectos_anyadir_modificar_proyecto">'.$titulo.'</button>';
        $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

        // Título
        $titulo .= " ".$idiomas->_("proyecto");
        if (($anyadir_proyecto == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
        {
            $titulo .= " (".$idiomas->_("duplicar").")";
        }

        // Se recupera el contenido de la ventana
        $error = rellena_contenido_ventana_anyadir_modificar_proyecto($anyadir_proyecto, $id_proyecto, $contenido);
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
            <div id="parametros_ventana_anyadir_modificar_proyecto"
                anyadir_proyecto="'.$anyadir_proyecto.'"
                id_proyecto="'.$id_proyecto.'"
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
	// Función para mostrar el contenido de la ventana de anyadir/modificar proyecto
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar proyecto
	function rellena_contenido_ventana_anyadir_modificar_proyecto($anyadir_proyecto, $id_proyecto, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar el proyecto (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_proyecto != ID_NINGUNO)
		{
			$fila_proyecto = dame_fila_proyecto($id_proyecto);

			$nombre = $fila_proyecto["nombre"];
            $descripcion = $fila_proyecto["descripcion"];
            $clase_sensor = $fila_proyecto["clase_sensor"];
            $id_sensor = $fila_proyecto["sensor"];
            $campo = $fila_proyecto["campo"];
            $intervalo_valores = $fila_proyecto["intervalo_valores"];
            $id_linea_base = $fila_proyecto["linea_base"];
            $cadena_fecha_inicio_base_local_local = convierte_formato_fecha($fila_proyecto["fecha_inicio"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_base_local_local = convierte_formato_fecha($fila_proyecto["fecha_fin"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $tipo_objetivo = $fila_proyecto["tipo_objetivo"];
            $tipo_valor_objetivo = $fila_proyecto["tipo_valor_objetivo"];
            $valor_objetivo = $fila_proyecto["valor_objetivo"];
		}
        else
        {
            $nombre = "";
            $descripcion = "";
            $clase_sensor = CLASE_NINGUNA;
            $id_sensor = ID_NINGUNO;
            $campo = CAMPO_NINGUNO;
            $intervalo_valores = INTERVALO_VALORES_NINGUNO;
            $id_linea_base = ID_NINGUNO;
            $fecha_hora_actual_local = dame_fecha_hora_actual_local();
            $cadena_fecha_inicio_base_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_base_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_fecha_local"]);
            $tipo_objetivo = TIPO_NINGUNO;
            $tipo_valor_objetivo = TIPO_NINGUNO;
            $valor_objetivo = "";
        }

        // Contenido de la ventana
        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_proyecto'
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
                    <textarea id='descripcion_proyecto'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_proyecto' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra($clase_sensor, $intervalo_valores, $campo);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
					<select id='intervalo_valores_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_proyecto(OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_NINGUNO, $intervalo_valores);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Línea base").": "."</span><br/>
					<select id='id_linea_base_proyecto' class='chosen-select-administracion'>";
        $contenido .= dame_lista_lineas_base_intervalo_valores($intervalo_valores, $id_linea_base);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_proyecto' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_base_local_local."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de fin").": "."</span><br/>
                    <input size='10' type='text' id='fecha_fin_proyecto' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_fin_base_local_local."'>
                    </span>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de objetivo").": "."</span><br/>
					<select id='tipo_objetivo_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_tipos_objetivo_proyecto($tipo_objetivo);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de valor de objetivo").": "."</span><br/>
					<select id='tipo_valor_objetivo_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_tipos_valor_objetivo_proyecto($tipo_valor_objetivo);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_valor_objetivo_proyecto'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Objetivo').": "."</span><br/>
					<input type='text' id='valor_objetivo_proyecto'
						class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$valor_objetivo."'>
				</div>
			</div>";

        return ("OK");
	}
?>
