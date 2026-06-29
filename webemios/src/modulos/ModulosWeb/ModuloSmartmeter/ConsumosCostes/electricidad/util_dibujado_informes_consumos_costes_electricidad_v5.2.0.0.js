//
// Funciones para el dibujado de los informes de consumos y costes (SmartMeter) (electricidad)
//


// Dibujado del informe de consumos y costes por tramo
function dibuja_informe_smartmeter_consumos_costes_tramos_electricidad(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var nombres_tramos = datos.nombres_tramos;
    var min_hora_grafica = new $.jsDate(datos.min_hora_grafica);
    var max_hora_grafica = new $.jsDate(datos.max_hora_grafica);
    var grafica_consumos_tramos_horarios = datos.grafica_consumos_tramos_horarios;
    var grafica_costes_tramos_horarios = datos.grafica_costes_tramos_horarios;
    var numero_huecos_datos_consumos_costes_tramos_horarios = datos.numero_huecos_datos_consumos_costes_tramos_horarios;
    var max_consumo_hora = datos.max_consumo_hora;
    var max_coste_hora = datos.max_coste_hora;
    var min_fecha_grafica = new $.jsDate(datos.min_fecha_grafica);
    var max_fecha_grafica = new $.jsDate(datos.max_fecha_grafica);
    var grafica_consumos_tramos_diarios = datos.grafica_consumos_tramos_diarios;
    var grafica_costes_tramos_diarios = datos.grafica_costes_tramos_diarios;
    var max_consumo_dia = datos.max_consumo_dia;
    var max_coste_dia = datos.max_coste_dia;
    var grafica_medias_consumos_tramos_dias_semana = datos.grafica_medias_consumos_tramos_dias_semana;
    var grafica_medias_costes_tramos_dias_semana = datos.grafica_medias_costes_tramos_dias_semana;
    var max_media_consumo_dia_semana = datos.max_media_consumo_dia_semana;
    var max_media_coste_dia_semana = datos.max_media_coste_dia_semana;
    var tabla_consumos_tramos = datos.tabla_consumos_tramos;
    var tabla_costes_tramos = datos.tabla_costes_tramos;
    var unidad_medida_consumo = datos.unidad_medida_consumo;
    var unidad_medida_coste = datos.unidad_medida_coste;

    // Parámetros
    var id_grafica_consumos_tramos_horarios = parametros.id_grafica_consumos_tramos_horarios;
    var id_grafica_consumos_tramos_diarios = parametros.id_grafica_consumos_tramos_diarios;
    var id_grafica_medias_consumos_tramos_dias_semana = parametros.id_grafica_medias_consumos_tramos_dias_semana;
    var id_contenedor_tabla_consumos_tramos = parametros.id_contenedor_tabla_consumos_tramos;
    var id_grafica_costes_tramos_horarios = parametros.id_grafica_costes_tramos_horarios;
    var id_grafica_costes_tramos_diarios = parametros.id_grafica_costes_tramos_diarios;
    var id_grafica_medias_costes_tramos_dias_semana = parametros.id_grafica_medias_costes_tramos_dias_semana;
    var id_contenedor_tabla_costes_tramos = parametros.id_contenedor_tabla_costes_tramos;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes_electricidad(
        TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS,
        elementos_informe,
        parametros);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_consumos_tramos_horarios = parametros.mostrar_grafica_consumos_tramos_horarios;
    var mostrar_grafica_consumos_tramos_diarios = parametros.mostrar_grafica_consumos_tramos_diarios;
    var mostrar_grafica_medias_consumos_tramos_dias_semana = parametros.mostrar_grafica_medias_consumos_tramos_dias_semana;
    var mostrar_tabla_consumos_tramos = parametros.mostrar_tabla_consumos_tramos;
    var mostrar_grafica_costes_tramos_horarios = parametros.mostrar_grafica_costes_tramos_horarios;
    var mostrar_grafica_costes_tramos_diarios = parametros.mostrar_grafica_costes_tramos_diarios;
    var mostrar_grafica_medias_costes_tramos_dias_semana = parametros.mostrar_grafica_medias_costes_tramos_dias_semana;
    var mostrar_tabla_costes_tramos = parametros.mostrar_tabla_costes_tramos;

    // Se muestran los elementos visibles
    if (mostrar_grafica_consumos_tramos_horarios == true) {
        muestra_elemento(id_grafica_consumos_tramos_horarios);
    }
    if (mostrar_grafica_consumos_tramos_diarios == true) {
        muestra_elemento(id_grafica_consumos_tramos_diarios);
    }
    if (mostrar_grafica_medias_consumos_tramos_dias_semana == true) {
        muestra_elemento(id_grafica_medias_consumos_tramos_dias_semana);
    }
    if (mostrar_tabla_consumos_tramos == true) {
        muestra_elemento(id_contenedor_tabla_consumos_tramos);
    }
    if (mostrar_grafica_costes_tramos_horarios == true) {
        muestra_elemento(id_grafica_costes_tramos_horarios);
    }
    if (mostrar_grafica_costes_tramos_diarios == true) {
        muestra_elemento(id_grafica_costes_tramos_diarios);
    }
    if (mostrar_grafica_medias_costes_tramos_dias_semana == true) {
        muestra_elemento(id_grafica_medias_costes_tramos_dias_semana);
    }
    if (mostrar_tabla_costes_tramos == true) {
        muestra_elemento(id_contenedor_tabla_costes_tramos);
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

    // Nombres de los días de la semana
    var nombres_dias_semana = dame_nombres_dias_semana();

    // Gráfica de consumos por tramo horarios
    if (mostrar_grafica_consumos_tramos_horarios == true) {
        muestra_grafica_temporal_barras_valores(
            id_grafica_consumos_tramos_horarios,
            TLNT.Idiomas._("Consumos por tramo horarios") + " (" + unidad_medida_consumo + ")",
            nombres_tramos,
            grafica_consumos_tramos_horarios, INTERVALO_VALORES_HORA, numero_huecos_datos_consumos_costes_tramos_horarios,
            min_hora_grafica, max_hora_grafica, true,
            max_consumo_hora, true,
            2, unidad_medida_consumo,
            true, false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de consumos por tramo diarios
    if (mostrar_grafica_consumos_tramos_diarios == true) {
        muestra_grafica_temporal_barras_valores(
            id_grafica_consumos_tramos_diarios,
            TLNT.Idiomas._("Consumos por tramo diarios") + " (" + unidad_medida_consumo + ")",
            nombres_tramos,
            grafica_consumos_tramos_diarios, INTERVALO_VALORES_DIA, 0,
            min_fecha_grafica, max_fecha_grafica, true,
            max_consumo_dia, true,
            2, unidad_medida_consumo,
            true, false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de medias de consumos por tramo por días de la semana
    if (mostrar_grafica_medias_consumos_tramos_dias_semana == true) {
        muestra_grafica_puntual_barras_valores(
            id_grafica_medias_consumos_tramos_dias_semana,
            TLNT.Idiomas._("Media de consumos por tramo por día de la semana") + " (" + unidad_medida_consumo + ")",
            nombres_tramos,
            grafica_medias_consumos_tramos_dias_semana,
            nombres_dias_semana, [6, 7],
            max_media_consumo_dia_semana, true,
            2, unidad_medida_consumo,
            true, false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de consumos por tramo
    if (mostrar_tabla_consumos_tramos == true) {
        $("#" + id_contenedor_tabla_consumos_tramos).html(tabla_consumos_tramos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_consumos_tramos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_consumos_tramos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_consumos_tramos, info_menu_contextual, TLNT.Idiomas._('Consumos por tramo'));
        }
    }

    // Gráfica de costes por tramo horarios
    if (mostrar_grafica_costes_tramos_horarios == true) {
        muestra_grafica_temporal_barras_valores(
            id_grafica_costes_tramos_horarios,
            TLNT.Idiomas._("Costes por tramo horarios") + " (" + unidad_medida_coste + ")",
            nombres_tramos,
            grafica_costes_tramos_horarios, INTERVALO_VALORES_HORA, numero_huecos_datos_consumos_costes_tramos_horarios,
            min_hora_grafica, max_hora_grafica, true,
            max_coste_hora, true,
            2, unidad_medida_coste,
            true, false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes por tramo diarios
    if (mostrar_grafica_costes_tramos_diarios == true) {
        muestra_grafica_temporal_barras_valores(
            id_grafica_costes_tramos_diarios,
            TLNT.Idiomas._("Costes por tramo diarios") + " (" + unidad_medida_coste + ")",
            nombres_tramos,
            grafica_costes_tramos_diarios, INTERVALO_VALORES_DIA, 0,
            min_fecha_grafica, max_fecha_grafica, true,
            max_coste_dia, true,
            2, unidad_medida_coste,
            true, false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de costes por tramo por días de la semana
    if (mostrar_grafica_medias_costes_tramos_dias_semana == true) {
        muestra_grafica_puntual_barras_valores(
            id_grafica_medias_costes_tramos_dias_semana,
            TLNT.Idiomas._("Media de costes por tramo por día de la semana") + " (" + unidad_medida_coste + ")",
            nombres_tramos,
            grafica_medias_costes_tramos_dias_semana,
            nombres_dias_semana, [6, 7],
            max_media_coste_dia_semana, true,
            2, unidad_medida_coste,
            true, false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de costes por tramo
    if (mostrar_tabla_costes_tramos == true) {
        $("#" + id_contenedor_tabla_costes_tramos).html(tabla_costes_tramos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_costes_tramos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_costes_tramos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_costes_tramos, info_menu_contextual, TLNT.Idiomas._('Costes por tramo'));
        }
    }
}


// Dibujado del informe de información de cortes de tensión
function dibuja_informe_smartmeter_cortes_tension_electricidad(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var grafica_consumos_cortes_tension = datos.grafica_consumos_cortes_tension;
    var tabla_cortes_tension = datos.tabla_cortes_tension;
    var etiquetas = datos.etiquetas;
    var etiquetas_unidad = datos.etiquetas_unidad;
    var unidades_medida = datos.unidades_medida;
    var max_consumo = datos.max_consumo;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_consumos_cortes_tension = parametros.id_grafica_consumos_cortes_tension;
    var id_contenedor_tabla_cortes_tension = parametros.id_contenedor_tabla_cortes_tension;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes_electricidad(
        TIPO_INFORME_SMARTMETER_CORTES_TENSION,
        elementos_informe,
        parametros);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_CORTES_TENSION,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_consumos_cortes_tension = parametros.mostrar_grafica_consumos_cortes_tension;
    var mostrar_tabla_cortes_tension = parametros.mostrar_tabla_cortes_tension;

    // Se muestran los elementos visibles
    if (mostrar_grafica_consumos_cortes_tension == true) {
        muestra_elemento(id_grafica_consumos_cortes_tension);
    }
    if (mostrar_tabla_cortes_tension == true) {
        muestra_elemento(id_contenedor_tabla_cortes_tension);
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
    var numero_valores_grafica_consumos_cortes_tension = dame_numero_maximo_valores_series_grafica(grafica_consumos_cortes_tension);
    var mostrar_indicadores_valores = (numero_valores_grafica_consumos_cortes_tension <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de consumos y cortes de tensión
    if (mostrar_grafica_consumos_cortes_tension == true) {
        muestra_grafica_temporal_lineas_valores_ejes_diferentes(
            id_grafica_consumos_cortes_tension,
            TLNT.Idiomas._("Cortes de tensión y consumo"),
            etiquetas_unidad,
            etiquetas,
            grafica_consumos_cortes_tension, INTERVALO_VALORES_TIEMPO_REAL,
            false,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            [0, 0], true,
            [1, max_consumo], true,
            2, unidades_medida,
            true,
            [TIPO_LINEAS_VALORES_CUADRADAS, TIPO_LINEAS_VALORES_ESTANDAR],
            mostrar_indicadores_valores,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de cortes de tensión
    if (mostrar_tabla_cortes_tension == true) {
        $("#" + id_contenedor_tabla_cortes_tension).html(tabla_cortes_tension);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_cortes_tension = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_cortes_tension);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_cortes_tension, info_menu_contextual, TLNT.Idiomas._('Cortes de tensión'));
        }
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes_electricidad(
    tipo_informe_smartmeter_consumos_costes_electricidad,
    elementos_informe,
    parametros) {
    switch (tipo_informe_smartmeter_consumos_costes_electricidad) {
        case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS: {
            var mostrar_grafica_consumos_tramos_horarios = true;
            var mostrar_grafica_consumos_tramos_diarios = true;
            var mostrar_grafica_medias_consumos_tramos_dias_semana = true;
            var mostrar_tabla_consumos_tramos = true;
            var mostrar_grafica_costes_tramos_horarios = true;
            var mostrar_grafica_costes_tramos_diarios = true;
            var mostrar_grafica_medias_costes_tramos_dias_semana = true;
            var mostrar_tabla_costes_tramos = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_CONSUMOS_TRAMOS_HORARIOS) == -1) {
                    mostrar_grafica_consumos_tramos_horarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_CONSUMOS_TRAMOS_DIARIOS) == -1) {
                    mostrar_grafica_consumos_tramos_diarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_MEDIAS_CONSUMOS_TRAMOS_DIAS_SEMANA) == -1) {
                    mostrar_grafica_medias_consumos_tramos_dias_semana = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TABLA_CONSUMOS_TRAMOS) == -1) {
                    mostrar_tabla_consumos_tramos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_COSTES_TRAMOS_HORARIOS) == -1) {
                    mostrar_grafica_costes_tramos_horarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_COSTES_TRAMOS_DIARIOS) == -1) {
                    mostrar_grafica_costes_tramos_diarios = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_MEDIAS_COSTES_TRAMOS_DIAS_SEMANA) == -1) {
                    mostrar_grafica_medias_costes_tramos_dias_semana = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TABLA_COSTES_TRAMOS) == -1) {
                    mostrar_tabla_costes_tramos = false;
                }
            }
            parametros.mostrar_grafica_consumos_tramos_horarios = mostrar_grafica_consumos_tramos_horarios;
            parametros.mostrar_grafica_consumos_tramos_diarios = mostrar_grafica_consumos_tramos_diarios;
            parametros.mostrar_grafica_medias_consumos_tramos_dias_semana = mostrar_grafica_medias_consumos_tramos_dias_semana;
            parametros.mostrar_tabla_consumos_tramos = mostrar_tabla_consumos_tramos;
            parametros.mostrar_grafica_costes_tramos_horarios = mostrar_grafica_costes_tramos_horarios;
            parametros.mostrar_grafica_costes_tramos_diarios = mostrar_grafica_costes_tramos_diarios;
            parametros.mostrar_grafica_medias_costes_tramos_dias_semana = mostrar_grafica_medias_costes_tramos_dias_semana;
            parametros.mostrar_tabla_costes_tramos = mostrar_tabla_costes_tramos;
            break;
        }
        case TIPO_INFORME_SMARTMETER_CORTES_TENSION: {
            var mostrar_grafica_consumos_cortes_tension = true;
            var mostrar_tabla_cortes_tension = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_GRAFICA_CORTES_TENSION_CONSUMOS) == -1) {
                    mostrar_grafica_consumos_cortes_tension = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TABLA_CORTES_TENSION) == -1) {
                    mostrar_tabla_cortes_tension = false;
                }
            }
            parametros.mostrar_grafica_consumos_cortes_tension = mostrar_grafica_consumos_cortes_tension;
            parametros.mostrar_tabla_cortes_tension = mostrar_tabla_cortes_tension;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Smartmeter - Consumos y costes por tramo)
function dibuja_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad(
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
    var id_grafica_consumos_tramos_horarios = prefijo_elemento + "grafica-consumos-tramos-horarios-consumos-costes-tramos";
    var id_grafica_consumos_tramos_diarios = prefijo_elemento + "grafica-consumos-tramos-diarios-consumos-costes-tramos";
    var id_grafica_medias_consumos_tramos_dias_semana = prefijo_elemento + "grafica-consumos-tramos-dias-semana-consumos-costes-tramos";
    var id_contenedor_tabla_consumos_tramos = prefijo_elemento + "contenedor-tabla-consumos-tramos-consumos-costes-tramos";
    var id_grafica_costes_tramos_horarios = prefijo_elemento + "grafica-costes-tramos-horarios-consumos-costes-tramos";
    var id_grafica_costes_tramos_diarios = prefijo_elemento + "grafica-costes-tramos-diarios-consumos-costes-tramos";
    var id_grafica_medias_costes_tramos_dias_semana = prefijo_elemento + "grafica-costes-tramos-dias-semana-consumos-costes-tramos";
    var id_contenedor_tabla_costes_tramos = prefijo_elemento + "contenedor-tabla-costes-tramos-consumos-costes-tramos";

    var parametros = {
        id_grafica_consumos_tramos_horarios: id_grafica_consumos_tramos_horarios,
        id_grafica_consumos_tramos_diarios: id_grafica_consumos_tramos_diarios,
        id_grafica_medias_consumos_tramos_dias_semana: id_grafica_medias_consumos_tramos_dias_semana,
        id_contenedor_tabla_consumos_tramos: id_contenedor_tabla_consumos_tramos,
        id_grafica_costes_tramos_horarios: id_grafica_costes_tramos_horarios,
        id_grafica_costes_tramos_diarios: id_grafica_costes_tramos_diarios,
        id_grafica_medias_costes_tramos_dias_semana: id_grafica_medias_costes_tramos_dias_semana,
        id_contenedor_tabla_costes_tramos: id_contenedor_tabla_costes_tramos};
    dibuja_informe_smartmeter_consumos_costes_tramos_electricidad(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Smartmeter - Cortes de tensión)
function dibuja_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad(
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

    // Se dibuja el elemento
    var id_grafica_consumos_cortes_tension = prefijo_elemento + "grafica-cortes-tension-consumos-cortes-tension";
    var id_contenedor_tabla_cortes_tension = prefijo_elemento + "contenedor-tabla-cortes-tension-cortes-tension";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        id_grafica_consumos_cortes_tension: id_grafica_consumos_cortes_tension,
        id_contenedor_tabla_cortes_tension: id_contenedor_tabla_cortes_tension};
    dibuja_informe_smartmeter_cortes_tension_electricidad(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Smartmeter - Excesos de potencia)
function dibuja_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad(
    info_elemento,
    datos_elemento,
    elementos_informe_elemento,
    parametros_elemento,
    tipo_informe) {
    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            dibuja_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad_Espanya(
                info_elemento,
                datos_elemento,
                elementos_informe_elemento,
                parametros_elemento,
                tipo_informe);
            break;
        }
    }
}


// Dibuja el elemento de una plantilla de informe (Smartmeter - Excesos de energía reactiva)
function dibuja_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad(
    info_elemento,
    datos_elemento,
    elementos_informe_elemento,
    parametros_elemento,
    tipo_informe) {
    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            dibuja_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
                info_elemento,
                datos_elemento,
                elementos_informe_elemento,
                parametros_elemento,
                tipo_informe);
            break;
        }
    }
}