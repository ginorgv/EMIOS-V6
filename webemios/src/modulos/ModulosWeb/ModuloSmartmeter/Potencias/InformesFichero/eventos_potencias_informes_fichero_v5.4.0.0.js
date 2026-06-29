//
// Funciones de informes fichero de potencias
//


// Muestra el informe de optimización de potencias automático
function smartmeter_optimizador_potencias_automatico_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_optimizador_potencias_automatico();

    // Comprobación de error en los parámetros del informe
    if (parametros_informe["error_parametros"] == true) {
        // Se muestra el informe
        muestra_informe_fichero_optimizador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_AUTOMATICO, parametros_informe, null);
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var id_tarifa = parametros_informe["id_tarifa"];
    var granularidad = parametros_informe["granularidad"];
    var rango_potencias = parametros_informe["rango_potencias"];
    var diferencia_potencia = parametros_informe["diferencia_potencia"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/dame_costes_potencias_optimas_sensor_tarifa_electrica.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        id_tarifa: id_tarifa,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        granularidad: granularidad,
        rango_potencias: rango_potencias,
        diferencia_potencia: diferencia_potencia,
        horario_semanal: cadena_horario_semanal,
        exclusion_fechas: cadena_exclusion_fechas,
        inclusion_fechas: cadena_inclusion_fechas,
        minutos_desfase_utc: minutos_desfase_utc
    },
    function (data, status) {
        // Se muestra el informe
        muestra_informe_fichero_optimizador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_AUTOMATICO, parametros_informe, data);
    });
}


// Muestra el informe de optimización de potencias manual
function smartmeter_optimizador_potencias_manual_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_optimizador_potencias_manual();

    // Comprobación de error en los parámetros del informe
    if (parametros_informe["error_parametros"] == true) {
        // Se muestra el informe
        muestra_informe_fichero_optimizador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_MANUAL, parametros_informe, null);
        return;
    }

    // Parámetros del informe
    var id_tarifa = parametros_informe["id_tarifa"];
    var ruta_fichero_potencias_maximas = parametros_informe["ruta_fichero_potencias_maximas"];

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/dame_costes_potencias_optimas_fichero_tarifa_electrica.php", {
        id_tarifa: id_tarifa,
        ruta_fichero_potencias_maximas: ruta_fichero_potencias_maximas
    },
    function (data, status) {
        // Se muestra el informe
        muestra_informe_fichero_optimizador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_MANUAL, parametros_informe, data);
    });
}


// Muestra el informe de los optimizadores de potencias
function muestra_informe_fichero_optimizador_potencias(tipo_optimizador_potencias, parametros_informe, resultado_script_php_json) {
    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            muestra_informe_fichero_optimizador_potencias_Espanya(tipo_optimizador_potencias, parametros_informe, resultado_script_php_json);
            break;
        }
    }
}


// Muestra el informe de simulación de potencias automático
function smartmeter_simulador_potencias_automatico_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_simulador_potencias_automatico();

    // Comprobación de error en los parámetros del informe
    if (parametros_informe["error_parametros"] == true) {
        // Se muestra el informe
        muestra_informe_fichero_simulador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_AUTOMATICO, parametros_informe, null);
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var id_tarifa = parametros_informe["id_tarifa"];
    var granularidad = parametros_informe["granularidad"];
    var rango_potencias = parametros_informe["rango_potencias"];
    var diferencia_potencia = parametros_informe["diferencia_potencia"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var potencias_tramos = parametros_informe["potencias_tramos"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/dame_costes_potencias_seleccionadas_sensor_tarifa_electrica.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        id_tarifa: id_tarifa,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        potencias_seleccionadas: potencias_tramos,
        granularidad: granularidad,
        rango_potencias: rango_potencias,
        diferencia_potencia: diferencia_potencia,
        horario_semanal: cadena_horario_semanal,
        exclusion_fechas: cadena_exclusion_fechas,
        inclusion_fechas: cadena_inclusion_fechas,
        minutos_desfase_utc: minutos_desfase_utc
    },
    function (data, status) {
        // Se muestra el informe
        muestra_informe_fichero_simulador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_AUTOMATICO, parametros_informe, data);
    });
}


// Muestra el informe de simulación de potencias manual
function smartmeter_simulador_potencias_manual_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_simulador_potencias_manual();

    // Comprobación de error en los parámetros del informe
    if (parametros_informe["error_parametros"] == true) {
        // Se muestra el informe
        muestra_informe_fichero_simulador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_MANUAL, parametros_informe, null);
        return;
    }

    // Parámetros del informe
    var id_tarifa = parametros_informe["id_tarifa"];
    var ruta_fichero_potencias_maximas = parametros_informe["ruta_fichero_potencias_maximas"];
    var potencias_tramos = parametros_informe["potencias_tramos"];

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/dame_costes_potencias_seleccionadas_fichero_tarifa_electrica.php", {
        id_tarifa: id_tarifa,
        ruta_fichero_potencias_maximas: ruta_fichero_potencias_maximas,
        potencias_seleccionadas: potencias_tramos
    },
    function (data, status) {
        // Se muestra el informe
        muestra_informe_fichero_simulador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_MANUAL, parametros_informe, data);
    });
}


// Muestra el informe de los simuladores de potencias
function muestra_informe_fichero_simulador_potencias(tipo_simulador_potencias, parametros_informe, resultado_script_php_json) {
    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            muestra_informe_fichero_simulador_potencias_Espanya(tipo_simulador_potencias, parametros_informe, resultado_script_php_json);
            break;
        }
    }
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe de optimización de potencias automático
function dame_parametros_informe_fichero_smartmeter_optimizador_potencias_automatico() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_optimizador_potencias_automatico").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_optimizador_potencias_automatico").text();

    // Identificador de tarifa eléctrica
    var id_tarifa = $('#id_tarifa_smartmeter_informe_fichero_optimizador_potencias_automatico').text();

    // Granularidad, rango de potencias y diferencia de potencia
    var granularidad = $('#granularidad_smartmeter_informe_fichero_optimizador_potencias_automatico').text();
    var rango_potencias = $('#rango_potencias_smartmeter_informe_fichero_optimizador_potencias_automatico').text();
    var diferencia_potencia = $('#diferencia_potencia_smartmeter_informe_fichero_optimizador_potencias_automatico').text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_optimizador_potencias_automatico").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_optimizador_potencias_automatico").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_optimizador_potencias_automatico").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["granularidad"] = granularidad;
    parametros_informe["rango_potencias"] = rango_potencias;
    parametros_informe["diferencia_potencia"] = diferencia_potencia;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_optimizador_potencias_automatico").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_optimizador_potencias_automatico").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, null, fecha_fin, null);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
            var hora_inicio = "00:00:00";
            var hora_fin = "23:59:59";
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


// Devuelve los parámetros del informe de optimización de potencias manual
function dame_parametros_informe_fichero_smartmeter_optimizador_potencias_manual() {
    // Identificador de tarifa eléctrica
    var id_tarifa = $('#id_tarifa_smartmeter_informe_fichero_optimizador_potencias_automatico').text();

    // Ruta de fichero de potencias máximas
    var ruta_fichero_potencias_maximas = $('#ruta_fichero_potencias_maximas_smartmeter_informe_fichero_optimizador_potencias_automatico').text();

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["ruta_fichero_potencias_maximas"] = ruta_fichero_potencias_maximas;

    // Información de error de parámetros
    parametros_informe["error_parametros"] = false;
    parametros_informe["descripcion_error_parametros"] = "";

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de simulación de potencias automático
function dame_parametros_informe_fichero_smartmeter_simulador_potencias_automatico() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_simulador_potencias_automatico").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_simulador_potencias_automatico").text();

    // Identificador de tarifa eléctrica
    var id_tarifa = $('#id_tarifa_smartmeter_informe_fichero_simulador_potencias_automatico').text();

    // Granularidad, rango de potencias y diferencia de potencia
    var granularidad = $('#granularidad_smartmeter_informe_fichero_simulador_potencias_automatico').text();
    var rango_potencias = $('#rango_potencias_smartmeter_informe_fichero_simulador_potencias_automatico').text();
    var diferencia_potencia = $('#diferencia_potencia_smartmeter_informe_fichero_simulador_potencias_automatico').text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_simulador_potencias_automatico").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_simulador_potencias_automatico").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_simulador_potencias_automatico").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Se recuperan las potencias de los tramos
    var potencias_tramos = [];
    $("#potencias_tramos_smartmeter_informe_fichero_simulador_potencias_automatico li").each(function() {
        potencias_tramos.push($(this).text());
    });

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["granularidad"] = granularidad;
    parametros_informe["rango_potencias"] = rango_potencias;
    parametros_informe["diferencia_potencia"] = diferencia_potencia;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;
    parametros_informe["potencias_tramos"] = potencias_tramos;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_simulador_potencias_automatico").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_simulador_potencias_automatico").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, null, fecha_fin, null);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
            var hora_inicio = "00:00:00";
            var hora_fin = "23:59:59";
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


// Devuelve los parámetros del informe de simulación de potencias manual
function dame_parametros_informe_fichero_smartmeter_simulador_potencias_manual() {
    // Identificador de tarifa eléctrica
    var id_tarifa = $('#id_tarifa_smartmeter_informe_fichero_simulador_potencias_manual').text();

    // Ruta de fichero de potencias máximas
    var ruta_fichero_potencias_maximas = $('#ruta_fichero_potencias_maximas_smartmeter_informe_fichero_simulador_potencias_manual').text();

    // Se recuperan las potencias de los tramos
    var potencias_tramos = [];
    $("#potencias_tramos_smartmeter_informe_fichero_simulador_potencias_manual li").each(function() {
        potencias_tramos.push($(this).text());
    });

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["ruta_fichero_potencias_maximas"] = ruta_fichero_potencias_maximas;
    parametros_informe["potencias_tramos"] = potencias_tramos;

    // Información de error en los parámetros
    parametros_informe["error_parametros"] = false;
    parametros_informe["descripcion_error_parametros"] = "";

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}