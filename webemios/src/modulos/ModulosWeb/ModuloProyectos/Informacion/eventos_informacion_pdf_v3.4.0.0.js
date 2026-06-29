//
// Funciones de informes PDF de información (de proyectos)
//


// Genera el informe PDF de información de un proyecto
function boton_proyectos_informacion_proyecto_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_proyectos_informacion_proyecto();
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO;
    parametros_informe["nombre_informe"] = "informe_informacion_proyecto";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de información de proyecto");

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero(parametros_informe);
}
