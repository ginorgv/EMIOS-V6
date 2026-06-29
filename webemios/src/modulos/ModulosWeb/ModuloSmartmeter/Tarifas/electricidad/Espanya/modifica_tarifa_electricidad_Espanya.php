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
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_TARIFA_ELECTRICA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_tarifa_electrica = $_POST['id_tarifa_electrica'];
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

    // Conversión de fechas
    $cadena_fecha_expiracion_base_datos_local = convierte_formato_fecha($cadena_fecha_expiracion_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
    $fecha_inicio_contrato_cierre = $fecha_inicio_contrato_cierre ? (convierte_formato_fecha($fecha_inicio_contrato_cierre, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS)) : NULL;

    // Se comprueba si existe otra tarifa eléctrica con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_tarifa_electrica)."')";
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
        // Comprobaciones antes de modificar la tarifa eléctrica:
        // - Si el tipo de tarifa eléctrica es 'pass-through' o 'cierre', se valida la fórmula de cálculo de precio de consumo
        // - Si hay grupo, se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas eléctricas del mismo grupo
        $modificar_tarifa = true;

        // Si el tipo de tarifa eléctrica es 'pass-through', se valida la fórmula de cálculo de precio de consumo
        if (($modificar_tarifa == true) && ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH))
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
                $modificar_tarifa = False;

                $error = $resultado_funcion_externa["error"];
                $descripcion_error = dame_descripcion_error_funcion_variables($error);

                $res = "ERROR";
                $msg = $idiomas->_("Ha ocurrido un error al evaluar la fórmula de precio de consumo")."\n(".$descripcion_error.")";
            }
        }

        if (($modificar_tarifa == true) && ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE))
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
                $modificar_tarifa = False;

                $error = $resultado_funcion_externa["error"];
                $descripcion_error = dame_descripcion_error_funcion_variables($error);

                $res = "ERROR";
                $msg = $idiomas->_("Ha ocurrido un error al evaluar la fórmula de precio de consumo")."\n(".$descripcion_error.")";
            }
        }

        // Si hay grupo, se comprueba que haya fecha de expiración y que no coincida ninguna otra fecha de expiración de tarifas eléctricas del mismo grupo
        if (($modificar_tarifa == true) && ($id_grupo != ID_NINGUNO))
        {
            $consulta_tarifas_electricas = "
                SELECT nombre
                FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    (grupo = '".$bd_red->_($id_grupo)."')
                    AND (fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."')
                    AND (id <> '".$bd_red->_($id_tarifa_electrica)."')";
            $res_tarifas_electricas = $bd_red->ejecuta_consulta($consulta_tarifas_electricas);
            if ($res_tarifas_electricas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas_electricas."'");
            }
            if ($res_tarifas_electricas->dame_numero_filas() > 0)
            {
                $modificar_tarifa = False;

                $fila_tarifa_electrica = $res_tarifas_electricas->dame_siguiente_fila();
                $nombre_tarifa_electrica = $fila_tarifa_electrica["nombre"];

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una tarifa en el mismo grupo con la misma fecha de expiración")."\n(".
                    $nombre_tarifa_electrica.")";
            }
        }

        // Se modifica la tarifa eléctrica
        if ($modificar_tarifa == true)
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

            // Se recuperan la filas de la tarifa y de los tramos de la tarifa anteriores (antes de la modificación)
            $fila_tarifa_electrica_anterior = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa_electrica);
            $filas_tramos_tarifa_electrica_anteriores = dame_filas_tramos_tarifa_electricidad_Espanya($id_tarifa_electrica);

            // Crear cadena porque puede tener valor o ser NULL
            $cadena_fecha_inicio_contrato_cierre = $fecha_inicio_contrato_cierre ? "'".$fecha_inicio_contrato_cierre."'" : "NULL";
            // Se modifica la tarifa eléctrica
            $operacion_modificacion = "
                UPDATE ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    tipo = '".$bd_red->_($tipo)."',
                    contrato = '".$bd_red->_($contrato)."',
                    grupo = '".$bd_red->_($id_grupo)."',
                    expiracion = '".$bd_red->_($expiracion)."',
                    fecha_expiracion = '".$bd_red->_($cadena_fecha_expiracion_base_datos_local)."',
                    numero_dias_preaviso_expiracion = '".$bd_red->_($numero_dias_preaviso_expiracion)."',
                    bonificacion_85 = '".$bd_red->_($bonificacion_85)."',
                    tipo_medida = '".$bd_red->_($tipo_medida)."',
                    potencia_nominal_transformador = '".$bd_red->_($potencia_nominal_transformador)."',
                    id_indicador_omie_pass_pool = '".$bd_red->_($id_indicador_omie_pass_pool)."',
                    tipo_calculo_coste_pass_pool = '".$bd_red->_($tipo_calculo_coste_pass_pool)."',
                    dia_calculo_coste_automatico_pass_pool = '".$bd_red->_($dia_calculo_coste_automatico_pass_pool)."',
                    formula_precio_consumo_pass_through = '".$bd_red->_($formula_precio_consumo_pass_through)."',
                    fecha_inicio_contrato_cierre = $cadena_fecha_inicio_contrato_cierre,
                    impuesto_electrico = '".$bd_red->_($impuesto_electrico)."',
                    tipo_alquiler_contador = '".$bd_red->_($tipo_alquiler_contador)."',
                    alquiler_contador = '".$bd_red->_($alquiler_contador)."',
                    iva = '".$bd_red->_($iva)."',
                    igic_reducido = '".$bd_red->_($igic_reducido)."',
                    igic_normal = '".$bd_red->_($igic_normal)."',
                    prorrateo = '".$bd_red->_($prorrateo)."'
                WHERE
                    id = '".$bd_red->_($id_tarifa_electrica)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }

            // Se actualiza la información de los tramos de la tarifa eléctrica (se eliminan y se añaden)
            $operacion_borrado_tramos_tarifa_electrica = "
                DELETE
                FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    tarifa_electrica = '".$bd_red->_($id_tarifa_electrica)."'";
            $res_borrado_tramos_tarifa_electrica = $bd_red->ejecuta_operacion($operacion_borrado_tramos_tarifa_electrica);
            if ($res_borrado_tramos_tarifa_electrica == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_tramos_tarifa_electrica."'");
            }
            foreach ($info_tramos as $info_tramo)
            {
                $operacion_insercion_tramo_tarifa_electrica = "
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
                $res_insercion_tramo_tarifa_electrica = $bd_red->ejecuta_operacion($operacion_insercion_tramo_tarifa_electrica);
                if ($res_insercion_tramo_tarifa_electrica == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_insercion_tramo_tarifa_electrica."'");
                }
            }

            // Si tiene grupo asignado se asigna el grupo a los sensores que tenían asignada la tarifa eléctrica
            if ($id_grupo != ID_NINGUNO)
            {
                asigna_grupo_tarifas_sensores_tarifa(MEDICION_ELECTRICIDAD, $id_grupo, $id_tarifa_electrica);
            }

            // Se recuperan la filas de la tarifa y de los tramos de la tarifa actuales
            $fila_tarifa_electrica_actual = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa_electrica);
            $filas_tramos_tarifa_electrica_actuales = dame_filas_tramos_tarifa_electricidad_Espanya($id_tarifa_electrica);

            anyade_accion_usuario_modificar_tarifa_electricidad_Espanya(
                $fila_tarifa_electrica_actual,
                $fila_tarifa_electrica_anterior,
                $filas_tramos_tarifa_electrica_actuales,
                $filas_tramos_tarifa_electrica_anteriores);

            $res = "OK";
            $msg = $idiomas->_("Tarifa modificada correctamente").".\n".
                $idiomas->_("Los nuevos parámetros tendrán efecto a partir de los siguientes datos recibidos").".\n".
                $idiomas->_("Si quiere que se vuelvan a calcular datos anteriores con los nuevos parámetros de la tarifa eléctrica, tendrá que recalcular datos eléctricos seleccionando esta tarifa eléctrica y el tiempo a partir del cual quiere que sean efectivos");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación de la tarifa
    function anyade_accion_usuario_modificar_tarifa_electricidad_Espanya(
        $fila_actual,
        $fila_anterior,
        $filas_tramos_actuales,
        $filas_tramos_anteriores)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_TARIFA;

        // Características de tipos de tarifas eléctricas
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($fila_actual["tipo"]);
        $tipo_calculo_coste_potencias = $caracteristicas_tipo_tarifa_electrica["tipo_calculo_coste_potencias"];
        $parametros_medida_datos_facturacion = $caracteristicas_tipo_tarifa_electrica["parametros_medida_datos_facturacion"];
        $tipo_tarifa_canarias = $caracteristicas_tipo_tarifa_electrica["tipo_tarifa_canarias"];
        $caracteristicas_tipo_tarifa_electrica_anterior = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($fila_anterior["tipo"]);
        $tipo_calculo_coste_potencias_anterior = $caracteristicas_tipo_tarifa_electrica_anterior["tipo_calculo_coste_potencias"];
        $parametros_medida_datos_facturacion_anterior = $caracteristicas_tipo_tarifa_electrica_anterior["parametros_medida_datos_facturacion"];
        $tipo_tarifa_canarias_anterior = $caracteristicas_tipo_tarifa_electrica_anterior["tipo_tarifa_canarias"];

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_ELECTRICIDAD;
        $parametros_accion_usuario_anteriores = array();
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_ELECTRICIDAD;
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
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_ELECTRICA] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_TARIFA_ELECTRICA] = $fila_anterior["tipo"];
        }
        if ($fila_actual["contrato"] != $fila_anterior["contrato"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CONTRATO_TARIFA_ELECTRICA] = $fila_actual["contrato"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_CONTRATO_TARIFA_ELECTRICA] = $fila_anterior["contrato"];
        }
        if ($fila_actual["grupo"] != $fila_anterior["grupo"])
        {
            $nombre_grupo = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_ELECTRICAS_ESPANYA, $fila_actual["grupo"]);
            $nombre_grupo_anterior = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_ELECTRICAS_ESPANYA, $fila_anterior["grupo"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo_anterior;
        }
        if ($fila_actual["expiracion"] != $fila_anterior["expiracion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_EXPIRACION_TARIFA] = $fila_actual["expiracion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_EXPIRACION_TARIFA] = $fila_anterior["expiracion"];
        }
        $log = dame_log();
        $log -> debug("La fila actual es:");
        $log -> debug($fila_actual["prorrateo"]);
        $log -> debug("La fila anterior es:");
        $log -> debug($fila_anterior["prorrateo"]);
        if ($fila_actual["prorrateo"] != $fila_anterior["prorrateo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRORRATEO_EXCESO_POTENCIA] = $fila_actual["prorrateo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRORRATEO_EXCESO_POTENCIA] = $fila_anterior["prorrateo"];
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
        if ($fila_actual["id_indicador_omie_pass_pool"] != $fila_anterior["id_indicador_omie_pass_pool"])
        {
            if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ID_INDICADOR_OMIE_PASS_POOL] = $fila_actual["id_indicador_omie_pass_pool"];
            }
            if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ID_INDICADOR_OMIE_PASS_POOL] = $fila_anterior["id_indicador_omie_pass_pool"];
            }
        }
        if ($fila_actual["tipo_calculo_coste_pass_pool"] != $fila_anterior["tipo_calculo_coste_pass_pool"])
        {
            if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CALCULO_COSTE_PASS_POOL] = $fila_actual["tipo_calculo_coste_pass_pool"];
            }
            if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_CALCULO_COSTE_PASS_POOL] = $fila_anterior["tipo_calculo_coste_pass_pool"];
            }
        }
        if ($fila_actual["dia_calculo_coste_automatico_pass_pool"] != $fila_anterior["dia_calculo_coste_automatico_pass_pool"])
        {
            if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL] = $fila_actual["dia_calculo_coste_automatico_pass_pool"];
            }
            if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL] = $fila_anterior["dia_calculo_coste_automatico_pass_pool"];
            }
        }
        if ($fila_actual["formula_precio_consumo_pass_through"] != $fila_anterior["formula_precio_consumo_pass_through"])
        {
            if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FORMULA_PRECIO_CONSUMO_PASS_THROUGH] = $fila_actual["formula_precio_consumo_pass_through"];
            }
            if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FORMULA_PRECIO_CONSUMO_PASS_THROUGH] = $fila_anterior["formula_precio_consumo_pass_through"];
            }
        }
        if ($fila_actual["formula_precio_consumo_cierre"] != $fila_anterior["formula_precio_consumo_cierre"])
        {
            if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FORMULA_PRECIO_CONSUMO_CIERRE] = $fila_actual["formula_precio_consumo_cierre"];
            }
            if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FORMULA_PRECIO_CONSUMO_CIERRE] = $fila_anterior["formula_precio_consumo_cierre"];
            }
        }
        if ($fila_actual["bonificacion_85"] != $fila_anterior["bonificacion_85"])
        {
            if ($tipo_calculo_coste_potencias == TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_BONIFICACION_85] = $fila_actual["bonificacion_85"];
            }
            if ($tipo_calculo_coste_potencias_anterior == TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_BONIFICACION_85] = $fila_anterior["bonificacion_85"];
            }
        }
        if ($fila_actual["tipo_medida"] != $fila_anterior["tipo_medida"])
        {
            if ($parametros_medida_datos_facturacion == true)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_MEDIDA] = $fila_actual["tipo_medida"];
            }
            if ($parametros_medida_datos_facturacion_anterior == true)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_MEDIDA] = $fila_anterior["tipo_medida"];
            }
        }
        if ($fila_actual["potencia_nominal_transformador"] != $fila_anterior["potencia_nominal_transformador"])
        {
            if ($parametros_medida_datos_facturacion == true)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_POTENCIA_NOMINAL_TRANSFORMADOR] = $fila_actual["potencia_nominal_transformador"];
            }
            if ($parametros_medida_datos_facturacion_anterior == true)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_POTENCIA_NOMINAL_TRANSFORMADOR] = $fila_anterior["potencia_nominal_transformador"];
            }
        }
        if ($fila_actual["impuesto_electrico"] != $fila_anterior["impuesto_electrico"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IMPUESTO_ELECTRICO] = $fila_actual["impuesto_electrico"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IMPUESTO_ELECTRICO] = $fila_anterior["impuesto_electrico"];
        }
        if ($fila_actual["tipo_alquiler_contador"] != $fila_anterior["tipo_alquiler_contador"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_ALQUILER_CONTADOR] = $fila_actual["tipo_alquiler_contador"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_ALQUILER_CONTADOR] = $fila_anterior["tipo_alquiler_contador"];
        }
        if ($fila_actual["alquiler_contador"] != $fila_anterior["alquiler_contador"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ALQUILER_CONTADOR] = $fila_actual["alquiler_contador"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ALQUILER_CONTADOR] = $fila_anterior["alquiler_contador"];
        }
        if ($tipo_tarifa_canarias == $tipo_tarifa_canarias_anterior)
        {
            if ($tipo_tarifa_canarias == false)
            {
                if ($fila_actual["iva"] != $fila_anterior["iva"])
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA] = $fila_actual["iva"];
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IVA] = $fila_anterior["iva"];
                }
            }
            else
            {
                if ($fila_actual["igic_reducido"] != $fila_anterior["igic_reducido"])
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IGIC_REDUCIDO] = $fila_actual["igic_reducido"];
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IGIC_REDUCIDO] = $fila_anterior["igic_reducido"];
                }
                if ($fila_actual["igic_normal"] != $fila_anterior["igic_normal"])
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IGIC_NORMAL] = $fila_actual["igic_normal"];
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IGIC_NORMAL] = $fila_anterior["igic_normal"];
                }
            }
        }
        else
        {
            if ($tipo_tarifa_canarias == false)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IVA] = $fila_actual["iva"];
            }
            else
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IGIC_REDUCIDO] = $fila_actual["igic_reducido"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IGIC_NORMAL] = $fila_actual["igic_normal"];
            }
            if ($tipo_tarifa_canarias_anterior == false)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IVA] = $fila_anterior["iva"];
            }
            else
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IGIC_REDUCIDO] = $fila_anterior["igic_reducido"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IGIC_NORMAL] = $fila_anterior["igic_normal"];
            }
        }
        anyade_parametros_tramos_accion_usuario_modificacion_tarifa_electricidad_Espanya(
            $fila_actual,
            $fila_anterior,
            $filas_tramos_actuales,
            $filas_tramos_anteriores,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores);

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
