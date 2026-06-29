//
// Funciones de informes automáticos de información (de Proyectos)
//


// Muestra la ventana para añadir el informe automático de información de proyecto
function boton_proyectos_informacion_proyecto_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_proyectos_informacion_proyecto();
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_proyecto = parametros_informe["id_proyecto"];

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO;
    var parametros_tipo = [
        id_proyecto].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}
