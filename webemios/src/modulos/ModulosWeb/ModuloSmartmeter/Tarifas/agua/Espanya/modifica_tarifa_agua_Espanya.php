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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/TarifaAgua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_TARIFA_AGUA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_tarifa_agua = $_POST['id_tarifa_agua'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $id_grupo = $_POST['id_grupo'];
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

    // Se comprueba si existe otra tarifa de agua con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_tarifa_agua)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una tarifa con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de modificar la tarifa de agua:
        // - Si hay grupo, se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas eléctricas del mismo grupo
        $modificar_tarifa = true;

        // Se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas de agua del mismo grupo
        if (($modificar_tarifa == true) && ($id_grupo != ID_NINGUNO))
        {
            $consulta_tarifas_agua = "
                SELECT nombre
                FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')
                    AND (id <> '".$bd_red->_($id_tarifa_agua)."')";
            $res_tarifas_agua = $bd_red->ejecuta_consulta($consulta_tarifas_agua);
            if ($res_tarifas_agua == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_agua."'");
            }
            if ($res_tarifas_agua->dame_numero_filas() > 0)
            {
                $modificar_tarifa = false;

                $fila_tarifa_agua = $res_tarifas_agua->dame_siguiente_fila();
                $nombre_tarifa_agua = $fila_tarifa_agua["nombre"];

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una tarifa en el mismo grupo con la misma fecha de expiración")."\n(".
                    $nombre_tarifa_agua.")";
            }
        }

        // Se modifica la tarifa de agua
        if ($modificar_tarifa == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_tarifa_agua_anterior = dame_fila_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa_agua);

            // Se modifica la tarifa de agua
            $operacion_modificacion = "
                UPDATE ".TABLA_TARIFAS_AGUA_ESPANYA."
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    tipo = '".$bd_red->_($tipo)."',
                    grupo = '".$bd_red->_($id_grupo)."',
                    expiracion = '".$bd_red->_($expiracion)."',
                    fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."',
                    numero_dias_preaviso_expiracion = '".$bd_red->_($numero_dias_preaviso_expiracion)."',
                    tipo_limites_consumo_tramos = '".$bd_red->_($tipo_limites_consumo_tramos)."',
                    limites_consumo_tramos = '".$bd_red->_($cadena_limites_consumo_tramos)."',
                    precios_consumo_tramos = '".$bd_red->_($cadena_precios_consumo_tramos)."',
                    tipo_alquiler_contador = '".$bd_red->_($tipo_alquiler_contador)."',
                    alquiler_contador = '".$bd_red->_($alquiler_contador)."',
                    iva_consumo = '".$bd_red->_($iva_consumo)."',
                    iva_alquiler_contador = '".$bd_red->_($iva_alquiler_contador)."',
                    igic_consumo = '".$bd_red->_($igic_consumo)."',
                    igic_alquiler_contador = '".$bd_red->_($igic_alquiler_contador)."'
                WHERE
                    id = '".$bd_red->_($id_tarifa_agua)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }

            // Si tiene grupo asignado se asigna el grupo a los sensores que tenían asignada la tarifa de agua
            if ($id_grupo != ID_NINGUNO)
            {
                asigna_grupo_tarifas_sensores_tarifa(MEDICION_AGUA, $id_grupo, $id_tarifa_agua);
            }

            // Se recupera la fila de la tarifa actual
            $fila_tarifa_agua_actual = dame_fila_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa_agua);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_tarifa_agua_Espanya(
                $fila_tarifa_agua_actual,
                $fila_tarifa_agua_anterior);

            $res = "OK";
            $msg = $idiomas->_("Tarifa modificada correctamente");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de adición de la tarifa
    function anyade_accion_usuario_modificar_tarifa_agua_Espanya($fila_actual, $fila_anterior)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_TARIFA;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_AGUA;
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_AGUA;
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["descripcion"] != $fila_anterior["descripcion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_actual["descripcion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_anterior["descripcion"];
        }
        if ($fila_actual["tipo"] != $fila_anterior["tipo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_AGUA] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_AGUA] = $fila_anterior["tipo"];
        }
        if ($fila_actual["grupo"] != $fila_anterior["grupo"])
        {
            $nombre_grupo = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_AGUA_ESPANYA, $fila_actual["grupo"]);
            $nombre_grupo_anterior = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_AGUA_ESPANYA, $fila_anterior["grupo"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo_anterior;
        }
        if ($fila_actual["expiracion"] != $fila_anterior["expiracion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_EXPIRACION_TARIFA] = $fila_actual["expiracion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_EXPIRACION_TARIFA] = $fila_anterior["expiracion"];
        }
        if ($fila_actual["fecha_expiracion"] != $fila_anterior["fecha_expiracion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_EXPIRACION] = $fila_actual["fecha_expiracion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_EXPIRACION] = $fila_anterior["fecha_expiracion"];
        }
        if (($fila_actual["numero_dias_preaviso_expiracion"] != $fila_anterior["numero_dias_preaviso_expiracion"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_PREAVISO_EXPIRACION] = $fila_actual["numero_dias_preaviso_expiracion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_PREAVISO_EXPIRACION] = $fila_anterior["numero_dias_preaviso_expiracion"];
        }
        if ($fila_actual["tipo_limites_consumo_tramos"] != $fila_anterior["tipo_limites_consumo_tramos"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_LIMITES_CONSUMO_TRAMOS_TARIFA_AGUA] = $fila_actual["tipo_limites_consumo_tramos"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_LIMITES_CONSUMO_TRAMOS_TARIFA_AGUA] = $fila_anterior["tipo_limites_consumo_tramos"];
        }
        if ($fila_actual["limites_consumo_tramos"] != $fila_anterior["limites_consumo_tramos"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LIMITES_CONSUMO_TRAMOS] = $fila_actual["limites_consumo_tramos"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LIMITES_CONSUMO_TRAMOS] = $fila_anterior["limites_consumo_tramos"];
        }
        if ($fila_actual["precios_consumo_tramos"] != $fila_anterior["precios_consumo_tramos"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS] = $fila_actual["precios_consumo_tramos"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS] = $fila_anterior["precios_consumo_tramos"];
        }
        if ($fila_actual["alquiler_contador"] != $fila_anterior["alquiler_contador"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ALQUILER_CONTADOR] = $fila_actual["alquiler_contador"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ALQUILER_CONTADOR] = $fila_anterior["alquiler_contador"];
        }
        if ($fila_actual["tipo_alquiler_contador"] != $fila_anterior["tipo_alquiler_contador"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_ALQUILER_CONTADOR] = $fila_actual["tipo_alquiler_contador"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_ALQUILER_CONTADOR] = $fila_anterior["tipo_alquiler_contador"];
        }
        if ($fila_actual["iva_consumo"] != $fila_anterior["iva_consumo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA_CONSUMO] = $fila_actual["iva_consumo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IVA_CONSUMO] = $fila_anterior["iva_consumo"];
        }
        if ($fila_actual["iva_alquiler_contador"] != $fila_anterior["iva_alquiler_contador"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA_ALQUILER_CONTADOR] = $fila_actual["iva_alquiler_contador"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IVA_ALQUILER_CONTADOR] = $fila_anterior["iva_alquiler_contador"];
        }
        if ($fila_actual["igic_consumo"] != $fila_anterior["igic_consumo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IGIC_CONSUMO] = $fila_actual["igic_consumo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IGIC_CONSUMO] = $fila_anterior["igic_consumo"];
        }
        if ($fila_actual["igic_alquiler_contador"] != $fila_anterior["igic_alquiler_contador"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IGIC_ALQUILER_CONTADOR] = $fila_actual["igic_alquiler_contador"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IGIC_ALQUILER_CONTADOR] = $fila_anterior["igic_alquiler_contador"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        // (siempre se añade el parámetro de medición)
        if (count($parametros_accion_usuario) == 1)
        {
            return;
        }

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"];
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"],
                $fila_anterior["nombre"]));
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores,
            NULL);
    }
?>
