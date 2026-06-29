//
// Funciones de informes PDF de facturas (de SmartMeter)
//


// Genera el informe pdf de simulador de factura
function boton_smartmeter_simulador_factura_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_factura(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA;
    parametros_informe["nombre_informe"] = "informe_simulacion_factura";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de simulación de factura");

    // Medición
    parametros_informe["medicion"] = medicion;

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombre_sensor"];
    delete parametros_informe["nombres_sensores_reparto_costes"];

    // Se eliminan las fechas y horas separadas
    delete parametros_informe["fecha_inicio"];
    delete parametros_informe["fecha_fin"];
    delete parametros_informe["hora_inicio"];
    delete parametros_informe["hora_fin"];

    // Exclusión de fechas
    var cadena_exclusion_fechas = dame_cadena_fechas(parametros_informe["exclusion_fechas"]);
    parametros_informe["exclusion_fechas"] = cadena_exclusion_fechas;

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero(parametros_informe);
}

