//
// Funciones de informes PDF de compra de energía (de SmartMeter)
//


// Genera el informe pdf de previsión de compra de energía
function boton_smartmeter_prevision_compra_energia_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_prevision_compra_energia();
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SMARTMETER_PREVISION_COMPRA_ENERGIA;
    parametros_informe["nombre_informe"] = "informe_prevision_compra_energia";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de previsión de compra de energía");

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombre_sensor"];

    // Se eliminan las fechas y horas separadas
    delete parametros_informe["fecha_inicio"];
    delete parametros_informe["fecha_fin"];
    delete parametros_informe["hora_inicio"];
    delete parametros_informe["hora_fin"];

    // Cadenas de fechas
    var cadena_fecha_inicio_perfil_horario = convierte_formato_fecha(parametros_informe["fecha_inicio_perfil_horario"], formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);
    var cadena_fecha_fin_perfil_horario = convierte_formato_fecha(parametros_informe["fecha_fin_perfil_horario"], formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);
    parametros_informe["fecha_inicio_perfil_horario"] = cadena_fecha_inicio_perfil_horario;
    parametros_informe["fecha_fin_perfil_horario"] = cadena_fecha_fin_perfil_horario;

    // Agrupaciones de días de la semana
    var cadena_agrupaciones_dias_semana = dame_cadena_agrupaciones_dias_semana(parametros_informe["agrupaciones_dias_semana"]);
    parametros_informe["agrupaciones_dias_semana"] = cadena_agrupaciones_dias_semana;

    // Exclusión de fechas
    var cadena_exclusion_fechas = dame_cadena_fechas(parametros_informe["exclusion_fechas"]);
    parametros_informe["exclusion_fechas"] = cadena_exclusion_fechas;

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero(parametros_informe);
}


// Genera el informe pdf de desvíos de compra de energía
function boton_smartmeter_desvios_compra_energia_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_desvios_compra_energia(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA;
    parametros_informe["nombre_informe"] = "informe_desvios_compra_energia";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de desvíos de compra de energía");

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


// Genera el informe pdf de desvíos ponderados de compra de energía
function boton_smartmeter_desvios_ponderados_compra_energia_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_desvios_ponderados_compra_energia(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA;
    parametros_informe["nombre_informe"] = "informe_desvios_ponderados_compra_energia";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de desvíos ponderados de compra de energía");

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombre_sensor"];
    delete parametros_informe["nombre_sensor_hijo"];

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
