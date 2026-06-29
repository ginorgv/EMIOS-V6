//
// Funciones de informes PDF de información (de valores de sensores)
//


// Genera el informe PDF de información de temperatura de un sensor
function boton_sensores_informacion_temperatura_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_temperatura(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA;
    parametros_informe["nombre_informe"] = "informe_temperatura";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de temperatura");

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


// Genera el informe PDF de información de humedad de un sensor
function boton_sensores_informacion_humedad_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_humedad(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD;
    parametros_informe["nombre_informe"] = "informe_humedad";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de humedad");

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


// Genera el informe PDF de información de luz interior de un sensor
function boton_sensores_informacion_luz_interior_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_luz_interior(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR;
    parametros_informe["nombre_informe"] = "informe_luz_interior";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de luz interior");

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


// Genera el informe PDF de información de viento de un sensor
function boton_sensores_informacion_viento_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_viento(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_VIENTO;
    parametros_informe["nombre_informe"] = "informe_viento";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de viento");

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


// Genera el informe pdf de información de energía activa de un sensor
function boton_sensores_informacion_energia_activa_generar_pdf() {
    boton_sensores_informacion_energia_generar_pdf(CLASE_SENSOR_ENERGIA_ACTIVA);
}


// Genera el informe pdf de información de energía reactiva de un sensor
function boton_sensores_informacion_energia_reactiva_generar_pdf() {
    boton_sensores_informacion_energia_generar_pdf(CLASE_SENSOR_ENERGIA_REACTIVA);
}


// Genera el informe pdf de información de energía de un sensor
function boton_sensores_informacion_energia_generar_pdf(clase_sensor) {
    // Sufijo de controles de tipo de energía
    var tipo_energia = null;
    switch (clase_sensor) {
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            tipo_energia = "activa";
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            tipo_energia = "reactiva";
            break;
        }
    }

    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_energia(tipo_energia, false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    switch (clase_sensor) {
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA;
            parametros_informe["nombre_informe"] = "informe_energia_activa";
            parametros_informe["titulo"] = TLNT.Idiomas._("Informe de energía activa");
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA;
            parametros_informe["nombre_informe"] = "informe_energia_reactiva";
            parametros_informe["titulo"] = TLNT.Idiomas._("Informe de energía reactiva");
            break;
        }
    }

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


// Genera el informe PDF de información de cortes de tensión de un sensor
function boton_sensores_informacion_cortes_tension_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_cortes_tension(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION;
    parametros_informe["nombre_informe"] = "informe_cortes_tension";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de cortes de tensión");

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


// Genera el informe PDF de información de compra de energía de un sensor
function boton_sensores_informacion_compra_energia_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_compra_energia(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA;
    parametros_informe["nombre_informe"] = "informe_compra_energia";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de compra de energía");

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


// Genera el informe PDF de información de gas de un sensor
function boton_sensores_informacion_gas_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_gas(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_GAS;
    parametros_informe["nombre_informe"] = "informe_gas";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de gas");

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


// Genera el informe PDF de información de agua de un sensor
function boton_sensores_informacion_agua_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_agua(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_AGUA;
    parametros_informe["nombre_informe"] = "informe_agua";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de agua");

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


// Genera el informe PDF de información genérica de un sensor
function boton_sensores_informacion_generica_generar_pdf() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_generica(false);
    if (parametros_informe == null) {
        return;
    }

    // Información para generar el informe
    parametros_informe["tipo"] = TIPO_INFORME_SENSORES_INFORMACION_GENERICA;
    parametros_informe["nombre_informe"] = "informe_generica";
    parametros_informe["titulo"] = TLNT.Idiomas._("Informe de datos genéricos");

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
