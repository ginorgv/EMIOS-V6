//
// Funciones de informes automáticos de estadística (de Sensores)
//


// Muestra la ventana para añadir el informe automático de histograma
function boton_sensores_histograma_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_histograma(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var detalle = parametros_informe["detalle"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Campo y parámetros extra
    var campo_parametros_extra = campo;
    if (parametros_extra_campo != "") {
        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
    }

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_HISTOGRAMA;
    var parametros_tipo = [
        id_ratio,
        clase_sensor,
        id_sensor,
        campo_parametros_extra,
        intervalo_valores,
        detalle,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de correlación
function boton_sensores_correlacion_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_correlacion(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clases_sensores_independientes = parametros_informe["clases_sensores_independientes"];
    var ids_sensores_independientes = parametros_informe["ids_sensores_independientes"];
    var campos_independientes = parametros_informe["campos_independientes"];
    var parametros_extra_campos_independientes = parametros_informe["parametros_extra_campos_independientes"];
    var clase_sensor_dependiente = parametros_informe["clase_sensor_dependiente"];
    var id_sensor_dependiente = parametros_informe["id_sensor_dependiente"];
    var campo_dependiente = parametros_informe["campo_dependiente"];
    var parametros_extra_campo_dependiente = parametros_informe["parametros_extra_campo_dependiente"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var funcion_correlacion = parametros_informe["funcion_correlacion"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Campo y parámetros extra independientes
    var campos_parametros_extra_independientes = [];
    for (var i = 0; i < clases_sensores_independientes.length; i++) {
        var campo_parametros_extra = campos_independientes[i];
        if (parametros_extra_campos_independientes[i] != "") {
            campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campos_independientes[i];
        }
        campos_parametros_extra_independientes.push(campo_parametros_extra);
    }

    // Campo y parámetros extra dependiente
    var campo_parametros_extra_dependiente = campo_dependiente;
    if (parametros_extra_campo_dependiente != "") {
        campo_parametros_extra_dependiente += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo_dependiente;
    }

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_CORRELACION;
    var cadena_clases_sensores_independientes = clases_sensores_independientes.join(SEPARADOR_PARAMETROS_SIMPLES);
    var cadena_ids_sensores_independientes = ids_sensores_independientes.join(SEPARADOR_PARAMETROS_SIMPLES);
    var cadena_campos_parametros_extra_independientes = campos_parametros_extra_independientes.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        id_ratio,
        cadena_clases_sensores_independientes,
        cadena_ids_sensores_independientes,
        cadena_campos_parametros_extra_independientes,
        clase_sensor_dependiente,
        id_sensor_dependiente,
        campo_parametros_extra_dependiente,
        intervalo_valores,
        funcion_correlacion,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}
