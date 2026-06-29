//
// Funciones de informes PDF de comparacion (de valores de sensores)
//


// Genera el informe pdf de comparacion de valores de periodos de un sensor
function boton_sensores_comparacion_periodos_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_periodos(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_COMPARACION_PERIODOS;
    parametros_informe["nombre_informe"] = "informe_comparacion_periodos";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de comparación de valores de periodos");

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombre_sensor"];

    // Horario semanal y exclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(parametros_informe["horario_semanal"]);
    var cadena_exclusion_fechas = dame_cadena_fechas(parametros_informe["exclusion_fechas"]);
    parametros_informe["horario_semanal"] = cadena_horario_semanal;
    parametros_informe["exclusion_fechas"] = cadena_exclusion_fechas;

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero(parametros_informe);
}


// Genera el informe pdf de comparacion de valores con perfil horario de un sensor
function boton_sensores_comparacion_perfil_horario_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_perfil_horario(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO;
    parametros_informe["nombre_informe"] = "informe_comparacion_perfil_horario";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de comparación de valores con perfil horario");

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

    // Horario semanal y exclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(parametros_informe["horario_semanal"]);
    var cadena_exclusion_fechas = dame_cadena_fechas(parametros_informe["exclusion_fechas"]);
    parametros_informe["horario_semanal"] = cadena_horario_semanal;
    parametros_informe["exclusion_fechas"] = cadena_exclusion_fechas;

    // Función que genera el informe y lo muestra en una ventana
    genera_informe_fichero(parametros_informe);
}


// Genera el informe pdf de comparacion de valores de campos iguales de sensores
function boton_sensores_comparacion_campos_iguales_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_campos_iguales(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES;
    parametros_informe["nombre_informe"] = "informe_comparacion_campos_iguales";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de comparación de valores de campos iguales");

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


// Genera el informe pdf de comparacion de valores de campos diferentes de sensores
function boton_sensores_comparacion_campos_diferentes_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_campos_diferentes(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES;
    parametros_informe["nombre_informe"] = "informe_comparacion_campos_diferentes";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de comparación de valores de campos diferentes");

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


// Genera el informe pdf de análisis comparativo
function boton_sensores_analisis_comparativo_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_comparativo(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO;
    parametros_informe["nombre_informe"] = "informe_analisis_comparativo";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de análisis comparativo");

    // Se eliminan los parámetros de nombres y descripciones
    delete parametros_informe["nombres_sensores_agregados"];
    delete parametros_informe["nombre_sensor_destacado"];

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


// Genera el informe pdf de valores generales
function boton_sensores_valores_generales_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_valores_generales(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_VALORES_GENERALES;
    parametros_informe["nombre_informe"] = "informe_valores_generales";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de valores generales");

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


// Genera el informe pdf de incrementos totales
function boton_sensores_incrementos_totales_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_incrementos_totales(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES;
    parametros_informe["nombre_informe"] = "informe_incrementos_totales";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de incrementos totales");

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
