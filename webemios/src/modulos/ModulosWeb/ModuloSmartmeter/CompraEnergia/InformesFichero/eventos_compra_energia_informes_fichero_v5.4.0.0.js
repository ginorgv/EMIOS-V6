//
// Funciones de informes fichero de compra de energía
//


// Muestra el informe de previsión de compra de energía
function smartmeter_prevision_compra_energia_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_prevision_compra_energia();

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            smartmeter_prevision_compra_energia_ver_informe_fichero_Espanya(parametros_informe);
            break;
        }
    }
}


// Muestra el informe de desvíos de compra de energía
function smartmeter_desvios_compra_energia_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_desvios_compra_energia();

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            smartmeter_desvios_compra_energia_ver_informe_fichero_Espanya(parametros_informe);
            break;
        }
    }
}


// Muestra el informe de desvíos ponderados de compra de energía
function smartmeter_desvios_ponderados_compra_energia_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_desvios_ponderados_compra_energia();

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            smartmeter_desvios_ponderados_compra_energia_ver_informe_fichero_Espanya(parametros_informe);
            break;
        }
    }
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe de previsión de compra de energía
function dame_parametros_informe_fichero_smartmeter_prevision_compra_energia() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_prevision_compra_energia").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_prevision_compra_energia").text();

    // Fechas de inicio y fin de perfil horario
    var fecha_inicio_perfil_horario = $('#fecha_inicio_perfil_horario_smartmeter_informe_fichero_prevision_compra_energia').text();
    var fecha_fin_perfil_horario = $('#fecha_fin_perfil_horario_smartmeter_informe_fichero_prevision_compra_energia').text();

    // Tipo de perfil horario y agrupaciones de días
    var tipo_perfil_horario = $('#tipo_perfil_horario_smartmeter_informe_fichero_prevision_compra_energia').text();
    var cadena_agrupaciones_dias_semana = $('#agrupaciones_dias_semana_smartmeter_informe_fichero_prevision_compra_energia').text();
    var agrupaciones_dias_semana = dame_agrupaciones_dias_semana(cadena_agrupaciones_dias_semana);

    // Exclusión de fechas
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_prevision_compra_energia").text();
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["fecha_inicio_perfil_horario"] = fecha_inicio_perfil_horario;
    parametros_informe["fecha_fin_perfil_horario"] = fecha_fin_perfil_horario;
    parametros_informe["tipo_perfil_horario"] = tipo_perfil_horario;
    parametros_informe["agrupaciones_dias_semana"] = agrupaciones_dias_semana;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_prevision_compra_energia").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_prevision_compra_energia").text();
        var hora_inicio = $("#hora_inicio_smartmeter_informe_fichero_prevision_compra_energia").text();
        var hora_fin = $("#hora_fin_smartmeter_informe_fichero_prevision_compra_energia").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, null, fecha_fin, null);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
            hora_inicio += ":00";
            hora_fin += ":59";
            var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
            var fecha_hora_fin = fecha_fin + ", " + hora_fin;

            parametros_informe["fecha_inicio"] = fecha_inicio;
            parametros_informe["fecha_fin"] = fecha_fin;
            parametros_informe["hora_inicio"] = hora_inicio;
            parametros_informe["hora_fin"] = hora_fin;
            parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
            parametros_informe["fecha_hora_fin"] = fecha_hora_fin;
        }
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de desvíos de compra de energía
function dame_parametros_informe_fichero_smartmeter_desvios_compra_energia() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_desvios_compra_energia").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_desvios_compra_energia").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_desvios_compra_energia").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_desvios_compra_energia").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_desvios_compra_energia").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_desvios_compra_energia").text();
        var hora_inicio = $("#hora_inicio_smartmeter_informe_fichero_desvios_compra_energia").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_desvios_compra_energia").text();
        var hora_fin = $("#hora_fin_smartmeter_informe_fichero_desvios_compra_energia").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
            hora_inicio += ":00";
            hora_fin += ":59";
            var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
            var fecha_hora_fin = fecha_fin + ", " + hora_fin;

            parametros_informe["fecha_inicio"] = fecha_inicio;
            parametros_informe["fecha_fin"] = fecha_fin;
            parametros_informe["hora_inicio"] = hora_inicio;
            parametros_informe["hora_fin"] = hora_fin;
            parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
            parametros_informe["fecha_hora_fin"] = fecha_hora_fin;
        }
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de desvíos ponderados de compra de energía
function dame_parametros_informe_fichero_smartmeter_desvios_ponderados_compra_energia() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();

    // Identificador y nombre de sensor hijo
    var id_sensor_hijo = $("#id_sensor_hijo_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();
    var nombre_sensor_hijo = $("#nombre_sensor_hijo_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["id_sensor_hijo"] = id_sensor_hijo;
    parametros_informe["nombre_sensor_hijo"] = nombre_sensor_hijo;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();
        var hora_inicio = $("#hora_inicio_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();
        var hora_fin = $("#hora_fin_smartmeter_informe_fichero_desvios_ponderados_compra_energia").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
            hora_inicio += ":00";
            hora_fin += ":59";
            var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
            var fecha_hora_fin = fecha_fin + ", " + hora_fin;

            parametros_informe["fecha_inicio"] = fecha_inicio;
            parametros_informe["fecha_fin"] = fecha_fin;
            parametros_informe["hora_inicio"] = hora_inicio;
            parametros_informe["hora_fin"] = hora_fin;
            parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
            parametros_informe["fecha_hora_fin"] = fecha_hora_fin;
        }
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}
