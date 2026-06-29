//
// Funciones de informes automáticos de análisis (de Sensores)
//


// Muestra la ventana para añadir el informe automático de análisis horario
function boton_sensores_analisis_horario_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_horario(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
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
    var tipo = TIPO_INFORME_SENSORES_ANALISIS_HORARIO;
    var parametros_tipo = [
        id_ratio,
        clase_sensor,
        id_sensor,
        campo_parametros_extra,
        tipo_mapa_calor,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de análisis diario
function boton_sensores_analisis_diario_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_diario(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
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
    var tipo = TIPO_INFORME_SENSORES_ANALISIS_DIARIO;
    var parametros_tipo = [
        id_ratio,
        clase_sensor,
        id_sensor,
        campo_parametros_extra,
        tipo_mapa_calor,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de análisis de comportamiento
function boton_sensores_analisis_comportamiento_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_comportamiento(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
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
    var tipo = TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO;
    var cadena_ids_sensores = ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        id_ratio,
        clase_sensor,
        campo_parametros_extra,
        cadena_ids_sensores,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}
