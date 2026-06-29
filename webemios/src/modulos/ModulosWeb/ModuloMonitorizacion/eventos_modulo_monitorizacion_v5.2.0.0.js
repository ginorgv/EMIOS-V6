/*
 * Módulo Monitorización
 *
 */


// Realiza el filtrado de histórico de procesado
function boton_monitorizacion_filtro_historico_procesado() {
    // Filtro de histórico de procesado
    var tipo_ejecucion_procesado = $('#tipo_ejecucion_procesado_monitorizacion_filtro_historico_procesado').val();
    var clase_sensor = $('#clase_sensor_monitorizacion_filtro_historico_procesado').val();
    var tipo_sensor = $('#tipo_sensor_monitorizacion_filtro_historico_procesado').val();
    var granularidad = $('#granularidad_monitorizacion_filtro_historico_procesado').val();

    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_monitorizacion_filtro_historico_procesado').val();
    var hora_inicio = $('#hora_inicio_monitorizacion_filtro_historico_procesado').val();
    var fecha_fin = $('#fecha_fin_monitorizacion_filtro_historico_procesado').val();
    var hora_fin = $('#hora_fin_monitorizacion_filtro_historico_procesado').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/lib/modulos/Procesado/dame_tabla_historico_procesado.php", {
        tipo_ejecucion_procesado: tipo_ejecucion_procesado,
        clase_sensor: clase_sensor,
        tipo_sensor: tipo_sensor,
        granularidad: granularidad,
		fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#tablaHistoricoProcesado").html(resultado.html);
        if (resultado.limite_elementos_tabla_superado == true) {
            jAlert(TLNT.Idiomas._("Número máximo de históricos de procesado superado (se muestran los más recientes)"));
        }

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Muestra información de tiempos de ejecución de procesado de una clase de sensor
function boton_monitorizacion_tiempos_ejecucion_procesado_ver_informe() {
    // Tipo de ejecución de procesado
    var tipo_ejecucion_procesado = $('#tipo_ejecucion_procesado_monitorizacion_tiempos_ejecucion_procesado').val();

    // Clase, tipo y granularidad
    var clase_sensor = $('#clase_sensor_monitorizacion_tiempos_ejecucion_procesado').val();
    var tipo_sensor = $('#tipo_sensor_monitorizacion_tiempos_ejecucion_procesado').val();
    if ((clase_sensor == CLASE_NINGUNA) && (tipo_sensor == TIPO_NINGUNO)) {
        jAlert(TLNT.Idiomas._('No hay clase ni tipo seleccionados'));
        return;
    }
    var granularidad = $('#granularidad_monitorizacion_tiempos_ejecucion_procesado').val();
    if ((tipo_ejecucion_procesado == TIPO_EJECUCION_PROCESADO_NORMAL) && (granularidad == GRANULARIDAD_NINGUNA)) {
        jAlert(TLNT.Idiomas._('No hay granularidad seleccionada'));
        return;
    }

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("monitorizacion_tiempos_ejecucion_procesado", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_monitorizacion_tiempos_ejecucion_procesado");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_monitorizacion_tiempos_ejecucion_procesado");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_monitorizacion_tiempos_ejecucion_procesado').val();
    var hora_inicio = $('#hora_inicio_monitorizacion_tiempos_ejecucion_procesado').val();
    var fecha_fin = $('#fecha_fin_monitorizacion_tiempos_ejecucion_procesado').val();
    var hora_fin = $('#hora_fin_monitorizacion_tiempos_ejecucion_procesado').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    hora_inicio += ":00";
    hora_fin += ":59";
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
    var fecha_hora_fin = fecha_fin + ", " + hora_fin;

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    $.post("./src/lib/modulos/Procesado/dame_informacion_tiempos_ejecucion_procesado.php", {
        tipo_ejecucion_procesado: tipo_ejecucion_procesado,
        clase_sensor: clase_sensor,
        tipo_sensor: tipo_sensor,
        granularidad: granularidad,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas)
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Comprobación de datos disponibles
        var hay_datos = resultado.hay_datos;
        if (hay_datos == false) {
            jAlert(TLNT.Idiomas._("No hay datos disponibles"));
            return;
        }

        // Se muestra el informe
        $("#informe-sin-datos-monitorizacion-tiempos-ejecucion-procesado").hide();
        $("#informe-monitorizacion-tiempos-ejecucion-procesado").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-tiempos-ejecucion-procesado",
            "texto-informacion-tiempos-ejecucion-procesado"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_grafica_tiempos_ejecucion: "grafica-tiempos-ejecucion-procesado",
            id_texto_informacion_datos_tiempos_ejecucion: "texto-informacion-tiempos-ejecucion-procesado"};
        dibuja_informe_tiempos_ejecucion_procesado(
            parametros,
            resultado);
    });
}


// Eliminación de operaciones de datos de sensores
function boton_monitorizacion_eliminar_operaciones_datos_sensores() {
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar las operaciones de datos de sensores?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/Procesado/elimina_operaciones_datos_sensores.php", {},
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_tabla_operaciones_datos_sensores();
            });
		}
	});
}


// Pausado de procesado
function boton_monitorizacion_pausar_procesado() {
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea pausar el procesado de datos?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/Procesado/envia_pausa_procesado.php", {},
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
            });
		}
	});
}


// Reanudación de procesado
function boton_monitorizacion_reanudar_procesado() {
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea reanudar el procesado de datos?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/Procesado/envia_reanudacion_procesado.php", {},
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
            });
		}
	});
}


// Envía una petición de ejecución manual de procesado
function boton_monitorizacion_ejecucion_manual_procesado() {
    // Tipo de ejecución de procesado
    var tipo_ejecucion_procesado = $('#tipo_ejecucion_procesado_monitorizacion_ejecucion_manual_procesado').val();

    // Clase y tipo de sensor
    var clase_sensor = $('#clase_sensor_monitorizacion_ejecucion_manual_procesado').val();
    var tipo_sensor = $('#tipo_sensor_monitorizacion_ejecucion_manual_procesado').val();
    if ((clase_sensor == CLASE_NINGUNA) && (tipo_sensor == TIPO_NINGUNO)) {
        jAlert(TLNT.Idiomas._('No hay clase ni tipo seleccionados'));
        return;
    }

    $.post("./src/lib/modulos/Procesado/envia_ejecucion_manual_procesado.php", {
        tipo_ejecucion_procesado: tipo_ejecucion_procesado,
		clase_sensor: clase_sensor,
        tipo_sensor: tipo_sensor
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
	});
}


// Realiza el filtrado de alarmas del servidor Emios
function boton_monitorizacion_filtro_alarmas() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_monitorizacion_filtro_alarmas').val();
    var hora_inicio = $('#hora_inicio_monitorizacion_filtro_alarmas').val();
    var fecha_fin = $('#fecha_fin_monitorizacion_filtro_alarmas').val();
    var hora_fin = $('#hora_fin_monitorizacion_filtro_alarmas').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/lib/modulos/Alarmas/dame_tabla_alarmas.php", {
        modulo: MODULO_MONITORIZACION,
        filtro: $('#filtro_monitorizacion_filtro_alarmas').val(),
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#tablaAlarmas").html(resultado.html);
        if (resultado.limite_elementos_tabla_superado == true) {
            jAlert(TLNT.Idiomas._("Número máximo de alarmas superado (se muestran las más recientes)"));
        }

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Realiza el filtrado de acciones de usuario
function boton_monitorizacion_filtro_acciones_usuario() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_monitorizacion_filtro_acciones_usuario').val();
    var hora_inicio = $('#hora_inicio_monitorizacion_filtro_acciones_usuario').val();
    var fecha_fin = $('#fecha_fin_monitorizacion_filtro_acciones_usuario').val();
    var hora_fin = $('#hora_fin_monitorizacion_filtro_acciones_usuario').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/lib/modulos/AccionesUsuario/dame_tabla_acciones.php", {
        modulo: MODULO_MONITORIZACION,
        filtro: $('#filtro_monitorizacion_filtro_acciones_usuario').val(),
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#tablaAccionesUsuario").html(resultado.html);
        if (resultado.limite_elementos_tabla_superado == true) {
            jAlert(TLNT.Idiomas._("Número máximo de acciones superado (se muestran las más recientes)"));
        }

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Realiza la exportacion de acciones de usuario
function boton_monitorizacion_exportar_acciones_usuario() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_monitorizacion_filtro_acciones_usuario').val();
    var hora_inicio = $('#hora_inicio_monitorizacion_filtro_acciones_usuario').val();
    var fecha_fin = $('#fecha_fin_monitorizacion_filtro_acciones_usuario').val();
    var hora_fin = $('#hora_fin_monitorizacion_filtro_acciones_usuario').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/lib/modulos/AccionesUsuario/exporta_acciones.php", {
        modulo: MODULO_MONITORIZACION,
        filtro: $('#filtro_monitorizacion_filtro_acciones_usuario').val(),
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se guarda el fichero de las acciones exportadas
        var ruta_fichero_acciones_exportadas = resultado.ruta_fichero_acciones_exportadas;
        if (ruta_fichero_acciones_exportadas != "") {
            jInfo(resultado.msg);
            window.location.href = ruta_fichero_acciones_exportadas;
        }
        else {
            jAlert(resultado.msg);
        }
	});
}
