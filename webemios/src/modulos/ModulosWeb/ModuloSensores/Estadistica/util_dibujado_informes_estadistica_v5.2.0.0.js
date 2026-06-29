//
// Funciones para el dibujado de los informes de estadística (Sensores)
//


// Dibujado del informe de histograma de sensores
function dibuja_informe_sensores_histograma(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var etiquetas_grafica_histograma = datos.etiquetas_grafica_histograma;
    var grafica_histograma = datos.grafica_histograma;
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var max_probabilidad = datos.max_probabilidad;
    var tabla_medidas_estadisticas = datos.tabla_medidas_estadisticas;
    var tabla_percentiles = datos.tabla_percentiles;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var id_grafica_histograma = parametros.id_grafica_histograma;
    var id_contenedor_tabla_medidas_estadisticas = parametros.id_contenedor_tabla_medidas_estadisticas;
    var id_contenedor_tabla_percentiles = parametros.id_contenedor_tabla_percentiles;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_estadistica(
        TIPO_INFORME_SENSORES_HISTOGRAMA,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_HISTOGRAMA,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_histograma = parametros.mostrar_grafica_histograma;
    var mostrar_tabla_medidas_estadisticas = parametros.mostrar_tabla_medidas_estadisticas;
    var mostrar_tabla_percentiles = parametros.mostrar_tabla_percentiles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_histograma == true) {
        muestra_elemento(id_grafica_histograma);
    }
    if (mostrar_tabla_medidas_estadisticas == true) {
        muestra_elemento(id_contenedor_tabla_medidas_estadisticas);
    }
    if (mostrar_tabla_percentiles == true) {
        muestra_elemento(id_contenedor_tabla_percentiles);
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

    // Gráfica de histograma
    if (mostrar_grafica_histograma == true) {
        var titulo_grafica_histograma_valores = TLNT.Idiomas._("Histograma") + " (" + TLNT.Idiomas._("probabilidad");
        if (unidad_medida != "") {
            titulo_grafica_histograma_valores += " - " + unidad_medida;
        }
        titulo_grafica_histograma_valores += ")";
        muestra_grafica_histograma_valores(
            id_grafica_histograma,
            titulo_grafica_histograma_valores,
            etiquetas_grafica_histograma,
            grafica_histograma,
            min_valor, max_valor,
            max_probabilidad, true,
            2,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de medidas estadísticas
    if (mostrar_tabla_medidas_estadisticas == true) {
        $("#" + id_contenedor_tabla_medidas_estadisticas).html(tabla_medidas_estadisticas);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_medidas_estadisticas = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_medidas_estadisticas);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_medidas_estadisticas, info_menu_contextual, TLNT.Idiomas._('Medidas estadísticas'));
        }
    }

    // Tabla de percentiles
    if (mostrar_tabla_percentiles == true) {
        $("#" + id_contenedor_tabla_percentiles).html(tabla_percentiles);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_percentiles = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_percentiles);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_percentiles, info_menu_contextual, TLNT.Idiomas._('Percentiles'));
        }
    }
}


// Dibujado del informe de correlación de sensores
function dibuja_informe_sensores_correlacion(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var tabla_funcion_correlacion = datos.tabla_funcion_correlacion;
    var etiquetas_grafica_correlacion = datos.etiquetas_grafica_correlacion;
    var grafica_correlacion = datos.grafica_correlacion;
    var min_valor_independiente = datos.min_valor_independiente;
    var max_valor_independiente = datos.max_valor_independiente;
    var min_valor_dependiente = datos.min_valor_dependiente;
    var max_valor_dependiente = datos.max_valor_dependiente;
    var unidad_medida_independiente = datos.unidad_medida_independiente;
    var unidad_medida_dependiente = datos.unidad_medida_dependiente;

    // Parámetros
    var id_grafica_correlacion = parametros.id_grafica_correlacion;
    var id_contenedor_tabla_funcion_correlacion = parametros.id_contenedor_tabla_funcion_correlacion;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_estadistica(
        TIPO_INFORME_SENSORES_CORRELACION,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_CORRELACION,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_correlacion = parametros.mostrar_grafica_correlacion;
    var mostrar_tabla_funcion_correlacion = parametros.mostrar_tabla_funcion_correlacion;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_correlacion == true) {
        muestra_elemento(id_grafica_correlacion);
    }
    if (mostrar_tabla_funcion_correlacion == true) {
        muestra_elemento(id_contenedor_tabla_funcion_correlacion);
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

    // Gráfica de correlación
    if (mostrar_grafica_correlacion == true) {
        if (grafica_correlacion != null) {
            // Se dibuja la gráfica
            var titulo_grafica = TLNT.Idiomas._("Correlación entre valores de sensores");
            if ((unidad_medida_independiente != "") && (unidad_medida_independiente != "")) {
                titulo_grafica += " (" + unidad_medida_independiente + " - " + unidad_medida_dependiente + ")";
            }
            muestra_grafica_correlacion_valores(
                id_grafica_correlacion,
                titulo_grafica,
                etiquetas_grafica_correlacion,
                grafica_correlacion,
                min_valor_independiente, max_valor_independiente, 2, unidad_medida_independiente,
                min_valor_dependiente, true,
                max_valor_dependiente, true,
                2, unidad_medida_dependiente,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_correlacion);
            }
            else {
                cambia_clase_elemento(id_grafica_correlacion, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_correlacion).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de correlación (es correlación multivariable)"));
            }
        }
    }

    // Tabla de función de correlación
    if (mostrar_tabla_funcion_correlacion == true) {
        $("#" + id_contenedor_tabla_funcion_correlacion).html(tabla_funcion_correlacion);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_funcion_correlacion = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_funcion_correlacion);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_funcion_correlacion, info_menu_contextual, TLNT.Idiomas._("Función de correlación"));
        }
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_sensores_estadistica(
    tipo_informe_sensores_estadistica,
    elementos_informe,
    parametros,
    datos) {
    switch (tipo_informe_sensores_estadistica) {
        case TIPO_INFORME_SENSORES_HISTOGRAMA: {
            var mostrar_grafica_histograma = true;
            var mostrar_tabla_medidas_estadisticas = true;
            var mostrar_tabla_percentiles = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_HISTOGRAMA_GRAFICA_HISTOGRAMA) == -1) {
                    mostrar_grafica_histograma = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_HISTOGRAMA_TABLA_MEDIDAS_ESTADISTICAS) == -1) {
                    mostrar_tabla_medidas_estadisticas = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_HISTOGRAMA_TABLA_PERCENTILES) == -1) {
                    mostrar_tabla_percentiles = false;
                }
            }
            parametros.mostrar_grafica_histograma = mostrar_grafica_histograma;
            parametros.mostrar_tabla_medidas_estadisticas = mostrar_tabla_medidas_estadisticas;
            parametros.mostrar_tabla_percentiles = mostrar_tabla_percentiles;
            break;
        }
        case TIPO_INFORME_SENSORES_CORRELACION: {
            var mostrar_grafica_correlacion = true;
            var mostrar_tabla_funcion_correlacion = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_CORRELACION_GRAFICA_CORRELACION) == -1) {
                    mostrar_grafica_correlacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_CORRELACION_TABLA_FUNCION_CORRELACION) == -1) {
                    mostrar_tabla_funcion_correlacion = false;
                }
            }
            parametros.mostrar_grafica_correlacion = mostrar_grafica_correlacion;
            parametros.mostrar_tabla_funcion_correlacion = mostrar_tabla_funcion_correlacion;

            // Indica si hay elementos visibles
            var grafica_correlacion = datos.grafica_correlacion;
            var hay_elementos_visibles =
                ((mostrar_grafica_correlacion == true) && (grafica_correlacion != null)) ||
                (mostrar_tabla_funcion_correlacion == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Sensores - Histograma)
function dibuja_elemento_plantilla_informe_sensores_histograma(
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

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_histograma = prefijo_elemento + "grafica-histograma";
    var id_contenedor_tabla_medidas_estadisticas = prefijo_elemento + "contenedor-tabla-medidas-estadisticas-histograma";
    var id_contenedor_tabla_percentiles = prefijo_elemento + "contenedor-tabla-percentiles-histograma";

    var parametros = {
        id_grafica_histograma: id_grafica_histograma,
        id_contenedor_tabla_medidas_estadisticas: id_contenedor_tabla_medidas_estadisticas,
        id_contenedor_tabla_percentiles: id_contenedor_tabla_percentiles};
    dibuja_informe_sensores_histograma(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Sensores - Correlación)
function dibuja_elemento_plantilla_informe_sensores_correlacion(
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
        $("#elemento-sin-sensores-seleccionados-elemento" + numero_elemento).hide();
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
    var id_grafica_correlacion = prefijo_elemento + "grafica-correlacion";
    var id_contenedor_tabla_funcion_correlacion = prefijo_elemento + "contenedor-tabla-funcion-correlacion";

    var parametros = {
        id_grafica_correlacion: id_grafica_correlacion,
        id_contenedor_tabla_funcion_correlacion: id_contenedor_tabla_funcion_correlacion};
    dibuja_informe_sensores_correlacion(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}

