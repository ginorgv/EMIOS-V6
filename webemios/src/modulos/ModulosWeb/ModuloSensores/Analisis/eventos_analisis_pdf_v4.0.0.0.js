//
// Funciones de informes PDF de análisis (de sensores)
//


// Genera el informe PDF de análisis horario
function boton_sensores_analisis_horario_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_horario(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_ANALISIS_HORARIO;
    parametros_informe["nombre_informe"] = "informe_analisis_horario";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de análisis horario");

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombre_sensor"];

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


// Genera el informe PDF de análisis diario
function boton_sensores_analisis_diario_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_diario(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_ANALISIS_DIARIO;
    parametros_informe["nombre_informe"] = "informe_analisis_diario";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de análisis diario");

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombre_sensor"];

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


// Genera el informe PDF de análisis de comportamiento
function boton_sensores_analisis_comportamiento_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_comportamiento(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO;
    parametros_informe["nombre_informe"] = "informe_analisis_comportamiento";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de análisis de comportamiento");

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombres_sensores"];

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
