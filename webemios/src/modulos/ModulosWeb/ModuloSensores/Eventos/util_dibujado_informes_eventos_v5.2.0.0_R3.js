//
// Funciones para el dibujado de los informes de eventos (Sensores)
//


// Dibujado del informe de activaciones de eventos
function dibuja_informe_sensores_activaciones_eventos(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var intervalo_valores = datos.intervalo_valores;
    var unidad_medida = datos.unidad_medida;
    var grafica_valores_sensor = datos.grafica_valores_sensor;
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var grafica_valores_acumulados_sensor = datos.grafica_valores_acumulados_sensor;
    var min_valor_acumulado = datos.min_valor_acumulado;
    var max_valor_acumulado = datos.max_valor_acumulado;
    var lineas_verticales_activaciones_eventos = datos.lineas_verticales_activaciones_eventos;
    var graficas_activaciones_eventos = datos.graficas_activaciones_eventos;
    var tablas_activaciones_eventos = datos.tablas_activaciones_eventos;
    var limite_elementos_tablas_activaciones_eventos_superado = datos.limite_elementos_tablas_activaciones_eventos_superado;
    var origenes_eventos = datos.origenes_eventos;
    var eventos_persistentes = datos.eventos_persistentes;
    var nombre_campo = datos.nombre_campo;
    var nombres_eventos = datos.nombres_eventos;
    var nombre_origen_eventos = datos.nombre_origen_eventos;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var clase_sensor = parametros.clase_sensor;
    var origen_evento = parametros.origen_evento;
    var campo = parametros.campo;
    var id_grafica_valores_sensor = parametros.id_grafica_valores_sensor;
    var id_grafica_valores_acumulados_sensor = parametros.id_grafica_valores_acumulados_sensor;
    var id_salto_pagina_graficas_valores_sensor_activaciones_eventos = parametros.id_salto_pagina_graficas_valores_sensor_activaciones_eventos;
    var id_graficas_activaciones_eventos = parametros.id_graficas_activaciones_eventos;
    var id_contenedores_tablas_activaciones_eventos = parametros.id_contenedores_tablas_activaciones_eventos;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_eventos(
        TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores_sensor = parametros.mostrar_grafica_valores_sensor;
    var mostrar_grafica_valores_acumulados_sensor = parametros.mostrar_grafica_valores_acumulados_sensor;
    var mostrar_graficas_activaciones_eventos = parametros.mostrar_graficas_activaciones_eventos;
    var mostrar_tablas_activaciones_eventos = parametros.mostrar_tablas_activaciones_eventos;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores_sensor == true) {
        muestra_elemento(id_grafica_valores_sensor);
    }
    if (mostrar_grafica_valores_acumulados_sensor == true) {
        muestra_elemento(id_grafica_valores_acumulados_sensor);
    }
    if (mostrar_graficas_activaciones_eventos == true) {
        for (var i = 1; i <= NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS; i++) {
            muestra_elemento(id_graficas_activaciones_eventos + "-" + i);
        }
    }
    if (mostrar_tablas_activaciones_eventos == true) {
        for (var i = 1; i <= NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS; i++) {
            muestra_elemento(id_contenedores_tablas_activaciones_eventos + "-" + i);
        }
    }

    // Número de valores de las gráficas de valores
    var numero_valores_grafica_valores_sensor = dame_numero_maximo_valores_series_grafica(grafica_valores_sensor);
    var numero_valores_grafica_valores_acumulados_sensor = dame_numero_maximo_valores_series_grafica(grafica_valores_acumulados_sensor);

    // Si el informe es Web y no es plantilla de informe
    // se muestra un aviso de que se ha superado el límite de activaciones de eventos en las tablas
    if ((tipo_informe == TIPO_INFORME_WEB_EMIOS) && (elementos_informe == null)) {
        if (limite_elementos_tablas_activaciones_eventos_superado == true) {
            jAlert(TLNT.Idiomas._("Se ha superado el número máximo de activaciones de eventos en las tablas") +
                " (" + TLNT.Idiomas._("máximo") + ": " + NUMERO_MAXIMO_FILAS_TABLAS_ACTIVACIONES_EVENTOS + ")");
        }
    }

    // Si el informe es fichero y no es plantilla de informe y sólo hay una gráfica de activaciones de eventos
    // no hay salto de página entre las gráficas de valores de sensor y las activaciones de eventos
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (elementos_informe == null)) {
        if (((numero_valores_grafica_valores_sensor == 0) && (numero_valores_grafica_valores_acumulados_sensor == 0)) ||
            (graficas_activaciones_eventos.length == 1)) {
            oculta_elemento(id_salto_pagina_graficas_valores_sensor_activaciones_eventos);
        }
        else {
            muestra_elemento(id_salto_pagina_graficas_valores_sensor_activaciones_eventos);
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
    var mostrar_indicadores_valores = (numero_valores_grafica_valores_sensor <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Líneas verticales de la gráfica de valores (activaciones de eventos)
    var lineas_verticales_grafica = [];
    if (lineas_verticales_activaciones_eventos != null) {
        for (var i = 0; i < lineas_verticales_activaciones_eventos.length; i++) {
            lineas_verticales_grafica.push(
                {
                    valor: lineas_verticales_activaciones_eventos[i]["valor"],
                    tipo: TIPO_LINEA_GRAFICA_VERTICAL_CONTINUA,
                    color: lineas_verticales_activaciones_eventos[i]["color"],
                    texto_tooltip: lineas_verticales_activaciones_eventos[i]["texto_tooltip"]
                }
            );
        }
    }

    // Gráfica de valores de sensor
    if (mostrar_grafica_valores_sensor == true) {
        if (numero_valores_grafica_valores_sensor > 0) {
            // Se dibuja la gráfica
            var titulo_grafica = nombre_campo;
            if (unidad_medida != "") {
                titulo_grafica += " (" + unidad_medida + ")";
            }
            var ajuste_valor_minimo = dame_ajuste_valor_minimo_grafica_valores_sensor(clase_sensor, campo, min_valor);
            var ajuste_valor_maximo = dame_ajuste_valor_maximo_grafica_valores_sensor(clase_sensor, campo, max_valor);
            min_valor = ajuste_valor_minimo.valor_minimo;
            max_valor = ajuste_valor_maximo.valor_maximo;
            var ajustar_valor_minimo = ajuste_valor_minimo.ajustar_valor_minimo;
            var ajustar_valor_maximo = ajuste_valor_maximo.ajustar_valor_maximo;
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_sensor,
                null,
                titulo_grafica,
                [nombre_origen_eventos],
                grafica_valores_sensor, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
                min_valor, ajustar_valor_minimo,
                max_valor, ajustar_valor_maximo,
                2, unidad_medida,
                lineas_verticales_grafica,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_valores_sensor);
            }
            else {
                cambia_clase_elemento(id_grafica_valores_sensor, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_valores_sensor).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de valores (no hay valores de sensor)"));
            }
        }
    }

    // Gráfica de valores acumulados de sensor
    if (mostrar_grafica_valores_acumulados_sensor == true) {
        if (numero_valores_grafica_valores_acumulados_sensor > 0) {
            // Se dibuja la gráfica
            var titulo_grafica = nombre_campo;
            if (unidad_medida != "") {
                titulo_grafica += " (" + unidad_medida + ")";
            }
            titulo_grafica += " (" + TLNT.Idiomas._("acumulado") + ")";
            var ajuste_valor_acumulado_minimo = dame_ajuste_valor_minimo_grafica_valores_sensor(clase_sensor, campo, min_valor_acumulado);
            min_valor_acumulado = ajuste_valor_acumulado_minimo.valor_minimo;
            var ajustar_valor_acumulado_minimo = ajuste_valor_acumulado_minimo.ajustar_valor_minimo;
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_acumulados_sensor,
                null,
                titulo_grafica,
                [nombre_origen_eventos],
                grafica_valores_acumulados_sensor, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
                min_valor_acumulado, ajustar_valor_acumulado_minimo,
                max_valor_acumulado, true,
                2, unidad_medida,
                lineas_verticales_grafica,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_valores_acumulados_sensor);
            }
            else {
                cambia_clase_elemento(id_grafica_valores_acumulados_sensor, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_valores_acumulados_sensor).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de valores acumulados (no hay valores acumulados de sensor)"));
            }
        }
    }

    // Gráficas de activaciones de eventos
    if (mostrar_graficas_activaciones_eventos == true) {
        for (var i = 1; i <= graficas_activaciones_eventos.length; i++) {
            // Se recupera la gráfica del evento
            // Nota: Si no hay datos, se añade un punto en la zona 'no visible' para poder dibujar la gráfica
            var grafica_activaciones_evento = graficas_activaciones_eventos[i - 1];
            if (grafica_activaciones_evento[0].length == 0) {
                grafica_activaciones_evento[0][0] = [0, -1, ""];
            }

            // Mostrar indicadores de valores
            var numero_valores_grafica_activaciones = grafica_activaciones_evento[0].length;
            var mostrar_indicadores_valores = (numero_valores_grafica_activaciones <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

            // Gráfica de activaciones de eventos
            var mostrar_lineas_valores = ((origenes_eventos[i - 1] == ORIGEN_EVENTO_SENSOR) && (eventos_persistentes[i - 1] == true));
            muestra_grafica_temporal_valores_si_no(
                id_graficas_activaciones_eventos + "-" + i,
                nombres_eventos[i - 1],
                null,
                grafica_activaciones_evento, INTERVALO_VALORES_TIEMPO_REAL,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
                null,
                mostrar_lineas_valores,
                TIPO_LINEAS_VALORES_CUADRADAS,
                mostrar_indicadores_valores,
                true,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }

        // Se ocultan las gráficas de activaciones de eventos que no se han mostrado
        for (var i = graficas_activaciones_eventos.length + 1; i <= NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS; i++) {
            oculta_elemento(id_graficas_activaciones_eventos + "-" + i);
        }
    }

    // Tablas de activaciones de eventos
    if (mostrar_tablas_activaciones_eventos == true) {
        for (var i = 1; i <= tablas_activaciones_eventos.length; i++) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedores_tablas_activaciones_eventos + "-" + i).html(tablas_activaciones_eventos[i - 1]);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_activaciones_evento = anyade_id_tabla_id_contenedor_tabla(id_contenedores_tablas_activaciones_eventos + "-" + i);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_activaciones_evento, info_menu_contextual, TLNT.Idiomas._('Activaciones de evento'));
            }
        }

        // Se ocultan las tablas de activaciones de eventos que no se han mostrado
        for (var i = tablas_activaciones_eventos.length + 1; i <= NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS; i++) {
            oculta_elemento(id_contenedores_tablas_activaciones_eventos + "-" + i);
        }
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_sensores_eventos(
    tipo_informe_sensores_eventos,
    elementos_informe,
    parametros,
    datos) {
    switch (tipo_informe_sensores_eventos) {
        case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS: {
            var mostrar_grafica_valores_sensor = true;
            var mostrar_grafica_valores_acumulados_sensor = true;
            var mostrar_graficas_activaciones_eventos = true;
            var mostrar_tablas_activaciones_eventos = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICA_VALORES_SENSOR) == -1) {
                    mostrar_grafica_valores_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICA_VALORES_ACUMULADOS_SENSOR) == -1) {
                    mostrar_grafica_valores_acumulados_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICAS_ACTIVACIONES_EVENTOS) == -1) {
                    mostrar_graficas_activaciones_eventos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TABLAS_ACTIVACIONES_EVENTOS) == -1) {
                    mostrar_tablas_activaciones_eventos = false;
                }
            }
            parametros.mostrar_grafica_valores_sensor = mostrar_grafica_valores_sensor;
            parametros.mostrar_grafica_valores_acumulados_sensor = mostrar_grafica_valores_acumulados_sensor;
            parametros.mostrar_graficas_activaciones_eventos = mostrar_graficas_activaciones_eventos;
            parametros.mostrar_tablas_activaciones_eventos = mostrar_tablas_activaciones_eventos;

            // Indica si hay elementos visibles
            var grafica_valores_sensor = datos.grafica_valores_sensor;
            var grafica_valores_acumulados_sensor = datos.grafica_valores_acumulados_sensor;
            var numero_valores_grafica_valores_sensor = dame_numero_maximo_valores_series_grafica(grafica_valores_sensor);
            var numero_valores_grafica_valores_acumulados_sensor = dame_numero_maximo_valores_series_grafica(grafica_valores_acumulados_sensor);
            var hay_elementos_visibles =
                ((mostrar_grafica_valores_sensor == true) && (numero_valores_grafica_valores_sensor > 0)) ||
                ((mostrar_grafica_valores_acumulados_sensor == true) && (numero_valores_grafica_valores_acumulados_sensor > 0)) ||
                (mostrar_graficas_activaciones_eventos == true) ||
                (mostrar_tablas_activaciones_eventos == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Sensores - Activaciones de eventos)
function dibuja_elemento_plantilla_informe_sensores_activaciones_eventos(
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
        $("#elemento-sin-eventos-seleccionados-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de eventos seleccionados
    var sin_eventos_seleccionados = datos_elemento.sin_eventos_seleccionados;
    if (sin_eventos_seleccionados == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-eventos-seleccionados-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Parámetros del elemento
    var fecha_hora_inicio_consulta = parametros_elemento.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros_elemento.fecha_hora_fin_consulta;

    // Clase de sensor, origen de evento y campo de sensor
    var clase_sensor = parametros_tipo["clase_sensor"];
    var origen_evento = parametros_tipo["origen_evento"];
    var campo = parametros_tipo["campo"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_valores_sensor = prefijo_elemento + "grafica-valores-sensor-activaciones-eventos";
    var id_grafica_valores_acumulados_sensor = prefijo_elemento + "grafica-valores-acumulados-sensor-activaciones-eventos";
    var id_graficas_activaciones_eventos = prefijo_elemento + "grafica-activaciones-evento-activaciones-eventos";
    var id_contenedores_tablas_activaciones_eventos = prefijo_elemento + "contenedor-tabla-activaciones-evento-activaciones-eventos";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        clase_sensor: clase_sensor,
        origen_evento: origen_evento,
        campo: campo,
        id_grafica_valores_sensor: id_grafica_valores_sensor,
        id_grafica_valores_acumulados_sensor: id_grafica_valores_acumulados_sensor,
        id_graficas_activaciones_eventos: id_graficas_activaciones_eventos,
        id_contenedores_tablas_activaciones_eventos: id_contenedores_tablas_activaciones_eventos};
    dibuja_informe_sensores_activaciones_eventos(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}

