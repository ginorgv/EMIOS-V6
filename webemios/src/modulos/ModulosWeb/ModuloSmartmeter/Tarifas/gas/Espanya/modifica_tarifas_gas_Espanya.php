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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/TarifaGas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_TARIFAS_GAS, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $ids_tarifas_gas = $_POST['ids_tarifas_gas'];
    $tipo = $_POST['tipo'];
    $expiracion = $_POST['expiracion'];
    $cadena_fecha_expiracion_local_local = $_POST['fecha_expiracion'];
    $numero_dias_preaviso_expiracion = $_POST['numero_dias_preaviso_expiracion'];
    $factor_conversion = $_POST['factor_conversion'];
    $precio_consumo = $_POST['precio_consumo'];
    $precio_caudal_diario = $_POST['precio_caudal_diario'];
    $caudal_diario = $_POST['caudal_diario'];
    $precio_termino_fijo_diario = $_POST['precio_termino_fijo_diario'];
    $impuesto_gas = $_POST['impuesto_gas'];
    $tipo_alquiler_contador = $_POST['tipo_alquiler_contador'];
    $alquiler_contador = $_POST['alquiler_contador'];
    $iva = $_POST['iva'];
    $tramos = $_POST['tramos'];

    // Conversión de fechas
    $cadena_fecha_expiracion_base_datos_local = convierte_formato_fecha($cadena_fecha_expiracion_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

    // Ids de tarifas de gas
    $cadena_ids_tarifas_gas_consulta = dame_cadena_ids_consulta($ids_tarifas_gas);

    // Comprobaciones antes de modificar las tarifas de gas:
    // - Si no hay expiración de tarifa, se comprueba que ninguna tarifa pertenezca a ningún grupo
    // - Si hay expiración de tarifa,
    //   se comprueba que en las tarifas que pertenezcan a grupos no coincida la fecha de expiración con alguna otra tarifa del mismo grupo
    $modificar_tarifas = true;

    // Si no hay expiración de tarifa, se comprueba que ninguna tarifa pertenezca a ningún grupo
    if (($modificar_tarifas == true) && ($expiracion == EXPIRACION_TARIFA_NO))
    {
        $consulta_tarifas_gas = "
            SELECT nombre
            FROM ".TABLA_TARIFAS_GAS_ESPANYA."
            WHERE
                (id IN (".$cadena_ids_tarifas_gas_consulta."))
                AND (grupo <> '".$bd_red->_(ID_NINGUNO)."')";
        $res_tarifas_gas = $bd_red->ejecuta_consulta($consulta_tarifas_gas);
        if ($res_tarifas_gas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_gas."'");
        }

        if ($res_tarifas_gas->dame_numero_filas() > 0)
        {
            $modificar_tarifas = False;

            $error = $resultado["error"];
            $nombres_tarifas_gas = "";
            while ($fila_tarifa_gas = $res_tarifas_gas->dame_siguiente_fila())
            {
                if ($nombres_tarifas_gas != "")
                {
                    $nombres_tarifas_gas .= ", ";
                }
                else
                {
                    $nombres_tarifas_gas .= $fila_tarifa_gas["nombre"];
                }
            }

            $res = "ERROR";
            $msg = $idiomas->_("Las tarifas con grupo asignado tienen que tener expiración")."\n(".
                $nombres_tarifas_gas.")";
        }
    }

    // Si hay expiración de tarifa,
    // se comprueba que en las tarifas que pertenezcan a grupos no coincida la fecha de expiración con alguna otra tarifa del mismo grupo
    if (($modificar_tarifas == true) && ($expiracion == EXPIRACION_TARIFA_SI))
    {
        $consulta_tarifas_gas_grupo = "
            SELECT
                id,
                nombre,
                grupo
            FROM ".TABLA_TARIFAS_GAS_ESPANYA."
            WHERE
                (id IN (".$cadena_ids_tarifas_gas_consulta."))
                AND (grupo <> '".$bd_red->_(ID_NINGUNO)."')";
        $res_tarifas_gas_grupo = $bd_red->ejecuta_consulta($consulta_tarifas_gas_grupo);
        if ($res_tarifas_gas_grupo == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_gas_grupo."'");
        }
        while ($fila_tarifa_gas_grupo = $res_tarifas_gas_grupo->dame_siguiente_fila())
        {
            $id_tarifa = $fila_tarifa_gas_grupo["id"];
            $nombre_tarifa_gas = $fila_tarifa_gas_grupo["nombre"];
            $id_grupo_tarifa_gas = $fila_tarifa_gas_grupo["grupo"];

            $nombre_tarifas_gas = "";
            $consulta_tarifas_gas_fecha_expiracion = "
                SELECT
                    nombre
                FROM ".TABLA_TARIFAS_GAS_ESPANYA."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo_tarifa_gas)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')
                    AND (id <> '".$bd_red->_($id_tarifa)."')";
            $res_tarifas_gas_fecha_expiracion = $bd_red->ejecuta_consulta($consulta_tarifas_gas_fecha_expiracion);
            if ($res_tarifas_gas_fecha_expiracion == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_gas_fecha_expiracion."'");
            }
            if ($res_tarifas_gas_fecha_expiracion->dame_numero_filas() > 0)
            {
                $modificar_tarifas = False;
                if ($nombres_tarifas_gas != "")
                {
                    $nombres_tarifas_gas .= ", ";
                }
                else
                {
                    $nombres_tarifas_gas .= $fila_tarifa_gas_grupo["nombre"];
                }
            }
        }
        if ($modificar_tarifas == false)
        {
            $res = "ERROR";
            $msg = $idiomas->_("La fecha de expiración de las tarifas con grupo asignado coincide con otras tarifas del mismo grupo")."\n(".
                $nombres_tarifas_gas.")";
        }
    }

    // Se modifican las tarifas de gas
    if ($modificar_tarifas == true)
    {
        // Flag de tarifas de gas modificadas
        $tarifas_gas_modificadas = false;

        // Características de tipo de tarifa de gas
        $caracteristicas_tipo_tarifa_gas = TarifaGas_Espanya::dame_caracteristicas_tipo_tarifa_gas($tipo);
        $tipo_calculo_coste_termino_fijo = $caracteristicas_tipo_tarifa_gas["tipo_calculo_coste_termino_fijo"];

        // Modificaciones en la tabla de tarifas de gas
        $info_campos_tarifas_gas = array();
        array_push($info_campos_tarifas_gas, array(
            "nombre" => "expiracion",
            "valor" => $expiracion,
            "valor_nulo" => EXPIRACION_TARIFA_NINGUNO));
        if ($expiracion != EXPIRACION_TARIFA_NINGUNO)
        {
            array_push($info_campos_tarifas_gas, array(
                "nombre" => "fecha_expiracion",
                "valor" => $cadena_fecha_expiracion_base_datos_local,
                "valor_nulo" => NULL));

            array_push($info_campos_tarifas_gas, array(
                "nombre" => "numero_dias_preaviso_expiracion",
                "valor" => $numero_dias_preaviso_expiracion,
                "valor_nulo" => ""));
        }
        switch ($tipo_calculo_coste_termino_fijo)
        {
            case TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES:
            {
                array_push($info_campos_tarifas_gas, array(
                    "nombre" => "factor_conversion",
                    "valor" => $factor_conversion,
                    "valor_nulo" => ""));
                array_push($info_campos_tarifas_gas, array(
                    "nombre" => "precio_consumo",
                    "valor" => $precio_consumo,
                    "valor_nulo" => ""));
                array_push($info_campos_tarifas_gas, array(
                    "nombre" => "precio_caudal_diario",
                    "valor" => $precio_caudal_diario,
                    "valor_nulo" => ""));
                array_push($info_campos_tarifas_gas, array(
                    "nombre" => "caudal_diario",
                    "valor" => $caudal_diario,
                    "valor_nulo" => ""));
                break;
            }
            case TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS:
            {
                array_push($info_campos_tarifas_gas, array(
                    "nombre" => "factor_conversion",
                    "valor" => $factor_conversion,
                    "valor_nulo" => ""));
                array_push($info_campos_tarifas_gas, array(
                    "nombre" => "precio_consumo",
                    "valor" => $precio_consumo,
                    "valor_nulo" => ""));
                array_push($info_campos_tarifas_gas, array(
                    "nombre" => "precio_termino_fijo_diario",
                    "valor" => $precio_termino_fijo_diario,
                    "valor_nulo" => ""));
                break;
            }
        }
        array_push($info_campos_tarifas_gas, array(
            "nombre" => "impuesto_gas",
            "valor" => $impuesto_gas,
            "valor_nulo" => ""));
        array_push($info_campos_tarifas_gas, array(
            "nombre" => "tipo_alquiler_contador",
            "valor" => $tipo_alquiler_contador,
            "valor_nulo" => TIPO_ALQUILER_CONTADOR_NINGUNO));
        if ($tipo_alquiler_contador != TIPO_ALQUILER_CONTADOR_NINGUNO)
        {
            array_push($info_campos_tarifas_gas, array(
                "nombre" => "alquiler_contador",
                "valor" => $alquiler_contador,
                "valor_nulo" => ""));
        }
        array_push($info_campos_tarifas_gas, array(
            "nombre" => "iva",
            "valor" => $iva,
            "valor_nulo" => ""));
        $clausula_modificacion_tarifas_gas = dame_clausula_modificacion_campos($bd_red, $info_campos_tarifas_gas);
        if ($clausula_modificacion_tarifas_gas != "")
        {
            $operacion_modificacion_tarifas_gas = "
                UPDATE ".TABLA_TARIFAS_GAS_ESPANYA."
                SET ";
            $operacion_modificacion_tarifas_gas .= $clausula_modificacion_tarifas_gas;
            $operacion_modificacion_tarifas_gas .= "
                WHERE
                    id IN (".$cadena_ids_tarifas_gas_consulta.")";
            $res_modificacion_tarifas_gas = $bd_red->ejecuta_operacion($operacion_modificacion_tarifas_gas);
            if ($res_modificacion_tarifas_gas == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_tarifas_gas."'");
            }
            $tarifas_gas_modificadas = true;
        }

        // Se añade la acción de usuario
        if ($tarifas_gas_modificadas == true)
        {
            anyade_accion_usuario_modificar_tarifas_gas_Espanya(
                $ids_tarifas_gas,
                $info_campos_tarifas_gas);
        }

        $res = "OK";
        if ($tarifas_gas_modificadas == true)
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
    function anyade_accion_usuario_modificar_tarifas_gas_Espanya(
        $ids_tarifas_gas,
        $info_campos_tarifas_gas)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICA_TARIFAS;
        $objeto_accion_usuario = NULL;

        // Nombres de parámetros
        $nombres_tarifas_gas = dame_nombres_tarifas(TABLA_TARIFAS_GAS_ESPANYA, $ids_tarifas_gas);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_GAS;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_TARIFAS] = $nombres_tarifas_gas;
        $info_campos_modificados_tarifas_gas = dame_info_campos_modificados($info_campos_tarifas_gas);
        foreach ($info_campos_modificados_tarifas_gas as $info_campo_modificado_tarifas_gas)
        {
            $nombre_campo_modificado = $info_campo_modificado_tarifas_gas["nombre"];
            $valor_campo_modificado = $info_campo_modificado_tarifas_gas["valor"];
            $parametro_accion_usuario = dame_parametro_accion_usuario_campo_tarifa_gas_Espanya($nombre_campo_modificado);
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


    // Devuelve el parámetro de la acción de usuario correspondiente al campo de la tarifa de gas
    function dame_parametro_accion_usuario_campo_tarifa_gas_Espanya($campo_tarifa_gas)
    {
        switch ($campo_tarifa_gas)
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
            case "factor_conversion":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_FACTOR_CONVERSION_TARIFA_GAS;
                break;
            }
            case "precio_consumo":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_TARIFA_GAS;
                break;
            }
            case "precio_caudal_diario":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_PRECIO_CAUDAL_DIARIO_TARIFA_GAS;
                break;
            }
            case "caudal_diario":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_CAUDAL_DIARIO_CONTRATADO_TARIFA_GAS;
                break;
            }
            case "precio_termino_fijo_diario":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_PRECIO_TERMINO_FIJO_DIARIO_GAS_ESPANYA;
                break;
            }
            case "impuesto_gas":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_IMPUESTO_TARIFA_GAS;
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
            case "iva":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_IVA;
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
