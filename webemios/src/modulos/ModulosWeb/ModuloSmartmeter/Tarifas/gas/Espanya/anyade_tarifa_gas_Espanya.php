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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/TarifaGas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_TARIFA_GAS, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_tarifa_gas_anterior = $_POST['id_tarifa_gas_anterior'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $id_grupo = $_POST['id_grupo'];
    $expiracion = $_POST['expiracion'];
    $cadena_fecha_expiracion_local_local = $_POST['fecha_expiracion'];
    $numero_dias_preaviso_expiracion = $_POST['numero_dias_preaviso_expiracion'];
    $factor_conversion = $_POST['factor_conversion'];
    $precio_consumo = $_POST['precio_consumo'];
    $precio_caudal_diario = $_POST['precio_caudal_diario'];
    $caudal_diario = $_POST['caudal_diario'];
    $precio_termino_fijo_diario = $_POST['precio_termino_fijo_diario'];
	$capacidad_contratada  = $_POST['capacidad_contratada'];
	$termino_fijo  = $_POST['termino_fijo'];
	$termino_fijo_por_cliente = $_POST['termino_fijo_por_cliente'];
	$termino_variable  = $_POST['termino_variable'];
	$exceso_caudal  = $_POST['exceso_caudal'];
    $impuesto_gas = $_POST['impuesto_gas'];
    $tipo_alquiler_contador = $_POST['tipo_alquiler_contador'];
    $alquiler_contador = $_POST['alquiler_contador'];
    $iva = $_POST['iva'];

    // Conversión de fechas
    $cadena_fecha_expiracion_base_datos_local = convierte_formato_fecha($cadena_fecha_expiracion_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

	// Se comprueba si existe una tarifa de gas con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM ".TABLA_TARIFAS_GAS_ESPANYA."
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
        // Comprobaciones antes de añadir la tarifa de gas:
        // - Si hay grupo, se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas de gas del mismo grupo
        $anyadir_tarifa = true;

        // Se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas de gas del mismo grupo
        if (($anyadir_tarifa == true) && ($id_grupo != ID_NINGUNO))
        {
            $consulta_tarifas_gas = "
                SELECT nombre
                FROM ".TABLA_TARIFAS_GAS_ESPANYA."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')";
            $res_tarifas_gas = $bd_red->ejecuta_consulta($consulta_tarifas_gas);
            if ($res_tarifas_gas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_gas."'");
            }
            if ($res_tarifas_gas->dame_numero_filas() > 0)
            {
                $anyadir_tarifa = False;

                $fila_tarifa_gas = $res_tarifas_gas->dame_siguiente_fila();
                $nombre_tarifa_gas = $fila_tarifa_gas["nombre"];

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una tarifa en el mismo grupo con la misma fecha de expiración")."\n(".
                    $nombre_tarifa_gas.")";
            }
        }

        // Se añade la tarifa de gas
        if ($anyadir_tarifa == true)
        {
			// Se configuran los parámetros para las tarifas de 2021
			$caracteristicas_tipo_tarifa_gas = TarifaGas_Espanya::dame_caracteristicas_tipo_tarifa_gas($tipo);
			if ($caracteristicas_tipo_tarifa_gas["tipo_calculo_coste_termino_fijo"]  == TIPO_CALCULO_COSTE_TARIFAS_2021)
			{
				$precio_consumo = $termino_variable;
				$precio_caudal_diario = $exceso_caudal;
				$caudal_diario = $capacidad_contratada;
				$precio_termino_fijo_diario = $termino_fijo;
			}
			elseif ($caracteristicas_tipo_tarifa_gas["tipo_calculo_coste_termino_fijo"]  == TIPO_CALCULO_COSTE_POR_CLIENTE)
			{
				$precio_consumo = $termino_variable;
				$precio_caudal_diario = 0;
				$caudal_diario = 0;
				$precio_termino_fijo_diario = $termino_fijo_por_cliente;
			}

						// Se añade la tarifa de gas
            $operacion_insercion = "
                INSERT INTO ".TABLA_TARIFAS_GAS_ESPANYA." (
                    nombre,
                    red,
                    descripcion,
                    tipo,
                    grupo,
                    expiracion,
                    fecha_expiracion,
                    numero_dias_preaviso_expiracion,
                    factor_conversion,
                    precio_consumo,
                    precio_caudal_diario,
                    caudal_diario,
                    precio_termino_fijo_diario,
                    impuesto_gas,
                    tipo_alquiler_contador,
                    alquiler_contador,
                    iva
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($descripcion)."',
                    '".$bd_red->_($tipo)."',
                    '".$bd_red->_($id_grupo)."',
                    '".$bd_red->_($expiracion)."',
                    '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."',
                    '".$bd_red->_($numero_dias_preaviso_expiracion)."',
                    '".$bd_red->_($factor_conversion)."',
                    '".$bd_red->_($precio_consumo)."',
                    '".$bd_red->_($precio_caudal_diario)."',
                    '".$bd_red->_($caudal_diario)."',
                    '".$bd_red->_($precio_termino_fijo_diario)."',
                    '".$bd_red->_($impuesto_gas)."',
                    '".$bd_red->_($tipo_alquiler_contador)."',
                    '".$bd_red->_($alquiler_contador)."',
                    '".$bd_red->_($iva)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }

            // Se recuperan el id y la fila de la tarifa de gas añadida
            $id_tarifa_gas = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_tarifa_gas = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa_gas);

            // Si el identificador de tarifa existe, es un duplicado de una tarifa existente:
            // - Se duplican los conceptos_adicionales de factura de la tarifa anterior
            if ($id_tarifa_gas_anterior != ID_NINGUNO)
            {
                duplica_conceptos_adicionales_factura_tarifa_anterior(
                    TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_GAS_ESPANYA,
                    $id_tarifa_gas_anterior,
                    $id_tarifa_gas);
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_tarifa_gas_Espanya($fila_tarifa_gas);

            $res = "OK";
            $msg = $idiomas->_("Tarifa añadida correctamente").".\n".
                $idiomas->_("Recuerde que debe asignar la tarifa de gas a un sensor de gas").".\n".
                $idiomas->_("Si este sensor ya tiene datos, tendrá que recalcular los datos de gas para que se calculen los consumos y costes de los sensores asociados a esta tarifa de gas");
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
    function anyade_accion_usuario_anyadir_tarifa_gas_Espanya($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_TARIFA;
        $objeto_accion_usuario = $fila["nombre"];

        // Características de tipo de tarifa de gas
        $caracteristicas_tipo_tarifa_gas = TarifaGas_Espanya::dame_caracteristicas_tipo_tarifa_gas($fila["tipo"]);
        $tipo_calculo_coste_termino_fijo = $caracteristicas_tipo_tarifa_gas["tipo_calculo_coste_termino_fijo"];

        // Nombres de parámetros
        $nombre_grupo = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_GAS_ESPANYA, $fila["grupo"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_GAS;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_GAS] = $fila["tipo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_EXPIRACION_TARIFA] = $fila["expiracion"];
        if ($fila["expiracion"] == EXPIRACION_TARIFA_SI)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_EXPIRACION] = $fila["fecha_expiracion"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_PREAVISO_EXPIRACION] = $fila["numero_dias_preaviso_expiracion"];
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FACTOR_CONVERSION_TARIFA_GAS] = $fila["factor_conversion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_TARIFA_GAS] = $fila["precio_consumo"];
        switch ($tipo_calculo_coste_termino_fijo)
        {
            case TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CAUDAL_DIARIO_TARIFA_GAS] = $fila["precio_caudal_diario"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAUDAL_DIARIO_CONTRATADO_TARIFA_GAS] = $fila["caudal_diario"];
                break;
            }
            case TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_TERMINO_FIJO_DIARIO_GAS_ESPANYA] = $fila["precio_termino_fijo_diario"];
                break;
            }
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IMPUESTO_TARIFA_GAS] = $fila["impuesto_gas"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_ALQUILER_CONTADOR] = $fila["tipo_alquiler_contador"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ALQUILER_CONTADOR] = $fila["alquiler_contador"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA] = $fila["iva"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
