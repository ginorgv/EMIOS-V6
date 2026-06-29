//
// Funciones de informes fichero de energía reactiva
//


// Muestra el informe de simulación de batería de condensadores
function smartmeter_simulador_bateria_condensadores_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_simulador_bateria_condensadores();

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            smartmeter_simulador_bateria_condensadores_ver_informe_fichero_Espanya(parametros_informe);
            break;
        }
    }
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe de simulación de batería de condensadores
function dame_parametros_informe_fichero_smartmeter_simulador_bateria_condensadores() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_simulador_bateria_condensadores").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_simulador_bateria_condensadores").text();

    // Diferencia de capacidad
    var diferencia_capacidad = $('#diferencia_capacidad_smartmeter_informe_fichero_simulador_bateria_condensadores').text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_simulador_bateria_condensadores").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_simulador_bateria_condensadores").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_simulador_bateria_condensadores").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["diferencia_capacidad"] = diferencia_capacidad;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_simulador_bateria_condensadores").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_simulador_bateria_condensadores").text();
        var hora_inicio = $("#hora_inicio_smartmeter_informe_fichero_simulador_bateria_condensadores").text();
        var hora_fin = $("#hora_fin_smartmeter_informe_fichero_simulador_bateria_condensadores").text();
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
