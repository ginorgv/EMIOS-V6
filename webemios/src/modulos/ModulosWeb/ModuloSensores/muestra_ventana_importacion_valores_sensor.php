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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/ImportacionesValoresSensores/ImportacionValoresSensorPendiente.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_procesado.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_IMPORTACION_VALORES_SENSOR, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_historico_importacion_valores_sensor = $_POST["id_historico_importacion_valores_sensor"];
    if ($id_historico_importacion_valores_sensor === NULL)
    {
        $id_historico_importacion_valores_sensor = ID_NINGUNO;
    }
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
    $pie .= '<button class="btn btn-success boton_sensores_importar_valores_sensor">'.$idiomas->_("Importar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= $idiomas->_("Importar valores");
    if ($id_historico_importacion_valores_sensor != ID_NINGUNO)
    {
        $titulo .= " (".$idiomas->_("repetir").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_importacion_valores_sensor(
        $id_historico_importacion_valores_sensor,
        $clase_sensor,
        $id_sensor,
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
        <div id="parametros_ventana_importacion_valores_sensor"
            id_historico_importacion_valores_sensor="'.$id_historico_importacion_valores_sensor.'"
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
	// Funciones para mostrar el contenido de la ventana de importación de valores de un sensor
	//


	// Función que rellena el contenido de la ventana de importación de valores de un sensor
	function rellena_contenido_ventana_importacion_valores_sensor(
        $id_historico_importacion,
        $clase_sensor,
        $id_sensor,
        &$contenido)
	{
        $idiomas = new Idiomas();

        // Si hay que modificar la importación (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_historico_importacion != ID_NINGUNO)
		{
			$fila_historico_importacion = dame_fila_historico_importacion_valores_sensor($id_historico_importacion);

            // Clase e identificador de sensor
            $id_red = $fila_historico_importacion["red"];
			$nombre_sensor = $fila_historico_importacion["sensor"];
            $clase_sensor = $fila_historico_importacion["clase_sensor"];
            $fila_sensor = dame_fila_sensor_nombre($id_red, $nombre_sensor);
            if ($fila_sensor !== NULL)
            {
                $id_sensor = $fila_sensor["id"];
            }
            else
            {
                $id_sensor = ID_NINGUNO;
            }

            // Tipo de valores y aplicar calibración
            $tipo_valores = $fila_historico_importacion["tipo_valores"];
            $aplicar_calibracion = $fila_historico_importacion["aplicar_calibracion"];

            // Parámetros de opciones de fichero CSV
            $parametros_opciones_fichero_csv = ImportacionValoresSensorPendiente::dame_nombres_valores_parametros_opciones_fichero_csv_importacion_valores_sensor(
                $tipo_valores,
                $fila_historico_importacion["opciones_fichero_csv"]);
            $formato_fichero = $parametros_opciones_fichero_csv["formato_fichero"];
            $caracter_separador = $parametros_opciones_fichero_csv["caracter_separador"];
            $id_punto_decimal = $parametros_opciones_fichero_csv["id_punto_decimal"];
            $numero_lineas_cabeceras = $parametros_opciones_fichero_csv["numero_lineas_cabeceras"];
            $numero_columna_fecha = $parametros_opciones_fichero_csv["numero_columna_fecha"];
            $formato_fecha = $parametros_opciones_fichero_csv["formato_fecha"];
            $hora_columna_independiente = $parametros_opciones_fichero_csv["hora_columna_independiente"];
            $numero_columna_hora = $parametros_opciones_fichero_csv["numero_columna_hora"];
            $formato_hora = $parametros_opciones_fichero_csv["formato_hora"];
            $zona_horaria = $parametros_opciones_fichero_csv["zona_horaria"];
            $numero_columna_horario_verano = $parametros_opciones_fichero_csv["numero_columna_horario_verano"];
            $numero_valores = $parametros_opciones_fichero_csv["numero_valores"];
            $horas_incrementos = $parametros_opciones_fichero_csv["horas_incrementos"];
            $tipo_incrementos = $parametros_opciones_fichero_csv["tipo_incrementos"];

            // Tipo de horas de incrementos
            if ($horas_incrementos == 0)
            {
                $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE;
            }
            else
            {
                $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO;
            }

            // Parámetros de opciones de valores de fichero CSV
            $parametros_opciones_valores_fichero_csv = ImportacionValoresSensorPendiente::dame_nombres_valores_parametros_opciones_valores_fichero_csv_importacion_valores_sensor($fila_historico_importacion["opciones_valores_fichero_csv"]);
            $columnas_valores = $parametros_opciones_valores_fichero_csv["columnas_valores"];
		}
        else
        {
            $formato_fichero = FORMATO_FICHERO_VALORES_PERSONALIZADO;
            $id_punto_decimal = ID_PUNTO_DECIMAL_COMA;
            $hora_columna_independiente = VALOR_NO;
            $zona_horaria = $_SESSION["zona_horaria"];
            $tipo_valores = TIPO_NINGUNO;
            $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO;
            $horas_incrementos = 0;
            $tipo_incrementos = TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL;
            $aplicar_calibracion;
        }

        // Contenido
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
                    <select id='clase_sensor_importacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_importacion_valores_sensor' class='chosen-select-administracion' hidden>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de valores").": "."</span><br/>
                    <input type='file' id='fichero_importacion_valores_sensor_file'>
                    <input type='text' id='fichero_importacion_valores_sensor_text'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_importacion_valores_sensor_seleccionar_fichero' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>
				</div>
			</div>";

        // Formato de fichero de valores; para configurar automáticamente los siguientes parámetros:
        // - Carácter separador
        // - Número de líneas de cabecera
        // - Columna de fecha
        // - Formato de fecha
        // - Hora en columna independiente (y formato de hora)

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Formato de fichero de valores").": "."</span><br/>
                    <select id='formato_fichero_valores_importacion_valores_sensor' class='select-administracion' hidden>";
        $contenido .= dame_lista_formatos_ficheros_valores($formato_fichero);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Carácter separador").": "."</span><br/>
                    <input type='text' id='caracter_separador_importacion_valores_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$caracter_separador."'>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Punto decimal").": "."</span><br/>
					<select id='id_punto_decimal_importacion_valores_sensor' class='select-administracion'>";
        # Se obtiene el punto decimal configurado en la red
        # de la sesion, si no tuviera configurado (imposible)
        # por defecto coma para evitar errores con Excel
        $punto_decimal = $_SESSION["punto_decimal"];

        if ($punto_decimal == ",")
        {
            $punto_decimal = ID_PUNTO_DECIMAL_COMA;
        }
        elseif ($punto_decimal == ".")
        {
            $punto_decimal = ID_PUNTO_DECIMAL_PUNTO;
        }
        else
        {
            $punto_decimal = ID_PUNTO_DECIMAL_COMA;
        }

        $contenido .= dame_lista_valores(
            array(
                array(ID_PUNTO_DECIMAL_COMA, dame_descripcion_id_punto_decimal(ID_PUNTO_DECIMAL_COMA)),
                array(ID_PUNTO_DECIMAL_PUNTO, dame_descripcion_id_punto_decimal(ID_PUNTO_DECIMAL_PUNTO))),
            array($punto_decimal));
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de líneas de cabecera").": "."</span><br/>
                    <input type='text' id='numero_lineas_cabeceras_importacion_valores_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_lineas_cabeceras."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Columna de fecha").": "."</span><br/>
                    <input type='text' id='columna_fecha_importacion_valores_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_columna_fecha."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Formato de fecha").": "."</span><br/>
                    <input type='text' id='formato_fecha_importacion_valores_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$formato_fecha."'>
                    <span id='boton_sensores_ayuda_formato_fecha_importacion_valores_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Hora en columna independiente").": "."</span><br/>
                    <select id='hora_columna_independiente_importacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($hora_columna_independiente);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_columna_hora_importacion_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Columna de hora").": "."</span><br/>
                    <input type='text' id='columna_hora_importacion_valores_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_columna_hora."'>
                </div>
            </div>

            <div class='row-fluid' id='control_formato_hora_importacion_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Formato de hora").": "."</span><br/>
                    <input type='text' id='formato_hora_importacion_valores_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$formato_hora."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Zona horaria").": "."</span><br/>
                    <select id='zona_horaria_importacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_zonas_horarias($zona_horaria);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_columna_horario_verano_importacion_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Columna de horario de verano").": "."</span><br/>
                    <input type='text' id='columna_horario_verano_importacion_valores_sensor'
                        class='TLNT_input_numerical input-administracion' value='".$numero_columna_horario_verano."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de valores").": "."</span><br/>
                    <input type='text' id='numero_valores_importacion_valores_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_valores."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Columnas de valores").": "."</span><br/>
                    <input type='text' id='columnas_valores_importacion_valores_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$columnas_valores."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de valores").": "."</span><br/>
                    <select id='tipo_valores_sensor_importacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_tipos_valores_sensor($tipo_valores);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_horas_incrementos_importacion_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Horas de incrementos").": "."</span><br/>
                    <select id='tipo_horas_incrementos_importacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_tipos_horas_incrementos_valores_sensor($tipo_horas_incrementos);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_horas_incrementos_importacion_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de horas de incrementos").": "."</span><br/>
                    <input type='text' id='horas_incrementos_importacion_valores_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$horas_incrementos."'>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_incrementos_importacion_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de incrementos").": "."</span><br/>
                    <select id='tipo_incrementos_importacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_tipos_incrementos_valores_sensor($tipo_incrementos);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Aplicar calibración").": "."</span><br/>
                    <select id='aplicar_calibracion_importacion_valores_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($aplicar_calibracion);
        $contenido .= "
                    </select>
                </div>
            </div>";

        return ("OK");
	}


    function convierte_formato_fecha_hora_python_a_formato_fecha_hora($formato_fecha_hora_python)
    {
        $formato_fecha_hora = $formato_fecha_hora_python;
        $formato_fecha_hora = str_replace("%d", "d", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%m", "m", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%y", "y", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%Y", "Y", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%H", "H", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%M", "M", $formato_fecha_hora);
        $formato_fecha_hora = str_replace("%S", "S", $formato_fecha_hora);
        return ($formato_fecha_hora);
    }
?>
