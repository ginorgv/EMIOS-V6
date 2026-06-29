//
// Funciones de informes automáticos de compra de energía
//


// Muestra la ventana para añadir el informe automático de desvíos de compra de energía
function boton_smartmeter_desvios_compra_energia_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_desvios_compra_energia(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA;
    var parametros_tipo = [
        id_sensor,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}


// Muestra la ventana para añadir el informe automático de desvíos ponderados de compra de energía
function boton_smartmeter_desvios_ponderados_compra_energia_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_desvios_ponderados_compra_energia(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var id_sensor_hijo = parametros_informe["id_sensor_hijo"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA;
    var parametros_tipo = [
        id_sensor,
        id_sensor_hijo,
        cadena_horario_semanal,
        cadena_exclusion_fechas,
        cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}
