<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/TarifaAgua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_TARIFAS_AGUA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $ids_tarifas_agua = $_POST['ids_tarifas_agua'];
    $tipo = $_POST['tipo'];
    $expiracion = $_POST['expiracion'];
    $cadena_fecha_expiracion_local_local = $_POST['fecha_expiracion'];
    $numero_dias_preaviso_expiracion = $_POST['numero_dias_preaviso_expiracion'];
    $tipo_limites_consumo_tramos = $_POST['tipo_limites_consumo_tramos'];
    $cadena_limites_consumo_tramos = $_POST['limites_consumo_tramos'];
    $cadena_precios_consumo_tramos = $_POST['precios_consumo_tramos'];
    $tipo_alquiler_contador = $_POST['tipo_alquiler_contador'];
    $alquiler_contador = $_POST['alquiler_contador'];
    $iva_consumo = $_POST['iva_consumo'];
    $igic_consumo = $_POST['igic_consumo'];
    $iva_alquiler_contador = $_POST['iva_alquiler_contador'];
    $igic_alquiler_contador = $_POST['igic_alquiler_contador'];

    // Conversión de fechas
    $cadena_fecha_expiracion_base_datos_local = convierte_formato_fecha($cadena_fecha_expiracion_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

    // Ids de tarifas de agua
    $cadena_ids_tarifas_agua_consulta = dame_cadena_ids_consulta($ids_tarifas_agua);

    // Comprobaciones antes de modificar las tarifas de agua:
    // - Si no hay expiración de tarifa, se comprueba que ninguna tarifa pertenezca a ningún grupo
    // - Si hay expiración de tarifa,
    //   se comprueba que en las tarifas que pertenezcan a grupos no coincida la fecha de expiración con alguna otra tarifa del mismo grupo
    $modificar_tarifas = true;

    // Si no hay expiración de tarifa, se comprueba que ninguna tarifa pertenezca a ningún grupo
    if (($modificar_tarifas == true) && ($expiracion == EXPIRACION_TARIFA_NO))
    {
        $consulta_tarifas_agua = "
            SELECT nombre
            FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
            WHERE
                (id IN (".$cadena_ids_tarifas_agua_consulta."))
                AND (grupo <> '".$bd_red->_(ID_NINGUNO)."')";
        $res_tarifas_agua = $bd_red->ejecuta_consulta($consulta_tarifas_agua);
        if ($res_tarifas_agua == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_agua."'");
        }

        if ($res_tarifas_agua->dame_numero_filas() > 0)
        {
            $modificar_tarifas = False;

            $error = $resultado["error"];
            $nombres_tarifas_agua = "";
            while ($fila_tarifa_agua = $res_tarifas_agua->dame_siguiente_fila())
            {
                if ($nombres_tarifas_agua != "")
                {
                    $nombres_tarifas_agua .= ", ";
                }
                else
                {
                    $nombres_tarifas_agua .= $fila_tarifa_agua["nombre"];
                }
            }

            $res = "ERROR";
            $msg = $idiomas->_("Las tarifas con grupo asignado tienen que tener expiración")."\n(".
                $nombres_tarifas_agua.")";
        }
    }

    // Si hay expiración de tarifa,
    // se comprueba que en las tarifas que pertenezcan a grupos no coincida la fecha de expiración con alguna otra tarifa del mismo grupo
    if (($modificar_tarifas == true) && ($expiracion == EXPIRACION_TARIFA_SI))
    {
        $consulta_tarifas_agua_grupo = "
            SELECT
                id,
                nombre,
                grupo
            FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
            WHERE
                (id IN (".$cadena_ids_tarifas_agua_consulta."))
                AND (grupo <> '".$bd_red->_(ID_NINGUNO)."')";
        $res_tarifas_agua_grupo = $bd_red->ejecuta_consulta($consulta_tarifas_agua_grupo);
        if ($res_tarifas_agua_grupo == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_agua_grupo."'");
        }
        while ($fila_tarifa_agua_grupo = $res_tarifas_agua_grupo->dame_siguiente_fila())
        {
            $id_tarifa = $fila_tarifa_agua_grupo["id"];
            $nombre_tarifa_agua = $fila_tarifa_agua_grupo["nombre"];
            $id_grupo_tarifa_agua = $fila_tarifa_agua_grupo["grupo"];

            $nombre_tarifas_agua = "";
            $consulta_tarifas_agua_fecha_expiracion = "
                SELECT nombre
                FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo_tarifa_agua)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')
                    AND (id <> '".$bd_red->_($id_tarifa)."')";
            $res_tarifas_agua_fecha_expiracion = $bd_red->ejecuta_consulta($consulta_tarifas_agua_fecha_expiracion);
            if ($res_tarifas_agua_fecha_expiracion == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_agua_fecha_expiracion."'");
            }
            if ($res_tarifas_agua_fecha_expiracion->dame_numero_filas() > 0)
            {
                $modificar_tarifas = False;
                if ($nombres_tarifas_agua != "")
                {
                    $nombres_tarifas_agua .= ", ";
                }
                else
                {
                    $nombres_tarifas_agua .= $fila_tarifa_agua_grupo["nombre"];
                }
            }
        }
        if ($modificar_tarifas == false)
        {
            $res = "ERROR";
            $msg = $idiomas->_("La fecha de expiración de las tarifas con grupo asignado coincide con otras tarifas del mismo grupo")."\n(".
                $nombres_tarifas_agua.")";
        }
    }

    // Se modifican las tarifas de agua
    if ($modificar_tarifas == true)
    {
        // Flag de tarifas de agua modificadas
        $tarifas_agua_modificadas = false;

        // Características de tipo de tarifa de agua
        $caracteristicas_tipo_tarifa_agua = TarifaAgua_Espanya::dame_caracteristicas_tipo_tarifa_agua($tipo);
        $tipo_calculo_coste_termino_fijo = $caracteristicas_tipo_tarifa_agua["tipo_calculo_coste_termino_fijo"];

        // Modificaciones en la tabla de tarifas de agua
        $info_campos_tarifas_agua = array();
        array_push($info_campos_tarifas_agua, array(
            "nombre" => "expiracion",
            "valor" => $expiracion,
            "valor_nulo" => EXPIRACION_TARIFA_NINGUNO));
        if ($expiracion != EXPIRACION_TARIFA_NINGUNO)
        {
            array_push($info_campos_tarifas_agua, array(
                "nombre" => "fecha_expiracion",
                "valor" => $cadena_fecha_expiracion_base_datos_local,
                "valor_nulo" => NULL));

            array_push($info_campos_tarifas_agua, array(
                "nombre" => "numero_dias_preaviso_expiracion",
                "valor" => $numero_dias_preaviso_expiracion,
                "valor_nulo" => ""));
        }
        array_push($info_campos_tarifas_agua, array(
            "nombre" => "tipo_limites_consumo_tramos",
            "valor" => $tipo_limites_consumo_tramos,
            "valor_nulo" => TIPO_LIMITES_CONSUMO_TRAMOS_NINGUNO));
        if ($cadena_precios_consumo_tramos != "")
        {
            array_push($info_campos_tarifas_agua, array(
                "nombre" => "limites_consumo_tramos",
                "valor" => $cadena_limites_consumo_tramos,
                "valor_nulo" => NULL));
            array_push($info_campos_tarifas_agua, array(
                "nombre" => "precios_consumo_tramos",
                "valor" => $cadena_precios_consumo_tramos,
                "valor_nulo" => NULL));
        }
        array_push($info_campos_tarifas_agua, array(
            "nombre" => "tipo_alquiler_contador",
            "valor" => $tipo_alquiler_contador,
            "valor_nulo" => TIPO_ALQUILER_CONTADOR_NINGUNO));
        if ($tipo_alquiler_contador != TIPO_ALQUILER_CONTADOR_NINGUNO)
        {
            array_push($info_campos_tarifas_agua, array(
                "nombre" => "alquiler_contador",
                "valor" => $alquiler_contador,
                "valor_nulo" => ""));
        }
        array_push($info_campos_tarifas_agua, array(
            "nombre" => "iva_consumo",
            "valor" => $iva_consumo,
            "valor_nulo" => ""));
        array_push($info_campos_tarifas_agua, array(
            "nombre" => "iva_alquiler_contador",
            "valor" => $iva_alquiler_contador,
            "valor_nulo" => ""));
        array_push($info_campos_tarifas_agua, array(
            "nombre" => "igic_consumo",
            "valor" => $igic_consumo,
            "valor_nulo" => ""));
        array_push($info_campos_tarifas_agua, array(
            "nombre" => "igic_alquiler_contador",
            "valor" => $igic_alquiler_contador,
            "valor_nulo" => ""));
        $clausula_modificacion_tarifas_agua = dame_clausula_modificacion_campos($bd_red, $info_campos_tarifas_agua);
        if ($clausula_modificacion_tarifas_agua != "")
        {
            $operacion_modificacion_tarifas_agua = "
                UPDATE ".TABLA_TARIFAS_AGUA_ESPANYA."
                SET ";
            $operacion_modificacion_tarifas_agua .= $clausula_modificacion_tarifas_agua;
            $operacion_modificacion_tarifas_agua .= "
                WHERE
                    id IN (".$cadena_ids_tarifas_agua_consulta.")";
            $res_modificacion_tarifas_agua = $bd_red->ejecuta_operacion($operacion_modificacion_tarifas_agua);
            if ($res_modificacion_tarifas_agua == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_tarifas_agua."'");
            }
            $tarifas_agua_modificadas = true;
        }

        // Se añade la acción de usuario
        if ($tarifas_agua_modificadas == true)
        {
            anyade_accion_usuario_modificar_tarifas_agua_Espanya(
                $ids_tarifas_agua,
                $info_campos_tarifas_agua);
        }

        $res = "OK";
        if ($tarifas_agua_modificadas == true)
        {
            $tipo_mensaje = TIPO_MENSAJE_INFORMACION;
            $msg = $idiomas->_("Tarifas modificadas correctamente");
        }
        else
        {
            $tipo_mensaje = TIPO_MENSAJE_AVISO;
            $msg = $idiomas->_("No hay modificaciones que realizar en las tarifas");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "tipo_mensaje" => $tipo_mensaje,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación de tarifas
    function anyade_accion_usuario_modificar_tarifas_agua_Espanya(
        $ids_tarifas_agua,
        $info_campos_tarifas_agua)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICA_TARIFAS;
        $objeto_accion_usuario = NULL;

        // Nombres de parámetros
        $nombres_tarifas_agua = dame_nombres_tarifas(TABLA_TARIFAS_AGUA_ESPANYA, $ids_tarifas_agua);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_AGUA;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_TARIFAS] = $nombres_tarifas_agua;
        $info_campos_modificados_tarifas_agua = dame_info_campos_modificados($info_campos_tarifas_agua);
        foreach ($info_campos_modificados_tarifas_agua as $info_campo_modificado_tarifas_agua)
        {
            $nombre_campo_modificado = $info_campo_modificado_tarifas_agua["nombre"];
            $valor_campo_modificado = $info_campo_modificado_tarifas_agua["valor"];
            $parametro_accion_usuario = dame_parametro_accion_usuario_campo_tarifa_agua_Espanya($nombre_campo_modificado);
            $parametros_accion_usuario[$parametro_accion_usuario] = $valor_campo_modificado;
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }


    // Devuelve el parámetro de la acción de usuario correspondiente al campo de la tarifa de agua
    function dame_parametro_accion_usuario_campo_tarifa_agua_Espanya($campo_tarifa_agua)
    {
        switch ($campo_tarifa_agua)
        {
            case "expiracion":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_EXPIRACION;
                break;
            }
            case "fecha_expiracion":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_FECHA_EXPIRACION;
                break;
            }
            case "numero_dias_preaviso_expiracion":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_PREAVISO_EXPIRACION;
                break;
            }
            case "tipo_limites_consumo_tramos":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_TIPO_LIMITES_CONSUMO_TRAMOS_TARIFA_AGUA;
                break;
            }
            case "limites_consumo_tramos":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_LIMITES_CONSUMO_TRAMOS;
                break;
            }
            case "precios_consumo_tramos":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS;
                break;
            }
            case "tipo_alquiler_contador":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_TIPO_ALQUILER_CONTADOR;
                break;
            }
            case "alquiler_contador":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_ALQUILER_CONTADOR;
                break;
            }
            case "iva_consumo":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_IVA_CONSUMO;
                break;
            }
            case "iva_alquiler_contador":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_IVA_ALQUILER_CONTADOR;
                break;
            }
            case "igic_consumo":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_IGIC_CONSUMO;
                break;
            }
            case "igic_alquiler_contador":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_IGIC_ALQUILER_CONTADOR;
                break;
            }
            default:
            {
                $parametro_accion_usuario = "Desconocido";
                break;
            }
        }
        return ($parametro_accion_usuario);
    }
?>
