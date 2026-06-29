<?php
	include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_sensores_software.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_wibeee.php');


    // Constantes

    // Indices de parámetros de clase de sensor externo 'Ficheros CSV'
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_PREFIJO_FICHERO", 0);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_FORMATO_FICHERO", 1);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_CARACTER_SEPARADOR", 2);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_PUNTO_DECIMAL", 3);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_CABECERAS", 4);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_NUMERO_LINEAS_CABECERAS", 5);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_NUMERO_COLUMNA_FECHA", 6);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_FORMATO_FECHA", 7);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_NUMERO_COLUMNA_HORA", 8);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_FORMATO_HORA", 9);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_ZONA_HORARIA", 10);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_NUMERO_COLUMNA_HORARIO_VERANO", 11);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_NUMERO_VALORES", 12);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_TIPO_VALORES", 13);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS", 14);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_TIPO_INCREMENTOS", 15);

    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_VALORES_NUMERO_COLUMNA", 0);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_VALORES_NUMERO_BIT_INICIAL", 1);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_VALORES_NUMERO_BITS_VALOR", 2);

    // Indices de parámetros de clase de sensor externo 'HTTP EMIOS'
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_EMIOS_OPCIONES_GENERALES_TIEMPO_MUESTREO", 0);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_EMIOS_OPCIONES_GENERALES_TIPO_VALORES", 1);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_EMIOS_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS", 2);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_EMIOS_OPCIONES_GENERALES_TIPO_INCREMENTOS", 3);

    // Indices de parámetros de clase de sensor externo 'HTTP XML PowerStudio'
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_GENERALES_TIEMPO_MUESTREO", 0);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_GENERALES_TIPO_VALORES", 1);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS", 2);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_GENERALES_TIPO_INCREMENTOS", 3);

	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_VALORES_DIRECCION_IP", 0);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_VALORES_PUERTO", 1);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_VALORES_NOMBRE_DISPOSITIVO", 2);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_VALORES_NOMBRE_VARIABLE", 3);

    // Indices de parámetros de clase de sensor externo 'Modbus IP'
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_ENCAPSULADO", 0);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_PROTOCOLO", 1);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_DIRECCION_IP", 2);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_PUERTO", 3);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_TIEMPO_MUESTREO", 4);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_TIPO_VALORES", 5);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS", 6);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_TIPO_INCREMENTOS", 7);

    // Indices de parámetros de clase de sensor externo 'Wibeee'
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_WIBEEE_OPCIONES_GENERALES_DIRECCION_MAC", 0);

	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_WIBEEE_OPCIONES_VALORES_TIPO_DATO", 0);

    // Indices de parámetros de clase de sensor externo 'API' generales
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_OPCIONES_GENERALES_TIEMPO_MUESTREO", 0);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_OPCIONES_GENERALES_TIPO_VALORES", 1);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS", 2);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_OPCIONES_GENERALES_TIPO_INCREMENTOS", 3);

    //Indices de parámetros de clase específicos de cada 'API'
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_OPCIONES_VALORES_API_SELECCIONADA",0);

    //Axontime
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_AXONTIME_OPCIONES_VALORES_CUPS_ID", 1);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_AXONTIME_OPCIONES_VALORES_TIPO_CURVA", 2);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_AXONTIME_OPCIONES_VALORES_VALOR_LECTURA", 3);

    //SGCLIMA
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_SGCLIMA_OPCIONES_VALORES_USUARIO", 1);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_SGCLIMA_OPCIONES_VALORES_PASSWORD", 2);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_SGCLIMA_OPCIONES_VALORES_ID_LOC", 3);
    define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_SGCLIMA_OPCIONES_VALORES_ID_PARAM", 4);

    //MTX_TUNNEL
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_MTX_OPCIONES_VALORES_SENSOR", 3);
	define("INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_MTX_OPCIONES_VALORES_PUERTO", 4);


    //
    // Funciones para devolver controles de tipo de sensor externo
    //


    // Devuelve los controles de configuración de sensor externo correspondientes a la clase de sensor externo especificada
    function dame_controles_clase_sensor_externo($clase_sensor_externo, $opciones_generales_externo, $opciones_valores_externo, $opciones_valores_datadis)
    {
        switch ($clase_sensor_externo)
        {
            case CLASE_SENSOR_EXTERNO_NINGUNA:
            {
                $controles = "";
                break;
            }
            case CLASE_SENSOR_EXTERNO_FICHEROS_CSV:
            {
                $controles = dame_controles_clase_sensor_externo_ficheros_csv($opciones_generales_externo, $opciones_valores_externo, $opciones_valores_datadis);
                break;
            }
            case CLASE_SENSOR_EXTERNO_HTTP_EMIOS:
            {
                $controles = dame_controles_clase_sensor_externo_http_emios($opciones_generales_externo, $opciones_valores_externo);
                break;
            }
            case CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO:
            {
                $controles = dame_controles_clase_sensor_externo_http_xml_powerstudio($opciones_generales_externo, $opciones_valores_externo);
                break;
            }
            case CLASE_SENSOR_EXTERNO_MODBUS_IP:
            {
                $controles = dame_controles_clase_sensor_externo_modbus_ip($opciones_generales_externo, $opciones_valores_externo);
                break;
            }
            case CLASE_SENSOR_EXTERNO_WIBEEE:
            {
                $controles = dame_controles_clase_sensor_externo_wibeee($opciones_generales_externo, $opciones_valores_externo);
                break;
            }
            case CLASE_SENSOR_EXTERNO_API:
            {
                $controles = dame_controles_clase_sensor_externo_api($opciones_generales_externo, $opciones_valores_externo);
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor externo desconocida: '".$clase_sensor_externo."'");
            }
        }
        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de sensor externo 'Ficheros CSV'
    function dame_controles_clase_sensor_externo_ficheros_csv($opciones_generales_externo, $opciones_valores_externo, $opciones_valores_datadis)
    {
        $idiomas = new Idiomas();

        // Si es una modificación del sensor externo
        if (($opciones_generales_externo !== NULL) && ($opciones_valores_externo !== NULL))
        {
            // Parámetros de opciones generales
            $parametros_opciones_generales_externo = dame_nombres_valores_parametros_opciones_generales_sensor_externo(
                CLASE_SENSOR_EXTERNO_FICHEROS_CSV,
                $opciones_generales_externo);
            $prefijo_fichero = $parametros_opciones_generales_externo["prefijo_fichero"];
            $formato_fichero = $parametros_opciones_generales_externo["formato_fichero"];
            $caracter_separador = $parametros_opciones_generales_externo["caracter_separador"];
            $id_punto_decimal = $parametros_opciones_generales_externo["id_punto_decimal"];
            $numero_lineas_cabeceras = $parametros_opciones_generales_externo["numero_lineas_cabeceras"];
            $numero_columna_fecha = $parametros_opciones_generales_externo["numero_columna_fecha"];
            $formato_fecha = $parametros_opciones_generales_externo["formato_fecha"];
            $hora_columna_independiente = $parametros_opciones_generales_externo["hora_columna_independiente"];
            $numero_columna_hora = $parametros_opciones_generales_externo["numero_columna_hora"];
            $formato_hora = $parametros_opciones_generales_externo["formato_hora"];
            $zona_horaria = $parametros_opciones_generales_externo["zona_horaria"];
            $numero_columna_horario_verano = $parametros_opciones_generales_externo["numero_columna_horario_verano"];
            $numero_valores = $parametros_opciones_generales_externo["numero_valores"];
            $tipo_valores = $parametros_opciones_generales_externo["tipo_valores"];
            $horas_incrementos = $parametros_opciones_generales_externo["horas_incrementos"];
            $tipo_incrementos = $parametros_opciones_generales_externo["tipo_incrementos"];

            // Tipo de horas de incrementos
            if ($horas_incrementos == 0)
            {
                $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE;
            }
            else
            {
                $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO;
            }

            // Parámetros de opciones de valores
            $parametros_opciones_valores_externo = dame_nombres_valores_parametros_opciones_valores_sensor_externo(
                CLASE_SENSOR_EXTERNO_FICHEROS_CSV,
                $opciones_valores_externo);
            $columnas_valores = $parametros_opciones_valores_externo["columnas_valores"];

            // Parámetros de sensor DATADIS
            if ($opciones_valores_datadis != "")
            {
                $parametros_datadis = explode(SEPARADOR_PARAMETROS_SIMPLES, $opciones_valores_datadis);
                $cups = $parametros_datadis[0];
                $distributor_code = $parametros_datadis[1];
                $measurement_type = $parametros_datadis[2];
                $point_type = $parametros_datadis[3];
                $authorized_nif = $parametros_datadis[4];
            }

        }
        else
        {
            // Valores por defecto de listas desplegables cuando se añade un sensor externo
            $formato_fichero = FORMATO_FICHERO_VALORES_PERSONALIZADO;
            $id_punto_decimal = ID_PUNTO_DECIMAL_PUNTO;
            $tipo_valores = TIPO_VALORES_SENSOR_PUNTUALES;
            $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO;
            $horas_incrementos = 0;
            $tipo_incrementos = TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL;
            $hora_columna_independiente = VALOR_NO;
            $zona_horaria = $_SESSION["zona_horaria"];
        }

        // Controles
        $controles .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Prefijo de fichero").": "."</span><br/>
                    <input type='text' id='prefijo_fichero_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($prefijo_fichero, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Formato de fichero").": "."</span><br/>
                    <select id='formato_fichero_valores_clase_externo_ficheros_csv_sensor' class='select-administracion' hidden>";
        $controles .= dame_lista_formatos_ficheros_valores($formato_fichero);
		$controles .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Carácter separador").": "."</span><br/>
                    <input type='text' id='caracter_separador_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($caracter_separador, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Punto decimal").": "."</span><br/>
					<select id='id_punto_decimal_clase_externo_ficheros_csv_sensor' class='select-administracion'>";
            $controles .= dame_lista_valores(
                array(
                    array(ID_PUNTO_DECIMAL_COMA, dame_descripcion_id_punto_decimal(ID_PUNTO_DECIMAL_COMA)),
                    array(ID_PUNTO_DECIMAL_PUNTO, dame_descripcion_id_punto_decimal(ID_PUNTO_DECIMAL_PUNTO))),
                array($id_punto_decimal));
            $controles .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de líneas de cabecera").": "."</span><br/>
                    <input type='text' id='numero_lineas_cabeceras_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_lineas_cabeceras."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Columna de fecha").": "."</span><br/>
                    <input type='text' id='columna_fecha_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_columna_fecha."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Formato de fecha").": "."</span><br/>
                    <input type='text' id='formato_fecha_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($formato_fecha, ENT_QUOTES)."'>
                    <span id='boton_sensores_ayuda_formato_fecha_sensor_externo_ficheros_csv_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Hora en columna independiente").": "."</span><br/>
                    <select id='hora_columna_independiente_clase_externo_ficheros_csv_sensor' class='select-administracion'>";
        $controles .= dame_lista_valores_si_no($hora_columna_independiente);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_columna_hora_clase_externo_ficheros_csv_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Columna de hora").": "."</span><br/>
                    <input type='text' id='columna_hora_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_columna_hora."'>
                </div>
            </div>

            <div class='row-fluid' id='control_formato_hora_clase_externo_ficheros_csv_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Formato de hora").": "."</span><br/>
                    <input type='text' id='formato_hora_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($formato_hora, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Zona horaria").": "."</span><br/>
                    <select id='zona_horaria_clase_externo_ficheros_csv_sensor' class='select-administracion'>";
        $controles .= dame_lista_zonas_horarias($zona_horaria);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_columna_horario_verano_clase_externo_ficheros_csv_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Columna de horario de verano").": "."</span><br/>
                    <input type='text' id='columna_horario_verano_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_numerical input-administracion' value='".$numero_columna_horario_verano."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de valores").": "."</span><br/>
                    <input type='text' id='numero_valores_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_valores."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Columnas de valores").": "."</span><br/>
                    <input type='text' id='columnas_valores_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($columnas_valores, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de valores").": "."</span><br/>
                    <select id='tipo_valores_sensor_clase_externo_ficheros_csv_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_valores_sensor($tipo_valores);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_horas_incrementos_clase_externo_ficheros_csv_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Horas de incrementos").": "."</span><br/>
                    <select id='tipo_horas_incrementos_clase_externo_ficheros_csv_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_horas_incrementos_valores_sensor($tipo_horas_incrementos);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_horas_incrementos_clase_externo_ficheros_csv_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de horas de incrementos").": "."</span><br/>
                    <input type='text' id='horas_incrementos_clase_externo_ficheros_csv_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$horas_incrementos."'>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_incrementos_clase_externo_ficheros_csv_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de incrementos").": "."</span><br/>
                    <select id='tipo_incrementos_clase_externo_ficheros_csv_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_incrementos_valores_sensor($tipo_incrementos);
        $controles .= "
                    </select>
                </div>
            </div>";

        //Opciones de Datadis

        $controles .= "
            <div class='row-fluid' id='control_campo_cups_datadis_api' style=".$display_datadis.">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Cups").": "."</span><br/>
                    <input type='text' id='cups_datadis_clase_externo_api'
                        class='input-administracion' value='".htmlspecialchars($cups, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid' id='control_campo_distributor_code_datadis_api' style=".$display_datadis.">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Codigo de Distribuidora").": "."</span><br/>
                    <input type='text' id='distributor_code_datadis_clase_externo_api'
                        class='input-administracion' value='".htmlspecialchars($distributor_code, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid' id='control_campo_measurement_type_api_sensor_externo' style=".$display_datadis.">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de datos").": "."</span><br/>
                    <select id='measurement_type_clase_externo_datadis_sensor' class='select-administracion'>";
                        $controles .= dame_lista_tipos_measurement_type($measurement_type);
                        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_campo_point_type_api_sensor_externo' style=".$display_datadis.">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de punto").": "."</span><br/>
                    <input type='text' id='point_type_clase_externo_api'
                        class='input-administracion' value='".$point_type."'>
                </div>
            </div>

            <div class='row-fluid' id='control_authorized_nif_api_sensor_externo' style=".$display_datadis.">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("NIF Autorizante").": "."</span><br/>
                    <input type='text' id='authorized_nif_clase_externo_api'
                        class='input-administracion' value='".$authorized_nif."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de sensor externo 'HTTP Emios'
    function dame_controles_clase_sensor_externo_http_emios($opciones_generales_externo, $opciones_valores_externo)
    {
        $idiomas = new Idiomas();

        // Valores por defecto de listas desplegables
        // (Nota: Se inicializan siempre porque si no no se puede cargar la información de todos los tipos de sensores externos 'HTTP Emios')
        $tipo_http_emios = TIPO_SENSOR_SOFTWARE_METEOROLOGICO;
        $proveedor_http_emios_tipo_meteorologico = PROVEEDOR_METEOROLOGICO_WORLDWEATHERONELINE;
        $tipo_informacion_http_emios_tipo_meteorologico = TIPO_INFORMACION_METEOROLOGICA_TEMPERATURA;
        $modo_localizacion_meteorologica_http_emios_tipo_meteorologico = MODO_LOCALIZACION_LOCALIDAD;

        // Si es una modificación del sensor externo
        if (($opciones_generales_externo !== NULL) && ($opciones_valores_externo !== NULL))
        {
            // Parámetros de opciones generales
            // (Nota: Los tipos de valores son siempre puntuales en sensores HTTP Emios)
            $parametros_opciones_generales_externo = dame_nombres_valores_parametros_opciones_generales_sensor_externo(
                CLASE_SENSOR_EXTERNO_HTTP_EMIOS,
                $opciones_generales_externo);
            $tiempo_muestreo = $parametros_opciones_generales_externo["tiempo_muestreo"];
            $tipo_valores = $parametros_opciones_generales_externo["tipo_valores"];
            $horas_incrementos = $parametros_opciones_generales_externo["horas_incrementos"];
            $tipo_incrementos = $parametros_opciones_generales_externo["tipo_incrementos"];

            // Parámetros de opciones de valores
            $parametros_opciones_valores_externo = dame_nombres_valores_parametros_opciones_valores_sensor_externo(
                CLASE_SENSOR_EXTERNO_HTTP_EMIOS,
                $opciones_valores_externo);
            $tipo_http_emios = $parametros_opciones_valores_externo["tipo_http_emios"];
            switch ($tipo_http_emios)
            {
                case TIPO_SENSOR_SOFTWARE_METEOROLOGICO:
                {
                    $proveedor_http_emios_tipo_meteorologico = $parametros_opciones_valores_externo["proveedor_http_emios_tipo_meteorologico"];
                    $tipo_informacion_http_emios_tipo_meteorologico = $parametros_opciones_valores_externo["tipo_informacion_http_emios_tipo_meteorologico"];
                    $latitud_tipo_meteorologico = $parametros_opciones_valores_externo["latitud_tipo_meteorologico"];
                    $longitud_tipo_meteorologico = $parametros_opciones_valores_externo["longitud_tipo_meteorologico"];
                    $localidad_tipo_meteorologico = $parametros_opciones_valores_externo["localidad_tipo_meteorologico"];
                    $pais_tipo_meteorologico = $parametros_opciones_valores_externo["pais_tipo_meteorologico"];
                    $idema_tipo_meteorologico = $parametros_opciones_valores_externo["idema_tipo_meteorologico"];
                    $modo_localizacion_meteorologica_http_emios_tipo_meteorologico = $parametros_opciones_valores_externo["modo_localizacion_meteorologica_http_emios_tipo_meteorologico"];
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de sensor externo HTTP Emios desconocido: '".$tipo_http_emios."'");
                }
            }
        }
        else
        {
            // Nota: Actualmente sólo se permiten valores puntuales en sensores HTTP Emios
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_clase_externo_http_emios_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_sensor_software($tipo_http_emios);
        $controles .= "
                    </select>
                </div>
            </div>";

        // Controles de sensor meteorológico HTTP Emios (sensor software Emios)
        $controles .= "
            <div class='row-fluid' id='control_proveedor_clase_externo_http_emios_tipo_meteorologico_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Proveedor").": "."</span><br/>
                    <select id='proveedor_clase_externo_http_emios_tipo_meteorologico_sensor' class='select-administracion'>";
        $controles .= dame_lista_proveedores_meteorologicos($proveedor_http_emios_tipo_meteorologico);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_informacion_clase_externo_http_emios_tipo_meteorologico_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de información").": "."</span><br/>
                    <select id='tipo_informacion_clase_externo_http_emios_tipo_meteorologico_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_informacion_meteorologica($proveedor_http_emios_tipo_meteorologico, $tipo_informacion_http_emios_tipo_meteorologico);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modo de localización").": "."</span><br/>
                    <select id='modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor' class='select-administracion'>";
        $controles .= dame_lista_modos_localizacion_meteorologica($proveedor_http_emios_tipo_meteorologico, $modo_localizacion_meteorologica_http_emios_tipo_meteorologico);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_latitud_clase_externo_http_emios_tipo_meteorologico_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Latitud").": "."</span><br/>
                    <input type='text' id='latitud_clase_externo_http_emios_tipo_meteorologico_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$latitud_tipo_meteorologico."'>
                </div>
            </div>

            <div class='row-fluid' id='control_longitud_clase_externo_http_emios_tipo_meteorologico_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Longitud").": "."</span><br/>
                    <input type='text' id='longitud_clase_externo_http_emios_tipo_meteorologico_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$longitud_tipo_meteorologico."'>
                </div>
            </div>

            <div class='row-fluid' id='control_localidad_clase_externo_http_emios_tipo_meteorologico_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Localidad").": "."</span><br/>
                    <input type='text' id='localidad_clase_externo_http_emios_tipo_meteorologico_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($localidad_tipo_meteorologico, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid' id='control_idema_clase_externo_http_emios_tipo_meteorologico_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Identificador de estación meteorológica").": "."</span><br/>
                    <input type='text' id='idema_clase_externo_http_emios_tipo_meteorologico_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($idema_tipo_meteorologico, ENT_QUOTES)."'>
                    <span id='boton_sensores_ayuda_idema_sensor_externo_http_emios_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>

            <div class='row-fluid' id='control_pais_clase_externo_http_emios_tipo_meteorologico_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("País").": "."</span><br/>
                    <input type='text' id='pais_clase_externo_http_emios_tipo_meteorologico_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($pais_tipo_meteorologico, ENT_QUOTES)."'>
                </div>
            </div>";

        // Tiempo de muestreo (para todos los sensores HTTP Emios)
        $controles .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tiempo de muestreo")." (".$idiomas->_("s")."): "."</span><br/>
                    <input type='text' id='tiempo_muestreo_clase_externo_http_emios_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$tiempo_muestreo."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de sensor externo 'HTTP XML PowerStudio'
    function dame_controles_clase_sensor_externo_http_xml_powerstudio($opciones_generales_externo, $opciones_valores_externo)
    {
        $idiomas = new Idiomas();

        // Si es una modificación del sensor externo
        if (($opciones_generales_externo !== NULL) && ($opciones_valores_externo !== NULL))
        {
            // Parámetros de opciones generales
            $parametros_opciones_generales_externo = dame_nombres_valores_parametros_opciones_generales_sensor_externo(
                CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO,
                $opciones_generales_externo);
            $tiempo_muestreo = $parametros_opciones_generales_externo["tiempo_muestreo"];
            $tipo_valores = $parametros_opciones_generales_externo["tipo_valores"];
            $horas_incrementos = $parametros_opciones_generales_externo["horas_incrementos"];
            $tipo_incrementos = $parametros_opciones_generales_externo["tipo_incrementos"];

            // Tipo de horas de incrementos
            if ($horas_incrementos == 0)
            {
                $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE;
            }
            else
            {
                $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO;
            }

            // Parámetros de opciones de valores
            $parametros_opciones_valores_externo = dame_nombres_valores_parametros_opciones_valores_sensor_externo(
                CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO,
                $opciones_valores_externo);
            $direccion_ip = $parametros_opciones_valores_externo["direccion_ip"];
            $puerto = $parametros_opciones_valores_externo["puerto"];
            $nombre_dispositivo = $parametros_opciones_valores_externo["nombre_dispositivo"];
            $nombre_variable = $parametros_opciones_valores_externo["nombre_variable"];
        }
        else
        {
            // Valores por defecto de listas desplegables cuando se añade un sensor externo
            $tipo_valores = TIPO_VALORES_SENSOR_PUNTUALES;
            $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO;
            $horas_incrementos = 0;
            $tipo_incrementos = TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL;
        }

        // Controles
        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección IP").": "."</span><br/>
                    <input type='text' id='direccion_ip_clase_externo_http_xml_powerstudio_sensor'
                        class='TLNT_input_mandatory input-administracion' value='".$direccion_ip."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Puerto").": "."</span><br/>
                    <input type='text' id='puerto_clase_externo_http_xml_powerstudio_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$puerto."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de dispositivo").": "."</span><br/>
                    <input type='text' id='nombre_dispositivo_clase_externo_http_xml_powerstudio_sensor'
                        class='TLNT_input_mandatory input-administracion' value='".htmlspecialchars($nombre_dispositivo, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de variable").": "."</span><br/>
                    <input type='text' id='nombre_variable_clase_externo_http_xml_powerstudio_sensor'
                        class='TLNT_input_mandatory input-administracion' value='".htmlspecialchars($nombre_variable, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tiempo de muestreo")." (".$idiomas->_("s")."): "."</span><br/>
                    <input type='text' id='tiempo_muestreo_clase_externo_http_xml_powerstudio_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$tiempo_muestreo."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de valores").": "."</span><br/>
                    <select id='tipo_valores_sensor_clase_externo_http_xml_powerstudio_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_valores_sensor($tipo_valores);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_horas_incrementos_clase_externo_http_xml_powerstudio_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Horas de incrementos").": "."</span><br/>
                    <select id='tipo_horas_incrementos_clase_externo_http_xml_powerstudio_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_horas_incrementos_valores_sensor($tipo_horas_incrementos);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_horas_incrementos_clase_externo_http_xml_powerstudio_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de horas de incrementos").": "."</span><br/>
                    <input type='text' id='horas_incrementos_clase_externo_http_xml_powerstudio_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$horas_incrementos."'>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_incrementos_clase_externo_http_xml_powerstudio_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de incrementos").": "."</span><br/>
                    <select id='tipo_incrementos_clase_externo_http_xml_powerstudio_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_incrementos_valores_sensor($tipo_incrementos);
        $controles .= "
                    </select>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de sensor externo 'Modbus IP'
    function dame_controles_clase_sensor_externo_modbus_ip($opciones_generales_externo, $opciones_valores_externo)
    {
        $idiomas = new Idiomas();

        // Si es una modificación del sensor externo
        if (($opciones_generales_externo !== NULL) && ($opciones_valores_externo !== NULL))
        {
            // Parámetros de opciones generales
            $parametros_opciones_generales_externo = dame_nombres_valores_parametros_opciones_generales_sensor_externo(
                CLASE_SENSOR_EXTERNO_MODBUS_IP,
                $opciones_generales_externo);
            $encapsulado = $parametros_opciones_generales_externo["encapsulado"];
            $protocolo = $parametros_opciones_generales_externo["protocolo"];
            $direccion_ip = $parametros_opciones_generales_externo["direccion_ip"];
            $puerto = $parametros_opciones_generales_externo["puerto"];
            $tiempo_muestreo = $parametros_opciones_generales_externo["tiempo_muestreo"];
            $tipo_valores = $parametros_opciones_generales_externo["tipo_valores"];
            $horas_incrementos = $parametros_opciones_generales_externo["horas_incrementos"];
            $tipo_incrementos = $parametros_opciones_generales_externo["tipo_incrementos"];

            // Tipo de horas de incrementos
            if ($horas_incrementos == 0)
            {
                $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE;
            }
            else
            {
                $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO;
            }

            // Nota: Los parámetros de opciones de valores se recuperan en su propia función
        }
        else
        {
            // Valores por defecto de listas desplegables cuando se añade un sensor externo
            $encapsulado = ENCAPSULADO_MODBUS_RTU;
            $protocolo = PROTOCOLO_TCP;
            $tipo_valores = TIPO_VALORES_SENSOR_PUNTUALES;
            $tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO;
            $horas_incrementos = 0;
            $tipo_incrementos = TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL;
        }

        // Controles
        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Encapsulado").": "."</span><br/>
                    <select id='id_encapsulado_clase_externo_modbus_ip_sensor' class='select-administracion'>";
        $controles .= dame_lista_encapsulados_modbus_ip($encapsulado);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Protocolo").": "."</span><br/>
                    <select id='protocolo_clase_externo_modbus_ip_sensor' class='select-administracion'>";
        $controles .= dame_lista_protocolos_ip($protocolo);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección IP").": "."</span><br/>
                    <input type='text' id='direccion_ip_clase_externo_modbus_ip_sensor'
                        class='TLNT_input_mandatory input-administracion' value='".$direccion_ip."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Puerto").": "."</span><br/>
                    <input type='text' id='puerto_clase_externo_modbus_ip_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$puerto."'>
                </div>
            </div>";

        $controles .= dame_controles_opciones_sensor_modbus("clase_externo_modbus_ip_sensor", $opciones_valores_externo);

        $controles .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de valores").": "."</span><br/>
                    <select id='tipo_valores_sensor_clase_externo_modbus_ip_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_valores_sensor($tipo_valores);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_horas_incrementos_clase_externo_modbus_ip_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Horas de incrementos").": "."</span><br/>
                    <select id='tipo_horas_incrementos_clase_externo_modbus_ip_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_horas_incrementos_valores_sensor($tipo_horas_incrementos);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_horas_incrementos_clase_externo_modbus_ip_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de horas de incrementos").": "."</span><br/>
                    <input type='text' id='horas_incrementos_clase_externo_modbus_ip_sensor'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$horas_incrementos."'>
                </div>
            </div>

            <div class='row-fluid' id='control_tipo_incrementos_clase_externo_modbus_ip_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de incrementos").": "."</span><br/>
                    <select id='tipo_incrementos_clase_externo_modbus_ip_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_incrementos_valores_sensor($tipo_incrementos);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tiempo de muestreo")." (".$idiomas->_("s")."): "."</span><br/>
                    <input type='text' id='tiempo_muestreo_clase_externo_modbus_ip_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$tiempo_muestreo."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de sensor externo 'Wibeee'
    function dame_controles_clase_sensor_externo_wibeee($opciones_generales_externo, $opciones_valores_externo)
    {
        $idiomas = new Idiomas();

        // Si es una modificación del sensor externo
        if (($opciones_generales_externo !== NULL) && ($opciones_valores_externo !== NULL))
        {
            // Parámetros de opciones generales
            $parametros_opciones_generales_externo = dame_nombres_valores_parametros_opciones_generales_sensor_externo(
                CLASE_SENSOR_EXTERNO_WIBEEE,
                $opciones_generales_externo);
            $direccion_mac = $parametros_opciones_generales_externo["direccion_mac"];

            // Parámetros de opciones de valores
            $parametros_opciones_valores_externo = dame_nombres_valores_parametros_opciones_valores_sensor_externo(
                CLASE_SENSOR_EXTERNO_WIBEEE,
                $opciones_valores_externo);
            $tipo_dato = $parametros_opciones_valores_externo["tipo_dato"];
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección MAC").": "."</span><br/>
					<input type='text' id='direccion_mac_clase_externo_wibeee_sensor'
						class='TLNT_input_mandatory TLNT_input_mac input-administracion' value='".$direccion_mac."'>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de dato").": "."</span><br/>
                    <select id='id_tipo_dato_clase_externo_wibeee_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_dato_wibeee($tipo_dato);
        $controles .= "
                    </select>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de sensor externo API
    function dame_controles_clase_sensor_externo_api($opciones_generales_externo, $opciones_valores_externo)
    {
        $idiomas = new Idiomas();

        // Valores por defecto de listas desplegables


        // Si es una modificación del sensor externo
        if (($opciones_generales_externo !== NULL) && ($opciones_valores_externo !== NULL))
        {
            // Parámetros de opciones generales

            //$parametros_opciones_generales_externo = dame_nombres_valores_parametros_opciones_generales_sensor_externo(
            //    CLASE_SENSOR_EXTERNO_API,
            //    $opciones_generales_externo);
            //    $log=dame_log();
            //$log->error(print_r($parametros_opciones_generales_externo, true));
            //$tiempo_muestreo = $parametros_opciones_generales_externo["tiempo_muestreo"];
            //$tipo_valores = $parametros_opciones_generales_externo["tipo_valores"];
            //$horas_incrementos = $parametros_opciones_generales_externo["horas_incrementos"];
            //$tipo_incrementos = $parametros_opciones_generales_externo["tipo_incrementos"];

            // Parámetros de opciones de valores
            $parametros_opciones_valores_externo = dame_nombres_valores_parametros_opciones_valores_sensor_externo(
                CLASE_SENSOR_EXTERNO_API,
                $opciones_valores_externo);
            $api_seleccionada = $parametros_opciones_valores_externo["api_seleccionada"];
            $control_desactiva_cambio_api = 'disabled';

            switch ($api_seleccionada)
            {
                case 'API_AXONTIME':
                {
                    $cups_id = $parametros_opciones_valores_externo["cups_id"];
                    $tipo_curva = $parametros_opciones_valores_externo["tipo_curva"];
                    $tipo_energia = $parametros_opciones_valores_externo["tipo_energia"];
                    break;
                }

                case 'API_SGCLIMA':
                {
                    $usuario = $parametros_opciones_valores_externo["usuario"];
                    if ($usuario != NULL){
                        $control_usuario = 'disabled';
                    }
                    $password = $parametros_opciones_valores_externo["password"];
                    if ($password != NULL){
                        $control_password = 'disabled';
                    }
                    $id_localizacion = $parametros_opciones_valores_externo["id_localizacion"];
                    $id_parametro = $parametros_opciones_valores_externo["id_parametro"];
                    break;
                }
                default:
                {
                    throw new Exception("API seleccionada desconocida: '".$api_seleccionada."'");
                }
            }
        }
        else
        {
            //$tipo_valores = TIPO_VALORES_SENSOR_PUNTUALES;
            //$tipo_horas_incrementos = TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO;
            //$horas_incrementos = 0;
            //$tipo_incrementos = TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL;
            $control_desactiva_cambio_api = '';
            $display_axontime = 'display:none';
            $display_sgclima = 'display:none';
        }

        if ($api_seleccionada != null) {

            switch ($api_seleccionada)
            {
                case 'API_AXONTIME':
                {
                    $display_axontime = 'display:block';
                    $display_sgclima = 'display:none';
                    break;
                }
                
                case 'API_SGCLIMA':
                {
                    $display_axontime = 'display:none';
                    $display_sgclima = 'display:block';
                    break;
                }
                default:
                {
                    $display_axontime = 'display:none';
                    $display_sgclima = 'display:none';
                    throw new Exception("API seleccionada desconocida: '".$api_seleccionada."'");
                    
                }
            }
        }



        // En el caso de que sea necesario meter la dirección de la api

    //    <div class='row-fluid' id='control_direccion_api_sensor_externo'>
    //            <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección Api").": "."</span><br/>
    //                <input type='text' id='direccion_api_clase_externo_api'
    //                    class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$direccion_api."'>
    //            </div>
    //        </div>



        //if ($api_seleccionada) {
        //    echo "funcion_cambia_api_sensores_externos();";
        //}

        // Devuelve el depslegable 'Api' para seleccionar una de la lista

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("API").": "."</span><br/>
                    <select id='api_seleccionada_sensor_externo_apis' class='select-administracion' ".$control_desactiva_cambio_api.">";
        $controles .= dame_lista_apis($api_seleccionada);
        $controles .= "
                    </select>
                </div>
            </div>";


        //Opciones de Axon Time
                $controles .= "<div class='row-fluid' id='control_campo_cups_id_api_sensor_externo' style=".$display_axontime.">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("CUPS ID").": "."</span><br/>
                    <input type='text' id='cups_id_clase_externo_api'
                        class='TLNT_input_float input-administracion' value='".$cups_id."'>
                </div>
            </div>
            <div class='row-fluid' id='control_campo_tipo_curva_api_sensor_externo' style=".$display_axontime.">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de curva").": "."</span><br/>
                    <select id='tipo_curva_clase_externo_axon_time_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_curva_sensor($tipo_curva);
        $controles .= "
                    </select>
                </div>
            </div>
            <div class='row-fluid' id='control_campo_tipo_energia_api_sensor_externo' style=".$display_axontime.">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de Energía").": "."</span><br/>
                    <select id='tipo_energia_clase_externo_axon_time_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_energia_sensor($tipo_energia);
        $controles .= "
                    </select>
                </div>
            </div>";

     //Opciones de Sgclima

            $controles .= "
        <div class='row-fluid' id='control_campo_usuario_api' style=".$display_sgclima.">
            <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Usuario").": "."</span><br/>
                <input type='text' id='usuario_api_clase_externo_api'
                    class='input-administracion' value='".htmlspecialchars($usuario, ENT_QUOTES)."' ".$control_usuario.">
            </div>
        </div>

        <div class='row-fluid' id='control_campo_password_api' style=".$display_sgclima.">
            <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Contraseña").": "."</span><br/>
                <input type='password' id='password_api_clase_externo_api'
                    class='input-administracion' value='".htmlspecialchars($password, ENT_QUOTES)."'".$control_usuario.">
            </div>
        </div>

        <div class='row-fluid' id='control_campo_id_localizacion_api_sensor_externo' style=".$display_sgclima.">
            <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("ID").": "."</span><br/>
                <input type='text' id='id_localizacion_clase_externo_api'
                    class='input-administracion' value='".$id_localizacion."'>
            </div>
        </div>

        <div class='row-fluid' id='control_campo_id_parametro_api_sensor_externo' style=".$display_sgclima.">
            <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Identificador Parámetro (ps_id)").": "."</span><br/>
                <input type='text' id='id_parametro_clase_externo_api'
                    class='input-administracion' value='".$id_parametro."'>
            </div>
        </div>";

        // Campos de timepo de muestreo, tipo de valores, horas de incrementos, etc... Gestionados externamente para estos sensores

        //<div class='row-fluid'>
        //        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tiempo de muestreo")." (".$idiomas->_("s")."): "."</span><br/>
        //            <input type='text' id='tiempo_muestreo_clase_externo_api_sensor'
        //                class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$tiempo_muestreo."'>
        //        </div>
        //    </div>
//
        //    <div class='row-fluid'>
        //        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de valores").": "."</span><br/>
        //            <select id='tipo_valores_sensor_clase_externo_api_sensor' class='select-administracion'>";
        //$controles .= dame_lista_tipos_valores_sensor($tipo_valores);
        //$controles .= "
        //            </select>
        //        </div>
        //    </div>
//
        //    <div class='row-fluid' id='control_tipo_horas_incrementos_clase_externo_api_sensor'>
        //        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Horas de incrementos").": "."</span><br/>
        //            <select id='tipo_horas_incrementos_clase_externo_api_sensor' class='select-administracion'>";
        //$controles .= dame_lista_tipos_horas_incrementos_valores_sensor($tipo_horas_incrementos);
        //$controles .= "
        //            </select>
        //        </div>
        //    </div>
//
        //    <div class='row-fluid' id='control_horas_incrementos_clase_externo_api_sensor'>
        //        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de horas de incrementos").": "."</span><br/>
        //            <input type='text' id='horas_incrementos_clase_externo_api_sensor'
        //                class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$horas_incrementos."'>
        //        </div>
        //    </div>
//
        //    <div class='row-fluid' id='control_tipo_incrementos_clase_externo_api_sensor'>
        //        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de incrementos").": "."</span><br/>
        //            <select id='tipo_incrementos_clase_externo_api_sensor' class='select-administracion'>";
        //$controles .= dame_lista_tipos_incrementos_valores_sensor($tipo_incrementos);
        //$controles .= "
        //            </select>
        //        </div>
        //    </div>";

        return ($controles);
    }


    //
    // Funciones de parámetros de sensores externos
    //


    function dame_nombres_valores_parametros_opciones_generales_sensor_externo($clase_sensor_externo, $cadena_opciones_generales_externo)
    {
        // Se recuperan los parámetros de opciones generales del sensor externo
        $nombres_valores_parametros_opciones_generales_sensor_externo = array();
        $parametros_opciones_generales_externo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_generales_externo);
        switch ($clase_sensor_externo)
        {
            case CLASE_SENSOR_EXTERNO_FICHEROS_CSV:
            {
                $prefijo_fichero_sustituto_separador = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_PREFIJO_FICHERO];
                $prefijo_fichero = str_replace(SUSTITUTO_SEPARADOR, SEPARADOR_PARAMETROS_SIMPLES, $prefijo_fichero_sustituto_separador);
                $formato_fichero = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_FORMATO_FICHERO];
                $nombres_valores_parametros_opciones_generales_sensor_externo["prefijo_fichero"] = $prefijo_fichero;
                $nombres_valores_parametros_opciones_generales_sensor_externo["formato_fichero"] = $formato_fichero;

                $caracter_separador_sustituto_separador = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_CARACTER_SEPARADOR];
                $caracter_separador = str_replace(SUSTITUTO_SEPARADOR, SEPARADOR_PARAMETROS_SIMPLES, $caracter_separador_sustituto_separador);
                $nombres_valores_parametros_opciones_generales_sensor_externo["caracter_separador"] = $caracter_separador;

                $punto_decimal_sustituto_separador = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_PUNTO_DECIMAL];
                $punto_decimal = str_replace(SUSTITUTO_SEPARADOR, SEPARADOR_PARAMETROS_SIMPLES, $punto_decimal_sustituto_separador);
                $id_punto_decimal = dame_id_punto_decimal($punto_decimal);
                $nombres_valores_parametros_opciones_generales_sensor_externo["id_punto_decimal"] = $id_punto_decimal;

                $cadena_cabeceras = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_CABECERAS];
                $numero_lineas_cabeceras = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_NUMERO_LINEAS_CABECERAS];
                if ($cadena_cabeceras == FICHERO_CSV_SIN_CABECERAS)
                {
                    $numero_lineas_cabeceras = 0;
                }
                $nombres_valores_parametros_opciones_generales_sensor_externo["numero_lineas_cabeceras"] = $numero_lineas_cabeceras;

                $numero_columna_fecha = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_NUMERO_COLUMNA_FECHA] + 1;
                $formato_fecha_python_sustituto_separador = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_FORMATO_FECHA];
                $formato_fecha_python = str_replace(SUSTITUTO_SEPARADOR, SEPARADOR_PARAMETROS_SIMPLES, $formato_fecha_python_sustituto_separador);
                $formato_fecha = convierte_formato_hora_python_a_formato_fecha_hora($formato_fecha_python);
                $numero_columna_hora = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_NUMERO_COLUMNA_HORA];
                $formato_hora_python_sustituto_separador = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_FORMATO_HORA];
                $formato_hora_python = str_replace(SUSTITUTO_SEPARADOR, SEPARADOR_PARAMETROS_SIMPLES, $formato_hora_python_sustituto_separador);
                if (($numero_columna_hora != "") && ($formato_hora_python != ""))
                {
                    $numero_columna_hora += 1;
                    $hora_columna_independiente = VALOR_SI;
                    $formato_hora = convierte_formato_hora_python_a_formato_fecha_hora($formato_hora_python);
                }
                else
                {
                    $hora_columna_independiente = VALOR_NO;
                }
                $nombres_valores_parametros_opciones_generales_sensor_externo["numero_columna_fecha"] = $numero_columna_fecha;
                $nombres_valores_parametros_opciones_generales_sensor_externo["formato_fecha"] = $formato_fecha;
                $nombres_valores_parametros_opciones_generales_sensor_externo["hora_columna_independiente"] = $hora_columna_independiente;
                $nombres_valores_parametros_opciones_generales_sensor_externo["numero_columna_hora"] = $numero_columna_hora;
                $nombres_valores_parametros_opciones_generales_sensor_externo["formato_hora"] = $formato_hora;

                $zona_horaria = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_ZONA_HORARIA];
                $numero_columna_horario_verano = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_NUMERO_COLUMNA_HORARIO_VERANO];
                if ($numero_columna_horario_verano != "")
                {
                    $numero_columna_horario_verano += 1;
                }
                $nombres_valores_parametros_opciones_generales_sensor_externo["zona_horaria"] = $zona_horaria;
                $nombres_valores_parametros_opciones_generales_sensor_externo["numero_columna_horario_verano"] = $numero_columna_horario_verano;

                $numero_valores = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_NUMERO_VALORES];
                $tipo_valores = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_TIPO_VALORES];
                if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    $segundos_incrementos = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS];
                    $horas_incrementos = (float) ($segundos_incrementos / 3600);
                    $tipo_incrementos = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_TIPO_INCREMENTOS];
                }
                $nombres_valores_parametros_opciones_generales_sensor_externo["numero_valores"] = $numero_valores;
                $nombres_valores_parametros_opciones_generales_sensor_externo["tipo_valores"] = $tipo_valores;
                $nombres_valores_parametros_opciones_generales_sensor_externo["horas_incrementos"] = $horas_incrementos;
                $nombres_valores_parametros_opciones_generales_sensor_externo["tipo_incrementos"] = $tipo_incrementos;
                break;
            }
            case CLASE_SENSOR_EXTERNO_HTTP_EMIOS:
            {
                $tiempo_muestreo = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_EMIOS_OPCIONES_GENERALES_TIEMPO_MUESTREO];
                $tipo_valores = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_EMIOS_OPCIONES_GENERALES_TIPO_VALORES];
                if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    $segundos_incrementos = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_EMIOS_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS];
                    $horas_incrementos = (float) ($segundos_incrementos / 3600);
                    $tipo_incrementos = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_EMIOS_OPCIONES_GENERALES_TIPO_INCREMENTOS];
                }
                $nombres_valores_parametros_opciones_generales_sensor_externo["tiempo_muestreo"] = $tiempo_muestreo;
                $nombres_valores_parametros_opciones_generales_sensor_externo["tipo_valores"] = $tipo_valores;
                $nombres_valores_parametros_opciones_generales_sensor_externo["horas_incrementos"] = $horas_incrementos;
                $nombres_valores_parametros_opciones_generales_sensor_externo["tipo_incrementos"] = $tipo_incrementos;
                break;
            }
            case CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO:
            {
                $tiempo_muestreo = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_GENERALES_TIEMPO_MUESTREO];
                $tipo_valores = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_GENERALES_TIPO_VALORES];
                if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    $segundos_incrementos = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS];
                    $horas_incrementos = (float) ($segundos_incrementos / 3600);
                    $tipo_incrementos = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_GENERALES_TIPO_INCREMENTOS];
                }
                $nombres_valores_parametros_opciones_generales_sensor_externo["tiempo_muestreo"] = $tiempo_muestreo;
                $nombres_valores_parametros_opciones_generales_sensor_externo["tipo_valores"] = $tipo_valores;
                $nombres_valores_parametros_opciones_generales_sensor_externo["horas_incrementos"] = $horas_incrementos;
                $nombres_valores_parametros_opciones_generales_sensor_externo["tipo_incrementos"] = $tipo_incrementos;
                break;
            }
            case CLASE_SENSOR_EXTERNO_MODBUS_IP:
            {
                $encapsulado = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_ENCAPSULADO];
                $protocolo = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_PROTOCOLO];
                $direccion_ip = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_DIRECCION_IP];
                $puerto = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_PUERTO];
                $tiempo_muestreo = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_TIEMPO_MUESTREO];
                $tipo_valores = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_TIPO_VALORES];
                if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    $segundos_incrementos = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS];
                    $horas_incrementos = (float) ($segundos_incrementos / 3600);
                    $tipo_incrementos = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_MODBUS_IP_OPCIONES_GENERALES_TIPO_INCREMENTOS];
                }
                $nombres_valores_parametros_opciones_generales_sensor_externo["encapsulado"] = $encapsulado;
                $nombres_valores_parametros_opciones_generales_sensor_externo["protocolo"] = $protocolo;
                $nombres_valores_parametros_opciones_generales_sensor_externo["direccion_ip"] = $direccion_ip;
                $nombres_valores_parametros_opciones_generales_sensor_externo["puerto"] = $puerto;
                $nombres_valores_parametros_opciones_generales_sensor_externo["tiempo_muestreo"] = $tiempo_muestreo;
                $nombres_valores_parametros_opciones_generales_sensor_externo["tipo_valores"] = $tipo_valores;
                $nombres_valores_parametros_opciones_generales_sensor_externo["horas_incrementos"] = $horas_incrementos;
                $nombres_valores_parametros_opciones_generales_sensor_externo["tipo_incrementos"] = $tipo_incrementos;
                break;
            }
            case CLASE_SENSOR_EXTERNO_WIBEEE:
            {
                $direccion_mac = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_WIBEEE_OPCIONES_GENERALES_DIRECCION_MAC];
                $nombres_valores_parametros_opciones_generales_sensor_externo["direccion_mac"] = $direccion_mac;
                break;
            }
            case CLASE_SENSOR_EXTERNO_API:
            {
                //$tiempo_muestreo = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_OPCIONES_GENERALES_TIEMPO_MUESTREO];
                //$tipo_valores = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_OPCIONES_GENERALES_TIPO_VALORES];
                //if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
                //{
                //    $segundos_incrementos = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS];
                //    $horas_incrementos = (float) ($segundos_incrementos / 3600);
                //    $tipo_incrementos = $parametros_opciones_generales_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_OPCIONES_GENERALES_TIPO_INCREMENTOS];
                //}
                //$nombres_valores_parametros_opciones_generales_sensor_externo["tiempo_muestreo"] = $tiempo_muestreo;
                //$nombres_valores_parametros_opciones_generales_sensor_externo["tipo_valores"] = $tipo_valores;
                //$nombres_valores_parametros_opciones_generales_sensor_externo["horas_incrementos"] = $horas_incrementos;
                //$nombres_valores_parametros_opciones_generales_sensor_externo["tipo_incrementos"] = $tipo_incrementos;
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor externo desconocida: '".$clase_sensor_externo."'");
            }
        }
        return ($nombres_valores_parametros_opciones_generales_sensor_externo);
    }


    function dame_nombres_valores_parametros_opciones_valores_sensor_externo($clase_sensor_externo, $cadena_opciones_valores_externo)
    {
        // Se recuperan los parámetros de opciones de valores del sensor externo
        $nombres_valores_parametros_opciones_valores_sensor_externo = array();
        switch ($clase_sensor_externo)
        {
            case CLASE_SENSOR_EXTERNO_FICHEROS_CSV:
            {
                // Columnas de valores
                $parametros_opciones_valores_externo = explode(SEPARADOR_PARAMETROS_VALORES, $cadena_opciones_valores_externo);
                $columnas_valores = "";
                for ($i = 0; $i < count($parametros_opciones_valores_externo); $i++)
                {
                    if ($i > 0)
                    {
                        $columnas_valores .= " ".SEPARADOR_PARAMETROS_VALORES." ";
                    }
                    $parametros_opciones_valor = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_opciones_valores_externo[$i]);
                    $numero_columna_valor = $parametros_opciones_valor[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_VALORES_NUMERO_COLUMNA] + 1;
                    $cadena_opciones_valor = $numero_columna_valor;

                    // Número de bit inicial y número de bits
                    if (count($parametros_opciones_valor) > (INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_VALORES_NUMERO_COLUMNA + 1))
                    {
                        $numero_bit_inicial_valor = $parametros_opciones_valor[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_VALORES_NUMERO_BIT_INICIAL] + 1;
                        $cadena_opciones_valor .= SEPARADOR_PARAMETROS_SIMPLES." ".$numero_bit_inicial_valor;
                    }
                    if (count($parametros_opciones_valor) == (INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_VALORES_NUMERO_BITS_VALOR + 1))
                    {
                        $numero_bits_valor = $parametros_opciones_valor[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_VALORES_NUMERO_BITS_VALOR];
                        $cadena_opciones_valor .= SEPARADOR_PARAMETROS_SIMPLES." ".$numero_bits_valor;
                    }
                    $columnas_valores .= $cadena_opciones_valor;
                }
                $nombres_valores_parametros_opciones_valores_sensor_externo["columnas_valores"] = $columnas_valores;
                break;
            }
            case CLASE_SENSOR_EXTERNO_HTTP_EMIOS:
            {
                // Parámetros de configuración HTTP Emios
                $parametros_opciones_valores_externo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores_externo);
                $parametros_configuracion_http_emios = array();
                foreach ($parametros_opciones_valores_externo as $parametro_opciones_valores_externo)
                {
                    $valor_parametro = explode("=", $parametro_opciones_valores_externo);
                    $nombre_parametro = $valor_parametro[0];
                    $parametros_configuracion_http_emios[$nombre_parametro] = $valor_parametro[1];
                }

                // Tipo de sensor HTTP Emios
                $tipo_http_emios = $parametros_configuracion_http_emios[PARAMETRO_TIPO_SENSOR_SOFTWARE];
                $nombres_valores_parametros_opciones_valores_sensor_externo["tipo_http_emios"] = $tipo_http_emios;

                // Parámetros de tipo de HTTP Emios
                switch ($tipo_http_emios)
                {
                    case TIPO_SENSOR_SOFTWARE_METEOROLOGICO:
                    {
                        $proveedor_http_emios_tipo_meteorologico = $parametros_configuracion_http_emios[PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_PROVEEDOR];
                        $clave_tipo_meteorologico = $parametros_configuracion_http_emios[PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_CLAVE];
                        $tipo_informacion_http_emios_tipo_meteorologico = $parametros_configuracion_http_emios[PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_TIPO_INFORMACION];
                        $nombres_valores_parametros_opciones_valores_sensor_externo["proveedor_http_emios_tipo_meteorologico"] = $proveedor_http_emios_tipo_meteorologico;
                        $nombres_valores_parametros_opciones_valores_sensor_externo["tipo_informacion_http_emios_tipo_meteorologico"] = $tipo_informacion_http_emios_tipo_meteorologico;
                        $nombres_valores_parametros_opciones_valores_sensor_externo["clave_tipo_meteorologico"] = $clave_tipo_meteorologico;

                        // Se establece el modo de localización en base a latitud, longitud, localidad y pais o idema
                        $latitud_tipo_meteorologico = $parametros_configuracion_http_emios[PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_LATITUD];
                        $longitud_tipo_meteorologico = $parametros_configuracion_http_emios[PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_LONGITUD];
                        $localidad_tipo_meteorologico = $parametros_configuracion_http_emios[PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_LOCALIDAD];
                        $pais_tipo_meteorologico = $parametros_configuracion_http_emios[PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_CODIGO_PAIS];
                        $idema_tipo_meteorologico = $parametros_configuracion_http_emios[PARAMETRO_SENSOR_SOFTWARE_METEOROLOGICO_IDEMA];
                        $nombres_valores_parametros_opciones_valores_sensor_externo["latitud_tipo_meteorologico"] = $latitud_tipo_meteorologico;
                        $nombres_valores_parametros_opciones_valores_sensor_externo["longitud_tipo_meteorologico"] = $longitud_tipo_meteorologico;
                        $nombres_valores_parametros_opciones_valores_sensor_externo["localidad_tipo_meteorologico"] = $localidad_tipo_meteorologico;
                        $nombres_valores_parametros_opciones_valores_sensor_externo["pais_tipo_meteorologico"] = $pais_tipo_meteorologico;
                        $nombres_valores_parametros_opciones_valores_sensor_externo["idema_tipo_meteorologico"] = $idema_tipo_meteorologico;

                        // Parámetros de proveedor meteorológico
                        switch ($proveedor_http_emios_tipo_meteorologico)
                        {
                            case PROVEEDOR_METEOROLOGICO_AEMET:
                            {
                                $modo_localizacion_meteorologica_http_emios_tipo_meteorologico = MODO_LOCALIZACION_IDEMA;
                                break;
                            }
                            case PROVEEDOR_METEOROLOGICO_WORLDWEATHERONELINE:
                            {
                                if (($latitud_tipo_meteorologico !== NULL) && ($longitud_tipo_meteorologico !== NULL))
                                {
                                    $modo_localizacion_meteorologica_http_emios_tipo_meteorologico = MODO_LOCALIZACION_COORDENADAS_GEOGRAFICAS;
                                }
                                else
                                {
                                    $modo_localizacion_meteorologica_http_emios_tipo_meteorologico = MODO_LOCALIZACION_LOCALIDAD;
                                }
                                break;
                            }
                            default:
                            {
                                throw new Exception("Proveedor meteorológico desconocido: '".$proveedor_http_emios_tipo_meteorologico."'");
                            }
                        }
                        $nombres_valores_parametros_opciones_valores_sensor_externo["modo_localizacion_meteorologica_http_emios_tipo_meteorologico"] = $modo_localizacion_meteorologica_http_emios_tipo_meteorologico;
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de sensor externo HTTP Emios desconocido: '".$tipo_http_emios."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO:
            {
                $parametros_opciones_valores_externo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores_externo);
                $direccion_ip = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_VALORES_DIRECCION_IP];
                $puerto = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_VALORES_PUERTO];
                $nombre_dispositivo = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_VALORES_NOMBRE_DISPOSITIVO];
                $nombre_variable = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_HTTP_XML_POWER_STUDIO_OPCIONES_VALORES_NOMBRE_VARIABLE];
                $nombres_valores_parametros_opciones_valores_sensor_externo["direccion_ip"] = $direccion_ip;
                $nombres_valores_parametros_opciones_valores_sensor_externo["puerto"] = $puerto;
                $nombres_valores_parametros_opciones_valores_sensor_externo["nombre_dispositivo"] = $nombre_dispositivo;
                $nombres_valores_parametros_opciones_valores_sensor_externo["nombre_variable"] = $nombre_variable;
                break;
            }
            case CLASE_SENSOR_EXTERNO_MODBUS_IP:
            {
                $nombres_valores_parametros_opciones_valores_sensor_externo = dame_nombres_valores_parametros_sensor_modbus($cadena_opciones_valores_externo);
                break;
            }
            case CLASE_SENSOR_EXTERNO_WIBEEE:
            {
                $tipo_dato = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_WIBEEE_OPCIONES_VALORES_TIPO_DATO];
                $nombres_valores_parametros_opciones_valores_sensor_externo["$tipo_dato"] = $tipo_dato;
                break;
            }
            case CLASE_SENSOR_EXTERNO_API:
            {
                $parametros_opciones_valores_externo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores_externo);
                $api_seleccionada = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_OPCIONES_VALORES_API_SELECCIONADA];

                $nombres_valores_parametros_opciones_valores_sensor_externo["api_seleccionada"] = $api_seleccionada;

                switch ($api_seleccionada) {
                    case 'API_AXONTIME':
                        {
                            $cups_id = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_AXONTIME_OPCIONES_VALORES_CUPS_ID];
                            $tipo_curva = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_AXONTIME_OPCIONES_VALORES_TIPO_CURVA];
                            $tipo_energia = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_AXONTIME_OPCIONES_VALORES_VALOR_LECTURA];
                            $nombres_valores_parametros_opciones_valores_sensor_externo["cups_id"] = $cups_id;
                            $nombres_valores_parametros_opciones_valores_sensor_externo["tipo_curva"] = $tipo_curva;
                            $nombres_valores_parametros_opciones_valores_sensor_externo["tipo_energia"] = $tipo_energia;
                            break;
                        }
                    case 'API_SGCLIMA':
                        {
                            $usuario = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_SGCLIMA_OPCIONES_VALORES_USUARIO];
                            $password = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_SGCLIMA_OPCIONES_VALORES_PASSWORD];
                            $id_localizacion = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_SGCLIMA_OPCIONES_VALORES_ID_LOC];
                            $id_parametro = $parametros_opciones_valores_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_API_SGCLIMA_OPCIONES_VALORES_ID_PARAM];
                            $nombres_valores_parametros_opciones_valores_sensor_externo["usuario"] = $usuario;
                            $nombres_valores_parametros_opciones_valores_sensor_externo["password"] = $password;
                            $nombres_valores_parametros_opciones_valores_sensor_externo["id_localizacion"] = $id_localizacion;
                            $nombres_valores_parametros_opciones_valores_sensor_externo["id_parametro"] = $id_parametro;
                            break;
                        }
                    default:
                        throw new Exception("Api seleccionada desconocida: '".$api_seleccionada."'");
                        break;
                }
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor externo desconocida: '".$clase_sensor_externo."'");
            }
        }
        return ($nombres_valores_parametros_opciones_valores_sensor_externo);
    }


    function dame_descripcion_parametros_opciones_generales_sensor_externo(
        $clase_sensor_externo,
        $cadena_opciones_generales_externo,
        $tipo_descripcion)
    {
        $idiomas = new Idiomas();

        // Delimitadores de descripción de parámetros
        $cadena_inicio_lista_parametros = "";
        $cadena_fin_lista_parametros = "";
        $cadena_inicio_primer_parametro = "";
        $cadena_inicio_parametro = "";
        $cadena_fin_parametro = "";
        establece_delimitadores_descripcion_parametros(
            $tipo_descripcion,
            $cadena_inicio_lista_parametros,
            $cadena_fin_lista_parametros,
            $cadena_inicio_primer_parametro,
            $cadena_inicio_parametro,
            $cadena_fin_parametro);

        // Parámetros de opciones generales del sensor externo
        $parametros_opciones_generales_externo = dame_nombres_valores_parametros_opciones_generales_sensor_externo(
            $clase_sensor_externo,
            $cadena_opciones_generales_externo);
        $html = $cadena_inicio_lista_parametros;
        switch ($clase_sensor_externo)
        {
            case CLASE_SENSOR_EXTERNO_NINGUNA:
            {
                break;
            }
            case CLASE_SENSOR_EXTERNO_FICHEROS_CSV:
            {
                $prefijo_fichero = $parametros_opciones_generales_externo["prefijo_fichero"];
                $formato_fichero = $parametros_opciones_generales_externo["formato_fichero"];
                $caracter_separador = $parametros_opciones_generales_externo["caracter_separador"];
                $id_punto_decimal = $parametros_opciones_generales_externo["id_punto_decimal"];
                $numero_lineas_cabeceras = $parametros_opciones_generales_externo["numero_lineas_cabeceras"];
                $numero_columna_fecha = $parametros_opciones_generales_externo["numero_columna_fecha"];
                $formato_fecha = $parametros_opciones_generales_externo["formato_fecha"];
                $hora_columna_independiente = $parametros_opciones_generales_externo["hora_columna_independiente"];
                $numero_columna_hora = $parametros_opciones_generales_externo["numero_columna_hora"];
                $formato_hora = $parametros_opciones_generales_externo["formato_hora"];
                $zona_horaria = $parametros_opciones_generales_externo["zona_horaria"];
                $numero_columna_horario_verano = $parametros_opciones_generales_externo["numero_columna_horario_verano"];
                $numero_valores = $parametros_opciones_generales_externo["numero_valores"];
                $tipo_valores = $parametros_opciones_generales_externo["tipo_valores"];
                $horas_incrementos = $parametros_opciones_generales_externo["horas_incrementos"];
                $tipo_incrementos = $parametros_opciones_generales_externo["tipo_incrementos"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Prefijo de fichero").": ".$prefijo_fichero.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Formato de fichero").": ".dame_descripcion_formato_fichero_valores($formato_fichero).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Carácter separador").": ".$caracter_separador.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Punto decimal").": ".dame_descripcion_id_punto_decimal($id_punto_decimal).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Número de líneas de cabecera").": ".$numero_lineas_cabeceras.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Columna de fecha").": ".$numero_columna_fecha.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Formato de fecha").": ".$formato_fecha.$cadena_fin_parametro;
                if ($hora_columna_independiente == VALOR_SI)
                {
                    $html .= $cadena_inicio_parametro.$idiomas->_("Columna de hora").": ".$numero_columna_hora.$cadena_fin_parametro;
                    $html .= $cadena_inicio_parametro.$idiomas->_("Formato de hora").": ".$formato_hora.$cadena_fin_parametro;
                }
                $html .= $cadena_inicio_parametro.$idiomas->_("Zona horaria").": ".dame_nombre_zona_horaria($zona_horaria).$cadena_fin_parametro;
                if ($numero_columna_horario_verano != "")
                {
                    $html .= $cadena_inicio_parametro.$idiomas->_("Columna de horario de verano").": ".$numero_columna_horario_verano.$cadena_fin_parametro;
                }
                $html .= $cadena_inicio_parametro.$idiomas->_("Número de valores").": ".$numero_valores.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Tipo de valores").": ".NodoSensor::dame_descripcion_tipo_valores_sensor($tipo_valores).$cadena_fin_parametro;
                if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    if ($horas_incrementos == 0)
                    {
                        $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE).$cadena_fin_parametro;
                    }
                    else
                    {
                        $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO).$cadena_fin_parametro;
                        $html .= $cadena_inicio_parametro.$idiomas->_("Número de horas de incrementos").": ".$horas_incrementos.$cadena_fin_parametro;
                    }
                    $html .= $cadena_inicio_parametro.$idiomas->_("Tipo de incrementos").": ".NodoSensor::dame_descripcion_tipo_incrementos_valores_sensor($tipo_incrementos).$cadena_fin_parametro;
                }
                break;
            }
            case CLASE_SENSOR_EXTERNO_HTTP_EMIOS:
            {
                $tiempo_muestreo = $parametros_opciones_generales_externo["tiempo_muestreo"];
                $tipo_valores = $parametros_opciones_generales_externo["tipo_valores"];
                $horas_incrementos = $parametros_opciones_generales_externo["horas_incrementos"];
                $tipo_incrementos = $parametros_opciones_generales_externo["tipo_incrementos"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Tiempo de muestreo")." (".$idiomas->_("s")."): ".$tiempo_muestreo.$cadena_fin_parametro;

                // Nota: Actualmente sólo hay valores puntuales en los sensores HTTP Emios (no se muestra)
                /*$html .= $cadena_inicio_parametro.$idiomas->_("Tipo de valores").": ".NodoSensor::dame_descripcion_tipo_valores_sensor($tipo_valores).$cadena_fin_parametro;
                if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    if ($horas_incrementos == 0)
                    {
                        $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE).$cadena_fin_parametro;
                    }
                    else
                    {
                        $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO).$cadena_fin_parametro;
                        $html .= $cadena_inicio_parametro.$idiomas->_("Número de horas de incrementos").": ".$horas_incrementos.$cadena_fin_parametro;
                    }
                }*/
                break;
            }
            case CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO:
            {
                $tiempo_muestreo = $parametros_opciones_generales_externo["tiempo_muestreo"];
                $tipo_valores = $parametros_opciones_generales_externo["tipo_valores"];
                $horas_incrementos = $parametros_opciones_generales_externo["horas_incrementos"];
                $tipo_incrementos = $parametros_opciones_generales_externo["tipo_incrementos"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Tiempo de muestreo")." (".$idiomas->_("s")."): ".$tiempo_muestreo.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Tipo de valores").": ".NodoSensor::dame_descripcion_tipo_valores_sensor($tipo_valores).$cadena_fin_parametro;
                if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    if ($horas_incrementos == 0)
                    {
                        $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE).$cadena_fin_parametro;
                    }
                    else
                    {
                        $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO).$cadena_fin_parametro;
                        $html .= $cadena_inicio_parametro.$idiomas->_("Número de horas de incrementos").": ".$horas_incrementos.$cadena_fin_parametro;
                    }
                }
                break;
            }
            case CLASE_SENSOR_EXTERNO_MODBUS_IP:
            {
                $encapsulado = $parametros_opciones_generales_externo["encapsulado"];
                $protocolo = $parametros_opciones_generales_externo["protocolo"];
                $direccion_ip = $parametros_opciones_generales_externo["direccion_ip"];
                $puerto = $parametros_opciones_generales_externo["puerto"];
                $tiempo_muestreo = $parametros_opciones_generales_externo["tiempo_muestreo"];
                $tipo_valores = $parametros_opciones_generales_externo["tipo_valores"];
                $horas_incrementos = $parametros_opciones_generales_externo["horas_incrementos"];
                $tipo_incrementos = $parametros_opciones_generales_externo["tipo_incrementos"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Encapsulado").": ".dame_descripcion_encapsulado_modbus($encapsulado).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Protocolo").": ".dame_descripcion_protocolo_ip($protocolo).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Dirección IP").": ".$direccion_ip.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Puerto").": ".$puerto.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Tiempo de muestreo")." (".$idiomas->_("s")."): ".$tiempo_muestreo.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Tipo de valores").": ".NodoSensor::dame_descripcion_tipo_valores_sensor($tipo_valores).$cadena_fin_parametro;
                if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    if ($horas_incrementos == 0)
                    {
                        $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE).$cadena_fin_parametro;
                    }
                    else
                    {
                        $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO).$cadena_fin_parametro;
                        $html .= $cadena_inicio_parametro.$idiomas->_("Número de horas de incrementos").": ".$horas_incrementos.$cadena_fin_parametro;
                    }
                }
                break;
            }
            case CLASE_SENSOR_EXTERNO_WIBEEE:
            {
                $direccion_mac = $parametros_opciones_generales_externo["direccion_mac"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Dirección MAC").": ".$direccion_mac.$cadena_fin_parametro;
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor externo desconocida: '".$clase_sensor_externo."'");
            }
            case CLASE_SENSOR_EXTERNO_API:
            {
                //$tiempo_muestreo = $parametros_opciones_generales_externo["tiempo_muestreo"];
                //$tipo_valores = $parametros_opciones_generales_externo["tipo_valores"];
                //$horas_incrementos = $parametros_opciones_generales_externo["horas_incrementos"];
                //$tipo_incrementos = $parametros_opciones_generales_externo["tipo_incrementos"];
                //$html .= $cadena_inicio_parametro.$idiomas->_("Tiempo de muestreo")." (".$idiomas->_("s")."): ".$tiempo_muestreo.$cadena_fin_parametro;
                //$html .= $cadena_inicio_parametro.$idiomas->_("Tipo de valores").": ".NodoSensor::dame_descripcion_tipo_valores_sensor($tipo_valores).$cadena_fin_parametro;
                //if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
                //{
                //    if ($horas_incrementos == 0)
                //    {
                //        $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE).$cadena_fin_parametro;
                //    }
                //    else
                //    {
                //        $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO).$cadena_fin_parametro;
                //        $html .= $cadena_inicio_parametro.$idiomas->_("Número de horas de incrementos").": ".$horas_incrementos.$cadena_fin_parametro;
                //    }
                //}
                break;
            }
        }
        $html .= $cadena_fin_lista_parametros;
        return ($html);
    }


    function dame_descripcion_parametros_opciones_valores_sensor_externo(
        $clase_sensor_externo,
        $cadena_opciones_valores_externo,
        $tipo_descripcion)
    {
        $idiomas = new Idiomas();

        // Delimitadores de descripción de parámetros
        $cadena_inicio_lista_parametros = "";
        $cadena_fin_lista_parametros = "";
        $cadena_inicio_primer_parametro = "";
        $cadena_inicio_parametro = "";
        $cadena_fin_parametro = "";
        establece_delimitadores_descripcion_parametros(
            $tipo_descripcion,
            $cadena_inicio_lista_parametros,
            $cadena_fin_lista_parametros,
            $cadena_inicio_primer_parametro,
            $cadena_inicio_parametro,
            $cadena_fin_parametro);

        // Parámetros de opciones de valores del sensor externo
        $parametros_opciones_valores_externo = dame_nombres_valores_parametros_opciones_valores_sensor_externo(
            $clase_sensor_externo,
            $cadena_opciones_valores_externo);
        $html = $cadena_inicio_lista_parametros;
        switch ($clase_sensor_externo)
        {
            case CLASE_SENSOR_EXTERNO_NINGUNA:
            {
                break;
            }
            case CLASE_SENSOR_EXTERNO_FICHEROS_CSV:
            {
                $columnas_valores = $parametros_opciones_valores_externo["columnas_valores"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Columnas de valores").": ".$columnas_valores.$cadena_fin_parametro;
                break;
            }
            case CLASE_SENSOR_EXTERNO_HTTP_EMIOS:
            {
                $tipo_http_emios = $parametros_opciones_valores_externo["tipo_http_emios"];
                switch ($tipo_http_emios)
                {
                    case TIPO_SENSOR_SOFTWARE_METEOROLOGICO:
                    {
                        $proveedor_http_emios_tipo_meteorologico = $parametros_opciones_valores_externo["proveedor_http_emios_tipo_meteorologico"];
                        $tipo_informacion_http_emios_tipo_meteorologico = $parametros_opciones_valores_externo["tipo_informacion_http_emios_tipo_meteorologico"];
                        $latitud_tipo_meteorologico = $parametros_opciones_valores_externo["latitud_tipo_meteorologico"];
                        $longitud_tipo_meteorologico = $parametros_opciones_valores_externo["longitud_tipo_meteorologico"];
                        $localidad_tipo_meteorologico = $parametros_opciones_valores_externo["localidad_tipo_meteorologico"];
                        $pais_tipo_meteorologico = $parametros_opciones_valores_externo["pais_tipo_meteorologico"];
                        $idema_tipo_meteorologico = $parametros_opciones_valores_externo["idema_tipo_meteorologico"];
                        $modo_localizacion_meteorologica_http_emios_tipo_meteorologico = $parametros_opciones_valores_externo["modo_localizacion_meteorologica_http_emios_tipo_meteorologico"];

                        $html .= $cadena_inicio_parametro.$idiomas->_("Proveedor").": ".dame_descripcion_proveedor_meteorologico($proveedor_http_emios_tipo_meteorologico).$cadena_fin_parametro;
                        $html .= $cadena_inicio_parametro.$idiomas->_("Tipo de información").": ".dame_descripcion_tipo_informacion_meteorologica($tipo_informacion_http_emios_tipo_meteorologico).$cadena_fin_parametro;
                        $html .= $cadena_inicio_parametro.$idiomas->_("Modo de localización").": ".dame_descripcion_modo_localizacion($modo_localizacion_meteorologica_http_emios_tipo_meteorologico).$cadena_fin_parametro;
                        switch ($modo_localizacion_meteorologica_http_emios_tipo_meteorologico)
                        {
                            case MODO_LOCALIZACION_COORDENADAS_GEOGRAFICAS:
                            {
                                $html .= $cadena_inicio_parametro.$idiomas->_("Latitud").": ".$latitud_tipo_meteorologico.$cadena_fin_parametro;
                                $html .= $cadena_inicio_parametro.$idiomas->_("Longitud").": ".$longitud_tipo_meteorologico.$cadena_fin_parametro;
                                break;
                            }
                            case MODO_LOCALIZACION_LOCALIDAD:
                            {
                                $html .= $cadena_inicio_parametro.$idiomas->_("Localidad").": ".htmlspecialchars($localidad_tipo_meteorologico, ENT_QUOTES).$cadena_fin_parametro;
                                $html .= $cadena_inicio_parametro.$idiomas->_("País").": ".htmlspecialchars($pais_tipo_meteorologico, ENT_QUOTES).$cadena_fin_parametro;
                                break;
                            }
                            case MODO_LOCALIZACION_IDEMA:
                            {
                                $html .= $cadena_inicio_parametro.$idiomas->_("Identificador de estación meteorológica").": ".htmlspecialchars($idema_tipo_meteorologico, ENT_QUOTES).$cadena_fin_parametro;
                                break;
                            }
                            default:
                            {
                                throw new Exception("Modo de localización meteorológica desconocido: '".$modo_localizacion_meteorologica_http_emios_tipo_meteorologico."'");
                            }
                        }
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de sensor externo HTTP Emios desconocido: '".$tipo_http_emios."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO:
            {
                $direccion_ip = $parametros_opciones_valores_externo["direccion_ip"];
                $puerto = $parametros_opciones_valores_externo["puerto"];
                $nombre_dispositivo = $parametros_opciones_valores_externo["nombre_dispositivo"];
                $nombre_variable = $parametros_opciones_valores_externo["nombre_variable"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Dirección IP").": ".$direccion_ip.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Puerto").": ".$puerto.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Nombre de dispositivo").": ".htmlspecialchars($nombre_dispositivo, ENT_QUOTES).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Nombre de variable").": ".htmlspecialchars($nombre_variable, ENT_QUOTES).$cadena_fin_parametro;
                break;
            }
            case CLASE_SENSOR_EXTERNO_MODBUS_IP:
            {
                $tipos_registros = $parametros_opciones_valores_externo["tipos_registros"];
                $direcciones_dispositivos = $parametros_opciones_valores_externo["direcciones_dispositivos"];
                $direcciones_registros = $parametros_opciones_valores_externo["direcciones_registros"];
                $numeros_elementos = $parametros_opciones_valores_externo["numeros_elementos"];
                $reversos_bytes = $parametros_opciones_valores_externo["reversos_bytes"];
                $reversos_registros = $parametros_opciones_valores_externo["reversos_registros"];
                $tipos_datos = $parametros_opciones_valores_externo["tipos_datos"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Tipos de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Direcciones de dispositivos").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $direcciones_dispositivos).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Direcciones de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $direcciones_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Números de elementos").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $numeros_elementos).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Reversos de bytes").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $reversos_bytes).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Reversos de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $reversos_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Tipos de dato").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_datos).$cadena_fin_parametro;
                break;
            }
            case CLASE_SENSOR_EXTERNO_WIBEEE:
            {
                $tipo_dato = $parametros_opciones_valores_externo["tipo_dato"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Tipo de datos").": ".dame_descripcion_tipo_dato_wibeee($tipo_dato).$cadena_fin_parametro;
                break;
            }
            case CLASE_SENSOR_EXTERNO_API:
            {
                $api_seleccionada = $parametros_opciones_valores_externo["api_seleccionada"];

                switch ($api_seleccionada)
                {
                    case 'API_AXONTIME':
                    {
                        $cups_id = $parametros_opciones_valores_externo["cups_id"];
                        $tipo_curva = $parametros_opciones_valores_externo["tipo_curva"];
                        $tipo_energia = $parametros_opciones_valores_externo["tipo_energia"];

                        $html .= $cadena_inicio_parametro.$idiomas->_("CUPS ID").": ".$cups_id.$cadena_fin_parametro;
                        $html .= $cadena_inicio_parametro.$idiomas->_("Tipo de curva").": ".htmlspecialchars($tipo_curva, ENT_QUOTES).$cadena_fin_parametro;
                        $html .= $cadena_inicio_parametro.$idiomas->_("Tipo de energía").": ".htmlspecialchars($tipo_energia, ENT_QUOTES).$cadena_fin_parametro;

                        break;
                    }

                    case 'API_SGCLIMA':
                    {
                        $id_localizacion = $parametros_opciones_valores_externo["id_localizacion"];
                        $id_parametro = $parametros_opciones_valores_externo["id_parametro"];

                        $html .= $cadena_inicio_parametro.$idiomas->_("ID Localización").": ".htmlspecialchars($id_localizacion, ENT_QUOTES).$cadena_fin_parametro;
                        $html .= $cadena_inicio_parametro.$idiomas->_("ID Parámetro").": ".htmlspecialchars($id_parametro, ENT_QUOTES).$cadena_fin_parametro;

                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de sensor externo HTTP Emios desconocido: '".$tipo_http_emios."'");
                    }
                }
                break;
            }

            default:
            {
                throw new Exception("Clase de sensor externo desconocida: '".$clase_sensor_externo."'");
            }
        }
        $html .= $cadena_fin_lista_parametros;
        return ($html);
    }
?>
