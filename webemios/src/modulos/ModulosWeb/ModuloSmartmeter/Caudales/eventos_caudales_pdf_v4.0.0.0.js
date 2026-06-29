//
// Funciones de informes PDF de caudales (de SmartMeter)
//


// Genera el informe pdf de optimización de caudales automático
function boton_smartmeter_optimizador_caudales_automatico_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_optimizador_caudales_automatico();
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_AUTOMATICO;
    parametros_informe["nombre_informe"] = "informe_optimizacion_caudales_automatico";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de optimización de caudales automático");

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


// Genera el informe pdf de optimización de caudales manual
function boton_smartmeter_optimizador_caudales_manual_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_optimizador_caudales_manual();
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_MANUAL;
    parametros_informe["nombre_informe"] = "informe_optimizacion_caudales_manual";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de optimización de caudales manual");

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero_control_seleccion_fichero(
        parametros_informe,
        "control_seleccion_fichero_caudales_maximos",
        "fichero_smartmeter_optimizador_caudales_manual_text");
}


// Genera el informe pdf de simulación de caudales automático
function boton_smartmeter_simulador_caudales_automatico_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_caudales_automatico();
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_AUTOMATICO;
    parametros_informe["nombre_informe"] = "informe_simulacion_caudales_automatico";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de simulación de caudales automático");

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


// Genera el informe pdf de simulación de caudales manual
function boton_smartmeter_simulador_caudales_manual_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_caudales_manual();
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_MANUAL;
    parametros_informe["nombre_informe"] = "informe_simulacion_caudales_manual";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de simulación de caudales manual");

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero_control_seleccion_fichero(
        parametros_informe,
        "control_seleccion_fichero_caudales_maximos",
        "fichero_smartmeter_simulador_caudales_manual_text");
}
