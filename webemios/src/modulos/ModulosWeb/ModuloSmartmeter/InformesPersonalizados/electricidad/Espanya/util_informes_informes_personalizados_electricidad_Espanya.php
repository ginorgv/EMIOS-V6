<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/util_informes_consumos_costes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/util_informes_consumos_costes_electricidad.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/Espanya/util_informes_consumos_costes_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/electricidad/util_electricidad.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_informes_facturas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/InformesFichero/util_informes_personalizados_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');


    //
    // Funciones de información de informes personalizados (electricidad - España)
    //


    // Devuelve el estudio general de un sensor
    function dame_estudio_general_sensor_electricidad_Espanya($parametros)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $medicion = $parametros["medicion"];
        $id_ratio = $parametros["id_ratio"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $apartados = $parametros["apartados"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Inicialización de resultados
        $datos_instalacion = array();
        $datos_portada = array();
        $datos_analisis_consumo = array();
        $datos_analisis_coste = array();
        $datos_excesos_potencia = array();
        $datos_excesos_energia_reactiva = array();
        $datos_cortes_tension = array();
        $datos_simulacion_factura = array();

        // Se recuperan las filas de valores del sensor (por horas y cuartos de hora)
        $parametros_filas_valores_sensor_horas = array(
            "id_sensor" => $id_sensor,
            "intervalo_valores" => INTERVALO_VALORES_HORA,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local);
        $filas_valores_sensor_horas = dame_filas_valores_sensor($parametros_filas_valores_sensor_horas);

        // Se recuperan las filas de valores del sensor (por días)
        $parametros_filas_valores_sensor_dias = $parametros_filas_valores_sensor_horas;
        $parametros_filas_valores_sensor_dias["intervalo_valores"] = INTERVALO_VALORES_DIA;
        $filas_valores_sensor_dias = dame_filas_valores_sensor($parametros_filas_valores_sensor_dias);

        // Se recupera el identificador de tarifa del sensor
        $id_tarifa = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_inicio_local_local);

        // Portada
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_ELECTRICIDAD_ESPANYA, $apartados) == true)
        {
            // Nombre de red
            $consulta_nombre_red = "
                SELECT nombre
                FROM redes
                WHERE
                   id = '".$_SESSION["id_red"]."'";
            $res_nombre_red = $bd_red->ejecuta_consulta($consulta_nombre_red);
            if ($res_nombre_red == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_nombre_red."'");
            }
            $fila_nombre_red = $res_nombre_red->dame_siguiente_fila();
            $nombre_red = $fila_nombre_red["nombre"];

            // Descripción de sensor
            $consulta_descripcion_sensor = "
                SELECT
                    descripcion
                FROM sensores
                WHERE
                    id = '".$bd_red->_($id_sensor)."'";
            $res_descripcion_sensor = $bd_red->ejecuta_consulta($consulta_descripcion_sensor);
            if ($res_descripcion_sensor == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_descripcion_sensor."'");
            }
            $fila_descripcion_sensor = $res_descripcion_sensor->dame_siguiente_fila();
            $descripcion_sensor = $fila_descripcion_sensor["descripcion"];
            if ($descripcion_sensor == "")
            {
                $descripcion_sensor = $nombre_sensor;
            }

            // Fechas de inicio y fin
            $cadena_fecha_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
            $cadena_fechas_locales_locales = $cadena_fecha_inicio_local_local." - ".$cadena_fecha_fin_local_local;

            // Datos del apartado
            $datos_portada["nombre_red"] = htmlspecialchars(strtoupper($nombre_red), ENT_QUOTES);
            $datos_portada["descripcion_sensor"] = htmlspecialchars($descripcion_sensor, ENT_QUOTES);
            $datos_portada["cadena_fechas"] = $cadena_fechas_locales_locales;
        }

        // Instalación
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_ELECTRICIDAD_ESPANYA, $apartados) == true)
        {
            $datos_instalacion = dame_datos_instalacion_sensor(MEDICION_ELECTRICIDAD, $id_sensor, $id_tarifa);
        }

        // Análisis de consumo
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_ELECTRICIDAD_ESPANYA, $apartados) == true)
        {
            // Información de energía activa (consumo por días)
            $parametros_informacion_sensor_energia_activa_consumo_dias = array(
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_ENERGIA_ACTIVA,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "campo" => CAMPO_INCREMENTO,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                "intervalo_valores" => INTERVALO_VALORES_DIA,
                "tipo_mapa_calor" => TIPO_MAPA_CALOR_NINGUNO,
                "minutos_desfase_utc" => $minutos_desfase_utc,
                "tipo_informe" => $tipo_informe);
            $res_informacion_sensor_energia_activa_consumo_dias = dame_informacion_sensor_energia(
                $parametros_informacion_sensor_energia_activa_consumo_dias,
                $filas_valores_sensor_dias);

            // Datos del apartado (gráfica de consumos)
            $datos_analisis_consumo["hay_datos_grafica_consumos"] = $res_informacion_sensor_energia_activa_consumo_dias["hay_datos"];
            if ($datos_analisis_consumo["hay_datos_grafica_consumos"] == true)
            {
                $datos_analisis_consumo["max_consumo"] = $res_informacion_sensor_energia_activa_consumo_dias["max_valor"];
                $datos_analisis_consumo["etiquetas_grafica_consumos"] = $res_informacion_sensor_energia_activa_consumo_dias["etiquetas_graficas"];
                $datos_analisis_consumo["grafica_consumos"] = $res_informacion_sensor_energia_activa_consumo_dias["grafica_valores"];
            }

            // Información de energía activa (consumo por horas)
            $parametros_informacion_sensor_energia_activa_consumo_horas = array(
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_ENERGIA_ACTIVA,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "campo" => CAMPO_INCREMENTO,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                "intervalo_valores" => INTERVALO_VALORES_HORA,
                "tipo_mapa_calor" => TIPO_MAPA_CALOR_SEMANAL,
                "minutos_desfase_utc" => $minutos_desfase_utc,
                "tipo_informe" => $tipo_informe);
            $res_informacion_sensor_energia_activa_consumo_horas = dame_informacion_sensor_energia(
                $parametros_informacion_sensor_energia_activa_consumo_horas,
                $filas_valores_sensor_horas);

            // Datos del apartado (mapa de calor de consumos)
            $datos_analisis_consumo["hay_datos_mapa_calor_consumos"] = $res_informacion_sensor_energia_activa_consumo_horas["hay_datos"];
            if ($datos_analisis_consumo["hay_datos_mapa_calor_consumos"] == true)
            {
                $datos_analisis_consumo["colores_mapa_calor_consumos"] = $res_informacion_sensor_energia_activa_consumo_horas["colores_mapa_calor_valores"];
                $datos_analisis_consumo["dias_mapa_calor_consumos"] = $res_informacion_sensor_energia_activa_consumo_horas["dias_mapa_calor_valores"];
                $datos_analisis_consumo["datos_mapa_calor_consumos"] = $res_informacion_sensor_energia_activa_consumo_horas["datos_mapa_calor_valores"];
                $datos_analisis_consumo["unidad_medida_consumo"] = $res_informacion_sensor_energia_activa_consumo_horas["unidad_medida"];
            }
        }

        // Análisis de coste
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_ELECTRICIDAD_ESPANYA, $apartados) == true)
        {
            // Información de energía activa (coste por días)
            $parametros_informacion_sensor_energia_activa_coste_dias = array(
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_ENERGIA_ACTIVA,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "campo" => CAMPO_COSTE,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                "intervalo_valores" => INTERVALO_VALORES_DIA,
                "tipo_mapa_calor" => TIPO_MAPA_CALOR_NINGUNO,
                "minutos_desfase_utc" => $minutos_desfase_utc,
                "tipo_informe" => $tipo_informe);
            $res_informacion_sensor_energia_activa_coste_dias = dame_informacion_sensor_energia(
                $parametros_informacion_sensor_energia_activa_coste_dias,
                $filas_valores_sensor_dias);

            // Datos del apartado (gráfica de costes)
            $datos_analisis_coste["hay_datos_grafica_costes"] = $res_informacion_sensor_energia_activa_coste_dias["hay_datos"];
            if ($datos_analisis_coste["hay_datos_grafica_costes"] == true)
            {
                $datos_analisis_coste["max_coste"] = $res_informacion_sensor_energia_activa_coste_dias["max_valor"];
                $datos_analisis_coste["etiquetas_grafica_costes"] = $res_informacion_sensor_energia_activa_coste_dias["etiquetas_graficas"];
                $datos_analisis_coste["grafica_costes"] = $res_informacion_sensor_energia_activa_coste_dias["grafica_valores"];
            }

            // Información de energía activa (coste por horas)
            $parametros_informacion_sensor_energia_activa_coste_horas = array(
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_ENERGIA_ACTIVA,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "campo" => CAMPO_COSTE,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                "intervalo_valores" => INTERVALO_VALORES_HORA,
                "tipo_mapa_calor" => TIPO_MAPA_CALOR_SEMANAL,
                "minutos_desfase_utc" => $minutos_desfase_utc,
                "tipo_informe" => $tipo_informe);
            $res_informacion_sensor_energia_activa_coste_horas = dame_informacion_sensor_energia(
                $parametros_informacion_sensor_energia_activa_coste_horas,
                $filas_valores_sensor_horas);

            // Datos del apartado (mapa de calor de costes)
            $datos_analisis_coste["hay_datos_mapa_calor_costes"] = $res_informacion_sensor_energia_activa_coste_horas["hay_datos"];
            if ($datos_analisis_coste["hay_datos_mapa_calor_costes"] == true)
            {
                $datos_analisis_coste["colores_mapa_calor_costes"] = $res_informacion_sensor_energia_activa_coste_horas["colores_mapa_calor_valores"];
                $datos_analisis_coste["dias_mapa_calor_costes"] = $res_informacion_sensor_energia_activa_coste_horas["dias_mapa_calor_valores"];
                $datos_analisis_coste["datos_mapa_calor_costes"] = $res_informacion_sensor_energia_activa_coste_horas["datos_mapa_calor_valores"];
                $datos_analisis_coste["unidad_medida_coste"] = $res_informacion_sensor_energia_activa_coste_horas["unidad_medida"];
            }
        }

        // Análisis de consumo y coste
        if ((in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_ELECTRICIDAD_ESPANYA, $apartados) == true) ||
            (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_ELECTRICIDAD_ESPANYA, $apartados) == true))
        {
            // Información de los periodos
            $info_periodos = dame_info_periodos_fechas_inicio_fin_parametros_duracion_separacion_periodos(
                $cadena_fecha_hora_inicio_local_local,
                $cadena_fecha_hora_fin_local_local,
                NULL);
            $cadena_fecha_hora_inicio_anterior_local_local = $info_periodos["cadena_fecha_hora_inicio_anterior_local_local"];
            $cadena_fecha_hora_inicio_posterior_local_local = $info_periodos["cadena_fecha_hora_inicio_posterior_local_local"];
            $numero_dias_periodo = $info_periodos["numero_dias_periodo"];

            // Información de comparación de periodos (por días)
            $parametros_consumos_costes_sensor_periodos = array(
                "medicion" => $medicion,
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_ENERGIA_ACTIVA,
                "id_ratio" => $id_ratio,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "fecha_hora_inicio_anterior" => $cadena_fecha_hora_inicio_anterior_local_local,
                "fecha_hora_inicio_posterior" => $cadena_fecha_hora_inicio_posterior_local_local,
                "numero_dias_periodo" => $numero_dias_periodo,
                "intervalo_valores" => INTERVALO_VALORES_DIA,
                "minutos_desfase_utc" => $minutos_desfase_utc);
            $res_consumos_costes_sensor_periodos = dame_consumos_costes_sensor_periodos($parametros_consumos_costes_sensor_periodos);

            // Datos del apartado (consumo por periodos)
            if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_ELECTRICIDAD_ESPANYA, $apartados) == true)
            {
                $datos_analisis_consumo["hay_datos_consumos_periodos"] = $res_consumos_costes_sensor_periodos["hay_datos"];
                if ($datos_analisis_consumo["hay_datos_consumos_periodos"] == true)
                {
                    $datos_analisis_consumo["max_consumo_periodos"] = $res_consumos_costes_sensor_periodos["max_consumo"];
                    $datos_analisis_consumo["etiquetas_consumos_periodos"] = $res_consumos_costes_sensor_periodos["etiquetas"];
                    $datos_analisis_consumo["etiquetas_tooltips_consumos_periodos"] = $res_consumos_costes_sensor_periodos["etiquetas_tooltips"];
                    $datos_analisis_consumo["min_fecha_consumo_periodos"] = $res_consumos_costes_sensor_periodos["min_fecha_consumo"];
                    $datos_analisis_consumo["max_fecha_consumo_periodos"] = $res_consumos_costes_sensor_periodos["max_fecha_consumo"];
                    $datos_analisis_consumo["grafica_consumos_periodos"] = $res_consumos_costes_sensor_periodos["grafica_consumos"];
                    $datos_analisis_consumo["tabla_evolucion_consumos_tramos"] = $res_consumos_costes_sensor_periodos["tabla_evolucion_consumos_tramos"];
                }
            }

            // Datos del apartado (coste por periodos)
            if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_ELECTRICIDAD_ESPANYA, $apartados) == true)
            {
                $datos_analisis_coste["hay_datos_costes_periodos"] = (
                    ($res_consumos_costes_sensor_periodos["hay_datos"] == true) &&
                    ($res_consumos_costes_sensor_periodos["hay_datos_costes"] == true));
                if ($datos_analisis_coste["hay_datos_costes_periodos"] == true)
                {
                    $datos_analisis_coste["max_coste_periodos"] = $res_consumos_costes_sensor_periodos["max_coste"];
                    $datos_analisis_coste["etiquetas_costes_periodos"] = $res_consumos_costes_sensor_periodos["etiquetas"];
                    $datos_analisis_coste["etiquetas_tooltips_costes_periodos"] = $res_consumos_costes_sensor_periodos["etiquetas_tooltips"];
                    $datos_analisis_coste["min_fecha_coste_periodos"] = $res_consumos_costes_sensor_periodos["min_fecha_coste"];
                    $datos_analisis_coste["max_fecha_coste_periodos"] = $res_consumos_costes_sensor_periodos["max_fecha_coste"];
                    $datos_analisis_coste["grafica_costes_periodos"] = $res_consumos_costes_sensor_periodos["grafica_costes"];
                    $datos_analisis_coste["tabla_evolucion_consumos_costes"] = $res_consumos_costes_sensor_periodos["tabla_evolucion_consumos_costes"];
                }
            }

            // Consumos y costes por tramo
            $parametros_consumos_costes_sensor_tramos = array(
                "id_ratio" => $id_ratio,
                "id_sensor" => $id_sensor,
                "valor" => ID_TODOS,
                "agrupacion_valores" => ID_TODOS,
                "nombre_sensor" => $nombre_sensor,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                "mostrar_tablas_tramos" => true);
            $res_consumos_costes_sensor_tramos = dame_consumos_costes_sensor_tramos_electricidad(
                $parametros_consumos_costes_sensor_tramos);

            // Datos del apartado (consumo por tramos)
            if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_ELECTRICIDAD_ESPANYA, $apartados) == true)
            {
                $datos_analisis_consumo["hay_datos_consumos_tramos"] = $res_consumos_costes_sensor_tramos["hay_datos"];
                if ($datos_analisis_consumo["hay_datos_consumos_tramos"] == true)
                {
                    $datos_analisis_consumo["max_media_consumo_dia_semana"] = $res_consumos_costes_sensor_tramos["max_media_consumo_dia_semana"];
                    $datos_analisis_consumo["nombres_tramos"] = $res_consumos_costes_sensor_tramos["nombres_tramos"];
                    $datos_analisis_consumo["grafica_medias_consumos_tramos_dias_semana"] = $res_consumos_costes_sensor_tramos["grafica_medias_consumos_tramos_dias_semana"];
                    $datos_analisis_consumo["tabla_consumos_tramos"] = $res_consumos_costes_sensor_tramos["tabla_consumos_tramos"];
                }
            }

            // Datos del apartado (coste por tramos)
            if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_ELECTRICIDAD_ESPANYA, $apartados) == true)
            {
                $datos_analisis_coste["hay_datos_costes_tramos"] = $res_consumos_costes_sensor_tramos["hay_datos"];
                if ($datos_analisis_coste["hay_datos_costes_tramos"] == true)
                {
                    $datos_analisis_coste["max_media_coste_dia_semana"] = $res_consumos_costes_sensor_tramos["max_media_coste_dia_semana"];
                    $datos_analisis_coste["nombres_tramos"] = $res_consumos_costes_sensor_tramos["nombres_tramos"];
                    $datos_analisis_coste["grafica_medias_costes_tramos_dias_semana"] = $res_consumos_costes_sensor_tramos["grafica_medias_costes_tramos_dias_semana"];
                    $datos_analisis_coste["tabla_costes_tramos"] = $res_consumos_costes_sensor_tramos["tabla_costes_tramos"];
                }
            }
        }

        // Resumen de consumo
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_CONSUMO_ELECTRICIDAD_ESPANYA, $apartados) == true)
        {
            // Información de energía activa (consumo por horas)
            $parametros_informacion_sensor_energia_activa_consumo_horas = array(
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_ENERGIA_ACTIVA,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "campo" => CAMPO_INCREMENTO,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                "intervalo_valores" => INTERVALO_VALORES_HORA,
                "tipo_mapa_calor" => TIPO_MAPA_CALOR_SEMANAL,
                "minutos_desfase_utc" => $minutos_desfase_utc,
                "tipo_informe" => $tipo_informe);
            $res_informacion_sensor_energia_activa_consumo_horas = dame_informacion_sensor_energia(
                $parametros_informacion_sensor_energia_activa_consumo_horas,
                $filas_valores_sensor_horas);

            // Datos del apartado (gráfica de consumos y mapa de calor de consumos)
            $datos_resumen_consumo["hay_datos_grafica_consumos_mapa_calor_consumos"] = $res_informacion_sensor_energia_activa_consumo_horas["hay_datos"];
            if ($datos_resumen_consumo["hay_datos_grafica_consumos_mapa_calor_consumos"] == true)
            {
                $datos_resumen_consumo["max_consumo"] = $res_informacion_sensor_energia_activa_consumo_horas["max_valor"];
                $datos_resumen_consumo["etiquetas_grafica_consumos"] = $res_informacion_sensor_energia_activa_consumo_horas["etiquetas_graficas"];
                $datos_resumen_consumo["grafica_consumos"] = $res_informacion_sensor_energia_activa_consumo_horas["grafica_valores"];
                $datos_resumen_consumo["colores_mapa_calor_consumos"] = $res_informacion_sensor_energia_activa_consumo_horas["colores_mapa_calor_valores"];
                $datos_resumen_consumo["dias_mapa_calor_consumos"] = $res_informacion_sensor_energia_activa_consumo_horas["dias_mapa_calor_valores"];
                $datos_resumen_consumo["datos_mapa_calor_consumos"] = $res_informacion_sensor_energia_activa_consumo_horas["datos_mapa_calor_valores"];
                $datos_resumen_consumo["unidad_medida_consumo"] = $res_informacion_sensor_energia_activa_consumo_horas["unidad_medida"];
            }

            // Información de los periodos
            $info_periodos = dame_info_periodos_fechas_inicio_fin_parametros_duracion_separacion_periodos(
                $cadena_fecha_hora_inicio_local_local,
                $cadena_fecha_hora_fin_local_local,
                NULL);
            $cadena_fecha_hora_inicio_anterior_local_local = $info_periodos["cadena_fecha_hora_inicio_anterior_local_local"];
            $cadena_fecha_hora_inicio_posterior_local_local = $info_periodos["cadena_fecha_hora_inicio_posterior_local_local"];
            $numero_dias_periodo = $info_periodos["numero_dias_periodo"];

            // Información de comparación de periodos
            $parametros_consumos_costes_sensor_periodos = array(
                "medicion" => $medicion,
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_ENERGIA_ACTIVA,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "fecha_hora_inicio_anterior" => $cadena_fecha_hora_inicio_anterior_local_local,
                "fecha_hora_inicio_posterior" => $cadena_fecha_hora_inicio_posterior_local_local,
                "numero_dias_periodo" => $numero_dias_periodo,
                "intervalo_valores" => INTERVALO_VALORES_DIA,
                "minutos_desfase_utc" => $minutos_desfase_utc);
            $res_consumos_costes_sensor_periodos = dame_consumos_costes_sensor_periodos($parametros_consumos_costes_sensor_periodos);

            // Datos del apartado (consumo por periodos)
            $datos_resumen_consumo["hay_datos_consumos_periodos"] = $res_consumos_costes_sensor_periodos["hay_datos"];
            if ($datos_resumen_consumo["hay_datos_consumos_periodos"] == true)
            {
                $datos_resumen_consumo["max_consumo_periodos"] = $res_consumos_costes_sensor_periodos["max_consumo"];
                $datos_resumen_consumo["etiquetas_consumos_periodos"] = $res_consumos_costes_sensor_periodos["etiquetas"];
                $datos_resumen_consumo["etiquetas_tooltips_consumos_periodos"] = $res_consumos_costes_sensor_periodos["etiquetas_tooltips"];
                $datos_resumen_consumo["min_fecha_consumo_periodos"] = $res_consumos_costes_sensor_periodos["min_fecha_consumo"];
                $datos_resumen_consumo["max_fecha_consumo_periodos"] = $res_consumos_costes_sensor_periodos["max_fecha_consumo"];
                $datos_resumen_consumo["grafica_consumos_periodos"] = $res_consumos_costes_sensor_periodos["grafica_consumos"];
            }
        }

        // Resumen de coste
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_COSTE_ELECTRICIDAD_ESPANYA, $apartados) == true)
        {
            // Información de energía activa (coste por horas)
            $parametros_informacion_sensor_energia_activa_coste_horas = array(
                "medicion" => $medicion,
                "id_ratio" => $id_ratio,
                "id_sensor" => $id_sensor,
                "clase_sensor" => CLASE_SENSOR_ENERGIA_ACTIVA,
                "nombre_sensor" => $nombre_sensor,
                "campo" => CAMPO_COSTE,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                "intervalo_valores" => INTERVALO_VALORES_HORA,
                "tipo_mapa_calor" => TIPO_MAPA_CALOR_SEMANAL,
                "minutos_desfase_utc" => $minutos_desfase_utc,
                "tipo_informe" => $tipo_informe);
            $res_informacion_sensor_energia_activa_coste_horas = dame_informacion_sensor_energia(
                $parametros_informacion_sensor_energia_activa_coste_horas,
                $filas_valores_sensor_horas);

            // Datos del apartado (gráfica de costes y mapa de calor de costes)
            $datos_resumen_coste["hay_datos_grafica_costes_mapa_calor_costes"] = $res_informacion_sensor_energia_activa_coste_horas["hay_datos"];
            if ($datos_resumen_coste["hay_datos_grafica_costes_mapa_calor_costes"] == true)
            {
                $datos_resumen_coste["max_coste"] = $res_informacion_sensor_energia_activa_coste_horas["max_valor"];
                $datos_resumen_coste["etiquetas_grafica_costes"] = $res_informacion_sensor_energia_activa_coste_horas["etiquetas_graficas"];
                $datos_resumen_coste["grafica_costes"] = $res_informacion_sensor_energia_activa_coste_horas["grafica_valores"];
                $datos_resumen_coste["colores_mapa_calor_costes"] = $res_informacion_sensor_energia_activa_coste_horas["colores_mapa_calor_valores"];
                $datos_resumen_coste["dias_mapa_calor_costes"] = $res_informacion_sensor_energia_activa_coste_horas["dias_mapa_calor_valores"];
                $datos_resumen_coste["datos_mapa_calor_costes"] = $res_informacion_sensor_energia_activa_coste_horas["datos_mapa_calor_valores"];
                $datos_resumen_coste["unidad_medida_coste"] = $res_informacion_sensor_energia_activa_coste_horas["unidad_medida"];
            }

            // Información de los periodos
            $info_periodos = dame_info_periodos_fechas_inicio_fin_parametros_duracion_separacion_periodos(
                $cadena_fecha_hora_inicio_local_local,
                $cadena_fecha_hora_fin_local_local,
                NULL);
            $cadena_fecha_hora_inicio_anterior_local_local = $info_periodos["cadena_fecha_hora_inicio_anterior_local_local"];
            $cadena_fecha_hora_inicio_posterior_local_local = $info_periodos["cadena_fecha_hora_inicio_posterior_local_local"];
            $numero_dias_periodo = $info_periodos["numero_dias_periodo"];

            // Información de comparación de periodos (por días)
            $parametros_consumos_costes_sensor_periodos = array(
                "medicion" => $medicion,
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_ENERGIA_ACTIVA,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "fecha_hora_inicio_anterior" => $cadena_fecha_hora_inicio_anterior_local_local,
                "fecha_hora_inicio_posterior" => $cadena_fecha_hora_inicio_posterior_local_local,
                "numero_dias_periodo" => $numero_dias_periodo,
                "intervalo_valores" => INTERVALO_VALORES_DIA,
                "minutos_desfase_utc" => $minutos_desfase_utc);
            $res_consumos_costes_sensor_periodos = dame_consumos_costes_sensor_periodos($parametros_consumos_costes_sensor_periodos);

            // Datos del apartado (coste por periodos)
            $datos_resumen_coste["hay_datos_costes_periodos"] = (
                ($res_consumos_costes_sensor_periodos["hay_datos"] == true) &&
                ($res_consumos_costes_sensor_periodos["hay_datos_costes"] == true));
            if ($datos_resumen_coste["hay_datos_costes_periodos"] == true)
            {
                $datos_resumen_coste["max_coste_periodos"] = $res_consumos_costes_sensor_periodos["max_coste"];
                $datos_resumen_coste["etiquetas_costes_periodos"] = $res_consumos_costes_sensor_periodos["etiquetas"];
                $datos_resumen_coste["etiquetas_tooltips_costes_periodos"] = $res_consumos_costes_sensor_periodos["etiquetas_tooltips"];
                $datos_resumen_coste["min_fecha_coste_periodos"] = $res_consumos_costes_sensor_periodos["min_fecha_coste"];
                $datos_resumen_coste["max_fecha_coste_periodos"] = $res_consumos_costes_sensor_periodos["max_fecha_coste"];
                $datos_resumen_coste["grafica_costes_periodos"] = $res_consumos_costes_sensor_periodos["grafica_costes"];
            }
        }

        // Excesos de potencia
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA, $apartados) == true)
        {
            // Flag para recuperar la información de excesos de potencia
            $recuperar_datos_excesos_potencia = true;
            if ($recuperar_datos_excesos_potencia == true)
            {
                $recuperar_datos_excesos_potencia = ($id_tarifa != ID_NINGUNO);
            }
            if ($recuperar_datos_excesos_potencia == true)
            {
                $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
                $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($fila_tarifa_electrica["tipo"]);
                $tipo_calculo_coste_potencias = $caracteristicas_tipo_tarifa_electrica["tipo_calculo_coste_potencias"];
                $recuperar_datos_excesos_potencia = ($tipo_calculo_coste_potencias != TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS);
            }

            // Datos del apartado (excesos de potencia)
            $datos_excesos_potencia["hay_datos_excesos_potencia"] = $recuperar_datos_excesos_potencia;
            if ($datos_excesos_potencia["hay_datos_excesos_potencia"] == true)
            {
                // Información de energía activa (sobrepotencia por horas)
                $parametros_informacion_sensor_energia_activa_sobrepotencia_horas = array(
                    "id_ratio" => $id_ratio,
                    "id_sensor" => $id_sensor,
                    "clase_sensor" => CLASE_SENSOR_ENERGIA_ACTIVA,
                    "nombre_sensor" => $nombre_sensor,
                    "campo" => CAMPO_SOBREPOTENCIA,
                    "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                    "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                    "intervalo_valores" => INTERVALO_VALORES_HORA,
                    "tipo_mapa_calor" => TIPO_MAPA_CALOR_SEMANAL,
                    "minutos_desfase_utc" => $minutos_desfase_utc,
                    "tipo_informe" => $tipo_informe);
                $res_informacion_sensor_energia_activa_sobrepotencia_horas = dame_informacion_sensor_energia(
                    $parametros_informacion_sensor_energia_activa_sobrepotencia_horas, $filas_valores_sensor_horas);

                // Datos del apartado (mapa de calor de sobrepotencia)
                $datos_excesos_potencia["hay_datos_mapa_calor_sobrepotencia"] = $res_informacion_sensor_energia_activa_sobrepotencia_horas["hay_datos"];
                if ($datos_excesos_potencia["hay_datos_mapa_calor_sobrepotencia"] == true)
                {
                    $datos_excesos_potencia["colores_mapa_calor_sobrepotencia"] = $res_informacion_sensor_energia_activa_sobrepotencia_horas["colores_mapa_calor_valores"];
                    $datos_excesos_potencia["dias_mapa_calor_sobrepotencia"] = $res_informacion_sensor_energia_activa_sobrepotencia_horas["dias_mapa_calor_valores"];
                    $datos_excesos_potencia["datos_mapa_calor_sobrepotencia"] = $res_informacion_sensor_energia_activa_sobrepotencia_horas["datos_mapa_calor_valores"];
                }

                // Información de sobrepotencias (por cuartos de hora)
                $parametros_sobrepotencias_sensor = array(
                    "id_sensor" => $id_sensor,
                    "nombre_sensor" => $nombre_sensor,
                    "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                    "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                    "minutos_desfase_utc" => $minutos_desfase_utc,
                    "granularidad" => GRANULARIDAD_CUARTOHORARIA);
                $res_sobrepotencias_sensor = dame_sobrepotencias_sensor_electricidad_Espanya(
                    $parametros_sobrepotencias_sensor);

                // Datos del apartado (gráfica de sobrepotencias absolutas y tabla de sobrepotencias por tramo)
                $datos_excesos_potencia["hay_datos_grafica_sobrepotencias_absolutas_tabla_sobrepotencias_tramos"] = $res_sobrepotencias_sensor["hay_datos"];
                if ($datos_excesos_potencia["hay_datos_grafica_sobrepotencias_absolutas_tabla_sobrepotencias_tramos"] == true)
                {
                    $datos_excesos_potencia["min_sobrepotencia_absoluta"] = $res_sobrepotencias_sensor["min_sobrepotencia_absoluta"];
                    $datos_excesos_potencia["max_sobrepotencia_absoluta"] = $res_sobrepotencias_sensor["max_sobrepotencia_absoluta"];
                    $datos_excesos_potencia["grafica_sobrepotencias_absolutas"] = $res_sobrepotencias_sensor["grafica_sobrepotencias_absolutas"];
                    $datos_excesos_potencia["tabla_sobrepotencias_tramos"] = $res_sobrepotencias_sensor["tabla_sobrepotencias_tramos"];
                }
            }
        }

        // Excesos de energía reactiva
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA, $apartados) == true)
        {
            // Flag para recuperar la información de excesos de energía reactiva
            $recuperar_datos_excesos_energia_reactiva = true;
            if ($recuperar_datos_excesos_energia_reactiva == true)
            {
                $info_sensor_energia_reactiva = dame_info_sensor_energia_reactiva_asociado($id_sensor);
                $recuperar_datos_excesos_energia_reactiva = ($info_sensor_energia_reactiva !== NULL);
            }

            // Datos del apartado (excesos de energía reactiva)
            $datos_excesos_energia_reactiva["hay_datos_excesos_energia_reactiva"] = $recuperar_datos_excesos_energia_reactiva;
            if ($datos_excesos_energia_reactiva["hay_datos_excesos_energia_reactiva"] == true)
            {
                // Información de excesos de energía reactiva
                $parametros_excesos_energia_reactiva_sensor = array(
                    "id_sensor" => $info_sensor_energia_reactiva["id"],
                    "nombre_sensor" => $info_sensor_energia_reactiva["nombre"],
                    "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                    "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                    "minutos_desfase_utc" => $minutos_desfase_utc);
                $res_excesos_energia_reactiva_sensor = dame_excesos_energia_reactiva_sensor_electricidad_Espanya(
                    $parametros_excesos_energia_reactiva_sensor,
                    $filas_valores_sensor_horas);

                // Datos del apartado (gráfica de consumos de energía y tabla de costes de energía reactiva por tramos)
                $datos_excesos_energia_reactiva["hay_datos_graficas_consumos_energia_coseno_phi_tabla_energia_reactiva_tramos"] = $res_excesos_energia_reactiva_sensor["hay_datos"];
                if ($datos_excesos_energia_reactiva["hay_datos_graficas_consumos_energia_coseno_phi_tabla_energia_reactiva_tramos"] == true)
                {
                    $datos_excesos_energia_reactiva["max_consumo"] = $res_excesos_energia_reactiva_sensor["max_consumo"];
                    $datos_excesos_energia_reactiva["etiquetas_consumos_energia"] = $res_excesos_energia_reactiva_sensor["etiquetas_consumos_energia"];
                    $datos_excesos_energia_reactiva["grafica_consumos_energia"] = $res_excesos_energia_reactiva_sensor["grafica_consumos_energia"];
                    $datos_excesos_energia_reactiva["max_coseno_phi"] = $res_excesos_energia_reactiva_sensor["max_coseno_phi"];
                    $datos_excesos_energia_reactiva["min_coseno_phi"] = $res_excesos_energia_reactiva_sensor["min_coseno_phi"];
                    $datos_excesos_energia_reactiva["etiquetas_coseno_phi"] = $res_excesos_energia_reactiva_sensor["etiquetas_coseno_phi"];
                    $datos_excesos_energia_reactiva["grafica_coseno_phi"] = $res_excesos_energia_reactiva_sensor["grafica_coseno_phi"];
                    $datos_excesos_energia_reactiva["tabla_energia_reactiva_tramos"] = $res_excesos_energia_reactiva_sensor["tabla_energia_reactiva_tramos"];
                }
            }
        }

        // Cortes de tensión
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_CORTES_TENSION_ELECTRICIDAD_ESPANYA, $apartados) == true)
        {
            // Flag para recuperar la información de cortes de tensión
            $recuperar_datos_cortes_tension = true;
            if ($recuperar_datos_cortes_tension == true)
            {
                $info_sensor_cortes_tension = dame_info_sensor_cortes_tension_asociado($id_sensor);
                $recuperar_datos_cortes_tension = ($info_sensor_cortes_tension !== NULL);
            }

            // Datos del apartado (cortes de tensión)
            $datos_cortes_tension["hay_datos_cortes_tension"] = $recuperar_datos_cortes_tension;
            if ($datos_cortes_tension["hay_datos_cortes_tension"] == true)
            {
                // Información de cortes de tensión
                $parametros_cortes_tension_sensor = array(
                    "id_sensor" => $info_sensor_cortes_tension["id"],
                    "nombre_sensor" => $info_sensor_cortes_tension["nombre"],
                    "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                    "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                    "minutos_desfase_utc" => $minutos_desfase_utc);
                $res_cortes_tension_sensor = dame_cortes_tension_sensor_electricidad(
                    $parametros_cortes_tension_sensor,
                    $filas_valores_sensor_horas);

                // Datos del apartado (gráfica de consumos y cortes de tensión y tabla de cortes de tensión)
                $datos_cortes_tension["hay_datos_grafica_cortes_tension_consumos_tabla_cortes_tension"] = $res_cortes_tension_sensor["hay_datos"];
                if ($datos_cortes_tension["hay_datos_grafica_cortes_tension_consumos_tabla_cortes_tension"] == true)
                {
                    $datos_cortes_tension["grafica_consumos_cortes_tension"] = $res_cortes_tension_sensor["grafica_consumos_cortes_tension"];
                    $datos_cortes_tension["tabla_cortes_tension"] = $res_cortes_tension_sensor["tabla_cortes_tension"];
                    $datos_cortes_tension["etiquetas"] = $res_cortes_tension_sensor["etiquetas"];
                    $datos_cortes_tension["etiquetas_unidad"] = $res_cortes_tension_sensor["etiquetas_unidad"];
                    $datos_cortes_tension["unidades_medida"] = $res_cortes_tension_sensor["unidades_medida"];
                    $datos_cortes_tension["max_consumo"] = $res_cortes_tension_sensor["max_consumo"];
                }
            }
        }

        // Simulación de factura
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_ELECTRICIDAD_ESPANYA, $apartados) == true)
        {
            // Flag para recuperar la información de simulación de factura
            $recuperar_datos_simulacion_factura = true;
            if ($recuperar_datos_simulacion_factura == true)
            {
                $recuperar_datos_simulacion_factura = ($id_tarifa != ID_NINGUNO);
            }

            // Datos del apartado (simulación de factura)
            $datos_simulacion_factura["hay_datos_simulacion_factura"] = $recuperar_datos_simulacion_factura;
            if ($datos_simulacion_factura["hay_datos_simulacion_factura"] == true)
            {
                // Información de simulación de factura
                $parametros_simulacion_factura_sensor_tarifa = array(
                    "id_sensor" => $id_sensor,
                    "nombre_sensor" => $nombre_sensor,
                    "id_tarifa" => $id_tarifa,
                    "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                    "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local);
                $res_simulacion_factura_sensor_tarifa = dame_simulacion_factura_sensor_tarifa_electricidad_Espanya(
                    $parametros_simulacion_factura_sensor_tarifa);

                // Datos del apartado
                $datos_simulacion_factura["hay_datos"] = $res_simulacion_factura_sensor_tarifa["hay_datos"];
                if ($datos_simulacion_factura["hay_datos"] == true)
                {
                    $datos_simulacion_factura["tabla_coste_consumo"] = $res_simulacion_factura_sensor_tarifa["tabla_coste_consumo"];
                    $datos_simulacion_factura["tabla_energia_activa"] = $res_simulacion_factura_sensor_tarifa["tabla_energia_activa"];
                    $datos_simulacion_factura["tabla_energia_activa_consumidor_directo"] = $res_simulacion_factura_sensor_tarifa["tabla_energia_activa_directo"];
                    $datos_simulacion_factura["tabla_energia_activa_tarifa_acceso"] = $res_simulacion_factura_sensor_tarifa["tabla_energia_activa_tarifa_acceso"];
                    $datos_simulacion_factura["tabla_potencia"] = $res_simulacion_factura_sensor_tarifa["tabla_potencia"];
                    $datos_simulacion_factura["hay_datos_potencia_maxima_excesos_potencia"] = $res_simulacion_factura_sensor_tarifa["hay_datos_potencia_maxima_excesos_potencia"];
                    $datos_simulacion_factura["tabla_potencia_maxima_excesos_potencia"] = $res_simulacion_factura_sensor_tarifa["tabla_potencia_maxima_excesos_potencia"];
                    $datos_simulacion_factura["hay_datos_energia_reactiva"] = $res_simulacion_factura_sensor_tarifa["hay_datos_energia_reactiva"];
                    $datos_simulacion_factura["tabla_energia_reactiva"] = $res_simulacion_factura_sensor_tarifa["tabla_energia_reactiva"];
                    $datos_simulacion_factura["tabla_otros_conceptos"] = $res_simulacion_factura_sensor_tarifa["tabla_otros_conceptos"];
                }
            }
        }

        // Se devuelve el resultado
        $resultado = array(
            "res" => "OK",
            "datos_portada" => $datos_portada,
            "datos_instalacion" => $datos_instalacion,
            "datos_resumen_consumo" => $datos_resumen_consumo,
            "datos_resumen_coste" => $datos_resumen_coste,
            "datos_analisis_consumo" => $datos_analisis_consumo,
            "datos_analisis_coste" => $datos_analisis_coste,
            "datos_excesos_potencia" => $datos_excesos_potencia,
            "datos_excesos_energia_reactiva" => $datos_excesos_energia_reactiva,
            "datos_cortes_tension" => $datos_cortes_tension,
            "datos_simulacion_factura" => $datos_simulacion_factura);
        return ($resultado);
    }


    //
    // Funciones para los apartados de informes personalizados
    //


    // Crea los controles de texto para los apartados de estudio general
    function dame_controles_textos_estudio_general_electricidad_Espanya()
    {
        $idiomas = new Idiomas();

        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_TEXTO;
        $controles_textos = "
            <div id='textos-smartmeter-estudio-general' class='controles-textos-informe'>
                <div class='contenedor-texto-informe-sin-margen-superior'>
                    <span>".$idiomas->_('Texto de introducción').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(0"." / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea class='area-texto-informe' id='texto-introduccion-estudio-general' rows='1'></textarea>
                </div>
            </div>";
        return ($controles_textos);
    }


    // Devuelve la lista de apartados del informe de estudio general
    function dame_lista_apartados_estudio_general_electricidad_Espanya()
    {
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_ELECTRICIDAD_ESPANYA."' sort_id='01'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_ELECTRICIDAD_ESPANYA."' sort_id='02'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_ELECTRICIDAD_ESPANYA."' sort_id='03'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_ELECTRICIDAD_ESPANYA."' sort_id='04'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_CONSUMO_ELECTRICIDAD_ESPANYA."' sort_id='05'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_CONSUMO_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_ELECTRICIDAD_ESPANYA."' sort_id='06'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_COSTE_ELECTRICIDAD_ESPANYA."' sort_id='07'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_COSTE_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA."' sort_id='08'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA."' sort_id='09'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_CORTES_TENSION_ELECTRICIDAD_ESPANYA."' sort_id='10'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_CORTES_TENSION_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_ELECTRICIDAD_ESPANYA."' sort_id='11'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_ELECTRICIDAD_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_ELECTRICIDAD_ESPANYA."' sort_id='12'>".dame_descripcion_apartado_estudio_general_electricidad_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_ELECTRICIDAD_ESPANYA)."</option>";

        return ($lista_apartados);
    }


    // Devuelve la descripción de los apartados del informe de estudio general
    function dame_descripcion_apartado_estudio_general_electricidad_Espanya($apartado)
    {
        switch ($apartado)
        {
            case APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Portada";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Introducción";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Instalación";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Análisis de consumo";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_CONSUMO_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Resumen de consumo";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Análisis de coste";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_COSTE_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Resumen de coste";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Excesos de potencia";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Excesos de energía reactiva";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_CORTES_TENSION_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Cortes de tensión";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Simulación de factura";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_ELECTRICIDAD_ESPANYA:
            {
                $descripcion = "Conclusiones";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_(htmlspecialchars($descripcion, ENT_QUOTES)));
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_estudio_general_electricidad_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-estudio-general'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-estudio-general' hidden>
                        <div id='apartado_portada_estudio_general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-portada-estudio-general' class='titulo-informe'>".$idiomas->_("Portada")."</div>
                            </div>
                            <div id='nombre-red-portada-estudio-general' class='texto-grande-portada-informe'></div>
                            <div id='descripcion-sensor-portada-estudio-general' class='texto-mediano-portada-informe'></div>
                            <div id='fechas-portada-estudio-general' class='texto-pequenyo-portada-informe'></div>
                        </div>

                        <div id='apartado-introduccion-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-introduccion-estudio-general' class='titulo-informe'>".$idiomas->_("Introducción")."</div>
                            </div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Introducción').": "."</span><br/>
                                <textarea readonly class='area-texto-informe' id='introduccion-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-instalacion-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-instalacion-estudio-general' class='titulo-informe'>".$idiomas->_("Instalación")."</div>
                            </div>
                            <table class='tabla-parametros'>
                                <tbody>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("CUPS").":"."</b></td>
                                        <td style='width:85%' id='cups-instalacion-estudio-general'></td>
                                    </tr>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("Descripción").":"."</b></td>
                                        <td style='width:85%' id='descripcion-instalacion-estudio-general'></td>
                                    </tr>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("Tipo").":"."</b></td>
                                        <td style='width:85%' id='tipo-instalacion-estudio-general'></td>
                                    </tr>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("Contrato").":"."</b></td>
                                        <td style='width:85%' id='contrato-instalacion-estudio-general'></td>
                                    </tr>
                                    <tr>
                                        <td style='width:15%'><b id='titulo-formula-precio-consumo-instalacion-estudio-general'>".$idiomas->_("Fórmula de precio de consumo").":"."</b></td>
                                        <td style='width:85%' id='formula-precio-consumo-instalacion-estudio-general' class='elemento-oculto' ></td>
                                    </tr>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("Fecha de inicio").":"."</b></td>
                                        <td style='width:85%' id='fecha-inicio-instalacion-estudio-general'></td>
                                    </tr>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("Fecha de fin").":"."</b></td>
                                        <td style='width:85%' id='fecha-fin-instalacion-estudio-general'>/td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class='tabla-datos100' id='contenedor-tabla-tramos-tarifa-electrica-instalacion-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-instalacion-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-analisis-consumo-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-analisis-consumo-estudio-general' class='titulo-informe'>".$idiomas->_("Análisis de consumo")."</div>
                            </div>
                            <div class='grafica100' id='grafica-consumos-analisis-consumo-estudio-general'></div>
                            <div class='grafica100' id='grafica-consumos-periodos-analisis-consumo-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-evolucion-consumos-tramos-analisis-consumo-estudio-general'></div>
                            <div class='grafica100' id='grafica-consumos-tramos-diarios-analisis-consumo-estudio-general'></div>
                            <div class='mapa-calor100' id='mapa-calor-semanal-consumos-analisis-consumo-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-consumos-tramos-analisis-consumo-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-analisis-consumo-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-resumen-consumo-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-resumen-consumo-estudio-general' class='titulo-informe'>".$idiomas->_("Resumen de consumo")."</div>
                            </div>
                            <div class='grafica100' id='grafica-consumos-resumen-consumo-estudio-general'></div>
                            <div class='grafica100' id='grafica-consumos-periodos-resumen-consumo-estudio-general'></div>
                            <div class='mapa-calor100' id='mapa-calor-semanal-consumos-resumen-consumo-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-resumen-consumo-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-analisis-coste-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-analisis-coste-estudio-general' class='titulo-informe'>".$idiomas->_("Análisis de coste")."</div>
                            </div>
                            <div class='grafica100' id='grafica-costes-analisis-coste-estudio-general'></div>
                            <div class='grafica100' id='grafica-costes-periodos-analisis-coste-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-evolucion-consumos-costes-analisis-coste-estudio-general'></div>
                            <div class='grafica100' id='grafica-costes-tramos-diarios-analisis-coste-estudio-general'></div>
                            <div class='mapa-calor100' id='mapa-calor-semanal-costes-analisis-coste-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-costes-tramos-analisis-coste-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-analisis-coste-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-resumen-coste-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-resumen-coste-estudio-general' class='titulo-informe'>".$idiomas->_("Resumen de coste")."</div>
                            </div>
                            <div class='grafica100' id='grafica-costes-resumen-coste-estudio-general'></div>
                            <div class='grafica100' id='grafica-costes-periodos-resumen-coste-estudio-general'></div>
                            <div class='mapa-calor100' id='mapa-calor-semanal-costes-resumen-coste-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-resumen-coste-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-excesos-potencia-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-excesos-potencia-estudio-general' class='titulo-informe'>".$idiomas->_("Excesos de potencia")."</div>
                            </div>
                            <div class='grafica100' id='grafica-sobrepotencias-absolutas-excesos-potencia-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-sobrepotencias-tramos-excesos-potencia-estudio-general'></div>
                            <div class='mapa-calor100' id='mapa-calor-semanal-sobrepotencias-excesos-potencia-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-excesos-potencia-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-excesos-energia-reactiva-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-excesos-energia-reactiva-estudio-general' class='titulo-informe'>".$idiomas->_("Excesos de energía reactiva")."</div>
                            </div>
                            <div class='grafica100' id='grafica-consumos-energia-excesos-energia-reactiva-estudio-general'></div>
                            <div class='grafica100' id='grafica-coseno-phi-excesos-energia-reactiva-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-excesos-energia-reactiva-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-cortes-tension-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-cortes-tension-estudio-general' class='titulo-informe'>".$idiomas->_("Cortes de tensión")."</div>
                            </div>
                            <div class='grafica100' id='grafica-cortes-tension-consumos-cortes-tension-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-cortes-tension-cortes-tension-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-cortes-tension-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-simulacion-factura-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-simulacion-factura-estudio-general' class='titulo-informe'>".$idiomas->_("Simulación de factura")."</div>
                            </div>
                            <div class='titulo-tabla-datos100' id='titulo-resumen-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-coste-consumo-simulacion-factura-estudio-general'></div>
                            <div class='titulo-tabla-datos100' id='titulo-detalles-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-energia-activa-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-energia-activa-consumidor-directo-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-energia-activa-tarifa-acceso-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-potencia-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-potencia-maxima-excesos-potencia-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-energia-reactiva-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-otros-conceptos-simulacion-factura-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-simulacion-factura-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-conclusiones-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-conclusiones-estudio-general' class='titulo-informe'>".$idiomas->_("Conclusiones")."</div>
                            </div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Conclusiones').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='conclusiones-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-avisos-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-avisos-estudio-general' class='titulo-informe'>".$idiomas->_("Avisos")."</div>
                            </div>
                            <div class='texto100' id='texto-avisos-estudio-general'></div>
                        </div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Portada
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-portada'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='texto-grande-portada-informe-informe-fichero-con-salto-pagina' id='nombre-red-portada-estudio-general'></div>
                        <div class='texto-mediano-portada-informe-informe-fichero' id='descripcion-sensor-portada-estudio-general'></div>
                        <div class='texto-pequenyo-portada-informe-informe-fichero' id='fechas-portada-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Introducción
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-introduccion-informe-fichero-estudio-general'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-introduccion'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='introduccion-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Instalación
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-instalacion'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-instalacion'></div>
                        <table class='tabla-parametros-informe-fichero'>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("CUPS").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='cups-instalacion-estudio-general'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Descripción").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='descripcion-instalacion-estudio-general'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='tipo-instalacion-estudio-general'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Contrato").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='contrato-instalacion-estudio-general'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero' id='titulo-formula-precio-consumo-instalacion-estudio-general'><b>".$idiomas->_("Fórmula de precio de consumo").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='formula-precio-consumo-instalacion-estudio-general'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='fecha-inicio-instalacion-estudio-general'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='fecha-fin-instalacion-estudio-general'></td>
                            </tr>
                        </table>
                        <br/>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-tramos-tarifa-electrica-instalacion-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-instalacion-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Páginas de análisis de consumo

                // Página de análisis de consumo (1)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-analisis-consumo-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-analisis-consumo'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-analisis-consumo-estudio-general'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-periodos-analisis-consumo-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-evolucion-consumos-tramos-analisis-consumo-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de análisis de consumo (2)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-analisis-consumo-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='grafica100-informe-fichero' separacion-superior-elementos-informe-fichero id='grafica-consumos-tramos-diarios-analisis-consumo-estudio-general'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-semanal-consumos-analisis-consumo-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-consumos-tramos-analisis-consumo-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-analisis-consumo-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de resumen de consumo
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-resumen-consumo'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-resumen-consumo'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-resumen-consumo-estudio-general'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-periodos-resumen-consumo-estudio-general'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-semanal-consumos-resumen-consumo-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-resumen-consumo-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Páginas de análisis de coste

                // Página de análisis de coste (1)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-analisis-coste-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-analisis-coste'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-analisis-coste-estudio-general'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-periodos-analisis-coste-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-evolucion-consumos-costes-analisis-coste-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de análisis de coste (2)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-analisis-coste-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-tramos-diarios-analisis-coste-estudio-general'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-semanal-costes-analisis-coste-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-costes-tramos-analisis-coste-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-analisis-coste-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de resumen de coste
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-resumen-coste'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-resumen-coste'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-resumen-coste-estudio-general'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-periodos-resumen-coste-estudio-general'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-semanal-costes-resumen-coste-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-resumen-coste-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de excesos de potencia
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-excesos-potencia'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-excesos-potencia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-sobrepotencias-absolutas-excesos-potencia-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-sobrepotencias-tramos-excesos-potencia-estudio-general'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-semanal-sobrepotencias-excesos-potencia-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-excesos-potencia-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de excesos de energía reactiva
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-excesos-energia-reactiva'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-excesos-energia-reactiva'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-energia-excesos-energia-reactiva-estudio-general'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-coseno-phi-excesos-energia-reactiva-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-excesos-energia-reactiva-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de cortes de tensión
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-cortes-tension'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-cortes-tension'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-cortes-tension-consumos-cortes-tension-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-cortes-tension-cortes-tension-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-cortes-tension-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de simulación de factura
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-simulacion-factura'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-simulacion-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-resumen-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-coste-consumo-simulacion-factura-estudio-general'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-detalles-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-activa-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-activa-consumidor-directo-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-activa-tarifa-acceso-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-potencia-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-potencia-maxima-excesos-potencia-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-reactiva-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-otros-conceptos-simulacion-factura-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-simulacion-factura-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de conclusiones
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-conclusiones'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-conclusiones'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='conclusiones-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de avisos
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-avisos'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-avisos'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-avisos-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }
?>
