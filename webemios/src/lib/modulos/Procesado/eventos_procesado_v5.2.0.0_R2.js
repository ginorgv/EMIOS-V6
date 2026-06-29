//
// Funciones de procesado
//


function boton_refrescar_tabla_historico_procesado() {
	var params = this.id.split('__');
	var id_historico_procesado = params[1];

    actualiza_tabla_historico_procesado_detalles(id_historico_procesado);
}


function actualiza_tabla_historico_procesado_detalles(id_historico_procesado) {
    // Se actualiza la información de la fila de la tabla
	$.post("./src/lib/modulos/Procesado/dame_informacion_fila_tabla_historico_procesado.php", {
        id_historico_procesado: id_historico_procesado
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_datos = "datosHistoricoProcesado__" + id_historico_procesado;
        $("#fila_" + id_datos).html(resultado.fila);

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();

        // Se actualiza la información detallada (si está visible)
        var detalles_tabla_visibles = dame_elemento_visible(id_datos + " .detalle-tabla-datos");
        if (detalles_tabla_visibles == true) {
            $.post("./comun/src/lib/modulos/dame_detalles_tabla.php", {
                id_datos: id_datos
            },
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                $('#' + id_datos + " .detalle-tabla-datos").html(resultado.html);

                // Establecimiento de eventos
                TLNT.Navegacion.establece_eventos_tablas_datos();
                TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
                TLNT.Navegacion.establece_eventos_detalles_tablas_datos();

                // Acciones 'extra' a realizar en los detalles de la tabla de datos
                TLNT.Navegacion.realiza_acciones_mostrado_detalle_tabla_datos(resultado);
            });
        }
	});
}


// Actualización de la tabla de operaciones de datos de sensores
function boton_actualizar_tabla_operaciones_datos_sensores() {
    actualiza_tabla_operaciones_datos_sensores();
}


function actualiza_tabla_operaciones_datos_sensores() {
    var modulo = $('#modulo').attr("name");

	$.post("./src/lib/modulos/Procesado/dame_tablas_operaciones_datos_sensores.php", {
        modulo: modulo,
        actualizacion_periodica_activada: actualizacion_periodica_operaciones_datos_sensores_activada
    },
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se actualizan las tablas de operaciones de datos de sensores
        $('#tablaOperacionesDatosSensores').html(resultado.html_tabla_operaciones_datos_sensores);
        $('#tabla-importaciones-valores-sensores-pendientes').html(resultado.html_tabla_importaciones_valores_sensores_pendientes);
        $('#tabla-recalculos-valores-clase-HORARIA').html(resultado.html_tabla_recalculos_valores_clase_horarios);
        $('#tabla-recalculos-valores-clase-CUARTOHORARIA').html(resultado.html_tabla_recalculos_valores_clase_cuartohorarios);
        $('#tabla-sensores-procesado-valores-antiguos-HORARIA').html(resultado.html_tabla_sensores_procesado_valores_antiguos_horarios);
        $('#tabla-sensores-procesado-valores-antiguos-CUARTOHORARIA').html(resultado.html_tabla_sensores_procesado_valores_antiguos_cuartohorarios);

        // Se actualiza el filtro de histórico de importaciones de valores de sensores
        boton_filtro_historico_importaciones_valores_sensores();

        // Se actualiza la fecha de actualización de la tabla de operaciones de datos de sensores
        actualiza_fecha_actualizacion_tabla_operaciones_datos_sensores();

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Actualiza en el pie de la página la fecha de actualización de la tabla de operaciones de datos de sensores
function actualiza_fecha_actualizacion_tabla_operaciones_datos_sensores() {
    var fecha_actual = new Date();
    var cadena_fecha_actual = convierte_fecha_a_cadena(fecha_actual, formato_fecha_local_jquery_ui);
    cadena_fecha_actual += ", " + dame_cadena_hora(fecha_actual);
    var texto_actualizado_hora_actual = TLNT.Idiomas._("hora de actualización de tabla") + ": " + cadena_fecha_actual;
    actualiza_texto_pie_pagina(texto_actualizado_hora_actual);
}


// Actualización periódica de la tabla de operaciones de datos de sensores
function boton_actualizacion_periodica_tabla_operaciones_datos_sensores() {
    inicia_actualizacion_periodica_tabla_operaciones_datos_sensores();
}


// Inicia la actualización periódica de la tabla de operaciones de datos de sensores
function inicia_actualizacion_periodica_tabla_operaciones_datos_sensores() {
    // Se activa o desactiva la actualización periódica de la tabla de nodos
    if (temporizador_actualizacion_pagina == null) {
        jPrompt(TLNT.Idiomas._("Intervalo de actualización periódica de operaciones de datos de sensores") + " (" + TLNT.Idiomas._("segundos") + ")",
            SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_OPERACIONES_DATOS_SENSORES_DEFECTO,
            TLNT.Idiomas._("Pregunta"),
            function(valor) {
                if (valor != null) {
                    if ((isNaN(valor) == true) || (
                        (valor < MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_OPERACIONES_DATOS_SENSORES_DEFECTO) ||
                        (valor > MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_OPERACIONES_DATOS_SENSORES_DEFECTO))) {
                        var mensaje_aviso = TLNT.Idiomas._("Intervalo de actualización periódica de operaciones de datos de sensores no válido") +
                            " (" + MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_OPERACIONES_DATOS_SENSORES_DEFECTO + " - " + MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_OPERACIONES_DATOS_SENSORES_DEFECTO + ")";
                        jAlert(mensaje_aviso, TLNT.Idiomas._("Aviso"), function(res) {
                            inicia_actualizacion_periodica_tabla_operaciones_datos_sensores();
                        });
                    }
                    else {
                        actualizacion_periodica_operaciones_datos_sensores_activada = true;
                        segundos_intervalo_actualizacion_tabla_operaciones_datos_sensores = valor;
                        temporizador_actualizacion_pagina = setTimeout(
                            expiracion_timeout_actualizacion_periodica_tabla_operaciones_datos_sensores,
                            segundos_intervalo_actualizacion_tabla_operaciones_datos_sensores * 1000);
                        jInfo(TLNT.Idiomas._("Actualización periódica de operaciones de datos de sensores activada"));

                        // Actualizar el icono de actualización periódica
                        $('#boton_actualizacion_periodica_tabla_operaciones_datos_sensores').removeClass("icon-play");
                        $('#boton_actualizacion_periodica_tabla_operaciones_datos_sensores').addClass("icon-pause");

                        // Se actualiza la tabla de operaciones de datos de sensores
                        actualiza_tabla_operaciones_datos_sensores();
                    }
                }
            }
        );
    }
    else {
        desactiva_actualizacion_periodica_tabla_operaciones_datos_sensores();
    }
}


// Expiración del timeout para actualización periódica de la tabla de operaciones de datos de sensores
function expiracion_timeout_actualizacion_periodica_tabla_operaciones_datos_sensores() {
    actualiza_tabla_operaciones_datos_sensores();
    temporizador_actualizacion_pagina = setTimeout(
        expiracion_timeout_actualizacion_periodica_tabla_operaciones_datos_sensores,
        segundos_intervalo_actualizacion_tabla_operaciones_datos_sensores * 1000);
}


// Desactiva la actualización periódica de la tabla de operaciones de datos de sensores
function desactiva_actualizacion_periodica_tabla_operaciones_datos_sensores() {
    if (temporizador_actualizacion_pagina != null) {
        actualizacion_periodica_operaciones_datos_sensores_activada = false;
        clearTimeout(temporizador_actualizacion_pagina);
        temporizador_actualizacion_pagina = null;

        jInfo(TLNT.Idiomas._("Actualización periódica de operaciones de datos de sensores desactivada"));

        $('.boton_actualizacion_periodica_tabla_operaciones_datos_sensores').removeClass("icon-pause");
        $('.boton_actualizacion_periodica_tabla_operaciones_datos_sensores').addClass("icon-play");
    }
}


//
// Funciones de importaciones de valores de sensores pendientes
//


// Elimina una importación de valores de un sensor pendiente
function boton_eliminar_importacion_valores_sensor_pendiente(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    // Identificador, nombre de sensor y hora
    var params = this.id.split('__');
    var id_importacion_pendiente = params[1];
    var hora = $(this).attr('hora');
    var nombre_sensor = $(this).attr('nombre_sensor');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la importación de valores de sensor?") +
        "\n(" + TLNT.Idiomas._("hora") + ": " + hora + ", " + TLNT.Idiomas._("sensor") + ": " + escapeHtml(nombre_sensor) + ")",
        TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            $.post("./src/lib/modulos/Procesado/ImportacionesValoresSensores/elimina_importacion_valores_sensor_pendiente.php", {
                id_importacion_pendiente: id_importacion_pendiente
            },
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                var modulo = $('#modulo').attr("name");
                actualiza_tabla_importaciones_valores_sensores_pendientes(modulo);
            });
        }
    });
}


// Actualiza la tabla de importaciones de valores de sensores pendientes
function actualiza_tabla_importaciones_valores_sensores_pendientes(modulo) {
	$.post("./src/lib/modulos/Procesado/ImportacionesValoresSensores/dame_tabla_importaciones_valores_sensores_pendientes.php", {
        modulo: modulo
    },
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		$('#tablaImportacionesValoresSensoresPendientes').html(resultado.html);

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Realiza el filtrado de histórico de importaciones de valores de sensores
function boton_filtro_historico_importaciones_valores_sensores() {
    var modulo = $('#modulo').attr('name');

    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_filtro_historico_importaciones_valores_sensores').val();
    var hora_inicio = $('#hora_inicio_filtro_historico_importaciones_valores_sensores').val();
    var fecha_fin = $('#fecha_fin_filtro_historico_importaciones_valores_sensores').val();
    var hora_fin = $('#hora_fin_filtro_historico_importaciones_valores_sensores').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/lib/modulos/Procesado/ImportacionesValoresSensores/dame_tabla_historico_importaciones_valores_sensores.php", {
        modulo: modulo,
        filtro: $('#filtro_filtro_historico_importaciones_valores_sensores').val(),
        clase_sensor: $('#clase_sensor_filtro_historico_importaciones_valores_sensores').val(),
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        resultado_ejecucion: $('#resultado_ejecucion_filtro_historico_importaciones_valores_sensores').val()
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#tablaHistoricoImportacionesValoresSensores").html(resultado.html);
        if (resultado.limite_elementos_tabla_superado == true) {
            jAlert(TLNT.Idiomas._("Número máximo de históricos de importaciones de valores de sensores (se muestran los más recientes)"));
        }

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Ventana de repetición importación de valores de un sensor
function boton_mostrar_ventana_repetir_importacion_valores_sensor() {
    var params = this.id.split('__');
	var id_historico_importacion_valores_sensor = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloSensores/muestra_ventana_importacion_valores_sensor.php", {
        id_historico_importacion_valores_sensor: id_historico_importacion_valores_sensor
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

        // Botón de ayuda
        $('#boton_ayuda_ventana_modal').addClass("boton_sensores_ayuda_importacion_valores_sensor");
        $('#boton_ayuda_ventana_modal').show();

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales_sensores();
	});
}
