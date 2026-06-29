//
// Funciones para el dibujado de los informes de procesado
//


// Dibujado del informe de tiempos de ejecución de procesaado
function dibuja_informe_tiempos_ejecucion_procesado(
    parametros,
    datos) {
    // Datos del resultado
    var max_segundos_ejecucion = datos.max_segundos_ejecucion;
    var etiquetas_grafica_tiempos_ejecucion = datos.etiquetas_grafica_tiempos_ejecucion;
    var grafica_tiempos_ejecucion = datos.grafica_tiempos_ejecucion;
    var texto_informacion_datos_tiempos_ejecucion = datos.texto_informacion_datos_tiempos_ejecucion;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_tiempos_ejecucion = parametros.id_grafica_tiempos_ejecucion;
    var id_texto_informacion_datos_tiempos_ejecucion = parametros.id_texto_informacion_datos_tiempos_ejecucion;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_MONITORIZACION,
        TIPO_INFORME_MONITORIZACION_TIEMPOS_EJECUCION_PROCESADO,
        null);

    // Se muestran los elementos
    muestra_elementos([
        id_grafica_tiempos_ejecucion,
        id_texto_informacion_datos_tiempos_ejecucion]);

    // Flags
    var mostrar_animaciones = true;
    var anyadir_menus_contextuales = true;

    // Mostrar indicadores de valores
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_tiempos_ejecucion);
    var mostrar_indicadores_valores = (numero_valores_grafica_valores <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de tiempos de ejecución de procesado
    muestra_grafica_temporal_lineas_valores(
        id_grafica_tiempos_ejecucion,
        null,
        TLNT.Idiomas._("Tiempos de ejecución") + " (" + TLNT.Idiomas._("segundos") + ")",
        etiquetas_grafica_tiempos_ejecucion,
        grafica_tiempos_ejecucion, null, INTERVALO_VALORES_TIEMPO_REAL,
        null,
        fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
        0, false,
        max_segundos_ejecucion, true,
        2, TLNT.Idiomas._("segundos"),
        null,
        true,
        TIPO_LINEAS_VALORES_ESTANDAR,
        mostrar_indicadores_valores,
        false,
        mostrar_animaciones,
        anyadir_menus_contextuales);

    // Texto de información de tiempos de ejecución de procesado
    $('#' + id_texto_informacion_datos_tiempos_ejecucion).html(texto_informacion_datos_tiempos_ejecucion);
}
