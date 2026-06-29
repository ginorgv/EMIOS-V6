//
// Funciones de informes PDF deinformes personalizados  (Módulo Smartmeter)
//


// Genera el informe PDF de estudio general
function boton_smartmeter_estudio_general_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_estudio_general(true, false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL;
    parametros_informe["nombre_informe"] = "estudio_general";
    parametros_informe["titulo"] = TLNT.Idiomas._("Estudio general");

    // Medición
    parametros_informe["medicion"] = medicion;

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombre_sensor"];

    // Se eliminan las fechas y horas separadas
    delete parametros_informe["fecha_inicio"];
    delete parametros_informe["fecha_fin"];
    delete parametros_informe["hora_inicio"];
    delete parametros_informe["hora_fin"];

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero(parametros_informe);
}
