//
// Funciones para el dibujado de los informes de consumos y costes (SmartMeter)
//


// Dibujado del informe de consumos y costes generales
function dibuja_informe_smartmeter_consumos_costes_generales(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes(
        TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES,
        elementos_informe,
        parametros,
        datos);

    // Datos del resultado
    var hay_datos_costes = datos.hay_datos_costes;
    var limite_sensores_graficas_superado = datos.limite_sensores_graficas_superado;
    var grafica_consumos = datos.grafica_consumos;
    var grafica_consumos_acumulados = datos.grafica_consumos_acumulados;
    var tabla_consumos_maximos_minimos = datos.tabla_consumos_maximos_minimos;
    var grafica_costes = datos.grafica_costes;
    var grafica_costes_acumulados = datos.grafica_costes_acumulados;
    var tabla_costes_maximos_minimos = datos.tabla_costes_maximos_minimos;
    var grafica_precios = datos.grafica_precios;
    var tabla_precios_maximos_minimos = datos.tabla_precios_maximos_minimos;
    var lineas_verticales_comentarios = datos.lineas_verticales_comentarios;
    var tabla_comentarios = datos.tabla_comentarios;
    var numero_comentarios = datos.numero_comentarios;
    var fecha_inicio_consumos = datos.fecha_inicio_consumos;
    var hora_inicio_consumos = datos.hora_inicio_consumos;
    var fecha_fin_consumos = datos.fecha_fin_consumos;
    var hora_fin_consumos = datos.hora_fin_consumos;
    var max_consumo = datos.max_consumo;
    var max_coste = datos.max_coste;
    var max_precio = datos.max_precio;
    var max_consumos_totales = datos.max_consumos_totales;
    var max_costes_totales = datos.max_costes_totales;
    var etiquetas_consumos = datos.etiquetas_consumos;
    var etiquetas_costes = datos.etiquetas_costes;
    var unidad_medida_consumo = datos.unidad_medida_consumo;
    var unidad_medida_coste = datos.unidad_medida_coste;
    var unidad_medida_precio = datos.unidad_medida_precio;
    var descripciones_sensores = datos.descripciones_sensores;

    // Parámetros
    var id_parametros_resultado_informe = parametros.id_parametros_resultado_informe;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var ids_sensores = parametros.ids_sensores;
    var intervalo_valores = parametros.intervalo_valores;
    var agregacion = parametros.agregacion;
    var id_grafica_consumos = parametros.id_grafica_consumos;
    var id_grafica_consumos_acumulados = parametros.id_grafica_consumos_acumulados;
    var id_descripciones_sensores = parametros.id_descripciones_sensores;
    var id_contenedor_tabla_consumos_maximos_minimos = parametros.id_contenedor_tabla_consumos_maximos_minimos;
    var id_grafica_costes = parametros.id_grafica_costes;
    var id_grafica_costes_acumulados = parametros.id_grafica_costes_acumulados;
    var id_contenedor_tabla_costes_maximos_minimos = parametros.id_contenedor_tabla_costes_maximos_minimos;
    var id_grafica_precios = parametros.id_grafica_precios;
    var id_contenedor_tabla_precios_maximos_minimos = parametros.id_contenedor_tabla_precios_maximos_minimos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES,
        ids_sensores.join(","));

    // Fechas de inicio y fin para acotar las fechas de adición y modificación de comentarios
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        $("#" + id_parametros_resultado_informe).attr("fecha_inicio_consumos", fecha_inicio_consumos);
        $("#" + id_parametros_resultado_informe).attr("hora_inicio_consumos", hora_inicio_consumos);
        $("#" + id_parametros_resultado_informe).attr("fecha_fin_consumos", fecha_fin_consumos);
        $("#" + id_parametros_resultado_informe).attr("hora_fin_consumos", hora_fin_consumos);
    }

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_consumos = parametros.mostrar_grafica_consumos;
    var mostrar_grafica_consumos_acumulados = parametros.mostrar_grafica_consumos_acumulados;
    var mostrar_descripciones_sensores = parametros.mostrar_descripciones_sensores;
    var mostrar_tabla_consumos_maximos_minimos = parametros.mostrar_tabla_consumos_maximos_minimos;
    var mostrar_grafica_costes = parametros.mostrar_grafica_costes;
    var mostrar_grafica_costes_acumulados = parametros.mostrar_grafica_costes_acumulados;
    var mostrar_tabla_costes_maximos_minimos = parametros.mostrar_tabla_costes_maximos_minimos;
    var mostrar_grafica_precios = parametros.mostrar_grafica_precios;
    var mostrar_tabla_precios_maximos_minimos = parametros.mostrar_tabla_precios_maximos_minimos;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles
    if (mostrar_grafica_consumos == true) {
        muestra_elemento(id_grafica_consumos);
    }
    if (mostrar_grafica_consumos_acumulados == true) {
        muestra_elemento(id_grafica_consumos_acumulados);
    }
    if (mostrar_descripciones_sensores == true) {
        muestra_elemento(id_descripciones_sensores);
    }
    if (mostrar_tabla_consumos_maximos_minimos == true) {
        muestra_elemento(id_contenedor_tabla_consumos_maximos_minimos);
    }
    if (mostrar_grafica_costes == true) {
        muestra_elemento(id_grafica_costes);
    }
    if (mostrar_grafica_costes_acumulados == true) {
        muestra_elemento(id_grafica_costes_acumulados);
    }
    if (mostrar_tabla_costes_maximos_minimos == true) {
        muestra_elemento(id_contenedor_tabla_costes_maximos_minimos);
    }
    if (mostrar_grafica_precios == true) {
        muestra_elemento(id_grafica_precios);
    }
    if (mostrar_tabla_precios_maximos_minimos == true) {
        muestra_elemento(id_contenedor_tabla_precios_maximos_minimos);
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

    // Si el informe es Web y no es plantilla de informe
    // se muestra un aviso de que se ha superado el límite de sensores para el dibujado de gráficas
    if ((tipo_informe == TIPO_INFORME_WEB_EMIOS) && (elementos_informe == null)) {
        if (limite_sensores_graficas_superado == true) {
            jAlert(TLNT.Idiomas._("Se ha superado el número máximo de sensores para el dibujado de gráficas") +
                " (" + TLNT.Idiomas._("máximo") + ": " + NUMERO_MAXIMO_SENSORES_GRAFICAS_VALORES_GENERALES + ")");
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
    if (limite_sensores_graficas_superado == false) {
        var numero_valores_grafica_consumos = dame_numero_maximo_valores_series_grafica(grafica_consumos);
        var mostrar_indicadores_valores = (numero_valores_grafica_consumos <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);
    }

    // Flag de tooltips personalizados
    var tooltips_personalizados = (agregacion != AGREGACION_NINGUNA);

    // Gráfica de consumos
    if (mostrar_grafica_consumos == true) {
        // Si no se ha superado el número máximo de sensores de gráficas, se dibujan las gráficas
        if (limite_sensores_graficas_superado == false) {
            // Se dibuja la gráfica
            muestra_grafica_temporal_lineas_valores(
                id_grafica_consumos,
                null,
                TLNT.Idiomas._("Consumo") + " (" + unidad_medida_consumo + ")",
                etiquetas_consumos,
                grafica_consumos, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                max_consumo, true,
                2, unidad_medida_consumo,
                lineas_comentarios,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                tooltips_personalizados,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_consumos);
            }
            else {
                cambia_clase_elemento(id_grafica_consumos, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_consumos).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de consumos") +
                    " (" +  TLNT.Idiomas._("se ha superado el límite de sensores") + ")");
            }
        }
    }

    // Gráfica de consumos acumulados
    if (mostrar_grafica_consumos_acumulados == true) {
        // Si no se ha superado el número máximo de sensores de gráficas, se dibujan las gráficas
        if (limite_sensores_graficas_superado == false) {
            // Se dibuja la gráfica
            muestra_grafica_temporal_lineas_valores(
                id_grafica_consumos_acumulados,
                null,
                TLNT.Idiomas._("Consumo acumulado") + " (" + unidad_medida_consumo + ")",
                etiquetas_consumos,
                grafica_consumos_acumulados, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                max_consumos_totales, true,
                2, unidad_medida_consumo,
                lineas_comentarios,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                tooltips_personalizados,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_consumos_acumulados);
            }
            else {
                cambia_clase_elemento(id_grafica_consumos_acumulados, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_consumos_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de consumos acumulados") +
                    " (" +  TLNT.Idiomas._("se ha superado el límite de sensores") + ")");
            }
        }
    }

    // Descripciones de sensores
    if (mostrar_descripciones_sensores == true) {
        if (descripciones_sensores != "") {
            $("#" + id_descripciones_sensores).html(descripciones_sensores);
        }
        else {
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_descripciones_sensores);
            }
            else {
                cambia_clase_elemento(id_descripciones_sensores, "texto-elemento-no-mostrado-informe");
                $("#" + id_descripciones_sensores).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestran las descripciones (no hay descripciones)"));
            }
        }
    }

    // Tabla de consumos máximos, mínimos y media por hora
    if (mostrar_tabla_consumos_maximos_minimos == true) {
        $("#" + id_contenedor_tabla_consumos_maximos_minimos).html(tabla_consumos_maximos_minimos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_consumos_maximos_minimos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_consumos_maximos_minimos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_consumos_maximos_minimos, info_menu_contextual, TLNT.Idiomas._('Consumos máximos y mínimos'));
        }
    }

    // Gráfica de costes
    if (mostrar_grafica_costes == true) {
        // Si hay datos de costes y no se ha superado el número máximo de sensores de gráficas, se dibujan las gráficas
        if ((hay_datos_costes == true) && (limite_sensores_graficas_superado == false)) {
            // Se dibuja la gráfica
            muestra_grafica_temporal_lineas_valores(
                id_grafica_costes,
                null,
                TLNT.Idiomas._("Coste") + " (" + unidad_medida_coste + ")",
                etiquetas_costes,
                grafica_costes, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                max_coste, true,
                2, unidad_medida_coste,
                lineas_comentarios,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                tooltips_personalizados,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_costes);
            }
            else {
                cambia_clase_elemento(id_grafica_costes, "texto-elemento-no-mostrado-informe");
                if (hay_datos_costes == false) {
                    $("#" + id_grafica_costes).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de costes") +
                        " (" +  TLNT.Idiomas._("no hay datos de coste") + ")");
                }
                else {
                    $("#" + id_grafica_costes).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de costes") +
                        " (" +  TLNT.Idiomas._("se ha superado el límite de sensores") + ")");
                }
            }
        }
    }

    // Gráfica de costes acumulados
    if (mostrar_grafica_costes_acumulados == true) {
        // Si hay datos de costes y no se ha superado el número máximo de sensores de gráficas, se dibujan las gráficas
        if ((hay_datos_costes == true) && (limite_sensores_graficas_superado == false)) {
            // Se dibuja la gráfica
            muestra_grafica_temporal_lineas_valores(
                id_grafica_costes_acumulados,
                null,
                TLNT.Idiomas._("Coste acumulado") + " (" + unidad_medida_coste + ")",
                etiquetas_costes,
                grafica_costes_acumulados, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                max_costes_totales, true,
                2, unidad_medida_coste,
                lineas_comentarios,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                tooltips_personalizados,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_costes_acumulados);
            }
            else {
                cambia_clase_elemento(id_grafica_costes_acumulados, "texto-elemento-no-mostrado-informe");
                if (hay_datos_costes == false) {
                    $("#" + id_grafica_costes_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de costes acumulados") +
                        " (" +  TLNT.Idiomas._("no hay datos de coste") + ")");
                }
                else {
                    $("#" + id_grafica_costes_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de costes acumulados") +
                        " (" +  TLNT.Idiomas._("se ha superado el límite de sensores") + ")");
                }
            }
        }
    }

    // Tabla de costes máximos, mínimos y media por hora
    if (mostrar_tabla_costes_maximos_minimos == true) {
        if (hay_datos_costes == true) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_costes_maximos_minimos).html(tabla_costes_maximos_minimos);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_costes_maximos_minimos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_costes_maximos_minimos);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_costes_maximos_minimos, info_menu_contextual, TLNT.Idiomas._('Costes máximos y mínimos'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_costes_maximos_minimos);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_costes_maximos_minimos, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_costes_maximos_minimos).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de costes máximos y mínimos") +
                    " (" +  TLNT.Idiomas._("no hay datos de coste") + ")");
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

    // Gráfica de precios
    if (mostrar_grafica_precios == true) {
        // Si hay datos de costes y no se ha superado el número máximo de sensores de gráficas, se dibujan las gráficas
        if ((hay_datos_costes == true) && (limite_sensores_graficas_superado == false)) {
            // Se dibuja la tabla
            var tipo_lineas_valores = null;
            switch (intervalo_valores) {
                case INTERVALO_VALORES_CUARTOHORA:
                case INTERVALO_VALORES_HORA: {
                    tipo_lineas_valores = TIPO_LINEAS_VALORES_CUADRADAS;
                    break;
                }
                default: {
                    tipo_lineas_valores = TIPO_LINEAS_VALORES_ESTANDAR;
                    break;
                }
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_precios,
                null,
                TLNT.Idiomas._("Precio") + " (" + unidad_medida_precio + ")",
                etiquetas_costes,
                grafica_precios, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                max_precio, true,
                6, unidad_medida_precio,
                lineas_comentarios,
                true,
                tipo_lineas_valores,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_precios);
            }
            else {
                cambia_clase_elemento(id_grafica_precios, "texto-elemento-no-mostrado-informe");
                if (hay_datos_costes == false) {
                    $("#" + id_grafica_precios).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de precios (no hay datos de coste)"));
                }
                else {
                    $("#" + id_grafica_precios).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de precios (se ha superado el límite de sensores)"));
                }
            }
        }
    }

    // Tabla de precios máximos y mínimos
    if (mostrar_tabla_precios_maximos_minimos == true) {
        if (hay_datos_costes == true) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_precios_maximos_minimos).html(tabla_precios_maximos_minimos);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_precios_maximos_minimos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_precios_maximos_minimos);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_precios_maximos_minimos, info_menu_contextual, TLNT.Idiomas._('Precios máximos y mínimos'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_precios_maximos_minimos);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_precios_maximos_minimos, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_precios_maximos_minimos).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de precios máximos y mínimos (no hay datos de coste)"));
            }
        }
    }

    // Se desactiva el flag de gráficas con comentarios
    grafica_con_comentarios = false;
}


// Dibujado del informe de consumos y costes totales
function dibuja_informe_smartmeter_consumos_costes_totales(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var hay_datos_costes = datos.hay_datos_costes;
    var grafica_consumos_totales = datos.grafica_consumos_totales;
    var grafica_porcentajes_consumos = datos.grafica_porcentajes_consumos;
    var tabla_consumos = datos.tabla_consumos;
    var grafica_costes_totales = datos.grafica_costes_totales;
    var grafica_porcentajes_costes = datos.grafica_porcentajes_costes;
    var grafica_precios_medios = datos.grafica_precios_medios;
    var tabla_costes = datos.tabla_costes;
    var max_consumos_totales = datos.max_consumos_totales;
    var max_costes_totales = datos.max_costes_totales;
    var max_precios_medios = datos.max_precios_medios;
    var etiquetas_consumos = datos.etiquetas_consumos;
    var etiquetas_costes = datos.etiquetas_costes;
    var unidad_medida_consumo = datos.unidad_medida_consumo;
    var unidad_medida_coste = datos.unidad_medida_coste;
    var unidad_medida_precio = datos.unidad_medida_precio;

    // Parámetros
    var id_grafica_consumos_totales = parametros.id_grafica_consumos_totales;
    var id_grafica_porcentajes_consumos = parametros.id_grafica_porcentajes_consumos;
    var id_contenedor_tabla_consumos = parametros.id_contenedor_tabla_consumos;
    var id_grafica_costes_totales = parametros.id_grafica_costes_totales;
    var id_grafica_porcentajes_costes = parametros.id_grafica_porcentajes_costes;
    var id_grafica_precios_medios = parametros.id_grafica_precios_medios;
    var id_contenedor_tabla_costes = parametros.id_contenedor_tabla_costes;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes(
        TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_consumos_totales = parametros.mostrar_grafica_consumos_totales;
    var mostrar_grafica_porcentajes_consumos = parametros.mostrar_grafica_porcentajes_consumos;
    var mostrar_tabla_consumos = parametros.mostrar_tabla_consumos;
    var mostrar_grafica_costes_totales = parametros.mostrar_grafica_costes_totales;
    var mostrar_grafica_porcentajes_costes = parametros.mostrar_grafica_porcentajes_costes;
    var mostrar_grafica_precios_medios = parametros.mostrar_grafica_precios_medios;
    var mostrar_tabla_costes = parametros.mostrar_tabla_costes;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles
    if (mostrar_grafica_consumos_totales == true) {
        muestra_elemento(id_grafica_consumos_totales);
    }
    if (mostrar_grafica_porcentajes_consumos == true) {
        muestra_elemento(id_grafica_porcentajes_consumos);
    }
    if (mostrar_tabla_consumos == true) {
        muestra_elemento(id_contenedor_tabla_consumos);
    }
    if (mostrar_grafica_costes_totales == true) {
        muestra_elemento(id_grafica_costes_totales);
    }
    if (mostrar_grafica_porcentajes_costes == true) {
        muestra_elemento(id_grafica_porcentajes_costes);
    }
    if (mostrar_grafica_precios_medios == true) {
        muestra_elemento(id_grafica_precios_medios);
    }
    if (mostrar_tabla_costes == true) {
        muestra_elemento(id_contenedor_tabla_costes);
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

    // Gráfica de consumos totales
    if (mostrar_grafica_consumos_totales == true) {
        var mostrar_valores_barras = (etiquetas_consumos.length <= NUMERO_MAXIMO_BARRAS_VALORES_VALOR_VISIBLE_GRAFICA_50);
        muestra_grafica_puntual_barras_valores(
            id_grafica_consumos_totales,
            TLNT.Idiomas._("Consumos totales") + " (" + unidad_medida_consumo + ")",
            etiquetas_consumos,
            grafica_consumos_totales,
            [TLNT.Idiomas._("Sensores")], null,
            max_consumos_totales, true,
            2, unidad_medida_consumo,
            false, mostrar_valores_barras,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de porcentajes de consumos
    if (mostrar_grafica_porcentajes_consumos == true) {
        var altura_grafica_consumos_totales = $("#" + id_grafica_consumos_totales).height();
        $("#" + id_grafica_porcentajes_consumos).height(altura_grafica_consumos_totales);
        muestra_grafica_tarta_valores(
            id_grafica_porcentajes_consumos,
            TLNT.Idiomas._("Porcentajes de consumo") + " (" + unidad_medida_consumo + ")",
            TLNT.Idiomas._("Sensores"),
            etiquetas_consumos,
            grafica_porcentajes_consumos,
            2, unidad_medida_consumo,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de consumos
    if (mostrar_tabla_consumos == true) {
        $("#" + id_contenedor_tabla_consumos).html(tabla_consumos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_consumos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_consumos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_consumos, info_menu_contextual, TLNT.Idiomas._('Consumos'));
        }
    }

    // Gráfica de costes totales
    if (mostrar_grafica_costes_totales == true) {
        if (hay_datos_costes == true) {
            var mostrar_valores_barras = (etiquetas_costes.length <= NUMERO_MAXIMO_BARRAS_VALORES_VALOR_VISIBLE_GRAFICA_50);
            muestra_grafica_puntual_barras_valores(
                id_grafica_costes_totales,
                TLNT.Idiomas._("Costes totales") + " (" + unidad_medida_coste + ")",
                etiquetas_costes,
                grafica_costes_totales,
                [TLNT.Idiomas._("Sensores")], null,
                max_costes_totales, true,
                2, unidad_medida_coste,
                false, mostrar_valores_barras,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_costes_totales);
            }
            else {
                cambia_clase_elemento(id_grafica_costes_totales, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_costes_totales).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de costes totales (no hay datos de coste)"));
            }
            oculta_elemento(id_grafica_porcentajes_costes);
        }
    }

    // Gráfica de porcentajes de costes
    if (mostrar_grafica_porcentajes_costes == true) {
        if (hay_datos_costes == true) {
            var altura_grafica_costes_totales = $("#" + id_grafica_costes_totales).height();
            $("#" + id_grafica_porcentajes_costes).height(altura_grafica_costes_totales);
            muestra_grafica_tarta_valores(
                id_grafica_porcentajes_costes,
                TLNT.Idiomas._("Porcentajes de coste") + " (" + unidad_medida_coste + ")",
                TLNT.Idiomas._("Sensores"),
                etiquetas_costes,
                grafica_porcentajes_costes,
                2, unidad_medida_coste,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_costes_totales);
            }
            else {
                cambia_clase_elemento(id_grafica_costes_totales, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_costes_totales).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de porcentajes de coste (no hay datos de coste)"));
            }
            oculta_elemento(id_grafica_porcentajes_costes);
        }
    }

    // Gráfica de precios medios
    if (mostrar_grafica_precios_medios == true) {
        if (hay_datos_costes == true) {
            // Mostrado de valores en barras de valores
            var mostrar_valores_barras = (etiquetas_consumos.length <= NUMERO_MAXIMO_BARRAS_VALORES_VALOR_VISIBLE_GRAFICA_50);

            // Se dibuja la gráfica
            muestra_grafica_puntual_barras_valores(
                id_grafica_precios_medios,
                TLNT.Idiomas._("Precio medio") + " (" + unidad_medida_precio + ")",
                etiquetas_costes,
                grafica_precios_medios,
                [TLNT.Idiomas._("Sensores")], null,
                max_precios_medios, true,
                4, unidad_medida_precio,
                false, mostrar_valores_barras,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_precios_medios);
            }
            else {
                cambia_clase_elemento(id_grafica_precios_medios, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_precios_medios).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de precios medios (no hay datos de coste)"));
            }
        }
    }

    // Tabla de costes
    if (mostrar_tabla_costes == true) {
        if (hay_datos_costes == true) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_costes).html(tabla_costes);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_costes = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_costes);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_costes, info_menu_contextual, TLNT.Idiomas._('Costes'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_costes);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_costes, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_costes).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de costes (no hay datos de coste)"));
            }
        }
    }
}


// Dibujado del informe de comparación de periodos
function dibuja_informe_smartmeter_comparacion_periodos(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var hay_datos_costes = datos.hay_datos_costes;
    var msg_aviso = datos.msg_aviso;
    var min_fecha_consumo = new $.jsDate(datos.min_fecha_consumo);
    var max_fecha_consumo = new $.jsDate(datos.max_fecha_consumo);
    var min_fecha_coste = new $.jsDate(datos.min_fecha_coste);
    var max_fecha_coste = new $.jsDate(datos.max_fecha_coste);
    var grafica_consumos = datos.grafica_consumos;
    var grafica_costes = datos.grafica_costes;
    var tabla_evolucion_consumos_costes = datos.tabla_evolucion_consumos_costes;
    var titulo_tabla_evolucion_consumos_costes = datos.titulo_tabla_evolucion_consumos_costes;
    var max_consumo = datos.max_consumo;
    var max_coste = datos.max_coste;
    var etiquetas = datos.etiquetas;
    var etiquetas_tooltips = datos.etiquetas_tooltips;
    var grafica_consumos_totales = datos.grafica_consumos_totales;
    var grafica_costes_totales = datos.grafica_costes_totales;
    var grafica_precios_medios = datos.grafica_precios_medios;
    var tabla_evolucion_precios_medios = datos.tabla_evolucion_precios_medios;
    var max_total_consumo = datos.max_total_consumo;
    var max_total_coste = datos.max_total_coste;
    var max_precio_medio = datos.max_precio_medio;
    var unidad_medida_consumo = datos.unidad_medida_consumo;
    var unidad_medida_coste = datos.unidad_medida_coste;
    var unidad_medida_precio = datos.unidad_medida_precio;
    var tabla_evolucion_consumos_tramos = datos.tabla_evolucion_consumos_tramos;

    // Parámetros
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_consumos = parametros.id_grafica_consumos;
    var id_grafica_costes = parametros.id_grafica_costes;
    var id_contenedor_tabla_evolucion_consumos_costes = parametros.id_contenedor_tabla_evolucion_consumos_costes;
    var id_contenedor_tabla_evolucion_consumos_tramos = parametros.id_contenedor_tabla_evolucion_consumos_tramos;
    var id_grafica_consumos_totales = parametros.id_grafica_consumos_totales;
    var id_grafica_costes_totales = parametros.id_grafica_costes_totales;
    var id_grafica_precios_medios = parametros.id_grafica_precios_medios;
    var id_contenedor_tabla_evolucion_precios_medios = parametros.id_contenedor_tabla_evolucion_precios_medios;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes(
        TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS,
        null);

    // Avisos a mostrar
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        if (msg_aviso != "") {
            jAlert(msg_aviso);
        }
    }

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_consumos = parametros.mostrar_grafica_consumos;
    var mostrar_grafica_costes = parametros.mostrar_grafica_costes;
    var mostrar_tabla_evolucion_consumos_costes = parametros.mostrar_tabla_evolucion_consumos_costes;
    var mostrar_tabla_evolucion_consumos_tramos = parametros.mostrar_tabla_evolucion_consumos_tramos;
    var mostrar_grafica_consumos_totales = parametros.mostrar_grafica_consumos_totales;
    var mostrar_grafica_costes_totales = parametros.mostrar_grafica_costes_totales;
    var mostrar_grafica_precios_medios = parametros.mostrar_grafica_precios_medios;
    var mostrar_tabla_evolucion_precios_medios = parametros.mostrar_tabla_evolucion_precios_medios;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles
    if (mostrar_grafica_consumos == true) {
        muestra_elemento(id_grafica_consumos);
    }
    if (mostrar_grafica_costes == true) {
        muestra_elemento(id_grafica_costes);
    }
    if (mostrar_tabla_evolucion_consumos_costes == true) {
        muestra_elemento(id_contenedor_tabla_evolucion_consumos_costes);
    }
    if (mostrar_tabla_evolucion_consumos_tramos == true) {
        muestra_elemento(id_contenedor_tabla_evolucion_consumos_tramos);
    }
    if (mostrar_grafica_consumos_totales == true) {
        muestra_elemento(id_grafica_consumos_totales);
    }
    if (mostrar_grafica_costes_totales == true) {
        muestra_elemento(id_grafica_costes_totales);
    }
    if (mostrar_grafica_precios_medios == true) {
        muestra_elemento(id_grafica_precios_medios);
    }
    if (mostrar_tabla_evolucion_precios_medios == true) {
        muestra_elemento(id_contenedor_tabla_evolucion_precios_medios);
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
    if (mostrar_grafica_consumos == true) {
        var titulo_grafica_consumos = TLNT.Idiomas._("Consumo") + " (" + TLNT.Idiomas._("comparación de periodos") + ")" + " (" + unidad_medida_consumo + ")";
        muestra_grafica_temporal_lineas_valores_fechas_diferentes(
            id_grafica_consumos,
            titulo_grafica_consumos,
            etiquetas,
            etiquetas_tooltips,
            grafica_consumos, intervalo_valores,
            min_fecha_consumo, max_fecha_consumo, false,
            0, false,
            max_consumo, true,
            2, unidad_medida_consumo,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes
    if (mostrar_grafica_costes == true) {
        if (hay_datos_costes == true) {
            // Se dibuja la gráfica
            var titulo_grafica_costes = TLNT.Idiomas._("Coste") + " (" + TLNT.Idiomas._("comparación de periodos") + ")" + " (" + unidad_medida_coste + ")";
            muestra_grafica_temporal_lineas_valores_fechas_diferentes(
                id_grafica_costes,
                titulo_grafica_costes,
                etiquetas,
                etiquetas_tooltips,
                grafica_costes, intervalo_valores,
                min_fecha_coste, max_fecha_coste, false,
                0, false,
                max_coste, true,
                2, unidad_medida_coste,
                null,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_costes);
            }
            else {
                cambia_clase_elemento(id_grafica_costes, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_costes).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de costes (no hay datos de coste)"));
            }
        }
    }

    // Tabla de evolución de consumos y costes
    // Nota: Se muestra siempre (sin información de costes si no hay datos de costes)
    if (mostrar_tabla_evolucion_consumos_costes == true) {
        $("#" + id_contenedor_tabla_evolucion_consumos_costes).html(tabla_evolucion_consumos_costes);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_evolucion_consumos_costes = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_evolucion_consumos_costes);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_evolucion_consumos_costes, info_menu_contextual, titulo_tabla_evolucion_consumos_costes);
        }
    }

    // Tabla de evolución de consumos de los periodos por tramos
    if (mostrar_tabla_evolucion_consumos_tramos == true) {
        if ((hay_datos_costes == true) && (tabla_evolucion_consumos_tramos != null)) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_evolucion_consumos_tramos).html(tabla_evolucion_consumos_tramos);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_evolucion_consumos_tramos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_evolucion_consumos_tramos);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_evolucion_consumos_tramos, info_menu_contextual, TLNT.Idiomas._('Evolución de consumos por tramo'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_evolucion_consumos_tramos);
            }
            else {
                // Nota: Si no hay elementos visibles es plantilla de informe y sólo se puede
                // seleccionar este elemento si es electricidad y hay tramos en el tipo de tarifas de electricidad
                // (por lo que si llega aquí siempre será porque no hay datos de costes)
                cambia_clase_elemento(id_contenedor_tabla_evolucion_consumos_tramos, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_evolucion_consumos_tramos).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de evolución de consumos por tramo (no hay datos de coste)"));
            }
        }
    }

    // Gráfica de consumos totales
    if (mostrar_grafica_consumos_totales == true) {
        var mostrar_valores_barras = (etiquetas.length <= NUMERO_MAXIMO_BARRAS_VALORES_VALOR_VISIBLE_GRAFICA_50);
        muestra_grafica_puntual_barras_valores(
            id_grafica_consumos_totales,
            TLNT.Idiomas._("Consumo total") + " (" + unidad_medida_consumo + ")",
            etiquetas,
            grafica_consumos_totales,
            [TLNT.Idiomas._("Periodos")], null,
            max_total_consumo, true,
            2, unidad_medida_consumo,
            false, mostrar_valores_barras,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes totales
    if (mostrar_grafica_costes_totales == true) {
        if (hay_datos_costes == true) {
            var mostrar_valores_barras = (etiquetas.length <= NUMERO_MAXIMO_BARRAS_VALORES_VALOR_VISIBLE_GRAFICA_50);
            muestra_grafica_puntual_barras_valores(
                id_grafica_costes_totales,
                TLNT.Idiomas._("Coste total") + " (" + unidad_medida_coste + ")",
                etiquetas,
                grafica_costes_totales,
                [TLNT.Idiomas._("Periodos")], null,
                max_total_coste, true,
                2, unidad_medida_coste,
                false, mostrar_valores_barras,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_costes_totales);
            }
            else {
                cambia_clase_elemento(id_grafica_costes_totales, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_costes_totales).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de costes totales (no hay datos de coste)"));
            }
        }
    }

    // Gráfica de precios medios
    if (mostrar_grafica_precios_medios == true) {
        if (hay_datos_costes == true) {
            // Mostrado de valores en barras de valores
            var mostrar_valores_barras = (etiquetas.length <= NUMERO_MAXIMO_BARRAS_VALORES_VALOR_VISIBLE_GRAFICA_100);

            // Se dibuja la gráfica
            muestra_grafica_puntual_barras_valores(
                id_grafica_precios_medios,
                TLNT.Idiomas._("Precio medio") + " (" + unidad_medida_precio + ")",
                etiquetas,
                grafica_precios_medios,
                [TLNT.Idiomas._("Periodos")], null,
                max_precio_medio, true,
                6, unidad_medida_precio,
                false, mostrar_valores_barras,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_precios_medios);
            }
            else {
                cambia_clase_elemento(id_grafica_precios_medios, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_precios_medios).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de precios medios (no hay datos de coste)"));
            }
        }
    }

    // Tabla de evolución de precios medios
    if (mostrar_tabla_evolucion_precios_medios == true) {
        if (hay_datos_costes == true) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_evolucion_precios_medios).html(tabla_evolucion_precios_medios);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_evolucion_precios_medios = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_evolucion_precios_medios);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_evolucion_precios_medios, info_menu_contextual, TLNT.Idiomas._('Evolución de precios medios'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_evolucion_precios_medios);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_evolucion_precios_medios, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_evolucion_precios_medios).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de evolución de precios medios (no hay datos de coste)"));
            }
        }
    }
}


// Dibujado del informe de simulación de tarifas
function dibuja_informe_smartmeter_simulador_tarifas(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var grafica_costes = datos.grafica_costes;
    var max_coste = datos.max_coste;
    var etiquetas_costes = datos.etiquetas_costes;
    var grafica_costes_totales = datos.grafica_costes_totales;
    var max_coste_total = datos.max_coste_total;
    var etiquetas_costes_totales = datos.etiquetas_costes_totales;
    var tabla_comparacion_coste_actual = datos.tabla_comparacion_coste_actual;
    var tabla_comparacion_mejor_opcion = datos.tabla_comparacion_mejor_opcion;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_costes = parametros.id_grafica_costes;
    var id_grafica_costes_totales = parametros.id_grafica_costes_totales;
    var id_contenedor_tabla_comparacion_coste_actual = parametros.id_contenedor_tabla_comparacion_coste_actual;
    var id_contenedor_tabla_comparacion_mejor_opcion = parametros.id_contenedor_tabla_comparacion_mejor_opcion;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes(
        TIPO_INFORME_SMARTMETER_SIMULADOR_TARIFAS,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_SIMULADOR_TARIFAS,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_costes = parametros.mostrar_grafica_costes;
    var mostrar_grafica_costes_totales = parametros.mostrar_grafica_costes_totales;
    var mostrar_tabla_comparacion_coste_actual = parametros.mostrar_tabla_comparacion_coste_actual;
    var mostrar_tabla_comparacion_mejor_opcion = parametros.mostrar_tabla_comparacion_mejor_opcion;

    // Se muestran los elementos visibles
    if (mostrar_grafica_costes == true) {
        muestra_elemento(id_grafica_costes);
    }
    if (mostrar_grafica_costes_totales == true) {
        muestra_elemento(id_grafica_costes_totales);
    }
    if (mostrar_tabla_comparacion_coste_actual == true) {
        muestra_elemento(id_contenedor_tabla_comparacion_coste_actual);
    }
    if (mostrar_tabla_comparacion_mejor_opcion == true) {
        muestra_elemento(id_contenedor_tabla_comparacion_mejor_opcion);
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
    var numero_valores_grafica_costes = dame_numero_maximo_valores_series_grafica(grafica_costes);
    var mostrar_indicadores_valores = (numero_valores_grafica_costes <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de costes
    if (mostrar_grafica_costes == true) {
        muestra_grafica_temporal_lineas_valores(
            id_grafica_costes,
            null,
            TLNT.Idiomas._("Coste") + " (" + moneda + ")",
            etiquetas_costes,
            grafica_costes, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_coste, true,
            2, moneda,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes totales
    if (mostrar_grafica_costes_totales == true) {
        // Mostrado de valores en barras de valores
        var mostrar_valores_barras = (etiquetas_costes_totales.length <= NUMERO_MAXIMO_BARRAS_VALORES_VALOR_VISIBLE_GRAFICA_100);

        // Se dibuja la gráfica
        muestra_grafica_puntual_barras_valores(
            id_grafica_costes_totales,
            TLNT.Idiomas._("Coste total") + " (" + moneda + ")",
            etiquetas_costes_totales,
            grafica_costes_totales,
            [TLNT.Idiomas._("Tarifas")], null,
            max_coste_total, true,
            2, moneda,
            false, mostrar_valores_barras,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de comparación con el coste actual
    if (mostrar_tabla_comparacion_coste_actual == true) {
        $("#" + id_contenedor_tabla_comparacion_coste_actual).html(tabla_comparacion_coste_actual);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_comparacion_coste_actual = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_comparacion_coste_actual);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_comparacion_coste_actual, info_menu_contextual, TLNT.Idiomas._('Comparación con el coste actual'));
        }
    }

    // Tabla de comparación con la mejor opción
    if (mostrar_tabla_comparacion_mejor_opcion == true) {
        $("#" + id_contenedor_tabla_comparacion_mejor_opcion).html(tabla_comparacion_mejor_opcion);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_comparacion_mejor_opcion = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_comparacion_mejor_opcion);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_comparacion_mejor_opcion, info_menu_contextual, TLNT.Idiomas._('Comparación con la opción más barata'));
        }
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes(
    tipo_informe_smartmeter_consumos_costes,
    elementos_informe,
    parametros,
    datos) {
    switch (tipo_informe_smartmeter_consumos_costes) {
        case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
            var mostrar_grafica_consumos = true;
            var mostrar_grafica_consumos_acumulados = true;
            var mostrar_descripciones_sensores = true;
            var mostrar_tabla_consumos_maximos_minimos = true;
            var mostrar_grafica_costes = true;
            var mostrar_grafica_costes_acumulados = true;
            var mostrar_tabla_costes_maximos_minimos = true;
            var mostrar_grafica_precios = true;
            var mostrar_tabla_precios_maximos_minimos = true;
            var mostrar_tabla_comentarios = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_CONSUMOS) == -1) {
                    mostrar_grafica_consumos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_CONSUMOS_ACUMULADOS) == -1) {
                    mostrar_grafica_consumos_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_DESCRIPCIONES_SENSORES) == -1) {
                    mostrar_descripciones_sensores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_CONSUMOS_MAXIMOS_MINIMOS) == -1) {
                    mostrar_tabla_consumos_maximos_minimos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_COSTES) == -1) {
                    mostrar_grafica_costes = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_COSTES_ACUMULADOS) == -1) {
                    mostrar_grafica_costes_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_COSTES_MAXIMOS_MINIMOS) == -1) {
                    mostrar_tabla_costes_maximos_minimos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_PRECIOS) == -1) {
                    mostrar_grafica_precios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_PRECIOS_MAXIMOS_MINIMOS) == -1) {
                    mostrar_tabla_precios_maximos_minimos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
            }
            parametros.mostrar_grafica_consumos = mostrar_grafica_consumos;
            parametros.mostrar_grafica_consumos_acumulados = mostrar_grafica_consumos_acumulados;
            parametros.mostrar_descripciones_sensores = mostrar_descripciones_sensores;
            parametros.mostrar_tabla_consumos_maximos_minimos = mostrar_tabla_consumos_maximos_minimos;
            parametros.mostrar_grafica_costes = mostrar_grafica_costes;
            parametros.mostrar_grafica_costes_acumulados = mostrar_grafica_costes_acumulados;
            parametros.mostrar_tabla_costes_maximos_minimos = mostrar_tabla_costes_maximos_minimos;
            parametros.mostrar_grafica_precios = mostrar_grafica_precios;
            parametros.mostrar_tabla_precios_maximos_minimos = mostrar_tabla_precios_maximos_minimos;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;

            // Indica si hay elementos visibles
            var limite_sensores_graficas_superado = datos.limite_sensores_graficas_superado;
            var hay_datos_costes = datos.hay_datos_costes;
            var tabla_comentarios = datos.tabla_comentarios;
            var hay_elementos_visibles =
                ((mostrar_grafica_consumos == true) && (limite_sensores_graficas_superado == false)) ||
                ((mostrar_grafica_consumos_acumulados == true) && (limite_sensores_graficas_superado == false)) ||
                (mostrar_tabla_consumos_maximos_minimos == true) ||
                ((mostrar_grafica_costes == true) && ((hay_datos_costes == true) && (limite_sensores_graficas_superado == false))) ||
                ((mostrar_grafica_costes_acumulados == true) && ((hay_datos_costes == true) && (limite_sensores_graficas_superado == false))) ||
                ((mostrar_tabla_costes_maximos_minimos == true) && (hay_datos_costes == true)) ||
                ((mostrar_grafica_precios == true) && (hay_datos_costes == true)) ||
                ((mostrar_tabla_precios_maximos_minimos == true) && (hay_datos_costes == true)) ||
                ((mostrar_tabla_comentarios == true) && (tabla_comentarios != null));
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES: {
            var mostrar_grafica_consumos_totales = true;
            var mostrar_grafica_porcentajes_consumos = true;
            var mostrar_tabla_consumos = true;
            var mostrar_grafica_costes_totales = true;
            var mostrar_grafica_porcentajes_costes = true;
            var mostrar_grafica_precios_medios = true;
            var mostrar_tabla_costes = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_CONSUMOS_TOTALES) == -1) {
                    mostrar_grafica_consumos_totales = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PORCENTAJES_CONSUMOS) == -1) {
                    mostrar_grafica_porcentajes_consumos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TABLA_CONSUMOS) == -1) {
                    mostrar_tabla_consumos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_COSTES_TOTALES) == -1) {
                    mostrar_grafica_costes_totales = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PORCENTAJES_COSTES) == -1) {
                    mostrar_grafica_porcentajes_costes = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PRECIOS_MEDIOS) == -1) {
                    mostrar_grafica_precios_medios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TABLA_COSTES) == -1) {
                    mostrar_tabla_costes = false;
                }
            }
            parametros.mostrar_grafica_consumos_totales = mostrar_grafica_consumos_totales;
            parametros.mostrar_grafica_porcentajes_consumos = mostrar_grafica_porcentajes_consumos;
            parametros.mostrar_tabla_consumos = mostrar_tabla_consumos;
            parametros.mostrar_grafica_costes_totales = mostrar_grafica_costes_totales;
            parametros.mostrar_grafica_porcentajes_costes = mostrar_grafica_porcentajes_costes;
            parametros.mostrar_grafica_precios_medios = mostrar_grafica_precios_medios;
            parametros.mostrar_tabla_costes = mostrar_tabla_costes;

            // Indica si hay elementos visibles
            var hay_datos_costes = datos.hay_datos_costes;
            var hay_elementos_visibles =
                (mostrar_grafica_consumos_totales == true) ||
                (mostrar_grafica_porcentajes_consumos == true) ||
                (mostrar_tabla_consumos == true) ||
                ((mostrar_grafica_costes_totales == true) && (hay_datos_costes == true)) ||
                ((mostrar_grafica_porcentajes_costes == true) && (hay_datos_costes == true)) ||
                ((mostrar_grafica_precios_medios == true) && (hay_datos_costes == true)) ||
                ((mostrar_tabla_costes == true) && (hay_datos_costes == true));
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS: {
            var mostrar_grafica_consumos = true;
            var mostrar_grafica_costes = true;
            var mostrar_tabla_evolucion_consumos_costes = true;
            var mostrar_tabla_evolucion_consumos_tramos = true;
            var mostrar_grafica_consumos_totales = true;
            var mostrar_grafica_costes_totales = true;
            var mostrar_grafica_precios_medios = true;
            var mostrar_tabla_evolucion_precios_medios = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_CONSUMOS) == -1) {
                    mostrar_grafica_consumos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_COSTES) == -1) {
                    mostrar_grafica_costes = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_CONSUMOS_COSTES) == -1) {
                    mostrar_tabla_evolucion_consumos_costes = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_CONSUMOS_TRAMOS) == -1) {
                    mostrar_tabla_evolucion_consumos_tramos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_CONSUMOS_TOTALES) == -1) {
                    mostrar_grafica_consumos_totales = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_COSTES_TOTALES) == -1) {
                    mostrar_grafica_costes_totales = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_PRECIOS_MEDIOS) == -1) {
                    mostrar_grafica_precios_medios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_PRECIOS_MEDIOS) == -1) {
                    mostrar_tabla_evolucion_precios_medios = false;
                }
            }
            parametros.mostrar_grafica_consumos = mostrar_grafica_consumos;
            parametros.mostrar_grafica_costes = mostrar_grafica_costes;
            parametros.mostrar_tabla_evolucion_consumos_costes = mostrar_tabla_evolucion_consumos_costes;
            parametros.mostrar_tabla_evolucion_consumos_tramos = mostrar_tabla_evolucion_consumos_tramos;
            parametros.mostrar_grafica_consumos_totales = mostrar_grafica_consumos_totales;
            parametros.mostrar_grafica_costes_totales = mostrar_grafica_costes_totales;
            parametros.mostrar_grafica_precios_medios = mostrar_grafica_precios_medios;
            parametros.mostrar_tabla_evolucion_precios_medios = mostrar_tabla_evolucion_precios_medios;

            // Indica si hay elementos visibles
            var hay_datos_costes = datos.hay_datos_costes;
            var hay_elementos_visibles =
                (mostrar_grafica_consumos == true) ||
                ((mostrar_grafica_costes == true) && (hay_datos_costes == true)) ||
                ((mostrar_tabla_evolucion_consumos_costes == true) && (hay_datos_costes == true)) ||
                ((mostrar_tabla_evolucion_consumos_tramos == true) && (hay_datos_costes == true)) ||
                (mostrar_grafica_consumos_totales == true) ||
                ((mostrar_grafica_costes_totales == true) && (hay_datos_costes == true)) ||
                ((mostrar_grafica_precios_medios == true) && (hay_datos_costes == true)) ||
                ((mostrar_tabla_evolucion_precios_medios == true) && (hay_datos_costes == true));
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case TIPO_INFORME_SMARTMETER_SIMULADOR_TARIFAS: {
            var mostrar_grafica_costes = true;
            var mostrar_grafica_costes_totales = true;
            var mostrar_tabla_comparacion_coste_actual = true;
            var mostrar_tabla_comparacion_mejor_opcion = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_GRAFICA_COSTES) == -1) {
                    mostrar_grafica_costes = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_GRAFICA_COSTES_TOTALES) == -1) {
                    mostrar_grafica_costes_totales = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TABLA_COMPARACION_COSTE_ACTUAL) == -1) {
                    mostrar_tabla_comparacion_coste_actual = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TABLA_COMPARACION_MEJOR_OPCION) == -1) {
                    mostrar_tabla_comparacion_mejor_opcion = false;
                }
            }
            parametros.mostrar_grafica_costes = mostrar_grafica_costes;
            parametros.mostrar_grafica_costes_totales = mostrar_grafica_costes_totales;
            parametros.mostrar_tabla_comparacion_coste_actual = mostrar_tabla_comparacion_coste_actual;
            parametros.mostrar_tabla_comparacion_mejor_opcion = mostrar_tabla_comparacion_mejor_opcion;
            break;
        }
    }
    return (parametros);
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Smartmeter - Consumos y costes generales)
function dibuja_elemento_plantilla_informe_smartmeter_consumos_costes_generales(
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

    // Parámetros del elemento
    var fecha_hora_inicio_consulta = parametros_elemento.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros_elemento.fecha_hora_fin_consulta;

    // Parámetros del tipo de informe
    var ids_sensores = parametros_tipo["ids_sensores"];
    var intervalo_valores = parametros_tipo["intervalo_valores"];
    var agregacion = parametros_tipo["agregacion"];
    var comentarios = parametros_tipo["comentarios"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-consumos-costes-generales";
    var id_grafica_consumos = prefijo_elemento + "grafica-consumos-consumos-costes-generales";
    var id_grafica_consumos_acumulados = prefijo_elemento + "grafica-consumos-acumulados-consumos-costes-generales";
    var id_descripciones_sensores = prefijo_elemento + "descripciones-sensores-consumos-costes-generales";
    var id_contenedor_tabla_consumos_maximos_minimos = prefijo_elemento + "contenedor-tabla-consumos-maximos-minimos-consumos-costes-generales";
    var id_grafica_costes = prefijo_elemento + "grafica-costes-consumos-costes-generales";
    var id_grafica_costes_acumulados = prefijo_elemento + "grafica-costes-acumulados-consumos-costes-generales";
    var id_contenedor_tabla_costes_maximos_minimos = prefijo_elemento + "contenedor-tabla-costes-maximos-minimos-consumos-costes-generales";
    var id_grafica_precios = prefijo_elemento + "grafica-precios-consumos-costes-generales";
    var id_contenedor_tabla_precios_maximos_minimos = prefijo_elemento + "contenedor-tabla-precios-maximos-minimos-consumos-costes-generales";
    var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-consumos-costes-generales";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        id_parametros_resultado_informe: id_parametros_resultado_informe,
        ids_sensores: ids_sensores,
        intervalo_valores: intervalo_valores,
        agregacion: agregacion,
        id_grafica_consumos: id_grafica_consumos,
        id_grafica_consumos_acumulados: id_grafica_consumos_acumulados,
        id_descripciones_sensores: id_descripciones_sensores,
        id_contenedor_tabla_consumos_maximos_minimos: id_contenedor_tabla_consumos_maximos_minimos,
        id_grafica_costes: id_grafica_costes,
        id_grafica_costes_acumulados: id_grafica_costes_acumulados,
        id_contenedor_tabla_costes_maximos_minimos: id_contenedor_tabla_costes_maximos_minimos,
        id_grafica_precios: id_grafica_precios,
        id_contenedor_tabla_precios_maximos_minimos: id_contenedor_tabla_precios_maximos_minimos,
        comentarios: comentarios,
        id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios};
    dibuja_informe_smartmeter_consumos_costes_generales(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Smartmeter - Consumos y costes totales)
function dibuja_elemento_plantilla_informe_smartmeter_consumos_costes_totales(
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

    // Parámetros del elemento
    var fecha_hora_inicio_consulta = parametros_elemento.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros_elemento.fecha_hora_fin_consulta;

    // Intervalo de valores
    var intervalo_valores = parametros_tipo["intervalo_valores"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_consumos_totales = prefijo_elemento + "grafica-consumos-totales-consumos-costes-totales";
    var id_grafica_porcentajes_consumos = prefijo_elemento + "grafica-porcentajes-consumos-consumos-costes-totales";
    var id_contenedor_tabla_consumos = prefijo_elemento + "contenedor-tabla-consumos-consumos-costes-totales";
    var id_grafica_costes_totales = prefijo_elemento + "grafica-costes-totales-consumos-costes-totales";
    var id_grafica_porcentajes_costes = prefijo_elemento + "grafica-porcentajes-costes-consumos-costes-totales";
    var id_grafica_precios_medios = prefijo_elemento + "grafica-precios-medios-consumos-costes-totales";
    var id_contenedor_tabla_costes = prefijo_elemento + "contenedor-tabla-costes-consumos-costes-totales";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        intervalo_valores: intervalo_valores,
        id_grafica_consumos_totales: id_grafica_consumos_totales,
        id_grafica_porcentajes_consumos: id_grafica_porcentajes_consumos,
        id_contenedor_tabla_consumos: id_contenedor_tabla_consumos,
        id_grafica_costes_totales: id_grafica_costes_totales,
        id_grafica_porcentajes_costes: id_grafica_porcentajes_costes,
        id_grafica_precios_medios: id_grafica_precios_medios,
        id_contenedor_tabla_costes: id_contenedor_tabla_costes};
    dibuja_informe_smartmeter_consumos_costes_totales(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Smartmeter - Comparación de periodos)
function dibuja_elemento_plantilla_informe_smartmeter_comparacion_periodos(
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

    // Intervalo de valores
    var intervalo_valores = parametros_tipo["intervalo_valores"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_consumos = prefijo_elemento + "grafica-consumos-comparacion-periodos";
    var id_grafica_costes = prefijo_elemento + "grafica-costes-comparacion-periodos";
    var id_contenedor_tabla_evolucion_consumos_costes = prefijo_elemento + "contenedor-tabla-evolucion-consumos-costes-comparacion-periodos";
    var id_contenedor_tabla_evolucion_consumos_tramos = prefijo_elemento + "contenedor-tabla-evolucion-consumos-tramos-comparacion-periodos";
    var id_grafica_consumos_totales = prefijo_elemento + "grafica-consumos-totales-comparacion-periodos";
    var id_grafica_costes_totales = prefijo_elemento + "grafica-costes-totales-comparacion-periodos";
    var id_grafica_precios_medios = prefijo_elemento + "grafica-precios-medios-comparacion-periodos";
    var id_contenedor_tabla_evolucion_precios_medios = prefijo_elemento + "contenedor-tabla-evolucion-precios-medios-comparacion-periodos";

    var parametros = {
        intervalo_valores: intervalo_valores,
        id_grafica_consumos: id_grafica_consumos,
        id_grafica_costes: id_grafica_costes,
        id_contenedor_tabla_evolucion_consumos_costes: id_contenedor_tabla_evolucion_consumos_costes,
        id_contenedor_tabla_evolucion_consumos_tramos: id_contenedor_tabla_evolucion_consumos_tramos,
        id_grafica_consumos_totales: id_grafica_consumos_totales,
        id_grafica_costes_totales: id_grafica_costes_totales,
        id_grafica_precios_medios: id_grafica_precios_medios,
        id_contenedor_tabla_evolucion_precios_medios: id_contenedor_tabla_evolucion_precios_medios};
    dibuja_informe_smartmeter_comparacion_periodos(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Smartmeter - Simulación de tarifas)
function dibuja_elemento_plantilla_informe_smartmeter_simulador_tarifas(
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
        $("#elemento-sin-tarifas-seleccionadas-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de sensor seleccionado
    var sin_sensor_seleccionado = datos_elemento.sin_sensor_seleccionado;
    if (sin_sensor_seleccionado == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).show();
        $("#elemento-sin-tarifas-seleccionadas-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de tarifas seleccionadas
    var sin_tarifas_seleccionadas = datos_elemento.sin_tarifas_seleccionadas;
    if (sin_tarifas_seleccionadas == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-tarifas-seleccionadas-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-tarifas-seleccionadas-elemento" + numero_elemento).hide();
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
    var id_grafica_costes = prefijo_elemento + "grafica-costes-simulador-tarifas";
    var id_grafica_costes_totales = prefijo_elemento + "grafica-costes-totales-simulador-tarifas";
    var id_contenedor_tabla_comparacion_coste_actual = prefijo_elemento + "contenedor-tabla-comparacion-coste-actual-simulador-tarifas";
    var id_contenedor_tabla_comparacion_mejor_opcion = prefijo_elemento + "contenedor-tabla-comparacion-mejor-opcion-simulador-tarifas";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        id_grafica_costes: id_grafica_costes,
        id_grafica_costes_totales: id_grafica_costes_totales,
        id_contenedor_tabla_comparacion_coste_actual: id_contenedor_tabla_comparacion_coste_actual,
        id_contenedor_tabla_comparacion_mejor_opcion: id_contenedor_tabla_comparacion_mejor_opcion};
    dibuja_informe_smartmeter_simulador_tarifas(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}

