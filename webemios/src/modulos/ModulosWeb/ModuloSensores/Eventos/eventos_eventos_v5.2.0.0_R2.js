//
// Funciones de eventos (sensores y grupos de sensores)
//


// Muestra la tabla de eventos aplicando el filtro
function boton_sensores_filtro_eventos_tabla() {
    boton_sensores_actualizar_tabla_eventos();
}


// Realiza el filtrado de histórico de eventos
function boton_sensores_filtro_historico_eventos() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_sensores_filtro_historico_eventos').val();
    var hora_inicio = $('#hora_inicio_sensores_filtro_historico_eventos').val();
    var fecha_fin = $('#fecha_fin_sensores_filtro_historico_eventos').val();
    var hora_fin = $('#hora_fin_sensores_filtro_historico_eventos').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_tabla_historico_eventos.php", {
        filtro: $('#filtro_sensores_filtro_historico_eventos').val(),
        clase_sensor: $('#clase_sensor_sensores_filtro_historico_eventos').val(),
		fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        tipo_fecha: $('#tipo_fecha_sensores_filtro_historico_eventos').val()
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#tablaHistoricoEventos").html(resultado.html);
        if (resultado.limite_elementos_tabla_superado == true) {
            jAlert(TLNT.Idiomas._("Número máximo de históricos de eventos superado (se muestran los más recientes)"));
        }

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


function boton_sensores_mostrar_ventana_anyadir_modificar_evento(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_evento = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/muestra_ventana_anyadir_modificar_evento.php", {
        id_evento: id_evento,
        tipo_operacion_administracion: tipo_operacion_administracion
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


function boton_sensores_eliminar_evento(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_evento = params[1];
    var nombre_evento = $(this).attr('nombre_evento');

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el evento?") + "\n(" + escapeHtml(nombre_evento) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/elimina_evento.php", {
                modulo: $("#modulo").attr("name"),
				id_evento: id_evento
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
				actualiza_tabla_eventos();
			});
		}
	});
}


function boton_sensores_anyadir_modificar_evento() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
	var anyadir_evento = $("#parametros_ventana_anyadir_modificar_evento").attr("anyadir_evento");
	var id_evento = $("#parametros_ventana_anyadir_modificar_evento").attr("id_evento");

    // No se permiten comas (',') en el nombre del evento
    // (en el histórico de eventos se guardan los nombres separados por comas)
    var nombre = $('#nombre_evento').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_evento").addClass('data-check-failed');
        return;
    }
    if (nombre.indexOf(",") > -1) {
        jAlert(TLNT.Idiomas._('No se permiten comas en el nombre del evento'));
        return;
    }

    // Clase (de sensor) del evento
    var clase_sensor = $('#clase_sensor_evento').val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Origen y tipo del evento
    var id_origen_evento = $('#id_origen_evento').val();
    if (id_origen_evento == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._('Hay que seleccionar un origen de evento'));
        return;
    }
    var tipo = $('#tipo_evento').val();
    if (tipo == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._('Hay que seleccionar un tipo de evento'));
        return;
    }

    // Se recupera el número de valores de la clase del sensor
    var numero_valores_clase_sensor = null;
    var granularidad = $('#granularidad_evento').val();
    switch (granularidad) {
        case GRANULARIDAD_TIEMPO_REAL: {
            numero_valores_clase_sensor = dame_numero_valores_clase_sensor(clase_sensor);
            break;
        }
        case GRANULARIDAD_CUARTOHORARIA:
        case GRANULARIDAD_HORARIA: {
            numero_valores_clase_sensor = dame_numero_valores_clase_clase_sensor(clase_sensor);
            break;
        }
    }

    // Parámetros del evento
    var tipo = $('#tipo_evento').val();
    var resultado_parametros_evento = dame_cadena_parametros_evento(tipo, numero_valores_clase_sensor);
    if (resultado_parametros_evento.parametros_correctos == false) {
        if (resultado_parametros_evento.descripcion_error != "") {
            jAlert(resultado_parametros_evento.descripcion_error);
        }
        return;
    }
    var parametros = resultado_parametros_evento.cadena_parametros_evento;

    // Se añade o modifica el evento
    if (anyadir_evento == true) {
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/anyade_evento.php", {
            nombre: nombre,
            descripcion: $('#descripcion_evento').val(),
            clase_sensor: clase_sensor,
            origen: $('#origen_evento').val(),
            id_origen: $('#id_origen_evento').val(),
            granularidad: $('#granularidad_evento').val(),
            tipo: tipo,
            parametros: parametros,
            alarma: $('#alarma_evento').val(),
            id_evento_anterior: id_evento
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);

            /*var kk1 = "&#039;Actuadores&#039;";
            jInfo(kk1);*/
            actualiza_tabla_eventos();
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/modifica_evento.php", {
            id_evento: id_evento,
            nombre: nombre,
            descripcion: $('#descripcion_evento').val(),
            clase_sensor: clase_sensor,
            origen: $('#origen_evento').val(),
            id_origen: $('#id_origen_evento').val(),
            granularidad: $('#granularidad_evento').val(),
            tipo: tipo,
            parametros: parametros,
            alarma: $('#alarma_evento').val()
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_eventos();
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_sensores_actualizar_tabla_eventos() {
    actualiza_tabla_eventos();
}


function actualiza_tabla_eventos() {
	var filtro = $('#filtro_sensores_filtro_eventos_tabla').val();
    var clase_sensor = $('#clase_sensor_sensores_filtro_eventos_tabla').val();
    var alarma = $('#alarma_evento_sensores_filtro_eventos_tabla').val();
    var activacion = $('#activacion_evento_sensores_filtro_eventos_tabla').val();

    $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_tabla_eventos.php", {
	    filtro: filtro,
        clase_sensor: clase_sensor,
        alarma: alarma,
        activacion: activacion,
        actualizacion_periodica_activada: actualizacion_periodica_eventos_activada
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaEventos').html(resultado.html);

        // Se actualiza la fecha de actualización de la tabla de eventos
        actualiza_fecha_actualizacion_tabla_eventos();

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Actualiza en el pie de la página la fecha de actualización de la tabla de eventos
function actualiza_fecha_actualizacion_tabla_eventos() {
    var fecha_actual = new Date();
    var cadena_fecha_actual = convierte_fecha_a_cadena(fecha_actual, formato_fecha_local_jquery_ui);
    cadena_fecha_actual += ", " + dame_cadena_hora(fecha_actual);
    var texto_actualizado_hora_actual = TLNT.Idiomas._("hora de actualización de tabla") + ": " + cadena_fecha_actual;
    actualiza_texto_pie_pagina(texto_actualizado_hora_actual);
}


// Actualización periódica de la tabla de eventos
function boton_sensores_actualizacion_periodica_tabla_eventos() {
    inicia_actualizacion_periodica_tabla_eventos();
}


// Inicia la actualización periódica de la tabla de eventos
function inicia_actualizacion_periodica_tabla_eventos() {
    // Se activa o desactiva la actualización periódica de la tabla de eventos
    if (temporizador_actualizacion_pagina == null) {
        jPrompt(TLNT.Idiomas._("Intervalo de actualización periódica de eventos") + " (" + TLNT.Idiomas._("segundos") + ")",
            SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_EVENTOS_DEFECTO,
            TLNT.Idiomas._("Pregunta"),
            function(valor) {
                if (valor != null) {
                    if ((isNaN(valor) == true) || (
                        (valor < MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_EVENTOS_DEFECTO) ||
                        (valor > MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_EVENTOS_DEFECTO))) {
                        var mensaje_aviso = TLNT.Idiomas._("Intervalo de actualización periódica de eventos no válido") +
                            " (" + MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_EVENTOS_DEFECTO + " - " + MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_EVENTOS_DEFECTO + ")";
                        jAlert(mensaje_aviso, TLNT.Idiomas._("Aviso"), function(res) {
                            inicia_actualizacion_periodica_tabla_eventos();
                        });
                    }
                    else {
                        actualizacion_periodica_eventos_activada = true;
                        segundos_intervalo_actualizacion_tabla_eventos = valor;
                        temporizador_actualizacion_pagina = setTimeout(
                            expiracion_timeout_actualizacion_periodica_tabla_eventos,
                            segundos_intervalo_actualizacion_tabla_eventos * 1000);
                        jInfo(TLNT.Idiomas._("Actualización periódica de eventos activada"));

                        // Actualizar el icono de actualización periódica
                        $('#boton_actualizacion_periodica_tabla_eventos').removeClass("icon-play");
                        $('#boton_actualizacion_periodica_tabla_eventos').addClass("icon-pause");

                        // Se actualiza la tabla de eventos
                        actualiza_tabla_eventos();
                    }
                }
            }
        );
    }
    else {
        desactiva_actualizacion_periodica_tabla_eventos();
    }
}


// Expiración del timeout para actualización periódica de la tabla de eventos
function expiracion_timeout_actualizacion_periodica_tabla_eventos() {
    actualiza_tabla_eventos();
    temporizador_actualizacion_pagina = setTimeout(
        expiracion_timeout_actualizacion_periodica_tabla_eventos,
        segundos_intervalo_actualizacion_tabla_eventos * 1000);
}


// Desactiva la actualización periódica de la tabla de eventos
function desactiva_actualizacion_periodica_tabla_eventos() {
    if (temporizador_actualizacion_pagina != null) {
        actualizacion_periodica_reglas_activada = false;
        clearTimeout(temporizador_actualizacion_pagina);
        temporizador_actualizacion_pagina = null;

        jInfo(TLNT.Idiomas._("Actualización periódica de eventos desactivada"));

        $('.boton_actuadores_actualizacion_periodica_tabla_eventos').removeClass("icon-pause");
        $('.boton_actuadores_actualizacion_periodica_tabla_eventos').addClass("icon-play");
    }
}


function boton_sensores_refrescar_tabla_evento() {
	var params = this.id.split('__');
	var id_evento = params[1];

    actualiza_tabla_evento_detalles(id_evento);
}


function actualiza_tabla_evento_detalles(id_evento) {
    // Se actualiza la información de la fila de la tabla
	$.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_informacion_fila_tabla_evento.php", {
        id_evento: id_evento
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_datos = "datosEvento__" + id_evento;
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


//
// Informes de eventos
//


// Muestra la información de activaciones de eventos
function boton_sensores_activaciones_eventos_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_activaciones_eventos(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var clase_sensor = parametros_informe["clase_sensor"];
    var origen_evento = parametros_informe["origen_evento"];
    var id_origen_evento = parametros_informe["id_origen_evento"];
    var nombre_origen_evento = parametros_informe["nombre_origen_evento"];
    var granularidad_evento = parametros_informe["granularidad_evento"];
    var ids_eventos = parametros_informe["ids_eventos"];
    var nombres_eventos = parametros_informe["nombres_eventos"];
    var campo = parametros_informe["campo"];
    var nombre_campo = parametros_informe["nombre_campo"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
	$.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_activaciones_eventos.php", {
        clase_sensor: clase_sensor,
        origen_evento: origen_evento,
        id_origen_evento: id_origen_evento,
        nombre_origen_evento: nombre_origen_evento,
        granularidad_evento: granularidad_evento,
        ids_eventos: ids_eventos,
        nombres_eventos: nombres_eventos,
		fecha_hora_inicio: fecha_hora_inicio,
		fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        campo: campo,
        nombre_campo: nombre_campo
	},
    function (data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Nota: No se comprueba si hay datos porque siempre se muestran al menos las gráficas de activaciones de eventos
        // (aunque no haya activaciones ...)

        // Se muestra el informe
        $("#informe-sin-datos-sensores-activaciones-eventos").hide();
        $("#informe-sensores-activaciones-eventos").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-valores-sensor-activaciones-eventos",
            "grafica-valores-acumulados-sensor-activaciones-eventos"]);
        for (var i = 1; i <= NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS; i++) {
            vacia_elemento("grafica-activaciones-evento-activaciones-eventos-" + i);
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            clase_sensor: clase_sensor,
            origen_evento: origen_evento,
            campo: campo,
            id_grafica_valores_sensor: "grafica-valores-sensor-activaciones-eventos",
            id_grafica_valores_acumulados_sensor: "grafica-valores-acumulados-sensor-activaciones-eventos",
            id_graficas_activaciones_eventos: "grafica-activaciones-evento-activaciones-eventos",
            id_contenedores_tablas_activaciones_eventos: "contenedor-tabla-activaciones-evento-activaciones-eventos"};
        dibuja_informe_sensores_activaciones_eventos(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de activaciones de eventos
function dame_parametros_informe_sensores_activaciones_eventos(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Filtro de eventos
    var clase_sensor = $('#clase_sensor_sensores_activaciones_eventos').val();
    var origen_evento = $('#origen_evento_sensores_activaciones_eventos').val();
    var id_origen_evento = $('#id_origen_evento_sensores_activaciones_eventos').val();
    var nombre_origen_evento = $('#id_origen_evento_sensores_activaciones_eventos :selected').text();
    var granularidad_evento = $('#granularidad_evento_sensores_activaciones_eventos').val();

    // Se comprueba si hay clase de sensor seleccionada
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
        return;
    }

    // Se recuperan los identificadores de los eventos seleccionados
    var ids_eventos = [];
    var nombres_eventos = [];
    $("#ids_eventos_sensores_activaciones_eventos option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_eventos.push($(this).val());
            nombres_eventos.push($(this).text());
        }
    });
    if (ids_eventos.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un evento"));
		return (null);
	}

    // Campo de sensor
    var campo = $('#campo_sensores_activaciones_eventos').val();
    var nombre_campo = $('#campo_sensores_activaciones_eventos :selected').text();

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["origen_evento"] = origen_evento;
    parametros_informe["id_origen_evento"] = id_origen_evento;
    parametros_informe["nombre_origen_evento"] = nombre_origen_evento;
    parametros_informe["granularidad_evento"] = granularidad_evento;
    parametros_informe["ids_eventos"] = ids_eventos;
    parametros_informe["nombres_eventos"] = nombres_eventos;
    parametros_informe["campo"] = campo;
    parametros_informe["nombre_campo"] = nombre_campo;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_activaciones_eventos').val();
        var hora_inicio = $('#hora_inicio_sensores_activaciones_eventos').val();
        var fecha_fin = $('#fecha_fin_sensores_activaciones_eventos').val();
        var hora_fin = $('#hora_fin_sensores_activaciones_eventos').val();
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
