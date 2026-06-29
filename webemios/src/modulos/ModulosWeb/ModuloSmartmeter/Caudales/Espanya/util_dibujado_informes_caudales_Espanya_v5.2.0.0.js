//
// Funciones para el dibujado de los informes de caudales (SmartMeter) (España)
//


// Dibujado del informe de optimización de caudales
function dibuja_informe_smartmeter_optimizador_caudales_Espanya(
    tipo_optimizador_caudales,
    parametros,
    datos,
    tipo_informe) {
    // Flag de gráfica de caudales diarios
    var hay_grafica_caudales_diarios = (tipo_optimizador_caudales == TIPO_OPTIMIZADOR_SIMULADOR_CAUDALES_AUTOMATICO);

    // Datos del resultado
    var paso_maximo_caudales_diarios = datos.paso_maximo_caudales_diarios;
    var tabla_caudal_diario_optimo = datos.tabla_caudal_diario_optimo;
    var caudales_diarios_costes = datos.caudales_diarios_costes;
    var coste_caudal_diario_optimo = datos.coste_caudal_diario_optimo;
    var caudal_diario_optimo = datos.caudal_diario_optimo;
    var coste_caudal_diario_actual = datos.coste_caudal_diario_actual;
    var caudal_diario_actual = datos.caudal_diario_actual;
    var coste_minimo_caudal_diario = datos.coste_minimo_caudal_diario;
    var coste_maximo_caudal_diario = datos.coste_maximo_caudal_diario;
    var caudal_diario_minimo = datos.caudal_diario_minimo;
    var caudal_diario_maximo = datos.caudal_diario_maximo;
    var caudal_diario_actual_optimo = datos.caudal_diario_actual_optimo;
    if (hay_grafica_caudales_diarios == true) {
        var min_caudal_diario = datos.min_caudal_diario;
        var max_caudal_diario = datos.max_caudal_diario;
        var etiquetas_grafica_caudales_diarios = datos.etiquetas_grafica_caudales_diarios;
        var grafica_caudales_diarios = datos.grafica_caudales_diarios;
    }

    // Parámetros
    var id_contenedor_tabla_caudal_diario_optimo = parametros.id_contenedor_tabla_caudal_diario_optimo;
    var id_grafica_caudales_diarios = parametros.id_grafica_caudales_diarios;
    var id_grafica_costes_caudales_diarios = parametros.id_grafica_costes_caudales_diarios;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES,
        null);

    // Aviso de paso de caudales diarios
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        if (paso_maximo_caudales_diarios > 1) {
            jAlert(TLNT.Idiomas._("El error máximo de los caudales diarios calculados es de") + " " + paso_maximo_caudales_diarios + " " + TLNT.Idiomas._("kWh"));
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

    // Tabla de caudal diario óptimo
    $("#" + id_contenedor_tabla_caudal_diario_optimo).html(tabla_caudal_diario_optimo);
    if (anyadir_menus_contextuales == true) {
        var id_tabla_caudal_diario_optimo = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_caudal_diario_optimo);
        var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
        anyade_menu_contextual(id_tabla_caudal_diario_optimo, info_menu_contextual, TLNT.Idiomas._('Caudal diario óptimo'));
    }

    // Gráfica de caudales_diarios
    if (hay_grafica_caudales_diarios == true) {
        // Mostrar indicadores de valores
        var numero_valores_grafica_caudales_diarios = dame_numero_maximo_valores_series_grafica(grafica_caudales_diarios);
        var mostrar_indicadores_valores = (numero_valores_grafica_caudales_diarios <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

        var titulo_grafica_potencias = TLNT.Idiomas._("Caudal diario") + " (" + TLNT.Idiomas._("kWh") + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_caudales_diarios,
            null,
            titulo_grafica_potencias,
            etiquetas_grafica_caudales_diarios,
            grafica_caudales_diarios, null, INTERVALO_VALORES_DIA,
            null,
            null, null, false,
            0, false,
            max_caudal_diario, true,
            2, TLNT.Idiomas._("kWh"),
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes de caudales diarios

    // Si las potencias actuales son óptimas, las potencias óptimas son las actuales
    if (caudal_diario_actual_optimo == true) {
        caudal_diario_optimo = caudal_diario_actual;
        coste_caudal_diario_optimo = coste_caudal_diario_actual;
    }

    // Puntos de caudales diarios a mostrar en las gráficas
    var puntos_caudales_diarios = [];
    puntos_caudales_diarios.push(
        {
            x: caudal_diario_optimo,
            y: coste_caudal_diario_optimo,
            icono: ICONO_PUNTO_GRAFICA_CUADRADO,
            nombre: TLNT.Idiomas._("caudal diario óptimo")
        }
    );
    puntos_caudales_diarios.push(
        {
            x: caudal_diario_actual,
            y: coste_caudal_diario_actual,
            icono: ICONO_PUNTO_GRAFICA_X,
            nombre: TLNT.Idiomas._("caudal diario actual")
        }
    );

    // Nota: Si se muestran menos del número mínimo de 'ticks' en el eje X, se establece el intervalo de 'ticks' a 1
    // (para no mostrar varios ticks con el mismo valor)
    var intervalo_caudales_diarios = null;
    if ((caudal_diario_maximo - caudal_diario_minimo) <= NUMERO_MINIMO_TICKS_CAUDALES) {
        intervalo_caudales_diarios = 1;
    }

    // Dibujado de la gráfica
    var titulo_grafica = TLNT.Idiomas._("Costes de caudales diarios") + " (" + TLNT.Idiomas._("kWh") + " - " + moneda + ")";
    muestra_grafica_lineal_lineas_valores_puntos(
        id_grafica_costes_caudales_diarios,
        titulo_grafica,
        null,
        caudales_diarios_costes,
        caudal_diario_minimo, caudal_diario_maximo, intervalo_caudales_diarios, TLNT.Idiomas._("kWh"),
        coste_minimo_caudal_diario, true,
        coste_maximo_caudal_diario, true,
        2, moneda,
        puntos_caudales_diarios,
        mostrar_animaciones,
        anyadir_menus_contextuales);
}


// Dibujado del informe de simulación de caudales
function dibuja_informe_smartmeter_simulador_caudales_Espanya(
    tipo_simulador_caudales,
    parametros,
    datos,
    tipo_informe) {
    // Flag de gráfica de caudales diarios
    var hay_grafica_caudales_diarios = (tipo_simulador_caudales == TIPO_OPTIMIZADOR_SIMULADOR_CAUDALES_AUTOMATICO);

    // Datos del resultado
    var paso_maximo_caudales_diarios = datos.paso_maximo_caudales_diarios;
    var tabla_caudal_diario_seleccionado = datos.tabla_caudal_diario_seleccionado;
    var caudales_diarios_costes = datos.caudales_diarios_costes;
    var coste_caudal_diario_seleccionado = datos.coste_caudal_diario_seleccionado;
    var caudal_diario_seleccionado = datos.caudal_diario_seleccionado;
    var coste_caudal_diario_actual = datos.coste_caudal_diario_actual;
    var caudal_diario_actual = datos.caudal_diario_actual;
    var coste_minimo_caudal_diario = datos.coste_minimo_caudal_diario;
    var coste_maximo_caudal_diario = datos.coste_maximo_caudal_diario;
    var caudal_diario_minimo = datos.caudal_diario_minimo;
    var caudal_diario_maximo = datos.caudal_diario_maximo;
    if (hay_grafica_caudales_diarios == true) {
        var min_caudal_diario = datos.min_caudal_diario;
        var max_caudal_diario = datos.max_caudal_diario;
        var etiquetas_grafica_caudales_diarios = datos.etiquetas_grafica_caudales_diarios;
        var grafica_caudales_diarios = datos.grafica_caudales_diarios;
    }

    // Parámetros
    var id_contenedor_tabla_caudal_diario_seleccionado = parametros.id_contenedor_tabla_caudal_diario_seleccionado;
    var id_grafica_caudales_diarios = parametros.id_grafica_caudales_diarios;
    var id_grafica_costes_caudales_diarios = parametros.id_grafica_costes_caudales_diarios;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES,
        null);

    // Aviso de paso de caudales diarios
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        if (paso_maximo_caudales_diarios > 1) {
            jAlert(TLNT.Idiomas._("El error máximo de los caudales diarios calculados es de") + " " + paso_maximo_caudales_diarios + " " + TLNT.Idiomas._("kWh"));
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

    // Tabla de caudal diario seleccionado
    $("#" + id_contenedor_tabla_caudal_diario_seleccionado).html(tabla_caudal_diario_seleccionado);
    if (anyadir_menus_contextuales == true) {
        var id_tabla_caudal_diario_optimo = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_caudal_diario_seleccionado);
        var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
        anyade_menu_contextual(id_tabla_caudal_diario_optimo, info_menu_contextual, TLNT.Idiomas._('Caudal diario seleccionado'));
    }

    // Gráfica de caudales_diarios
    if (hay_grafica_caudales_diarios == true) {
        // Mostrar indicadores de valores
        var numero_valores_grafica_caudales_diarios = dame_numero_maximo_valores_series_grafica(grafica_caudales_diarios);
        var mostrar_indicadores_valores = (numero_valores_grafica_caudales_diarios <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

        var titulo_grafica_potencias = TLNT.Idiomas._("Caudal diario") + " (" + TLNT.Idiomas._("kWh") + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_caudales_diarios,
            null,
            titulo_grafica_potencias,
            etiquetas_grafica_caudales_diarios,
            grafica_caudales_diarios, null, INTERVALO_VALORES_DIA,
            null,
            null, null, false,
            0, false,
            max_caudal_diario, true,
            2, TLNT.Idiomas._("kWh"),
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes de caudales diarios

    // Puntos de caudales diarios a mostrar en las gráficas
    var puntos_caudales_diarios = [];
    puntos_caudales_diarios.push(
        {
            x: caudal_diario_seleccionado,
            y: coste_caudal_diario_seleccionado,
            icono: ICONO_PUNTO_GRAFICA_CUADRADO,
            nombre: TLNT.Idiomas._("caudal diario seleccionado")
        }
    );
    puntos_caudales_diarios.push(
        {
            x: caudal_diario_actual,
            y: coste_caudal_diario_actual,
            icono: ICONO_PUNTO_GRAFICA_X,
            nombre: TLNT.Idiomas._("caudal diario actual")
        }
    );

    // Nota: Si se muestran menos del número mínimo de 'ticks' en el eje X, se establece el intervalo de 'ticks' a 1
    // (para no mostrar varios ticks con el mismo valor)
    var intervalo_caudales_diarios = null;
    if ((caudal_diario_maximo - caudal_diario_minimo) <= NUMERO_MINIMO_TICKS_CAUDALES) {
        intervalo_caudales_diarios = 1;
    }

    // Dibujado de la gráfica
    var titulo_grafica = TLNT.Idiomas._("Costes de caudales diarios") + " (" + TLNT.Idiomas._("kWh") + " - " + moneda + ")";
    muestra_grafica_lineal_lineas_valores_puntos(
        id_grafica_costes_caudales_diarios,
        titulo_grafica,
        null,
        caudales_diarios_costes,
        caudal_diario_minimo, caudal_diario_maximo, intervalo_caudales_diarios, TLNT.Idiomas._("kWh"),
        coste_minimo_caudal_diario, true,
        coste_maximo_caudal_diario, true,
        2, moneda,
        puntos_caudales_diarios,
        mostrar_animaciones,
        anyadir_menus_contextuales);
}
