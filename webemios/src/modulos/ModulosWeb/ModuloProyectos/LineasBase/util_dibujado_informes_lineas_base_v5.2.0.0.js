//
// Funciones para el dibujado de los informes de líneas base (Proyectos)
//


// Dibujado del informe de simulación de línea base
function dibuja_informe_proyectos_simulador_linea_base(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var min_fecha = new $.jsDate(datos.min_fecha);
    var max_fecha = new $.jsDate(datos.max_fecha);
    var grafica_valores = datos.grafica_valores;
    var bandas_valores = datos.bandas_valores;
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var etiquetas_valores = datos.etiquetas_valores;
    var grafica_diferencias = datos.grafica_diferencias;
    var min_diferencia = datos.min_diferencia;
    var max_diferencia = datos.max_diferencia;
    var campo_incremental = datos.campo_incremental;
    var grafica_diferencias_acumuladas = datos.grafica_diferencias_acumuladas;
    var min_diferencia_acumulada = datos.min_diferencia_acumulada;
    var max_diferencia_acumulada = datos.max_diferencia_acumulada;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;
    var intervalo_valores = datos.intervalo_valores;
    var tabla_error_coeficientes_linea_base = datos.tabla_error_coeficientes_linea_base;
    var tabla_errores_coeficientes_lineas_base_excepciones = datos.tabla_errores_coeficientes_lineas_base_excepciones;
    var lineas_verticales_comentarios = datos.lineas_verticales_comentarios;
    var tabla_comentarios = datos.tabla_comentarios;
    var numero_comentarios = datos.numero_comentarios;
    var fecha_inicio_valores = datos.fecha_inicio_valores;
    var hora_inicio_valores = datos.hora_inicio_valores;
    var fecha_fin_valores = datos.fecha_fin_valores;
    var hora_fin_valores = datos.hora_fin_valores;
    var nombre_sensor = datos.nombre_sensor;
    var descripcion_sensor = datos.descripcion_sensor;

    // Parámetros
    var id_parametros_resultado_informe = parametros.id_parametros_resultado_informe;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_grafica_diferencias = parametros.id_grafica_diferencias;
    var id_grafica_diferencias_acumuladas = parametros.id_grafica_diferencias_acumuladas;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_contenedor_tabla_error_coeficientes_linea_base = parametros.id_contenedor_tabla_error_coeficientes_linea_base;
    var id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones = parametros.id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_proyectos_lineas_base(
        TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_PROYECTOS,
        TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE,
        nombre_sensor);

    // Fechas de inicio y fin para acotar las fechas de adición y modificación de comentarios
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        $("#" + id_parametros_resultado_informe).attr("fecha_inicio_valores", fecha_inicio_valores);
        $("#" + id_parametros_resultado_informe).attr("hora_inicio_valores", hora_inicio_valores);
        $("#" + id_parametros_resultado_informe).attr("fecha_fin_valores", fecha_fin_valores);
        $("#" + id_parametros_resultado_informe).attr("hora_fin_valores", hora_fin_valores);
    }

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_grafica_diferencias = parametros.mostrar_grafica_diferencias;
    var mostrar_grafica_diferencias_acumuladas = parametros.mostrar_grafica_diferencias_acumuladas;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_tabla_error_coeficientes_linea_base = parametros.mostrar_tabla_error_coeficientes_linea_base;
    var mostrar_tabla_errores_coeficientes_lineas_base_excepciones = parametros.mostrar_tabla_errores_coeficientes_lineas_base_excepciones;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_grafica_diferencias == true) {
        muestra_elemento(id_grafica_diferencias);
    }
    if (mostrar_grafica_diferencias_acumuladas == true) {
        muestra_elemento(id_grafica_diferencias_acumuladas);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_tabla_error_coeficientes_linea_base == true) {
        muestra_elemento(id_contenedor_tabla_error_coeficientes_linea_base);
    }
    if (mostrar_tabla_errores_coeficientes_lineas_base_excepciones == true) {
        muestra_elemento(id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }

    // Gráficas con comentarios
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

    // Líneas de comentarios de las gráficas de información
    var lineas_comentarios = [];
    if (lineas_verticales_comentarios != null) {
        for (var i = 0; i < lineas_verticales_comentarios.length; i++) {
            lineas_comentarios.push(
                {
                    valor: lineas_verticales_comentarios[i]["valor"],
                    tipo: TIPO_LINEA_GRAFICA_VERTICAL_CONTINUA,
                    color: lineas_verticales_comentarios[i]["color"],
                    texto_tooltip: lineas_verticales_comentarios[i]["texto_tooltip"]
                }
            );
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
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        var titulo_grafica_valores = TLNT.Idiomas._("Valores");
        if (unidad_medida != "") {
            titulo_grafica_valores += " (" + unidad_medida + ")";
        }
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica_valores,
            etiquetas_valores,
            grafica_valores, bandas_valores, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
            min_valor, true,
            max_valor, true,
            numero_decimales_valores, unidad_medida,
            lineas_comentarios,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            true,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Línea de referencia de diferencia 0 a mostrar en la gráfica
    var lineas_referencia = [dame_linea_referencia_grafica_valores(
        0,
        TIPO_LINEA_GRAFICA_HORIZONTAL_DISCONTINUA,
        "rgba(75, 75, 75, 0.25)",
        TLNT.Idiomas._("Diferencia cero"))];

    // Líneas de las gráficas de diferencias
    var lineas_graficas_diferencias = null;
    if (lineas_comentarios != null) {
        lineas_graficas_diferencias = [];
        for (var i = 0; i < lineas_comentarios.length; i++) {
            lineas_graficas_diferencias.push(lineas_comentarios[i]);
        }
    }
    if (lineas_referencia != null) {
        if (lineas_graficas_diferencias == null) {
            lineas_graficas_diferencias = [];
        }
        for (var i = 0; i < lineas_referencia.length; i++) {
            lineas_graficas_diferencias.push(lineas_referencia[i]);
        }
    }

    // Gráfica de diferencias
    if (mostrar_grafica_diferencias == true) {
        var titulo_grafica_diferencias = TLNT.Idiomas._("Diferencias");
        if (unidad_medida != "") {
            titulo_grafica_diferencias += " (" + unidad_medida + ")";
        }
        muestra_grafica_temporal_lineas_valores(
            id_grafica_diferencias,
            null,
            titulo_grafica_diferencias,
            null,
            grafica_diferencias, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
            min_diferencia, true,
            max_diferencia, true,
            numero_decimales_valores, unidad_medida,
            lineas_graficas_diferencias,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de diferencias acumuladas
    if (mostrar_grafica_diferencias_acumuladas == true) {
        if (campo_incremental == true) {
            var titulo_grafica_diferencias_acumuladas = TLNT.Idiomas._("Diferencias acumuladas");
            if (unidad_medida != "") {
                titulo_grafica_diferencias_acumuladas += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_diferencias_acumuladas,
                null,
                titulo_grafica_diferencias_acumuladas,
                null,
                grafica_diferencias_acumuladas, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
                min_diferencia_acumulada, true,
                max_diferencia_acumulada, true,
                numero_decimales_valores, unidad_medida,
                lineas_graficas_diferencias,
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
                oculta_elemento(id_grafica_diferencias_acumuladas);
            }
            else {
                cambia_clase_elemento(id_grafica_diferencias_acumuladas, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_diferencias_acumuladas).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de diferencias acumuladas (el campo es de tipo puntual)"));
            }
        }
    }

    // Descripción de sensor
    if (mostrar_descripcion_sensor == true) {
        if (descripcion_sensor != "") {
            $("#" + id_descripcion_sensor).html(descripcion_sensor);
        }
        else {
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_descripcion_sensor);
            }
            else {
                cambia_clase_elemento(id_descripcion_sensor, "texto-elemento-no-mostrado-informe");
                $("#" + id_descripcion_sensor).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la descripción (no hay descripción)"));
            }
        }
    }

    // Tabla de error de línea base
    if (mostrar_tabla_error_coeficientes_linea_base == true) {
        if (tabla_error_coeficientes_linea_base != null) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_error_coeficientes_linea_base).html(tabla_error_coeficientes_linea_base);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_error_coeficientes_linea_base = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_error_coeficientes_linea_base);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_error_coeficientes_linea_base, info_menu_contextual, TLNT.Idiomas._('Error y coeficientes de línea base'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_error_coeficientes_linea_base);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_error_coeficientes_linea_base, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_error_coeficientes_linea_base).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de error y coeficientes de línea base"));
            }
        }
    }

    // Tabla de errores de líneas base excepciones
    if (mostrar_tabla_errores_coeficientes_lineas_base_excepciones == true) {
        if (tabla_errores_coeficientes_lineas_base_excepciones != null) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones).html(tabla_errores_coeficientes_lineas_base_excepciones);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_errores_coeficientes_lineas_base_excepciones = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_errores_coeficientes_lineas_base_excepciones, info_menu_contextual, TLNT.Idiomas._('Errores y coeficientes de líneas base de excepciones'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de errores y coeficientes de líneas base de excepciones"));
            }
        }
    }

    // Tabla de comentarios
    if (mostrar_tabla_comentarios == true) {
        if (tabla_comentarios != null) {
            // Se rellena la tabla
            $("#" + id_contenedor_tabla_comentarios).html(tabla_comentarios);

            // Menú contextual de la tabla y eventos para administración de comentarios
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                var id_tabla_comentarios = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_comentarios);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_comentarios, info_menu_contextual, TLNT.Idiomas._('Comentarios'));
                TLNT.Navegacion.establece_eventos_tablas_datos_informes_smartmeter();
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
function anyade_parametros_elementos_visibles_informe_proyectos_lineas_base(
    tipo_informe_proyectos_lineas_base,
    elementos_informe,
    parametros,
    datos) {
    switch (tipo_informe_proyectos_lineas_base) {
        case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE: {
            var mostrar_grafica_valores = true;
            var mostrar_grafica_diferencias = true;
            var mostrar_grafica_diferencias_acumuladas = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_tabla_error_coeficientes_linea_base = true;
            var mostrar_tabla_errores_coeficientes_lineas_base_excepciones = true;
            var mostrar_tabla_comentarios = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_DIFERENCIAS) == -1) {
                    mostrar_grafica_diferencias = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_DIFERENCIAS_ACUMULADAS) == -1) {
                    mostrar_grafica_diferencias_acumuladas = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_ERROR_COEFICIENTES_LINEA_BASE) == -1) {
                    mostrar_tabla_error_coeficientes_linea_base = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_ERRORES_COEFICIENTES_LINEAS_BASE_EXCEPCIONES) == -1) {
                    mostrar_tabla_errores_coeficientes_lineas_base_excepciones = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_grafica_diferencias = mostrar_grafica_diferencias;
            parametros.mostrar_grafica_diferencias_acumuladas = mostrar_grafica_diferencias_acumuladas;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_tabla_error_coeficientes_linea_base = mostrar_tabla_error_coeficientes_linea_base;
            parametros.mostrar_tabla_errores_coeficientes_lineas_base_excepciones = mostrar_tabla_errores_coeficientes_lineas_base_excepciones;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;

            // Indica si hay elementos visibles
            var campo_incremental = datos.campo_incremental;
            var tabla_comentarios = datos.tabla_comentarios;
            var tabla_error_coeficientes_linea_base = datos.tabla_error_coeficientes_linea_base;
            var tabla_errores_coeficientes_lineas_base_excepciones = datos.tabla_errores_coeficientes_lineas_base_excepciones;
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                (mostrar_grafica_diferencias == true) ||
                ((mostrar_grafica_diferencias_acumuladas == true) && (campo_incremental == true)) ||
                ((mostrar_tabla_error_coeficientes_linea_base == true) && (tabla_error_coeficientes_linea_base != null)) ||
                ((mostrar_tabla_errores_coeficientes_lineas_base_excepciones == true) && (tabla_errores_coeficientes_lineas_base_excepciones != null)) ||
                ((mostrar_tabla_comentarios == true) && (tabla_comentarios != null));
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Proyectos - Simulador de línea base)
function dibuja_elemento_plantilla_informe_proyectos_simulador_linea_base(
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
        $("#elemento-sin-linea-base-seleccionada-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de línea base seleccionada
    var sin_linea_base_seleccionada = datos_elemento.sin_linea_base_seleccionada;
    if (sin_linea_base_seleccionada == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-linea-base-seleccionada-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-linea-base-seleccionada-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Parámetros del elemento
    var fecha_hora_inicio_consulta = parametros_elemento.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros_elemento.fecha_hora_fin_consulta;

    // Parámetros del tipo de informe
    var comentarios = parametros_tipo["comentarios"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-simulador-linea-base";
    var id_grafica_valores = prefijo_elemento + "grafica-valores-simulador-linea-base";
    var id_grafica_diferencias = prefijo_elemento + "grafica-diferencias-simulador-linea-base";
    var id_grafica_diferencias_acumuladas = prefijo_elemento + "grafica-diferencias-acumuladas-simulador-linea-base";
    var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-simulador-linea-base";
    var id_contenedor_tabla_error_coeficientes_linea_base = prefijo_elemento + "contenedor-tabla-error-coeficientes-linea-base-simulador-linea-base";
    var id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones = prefijo_elemento + "contenedor-tabla-errores-coeficientes-lineas-base-excepciones-simulador-linea-base";
    var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-simulador-linea-base";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        id_parametros_resultado_informe: id_parametros_resultado_informe,
        id_grafica_valores: id_grafica_valores,
        id_grafica_diferencias: id_grafica_diferencias,
        id_grafica_diferencias_acumuladas: id_grafica_diferencias_acumuladas,
        id_descripcion_sensor: id_descripcion_sensor,
        id_contenedor_tabla_error_coeficientes_linea_base: id_contenedor_tabla_error_coeficientes_linea_base,
        id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones: id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones,
        comentarios: comentarios,
        id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios};
    dibuja_informe_proyectos_simulador_linea_base(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}
