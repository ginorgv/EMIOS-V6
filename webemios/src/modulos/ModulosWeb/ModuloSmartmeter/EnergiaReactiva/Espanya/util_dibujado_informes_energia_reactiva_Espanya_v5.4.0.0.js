//
// Funciones para el dibujado de los informes de energía reactiva (SmartMeter) (España)
//


// Dibujado del informe de simulación de batería de condensadores
function dibuja_informe_smartmeter_simulador_bateria_condensadores_Espanya(
    parametros,
    datos,
    tipo_informe) {
    // Datos del resultado
    var grafica_consumos_energia = datos.grafica_consumos_energia;
    var grafica_coseno_phi = datos.grafica_coseno_phi;
    var grafica_penalizable = datos.grafica_penalizable;
    var tabla_energia_reactiva_tramos = datos.tabla_energia_reactiva_tramos;
    var etiquetas_consumos_energia = datos.etiquetas_consumos_energia;
    var etiquetas_coseno_phi = datos.etiquetas_coseno_phi;
    var max_consumo = datos.max_consumo;
    var min_coseno_phi = datos.min_coseno_phi;
    var max_coseno_phi = datos.max_coseno_phi;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_consumos_energia = parametros.id_grafica_consumos_energia;
    var id_grafica_coseno_phi = parametros.id_grafica_coseno_phi;
    var id_grafica_penalizable = parametros.id_grafica_penalizable;
    var id_contenedor_tabla_energia_reactiva_tramos = parametros.id_contenedor_tabla_energia_reactiva_tramos;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_SIMULADOR_BATERIA_CONDENSADORES,
        null);

    // Comprobación de datos disponibles de penalizable (sólo hay una serie)
    var numero_valores_grafica_penalizable = dame_numero_maximo_valores_series_grafica(grafica_penalizable);
    var hay_datos_penalizable = (numero_valores_grafica_penalizable > 0);

    // Se muestran los elementos visibles
    if (hay_datos_penalizable == true) {
        muestra_elementos([
            id_contenedor_tabla_energia_reactiva_tramos,
            id_grafica_penalizable]);
    }
    else {
        oculta_elementos([
            id_contenedor_tabla_energia_reactiva_tramos,
            id_grafica_penalizable]);
    }
    muestra_elementos([
        id_grafica_consumos_energia,
        id_grafica_coseno_phi]);

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
    var numero_valores_grafica_consumos_energia = dame_numero_maximo_valores_series_grafica(grafica_consumos_energia);
    var mostrar_indicadores_valores = (numero_valores_grafica_consumos_energia <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Tabla de energía reactiva (por tramo)
    if (hay_datos_penalizable == true) {
        $("#" + id_contenedor_tabla_energia_reactiva_tramos).html(tabla_energia_reactiva_tramos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_energia_reactiva_tramos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_energia_reactiva_tramos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_energia_reactiva_tramos, info_menu_contextual, TLNT.Idiomas._('Energía reactiva por tramo'));
        }
    }

    // Gráfica de consumos de energía activa y reactiva
    muestra_grafica_temporal_lineas_valores(
        id_grafica_consumos_energia,
        null,
        TLNT.Idiomas._("Consumo"),
        etiquetas_consumos_energia,
        grafica_consumos_energia, null, INTERVALO_VALORES_HORA,
        null,
        fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
        0, false,
        max_consumo, true,
        2, "",
        null,
        true,
        TIPO_LINEAS_VALORES_ESTANDAR,
        mostrar_indicadores_valores,
        false,
        mostrar_animaciones,
        anyadir_menus_contextuales);

    // Gráfica de coseno de phi
    var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_COSENO_PHI);
    muestra_grafica_temporal_lineas_valores(
        id_grafica_coseno_phi,
        null,
        TLNT.Idiomas._("Coseno de phi") ,
        etiquetas_coseno_phi,
        grafica_coseno_phi, null, INTERVALO_VALORES_HORA,
        null,
        fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
        min_coseno_phi, true,
        max_coseno_phi, true,
        3, "",
        lineas_referencia,
        true,
        TIPO_LINEAS_VALORES_ESTANDAR,
        mostrar_indicadores_valores,
        false,
        mostrar_animaciones,
        anyadir_menus_contextuales);

    // Gráfica de penalizable
    if (hay_datos_penalizable == true) {
        muestra_grafica_temporal_valores_si_no(
            id_grafica_penalizable,
            TLNT.Idiomas._("Penalizable"),
            null,
            grafica_penalizable, INTERVALO_VALORES_HORA,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            null,
            true,
            TIPO_LINEAS_VALORES_CUADRADAS,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }
}



