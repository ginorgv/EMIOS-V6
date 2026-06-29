//
// Funciones para el dibujado de los informes de información (Sensores)
//


// Dibujado del informe de información de sensores
function dibuja_informe_sensores_informacion(
    clase_sensor,
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var texto_informacion_datos = datos.texto_informacion_datos;
    var lineas_verticales_comentarios = datos.lineas_verticales_comentarios;
    var tabla_comentarios = datos.tabla_comentarios;
    var numero_comentarios = datos.numero_comentarios;
    var fecha_inicio_valores = datos.fecha_inicio_valores;
    var hora_inicio_valores = datos.hora_inicio_valores;
    var fecha_fin_valores = datos.fecha_fin_valores;
    var hora_fin_valores = datos.hora_fin_valores;
    var descripcion_sensor = datos.descripcion_sensor;

    // Parámetros
    var id_parametros_resultado_informe = parametros.id_parametros_resultado_informe;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_informacion(
        clase_sensor,
        elementos_informe,
        parametros,
        datos);

    // Fechas de inicio y fin para acotar las fechas de adición y modificación de comentarios
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        $("#" + id_parametros_resultado_informe).attr("fecha_inicio_valores", fecha_inicio_valores);
        $("#" + id_parametros_resultado_informe).attr("hora_inicio_valores", hora_inicio_valores);
        $("#" + id_parametros_resultado_informe).attr("fecha_fin_valores", fecha_fin_valores);
        $("#" + id_parametros_resultado_informe).attr("hora_fin_valores", hora_fin_valores);
    }

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

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

    // Se dibuja el informe específico de la clase de sensor (gráficas con comentarios)
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
    switch (clase_sensor) {
        case CLASE_SENSOR_TEMPERATURA: {
            dibuja_informe_sensores_informacion_temperatura(
                parametros,
                datos,
                lineas_comentarios,
                elementos_informe,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_HUMEDAD: {
            dibuja_informe_sensores_informacion_humedad(
                parametros,
                datos,
                lineas_comentarios,
                elementos_informe,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_LUZ_INTERIOR: {
            dibuja_informe_sensores_informacion_luz_interior(
                parametros,
                datos,
                lineas_comentarios,
                elementos_informe,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_VIENTO: {
            dibuja_informe_sensores_informacion_viento(
                parametros,
                datos,
                lineas_comentarios,
                elementos_informe,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_ENERGIA_ACTIVA:
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            dibuja_informe_sensores_informacion_energia(
                parametros,
                datos,
                lineas_comentarios,
                elementos_informe,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_CORTES_TENSION: {
            dibuja_informe_sensores_informacion_cortes_tension(
                parametros,
                datos,
                lineas_comentarios,
                elementos_informe,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_COMPRA_ENERGIA: {
            dibuja_informe_sensores_informacion_compra_energia(
                parametros,
                datos,
                lineas_comentarios,
                elementos_informe,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_GAS: {
            dibuja_informe_sensores_informacion_gas(
                parametros,
                datos,
                lineas_comentarios,
                elementos_informe,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_AGUA: {
            dibuja_informe_sensores_informacion_agua(
                parametros,
                datos,
                lineas_comentarios,
                elementos_informe,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_GENERICA: {
            dibuja_informe_sensores_informacion_generica(
                parametros,
                datos,
                lineas_comentarios,
                elementos_informe,
                tipo_informe);
            break;
        }
    }
    grafica_con_comentarios = false;

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

    // Texto de información
    if (mostrar_texto_informacion == true) {
        $("#" + id_texto_informacion_datos).html(texto_informacion_datos);
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
                TLNT.Navegacion.establece_eventos_tablas_datos_informes_sensores();
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
function anyade_parametros_elementos_visibles_informe_sensores_informacion(
    clase_sensor,
    elementos_informe,
    parametros,
    datos) {
    switch (clase_sensor) {
        case CLASE_SENSOR_TEMPERATURA: {
            var mostrar_grafica_temperatura = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_texto_informacion = true;
            var mostrar_tabla_comentarios = true;
            var mostrar_mapa_calor_temperatura = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_TEMPERATURA) == -1) {
                    mostrar_grafica_temperatura = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION) == -1) {
                    mostrar_texto_informacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_TEMPERATURA) == -1) {
                    mostrar_mapa_calor_temperatura = false;
                }
            }
            parametros.mostrar_grafica_temperatura = mostrar_grafica_temperatura;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_texto_informacion = mostrar_texto_informacion;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;
            parametros.mostrar_mapa_calor_temperatura = mostrar_mapa_calor_temperatura;

            // Indica si hay elementos visibles
            var hay_elementos_visibles =
                (mostrar_grafica_temperatura == true) ||
                (mostrar_texto_informacion == true) ||
                (mostrar_mapa_calor_temperatura == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case CLASE_SENSOR_HUMEDAD: {
            var mostrar_grafica_humedad = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_texto_informacion = true;
            var mostrar_tabla_comentarios = true;
            var mostrar_mapa_calor_humedad = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_HUMEDAD) == -1) {
                    mostrar_grafica_humedad = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION) == -1) {
                    mostrar_texto_informacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_HUMEDAD) == -1) {
                    mostrar_mapa_calor_humedad = false;
                }
            }
            parametros.mostrar_grafica_humedad = mostrar_grafica_humedad;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_texto_informacion = mostrar_texto_informacion;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;
            parametros.mostrar_mapa_calor_humedad = mostrar_mapa_calor_humedad;

            // Indica si hay elementos visibles
            var hay_elementos_visibles =
                (mostrar_grafica_humedad == true) ||
                (mostrar_texto_informacion == true) ||
                (mostrar_mapa_calor_humedad == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case CLASE_SENSOR_LUZ_INTERIOR: {
            var mostrar_grafica_luz = true;
            var mostrar_grafica_luz_artificial = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_texto_informacion = true;
            var mostrar_tabla_comentarios = true;
            var mostrar_mapa_calor_luz = true;
            var mostrar_mapa_calor_luz_artificial = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_LUZ) == -1) {
                    mostrar_grafica_luz = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_LUZ_ARTIFICIAL) == -1) {
                    mostrar_grafica_luz_artificial = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION) == -1) {
                    mostrar_texto_informacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_LUZ) == -1) {
                    mostrar_mapa_calor_luz = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_LUZ_ARTIFICIAL) == -1) {
                    mostrar_mapa_calor_luz_artificial = false;
                }
            }
            parametros.mostrar_grafica_luz = mostrar_grafica_luz;
            parametros.mostrar_grafica_luz_artificial = mostrar_grafica_luz_artificial;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_texto_informacion = mostrar_texto_informacion;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;
            parametros.mostrar_mapa_calor_luz = mostrar_mapa_calor_luz;
            parametros.mostrar_mapa_calor_luz_artificial = mostrar_mapa_calor_luz_artificial;

            // Indica si hay elementos visibles
            var hay_elementos_visibles =
                (mostrar_grafica_luz == true) ||
                (mostrar_grafica_luz_artificial == true) ||
                (mostrar_texto_informacion == true) ||
                (mostrar_mapa_calor_luz == true) ||
                (mostrar_mapa_calor_luz_artificial == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case CLASE_SENSOR_VIENTO: {
            var mostrar_grafica_velocidad_viento = true;
            var mostrar_grafica_direccion_viento = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_texto_informacion = true;
            var mostrar_graficos_viento = true;
            var mostrar_tabla_comentarios = true;
            var mostrar_mapa_calor_velocidad_viento = true;
            var mostrar_mapa_calor_direccion_viento = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VELOCIDAD_VIENTO) == -1) {
                    mostrar_grafica_velocidad_viento = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_DIRECCION_VIENTO) == -1) {
                    mostrar_grafica_direccion_viento = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION) == -1) {
                    mostrar_texto_informacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICOS_VIENTO) == -1) {
                    mostrar_graficos_viento = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VELOCIDAD_VIENTO) == -1) {
                    mostrar_mapa_calor_velocidad_viento = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_DIRECCION_VIENTO) == -1) {
                    mostrar_mapa_calor_direccion_viento = false;
                }
            }
            parametros.mostrar_grafica_velocidad_viento = mostrar_grafica_velocidad_viento;
            parametros.mostrar_grafica_direccion_viento = mostrar_grafica_direccion_viento;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_texto_informacion = mostrar_texto_informacion;
            parametros.mostrar_graficos_viento = mostrar_graficos_viento;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;
            parametros.mostrar_mapa_calor_velocidad_viento = mostrar_mapa_calor_velocidad_viento;
            parametros.mostrar_mapa_calor_direccion_viento = mostrar_mapa_calor_direccion_viento;

            // Indica si hay elementos visibles
            var hay_elementos_visibles =
                (mostrar_grafica_velocidad_viento == true) ||
                (mostrar_grafica_direccion_viento == true) ||
                (mostrar_graficos_viento == true) ||
                (mostrar_texto_informacion == true) ||
                (mostrar_mapa_calor_velocidad_viento == true) ||
                (mostrar_mapa_calor_direccion_viento == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case CLASE_SENSOR_ENERGIA_ACTIVA:
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            var mostrar_grafica_valores = true;
            var mostrar_grafica_valores_acumulados = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_texto_informacion = true;
            var mostrar_tabla_comentarios = true;
            var mostrar_mapa_calor_valores = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS) == -1) {
                    mostrar_grafica_valores_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION) == -1) {
                    mostrar_texto_informacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES) == -1) {
                    mostrar_mapa_calor_valores = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_grafica_valores_acumulados = mostrar_grafica_valores_acumulados;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_texto_informacion = mostrar_texto_informacion;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;
            parametros.mostrar_mapa_calor_valores = mostrar_mapa_calor_valores;

            // Indica si hay elementos visibles
            var campo_incremental = datos.campo_incremental;
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                ((mostrar_grafica_valores_acumulados == true) && (campo_incremental == true)) ||
                (mostrar_texto_informacion == true) ||
                (mostrar_mapa_calor_valores == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case CLASE_SENSOR_CORTES_TENSION: {
            var mostrar_grafica_cortes_tension = true;
            var mostrar_grafica_cortes_tension_acumulados = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_texto_informacion = true;
            var mostrar_tabla_comentarios = true;
            var mostrar_mapa_calor_cortes_tension = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_CORTES_TENSION) == -1) {
                    mostrar_grafica_cortes_tension = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_CORTES_TENSION_ACUMULADOS) == -1) {
                    mostrar_grafica_cortes_tension_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION) == -1) {
                    mostrar_texto_informacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_CORTES_TENSION) == -1) {
                    mostrar_mapa_calor_cortes_tension = false;
                }
            }
            parametros.mostrar_grafica_cortes_tension = mostrar_grafica_cortes_tension;
            parametros.mostrar_grafica_cortes_tension_acumulados = mostrar_grafica_cortes_tension_acumulados;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_texto_informacion = mostrar_texto_informacion;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;
            parametros.mostrar_mapa_calor_cortes_tension = mostrar_mapa_calor_cortes_tension;

            // Indica si hay elementos visibles
            var hay_elementos_visibles =
                (mostrar_grafica_cortes_tension == true) ||
                (mostrar_grafica_cortes_tension_acumulados == true) ||
                (mostrar_texto_informacion == true) ||
                (mostrar_mapa_calor_cortes_tension == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case CLASE_SENSOR_GAS: {
            var mostrar_grafica_valores = true;
            var mostrar_grafica_valores_acumulados = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_texto_informacion = true;
            var mostrar_tabla_comentarios = true;
            var mostrar_mapa_calor_valores = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS) == -1) {
                    mostrar_grafica_valores_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION) == -1) {
                    mostrar_texto_informacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES) == -1) {
                    mostrar_mapa_calor_valores = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_grafica_valores_acumulados = mostrar_grafica_valores_acumulados;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_texto_informacion = mostrar_texto_informacion;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;
            parametros.mostrar_mapa_calor_valores = mostrar_mapa_calor_valores;

            // Indica si hay elementos visibles
            var campo_incremental = datos.campo_incremental;
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                ((mostrar_grafica_valores_acumulados == true) && (campo_incremental == true)) ||
                (mostrar_texto_informacion == true) ||
                (mostrar_mapa_calor_valores == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case CLASE_SENSOR_COMPRA_ENERGIA: {
            var mostrar_grafica_valores = true;
            var mostrar_grafica_valores_acumulados = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_texto_informacion = true;
            var mostrar_tabla_comentarios = true;
            var mostrar_mapa_calor_valores = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS) == -1) {
                    mostrar_grafica_valores_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION) == -1) {
                    mostrar_texto_informacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES) == -1) {
                    mostrar_mapa_calor_valores = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_grafica_valores_acumulados = mostrar_grafica_valores_acumulados;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_texto_informacion = mostrar_texto_informacion;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;
            parametros.mostrar_mapa_calor_valores = mostrar_mapa_calor_valores;

            // Indica si hay elementos visibles
            var campo_incremental = datos.campo_incremental;
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                ((mostrar_grafica_valores_acumulados == true) && (campo_incremental == true)) ||
                (mostrar_texto_informacion == true) ||
                (mostrar_mapa_calor_valores == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case CLASE_SENSOR_AGUA: {
            var mostrar_grafica_valores = true;
            var mostrar_grafica_valores_acumulados = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_texto_informacion = true;
            var mostrar_tabla_comentarios = true;
            var mostrar_mapa_calor_valores = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS) == -1) {
                    mostrar_grafica_valores_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION) == -1) {
                    mostrar_texto_informacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES) == -1) {
                    mostrar_mapa_calor_valores = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_grafica_valores_acumulados = mostrar_grafica_valores_acumulados;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_texto_informacion = mostrar_texto_informacion;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;
            parametros.mostrar_mapa_calor_valores = mostrar_mapa_calor_valores;

            // Indica si hay elementos visibles
            var campo_incremental = datos.campo_incremental;
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                ((mostrar_grafica_valores_acumulados == true) && (campo_incremental == true)) ||
                (mostrar_texto_informacion == true) ||
                (mostrar_mapa_calor_valores == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case CLASE_SENSOR_GENERICA: {
            var mostrar_grafica_valores = true;
            var mostrar_grafica_valores_acumulados = true;
            var mostrar_descripcion_sensor = true;
            var mostrar_texto_informacion = true;
            var mostrar_tabla_comentarios = true;
            var mostrar_mapa_calor_valores = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS) == -1) {
                    mostrar_grafica_valores_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR) == -1) {
                    mostrar_descripcion_sensor = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION) == -1) {
                    mostrar_texto_informacion = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) == -1) {
                    mostrar_tabla_comentarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES) == -1) {
                    mostrar_mapa_calor_valores = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_grafica_valores_acumulados = mostrar_grafica_valores_acumulados;
            parametros.mostrar_descripcion_sensor = mostrar_descripcion_sensor;
            parametros.mostrar_texto_informacion = mostrar_texto_informacion;
            parametros.mostrar_tabla_comentarios = mostrar_tabla_comentarios;
            parametros.mostrar_mapa_calor_valores = mostrar_mapa_calor_valores;

            // Indica si hay elementos visibles
            var campo_incremental = datos.campo_incremental;
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                ((mostrar_grafica_valores_acumulados == true) && (campo_incremental == true)) ||
                (mostrar_texto_informacion == true) ||
                (mostrar_mapa_calor_valores == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
    }

    // Tabla de comentarios
    if (parametros.hay_elementos_visibles == false) {
        var tabla_comentarios = datos.tabla_comentarios;
        if ((mostrar_tabla_comentarios == true) && (tabla_comentarios != null)) {
            parametros.hay_elementos_visibles = true;
        }
    }
}


// Dibujado del informe de información de sensores (temperatura)
function dibuja_informe_sensores_informacion_temperatura(
    parametros,
    datos,
    lineas_comentarios,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var min_temperatura = datos.min_temperatura;
    var max_temperatura = datos.max_temperatura;
    var etiquetas_grafica_temperatura = datos.etiquetas_grafica_temperatura;
    var grafica_temperatura = datos.grafica_temperatura;
    var dias_mapa_calor_temperatura = datos.dias_mapa_calor_temperatura;
    var datos_mapa_calor_temperatura = datos.datos_mapa_calor_temperatura;
    var texto_informacion_datos = datos.texto_informacion_datos;
    var descripcion_campo = datos.descripcion_campo;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var campo = parametros.campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_temperatura = parametros.id_grafica_temperatura;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_temperatura = parametros.id_mapa_calor_temperatura;
    var altura_maxima_mapa_calor_temperatura = parametros.altura_maxima_mapa_calor_temperatura;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_temperatura = parametros.mostrar_grafica_temperatura;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var mostrar_mapa_calor_temperatura = parametros.mostrar_mapa_calor_temperatura;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_temperatura == true) {
        muestra_elemento(id_grafica_temperatura);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_texto_informacion == true) {
        muestra_elemento(id_texto_informacion_datos);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }
    if (mostrar_mapa_calor_temperatura == true) {
        muestra_elemento(id_mapa_calor_temperatura);
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

    // Mostrar líneas de valores
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_temperatura = dame_numero_maximo_valores_series_grafica(grafica_temperatura);
    var mostrar_indicadores_valores = (numero_valores_grafica_temperatura <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de temperatura
    if (mostrar_grafica_temperatura == true) {
        // Líneas de la gráfica de valores
        var lineas_grafica_valores = null;
        if (lineas_comentarios != null) {
            lineas_grafica_valores = [];
            for (var i = 0; i < lineas_comentarios.length; i++) {
                lineas_grafica_valores.push(lineas_comentarios[i]);
            }
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_TEMPERATURA, campo);
        if (lineas_referencia != null) {
            if (lineas_grafica_valores == null) {
                lineas_grafica_valores = [];
            }
            for (var i = 0; i < lineas_referencia.length; i++) {
                lineas_grafica_valores.push(lineas_referencia[i]);
            }
        }

        // Se dibuja la gráfica
        var titulo_grafica_temperatura = descripcion_campo + " (" + unidad_medida + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_temperatura,
            null,
            titulo_grafica_temperatura,
            etiquetas_grafica_temperatura,
            grafica_temperatura, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_temperatura, true,
            max_temperatura, true,
            numero_decimales_valores, unidad_medida,
            lineas_grafica_valores,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor de temperatura
    if (mostrar_mapa_calor_temperatura == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_temperatura = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_temperatura = TLNT.Idiomas._("Mapa de calor diario de") + " " + descripcion_campo.uncapitalize() + " (" + unidad_medida + ")";
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_temperatura = TLNT.Idiomas._("Mapa de calor semanal de") + " " + descripcion_campo.uncapitalize() + " (" + unidad_medida + ")";
                    break;
                }
            }
            muestra_grafico_mapa_calor(
                id_mapa_calor_temperatura,
                tipo_mapa_calor,
                titulo_mapa_calor_temperatura,
                dias_mapa_calor_temperatura,
                null,
                datos_mapa_calor_temperatura,
                null,
                null,
                true,
                ESCALA_COLORES_AZUL_ROJO,
                altura_maxima_mapa_calor_temperatura,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }
}


// Dibujado del informe de información de sensores (humedad)
function dibuja_informe_sensores_informacion_humedad(
    parametros,
    datos,
    lineas_comentarios,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var etiquetas_grafica_humedad = datos.etiquetas_grafica_humedad;
    var grafica_humedad = datos.grafica_humedad;
    var dias_mapa_calor_humedad = datos.dias_mapa_calor_humedad;
    var datos_mapa_calor_humedad = datos.datos_mapa_calor_humedad;
    var texto_informacion_datos = datos.texto_informacion_datos;
    var numero_decimales_valores = datos.numero_decimales_valores;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_humedad = parametros.id_grafica_humedad;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_humedad = parametros.id_mapa_calor_humedad;
    var altura_maxima_mapa_calor_humedad = parametros.altura_maxima_mapa_calor_humedad;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_humedad = parametros.mostrar_grafica_humedad;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var mostrar_mapa_calor_humedad = parametros.mostrar_mapa_calor_humedad;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_humedad == true) {
        muestra_elemento(id_grafica_humedad);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_texto_informacion == true) {
        muestra_elemento(id_texto_informacion_datos);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }
    if (mostrar_mapa_calor_humedad == true) {
        muestra_elemento(id_mapa_calor_humedad);
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

    // Mostrar líneas de valores
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_humedad = dame_numero_maximo_valores_series_grafica(grafica_humedad);
    var mostrar_indicadores_valores = (numero_valores_grafica_humedad <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de humedad
    if (mostrar_grafica_humedad == true) {
        // Líneas de la gráfica de valores
        var lineas_grafica_valores = null;
        if (lineas_comentarios != null) {
            lineas_grafica_valores = [];
            for (var i = 0; i < lineas_comentarios.length; i++) {
                lineas_grafica_valores.push(lineas_comentarios[i]);
            }
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_HUMEDAD, CAMPO_HUMEDAD);
        if (lineas_referencia != null) {
            if (lineas_grafica_valores == null) {
                lineas_grafica_valores = [];
            }
            for (var i = 0; i < lineas_referencia.length; i++) {
                lineas_grafica_valores.push(lineas_referencia[i]);
            }
        }

        // Se dibuja la gráfica
        var titulo_grafica_humedad = TLNT.Idiomas._("Humedad") + " (" + "%" + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_humedad,
            null,
            titulo_grafica_humedad,
            etiquetas_grafica_humedad,
            grafica_humedad, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            100, false,
            numero_decimales_valores, TLNT.Idiomas._("%"),
            lineas_grafica_valores,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor de humedad
    if (mostrar_mapa_calor_humedad == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_humedad = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_humedad = TLNT.Idiomas._("Mapa de calor diario de humedad") + " (" + TLNT.Idiomas._("%") + ")";
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_humedad = TLNT.Idiomas._("Mapa de calor semanal de humedad") + " (" + TLNT.Idiomas._("%") + ")";
                    break;
                }
            }
            muestra_grafico_mapa_calor(
                id_mapa_calor_humedad,
                tipo_mapa_calor,
                titulo_mapa_calor_humedad,
                dias_mapa_calor_humedad,
                null,
                datos_mapa_calor_humedad,
                null,
                null,
                true,
                ESCALA_COLORES_AMARILLO_AZUL,
                altura_maxima_mapa_calor_humedad,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }
}


// Dibujado del informe de información de sensores (luz interior)
function dibuja_informe_sensores_informacion_luz_interior(
    parametros,
    datos,
    lineas_comentarios,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var max_luz = datos.max_luz;
    var etiquetas_graficas = datos.etiquetas_graficas;
    var grafica_luz = datos.grafica_luz;
    var grafica_luz_artificial = datos.grafica_luz_artificial;
    var dias_mapas_calor = datos.dias_mapas_calor;
    var datos_mapa_calor_luz = datos.datos_mapa_calor_luz;
    var datos_mapa_calor_luz_artificial = datos.datos_mapa_calor_luz_artificial;
    var texto_informacion_datos = datos.texto_informacion_datos;
    var numero_decimales_valores_luz = datos.numero_decimales_valores_luz;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_luz = parametros.id_grafica_luz;
    var id_grafica_luz_artificial = parametros.id_grafica_luz_artificial;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_luz = parametros.id_mapa_calor_luz;
    var altura_maxima_mapa_calor_luz = parametros.altura_maxima_mapa_calor_luz;
    var id_mapa_calor_luz_artificial = parametros.id_mapa_calor_luz_artificial;
    var altura_maxima_mapa_calor_luz_artificial = parametros.altura_maxima_mapa_calor_luz_artificial;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_luz = parametros.mostrar_grafica_luz;
    var mostrar_grafica_luz_artificial = parametros.mostrar_grafica_luz_artificial;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var mostrar_mapa_calor_luz = parametros.mostrar_mapa_calor_luz;
    var mostrar_mapa_calor_luz_artificial = parametros.mostrar_mapa_calor_luz_artificial;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_luz == true) {
        muestra_elemento(id_grafica_luz);
    }
    if (mostrar_grafica_luz_artificial == true) {
        muestra_elemento(id_grafica_luz_artificial);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_texto_informacion == true) {
        muestra_elemento(id_texto_informacion_datos);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }
    if (mostrar_mapa_calor_luz == true) {
        muestra_elemento(id_mapa_calor_luz);
    }
    if (mostrar_mapa_calor_luz_artificial == true) {
        muestra_elemento(id_mapa_calor_luz_artificial);
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

    // Mostrar líneas de valores y tipo de líneas de valores
    var mostrar_lineas_valores = true;
    var tipo_lineas_valores = TIPO_LINEAS_VALORES_ESTANDAR;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS: {
            tipo_lineas_valores = TIPO_LINEAS_VALORES_CUADRADAS;
            break;
        }
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_luz = dame_numero_maximo_valores_series_grafica(grafica_luz);
    var mostrar_indicadores_valores = (numero_valores_grafica_luz <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de luz
    if (mostrar_grafica_luz == true) {
        // Líneas de la gráfica de valores
        var lineas_grafica_valores = null;
        if (lineas_comentarios != null) {
            lineas_grafica_valores = [];
            for (var i = 0; i < lineas_comentarios.length; i++) {
                lineas_grafica_valores.push(lineas_comentarios[i]);
            }
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_LUZ_INTERIOR, CAMPO_ILUMINACION);
        if (lineas_referencia != null) {
            if (lineas_grafica_valores == null) {
                lineas_grafica_valores = [];
            }
            for (var i = 0; i < lineas_referencia.length; i++) {
                lineas_grafica_valores.push(lineas_referencia[i]);
            }
        }

        // Se dibuja la gráfica
        var titulo_grafica_luz = TLNT.Idiomas._("Iluminación") + " (" + TLNT.Idiomas._("luxes") + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_luz,
            null,
            titulo_grafica_luz,
            etiquetas_graficas,
            grafica_luz, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_luz, true,
            numero_decimales_valores_luz, TLNT.Idiomas._("luxes"),
            lineas_grafica_valores,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de luz artificial
    if (mostrar_grafica_luz_artificial == true) {
        var titulo_grafica_luz_artificial = TLNT.Idiomas._("Luz artificial");
        muestra_grafica_temporal_valores_si_no(
            id_grafica_luz_artificial,
            titulo_grafica_luz_artificial,
            etiquetas_graficas,
            grafica_luz_artificial, intervalo_valores,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            lineas_comentarios,
            mostrar_lineas_valores,
            tipo_lineas_valores,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor de iluminación
    if (mostrar_mapa_calor_luz == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_iluminacion = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_iluminacion = TLNT.Idiomas._("Mapa de calor diario de iluminación") + " (" + TLNT.Idiomas._("luxes") + ")";
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_iluminacion = TLNT.Idiomas._("Mapa de calor semanal de iluminación") + " (" + TLNT.Idiomas._("luxes") + ")";
                    break;
                }
            }
            muestra_grafico_mapa_calor(
                id_mapa_calor_luz,
                tipo_mapa_calor,
                titulo_mapa_calor_iluminacion,
                dias_mapas_calor,
                null,
                datos_mapa_calor_luz,
                null,
                null,
                true,
                ESCALA_COLORES_NEGRO_AMARILLO,
                altura_maxima_mapa_calor_luz,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }

    // Mapa de calor de luz artificial
    if (mostrar_mapa_calor_luz_artificial == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_luz_artificial = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_luz_artificial = TLNT.Idiomas._("Mapa de calor diario de luz artificial") + " (" + "%" + ")";
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_luz_artificial = TLNT.Idiomas._("Mapa de calor semanal de luz artificial") + " (" + "%" + ")";
                    break;
                }
            }
            muestra_grafico_mapa_calor(
                id_mapa_calor_luz_artificial,
                tipo_mapa_calor,
                titulo_mapa_calor_luz_artificial,
                dias_mapas_calor,
                null,
                datos_mapa_calor_luz_artificial,
                null,
                null,
                true,
                ESCALA_COLORES_ROJO_VERDE,
                altura_maxima_mapa_calor_luz_artificial,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }
}


// Dibujado del informe de información de sensores (viento)
function dibuja_informe_sensores_informacion_viento(
    parametros,
    datos,
    lineas_comentarios,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var max_velocidad_media = datos.max_velocidad_media;
    var max_frecuencia = datos.max_frecuencia;
    var max_velocidad = datos.max_velocidad;
    var datos_viento = datos.datos_viento;
    var etiquetas_graficas = datos.etiquetas_graficas;
    var grafica_velocidad = datos.grafica_velocidad;
    var grafica_direccion = datos.grafica_direccion;
    var dias_mapas_calor = datos.dias_mapas_calor;
    var datos_mapa_calor_velocidad = datos.datos_mapa_calor_velocidad;
    var datos_mapa_calor_direccion = datos.datos_mapa_calor_direccion;
    var texto_informacion_datos = datos.texto_informacion_datos;
    var numeros_decimales_valores_velocidad = datos.numeros_decimales_valores_velocidad;
    var numeros_decimales_valores_direccion = datos.numeros_decimales_valores_direccion;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_velocidad_viento = parametros.id_grafica_velocidad_viento;
    var id_grafica_direccion_viento = parametros.id_grafica_direccion_viento;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_grafico_frecuencia_viento = parametros.id_grafico_frecuencia_viento;
    var id_grafico_velocidad_viento = parametros.id_grafico_velocidad_viento;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_velocidad_viento = parametros.id_mapa_calor_velocidad_viento;
    var altura_maxima_mapa_calor_velocidad_viento = parametros.altura_maxima_mapa_calor_velocidad_viento;
    var id_mapa_calor_direccion_viento = parametros.id_mapa_calor_direccion_viento;
    var altura_maxima_mapa_calor_direccion_viento = parametros.altura_maxima_mapa_calor_direccion_viento;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_INFORMACION_VIENTO,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_velocidad_viento = parametros.mostrar_grafica_velocidad_viento;
    var mostrar_grafica_direccion_viento = parametros.mostrar_grafica_direccion_viento;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_graficos_viento = parametros.mostrar_graficos_viento;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var mostrar_mapa_calor_velocidad_viento = parametros.mostrar_mapa_calor_velocidad_viento;
    var mostrar_mapa_calor_direccion_viento = parametros.mostrar_mapa_calor_direccion_viento;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_velocidad_viento == true) {
        muestra_elemento(id_grafica_velocidad_viento);
    }
    if (mostrar_grafica_direccion_viento == true) {
        muestra_elemento(id_grafica_direccion_viento);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_texto_informacion == true) {
        muestra_elemento(id_texto_informacion_datos);
    }
    if (mostrar_graficos_viento == true) {
        muestra_elementos([
            id_grafico_frecuencia_viento,
            id_grafico_velocidad_viento]);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }
    if (mostrar_mapa_calor_velocidad_viento == true) {
        muestra_elemento(id_mapa_calor_velocidad_viento);
    }
    if (mostrar_mapa_calor_direccion_viento == true) {
        muestra_elemento(id_mapa_calor_direccion_viento);
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

    // Mostrar líneas de valores
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_velocidad = dame_numero_maximo_valores_series_grafica(grafica_velocidad);
    var mostrar_indicadores_valores = (numero_valores_grafica_velocidad <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de velocidad de viento
    if (mostrar_grafica_velocidad_viento == true) {
        // Líneas de la gráfica de valores
        var lineas_grafica_valores = null;
        if (lineas_comentarios != null) {
            lineas_grafica_valores = [];
            for (var i = 0; i < lineas_comentarios.length; i++) {
                lineas_grafica_valores.push(lineas_comentarios[i]);
            }
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_VIENTO, CAMPO_VELOCIDAD);
        if (lineas_referencia != null) {
            if (lineas_grafica_valores == null) {
                lineas_grafica_valores = [];
            }
            for (var i = 0; i < lineas_referencia.length; i++) {
                lineas_grafica_valores.push(lineas_referencia[i]);
            }
        }

        // Se dibuja la gráfica
        var titulo_grafica_velocidad = TLNT.Idiomas._("Velocidad del viento") + " (" + unidad_medida_velocidad + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_velocidad_viento,
            null,
            titulo_grafica_velocidad,
            etiquetas_graficas,
            grafica_velocidad, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_velocidad, true,
            numeros_decimales_valores_velocidad, unidad_medida_velocidad,
            lineas_grafica_valores,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de dirección de viento
    if (mostrar_grafica_direccion_viento == true) {
        // Líneas de la gráfica de valores
        var lineas_grafica_valores = null;
        if (lineas_comentarios != null) {
            lineas_grafica_valores = [];
            for (var i = 0; i < lineas_comentarios.length; i++) {
                lineas_grafica_valores.push(lineas_comentarios[i]);
            }
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_VIENTO, CAMPO_DIRECCION);
        if (lineas_referencia != null) {
            if (lineas_grafica_valores == null) {
                lineas_grafica_valores = [];
            }
            for (var i = 0; i < lineas_referencia.length; i++) {
                lineas_grafica_valores.push(lineas_referencia[i]);
            }
        }

        // Se dibuja la gráfica
        var titulo_grafica_direccion = TLNT.Idiomas._("Dirección del viento") + " (" + "º" + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_direccion_viento,
            null,
            titulo_grafica_direccion,
            etiquetas_graficas,
            grafica_direccion, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            359, false,
            numeros_decimales_valores_direccion, "º",
            lineas_grafica_valores,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Se muestran los gráficos de viento
    if (mostrar_graficos_viento == true) {
        muestra_graficos_viento(
            id_grafico_frecuencia_viento,
            id_grafico_velocidad_viento,
            datos_viento,
            max_frecuencia,
            max_velocidad_media,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor de velocidad
    if (mostrar_mapa_calor_velocidad_viento == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_velocidad_viento = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_velocidad_viento = TLNT.Idiomas._("Mapa de calor diario de velocidad del viento") + " (" + unidad_medida_velocidad + ")";
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_velocidad_viento = TLNT.Idiomas._("Mapa de calor semanal de velocidad del viento") + " (" + unidad_medida_velocidad + ")";
                    break;
                }
            }
            muestra_grafico_mapa_calor(
                id_mapa_calor_velocidad_viento,
                tipo_mapa_calor,
                titulo_mapa_calor_velocidad_viento,
                dias_mapas_calor,
                null,
                datos_mapa_calor_velocidad,
                null,
                null,
                true,
                ESCALA_COLORES_AZUL_ROJO,
                altura_maxima_mapa_calor_velocidad_viento,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }

    // Mapa de calor de dirección
    if (mostrar_mapa_calor_direccion_viento == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_direccion_viento = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_direccion_viento = TLNT.Idiomas._("Mapa de calor diario de dirección del viento") + " (" + "º" + ")";
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_direccion_viento = TLNT.Idiomas._("Mapa de calor semanal de dirección del viento") + " (" + "º" + ")";
                    break;
                }
            }
            muestra_grafico_mapa_calor(
                id_mapa_calor_direccion_viento,
                tipo_mapa_calor,
                titulo_mapa_calor_direccion_viento,
                dias_mapas_calor,
                null,
                datos_mapa_calor_direccion,
                null,
                null,
                true,
                ESCALA_COLORES_BLANCO_NEGRO,
                altura_maxima_mapa_calor_direccion_viento,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }
}


// Dibujado del informe de información de sensores (energia)
function dibuja_informe_sensores_informacion_energia(
    parametros,
    datos,
    lineas_comentarios,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var suma_valores = datos.suma_valores;
    var etiquetas_graficas = datos.etiquetas_graficas;
    var grafica_valores = datos.grafica_valores;
    var campo_incremental = datos.campo_incremental;
    var grafica_valores_acumulados = datos.grafica_valores_acumulados;
    var colores_mapa_calor_valores = datos.colores_mapa_calor_valores;
    var dias_mapa_calor_valores = datos.dias_mapa_calor_valores;
    var datos_mapa_calor_valores = datos.datos_mapa_calor_valores;
    var texto_informacion_datos = datos.texto_informacion_datos;
    var descripcion_campo = datos.descripcion_campo;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var clase_sensor = parametros.clase_sensor;
    var campo = parametros.campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_grafica_valores_acumulados = parametros.id_grafica_valores_acumulados;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_valores = parametros.id_mapa_calor_valores;
    var altura_maxima_mapa_calor_valores = parametros.altura_maxima_mapa_calor_valores;

    // Información de dibujado del informe
    switch (parametros.clase_sensor) {
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            establece_informacion_dibujado_informe(
                MODULO_SENSORES,
                TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA,
                null);
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            establece_informacion_dibujado_informe(
                MODULO_SENSORES,
                TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA,
                null);
            break;
        }
    }

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_grafica_valores_acumulados = parametros.mostrar_grafica_valores_acumulados;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var mostrar_mapa_calor_valores = parametros.mostrar_mapa_calor_valores;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_grafica_valores_acumulados == true) {
        muestra_elemento(id_grafica_valores_acumulados);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_texto_informacion == true) {
        muestra_elemento(id_texto_informacion_datos);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }
    if (mostrar_mapa_calor_valores == true) {
        muestra_elemento(id_mapa_calor_valores);
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

    // Mostrar líneas de valores
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            break;
        }
    }

    // Tipo de líneas de valores
    var tipo_lineas_valores = TIPO_LINEAS_VALORES_ESTANDAR;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_CUARTOHORA:
        case INTERVALO_VALORES_HORA: {
            switch (campo) {
                case CAMPO_PENALIZABLE:
                case CAMPO_TRAMO: {
                    tipo_lineas_valores = TIPO_LINEAS_VALORES_CUADRADAS;
                    break;
                }
            }
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        // Líneas de la gráfica de valores
        var lineas_grafica_valores = null;
        if (lineas_comentarios != null) {
            lineas_grafica_valores = [];
            for (var i = 0; i < lineas_comentarios.length; i++) {
                lineas_grafica_valores.push(lineas_comentarios[i]);
            }
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(clase_sensor, campo);
        if (lineas_referencia != null) {
            if (lineas_grafica_valores == null) {
                lineas_grafica_valores = [];
            }
            for (var i = 0; i < lineas_referencia.length; i++) {
                lineas_grafica_valores.push(lineas_referencia[i]);
            }
        }

        // Se dibuja la gráfica de valores
        var titulo_grafica_valores = descripcion_campo;
        if (unidad_medida != "") {
            titulo_grafica_valores += " (" + unidad_medida + ")";
        }
        var ajuste_valor_minimo = dame_ajuste_valor_minimo_grafica_valores_sensor(clase_sensor, campo, min_valor);
        var ajuste_valor_maximo = dame_ajuste_valor_maximo_grafica_valores_sensor(clase_sensor, campo, max_valor);
        min_valor = ajuste_valor_minimo.valor_minimo;
        max_valor = ajuste_valor_maximo.valor_maximo;
        var ajustar_valor_minimo = ajuste_valor_minimo.ajustar_valor_minimo;
        var ajustar_valor_maximo = ajuste_valor_maximo.ajustar_valor_maximo;
        var tooltip_personalizado = (campo_incremental == true);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica_valores,
            etiquetas_graficas,
            grafica_valores, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_valor, ajustar_valor_minimo,
            max_valor, ajustar_valor_maximo,
            numero_decimales_valores, unidad_medida,
            lineas_grafica_valores,
            mostrar_lineas_valores,
            tipo_lineas_valores,
            mostrar_indicadores_valores,
            tooltip_personalizado,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de valores acumulados
    if (mostrar_grafica_valores_acumulados == true) {
        if (campo_incremental == true) {
            // Se dibuja la gráfica
            var titulo_grafica_valores_acumulados = descripcion_campo;
            titulo_grafica_valores_acumulados += " (" + TLNT.Idiomas._("acumulado") + ")";
            if (unidad_medida != "") {
                titulo_grafica_valores_acumulados += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_acumulados,
                null,
                titulo_grafica_valores_acumulados,
                etiquetas_graficas,
                grafica_valores_acumulados, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                suma_valores, true,
                numero_decimales_valores, unidad_medida,
                lineas_comentarios,
                mostrar_lineas_valores,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_valores_acumulados);
            }
            else {
                cambia_clase_elemento(id_grafica_valores_acumulados, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_valores_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de valores acumulados (el campo es puntual)"));
            }
        }
    }

    // Mapa de calor de valores
    if (mostrar_mapa_calor_valores == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_valores = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor diario de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor semanal de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
            }
            if (unidad_medida != "") {
                titulo_mapa_calor_valores += " (" + unidad_medida + ")";
            }
            var escala_colores_mapa_calor = dame_escala_colores_mapa_calor(colores_mapa_calor_valores);
            muestra_grafico_mapa_calor(
                id_mapa_calor_valores,
                tipo_mapa_calor,
                titulo_mapa_calor_valores,
                dias_mapa_calor_valores,
                null,
                datos_mapa_calor_valores,
                null,
                null,
                true,
                escala_colores_mapa_calor,
                altura_maxima_mapa_calor_valores,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }
}


// Dibujado del informe de información de sensores (cortes de tensión)
function dibuja_informe_sensores_informacion_cortes_tension(
    parametros,
    datos,
    lineas_comentarios,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var suma_cortes_tension = datos.suma_cortes_tension;
    var etiquetas_graficas = datos.etiquetas_graficas;
    var grafica_cortes_tension = datos.grafica_cortes_tension;
    var grafica_cortes_tension_acumulados = datos.grafica_cortes_tension_acumulados;
    var dias_mapa_calor_cortes_tension = datos.dias_mapa_calor_cortes_tension;
    var datos_mapa_calor_cortes_tension = datos.datos_mapa_calor_cortes_tension;
    var texto_informacion_datos = datos.texto_informacion_datos_cortes;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_cortes_tension = parametros.id_grafica_cortes_tension;
    var id_grafica_cortes_tension_acumulados = parametros.id_grafica_cortes_tension_acumulados;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_cortes_tension = parametros.id_mapa_calor_cortes_tension;
    var altura_maxima_mapa_calor_cortes_tension = parametros.altura_maxima_mapa_calor_cortes_tension;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_cortes_tension = parametros.mostrar_grafica_cortes_tension;
    var mostrar_grafica_cortes_tension_acumulados = parametros.mostrar_grafica_cortes_tension_acumulados;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var mostrar_mapa_calor_cortes_tension = parametros.mostrar_mapa_calor_cortes_tension;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_cortes_tension == true) {
        muestra_elemento(id_grafica_cortes_tension);
    }
    if (mostrar_grafica_cortes_tension_acumulados == true) {
        muestra_elemento(id_grafica_cortes_tension_acumulados);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_texto_informacion == true) {
        muestra_elemento(id_texto_informacion_datos);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }
    if (mostrar_mapa_calor_cortes_tension == true) {
        muestra_elemento(id_mapa_calor_cortes_tension);
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

    // Mostrar líneas de valores
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_cortes_tension = dame_numero_maximo_valores_series_grafica(grafica_cortes_tension);
    var mostrar_indicadores_valores = (numero_valores_grafica_cortes_tension <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de cortes de tension
    if (mostrar_grafica_cortes_tension == true) {
        var titulo_grafica_cortes_tension = TLNT.Idiomas._("Cortes de tensión");
        muestra_grafica_temporal_lineas_valores(
            id_grafica_cortes_tension,
            null,
            titulo_grafica_cortes_tension,
            etiquetas_graficas,
            grafica_cortes_tension, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, true,
            1, true,
            0, "",
            lineas_comentarios,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_CUADRADAS,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de cortes de tensión acumulados
    if (mostrar_grafica_cortes_tension_acumulados == true) {
        var titulo_grafica_cortes_tension_acumulados = TLNT.Idiomas._("Cortes de tensión") + " (" + TLNT.Idiomas._("acumulado") + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_cortes_tension_acumulados,
            null,
            titulo_grafica_cortes_tension_acumulados,
            etiquetas_graficas,
            grafica_cortes_tension_acumulados, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            suma_cortes_tension, true,
            0, "",
            lineas_comentarios,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_CUADRADAS,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor de cortes de tensión
    if (mostrar_mapa_calor_cortes_tension == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_cortes_tension = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_cortes_tension = TLNT.Idiomas._("Mapa de calor diario de cortes de tensión");
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_cortes_tension = TLNT.Idiomas._("Mapa de calor semanal de cortes de tensión");
                    break;
                }
            }
            muestra_grafico_mapa_calor(
                id_mapa_calor_cortes_tension,
                tipo_mapa_calor,
                titulo_mapa_calor_cortes_tension,
                dias_mapa_calor_cortes_tension,
                null,
                datos_mapa_calor_cortes_tension,
                null,
                null,
                true,
                ESCALA_COLORES_VERDE_ROJO,
                altura_maxima_mapa_calor_cortes_tension,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }
}


// Dibujado del informe de información de sensores (compra de energía)
function dibuja_informe_sensores_informacion_compra_energia(
    parametros,
    datos,
    lineas_comentarios,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var min_valor_acumulado = datos.min_valor_acumulado;
    var max_valor_acumulado = datos.max_valor_acumulado;
    var etiquetas_graficas = datos.etiquetas_graficas;
    var grafica_valores = datos.grafica_valores;
    var campo_incremental = datos.campo_incremental;
    var grafica_valores_acumulados = datos.grafica_valores_acumulados;
    var colores_mapa_calor_valores = datos.colores_mapa_calor_valores;
    var dias_mapa_calor_valores = datos.dias_mapa_calor_valores;
    var datos_mapa_calor_valores = datos.datos_mapa_calor_valores;
    var datos_mapa_calor_valores_visibles = datos.datos_mapa_calor_valores_visibles;
    var texto_informacion_datos = datos.texto_informacion_datos;
    var descripcion_campo = datos.descripcion_campo;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var campo = parametros.campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_grafica_valores_acumulados = parametros.id_grafica_valores_acumulados;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_valores = parametros.id_mapa_calor_valores;
    var altura_maxima_mapa_calor_valores = parametros.altura_maxima_mapa_calor_valores;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_grafica_valores_acumulados = parametros.mostrar_grafica_valores_acumulados;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var mostrar_mapa_calor_valores = parametros.mostrar_mapa_calor_valores;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_grafica_valores_acumulados == true) {
        muestra_elemento(id_grafica_valores_acumulados);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_texto_informacion == true) {
        muestra_elemento(id_texto_informacion_datos);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }
    if (mostrar_mapa_calor_valores == true) {
        muestra_elemento(id_mapa_calor_valores);
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

    // Mostrar líneas de valores
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            break;
        }
    }

    // Tipo de líneas de valores
    var tipo_lineas_valores = TIPO_LINEAS_VALORES_ESTANDAR;
    switch (campo) {
        case CAMPO_PENALIZABLE: {
            tipo_lineas_valores = TIPO_LINEAS_VALORES_CUADRADAS;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        // Líneas de la gráfica de valores
        var lineas_grafica_valores = null;
        if (lineas_comentarios != null) {
            lineas_grafica_valores = [];
            for (var i = 0; i < lineas_comentarios.length; i++) {
                lineas_grafica_valores.push(lineas_comentarios[i]);
            }
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_COMPRA_ENERGIA, campo);
        if (lineas_referencia != null) {
            if (lineas_grafica_valores == null) {
                lineas_grafica_valores = [];
            }
            for (var i = 0; i < lineas_referencia.length; i++) {
                lineas_grafica_valores.push(lineas_referencia[i]);
            }
        }

        // Se dibuja la gráfica
        var titulo_grafica_valores = descripcion_campo;
        if (unidad_medida != "") {
            titulo_grafica_valores += " (" + unidad_medida + ")";
        }
        var ajuste_valor_minimo = dame_ajuste_valor_minimo_grafica_valores_sensor(CLASE_SENSOR_COMPRA_ENERGIA, campo, min_valor);
        var ajuste_valor_maximo = dame_ajuste_valor_maximo_grafica_valores_sensor(CLASE_SENSOR_COMPRA_ENERGIA, campo, max_valor);
        min_valor = ajuste_valor_minimo.valor_minimo;
        max_valor = ajuste_valor_maximo.valor_maximo;
        var ajustar_valor_minimo = ajuste_valor_minimo.ajustar_valor_minimo;
        var ajustar_valor_maximo = ajuste_valor_maximo.ajustar_valor_maximo;
        var tooltip_personalizado = (campo_incremental == true);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica_valores,
            etiquetas_graficas,
            grafica_valores, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_valor, ajustar_valor_minimo,
            max_valor, ajustar_valor_maximo,
            numero_decimales_valores, unidad_medida,
            lineas_comentarios,
            mostrar_lineas_valores,
            tipo_lineas_valores,
            mostrar_indicadores_valores,
            tooltip_personalizado,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de valores acumulados
    if (mostrar_grafica_valores_acumulados == true) {
        if (campo_incremental == true) {
            // Valores acumulados mínimo y máximo
            var ajustar_valor_acumulado_minimo = null;
            var ajustar_valor_acumulado_maximo = null;
            if (min_valor_acumulado < 0) {
                ajustar_valor_acumulado_minimo = false;
            }
            else {
                min_valor_acumulado = 0;
                ajustar_valor_acumulado_minimo = true;
            }
            if (max_valor_acumulado > 0) {
                ajustar_valor_acumulado_minimo = false;
            }
            else {
                max_valor_acumulado = 0;
                ajustar_valor_acumulado_maximo = true;
            }

            // Se dibuja la gráfica
            var titulo_grafica_valores_acumulados = descripcion_campo;
            titulo_grafica_valores_acumulados += " (" + TLNT.Idiomas._("acumulado") + ")";
            if (unidad_medida != "") {
                titulo_grafica_valores_acumulados += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_acumulados,
                null,
                titulo_grafica_valores_acumulados,
                etiquetas_graficas,
                grafica_valores_acumulados, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                min_valor_acumulado, ajustar_valor_acumulado_minimo,
                max_valor_acumulado, ajustar_valor_acumulado_maximo,
                numero_decimales_valores, unidad_medida,
                lineas_comentarios,
                mostrar_lineas_valores,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_valores_acumulados);
            }
            else {
                cambia_clase_elemento(id_grafica_valores_acumulados, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_valores_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de valores acumulados (el campo es puntual)"));
            }
        }
    }

    // Mapa de calor de valores
    if (mostrar_mapa_calor_valores == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_valores = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor diario de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor semanal de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
            }
            if (unidad_medida != "") {
                titulo_mapa_calor_valores += " (" + unidad_medida + ")";
            }
            var escala_colores_mapa_calor_valores = dame_escala_colores_mapa_calor(colores_mapa_calor_valores);
            muestra_grafico_mapa_calor(
                id_mapa_calor_valores,
                tipo_mapa_calor,
                titulo_mapa_calor_valores,
                dias_mapa_calor_valores,
                null,
                datos_mapa_calor_valores,
                datos_mapa_calor_valores_visibles,
                null,
                true,
                escala_colores_mapa_calor_valores,
                altura_maxima_mapa_calor_valores,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }
}


// Dibujado del informe de información de sensores (gas)
function dibuja_informe_sensores_informacion_gas(
    parametros,
    datos,
    lineas_comentarios,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var suma_valores = datos.suma_valores;
    var etiquetas_graficas = datos.etiquetas_graficas;
    var grafica_valores = datos.grafica_valores;
    var campo_incremental = datos.campo_incremental;
    var grafica_valores_acumulados = datos.grafica_valores_acumulados;
    var colores_mapa_calor_valores = datos.colores_mapa_calor_valores;
    var dias_mapa_calor_valores = datos.dias_mapa_calor_valores;
    var datos_mapa_calor_valores = datos.datos_mapa_calor_valores;
    var texto_informacion_datos = datos.texto_informacion_datos;
    var descripcion_campo = datos.descripcion_campo;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var campo = parametros.campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_grafica_valores_acumulados = parametros.id_grafica_valores_acumulados;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_valores = parametros.id_mapa_calor_valores;
    var altura_maxima_mapa_calor_valores = parametros.altura_maxima_mapa_calor_valores;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_INFORMACION_GAS,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_grafica_valores_acumulados = parametros.mostrar_grafica_valores_acumulados;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var mostrar_mapa_calor_valores = parametros.mostrar_mapa_calor_valores;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_grafica_valores_acumulados == true) {
        muestra_elemento(id_grafica_valores_acumulados);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_texto_informacion == true) {
        muestra_elemento(id_texto_informacion_datos);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }
    if (mostrar_mapa_calor_valores == true) {
        muestra_elemento(id_mapa_calor_valores);
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

    // Mostrar líneas de valores
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        // Líneas de la gráfica de valores
        var lineas_grafica_valores = null;
        if (lineas_comentarios != null) {
            lineas_grafica_valores = [];
            for (var i = 0; i < lineas_comentarios.length; i++) {
                lineas_grafica_valores.push(lineas_comentarios[i]);
            }
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_GAS, campo);
        if (lineas_referencia != null) {
            if (lineas_grafica_valores == null) {
                lineas_grafica_valores = [];
            }
            for (var i = 0; i < lineas_referencia.length; i++) {
                lineas_grafica_valores.push(lineas_referencia[i]);
            }
        }

        // Se dibuja la gráfica
        var titulo_grafica_valores = descripcion_campo;
        if (unidad_medida != "") {
            titulo_grafica_valores += " (" + unidad_medida + ")";
        }
        var ajuste_valor_minimo = dame_ajuste_valor_minimo_grafica_valores_sensor(CLASE_SENSOR_GAS, campo, min_valor);
        var ajuste_valor_maximo = dame_ajuste_valor_maximo_grafica_valores_sensor(CLASE_SENSOR_GAS, campo, max_valor);
        min_valor = ajuste_valor_minimo.valor_minimo;
        max_valor = ajuste_valor_maximo.valor_maximo;
        var ajustar_valor_minimo = ajuste_valor_minimo.ajustar_valor_minimo;
        var ajustar_valor_maximo = ajuste_valor_maximo.ajustar_valor_maximo;
        var tooltip_personalizado = (campo_incremental == true);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica_valores,
            etiquetas_graficas,
            grafica_valores, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_valor, ajustar_valor_minimo,
            max_valor, ajustar_valor_maximo,
            numero_decimales_valores, unidad_medida,
            lineas_comentarios,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            tooltip_personalizado,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de valores acumulados
    if (mostrar_grafica_valores_acumulados == true) {
        if (campo_incremental == true) {
            // Se dibuja la gráfica
            var titulo_grafica_valores_acumulados = descripcion_campo;
            titulo_grafica_valores_acumulados += " (" + TLNT.Idiomas._("acumulado") + ")";
            if (unidad_medida != "") {
                titulo_grafica_valores_acumulados += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_acumulados,
                null,
                titulo_grafica_valores_acumulados,
                etiquetas_graficas,
                grafica_valores_acumulados, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                suma_valores, true,
                numero_decimales_valores, unidad_medida,
                lineas_comentarios,
                mostrar_lineas_valores,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_valores_acumulados);
            }
            else {
                cambia_clase_elemento(id_grafica_valores_acumulados, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_valores_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de valores acumulados (el campo es puntual)"));
            }
        }
    }

    // Mapa de calor de valores
    if (mostrar_mapa_calor_valores == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_valores = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor diario de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor semanal de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
            }
            if (unidad_medida != "") {
                titulo_mapa_calor_valores += " (" + unidad_medida + ")";
            }
            var escala_colores_mapa_calor_valores = dame_escala_colores_mapa_calor(colores_mapa_calor_valores);
            muestra_grafico_mapa_calor(
                id_mapa_calor_valores,
                tipo_mapa_calor,
                titulo_mapa_calor_valores,
                dias_mapa_calor_valores,
                null,
                datos_mapa_calor_valores,
                null,
                null,
                true,
                escala_colores_mapa_calor_valores,
                altura_maxima_mapa_calor_valores,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }
}


// Dibujado del informe de información de sensores (agua)
function dibuja_informe_sensores_informacion_agua(
    parametros,
    datos,
    lineas_comentarios,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var suma_valores = datos.suma_valores;
    var etiquetas_graficas = datos.etiquetas_graficas;
    var grafica_valores = datos.grafica_valores;
    var campo_incremental = datos.campo_incremental;
    var grafica_valores_acumulados = datos.grafica_valores_acumulados;
    var colores_mapa_calor_valores = datos.colores_mapa_calor_valores;
    var dias_mapa_calor_valores = datos.dias_mapa_calor_valores;
    var datos_mapa_calor_valores = datos.datos_mapa_calor_valores;
    var texto_informacion_datos = datos.texto_informacion_datos;
    var descripcion_campo = datos.descripcion_campo;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var campo = parametros.campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_grafica_valores_acumulados = parametros.id_grafica_valores_acumulados;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_valores = parametros.id_mapa_calor_valores;
    var altura_maxima_mapa_calor_valores = parametros.altura_maxima_mapa_calor_valores;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_INFORMACION_AGUA,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_grafica_valores_acumulados = parametros.mostrar_grafica_valores_acumulados;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var mostrar_mapa_calor_valores = parametros.mostrar_mapa_calor_valores;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_grafica_valores_acumulados == true) {
        muestra_elemento(id_grafica_valores_acumulados);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_texto_informacion == true) {
        muestra_elemento(id_texto_informacion_datos);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }
    if (mostrar_mapa_calor_valores == true) {
        muestra_elemento(id_mapa_calor_valores);
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

    // Mostrar líneas de valores
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        // Líneas de la gráfica de valores
        var lineas_grafica_valores = null;
        if (lineas_comentarios != null) {
            lineas_grafica_valores = [];
            for (var i = 0; i < lineas_comentarios.length; i++) {
                lineas_grafica_valores.push(lineas_comentarios[i]);
            }
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_AGUA, campo);
        if (lineas_referencia != null) {
            if (lineas_grafica_valores == null) {
                lineas_grafica_valores = [];
            }
            for (var i = 0; i < lineas_referencia.length; i++) {
                lineas_grafica_valores.push(lineas_referencia[i]);
            }
        }

        // Se dibuja la gráfica
        var titulo_grafica_valores = descripcion_campo;
        if (unidad_medida != "") {
            titulo_grafica_valores += " (" + unidad_medida + ")";
        }
        var ajuste_valor_minimo = dame_ajuste_valor_minimo_grafica_valores_sensor(CLASE_SENSOR_AGUA, campo, min_valor);
        var ajuste_valor_maximo = dame_ajuste_valor_maximo_grafica_valores_sensor(CLASE_SENSOR_AGUA, campo, max_valor);
        min_valor = ajuste_valor_minimo.valor_minimo;
        max_valor = ajuste_valor_maximo.valor_maximo;
        var ajustar_valor_minimo = ajuste_valor_minimo.ajustar_valor_minimo;
        var ajustar_valor_maximo = ajuste_valor_maximo.ajustar_valor_maximo;
        var tooltip_personalizado = (campo_incremental == true);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica_valores,
            etiquetas_graficas,
            grafica_valores, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_valor, ajustar_valor_minimo,
            max_valor, ajustar_valor_maximo,
            numero_decimales_valores, unidad_medida,
            lineas_comentarios,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            tooltip_personalizado,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de valores acumulados
    if (mostrar_grafica_valores_acumulados == true) {
        if (campo_incremental == true) {
            // Se dibuja la gráfica
            var titulo_grafica_valores_acumulados = descripcion_campo;
            titulo_grafica_valores_acumulados += " (" + TLNT.Idiomas._("acumulado") + ")";
            if (unidad_medida != "") {
                titulo_grafica_valores_acumulados += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_acumulados,
                null,
                titulo_grafica_valores_acumulados,
                etiquetas_graficas,
                grafica_valores_acumulados, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                suma_valores, true,
                numero_decimales_valores, unidad_medida,
                lineas_comentarios,
                mostrar_lineas_valores,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_valores_acumulados);
            }
            else {
                cambia_clase_elemento(id_grafica_valores_acumulados, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_valores_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de valores acumulados (el campo es puntual)"));
            }
        }
    }

    // Mapa de calor de valores
    if (mostrar_mapa_calor_valores == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_valores = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor diario de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor semanal de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
            }
            if (unidad_medida != "") {
                titulo_mapa_calor_valores += " (" + unidad_medida + ")";
            }
            var escala_colores_mapa_calor_valores = dame_escala_colores_mapa_calor(colores_mapa_calor_valores);
            muestra_grafico_mapa_calor(
                id_mapa_calor_valores,
                tipo_mapa_calor,
                titulo_mapa_calor_valores,
                dias_mapa_calor_valores,
                null,
                datos_mapa_calor_valores,
                null,
                null,
                true,
                escala_colores_mapa_calor_valores,
                altura_maxima_mapa_calor_valores,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }
}


// Dibujado del informe de información de sensores (genérica)
function dibuja_informe_sensores_informacion_generica(
    parametros,
    datos,
    lineas_comentarios,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var min_suma_valores = datos.min_suma_valores;
    var max_suma_valores = datos.max_suma_valores;
    var etiquetas_graficas = datos.etiquetas_graficas;
    var tipo_lineas_valores = datos.tipo_lineas_valores;
    var grafica_valores = datos.grafica_valores;
    var campo_incremental = datos.campo_incremental;
    var grafica_valores_acumulados = datos.grafica_valores_acumulados;
    var colores_mapa_calor_valores = datos.colores_mapa_calor_valores;
    var dias_mapa_calor_valores = datos.dias_mapa_calor_valores;
    var datos_mapa_calor_valores = datos.datos_mapa_calor_valores;
    var texto_informacion_datos = datos.texto_informacion_datos;
    var nombre_medida = datos.nombre_medida;
    var descripcion_campo = datos.descripcion_campo;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var campo = parametros.campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_grafica_valores_acumulados = parametros.id_grafica_valores_acumulados;
    var id_descripcion_sensor = parametros.id_descripcion_sensor;
    var id_texto_informacion_datos = parametros.id_texto_informacion_datos;
    var id_contenedor_tabla_comentarios = parametros.id_contenedor_tabla_comentarios;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_valores = parametros.id_mapa_calor_valores;
    var altura_maxima_mapa_calor_valores = parametros.altura_maxima_mapa_calor_valores;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_INFORMACION_GENERICA,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_grafica_valores_acumulados = parametros.mostrar_grafica_valores_acumulados;
    var mostrar_descripcion_sensor = parametros.mostrar_descripcion_sensor;
    var mostrar_texto_informacion = parametros.mostrar_texto_informacion;
    var mostrar_tabla_comentarios = parametros.mostrar_tabla_comentarios;
    var mostrar_mapa_calor_valores = parametros.mostrar_mapa_calor_valores;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_grafica_valores_acumulados == true) {
        muestra_elemento(id_grafica_valores_acumulados);
    }
    if (mostrar_descripcion_sensor == true) {
        muestra_elemento(id_descripcion_sensor);
    }
    if (mostrar_texto_informacion == true) {
        muestra_elemento(id_texto_informacion_datos);
    }
    if (mostrar_tabla_comentarios == true) {
        muestra_elemento(id_contenedor_tabla_comentarios);
    }
    if (mostrar_mapa_calor_valores == true) {
        muestra_elemento(id_mapa_calor_valores);
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

    // Mostrar líneas de valores
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        var titulo_grafica_valores = "";
        if (nombre_medida != "") {
            if (unidad_medida != "") {
                titulo_grafica_valores = nombre_medida.capitalize() + " (" + unidad_medida + ")" + " (" + descripcion_campo.uncapitalize() + ")";
            }
            else {
                titulo_grafica_valores = nombre_medida.capitalize() + " (" + descripcion_campo.uncapitalize() + ")";
            }
        }
        else {
            if (unidad_medida != "") {
                titulo_grafica_valores = descripcion_campo + " (" + unidad_medida + ")";
            }
            else {
                titulo_grafica_valores = descripcion_campo;
            }
        };
        var tooltip_personalizado = (campo_incremental == true);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica_valores,
            etiquetas_graficas,
            grafica_valores, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_valor, true,
            max_valor, true,
            numero_decimales_valores, unidad_medida,
            lineas_comentarios,
            mostrar_lineas_valores,
            tipo_lineas_valores,
            mostrar_indicadores_valores,
            tooltip_personalizado,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de valores acumulados
    if (mostrar_grafica_valores_acumulados == true) {
        if (campo_incremental == true) {
            // Se dibuja la gráfica
            var titulo_grafica_valores_acumulados = "";
            if (nombre_medida != "") {
                if (unidad_medida != "") {
                    titulo_grafica_valores_acumulados = nombre_medida.capitalize() + " (" + TLNT.Idiomas._("acumulado") + ")" +
                        " (" + unidad_medida + ")" + " (" + descripcion_campo.uncapitalize() + ")";
                }
                else {
                    titulo_grafica_valores_acumulados = nombre_medida.capitalize() + " (" + TLNT.Idiomas._("acumulado") + ")" +
                        " (" + descripcion_campo.uncapitalize() + ")";
                }
            }
            else {
                if (unidad_medida != "") {
                    titulo_grafica_valores_acumulados = descripcion_campo + " (" + TLNT.Idiomas._("acumulado") + ")" +
                        " (" + unidad_medida + ")";
                }
                else {
                    titulo_grafica_valores_acumulados = descripcion_campo + " (" + TLNT.Idiomas._("acumulado") + ")";
                }
            };
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_acumulados,
                null,
                titulo_grafica_valores_acumulados,
                etiquetas_graficas,
                grafica_valores_acumulados, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                min_suma_valores, true,
                max_suma_valores, true,
                numero_decimales_valores, unidad_medida,
                lineas_comentarios,
                mostrar_lineas_valores,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_valores_acumulados);
            }
            else {
                cambia_clase_elemento(id_grafica_valores_acumulados, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_valores_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de valores acumulados (el campo es puntual)"));
            }
        }
    }

    // Mapa de calor de valores
    if (mostrar_mapa_calor_valores == true) {
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var titulo_mapa_calor_valores = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor diario de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_valores = TLNT.Idiomas._("Mapa de calor semanal de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
            }
            if (unidad_medida != "") {
                titulo_mapa_calor_valores += " (" + unidad_medida + ")";
            }
            var escala_colores_mapa_calor_valores = dame_escala_colores_mapa_calor(colores_mapa_calor_valores);
            muestra_grafico_mapa_calor(
                id_mapa_calor_valores,
                tipo_mapa_calor,
                titulo_mapa_calor_valores,
                dias_mapa_calor_valores,
                null,
                datos_mapa_calor_valores,
                null,
                null,
                true,
                escala_colores_mapa_calor_valores,
                altura_maxima_mapa_calor_valores,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (sensores - información)
function dibuja_elemento_plantilla_informe_sensores_informacion(
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

    // Parámetros del elemento
    var fecha_hora_inicio_consulta = parametros_elemento.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros_elemento.fecha_hora_fin_consulta;

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Selección de clase de sensor
    var clase_sensor = parametros_tipo["clase_sensor"];
    switch (clase_sensor) {
        case CLASE_SENSOR_TEMPERATURA: {
            var campo = parametros_tipo["campo"];
            var intervalo_valores = parametros_tipo["intervalo_valores"];
            var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];
            var comentarios = parametros_tipo["comentarios"];

            // Se dibuja el elemento
            var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-temperatura";
            var id_grafica_temperatura = prefijo_elemento + "grafica-informacion-temperatura";
            var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-informacion-temperatura";
            var id_texto_informacion_datos = prefijo_elemento + "texto-informacion-datos-informacion-temperatura";
            var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-informacion-temperatura";
            var id_mapa_calor_temperatura = prefijo_elemento + "mapa-calor-informacion-temperatura";

            var parametros = {
                fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                fecha_hora_fin_consulta: fecha_hora_fin_consulta,
                id_parametros_resultado_informe: id_parametros_resultado_informe,
                intervalo_valores: intervalo_valores,
                id_grafica_temperatura: id_grafica_temperatura,
                id_descripcion_sensor: id_descripcion_sensor,
                id_texto_informacion_datos: id_texto_informacion_datos,
                comentarios: comentarios,
                id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
                tipo_mapa_calor: tipo_mapa_calor,
                id_mapa_calor_temperatura: id_mapa_calor_temperatura,
                altura_maxima_mapa_calor_temperatura: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
            dibuja_informe_sensores_informacion(
                CLASE_SENSOR_TEMPERATURA,
                parametros,
                datos_elemento,
                elementos_informe_elemento,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_HUMEDAD: {
            var intervalo_valores = parametros_tipo["intervalo_valores"];
            var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];
            var comentarios = parametros_tipo["comentarios"];

            // Se dibuja el elemento
            var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-humedad";
            var id_grafica_humedad = prefijo_elemento + "grafica-informacion-humedad";
            var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-informacion-humedad";
            var id_texto_informacion_datos = prefijo_elemento + "texto-informacion-datos-informacion-humedad";
            var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-informacion-humedad";
            var id_mapa_calor_humedad = prefijo_elemento + "mapa-calor-informacion-humedad";

            var parametros = {
                fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                fecha_hora_fin_consulta: fecha_hora_fin_consulta,
                id_parametros_resultado_informe: id_parametros_resultado_informe,
                intervalo_valores: intervalo_valores,
                id_grafica_humedad: id_grafica_humedad,
                id_descripcion_sensor: id_descripcion_sensor,
                id_texto_informacion_datos: id_texto_informacion_datos,
                comentarios: comentarios,
                id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
                tipo_mapa_calor: tipo_mapa_calor,
                id_mapa_calor_humedad: id_mapa_calor_humedad,
                altura_maxima_mapa_calor_humedad: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
            dibuja_informe_sensores_informacion(
                CLASE_SENSOR_HUMEDAD,
                parametros,
                datos_elemento,
                elementos_informe_elemento,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_LUZ_INTERIOR: {
            var intervalo_valores = parametros_tipo["intervalo_valores"];
            var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];
            var comentarios = parametros_tipo["comentarios"];

            // Se dibuja el elemento
            var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-luz-interior";
            var id_grafica_luz = prefijo_elemento + "grafica-luz-informacion-luz-interior";
            var id_grafica_luz_artificial = prefijo_elemento + "grafica-luz-artificial-informacion-luz-interior";
            var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-informacion-luz-interior";
            var id_texto_informacion_datos = prefijo_elemento + "texto-informacion-datos-informacion-luz-interior";
            var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-informacion-luz-interior";
            var id_mapa_calor_luz = prefijo_elemento + "mapa-calor-luz-informacion-luz-interior";
            var id_mapa_calor_luz_artificial = prefijo_elemento + "mapa-calor-luz-artificial-informacion-luz-interior";

            var parametros = {
                fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                fecha_hora_fin_consulta: fecha_hora_fin_consulta,
                id_parametros_resultado_informe: id_parametros_resultado_informe,
                intervalo_valores: intervalo_valores,
                id_grafica_luz: id_grafica_luz,
                id_grafica_luz_artificial: id_grafica_luz_artificial,
                id_descripcion_sensor: id_descripcion_sensor,
                id_texto_informacion_datos: id_texto_informacion_datos,
                comentarios: comentarios,
                id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
                tipo_mapa_calor: tipo_mapa_calor,
                id_mapa_calor_luz: id_mapa_calor_luz,
                altura_maxima_mapa_calor_luz: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA,
                id_mapa_calor_luz_artificial: id_mapa_calor_luz_artificial,
                altura_maxima_mapa_calor_luz_artificial: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
            dibuja_informe_sensores_informacion(
                CLASE_SENSOR_LUZ_INTERIOR,
                parametros,
                datos_elemento,
                elementos_informe_elemento,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_VIENTO: {
            var intervalo_valores = parametros_tipo["intervalo_valores"];
            var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];
            var comentarios = parametros_tipo["comentarios"];

            // Se dibuja el elemento
            var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-viento";
            var id_grafica_velocidad_viento = prefijo_elemento + "grafica-velocidad-informacion-viento";
            var id_grafica_direccion_viento = prefijo_elemento + "grafica-direccion-informacion-viento";
            var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-informacion-viento";
            var id_texto_informacion_datos = prefijo_elemento + "texto-informacion-datos-informacion-viento";
            var id_grafico_frecuencia_viento = prefijo_elemento + "grafico-frecuencia-informacion-viento";
            var id_grafico_velocidad_viento = prefijo_elemento + "grafico-velocidad-informacion-viento";
            var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-informacion-viento";
            var id_mapa_calor_velocidad_viento = prefijo_elemento + "mapa-calor-velocidad-informacion-viento";
            var id_mapa_calor_direccion_viento = prefijo_elemento + "mapa-calor-direccion-informacion-viento";

            var parametros = {
                fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                fecha_hora_fin_consulta: fecha_hora_fin_consulta,
                id_parametros_resultado_informe: id_parametros_resultado_informe,
                intervalo_valores: intervalo_valores,
                id_grafica_velocidad_viento: id_grafica_velocidad_viento,
                id_grafica_direccion_viento: id_grafica_direccion_viento,
                id_descripcion_sensor: id_descripcion_sensor,
                id_texto_informacion_datos: id_texto_informacion_datos,
                id_grafico_frecuencia_viento: id_grafico_frecuencia_viento,
                id_grafico_velocidad_viento: id_grafico_velocidad_viento,
                comentarios: comentarios,
                id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
                tipo_mapa_calor: tipo_mapa_calor,
                id_mapa_calor_velocidad_viento: id_mapa_calor_velocidad_viento,
                altura_maxima_mapa_calor_velocidad_viento: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA,
                id_mapa_calor_direccion_viento: id_mapa_calor_direccion_viento,
                altura_maxima_mapa_calor_direccion_viento: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
            dibuja_informe_sensores_informacion(
                CLASE_SENSOR_VIENTO,
                parametros,
                datos_elemento,
                elementos_informe_elemento,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_ENERGIA_ACTIVA:
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            var campo = parametros_tipo["campo"];
            var intervalo_valores = parametros_tipo["intervalo_valores"];
            var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];
            var comentarios = parametros_tipo["comentarios"];

            // Sufijo de controles de tipo de energía
            var sufijo_tipo_energia = null;
            switch (clase_sensor) {
                case CLASE_SENSOR_ENERGIA_ACTIVA: {
                    sufijo_tipo_energia = "activa";
                    break;
                }
                case CLASE_SENSOR_ENERGIA_REACTIVA: {
                    sufijo_tipo_energia = "reactiva";
                    break;
                }
            }

            // Se dibuja el elemento
            var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-energia-" + sufijo_tipo_energia;
            var id_grafica_valores = prefijo_elemento + "grafica-informacion-energia-" + sufijo_tipo_energia;
            var id_grafica_valores_acumulados = prefijo_elemento + "grafica-informacion-energia-" + sufijo_tipo_energia + "-acumulado";
            var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-informacion-energia-" + sufijo_tipo_energia;
            var id_texto_informacion_datos = prefijo_elemento + "texto-informacion-datos-informacion-energia-" + sufijo_tipo_energia;
            var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-informacion-energia-" + sufijo_tipo_energia;
            var id_mapa_calor_valores = prefijo_elemento + "mapa-calor-informacion-energia-" + sufijo_tipo_energia;

            var parametros = {
                clase_sensor: clase_sensor,
                campo: campo,
                fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                fecha_hora_fin_consulta: fecha_hora_fin_consulta,
                id_parametros_resultado_informe: id_parametros_resultado_informe,
                intervalo_valores: intervalo_valores,
                id_grafica_valores: id_grafica_valores,
                id_grafica_valores_acumulados: id_grafica_valores_acumulados,
                id_descripcion_sensor: id_descripcion_sensor,
                id_texto_informacion_datos: id_texto_informacion_datos,
                comentarios: comentarios,
                id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
                tipo_mapa_calor: tipo_mapa_calor,
                id_mapa_calor_valores: id_mapa_calor_valores,
                altura_maxima_mapa_calor_valores: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
            dibuja_informe_sensores_informacion(
                clase_sensor,
                parametros,
                datos_elemento,
                elementos_informe_elemento,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_CORTES_TENSION: {
            var intervalo_valores = parametros_tipo["intervalo_valores"];
            var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];
            var comentarios = parametros_tipo["comentarios"];

            // Se dibuja el elemento
            var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-cortes-tension";
            var id_grafica_cortes_tension = prefijo_elemento + "grafica-informacion-cortes-tension";
            var id_grafica_cortes_tension_acumulados = prefijo_elemento + "grafica-informacion-cortes-tension-acumulado";
            var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-informacion-cortes-tension";
            var id_texto_informacion_datos = prefijo_elemento + "texto-informacion-datos-informacion-cortes-tension";
            var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-informacion-cortes-tension";
            var id_mapa_calor_cortes_tension = prefijo_elemento + "mapa-calor-informacion-cortes-tension";

            var parametros = {
                fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                fecha_hora_fin_consulta: fecha_hora_fin_consulta,
                id_parametros_resultado_informe: id_parametros_resultado_informe,
                intervalo_valores: intervalo_valores,
                id_grafica_cortes_tension: id_grafica_cortes_tension,
                id_grafica_cortes_tension_acumulados: id_grafica_cortes_tension_acumulados,
                id_descripcion_sensor: id_descripcion_sensor,
                id_texto_informacion_datos: id_texto_informacion_datos,
                comentarios: comentarios,
                id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
                tipo_mapa_calor: tipo_mapa_calor,
                id_mapa_calor_cortes_tension: id_mapa_calor_cortes_tension,
                altura_maxima_mapa_calor_cortes_tension: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
            dibuja_informe_sensores_informacion(
                CLASE_SENSOR_CORTES_TENSION,
                parametros,
                datos_elemento,
                elementos_informe_elemento,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_COMPRA_ENERGIA: {
            var intervalo_valores = parametros_tipo["intervalo_valores"];
            var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];
            var comentarios = parametros_tipo["comentarios"];

            // Se dibuja el elemento
            var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-compra-energia";
            var id_grafica_valores = prefijo_elemento + "grafica-informacion-compra-energia";
            var id_grafica_valores_acumulados = prefijo_elemento + "grafica-informacion-compra-energia-acumulado";
            var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-informacion-compra-energia";
            var id_texto_informacion_datos = prefijo_elemento + "texto-informacion-datos-informacion-compra-energia";
            var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-informacion-compra-energia";
            var id_mapa_calor_valores = prefijo_elemento + "mapa-calor-informacion-compra-energia";

            var parametros = {
                fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                fecha_hora_fin_consulta: fecha_hora_fin_consulta,
                id_parametros_resultado_informe: id_parametros_resultado_informe,
                intervalo_valores: intervalo_valores,
                id_grafica_valores: id_grafica_valores,
                id_grafica_valores_acumulados: id_grafica_valores_acumulados,
                id_descripcion_sensor: id_descripcion_sensor,
                id_texto_informacion_datos: id_texto_informacion_datos,
                comentarios: comentarios,
                id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
                tipo_mapa_calor: tipo_mapa_calor,
                id_mapa_calor_valores: id_mapa_calor_valores,
                altura_maxima_mapa_calor_valores: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
            dibuja_informe_sensores_informacion(
                CLASE_SENSOR_COMPRA_ENERGIA,
                parametros,
                datos_elemento,
                elementos_informe_elemento,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_GAS: {
            var intervalo_valores = parametros_tipo["intervalo_valores"];
            var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];
            var comentarios = parametros_tipo["comentarios"];

            // Se dibuja el elemento
            var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-gas";
            var id_grafica_valores = prefijo_elemento + "grafica-informacion-gas";
            var id_grafica_valores_acumulados = prefijo_elemento + "grafica-informacion-gas-acumulado";
            var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-informacion-gas";
            var id_texto_informacion_datos = prefijo_elemento + "texto-informacion-datos-informacion-gas";
            var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-informacion-gas";
            var id_mapa_calor_valores = prefijo_elemento + "mapa-calor-informacion-gas";

            var parametros = {
                fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                fecha_hora_fin_consulta: fecha_hora_fin_consulta,
                id_parametros_resultado_informe: id_parametros_resultado_informe,
                intervalo_valores: intervalo_valores,
                id_grafica_valores: id_grafica_valores,
                id_grafica_valores_acumulados: id_grafica_valores_acumulados,
                id_descripcion_sensor: id_descripcion_sensor,
                id_texto_informacion_datos: id_texto_informacion_datos,
                comentarios: comentarios,
                id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
                tipo_mapa_calor: tipo_mapa_calor,
                id_mapa_calor_valores: id_mapa_calor_valores,
                altura_maxima_mapa_calor_valores: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
            dibuja_informe_sensores_informacion(
                CLASE_SENSOR_GAS,
                parametros,
                datos_elemento,
                elementos_informe_elemento,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_AGUA: {
            var intervalo_valores = parametros_tipo["intervalo_valores"];
            var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];
            var comentarios = parametros_tipo["comentarios"];

            // Se dibuja el elemento
            var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-agua";
            var id_grafica_valores = prefijo_elemento + "grafica-informacion-agua";
            var id_grafica_valores_acumulados = prefijo_elemento + "grafica-informacion-agua-acumulado";
            var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-informacion-agua";
            var id_texto_informacion_datos = prefijo_elemento + "texto-informacion-datos-informacion-agua";
            var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-informacion-agua";
            var id_mapa_calor_valores = prefijo_elemento + "mapa-calor-informacion-agua";

            var parametros = {
                fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                fecha_hora_fin_consulta: fecha_hora_fin_consulta,
                id_parametros_resultado_informe: id_parametros_resultado_informe,
                intervalo_valores: intervalo_valores,
                id_grafica_valores: id_grafica_valores,
                id_grafica_valores_acumulados: id_grafica_valores_acumulados,
                id_descripcion_sensor: id_descripcion_sensor,
                id_texto_informacion_datos: id_texto_informacion_datos,
                comentarios: comentarios,
                id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
                tipo_mapa_calor: tipo_mapa_calor,
                id_mapa_calor_valores: id_mapa_calor_valores,
                altura_maxima_mapa_calor_valores: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
            dibuja_informe_sensores_informacion(
                CLASE_SENSOR_AGUA,
                parametros,
                datos_elemento,
                elementos_informe_elemento,
                tipo_informe);
            break;
        }
        case CLASE_SENSOR_GENERICA: {
            var intervalo_valores = parametros_tipo["intervalo_valores"];
            var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];
            var comentarios = parametros_tipo["comentarios"];

            // Se dibuja el elemento
            var id_parametros_resultado_informe = prefijo_elemento + "parametros-resultado-informe-informacion-generica";
            var id_grafica_valores = prefijo_elemento + "grafica-informacion-generica";
            var id_grafica_valores_acumulados = prefijo_elemento + "grafica-informacion-generica-acumulado";
            var id_descripcion_sensor = prefijo_elemento + "descripcion-sensor-informacion-generica";
            var id_texto_informacion_datos = prefijo_elemento + "texto-informacion-datos-informacion-generica";
            var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios-informacion-generica";
            var id_mapa_calor_valores = prefijo_elemento + "mapa-calor-informacion-generica";

            var parametros = {
                fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                fecha_hora_fin_consulta: fecha_hora_fin_consulta,
                id_parametros_resultado_informe: id_parametros_resultado_informe,
                intervalo_valores: intervalo_valores,
                id_grafica_valores: id_grafica_valores,
                id_grafica_valores_acumulados: id_grafica_valores_acumulados,
                id_descripcion_sensor: id_descripcion_sensor,
                id_texto_informacion_datos: id_texto_informacion_datos,
                comentarios: comentarios,
                id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
                tipo_mapa_calor: tipo_mapa_calor,
                id_mapa_calor_valores: id_mapa_calor_valores,
                altura_maxima_mapa_calor_valores: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
            dibuja_informe_sensores_informacion(
                CLASE_SENSOR_GENERICA,
                parametros,
                datos_elemento,
                elementos_informe_elemento,
                tipo_informe);
            break;
        }
    }
}


