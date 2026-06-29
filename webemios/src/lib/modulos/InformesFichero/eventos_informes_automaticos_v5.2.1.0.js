//
// Eventos y funciones de informes automáticos
//


// Muestra la ventana de añadir informe automático
function muestra_ventana_anyadir_informe_automatico(tipo, parametros_tipo, parametros_tipo_json) {
    $.post("./src/lib/modulos/InformesFichero/muestra_ventana_anyadir_modificar_informe_automatico.php", {
        tipo: tipo,
        parametros_tipo: parametros_tipo,
        parametros_tipo_json: parametros_tipo_json
	},
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


// Muestra la ventana de modificar informe automático
function boton_mostrar_ventana_modificar_informe_automatico(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_informe_automatico = params[1];

    $.post("./src/lib/modulos/InformesFichero/muestra_ventana_anyadir_modificar_informe_automatico.php", {
        id_informe_automatico: id_informe_automatico
	},
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


function boton_eliminar_informe_automatico(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_informe_automatico = params[1];
    var nombre_informe_automatico = $(this).attr('nombre_informe_automatico');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el informe automático?") + "\n(" + escapeHtml(nombre_informe_automatico) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/InformesFichero/elimina_informe_automatico.php", {
                id_informe_automatico: id_informe_automatico
			},
			function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_tabla_informes_automaticos();
			});
		}
	});
}


function boton_anyadir_modificar_informe_automatico() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
	var anyadir_informe_automatico = $("#parametros_ventana_anyadir_modificar_informe_automatico").attr("anyadir_informe_automatico");
	var id_informe_automatico = $("#parametros_ventana_anyadir_modificar_informe_automatico").attr("id_informe_automatico");
    var tipo = $("#parametros_ventana_anyadir_modificar_informe_automatico").attr("tipo");
    var parametros_tipo = $("#parametros_ventana_anyadir_modificar_informe_automatico").attr("parametros_tipo");
    var parametros_tipo_json = $("#parametros_ventana_anyadir_modificar_informe_automatico").attr("parametros_tipo_json");

    // Parámetros
    var nombre = $('#nombre_informe_automatico').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_informe_automatico').addClass('data-check-failed');
        return;
    }
    var hora_envio_informe_automatico = $('#hora_envio_informe_automatico').val();
    // Se capa la hora para que siempre sean las en punto
    // y no puedan programarse con minutos
    hora_envio_informe_automatico = hora_envio_informe_automatico.substr(0,3) + '00';

    var periodicidad = $('#periodicidad_informe_automatico').val();
    var dia_generacion = ID_NINGUNO;
    var parametros_periodo_personalizado = ID_NINGUNO;

    switch (periodicidad) {
        case PERIODICIDAD_INFORME_AUTOMATICO_SEMANAL: {
            dia_generacion = $('#dia_semana_informe_automatico').val();
            break;
        }
        case PERIODICIDAD_INFORME_AUTOMATICO_MENSUAL: {
            dia_generacion = $('#dia_mes_informe_automatico').val();
            if ((parseInt(dia_generacion) < VALOR_MINIMO_DIA_MES_GENERACION_INFORME_AUTOMATICO) ||
                (parseInt(dia_generacion) > VALOR_MAXIMO_DIA_MES_GENERACION_INFORME_AUTOMATICO)) {
                var descripcion_error = TLNT.Idiomas._('El día de mes es incorrecto') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    VALOR_MINIMO_DIA_MES_GENERACION_INFORME_AUTOMATICO + " - " + VALOR_MAXIMO_DIA_MES_GENERACION_INFORME_AUTOMATICO + ")";
                jAlert(descripcion_error);
                return;
            }
            break;
        }
        // Control de parametros para periodos personalizados
        case PERIODICIDAD_INFORME_AUTOMATICO_PERSONALIZADA: {
            tipo_periodo = $('#tipo_periodo_informe_automatico').val();
            numero_periodos = $('#numero_periodos_informe_automatico').val();
            // Se formatea la fecha a formato emios YYYY-MM-DD
            parts = $('#fecha_proximo_envio_informe_automatico').val().split('/').reverse();
            fecha_proximo_envio = parts.join('-')

            if (!tipo_periodo || !numero_periodos || !fecha_proximo_envio){
                var descripcion_error = TLNT.Idiomas._('Rellene los parametros de periodo personalizado');
                jAlert(descripcion_error);
                return;
            }
            // Maximo de periodos mensuales
            if ((tipo_periodo == 'MENSUAL') && ((parseInt(numero_periodos) < 1) || (parseInt(numero_periodos) > 12))) {
                var descripcion_error = TLNT.Idiomas._('El número de meses debe ser entre 1 y 12');
                jAlert(descripcion_error);
                return;
            }
            // Maximo de periodos semanales
            if ((tipo_periodo == 'SEMANAL') && ((parseInt(numero_periodos) < 1) || (parseInt(numero_periodos) > 52))) {
                var descripcion_error = TLNT.Idiomas._('El número de semanas debe ser entre 1 y 52');
                jAlert(descripcion_error);
                return;
            }
            // Maximo de periodos diarios
            if ((tipo_periodo == 'DIARIO') && ((parseInt(numero_periodos) < 1) || (parseInt(numero_periodos) > 365))) {
                var descripcion_error = TLNT.Idiomas._('El número de dias debe ser entre 1 y 365');
                jAlert(descripcion_error);
                return;
            }
            var parametros_periodo_personalizado = [
                tipo_periodo,
                numero_periodos,
                fecha_proximo_envio].join(SEPARADOR_PARAMETROS_SIMPLES);
            break;
        }
    }
    var numero_dias_retraso_generacion = $('#numero_dias_retraso_informe_automatico').val();
    if ((parseInt(numero_dias_retraso_generacion) < VALOR_MINIMO_NUMERO_DIAS_RETRASO_GENERACION_INFORME_AUTOMATICO) ||
        (parseInt(numero_dias_retraso_generacion) > VALOR_MAXIMO_NUMERO_DIAS_RETRASO_GENERACION_INFORME_AUTOMATICO)) {
        var descripcion_error = TLNT.Idiomas._('El número de días de retraso es incorrecto') +
            " (" + TLNT.Idiomas._('rango de valores') + ": " +
            VALOR_MINIMO_NUMERO_DIAS_RETRASO_GENERACION_INFORME_AUTOMATICO + " - " + VALOR_MAXIMO_NUMERO_DIAS_RETRASO_GENERACION_INFORME_AUTOMATICO + ")";
        jAlert(descripcion_error);
        return;
    }


    var parametros_periodicidad = [
        dia_generacion,
        numero_dias_retraso_generacion].join(SEPARADOR_PARAMETROS_SIMPLES);
    var tipo_seleccion_periodo_tiempo = $('#tipo_seleccion_periodo_tiempo_informe_automatico').val();
    var periodo_tiempo = $('#periodo_tiempo_informe_automatico').val();
    var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_informe_automatico').val();
    var parametros_periodo_tiempo = [
        tipo_seleccion_periodo_tiempo,
        periodo_tiempo,
        iniciar_comienzo_periodo_tiempo].join(SEPARADOR_PARAMETROS_SIMPLES);
    var numero_horas_desplazamiento = $('#numero_horas_desplazamiento_informe_automatico').val();
    if ((parseInt(numero_horas_desplazamiento) < VALOR_MINIMO_NUMERO_HORAS_DESPLAZAMIENTO_GENERACION_INFORME_AUTOMATICO) ||
        (parseInt(numero_horas_desplazamiento) > VALOR_MAXIMO_NUMERO_HORAS_DESPLAZAMIENTO_GENERACION_INFORME_AUTOMATICO)) {
        var descripcion_error = TLNT.Idiomas._('El número de horas de desplazamiento es incorrecto') +
            " (" + TLNT.Idiomas._('rango de valores') + ": " +
            VALOR_MINIMO_NUMERO_HORAS_DESPLAZAMIENTO_GENERACION_INFORME_AUTOMATICO + " - " + VALOR_MAXIMO_NUMERO_HORAS_DESPLAZAMIENTO_GENERACION_INFORME_AUTOMATICO + ")";
        jAlert(descripcion_error);
        return;
    }
    var cadena_direcciones_destino = $('#direcciones_email_destino_informe_automatico').val();
    if (comprueba_longitud_cadena(cadena_direcciones_destino, NUMERO_MAXIMO_CARACTERES_DIRECCIONES_EMAIL) == false) {
        $('#direcciones_email_destino_informe_automatico').addClass('data-check-failed');
        return;
    }
    var direcciones_destino = cadena_direcciones_destino.split(SEPARADOR_DIRECCIONES_EMAIL);
    for (var i = 0; i < direcciones_destino.length; i++) {
        direcciones_destino[i] = direcciones_destino[i].trim();
        if (PATRON_DIRECCION_EMAIL.test(direcciones_destino[i]) == false) {
            jAlert(TLNT.Idiomas._('Las direcciones e-mail de destino deben ser correctas y separadas por punto y coma'));
            return;
        }
    }
    cadena_direcciones_destino = direcciones_destino.join(SEPARADOR_DIRECCIONES_EMAIL);

    // Se añade o modifica el informe automático
    if (anyadir_informe_automatico == true) {
        // Se añade el informe automático
        $.post("./src/lib/modulos/InformesFichero/anyade_informe_automatico.php", {
            nombre: nombre,
            hora_envio: hora_envio_informe_automatico,
            periodicidad: periodicidad,
            parametros_periodicidad: parametros_periodicidad,
            parametros_periodo_tiempo: parametros_periodo_tiempo,
            numero_horas_desplazamiento: numero_horas_desplazamiento,
            tipo: tipo,
            parametros_tipo: parametros_tipo,
            parametros_tipo_json: parametros_tipo_json,
            direcciones_email_destino: cadena_direcciones_destino,
            parametros_periodo_personalizado: parametros_periodo_personalizado
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Identificador de informe automático añadido
            var id_informe_automatico = resultado.id_informe_automatico;

            // Se guardan las imágenes (personalizadas) del informe automático
            switch (tipo) {
                case TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME: {
                    if ($('#imagenes_informe_plantilla_informe').length > 0) {
                        var cadena_ids_elementos_imagen = $("#imagenes_informe_plantilla_informe").attr("ids_elementos_imagen");
                        var ids_elementos_imagen = cadena_ids_elementos_imagen.split(SEPARADOR_PARAMETROS_SIMPLES);
                        for (var i = 0; i < ids_elementos_imagen.length; i++) {
                            var id_elemento_imagen = ids_elementos_imagen[i];
                            var fichero_imagen_elementos_imagen_text = $("#fichero_imagen_plantilla_informe_text_" + id_elemento_imagen).val();

                            // Se guarda la imagen personalizada
                            if (fichero_imagen_elementos_imagen_text != "") {
                                var id_origen = [
                                    id_informe_automatico,
                                    id_elemento_imagen].join(SEPARADOR_PARAMETROS_SIMPLES);
                                var control_fichero_imagen = $("#fichero_imagen_plantilla_informe_file_" + id_elemento_imagen)[0];
                                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_INFORME_AUTOMATICO_PLANTILLA_INFORME_IMAGEN, id_origen, control_fichero_imagen);
                                if (imagen_guardada_correcta == false) {
                                    return;
                                }
                            }
                        }
                    }
                    break;
                }
            }

            jInfo(resultado.msg);

            // Nota: No se actualiza la tabla de informes automáticos porque se añaden desde otros módulos y secciones
        });
    }
    else {
        $.post("./src/lib/modulos/InformesFichero/modifica_informe_automatico.php", {
            id_informe_automatico: id_informe_automatico,
            nombre: nombre,
            hora_envio: hora_envio_informe_automatico,
            periodicidad: periodicidad,
            parametros_periodicidad: parametros_periodicidad,
            parametros_periodo_tiempo: parametros_periodo_tiempo,
            numero_horas_desplazamiento: numero_horas_desplazamiento,
            tipo: tipo,
            parametros_tipo: parametros_tipo,
            parametros_tipo_json: parametros_tipo_json,
            direcciones_email_destino: cadena_direcciones_destino,
            parametros_periodo_personalizado: parametros_periodo_personalizado
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_informes_automaticos();
            $('#ventana_modal').modal('hide');
        });
    }
}


// Muestra la tabla de informes automáticos aplicando el filtro
function boton_filtro_informes_automaticos_tabla() {
    boton_actualizar_tabla_informes_automaticos();
}


// Actualización de la tabla de informes automáticos
function boton_actualizar_tabla_informes_automaticos() {
    actualiza_tabla_informes_automaticos();
}


// Actualiza la tabla de informes automáticos
function actualiza_tabla_informes_automaticos() {
    var filtro = $('#filtro_filtro_informes_automaticos_tabla').val();

	$.post("./src/lib/modulos/InformesFichero/dame_tabla_informes_automaticos.php", {
        filtro: filtro
    },
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		$('#tablaInformesAutomaticos').html(resultado.html);

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}