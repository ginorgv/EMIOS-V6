<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    // Constantes

    // Indices de parámetros de clase de interfaz de actuador hardware 'Modbus IP'
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_IP_UBICACION_INTERFAZ_ENCAPSULADO", 0);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_IP_UBICACION_INTERFAZ_PROTOCOLO", 1);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_IP_UBICACION_INTERFAZ_DIRECCION_IP", 2);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_IP_UBICACION_INTERFAZ_PUERTO", 3);

    // Indices de parámetros de clase de interfaz de actuador hardware 'Modbus serie'
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_SERIE_UBICACION_INTERFAZ_ENCAPSULADO", 0);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_SERIE_UBICACION_INTERFAZ_VELOCIDAD", 1);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_SERIE_UBICACION_INTERFAZ_NUMERO_BITS_PARADA", 2);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_SERIE_UBICACION_INTERFAZ_PARIDAD", 3);

    // Indices de parámetros de clase de interfaz de actuador software 'E-mail'
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_EMAIL_OPCIONES_INTERFAZ_DIRECCION_REMITENTE", 0);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_EMAIL_OPCIONES_INTERFAZ_DIRECCIONES_DESTINO", 1);

    // Indices de parámetros de clase de interfaz de actuador software 'Modbus IP'
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_MODBUS_IP_UBICACION_INTERFAZ_ENCAPSULADO", 0);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_MODBUS_IP_UBICACION_INTERFAZ_PROTOCOLO", 1);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_MODBUS_IP_UBICACION_INTERFAZ_DIRECCION_IP", 2);
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_MODBUS_IP_UBICACION_INTERFAZ_PUERTO", 3);

    // Indices de parámetros de opciones de actuador Modbus
    define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_TIPO_REGISTRO", 0);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_DIRECCION_DISPOSITIVO", 1);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_DIRECCION_REGISTRO", 2);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_NUMERO_ELEMENTOS", 3);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_REVERSO_BYTES", 4);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_REVERSO_REGISTROS", 5);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_TIPO_DATO", 6);
	define("INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_NUMERO_BIT_INICIAL", 7);


    //
    // Funciones para devolver controles de interfaces de actuadores
    //


    // Devuelve los controles de configuración correspondientes a un actuador de la clase de interfaz especificada
    function dame_controles_clase_interfaz_actuador(
        $tipo_actuador,
        $clase_interfaz_actuador,
        $ubicacion_interfaz,
        $opciones_interfaz)
    {
        switch ($tipo_actuador)
        {
            case TIPO_NINGUNO:
            {
                $controles = "";
                break;
            }
            case TIPO_ACTUADOR_HARDWARE:
            {
                switch ($clase_interfaz_actuador)
                {
                    case CLASE_NINGUNA:
                    {
                        $controles = "";
                        break;
                    }
                    case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
                    {
                        $controles = dame_controles_clase_interfaz_actuador_hardware_modbus_ip($ubicacion_interfaz, $opciones_interfaz);
                        break;
                    }
                    case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE:
                    {
                        $controles = dame_controles_clase_interfaz_actuador_hardware_modbus_serie($ubicacion_interfaz, $opciones_interfaz);
                        break;
                    }
                    case CLASE_INTERFAZ_ACTUADOR_PWM:
                    {
                        $controles = dame_controles_clase_interfaz_actuador_hardware_pwm($ubicacion_interfaz, $opciones_interfaz);
                        break;
                    }
                    case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
                    {
                        $controles = dame_controles_clase_interfaz_actuador_hardware_simulado($ubicacion_interfaz, $opciones_interfaz);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Clase de interfaz de actuador desconocida: '".$clase_interfaz_actuador."'");
                    }
                }
                break;
            }
            case TIPO_ACTUADOR_SOFTWARE:
            {
                switch ($clase_interfaz_actuador)
                {
                    case CLASE_NINGUNA:
                    {
                        $controles = "";
                        break;
                    }
                    case CLASE_INTERFAZ_ACTUADOR_EMAIL:
                    {
                        $controles = dame_controles_clase_interfaz_actuador_software_email($ubicacion_interfaz, $opciones_interfaz);
                        break;
                    }
                    case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
                    {
                        $controles = dame_controles_clase_interfaz_actuador_software_modbus_ip($ubicacion_interfaz, $opciones_interfaz);
                        break;
                    }
                    case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
                    {
                        $controles = dame_controles_clase_interfaz_actuador_software_simulado($ubicacion_interfaz, $opciones_interfaz);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Clase de interfaz de actuador desconocida: '".$clase_interfaz_actuador."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Tipo de actuador desconocido: '".$tipo_actuador."'");
            }
        }
        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a un actuador hardware con la clase de interfaz 'Modbus IP'
    function dame_controles_clase_interfaz_actuador_hardware_modbus_ip($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de ubicación de interfaz
            $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_actuador_hardware(
                CLASE_INTERFAZ_ACTUADOR_MODBUS_IP,
                $ubicacion_interfaz);
            $encapsulado = $parametros_ubicacion_interfaz["encapsulado"];
            $protocolo = $parametros_ubicacion_interfaz["protocolo"];
            $direccion_ip = $parametros_ubicacion_interfaz["direccion_ip"];
            $puerto = $parametros_ubicacion_interfaz["puerto"];

            // Nota: Los parámetros de opciones de interfaz se recuperan en su propia función
        }
        else
        {
            // Valores por defecto de listas desplegables en pestaña interfaz cuando se añade un actuador
            $encapsulado = ENCAPSULADO_MODBUS_TCP;
            $protocolo = PROTOCOLO_TCP;
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Encapsulado").": "."</span><br/>
                    <select id='encapsulado_clase_interfaz_modbus_ip_hardware_actuador' class='select-administracion'>";
        $controles .= dame_lista_encapsulados_modbus_ip($encapsulado);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Protocolo").": "."</span><br/>
                    <select id='protocolo_clase_interfaz_modbus_ip_hardware_actuador' class='select-administracion'>";
        $controles .= dame_lista_protocolos_ip($protocolo);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección IP").": "."</span><br/>
                    <input type='text' id='direccion_ip_clase_interfaz_modbus_ip_hardware_actuador'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($direccion_ip, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Puerto").": "."</span><br/>
                    <input type='text' id='puerto_clase_interfaz_modbus_ip_hardware_actuador'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$puerto."'>
                </div>
            </div>";
        $controles .= dame_controles_opciones_interfaz_clase_actuador_modbus("clase_interfaz_modbus_actuador", $opciones_interfaz);
        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a un actuador hardware con la clase de interfaz 'Modbus serie'
    function dame_controles_clase_interfaz_actuador_hardware_modbus_serie($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de ubicación de interfaz
            $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_actuador_hardware(
                CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE,
                $ubicacion_interfaz);
            $encapsulado = $parametros_ubicacion_interfaz["encapsulado"];
            $velocidad = $parametros_ubicacion_interfaz["velocidad"];
            $numero_bits_parada = $parametros_ubicacion_interfaz["numero_bits_parada"];
            $paridad = $parametros_ubicacion_interfaz["paridad"];

            // Nota: Los parámetros de opciones de interfaz se recuperan en su propia función
        }
        else
        {
            // Valores por defecto de listas desplegables en pestaña interfaz cuando se añade un actuador
            $encapsulado = ENCAPSULADO_MODBUS_RTU;
            $numero_bits_parada = 1;
            $paridad = PARIDAD_PUERTO_SERIE_NINGUNA;
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Encapsulado").": "."</span><br/>
                    <select id='encapsulado_clase_interfaz_modbus_serie_hardware_actuador' class='select-administracion'>";
        $controles .= dame_lista_encapsulados_modbus_serie($encapsulado);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Velocidad").": "."</span><br/>
                    <input type='text' id='velocidad_clase_interfaz_modbus_serie_hardware_actuador'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$velocidad."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Bits de parada").": "."</span><br/>
                    <select id='numero_bits_parada_clase_interfaz_modbus_serie_hardware_actuador' class='select-administracion'>";
        $controles .= dame_lista_numeros_bits_parada_puerto_serie($numero_bits_parada);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Paridad").": "."</span><br/>
                    <select id='paridad_clase_interfaz_modbus_serie_hardware_actuador' class='select-administracion'>";
        $controles .= dame_lista_paridades_puerto_serie($paridad);
        $controles .= "
                    </select>
                </div>
            </div>";
        $controles .= dame_controles_opciones_interfaz_clase_actuador_modbus("clase_interfaz_modbus_actuador", $opciones_interfaz);
        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a un actuador hardware con la clase de interfaz 'PWM'
    function dame_controles_clase_interfaz_actuador_hardware_pwm($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de opciones de interfaz
            $parametros_opciones_interfaz = dame_nombres_valores_parametros_opciones_interfaz_actuador_hardware(
                CLASE_INTERFAZ_ACTUADOR_PWM,
                $opciones_interfaz);
            $numeros_pines = $parametros_opciones_interfaz["numeros_pines"];
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Números de pines").": "."</span><br/>
                    <input type='text' id='numeros_pines_clase_interfaz_pwm_actuador'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$numeros_pines."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a un actuador hardware con la clase de interfaz 'Simulado'
    function dame_controles_clase_interfaz_actuador_hardware_simulado($ubicacion_interfaz, $opciones_interfaz)
    {
        $controles = "";
        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a un actuador software con la clase de interfaz 'E-mail'
    function dame_controles_clase_interfaz_actuador_software_email($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();
        $direccion_remitente = "info@energy-minus.es";
        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de opciones de interfaz
            $parametros_opciones_interfaz = dame_nombres_valores_parametros_opciones_interfaz_actuador_software(
                CLASE_INTERFAZ_ACTUADOR_EMAIL,
                $opciones_interfaz);
            $direccion_remitente = $parametros_opciones_interfaz["direccion_remitente"];
            $direcciones_destino = $parametros_opciones_interfaz["direcciones_destino"];
            $direcciones_destino = str_replace(" ", "", $direcciones_destino);
            $direcciones_destino = str_replace(SEPARADOR_DIRECCIONES_EMAIL, SEPARADOR_DIRECCIONES_EMAIL." ", $direcciones_destino);
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección e-mail del remitente").": "."</span><br/>
                    <input type='text' id='direccion_remitente_clase_interfaz_email_actuador'
                        class='TLNT_input_mandatory input-administracion' value='".$direccion_remitente."'readonly>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Direcciones e-mail de destino").": "."</span><br/>
                    <input type='text' id='direcciones_destino_clase_interfaz_email_actuador'
                        class='TLNT_input_mandatory input-administracion' value='".$direcciones_destino."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a un actuador software con la clase de interfaz 'Modbus IP'
    function dame_controles_clase_interfaz_actuador_software_modbus_ip($ubicacion_interfaz, $opciones_interfaz)
    {
        $idiomas = new Idiomas();

        if (($ubicacion_interfaz !== NULL) && ($opciones_interfaz !== NULL))
        {
            // Parámetros de ubicación de interfaz
            $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_actuador_software(
                CLASE_INTERFAZ_ACTUADOR_MODBUS_IP,
                $ubicacion_interfaz);
            $encapsulado = $parametros_ubicacion_interfaz["encapsulado"];
            $protocolo = $parametros_ubicacion_interfaz["protocolo"];
            $direccion_ip = $parametros_ubicacion_interfaz["direccion_ip"];
            $puerto = $parametros_ubicacion_interfaz["puerto"];

            // Nota: Los parámetros de opciones de interfaz se recuperan en su propia función
        }
        else
        {
            // Valores por defecto de listas desplegables en pestaña interfaz cuando se añade un actuador
            $encapsulado = ENCAPSULADO_MODBUS_RTU;
            $protocolo = PROTOCOLO_TCP;
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Encapsulado").": "."</span><br/>
                    <select id='encapsulado_clase_interfaz_modbus_ip_software_actuador' class='select-administracion'>";
        $controles .= dame_lista_encapsulados_modbus_ip($encapsulado);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Protocolo").": "."</span><br/>
                    <select id='protocolo_clase_interfaz_modbus_ip_software_actuador' class='select-administracion'>";
        $controles .= dame_lista_protocolos_ip($protocolo);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección IP").": "."</span><br/>
                    <input type='text' id='direccion_ip_clase_interfaz_modbus_ip_software_actuador'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($direccion_ip, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Puerto").": "."</span><br/>
                    <input type='text' id='puerto_clase_interfaz_modbus_ip_software_actuador'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$puerto."'>
                </div>
            </div>";
        $controles .= dame_controles_opciones_interfaz_clase_actuador_modbus("clase_interfaz_modbus_actuador", $opciones_interfaz);
        return ($controles);
    }


    // Devuelve los controles de configuración correspondientes a un actuador hardware con la clase de interfaz 'Simulado'
    function dame_controles_clase_interfaz_actuador_software_simulado($ubicacion_interfaz, $opciones_interfaz)
    {
        $controles = "";
        return ($controles);
    }


    //
    // Funciones de parámetros de interfaces de actuadores
    //


    function dame_nombres_valores_parametros_ubicacion_interfaz_actuador_hardware($clase_interfaz_actuador, $cadena_ubicacion_interfaz)
    {
        // Se recuperan los parámetros de ubicación de interfaz
        $nombres_valores_parametros_ubicacion_interfaz_actuador = array();
        $parametros_ubicacion_interfaz = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ubicacion_interfaz);
        switch ($clase_interfaz_actuador)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
            {
                $encapsulado = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_IP_UBICACION_INTERFAZ_ENCAPSULADO];
                $protocolo = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_IP_UBICACION_INTERFAZ_PROTOCOLO];
                $direccion_ip = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_IP_UBICACION_INTERFAZ_DIRECCION_IP];
                $puerto = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_IP_UBICACION_INTERFAZ_PUERTO];
                $nombres_valores_parametros_ubicacion_interfaz_actuador["encapsulado"] = $encapsulado;
                $nombres_valores_parametros_ubicacion_interfaz_actuador["protocolo"] = $protocolo;
                $nombres_valores_parametros_ubicacion_interfaz_actuador["direccion_ip"] = $direccion_ip;
                $nombres_valores_parametros_ubicacion_interfaz_actuador["puerto"] = $puerto;
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE:
            {
                $encapsulado = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_SERIE_UBICACION_INTERFAZ_ENCAPSULADO];
                $velocidad = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_SERIE_UBICACION_INTERFAZ_VELOCIDAD];
                $numero_bits_parada = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_SERIE_UBICACION_INTERFAZ_NUMERO_BITS_PARADA];
                $paridad = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_HARDWARE_MODBUS_SERIE_UBICACION_INTERFAZ_PARIDAD];
                $nombres_valores_parametros_ubicacion_interfaz_actuador["encapsulado"] = $encapsulado;
                $nombres_valores_parametros_ubicacion_interfaz_actuador["velocidad"] = $velocidad;
                $nombres_valores_parametros_ubicacion_interfaz_actuador["numero_bits_parada"] = $numero_bits_parada;
                $nombres_valores_parametros_ubicacion_interfaz_actuador["paridad"] = $paridad;
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_PWM:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de actuador hardware desconocida: '".$clase_interfaz_actuador."'");
            }
        }
        return ($nombres_valores_parametros_ubicacion_interfaz_actuador);
    }


    function dame_nombres_valores_parametros_opciones_interfaz_actuador_hardware($clase_interfaz_actuador, $cadena_opciones_interfaz)
    {
        // Se recuperan los parámetros de opciones de interfaz
        $nombres_valores_parametros_opciones_interfaz_actuador = array();
        switch ($clase_interfaz_actuador)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE:
            {
                $nombres_valores_parametros_opciones_interfaz_actuador = dame_nombres_valores_parametros_actuador_modbus($cadena_opciones_interfaz);
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_PWM:
            {
                $numeros_pines = str_replace(SEPARADOR_PARAMETROS_VALORES, " ".SEPARADOR_PARAMETROS_VALORES." ", $cadena_opciones_interfaz);
                $nombres_valores_parametros_opciones_interfaz_actuador["numeros_pines"] = $numeros_pines;
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de actuador hardware desconocida: '".$clase_interfaz_actuador."'");
            }
        }
        return ($nombres_valores_parametros_opciones_interfaz_actuador);
    }


    function dame_nombres_valores_parametros_ubicacion_interfaz_actuador_software($clase_interfaz_actuador, $cadena_ubicacion_interfaz)
    {
        // Se recuperan los parámetros de ubicación de interfaz
        $nombres_valores_parametros_ubicacion_interfaz_actuador = array();
        $parametros_ubicacion_interfaz = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ubicacion_interfaz);
        switch ($clase_interfaz_actuador)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_EMAIL:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
            {
                $encapsulado = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_MODBUS_IP_UBICACION_INTERFAZ_ENCAPSULADO];
                $protocolo = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_MODBUS_IP_UBICACION_INTERFAZ_PROTOCOLO];
                $direccion_ip = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_MODBUS_IP_UBICACION_INTERFAZ_DIRECCION_IP];
                $puerto = $parametros_ubicacion_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_MODBUS_IP_UBICACION_INTERFAZ_PUERTO];
                $nombres_valores_parametros_ubicacion_interfaz_actuador["encapsulado"] = $encapsulado;
                $nombres_valores_parametros_ubicacion_interfaz_actuador["protocolo"] = $protocolo;
                $nombres_valores_parametros_ubicacion_interfaz_actuador["direccion_ip"] = $direccion_ip;
                $nombres_valores_parametros_ubicacion_interfaz_actuador["puerto"] = $puerto;
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de actuador software desconocida: '".$clase_interfaz_actuador."'");
            }
        }
        return ($nombres_valores_parametros_ubicacion_interfaz_actuador);
    }


    function dame_nombres_valores_parametros_opciones_interfaz_actuador_software($clase_interfaz_actuador, $cadena_opciones_interfaz)
    {
        // Se recuperan los parámetros de opciones de interfaz
        $nombres_valores_parametros_opciones_interfaz_actuador = array();
        switch ($clase_interfaz_actuador)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_EMAIL:
            {
                $parametros_opciones_interfaz = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_interfaz);
                $direccion_remitente = $parametros_opciones_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_EMAIL_OPCIONES_INTERFAZ_DIRECCION_REMITENTE];
                $direcciones_destino = $parametros_opciones_interfaz[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_SOFTWARE_EMAIL_OPCIONES_INTERFAZ_DIRECCIONES_DESTINO];
                $nombres_valores_parametros_opciones_interfaz_actuador["direccion_remitente"] = $direccion_remitente;
                $nombres_valores_parametros_opciones_interfaz_actuador["direcciones_destino"] = $direcciones_destino;
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
            {
                $nombres_valores_parametros_opciones_interfaz_actuador = dame_nombres_valores_parametros_actuador_modbus($cadena_opciones_interfaz);
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
            {
                // Sin parámetros de opciones de interfaz
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de actuador software desconocida: '".$clase_interfaz_actuador."'");
            }
        }
        return ($nombres_valores_parametros_opciones_interfaz_actuador);
    }


    function dame_descripcion_parametros_ubicacion_interfaz_actuador_hardware(
        $clase_interfaz_actuador,
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
        $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_actuador_hardware(
            $clase_interfaz_actuador,
            $cadena_ubicacion_interfaz);
        $html = $cadena_inicio_lista_parametros;
        switch ($clase_interfaz_actuador)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
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
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE:
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
            case CLASE_INTERFAZ_ACTUADOR_PWM:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de actuador desconocida: '".$clase_interfaz_actuador."'");
            }
        }
        $html .= $cadena_fin_lista_parametros;
        return ($html);
    }


    function dame_descripcion_parametros_opciones_interfaz_actuador_hardware(
        $clase_interfaz_actuador,
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
        $parametros_opciones_interfaz = dame_nombres_valores_parametros_opciones_interfaz_actuador_hardware(
            $clase_interfaz_actuador,
            $cadena_opciones_interfaz);
        $html = $cadena_inicio_lista_parametros;
        switch ($clase_interfaz_actuador)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE:
            {
                $tipos_registros = $parametros_opciones_interfaz["tipos_registros"];
                $direcciones_dispositivos = $parametros_opciones_interfaz["direcciones_dispositivos"];
                $direcciones_registros = $parametros_opciones_interfaz["direcciones_registros"];
                $numeros_elementos = $parametros_opciones_interfaz["numeros_elementos"];
                $reversos_bytes = $parametros_opciones_interfaz["reversos_bytes"];
                $reversos_registros = $parametros_opciones_interfaz["reversos_registros"];
                $tipos_datos = $parametros_opciones_interfaz["tipos_datos"];
                $numeros_bits_iniciales = $parametros_opciones_interfaz["numeros_bits_iniciales"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Tipos de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Direcciones de dispositivos").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $direcciones_dispositivos).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Direcciones de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $direcciones_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Números de elementos").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $numeros_elementos).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Reversos de bytes").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $reversos_bytes).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Reversos de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $reversos_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Tipos de dato").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_datos).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Números de bits iniciales").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $numeros_bits_iniciales).$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_PWM:
            {
                $numeros_pines = $parametros_opciones_interfaz["numeros_pines"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Números de pines").": ".$numeros_pines.$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
            {
                // Sin parámetros de opciones de interfaz
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de actuador desconocida: '".$clase_interfaz_actuador."'");
            }
        }
        $html .= $cadena_fin_lista_parametros;
        return ($html);
    }


    function dame_descripcion_parametros_ubicacion_interfaz_actuador_software(
        $clase_interfaz_actuador,
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
        $parametros_ubicacion_interfaz = dame_nombres_valores_parametros_ubicacion_interfaz_actuador_software(
            $clase_interfaz_actuador,
            $cadena_ubicacion_interfaz);
        $html = $cadena_inicio_lista_parametros;
        switch ($clase_interfaz_actuador)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_EMAIL:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
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
            case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
            {
                // Sin parámetros de ubicación de interfaz
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de actuador desconocida: '".$clase_interfaz_actuador."'");
            }
        }
        $html .= $cadena_fin_lista_parametros;
        return ($html);
    }


    function dame_descripcion_parametros_opciones_interfaz_actuador_software(
        $clase_interfaz_actuador,
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
        $parametros_opciones_interfaz = dame_nombres_valores_parametros_opciones_interfaz_actuador_software(
            $clase_interfaz_actuador,
            $cadena_opciones_interfaz);
        $html = $cadena_inicio_lista_parametros;
        switch ($clase_interfaz_actuador)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_EMAIL:
            {
                $direccion_remitente = $parametros_opciones_interfaz["direccion_remitente"];
                $direcciones_destino = $parametros_opciones_interfaz["direcciones_destino"];
                $direcciones_destino = str_replace(" ", "", $direcciones_destino);
                $direcciones_destino = str_replace(SEPARADOR_DIRECCIONES_EMAIL, SEPARADOR_DIRECCIONES_EMAIL." ", $direcciones_destino);

                $html .= $cadena_inicio_parametro.$idiomas->_("Dirección e-mail del remitente").": ".$direccion_remitente.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Direcciones e-mail de destino").": ".$direcciones_destino.$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
            {
                $tipos_registros = $parametros_opciones_interfaz["tipos_registros"];
                $direcciones_dispositivos = $parametros_opciones_interfaz["direcciones_dispositivos"];
                $direcciones_registros = $parametros_opciones_interfaz["direcciones_registros"];
                $numeros_elementos = $parametros_opciones_interfaz["numeros_elementos"];
                $reversos_bytes = $parametros_opciones_interfaz["reversos_bytes"];
                $reversos_registros = $parametros_opciones_interfaz["reversos_registros"];
                $tipos_datos = $parametros_opciones_interfaz["tipos_datos"];
                $numeros_bits_iniciales = $parametros_opciones_interfaz["numeros_bits_iniciales"];

                $html .= $cadena_inicio_parametro.$idiomas->_("Tipos de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Direcciones de dispositivos").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $direcciones_dispositivos).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Direcciones de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $direcciones_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Números de elementos").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $numeros_elementos).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Reversos de bytes").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $reversos_bytes).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Reversos de registros").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $reversos_registros).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Tipos de dato").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_datos).$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Números de bits iniciales").": ".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $numeros_bits_iniciales).$cadena_fin_parametro;
                break;
            }
            case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
            {
                // Sin parámetros de opciones de interfaz
                break;
            }
            default:
            {
                throw new Exception("Clase de interfaz de actuador desconocida: '".$clase_interfaz_actuador."'");
            }
        }
        $html .= $cadena_fin_lista_parametros;
        return ($html);
    }


    //
    // Funciones de controles y parámetros de actuadores modbus
    //


    // Devuelve los controles de opciones de interfaz de los interfaces de actuador de clase Modbus
    function dame_controles_opciones_interfaz_clase_actuador_modbus($id_controles, $cadena_opciones_interfaz_actuador_modbus)
    {
        $idiomas = new Idiomas();

        // Parámetros de opciones de interfaz de actuador modbus
        $nombres_valores_parametros_opciones_interfaz_actuador_modbus = dame_nombres_valores_parametros_actuador_modbus($cadena_opciones_interfaz_actuador_modbus);
        $tipos_registros = $nombres_valores_parametros_opciones_interfaz_actuador_modbus["tipos_registros"];
        $direcciones_dispositivos = $nombres_valores_parametros_opciones_interfaz_actuador_modbus["direcciones_dispositivos"];
        $direcciones_registros = $nombres_valores_parametros_opciones_interfaz_actuador_modbus["direcciones_registros"];
        $numeros_elementos = $nombres_valores_parametros_opciones_interfaz_actuador_modbus["numeros_elementos"];
        $reversos_bytes = $nombres_valores_parametros_opciones_interfaz_actuador_modbus["reversos_bytes"];
        $reversos_registros = $nombres_valores_parametros_opciones_interfaz_actuador_modbus["reversos_registros"];
        $tipos_datos = $nombres_valores_parametros_opciones_interfaz_actuador_modbus["tipos_datos"];
        $numeros_bits_iniciales = $nombres_valores_parametros_opciones_interfaz_actuador_modbus["numeros_bits_iniciales"];

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipos de registros").": "."</span><br/>
                    <input type='text' id='tipos_registros_".$id_controles."'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_registros)."'>
                    <span id='boton_actuadores_ayuda_tipos_registro_modbus_actuador' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Direcciones de dispositivos").": "."</span><br/>
                    <input type='text' id='direcciones_dispositivos_".$id_controles."'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $direcciones_dispositivos)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Direcciones de registros").": "."</span><br/>
                    <input type='text' id='direcciones_registros_".$id_controles."'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $direcciones_registros)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Números de elementos").": "."</span><br/>
                    <input type='text' id='numeros_elementos_".$id_controles."'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $numeros_elementos)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Reversos de bytes").": "."</span><br/>
                    <input type='text' id='reversos_bytes_".$id_controles."'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $reversos_bytes)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Reversos de registros").": "."</span><br/>
                    <input type='text' id='reversos_registros_".$id_controles."'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $reversos_registros)."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipos de dato").": "."</span><br/>
                    <input type='text' id='tipos_datos_".$id_controles."'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_datos)."'>
                    <span id='boton_actuadores_ayuda_tipos_dato_modbus_actuador' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Números de bits iniciales").": "."</span><br/>
                    <input type='text' id='numeros_bits_iniciales_".$id_controles."'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $numeros_bits_iniciales)."'>
                </div>
            </div>";
        return ($controles);
    }


    function dame_nombres_valores_parametros_actuador_modbus($cadena_opciones_valores_actuador_modbus)
    {
        // Se recuperan los parámetros de opciones de valores de un actuador modbus
        $nombres_valores_parametros_opciones_valores_actuador_modbus = array();
        $tipos_registros = array();
        $direcciones_dispositivos = array();
        $direcciones_registros = array();
        $numeros_elementos = array();
        $reversos_bytes = array();
        $reversos_registros = array();
        $tipos_datos = array();
        $numeros_bits_iniciales = array();
        $cadena_opciones_valores_actuador_modbus = str_replace(" ", "", $cadena_opciones_valores_actuador_modbus);
        $cadenas_parametros_valores = explode(SEPARADOR_PARAMETROS_VALORES, $cadena_opciones_valores_actuador_modbus);
        for ($i = 0; $i < count($cadenas_parametros_valores); $i++)
        {
            $parametros_valor = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadenas_parametros_valores[$i]);
            array_push($tipos_registros, $parametros_valor[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_TIPO_REGISTRO]);
            array_push($direcciones_dispositivos, $parametros_valor[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_DIRECCION_DISPOSITIVO]);
            array_push($direcciones_registros, $parametros_valor[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_DIRECCION_REGISTRO]);
            array_push($numeros_elementos, $parametros_valor[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_NUMERO_ELEMENTOS]);
            array_push($reversos_bytes, $parametros_valor[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_REVERSO_BYTES]);
            array_push($reversos_registros, $parametros_valor[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_REVERSO_REGISTROS]);
            array_push($tipos_datos, $parametros_valor[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_TIPO_DATO]);
            array_push($numeros_bits_iniciales, $parametros_valor[INDICE_PARAMETRO_CLASE_INTERFAZ_ACTUADOR_MODBUS_OPCIONES_NUMERO_BIT_INICIAL]);
        }
        $nombres_valores_parametros_opciones_valores_actuador_modbus["tipos_registros"] = $tipos_registros;
        $nombres_valores_parametros_opciones_valores_actuador_modbus["direcciones_dispositivos"] = $direcciones_dispositivos;
        $nombres_valores_parametros_opciones_valores_actuador_modbus["direcciones_registros"] = $direcciones_registros;
        $nombres_valores_parametros_opciones_valores_actuador_modbus["numeros_elementos"] = $numeros_elementos;
        $nombres_valores_parametros_opciones_valores_actuador_modbus["reversos_bytes"] = $reversos_bytes;
        $nombres_valores_parametros_opciones_valores_actuador_modbus["reversos_registros"] = $reversos_registros;
        $nombres_valores_parametros_opciones_valores_actuador_modbus["tipos_datos"] = $tipos_datos;
        $nombres_valores_parametros_opciones_valores_actuador_modbus["numeros_bits_iniciales"] = $numeros_bits_iniciales;
        return ($nombres_valores_parametros_opciones_valores_actuador_modbus);
    }
?>
