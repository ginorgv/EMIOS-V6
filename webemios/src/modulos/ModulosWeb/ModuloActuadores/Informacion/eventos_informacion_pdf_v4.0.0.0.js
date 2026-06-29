//
// Funciones de informes PDF de información (de actuadores)
//


// Genera el informe PDF de información de acciones enviadas a un actuador o grupo de actuadores
function boton_actuadores_informacion_acciones_enviadas_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_actuadores_informacion_acciones_enviadas(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS;
    parametros_informe["nombre_informe"] = "informe_acciones_enviadas";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de acciones enviadas");

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombre_clase_actuador"];
    delete parametros_informe["nombre_destino_accion"];
    delete parametros_informe["nombre_sensor"];
    delete parametros_informe["nombre_campo"];

    // Se eliminan las fechas y horas separadas
    delete parametros_informe["fecha_inicio"];
    delete parametros_informe["fecha_fin"];
    delete parametros_informe["hora_inicio"];
    delete parametros_informe["hora_fin"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(parametros_informe["horario_semanal"]);
    var cadena_exclusion_fechas = dame_cadena_fechas(parametros_informe["exclusion_fechas"]);
    var cadena_inclusion_fechas = dame_cadena_fechas(parametros_informe["inclusion_fechas"]);
    parametros_informe["horario_semanal"] = cadena_horario_semanal;
    parametros_informe["exclusion_fechas"] = cadena_exclusion_fechas;
    parametros_informe["inclusion_fechas"] = cadena_inclusion_fechas;

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero(parametros_informe);
}
