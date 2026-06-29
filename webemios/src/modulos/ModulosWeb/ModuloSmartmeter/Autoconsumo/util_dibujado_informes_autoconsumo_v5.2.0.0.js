//
// Funciones para el dibujado de los informes de autoconsumo (SmartMeter)
//


// Dibujado del informe de simulación de autoconsumo
function dibuja_informe_smartmeter_simulador_autoconsumo(
    parametros,
    datos,
    tipo_informe) {
    // Datos del resultado
    var grafica_consumos = datos.grafica_consumos;
    var grafica_consumos_acumulados = datos.grafica_consumos_acumulados;
    var tabla_consumos = datos.tabla_consumos;
    var mostrar_info_costes = datos.mostrar_info_costes;
    var hay_datos_costes = datos.hay_datos_costes;
    var grafica_costes = datos.grafica_costes;
    var grafica_costes_acumulados = datos.grafica_costes_acumulados;
    var tabla_costes = datos.tabla_costes;
    var etiquetas_consumos = datos.etiquetas_consumos;
    var etiquetas_consumos_acumulados = datos.etiquetas_consumos_acumulados;
    var etiquetas_costes = datos.etiquetas_costes;
    var max_consumo = datos.max_consumo;
    var max_consumo_acumulado = datos.max_consumo_acumulado;
    var max_coste = datos.max_coste;
    var max_coste_acumulado = datos.max_coste_acumulado;
    var unidad_medida_consumo = datos.unidad_medida_consumo;
    var unidad_medida_coste = datos.unidad_medida_coste;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_consumos = parametros.id_grafica_consumos;
    var id_grafica_consumos_acumulados = parametros.id_grafica_consumos_acumulados;
    var id_contenedor_tabla_consumos = parametros.id_contenedor_tabla_consumos;
    var id_grafica_costes = parametros.id_grafica_costes;
    var id_grafica_costes_acumulados = parametros.id_grafica_costes_acumulados;
    var id_contenedor_tabla_costes = parametros.id_contenedor_tabla_costes;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_SIMULADOR_AUTOCONSUMO,
        null);

    // Si el informe es Web, hay que mostrar información de costes y no hay datos de coste se muestra un aviso
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        if ((mostrar_info_costes == true) && (hay_datos_costes == false)) {
            jAlert(TLNT.Idiomas._("No se han podido calcular los costes"));
        }
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

    // Gráfica de consumos
    muestra_grafica_temporal_lineas_valores(
        id_grafica_consumos,
        null,
        TLNT.Idiomas._("Consumo") + " (" + unidad_medida_consumo + ")",
        etiquetas_consumos,
        grafica_consumos, null, INTERVALO_VALORES_HORA,
        null,
        fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
        0, false,
        max_consumo, true,
        2, unidad_medida_consumo,
        null,
        true,
        TIPO_LINEAS_VALORES_ESTANDAR,
        mostrar_indicadores_valores,
        false,
        mostrar_animaciones,
        anyadir_menus_contextuales);

    // Gráfica de consumos acumulados
    muestra_grafica_temporal_lineas_valores(
        id_grafica_consumos_acumulados,
        null,
        TLNT.Idiomas._("Consumo acumulado") + " (" + unidad_medida_consumo + ")",
        etiquetas_consumos_acumulados,
        grafica_consumos_acumulados, null, INTERVALO_VALORES_HORA,
        null,
        fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
        0, false,
        max_consumo_acumulado, true,
        2, unidad_medida_consumo,
        null,
        true,
        TIPO_LINEAS_VALORES_ESTANDAR,
        mostrar_indicadores_valores,
        false,
        mostrar_animaciones,
        anyadir_menus_contextuales);

    // Tabla de consumos
    $("#" + id_contenedor_tabla_consumos).html(tabla_consumos);
    if (anyadir_menus_contextuales == true) {
        var id_tabla_consumos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_consumos);
        var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
        anyade_menu_contextual(id_tabla_consumos, info_menu_contextual, TLNT.Idiomas._('Consumos'));
    }

    // Si hay datos de costes
    if (hay_datos_costes == true) {
        // Gráfica de costes
        muestra_elemento(id_grafica_costes);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_costes,
            null,
            TLNT.Idiomas._("Coste") + " (" + unidad_medida_coste + ")",
            etiquetas_costes,
            grafica_costes, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_coste, true,
            2, unidad_medida_coste,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);

        // Gráfica de costes acumulados
        muestra_elemento(id_grafica_costes_acumulados);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_costes_acumulados,
            null,
            TLNT.Idiomas._("Coste acumulado") + " (" + unidad_medida_coste + ")",
            etiquetas_costes,
            grafica_costes_acumulados, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_coste_acumulado, true,
            2, unidad_medida_coste,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);

        // Tabla de costes
        muestra_elemento(id_contenedor_tabla_costes);
        $("#" + id_contenedor_tabla_costes).html(tabla_costes);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_costes = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_costes);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_costes, info_menu_contextual, TLNT.Idiomas._('Costes'));
        }
    }
    else {
        oculta_elemento(id_grafica_costes);
        oculta_elemento(id_grafica_costes_acumulados);
        oculta_elemento(id_contenedor_tabla_costes);
    }
}
