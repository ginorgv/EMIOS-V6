//
// Funciones para el dibujado de los informes de información (Proyectos)
//


// Dibujado del informe de información de proyecto
function dibuja_informe_proyectos_informacion_proyecto(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var tabla_parametros_proyecto = datos.tabla_parametros_proyecto;
    var tabla_informacion_proyecto = datos.tabla_informacion_proyecto;
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
    var tabla_valores_adicionales_proyecto = datos.tabla_valores_adicionales_proyecto;
    var tabla_error_coeficientes_linea_base = datos.tabla_error_coeficientes_linea_base;
    var tabla_errores_coeficientes_lineas_base_excepciones = datos.tabla_errores_coeficientes_lineas_base_excepciones;

    // Parámetros
    var id_contenedor_tabla_parametros_proyecto = parametros.id_contenedor_tabla_parametros_proyecto;
    var id_contenedor_tabla_informacion_proyecto = parametros.id_contenedor_tabla_informacion_proyecto;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_grafica_diferencias = parametros.id_grafica_diferencias;
    var id_grafica_diferencias_acumuladas = parametros.id_grafica_diferencias_acumuladas;
    var id_contenedor_tabla_valores_adicionales_proyecto = parametros.id_contenedor_tabla_valores_adicionales_proyecto;
    var id_contenedor_tabla_error_coeficientes_linea_base = parametros.id_contenedor_tabla_error_coeficientes_linea_base;
    var id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones = parametros.id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_proyectos_informacion(
        TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_PROYECTOS,
        TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_tabla_parametros_proyecto = parametros.mostrar_tabla_parametros_proyecto;
    var mostrar_tabla_informacion_proyecto = parametros.mostrar_tabla_informacion_proyecto;
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_grafica_diferencias = parametros.mostrar_grafica_diferencias;
    var mostrar_grafica_diferencias_acumuladas = parametros.mostrar_grafica_diferencias_acumuladas;
    var mostrar_tabla_valores_adicionales_proyecto = parametros.mostrar_tabla_valores_adicionales_proyecto;
    var mostrar_tabla_error_coeficientes_linea_base = parametros.mostrar_tabla_error_coeficientes_linea_base;
    var mostrar_tabla_errores_coeficientes_lineas_base_excepciones = parametros.mostrar_tabla_errores_coeficientes_lineas_base_excepciones;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles
    if (mostrar_tabla_parametros_proyecto == true) {
        muestra_elemento(id_contenedor_tabla_parametros_proyecto);
    }
    if (mostrar_tabla_informacion_proyecto == true) {
        muestra_elemento(id_contenedor_tabla_informacion_proyecto);
    }
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_grafica_diferencias == true) {
        muestra_elemento(id_grafica_diferencias);
    }
    if (mostrar_grafica_diferencias_acumuladas == true) {
        muestra_elemento(id_grafica_diferencias_acumuladas);
    }
    if (mostrar_tabla_valores_adicionales_proyecto == true) {
        muestra_elemento(id_contenedor_tabla_valores_adicionales_proyecto);
    }
    if (mostrar_tabla_error_coeficientes_linea_base == true) {
        muestra_elemento(id_contenedor_tabla_error_coeficientes_linea_base);
    }
    if (mostrar_tabla_errores_coeficientes_lineas_base_excepciones == true) {
        muestra_elemento(id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones);
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

    // Tabla de parámetros de proyecto
    if (mostrar_tabla_parametros_proyecto == true) {
        $("#" + id_contenedor_tabla_parametros_proyecto).html(tabla_parametros_proyecto);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_parametros_proyecto = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_parametros_proyecto);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_parametros_proyecto, info_menu_contextual, TLNT.Idiomas._('Parámetros de proyecto'));
        }
    }

    // Tabla de información de proyecto
    if (mostrar_tabla_informacion_proyecto == true) {
        $("#" + id_contenedor_tabla_informacion_proyecto).html(tabla_informacion_proyecto);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_informacion_proyecto = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_informacion_proyecto);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_informacion_proyecto, info_menu_contextual, TLNT.Idiomas._('Información de proyecto'));
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
            min_fecha, max_fecha, true,
            min_valor, true,
            max_valor, true,
            numero_decimales_valores, unidad_medida,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            true,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Línea de referencia de diferencia 0 a mostrar en las gráficas de diferencias
    var lineas_referencia = [dame_linea_referencia_grafica_valores(
        0,
        TIPO_LINEA_GRAFICA_HORIZONTAL_DISCONTINUA,
        "rgba(75, 75, 75, 0.25)",
        TLNT.Idiomas._("Diferencia cero"))];

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
            min_fecha, max_fecha, true,
            min_diferencia, true,
            max_diferencia, true,
            numero_decimales_valores, unidad_medida,
            lineas_referencia,
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
            // Se dibuja la gráfica
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
                min_fecha, max_fecha, true,
                min_diferencia_acumulada, true,
                max_diferencia_acumulada, true,
                numero_decimales_valores, unidad_medida,
                lineas_referencia,
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

    // Tablas de valores adicionales de proyecto
    if (mostrar_tabla_valores_adicionales_proyecto == true) {
        if (tabla_valores_adicionales_proyecto != null) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_valores_adicionales_proyecto).html(tabla_valores_adicionales_proyecto);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_valores_adicionales_proyecto = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_valores_adicionales_proyecto);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_valores_adicionales_proyecto, info_menu_contextual, TLNT.Idiomas._('Valores adicionales'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_valores_adicionales_proyecto);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_valores_adicionales_proyecto, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_valores_adicionales_proyecto).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de valores adicionales de proyecto (no hay valores adicionales)"));
            }
        }
    }

    // Tabla de error y coeficientes de línea base
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

    // Tabla de errores y coeficientes de líneas base excepciones
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
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_proyectos_informacion(
    tipo_informe_proyectos_informacion,
    elementos_informe,
    parametros,
    datos) {
    switch (tipo_informe_proyectos_informacion) {
        case TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO: {
            var mostrar_tabla_parametros_proyecto = true;
            var mostrar_tabla_informacion_proyecto = true;
            var mostrar_grafica_valores = true;
            var mostrar_grafica_diferencias = true;
            var mostrar_grafica_diferencias_acumuladas = true;
            var mostrar_tabla_valores_adicionales_proyecto = true;
            var mostrar_tabla_error_coeficientes_linea_base = true;
            var mostrar_tabla_errores_coeficientes_lineas_base_excepciones = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_PARAMETROS_PROYECTO) == -1) {
                    mostrar_tabla_parametros_proyecto = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_INFORMACION_PROYECTO) == -1) {
                    mostrar_tabla_informacion_proyecto = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_DIFERENCIAS) == -1) {
                    mostrar_grafica_diferencias = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_DIFERENCIAS_ACUMULADAS) == -1) {
                    mostrar_grafica_diferencias_acumuladas = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_VALORES_ADICIONALES_PROYECTO) == -1) {
                    mostrar_tabla_valores_adicionales_proyecto = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_ERROR_COEFICIENTES_LINEA_BASE) == -1) {
                    mostrar_tabla_error_coeficientes_linea_base = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_ERRORES_COEFICIENTES_LINEAS_BASE_EXCEPCIONES) == -1) {
                    mostrar_tabla_errores_coeficientes_lineas_base_excepciones = false;
                }
            }
            parametros.mostrar_tabla_parametros_proyecto = mostrar_tabla_parametros_proyecto;
            parametros.mostrar_tabla_informacion_proyecto = mostrar_tabla_informacion_proyecto;
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_grafica_diferencias = mostrar_grafica_diferencias;
            parametros.mostrar_grafica_diferencias_acumuladas = mostrar_grafica_diferencias_acumuladas;
            parametros.mostrar_tabla_valores_adicionales_proyecto = mostrar_tabla_valores_adicionales_proyecto;
            parametros.mostrar_tabla_error_coeficientes_linea_base = mostrar_tabla_error_coeficientes_linea_base;
            parametros.mostrar_tabla_errores_coeficientes_lineas_base_excepciones = mostrar_tabla_errores_coeficientes_lineas_base_excepciones;

            // Indica si hay elementos visibles
            var campo_incremental = datos.campo_incremental;
            var tabla_valores_adicionales_proyecto = datos.tabla_valores_adicionales_proyecto;
            var tabla_error_coeficientes_linea_base = datos.tabla_error_coeficientes_linea_base;
            var tabla_errores_coeficientes_lineas_base_excepciones = datos.tabla_errores_coeficientes_lineas_base_excepciones;
            var hay_elementos_visibles =
                (mostrar_tabla_parametros_proyecto == true) ||
                (mostrar_tabla_informacion_proyecto == true) ||
                (mostrar_grafica_valores == true) ||
                (mostrar_grafica_diferencias == true) ||
                ((mostrar_grafica_diferencias_acumuladas == true) && (campo_incremental == true)) ||
                ((mostrar_tabla_valores_adicionales_proyecto == true) && (tabla_valores_adicionales_proyecto != null)) ||
                ((mostrar_tabla_error_coeficientes_linea_base == true) && (tabla_error_coeficientes_linea_base != null)) ||
                ((mostrar_tabla_errores_coeficientes_lineas_base_excepciones == true) && (tabla_errores_coeficientes_lineas_base_excepciones != null));
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Proyectos - Información de proyecto)
function dibuja_elemento_plantilla_informe_proyectos_informacion_proyecto(
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
        $("#elemento-sin-proyecto-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de proyecto seleccionado
    var sin_proyecto_seleccionado = datos_elemento.sin_proyecto_seleccionado;
    if (sin_proyecto_seleccionado == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-proyecto-seleccionado-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-proyecto-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_contenedor_tabla_parametros_proyecto = prefijo_elemento + "contenedor-tabla-parametros-proyecto-informacion-proyecto";
    var id_contenedor_tabla_informacion_proyecto = prefijo_elemento + "contenedor-tabla-informacion-proyecto-informacion-proyecto";
    var id_grafica_valores = prefijo_elemento + "grafica-valores-informacion-proyecto";
    var id_grafica_diferencias = prefijo_elemento + "grafica-diferencias-informacion-proyecto";
    var id_grafica_diferencias_acumuladas = prefijo_elemento + "grafica-diferencias-acumuladas-informacion-proyecto";
    var id_contenedor_tabla_valores_adicionales_proyecto = prefijo_elemento + "contenedor-tabla-valores-adicionales-proyecto-informacion-proyecto";
    var id_contenedor_tabla_error_coeficientes_linea_base = prefijo_elemento + "contenedor-tabla-error-coeficientes-linea-base-informacion-proyecto";
    var id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones = prefijo_elemento + "contenedor-tabla-errores-coeficientes-lineas-base-excepciones-informacion-proyecto";

    var parametros = {
        id_contenedor_tabla_parametros_proyecto: id_contenedor_tabla_parametros_proyecto,
        id_contenedor_tabla_informacion_proyecto: id_contenedor_tabla_informacion_proyecto,
        id_grafica_valores: id_grafica_valores,
        id_grafica_diferencias: id_grafica_diferencias,
        id_grafica_diferencias_acumuladas: id_grafica_diferencias_acumuladas,
        id_contenedor_tabla_valores_adicionales_proyecto: id_contenedor_tabla_valores_adicionales_proyecto,
        id_contenedor_tabla_error_coeficientes_linea_base: id_contenedor_tabla_error_coeficientes_linea_base,
        id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones: id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones};
    dibuja_informe_proyectos_informacion_proyecto(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}



