//
// Funciones de informes automáticos de líneas base (de Proyectos)
//


// Muestra la ventana para añadir el informe automático de simulador de línea base
function boton_proyectos_simulador_linea_base_anyadir_informe_automatico() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_proyectos_simulador_linea_base();
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_linea_base = parametros_informe["id_linea_base"];
    var comentarios = parametros_informe["comentarios"];

    // Tipo y parámetros de tipo
    var tipo = TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE;
    var parametros_tipo = [
        id_linea_base,
        comentarios].join(SEPARADOR_PARAMETROS_COMPUESTOS);
    var parametros_tipo_json = "";

    // Se muestra la ventana de añadir informe automático
    muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json);
}

