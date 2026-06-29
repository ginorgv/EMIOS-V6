<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_sensores_software.php');


    // Constantes

    // Indices de parámetros de clase de interfaz de sensor 'asíncrono serie'
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_UBICACION_INTERFAZ_TIPO_PUERTO_SERIE", 0);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_UBICACION_INTERFAZ_VELOCIDAD", 1);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_UBICACION_INTERFAZ_NUMERO_BITS_PARADA", 2);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_UBICACION_INTERFAZ_PARIDAD", 3);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_UBICACION_INTERFAZ_PROTOCOLO", 4);

    define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_OPCIONES_INTERFAZ_NUMERO_REGISTRO", 0);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_OPCIONES_INTERFAZ_CADUCIDAD", 1);

    // Indices de parámetros de clase de interfaz de sensor 'HTTP ABBODINCEM'
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM_UBICACION_INTERFAZ_DIRECCION_IP", 0);

    // Indices de parámetros de clase de interfaz de sensor 'IEC 102 serie'
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_VELOCIDAD", 0);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_NUMERO_BITS_PARADA", 1);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_PARIDAD", 2);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_DIRECCION_ENLACE", 3);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_PUNTO_MEDIDA", 4);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_CLAVE", 5);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_COMANDOS_ENLACE", 6);

	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_OPCIONES_INTERFAZ_VALOR", 0);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_OPCIONES_INTERFAZ_CADUCIDAD", 1);

    // Indices de parámetros de clase de interfaz de sensor 'Modbus serie'
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_SERIE_UBICACION_INTERFAZ_ENCAPSULADO", 0);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_SERIE_UBICACION_INTERFAZ_VELOCIDAD", 1);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_SERIE_UBICACION_INTERFAZ_NUMERO_BITS_PARADA", 2);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_SERIE_UBICACION_INTERFAZ_PARIDAD", 3);

	// Indices de parámetros de clase de interfaz de sensor 'Modbus IP'
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_IP_UBICACION_INTERFAZ_ENCAPSULADO", 0);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_IP_UBICACION_INTERFAZ_PROTOCOLO", 1);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_IP_UBICACION_INTERFAZ_DIRECCION_IP", 2);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_IP_UBICACION_INTERFAZ_PUERTO", 3);


    //
    // Funciones para devolver controles de interfaces de sensor
    //


    // Devuelve los controles de configuración correspondientes a la clase de interfaz especificada
    function dame_controles_clase_interfaz_sensor($clase_interfaz_sensor, $ubicacion_interfaz, $opciones_interfaz)
    {
        switch ($clase_interfaz_sensor)
        {
            case CLASE_NINGUNA:
            {
                $controles = "";
                break;
            }
            case CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE:
            {
                $controles = dame_controles_clase_interfaz_sensor_asincrono_serie($ubicacion_interfaz, $opciones_interfaz);
                break;
            }
            case CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM:
            {
                $controles = dame_controles_clase_interfaz_sensor_http_abbodincem($ubicacion_interfaz, $opciones_interfaz);
                break;
            }
            case CLASE_INTERFAZ_SENSOR_IEC102_SERIE:
            {
                $controles = dame_controles_clase_interfaz_sensor_iec102_serie($ubicacion_interfaz, $opciones_interfaz);
                break;
            }
            case CLASE_INTERFAZ_SENSOR_MODBUS_SERIE:
            {
                $controles = dame_controles_clase_interfaz_sensor_modbus_serie($ubicacion_interfaz, $opciones_interfaz);
                break;
            }
            case CLASE_INTERFAZ_SENSOR_MODBUS_IP:
            {
                $controles = dame_controles_clase_interfaz_sensor_modbus_ip($ubicacion_interfaz, $opciones_interfaz);
                break;
            }
            case CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS:
            {
                $controles = dame_controles_clase_interfaz_sensor_valores_aleatorios($ubicacion_interfaz, $opciones_interfaz);
                break;
            }
            case CLASE_INTERFAZ_SENSOR_VALORES_FIJOS:
            {
                $controles = dame_controles_clase_interfaz_sensor_valores_fijos($ubicacion_interfaz, $opciones_interfaz);
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de sensor desconocida: '".$clase_interfaz_sensor."'");
            }
        }
        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de interfaz 'Asíncrono serie'
    function dame_controles_clase_interfaz_sensor_asincrono_serie($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if ($ubicacion_interfaz !== NULL)
        {
            // Parámetros de ubicación de interfaz
            $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_sensor(
                CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE,
                $ubicacion_interfaz);
            $tipo_puerto_serie = $parametros_ubicacion_interfaz["tipo_puerto_serie"];
            $velocidad = $parametros_ubicacion_interfaz["velocidad"];
            $numero_bits_parada = $parametros_ubicacion_interfaz["numero_bits_parada"];
            $paridad = $parametros_ubicacion_interfaz["paridad"];
            $protocolo = $parametros_ubicacion_interfaz["protocolo"];

            // Parámetros de opciones de interfaz
            $parametros_opciones_interfaz = dame_nombres_valores_parametros_opciones_interfaz_sensor(
                CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE,
                $opciones_interfaz);
            $numero_registro = $parametros_opciones_interfaz["numero_registro"];
            $caducidad = $parametros_opciones_interfaz["caducidad"];
        }
        else
        {
            // Valores por defecto de listas desplegables cuando se añade un sensor
            $tipo_puerto_serie = TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_UART;
            $numero_bits_parada = 1;
            $paridad = PARIDAD_PUERTO_SERIE_NINGUNA;
            $protocolo = PROTOCOLO_API_XBEE;
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Puerto serie").": "."</span><br/>
                    <select id='tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor' class='select-administracion'>";
        $controles .= dame_lista_tipos_puerto_serie_clase_interfaz_sensor_asincrono_serie($tipo_puerto_serie);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Velocidad").": "."</span><br/>
                    <input type='text' id='velocidad_clase_interfaz_asincrono_serie_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$velocidad."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Bits de parada").": "."</span><br/>
                    <select id='numero_bits_parada_clase_interfaz_asincrono_serie_sensor' class='select-administracion'>";
        $controles .= dame_lista_numeros_bits_parada_puerto_serie($numero_bits_parada);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Paridad").": "."</span><br/>
                    <select id='paridad_clase_interfaz_asincrono_serie_sensor' class='select-administracion'>";
        $controles .= dame_lista_paridades_puerto_serie($paridad);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Protocolo").": "."</span><br/>
                    <select id='protocolo_clase_interfaz_asincrono_serie_sensor' class='select-administracion'>";
        $controles .= dame_lista_protocolos_clase_interfaz_sensor_asincrono_serie($protocolo);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de registro").": "."</span><br/>
                    <input type='text' id='numero_registro_clase_interfaz_asincrono_serie_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_registro."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Caducidad")." (".$idiomas->_("s")."): "."</span><br/>
                    <input type='text' id='caducidad_clase_interfaz_asincrono_serie_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$caducidad."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de interfaz 'Abbodincem'
    function dame_controles_clase_interfaz_sensor_http_abbodincem($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de ubicación de interfaz
            $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_sensor(
                CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM,
                $ubicacion_interfaz);
            $direccion_ip = $parametros_ubicacion_interfaz["direccion_ip"];
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección IP").": "."</span><br/>
                    <input type='text' id='direccion_ip_clase_interfaz_http_abbodincem_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($direccion_ip, ENT_QUOTES)."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de interfaz 'IEC 102 serie'
    function dame_controles_clase_interfaz_sensor_iec102_serie($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de ubicación de interfaz
            $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_sensor(
                CLASE_INTERFAZ_SENSOR_IEC102_SERIE,
                $ubicacion_interfaz);
            $velocidad = $parametros_ubicacion_interfaz["velocidad"];
            $numero_bits_parada = $parametros_ubicacion_interfaz["numero_bits_parada"];
            $paridad = $parametros_ubicacion_interfaz["paridad"];
            $direccion_enlace = $parametros_ubicacion_interfaz["direccion_enlace"];
            $punto_medida = $parametros_ubicacion_interfaz["punto_medida"];
            $clave = $parametros_ubicacion_interfaz["clave"];
            $comandos_enlace = $parametros_ubicacion_interfaz["comandos_enlace"];

            // Parámetros de opciones de interfaz
            $parametros_opciones_interfaz = dame_nombres_valores_parametros_opciones_interfaz_sensor(
                CLASE_INTERFAZ_SENSOR_IEC102_SERIE,
                $opciones_interfaz);
            $valor = $parametros_opciones_interfaz["valor"];
            $caducidad = $parametros_opciones_interfaz["caducidad"];
        }
        else
        {
            // Valores por defecto de listas desplegables cuando se añade un sensor
            $numero_bits_parada = 1;
            $paridad = PARIDAD_PUERTO_SERIE_NINGUNA;
            $valor = VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_ACTIVA_IMPORTADA;
            $comandos_enlace = VALOR_NO;
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Velocidad").": "."</span><br/>
                    <input type='text' id='velocidad_clase_interfaz_iec102_serie_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$velocidad."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Bits de parada").": "."</span><br/>
                    <select id='numero_bits_parada_clase_interfaz_iec102_serie_sensor' class='select-administracion'>";
        $controles .= dame_lista_numeros_bits_parada_puerto_serie($numero_bits_parada);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Paridad").": "."</span><br/>
                    <select id='paridad_clase_interfaz_iec102_serie_sensor' class='select-administracion'>";
        $controles .= dame_lista_paridades_puerto_serie($paridad);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección de enlace").": "."</span><br/>
                    <input type='text' id='direccion_enlace_clase_interfaz_iec102_serie_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$direccion_enlace."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Punto de medida").": "."</span><br/>
                    <input type='text' id='punto_medida_clase_interfaz_iec102_serie_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$punto_medida."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clave").": "."</span><br/>
                    <input type='text' id='clave_clase_interfaz_iec102_serie_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$clave."'>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Comandos de enlace").": "."</span><br/>
					<select id='comandos_enlace_clase_interfaz_iec102_serie_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($comandos_enlace);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valor").": "."</span><br/>
                    <select id='id_valor_clase_interfaz_iec102_serie_sensor' class='select-administracion'>";
        $controles .= dame_lista_valores_clase_interfaz_sensor_iec102_serie($valor);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Caducidad")." (".$idiomas->_("s")."): "."</span><br/>
                    <input type='text' id='caducidad_clase_interfaz_iec102_serie_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$caducidad."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de interfaz 'Modbus serie'
    function dame_controles_clase_interfaz_sensor_modbus_serie($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de ubicación de interfaz
            $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_sensor(
                CLASE_INTERFAZ_SENSOR_MODBUS_SERIE,
                $ubicacion_interfaz);
            $encapsulado = $parametros_ubicacion_interfaz["encapsulado"];
            $velocidad = $parametros_ubicacion_interfaz["velocidad"];
            $numero_bits_parada = $parametros_ubicacion_interfaz["numero_bits_parada"];
            $paridad = $parametros_ubicacion_interfaz["paridad"];

            // Nota: Los parámetros de opciones de interfaz se recuperan en su propia función
        }
        else
        {
            // Valores por defecto de listas desplegables cuando se añade un sensor
            $encapsulado = ENCAPSULADO_MODBUS_RTU;
            $numero_bits_parada = 1;
            $paridad = PARIDAD_PUERTO_SERIE_NINGUNA;
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Encapsulado").": "."</span><br/>
                    <select id='encapsulado_clase_interfaz_modbus_serie_sensor' class='select-administracion'>";
        $controles .= dame_lista_encapsulados_modbus_serie($encapsulado);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Velocidad").": "."</span><br/>
                    <input type='text' id='velocidad_clase_interfaz_modbus_serie_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$velocidad."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Bits de parada").": "."</span><br/>
                    <select id='numero_bits_parada_clase_interfaz_modbus_serie_sensor' class='select-administracion'>";
        $controles .= dame_lista_numeros_bits_parada_puerto_serie($numero_bits_parada);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Paridad").": "."</span><br/>
                    <select id='paridad_clase_interfaz_modbus_serie_sensor' class='select-administracion'>";
        $controles .= dame_lista_paridades_puerto_serie($paridad);
        $controles .= "
                    </select>
                </div>
            </div>";

        $controles .= dame_controles_opciones_sensor_modbus("clase_interfaz_modbus_sensor", $opciones_interfaz);
        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de interfaz 'Modbus IP'
    function dame_controles_clase_interfaz_sensor_modbus_ip($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de ubicación de interfaz
            $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_sensor(
                CLASE_INTERFAZ_SENSOR_MODBUS_IP,
                $ubicacion_interfaz);
            $encapsulado = $parametros_ubicacion_interfaz["encapsulado"];
            $protocolo = $parametros_ubicacion_interfaz["protocolo"];
            $direccion_ip = $parametros_ubicacion_interfaz["direccion_ip"];
            $puerto = $parametros_ubicacion_interfaz["puerto"];

            // Nota: Los parámetros de opciones de interfaz se recuperan en su propia función
        }
        else
        {
            // Valores por defecto de listas desplegables cuando se añade un sensor
            $encapsulado = ENCAPSULADO_MODBUS_TCP;
            $protocolo = PROTOCOLO_TCP;
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Encapsulado").": "."</span><br/>
                    <select id='encapsulado_clase_interfaz_modbus_ip_sensor' class='select-administracion'>";
        $controles .= dame_lista_encapsulados_modbus_ip($encapsulado);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Protocolo").": "."</span><br/>
                    <select id='protocolo_clase_interfaz_modbus_ip_sensor' class='select-administracion'>";
        $controles .= dame_lista_protocolos_ip($protocolo);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección IP").": "."</span><br/>
                    <input type='text' id='direccion_ip_clase_interfaz_modbus_ip_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($direccion_ip, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Puerto").": "."</span><br/>
                    <input type='text' id='puerto_clase_interfaz_modbus_ip_sensor'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$puerto."'>
                </div>
            </div>";

        $controles .= dame_controles_opciones_sensor_modbus("clase_interfaz_modbus_sensor", $opciones_interfaz);
        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de interfaz 'Valores aleatorios'
    function dame_controles_clase_interfaz_sensor_valores_aleatorios($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de opciones de interfaz
            $parametros_opciones_interfaz = dame_nombres_valores_parametros_opciones_interfaz_sensor(
                CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS,
                $opciones_interfaz);
            $valores_aleatorios = $parametros_opciones_interfaz["valores_aleatorios"];
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valores").": "."</span><br/>
                    <input type='text' id='valores_clase_interfaz_valores_aleatorios_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$valores_aleatorios."'>
                    <span id='boton_sensores_ayuda_valores_aleatorios_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a la clase de interfaz 'Valores fijos'
    function dame_controles_clase_interfaz_sensor_valores_fijos($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de opciones de interfaz
            $parametros_opciones_interfaz = dame_nombres_valores_parametros_opciones_interfaz_sensor(
                CLASE_INTERFAZ_SENSOR_VALORES_FIJOS,
                $opciones_interfaz);
            $valores_fijos = $parametros_opciones_interfaz["valores_fijos"];
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valores").": "."</span><br/>
                    <input type='text' id='valores_clase_interfaz_valores_fijos_sensor'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$valores_fijos."'>
                </div>
            </div>";

        return ($controles);
    }


    //
    // Funciones de parámetros de interfaces de sensores
    //


    function dame_nombres_valores_parametros_ubicacion_interfaz_sensor($clase_interfaz_sensor, $cadena_ubicacion_interfaz)
    {
        // Se recuperan los parámetros de ubicación de interfaz
        $parametros_ubicacion_interfaz = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ubicacion_interfaz);
        $nombres_valores_parametros_ubicacion_interfaz_sensor = array();
        switch ($clase_interfaz_sensor)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE:
            {
                $tipo_puerto_serie = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_UBICACION_INTERFAZ_TIPO_PUERTO_SERIE];
                $velocidad = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_UBICACION_INTERFAZ_VELOCIDAD];
                $numero_bits_parada = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_UBICACION_INTERFAZ_NUMERO_BITS_PARADA];
                $paridad = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_UBICACION_INTERFAZ_PARIDAD];
                $protocolo = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_UBICACION_INTERFAZ_PROTOCOLO];
                $nombres_valores_parametros_ubicacion_interfaz_sensor["tipo_puerto_serie"] = $tipo_puerto_serie;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["velocidad"] = $velocidad;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["numero_bits_parada"] = $numero_bits_parada;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["paridad"] = $paridad;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["protocolo"] = $protocolo;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM:
            {
                $direccion_ip = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM_UBICACION_INTERFAZ_DIRECCION_IP];
                $nombres_valores_parametros_ubicacion_interfaz_sensor["direccion_ip"] = $direccion_ip;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_IEC102_SERIE:
            {
                $velocidad = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_VELOCIDAD];
                $numero_bits_parada = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_NUMERO_BITS_PARADA];
                $paridad = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_PARIDAD];
                $direccion_enlace = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_DIRECCION_ENLACE];
                $punto_medida = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_PUNTO_MEDIDA];
                $clave = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_CLAVE];
                $comandos_enlace = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_UBICACION_INTERFAZ_COMANDOS_ENLACE];
                $nombres_valores_parametros_ubicacion_interfaz_sensor["velocidad"] = $velocidad;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["numero_bits_parada"] = $numero_bits_parada;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["paridad"] = $paridad;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["direccion_enlace"] = $direccion_enlace;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["punto_medida"] = $punto_medida;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["clave"] = $clave;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["comandos_enlace"] = $comandos_enlace;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_MODBUS_SERIE:
            {
                $encapsulado = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_SERIE_UBICACION_INTERFAZ_ENCAPSULADO];
                $velocidad = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_SERIE_UBICACION_INTERFAZ_VELOCIDAD];
                $numero_bits_parada = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_SERIE_UBICACION_INTERFAZ_NUMERO_BITS_PARADA];
                $paridad = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_SERIE_UBICACION_INTERFAZ_PARIDAD];
                $nombres_valores_parametros_ubicacion_interfaz_sensor["encapsulado"] = $encapsulado;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["velocidad"] = $velocidad;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["numero_bits_parada"] = $numero_bits_parada;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["paridad"] = $paridad;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_MODBUS_IP:
            {
                $encapsulado = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_IP_UBICACION_INTERFAZ_ENCAPSULADO];
                $protocolo = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_IP_UBICACION_INTERFAZ_PROTOCOLO];
                $direccion_ip = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_IP_UBICACION_INTERFAZ_DIRECCION_IP];
                $puerto = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_MODBUS_IP_UBICACION_INTERFAZ_PUERTO];
                $nombres_valores_parametros_ubicacion_interfaz_sensor["encapsulado"] = $encapsulado;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["protocolo"] = $protocolo;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["direccion_ip"] = $direccion_ip;
                $nombres_valores_parametros_ubicacion_interfaz_sensor["puerto"] = $puerto;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS:
            case CLASE_INTERFAZ_SENSOR_VALORES_FIJOS:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de sensor desconocida: '".$clase_interfaz_sensor."'");
            }
        }
        return ($nombres_valores_parametros_ubicacion_interfaz_sensor);
    }


    function dame_nombres_valores_parametros_opciones_interfaz_sensor($clase_interfaz_sensor, $cadena_opciones_interfaz)
    {
        // Se recuperan los parámetros de opciones de interfaz
        $nombres_valores_parametros_opciones_interfaz_sensor = array();
        switch ($clase_interfaz_sensor)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE:
            {
                $parametros_opciones_interfaz = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_interfaz);
                $numero_registro = $parametros_opciones_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_OPCIONES_INTERFAZ_NUMERO_REGISTRO];
                $caducidad = $parametros_opciones_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE_OPCIONES_INTERFAZ_CADUCIDAD];
                $nombres_valores_parametros_opciones_interfaz_sensor["numero_registro"] = $numero_registro;
                $nombres_valores_parametros_opciones_interfaz_sensor["caducidad"] = $caducidad;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM:
            {
                // Sin parámetros de opciones de interfaz
                break;
            }
            case CLASE_INTERFAZ_SENSOR_IEC102_SERIE:
            {
                $parametros_opciones_interfaz = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_interfaz);
                $valor = $parametros_opciones_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_OPCIONES_INTERFAZ_VALOR];
                $caducidad = $parametros_opciones_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_SENSOR_IEC_102_SERIE_OPCIONES_INTERFAZ_CADUCIDAD];
                $nombres_valores_parametros_opciones_interfaz_sensor["valor"] = $valor;
                $nombres_valores_parametros_opciones_interfaz_sensor["caducidad"] = $caducidad;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_MODBUS_SERIE:
            case CLASE_INTERFAZ_SENSOR_MODBUS_IP:
            {
                $nombres_valores_parametros_opciones_interfaz_sensor = dame_nombres_valores_parametros_sensor_modbus($cadena_opciones_interfaz);
                break;
            }
            case CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS:
            {
                $valores_aleatorios = str_replace(SEPARADOR_PARAMETROS_VALORES, " ".SEPARADOR_PARAMETROS_VALORES." ", $cadena_opciones_interfaz);
                $nombres_valores_parametros_opciones_interfaz_sensor["valores_aleatorios"] = $valores_aleatorios;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_VALORES_FIJOS:
            {
                $valores_fijos = str_replace(SEPARADOR_PARAMETROS_VALORES, " ".SEPARADOR_PARAMETROS_VALORES." ", $cadena_opciones_interfaz);
                $nombres_valores_parametros_opciones_interfaz_sensor["valores_fijos"] = $valores_fijos;
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de sensor desconocida: '".$clase_interfaz_sensor."'");
            }
        }
        return ($nombres_valores_parametros_opciones_interfaz_sensor);
    }


    function dame_descripcion_parametros_ubicacion_interfaz_sensor(
        $clase_interfaz_sensor,
        $cadena_ubicacion_interfaz,
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

        // Parámetros de ubicación de interfaz
        $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_sensor(
            $clase_interfaz_sensor,
            $cadena_ubicacion_interfaz);
        $html = $cadena_inicio_lista_parametros;
        switch ($clase_interfaz_sensor)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE:
            {
                $tipo_puerto_serie = $parametros_ubicacion_interfaz["tipo_puerto_serie"];
                $velocidad = $parametros_ubicacion_interfaz["velocidad"];
                $numero_bits_parada = $parametros_ubicacion_interfaz["numero_bits_parada"];
                $paridad = $parametros_ubicacion_interfaz["paridad"];
                $protocolo = $parametros_ubicacion_interfaz["protocolo"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Puerto serie").": ".dame_descripcion_tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor($tipo_puerto_serie).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Velocidad").": ".$velocidad.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Bits de parada").": ".$numero_bits_parada.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Paridad").": ".dame_descripcion_paridad_puerto_serie($paridad).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Protocolo").": ".dame_descripcion_protocolo_serie($protocolo).$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM:
            {
                $direccion_ip = $parametros_ubicacion_interfaz["direccion_ip"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Dirección IP").": ".$direccion_ip.$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_IEC102_SERIE:
            {
                $velocidad = $parametros_ubicacion_interfaz["velocidad"];
                $numero_bits_parada = $parametros_ubicacion_interfaz["numero_bits_parada"];
                $paridad = $parametros_ubicacion_interfaz["paridad"];
                $direccion_enlace = $parametros_ubicacion_interfaz["direccion_enlace"];
                $punto_medida = $parametros_ubicacion_interfaz["punto_medida"];
                $clave = $parametros_ubicacion_interfaz["clave"];
                $comandos_enlace = $parametros_ubicacion_interfaz["comandos_enlace"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Velocidad").": ".$velocidad.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Bits de parada").": ".$numero_bits_parada.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Paridad").": ".dame_descripcion_paridad_puerto_serie($paridad).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Dirección de enlace").": ".$direccion_enlace.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Punto de medida").": ".$punto_medida.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Clave").": ".$clave.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Comandos de enlace").": ".dame_descripcion_valores_si_no($comandos_enlace).$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_MODBUS_SERIE:
            {
                $encapsulado = $parametros_ubicacion_interfaz["encapsulado"];
                $velocidad = $parametros_ubicacion_interfaz["velocidad"];
                $numero_bits_parada = $parametros_ubicacion_interfaz["numero_bits_parada"];
                $paridad = $parametros_ubicacion_interfaz["paridad"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Encapsulado").": ".dame_descripcion_encapsulado_modbus($encapsulado).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Velocidad").": ".$velocidad.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Bits de parada").": ".$numero_bits_parada.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Paridad").": ".dame_descripcion_paridad_puerto_serie($paridad).$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_MODBUS_IP:
            {
                $encapsulado = $parametros_ubicacion_interfaz["encapsulado"];
                $protocolo = $parametros_ubicacion_interfaz["protocolo"];
                $direccion_ip = $parametros_ubicacion_interfaz["direccion_ip"];
                $puerto = $parametros_ubicacion_interfaz["puerto"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Encapsulado").": ".dame_descripcion_encapsulado_modbus($encapsulado).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Protocolo").": ".dame_descripcion_protocolo_ip($protocolo).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Dirección IP").": ".$direccion_ip.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Puerto").": ".$puerto.$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS:
            case CLASE_INTERFAZ_SENSOR_VALORES_FIJOS:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de sensor desconocida: '".$clase_interfaz_sensor."'");
            }
        }
        $html .= $cadena_fin_lista_parametros;
        return ($html);
    }


    function dame_descripcion_parametros_opciones_interfaz_sensor(
        $clase_interfaz_sensor,
        $cadena_opciones_interfaz,
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

        // Parámetros de opciones de interfaz
        $parametros_opciones_interfaz = dame_nombres_valores_parametros_opciones_interfaz_sensor(
            $clase_interfaz_sensor,
            $cadena_opciones_interfaz);
        $html = $cadena_inicio_lista_parametros;
        switch ($clase_interfaz_sensor)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE:
            {
                $numero_registro = $parametros_opciones_interfaz["numero_registro"];
                $caducidad = $parametros_opciones_interfaz["caducidad"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Número de registro").": ".$numero_registro.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Caducidad")." (".$idiomas->_("s")."): ".$caducidad.$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM:
            {
                // Sin parámetros de opciones de interfaz
                break;
            }
            case CLASE_INTERFAZ_SENSOR_IEC102_SERIE:
            {
                $valor = $parametros_opciones_interfaz["valor"];
                $caducidad = $parametros_opciones_interfaz["caducidad"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Valor").": ".dame_lista_valores_clase_interfaz_sensor_iec102_serie($valor).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Caducidad")." (".$idiomas->_("s")."): ".$caducidad.$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_MODBUS_SERIE:
            case CLASE_INTERFAZ_SENSOR_MODBUS_IP:
            {
                $tipos_registros = $parametros_opciones_interfaz["tipos_registros"];
                $direcciones_dispositivos = $parametros_opciones_interfaz["direcciones_dispositivos"];
                $direcciones_registros = $parametros_opciones_interfaz["direcciones_registros"];
                $numeros_elementos = $parametros_opciones_interfaz["numeros_elementos"];
                $reversos_bytes = $parametros_opciones_interfaz["reversos_bytes"];
                $reversos_registros = $parametros_opciones_interfaz["reversos_registros"];
                $tipos_datos = $parametros_opciones_interfaz["tipos_datos"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Tipos de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Direcciones de dispositivos").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $direcciones_dispositivos).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Direcciones de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $direcciones_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Números de elementos").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $numeros_elementos).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Reversos de bytes").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $reversos_bytes).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Reversos de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $reversos_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Tipos de dato").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_datos).$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS:
            {
                $valores_aleatorios = $parametros_opciones_interfaz["valores_aleatorios"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Valores").": ".$valores_aleatorios.$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_SENSOR_VALORES_FIJOS:
            {
                $valores_fijos = $parametros_opciones_interfaz["valores_fijos"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Valores").": ".$valores_fijos.$cadena_fin_parametro;
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de sensor desconocida: '".$clase_interfaz_sensor."'");
            }
        }
        $html .= $cadena_fin_lista_parametros;
        return ($html);
    }


    //
    // Funciones auxiliares
    //


    function dame_lista_tipos_puerto_serie_clase_interfaz_sensor_asincrono_serie($tipo_puerto_serie_seleccionado)
    {
        $tipos_puerto_serie = array();
        array_push($tipos_puerto_serie, array(
            "id" => TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_ARDUINO,
            "nombre" => dame_descripcion_tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor(TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_ARDUINO)));
        array_push($tipos_puerto_serie, array(
            "id" => TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_UART,
            "nombre" => dame_descripcion_tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor(TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_UART)));
        array_push($tipos_puerto_serie, array(
            "id" => TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_XBEE,
            "nombre" => dame_descripcion_tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor(TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_XBEE)));

        foreach ($tipos_puerto_serie as $tipo_puerto_serie)
        {
            $lista .= "<option value='".$tipo_puerto_serie['id']."'";
			if ($tipo_puerto_serie['id'] == $tipo_puerto_serie_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$tipo_puerto_serie['nombre']."</option>";
        }

        return ($lista);
    }


    function dame_descripcion_tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor($tipo_puerto_serie)
    {
        switch ($tipo_puerto_serie)
        {
            case TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_ARDUINO:
            {
                $descripcion = "Arduino";
                break;
            }
            case TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_UART:
            {
                $descripcion = "Uart";
                break;
            }
            case TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_XBEE:
            {
                $descripcion = "Xbee";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    function dame_lista_protocolos_clase_interfaz_sensor_asincrono_serie($protocolo_seleccionado)
    {
        $protocolos = array();
        array_push($protocolos, array(
            "id" => PROTOCOLO_EMIOS,
            "nombre" => dame_descripcion_protocolo_serie(PROTOCOLO_EMIOS)));
        array_push($protocolos, array(
            "id" => PROTOCOLO_API_XBEE,
            "nombre" => dame_descripcion_protocolo_serie(PROTOCOLO_API_XBEE)));

        foreach ($protocolos as $protocolo)
        {
            $lista .= "<option value='".$protocolo['id']."'";
			if ($protocolo['id'] == $protocolo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$protocolo['nombre']."</option>";
        }

        return ($lista);
    }


    function dame_lista_valores_clase_interfaz_sensor_iec102_serie($valor_seleccionado)
    {
        $valores = array();
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_ACTIVA_IMPORTADA,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_ACTIVA_IMPORTADA)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_ACTIVA_EXPORTADA,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_ACTIVA_EXPORTADA)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q1,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q1)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q2,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q2)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q3,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q3)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q4,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q4)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_TOTAL,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_TOTAL)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_TOTAL,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_TOTAL)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_TOTAL,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_TOTAL)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_I,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_I)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_I,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_I)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_I,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_I)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_II,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_II)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_II,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_II)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_II,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_II)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_III,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_III)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_III,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_III)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_III,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_III)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_I,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_I)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_I,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_I)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_II,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_II)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_II,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_II)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_III,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_III)));
        array_push($valores, array(
            "id" => VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_III,
            "nombre" => dame_descripcion_valor_clase_interfaz_iec102_serie_sensor(VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_III)));

        foreach ($valores as $valor)
        {
            $lista .= "<option value='".$valor['id']."'";
			if ($valor['id'] == $valor_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$valor['nombre']."</option>";
        }

        return ($lista);
    }


    function dame_descripcion_valor_clase_interfaz_iec102_serie_sensor($valor)
    {
        $idiomas = new Idiomas();
        switch ($valor)
        {
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_ACTIVA_IMPORTADA:
            {
                $descripcion = $idiomas->_("Energía activa importada")." (".$idiomas->_("kWh").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_ACTIVA_EXPORTADA:
            {
                $descripcion = $idiomas->_("Energía activa exportada")." (".$idiomas->_("kWh").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q1:
            {
                $descripcion = $idiomas->_("Energía reactiva Q1")." (".$idiomas->_("kVArh").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q2:
            {
                $descripcion = $idiomas->_("Energía reactiva Q2")." (".$idiomas->_("kVArh").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q3:
            {
                $descripcion = $idiomas->_("Energía reactiva Q3")." (".$idiomas->_("kVArh").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_ENERGIA_REACTIVA_Q4:
            {
                $descripcion = $idiomas->_("Energía reactiva Q4")." (".$idiomas->_("kVArh").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_TOTAL:
            {
                $descripcion = $idiomas->_("Potencia activa total")." (".$idiomas->_("kW").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_TOTAL:
            {
                $descripcion = $idiomas->_("Potencia reactiva total")." (".$idiomas->_("kVAr").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_TOTAL:
            {
                $descripcion = $idiomas->_("Factor de potencia total")." (".$idiomas->_("Milésimas").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_I:
            {
                $descripcion = $idiomas->_("Potencia activa fase I")." (".$idiomas->_("kW").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_I:
            {
                $descripcion = $idiomas->_("Potencia reactiva fase I")." (".$idiomas->_("kVAr").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_I:
            {
                $descripcion = $idiomas->_("Factor de potencia fase I")." (".$idiomas->_("Milésimas").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_II:
            {
                $descripcion = $idiomas->_("Potencia activa fase II")." (".$idiomas->_("kW").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_II:
            {
                $descripcion = $idiomas->_("Potencia reactiva fase II")." (".$idiomas->_("kVAr").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_II:
            {
                $descripcion = $idiomas->_("Factor de potencia fase II")." (".$idiomas->_("Milésimas").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_ACTIVA_FASE_III:
            {
                $descripcion = $idiomas->_("Potencia activa fase III")." (".$idiomas->_("kW").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_POTENCIA_REACTIVA_FASE_III:
            {
                $descripcion = $idiomas->_("Potencia reactiva fase III")." (".$idiomas->_("kVAr").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_FACTOR_POTENCIA_FASE_III:
            {
                $descripcion = $idiomas->_("Factor de potencia fase III")." (".$idiomas->_("Milésimas").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_I:
            {
                $descripcion = $idiomas->_("Intensidad fase I")." (".$idiomas->_("dA").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_I:
            {
                $descripcion = $idiomas->_("Tensión fase I")." (".$idiomas->_("dV").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_II:
            {
                $descripcion = $idiomas->_("Intensidad fase II")." (".$idiomas->_("dA").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_II:
            {
                $descripcion = $idiomas->_("Tensión fase II")." (".$idiomas->_("dV").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_INTENSIDAD_FASE_III:
            {
                $descripcion = $idiomas->_("Intensidad fase III")." (".$idiomas->_("dA").")";
                break;
            }
            case VALOR_CLASE_INTERFAZ_IEC102_SERIE_TENSION_FASE_III:
            {
                $descripcion = $idiomas->_("Tensión fase III")." (".$idiomas->_("dV").")";
                break;
            }
            default:
            {
                $descripcion = $idiomas->_("Desconocido");
                break;
            }
        }

        return ($descripcion);
    }
?>
