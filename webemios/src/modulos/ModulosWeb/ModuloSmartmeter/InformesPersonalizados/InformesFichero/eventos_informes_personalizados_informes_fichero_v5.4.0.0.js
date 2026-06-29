//
// Funciones de informes fichero de informes personalizados
//


// Genera el informe fichero de estudio general
function smartmeter_estudio_general_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_estudio_general();

    // Selección de medición y país
    var medicion = parametros_informe["medicion"];
    switch (medicion) {
        case MEDICION_ELECTRICIDAD: {
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    smartmeter_estudio_general_ver_informe_fichero_electricidad_Espanya(parametros_informe);
                    break;
                }
            }
            break;
        }
        case MEDICION_GAS: {
            switch (pais_tarifas_gas) {
                case PAIS_ESPANYA: {
                    smartmeter_estudio_general_ver_informe_fichero_gas_Espanya(parametros_informe);
                    break;
                }
            }
            break;
        }
        case MEDICION_AGUA: {
            switch (pais_tarifas_agua) {
                case PAIS_ESPANYA: {
                    smartmeter_estudio_general_ver_informe_fichero_agua_Espanya(parametros_informe);
                    break;
                }
            }
            break;
        }
    }
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero de estudio general
function dame_parametros_informe_fichero_smartmeter_estudio_general() {
    // Se recupera la medición
    var medicion = $("#medicion_smartmeter_informe_fichero_estudio_general").text();

    // Se recupera el ratio
    var id_ratio = $("#id_ratio_smartmeter_informe_fichero_estudio_general").text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_estudio_general").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_estudio_general").text();

    // Se recuperan los apartados
    var apartados = [];
    $("#apartados_smartmeter_informe_fichero_estudio_general li").each(function () {
        apartados.push($(this).text());
    });

    // Parámetros tipo json
    var cadena_parametros_tipo_json = $("#parametros_tipo_json_smartmeter_informe_fichero_estudio_general").text();
    var parametros_tipo_json = jQuery.parseJSON(cadena_parametros_tipo_json);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["medicion"] = medicion;
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["apartados"] = apartados;
    parametros_informe["parametros_tipo_json"] = parametros_tipo_json;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_estudio_general").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_estudio_general").text();
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