<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_RED, $_POST);

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
    $paises_tarifas = $_POST['paises_tarifas'];
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

    // Se comprueba si existe una red con el mismo identificador
    $consulta_existe_id = "
        SELECT id
        FROM redes
        WHERE
            id = '".$bd_red->_($id_red)."'";
    $res_existe_id = $bd_red->ejecuta_consulta($consulta_existe_id);
    if ($res_existe_id == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe_id->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una red con este identificador");
    }
    else
    {
        // Se comprueba si existe una red con el mismo nombre
        $consulta_existe_nombre = "
            SELECT nombre
            FROM redes
            WHERE
                nombre = '".$bd_red->_($nombre)."'";
        $res_existe_nombre = $bd_red->ejecuta_consulta($consulta_existe_nombre);
        if ($res_existe_nombre == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_existe_nombre."'");
        }
        if ($res_existe_nombre->dame_numero_filas() > 0)
        {
            $res = "ERROR";
            $msg = $idiomas->_("Ya existe una red con el mismo nombre");
        }
        else
        {
            // Se añade la red
            $operacion_insercion = "
                INSERT INTO redes (
                    id,
                    nombre,
                    cliente,
                    zona_horaria,
                    idioma,
                    tipo_formato_fecha_local,
                    separador_miles,
                    punto_decimal,
                    unidades_medida,
                    paises_tarifas,
                    medicion_defecto,
                    procesado_cuartohorario,
                    parametros_caducidad_valores,
                    direcciones_email_envio_notificaciones,
                    direccion_origen_email_informes_automaticos,
                    version_fuentes,
                    version_fuentes_web,
                    logo_personalizado,
                    nombre_logo,
                    url_logo,
                    titulo_web,
                    tema,
                    paleta_colores_graficas,
                    periodo_completo_informes_defecto,
                    tipo_mapa,
                    nombre_mapa,
                    factor_reduccion_imagen_mapa_local,
                    etiquetas_mapa,
                    latitud_mapa_defecto,
                    longitud_mapa_defecto,
                    zoom_mapa_defecto
                ) VALUES (
                    '".$bd_red->_($id_red)."',
                    '".$bd_red->_($nombre)."',
                    '".$bd_red->_($id_cliente)."',
                    '".$bd_red->_($zona_horaria)."',
                    '".$bd_red->_($idioma)."',
                    '".$bd_red->_($tipo_formato_fecha_local)."',
                    '".$bd_red->_($separador_miles)."',
                    '".$bd_red->_($punto_decimal)."',
                    '".$bd_red->_($unidades_medida)."',
                    '".$bd_red->_($paises_tarifas)."',
                    '".$bd_red->_($medicion_defecto)."',
                    '".$bd_red->_($procesado_cuartohorario)."',
                    '".$bd_red->_($parametros_caducidad_valores)."',
                    '".$bd_red->_($direcciones_email_envio_notificaciones)."',
                    '".$bd_red->_($direccion_origen_email_informes_automaticos)."',
                    '".$bd_red->_($version_fuentes)."',
                    '".$bd_red->_($version_fuentes_web)."',
                    '".$bd_red->_($logo_personalizado)."',
                    '".$bd_red->_($nombre_logo)."',
                    '".$bd_red->_($url_logo)."',
                    '".$bd_red->_($titulo_web)."',
                    '".$bd_red->_($tema)."',
                    '".$bd_red->_($paleta_colores_graficas)."',
                    '".$bd_red->_($periodo_completo_informes_defecto)."',
                    '".$bd_red->_($tipo_mapa)."',
                    '".$bd_red->_($nombre_mapa)."',
                    '".$bd_red->_($factor_reduccion_imagen_mapa_local)."',
                    '".$bd_red->_($etiquetas_mapa)."',
                    '".$bd_red->_($latitud_mapa_defecto)."',
                    '".$bd_red->_($longitud_mapa_defecto)."',
                    '".$bd_red->_($zoom_mapa_defecto)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                $res = "OK";
                $msg = $idiomas->_("Red añadida correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_red" => $id_red))
    );
?>
