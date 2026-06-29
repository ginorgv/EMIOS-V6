//
// Funciones para el dibujado de los informes de información (Actuadores)
//


// Dibujado del informe de información de acciones enviadas de actuadores
function dibuja_informe_actuadores_informacion_acciones_enviadas(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var min_valor_acumulado = datos.min_valor_acumulado;
    var max_valor_acumulado = datos.max_valor_acumulado;
    var grafica_valores_sensor = datos.grafica_valores_sensor;
    var campo_incremental = datos.campo_incremental;
    var grafica_valores_acumulados_sensor = datos.grafica_valores_acumulados_sensor;
    var unidad_medida = datos.unidad_medida;
    var lineas_verticales_acciones_enviadas = datos.lineas_verticales_acciones_enviadas;
    var lineas_verticales_errores_acciones_enviadas = datos.lineas_verticales_errores_acciones_enviadas;
    var grafica_acciones_enviadas = datos.grafica_acciones_enviadas;
    var clase_estado_persistente = datos.clase_estado_persistente;
    var lineas_verticales_comentarios = datos.lineas_verticales_comentarios;
    var tabla_acciones_enviadas = datos.tabla_acciones_enviadas;
    var limite_elementos_tabla_acciones_enviadas_superado = datos.limite_elementos_tabla_acciones_enviadas_superado;
    var tabla_comentarios = datos.tabla_comentarios;
    var numero_comentarios = datos.numero_comentarios;
    var nombre_clase_actuador = datos.nombre_clase_actuador;
    var destino_accion = datos.destino_accion;
    var nombre_destino_accion = datos.nombre_destino_accion;
    var nombre_sensor = datos.nombre_sensor;
    var nombre_campo = datos.nombre_campo;
    var descripcion_destino = datos.descripcion_destino;

    // Parámetros
    var fecha_inicio = parametros.fecha_inicio;
    var fecha_fin = parametros.fecha_fin;
    var hora_inicio = parametros.hora_inicio;
    var hora_fin = parametros.hora_fin;
    var clase_actuador = parametros.clase_actuador;
    var clase_sensor = parametros.clase_sensor;
    var campo = parametros.campo;
    var id_sensor = parametros.id_sensor;
    var intervalo_valores = parametros.intervalo_valores;
    var id_parametros_resultado_informe = parametros.id_parametros_resultado_informe;
    var id_grafica_valores_sensor = parametros.id_grafica_valores_sensor;
    var id_grafica_valores_acumulados_sensor = parametros.id_grafica_valores_acumulados_sensor;
    var id_grafica_acciones_enviadas = parametros.id_grafica_acciones_enviadas;
    var id_descripcion_destino = parametros.id_descripcion_destino;
    var id_contenedor_tabla_acciones_enviadas = parametros.id_contenedor_tabla_acciones_enviadas;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_actuadores_informacion(
        TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS,
        elementos_informe,
        parametros);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_ACTUADORES,
        TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS,
        destino_accion);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores_sensor = parametros.mostrar_grafica_valores_sensor;
    var mostrar_grafica_valores_acumulados_sensor = parametros.mostrar_grafica_valores_acumulados_sensor;
    var mostrar_grafica_acciones_enviadas = parametros.mostrar_grafica_acciones_enviadas;
    var mostrar_tabla_acciones_enviadas = parametros.mostrar_tabla_acciones_enviadas;
    var mostrar_descripcion_destino = parametros.mostrar_descripcion_destino;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Fechas de inicio y fin de consulta
    var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
    var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores_sensor == true) {
        muestra_elemento(id_grafica_valores_sensor);
    }
    if (mostrar_grafica_valores_acumulados_sensor == true) {
        muestra_elemento(id_grafica_valores_acumulados_sensor);
    }
    if (mostrar_grafica_acciones_enviadas == true) {
        muestra_elemento(id_grafica_acciones_enviadas);
    }
    if (mostrar_grafica_acciones_enviadas == true) {
        muestra_elemento(id_grafica_acciones_enviadas);
    }
    if (mostrar_descripcion_destino == true) {
        muestra_elemento(id_descripcion_destino);
    }
    if (mostrar_tabla_acciones_enviadas == true) {
        muestra_elemento(id_contenedor_tabla_acciones_enviadas);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }

    // Fechas de inicio y fin para acotar las fechas de adición y modificación de comentarios
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        $("#" + id_parametros_resultado_informe).attr("fecha_inicio_acciones", fecha_inicio);
        $("#" + id_parametros_resultado_informe).attr("hora_inicio_acciones", hora_inicio);
        $("#" + id_parametros_resultado_informe).attr("fecha_fin_acciones", fecha_fin);
        $("#" + id_parametros_resultado_informe).attr("hora_fin_acciones", hora_fin);
    }

    // Si el informe es Web y no es plantilla de informe
    // se muestra un aviso de que se ha superado el límite de activaciones de eventos en las tablas
    if ((tipo_informe == TIPO_INFORME_WEB_EMIOS) && (elementos_informe == null)) {
        if (limite_elementos_tabla_acciones_enviadas_superado == true) {
            jAlert(TLNT.Idiomas._("Se ha superado el número máximo de acciones enviadas en la tabla") +
                " (" + TLNT.Idiomas._("máximo") + ": " + NUMERO_MAXIMO_FILAS_TABLA_ACCIONES_ENVIADAS + ")");
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

    // Intervalo de valores (líneas y puntos)
    var mostrar_lineas_valores_sensor = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS: {
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores_sensor = false;
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
    }

    // Comprobación de datos disponibles de valores de sensor (sólo hay una serie)
    var hay_datos_valores_sensor = null;
    var mostrar_indicadores_valores_sensor = null;
    if (id_sensor != ID_NINGUNO) {
        var numero_valores_grafica_valores_sensor = dame_numero_maximo_valores_series_grafica(grafica_valores_sensor);
        hay_datos_valores_sensor = (numero_valores_grafica_valores_sensor > 0);
        mostrar_indicadores_valores_sensor = (numero_valores_grafica_valores_sensor <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);
    }

    // Líneas verticales de las gráfica de valores (acciones enviadas)
    if (lineas_verticales_acciones_enviadas != null) {
        var lineas_verticales_grafica_valores_sensor = [];
        for (var i = 0; i < lineas_verticales_acciones_enviadas.length; i++) {
            lineas_verticales_grafica_valores_sensor.push(
                {
                    valor: lineas_verticales_acciones_enviadas[i]["valor"],
                    tipo: TIPO_LINEA_GRAFICA_VERTICAL_CONTINUA,
                    color: lineas_verticales_acciones_enviadas[i]["color"],
                    texto_tooltip: lineas_verticales_acciones_enviadas[i]["texto_tooltip"]
                }
            );
        }
    }

    // Gráfica de valores de sensor
    if (mostrar_grafica_valores_sensor == true) {
        if ((id_sensor != ID_NINGUNO) &&
            (hay_datos_valores_sensor == true)) {
            // Se dibuja la gráfica
            var titulo_grafica_valores_sensor = nombre_campo;
            if (unidad_medida != "") {
                titulo_grafica_valores_sensor += " (" + unidad_medida + ")";
            }
            var ajuste_valor_minimo = dame_ajuste_valor_minimo_grafica_valores_sensor(clase_sensor, campo, min_valor);
            min_valor = ajuste_valor_minimo.valor_minimo;
            var ajustar_valor_minimo = ajuste_valor_minimo.ajustar_valor_minimo;
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_sensor,
                null,
                titulo_grafica_valores_sensor,
                [nombre_sensor],
                grafica_valores_sensor, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
                min_valor, ajustar_valor_minimo,
                max_valor, true,
                2, unidad_medida,
                lineas_verticales_grafica_valores_sensor,
                mostrar_lineas_valores_sensor,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores_sensor,
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
                if (id_sensor == ID_NINGUNO) {
                    $("#" + id_grafica_valores_sensor).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de valores de sensor (no hay sensor seleccionado)"));
                }
                else {
                    if (sin_datos_valores_sensor == false) {
                        $("#" + id_grafica_valores_sensor).html("<i class='icon-warning-sign color-rojo'></i> " +
                            TLNT.Idiomas._("No se muestra la gráfica de valores de sensor (no hay valores de sensor)"));
                    }
                }
            }
        }
    }

    // Gráfica de valores acumulados de sensor
    if (mostrar_grafica_valores_acumulados_sensor == true) {
        if ((id_sensor != ID_NINGUNO) &&
            (campo_incremental == true) &&
            (hay_datos_valores_sensor == true)) {
            // Se dibuja la gráfica
            var titulo_grafica_valores_acumulados_sensor = nombre_campo;
            if (unidad_medida != "") {
                titulo_grafica_valores_acumulados_sensor += " (" + unidad_medida + ")";
            }
            titulo_grafica_valores_acumulados_sensor += " (" + TLNT.Idiomas._("acumulado") + ")";
            var ajuste_valor_acumulado_minimo = dame_ajuste_valor_minimo_grafica_valores_sensor(clase_sensor, campo, min_valor_acumulado);
            min_valor_acumulado = ajuste_valor_acumulado_minimo.valor_minimo;
            var ajustar_valor_minimo_acumulado = ajuste_valor_acumulado_minimo.ajustar_valor_minimo;
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_acumulados_sensor,
                null,
                titulo_grafica_valores_acumulados_sensor,
                [nombre_sensor],
                grafica_valores_acumulados_sensor, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
                min_valor_acumulado, ajustar_valor_minimo_acumulado,
                max_valor_acumulado, true,
                2, unidad_medida,
                lineas_verticales_grafica_valores_sensor,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores_sensor,
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
                if (id_sensor == ID_NINGUNO) {
                    $("#" + id_grafica_valores_acumulados_sensor).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de valores acumulados de sensor (no hay sensor seleccionado)"));
                }
                else {
                    if (campo_incremental == false) {
                        $("#" + id_grafica_valores_acumulados_sensor).html("<i class='icon-warning-sign color-rojo'></i> " +
                            TLNT.Idiomas._("No se muestra la gráfica de valores acumulados de sensor (el campo es de tipo puntual)"));
                    }
                    else {
                        if (sin_datos_valores_sensor == false) {
                            $("#" + id_grafica_valores_acumulados_sensor).html("<i class='icon-warning-sign color-rojo'></i> " +
                                TLNT.Idiomas._("No se muestra la gráfica de valores acumulados de sensor (no hay valores de sensor)"));
                        }
                    }
                }
            }
        }
    }

    // Gráfica de acciones enviadas
    if (mostrar_grafica_acciones_enviadas == true) {
        // Ticks dependiendo de la clase de actuador
        var valor_tick_0 = null;
        var valor_tick_1 = null;
        var ticks_valores_acciones = null;
        switch (clase_actuador) {
            case CLASE_ACTUADOR_MENSAJE: {
                valor_tick_0 = 0;
                var valor_tick_0_5 = 0.5;
                valor_tick_1 = 1;
                ticks_valores_acciones = [
                    [valor_tick_0, ""],
                    [valor_tick_0_5, TLNT.Idiomas._("Mensaje")],
                    [valor_tick_1, ""]];
                break;
            }
            case CLASE_ACTUADOR_INTERRUPTOR:
            case CLASE_ACTUADOR_TELEPOSTE:
            case CLASE_ACTUADOR_LUZ_GRADUAL_4: {
                valor_tick_0 = -0.1;
                valor_tick_1 = 1.1;
                ticks_valores_acciones = [
                    [valor_tick_0, TLNT.Idiomas._("Apagado")],
                    [valor_tick_1, TLNT.Idiomas._("Encendido")]];
                break;
            }
            case CLASE_ACTUADOR_GENERICA: {
                valor_tick_0 = 0;
                valor_tick_1 = 1;
                ticks_valores_acciones = [
                    [valor_tick_0, "0"],
                    [valor_tick_1, "1"]];
                break;
            }
        }

        // Mostrar líneas de valores e indicadores de valores
        var numero_valores_grafica_acciones_enviadas = dame_numero_maximo_valores_series_grafica(grafica_acciones_enviadas);
        var mostrar_lineas_valores_actuador = (clase_estado_persistente == true);
        var mostrar_indicadores_valores_actuador = (mostrar_lineas_valores_actuador == false) ||
            (numero_valores_grafica_acciones_enviadas <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

        // Líneas verticales de la gráfica de acciones enviadas (errores en las acciones y comentarios)
        var lineas_verticales_grafica_acciones_enviadas = [];
        if (lineas_verticales_errores_acciones_enviadas != null) {
            for (var i = 0; i < lineas_verticales_errores_acciones_enviadas.length; i++) {
                lineas_verticales_grafica_acciones_enviadas.push(
                    {
                        valor: lineas_verticales_errores_acciones_enviadas[i]["valor"],
                        tipo: TIPO_LINEA_GRAFICA_VERTICAL_CONTINUA,
                        color: lineas_verticales_errores_acciones_enviadas[i]["color"],
                        texto_tooltip: lineas_verticales_errores_acciones_enviadas[i]["texto_tooltip"]
                    }
                );
            }
        }
        if (lineas_verticales_comentarios != null) {
            for (var i = 0; i < lineas_verticales_comentarios.length; i++) {
                lineas_verticales_grafica_acciones_enviadas.push(
                    {
                        valor: lineas_verticales_comentarios[i]["valor"],
                        tipo: TIPO_LINEA_GRAFICA_VERTICAL_CONTINUA,
                        color: lineas_verticales_comentarios[i]["color"],
                        texto_tooltip: lineas_verticales_comentarios[i]["texto_tooltip"]
                    }
                );
            }
        }

        // Gráfica de acciones enviadas (gráfica con comentarios)
        switch (tipo_informe) {
            case TIPO_INFORME_WEB_EMIOS: {
                var comentarios = parametros.comentarios;
                grafica_con_comentarios = (comentarios != COMENTARIOS_NINGUNO);
                break;
            }
            case TIPO_INFORME_FICHERO: {
                grafica_con_comentarios = false;
                break;
            }
        }
        var titulo_grafica = nombre_clase_actuador;
        switch (destino_accion) {
            case DESTINO_ACCION_GRUPO_ACTUADORES: {
                titulo_grafica += " (" + TLNT.Idiomas._("grupo") + ")";
                break;
            }
        }
        muestra_grafica_temporal_lineas_valores(
            id_grafica_acciones_enviadas,
            ALTURA_GRAFICA_ACCIONES_ENVIADAS,
            titulo_grafica,
            [nombre_destino_accion],
            grafica_acciones_enviadas, null, INTERVALO_VALORES_TIEMPO_REAL,
            ticks_valores_acciones,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
            valor_tick_0, false,
            valor_tick_1, false,
            2, "",
            lineas_verticales_grafica_acciones_enviadas,
            mostrar_lineas_valores_actuador,
            TIPO_LINEAS_VALORES_CUADRADAS,
            mostrar_indicadores_valores_actuador,
            true,
            mostrar_animaciones,
            anyadir_menus_contextuales);
        grafica_con_comentarios = false;
    }

    // Descripción de destino
    if (mostrar_descripcion_destino == true) {
        if (descripcion_destino != "") {
            $("#" + id_descripcion_destino).html(descripcion_destino);
        }
        else {
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_descripcion_destino);
            }
            else {
                cambia_clase_elemento(id_descripcion_destino, "texto-elemento-no-mostrado-informe");
                $("#" + id_descripcion_destino).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la descripción (no hay descripción)"));
            }
        }
    }

    // Tabla de acciones_enviadas
    if (mostrar_tabla_acciones_enviadas == true) {
        // Se dibuja la tabla de acciones enviadas
        if (tabla_acciones_enviadas != null) {
            // Se rellena la tabla
            $("#" + id_contenedor_tabla_acciones_enviadas).html(tabla_acciones_enviadas);

            // Menú contextual de la tabla
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                var id_tabla_acciones_enviadas = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_acciones_enviadas);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_acciones_enviadas, info_menu_contextual, TLNT.Idiomas._('Acciones enviadas'));
            }
        }
    }

    // Tabla de comentarios
    if (mostrar_tabla_comentarios == true) {
        // Se dibuja la tabla de comentarios
        if (tabla_comentarios != null) {
            // Se rellena la tabla
            $("#" + id_contenedor_tabla_comentarios).html(tabla_comentarios);

            // Menú contextual de la tabla y eventos para administración de comentarios
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                var id_tabla_comentarios = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_comentarios);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_comentarios, info_menu_contextual, TLNT.Idiomas._('Comentarios'));
                TLNT.Navegacion.establece_eventos_tablas_datos_informes_actuadores();
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_comentarios);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_comentarios, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_comentarios).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de comentarios (no hay comentarios)"));
            }
        }
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_actuadores_informacion(
    tipo_informe_sensores_estadistica,
    elementos_informe,
    parametros) {
    switch (tipo_informe_sensores_estadistica) {
        case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS: {
            var mostrar_grafica_valores_sensor = true;
            var mostrar_grafica_valores_acumulados_sensor = true;
            var mostrar_grafica_acciones_enviadas = true;
            var mostrar_descripcion_destino = true;
            var mostrar_tabla_acciones_enviadas = true;
            var mostrar_tabla_comentarios = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_VALORES_SENSOR) == -1) {
                    mostrar_grafica_valores_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_VALORES_ACUMULADOS_SENSOR) == -1) {
                    mostrar_grafica_valores_acumulados_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_ACCIONES_ENVIADAS) == -1) {
                    mostrar_grafica_acciones_enviadas = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESCRIPCION_DESTINO) == -1) {
                    mostrar_descripcion_destino = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TABLA_ACCIONES_ENVIADAS) == -1) {
                    mostrar_tabla_acciones_enviadas = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
            }
            parametros.mostrar_grafica_valores_sensor = mostrar_grafica_valores_sensor;
            parametros.mostrar_grafica_valores_acumulados_sensor = mostrar_grafica_valores_acumulados_sensor;
            parametros.mostrar_grafica_acciones_enviadas = mostrar_grafica_acciones_enviadas;
            parametros.mostrar_descripcion_destino = mostrar_descripcion_destino;
            parametros.mostrar_tabla_acciones_enviadas = mostrar_tabla_acciones_enviadas;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;

            // Indica si hay elementos visibles
            var hay_elementos_visibles =
                (mostrar_grafica_acciones_enviadas == true) ||
                (mostrar_tabla_acciones_enviadas == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Actuadores - Información de acciones enviadas)
function dibuja_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas(
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
        $("#elemento-sin-destino-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de destino seleccionado
    var sin_destino_seleccionado = datos_elemento.sin_destino_seleccionado;
    if (sin_destino_seleccionado == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-destino-seleccionado-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-destino-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Parámetros del elemento
    var fecha_inicio = parametros_elemento.fecha_inicio;
    var fecha_fin = parametros_elemento.fecha_fin;
    var hora_inicio = parametros_elemento.hora_inicio;
    var hora_fin = parametros_elemento.hora_fin;

    // Parámetros del tipo del informe
    var clase_actuador = parametros_tipo["clase_actuador"];
    var clase_sensor = parametros_tipo["clase_sensor"];
    var campo = parametros_tipo["campo"];
    var id_sensor = parametros_tipo["id_sensor"];
    var intervalo_valores = parametros_tipo["intervalo_valores"];
    var comentarios = parametros_tipo["comentarios"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-acciones-enviadas";
    var id_grafica_valores_sensor = prefijo_elemento + "grafica-valores-sensor-informacion-acciones-enviadas";
    var id_grafica_valores_acumulados_sensor = prefijo_elemento + "grafica-valores-acumulados-sensor-informacion-acciones-enviadas";
    var id_grafica_acciones_enviadas = prefijo_elemento + "grafica-acciones-enviadas-informacion-acciones-enviadas";
    var id_descripcion_destino = prefijo_elemento + "descripcion-destino-informacion-acciones-enviadas";
    var id_contenedor_tabla_acciones_enviadas = prefijo_elemento + "contenedor-tabla-acciones-enviadas-informacion-acciones-enviadas";
    var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-actuador-informacion-acciones-enviadas";

    var parametros = {
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin,
        hora_inicio: hora_inicio,
        hora_fin: hora_fin,
        clase_actuador: clase_actuador,
        clase_sensor: clase_sensor,
        campo: campo,
        id_sensor: id_sensor,
        intervalo_valores: intervalo_valores,
        id_parametros_resultado_informe: id_parametros_resultado_informe,
        id_grafica_valores_sensor: id_grafica_valores_sensor,
        id_grafica_valores_acumulados_sensor: id_grafica_valores_acumulados_sensor,
        id_grafica_acciones_enviadas: id_grafica_acciones_enviadas,
        id_descripcion_destino: id_descripcion_destino,
        id_contenedor_tabla_acciones_enviadas: id_contenedor_tabla_acciones_enviadas,
        comentarios: comentarios,
        id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios};
    dibuja_informe_actuadores_informacion_acciones_enviadas(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


