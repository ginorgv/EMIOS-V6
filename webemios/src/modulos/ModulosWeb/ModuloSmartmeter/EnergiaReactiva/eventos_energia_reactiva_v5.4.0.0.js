//
// Funciones de energía reactiva (de SmartMeter)
//


// Muestra el informe de simulación de batería de condensadores
function boton_smartmeter_simulador_bateria_condensadores_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_bateria_condensadores();
    if (parametros_informe == null) {
        return;
    }

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_simulador_bateria_condensadores_ver_informe_Espanya(parametros_informe);
            break;
        }
    }
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de simulación de batería de condensadores
function dame_parametros_informe_smartmeter_simulador_bateria_condensadores() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    var id_sensor = $('#id_sensor_smartmeter_simulador_bateria_condensadores').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}

    // Se recupera el nombre del sensor seleccionado
    var nombre_sensor = $('#id_sensor_smartmeter_simulador_bateria_condensadores :selected').text();

    // Diferencia de capacidad
    var diferencia_capacidad = $('#numero_diferencia_capacidad_smartmeter_simulador_bateria_condensadores').val();
    if (PATRON_NUMERO_ENTERO.test(diferencia_capacidad) == false) {
        jAlert(TLNT.Idiomas._("La diferencia de capacidad debe ser un valor numérico"));
        return (null);
    }

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_simulador_bateria_condensadores", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_simulador_bateria_condensadores");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_simulador_bateria_condensadores");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["diferencia_capacidad"] = diferencia_capacidad;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var fecha_inicio = $('#fecha_inicio_smartmeter_simulador_bateria_condensadores').val();
    var hora_inicio = $('#hora_inicio_smartmeter_simulador_bateria_condensadores').val();
    var fecha_fin = $('#fecha_fin_smartmeter_simulador_bateria_condensadores').val();
    var hora_fin = $('#hora_fin_smartmeter_simulador_bateria_condensadores').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return (null);
    }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}

