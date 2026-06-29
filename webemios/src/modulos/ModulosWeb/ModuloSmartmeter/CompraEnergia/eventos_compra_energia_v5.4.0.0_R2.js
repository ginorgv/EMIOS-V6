//
// Funciones de compra de energía (de SmartMeter)
//


//
// Funciones de herramientas de compra de energía (de SmartMeter)
//


// Ventana de importación de valores diarios de compra de energia de un sensor
function boton_smartmeter_mostrar_ventana_importacion_valores_diarios_compra_energia_sensor() {
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/muestra_ventana_importacion_valores_diarios_compra_energia_sensor.php", {},
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		// Se muestra la ventana modal
        $('#ventana_modal').modal('show');
		TLNT.Navegacion.carga_ventana_modal(
			resultado.titulo,
			resultado.contenido,
			resultado.pie);

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales();
	});
}


// Importación de valores diarios de compra de energía de un sensor
function boton_smartmeter_importar_valores_diarios_compra_energia_sensor() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_importacion_valores_diarios_compra_energia_sensor').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return;
	}
    var nombre_sensor = $('#id_sensor_importacion_valores_diarios_compra_energia_sensor :selected').text();

    // Se comprueba el tamaño del fichero
    var tamanyo_fichero_importacion_bytes = document.getElementById('fichero_importacion_valores_diarios_compra_energia_sensor_file').files[0].size;
    var tamanyo_fichero_importacion_kbs = (tamanyo_fichero_importacion_bytes / 1024);
    if (tamanyo_fichero_importacion_kbs > TAMANYO_MAXIMO_FICHERO_VALORES_KBS) {
        jAlert(TLNT.Idiomas._("El tamaño del fichero de valores es demasiado grande") + "\n" +
            "(" + TLNT.Idiomas._("tamaño máximo") + ": " + formatea_numero(TAMANYO_MAXIMO_FICHERO_VALORES_KBS, 0) + " " + TLNT.Idiomas._("KBs") + ") " +
            "(" + TLNT.Idiomas._("tamaño actual") + ": " + formatea_numero(tamanyo_fichero_importacion_kbs, 0) + " " + TLNT.Idiomas._("KBs") + ")");
        return;
    }

    // Pregunta de confirmación
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("Los valores guardados del sensor en el rango de fechas a importar se borrarán. ¿Está seguro?"), TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            // http://stackoverflow.com/questions/4069982/document-getelementbyid-vs-jquery
            var control_seleccion_fichero_importacion = $('#fichero_importacion_valores_diarios_compra_energia_sensor_file')[0];

            // Se crean los datos del formulario
            var datos_formulario = new FormData();
            datos_formulario.append("nombre_sensor", nombre_sensor);
            datos_formulario.append("fichero_valores", control_seleccion_fichero_importacion.files[0]);
            datos_formulario.append("origen_importacion_valores", ORIGEN_IMPORTACION_VALORES_DIARIOS_COMPRA_ENERGIA_SENSOR_HERRAMIENTAS);

            // Llamada 'ajax' POST
            $.ajax({
                url: "./src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/importa_valores_diarios_compra_energia_sensor.php",
                type: "POST",
                data: datos_formulario,
                processData: false,
                contentType: false,
                timeout: SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX_SEGUNDO_PLANO * 1000,
                success: function(result) {
                    var resultado = dame_resultado_ejecucion_script_php_json(result);
                    if (resultado == null) {
                        return;
                    }

                    jInfo(resultado.msg);
                }
            });
        }
    });
}


// Ventana de recálculo de valores de compra de energía de un sensor (y de su sensor asociado)
function boton_smartmeter_mostrar_ventana_recalculo_valores_compra_energia_sensor() {
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/muestra_ventana_recalculo_valores_compra_energia_sensor.php", {},
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se muestra la ventana modal
        $('#ventana_modal').modal('show');
		TLNT.Navegacion.carga_ventana_modal(
			resultado.titulo,
			resultado.contenido,
			resultado.pie);

        // Se modifica el estilo de la ventana modal
        $('#ventana_modal .modal-body').removeClass('mostrar-barra-desplazamiento-y');
        $('#ventana_modal .modal-body').addClass('mostrar-todos-elementos-y');

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales();
	});
}


// Recálculo de valores de compra de energía de un sensor
function boton_smartmeter_recalcular_valores_compra_energia_sensor() {
    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_recalculo_valores_compra_energia_sensor').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return;
	}
    var nombre_sensor = $('#id_sensor_recalculo_valores_compra_energia_sensor :selected').text();

    // Fecha
    var fecha = $('#fecha_inicio_recalculo_valores_compra_energia_sensor').val();

    // Se borran los valores del sensor
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("Los valores de compra de energía del sensor posteriores a la fecha seleccionada se recalcularán. ¿Está seguro?"), TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            // Se recalculan los valores de compra de energía del sensor:
            // - 1: Se borran los valores del sensor asociado a partir de la fecha de recálculo de valores
            // - 2: Se guarda la fecha de recálculo de valores de clase del sensor de compra de energía
            var fecha_hora = fecha + ", " + "00:00:00";
            $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/recalcula_valores_compra_energia_sensor.php", {
                nombre_sensor: nombre_sensor,
                id_sensor: id_sensor,
                fecha_hora: fecha_hora
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
            });
        }
    });
}


//
// Funciones de informes de compra de energía (de SmartMeter)
//


// Muestra el informe de previsión de compra de energía
function boton_smartmeter_prevision_compra_energia_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_prevision_compra_energia();
    if (parametros_informe == null) {
        return;
    }

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_prevision_compra_energia_ver_informe_Espanya(parametros_informe);
            break;
        }
    }
}


// Exporta los valores a fichero del informe de previsión de compra de energía e importa estos valores al sensor de compra de energía
function boton_smartmeter_prevision_compra_energia_exportar_importar_valores_diarios() {
    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_prevision_compra_energia_exportar_importar_valores_diarios_Espanya();
            break;
        }
    }
}


// Muestra el informe de desvíos de compra de energía
function boton_smartmeter_desvios_compra_energia_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_desvios_compra_energia(false);
    if (parametros_informe == null) {
        return;
    }

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_desvios_compra_energia_ver_informe_Espanya(parametros_informe);
            break;
        }
    }
}


// Muestra el informe de desvíos ponderados de compra de energía
function boton_smartmeter_desvios_ponderados_compra_energia_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_desvios_ponderados_compra_energia(false);
    if (parametros_informe == null) {
        return;
    }

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_desvios_ponderados_compra_energia_ver_informe_Espanya(parametros_informe);
            break;
        }
    }
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de previsión de compra de energía
function dame_parametros_informe_smartmeter_prevision_compra_energia() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_prevision_compra_energia').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_smartmeter_prevision_compra_energia :selected').text();

    // Fechas de inicio y fin de perfil horario
    var fecha_inicio_perfil_horario = $('#fecha_inicio_perfil_horario_smartmeter_prevision_compra_energia').val();
    var fecha_fin_perfil_horario = $('#fecha_fin_perfil_horario_smartmeter_prevision_compra_energia').val();
    var fechas_perfil_horario_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio_perfil_horario, null, fecha_fin_perfil_horario, null);
    if (fechas_perfil_horario_correctas == false) {
        return (null);
    }

    // Tipo de perfil horario y agrupaciones de días de la semana
    var tipo_perfil_horario = $('#tipo_perfil_horario_smartmeter_prevision_compra_energia').val();
    var agrupaciones_dias_semana = dame_agrupaciones_dias_semana_control("cadena_agrupaciones_dias_semana_smartmeter_prevision_compra_energia");
    if (agrupaciones_dias_semana.correcto == false) {
        return (null);
    }
    if (tipo_perfil_horario == TIPO_PERFIL_HORARIO_CONFIGURABLE) {
        if (agrupaciones_dias_semana.agrupaciones_dias.length == 0) {
            jAlert(TLNT.Idiomas._("No hay agrupaciones de días de la semana"));
            return (null);
        }
    }

    // Exclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_prevision_compra_energia");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }

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
    var fecha_inicio = $('#fecha_inicio_smartmeter_prevision_compra_energia').val();
    var fecha_fin = $('#fecha_fin_smartmeter_prevision_compra_energia').val();
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


// Devuelve los parámetros del informe de desvíos de compra de energía
function dame_parametros_informe_smartmeter_desvios_compra_energia(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_desvios_compra_energia').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_smartmeter_desvios_compra_energia :selected').text();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_desvios_compra_energia", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_desvios_compra_energia");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_desvios_compra_energia");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_desvios_compra_energia').val();
        var hora_inicio = $('#hora_inicio_smartmeter_desvios_compra_energia').val();
        var fecha_fin = $('#fecha_fin_smartmeter_desvios_compra_energia').val();
        var hora_fin = $('#hora_fin_smartmeter_desvios_compra_energia').val();
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
    }

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de desvíos ponderados de compra de energía
function dame_parametros_informe_smartmeter_desvios_ponderados_compra_energia(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_desvios_ponderados_compra_energia').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor de compra de energía seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_smartmeter_desvios_ponderados_compra_energia :selected').text();

    // Identificador y nombre de sensor hijo
    var id_sensor_hijo = $('#id_sensor_hijo_smartmeter_desvios_ponderados_compra_energia').val();
    if (id_sensor_hijo == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor hijo seleccionado"));
        return (null);
	}
    var nombre_sensor_hijo = $('#id_sensor_hijo_smartmeter_desvios_ponderados_compra_energia :selected').text();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_desvios_ponderados_compra_energia", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_desvios_ponderados_compra_energia");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_desvios_ponderados_compra_energia");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["id_sensor_hijo"] = id_sensor_hijo;
    parametros_informe["nombre_sensor_hijo"] = nombre_sensor_hijo;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_desvios_ponderados_compra_energia').val();
        var hora_inicio = $('#hora_inicio_smartmeter_desvios_ponderados_compra_energia').val();
        var fecha_fin = $('#fecha_fin_smartmeter_desvios_ponderados_compra_energia').val();
        var hora_fin = $('#hora_fin_smartmeter_desvios_ponderados_compra_energia').val();
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
    }

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}

