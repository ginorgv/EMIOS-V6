//
// Funciones de informes automáticos de informes personalizados
//


// Muestra la ventana para añadir el informe automático de estudio general
function boton_smartmeter_estudio_general_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_estudio_general(false, true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var apartados = parametros_informe["apartados"];
    var parametros_tipo_json = parametros_informe["parametros_tipo_json"];

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL;
    var cadena_apartados = apartados.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        medicion,
        id_ratio,
        id_sensor,
        cadena_apartados].join(SEPARADOR_PARAMETROS_COMPUESTOS);

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}
