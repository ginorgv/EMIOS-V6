//
// Funciones de caudales (de SmartMeter)
//


// Muestra el informe de optimización de caudales automático
function boton_smartmeter_optimizador_caudales_automatico_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_optimizador_caudales_automatico();
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var id_tarifa = parametros_informe["id_tarifa"];
    var diferencia_caudal = parametros_informe["diferencia_caudal"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/dame_costes_caudales_optimos_sensor_tarifa_gas.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        id_tarifa: id_tarifa,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        diferencia_caudal: diferencia_caudal,
        horario_semanal: cadena_horario_semanal,
        exclusion_fechas: cadena_exclusion_fechas,
        inclusion_fechas: cadena_inclusion_fechas,
        minutos_desfase_utc: minutos_desfase_utc
    },
    function (data, status) {
        // Se muestra el informe
        muestra_informe_optimizador_caudales(TIPO_OPTIMIZADOR_SIMULADOR_CAUDALES_AUTOMATICO, data);
    });
}


// Descarga la plantilla de fichero de caudales máximos para la optimización manual
function boton_smartmeter_optimizador_caudales_manual_descargar_plantilla_fichero() {
    // Selección de país
    switch (pais_tarifas_gas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_optimizador_caudales_manual_descargar_plantilla_fichero_Espanya();
            break;
        }
    }
}


// Muestra el informe de optimización de caudales manual
function boton_smartmeter_optimizador_caudales_manual_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_optimizador_caudales_manual();
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_tarifa = parametros_informe["id_tarifa"];
    var control_seleccion_fichero_caudales_maximos = parametros_informe["control_seleccion_fichero_caudales_maximos"];

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_tarifa", id_tarifa);
    datos_formulario.append("fichero_caudales_maximos", control_seleccion_fichero_caudales_maximos.files[0]);

    // Llamada 'ajax' POST
    $.ajax({
        url: "./src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/dame_costes_caudales_optimos_fichero_tarifa_gas.php",
        type: "POST",
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            // Se muestra el informe
            muestra_informe_optimizador_caudales(TIPO_OPTIMIZADOR_SIMULADOR_CAUDALES_MANUAL, result);
        },
        error: function(request, status, err) {}
    });
}


// Muestra el informe de los optimizadores de caudales
function muestra_informe_optimizador_caudales(tipo_optimizador_caudales, resultado_script_php_json) {
    // Selección de país
    switch (pais_tarifas_gas) {
        case PAIS_ESPANYA: {
            muestra_informe_optimizador_caudales_Espanya(tipo_optimizador_caudales, resultado_script_php_json);
            break;
        }
    }
}


// Muestra el informe de simulación de caudales automático
function boton_smartmeter_simulador_caudales_automatico_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_caudales_automatico();
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var id_tarifa = parametros_informe["id_tarifa"];
    var diferencia_caudal = parametros_informe["diferencia_caudal"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var caudal = parametros_informe["caudal"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/dame_costes_caudales_seleccionados_sensor_tarifa_gas.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        id_tarifa: id_tarifa,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        caudal_seleccionado: caudal,
        diferencia_caudal: diferencia_caudal,
        horario_semanal: cadena_horario_semanal,
        exclusion_fechas: cadena_exclusion_fechas,
        inclusion_fechas: cadena_inclusion_fechas,
        minutos_desfase_utc: minutos_desfase_utc
    },
    function (data, status) {
        // Se muestra el informe
        muestra_informe_simulador_caudales(TIPO_OPTIMIZADOR_SIMULADOR_CAUDALES_AUTOMATICO, data);
    });
}


// Descarga la plantilla de fichero de caudales máximos para la simulación manual
function boton_smartmeter_simulador_caudales_manual_descargar_plantilla_fichero() {
    // Selección de país
    switch (pais_tarifas_gas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_simulador_caudales_manual_descargar_plantilla_fichero_Espanya();
            break;
        }
    }
}


// Muestra el informe de simulación de caudales manual
function boton_smartmeter_simulador_caudales_manual_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_caudales_manual();
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_tarifa = parametros_informe["id_tarifa"];
    var control_seleccion_fichero_caudales_maximos = parametros_informe["control_seleccion_fichero_caudales_maximos"];
    var caudal = parametros_informe["caudal"];

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_tarifa", id_tarifa);
    datos_formulario.append("fichero_caudales_maximos", control_seleccion_fichero_caudales_maximos.files[0]);
    datos_formulario.append("caudal_seleccionado", caudal);

    // Llamada 'ajax' POST
    $.ajax({
        url: "./src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/dame_costes_caudales_seleccionados_fichero_tarifa_gas.php",
        type: "POST",
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            // Se muestra el informe
            muestra_informe_simulador_caudales(TIPO_OPTIMIZADOR_SIMULADOR_CAUDALES_MANUAL, result);
        },
        error: function(request, status, err) {}
    });
}


// Muestra el informe de los simuladores de caudales
function muestra_informe_simulador_caudales(tipo_simulador_caudales, resultado_script_php_json) {
    // Selección de país
    switch (pais_tarifas_gas) {
        case PAIS_ESPANYA: {
            muestra_informe_simulador_caudales_Espanya(tipo_simulador_caudales, resultado_script_php_json);
            break;
        }
    }
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de optimización de caudales automático
function dame_parametros_informe_smartmeter_optimizador_caudales_automatico() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador de sensor
    var id_sensor = $('#id_sensor_smartmeter_optimizador_caudales_automatico').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}

    // Nombre de sensor
    var nombre_sensor = $('#id_sensor_smartmeter_optimizador_caudales_automatico :selected').text();

    // Identificador de tarifa de gas
    var id_tarifa = $('#id_tarifa_smartmeter_optimizador_caudales_automatico').val();
	if (id_tarifa == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay tarifa seleccionada"));
        return (null);
	}

    // Diferencia de caudal
    var diferencia_caudal = $('#numero_diferencia_caudal_smartmeter_optimizador_caudales_automatico').val();
    if (PATRON_NUMERO_ENTERO.test(diferencia_caudal) == false) {
        jAlert(TLNT.Idiomas._("La diferencia de caudal debe ser un valor numérico"));
        return (null);
    }

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_optimizador_caudales_automatico", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_optimizador_caudales_automatico");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_optimizador_caudales_automatico");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["diferencia_caudal"] = diferencia_caudal;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var fecha_inicio = $('#fecha_inicio_smartmeter_optimizador_caudales_automatico').val();
    var fecha_fin = $('#fecha_fin_smartmeter_optimizador_caudales_automatico').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
    if (fechas_correctas == false) {
        return (null);
    }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de optimización de caudales manual
function dame_parametros_informe_smartmeter_optimizador_caudales_manual() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador de tarifa de gas
    var id_tarifa = $('#id_tarifa_smartmeter_optimizador_caudales_manual').val();
	if (id_tarifa == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay tarifa seleccionada"));
        return (null);
	}

    // Fichero de caudales máximos y tamaño de fichero
    if ($("#fichero_smartmeter_optimizador_caudales_manual_text").val() == "")
    {
        jAlert(TLNT.Idiomas._("No hay fichero de caudales máximos seleccionado"));
        return (null);
    }
    var tamanyo_fichero_caudales_maximos_bytes = document.getElementById("fichero_smartmeter_optimizador_caudales_manual_file").files[0].size;
    var tamanyo_fichero_caudales_maximos_kbs = (tamanyo_fichero_caudales_maximos_bytes / 1024);
    if (tamanyo_fichero_caudales_maximos_kbs > TAMANYO_MAXIMO_FICHERO_CAUDALES_MAXIMOS_KBS) {
        jAlert(TLNT.Idiomas._("El tamaño del fichero de caudales máximos es demasiado grande") + "\n" +
            "(" + TLNT.Idiomas._("tamaño máximo") + ": " + TAMANYO_MAXIMO_FICHERO_CAUDALES_MAXIMOS_KBS + " " + TLNT.Idiomas._("KBs") + ") " +
            "(" + TLNT.Idiomas._("tamaño actual") + ": " + formatea_numero(tamanyo_fichero_caudales_maximos_kbs, 0) + " " + TLNT.Idiomas._("KBs") + ")");
        return (null);
    }
    var control_seleccion_fichero_caudales_maximos = $('#fichero_smartmeter_optimizador_caudales_manual_file')[0];

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["control_seleccion_fichero_caudales_maximos"] = control_seleccion_fichero_caudales_maximos;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de simulación de caudales automático
function dame_parametros_informe_smartmeter_simulador_caudales_automatico() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador de sensor
    var id_sensor = $('#id_sensor_smartmeter_simulador_caudales_automatico').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}

    // Nombre de sensor
    var nombre_sensor = $('#id_sensor_smartmeter_simulador_caudales_automatico :selected').text();

    // Identificador de tarifa de gas
    var id_tarifa = $('#id_tarifa_smartmeter_simulador_caudales_automatico').val();
	if (id_tarifa == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay tarifa seleccionada"));
        return (null);
	}

    // Caudal
    var caudal = $('#caudal_manual_smartmeter_simulador_caudales_automatico').val();
    if (PATRON_NUMERO_NATURAL.test(caudal) == false) {
        jAlert(TLNT.Idiomas._('El caudal debe ser númerico'));
        return (null);
    }
    if (parseInt(caudal) < 0) {
        jAlert(TLNT.Idiomas._('El caudal debe ser igual o mayor que 0'));
        return (null);
    }

    // Diferencia de caudal
    var diferencia_caudal = $('#numero_diferencia_caudal_smartmeter_simulador_caudales_automatico').val();
    if (PATRON_NUMERO_ENTERO.test(diferencia_caudal) == false) {
        jAlert(TLNT.Idiomas._("La diferencia de caudal debe ser un valor numérico"));
        return (null);
    }

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_simulador_caudales_automatico", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_simulador_caudales_automatico");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_simulador_caudales_automatico");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["caudal"] = caudal;
    parametros_informe["diferencia_caudal"] = diferencia_caudal;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var fecha_inicio = $('#fecha_inicio_smartmeter_simulador_caudales_automatico').val();
    var fecha_fin = $('#fecha_fin_smartmeter_simulador_caudales_automatico').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
    if (fechas_correctas == false) {
        return (null);
    }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de simulación de caudales manual
function dame_parametros_informe_smartmeter_simulador_caudales_manual() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador de tarifa de gas
    var id_tarifa = $('#id_tarifa_smartmeter_simulador_caudales_manual').val();
	if (id_tarifa == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay tarifa seleccionada"));
        return (null);
	}

    // Fichero de caudales máximos y tamaño de fichero
    if ($("#fichero_smartmeter_simulador_caudales_manual_text").val() == "")
    {
        jAlert(TLNT.Idiomas._("No hay fichero de caudales máximos seleccionado"));
        return (null);
    }
    var tamanyo_fichero_caudales_maximos_bytes = document.getElementById("fichero_smartmeter_simulador_caudales_manual_file").files[0].size;
    var tamanyo_fichero_caudales_maximos_kbs = (tamanyo_fichero_caudales_maximos_bytes / 1024);
    if (tamanyo_fichero_caudales_maximos_kbs > TAMANYO_MAXIMO_FICHERO_CAUDALES_MAXIMOS_KBS) {
        jAlert(TLNT.Idiomas._("El tamaño del fichero de caudales máximos es demasiado grande") + "\n" +
            "(" + TLNT.Idiomas._("tamaño máximo") + ": " + TAMANYO_MAXIMO_FICHERO_CAUDALES_MAXIMOS_KBS + " " + TLNT.Idiomas._("KBs") + ") " +
            "(" + TLNT.Idiomas._("tamaño actual") + ": " + formatea_numero(tamanyo_fichero_caudales_maximos_kbs, 0) + " " + TLNT.Idiomas._("KBs") + ")");
        return (null);
    }
    var control_seleccion_fichero_caudales_maximos = $('#fichero_smartmeter_simulador_caudales_manual_file')[0];

    // Caudal
    var caudal = $('#caudal_manual_smartmeter_simulador_caudales_manual').val();
    if (PATRON_NUMERO_NATURAL.test(caudal) == false) {
        jAlert(TLNT.Idiomas._('El caudal debe ser númerico'));
        return (null);
    }
    if (parseInt(caudal) < 0) {
        jAlert(TLNT.Idiomas._('El caudal debe ser igual o mayor que 0'));
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["control_seleccion_fichero_caudales_maximos"] = control_seleccion_fichero_caudales_maximos;
    parametros_informe["caudal"] = caudal;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}