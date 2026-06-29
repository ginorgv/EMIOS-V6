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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_TARIFA_ELECTRICA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_tarifa_electrica_anterior = $_POST['id_tarifa_electrica_anterior'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $bonificacion_85 = $_POST['bonificacion_85'];
    $tipo_medida = $_POST['tipo_medida'];
    $potencia_nominal_transformador = $_POST['potencia_nominal_transformador'];
    $contrato = $_POST['contrato'];
    $id_grupo = $_POST['id_grupo'];
    $expiracion = $_POST['expiracion'];
    $cadena_fecha_expiracion_local_local = $_POST['fecha_expiracion'];
    $numero_dias_preaviso_expiracion = $_POST['numero_dias_preaviso_expiracion'];

    // Las variables con valores pass_pool y pass_through pueden corresponder a esas tarifas o a una de cierre, ya que es una combinación de ambas
    $id_indicador_omie_pass_pool = $_POST['id_indicador_omie_pass_pool'];
    $tipo_calculo_coste_pass_pool = $_POST['tipo_calculo_coste_pass_pool'];
    $dia_calculo_coste_automatico_pass_pool = $_POST['dia_calculo_coste_automatico_pass_pool'];
    $formula_precio_consumo_pass_through = $_POST['formula_precio_consumo_pass_through'];
    $fecha_inicio_contrato_cierre = $_POST['fecha_inicio_contrato_cierre'];

    $impuesto_electrico = $_POST['impuesto_electrico'];
    $tipo_alquiler_contador = $_POST['tipo_alquiler_contador'];
    $alquiler_contador = $_POST['alquiler_contador'];
    $iva = $_POST['iva'];
    $igic_reducido = $_POST['igic_reducido'];
    $igic_normal = $_POST['igic_normal'];
    $prorrateo = $_POST['prorrateo'];
    $info_tramos = $_POST['info_tramos'];    

    $log = dame_log();
    $log -> debug("La información de los tramos es: ");
    $log -> debug($info_tramos);

    // Conversión de fechas
    $cadena_fecha_expiracion_base_datos_local = convierte_formato_fecha($cadena_fecha_expiracion_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
    $fecha_inicio_contrato_cierre = $fecha_inicio_contrato_cierre ? (convierte_formato_fecha($fecha_inicio_contrato_cierre, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS)) : NULL;

	// Se comprueba si existe una tarifa eléctrica con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    // Comprobar que la tarifa que se quiere añadir tiene información asociada. En caso de no tenerla se debe borrar la caché para refrescar el archivo JavaScript
    if (!$info_tramos)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Datos de navegación desincronizados con el servidor. Por favor, borre la caché del navegador. Puede consultar en internet cómo hacerlo.");
    }
    elseif ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una tarifa con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de añadir la tarifa eléctrica:
        // - Si el tipo de tarifa eléctrica es 'pass-through' o 'cierre', se valida la fórmula de cálculo de precio de consumo
        // - Si hay grupo, se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas eléctricas del mismo grupo
        $anyadir_tarifa = true;

        // Si los datos son correctos se evalua la función de cálculo de precio de consumo
        if (($anyadir_tarifa == true) && ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH))
        {
            // Parámetros de la función a llamar
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_EVALUA_FORMULA_PRECIO_CONSUMO_PASS_THROUGH_ESPANYA,
                    "formula_precio_consumo_pass_through" => $formula_precio_consumo_pass_through
                );

            // Llamada a función 'externa'
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Si la fórmula de valores es incorrecta se devuelve un error
            if ($resultado_funcion_externa["formula_correcta"] == 0)
            {
                $anyadir_tarifa = False;

                $error = $resultado_funcion_externa["error"];
                $descripcion_error = dame_descripcion_error_funcion_variables($error);

                $res = "ERROR";
                $msg = $idiomas->_("Ha ocurrido un error al evaluar la fórmula de precio de consumo")."\n(".
                    $descripcion_error.")";
            }
        }

        // Si los datos son correctos se evalua la función de cálculo de precio de consumo
        if (($anyadir_tarifa == true) && ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE))
        {
            // Parámetros de la función a llamar
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_EVALUA_FORMULA_PRECIO_CONSUMO_CIERRE_ESPANYA,
                    "formula_precio_consumo_cierre" => $formula_precio_consumo_pass_through
                );

            // Llamada a función 'externa'
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Si la fórmula de valores es incorrecta se devuelve un error
            if ($resultado_funcion_externa["formula_correcta"] == 0)
            {
                $anyadir_tarifa = False;

                $error = $resultado_funcion_externa["error"];
                $descripcion_error = dame_descripcion_error_funcion_variables($error);

                $res = "ERROR";
                $msg = $idiomas->_("Ha ocurrido un error al evaluar la fórmula de precio de consumo")."\n(".$descripcion_error.")";
            }
        }

        // Se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas eléctricas del mismo grupo
        if (($anyadir_tarifa == true) && ($id_grupo != ID_NINGUNO))
        {
            $consulta_tarifas_electricas = "
                SELECT nombre
                FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')";
            $res_tarifas_electricas = $bd_red->ejecuta_consulta($consulta_tarifas_electricas);
            if ($res_tarifas_electricas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_electricas."'");
            }
            if ($res_tarifas_electricas->dame_numero_filas() > 0)
            {
                $anyadir_tarifa = False;

                $fila_tarifa_electrica = $res_tarifas_electricas->dame_siguiente_fila();
                $nombre_tarifa_electrica = $fila_tarifa_electrica["nombre"];

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una tarifa en el mismo grupo con la misma fecha de expiración")."\n(".
                    $nombre_tarifa_electrica.")";
            }
        }

        // Se añade la tarifa eléctrica
        if ($anyadir_tarifa == true)
        {
            // EMG: Para las tarifas 3.0TD y las 6.1TD comprobamos si la potencia contratada es inferior a 50kW para los cálculos de los excesos de potencia.
            // Si la potencia contratada es inferior a 50kW en todos los tramos, se calcula por maxímetro, si no, por curva.
            // Para distringuirlo guarmados las tarifas como XXTD (por curva) o XXTD_MAX
            
            // TODO: Se puede refactorizar este if con un in_array
            if( ($tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME ||
				$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME ||
				$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022 ||
				$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023 ||
				$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024 ||
				$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025 ||
				$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL ||
				$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026 ||
				$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026 ||
                $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026) &&
                ($info_tramos[5]["potencia"] <= 50))
            {
							$tipo = $tipo."_MAX";
            }

            // Crear cadena porque puede tener valor o ser NULL
            $cadena_fecha_inicio_contrato_cierre = $fecha_inicio_contrato_cierre ? "'".$fecha_inicio_contrato_cierre."'" : "NULL";
            // Se añade la tarifa eléctrica
            $operacion_insercion = "
                INSERT INTO ".TABLA_TARIFAS_ELECTRICAS_ESPANYA." (
                    nombre,
                    red,
                    descripcion,
                    tipo,
                    bonificacion_85,
                    tipo_medida,
                    potencia_nominal_transformador,
                    contrato,
                    grupo,
                    expiracion,
                    fecha_expiracion,
                    numero_dias_preaviso_expiracion,
                    id_indicador_omie_pass_pool,
                    tipo_calculo_coste_pass_pool,
                    dia_calculo_coste_automatico_pass_pool,
                    formula_precio_consumo_pass_through,
                    fecha_inicio_contrato_cierre,
                    impuesto_electrico,
                    tipo_alquiler_contador,
                    alquiler_contador,
                    iva,
                    igic_reducido,
                    igic_normal,
                    prorrateo
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($descripcion)."',
                    '".$bd_red->_($tipo)."',
                    '".$bd_red->_($bonificacion_85)."',
                    '".$bd_red->_($tipo_medida)."',
                    '".$bd_red->_($potencia_nominal_transformador)."',
                    '".$bd_red->_($contrato)."',
                    '".$bd_red->_($id_grupo)."',
                    '".$bd_red->_($expiracion)."',
                    '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."',
                    '".$bd_red->_($numero_dias_preaviso_expiracion)."',
                    '".$bd_red->_($id_indicador_omie_pass_pool)."',
                    '".$bd_red->_($tipo_calculo_coste_pass_pool)."',
                    '".$bd_red->_($dia_calculo_coste_automatico_pass_pool)."',
                    '".$bd_red->_($formula_precio_consumo_pass_through)."',
                    $cadena_fecha_inicio_contrato_cierre,
                    '".$bd_red->_($impuesto_electrico)."',
                    '".$bd_red->_($tipo_alquiler_contador)."',
                    '".$bd_red->_($alquiler_contador)."',
                    '".$bd_red->_($iva)."',
                    '".$bd_red->_($igic_reducido)."',
                    '".$bd_red->_($igic_normal)."',
                    '".$bd_red->_($prorrateo)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }

            // Se recuperan el id y la fila de la tarifa eléctrica añadida
            $id_tarifa_electrica = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa_electrica);

            // Si el identificador de tarifa existe, es un duplicado de una tarifa existente:
            // - Se duplican los conceptos_adicionales de factura de la tarifa anterior
            if ($id_tarifa_electrica_anterior != ID_NINGUNO)
            {
                duplica_conceptos_adicionales_factura_tarifa_anterior(
                    TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_ELECTRICAS_ESPANYA,
                    $id_tarifa_electrica_anterior,
                    $id_tarifa_electrica);
            }

            // Se añade la información de los tramos de la tarifa eléctrica
            foreach ($info_tramos as $info_tramo)
            {
                $operacion_insercion_tramo = "
                    INSERT INTO ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA." (
                        red,
                        tarifa_electrica,
                        tramo,
                        precio_consumo,
                        coeficiente_a_precio_consumo_pass_pool,
                        coeficiente_b_precio_consumo_pass_pool,
                        precio_consumo_tarifa_acceso,
                        precio_potencia,
                        potencia
                    ) VALUES (
                        '".$_SESSION["id_red"]."',
                        '".$bd_red->_($id_tarifa_electrica)."',
                        '".$bd_red->_($info_tramo["numero_tramo"])."',
                        '".$bd_red->_($info_tramo["precio_consumo"])."',
                        '".$bd_red->_($info_tramo["coeficiente_a_precio_consumo_pass_pool"])."',
                        '".$bd_red->_($info_tramo["coeficiente_b_precio_consumo_pass_pool"])."',
                        '".$bd_red->_($info_tramo["precio_consumo_tarifa_acceso"])."',
                        '".$bd_red->_($info_tramo["precio_potencia"])."',
                        '".$bd_red->_($info_tramo["potencia"])."'
                    )";
                $res_insercion_tramo = $bd_red->ejecuta_operacion($operacion_insercion_tramo);
                if ($res_insercion_tramo == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_insercion_tramo."'");
                }
            }

            // Se recuperan las filas de los tramos de la tarifa eléctrica añadida
            $filas_tramos_tarifa_electrica = dame_filas_tramos_tarifa_electricidad_Espanya($id_tarifa_electrica);

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_tarifa_electricidad_Espanya($fila_tarifa_electrica, $filas_tramos_tarifa_electrica);

            $res = "OK";
            $msg = $idiomas->_("Tarifa añadida correctamente").".\n".
                $idiomas->_("Recuerde que debe asignar la tarifa eléctrica a un sensor de energía activa").".\n".
                $idiomas->_("Si este sensor ya tiene datos, tendrá que recalcular los datos eléctricos para que se calculen los costes y tramos de los sensores asociados a esta tarifa eléctrica");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de adición de la tarifa eléctrica
    function anyade_accion_usuario_anyadir_tarifa_electricidad_Espanya($fila, $filas_tramos)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_TARIFA;
        $objeto_accion_usuario = $fila["nombre"];

        // Características de tipo de tarifa eléctrica
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($fila["tipo"]);
        $tipo_calculo_coste_potencias = $caracteristicas_tipo_tarifa_electrica["tipo_calculo_coste_potencias"];
        $parametros_medida_datos_facturacion = $caracteristicas_tipo_tarifa_electrica["parametros_medida_datos_facturacion"];
        $tipo_tarifa_canarias = $caracteristicas_tipo_tarifa_electrica["tipo_tarifa_canarias"];

        // Nombres de parámetros
        $nombre_grupo = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_ELECTRICAS_ESPANYA, $fila["grupo"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_ELECTRICIDAD;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_ELECTRICA] = $fila["tipo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CONTRATO_TARIFA_ELECTRICA] = $fila["contrato"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_EXPIRACION_TARIFA] = $fila["expiracion"];
        if ($fila["expiracion"] == EXPIRACION_TARIFA_SI)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_EXPIRACION] = $fila["fecha_expiracion"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_DIAS_PREAVISO_EXPIRACION] = $fila["numero_dias_preaviso_expiracion"];
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRORRATEO_EXCESO_POTENCIA] = $fila["prorrateo"];
        switch ($fila["contrato"])
        {
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ID_INDICADOR_OMIE_PASS_POOL] = $fila["id_indicador_omie_pass_pool"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CALCULO_COSTE_PASS_POOL] = $fila["tipo_calculo_coste_pass_pool"];
                if ($fila["tipo_calculo_coste_pass_pool"] == TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_AUTOMATICO)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL] = $fila["dia_calculo_coste_automatico_pass_pool"];
                }
                break;
            }
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FORMULA_PRECIO_CONSUMO_PASS_THROUGH] = $fila["formula_precio_consumo_pass_through"];
                break;
            }
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FORMULA_PRECIO_CONSUMO_CIERRE] = $fila["formula_precio_consumo_cierre"];
                break;
            }
        }
        if ($tipo_calculo_coste_potencias == TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_BONIFICACION_85] = $fila["bonificacion_85"];
        }
        if ($parametros_medida_datos_facturacion == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_MEDIDA] = $fila["tipo_medida"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_POTENCIA_NOMINAL_TRANSFORMADOR] = $fila["potencia_nominal_transformador"];
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IMPUESTO_ELECTRICO] = $fila["impuesto_electrico"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_ALQUILER_CONTADOR] = $fila["tipo_alquiler_contador"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ALQUILER_CONTADOR] = $fila["alquiler_contador"];
        if ($tipo_tarifa_canarias == false)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA] = $fila["iva"];
        }
        else
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IGIC_NORMAL] = $fila["igic_normal"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IGIC_REDUCIDO] = $fila["igic_reducido"];
        }
        anyade_parametros_tramos_accion_usuario_tarifa_electricidad_Espanya($fila, $filas_tramos, $parametros_accion_usuario);

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
