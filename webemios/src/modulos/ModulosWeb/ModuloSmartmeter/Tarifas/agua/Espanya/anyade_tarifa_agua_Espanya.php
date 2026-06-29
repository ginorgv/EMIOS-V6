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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_TARIFA_AGUA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_tarifa_agua_anterior = $_POST['id_tarifa_agua_anterior'];
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

	// Se comprueba si existe una tarifa de agua con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')";
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
        // Comprobaciones antes de añadir la tarifa de agua:
        // - Si hay grupo, se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas de agua del mismo grupo
        $anyadir_tarifa = true;

        // Se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas de agua del mismo grupo
        if (($anyadir_tarifa == true) && ($id_grupo != ID_NINGUNO))
        {
            $consulta_tarifas_agua = "
                SELECT nombre
                FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')";
            $res_tarifas_agua = $bd_red->ejecuta_consulta($consulta_tarifas_agua);
            if ($res_tarifas_agua == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_agua."'");
            }
            if ($res_tarifas_agua->dame_numero_filas() > 0)
            {
                $anyadir_tarifa = False;

                $fila_tarifa_agua = $res_tarifas_agua->dame_siguiente_fila();
                $nombre_tarifa_agua = $fila_tarifa_agua["nombre"];

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una tarifa en el mismo grupo con la misma fecha de expiración")."\n(".
                    $nombre_tarifa_agua.")";
            }
        }

        // Se añade la tarifa de agua
        if ($anyadir_tarifa == true)
        {
            // Se añade la tarifa de agua
            $operacion_insercion = "
                INSERT INTO ".TABLA_TARIFAS_AGUA_ESPANYA." (
                    nombre,
                    red,
                    descripcion,
                    tipo,
                    grupo,
                    expiracion,
                    fecha_expiracion,
                    numero_dias_preaviso_expiracion,
                    tipo_limites_consumo_tramos,
                    limites_consumo_tramos,
                    precios_consumo_tramos,
                    tipo_alquiler_contador,
                    alquiler_contador,
                    iva_consumo,
                    igic_consumo,
                    iva_alquiler_contador,
                    igic_alquiler_contador
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($descripcion)."',
                    '".$bd_red->_($tipo)."',
                    '".$bd_red->_($id_grupo)."',
                    '".$bd_red->_($expiracion)."',
                    '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."',
                    '".$bd_red->_($numero_dias_preaviso_expiracion)."',
                    '".$bd_red->_($tipo_limites_consumo_tramos)."',
                    '".$bd_red->_($cadena_limites_consumo_tramos)."',
                    '".$bd_red->_($cadena_precios_consumo_tramos)."',
                    '".$bd_red->_($tipo_alquiler_contador)."',
                    '".$bd_red->_($alquiler_contador)."',
                    '".$bd_red->_($iva_consumo)."',
                    '".$bd_red->_($igic_consumo)."',
                    '".$bd_red->_($iva_alquiler_contador)."',
                    '".$bd_red->_($igic_alquiler_contador)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }

            // Se recuperan el id y la fila de la tarifa de agua añadida
            $id_tarifa_agua = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_tarifa_agua = dame_fila_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa_agua);

            // Si el identificador de tarifa existe, es un duplicado de una tarifa existente:
            // - Se duplican los conceptos_adicionales de factura de la tarifa anterior
            if ($id_tarifa_agua_anterior != ID_NINGUNO)
            {
                duplica_conceptos_adicionales_factura_tarifa_anterior(
                    TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_AGUA_ESPANYA,
                    $id_tarifa_agua_anterior,
                    $id_tarifa_agua);
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_tarifa_agua_Espanya($fila_tarifa_agua);

            $res = "OK";
            $msg = $idiomas->_("Tarifa añadida correctamente").".\n".
                $idiomas->_("Recuerde que debe asignar la tarifa de agua a un sensor de agua");
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
    function anyade_accion_usuario_anyadir_tarifa_agua_Espanya($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_TARIFA;
        $objeto_accion_usuario = $fila["nombre"];

        // Características de tipo de tarifa de agua
        $caracteristicas_tipo_tarifa_agua = TarifaAgua_Espanya::dame_caracteristicas_tipo_tarifa_agua($fila["tipo"]);
        $tipo_tarifa_canarias = $caracteristicas_tipo_tarifa_agua["tipo_tarifa_canarias"];

        // Nombres de parámetros
        $nombre_grupo = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_AGUA_ESPANYA, $fila["grupo"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_AGUA;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_AGUA] = $fila["tipo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_EXPIRACION_TARIFA] = $fila["expiracion"];
        if ($fila["expiracion"] == EXPIRACION_TARIFA_SI)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_EXPIRACION] = $fila["fecha_expiracion"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_PREAVISO_EXPIRACION] = $fila["numero_dias_preaviso_expiracion"];
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_LIMITES_CONSUMO_TRAMOS_TARIFA_AGUA] = $fila["tipo_limites_consumos_tramos"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LIMITES_CONSUMO_TRAMOS] = $fila["limites_consumos_tramos"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS] = $fila["precios_consumos_tramos"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_ALQUILER_CONTADOR] = $fila["tipo_alquiler_contador"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ALQUILER_CONTADOR] = $fila["alquiler_contador"];
        if ($tipo_tarifa_canarias == false)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA_CONSUMO] = $fila["iva_consumo"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA_ALQUILER_CONTADOR] = $fila["iva_alquiler_contador"];
        }
        else
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IGIC_CONSUMO] = $fila["igic_consumo"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IGIC_ALQUILER_CONTADOR] = $fila["igic_alquiler_contador"];
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
