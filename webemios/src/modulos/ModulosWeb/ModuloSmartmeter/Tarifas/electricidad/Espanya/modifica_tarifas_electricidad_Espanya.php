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
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_TARIFAS_ELECTRICAS, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $ids_tarifas_electricas = $_POST['ids_tarifas_electricas'];
    $tipo = $_POST['tipo'];
    $contrato = $_POST['contrato'];
    $expiracion = $_POST['expiracion'];
    $cadena_fecha_expiracion_local_local = $_POST['fecha_expiracion'];
    $numero_dias_preaviso_expiracion = $_POST['numero_dias_preaviso_expiracion'];
    $bonificacion_85 = $_POST['bonificacion_85'];
    $tipo_medida = $_POST['tipo_medida'];
    $potencia_nominal_transformador = $_POST['potencia_nominal_transformador'];
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
    $info_tramos = $_POST['info_tramos'];
    $prorrateo = $_POST['prorrateo'];

    // Conversión de fechas
    $cadena_fecha_expiracion_base_datos_local = convierte_formato_fecha($cadena_fecha_expiracion_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

    // Ids de tarifas eléctricas
    $cadena_ids_tarifas_electricas_consulta = dame_cadena_ids_consulta($ids_tarifas_electricas);

    // Comprobaciones antes de modificar las tarifas eléctricas:
    // - Si el tipo de tarifa eléctrica es 'pass-through', se valida la fórmula de cálculo de precio de consumo
    // - Si el tiop de tarifa eléctrica es 'cierre', //TODO: JBR
    // - Si no hay expiración de tarifa, se comprueba que ninguna tarifa pertenezca a ningún grupo
    // - Si hay expiración de tarifa,
    //   se comprueba que en las tarifas que pertenezcan a grupos no coincida la fecha de expiración con alguna otra tarifa del mismo grupo
    $modificar_tarifas = true;

    // Si los datos son correctos se evalua la función de cálculo de precio de consumo (si es necesario)
    if (($modificar_tarifas == true) &&
        ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH) &&
        ($formula_precio_consumo_pass_through != ""))
    {
        if ($tipo != TIPO_TARIFA_TODOS)
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
                $modificar_tarifas = False;

                $error = $resultado_funcion_externa["error"];
                $descripcion_error = dame_descripcion_error_funcion_variables($error);

                $res = "ERROR";
                $msg = $idiomas->_("Ha ocurrido un error al evaluar la fórmula de precio de consumo")."\n(".
                    $descripcion_error.")";
            }
        }
        else
        {
            $modificar_tarifas = false;

            $res = "ERROR";
            $msg = $idiomas->_("No se puede modificar la fórmula de precio de consumo sin seleccionar un tipo de tarifa eléctrica");
        }
    }

    if (($modificar_tarifas == true) &&
        ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE) &&
        ($formula_precio_consumo_pass_through != ""))
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
            $modificar_tarifas = False;

            $error = $resultado_funcion_externa["error"];
            $descripcion_error = dame_descripcion_error_funcion_variables($error);

            $res = "ERROR";
            $msg = $idiomas->_("Ha ocurrido un error al evaluar la fórmula de precio de consumo")."\n(".$descripcion_error.")";
        }
    }

    // Si no hay expiración de tarifa, se comprueba que ninguna tarifa pertenezca a ningún grupo
    if (($modificar_tarifas == true) && ($expiracion == EXPIRACION_TARIFA_NO))
    {
        $consulta_tarifas_electricas = "
            SELECT nombre
            FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                (id IN (".$cadena_ids_tarifas_electricas_consulta."))
                AND (grupo <> '".$bd_red->_(ID_NINGUNO)."')";
        $res_tarifas_electricas = $bd_red->ejecuta_consulta($consulta_tarifas_electricas);
        if ($res_tarifas_electricas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_electricas."'");
        }

        if ($res_tarifas_electricas->dame_numero_filas() > 0)
        {
            $modificar_tarifas = False;

            $error = $resultado["error"];
            $nombres_tarifas_electricas = "";
            while ($fila_tarifa_electrica = $res_tarifas_electricas->dame_siguiente_fila())
            {
                if ($nombres_tarifas_electricas != "")
                {
                    $nombres_tarifas_electricas .= ", ";
                }
                else
                {
                    $nombres_tarifas_electricas .= $fila_tarifa_electrica["nombre"];
                }
            }

            $res = "ERROR";
            $msg = $idiomas->_("Las tarifas con grupo asignado tienen que tener expiración")."\n(".
                $nombres_tarifas_electricas.")";
        }
    }

    // Si hay expiración de tarifa,
    // se comprueba que en las tarifas que pertenezcan a grupos no coincida la fecha de expiración con alguna otra tarifa del mismo grupo
    if (($modificar_tarifas == true) && ($expiracion == EXPIRACION_TARIFA_SI))
    {
        $consulta_tarifas_electricas_grupo = "
            SELECT
                id,
                nombre,
                grupo
            FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                (id IN (".$cadena_ids_tarifas_electricas_consulta."))
                AND (grupo <> '".$bd_red->_(ID_NINGUNO)."')";
        $res_tarifas_electricas_grupo = $bd_red->ejecuta_consulta($consulta_tarifas_electricas_grupo);
        if ($res_tarifas_electricas_grupo == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_electricas_grupo."'");
        }
        while ($fila_tarifa_electrica_grupo = $res_tarifas_electricas_grupo->dame_siguiente_fila())
        {
            $id_tarifa = $fila_tarifa_electrica_grupo["id"];
            $nombre_tarifa_electrica = $fila_tarifa_electrica_grupo["nombre"];
            $id_grupo_tarifa_electrica = $fila_tarifa_electrica_grupo["grupo"];

            $nombre_tarifas_electricas = "";
            $consulta_tarifas_electricas_fecha_expiracion = "
                SELECT nombre
                FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo_tarifa_electrica)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')
                    AND (id <> '".$bd_red->_($id_tarifa)."')";
            $res_tarifas_electricas_fecha_expiracion = $bd_red->ejecuta_consulta($consulta_tarifas_electricas_fecha_expiracion);
            if ($res_tarifas_electricas_fecha_expiracion == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_electricas_fecha_expiracion."'");
            }
            if ($res_tarifas_electricas_fecha_expiracion->dame_numero_filas() > 0)
            {
                $modificar_tarifas = false;
                if ($nombres_tarifas_electricas != "")
                {
                    $nombres_tarifas_electricas .= ", ";
                }
                else
                {
                    $nombres_tarifas_electricas .= $fila_tarifa_electrica_grupo["nombre"];
                }
            }
        }
        if ($modificar_tarifas == false)
        {
            $res = "ERROR";
            $msg = $idiomas->_("La fecha de expiración de las tarifas con grupo asignado coincide con otras tarifas del mismo grupo")."\n(".
                $nombres_tarifas_electricas.")";
        }
    }

    // Se modifican las tarifas eléctricas
    if ($modificar_tarifas == true)
    {
        // Flag de tarifas eléctricas modificadas
        $tarifas_electricas_modificadas = false;

        // EMG: Para las tarifas 3.0TD y 6.1TD comprobamos si la potencia contratada es inferior a 50kW para los cálculos de los excesos de potencia.
        // Si la potencia contratada es inferior a 50kW en todos los tramos, se calcula por maxímetro, si no, por curva.
        // Para distringuirlo guarmados las tarifas como XXTD (por curva) o XXTD_MAX
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
        }else if(($tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026_MAX ||
            $tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026_MAX ||
			$tipo == TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026_MAX
            ) &&
            ($info_tramos[5]["potencia"] > 50))
        {
            $tipo = substr($tipo, 0, -4);
        }



        // Modificaciones en la tabla de tarifas eléctricas
        $info_campos_tarifas_electricas = array();
        array_push($info_campos_tarifas_electricas, array(
            "nombre" => "expiracion",
            "valor" => $expiracion,
            "valor_nulo" => EXPIRACION_TARIFA_NINGUNO));
        if ($expiracion != EXPIRACION_TARIFA_NINGUNO)
        {
            array_push($info_campos_tarifas_electricas, array(
                "nombre" => "fecha_expiracion",
                "valor" => $cadena_fecha_expiracion_base_datos_local,
                "valor_nulo" => NULL));
            array_push($info_campos_tarifas_electricas, array(
                "nombre" => "numero_dias_preaviso_expiracion",
                "valor" => $numero_dias_preaviso_expiracion,
                "valor_nulo" => ""));
        }
        array_push($info_campos_tarifas_electricas, array(
                "nombre" => "prorrateo",
                "valor" => $prorrateo,
                "valor_nulo" => PRORRATEO_TARIFA_SI));
    
        if ($bonificacion_85 != BONIFICACION_85_TARIFA_ELECTRICA_NINGUNA)
        {
            array_push($info_campos_tarifas_electricas, array(
                "nombre" => "bonificacion_85",
                "valor" => $bonificacion_85,
                "valor_nulo" => ""));
        }
        if ($tipo_medida != TIPO_MEDIDA_TARIFA_ELECTRICA_NINGUNA)
        {
            array_push($info_campos_tarifas_electricas, array(
                "nombre" => "tipo_medida",
                "valor" => $tipo_medida,
                "valor_nulo" => ""));
            if ($tipo_medida == TIPO_MEDIDA_TARIFA_ELECTRICA_BAJA_TENSION)
            {
                array_push($info_campos_tarifas_electricas, array(
                    "nombre" => "potencia_nominal_transformador",
                    "valor" => $potencia_nominal_transformador,
                    "valor_nulo" => NULL));
            }
        }
        switch ($contrato)
        {
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL:
            {
                array_push($info_campos_tarifas_electricas, array(
                    "nombre" => "id_indicador_omie_coste_pass_pool",
                    "valor" => $id_indicador_omie_pass_pool,
                    "valor_nulo" => ID_INDICADOR_OMIE_TARIFA_ELECTRICA_NINGUNO));
                array_push($info_campos_tarifas_electricas, array(
                    "nombre" => "tipo_calculo_coste_pass_pool",
                    "valor" => $tipo_calculo_coste_pass_pool,
                    "valor_nulo" => TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO));
                if ($tipo_calculo_coste_pass_pool != TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO)
                {
                    array_push($info_campos_tarifas_electricas, array(
                        "nombre" => "dia_calculo_coste_automatico_pass_pool",
                        "valor" => $dia_calculo_coste_automatico_pass_pool,
                        "valor_nulo" => ""));
                }
                break;
            }
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH:
            {
                array_push($info_campos_tarifas_electricas, array(
                    "nombre" => "formula_precio_consumo_pass_through",
                    "valor" => $formula_precio_consumo_pass_through,
                    "valor_nulo" => ""));
                break;
            }
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE:
            {
                array_push($info_campos_tarifas_electricas, array(
                    "nombre" => "id_indicador_omie_coste_pass_pool",
                    "valor" => $id_indicador_omie_pass_pool,
                    "valor_nulo" => ID_INDICADOR_OMIE_TARIFA_ELECTRICA_NINGUNO));
                array_push($info_campos_tarifas_electricas, array(
                    "nombre" => "tipo_calculo_coste_pass_pool",
                    "valor" => $tipo_calculo_coste_pass_pool,
                    "valor_nulo" => TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO));
                array_push($info_campos_tarifas_electricas, array(
                    "nombre" => "fecha_inicio_contrato_cierre",
                    "valor" => $fecha_inicio_contrato_cierre,
                    "valor_nulo" => ""));
                array_push($info_campos_tarifas_electricas, array(
                    "nombre" => "formula_precio_consumo_pass_through",
                    "valor" => $formula_precio_consumo_pass_through,
                    "valor_nulo" => ""));
            }
        }
        array_push($info_campos_tarifas_electricas, array(
            "nombre" => "impuesto_electrico",
            "valor" => $impuesto_electrico,
            "valor_nulo" => ""));
        array_push($info_campos_tarifas_electricas, array(
            "nombre" => "tipo_alquiler_contador",
            "valor" => $tipo_alquiler_contador,
            "valor_nulo" => TIPO_ALQUILER_CONTADOR_NINGUNO));
        if ($tipo_alquiler_contador != TIPO_ALQUILER_CONTADOR_NINGUNO)
        {
            array_push($info_campos_tarifas_electricas, array(
                "nombre" => "alquiler_contador",
                "valor" => $alquiler_contador,
                "valor_nulo" => ""));
        }
        array_push($info_campos_tarifas_electricas, array(
            "nombre" => "iva",
            "valor" => $iva,
            "valor_nulo" => ""));
        array_push($info_campos_tarifas_electricas, array(
            "nombre" => "igic_reducido",
            "valor" => $igic_reducido,
            "valor_nulo" => ""));
        array_push($info_campos_tarifas_electricas, array(
            "nombre" => "igic_normal",
            "valor" => $igic_normal,
            "valor_nulo" => ""));
        $clausula_modificacion_tarifas_electricas = dame_clausula_modificacion_campos($bd_red, $info_campos_tarifas_electricas);
        if ($clausula_modificacion_tarifas_electricas != "")
        {
            $operacion_modificacion_tarifas_electricas = "
                UPDATE ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
                SET ";
            $operacion_modificacion_tarifas_electricas .= $clausula_modificacion_tarifas_electricas;
            $operacion_modificacion_tarifas_electricas .= "
                WHERE
                    id IN (".$cadena_ids_tarifas_electricas_consulta.")";
            $res_modificacion_tarifas_electricas = $bd_red->ejecuta_operacion($operacion_modificacion_tarifas_electricas);
            if ($res_modificacion_tarifas_electricas == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_tarifas_electricas."'");
            }
            $tarifas_electricas_modificadas = true;
        }

        // Se modifican los tramos de la tarifa eléctrica
        $info_campos_tramos_tarifas_electricas = array();
        foreach ($info_tramos as $info_tramo)
        {
            $numero_tramo = $info_tramo["numero_tramo"];
            $info_campos_tramo_tarifas_electricas = array();
            array_push($info_campos_tramo_tarifas_electricas, array(
                "nombre" => "precio_consumo",
                "valor" => $info_tramo["precio_consumo"],
                "valor_nulo" => ""));
            array_push($info_campos_tramo_tarifas_electricas, array(
                "nombre" => "coeficiente_a_precio_consumo_pass_pool",
                "valor" => $info_tramo["coeficiente_a_precio_consumo_pass_pool"],
                "valor_nulo" => ""));
            array_push($info_campos_tramo_tarifas_electricas, array(
                "nombre" => "coeficiente_b_precio_consumo_pass_pool",
                "valor" => $info_tramo["coeficiente_b_precio_consumo_pass_pool"],
                "valor_nulo" => ""));
            array_push($info_campos_tramo_tarifas_electricas, array(
                "nombre" => "precio_consumo_tarifa_acceso",
                "valor" => $info_tramo["precio_consumo_tarifa_acceso"],
                "valor_nulo" => ""));
            array_push($info_campos_tramo_tarifas_electricas, array(
                "nombre" => "precio_potencia",
                "valor" => $info_tramo["precio_potencia"],
                "valor_nulo" => ""));
            array_push($info_campos_tramo_tarifas_electricas, array(
                "nombre" => "potencia",
                "valor" => $info_tramo["potencia"],
                "valor_nulo" => ""));
            $clausula_modificacion_tramo_tarifas_electricas = dame_clausula_modificacion_campos($bd_red, $info_campos_tramo_tarifas_electricas);
            if ($clausula_modificacion_tramo_tarifas_electricas != "")
            {
                $operacion_modificacion_tramo_tarifas_electricas = "
                    UPDATE ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
                    SET ";
                $operacion_modificacion_tramo_tarifas_electricas .= $clausula_modificacion_tramo_tarifas_electricas;
                $operacion_modificacion_tramo_tarifas_electricas .= "
                    WHERE
                        (tarifa_electrica IN (".$cadena_ids_tarifas_electricas_consulta."))
                        AND (tramo = '".$bd_red->_($numero_tramo)."')";
                $res_modificacion_tramo_tarifas_electricas = $bd_red->ejecuta_operacion($operacion_modificacion_tramo_tarifas_electricas);
                if ($res_modificacion_tramo_tarifas_electricas == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion_tramo_tarifas_electricas."'");
                }
                $tarifas_electricas_modificadas = true;
            }
            $info_campos_tramos_tarifas_electricas[$numero_tramo] = $info_campos_tramo_tarifas_electricas;
        }

        // Se añade la acción de usuario
        if ($tarifas_electricas_modificadas == true)
        {
            anyade_accion_usuario_modificar_tarifas_electricidad_Espanya(
                $tipo,
                $contrato,
                $ids_tarifas_electricas,
                $info_campos_tarifas_electricas,
                $info_campos_tramos_tarifas_electricas);
        }

        $res = "OK";
        if ($tarifas_electricas_modificadas == true)
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


    // Añade la acción de usuario de modificación de tarifas eléctricas
    function anyade_accion_usuario_modificar_tarifas_electricidad_Espanya(
        $tipo,
        $contrato,
        $ids_tarifas_electricas,
        $info_campos_tarifas_electricas,
        $info_campos_tramos_tarifas_electricas)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICA_TARIFAS;
        $objeto_accion_usuario = NULL;

        // Nombres de parámetros
        $nombres_tarifas_electricas = dame_nombres_tarifas(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $ids_tarifas_electricas);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_ELECTRICIDAD;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_TARIFAS] = $nombres_tarifas_electricas;
        $info_campos_modificados_tarifas_electricas = dame_info_campos_modificados($info_campos_tarifas_electricas);
        foreach ($info_campos_modificados_tarifas_electricas as $info_campo_modificado_tarifas_electricas)
        {
            $nombre_campo_modificado = $info_campo_modificado_tarifas_electricas["nombre"];
            $valor_campo_modificado = $info_campo_modificado_tarifas_electricas["valor"];
            $parametro_accion_usuario = dame_parametro_accion_usuario_campo_tarifa_electricidad_Espanya($nombre_campo_modificado);
            $parametros_accion_usuario[$parametro_accion_usuario] = $valor_campo_modificado;
        }
        anyade_parametros_tramos_accion_usuario_modificacion_tarifas_electricidad_Espanya(
            $tipo,
            $contrato,
            $info_campos_tramos_tarifas_electricas,
            $parametros_accion_usuario);

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }


    // Devuelve el parámetro de la acción de usuario correspondiente al campo de la tarifa eléctrica
    function dame_parametro_accion_usuario_campo_tarifa_electricidad_Espanya($campo_tarifa_electrica)
    {
        switch ($campo_tarifa_electrica)
        {
            case "tipo":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_ELECTRICA;
                break;
            }
            case "contrato":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_TIPO_CONTRATO_TARIFA_ELECTRICA;
                break;
            }
            case "expiracion":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_EXPIRACION;
                break;
            }
            case "prorrateo":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_PRORRATEO_EXCESO_POTENCIA;
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
            case "bonificacion_85":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_BONIFICACION_85;
                break;
            }
            case "tipo_medida":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_TIPO_MEDIDA;
                break;
            }
            case "potencia_nominal_transformador":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_POTENCIA_NOMINAL_TRANSFORMADOR;
                break;
            }
            case "id_indicador_omie_pass_pool":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_ID_INDICADOR_OMIE_PASS_POOL;
                break;
            }
            case "tipo_calculo_coste_pass_pool":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_TIPO_CALCULO_COSTE_PASS_POOL;
                break;
            }
            case "dia_calculo_coste_automatico_pass_pool":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL;
                break;
            }
            case "formula_precio_consumo_pass_through":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_FORMULA_PRECIO_CONSUMO_PASS_THROUGH;
                break;
            }
            case "impuesto_electrico":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_IMPUESTO_ELECTRICO;
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
            case "igic_reducido":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_IGIC_REDUCIDO;
                break;
            }
            case "igic_normal":
            {
                $parametro_accion_usuario = PARAMETRO_ACCION_USUARIO_IGIC_NORMAL;
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
