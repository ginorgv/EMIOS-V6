//
// Funciones para el dibujado de los informes de análisis (Sensores)
//


// Dibujado del informe de análisis horario de sensores
function dibuja_informe_sensores_analisis_horario(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var etiquetas_grafica_valores = datos.etiquetas_grafica_valores;
    var grafica_valores = datos.grafica_valores;
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var clase_procesado_valores = datos.clase_procesado_valores;
    var dias_mapa_calor_valores = datos.dias_mapa_calor_valores;
    var datos_mapa_calor_valores = datos.datos_mapa_calor_valores;
    var etiquetas_porcentajes_valores = datos.etiquetas_porcentajes_valores;
    var grafica_porcentajes_valores = datos.grafica_porcentajes_valores;
    var max_porcentaje_valores = datos.max_porcentaje_valores;
    var grafica_lorenz_valores = datos.grafica_lorenz_valores;
    var tabla_percentiles_valores = datos.tabla_percentiles_valores;
    var unidad_medida = datos.unidad_medida;

    // Recuperación de datos del resultado (medias de valores)
    var grafica_medias_valores = datos.grafica_medias_valores;
    var min_media_valores = datos.min_media_valores;
    var max_media_valores = datos.max_media_valores;
    var banda_valores = datos.banda_valores;
    var grafica_coeficientes_variacion_valores = datos.grafica_coeficientes_variacion_valores;
    var min_coeficiente_variacion_valores = datos.min_coeficiente_variacion_valores;
    var max_coeficiente_variacion_valores = datos.max_coeficiente_variacion_valores;

    // Parámetros
    var clase_sensor = parametros.clase_sensor;
    var campo = parametros.campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_valores = parametros.id_grafica_valores;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_valores = parametros.id_mapa_calor_valores;
    var altura_maxima_mapa_calor_valores = parametros.altura_maxima_mapa_calor_valores;
    var id_grafica_medias_valores = parametros.id_grafica_medias_valores;
    var id_grafica_coeficientes_variacion_valores = parametros.id_grafica_coeficientes_variacion_valores;
    var id_grafica_lorenz_valores = parametros.id_grafica_lorenz_valores;
    var id_contenedor_tabla_percentiles_valores = parametros.id_contenedor_tabla_percentiles_valores;
    var id_grafica_porcentajes_valores = parametros.id_grafica_porcentajes_valores;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_analisis(
        TIPO_INFORME_SENSORES_ANALISIS_HORARIO,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_ANALISIS_HORARIO,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_mapa_calor_valores = parametros.mostrar_mapa_calor_valores;
    var mostrar_grafica_medias_valores = parametros.mostrar_grafica_medias_valores;
    var mostrar_grafica_coeficientes_variacion_valores = parametros.mostrar_grafica_coeficientes_variacion_valores;
    var mostrar_grafica_lorenz_valores = parametros.mostrar_grafica_lorenz_valores;
    var mostrar_tabla_percentiles_valores = parametros.mostrar_tabla_percentiles_valores;
    var mostrar_grafica_porcentajes_valores = parametros.mostrar_grafica_porcentajes_valores;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_mapa_calor_valores == true) {
        muestra_elemento(id_mapa_calor_valores);
    }
    if (mostrar_grafica_medias_valores == true) {
        muestra_elemento(id_grafica_medias_valores);
    }
    if (mostrar_grafica_coeficientes_variacion_valores == true) {
        muestra_elemento(id_grafica_coeficientes_variacion_valores);
    }
    if (mostrar_grafica_lorenz_valores == true) {
        muestra_elemento(id_grafica_lorenz_valores);
    }
    if (mostrar_tabla_percentiles_valores == true) {
        muestra_elemento(id_contenedor_tabla_percentiles_valores);
    }
    if (mostrar_grafica_porcentajes_valores == true) {
        muestra_elemento(id_grafica_porcentajes_valores);
    }

    // Flags según el tipo de informe
    var mostrar_animaciones = null;
    var anyadir_menus_contextuales = null;
    switch (tipo_informe) {
        case TIPO_INFORME_WEB_EMIOS: {
            mostrar_animaciones = true;
            anyadir_menus_contextuales = true;
            break;
        }
        case TIPO_INFORME_FICHERO: {
            mostrar_animaciones = false;
            anyadir_menus_contextuales = false;
            break;
        }
    }

    // Mostrar líneas de valores sólo si la clase tiene procesado (para mostrar las separaciones si no hay valores o sólo puntos)
    var mostrar_lineas_valores = (clase_procesado_valores == true);

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        var titulo_grafica = TLNT.Idiomas._("Valores por hora");
        if (unidad_medida != "") {
            titulo_grafica += " (" + unidad_medida + ")";
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(clase_sensor, campo);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica,
            etiquetas_grafica_valores,
            grafica_valores, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_valor, true,
            max_valor, true,
            2, unidad_medida,
            lineas_referencia,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor de valores
    if (mostrar_mapa_calor_valores == true) {
        var titulo_mapa_calor_valores = null;
        switch (tipo_mapa_calor) {
            case TIPO_MAPA_CALOR_DIARIO: {
                titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor diario de valores");
                break;
            }
            case TIPO_MAPA_CALOR_SEMANAL: {
                titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor semanal de valores");
                break;
            }
        }
        if (unidad_medida != "") {
            titulo_mapa_calor_valores += " (" + unidad_medida + ")";
        }
        muestra_grafico_mapa_calor(
            id_mapa_calor_valores,
            tipo_mapa_calor,
            titulo_mapa_calor_valores,
            dias_mapa_calor_valores,
            null,
            datos_mapa_calor_valores,
            null,
            null,
            true,
            ESCALA_COLORES_VERDE_ROJO,
            altura_maxima_mapa_calor_valores,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de medias de valores
    if (mostrar_grafica_medias_valores == true) {
        var titulo_grafica = TLNT.Idiomas._("Medias de valores por hora del día");
        if (unidad_medida != "") {
            titulo_grafica += " (" + unidad_medida + ")";
        }
        muestra_grafica_puntual_lineas_valores(
            id_grafica_medias_valores,
            titulo_grafica,
            null,
            grafica_medias_valores, banda_valores,
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23], TLNT.Idiomas._("H"), false,
            min_media_valores, true,
            max_media_valores, true,
            2, unidad_medida,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de coeficientes de variación
    if (mostrar_grafica_coeficientes_variacion_valores == true) {
        muestra_grafica_puntual_lineas_valores(
            id_grafica_coeficientes_variacion_valores,
            TLNT.Idiomas._("Coeficientes de variación de valores por hora del día"),
            null,
            grafica_coeficientes_variacion_valores, null,
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23], TLNT.Idiomas._("H"), false,
            min_coeficiente_variacion_valores, true,
            max_coeficiente_variacion_valores, true,
            2, "",
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Curva de Lorenz de valores
    if (mostrar_grafica_lorenz_valores == true) {
        muestra_grafica_curva_lorenz(
            id_grafica_lorenz_valores,
            TLNT.Idiomas._("Curva de Lorenz de valores"),
            TLNT.Idiomas._("% de horas"),
            TLNT.Idiomas._("% de valores"),
            grafica_lorenz_valores,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de percentiles de valores
    if (mostrar_tabla_percentiles_valores == true) {
        $("#" + id_contenedor_tabla_percentiles_valores).html(tabla_percentiles_valores);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_percentiles_valores = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_percentiles_valores);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_percentiles_valores, info_menu_contextual, TLNT.Idiomas._('Percentiles de valores'));
        }
    }

    // Gráfica de porcentajes de valores por hora en el día
    if (mostrar_grafica_porcentajes_valores == true) {
        muestra_grafica_puntual_barras_etiquetas_valores(
            id_grafica_porcentajes_valores,
            TLNT.Idiomas._("Porcentajes de valores por hora del día") + " (" + "%" + ")",
            etiquetas_porcentajes_valores,
            [grafica_porcentajes_valores],
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
            max_porcentaje_valores, true,
            2, "%", false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }
}


// Dibujado del informe de análisis diario de sensores
function dibuja_informe_sensores_analisis_diario(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var etiquetas_grafica_valores = datos.etiquetas_grafica_valores;
    var grafica_valores = datos.grafica_valores;
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var clase_procesado_valores = datos.clase_procesado_valores;
    var dias_mapa_calor_valores = datos.dias_mapa_calor_valores;
    var datos_mapa_calor_valores = datos.datos_mapa_calor_valores;
    var campo_incremental = datos.campo_incremental;
    var etiquetas_grafica_valores_medias_maximos_minimos = datos.etiquetas_grafica_valores_medias_maximos_minimos;
    var grafica_sumas_valores = datos.grafica_sumas_valores;
    var max_suma_valores = datos.max_suma_valores;
    var grafica_valores_medias_maximos_minimos = datos.grafica_valores_medias_maximos_minimos;
    var tabla_maximos_minimos_medias_medidas = datos.tabla_maximos_minimos_medias_medidas;
    var tabla_valores_dia = datos.tabla_valores_dia;
    var unidad_medida = datos.unidad_medida;

    // Recuperación de datos del resultado (medias de valores)
    var grafica_medias_valores = datos.grafica_medias_valores;
    var min_media_valores = datos.min_media_valores;
    var max_media_valores = datos.max_media_valores;
    var banda_valores = datos.banda_valores;
    var grafica_coeficientes_variacion_valores = datos.grafica_coeficientes_variacion_valores;
    var min_coeficiente_variacion_valores = datos.min_coeficiente_variacion_valores;
    var max_coeficiente_variacion_valores = datos.max_coeficiente_variacion_valores;

    // Parámetros
    var clase_sensor = parametros.clase_sensor;
    var campo = parametros.campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_valores = parametros.id_grafica_valores;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_valores = parametros.id_mapa_calor_valores;
    var altura_maxima_mapa_calor_valores = parametros.altura_maxima_mapa_calor_valores;
    var id_grafica_medias_valores = parametros.id_grafica_medias_valores;
    var id_grafica_coeficientes_variacion_valores = parametros.id_grafica_coeficientes_variacion_valores;
    var id_grafica_sumas_valores = parametros.id_grafica_sumas_valores;
    var id_grafica_valores_medias_maximos_minimos = parametros.id_grafica_valores_medias_maximos_minimos;
    var id_contenedor_tabla_maximos_minimos_medias_medidas = parametros.id_contenedor_tabla_maximos_minimos_medias_medidas;
    var id_contenedor_tabla_valores_dia = parametros.id_contenedor_tabla_valores_dia;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_analisis(
        TIPO_INFORME_SENSORES_ANALISIS_DIARIO,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_ANALISIS_DIARIO,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_mapa_calor_valores = parametros.mostrar_mapa_calor_valores;
    var mostrar_grafica_medias_valores = parametros.mostrar_grafica_medias_valores;
    var mostrar_grafica_coeficientes_variacion_valores = parametros.mostrar_grafica_coeficientes_variacion_valores;
    var mostrar_grafica_sumas_valores = parametros.mostrar_grafica_sumas_valores;
    var mostrar_grafica_valores_medias_maximos_minimos = parametros.mostrar_grafica_valores_medias_maximos_minimos;
    var mostrar_tabla_maximos_minimos_medias_medidas = parametros.mostrar_tabla_maximos_minimos_medias_medidas;
    var mostrar_tabla_valores_dia = parametros.mostrar_tabla_valores_dia;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_mapa_calor_valores == true) {
        muestra_elemento(id_mapa_calor_valores);
    }
    if (mostrar_grafica_medias_valores == true) {
        muestra_elemento(id_grafica_medias_valores);
    }
    if (mostrar_grafica_coeficientes_variacion_valores == true) {
        muestra_elemento(id_grafica_coeficientes_variacion_valores);
    }
    if (mostrar_grafica_sumas_valores == true) {
        muestra_elemento(id_grafica_sumas_valores);
    }
    if (mostrar_grafica_valores_medias_maximos_minimos == true) {
        muestra_elemento(id_grafica_valores_medias_maximos_minimos);
    }
    if (mostrar_tabla_maximos_minimos_medias_medidas == true) {
        muestra_elemento(id_contenedor_tabla_maximos_minimos_medias_medidas);
    }
    if (mostrar_tabla_valores_dia == true) {
        muestra_elemento(id_contenedor_tabla_valores_dia);
    }

    // Flags según el tipo de informe
    var mostrar_animaciones = null;
    var anyadir_menus_contextuales = null;
    switch (tipo_informe) {
        case TIPO_INFORME_WEB_EMIOS: {
            mostrar_animaciones = true;
            anyadir_menus_contextuales = true;
            break;
        }
        case TIPO_INFORME_FICHERO: {
            mostrar_animaciones = false;
            anyadir_menus_contextuales = false;
            break;
        }
    }

    // Mostrar líneas de valores sólo si la clase tiene procesado (para mostrar las separaciones si no hay valores o sólo puntos)
    var mostrar_lineas_valores = (clase_procesado_valores == true);

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        var titulo_grafica = TLNT.Idiomas._("Valores por hora");
        if (unidad_medida != "") {
            titulo_grafica += " (" + unidad_medida + ")";
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(clase_sensor, campo);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica,
            etiquetas_grafica_valores,
            grafica_valores, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_valor, true,
            max_valor, true,
            2, unidad_medida,
            lineas_referencia,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor de valores
    if (mostrar_mapa_calor_valores == true) {
        var titulo_mapa_calor_valores = null;
        switch (tipo_mapa_calor) {
            case TIPO_MAPA_CALOR_DIARIO: {
                titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor diario de valores");
                break;
            }
            case TIPO_MAPA_CALOR_SEMANAL: {
                titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor semanal de valores");
                break;
            }
        }
        if (unidad_medida != "") {
            titulo_mapa_calor_valores += " (" + unidad_medida + ")";
        }
        muestra_grafico_mapa_calor(
            id_mapa_calor_valores,
            tipo_mapa_calor,
            titulo_mapa_calor_valores,
            dias_mapa_calor_valores,
            null,
            datos_mapa_calor_valores,
            null,
            null,
            true,
            ESCALA_COLORES_VERDE_ROJO,
            altura_maxima_mapa_calor_valores,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Ticks de los días de la semana
    var nombres_dias_semana = dame_nombres_dias_semana();
    var ticks_dias_semana = [
        [1, nombres_dias_semana[0]],
        [2, nombres_dias_semana[1]],
        [3, nombres_dias_semana[2]],
        [4, nombres_dias_semana[3]],
        [5, nombres_dias_semana[4]],
        [6, nombres_dias_semana[5]],
        [7, nombres_dias_semana[6]]];

    // Gráfica de medias de valores
    if (mostrar_grafica_medias_valores == true) {
        var titulo_grafica = TLNT.Idiomas._("Medias de valores por día de la semana");
        if (unidad_medida != "") {
            titulo_grafica += " (" + unidad_medida + ")";
        }
        muestra_grafica_puntual_lineas_valores(
            id_grafica_medias_valores,
            titulo_grafica,
            null,
            grafica_medias_valores, banda_valores,
            ticks_dias_semana, "", true,
            min_media_valores, true,
            max_media_valores, true,
            2, unidad_medida,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de coeficientes de variación
    if (mostrar_grafica_coeficientes_variacion_valores == true) {
        muestra_grafica_puntual_lineas_valores(
            id_grafica_coeficientes_variacion_valores,
            TLNT.Idiomas._("Coeficientes de variación de valores por día de la semana"),
            null,
            grafica_coeficientes_variacion_valores, null,
            ticks_dias_semana, "", true,
            min_coeficiente_variacion_valores, true,
            max_coeficiente_variacion_valores, true,
            2, "",
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de sumas de valores
    if (mostrar_grafica_sumas_valores == true) {
        if (campo_incremental == true) {
            // Se dibuja la gráfica
            titulo_grafica = TLNT.Idiomas._("Totales de valores diarios");
            if (unidad_medida != "") {
                titulo_grafica += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_sumas_valores,
                null,
                titulo_grafica,
                null,
                grafica_sumas_valores, null, INTERVALO_VALORES_DIA,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                max_suma_valores, true,
                2, unidad_medida,
                null,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                true,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_sumas_valores);
            }
            else {
                cambia_clase_elemento(id_grafica_sumas_valores, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_sumas_valores).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de totales de valores diarios (el campo es de tipo puntual)"));
            }
        }
    }

    // Gráfica de medias de valores, valores máximos y minimos
    if (mostrar_grafica_valores_medias_maximos_minimos == true) {
        titulo_grafica = TLNT.Idiomas._("Medias de valores, valores máximos y mínimos por hora diarios");
        if (unidad_medida != "") {
            titulo_grafica += " (" + unidad_medida + ")";
        }
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores_medias_maximos_minimos,
            null,
            titulo_grafica,
            etiquetas_grafica_valores_medias_maximos_minimos,
            grafica_valores_medias_maximos_minimos, null, INTERVALO_VALORES_DIA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_valor, true,
            max_valor, true,
            2, unidad_medida,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            true,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de máximos, mínimos y medias de medidas
    if (mostrar_tabla_maximos_minimos_medias_medidas == true) {
        $("#" + id_contenedor_tabla_maximos_minimos_medias_medidas).html(tabla_maximos_minimos_medias_medidas);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_maximos_minimos_medias_medidas = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_maximos_minimos_medias_medidas);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_maximos_minimos_medias_medidas, info_menu_contextual, TLNT.Idiomas._("Máximos, mínimos y medias de medidas diarias"));
        }
    }

    // Tabla de valores por día
    if (mostrar_tabla_valores_dia == true) {
        $("#" + id_contenedor_tabla_valores_dia).html(tabla_valores_dia);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_valores_dia = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_valores_dia);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_valores_dia, info_menu_contextual, TLNT.Idiomas._('Valores diarios'));
        }
    }
}


// Dibujado del informe de análisis de comportamiento de sensores
function dibuja_informe_sensores_analisis_comportamiento(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var etiquetas_graficas_valores = datos.etiquetas_graficas_valores;
    var grafica_coeficientes_estabilidad_valores = datos.grafica_coeficientes_estabilidad_valores;
    var grafica_amplitudes_valores = datos.grafica_amplitudes_valores;
    var grafica_alturas_relativas_valores_maximos = datos.grafica_alturas_relativas_valores_maximos;
    var max_amplitud_valores = datos.max_amplitud_valores;
    var max_altura_relativa_valores_maximos = datos.max_altura_relativa_valores_maximos;
    var texto_explicacion_coeficiente_estabilidad = datos.texto_explicacion_coeficiente_estabilidad;
    var texto_explicacion_amplitud = datos.texto_explicacion_amplitud;
    var texto_explicacion_altura_relativa_maxima = datos.texto_explicacion_altura_relativa_maxima;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var id_grafica_coeficientes_estabilidad_valores = parametros.id_grafica_coeficientes_estabilidad_valores;
    var id_texto_explicacion_coeficiente_estabilidad = parametros.id_texto_explicacion_coeficiente_estabilidad;
    var id_grafica_amplitudes_valores = parametros.id_grafica_amplitudes_valores;
    var id_texto_explicacion_amplitud = parametros.id_texto_explicacion_amplitud;
    var id_grafica_alturas_relativas_valores_maximos = parametros.id_grafica_alturas_relativas_valores_maximos;
    var id_texto_explicacion_altura_relativa_maxima = parametros.id_texto_explicacion_altura_relativa_maxima;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_analisis(
        TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_coeficientes_estabilidad_valores = parametros.mostrar_grafica_coeficientes_estabilidad_valores;
    var mostrar_texto_explicacion_coeficiente_estabilidad = parametros.mostrar_texto_explicacion_coeficiente_estabilidad;
    var mostrar_grafica_amplitudes_valores = parametros.mostrar_grafica_amplitudes_valores;
    var mostrar_texto_explicacion_amplitud = parametros.mostrar_texto_explicacion_amplitud;
    var mostrar_grafica_alturas_relativas_valores_maximos = parametros.mostrar_grafica_alturas_relativas_valores_maximos;
    var mostrar_texto_explicacion_altura_relativa_maxima = parametros.mostrar_texto_explicacion_altura_relativa_maxima;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_coeficientes_estabilidad_valores == true) {
        muestra_elemento(id_grafica_coeficientes_estabilidad_valores);
    }
    if (mostrar_texto_explicacion_coeficiente_estabilidad == true) {
        muestra_elemento(id_texto_explicacion_coeficiente_estabilidad);
    }
    if (mostrar_grafica_amplitudes_valores == true) {
        muestra_elemento(id_grafica_amplitudes_valores);
    }
    if (mostrar_texto_explicacion_amplitud == true) {
        muestra_elemento(id_texto_explicacion_amplitud);
    }
    if (mostrar_grafica_alturas_relativas_valores_maximos == true) {
        muestra_elemento(id_grafica_alturas_relativas_valores_maximos);
    }
    if (mostrar_texto_explicacion_altura_relativa_maxima == true) {
        muestra_elemento(id_texto_explicacion_altura_relativa_maxima);
    }

    // Flags según el tipo de informe
    var mostrar_animaciones = null;
    var anyadir_menus_contextuales = null;
    switch (tipo_informe) {
        case TIPO_INFORME_WEB_EMIOS: {
            mostrar_animaciones = true;
            anyadir_menus_contextuales = true;
            break;
        }
        case TIPO_INFORME_FICHERO: {
            mostrar_animaciones = false;
            anyadir_menus_contextuales = false;
            break;
        }
    }

    // Mostrado de valores en barras de valores
    var mostrar_valores_barras = (etiquetas_graficas_valores.length <= NUMERO_MAXIMO_BARRAS_VALORES_VALOR_VISIBLE_GRAFICA_100);

    // Gráfica de coeficientes de estabilidad de valores
    if (mostrar_grafica_coeficientes_estabilidad_valores == true) {
        muestra_grafica_puntual_barras_valores(
            id_grafica_coeficientes_estabilidad_valores,
            TLNT.Idiomas._("Coeficientes de estabilidad de valores") + " (" + "%" + ")",
            etiquetas_graficas_valores,
            grafica_coeficientes_estabilidad_valores,
            [TLNT.Idiomas._("Sensores")], null,
            100, false,
            2, "%",
            false, mostrar_valores_barras,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Texto de explicación de coeficiente de estabilidad
    if (mostrar_texto_explicacion_coeficiente_estabilidad == true) {
        $("#" + id_texto_explicacion_coeficiente_estabilidad).html(texto_explicacion_coeficiente_estabilidad);
    }

    // Gráfica de amplitudes de valores
    if (mostrar_grafica_amplitudes_valores == true) {
        var titulo_grafica = TLNT.Idiomas._("Amplitudes máximas de valores");
        if (unidad_medida != "") {
            titulo_grafica += " (" + unidad_medida + ")";
        }
        muestra_grafica_puntual_barras_valores(
            id_grafica_amplitudes_valores,
            titulo_grafica,
            etiquetas_graficas_valores,
            grafica_amplitudes_valores,
            [TLNT.Idiomas._("Sensores")], null,
            max_amplitud_valores, mostrar_valores_barras,
            2, unidad_medida,
            false, true,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Texto de explicación de amplitud
    if (mostrar_texto_explicacion_amplitud == true) {
        $("#" + id_texto_explicacion_amplitud).html(texto_explicacion_amplitud);
    }

    // Gráfica de alturas relativas de valores máximos
    if (mostrar_grafica_alturas_relativas_valores_maximos == true) {
        muestra_grafica_puntual_barras_valores(
            id_grafica_alturas_relativas_valores_maximos,
            TLNT.Idiomas._("Alturas relativas de valores máximos") + " (" + "%" + ")",
            etiquetas_graficas_valores,
            grafica_alturas_relativas_valores_maximos,
            [TLNT.Idiomas._("Sensores")], null,
            max_altura_relativa_valores_maximos, true,
            2, "%",
            false, mostrar_valores_barras,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Texto de explicación de altura relativa máxima
    if (mostrar_texto_explicacion_altura_relativa_maxima == true) {
        $("#" + id_texto_explicacion_altura_relativa_maxima).html(texto_explicacion_altura_relativa_maxima);
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_sensores_analisis(
    tipo_informe_sensores_analisis,
    elementos_informe,
    parametros,
    datos) {
    switch (tipo_informe_sensores_analisis) {
        case TIPO_INFORME_SENSORES_ANALISIS_HORARIO: {
            var mostrar_grafica_valores = true;
            var mostrar_mapa_calor_valores = true;
            var mostrar_grafica_medias_valores = true;
            var mostrar_grafica_coeficientes_variacion_valores = true;
            var mostrar_grafica_lorenz_valores = true;
            var mostrar_tabla_percentiles_valores = true;
            var mostrar_grafica_porcentajes_valores = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_MAPA_CALOR_VALORES) == -1) {
                    mostrar_mapa_calor_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_MEDIAS_VALORES) == -1) {
                    mostrar_grafica_medias_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_COEFICIENTES_VARIACION_VALORES) == -1) {
                    mostrar_grafica_coeficientes_variacion_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_LORENZ_VALORES) == -1) {
                    mostrar_grafica_lorenz_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_TABLA_PERCENTILES_VALORES) == -1) {
                    mostrar_tabla_percentiles_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_PORCENTAJES_VALORES) == -1) {
                    mostrar_grafica_porcentajes_valores = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_mapa_calor_valores = mostrar_mapa_calor_valores;
            parametros.mostrar_grafica_medias_valores = mostrar_grafica_medias_valores;
            parametros.mostrar_grafica_coeficientes_variacion_valores = mostrar_grafica_coeficientes_variacion_valores;
            parametros.mostrar_grafica_lorenz_valores = mostrar_grafica_lorenz_valores;
            parametros.mostrar_tabla_percentiles_valores = mostrar_tabla_percentiles_valores;
            parametros.mostrar_grafica_porcentajes_valores = mostrar_grafica_porcentajes_valores;
            break;
        }
        case TIPO_INFORME_SENSORES_ANALISIS_DIARIO: {
            var mostrar_grafica_valores = true;
            var mostrar_mapa_calor_valores = true;
            var mostrar_grafica_medias_valores = true;
            var mostrar_grafica_coeficientes_variacion_valores = true;
            var mostrar_grafica_sumas_valores = true;
            var mostrar_grafica_valores_medias_maximos_minimos = true;
            var mostrar_tabla_maximos_minimos_medias_medidas = true;
            var mostrar_tabla_valores_dia = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_MAPA_CALOR_VALORES) == -1) {
                    mostrar_mapa_calor_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_MEDIAS_VALORES) == -1) {
                    mostrar_grafica_medias_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_COEFICIENTES_VARIACION_VALORES) == -1) {
                    mostrar_grafica_coeficientes_variacion_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_SUMAS_VALORES) == -1) {
                    mostrar_grafica_sumas_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_VALORES_MEDIAS_MAXIMOS_MINIMOS) == -1) {
                    mostrar_grafica_valores_medias_maximos_minimos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_TABLA_MAXIMOS_MINIMOS_MEDIAS_MEDIDAS) == -1) {
                    mostrar_tabla_maximos_minimos_medias_medidas = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_TABLA_VALORES_DIA) == -1) {
                    mostrar_tabla_valores_dia = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_mapa_calor_valores = mostrar_mapa_calor_valores;
            parametros.mostrar_grafica_medias_valores = mostrar_grafica_medias_valores;
            parametros.mostrar_grafica_coeficientes_variacion_valores = mostrar_grafica_coeficientes_variacion_valores;
            parametros.mostrar_grafica_sumas_valores = mostrar_grafica_sumas_valores;
            parametros.mostrar_grafica_valores_medias_maximos_minimos = mostrar_grafica_valores_medias_maximos_minimos;
            parametros.mostrar_tabla_maximos_minimos_medias_medidas = mostrar_tabla_maximos_minimos_medias_medidas;
            parametros.mostrar_tabla_valores_dia = mostrar_tabla_valores_dia;

            // Indica si hay elementos visibles
            var campo_incremental = datos.campo_incremental;
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                (mostrar_mapa_calor_valores == true) ||
                (mostrar_grafica_medias_valores == true) ||
                (mostrar_grafica_coeficientes_variacion_valores == true) ||
                ((mostrar_grafica_sumas_valores == true) && (campo_incremental == true)) ||
                (mostrar_grafica_valores_medias_maximos_minimos == true) ||
                (mostrar_tabla_maximos_minimos_medias_medidas == true) ||
                (mostrar_tabla_valores_dia == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO: {
            var mostrar_grafica_coeficientes_estabilidad_valores = true;
            var mostrar_texto_explicacion_coeficiente_estabilidad = true;
            var mostrar_grafica_amplitudes_valores = true;
            var mostrar_texto_explicacion_amplitud = true;
            var mostrar_grafica_alturas_relativas_valores_maximos = true;
            var mostrar_texto_explicacion_altura_relativa_maxima = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_COEFICIENTES_ESTABILIDAD_VALORES) == -1) {
                    mostrar_grafica_coeficientes_estabilidad_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_COEFICIENTE_ESTABILIDAD) == -1) {
                    mostrar_texto_explicacion_coeficiente_estabilidad = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_AMPLITUDES_VALORES) == -1) {
                    mostrar_grafica_amplitudes_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_AMPLITUD) == -1) {
                    mostrar_texto_explicacion_amplitud = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_ALTURAS_RELATIVAS_VALORES_MAXIMOS) == -1) {
                    mostrar_grafica_alturas_relativas_valores_maximos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_ALTURA_RELATIVA_MAXIMA) == -1) {
                    mostrar_texto_explicacion_altura_relativa_maxima = false;
                }
            }
            parametros.mostrar_grafica_coeficientes_estabilidad_valores = mostrar_grafica_coeficientes_estabilidad_valores;
            parametros.mostrar_texto_explicacion_coeficiente_estabilidad = mostrar_texto_explicacion_coeficiente_estabilidad;
            parametros.mostrar_grafica_amplitudes_valores = mostrar_grafica_amplitudes_valores;
            parametros.mostrar_texto_explicacion_amplitud = mostrar_texto_explicacion_amplitud;
            parametros.mostrar_grafica_alturas_relativas_valores_maximos = mostrar_grafica_alturas_relativas_valores_maximos;
            parametros.mostrar_texto_explicacion_altura_relativa_maxima = mostrar_texto_explicacion_altura_relativa_maxima;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Sensores - Análisis horario)
function dibuja_elemento_plantilla_informe_sensores_analisis_horario(
    info_elemento,
    datos_elemento,
    elementos_informe_elemento,
    parametros_elemento,
    tipo_informe) {
    // Información del elemento
    var numero_elemento = info_elemento["numero_elemento"];
    var parametros_tipo = info_elemento["parametros_tipo"];

    // Comprobación de error
    var hay_error = (datos_elemento.res == "ERROR");
    if (hay_error == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).html(
            "<i class='icon-warning-sign color-rojo'></i> " + datos_elemento.msg);
        $("#elemento-error-datos-elemento" + numero_elemento).show();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de sensor seleccionado
    var sin_sensor_seleccionado = datos_elemento.sin_sensor_seleccionado;
    if (sin_sensor_seleccionado == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Parámetros del elemento
    var fecha_hora_inicio_consulta = parametros_elemento.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros_elemento.fecha_hora_fin_consulta;

    // Clase de sensor y campo
    var clase_sensor = parametros_tipo["clase_sensor"];
    var campo = parametros_tipo["campo"];

    // Tipo de mapa de calor
    var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_valores = prefijo_elemento + "grafica-valores-analisis-horario";
    var id_mapa_calor_valores = prefijo_elemento + "mapa-calor-valores-analisis-horario";
    var id_grafica_medias_valores = prefijo_elemento + "grafica-medias-valores-analisis-horario";
    var id_grafica_coeficientes_variacion_valores = prefijo_elemento + "grafica-coeficientes-variacion-valores-analisis-horario";
    var id_grafica_lorenz_valores = prefijo_elemento + "grafica-lorenz-valores-analisis-horario";
    var id_contenedor_tabla_percentiles_valores = prefijo_elemento + "contenedor-tabla-percentiles-valores-analisis-horario";
    var id_grafica_porcentajes_valores = prefijo_elemento + "grafica-porcentajes-valores-analisis-horario";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        clase_sensor: clase_sensor,
        campo: campo,
        id_grafica_valores: id_grafica_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        id_mapa_calor_valores: id_mapa_calor_valores,
        altura_maxima_mapa_calor_valores: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA,
        id_grafica_medias_valores: id_grafica_medias_valores,
        id_grafica_coeficientes_variacion_valores: id_grafica_coeficientes_variacion_valores,
        id_grafica_lorenz_valores: id_grafica_lorenz_valores,
        id_contenedor_tabla_percentiles_valores: id_contenedor_tabla_percentiles_valores,
        id_grafica_porcentajes_valores: id_grafica_porcentajes_valores};
    dibuja_informe_sensores_analisis_horario(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Sensores - Análisis diario)
function dibuja_elemento_plantilla_informe_sensores_analisis_diario(
    info_elemento,
    datos_elemento,
    elementos_informe_elemento,
    parametros_elemento,
    tipo_informe) {
    // Información del elemento
    var numero_elemento = info_elemento["numero_elemento"];
    var parametros_tipo = info_elemento["parametros_tipo"];

    // Comprobación de error
    var hay_error = (datos_elemento.res == "ERROR");
    if (hay_error == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).html(
            "<i class='icon-warning-sign color-rojo'></i> " + datos_elemento.msg);
        $("#elemento-error-datos-elemento" + numero_elemento).show();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de sensor seleccionado
    var sin_sensor_seleccionado = datos_elemento.sin_sensor_seleccionado;
    if (sin_sensor_seleccionado == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Parámetros del elemento
    var fecha_hora_inicio_consulta = parametros_elemento.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros_elemento.fecha_hora_fin_consulta;

    // Clase de sensor y campo
    var clase_sensor = parametros_tipo["clase_sensor"];
    var campo = parametros_tipo["campo"];

    // Tipo de mapa de calor
    var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_valores = prefijo_elemento + "grafica-valores-analisis-diario";
    var id_mapa_calor_valores = prefijo_elemento + "mapa-calor-valores-analisis-diario";
    var id_grafica_medias_valores = prefijo_elemento + "grafica-medias-valores-analisis-diario";
    var id_grafica_coeficientes_variacion_valores = prefijo_elemento + "grafica-coeficientes-variacion-valores-analisis-diario";
    var id_grafica_sumas_valores = prefijo_elemento + "grafica-sumas-valores-analisis-diario";
    var id_grafica_valores_medias_maximos_minimos = prefijo_elemento + "grafica-valores-medias-maximos-minimos-analisis-diario";
    var id_contenedor_tabla_maximos_minimos_medias_medidas = prefijo_elemento + "contenedor-tabla-maximos-minimos-medias-medidas-analisis-diario";
    var id_contenedor_tabla_valores_dia = prefijo_elemento + "contenedor-tabla-valores-dia-analisis-diario";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        clase_sensor: clase_sensor,
        campo: campo,
        id_grafica_valores: id_grafica_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        id_mapa_calor_valores: id_mapa_calor_valores,
        altura_maxima_mapa_calor_valores: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA,
        id_grafica_medias_valores: id_grafica_medias_valores,
        id_grafica_coeficientes_variacion_valores: id_grafica_coeficientes_variacion_valores,
        id_grafica_sumas_valores: id_grafica_sumas_valores,
        id_grafica_valores_medias_maximos_minimos: id_grafica_valores_medias_maximos_minimos,
        id_contenedor_tabla_maximos_minimos_medias_medidas: id_contenedor_tabla_maximos_minimos_medias_medidas,
        id_contenedor_tabla_valores_dia: id_contenedor_tabla_valores_dia};
    dibuja_informe_sensores_analisis_diario(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Sensores - Análisis de comportamiento)
function dibuja_elemento_plantilla_informe_sensores_analisis_comportamiento(
    info_elemento,
    datos_elemento,
    elementos_informe_elemento,
    parametros_elemento,
    tipo_informe) {
    // Información del elemento
    var numero_elemento = info_elemento["numero_elemento"];
    var parametros_tipo = info_elemento["parametros_tipo"];

    // Comprobación de error
    var hay_error = (datos_elemento.res == "ERROR");
    if (hay_error == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).html(
            "<i class='icon-warning-sign color-rojo'></i> " + datos_elemento.msg);
        $("#elemento-error-datos-elemento" + numero_elemento).show();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de sensores seleccionados
    var sin_sensores_seleccionados = datos_elemento.sin_sensores_seleccionados;
    if (sin_sensores_seleccionados == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensores-seleccionados-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensores-seleccionados-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_coeficientes_estabilidad_valores = prefijo_elemento + "grafica-coeficientes-estabilidad-valores-analisis-comportamiento";
    var id_texto_explicacion_coeficiente_estabilidad = prefijo_elemento + "texto-explicacion-coeficiente-estabilidad-analisis-comportamiento";
    var id_grafica_amplitudes_valores = prefijo_elemento + "grafica-amplitudes-valores-analisis-comportamiento";
    var id_texto_explicacion_amplitud = prefijo_elemento + "texto-explicacion-amplitud-analisis-comportamiento";
    var id_grafica_alturas_relativas_valores_maximos = prefijo_elemento + "grafica-alturas-relativas-valores-maximos-analisis-comportamiento";
    var id_texto_explicacion_altura_relativa_maxima = prefijo_elemento + "texto-explicacion-altura-relativa-maxima-analisis-comportamiento";

    var parametros = {
        id_grafica_coeficientes_estabilidad_valores: id_grafica_coeficientes_estabilidad_valores,
        id_texto_explicacion_coeficiente_estabilidad: id_texto_explicacion_coeficiente_estabilidad,
        id_grafica_amplitudes_valores: id_grafica_amplitudes_valores,
        id_texto_explicacion_amplitud: id_texto_explicacion_amplitud,
        id_grafica_alturas_relativas_valores_maximos: id_grafica_alturas_relativas_valores_maximos,
        id_texto_explicacion_altura_relativa_maxima: id_texto_explicacion_altura_relativa_maxima};
    dibuja_informe_sensores_analisis_comportamiento(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}
