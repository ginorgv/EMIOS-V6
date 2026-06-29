//
// Funciones para el dibujado de los informes de potencias (SmartMeter) (España)
//


// Dibujado del informe de optimización de potencias
function dibuja_informe_smartmeter_optimizador_potencias_Espanya(
    tipo_optimizador_potencias,
    parametros,
    datos,
    tipo_informe) {
    // Flag de gráfica de potencias
    var hay_grafica_potencias = (tipo_optimizador_potencias == TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_AUTOMATICO);

    // Datos del resultado
    var msg_aviso = datos.msg_aviso;
    var paso_maximo_potencias = datos.paso_maximo_potencias;
    var tabla_potencias_optimas_tramos = datos.tabla_potencias_optimas_tramos;
    var datos_potencias_tramos = datos.datos_potencias_tramos;
    var potencia_minima = datos.potencia_minima;
    var potencia_maxima = datos.potencia_maxima;
    var potencias_actuales_optimas = datos.potencias_actuales_optimas;
    if (hay_grafica_potencias == true) {
        var min_potencia = datos.min_potencia;
        var max_potencia = datos.max_potencia;
        var etiquetas_grafica_potencias = datos.etiquetas_grafica_potencias;
        var grafica_potencias = datos.grafica_potencias;
        var intervalo_valores = datos.intervalo_valores;
    }

    // Parámetros
    var id_contenedor_tabla_potencias_optimas_tramos = parametros.id_contenedor_tabla_potencias_optimas_tramos;
    var id_graficas_costes_potencias_tramos = parametros.id_graficas_costes_potencias_tramos;
    if (hay_grafica_potencias == true) {
        var id_grafica_potencias = parametros.id_grafica_potencias;
    }

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS,
        null);

    // Avisos a mostrar
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        if (msg_aviso != "") {
            jAlert(msg_aviso);
        }
        if (paso_maximo_potencias > 1) {
            jAlert(TLNT.Idiomas._("El error máximo de las potencias calculadas es de") + " " + paso_maximo_potencias + " " + TLNT.Idiomas._("kW"));
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

    // Tabla de potencias óptimas (por tramo)
    $("#" + id_contenedor_tabla_potencias_optimas_tramos).html(tabla_potencias_optimas_tramos);
    if (anyadir_menus_contextuales == true) {
        var id_tabla_potencias_optimas_tramos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_potencias_optimas_tramos);
        var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
        anyade_menu_contextual(id_tabla_potencias_optimas_tramos, info_menu_contextual, TLNT.Idiomas._('Potencias óptimas por tramo'));
    }

    // Gráfica de potencias
    if (hay_grafica_potencias == true) {
        // Mostrar indicadores de valores
        var numero_valores_grafica_potencias = dame_numero_maximo_valores_series_grafica(grafica_potencias);
        var mostrar_indicadores_valores = (numero_valores_grafica_potencias <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

        var titulo_grafica_potencias = TLNT.Idiomas._("Potencia") + " (" + TLNT.Idiomas._("kW") + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_potencias,
            null,
            titulo_grafica_potencias,
            etiquetas_grafica_potencias,
            grafica_potencias, null, intervalo_valores,
            null,
            null, null, false,
            0, false,
            max_potencia, true,
            2, TLNT.Idiomas._("kW"),
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Se muestran o ocultan las gráficas de costes de potencias (por tramo)
    var numero_tramos_tarifa_electrica = datos_potencias_tramos.length;
    for (var i = 1; i <= numero_tramos_tarifa_electrica; i++) {
        muestra_elemento(id_graficas_costes_potencias_tramos + "-" + i);
    }
    for (var i = (numero_tramos_tarifa_electrica + 1); i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; i++) {
        oculta_elemento(id_graficas_costes_potencias_tramos + "-" + i);
    }

    // Gráficas de costes de potencias (por tramo)
    for (var i = 0; i < numero_tramos_tarifa_electrica; i++) {
        var tramo = datos_potencias_tramos[i]["tramo"];
        var potencias_costes = datos_potencias_tramos[i]["potencias_costes"];

        // Valores mínimo y máximo del eje x (potencia) y del eje y (coste);
        var coste_minimo = datos_potencias_tramos[i]["coste_minimo_potencia"];
        var coste_maximo = datos_potencias_tramos[i]["coste_maximo_potencia"];
        var potencia_actual = datos_potencias_tramos[i]["potencia_actual"];
        var potencia_optima = datos_potencias_tramos[i]["potencia_seleccionada"];
        var coste_actual = datos_potencias_tramos[i]["coste_potencia_actual"];
        var coste_optimo = datos_potencias_tramos[i]["coste_potencia_seleccionada"];

        // Si las potencias actuales son óptimas, las potencias óptimas son las actuales
        if (potencias_actuales_optimas == true) {
            potencia_optima = potencia_actual;
            coste_optimo = datos_potencias_tramos[i]["coste_potencia_actual"];
        }

        // Puntos de potencias a mostrar en las gráficas
        var puntos_potencias = [];
        puntos_potencias.push(
            {
                x: potencia_optima,
                y: coste_optimo,
                icono: ICONO_PUNTO_GRAFICA_CUADRADO,
                nombre: TLNT.Idiomas._("potencia óptima")
            }
        );
        puntos_potencias.push(
            {
                x: potencia_actual,
                y: coste_actual,
                icono: ICONO_PUNTO_GRAFICA_X,
                nombre: TLNT.Idiomas._("potencia actual")
            }
        );

        // Nota: Si se muestran menos del número mínimo de 'ticks' en el eje X, se establece el intervalo de 'ticks' a 1
        // (para no mostrar varios ticks con el mismo valor)
        var intervalo_potencias = null;
        if ((potencia_maxima - potencia_minima) <= NUMERO_MINIMO_TICKS_POTENCIAS_TRAMOS) {
            intervalo_potencias = 1;
        }

        // Dibujado de la gráfica
        var div_grafica = id_graficas_costes_potencias_tramos + "-" + tramo;
        var titulo_grafica = TLNT.Idiomas._("Costes de potencias en tramo") + " " + tramo + " (" + TLNT.Idiomas._("kW") + " - " + moneda + ")";
        muestra_grafica_lineal_lineas_valores_puntos(
            div_grafica,
            titulo_grafica,
            null,
            potencias_costes,
            potencia_minima, potencia_maxima, intervalo_potencias, TLNT.Idiomas._("kW"),
            coste_minimo, true,
            coste_maximo, true,
            2, moneda,
            puntos_potencias,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }
}


// Dibujado del informe de simulación de potencias
function dibuja_informe_smartmeter_simulador_potencias_Espanya(
    tipo_simulador_potencias,
    parametros,
    datos,
    tipo_informe) {
    // Flag de gráfica de potencias
    var hay_grafica_potencias = (tipo_simulador_potencias == TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_AUTOMATICO);

    // Datos del resultado
    var msg_aviso = datos.msg_aviso;
    var paso_maximo_potencias = datos.paso_maximo_potencias;
    var tabla_potencias_seleccionadas_tramos = datos.tabla_potencias_seleccionadas_tramos;
    var datos_potencias_tramos = datos.datos_potencias_tramos;
    var potencia_minima = datos.potencia_minima;
    var potencia_maxima = datos.potencia_maxima;
    if (hay_grafica_potencias == true) {
        var min_potencia = datos.min_potencia;
        var max_potencia = datos.max_potencia;
        var etiquetas_grafica_potencias = datos.etiquetas_grafica_potencias;
        var grafica_potencias = datos.grafica_potencias;
        var intervalo_valores = datos.intervalo_valores;
    }

    // Parámetros
    var id_contenedor_tabla_potencias_seleccionadas_tramos = parametros.id_contenedor_tabla_potencias_seleccionadas_tramos;
    var id_graficas_costes_potencias_tramos = parametros.id_graficas_costes_potencias_tramos;
    if (hay_grafica_potencias == true) {
        var id_grafica_potencias = parametros.id_grafica_potencias;
    }

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS,
        null);

    // Aviso de paso de potencias
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        if (msg_aviso != "") {
            jAlert(msg_aviso);
        }
        if (paso_maximo_potencias > 1) {
            jAlert(TLNT.Idiomas._("El error máximo de las potencias calculadas es de") + " " + paso_maximo_potencias + " " + TLNT.Idiomas._("kW"));
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

    // Tabla de potencias seleccionadas (por tramo)
    $("#" + id_contenedor_tabla_potencias_seleccionadas_tramos).html(tabla_potencias_seleccionadas_tramos);
    if (anyadir_menus_contextuales == true) {
        var id_tabla_potencias_seleccionadas_tramos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_potencias_seleccionadas_tramos);
        var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
        anyade_menu_contextual(id_tabla_potencias_seleccionadas_tramos, info_menu_contextual, TLNT.Idiomas._('Potencias seleccionadas por tramo'));
    }

    // Gráfica de potencias
    if (hay_grafica_potencias == true) {
        // Mostrar indicadores de valores
        var numero_valores_grafica_potencias = dame_numero_maximo_valores_series_grafica(grafica_potencias);
        var mostrar_indicadores_valores = (numero_valores_grafica_potencias <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

        var titulo_grafica_potencias = TLNT.Idiomas._("Potencia") + " (" + TLNT.Idiomas._("kW") + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_potencias,
            null,
            titulo_grafica_potencias,
            etiquetas_grafica_potencias,
            grafica_potencias, null, intervalo_valores,
            null,
            null, null, false,
            0, false,
            max_potencia, true,
            2, TLNT.Idiomas._("kW"),
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Se muestran o ocultan las gráficas de costes de potencias (por tramo)
    var numero_tramos_tarifa_electrica = datos_potencias_tramos.length;
    for (var i = 1; i <= numero_tramos_tarifa_electrica; i++) {
        muestra_elemento(id_graficas_costes_potencias_tramos + "-" + i);
    }
    for (var i = (numero_tramos_tarifa_electrica + 1); i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; i++) {
        oculta_elemento(id_graficas_costes_potencias_tramos + "-" + i);
    }

    // Gráficas de costes de potencias (por tramo)
    for (var i = 0; i < numero_tramos_tarifa_electrica; i++) {
        var tramo = datos_potencias_tramos[i]["tramo"];
        var potencias_costes = datos_potencias_tramos[i]["potencias_costes"];

        // Valores mínimo y máximo del eje x (potencia) y del eje y (coste);
        var coste_minimo = datos_potencias_tramos[i]["coste_minimo_potencia"];
        var coste_maximo = datos_potencias_tramos[i]["coste_maximo_potencia"];
        var potencia_actual = datos_potencias_tramos[i]["potencia_actual"];
        var potencia_seleccionada = datos_potencias_tramos[i]["potencia_seleccionada"];
        var coste_actual = datos_potencias_tramos[i]["coste_potencia_actual"];
        var coste_seleccionado = datos_potencias_tramos[i]["coste_potencia_seleccionada"];

        // Puntos de potencias a mostrar en las gráficas
        var puntos_potencias = [];
        puntos_potencias.push(
            {
                x: potencia_seleccionada,
                y: coste_seleccionado,
                icono: ICONO_PUNTO_GRAFICA_CUADRADO,
                nombre: TLNT.Idiomas._("potencia seleccionada")
            }
        );
        puntos_potencias.push(
            {
                x: potencia_actual,
                y: coste_actual,
                icono: ICONO_PUNTO_GRAFICA_X,
                nombre: TLNT.Idiomas._("potencia actual")
            }
        );

        // Nota: Si se muestran menos del número mínimo de 'ticks' en el eje X, se establece el intervalo de 'ticks' a 1
        // (para no mostrar varios ticks con el mismo valor)
        var intervalo_potencias = null;
        if ((potencia_maxima - potencia_minima) <= NUMERO_MINIMO_TICKS_POTENCIAS_TRAMOS) {
            intervalo_potencias = 1;
        }

        // Dibujado de la gráfica
        var div_grafica = id_graficas_costes_potencias_tramos + "-" + tramo;
        var titulo_grafica = TLNT.Idiomas._("Costes de potencias en tramo") + " " + tramo + " (" + TLNT.Idiomas._("kW") + " - " + moneda + ")";
        muestra_grafica_lineal_lineas_valores_puntos(
            div_grafica,
            titulo_grafica,
            null,
            potencias_costes,
            potencia_minima, potencia_maxima, intervalo_potencias, TLNT.Idiomas._("kW"),
            coste_minimo, true,
            coste_maximo, true,
            2, moneda,
            puntos_potencias,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }
}





