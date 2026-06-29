//
// Funciones para el dibujado de los informes de comparación (Sensores)
//


// Dibujado del informe de comparación de periodos de sensores
function dibuja_informe_sensores_comparacion_periodos(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var msg_aviso = datos.msg_aviso;
    var min_fecha = new $.jsDate(datos.min_fecha);
    var max_fecha = new $.jsDate(datos.max_fecha);
    var grafica_valores = datos.grafica_valores;
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var etiquetas_valores = datos.etiquetas_valores;
    var etiquetas_tooltips_valores = datos.etiquetas_tooltips_valores;
    var tipo_lineas_valores = datos.tipo_lineas_valores;
    var tabla_evolucion_valores = datos.tabla_evolucion_valores;
    var etiquetas_diferencias = datos.etiquetas_diferencias;
    var grafica_diferencias = datos.grafica_diferencias;
    var min_diferencia = datos.min_diferencia;
    var max_diferencia = datos.max_diferencia;
    var campo_incremental = datos.campo_incremental;
    var grafica_diferencias_acumuladas = datos.grafica_diferencias_acumuladas;
    var min_diferencia_acumulada = datos.min_diferencia_acumulada;
    var max_diferencia_acumulada = datos.max_diferencia_acumulada;
    var dias_mapa_calor_diferencias = datos.dias_mapa_calor_diferencias;
    var datos_mapa_calor_diferencias = datos.datos_mapa_calor_diferencias;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;
        
    // Parámetros
    var clase_sensor = parametros.clase_sensor;
    var campo = parametros.campo;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_contenedor_tabla_evolucion_valores = parametros.id_contenedor_tabla_evolucion_valores;
    var id_grafica_diferencias = parametros.id_grafica_diferencias;
    var id_grafica_diferencias_acumuladas = parametros.id_grafica_diferencias_acumuladas;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_diferencias = parametros.id_mapa_calor_diferencias;
    var altura_maxima_mapa_calor_diferencias = parametros.altura_maxima_mapa_calor_diferencias;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_comparacion(
        TIPO_INFORME_SENSORES_COMPARACION_PERIODOS,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_COMPARACION_PERIODOS,
        null);

    // Avisos a mostrar
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        if (msg_aviso != "") {
            jAlert(msg_aviso);
        }
    }

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_tabla_evolucion_valores = parametros.mostrar_tabla_evolucion_valores;
    var mostrar_grafica_diferencias = parametros.mostrar_grafica_diferencias;
    var mostrar_grafica_diferencias_acumuladas = parametros.mostrar_grafica_diferencias_acumuladas;
    var mostrar_mapa_calor_diferencias = parametros.mostrar_mapa_calor_diferencias;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_tabla_evolucion_valores == true) {
        muestra_elemento(id_contenedor_tabla_evolucion_valores);
    }
    if (mostrar_grafica_diferencias == true) {
        muestra_elemento(id_grafica_diferencias);
    }
    if (mostrar_grafica_diferencias_acumuladas == true) {
        muestra_elemento(id_grafica_diferencias_acumuladas);
    }
    if (mostrar_mapa_calor_diferencias == true) {
        muestra_elemento(id_mapa_calor_diferencias);
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
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS: {
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
    }

    // Número de valores de gráfica y de mapa de calor de diferencias
    var numero_valores_grafica_diferencias = dame_numero_maximo_valores_series_grafica(grafica_diferencias);
    var numero_valores_mapa_calor_diferencias = datos_mapa_calor_diferencias.length;

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores de periodos
    if (mostrar_grafica_valores == true) {
        var titulo_grafica = TLNT.Idiomas._("Valores") + " (" + TLNT.Idiomas._("comparación de periodos") + ")";
        if (unidad_medida != "") {
            titulo_grafica += " (" + unidad_medida + ")";
        }
        var ajuste_valor_minimo = dame_ajuste_valor_minimo_grafica_valores_sensor(clase_sensor, campo, min_valor);
        var ajuste_valor_maximo = dame_ajuste_valor_maximo_grafica_valores_sensor(clase_sensor, campo, max_valor);
        min_valor = ajuste_valor_minimo.valor_minimo;
        max_valor = ajuste_valor_maximo.valor_maximo;
        var ajustar_valor_minimo = ajuste_valor_minimo.ajustar_valor_minimo;
        var ajustar_valor_maximo = ajuste_valor_maximo.ajustar_valor_maximo;
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(clase_sensor, campo);
        muestra_grafica_temporal_lineas_valores_fechas_diferentes(
            id_grafica_valores,
            titulo_grafica,
            etiquetas_valores,
            etiquetas_tooltips_valores,
            grafica_valores,
            intervalo_valores,
            min_fecha, max_fecha, true,
            min_valor, ajustar_valor_minimo,
            max_valor, ajustar_valor_maximo,
            numero_decimales_valores, unidad_medida,
            lineas_referencia,
            mostrar_lineas_valores,
            tipo_lineas_valores,
            mostrar_indicadores_valores,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de evolución de valores
    if (mostrar_tabla_evolucion_valores == true) {
        $("#" + id_contenedor_tabla_evolucion_valores).html(tabla_evolucion_valores);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_evolucion_valores = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_evolucion_valores);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_evolucion_valores, info_menu_contextual, TLNT.Idiomas._('Evolución de valores'));
        }
    }

    // Línea de referencia de diferencia 0 a mostrar en la gráfica
    var lineas_referencia = [dame_linea_referencia_grafica_valores(
        0,
        TIPO_LINEA_GRAFICA_HORIZONTAL_DISCONTINUA,
        "rgba(75, 75, 75, 0.25)",
        TLNT.Idiomas._("Diferencia cero"))];

    // Gráfica de diferencias
    if (mostrar_grafica_diferencias == true) {
        if (numero_valores_grafica_diferencias > 0) {
            var titulo_grafica = TLNT.Idiomas._("Diferencias");
            if (unidad_medida != "") {
                titulo_grafica += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_diferencias,
                null,
                titulo_grafica,
                etiquetas_diferencias,
                grafica_diferencias, null, intervalo_valores,
                null,
                min_fecha, max_fecha, true,
                min_diferencia, true,
                max_diferencia, true,
                numero_decimales_valores, unidad_medida,
                lineas_referencia,
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
                oculta_elemento(id_grafica_diferencias);
            }
            else {
                cambia_clase_elemento(id_grafica_diferencias, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_diferencias).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de diferencias (las fechas de los valores de los periodos no se solapan)"));
            }
        }
    }

    // Gráfica de diferencias acumuladas
    if (mostrar_grafica_diferencias_acumuladas == true) {
        if ((campo_incremental == true) && (numero_valores_grafica_diferencias > 0)) {
            var titulo_grafica_diferencias_acumuladas = TLNT.Idiomas._("Diferencias acumuladas");
            if (unidad_medida != "") {
                titulo_grafica_diferencias_acumuladas += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_diferencias_acumuladas,
                null,
                titulo_grafica_diferencias_acumuladas,
                etiquetas_diferencias,
                grafica_diferencias_acumuladas, null, intervalo_valores,
                null,
                min_fecha, max_fecha, true,
                min_diferencia_acumulada, true,
                max_diferencia_acumulada, true,
                numero_decimales_valores, unidad_medida,
                lineas_referencia,
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
                oculta_elemento(id_grafica_diferencias_acumuladas);
            }
            else {
                cambia_clase_elemento(id_grafica_diferencias_acumuladas, "texto-elemento-no-mostrado-informe");
                if (campo_incremental == false) {
                    $("#" + id_grafica_diferencias_acumuladas).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de diferencias acumuladas (el campo es de tipo puntual)"));
                }
                else {
                    $("#" + id_grafica_diferencias_acumuladas).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de diferencias acumuladas (las fechas de los valores de los periodos no se solapan)"));
                }
            }
        }
    }

    // Mapa de calor diario de diferencias de valores de periodos
    if (mostrar_mapa_calor_diferencias == true) {
        if (numero_valores_mapa_calor_diferencias > 0) {
            var titulo_mapa_calor_diferencias = TLNT.Idiomas._("Diferencias horarias por día") + " (" + unidad_medida + ")";
            muestra_grafico_mapa_calor(
                id_mapa_calor_diferencias,
                tipo_mapa_calor,
                titulo_mapa_calor_diferencias,
                dias_mapa_calor_diferencias,
                null,
                datos_mapa_calor_diferencias,
                null,
                null,
                true,
                ESCALA_COLORES_VERDE_ROJO,
                altura_maxima_mapa_calor_diferencias,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_mapa_calor_diferencias);
            }
            else {
                cambia_clase_elemento(id_mapa_calor_diferencias, "texto-elemento-no-mostrado-informe");
                $("#" + id_mapa_calor_diferencias).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra el mapa de calor de diferencias (las fechas de los valores de los periodos no se solapan)"));
            }
        }
    }
}


// Dibujado del informe de comparación con perfil horario de sensores
function dibuja_informe_sensores_comparacion_perfil_horario(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var min_fecha = new $.jsDate(datos.min_fecha);
    var max_fecha = new $.jsDate(datos.max_fecha);
    var etiquetas_grafica_valores = datos.etiquetas_grafica_valores;
    var grafica_valores = datos.grafica_valores;
    var bandas_valores = datos.bandas_valores;
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var grafica_diferencias = datos.grafica_diferencias;
    var min_diferencia = datos.min_diferencia;
    var max_diferencia = datos.max_diferencia;
    var campo_incremental = datos.campo_incremental;
    var grafica_diferencias_acumuladas = datos.grafica_diferencias_acumuladas;
    var min_diferencia_acumulada = datos.min_diferencia_acumulada;
    var max_diferencia_acumulada = datos.max_diferencia_acumulada;
    var dias_mapa_calor_diferencias = datos.dias_mapa_calor_diferencias;
    var datos_mapa_calor_diferencias = datos.datos_mapa_calor_diferencias;
    var etiquetas_grafica_valores_perfil_horario = datos.etiquetas_grafica_valores_perfil_horario;
    var grafica_valores_perfil_horario = datos.grafica_valores_perfil_horario;
    var bandas_valores_perfil_horario = datos.bandas_valores_perfil_horario;
    var min_valor_perfil_horario = datos.min_valor_perfil_horario;
    var max_valor_perfil_horario = datos.max_valor_perfil_horario;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var clase_sensor = parametros.clase_sensor;
    var campo = parametros.campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_grafica_diferencias = parametros.id_grafica_diferencias;
    var id_grafica_diferencias_acumuladas = parametros.id_grafica_diferencias_acumuladas;
    var id_grafica_valores_perfil_horario = parametros.id_grafica_valores_perfil_horario;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_diferencias = parametros.id_mapa_calor_diferencias;
    var altura_maxima_mapa_calor_diferencias = parametros.altura_maxima_mapa_calor_diferencias;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_comparacion(
        TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_grafica_diferencias = parametros.mostrar_grafica_diferencias;
    var mostrar_grafica_diferencias_acumuladas = parametros.mostrar_grafica_diferencias_acumuladas;
    var mostrar_grafica_valores_perfil_horario = parametros.mostrar_grafica_valores_perfil_horario;
    var mostrar_mapa_calor_diferencias = parametros.mostrar_mapa_calor_diferencias;
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
    if (mostrar_grafica_valores_perfil_horario == true) {
        muestra_elemento(id_grafica_valores_perfil_horario);
    }
    if (mostrar_mapa_calor_diferencias == true) {
        muestra_elemento(id_mapa_calor_diferencias);
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
    var numero_valores_grafica_valores_perfil_horario = dame_numero_maximo_valores_series_grafica(grafica_valores_perfil_horario);
    var mostrar_indicadores_valores_perfil_horario = (numero_valores_grafica_valores_perfil_horario <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Número de valores de mapa de calor de diferencias
    var numero_valores_mapa_calor_diferencias = datos_mapa_calor_diferencias.length;

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        var titulo_grafica_valores = TLNT.Idiomas._("Valores");
        if (unidad_medida != "") {
            titulo_grafica_valores += " (" + unidad_medida + ")";
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(clase_sensor, campo);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica_valores,
            etiquetas_grafica_valores,
            grafica_valores, bandas_valores, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
            min_valor, true,
            max_valor, true,
            numero_decimales_valores, unidad_medida,
            lineas_referencia,
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

    // Gráfica de valores de perfil horario
    if (mostrar_grafica_valores_perfil_horario == true) {
        var titulo_grafica_valores_perfil_horario = TLNT.Idiomas._("Valores de perfil horario") + " (" + unidad_medida + ")";
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores_perfil_horario,
            null,
            titulo_grafica_valores_perfil_horario,
            etiquetas_grafica_valores_perfil_horario,
            grafica_valores_perfil_horario, bandas_valores_perfil_horario, intervalo_valores,
            null,
            null, null, false,
            min_valor_perfil_horario, true,
            max_valor_perfil_horario, true,
            2, unidad_medida,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores_perfil_horario,
            true,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Mapa de calor diario de diferencias de valores reales y simulados
    if (mostrar_mapa_calor_diferencias == true) {
        if (numero_valores_mapa_calor_diferencias > 0) {
            var titulo_mapa_calor_diferencias = TLNT.Idiomas._("Diferencias horarias por día") + " (" + unidad_medida + ")";
            muestra_grafico_mapa_calor(
                id_mapa_calor_diferencias,
                tipo_mapa_calor,
                titulo_mapa_calor_diferencias,
                dias_mapa_calor_diferencias,
                null,
                datos_mapa_calor_diferencias,
                null,
                null,
                true,
                ESCALA_COLORES_VERDE_ROJO,
                altura_maxima_mapa_calor_diferencias,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            oculta_elemento(id_mapa_calor_diferencias);
        }
    }
}


// Dibujado del informe de comparación de campos iguales de sensores
function dibuja_informe_sensores_comparacion_campos_iguales(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var min_fecha = new $.jsDate(datos.min_fecha);
    var max_fecha = new $.jsDate(datos.max_fecha);
    var grafica_valores = datos.grafica_valores;
    var min_valor = datos.min_valor;
    var max_valor = datos.max_valor;
    var etiquetas_valores = datos.etiquetas_valores;
    var tipo_lineas_valores = datos.tipo_lineas_valores;
    var tabla_diferencias_valores = datos.tabla_diferencias_valores;
    var grafica_diferencias = datos.grafica_diferencias;
    var min_diferencia = datos.min_diferencia;
    var max_diferencia = datos.max_diferencia;
    var etiquetas_diferencias = datos.etiquetas_diferencias;
    var dias_mapas_calor_diferencias = datos.dias_mapas_calor_diferencias;
    var datos_mapas_calor_diferencias = datos.datos_mapas_calor_diferencias;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var clase_sensor = parametros.clase_sensor;
    var campo = parametros.campo;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_contenedor_tabla_diferencias_valores = parametros.id_contenedor_tabla_diferencias_valores;
    var id_grafica_diferencias = parametros.id_grafica_diferencias;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapas_calor_diferencias = parametros.id_mapas_calor_diferencias;
    var altura_maxima_mapas_calor_diferencias = parametros.altura_maxima_mapas_calor_diferencias;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_comparacion(
        TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_tabla_diferencias_valores = parametros.mostrar_tabla_diferencias_valores;
    var mostrar_grafica_diferencias = parametros.mostrar_grafica_diferencias;
    var mostrar_mapas_calor_diferencias = parametros.mostrar_mapas_calor_diferencias;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_tabla_diferencias_valores == true) {
        muestra_elemento(id_contenedor_tabla_diferencias_valores);
    }
    if (mostrar_grafica_diferencias == true) {
        muestra_elemento(id_grafica_diferencias);
    }
    if (mostrar_mapas_calor_diferencias == true) {
        for (var i = 1; i <= NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; i++) {
            muestra_elemento(id_mapas_calor_diferencias + "-" + i);
        }
    }

    // Comprobación de datos disponibles de diferencias (gráfica y mapas de calor)
    var hay_datos_diferencias = false;
    for (var i = 0; i < grafica_diferencias.length; i++) {
        if (grafica_diferencias[i].length > 0) {
            hay_datos_diferencias = true;
            break;
        }
    }
    if (mostrar_mapas_calor_diferencias == true) {
        var numero_mapas_calor = datos_mapas_calor_diferencias.length;
        var numero_mapas_calor_con_valores = 0;
        for (var i = 0; i < numero_mapas_calor; i++) {
            var numero_valores_mapa_calor_diferencias = datos_mapas_calor_diferencias[i].length;
            if (numero_valores_mapa_calor_diferencias > 0) {
                numero_mapas_calor_con_valores += 1;
            }
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
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS: {
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        var titulo_grafica = TLNT.Idiomas._("Valores");
        if (unidad_medida != "") {
            titulo_grafica += " (" + unidad_medida + ")";
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(clase_sensor, campo);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica,
            etiquetas_valores,
            grafica_valores, null, intervalo_valores,
            null,
            min_fecha, max_fecha, true,
            min_valor, true,
            max_valor, true,
            numero_decimales_valores, unidad_medida,
            lineas_referencia,
            mostrar_lineas_valores,
            tipo_lineas_valores,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de diferencias de valores
    if (mostrar_tabla_diferencias_valores == true) {
        if (tabla_diferencias_valores != "") {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_diferencias_valores).html(tabla_diferencias_valores);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_diferencias_valores = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_diferencias_valores);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_diferencias_valores, info_menu_contextual, TLNT.Idiomas._('Diferencias de valores'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_diferencias_valores);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_diferencias_valores, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_diferencias_valores).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de diferencias de valores (sólo hay valores de un sensor)"));
            }
        }
    }

    // Gráfica de diferencias
    if (mostrar_grafica_diferencias == true) {
        if (hay_datos_diferencias == true) {
            // Línea de referencia de diferencia 0 a mostrar en la gráfica
            var lineas_referencia = [dame_linea_referencia_grafica_valores(
                0,
                TIPO_LINEA_GRAFICA_HORIZONTAL_DISCONTINUA,
                "rgba(75, 75, 75, 0.25)",
                TLNT.Idiomas._("Diferencia cero"))];

            // Se dibuja la gráfica
            var titulo_grafica = TLNT.Idiomas._("Diferencias");
            if (unidad_medida != "") {
                titulo_grafica += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_diferencias,
                null,
                titulo_grafica,
                etiquetas_diferencias,
                grafica_diferencias, null, intervalo_valores,
                null,
                min_fecha, max_fecha, true,
                min_diferencia, true,
                max_diferencia, true,
                2, unidad_medida,
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
                oculta_elemento(id_grafica_diferencias);
            }
            else {
                cambia_clase_elemento(id_grafica_diferencias, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_diferencias).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de diferencias (las fechas de los valores de los sensores no se solapan)"));
            }
        }
    }

    // Mapas de calor de diferencias de valores de sensores
    if (mostrar_mapas_calor_diferencias == true) {
        if (numero_mapas_calor_con_valores > 0) {
            // Se dibujan los mapas de calor
            var numero_mapas_calor = datos_mapas_calor_diferencias.length;
            for (var i = 0; i < numero_mapas_calor; i++) {
                var numero_valores_mapa_calor_diferencias = datos_mapas_calor_diferencias[i].length;
                if (numero_valores_mapa_calor_diferencias > 0) {
                    var titulo_mapa_calor_diferencias = null;
                    switch (tipo_mapa_calor) {
                        case TIPO_MAPA_CALOR_DIARIO: {
                            titulo_mapa_calor_diferencias = TLNT.Idiomas._("Diferencias horarias por día") + " (" + etiquetas_diferencias[i] + ")";
                            break;
                        }
                        case TIPO_MAPA_CALOR_SEMANAL: {
                            titulo_mapa_calor_diferencias = TLNT.Idiomas._("Diferencias horarias por día de la semana") + " (" + etiquetas_diferencias[i] + ")";
                            break;
                        }
                    }
                    if (unidad_medida != "") {
                        titulo_mapa_calor_diferencias += " (" + unidad_medida + ")";
                    }
                    var numero_mapa_calor = i + 1;
                    var id_mapa_calor_diferencias = id_mapas_calor_diferencias + "-" + numero_mapa_calor;
                    muestra_grafico_mapa_calor(
                        id_mapa_calor_diferencias,
                        tipo_mapa_calor,
                        titulo_mapa_calor_diferencias,
                        dias_mapas_calor_diferencias[i],
                        null,
                        datos_mapas_calor_diferencias[i],
                        null,
                        null,
                        true,
                        ESCALA_COLORES_VERDE_ROJO,
                        altura_maxima_mapas_calor_diferencias,
                        mostrar_animaciones,
                        anyadir_menus_contextuales);
                }
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            // Nota: Se muestra el mensaje en el primer mapa de calor de diferencias, el resto se ocultan siempre
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_mapas_calor_diferencias + "-1");
            }
            else {
                cambia_clase_elemento(id_mapas_calor_diferencias + "-1", "texto-elemento-no-mostrado-informe");
                $("#" + id_mapas_calor_diferencias + "-1").html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestran los mapas de calor de diferencias (las fechas de los valores de los sensores no se solapan)"));
            }
            for (var i = 2; i <= NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; i++) {
                oculta_elemento(id_mapas_calor_diferencias + "-" + i);
            }
        }
    }
}


// Dibujado del informe de comparación de campos diferentes de sensores
function dibuja_informe_sensores_comparacion_campos_diferentes(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var grafica_valores = datos.grafica_valores;
    var min_valores = datos.min_valores;
    var max_valores = datos.max_valores;
    var etiquetas_valores = datos.etiquetas_valores;
    var etiquetas_valores_unidad = datos.etiquetas_valores_unidad;
    var tipos_lineas_valores = datos.tipos_lineas_valores;
    var numeros_decimales_valores = datos.numeros_decimales_valores;
    var unidades_medida = datos.unidades_medida;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var unificar_escalas = parametros.unificar_escalas;
    var id_grafica_valores = parametros.id_grafica_valores;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_comparacion(
        TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
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
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS: {
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores de ejes diferentes
    if (mostrar_grafica_valores == true) {
        var titulo_grafica = TLNT.Idiomas._("Valores");
        muestra_grafica_temporal_lineas_valores_ejes_diferentes(
            id_grafica_valores,
            titulo_grafica,
            etiquetas_valores_unidad,
            etiquetas_valores,
            grafica_valores, intervalo_valores,
            unificar_escalas,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_valores, true,
            max_valores, true,
            numeros_decimales_valores, unidades_medida,
            mostrar_lineas_valores,
            tipos_lineas_valores,
            mostrar_indicadores_valores,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }
}


// Dibujado del informe de analisis comparativo de sensores
function dibuja_informe_sensores_analisis_comparativo(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var campo_incremental = datos.campo_incremental;
    var grafica_valores = datos.grafica_valores;
    var grafica_valores_acumulados = datos.grafica_valores_acumulados;
    var tabla_valores_maximos_minimos = datos.tabla_valores_maximos_minimos;
    var grafica_pareto = datos.grafica_pareto;
    var tabla_valores_pareto = datos.tabla_valores_pareto;
    var dias_mapa_calor_diferencias = datos.dias_mapa_calor_diferencias;
    var datos_mapa_calor_diferencias = datos.datos_mapa_calor_diferencias;
    var min_valores = datos.min_valores;
    var max_valores = datos.max_valores;
    var max_sumas_valores = datos.max_sumas_valores;
    var max_valor_pareto = datos.max_valor_pareto;
    var media_valores_pareto = datos.media_valores_pareto;
    var numero_valor_destacado_pareto = datos.numero_valor_destacado_pareto;
    var etiquetas_valores = datos.etiquetas_valores;
    var etiquetas_pareto = datos.etiquetas_pareto;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;
    var descripcion_campo = datos.descripcion_campo;
    var id_sensor_destacado = datos.id_sensor_destacado;
    var nombre_sensor_destacado = datos.nombre_sensor_destacado;

    // Parámetros
    var clase_sensor = parametros.clase_sensor;
    var campo = parametros.campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_grafica_valores_acumulados = parametros.id_grafica_valores_acumulados;
    var id_contenedor_tabla_valores_maximos_minimos = parametros.id_contenedor_tabla_valores_maximos_minimos;
    var id_grafica_pareto = parametros.id_grafica_pareto;
    var id_contenedor_tabla_valores_pareto = parametros.id_contenedor_tabla_valores_pareto;
    var tipo_mapa_calor = parametros.tipo_mapa_calor;
    var id_mapa_calor_diferencias = parametros.id_mapa_calor_diferencias;
    var altura_maxima_mapa_calor_diferencias = parametros.altura_maxima_mapa_calor_diferencias;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_comparacion(
        TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_grafica_valores_acumulados = parametros.mostrar_grafica_valores_acumulados;
    var mostrar_tabla_valores_maximos_minimos = parametros.mostrar_tabla_valores_maximos_minimos;
    var mostrar_grafica_pareto = parametros.mostrar_grafica_pareto;
    var mostrar_tabla_valores_pareto = parametros.mostrar_tabla_valores_pareto;
    var mostrar_mapa_calor_diferencias = parametros.mostrar_mapa_calor_diferencias;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_grafica_valores_acumulados == true) {
        muestra_elemento(id_grafica_valores_acumulados);
    }
    if (mostrar_tabla_valores_maximos_minimos == true) {
        muestra_elemento(id_contenedor_tabla_valores_maximos_minimos);
    }
    if (mostrar_grafica_pareto == true) {
        muestra_elemento(id_grafica_pareto);
    }
    if (mostrar_tabla_valores_pareto == true) {
        muestra_elemento(id_contenedor_tabla_valores_pareto);
    }
    if (mostrar_mapa_calor_diferencias == true) {
        muestra_elemento(id_mapa_calor_diferencias);
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
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS: {
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        var titulo_grafica = TLNT.Idiomas._("Valores");
        if (unidad_medida != "") {
            titulo_grafica += " (" + unidad_medida + ")";
        }
        var valor_minimo = null;
        var ajustar_valor_minimo = null;
        if (campo_incremental == true) {
            valor_minimo = 0;
            ajustar_valor_minimo = false;
        }
        else {
            valor_minimo = min_valores;
            ajustar_valor_minimo = true;
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(clase_sensor, campo);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_valores,
            null,
            titulo_grafica,
            etiquetas_valores,
            grafica_valores, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            valor_minimo, ajustar_valor_minimo,
            max_valores, true,
            numero_decimales_valores, unidad_medida,
            lineas_referencia,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            true,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de valores acumulados
    if (mostrar_grafica_valores_acumulados == true) {
        if (campo_incremental == true) {
            // Se dibuja la gráfica
            var titulo_grafica = TLNT.Idiomas._("Valores acumulados");
            if (unidad_medida != "") {
                titulo_grafica += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_acumulados,
                null,
                titulo_grafica,
                etiquetas_valores,
                grafica_valores_acumulados, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                max_sumas_valores, true,
                numero_decimales_valores, unidad_medida,
                null,
                mostrar_lineas_valores,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                true,
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
                if (campo_incremental == false) {
                    $("#" + id_grafica_valores_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de valores acumulados (el campo es puntual)"));
                }
            }
        }
    }

    // Tabla de valores máximos y mínimos
    if (mostrar_tabla_valores_maximos_minimos == true) {
        $("#" + id_contenedor_tabla_valores_maximos_minimos).html(tabla_valores_maximos_minimos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_valores_maximos_minimos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_valores_maximos_minimos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_valores_maximos_minimos, info_menu_contextual, TLNT.Idiomas._("Valores máximos y mínimos"));
        }
    }

    // Gráfica de pareto
    if (mostrar_grafica_pareto == true) {
        // Mostrado de valores en barras de valores
        var mostrar_valores_barras = (etiquetas_pareto.length <= NUMERO_MAXIMO_BARRAS_VALORES_VALOR_VISIBLE_GRAFICA_100);

        // Se dibuja la gráfica
        var titulo_grafica_pareto = TLNT.Idiomas._("Pareto");
        if (unidad_medida != "") {
            titulo_grafica_pareto += " (" + unidad_medida + ")";
        }
        var lineas_referencia = [dame_linea_referencia_grafica_valores(
            media_valores_pareto,
            TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA,
            colores_graficas_jqplot[0],
            TLNT.Idiomas._("Media de valores"))];
        // Nota: El valor destacado es el 4 valor (media, maximo, mínimo y sensor destacado)
        // (por eso el índice del color es el 3, empieza en el 0)
        var indice_color_barra_valor_destacado_pareto = 3;
        muestra_grafica_pareto(
            id_grafica_pareto,
            titulo_grafica_pareto,
            etiquetas_pareto,
            grafica_pareto,
            max_valor_pareto, true,
            numero_decimales_valores, unidad_medida,
            lineas_referencia,
            numero_valor_destacado_pareto, indice_color_barra_valor_destacado_pareto,
            mostrar_valores_barras,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de valores de pareto
    if (mostrar_tabla_valores_pareto == true) {
        $("#" + id_contenedor_tabla_valores_pareto).html(tabla_valores_pareto);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_valores_pareto = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_valores_pareto);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_valores_pareto, info_menu_contextual, TLNT.Idiomas._("Valores de Pareto"));
        }
    }

    // Número de valores de mapa de calor de diferencias
    var numero_valores_mapa_calor_diferencias = datos_mapa_calor_diferencias.length;

    // Mapa de calor diario de diferencias de valores del sensor destacado con la media
    if (mostrar_mapa_calor_diferencias == true) {
        if (numero_valores_mapa_calor_diferencias > 0) {
            var titulo_mapa_calor_diferencias = null;
            switch (tipo_mapa_calor) {
                case TIPO_MAPA_CALOR_DIARIO: {
                    titulo_mapa_calor_diferencias = TLNT.Idiomas._("Mapa de calor diario de diferencias con la media de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
                case TIPO_MAPA_CALOR_SEMANAL: {
                    titulo_mapa_calor_diferencias = TLNT.Idiomas._("Mapa de calor semanal de diferencias con la media de") + " " + descripcion_campo.uncapitalize();
                    break;
                }
            }
            if (unidad_medida != "") {
                titulo_mapa_calor_diferencias += " (" + unidad_medida + ")";
            }
            titulo_mapa_calor_diferencias += " (" + nombre_sensor_destacado + ")";
            muestra_grafico_mapa_calor(
                id_mapa_calor_diferencias,
                tipo_mapa_calor,
                titulo_mapa_calor_diferencias,
                dias_mapa_calor_diferencias,
                null,
                datos_mapa_calor_diferencias,
                null,
                null,
                true,
                ESCALA_COLORES_VERDE_ROJO,
                altura_maxima_mapa_calor_diferencias,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_mapa_calor_diferencias);
            }
            else {
                cambia_clase_elemento(id_mapa_calor_diferencias, "texto-elemento-no-mostrado-informe");
                if (id_sensor_destacado == ID_NINGUNO) {
                    $("#" + id_mapa_calor_diferencias).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra el mapa de calor de diferencias con la media (no hay sensor destacado seleccionado)"));
                }
                else {
                    $("#" + id_mapa_calor_diferencias).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra el mapa de calor de diferencias con la media (las fechas de los valores no se solapan)"));
                }
            }
        }
    }
}


// Dibujado del informe de valores generales de sensores
function dibuja_informe_sensores_valores_generales(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var limite_sensores_graficas_superado = datos.limite_sensores_graficas_superado;
    var campo_incremental = datos.campo_incremental;
    var grafica_valores = datos.grafica_valores;
    var grafica_valores_acumulados = datos.grafica_valores_acumulados;
    var tabla_valores_maximos_minimos = datos.tabla_valores_maximos_minimos;
    var min_valores = datos.min_valores;
    var max_valores = datos.max_valores;
    var max_sumas_valores = datos.max_sumas_valores;
    var etiquetas_valores = datos.etiquetas_valores;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;

    // Parámetros
    var primera_clase_sensor = parametros.primera_clase_sensor;
    var primer_campo = parametros.primer_campo;
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var intervalo_valores = parametros.intervalo_valores;
    var agregacion = parametros.agregacion;
    var id_grafica_valores = parametros.id_grafica_valores;
    var id_grafica_valores_acumulados = parametros.id_grafica_valores_acumulados;
    var id_contenedor_tabla_valores_maximos_minimos = parametros.id_contenedor_tabla_valores_maximos_minimos;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_comparacion(
        TIPO_INFORME_SENSORES_VALORES_GENERALES,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_VALORES_GENERALES,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_valores = parametros.mostrar_grafica_valores;
    var mostrar_grafica_valores_acumulados = parametros.mostrar_grafica_valores_acumulados;
    var mostrar_tabla_valores_maximos_minimos = parametros.mostrar_tabla_valores_maximos_minimos;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_valores == true) {
        muestra_elemento(id_grafica_valores);
    }
    if (mostrar_grafica_valores_acumulados == true) {
        muestra_elemento(id_grafica_valores_acumulados);
    }
    if (mostrar_tabla_valores_maximos_minimos == true) {
        muestra_elemento(id_contenedor_tabla_valores_maximos_minimos);
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

    // Intervalo de valores (líneas y puntos)
    var mostrar_lineas_valores = true;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS: {
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            mostrar_lineas_valores = false;
            intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
            break;
        }
    }

    // Mostrar indicadores de valores
    if (limite_sensores_graficas_superado == false) {
        var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
        var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);
    }

    // Flag de tooltips personalizados
    var tooltips_personalizados = (agregacion != AGREGACION_NINGUNA);

    // Gráfica de valores
    if (mostrar_grafica_valores == true) {
        // Si no se ha superado el número máximo de sensores de gráficas, se dibujan las gráficas
        if (limite_sensores_graficas_superado == false) {
            // Se dibuja la gráfica
            var titulo_grafica = TLNT.Idiomas._("Valores");
            if (unidad_medida != "") {
                titulo_grafica += " (" + unidad_medida + ")";
            }
            var valor_minimo = null;
            var ajustar_valor_minimo = null;
            if (campo_incremental == true) {
                valor_minimo = 0;
                ajustar_valor_minimo = false;
            }
            else {
                valor_minimo = min_valores;
                ajustar_valor_minimo = true;
            }
            var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(primera_clase_sensor, primer_campo);
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores,
                null,
                titulo_grafica,
                etiquetas_valores,
                grafica_valores, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                valor_minimo, ajustar_valor_minimo,
                max_valores, true,
                numero_decimales_valores, unidad_medida,
                lineas_referencia,
                mostrar_lineas_valores,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                tooltips_personalizados,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_valores);
            }
            else {
                cambia_clase_elemento(id_grafica_valores, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_valores).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de valores") +
                        " (" + TLNT.Idiomas._("se ha superado el límite de sensores") + ")");
            }
        }
    }

    // Gráfica de valores acumulados
    if (mostrar_grafica_valores_acumulados == true) {
        // Si no se ha superado el número máximo de sensores de gráficas, se dibujan las gráficas
        if ((limite_sensores_graficas_superado == false) && (campo_incremental == true)) {
            // Se dibuja la gráfica
            var titulo_grafica = TLNT.Idiomas._("Valores acumulados");
            if (unidad_medida != "") {
                titulo_grafica += " (" + unidad_medida + ")";
            }
            muestra_grafica_temporal_lineas_valores(
                id_grafica_valores_acumulados,
                null,
                titulo_grafica,
                etiquetas_valores,
                grafica_valores_acumulados, null, intervalo_valores,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                0, false,
                max_sumas_valores, true,
                numero_decimales_valores, unidad_medida,
                null,
                mostrar_lineas_valores,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                tooltips_personalizados,
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
                if (limite_sensores_graficas_superado == true) {
                    $("#" + id_grafica_valores_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                        TLNT.Idiomas._("No se muestra la gráfica de valores acumulados") +
                        " (" + TLNT.Idiomas._("se ha superado el límite de sensores") + ")");
                }
                else {
                    if (campo_incremental == false) {
                        $("#" + id_grafica_valores_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                            TLNT.Idiomas._("No se muestra la gráfica de valores acumulados") +
                            " (" + TLNT.Idiomas._("el campo es puntual") + ")");
                    }
                }
            }
        }
    }

    // Tabla de valores máximos y mínimos
    if (mostrar_tabla_valores_maximos_minimos == true) {
        $("#" + id_contenedor_tabla_valores_maximos_minimos).html(tabla_valores_maximos_minimos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_valores_maximos_minimos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_valores_maximos_minimos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_valores_maximos_minimos, info_menu_contextual, TLNT.Idiomas._("Valores máximos y mínimos"));
        }
    }
}


// Dibujado del informe de incrementos totales de sensores
function dibuja_informe_sensores_incrementos_totales(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var limite_sensores_graficas_superado = datos.limite_sensores_graficas_superado;
    var grafica_incrementos_totales = datos.grafica_incrementos_totales;
    var grafica_porcentajes_incrementos = datos.grafica_porcentajes_incrementos;
    var grafica_incrementos = datos.grafica_incrementos;
    var grafica_incrementos_acumulados = datos.grafica_incrementos_acumulados;
    var max_incrementos_totales = datos.max_incrementos_totales;
    var max_incrementos = datos.max_incrementos;
    var max_incrementos_acumulados = datos.max_incrementos_acumulados;
    var etiquetas_incrementos = datos.etiquetas_incrementos;
    var tabla_incrementos = datos.tabla_incrementos;
    var numero_decimales_valores = datos.numero_decimales_valores;
    var unidad_medida = datos.unidad_medida;
    if (parametros.intervalo_valores != INTERVALO_VALORES_TIEMPO_REAL) {
        var min_fecha_graficas_incrementos = new $.jsDate(datos.min_fecha_graficas_incrementos);
        var max_fecha_graficas_incrementos = new $.jsDate(datos.max_fecha_graficas_incrementos);
    }

    // Parámetros
    var intervalo_valores = parametros.intervalo_valores;
    var id_grafica_incrementos_totales = parametros.id_grafica_incrementos_totales;
    var id_grafica_porcentajes_incrementos = parametros.id_grafica_porcentajes_incrementos;
    var id_grafica_incrementos = parametros.id_grafica_incrementos;
    var id_grafica_incrementos_acumulados = parametros.id_grafica_incrementos_acumulados;
    var id_contenedor_tabla_incrementos = parametros.id_contenedor_tabla_incrementos;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_sensores_comparacion(
        TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SENSORES,
        TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_incrementos_totales = parametros.mostrar_grafica_incrementos_totales;
    var mostrar_grafica_porcentajes_incrementos = parametros.mostrar_grafica_porcentajes_incrementos;
    var mostrar_grafica_incrementos = parametros.mostrar_grafica_incrementos;
    var mostrar_grafica_incrementos_acumulados = parametros.mostrar_grafica_incrementos_acumulados;
    var mostrar_tabla_incrementos = parametros.mostrar_tabla_incrementos;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles (por defecto ocultos en plantillas de informes)
    if (mostrar_grafica_incrementos_totales == true) {
        muestra_elemento(id_grafica_incrementos_totales);
    }
    if (mostrar_grafica_porcentajes_incrementos == true) {
        muestra_elemento(id_grafica_porcentajes_incrementos);
    }
    if (mostrar_grafica_incrementos == true) {
        muestra_elemento(id_grafica_incrementos);
    }
    if (mostrar_grafica_incrementos_acumulados == true) {
        muestra_elemento(id_grafica_incrementos_acumulados);
    }
    if (mostrar_tabla_incrementos == true) {
        muestra_elemento(id_contenedor_tabla_incrementos);
    }

    // Si el informe es Web y no es plantilla de informe
    // se muestra un aviso de que se ha superado el límite de sensores para el dibujado de gráficas
    if ((tipo_informe == TIPO_INFORME_WEB_EMIOS) && (elementos_informe == null)) {
        if (limite_sensores_graficas_superado == true) {
            jAlert(TLNT.Idiomas._("Se ha superado el número máximo de sensores para el dibujado de gráficas") +
                " (" + TLNT.Idiomas._("máximo") + ": " + NUMERO_MAXIMO_SENSORES_GRAFICAS_INCREMENTOS_TOTALES + ")");
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

    // Gráfica de incrementos totales
    if (mostrar_grafica_incrementos_totales == true) {
        var mostrar_valores_barras = (etiquetas_incrementos.length <= NUMERO_MAXIMO_BARRAS_VALORES_VALOR_VISIBLE_GRAFICA_100);
        var titulo_grafica_incrementos_totales = TLNT.Idiomas._("Incrementos totales");
        if (unidad_medida != "") {
            titulo_grafica_incrementos_totales += " (" + unidad_medida + ")";
        }
        muestra_grafica_puntual_barras_valores(
            id_grafica_incrementos_totales,
            titulo_grafica_incrementos_totales,
            etiquetas_incrementos,
            grafica_incrementos_totales,
            [TLNT.Idiomas._("Sensores")], null,
            max_incrementos_totales, true,
            numero_decimales_valores, unidad_medida,
            false, mostrar_valores_barras,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de porcentajes de incrementos
    if (mostrar_grafica_porcentajes_incrementos == true) {
        var titulo_grafica_porcentajes_incrementos = TLNT.Idiomas._("Porcentajes de incrementos");
        if (unidad_medida != "") {
            titulo_grafica_porcentajes_incrementos += " (" + unidad_medida + ")";
        }
        var altura_grafica_incrementos_totales = $("#" + id_grafica_incrementos_totales).height();
        $("#" + id_grafica_porcentajes_incrementos).height(altura_grafica_incrementos_totales);
        muestra_grafica_tarta_valores(
            id_grafica_porcentajes_incrementos,
            titulo_grafica_porcentajes_incrementos,
            TLNT.Idiomas._("Sensores"),
            etiquetas_incrementos,
            grafica_porcentajes_incrementos,
            numero_decimales_valores, unidad_medida,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de incrementos
    if (mostrar_grafica_incrementos == true) {
        // Si no se ha superado el número máximo de sensores de gráficas, se dibujan las gráficas
        if (limite_sensores_graficas_superado == false) {
            if (intervalo_valores != INTERVALO_VALORES_TIEMPO_REAL) {
                muestra_grafica_temporal_barras_valores(
                    id_grafica_incrementos,
                    TLNT.Idiomas._("Incrementos") + " (" + unidad_medida + ")",
                    etiquetas_incrementos,
                    grafica_incrementos, intervalo_valores, 0,
                    min_fecha_graficas_incrementos, max_fecha_graficas_incrementos, true,
                    max_incrementos, true,
                    2, unidad_medida,
                    true, false,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);
            }
            else {
                oculta_elemento(id_grafica_incrementos);
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_incrementos);
            }
            else {
                cambia_clase_elemento(id_grafica_incrementos, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_incrementos).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de incrementos") +
                        " (" + TLNT.Idiomas._("se ha superado el límite de sensores") + ")");
            }
        }
    }

    // Gráfica de incrementos acumulados
    if (mostrar_grafica_incrementos_acumulados == true) {
        // Si no se ha superado el número máximo de sensores de gráficas, se dibujan las gráficas
        if (limite_sensores_graficas_superado == false) {
            if (intervalo_valores != INTERVALO_VALORES_TIEMPO_REAL) {
                muestra_grafica_temporal_barras_valores(
                    id_grafica_incrementos_acumulados,
                    TLNT.Idiomas._("Incrementos acumulados") + " (" + unidad_medida + ")",
                    etiquetas_incrementos,
                    grafica_incrementos_acumulados, intervalo_valores, 0,
                    min_fecha_graficas_incrementos, max_fecha_graficas_incrementos, true,
                    max_incrementos_acumulados, true,
                    2, unidad_medida,
                    true, false,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);
            }
            else {
                oculta_elemento(id_grafica_incrementos_acumulados);
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_incrementos_acumulados);
            }
            else {
                cambia_clase_elemento(id_grafica_incrementos_acumulados, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_incrementos_acumulados).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de incrementos acumulados") +
                        " (" + TLNT.Idiomas._("se ha superado el límite de sensores") + ")");
            }
        }
    }

    // Tabla de incrementos
    if (mostrar_tabla_incrementos == true) {
        $("#" + id_contenedor_tabla_incrementos).html(tabla_incrementos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_incrementos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_incrementos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_incrementos, info_menu_contextual, TLNT.Idiomas._("Incrementos"));
        }
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_sensores_comparacion(
    tipo_informe_sensores_comparacion,
    elementos_informe,
    parametros,
    datos) {
    switch (tipo_informe_sensores_comparacion) {
        case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS: {
            var mostrar_grafica_valores = true;
            var mostrar_tabla_evolucion_valores = true;
            var mostrar_grafica_diferencias = true;
            var mostrar_grafica_diferencias_acumuladas = true;
            var mostrar_mapa_calor_diferencias = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_TABLA_EVOLUCION_VALORES) == -1) {
                    mostrar_tabla_evolucion_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_DIFERENCIAS) == -1) {
                    mostrar_grafica_diferencias = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_DIFERENCIAS_ACUMULADAS) == -1) {
                    mostrar_grafica_diferencias_acumuladas = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_MAPA_CALOR_DIFERENCIAS) == -1) {
                    mostrar_mapa_calor_diferencias = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_tabla_evolucion_valores = mostrar_tabla_evolucion_valores;
            parametros.mostrar_grafica_diferencias = mostrar_grafica_diferencias;
            parametros.mostrar_grafica_diferencias_acumuladas = mostrar_grafica_diferencias_acumuladas;
            parametros.mostrar_mapa_calor_diferencias = mostrar_mapa_calor_diferencias;

            // Indica si hay elementos visibles
            var grafica_diferencias = datos.grafica_diferencias;
            var datos_mapa_calor_diferencias = datos.datos_mapa_calor_diferencias;
            var numero_valores_grafica_diferencias = dame_numero_maximo_valores_series_grafica(grafica_diferencias);
            var numero_valores_mapa_calor_diferencias = datos_mapa_calor_diferencias.length;
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                (mostrar_tabla_evolucion_valores == true) ||
                ((mostrar_grafica_diferencias == true) && (numero_valores_grafica_diferencias > 0)) ||
                ((mostrar_grafica_diferencias_acumuladas == true) && (campo_incremental == true)) ||
                ((mostrar_mapa_calor_diferencias == true) && (numero_valores_mapa_calor_diferencias > 0));
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO: {
            var mostrar_grafica_valores = true;
            var mostrar_grafica_diferencias = true;
            var mostrar_grafica_diferencias_acumuladas = true;
            var mostrar_grafica_valores_perfil_horario = true;
            var mostrar_mapa_calor_diferencias = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_DIFERENCIAS) == -1) {
                    mostrar_grafica_diferencias = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_DIFERENCIAS_ACUMULADAS) == -1) {
                    mostrar_grafica_diferencias_acumuladas = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_VALORES_PERFIL_HORARIO) == -1) {
                    mostrar_grafica_valores_perfil_horario = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_MAPA_CALOR_DIFERENCIAS) == -1) {
                    mostrar_mapa_calor_diferencias = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_grafica_diferencias = mostrar_grafica_diferencias;
            parametros.mostrar_grafica_diferencias_acumuladas = mostrar_grafica_diferencias_acumuladas;
            parametros.mostrar_grafica_valores_perfil_horario = mostrar_grafica_valores_perfil_horario;
            parametros.mostrar_mapa_calor_diferencias = mostrar_mapa_calor_diferencias;

            // Indica si hay elementos visibles
            var campo_incremental = datos.campo_incremental;
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                (mostrar_grafica_diferencias == true) ||
                ((mostrar_grafica_diferencias_acumuladas == true) && (campo_incremental == true)) ||
                (mostrar_grafica_valores_perfil_horario == true) ||
                (mostrar_mapa_calor_diferencias == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES: {
            var mostrar_grafica_valores = true;
            var mostrar_tabla_diferencias_valores = true;
            var mostrar_grafica_diferencias = true;
            var mostrar_mapas_calor_diferencias = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TABLA_DIFERENCIAS_VALORES) == -1) {
                    mostrar_tabla_diferencias_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_GRAFICA_DIFERENCIAS) == -1) {
                    mostrar_grafica_diferencias = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_MAPAS_CALOR_DIFERENCIAS) == -1) {
                    mostrar_mapas_calor_diferencias = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_tabla_diferencias_valores = mostrar_tabla_diferencias_valores;
            parametros.mostrar_grafica_diferencias = mostrar_grafica_diferencias;
            parametros.mostrar_mapas_calor_diferencias = mostrar_mapas_calor_diferencias;

            // Indica si hay elementos visibles
            var tabla_diferencias_valores = datos.tabla_diferencias_valores;
            var grafica_diferencias = datos.grafica_diferencias;
            var hay_datos_diferencias = false;
            for (var i = 0; i < grafica_diferencias.length; i++) {
                if (grafica_diferencias[i].length > 0) {
                    hay_datos_diferencias = true;
                    break;
                }
            }
            if (mostrar_mapas_calor_diferencias == true) {
                var datos_mapas_calor_diferencias = datos.datos_mapas_calor_diferencias;
                var numero_mapas_calor = datos_mapas_calor_diferencias.length;
                var numero_mapas_calor_con_valores = 0;
                for (var i = 0; i < numero_mapas_calor; i++) {
                    var numero_valores_mapa_calor_diferencias = datos_mapas_calor_diferencias[i].length;
                    if (numero_valores_mapa_calor_diferencias > 0) {
                        numero_mapas_calor_con_valores += 1;
                    }
                }
            }
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                ((mostrar_tabla_diferencias_valores == true) && (tabla_diferencias_valores == true)) ||
                ((mostrar_grafica_diferencias == true) && (hay_datos_diferencias == true)) ||
                ((mostrar_mapas_calor_diferencias == true) && (numero_mapas_calor_con_valores > 0));
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES: {
            var mostrar_grafica_valores = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            break;
        }
        case TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO: {
            var mostrar_grafica_valores = true;
            var mostrar_grafica_valores_acumulados = true;
            var mostrar_tabla_valores_maximos_minimos = true;
            var mostrar_grafica_pareto = true;
            var mostrar_tabla_valores_pareto = true;
            var mostrar_mapa_calor_diferencias = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_VALORES_ACUMULADOS) == -1) {
                    mostrar_grafica_valores_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_TABLA_VALORES_MAXIMOS_MINIMOS) == -1) {
                    mostrar_tabla_valores_maximos_minimos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_PARETO) == -1) {
                    mostrar_grafica_pareto = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_TABLA_VALORES_PARETO) == -1) {
                    mostrar_tabla_valores_pareto = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_MAPA_CALOR_DIFERENCIAS) == -1) {
                    mostrar_mapa_calor_diferencias = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_grafica_valores_acumulados = mostrar_grafica_valores_acumulados;
            parametros.mostrar_tabla_valores_maximos_minimos = mostrar_tabla_valores_maximos_minimos;
            parametros.mostrar_grafica_pareto = mostrar_grafica_pareto;
            parametros.mostrar_tabla_valores_pareto = mostrar_tabla_valores_pareto;
            parametros.mostrar_mapa_calor_diferencias = mostrar_mapa_calor_diferencias;

            // Indica si hay elementos visibles
            var campo_incremental = true;
            var hay_elementos_visibles =
                (mostrar_grafica_valores == true) ||
                ((mostrar_grafica_valores_acumulados == true) && (campo_incremental == true)) ||
                (mostrar_tabla_valores_maximos_minimos == true) ||
                (mostrar_grafica_pareto == true) ||
                (mostrar_tabla_valores_pareto == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case TIPO_INFORME_SENSORES_VALORES_GENERALES: {
            var mostrar_grafica_valores = true;
            var mostrar_grafica_valores_acumulados = true;
            var mostrar_tabla_valores_maximos_minimos = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_GRAFICA_VALORES) == -1) {
                    mostrar_grafica_valores = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_GRAFICA_VALORES_ACUMULADOS) == -1) {
                    mostrar_grafica_valores_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_TABLA_VALORES_MAXIMOS_MINIMOS) == -1) {
                    mostrar_tabla_valores_maximos_minimos = false;
                }
            }
            parametros.mostrar_grafica_valores = mostrar_grafica_valores;
            parametros.mostrar_grafica_valores_acumulados = mostrar_grafica_valores_acumulados;
            parametros.mostrar_tabla_valores_maximos_minimos = mostrar_tabla_valores_maximos_minimos;

            // Indica si hay elementos visibles
            var limite_sensores_graficas_superado = datos.limite_sensores_graficas_superado;
            var campo_incremental = datos.campo_incremental;
            var hay_elementos_visibles =
                ((mostrar_grafica_valores == true) && (limite_sensores_graficas_superado == false)) ||
                ((mostrar_grafica_valores_acumulados == true) && (limite_sensores_graficas_superado == false) && (campo_incremental == true)) ||
                (mostrar_tabla_valores_maximos_minimos == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
        case TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES: {
            var mostrar_grafica_incrementos_totales = true;
            var mostrar_grafica_porcentajes_incrementos = true;
            var mostrar_grafica_incrementos = true;
            var mostrar_grafica_incrementos_acumulados = true;
            var mostrar_tabla_incrementos = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS_TOTALES) == -1) {
                    mostrar_grafica_incrementos_totales = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_PORCENTAJES_INCREMENTOS) == -1) {
                    mostrar_grafica_porcentajes_incrementos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS) == -1) {
                    mostrar_grafica_incrementos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS_ACUMULADOS) == -1) {
                    mostrar_grafica_incrementos_acumulados = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_TABLA_INCREMENTOS) == -1) {
                    mostrar_tabla_incrementos = false;
                }
            }
            parametros.mostrar_grafica_incrementos_totales = mostrar_grafica_incrementos_totales;
            parametros.mostrar_grafica_porcentajes_incrementos = mostrar_grafica_porcentajes_incrementos;
            parametros.mostrar_grafica_incrementos = mostrar_grafica_incrementos;
            parametros.mostrar_grafica_incrementos_acumulados = mostrar_grafica_incrementos_acumulados;
            parametros.mostrar_tabla_incrementos = mostrar_tabla_incrementos;

            // Indica si hay elementos visibles
            var limite_sensores_graficas_superado = datos.limite_sensores_graficas_superado;
            var hay_elementos_visibles =
                (mostrar_grafica_incrementos_totales == true) ||
                (mostrar_grafica_porcentajes_incrementos == true) ||
                ((mostrar_grafica_incrementos == true) && (limite_sensores_graficas_superado == false)) ||
                ((mostrar_grafica_incrementos_acumulados == true) && (limite_sensores_graficas_superado == false)) ||
                (mostrar_tabla_incrementos == true);
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Sensores - Comparación de periodos)
function dibuja_elemento_plantilla_informe_sensores_comparacion_periodos(
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

    // Clase de sensor y campo
    var clase_sensor = parametros_tipo["clase_sensor"];
    var campo = parametros_tipo["campo"];

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = parametros_tipo["intervalo_valores"];
    var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_valores = prefijo_elemento + "grafica-valores-comparacion-periodos";
    var id_contenedor_tabla_evolucion_valores = prefijo_elemento + "contenedor-tabla-evolucion-valores-comparacion-periodos";
    var id_grafica_diferencias = prefijo_elemento + "grafica-diferencias-comparacion-periodos";
    var id_grafica_diferencias_acumuladas = prefijo_elemento + "grafica-diferencias-acumuladas-comparacion-periodos";
    var id_mapa_calor_diferencias = prefijo_elemento + "mapa-calor-diferencias-comparacion-periodos";

    var parametros = {
        clase_sensor: clase_sensor,
        campo: campo,
        intervalo_valores: intervalo_valores,
        id_grafica_valores: id_grafica_valores,
        id_contenedor_tabla_evolucion_valores: id_contenedor_tabla_evolucion_valores,
        id_grafica_diferencias: id_grafica_diferencias,
        id_grafica_diferencias_acumuladas: id_grafica_diferencias_acumuladas,
        tipo_mapa_calor: tipo_mapa_calor,
        id_mapa_calor_diferencias: id_mapa_calor_diferencias,
        altura_maxima_mapa_calor_diferencias: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
    dibuja_informe_sensores_comparacion_periodos(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Sensores - Comparación con perfil horario)
function dibuja_elemento_plantilla_informe_sensores_comparacion_perfil_horario(
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

    // Clase de sensor y campo
    var clase_sensor = parametros_tipo["clase_sensor"];
    var campo = parametros_tipo["campo"];

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = parametros_tipo["intervalo_valores"];
    var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];

    // Parámetros del elemento
    var fecha_hora_inicio_consulta = parametros_elemento.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros_elemento.fecha_hora_fin_consulta;

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_valores = prefijo_elemento + "grafica-valores-comparacion-perfil-horario";
    var id_grafica_diferencias = prefijo_elemento + "grafica-diferencias-comparacion-perfil-horario";
    var id_grafica_diferencias_acumuladas = prefijo_elemento + "grafica-diferencias-acumuladas-comparacion-perfil-horario";
    var id_grafica_valores_perfil_horario = prefijo_elemento + "grafica-valores-perfil-horario-comparacion-perfil-horario";
    var id_mapa_calor_diferencias = prefijo_elemento + "mapa-calor-diferencias-comparacion-perfil-horario";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        clase_sensor: clase_sensor,
        campo: campo,
        intervalo_valores: intervalo_valores,
        id_grafica_valores: id_grafica_valores,
        id_grafica_diferencias: id_grafica_diferencias,
        id_grafica_diferencias_acumuladas: id_grafica_diferencias_acumuladas,
        id_grafica_valores_perfil_horario: id_grafica_valores_perfil_horario,
        tipo_mapa_calor: tipo_mapa_calor,
        id_mapa_calor_diferencias: id_mapa_calor_diferencias,
        altura_maxima_mapa_calor_diferencias: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
    dibuja_informe_sensores_comparacion_perfil_horario(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Sensores - Comparación de campos iguales)
function dibuja_elemento_plantilla_informe_sensores_comparacion_campos_iguales(
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

    // Clase de sensor y campo
    var clase_sensor = parametros_tipo["clase_sensor"];
    var campo = parametros_tipo["campo"];

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = parametros_tipo["intervalo_valores"];
    var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_valores = prefijo_elemento + "grafica-valores-comparacion-campos-iguales";
    var id_contenedor_tabla_diferencias_valores = prefijo_elemento + "contenedor-tabla-diferencias-valores-comparacion-campos-iguales";
    var id_grafica_diferencias = prefijo_elemento + "grafica-diferencias-comparacion-campos-iguales";
    var id_mapas_calor_diferencias = prefijo_elemento + "mapa-calor-diferencias-comparacion-campos-iguales";

    var parametros = {
        clase_sensor: clase_sensor,
        campo: campo,
        intervalo_valores: intervalo_valores,
        id_grafica_valores: id_grafica_valores,
        id_contenedor_tabla_diferencias_valores: id_contenedor_tabla_diferencias_valores,
        id_grafica_diferencias: id_grafica_diferencias,
        tipo_mapa_calor: tipo_mapa_calor,
        id_mapas_calor_diferencias: id_mapas_calor_diferencias,
        altura_maxima_mapas_calor_diferencias: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
    dibuja_informe_sensores_comparacion_campos_iguales(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Sensores - Comparación de campos diferentes)
function dibuja_elemento_plantilla_informe_sensores_comparacion_campos_diferentes(
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

    // Intervalo de valores y unificar_escalas
    var intervalo_valores = parametros_tipo["intervalo_valores"];
    var unificar_escalas = parametros_tipo["unificar_escalas"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_valores = prefijo_elemento + "grafica-valores-comparacion-campos-diferentes";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        intervalo_valores: intervalo_valores,
        unificar_escalas: unificar_escalas,
        id_grafica_valores: id_grafica_valores};
    dibuja_informe_sensores_comparacion_campos_diferentes(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Sensores - Análisis comparativo)
function dibuja_elemento_plantilla_informe_sensores_analisis_comparativo(
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
        $("#elemento-sin-sensores-agregados-seleccionados-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de sensores agregados seleccionados
    var sin_sensores_agregados_seleccionados = datos_elemento.sin_sensores_agregados_seleccionados;
    if (sin_sensores_agregados_seleccionados == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensores-agregados-seleccionados-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensores-agregados-seleccionados-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Clase de sensor y campo
    var clase_sensor = parametros_tipo["clase_sensor"];
    var campo = parametros_tipo["campo"];

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = parametros_tipo["intervalo_valores"];
    var tipo_mapa_calor = parametros_tipo["tipo_mapa_calor"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_valores = prefijo_elemento + "grafica-valores-analisis-comparativo";
    var id_grafica_valores_acumulados = prefijo_elemento + "grafica-valores-acumulados-analisis-comparativo";
    var id_contenedor_tabla_valores_maximos_minimos = prefijo_elemento + "contenedor-tabla-valores-maximos-minimos-analisis-comparativo";
    var id_grafica_pareto = prefijo_elemento + "grafica-pareto-analisis-comparativo";
    var id_contenedor_tabla_valores_pareto = prefijo_elemento + "contenedor-tabla-valores-pareto-analisis-comparativo";
    var id_mapa_calor_diferencias = prefijo_elemento + "mapa-calor-diferencias-media-analisis-comparativo";

    var parametros = {
        clase_sensor: clase_sensor,
        campo: campo,
        intervalo_valores: intervalo_valores,
        id_grafica_valores: id_grafica_valores,
        id_grafica_valores_acumulados: id_grafica_valores_acumulados,
        id_contenedor_tabla_valores_maximos_minimos: id_contenedor_tabla_valores_maximos_minimos,
        id_grafica_pareto: id_grafica_pareto,
        id_contenedor_tabla_valores_pareto: id_contenedor_tabla_valores_pareto,
        tipo_mapa_calor: tipo_mapa_calor,
        id_mapa_calor_diferencias: id_mapa_calor_diferencias,
        altura_maxima_mapa_calor_diferencias: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO_ILIMITADA};
    dibuja_informe_sensores_analisis_comparativo(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Sensores - Valores generales)
function dibuja_elemento_plantilla_informe_sensores_valores_generales(
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

    // Primera clase de sensor y campo
    var primera_clase_sensor = parametros_tipo.clases_sensor[0];
    var primer_campo = parametros_tipo.campos[0];

    // Intervalo de valores y agregación
    var intervalo_valores = parametros_tipo["intervalo_valores"];
    var agregacion = parametros_tipo["agregacion"];

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_valores = prefijo_elemento + "grafica-valores-valores-generales";
    var id_grafica_valores_acumulados = prefijo_elemento + "grafica-valores-acumulados-valores-generales";
    var id_contenedor_tabla_valores_maximos_minimos = prefijo_elemento + "contenedor-tabla-valores-maximos-minimos-valores-generales";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        primera_clase_sensor: primera_clase_sensor,
        primer_campo: primer_campo,
        intervalo_valores: intervalo_valores,
        agregacion: agregacion,
        id_grafica_valores: id_grafica_valores,
        id_grafica_valores_acumulados: id_grafica_valores_acumulados,
        id_contenedor_tabla_valores_maximos_minimos: id_contenedor_tabla_valores_maximos_minimos};
    dibuja_informe_sensores_valores_generales(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Sensores - Incrementos totales)
function dibuja_elemento_plantilla_informe_sensores_incrementos_totales(
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
    var id_grafica_incrementos_totales = prefijo_elemento + "grafica-incrementos-totales-incrementos-totales";
    var id_grafica_porcentajes_incrementos = prefijo_elemento + "grafica-porcentajes-incrementos-incrementos-totales";
    var id_grafica_incrementos = prefijo_elemento + "grafica-incrementos-incrementos-totales";
    var id_grafica_incrementos_acumulados = prefijo_elemento + "grafica-incrementos-acumulados-incrementos-totales";
    var id_contenedor_tabla_incrementos = prefijo_elemento + "contenedor-tabla-incrementos-incrementos-totales";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        intervalo_valores: intervalo_valores,
        id_grafica_incrementos_totales: id_grafica_incrementos_totales,
        id_grafica_porcentajes_incrementos: id_grafica_porcentajes_incrementos,
        id_grafica_incrementos: id_grafica_incrementos,
        id_grafica_incrementos_acumulados: id_grafica_incrementos_acumulados,
        id_contenedor_tabla_incrementos: id_contenedor_tabla_incrementos};
    dibuja_informe_sensores_incrementos_totales(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}
