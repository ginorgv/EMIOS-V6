////
// Funciones de informes PDF de líneas base
//


// Genera el informe PDF de simulación de línea base
function boton_proyectos_simulador_linea_base_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_proyectos_simulador_linea_base();
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE;
    parametros_informe["nombre_informe"] = "informe_simulacion_linea_base";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de simulación de línea base");

    // Se eliminan las fechas y horas separadas
    delete parametros_informe["fecha_inicio"];
    delete parametros_informe["fecha_fin"];
    delete parametros_informe["hora_inicio"];
    delete parametros_informe["hora_fin"];

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero(parametros_informe);
}
