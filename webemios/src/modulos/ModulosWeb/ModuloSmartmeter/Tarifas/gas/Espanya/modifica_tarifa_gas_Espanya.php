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
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_TARIFA_GAS, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_tarifa_gas = $_POST['id_tarifa_gas'];
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
	$termino_fijo_por_cliente  = $_POST['termino_fijo_por_cliente'];
	$termino_variable  = $_POST['termino_variable'];
	$exceso_caudal  = $_POST['exceso_caudal'];
	$impuesto_gas = $_POST['impuesto_gas'];
    $tipo_alquiler_contador = $_POST['tipo_alquiler_contador'];
    $alquiler_contador = $_POST['alquiler_contador'];
    $iva = $_POST['iva'];
    $tramos = $_POST['tramos'];

    // Conversión de fechas
    $cadena_fecha_expiracion_base_datos_local = convierte_formato_fecha($cadena_fecha_expiracion_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

    // Se comprueba si existe otra tarifa de gas con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM ".TABLA_TARIFAS_GAS_ESPANYA."
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_tarifa_gas)."')";
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
        // Comprobaciones antes de modificar la tarifa de gas:
        // - Si hay grupo, se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas eléctricas del mismo grupo
        $modificar_tarifa = true;

        // Se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas de gas del mismo grupo
        if (($modificar_tarifa == true) && ($id_grupo != ID_NINGUNO))
        {
            $consulta_tarifas_gas = "
                SELECT nombre
                FROM ".TABLA_TARIFAS_GAS_ESPANYA."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')
                    AND (id <> '".$bd_red->_($id_tarifa_gas)."')";
            $res_tarifas_gas = $bd_red->ejecuta_consulta($consulta_tarifas_gas);
            if ($res_tarifas_gas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_gas."'");
            }
            if ($res_tarifas_gas->dame_numero_filas() > 0)
            {
                $modificar_tarifa = False;

                $fila_tarifa_gas = $res_tarifas_gas->dame_siguiente_fila();
                $nombre_tarifa_gas = $fila_tarifa_gas["nombre"];

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una tarifa en el mismo grupo con la misma fecha de expiración")."\n(".
                    $nombre_tarifa_gas.")";
            }
        }

        // Se modifica la tarifa de gas
        if ($modificar_tarifa == true)
        {

			// Se modifican las tarifas para ajustarlas a las de 2021
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

            // Se recupera la fila anterior (antes de la modificación)
            $fila_tarifa_gas_anterior = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa_gas);

            // Se modifica la tarifa de gas
            $operacion_modificacion = "
                UPDATE ".TABLA_TARIFAS_GAS_ESPANYA."
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    tipo = '".$bd_red->_($tipo)."',
                    grupo = '".$bd_red->_($id_grupo)."',
                    expiracion = '".$bd_red->_($expiracion)."',
                    fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."',
                    numero_dias_preaviso_expiracion = '".$bd_red->_($numero_dias_preaviso_expiracion)."',
                    factor_conversion = '".$bd_red->_($factor_conversion)."',
                    precio_consumo = '".$bd_red->_($precio_consumo)."',
                    precio_caudal_diario = '".$bd_red->_($precio_caudal_diario)."',
                    caudal_diario = '".$bd_red->_($caudal_diario)."',
                    precio_termino_fijo_diario = '".$bd_red->_($precio_termino_fijo_diario)."',
                    impuesto_gas = '".$bd_red->_($impuesto_gas)."',
                    tipo_alquiler_contador = '".$bd_red->_($tipo_alquiler_contador)."',
                    alquiler_contador = '".$bd_red->_($alquiler_contador)."',
                    iva = '".$bd_red->_($iva)."'
                WHERE
                    id = '".$bd_red->_($id_tarifa_gas)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }

            // Si tiene grupo asignado se asigna el grupo a los sensores que tenían asignada la tarifa de gas
            if ($id_grupo != ID_NINGUNO)
            {
                asigna_grupo_tarifas_sensores_tarifa(MEDICION_GAS, $id_grupo, $id_tarifa_gas);
            }

            // Se recupera la fila de la tarifa actual
            $fila_tarifa_gas_actual = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa_gas);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_tarifa_gas_Espanya(
                $fila_tarifa_gas_actual,
                $fila_tarifa_gas_anterior);

            $res = "OK";
            $msg = $idiomas->_("Tarifa modificada correctamente").".\n".
                $idiomas->_("Los nuevos parámetros tendrán efecto a partir de los siguientes datos recibidos").".\n".
                $idiomas->_("Si quiere que se vuelvan a calcular datos anteriores con los nuevos parámetros de la tarifa de gas, tendrá que recalcular datos de gas seleccionando esta tarifa de gas y el tiempo a partir del cual quiere que sean efectivos");
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
    function anyade_accion_usuario_modificar_tarifa_gas_Espanya($fila_actual, $fila_anterior)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_TARIFA;

        // Características de tipos de tarifas de gas
        $caracteristicas_tipo_tarifa_gas = TarifaGas_Espanya::dame_caracteristicas_tipo_tarifa_gas($fila_actual["tipo"]);
        $tipo_calculo_coste_termino_fijo = $caracteristicas_tipo_tarifa_gas["tipo_calculo_coste_termino_fijo"];
        $caracteristicas_tipo_tarifa_gas_anterior = TarifaGas_Espanya::dame_caracteristicas_tipo_tarifa_gas($fila_anterior["tipo"]);
        $tipo_calculo_coste_termino_fijo_anterior = $caracteristicas_tipo_tarifa_gas_anterior["tipo_calculo_coste_termino_fijo"];

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_GAS;
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_GAS;
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
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_GAS] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_GAS] = $fila_anterior["tipo"];
        }
        if ($fila_actual["grupo"] != $fila_anterior["grupo"])
        {
            $nombre_grupo = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_GAS_ESPANYA, $fila_actual["grupo"]);
            $nombre_grupo_anterior = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_GAS_ESPANYA, $fila_anterior["grupo"]);
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
        if ($fila_actual["factor_conversion"] != $fila_anterior["factor_conversion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FACTOR_CONVERSION_TARIFA_GAS] = $fila_actual["factor_conversion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FACTOR_CONVERSION_TARIFA_GAS] = $fila_anterior["factor_conversion"];
        }
        if ($fila_actual["precio_consumo"] != $fila_anterior["precio_consumo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_TARIFA_GAS] = $fila_actual["precio_consumo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_CONSUMO_TARIFA_GAS] = $fila_anterior["precio_consumo"];
        }
        if ($fila_actual["precio_caudal_diario"] != $fila_anterior["precio_caudal_diario"])
        {
            if ($tipo_calculo_coste_termino_fijo == TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_CAUDAL_DIARIO_TARIFA_GAS] = $fila_actual["precio_caudal_diario"];
            }
            if ($tipo_calculo_coste_termino_fijo_anterior == TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_CAUDAL_DIARIO_TARIFA_GAS] = $fila_anterior["precio_caudal_diario"];
            }
        }
        if ($fila_actual["caudal_diario"] != $fila_anterior["caudal_diario"])
        {
            if ($tipo_calculo_coste_termino_fijo == TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAUDAL_DIARIO_CONTRATADO_TARIFA_GAS] = $fila_actual["caudal_diario"];
            }
            if ($tipo_calculo_coste_termino_fijo_anterior == TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CAUDAL_DIARIO_CONTRATADO_TARIFA_GAS] = $fila_anterior["caudal_diario"];
            }
        }
        if ($fila_actual["precio_termino_fijo_diario"] != $fila_anterior["precio_termino_fijo_diario"])
        {
            if ($tipo_calculo_coste_termino_fijo == TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIO_TERMINO_FIJO_DIARIO_GAS_ESPANYA] = $fila_actual["precio_termino_fijo_diario"];
            }
            if ($tipo_calculo_coste_termino_fijo_anterior == TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIO_TERMINO_FIJO_DIARIO_GAS_ESPANYA] = $fila_anterior["precio_termino_fijo_diario"];
            }
        }
        if ($fila_actual["impuesto_gas"] != $fila_anterior["impuesto_gas"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IMPUESTO_TARIFA_GAS] = $fila_actual["impuesto_gas"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IMPUESTO_TARIFA_GAS] = $fila_anterior["impuesto_gas"];
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
        if ($fila_actual["iva"] != $fila_anterior["iva"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA] = $fila_actual["iva"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IVA] = $fila_anterior["iva"];
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
