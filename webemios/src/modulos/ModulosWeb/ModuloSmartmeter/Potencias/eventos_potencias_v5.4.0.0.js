//
// Funciones de potencias (de SmartMeter)
//


// Muestra el informe de optimización de potencias automático
function boton_smartmeter_optimizador_potencias_automatico_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_optimizador_potencias_automatico();
    if (parametros_informe == null) {
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
        muestra_informe_optimizador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_AUTOMATICO, data);
    });
}


// Descarga la plantilla de fichero de potencias máximas para la optimización manual
function boton_smartmeter_optimizador_potencias_manual_descargar_plantilla_fichero() {
    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_optimizador_potencias_manual_descargar_plantilla_fichero_Espanya();
            break;
        }
    }
}


// Muestra el informe de optimización de potencias manual
function boton_smartmeter_optimizador_potencias_manual_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_optimizador_potencias_manual();
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_tarifa = parametros_informe["id_tarifa"];
    var control_seleccion_fichero_potencias_maximas = parametros_informe["control_seleccion_fichero_potencias_maximas"];

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_tarifa", id_tarifa);
    datos_formulario.append("fichero_potencias_maximas", control_seleccion_fichero_potencias_maximas.files[0]);

    // Llamada 'ajax' POST
    $.ajax({
        url: "./src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/dame_costes_potencias_optimas_fichero_tarifa_electrica.php",
        type: "POST",
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            // Se muestra el informe
            muestra_informe_optimizador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_MANUAL, result);
        },
        error: function(request, status, err) {}
    });
}


// Muestra el informe de los optimizadores de potencias
function muestra_informe_optimizador_potencias(tipo_optimizador_potencias, resultado_script_php_json) {
    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            muestra_informe_optimizador_potencias_Espanya(tipo_optimizador_potencias, resultado_script_php_json);
            break;
        }
    }
}


// Muestra el informe de simulación de potencias automático
function boton_smartmeter_simulador_potencias_automatico_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_potencias_automatico();
    if (parametros_informe == null) {
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
        granularidad: granularidad,
        potencias_seleccionadas: potencias_tramos,
        rango_potencias: rango_potencias,
        diferencia_potencia: diferencia_potencia,
        horario_semanal: cadena_horario_semanal,
        exclusion_fechas: cadena_exclusion_fechas,
        inclusion_fechas: cadena_inclusion_fechas,
        minutos_desfase_utc: minutos_desfase_utc
    },
    function (data, status) {
        // Se muestra el informe
        muestra_informe_simulador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_AUTOMATICO, data);
    });
}


// Descarga la plantilla de fichero de potencias máximas para la simulación manual
function boton_smartmeter_simulador_potencias_manual_descargar_plantilla_fichero() {
    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_simulador_potencias_manual_descargar_plantilla_fichero_Espanya();
            break;
        }
    }
}


// Muestra el informe de simulación de potencias manual
function boton_smartmeter_simulador_potencias_manual_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_potencias_manual();
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_tarifa = parametros_informe["id_tarifa"];
    var control_seleccion_fichero_potencias_maximas = parametros_informe["control_seleccion_fichero_potencias_maximas"];
    var potencias_tramos = parametros_informe["potencias_tramos"];

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_tarifa", id_tarifa);
    datos_formulario.append("fichero_potencias_maximas", control_seleccion_fichero_potencias_maximas.files[0]);
    datos_formulario.append("potencias_seleccionadas", potencias_tramos);

    // Llamada 'ajax' POST
    $.ajax({
        url: "./src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/dame_costes_potencias_seleccionadas_fichero_tarifa_electrica.php",
        type: "POST",
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            // Se muestra el informe
            muestra_informe_simulador_potencias(TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_MANUAL, result);
        },
        error: function(request, status, err) {}
    });
}


// Muestra el informe de los simuladores de potencias
function muestra_informe_simulador_potencias(tipo_simulador_potencias, resultado_script_php_json) {
    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            muestra_informe_simulador_potencias_Espanya(tipo_simulador_potencias, resultado_script_php_json);
            break;
        }
    }
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de optimización de potencias automático
function dame_parametros_informe_smartmeter_optimizador_potencias_automatico() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador de sensor
    var id_sensor = $('#id_sensor_smartmeter_optimizador_potencias_automatico').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}

    // Nombre de sensor
    var nombre_sensor = $('#id_sensor_smartmeter_optimizador_potencias_automatico :selected').text();

    // Identificador de tarifa eléctrica
    var id_tarifa = $('#id_tarifa_smartmeter_optimizador_potencias_automatico').val();
	if (id_tarifa == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay tarifa seleccionada"));
        return (null);
	}

    // Granularidad y rango de potencias
    var granularidad = $('#granularidad_smartmeter_optimizador_potencias_automatico').val();
    var rango_potencias = $('#rango_potencias_smartmeter_optimizador_potencias_automatico').val();

    // Diferencia de potencia
    var diferencia_potencia = $('#numero_diferencia_potencia_smartmeter_optimizador_potencias_automatico').val();
    if (PATRON_NUMERO_ENTERO.test(diferencia_potencia) == false) {
        jAlert(TLNT.Idiomas._("La diferencia de potencia debe ser un valor numérico"));
        return (null);
    }

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_optimizador_potencias_automatico", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_optimizador_potencias_automatico");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_optimizador_potencias_automatico");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

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
    var fecha_inicio = $('#fecha_inicio_smartmeter_optimizador_potencias_automatico').val();
    var fecha_fin = $('#fecha_fin_smartmeter_optimizador_potencias_automatico').val();
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


// Devuelve los parámetros del informe de optimización de potencias manual
function dame_parametros_informe_smartmeter_optimizador_potencias_manual() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador de tarifa eléctrica
    var id_tarifa = $('#id_tarifa_smartmeter_optimizador_potencias_manual').val();
	if (id_tarifa == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay tarifa seleccionada"));
        return (null);
	}

    // Ficheros de potencias máximas y tamaño de fichero
    if ($("#fichero_smartmeter_optimizador_potencias_manual_text").val() == "")
    {
        jAlert(TLNT.Idiomas._("No hay fichero de potencias máximas seleccionado"));
        return (null);
    }
    var tamanyo_fichero_potencias_maximas_bytes = document.getElementById("fichero_smartmeter_optimizador_potencias_manual_file").files[0].size;
    var tamanyo_fichero_potencias_maximas_kbs = (tamanyo_fichero_potencias_maximas_bytes / 1024);
    if (tamanyo_fichero_potencias_maximas_kbs > TAMANYO_MAXIMO_FICHERO_POTENCIAS_MAXIMAS_KBS) {
        jAlert(TLNT.Idiomas._("El tamaño del fichero de potencias máximas es demasiado grande") + "\n" +
            "(" + TLNT.Idiomas._("tamaño máximo") + ": " + TAMANYO_MAXIMO_FICHERO_POTENCIAS_MAXIMAS_KBS + " " + TLNT.Idiomas._("KBs") + ") " +
            "(" + TLNT.Idiomas._("tamaño actual") + ": " + formatea_numero(tamanyo_fichero_potencias_maximas_kbs, 0) + " " + TLNT.Idiomas._("KBs") + ")");
        return (null);
    }
    var control_seleccion_fichero_potencias_maximas = $('#fichero_smartmeter_optimizador_potencias_manual_file')[0];

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["control_seleccion_fichero_potencias_maximas"] = control_seleccion_fichero_potencias_maximas;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de simulación de potencias automático
function dame_parametros_informe_smartmeter_simulador_potencias_automatico() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador de sensor
    var id_sensor = $('#id_sensor_smartmeter_simulador_potencias_automatico').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}

    // Nombre de sensor
    var nombre_sensor = $('#id_sensor_smartmeter_simulador_potencias_automatico :selected').text();

    // Identificador de tarifa eléctrica
    var id_tarifa = $('#id_tarifa_smartmeter_simulador_potencias_automatico').val();
	if (id_tarifa == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay tarifa seleccionada"));
        return (null);
	}

    // Granularidad y rango de potencias
    var granularidad = $('#granularidad_smartmeter_simulador_potencias_automatico').val();
    var rango_potencias = $('#rango_potencias_smartmeter_simulador_potencias_automatico').val();

    // Diferencia de potencia
    var diferencia_potencia = $('#numero_diferencia_potencia_smartmeter_simulador_potencias_automatico').val();
    if (PATRON_NUMERO_ENTERO.test(diferencia_potencia) == false) {
        jAlert(TLNT.Idiomas._("La diferencia de potencia debe ser un valor numérico"));
        return (null);
    }

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_simulador_potencias_automatico", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_simulador_potencias_automatico");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_simulador_potencias_automatico");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

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

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_tarifa", id_tarifa);

    // Nota: Las llamadas Ajax son síncronas porque es necesario recupera la información de la tarifa eléctrica antes de continuar

    // Selección de país
    var potencias_tramos_correctas = true;
    var potencias_tramos = [];
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            $.ajax({
                url: "./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/dame_info_tarifa_electricidad_Espanya.php",
                type: "POST",
                async: false,
                data: datos_formulario,
                processData: false,
                contentType: false,
                success: function(result) {
                    var resultado = dame_resultado_ejecucion_script_php_json(result);
                    if (resultado == null) {
                        return (null);
                    }

                    // Tipo de tarifa eléctrica seleccionada y número de tramos
                    var tipo_tarifa_electrica = resultado.tipo_tarifa_electrica;
                    var numero_tramos_tarifa_electrica = parseInt(resultado.numero_tramos);

                    // Se recuperan las potencias de los tramos
                    potencias_tramos = [];
                    for (var i = 1; i <= numero_tramos_tarifa_electrica; i++) {
                        var id_potencia_tramo = "#potencia_manual_tramo_" + i + "_smartmeter_simulador_potencias_automatico";
                        potencias_tramos[i - 1] = $(id_potencia_tramo).val();
                        if (PATRON_NUMERO_REAL.test(potencias_tramos[i - 1]) == false) {
                            jAlert(TLNT.Idiomas._("La potencia debe ser un valor numérico"));
                            potencias_tramos_correctas = false;
                            return (null);
                        }
                    }

                    // Se comprueba que las potencias de los tramos son correctas
                    potencias_tramos_correctas = comprueba_potencias_tramos_tarifa_electrica_correctas(
                        tipo_tarifa_electrica,
                        potencias_tramos);
                }
            });
            break;
        }
    }
    if (potencias_tramos_correctas == false) {
        return (null);
    }
    parametros_informe["potencias_tramos"] = potencias_tramos;

    // Se recuperan las fechas
    var fecha_inicio = $('#fecha_inicio_smartmeter_simulador_potencias_automatico').val();
    var fecha_fin = $('#fecha_fin_smartmeter_simulador_potencias_automatico').val();
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


// Devuelve los parámetros del informe de simulación de potencias manual
function dame_parametros_informe_smartmeter_simulador_potencias_manual() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador de tarifa eléctrica
    var id_tarifa = $('#id_tarifa_smartmeter_simulador_potencias_manual').val();
	if (id_tarifa == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay tarifa seleccionada"));
        return (null);
	}

    // Fichero de potencias máximas y tamaño de fichero
    if ($("#fichero_smartmeter_simulador_potencias_manual_text").val() == "")
    {
        jAlert(TLNT.Idiomas._("No hay fichero de potencias máximas seleccionado"));
        return (null);
    }
    var tamanyo_fichero_potencias_maximas_bytes = document.getElementById("fichero_smartmeter_simulador_potencias_manual_file").files[0].size;
    var tamanyo_fichero_potencias_maximas_kbs = (tamanyo_fichero_potencias_maximas_bytes / 1024);
    if (tamanyo_fichero_potencias_maximas_kbs > TAMANYO_MAXIMO_FICHERO_POTENCIAS_MAXIMAS_KBS) {
        jAlert(TLNT.Idiomas._("El tamaño del fichero de potencias máximas es demasiado grande") + "\n" +
            "(" + TLNT.Idiomas._("tamaño máximo") + ": " + TAMANYO_MAXIMO_FICHERO_POTENCIAS_MAXIMAS_KBS + " " + TLNT.Idiomas._("KBs") + ") " +
            "(" + TLNT.Idiomas._("tamaño actual") + ": " + formatea_numero(tamanyo_fichero_potencias_maximas_kbs, 0) + " " + TLNT.Idiomas._("KBs") + ")");
        return (null);
    }
    var control_seleccion_fichero_potencias_maximas = $('#fichero_smartmeter_simulador_potencias_manual_file')[0];

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["control_seleccion_fichero_potencias_maximas"] = control_seleccion_fichero_potencias_maximas;
    parametros_informe["potencias_tramos"] = potencias_tramos;

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_tarifa", id_tarifa);

    // Nota: Las llamadas Ajax son síncronas porque es necesario recupera la información de la tarifa eléctrica antes de continuar

    // Selección de país
    var potencias_tramos_correctas = true;
    var potencias_tramos = [];
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            $.ajax({
                url: "./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/dame_info_tarifa_electricidad_Espanya.php",
                type: "POST",
                async: false,
                data: datos_formulario,
                processData: false,
                contentType: false,
                success: function(result) {
                    var resultado = dame_resultado_ejecucion_script_php_json(result);
                    if (resultado == null) {
                        return (null);
                    }

                    // Tipo de tarifa eléctrica seleccionada y número de tramos
                    var tipo_tarifa_electrica = resultado.tipo_tarifa_electrica;
                    var numero_tramos_tarifa_electrica = parseInt(resultado.numero_tramos);

                    // Se recuperan las potencias de los tramos
                    potencias_tramos = [];
                    for (var i = 1; i <= numero_tramos_tarifa_electrica; i++) {
                        var id_potencia_tramo = "#potencia_manual_tramo_" + i + "_smartmeter_simulador_potencias_manual";
                        potencias_tramos[i - 1] = $(id_potencia_tramo).val();
                        if (PATRON_NUMERO_REAL.test(potencias_tramos[i - 1]) == false) {
                            jAlert(TLNT.Idiomas._("La potencia debe ser un valor numérico"));
                            potencias_tramos_correctas = false;
                            return (null);
                        }
                    }

                    // Se comprueba que las potencias de los tramos son correctas
                    potencias_tramos_correctas = comprueba_potencias_tramos_tarifa_electrica_correctas(
                        tipo_tarifa_electrica,
                        potencias_tramos);
                }
            });
            break;
        }
    }
    if (potencias_tramos_correctas == false) {
        return (null);
    }
    parametros_informe["potencias_tramos"] = potencias_tramos;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}