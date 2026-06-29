//
// Funciones para el dibujado de los informes de compra de energía (SmartMeter) (España)
//


// Dibujado del informe de previsión de compra de energía
function dibuja_informe_smartmeter_prevision_compra_energia_Espanya(
    parametros,
    datos,
    tipo_informe) {
    // Datos del resultado
    var etiquetas_grafica_consumos_estimados = datos.etiquetas_grafica_consumos_estimados;
    var grafica_consumos_estimados = datos.grafica_consumos_estimados;
    var max_consumo_estimado = datos.max_consumo_estimado;
    var dias_mapa_calor_consumos_estimados = datos.dias_mapa_calor_consumos_estimados;
    var horas_mapa_calor_consumos_estimados = datos.horas_mapa_calor_consumos_estimados;
    var datos_mapa_calor_consumos_estimados = datos.datos_mapa_calor_consumos_estimados;
    var etiquetas_grafica_consumos_perfil_horario = datos.etiquetas_grafica_consumos_perfil_horario;
    var grafica_consumos_perfil_horario = datos.grafica_consumos_perfil_horario;
    var bandas_consumos_perfil_horario = datos.bandas_consumos_perfil_horario;
    var max_consumo_perfil_horario = datos.max_consumo_perfil_horario;
    var dias_mapa_calor_consumos_perfil_horario_semanales = datos.dias_mapa_calor_consumos_perfil_horario_semanales;
    var datos_mapa_calor_consumos_perfil_horario_semanales = datos.datos_mapa_calor_consumos_perfil_horario_semanales;
    var dias_mapa_calor_consumos_perfil_horario = datos.dias_mapa_calor_consumos_perfil_horario;
    var datos_mapa_calor_consumos_perfil_horario = datos.datos_mapa_calor_consumos_perfil_horario;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_consumos_estimados = parametros.id_grafica_consumos_estimados;
    var id_mapa_calor_consumos_estimados = parametros.id_mapa_calor_consumos_estimados;
    var id_grafica_consumos_perfil_horario = parametros.id_grafica_consumos_perfil_horario;
    var id_mapa_calor_consumos_perfil_horario_semanales = parametros.id_mapa_calor_consumos_perfil_horario_semanales;
    var id_mapa_calor_consumos_perfil_horario = parametros.id_mapa_calor_consumos_perfil_horario;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_PREVISION_COMPRA_ENERGIA,
        null);

    // Se muestran los elementos visibles
    muestra_elementos([
        id_grafica_consumos_estimados,
        id_mapa_calor_consumos_estimados]);

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

    // Mostrar indicadores de valores
    var numero_valores_grafica_consumos_estimados = dame_numero_maximo_valores_series_grafica(grafica_consumos_estimados);
    var mostrar_indicadores_valores_consumos_estimados = (numero_valores_grafica_consumos_estimados <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);
    var numero_valores_grafica_consumos_perfil_horario = dame_numero_maximo_valores_series_grafica(grafica_consumos_perfil_horario);
    var mostrar_indicadores_valores_consumos_perfil_horario = (numero_valores_grafica_consumos_perfil_horario <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de consumo estimado
    var titulo_grafica_consumos_estimados = TLNT.Idiomas._("Consumo estimado") + " (" + unidad_medida + ")";
    muestra_grafica_temporal_lineas_valores(
        id_grafica_consumos_estimados,
        null,
        titulo_grafica_consumos_estimados,
        etiquetas_grafica_consumos_estimados,
        grafica_consumos_estimados, null, INTERVALO_VALORES_HORA,
        null,
        fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
        0, false,
        max_consumo_estimado, true,
        2, unidad_medida,
        null,
        true,
        TIPO_LINEAS_VALORES_ESTANDAR,
        mostrar_indicadores_valores_consumos_estimados,
        false,
        mostrar_animaciones,
        anyadir_menus_contextuales);

    // Mapa de calor de consumo estimado
    var titulo_mapa_calor_consumos_estimados = TLNT.Idiomas._("Consumo estimado") + " (" + unidad_medida + ")";
    var periodos_mapa_calor_consumos_estimado = [
        dias_mapa_calor_consumos_estimados,
        dame_indices_dias_entre_semana_fechas(dias_mapa_calor_consumos_estimados)];
    var subperiodos_mapa_calor_consumos_estimado = [
        horas_mapa_calor_consumos_estimados,
        null];
    muestra_grafico_mapa_calor(
        id_mapa_calor_consumos_estimados,
        TIPO_MAPA_CALOR_PERSONALIZADO,
        titulo_mapa_calor_consumos_estimados,
        periodos_mapa_calor_consumos_estimado,
        subperiodos_mapa_calor_consumos_estimado,
        datos_mapa_calor_consumos_estimados,
        null,
        1,
        true,
        ESCALA_COLORES_VERDE_ROJO,
        null,
        mostrar_animaciones,
        anyadir_menus_contextuales);

    // Gráfica de consumos de perfil horario
    var titulo_grafica_consumos_perfil_horario = TLNT.Idiomas._("Consumos de perfil horario") + " (" + unidad_medida + ")";
    muestra_grafica_temporal_lineas_valores(
        id_grafica_consumos_perfil_horario,
        null,
        titulo_grafica_consumos_perfil_horario,
        etiquetas_grafica_consumos_perfil_horario,
        grafica_consumos_perfil_horario, bandas_consumos_perfil_horario, INTERVALO_VALORES_HORA,
        null,
        null, null, false,
        0, false,
        max_consumo_perfil_horario, true,
        2, unidad_medida,
        null,
        true,
        TIPO_LINEAS_VALORES_ESTANDAR,
        mostrar_indicadores_valores_consumos_perfil_horario,
        true,
        mostrar_animaciones,
        anyadir_menus_contextuales);

    // Mapa de calor de consumos de perfil horario semanales
    var titulo_mapa_calor_consumos_perfil_horario_semanales = TLNT.Idiomas._("Media de consumo semanal de perfil horario") + " (" + unidad_medida + ")";
    muestra_grafico_mapa_calor(
        id_mapa_calor_consumos_perfil_horario_semanales,
        TIPO_MAPA_CALOR_SEMANAL,
        titulo_mapa_calor_consumos_perfil_horario_semanales,
        dias_mapa_calor_consumos_perfil_horario_semanales,
        null,
        datos_mapa_calor_consumos_perfil_horario_semanales,
        null,
        1,
        true,
        ESCALA_COLORES_VERDE_ROJO,
        null,
        mostrar_animaciones,
        anyadir_menus_contextuales);

    // Mapa de calor de consumos de perfil horario
    var titulo_mapa_calor_consumos_perfil_horario = TLNT.Idiomas._("Consumo de perfil horario") + " (" + unidad_medida + ")";
    muestra_grafico_mapa_calor(
        id_mapa_calor_consumos_perfil_horario,
        TIPO_MAPA_CALOR_DIARIO,
        titulo_mapa_calor_consumos_perfil_horario,
        dias_mapa_calor_consumos_perfil_horario,
        null,
        datos_mapa_calor_consumos_perfil_horario,
        null,
        1,
        true,
        ESCALA_COLORES_VERDE_ROJO,
        null,
        mostrar_animaciones,
        anyadir_menus_contextuales);
}


// Dibujado del informe de desvíos de compra de energía
function dibuja_informe_smartmeter_desvios_compra_energia_Espanya(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var tabla_consumos_desvios_totales = datos.tabla_consumos_desvios_totales;
    var etiquetas_graficas_consumos = datos.etiquetas_graficas_consumos;
    var etiquetas_sensor = datos.etiquetas_sensor;
    var grafica_consumos = datos.grafica_consumos;
    var grafica_consumos_acumulados = datos.grafica_consumos_acumulados;
    var grafica_desvios_consumo = datos.grafica_desvios_consumo;
    var grafica_desvios_consumo_acumulados = datos.grafica_desvios_consumo_acumulados;
    var grafica_costes_desvios = datos.grafica_costes_desvios;
    var grafica_costes_desvios_acumulados = datos.grafica_costes_desvios_acumulados;
    var min_desvio_consumo = datos.min_desvio_consumo;
    var min_desvio_consumo_acumulado = datos.min_desvio_consumo_acumulado;
    var min_coste_desvio = datos.min_coste_desvio;
    var min_coste_desvio_acumulado = datos.min_coste_desvio_acumulado;
    var max_consumos = datos.max_consumos;
    var max_consumos_acumulados = datos.max_consumos_acumulados;
    var max_desvio_consumo = datos.max_desvio_consumo;
    var max_desvio_consumo_acumulado = datos.max_desvio_consumo_acumulado;
    var max_coste_desvio = datos.max_coste_desvio;
    var max_coste_desvio_acumulado = datos.max_coste_desvio_acumulado;
    var colores_mapas_calor = datos.colores_mapas_calor;
    var dias_mapas_calor = datos.dias_mapas_calor;
    var datos_mapas_calor = datos.datos_mapas_calor;
    var datos_mapa_calor_desvios_consumo_visibles = datos.datos_mapa_calor_desvios_consumo_visibles;
    var datos_mapa_calor_costes_desvios_visibles = datos.datos_mapa_calor_costes_desvios_visibles;
    var unidad_medida_consumo = datos.unidad_medida_consumo;
    var unidad_medida_coste = datos.unidad_medida_coste;

    // Escala de colores de los mapas de calor
    var escala_colores_mapas_calor = dame_escala_colores_mapa_calor(colores_mapas_calor);

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_contenedor_tabla_consumos_desvios_totales = parametros.id_contenedor_tabla_consumos_desvios_totales;
    var id_grafica_consumos = parametros.id_grafica_consumos;
    var id_grafica_consumos_acumulados = parametros.id_grafica_consumos_acumulados;
    var id_grafica_desvios_consumo = parametros.id_grafica_desvios_consumo;
    var id_grafica_desvios_consumo_acumulados = parametros.id_grafica_desvios_consumo_acumulados;
    var id_mapa_calor_desvios_consumo = parametros.id_mapa_calor_desvios_consumo;
    var id_grafica_costes_desvios = parametros.id_grafica_costes_desvios;
    var id_grafica_costes_desvios_acumulados = parametros.id_grafica_costes_desvios_acumulados;
    var id_mapa_calor_costes_desvios = parametros.id_mapa_calor_costes_desvios;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_compra_energia(
        TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_tabla_consumos_desvios_totales = parametros.mostrar_tabla_consumos_desvios_totales;
    var mostrar_grafica_consumos = parametros.mostrar_grafica_consumos;
    var mostrar_grafica_consumos_acumulados = parametros.mostrar_grafica_consumos_acumulados;
    var mostrar_grafica_desvios_consumo = parametros.mostrar_grafica_desvios_consumo;
    var mostrar_grafica_desvios_consumo_acumulados = parametros.mostrar_grafica_desvios_consumo_acumulados;
    var mostrar_mapa_calor_desvios_consumo = parametros.mostrar_mapa_calor_desvios_consumo;
    var mostrar_grafica_costes_desvios = parametros.mostrar_grafica_costes_desvios;
    var mostrar_grafica_costes_desvios_acumulados = parametros.mostrar_grafica_costes_desvios_acumulados;
    var mostrar_mapa_calor_costes_desvios = parametros.mostrar_mapa_calor_costes_desvios;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_tabla_consumos_desvios_totales == true) {
        muestra_elemento(id_contenedor_tabla_consumos_desvios_totales);
    }
    if (mostrar_grafica_consumos == true) {
        muestra_elemento(id_grafica_consumos);
    }
    if (mostrar_grafica_consumos_acumulados == true) {
        muestra_elemento(id_grafica_consumos_acumulados);
    }
    if (mostrar_grafica_desvios_consumo == true) {
        muestra_elemento(id_grafica_desvios_consumo);
    }
    if (mostrar_grafica_desvios_consumo_acumulados == true) {
        muestra_elemento(id_grafica_desvios_consumo_acumulados);
    }
    if (mostrar_mapa_calor_desvios_consumo == true) {
        muestra_elemento(id_mapa_calor_desvios_consumo);
    }
    if (mostrar_grafica_costes_desvios == true) {
        muestra_elemento(id_grafica_costes_desvios);
    }
    if (mostrar_grafica_costes_desvios_acumulados == true) {
        muestra_elemento(id_grafica_costes_desvios_acumulados);
    }
    if (mostrar_mapa_calor_costes_desvios == true) {
        muestra_elemento(id_mapa_calor_costes_desvios);
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

    // Mostrar indicadores de valores
    var numero_valores_grafica_consumos = dame_numero_maximo_valores_series_grafica(grafica_consumos);
    var mostrar_indicadores_valores = (numero_valores_grafica_consumos <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Tabla de consumos y desvíos totales
    if (mostrar_tabla_consumos_desvios_totales == true) {
        $("#" + id_contenedor_tabla_consumos_desvios_totales).html(tabla_consumos_desvios_totales);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_consumos_desvios_totales = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_consumos_desvios_totales);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_consumos_desvios_totales, info_menu_contextual, TLNT.Idiomas._('Consumos y desvíos totales'));
        }
    }

    // Gráfica de consumos (estimado y real)
    if (mostrar_grafica_consumos == true) {
        var titulo_grafica_consumos = TLNT.Idiomas._("Consumos") + " (" + unidad_medida_consumo + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_consumos,
            null,
            titulo_grafica_consumos,
            etiquetas_graficas_consumos,
            grafica_consumos, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_consumos, true,
            2, unidad_medida_consumo,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de consumos acumulados (estimado y real)
    if (mostrar_grafica_consumos_acumulados == true) {
        var titulo_grafica_consumos_acumulados = TLNT.Idiomas._("Consumos acumulados") + " (" + unidad_medida_consumo + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_consumos_acumulados,
            null,
            titulo_grafica_consumos_acumulados,
            etiquetas_graficas_consumos,
            grafica_consumos_acumulados, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_consumos_acumulados, true,
            2, unidad_medida_consumo,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de desvíos de consumo
    if (mostrar_grafica_desvios_consumo == true) {
        var titulo_grafica_desvios_consumo = TLNT.Idiomas._("Desvíos de consumo") + " (" + unidad_medida_consumo + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_desvios_consumo,
            null,
            titulo_grafica_desvios_consumo,
            etiquetas_sensor,
            grafica_desvios_consumo, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_desvio_consumo, true,
            max_desvio_consumo, true,
            2, unidad_medida_consumo,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de desvíos acumulados de consumo
    if (mostrar_grafica_desvios_consumo_acumulados == true) {
        var titulo_grafica_desvios_consumo_acumulados = TLNT.Idiomas._("Desvíos acumulados de consumo") + " (" + unidad_medida_consumo + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_desvios_consumo_acumulados,
            null,
            titulo_grafica_desvios_consumo_acumulados,
            etiquetas_sensor,
            grafica_desvios_consumo_acumulados, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_desvio_consumo_acumulado, true,
            max_desvio_consumo_acumulado, true,
            2, unidad_medida_consumo,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor de desvíos de consumo
    if (mostrar_mapa_calor_desvios_consumo == true) {
        var titulo_mapa_calor_desvios_consumo = TLNT.Idiomas._("Desvíos de consumo") + " (" + unidad_medida_consumo + ")";
        muestra_grafico_mapa_calor(
            id_mapa_calor_desvios_consumo,
            TIPO_MAPA_CALOR_DIARIO,
            titulo_mapa_calor_desvios_consumo,
            dias_mapas_calor,
            null,
            datos_mapas_calor,
            datos_mapa_calor_desvios_consumo_visibles,
            null,
            false,
            escala_colores_mapas_calor,
            null,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes de desvíos (de consumo)
    if (mostrar_grafica_costes_desvios == true) {
        var titulo_grafica_costes_desvios = TLNT.Idiomas._("Costes de desvíos") + " (" + unidad_medida_coste + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_costes_desvios,
            null,
            titulo_grafica_costes_desvios,
            etiquetas_sensor,
            grafica_costes_desvios, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_coste_desvio, true,
            max_coste_desvio, true,
            2, unidad_medida_coste,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes de desvíos acumulados (de consumo)
    if (mostrar_grafica_costes_desvios_acumulados == true) {
        var titulo_grafica_costes_desvios_acumulados = TLNT.Idiomas._("Costes acumulados de desvíos") + " (" + unidad_medida_coste + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_costes_desvios_acumulados,
            null,
            titulo_grafica_costes_desvios_acumulados,
            etiquetas_sensor,
            grafica_costes_desvios_acumulados, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_coste_desvio_acumulado, true,
            max_coste_desvio_acumulado, true,
            2, unidad_medida_coste,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor de costes de desvíos (de consumo)
    if (mostrar_mapa_calor_costes_desvios == true) {
        var titulo_mapa_calor_costes_desvios = TLNT.Idiomas._("Costes de desvíos") + " (" + unidad_medida_coste + ")";
        muestra_grafico_mapa_calor(
            id_mapa_calor_costes_desvios,
            TIPO_MAPA_CALOR_DIARIO,
            titulo_mapa_calor_costes_desvios,
            dias_mapas_calor,
            null,
            datos_mapas_calor,
            datos_mapa_calor_costes_desvios_visibles,
            null,
            false,
            escala_colores_mapas_calor,
            null,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }
}


// Dibujado del informe de desvíos ponderados de compra de energía
function dibuja_informe_smartmeter_desvios_ponderados_compra_energia_Espanya(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var tabla_consumos_coste_desvio_ponderado_totales = datos.tabla_consumos_coste_desvio_ponderado_totales;
    var etiquetas_graficas_consumos = datos.etiquetas_graficas_consumos;
    var etiquetas_sensor_hijo = datos.etiquetas_sensor_hijo;
    var grafica_consumos = datos.grafica_consumos;
    var grafica_consumos_acumulados = datos.grafica_consumos_acumulados;
    var grafica_costes_desvios_ponderados = datos.grafica_costes_desvios_ponderados;
    var grafica_costes_desvios_ponderados_acumulados = datos.grafica_costes_desvios_ponderados_acumulados;
    var min_coste_desvio_ponderado = datos.min_coste_desvio_ponderado;
    var min_coste_desvio_ponderado_acumulado = datos.min_coste_desvio_ponderado_acumulado;
    var max_consumos = datos.max_consumos;
    var max_consumos_acumulados = datos.max_consumos_acumulados;
    var max_coste_desvio_ponderado = datos.max_coste_desvio_ponderado;
    var max_coste_desvio_ponderado_acumulado = datos.max_coste_desvio_ponderado_acumulado;
    var dias_mapa_calor_costes_desvios_ponderados = datos.dias_mapa_calor_costes_desvios_ponderados;
    var datos_mapa_calor_costes_desvios_ponderados = datos.datos_mapa_calor_costes_desvios_ponderados;
    var unidad_medida_consumo = datos.unidad_medida_consumo;
    var unidad_medida_coste = datos.unidad_medida_coste;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_contenedor_tabla_consumos_coste_desvio_ponderado_totales = parametros.id_contenedor_tabla_consumos_coste_desvio_ponderado_totales;
    var id_grafica_consumos = parametros.id_grafica_consumos;
    var id_grafica_consumos_acumulados = parametros.id_grafica_consumos_acumulados;
    var id_grafica_costes_desvios_ponderados = parametros.id_grafica_costes_desvios_ponderados;
    var id_grafica_costes_desvios_ponderados_acumulados = parametros.id_grafica_costes_desvios_ponderados_acumulados;
    var id_mapa_calor_costes_desvios_ponderados = parametros.id_mapa_calor_costes_desvios_ponderados;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_compra_energia(
        TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_tabla_consumos_coste_desvio_ponderado_totales = parametros.mostrar_tabla_consumos_coste_desvio_ponderado_totales;
    var mostrar_grafica_consumos = parametros.mostrar_grafica_consumos;
    var mostrar_grafica_consumos_acumulados = parametros.mostrar_grafica_consumos_acumulados;
    var mostrar_grafica_costes_desvios_ponderados = parametros.mostrar_grafica_costes_desvios_ponderados;
    var mostrar_grafica_costes_desvios_ponderados_acumulados = parametros.mostrar_grafica_costes_desvios_ponderados_acumulados;
    var mostrar_mapa_calor_costes_desvios_ponderados = parametros.mostrar_mapa_calor_costes_desvios_ponderados;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_tabla_consumos_coste_desvio_ponderado_totales == true) {
        muestra_elemento(id_contenedor_tabla_consumos_coste_desvio_ponderado_totales);
    }
    if (mostrar_grafica_consumos == true) {
        muestra_elemento(id_grafica_consumos);
    }
    if (mostrar_grafica_consumos_acumulados == true) {
        muestra_elemento(id_grafica_consumos_acumulados);
    }
    if (mostrar_grafica_costes_desvios_ponderados == true) {
        muestra_elemento(id_grafica_costes_desvios_ponderados);
    }
    if (mostrar_grafica_costes_desvios_ponderados_acumulados == true) {
        muestra_elemento(id_grafica_costes_desvios_ponderados_acumulados);
    }
    if (mostrar_mapa_calor_costes_desvios_ponderados == true) {
        muestra_elemento(id_mapa_calor_costes_desvios_ponderados);
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

    // Mostrar indicadores de valores
    var numero_valores_grafica_consumos = dame_numero_maximo_valores_series_grafica(grafica_consumos);
    var mostrar_indicadores_valores = (numero_valores_grafica_consumos <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Tabla de consumos y coste de desvío ponderado totales
    if (mostrar_tabla_consumos_coste_desvio_ponderado_totales == true) {
        $("#" + id_contenedor_tabla_consumos_coste_desvio_ponderado_totales).html(tabla_consumos_coste_desvio_ponderado_totales);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_consumos_coste_desvio_ponderado_totales = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_consumos_coste_desvio_ponderado_totales);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_consumos_coste_desvio_ponderado_totales, info_menu_contextual, TLNT.Idiomas._('Consumos y coste de desvío ponderado totales'));
        }
    }

    // Gráfica de consumos (bruto y neto del sensor hijo y real del sensor de compra de energía)
    if (mostrar_grafica_consumos == true) {
        var titulo_grafica_consumos = TLNT.Idiomas._("Consumos") + " (" + unidad_medida_consumo + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_consumos,
            null,
            titulo_grafica_consumos,
            etiquetas_graficas_consumos,
            grafica_consumos, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_consumos, true,
            2, unidad_medida_consumo,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de consumos acumulados (bruto y neto del sensor hijo y real del sensor de compra de energía)
    if (mostrar_grafica_consumos_acumulados == true) {
        var titulo_grafica_consumos_acumulados = TLNT.Idiomas._("Consumos acumulados") + " (" + unidad_medida_consumo + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_consumos_acumulados,
            null,
            titulo_grafica_consumos_acumulados,
            etiquetas_graficas_consumos,
            grafica_consumos_acumulados, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_consumos_acumulados, true,
            2, unidad_medida_consumo,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes de desvíos ponderados
    if (mostrar_grafica_costes_desvios_ponderados == true) {
        var titulo_grafica_costes_desvios_ponderados = TLNT.Idiomas._("Costes de desvíos ponderados") + " (" + unidad_medida_coste + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_costes_desvios_ponderados,
            null,
            titulo_grafica_costes_desvios_ponderados,
            etiquetas_sensor_hijo,
            grafica_costes_desvios_ponderados, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_coste_desvio_ponderado, true,
            max_coste_desvio_ponderado, true,
            2, unidad_medida_coste,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes de desvíos ponderados acumulados
    if (mostrar_grafica_costes_desvios_ponderados_acumulados == true) {
        var titulo_grafica_costes_desvios_ponderados_acumulados = TLNT.Idiomas._("Costes de desvíos ponderados acumulados") + " (" + unidad_medida_coste + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_costes_desvios_ponderados_acumulados,
            null,
            titulo_grafica_costes_desvios_ponderados_acumulados,
            etiquetas_sensor_hijo,
            grafica_costes_desvios_ponderados_acumulados, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_coste_desvio_ponderado_acumulado, true,
            max_coste_desvio_ponderado_acumulado, true,
            2, unidad_medida_coste,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor de costes de desvíos ponderados
    if (mostrar_mapa_calor_costes_desvios_ponderados == true)
    {
        var titulo_mapa_calor_costes_desvios_ponderados = TLNT.Idiomas._("Costes de desvíos ponderados") + " (" + unidad_medida_coste + ")";
        muestra_grafico_mapa_calor(
            id_mapa_calor_costes_desvios_ponderados,
            TIPO_MAPA_CALOR_DIARIO,
            titulo_mapa_calor_costes_desvios_ponderados,
            dias_mapa_calor_costes_desvios_ponderados,
            null,
            datos_mapa_calor_costes_desvios_ponderados,
            null,
            null,
            true,
            ESCALA_COLORES_VERDE_ROJO,
            null,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_smartmeter_compra_energia(
    tipo_informe_smartmeter_compra_energia,
    elementos_informe,
    parametros,
    datos) {
    switch (tipo_informe_smartmeter_compra_energia) {
        case TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA: {
            var mostrar_tabla_consumos_desvios_totales = true;
            var mostrar_grafica_consumos = true;
            var mostrar_grafica_consumos_acumulados = true;
            var mostrar_grafica_desvios_consumo = true;
            var mostrar_grafica_desvios_consumo_acumulados = true;
            var mostrar_mapa_calor_desvios_consumo = true;
            var mostrar_grafica_costes_desvios = true;
            var mostrar_grafica_costes_desvios_acumulados = true;
            var mostrar_mapa_calor_costes_desvios = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_TABLA_CONSUMOS_DESVIOS_TOTALES) == -1) {
                    mostrar_tabla_consumos_desvios_totales = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS) == -1) {
                    mostrar_grafica_consumos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS_ACUMULADOS) == -1) {
                    mostrar_grafica_consumos_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_DESVIOS_CONSUMO) == -1) {
                    mostrar_grafica_desvios_consumo = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_DESVIOS_CONSUMO_ACUMULADOS) == -1) {
                    mostrar_grafica_desvios_consumo_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_DESVIOS_CONSUMO) == -1) {
                    mostrar_mapa_calor_desvios_consumo = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS) == -1) {
                    mostrar_grafica_costes_desvios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_ACUMULADOS) == -1) {
                    mostrar_grafica_costes_desvios_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_COSTES_DESVIOS) == -1) {
                    mostrar_mapa_calor_costes_desvios = false;
                }
            }
            parametros.mostrar_tabla_consumos_desvios_totales = mostrar_tabla_consumos_desvios_totales;
            parametros.mostrar_grafica_consumos = mostrar_grafica_consumos;
            parametros.mostrar_grafica_consumos_acumulados = mostrar_grafica_consumos_acumulados;
            parametros.mostrar_grafica_desvios_consumo = mostrar_grafica_desvios_consumo;
            parametros.mostrar_grafica_desvios_consumo_acumulados = mostrar_grafica_desvios_consumo_acumulados;
            parametros.mostrar_mapa_calor_desvios_consumo = mostrar_mapa_calor_desvios_consumo;
            parametros.mostrar_grafica_costes_desvios = mostrar_grafica_costes_desvios;
            parametros.mostrar_grafica_costes_desvios_acumulados = mostrar_grafica_costes_desvios_acumulados;
            parametros.mostrar_mapa_calor_costes_desvios = mostrar_mapa_calor_costes_desvios;
            break;
        }
        case TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA: {
            var mostrar_tabla_consumos_coste_desvio_ponderado_totales = true;
            var mostrar_grafica_consumos = true;
            var mostrar_grafica_consumos_acumulados = true;
            var mostrar_grafica_costes_desvios_ponderados = true;
            var mostrar_grafica_costes_desvios_ponderados_acumulados = true;
            var mostrar_mapa_calor_costes_desvios_ponderados = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_TABLA_CONSUMOS_COSTE_DESVIO_PONDERADO_TOTALES) == -1) {
                    mostrar_tabla_consumos_coste_desvio_ponderado_totales = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS) == -1) {
                    mostrar_grafica_consumos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS_ACUMULADOS) == -1) {
                    mostrar_grafica_consumos_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_PONDERADOS) == -1) {
                    mostrar_grafica_costes_desvios_ponderados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_PONDERADOS_ACUMULADOS) == -1) {
                    mostrar_grafica_costes_desvios_ponderados_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_COSTES_DESVIOS_PONDERADOS) == -1) {
                    mostrar_mapa_calor_costes_desvios_ponderados = false;
                }
            }
            parametros.mostrar_tabla_consumos_coste_desvio_ponderado_totales = mostrar_tabla_consumos_coste_desvio_ponderado_totales;
            parametros.mostrar_grafica_consumos = mostrar_grafica_consumos;
            parametros.mostrar_grafica_consumos_acumulados = mostrar_grafica_consumos_acumulados;
            parametros.mostrar_grafica_costes_desvios_ponderados = mostrar_grafica_costes_desvios_ponderados;
            parametros.mostrar_grafica_costes_desvios_ponderados_acumulados = mostrar_grafica_costes_desvios_ponderados_acumulados;
            parametros.mostrar_mapa_calor_costes_desvios_ponderados = mostrar_mapa_calor_costes_desvios_ponderados;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (SmartMeter - Desvíos de compra de energía)
function dibuja_elemento_plantilla_informe_smartmeter_desvios_compra_energia(
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

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_contenedor_tabla_consumos_desvios_totales = prefijo_elemento + "contenedor-tabla-consumos-desvios-totales-desvios-compra-energia";
    var id_grafica_consumos = prefijo_elemento + "grafica-consumos-desvios-compra-energia";
    var id_grafica_consumos_acumulados = prefijo_elemento + "grafica-consumos-acumulados-desvios-compra-energia";
    var id_grafica_desvios_consumo = prefijo_elemento + "grafica-desvios-consumo-desvios-compra-energia";
    var id_grafica_desvios_consumo_acumulados = prefijo_elemento + "grafica-desvios-consumo-acumulados-desvios-compra-energia";
    var id_mapa_calor_desvios_consumo = prefijo_elemento + "mapa-calor-desvios-consumo-desvios-compra-energia";
    var id_grafica_costes_desvios = prefijo_elemento + "grafica-costes-desvios-desvios-compra-energia";
    var id_grafica_costes_desvios_acumulados = prefijo_elemento + "grafica-costes-desvios-acumulados-desvios-compra-energia";
    var id_mapa_calor_costes_desvios = prefijo_elemento + "mapa-calor-costes-desvios-desvios-compra-energia";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        id_contenedor_tabla_consumos_desvios_totales: id_contenedor_tabla_consumos_desvios_totales,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        id_grafica_consumos: id_grafica_consumos,
        id_grafica_consumos_acumulados: id_grafica_consumos_acumulados,
        id_grafica_desvios_consumo: id_grafica_desvios_consumo,
        id_grafica_desvios_consumo_acumulados: id_grafica_desvios_consumo_acumulados,
        id_mapa_calor_desvios_consumo: id_mapa_calor_desvios_consumo,
        id_grafica_costes_desvios: id_grafica_costes_desvios,
        id_grafica_costes_desvios_acumulados: id_grafica_costes_desvios_acumulados,
        id_mapa_calor_costes_desvios: id_mapa_calor_costes_desvios};
    dibuja_informe_smartmeter_desvios_compra_energia_Espanya(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (SmartMeter - Desvíos ponderados de compra de energía)
function dibuja_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia(
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
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Parámetros del elemento
    var fecha_hora_inicio_consulta = parametros_elemento.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros_elemento.fecha_hora_fin_consulta;

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_contenedor_tabla_consumos_coste_desvio_ponderado_totales = prefijo_elemento + "contenedor-tabla-consumos-coste-desvio-ponderado-totales-desvios-ponderados-compra-energia";
    var id_grafica_consumos = prefijo_elemento + "grafica-consumos-desvios-ponderados-compra-energia";
    var id_grafica_consumos_acumulados = prefijo_elemento + "grafica-consumos-acumulados-desvios-ponderados-compra-energia";
    var id_grafica_costes_desvios_ponderados = prefijo_elemento + "grafica-costes-desvios-ponderados-desvios-ponderados-compra-energia";
    var id_grafica_costes_desvios_ponderados_acumulados = prefijo_elemento + "grafica-costes-desvios-ponderados-acumulados-desvios-ponderados-compra-energia";
    var id_mapa_calor_costes_desvios_ponderados = prefijo_elemento + "mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        id_contenedor_tabla_consumos_coste_desvio_ponderado_totales: id_contenedor_tabla_consumos_coste_desvio_ponderado_totales,
        id_grafica_consumos: id_grafica_consumos,
        id_grafica_consumos_acumulados: id_grafica_consumos_acumulados,
        id_grafica_costes_desvios_ponderados: id_grafica_costes_desvios_ponderados,
        id_grafica_costes_desvios_ponderados_acumulados: id_grafica_costes_desvios_ponderados_acumulados,
        id_mapa_calor_costes_desvios_ponderados: id_mapa_calor_costes_desvios_ponderados};
    dibuja_informe_smartmeter_desvios_ponderados_compra_energia_Espanya(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}
