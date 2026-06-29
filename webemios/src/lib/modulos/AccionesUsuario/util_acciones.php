<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/AccionUsuario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_interfaces_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_interfaces_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/ImportacionesValoresSensores/ImportacionValoresSensorPendiente.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_facturas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');



    // Se añade una acción de usuario a la base de datos
	function anyade_accion_usuario(
        $tipo,
        $objeto,
        $parametros,
        $parametros_anteriores,
        $resultado)
	{
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Sustitución de valores de parámetros 'especiales'
        sustituye_valores_parametros_accion_usuario($parametros);
        sustituye_valores_parametros_accion_usuario($parametros_anteriores);

        // Parámetros
        $id_red = $_SESSION["id_red"];
        $id_usuario = $_SESSION["id_usuario"];
        $cadena_objeto = "";
        $cadena_parametros_json = "";
        $cadena_parametros_anteriores_json = "";
        $cadena_resultado_json = "";
        if ($objeto !== NULL)
        {
            $cadena_objeto = $objeto;
        }
        if ($parametros !== NULL)
        {
            $cadena_parametros_json = json_encode($parametros);
        }
        if ($parametros_anteriores !== NULL)
        {
            $cadena_parametros_anteriores_json = json_encode($parametros_anteriores);
        }
        if ($resultado !== NULL)
        {
            $cadena_resultado_json = json_encode($resultado);
        }
        $observaciones = "";

        // Fecha y hora actual UTC
        $fecha_hora_utc = dame_fecha_hora_actual_utc();
        $cadena_fecha_hora_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_utc, FORMATO_FECHA_HORA_BASE_DATOS);

        // Se añade la acción de usuario
        $operacion_insercion = "
            INSERT INTO acciones_usuario (
                red,
                hora,
                usuario,
                tipo,
                objeto,
                parametros,
                parametros_anteriores,
                resultado,
                observaciones
            ) VALUES (
                '".$bd_datos->_($id_red)."',
                '".$bd_datos->_($cadena_fecha_hora_base_datos_utc)."',
                '".$bd_datos->_($id_usuario)."',
                '".$bd_datos->_($tipo)."',
                '".$bd_datos->_($cadena_objeto)."',
                '".$bd_datos->_($cadena_parametros_json)."',
                '".$bd_datos->_($cadena_parametros_anteriores_json)."',
                '".$bd_datos->_($cadena_resultado_json)."',
                '".$bd_datos->_($observaciones)."'
            )";
        $res_insercion = $bd_datos->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }


    // Sustitución de valores de parámetros
    // - Nota: Parámetros de nombres
    function sustituye_valores_parametros_accion_usuario(&$parametros_accion)
    {
        foreach ($parametros_accion as $parametro_accion => $valor_parametro_accion)
        {
            if (dame_parametro_nombre_elemento_accion_usuario($parametro_accion) == true)
            {
                sustituye_valor_parametro_nombre_elemento_accion_usuario($valor_parametro_accion);
                $parametros_accion[$parametro_accion] = $valor_parametro_accion;
            }
        }
    }


    // Restauración de valores de parámetros
    // - Nota: Parámetros de nombres
    function restaura_valor_parametro_accion_usuario($parametro_accion, &$valor_parametro_accion)
    {
        if (dame_parametro_nombre_elemento_accion_usuario($parametro_accion) == true)
        {
            restaura_valor_parametro_nombre_elemento_accion_usuario($valor_parametro_accion);
        }
    }


    // Devuelve si el parámetro de acción de usuario es un nombre de un elemento
    function dame_parametro_nombre_elemento_accion_usuario($parametro_accion)
    {
        $parametro_nombre = false;
        switch ($parametro_accion)
        {
            case PARAMETRO_ACCION_USUARIO_NOMBRE:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_DESTINO:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_USUARIO:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_RED:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION_PADRE:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION_HIJA:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR_PADRE:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR_HIJO:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_PROGRAMACION:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_TARIFA:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO_TARIFAS:
            case PARAMETRO_ACCION_USUARIO_NOMBRE_LINEA_BASE:
            {
                $parametro_nombre = true;
            }
        }
        return ($parametro_nombre);
    }


    // Sustitución de valores de parámetros de nombres
    function sustituye_valor_parametro_nombre_elemento_accion_usuario(&$valor_parametro_accion)
    {
        $idiomas = new Idiomas();

        if ($valor_parametro_accion == $idiomas->_("Ninguno"))
        {
            $valor_parametro_accion = ID_NOMBRE_NINGUNO_ACCION_USUARIO;
        }
        if ($valor_parametro_accion == $idiomas->_("Ninguna"))
        {
            $valor_parametro_accion = ID_NOMBRE_NINGUNA_ACCION_USUARIO;
        }
        if ($valor_parametro_accion == $idiomas->_("Desconocido"))
        {
            $valor_parametro_accion = ID_NOMBRE_DESCONOCIDO_ACCION_USUARIO;
        }
        if ($valor_parametro_accion == $idiomas->_("Desconocida"))
        {
            $valor_parametro_accion = ID_NOMBRE_DESCONOCIDA_ACCION_USUARIO;
        }
    }


    // Restauración de valores de parámetros de nombres
    function restaura_valor_parametro_nombre_elemento_accion_usuario(&$valor_parametro_accion)
    {
        $idiomas = new Idiomas();

        if ($valor_parametro_accion == ID_NOMBRE_NINGUNO_ACCION_USUARIO)
        {
            $valor_parametro_accion = $idiomas->_("Ninguno");
        }
        if ($valor_parametro_accion == ID_NOMBRE_NINGUNA_ACCION_USUARIO)
        {
            $valor_parametro_accion = $idiomas->_("Ninguna");
        }
        if ($valor_parametro_accion == ID_NOMBRE_DESCONOCIDO_ACCION_USUARIO)
        {
            $valor_parametro_accion = $idiomas->_("Desconocido");
        }
        if ($valor_parametro_accion == ID_NOMBRE_DESCONOCIDA_ACCION_USUARIO)
        {
            $valor_parametro_accion = $idiomas->_("Desconocida");
        }
    }


    //
    // Funciones de descripciones de parámetros de acciones (por módulos)
    //


    //
    // Varios módulos
    //


    function dame_descripcion_valor_parametro_accion_usuario_numero_fechas_horas(
        $numero,
        $cadena_fecha_hora_inicio_base_datos_local,
        $cadena_fecha_hora_fin_base_datos_local,
        $tipo_descripcion)
    {
        $idiomas = new Idiomas();

        $cadena_fecha_hora_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
        $descripcion = $numero." (".
            $idiomas->_("hora de inicio").": ".$cadena_fecha_hora_inicio_local_local.", ".
            $idiomas->_("hora de fin").": ".$cadena_fecha_hora_fin_local_local.")";
        return ($descripcion);
    }


    function dame_descripcion_valor_parametro_accion_dias_semana($cadena_dias_semana, $tipo_descripcion)
    {
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

        // Días de la semana
        $dias_semana = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_dias_semana);
        $descripcion .= $cadena_inicio_lista_parametros;
        foreach ($dias_semana as $dia_semana)
        {
            $descripcion .= $cadena_inicio_parametro.dame_nombre_dia_semana($dia_semana).$cadena_fin_parametro;
        }
        $descripcion .= $cadena_fin_lista_parametros;

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_TEXTO:
            {
                $descripcion = substr($descripcion, 1);
                break;
            }
        }
        return ($descripcion);
    }


    function dame_descripcion_cadena_nombres($nombres, $tipo_descripcion)
    {
        // Si no hay nombres se devuelve cadena vacía
        $descripcion = "";
        if (count($nombres) == 0)
        {
            return ($descripcion);
        }

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

        // Descripción de nombres
        $descripcion .= $cadena_inicio_lista_parametros;
        foreach ($nombres as $nombre)
        {
            if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
            {
                $nombre = htmlspecialchars($nombre, ENT_QUOTES);
            }
            $descripcion .= $cadena_inicio_parametro.$nombre.$cadena_fin_parametro;
        }
        $descripcion .= $cadena_fin_lista_parametros;
        return ($descripcion);
    }


    //
    // Módulo Localizaciones
    //


    function dame_descripcion_valor_parametro_accion_usuario_ratios_localizacion($info_ratios_localizacion, $tipo_descripcion)
    {
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

        // Ratios
        $descripcion .= $cadena_inicio_lista_parametros;
        foreach ($info_ratios_localizacion as $nombre_ratio => $info_ratio_localizacion)
        {
            $unidad_medida_ratio = $info_ratio_localizacion["unidad_medida"];
            $tipo_ratio = $info_ratio_localizacion["tipo"];
            $valor_sensor_ratio = $info_ratio_localizacion["valor_sensor"];

            if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
            {
                $nombre_ratio = htmlspecialchars($nombre_ratio, ENT_QUOTES);
                $unidad_medida_ratio = htmlspecialchars($unidad_medida_ratio, ENT_QUOTES);
            }
            $descripcion .= $cadena_inicio_parametro.$nombre_ratio." (".$unidad_medida_ratio."): ";
            switch ($tipo_ratio)
            {
                case TIPO_RATIO_FIJO:
                {
                    $descripcion .= formatea_numero($valor_sensor_ratio, 2);
                    break;
                }
                case TIPO_RATIO_VARIABLE:
                {
                    if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                    {
                        $valor_sensor_ratio = htmlspecialchars($valor_sensor_ratio, ENT_QUOTES);
                    }
                    $descripcion .= $valor_sensor_ratio;
                    break;
                }
            }
            $descripcion .= $cadena_fin_parametro;
        }
        $descripcion .= $cadena_fin_lista_parametros;

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_TEXTO:
            {
                $descripcion = substr($descripcion, 1);
                break;
            }
        }
        return ($descripcion);
    }


    //
    // Módulo Sensores
    //


    function dame_descripcion_valor_parametro_accion_usuario_opciones_fichero_csv($tipo_valores, $opciones_fichero_csv, $tipo_descripcion)
    {
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

        // Opciones de fichero CSV
        $descripcion .= ImportacionValoresSensorPendiente::dame_descripcion_parametros_opciones_fichero_csv_importacion_valores_sensor(
            $tipo_valores,
            $opciones_fichero_csv,
            $tipo_descripcion);

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_TEXTO:
            {
                $descripcion = substr($descripcion, 1);
                break;
            }
        }
        return ($descripcion);
    }


    function dame_descripcion_valor_parametro_accion_usuario_opciones_valores_fichero_csv($opciones_valores_fichero_csv, $tipo_descripcion)
    {
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

        // Opciones de fichero CSV
        $descripcion .= ImportacionValoresSensorPendiente::dame_descripcion_parametros_opciones_valores_fichero_csv_importacion_valores_sensor(
            $opciones_valores_fichero_csv,
            $tipo_descripcion);

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_TEXTO:
            {
                $descripcion = substr($descripcion, 1);
                break;
            }
        }
        return ($descripcion);
    }


    function dame_descripcion_valor_parametro_accion_usuario_parametros_clase_sensor($clase, $parametros_clase, $tipo_descripcion)
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

        // Parámetros específicos de clase de sensor
        $descripcion .= $cadena_inicio_lista_parametros;
        switch ($clase)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tarifa_electrica = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA];
                        $nombre_grupo_tarifas_electricas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS];
                        $error_maximo_validacion_facturas_energia = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_ENERGIA];
                        $error_maximo_validacion_facturas_potencia = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_POTENCIA];
                        $error_maximo_validacion_facturas_otros_conceptos_coste_total = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_OTROS_CONCEPTOS_COSTE_TOTAL];
                        $tipo_fichero_validacion_facturas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_TIPO_FICHERO_VALIDACION_FACTURAS];
                        $prefijo_fichero_validacion_facturas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_PREFIJO_FICHERO_VALIDACION_FACTURAS];
                        restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_tarifa_electrica);
                        restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_grupo_tarifas_electricas);

                        if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                        {
                            $nombre_tarifa_electrica = htmlspecialchars($nombre_tarifa_electrica, ENT_QUOTES);
                            $nombre_grupo_tarifas_electricas = htmlspecialchars($nombre_grupo_tarifas_electricas, ENT_QUOTES);
                        }
                        $descripcion .= $cadena_inicio_parametro.$idiomas->_("Tarifa eléctrica").": ".$nombre_tarifa_electrica.$cadena_fin_parametro;
                        $descripcion .= $cadena_inicio_parametro.$idiomas->_("Grupo de tarifas eléctricas").": ".$nombre_grupo_tarifas_electricas.$cadena_fin_parametro;
                        if ($cups != "")
                        {
                            $descripcion .= $cadena_inicio_parametro.$idiomas->_("CUPS").": ".$cups.$cadena_fin_parametro;
                        }
                        $descripcion .= $cadena_inicio_parametro.$idiomas->_("Error máximo en validación de facturas y cierres")." (".$idiomas->_("energía")."): ".$error_maximo_validacion_facturas_energia.$cadena_fin_parametro;
                        if ($error_maximo_validacion_facturas_potencia != "")
                        {
                            $descripcion .= $cadena_inicio_parametro.$idiomas->_("Error máximo en validación de facturas y cierres")." (".$idiomas->_("potencia")."): ".$error_maximo_validacion_facturas_potencia.$cadena_fin_parametro;
                        }
                        if ($error_maximo_validacion_facturas_otros_conceptos_coste_total != "")
                        {
                            $descripcion .= $cadena_inicio_parametro.$idiomas->_("Error máximo en validación de facturas y cierres")." (".$idiomas->_("otros conceptos y coste total")."): ".$error_maximo_validacion_facturas_otros_conceptos_coste_total.$cadena_fin_parametro;
                        }
                        $descripcion .= $cadena_inicio_parametro.$idiomas->_("Tipo de fichero de validación automática de facturas y cierres")."): ".
                            dame_descripcion_tipo_fichero_validacion_facturas_electricidad_Espanya($tipo_fichero_validacion_facturas)."<br/>";
                        if ($prefijo_fichero_validacion_facturas != "")
                        {
                            $descripcion .= $cadena_inicio_parametro.$idiomas->_("Prefijo de fichero de validación de facturas y cierres").": ".$prefijo_fichero_validacion_facturas.$cadena_fin_parametro;
                        }
                        break;
                    }
                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas desconocido: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $nombre_sensor_energia_activa = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA];
                restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_sensor_energia_activa);

                if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                {
                    $nombre_sensor_energia_activa = htmlspecialchars($nombre_sensor_energia_activa, ENT_QUOTES);
                }
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Sensor de energía activa").": ".$nombre_sensor_energia_activa.$cadena_fin_parametro;
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $nombre_sensor_energia_activa = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA];
                restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_sensor_energia_activa);

                if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                {
                    $nombre_sensor_energia_activa = htmlspecialchars($nombre_sensor_energia_activa, ENT_QUOTES);
                }
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Sensor de energía activa").": ".$nombre_sensor_energia_activa.$cadena_fin_parametro;
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $nombres_sensores_hijos = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
                $nombre_sensor_asociado = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];
                restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_sensor_asociado);

                if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                {
                    for ($i = 0; $i < count($nombres_sensores_hijos); $i++)
                    {
                        $nombres_sensores_hijos[$i] = htmlspecialchars($nombres_sensores_hijos[$i], ENT_QUOTES);
                    }
                    $nombre_sensor_asociado = htmlspecialchars($nombre_sensor_asociado, ENT_QUOTES);
                }
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Sensores hijos").": ".$cadena_inicio_lista_parametros;
                for ($i = 0; $i < count($nombres_sensores_hijos); $i++)
                {
                    $descripcion .= $cadena_inicio_parametro.$nombres_sensores_hijos[$i].$cadena_fin_parametro;
                }
                $descripcion .= $cadena_fin_lista_parametros;
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Sensor asociado").": ".$nombre_sensor_asociado.$cadena_fin_parametro;
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tarifa_gas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_TARIFA_GAS];
                        $nombre_grupo_tarifas_gas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_GRUPO_TARIFAS_GAS];
                        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_CUPS];
                        restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_tarifa_gas);
                        restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_grupo_tarifas_gas);

                        if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                        {
                            $nombre_tarifa_gas = htmlspecialchars($nombre_tarifa_gas, ENT_QUOTES);
                            $nombre_grupo_tarifas_gas = htmlspecialchars($nombre_grupo_tarifas_gas, ENT_QUOTES);
                        }
                        $descripcion .= $cadena_inicio_parametro.$idiomas->_("Tarifa de gas").": ".$nombre_tarifa_gas.$cadena_fin_parametro;
                        $descripcion .= $cadena_inicio_parametro.$idiomas->_("Grupo de tarifas de gas").": ".$nombre_grupo_tarifas_gas.$cadena_fin_parametro;
                        if ($cups != "")
                        {
                            $descripcion .= $cadena_inicio_parametro.$idiomas->_("CUPS").": ".$cups.$cadena_fin_parametro;
                        }
                        break;
                    }
                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas desconocido: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tarifa_agua = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_TARIFA_AGUA];
                        $nombre_grupo_tarifas_agua = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_GRUPO_TARIFAS_AGUA];
                        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_CUPS];
                        restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_tarifa_agua);
                        restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_grupo_tarifas_agua);

                        if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                        {
                            $nombre_tarifa_agua = htmlspecialchars($nombre_tarifa_agua, ENT_QUOTES);
                            $nombre_grupo_tarifas_agua = htmlspecialchars($nombre_grupo_tarifas_agua, ENT_QUOTES);
                        }
                        $descripcion .= $cadena_inicio_parametro.$idiomas->_("Tarifa de agua").": ".$nombre_tarifa_agua.$cadena_fin_parametro;
                        $descripcion .= $cadena_inicio_parametro.$idiomas->_("Grupo de tarifas de agua").": ".$nombre_grupo_tarifas_agua.$cadena_fin_parametro;
                        if ($cups != "")
                        {
                            $descripcion .= $cadena_inicio_parametro.$idiomas->_("CUPS").": ".$cups.$cadena_fin_parametro;
                        }
                        break;
                    }
                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua desconocido: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                $nombre_medida = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_NOMBRE_MEDIDA];
                $unidad_medida = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_UNIDAD_MEDIDA];
                $icono = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_ICONO];
                $colores_mapa_calor_valor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_VALOR];
                $colores_mapa_calor_incremento = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_INCREMENTO];
                $mostrar_incrementos_calculados = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_MOSTRAR_INCREMENTOS_CALCULADOS];

                if ($nombre_medida != "")
                {
                    if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                    {
                        $nombre_medida = htmlspecialchars($nombre_medida, ENT_QUOTES);
                    }
                    $descripcion .= $cadena_inicio_parametro.$idiomas->_("Nombre de medida").": ".$nombre_medida.$cadena_fin_parametro;
                }
                if ($unidad_medida != "")
                {
                    if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                    {
                        $unidad_medida = htmlspecialchars($unidad_medida, ENT_QUOTES);
                    }
                    $descripcion .= $cadena_inicio_parametro.$idiomas->_("Unidad de medida").": ".$unidad_medida.$cadena_fin_parametro;
                }
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Icono de mapa").": ".dame_descripcion_icono_mapa($icono).$cadena_fin_parametro;
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Colores de mapa de calor de valores").": ".dame_descripcion_colores_mapa_calor($colores_mapa_calor_valor).$cadena_fin_parametro;
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Colores de mapa de calor de incrementos de valores").": ".dame_descripcion_colores_mapa_calor($colores_mapa_calor_incremento).$cadena_fin_parametro;
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Mostrar incrementos calculados").": ".dame_descripcion_valores_si_no($mostrar_incrementos_calculados).$cadena_fin_parametro;
                break;
            }
        }
        $descripcion .= $cadena_fin_lista_parametros;

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_TEXTO:
            {
                $descripcion = substr($descripcion, 1);
                break;
            }
        }
        return ($descripcion);
    }


    function dame_descripcion_valor_parametro_accion_usuario_parametros_tipo_sensor($tipo, $parametros_tipo, $tipo_descripcion)
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

        // Parámetros específicos de tipo de sensor
        $descripcion .= $cadena_inicio_lista_parametros;
        switch ($tipo)
        {
            case TIPO_SENSOR_REAL:
            {
                $nombre_axon = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON];
                $clase_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_REAL_CLASE_INTERFAZ];
                $ubicacion_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_REAL_UBICACION_INTERFAZ];
                $opciones_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_REAL_OPCIONES_INTERFAZ];
                restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_axon);

                if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                {
                    $nombre_axon = htmlspecialchars($nombre_axon, ENT_QUOTES);
                }
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Axón").": ".$nombre_axon.$cadena_fin_parametro;
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Clase de interfaz").": ".NodoSensor::dame_descripcion_clase_interfaz_sensor($clase_interfaz).$cadena_fin_parametro;
                if ($ubicacion_interfaz != "")
                {
                    $descripcion .= $cadena_inicio_parametro.$idiomas->_("Ubicación de interfaz").": ".dame_descripcion_parametros_ubicacion_interfaz_sensor(
                        $clase_interfaz,
                        $ubicacion_interfaz,
                        $tipo_descripcion).$cadena_fin_parametro;
                }
                if ($opciones_interfaz != "")
                {
                    $descripcion .= $cadena_inicio_parametro.$idiomas->_("Opciones de interfaz").": ".dame_descripcion_parametros_opciones_interfaz_sensor(
                        $clase_interfaz,
                        $opciones_interfaz,
                        $tipo_descripcion).$cadena_fin_parametro;
                }
                break;
            }
            case TIPO_SENSOR_VIRTUAL:
            {
                $clase_virtual = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_VIRTUAL_CLASE_VIRTUAL];
                $descripcion_clase_virtual = NodoSensor::dame_descripcion_clase_sensor_virtual($clase_virtual);

                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Clase de sensor virtual").": ".$descripcion_clase_virtual.$cadena_fin_parametro;
                break;
            }
            case TIPO_SENSOR_PROCESADO:
            {
                $clase_procesado = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_CLASE_PROCESADO];
                $descripcion_clase_procesado = NodoSensor::dame_descripcion_clase_sensor_procesado($clase_procesado);

                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Clase de sensor de procesado").": ".$descripcion_clase_procesado.$cadena_fin_parametro;
                switch ($clase_procesado)
                {
                    case CLASE_SENSOR_PROCESADO_FUNCION_VALORES:
                    {
                        $funcion_valores_horaria_sensor_procesado = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_FUNCION_VALORES_HORARIA];
                        $misma_funcion_valores_cuartohoraria_sensor_procesado = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_MISMA_FUNCION_VALORES_CUARTOHORARIA];
                        $funcion_valores_cuartohoraria_sensor_procesado = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_FUNCION_VALORES_CUARTOHORARIA];
                        if ($funcion_valores_horaria_sensor_procesado == "")
                        {
                            $funcion_valores_horaria_sensor_procesado = $idiomas->_("ND");
                        }
                        $descripcion .= $cadena_inicio_parametro.$idiomas->_("Función de valores").": ".$funcion_valores_horaria_sensor_procesado.$cadena_fin_parametro;
                        if ($misma_funcion_valores_cuartohoraria_sensor_procesado == VALOR_NO)
                        {
                            if ($funcion_valores_cuartohoraria_sensor_procesado == "")
                            {
                                $funcion_valores_cuartohoraria_sensor_procesado = $idiomas->_("ND");
                            }
                            $descripcion .= $cadena_inicio_parametro.$idiomas->_("Función de valores cuartohoraria").": ".
                                $funcion_valores_cuartohoraria_sensor_procesado.$cadena_fin_parametro;
                        }
                        break;
                    }
                }
                break;
            }
            case TIPO_SENSOR_EXTERNO:
            {
                $id_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];
                $clase_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                $opciones_generales = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES];
                $opciones_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];

                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Identificador externo").": ".$id_externo.$cadena_fin_parametro;
                $descripcion_clase_externo = NodoSensor::dame_descripcion_clase_sensor_externo($clase_externo);
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Clase de sensor externo").": ".$descripcion_clase_externo.$cadena_fin_parametro;
                switch ($clase_externo)
                {
                    case CLASE_SENSOR_EXTERNO_NINGUNA:
                    {
                        break;
                    }
                    default:
                    {
                        if ($opciones_generales != "")
                        {
                            $descripcion .= $cadena_inicio_parametro.$idiomas->_("Opciones generales").": ".dame_descripcion_parametros_opciones_generales_sensor_externo(
                                $clase_sensor_externo,
                                $opciones_generales,
                                $tipo_descripcion).$cadena_fin_parametro;
                        }
                        if ($opciones_valores != "")
                        {
                            $descripcion .= $cadena_inicio_parametro.$idiomas->_("Opciones de valores").": ".dame_descripcion_parametros_opciones_valores_sensor_externo(
                                $clase_sensor_externo,
                                $opciones_valores,
                                $tipo_descripcion).$cadena_fin_parametro;
                        }
                    }
                }
                break;
            }
        }
        $descripcion .= $cadena_fin_lista_parametros;

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_TEXTO:
            {
                $descripcion = substr($descripcion, 1);
                break;
            }
        }
        return ($descripcion);
    }


    function dame_descripcion_valor_parametro_accion_usuario_campo_parametros_extra($clase_sensor, $cadena_campo_parametros_extra, $tipo_descripcion)
    {
        $idiomas = new Idiomas();

        // Campo y parámetros extra
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $cadena_campo_parametros_extra);
        $campo = $campo_parametros_extra[0];
        $parametros_extra = $campo_parametros_extra[1];

        // Parámetros extra específicos de clase de sensor
        $descripcion = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_parametros_extra[0]);
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                $campo = $campo_parametros_extra[0];
                $parametros_extra = $campo_parametros_extra[1];
                switch ($campo)
                {
                    case CAMPO_GRADOS_HORA_CALEFACCION:
                    case CAMPO_GRADOS_HORA_REFRIGERACION:
                    case CAMPO_GRADOS_DIA_CALEFACCION:
                    case CAMPO_GRADOS_DIA_REFRIGERACION:
                    {
                        $descripcion .= " (".$idiomas->_("Referencia").": ".$parametros_extra.")";
                        break;
                    }
                }
                break;
            }
        }
        return ($descripcion);
    }


    function dame_descripcion_valor_parametro_accion_usuario_campos_hijo_sensor_procesado($clase_sensor, $cadena_campos, $tipo_descripcion)
    {
        $descripcion = "";
        $campos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_campos);
        foreach ($campos as $campo)
        {
            $descripcion_campo = dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
            if ($descripcion == "")
            {
                $descripcion .= $descripcion_campo;
            }
            else
            {
                $descripcion .= ", ".strtolower($descripcion_campo);
            }
        }
        return ($descripcion);
    }


    //
    // Módulo Actuadores
    //


    function dame_descripcion_valor_parametro_accion_usuario_parametros_clase_actuador($clase, $parametros_clase, $tipo_descripcion)
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

        // Parámetros específicos de clase de actuador
        $descripcion .= $cadena_inicio_lista_parametros;
        switch ($clase)
        {
            case CLASE_ACTUADOR_GENERICA:
            {
                $icono_mapa = dame_descripcion_icono_mapa($parametros_clase[INDICE_PARAMETRO_CLASE_ACTUADOR_GENERICA_ICONO]);

                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Icono de mapa").": ".$icono_mapa.$cadena_fin_parametro;
                break;
            }
        }
        $descripcion .= $cadena_fin_lista_parametros;

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_TEXTO:
            {
                $descripcion = substr($descripcion, 1);
                break;
            }
        }
        return ($descripcion);
    }


    function dame_descripcion_valor_parametro_accion_usuario_parametros_tipo_actuador($tipo, $parametros_tipo, $tipo_descripcion)
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

        // Parámetros específicos de tipo de actuador
        $descripcion .= $cadena_inicio_lista_parametros;
        switch ($tipo)
        {
            case TIPO_ACTUADOR_HARDWARE:
            {
                $nombre_axon = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON];
                $clase_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_CLASE_INTERFAZ];
                $ubicacion_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_UBICACION_INTERFAZ];
                $opciones_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_OPCIONES_INTERFAZ];
                restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_axon);

                if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                {
                    $nombre_axon = htmlspecialchars($nombre_axon, ENT_QUOTES);
                }
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Axón").": ".$nombre_axon.$cadena_fin_parametro;
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Clase de interfaz").": ".NodoActuador::dame_descripcion_clase_interfaz_actuador($clase_interfaz).$cadena_fin_parametro;
                if ($ubicacion_interfaz != "")
                {
                    $descripcion .= $cadena_inicio_parametro.$idiomas->_("Ubicación de interfaz").": ".dame_descripcion_parametros_ubicacion_interfaz_actuador_hardware(
                        $clase_interfaz,
                        $ubicacion_interfaz,
                        $tipo_descripcion).$cadena_fin_parametro;
                }
                if ($opciones_interfaz != "")
                {
                    $descripcion .= $cadena_inicio_parametro.$idiomas->_("Opciones de interfaz").": ".dame_descripcion_parametros_ubicacion_interfaz_actuador_software(
                        $clase_interfaz,
                        $ubicacion_interfaz,
                        $tipo_descripcion).$cadena_fin_parametro;
                }
                break;
            }
            case TIPO_ACTUADOR_SOFTWARE:
            {
                $clase_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_CLASE_INTERFAZ];
                $ubicacion_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_UBICACION_INTERFAZ];
                $opciones_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_OPCIONES_INTERFAZ];
                restaura_valor_parametro_nombre_elemento_accion_usuario($nombre_axon);

                if ($tipo_descripcion == TIPO_DESCRIPCION_HTML)
                {
                    $nombre_axon = htmlspecialchars($nombre_axon, ENT_QUOTES);
                }
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Clase de interfaz").": ".NodoActuador::dame_descripcion_clase_interfaz_actuador($clase_interfaz).$cadena_fin_parametro;
                if ($ubicacion_interfaz != "")
                {
                    $descripcion .= $cadena_inicio_parametro.$idiomas->_("Ubicación de interfaz").": ".dame_descripcion_parametros_opciones_interfaz_actuador_hardware(
                        $clase_interfaz,
                        $opciones_interfaz,
                        $tipo_descripcion).$cadena_fin_parametro;
                }
                if ($opciones_interfaz != "")
                {
                    $descripcion .= $cadena_inicio_parametro.$idiomas->_("Opciones de interfaz").": ".dame_descripcion_parametros_opciones_interfaz_actuador_software(
                        $clase_interfaz,
                        $opciones_interfaz,
                        $tipo_descripcion).$cadena_fin_parametro;
                }
                break;
            }
        }
        $descripcion .= $cadena_fin_lista_parametros;

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_TEXTO:
            {
                $descripcion = substr($descripcion, 1);
                break;
            }
        }
        return ($descripcion);
    }


    //
    // Módulo SmartMeter
    //


    function dame_descripcion_valores_tramos_tarifa_electrica($parametro_accion, $valores_tramo, $tipo_descripcion)
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

        // Descripción de los valores de los tramos
        $descripcion .= $cadena_inicio_lista_parametros;
        foreach ($valores_tramo as $numero_tramo => $valor_tramo)
        {
            $descripcion .= $cadena_inicio_parametro.$idiomas->_("Tramo")." ".$numero_tramo.": ".$valor_tramo;
            switch ($parametro_accion)
            {
                case PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS_TARIFA_ELECTRICA:
                case PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TARIFA_ACCESO_TRAMOS_TARIFA_ELECTRICA:
                {
                    $descripcion .= " ".$idiomas->_("€")."/".$idiomas->_("kWh");
                    break;
                }
                case PARAMETRO_ACCION_USUARIO_POTENCIAS_TRAMOS_TARIFA_ELECTRICA:
                {
                    $descripcion .= " ".$idiomas->_("kW");
                    break;
                }
                case PARAMETRO_ACCION_USUARIO_PRECIOS_POTENCIAS_TRAMOS_TARIFA_ELECTRICA:
                {
                    $descripcion .= " ".$idiomas->_("€")."/".$idiomas->_("kWh")."-".$idiomas->_("día");
                    break;
                }
                default:
                {
                    break;
                }
            }
            $descripcion .= $cadena_fin_parametro;
        }
        $descripcion .= $cadena_fin_lista_parametros;

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_TEXTO:
            {
                $descripcion = substr($descripcion, 1);
                break;
            }
        }
        return ($descripcion);
    }


    //
    // Módulo Proyectos
    //


    function dame_descripcion_valor_parametro_accion_usuario_parametros_tipo_linea_base($tipo, $parametros_tipo, $tipo_descripcion)
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

        // Parámetros de tipo de línea base
        $descripcion .= $cadena_inicio_lista_parametros;
        switch ($tipo)
        {
            case TIPO_LINEA_BASE_PERIODICA:
            {
                $periodicidad_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_LINEA_BASE_PERIODICA_PERIODICIDAD_VALORES];
                $tipo_calculo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_LINEA_BASE_PERIODICA_TIPO_CALCULO_VALORES];

                $descripcion_periodicidad_valores = LineaBase::dame_descripcion_periodicidad_valores_linea_base_periodica($periodicidad_valores);
                $descripcion_tipo_calculo_valores = LineaBase::dame_descripcion_tipo_calculo_valores_linea_base_periodica($tipo_calculo_valores);
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Periodicidad de valores").": ".$descripcion_periodicidad_valores.$cadena_fin_parametro;
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Tipo de cálculo de valores").": ".$descripcion_tipo_calculo_valores.$cadena_fin_parametro;
                break;
            }
            case TIPO_LINEA_BASE_FUNCIONAL:
            {
                $funcion_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_LINEA_BASE_FUNCIONAL_FUNCION_VALORES];
                $descripcion .= $cadena_inicio_parametro.$idiomas->_("Función de valores").": ".$funcion_valores.$cadena_fin_parametro;
                break;
            }
        }
        $descripcion .= $cadena_fin_lista_parametros;

        // Se elimina el primer salto de línea (\n) si el tipo de descripción es texto
        switch ($tipo_descripcion)
        {
            case TIPO_DESCRIPCION_TEXTO:
            {
                $descripcion = substr($descripcion, 1);
                break;
            }
        }
        return ($descripcion);
    }
?>
