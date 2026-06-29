//
// Funciones de informes PDF de eventos
//


// Genera el informe PDF de activaciones de eventos de un sensor o grupo de sensores
function boton_sensores_activaciones_eventos_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_activaciones_eventos(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS;
    parametros_informe["nombre_informe"] = "informe_activaciones_eventos";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de activaciones de eventos");

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombre_origen_evento"];
    delete parametros_informe["nombres_eventos"];
    delete parametros_informe["nombre_campo"];

    // Se eliminan las fechas y horas separadas
    delete parametros_informe["fecha_inicio"];
    delete parametros_informe["fecha_fin"];
    delete parametros_informe["hora_inicio"];
    delete parametros_informe["hora_fin"];

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero(parametros_informe);
}
