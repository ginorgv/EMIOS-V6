<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_elementos_adicionales_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_hijos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    // Constantes

    // Indices de parámetros de sensor ModBus
    define("INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_TIPO_REGISTRO", 0);
	define("INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_DIRECCION_DISPOSITIVO", 1);
	define("INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_DIRECCION_REGISTRO", 2);
	define("INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_NUMERO_ELEMENTOS", 3);
	define("INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_REVERSO_BYTES", 4);
	define("INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_REVERSO_REGISTROS", 5);
	define("INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_TIPO_DATO", 6);


    // Devuelve los controles de opciones de Modbus de sensores (interfaces - sensores reales y sensores externos)
    function dame_controles_opciones_sensor_modbus($id_controles, $cadena_opciones_valores_sensor_modbus)
    {
        $idiomas = new Idiomas();

        // Parámetros de opciones de valores de sensor modbus
        $nombres_valores_parametros_opciones_valores_sensor_modbus = dame_nombres_valores_parametros_sensor_modbus($cadena_opciones_valores_sensor_modbus);
        $tipos_registros = $nombres_valores_parametros_opciones_valores_sensor_modbus["tipos_registros"];
        $direcciones_dispositivos = $nombres_valores_parametros_opciones_valores_sensor_modbus["direcciones_dispositivos"];
        $direcciones_registros = $nombres_valores_parametros_opciones_valores_sensor_modbus["direcciones_registros"];
        $numeros_elementos = $nombres_valores_parametros_opciones_valores_sensor_modbus["numeros_elementos"];
        $reversos_bytes = $nombres_valores_parametros_opciones_valores_sensor_modbus["reversos_bytes"];
        $reversos_registros = $nombres_valores_parametros_opciones_valores_sensor_modbus["reversos_registros"];
        $tipos_datos = $nombres_valores_parametros_opciones_valores_sensor_modbus["tipos_datos"];

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipos de registros").": "."</span><br/>
                    <input type='text' id='tipos_registros_".$id_controles."'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".implode(" ".SEPARADOR_PARAMETROS_VALORES." ", $tipos_registros)."'>
                    <span id='boton_sensores_ayuda_tipos_registro_modbus_sensor' class='clickable'>
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
                    <span id='boton_sensores_ayuda_tipos_dato_modbus_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>";
        return ($controles);
    }


    // Devuelve los nombres y valores de opciones de Modbus de sensores (interfaces - sensores reales y sensores externos)
    function dame_nombres_valores_parametros_sensor_modbus($cadena_opciones_valores_sensor_modbus)
    {
        // Se recuperan los parámetros de opciones de valores de un sensor modbus
        $nombres_valores_parametros_opciones_valores_sensor_modbus = array();
        $tipos_registros = array();
        $direcciones_dispositivos = array();
        $direcciones_registros = array();
        $numeros_elementos = array();
        $reversos_bytes = array();
        $reversos_registros = array();
        $tipos_datos = array();
        $cadena_opciones_valores_sensor_modbus = str_replace(" ", "", $cadena_opciones_valores_sensor_modbus);
        $cadenas_parametros_valores = explode(SEPARADOR_PARAMETROS_VALORES, $cadena_opciones_valores_sensor_modbus);
        for ($i = 0; $i < count($cadenas_parametros_valores); $i++)
        {
            $parametros_valor = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadenas_parametros_valores[$i]);
            array_push($tipos_registros, $parametros_valor[INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_TIPO_REGISTRO]);
            array_push($direcciones_dispositivos, $parametros_valor[INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_DIRECCION_DISPOSITIVO]);
            array_push($direcciones_registros, $parametros_valor[INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_DIRECCION_REGISTRO]);
            array_push($numeros_elementos, $parametros_valor[INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_NUMERO_ELEMENTOS]);
            array_push($reversos_bytes, $parametros_valor[INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_REVERSO_BYTES]);
            array_push($reversos_registros, $parametros_valor[INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_REVERSO_REGISTROS]);
            array_push($tipos_datos, $parametros_valor[INDICE_PARAMETRO_SENSOR_MODBUS_OPCIONES_TIPO_DATO]);
        }
        $nombres_valores_parametros_opciones_valores_sensor_modbus["tipos_registros"] = $tipos_registros;
        $nombres_valores_parametros_opciones_valores_sensor_modbus["direcciones_dispositivos"] = $direcciones_dispositivos;
        $nombres_valores_parametros_opciones_valores_sensor_modbus["direcciones_registros"] = $direcciones_registros;
        $nombres_valores_parametros_opciones_valores_sensor_modbus["numeros_elementos"] = $numeros_elementos;
        $nombres_valores_parametros_opciones_valores_sensor_modbus["reversos_bytes"] = $reversos_bytes;
        $nombres_valores_parametros_opciones_valores_sensor_modbus["reversos_registros"] = $reversos_registros;
        $nombres_valores_parametros_opciones_valores_sensor_modbus["tipos_datos"] = $tipos_datos;
        return ($nombres_valores_parametros_opciones_valores_sensor_modbus);
    }


    //
    // Funciones utilizadas en la administración de sensores
    //


    function dame_posible_eliminar_sensor(
        $id_sensor,
        $fila_sensor,
        &$msg,
        $sufijo_mensaje_aviso)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Comprobaciones antes de eliminar el sensor:
        // - Se comprueba si el sensor tiene eventos asignados
        // - Se comprueba si existe algun suceso asignado a este sensor (de timeout de envío)
        // - Se comprueba si es un sensor de activa y esta asociado a algún sensor de reactiva o de cortes de tensión
        // - Se comprueba si el sensor es un hijo de otro sensor
        // - Se comprueba si el sensor tiene líneas base o proyectos asociados
        // - Se comprueba si el sensor está asignado a alguna variable de lineas base
        // - Se comprueba si el sensor tiene alguna importación pendiente
        $posible_eliminar_sensor = true;

        // Información del sensor
        $nombre_sensor = $fila_sensor["nombre"];
        $clase_sensor = $fila_sensor["clase"];
        $cadena_parametros_clase = $fila_sensor["parametros_clase"];
        $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase);

        // Se comprueba si el sensor tiene eventos asignados
        if ($posible_eliminar_sensor == true)
        {
            $consulta_eventos = "
                SELECT nombre
                FROM eventos
                WHERE
                    (origen = '".ORIGEN_EVENTO_SENSOR."')
                    AND (id_origen = '".$bd_red->_($id_sensor)."')
                ORDER BY nombre ASC";
            $res_eventos = $bd_red->ejecuta_consulta($consulta_eventos);
            if ($res_eventos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_eventos."'");
            }
            if ($res_eventos->dame_numero_filas() > 0)
            {
                $posible_eliminar_sensor = false;

                $fila_evento = $res_eventos->dame_siguiente_fila();
                $nombre_evento = $fila_evento["nombre"];

                $msg = $idiomas->_("No se puede eliminar el sensor porque tiene eventos asignados")."\n(".
                    $nombre_evento.")";
            }
        }

        // Se comprueba si existe algun suceso asignado a este sensor (de timeout de envío)
        if ($posible_eliminar_sensor == true)
        {
            $consulta_sucesos = "
                SELECT *
                FROM sucesos_reglas
                WHERE
                    (causa = '".CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR."')
                    AND (origen = '".ORIGEN_SUCESO_SENSOR."')
                    AND (id_origen = '".$bd_red->_($id_sensor)."')
                ORDER BY nombre ASC";
            $res_sucesos = $bd_red->ejecuta_consulta($consulta_sucesos);
            if ($res_sucesos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_sucesos."'");
            }
            if ($res_sucesos->dame_numero_filas() > 0)
            {
                $posible_eliminar_sensor = false;

                $fila_suceso = $res_sucesos->dame_siguiente_fila();
                $nombre_suceso = $fila_suceso["nombre"];
                $id_regla_suceso = $fila_suceso["regla"];
                $nombre_regla_suceso = dame_nombre_regla($id_regla_suceso);

                $msg = $idiomas->_("No se puede eliminar el sensor porque tiene sucesos de timeout de envío asignados")."\n(".
                    $idiomas->_("suceso").": ".$nombre_suceso.", ".
                    $idiomas->_("regla").": ".$nombre_regla_suceso.")";
            }
        }

        // Se comprueba si es un sensor de activa y esta asociado a algún sensor de reactiva o de cortes de tensión
        if ($posible_eliminar_sensor == true)
        {
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    if ($posible_eliminar_sensor == true)
                    {
                        $consulta_sensores = "
                            SELECT nombre
                            FROM sensores
                            WHERE
                                (clase = '".CLASE_SENSOR_ENERGIA_REACTIVA."')
                                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_sensor)."')";
                        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
                        if ($res_sensores == false)
                        {
                            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
                        }
                        if ($res_sensores->dame_numero_filas() > 0)
                        {
                            $posible_eliminar_sensor = false;

                            $fila_sensor = $res_sensores->dame_siguiente_fila();
                            $nombre_sensor = $fila_sensor["nombre"];

                            $msg = $idiomas->_("No se puede eliminar el sensor porque está asignado a un sensor de energía reactiva")."\n(".
                                $nombre_sensor.")";
                        }
                    }

                    if ($posible_eliminar_sensor == true)
                    {
                        $consulta_sensores = "
                            SELECT nombre
                            FROM sensores
                            WHERE
                                (clase = '".CLASE_SENSOR_CORTES_TENSION."')
                                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_sensor)."')";
                        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
                        if ($res_sensores == false)
                        {
                            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
                        }
                        if ($res_sensores->dame_numero_filas() > 0)
                        {
                            $posible_eliminar_sensor = false;

                            $fila_sensor = $res_sensores->dame_siguiente_fila();
                            $nombre_sensor = $fila_sensor["nombre"];

                            $msg = $idiomas->_("No se puede eliminar el sensor porque está asignado a un sensor de cortes de tensión")."\n(".
                                $nombre_sensor.")";
                        }
                    }
                    break;
                }
            }
        }

        // Se comprueba si el sensor es hijo de algún sensor
        if ($posible_eliminar_sensor == true)
        {
            $consulta_hijos_sensores = "
                SELECT sensor_padre
                FROM hijos_sensores
                WHERE
                    sensor_hijo = '".$bd_red->_($id_sensor)."'
                ORDER BY sensor_padre";
            $res_hijos_sensores = $bd_red->ejecuta_consulta($consulta_hijos_sensores);
            if ($res_hijos_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_hijos_sensores."'");
            }
            if ($res_hijos_sensores->dame_numero_filas() > 0)
            {
                $posible_eliminar_sensor = false;

                $fila_hijo_sensor = $res_hijos_sensores->dame_siguiente_fila();
                $id_sensor_padre = $fila_hijo_sensor["sensor_padre"];
                $nombre_sensor_padre = dame_nombre_sensor($id_sensor_padre);

                $msg = $idiomas->_("No se puede eliminar el sensor porque es hijo de otro sensor")."\n(".
                    $nombre_sensor_padre.")";
            }
        }

        // Se comprueba si el sensor esta asignado a alguna línea base
        if ($posible_eliminar_sensor == true)
        {
            $consulta_lineas_base = "
                SELECT nombre
                FROM lineas_base
                WHERE
                    sensor = '".$bd_red->_($id_sensor)."'
                ORDER BY nombre ASC";
            $res_lineas_base = $bd_red->ejecuta_consulta($consulta_lineas_base);
            if ($res_lineas_base == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_lineas_base."'");
            }
            if ($res_lineas_base->dame_numero_filas() > 0)
            {
                $posible_eliminar_sensor = false;

                $fila_linea_base = $res_lineas_base->dame_siguiente_fila();
                $nombre_linea_base = $fila_linea_base["nombre"];

                $msg = $idiomas->_("No se puede eliminar el sensor porque tiene líneas base asignadas")."\n(".
                    $nombre_linea_base.")";
            }
        }

        // Se comprueba si el sensor esta asignado a algún proyecto
        if ($posible_eliminar_sensor == true)
        {
            $consulta_proyectos = "
                SELECT nombre
                FROM proyectos
                WHERE
                    sensor = '".$bd_red->_($id_sensor)."'
                ORDER BY nombre ASC";
            $res_proyectos = $bd_red->ejecuta_consulta($consulta_proyectos);
            if ($res_proyectos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_proyectos."'");
            }
            if ($res_proyectos->dame_numero_filas() > 0)
            {
                $posible_eliminar_sensor = false;

                $fila_proyecto = $res_proyectos->dame_siguiente_fila();
                $nombre_proyecto = $fila_proyecto["nombre"];

                $msg = $idiomas->_("No se puede eliminar el sensor porque tiene proyectos asignados")."\n(".
                    $nombre_proyecto.")";
            }
        }

        // Se comprueba si el sensor esta asignado a alguna variable de líneas base
        if ($posible_eliminar_sensor == true)
        {
            $consulta_variables_lineas_base = "
                SELECT *
                FROM variables_lineas_base
                WHERE
                    sensor = '".$bd_red->_($id_sensor)."'
                ORDER BY nombre ASC";
            $res_variables_lineas_base = $bd_red->ejecuta_consulta($consulta_variables_lineas_base);
            if ($res_variables_lineas_base == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_variables_lineas_base."'");
            }
            if ($res_variables_lineas_base->dame_numero_filas() > 0)
            {
                $posible_eliminar_sensor = false;

                $fila_variable_linea_base = $res_variables_lineas_base->dame_siguiente_fila();
                $id_linea_base_variable_linea_base = $fila_variable_linea_base["linea_base"];
                $nombre_variable_linea_base = $fila_variable_linea_base["nombre"];
                $nombre_linea_base_variable_linea_base = dame_nombre_linea_base($id_linea_base_variable_linea_base);

                $msg = $idiomas->_("No se puede eliminar el sensor porque está asignado a alguna variable de líneas base")."\n(".
                    $idiomas->_("variable").": ".$nombre_variable_linea_base.", ".
                    $idiomas->_("línea base").": ".$nombre_linea_base_variable_linea_base.")";
            }
        }

        // Se comprueba si el sensor tiene alguna importación de valores pendiente
        if ($posible_eliminar_sensor == true)
        {
            $consulta_importaciones_valores_sensores_pendientes = "
                SELECT id
                FROM importaciones_valores_sensores_pendientes
                WHERE
                    sensor = '".$bd_red->_($id_sensor)."'";
            $res_importaciones_valores_sensores_pendientes = $bd_red->ejecuta_consulta($consulta_importaciones_valores_sensores_pendientes);
            if ($res_importaciones_valores_sensores_pendientes == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_importaciones_valores_sensores_pendientes."'");
            }
            if ($res_importaciones_valores_sensores_pendientes->dame_numero_filas() > 0)
            {
                $posible_eliminar_sensor = false;

                $msg = $idiomas->_("No se puede eliminar el sensor porque tiene importaciones de valores pendientes");
            }
        }

        // Se comprueba si se pueden eliminar los elementos adicionales según la clase de sensor
        if ($posible_eliminar_sensor == true)
        {
            $msg = "";
            $posible_eliminar_elementos_adicionales_sensor = dame_posible_eliminar_elementos_adicionales_clase_sensor(
                $nombre_sensor,
                $clase_sensor,
                $parametros_clase,
                $msg);
            if ($posible_eliminar_elementos_adicionales_sensor == false)
            {
                $posible_eliminar_sensor = false;
            }
        }

        // Se añade el nombre del sensor al mensaje de aviso (si es necesario)
        if ($sufijo_mensaje_aviso != "")
        {
            $msg .= "\n(".$sufijo_mensaje_aviso.")";
        }

        // Se devuelve si es posible eliminar el sensor
        return ($posible_eliminar_sensor);
    }


    function elimina_sensor($id_sensor, $fila_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera la información del sensor
        $nombre_sensor = $fila_sensor['nombre'];
        $clase_sensor = $fila_sensor['clase'];
        $cadena_parametros_clase = $fila_sensor['parametros_clase'];

        // Parámetros de de clase de sensor
        $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase);

        // Se borra el sensor
        $operacion_borrado = "
            DELETE
            FROM sensores
            WHERE
                id = '".$bd_red->_($id_sensor)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se eliminan los elementos adicionales según la clase de sensor
            elimina_elementos_adicionales_clase_sensor(
                $id_sensor,
                $nombre_sensor,
                $clase_sensor,
                $parametros_clase);

            // Acciones a realizar al eliminar un sensor
            realiza_acciones_sensor_eliminado($id_sensor, $fila_sensor);
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }


    //
    // Funciones de acciones al realizar operaciones de administración de sensores
    //


    // Realiza acciones al añadir un sensor
    function realiza_acciones_sensor_anyadido($id_sensor, $fila)
    {
        // Información del sensor
        $tipo_sensor = $fila["tipo"];
        $cadena_parametros_tipo = $fila["parametros_tipo"];
        $id_localizacion = $fila["localizacion"];
        $id_grupo = $fila["grupo"];

        // Se notifica la operación de administración
        switch ($tipo_sensor)
        {
            case TIPO_SENSOR_EXTERNO:
            {
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
                $clase_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                $parametros_extra = array(
                    "clase_sensor_externo" => $clase_sensor_externo);
                break;
            }
            default:
            {
                $parametros_extra = array();
                break;
            }
        }
        notifica_operacion_administracion_sensor($tipo_sensor, OPERACION_ADICION, $id_sensor, $parametros_extra);

        // Se recarga la configuración del dispositivo
        switch ($tipo_sensor)
        {
            case TIPO_SENSOR_REAL:
            {
                $id_dispositivo = dame_dispositivo_sensor_real($id_sensor);
                recarga_configuracion_dispositivo($id_dispositivo);
                break;
            }
        }

        // Se añade el sensor al usuario actual (si es necesario)
        if (($id_localizacion == ID_NINGUNO) && ($id_grupo == ID_NINGUNO))
        {
            anyade_sensor_grupo_parametros_modulo_sensores_usuario_actual(TIPO_NODO_SENSOR, $id_sensor);
        }
    }


    // Realiza acciones al modificar un sensor
    function realiza_acciones_sensor_modificado(
        $id_sensor,
        $fila_actual,
        $fila_anterior)
    {
        // Información del sensor
        $tipo_sensor = $fila_actual["tipo"];

        // Se notifica la operación de administración del sensor
        switch ($tipo_sensor)
        {
            case TIPO_SENSOR_EXTERNO:
            {
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actual["parametros_tipo"]);
                $clase_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                $parametros_tipo_anteriores = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_anterior["parametros_tipo"]);
                $clase_sensor_externo_anterior = $parametros_tipo_anteriores[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                $parametros_extra = array(
                    "clase_sensor_externo" => $clase_sensor_externo,
                    "clase_sensor_externo_anterior" => $clase_sensor_externo_anterior);
                break;
            }
            default:
            {
                $parametros_extra = array();
                break;
            }
        }
        notifica_operacion_administracion_sensor($tipo_sensor, OPERACION_MODIFICACION, $id_sensor, $parametros_extra);

        // Se recargan las configuraciones de los dispositivos del sensor (anterior y actual)
        switch ($tipo_sensor)
        {
            case TIPO_SENSOR_REAL:
            {
                $id_dispositivo_actual = dame_dispositivo_sensor_real($fila_actual);
                $id_dispositivo_anterior = dame_dispositivo_sensor_real($fila_anterior);
                recarga_configuracion_dispositivo($id_dispositivo_actual);
                if ($id_dispositivo_actual != $id_dispositivo_anterior)
                {
                    recarga_configuracion_dispositivo($id_dispositivo_anterior);
                }
                break;
            }
        }

        // Si se ha cambiado el nombre, se modifican los comentarios del sensor
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            modifica_comentarios_nodo(TIPO_NODO_SENSOR, $fila_anterior["nombre"], $fila_actual["nombre"]);
        }

        // Si se ha modifica el grupo o la localización del sensor,
        // se eliminan los elementos que han dejado de ser visibles por los usuarios
        // (pueden dejar de ver el sensor actual)
        $comprobar_elementos_no_visibles_parametros_modulos_usuarios = false;
        if (($fila_anterior["grupo"] != ID_NINGUNO) && ($fila_anterior["grupo"] != $fila_actual["grupo"]))
        {
            $comprobar_elementos_no_visibles_parametros_modulos_usuarios = true;
        }
        if (($fila_anterior["localizacion"] != ID_NINGUNO) && ($fila_anterior["localizacion"] != $fila_actual["localizacion"]))
        {
            $comprobar_elementos_no_visibles_parametros_modulos_usuarios = true;
        }
        if (($fila_anterior["visible_localizaciones_hijas"] == VALOR_SI) && ($fila_actual["visible_localizaciones_hijas"] == VALOR_NO))
        {
            $comprobar_elementos_no_visibles_parametros_modulos_usuarios = true;
        }
        if ($comprobar_elementos_no_visibles_parametros_modulos_usuarios == true)
        {
            elimina_modifica_elementos_no_visibles_parametros_modulos_usuarios();
        }

        // Si se ha modificado la localización, se elimina el sensor de los equipos de las instalaciones (si es necesario)
        if (($fila_anterior["localizacion"] != ID_NINGUNO) && ($fila_anterior["localizacion"] != $fila_actual["localizacion"]))
        {
            elimina_id_nodo_equipos_instalaciones(TIPO_NODO_SENSOR, $id_sensor);
        }
    }


    // Realiza acciones al eliminar un sensor
    function realiza_acciones_sensor_eliminado($id_sensor, $fila)
    {
        $bd_red = BaseDatosRed::dame_base_datos();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Información del sensor
        $nombre_sensor = $fila["nombre"];
        $tipo_sensor = $fila["tipo"];
        $cadena_parametros_tipo = $fila["parametros_tipo"];
        $clase_sensor = $fila["clase"];
        $tipo_valores = $fila["tipo_valores"];
        $incrementos_tiempo_real_horarios_sensor = $fila["incrementos_tiempo_real_horarios"];

        // Se borran los sensores hijos de este sensor
        switch ($tipo_sensor)
        {
            case TIPO_SENSOR_VIRTUAL:
            case TIPO_SENSOR_PROCESADO:
            {
                $operacion_borrado_hijos_sensores = "
                    DELETE
                    FROM hijos_sensores
                    WHERE
                        sensor_padre = '".$bd_red->_($id_sensor)."'";
                $res_borrado_hijos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_hijos_sensores);
                if ($res_borrado_hijos_sensores == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_hijos_sensores."'");
                }
                break;
            }
        }

        // Se guarda la información de los valores pendientes de borrado (para borrar sus datos en el procesado al cabo de un tiempo)
        // (sólo si el sensor tiene valores)
        if ($fila["ultimos_valores"] !== NULL)
        {
            // Fecha y hora actual UTC
            // (se establece la hora a las 00:00 para que el borrado de los valores pendientes de borrado se realice en el primer procesado
            //  posterior a las 00:00)
            $fecha_hora_local = dame_fecha_hora_actual_local();
            $fecha_hora_local->modify('+1 day');
            $fecha_hora_local->setTime(0, 0, 0);
            $fecha_hora_utc = cambia_zona_horaria_fecha_hora($fecha_hora_local, ZONA_HORARIA_UTC);
            $cadena_fecha_hora_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_utc, FORMATO_FECHA_HORA_BASE_DATOS);

            // Se añade la información de valores pendientes de borrado del sensor
            $operacion_insercion_informacion_valores_pendientes_borrado = "
                INSERT INTO informacion_valores_pendientes_borrado (
                    red,
                    sensor,
                    clase,
                    tipo_valores,
                    incrementos_tiempo_real_horarios,
                    hora
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_datos->_($nombre_sensor)."',
                    '".$bd_datos->_($clase_sensor)."',
                    '".$bd_datos->_($tipo_valores)."',
                    '".$bd_datos->_($incrementos_tiempo_real_horarios_sensor)."',
                    '".$bd_datos->_($cadena_fecha_hora_base_datos_utc)."'
                )";
            $res_insercion_informacion_valores_pendientes_borrado = $bd_datos->ejecuta_operacion($operacion_insercion_informacion_valores_pendientes_borrado);
            if ($res_insercion_informacion_valores_pendientes_borrado == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_informacion_valores_pendientes_borrado."'");
            }
        }

        // Se notifica la operación de administración del sensor
        switch ($tipo_sensor)
        {
            case TIPO_SENSOR_EXTERNO:
            {
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
                $clase_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                $parametros_extra = array(
                    "clase_sensor_externo" => $clase_sensor_externo);
                break;
            }
            default:
            {
                $parametros_extra = array();
                break;
            }
        }
        notifica_operacion_administracion_sensor($tipo_sensor, OPERACION_BORRADO, $id_sensor, $parametros_extra);

        // Se recarga la configuración del dispositivo del sensor
        switch ($tipo_sensor)
        {
            case TIPO_SENSOR_REAL:
            {
                $id_dispositivo = dame_dispositivo_sensor_real($fila);
                recarga_configuracion_dispositivo($id_dispositivo);
                break;
            }
        }

        // Se eliminan los comentarios del sensor
        elimina_comentarios_nodo(TIPO_NODO_SENSOR, $fila["nombre"]);

        // Se eliminan los widgets correspondientes
        elimina_widgets_sensor_eliminado($id_sensor);

        // Se modifican los elementos de plantillas de informes que contengan este sensor (se establece a ninguno)
        modifica_elementos_plantillas_informes_sensor_eliminado($id_sensor);

        // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este sensor seleccionado en algún parámetro
        modifica_informes_automaticos_plantillas_informes_sensor_eliminado($id_sensor);

        // Se eliminan los informes automáticos correspondientes
        elimina_informes_automaticos_sensor_eliminado($id_sensor);

        // Se elimina el sensor de los parámetros del módulo Sensores de los usuarios (si es necesario)
        elimina_sensor_grupo_parametros_modulo_sensores_usuarios(TIPO_NODO_SENSOR, $id_sensor);

        // Se elimina el sensor de los equipos de las instalaciones (si es necesario)
        elimina_id_nodo_equipos_instalaciones(TIPO_NODO_SENSOR, $id_sensor);

        // Se elimina el sensor de los ratios de tipo variable correspondientes (y de los ratios localizaciones)
        elimina_sensor_ratios_variables_localizaciones($id_sensor);

        // Se eliminan las posiciones de mapa del sensor
        elimina_info_posiciones_mapa_elemento_base_datos(TIPO_ELEMENTO_MAPA_SENSOR, $id_sensor);
    }
?>