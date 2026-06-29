<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_RED, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_red = $_POST['id_red'];
    $nombre = $_POST['nombre'];
    $id_cliente = $_POST['id_cliente'];
    $zona_horaria = $_POST['zona_horaria'];
    $idioma = $_POST['idioma'];
    $tipo_formato_fecha_local = $_POST['tipo_formato_fecha_local'];
    $separador_miles = $_POST['separador_miles'];
    $punto_decimal = $_POST['punto_decimal'];
    $unidades_medida = $_POST['unidades_medida'];
    $cadena_paises_tarifas = $_POST['paises_tarifas'];
    $medicion_defecto = $_POST['medicion_defecto'];
    $procesado_cuartohorario = $_POST['procesado_cuartohorario'];
    $parametros_caducidad_valores = $_POST['parametros_caducidad_valores'];
    $direcciones_email_envio_notificaciones = $_POST['direcciones_email_envio_notificaciones'];
    $direccion_origen_email_informes_automaticos = $_POST['direccion_origen_email_informes_automaticos'];
    $version_fuentes = $_POST['version_fuentes'];
    $version_fuentes_web = $_POST['version_fuentes_web'];
    $logo_personalizado = $_POST['logo_personalizado'];
    $nombre_logo = $_POST['nombre_logo'];
    $url_logo = $_POST['url_logo'];
    $titulo_web = $_POST['titulo_web'];
    $tema = $_POST['tema'];
    $paleta_colores_graficas = $_POST['paleta_colores_graficas'];
    $periodo_completo_informes_defecto = $_POST['periodo_completo_informes_defecto'];
    $tipo_mapa = $_POST['tipo_mapa'];
    $nombre_mapa = $_POST['nombre_mapa'];
    $factor_reduccion_imagen_mapa_local = $_POST['factor_reduccion_imagen_mapa_local'];
    $etiquetas_mapa = $_POST['etiquetas_mapa'];
    $latitud_mapa_defecto = $_POST['latitud_mapa_defecto'];
    $longitud_mapa_defecto = $_POST['longitud_mapa_defecto'];
    $zoom_mapa_defecto = $_POST['zoom_mapa_defecto'];

    // Parámetros auxiliares
    $pais_tarifas_electricas_anterior = $_POST['pais_tarifas_electricas_anterior'];
    $pais_tarifas_gas_anterior = $_POST['pais_tarifas_gas_anterior'];
    $pais_tarifas_agua_anterior = $_POST['pais_tarifas_agua_anterior'];
    $logo_personalizado_anterior = $_POST['logo_personalizado_anterior'];
    $tipo_mapa_anterior = $_POST['tipo_mapa_anterior'];

    // Flag de red actual
    $red_actual = VALOR_NO;
    if ($_SESSION["id_red"] == $id_red)
    {
        $red_actual = VALOR_SI;
    }

    // Se comprueba si existe otra red con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM redes
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (id <> '".$bd_red->_($id_red)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una red con el mismo nombre");
    }
    else
    {
        // Si antes había logo personalizado y ahora no, se eliminan los logos anteriores
        if (($logo_personalizado_anterior == VALOR_SI) && ($logo_personalizado == VALOR_NO))
        {
            elimina_imagen_base_datos(ORIGEN_IMAGEN_RED_LOGO, $id_red);
            elimina_imagen_base_datos(ORIGEN_IMAGEN_RED_LOGO_PDF, $id_red);
        }

        // Si el tipo de mapa anterior era local y ahora es internet, se elimina la imagen anterior
        if (($tipo_mapa_anterior == TIPO_MAPA_LOCAL) && ($tipo_mapa == TIPO_MAPA_INTERNET))
        {
            elimina_imagen_base_datos(ORIGEN_IMAGEN_RED_MAPA, $id_red);
        }

        // Se elimina las posiciones del mapa si se ha modificado el tipo de mapa
        if ($tipo_mapa_anterior != $tipo_mapa)
        {
            elimina_info_posiciones_mapa_origen_base_datos(ORIGEN_MAPA_RED, $id_red);

            // Se recorren las localizaciones y si no tienen mapa personalizado
            // se eliminan las posiciones del mapa de esa localización (están utilizando el mapa de la red)
            $consulta_localizaciones = "
                SELECT id
                FROM localizaciones
                WHERE
                    (red = '".$bd_red->_($id_red)."')
                    AND (mapa_personalizado = '".VALOR_NO."')";
            $res_localizaciones = $bd_red->ejecuta_consulta($consulta_localizaciones);
            if ($res_localizaciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_localizaciones."'");
            }
            while ($fila_localizacion = $res_localizaciones->dame_siguiente_fila())
            {
                elimina_info_posiciones_mapa_origen_base_datos(ORIGEN_MAPA_LOCALIZACION, $fila_localizacion["id"]);
            }
        }

        // Se modifica la red
        $operacion_modificacion = "
            UPDATE redes
            SET
                nombre = '".$bd_red->_($nombre)."',
                cliente = '".$bd_red->_($id_cliente)."',
                zona_horaria = '".$bd_red->_($zona_horaria)."',
                idioma = '".$bd_red->_($idioma)."',
                tipo_formato_fecha_local = '".$bd_red->_($tipo_formato_fecha_local)."',
                separador_miles = '".$bd_red->_($separador_miles)."',
                punto_decimal = '".$bd_red->_($punto_decimal)."',
                unidades_medida = '".$bd_red->_($unidades_medida)."',
                paises_tarifas = '".$bd_red->_($cadena_paises_tarifas)."',
                medicion_defecto = '".$bd_red->_($medicion_defecto)."',
                procesado_cuartohorario = '".$bd_red->_($procesado_cuartohorario)."',
                parametros_caducidad_valores = '".$bd_red->_($parametros_caducidad_valores)."',
                direcciones_email_envio_notificaciones = '".$bd_red->_($direcciones_email_envio_notificaciones)."',
                direccion_origen_email_informes_automaticos = '".$bd_red->_($direccion_origen_email_informes_automaticos)."',
                version_fuentes = '".$bd_red->_($version_fuentes)."',
                version_fuentes_web = '".$bd_red->_($version_fuentes_web)."',
                logo_personalizado = '".$bd_red->_($logo_personalizado)."',
                nombre_logo = '".$bd_red->_($nombre_logo)."',
                url_logo = '".$bd_red->_($url_logo)."',
                titulo_web = '".$bd_red->_($titulo_web)."',
                tema = '".$bd_red->_($tema)."',
                paleta_colores_graficas = '".$bd_red->_($paleta_colores_graficas)."',
                periodo_completo_informes_defecto = '".$bd_red->_($periodo_completo_informes_defecto)."',
                tipo_mapa = '".$bd_red->_($tipo_mapa)."',
                nombre_mapa = '".$bd_red->_($nombre_mapa)."',
                factor_reduccion_imagen_mapa_local = '".$bd_red->_($factor_reduccion_imagen_mapa_local)."',
                etiquetas_mapa = '".$bd_red->_($etiquetas_mapa)."',
                latitud_mapa_defecto = '".$bd_red->_($latitud_mapa_defecto)."',
                longitud_mapa_defecto = '".$bd_red->_($longitud_mapa_defecto)."',
                zoom_mapa_defecto = '".$bd_red->_($zoom_mapa_defecto)."'
            WHERE
                id = '".$bd_red->_($id_red)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Si se han modificado los países de tarifas,
            // se establecen los parámetros por defecto de los sensores de las clases correspondientes
            $paises_tarifas = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_paises_tarifas);
            $pais_tarifas_electricas = $paises_tarifas[INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_ELECTRICAS];
            $pais_tarifas_gas = $paises_tarifas[INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_GAS];
            $pais_tarifas_agua = $paises_tarifas[INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_AGUA];
            if ($pais_tarifas_electricas != $pais_tarifas_electricas_anterior)
            {
                establece_parametros_clase_defecto_sensores_clase_pais_tarifas($id_red, CLASE_SENSOR_ENERGIA_ACTIVA, $pais_tarifas_electricas);
            }
            if ($pais_tarifas_gas != $pais_tarifas_gas_anterior)
            {
                establece_parametros_clase_defecto_sensores_clase_pais_tarifas($id_red, CLASE_SENSOR_GAS, $pais_tarifas_gas);
            }
            if ($pais_tarifas_agua != $pais_tarifas_agua_anterior)
            {
                establece_parametros_clase_defecto_sensores_clase_pais_tarifas($id_red, CLASE_SENSOR_AGUA, $pais_tarifas_agua);
            }

            $res = "OK";
            $msg = $idiomas->_("Red modificada correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "red_actual" => $red_actual))
    );


    //
    // Funciones auxiliares
    //


    // Establece los parámetros de clase por defecto de los sensores según el país de tarifas
    function establece_parametros_clase_defecto_sensores_clase_pais_tarifas($id_red, $clase_sensor, $pais_tarifas)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los parámetros por defecto de la clase correspondiente según el país de tarifas
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $parametros_clase = dame_parametros_defecto_clase_energia_activa_pais_tarifas($pais_tarifas);
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $parametros_clase = dame_parametros_defecto_clase_gas_pais_tarifas($pais_tarifas);
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $parametros_clase = dame_parametros_defecto_clase_agua_pais_tarifas($pais_tarifas);
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor incorrecta: '".$clase_sensor."'");
            }
        }
        $cadena_parametros_clase = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_clase);

        // Se modifican los sensores correspondientes
        $operacion_modificacion = "
            UPDATE sensores
            SET
                parametros_clase = '".$bd_red->_($cadena_parametros_clase)."'
            WHERE
                (red = '".$bd_red->_($id_red)."')
                AND (clase = '".$bd_red->_($clase_sensor)."')";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }

        // Se notifica la modificacion de cada uno de los sensores
        // (para actualizar sus últimos valores de clase correspondientes)
        $consulta_sensores = "
            SELECT *
            FROM sensores
            WHERE
                (red = '".$bd_red->_($id_red)."')
                AND (clase = '".$bd_red->_($clase_sensor)."')";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            $id_sensor = $fila_sensor["id"];
            $tipo_sensor = $fila_sensor["tipo"];

            switch ($tipo_sensor)
            {
                case TIPO_SENSOR_EXTERNO:
                {
                    $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_tipo"]);
                    $clase_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                    $parametros_extra = array(
                        "clase_sensor_externo" => $clase_sensor_externo,
                        "clase_sensor_externo_anterior" => $clase_sensor_externo);
                    break;
                }
                default:
                {
                    $parametros_extra = array();
                    break;
                }
            }
            notifica_operacion_administracion_sensor($tipo_sensor, OPERACION_MODIFICACION, $id_sensor, $parametros_extra);
        }
    }
?>
