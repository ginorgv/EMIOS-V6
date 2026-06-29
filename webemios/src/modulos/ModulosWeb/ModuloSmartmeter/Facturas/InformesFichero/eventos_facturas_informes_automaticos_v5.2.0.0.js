//
// Funciones de informes automáticos de facturas (de SmartMeter)
//


// Muestra la ventana para añadir el informe automático de simulación de factura
function boton_smartmeter_simulador_factura_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_factura(true);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var id_tarifa = parametros_informe["id_tarifa"];
    var ids_sensores_reparto_costes = parametros_informe["ids_sensores_reparto_costes"];

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA;
    var cadena_ids_sensores_reparto_costes = ids_sensores_reparto_costes.join(SEPARADOR_PARAMETROS_SIMPLES);
    var parametros_tipo = [
        medicion,
        id_sensor,
        id_tarifa,
        cadena_ids_sensores_reparto_costes].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}
