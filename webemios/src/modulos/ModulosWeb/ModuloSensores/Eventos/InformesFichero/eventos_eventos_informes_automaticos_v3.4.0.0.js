//
// Funciones de informes automáticos de eventos (de Sensores)
//


// Muestra la ventana para añadir el informe automático de activaciones de eventos
function boton_sensores_activaciones_eventos_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_activaciones_eventos(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var clase_sensor = parametros_informe["clase_sensor"];
    var origen_evento = parametros_informe["origen_evento"];
    var id_origen_evento = parametros_informe["id_origen_evento"];
    var granularidad_evento = parametros_informe["granularidad_evento"];
    var ids_eventos = parametros_informe["ids_eventos"];
    var campo = parametros_informe["campo"];

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS;
    var cadena_ids_eventos = ids_eventos.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        clase_sensor,
        origen_evento,
        id_origen_evento,
        granularidad_evento,
        cadena_ids_eventos,
        campo].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}
