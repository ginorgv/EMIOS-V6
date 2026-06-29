/*
 * Funciones de administración de nodos
 *
 */


function boton_actualizar_tabla_nodos() {
    var params = this.id.split('__');
	var tipo_nodo = params[1];

    actualiza_tablas_nodos(tipo_nodo);
}


function actualiza_tablas_nodos(tipo_nodo) {
    switch (tipo_nodo) {
        case TIPO_NODO_DISPOSITIVO:
        case TIPO_NODO_AXON: {
            actualiza_informacion_red();
            actualiza_tabla_nodos(TIPO_NODO_DISPOSITIVO);
            actualiza_tabla_nodos(TIPO_NODO_AXON);
            break;
        }
        case TIPO_NODO_SENSOR:
        case TIPO_NODO_GRUPO_SENSORES: {
            actualiza_tabla_nodos(TIPO_NODO_SENSOR);
            actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);
            break;
        }
        case TIPO_NODO_ACTUADOR:
        case TIPO_NODO_GRUPO_ACTUADORES: {
            actualiza_tabla_nodos(TIPO_NODO_ACTUADOR);
            actualiza_tabla_nodos(TIPO_NODO_GRUPO_ACTUADORES);
            break;
        }
        case TIPO_NODO_RED: {
            actualiza_tabla_clientes();
            actualiza_tabla_nodos(TIPO_NODO_RED);
            break;
        }
        default: {
            actualiza_tabla_nodos(tipo_nodo);
            break;
        }
    }
}


function actualiza_tabla_nodos(tipo_nodo) {
	var modulo = $('#modulo').attr("name");
    var filtro = "";
    var parametros_tipo_nodo = "";
    switch (tipo_nodo) {
        case TIPO_NODO_RED: {
            filtro = $('#filtro_administracion_filtro_redes_tabla').val();
            break;
        }
        case TIPO_NODO_SENSOR: {
            filtro = $('#filtro_sensores_filtro_sensores_tabla').val();
            var tipo = $('#tipo_sensor_sensores_filtro_sensores_tabla').val();
            var clase = $('#clase_sensor_sensores_filtro_sensores_tabla').val();
            var id_grupo = $('#id_grupo_sensores_sensores_filtro_sensores_tabla').val();
            var estado = $('#estado_sensor_sensores_filtro_sensores_tabla').val();

            // Se añade el ratio a los parámetros del filtro de sensores
            var id_ratio = dame_id_ratio_seleccionado();
            parametros_tipo_nodo = {
                tipo: tipo,
                clase: clase,
                id_grupo: id_grupo,
                estado: estado,
                id_ratio: id_ratio
            };
            break;
        }
        case TIPO_NODO_GRUPO_SENSORES: {
            filtro = $('#filtro_sensores_filtro_grupos_tabla').val();
            var clase = $('#clase_sensor_sensores_filtro_grupos_tabla').val();
            parametros_tipo_nodo = {
                clase: clase
            };
            break;
        }
        case TIPO_NODO_ACTUADOR: {
            filtro = $('#filtro_actuadores_filtro_actuadores_tabla').val();
            var tipo = $('#tipo_actuador_actuadores_filtro_actuadores_tabla').val();
            var clase = $('#clase_actuador_actuadores_filtro_actuadores_tabla').val();
            var id_grupo = $('#id_grupo_actuadores_actuadores_filtro_actuadores_tabla').val();
            var estado = $('#estado_actuador_actuadores_filtro_actuadores_tabla').val();
            parametros_tipo_nodo = {
                tipo: tipo,
                clase: clase,
                id_grupo: id_grupo,
                estado: estado
            };
            break;
        }
        case TIPO_NODO_GRUPO_ACTUADORES: {
            filtro = $('#filtro_actuadores_filtro_grupos_tabla').val();
            var clase = $('#clase_actuador_actuadores_filtro_grupos_tabla').val();
            parametros_tipo_nodo = {
                clase: clase
            };
            break;
        }
    }

    $.post("./src/lib/modulos/Nodos/administracion/dame_tabla_nodos.php", {
		tipo_nodo: tipo_nodo,
        modulo: modulo,
        filtro: filtro,
        parametros_tipo_nodo: parametros_tipo_nodo,
        tipo_nodo_actualizacion_periodica: tipo_nodo_actualizacion_periodica
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		$('#' + resultado.id_tabla).html(resultado.html);

        // Se actualiza la fecha de actualización de la tabla de nodos
        actualiza_fecha_actualizacion_tabla_nodos();

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Actualiza en el pie de la página la fecha de actualización de la tabla de nodos
function actualiza_fecha_actualizacion_tabla_nodos() {
    var fecha_actual = new Date();
    var cadena_fecha_actual = convierte_fecha_a_cadena(fecha_actual, formato_fecha_local_jquery_ui);
    cadena_fecha_actual += ", " + dame_cadena_hora(fecha_actual);
    var texto_actualizado_hora_actual = TLNT.Idiomas._("hora de actualización de tabla") + ": " + cadena_fecha_actual;
    actualiza_texto_pie_pagina(texto_actualizado_hora_actual);
}


// Actualización periódica de la tabla de nodos
function boton_actualizacion_periodica_tabla_nodos() {
    var params = this.id.split('__');
    var tipo_nodo = params[1];

    inicia_actualizacion_periodica_tabla_nodos(tipo_nodo);
}


// Inicia la actualización periódica de la tabla de nodos
function inicia_actualizacion_periodica_tabla_nodos(tipo_nodo) {
    // Se activa o desactiva la actualización periódica de la tabla de nodos
    if (temporizador_actualizacion_pagina == null) {
        jPrompt(TLNT.Idiomas._("Intervalo de actualización periódica de tabla") + " (" + TLNT.Idiomas._("segundos") + ")",
            SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_NODOS_DEFECTO,
            TLNT.Idiomas._("Pregunta"),
            function(valor) {
                if (valor != null) {
                    if ((isNaN(valor) == true) || (
                        (valor < MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_NODOS_DEFECTO) ||
                        (valor > MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_NODOS_DEFECTO))) {
                        var mensaje_aviso = TLNT.Idiomas._("Intervalo de actualización periódica de tabla no válido") +
                            " (" + MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_NODOS_DEFECTO + " - " + MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_NODOS_DEFECTO + ")";
                        jAlert(mensaje_aviso, TLNT.Idiomas._("Aviso"), function(res) {
                            inicia_actualizacion_periodica_tabla_nodos(tipo_nodo);
                        });
                    }
                    else {
                        tipo_nodo_actualizacion_periodica = tipo_nodo;
                        segundos_intervalo_actualizacion_tabla_nodos = valor;
                        temporizador_actualizacion_pagina = setTimeout(
                            expiracion_timeout_actualizacion_periodica_tabla_nodos_tipo_nodo,
                            segundos_intervalo_actualizacion_tabla_nodos * 1000,
                            tipo_nodo);
                        jInfo(TLNT.Idiomas._("Actualización periódica de tabla activada"));

                        // Actualizar el icono de actualización periódica
                        $('#boton_actualizacion_periodica_tabla__' + tipo_nodo).removeClass("icon-play");
                        $('#boton_actualizacion_periodica_tabla__' + tipo_nodo).addClass("icon-pause");

                        // Se actualizan las tablas de nodos
                        actualiza_tablas_nodos(tipo_nodo);
                    }
                }
            }
        );
    }
    else {
        desactiva_actualizacion_periodica_tabla_nodos();
    }
}


// Expiración del timeout para actualización periódica de la tabla de nodos
function expiracion_timeout_actualizacion_periodica_tabla_nodos_tipo_nodo(tipo_nodo) {
    actualiza_tablas_nodos(tipo_nodo);
    temporizador_actualizacion_pagina = setTimeout(
        expiracion_timeout_actualizacion_periodica_tabla_nodos_tipo_nodo,
        segundos_intervalo_actualizacion_tabla_nodos * 1000,
        tipo_nodo);
}


// Desactiva la actualización periódica de la tabla de nodos
function desactiva_actualizacion_periodica_tabla_nodos() {
    if (temporizador_actualizacion_pagina != null) {
        tipo_nodo_actualizacion_periodica = null;
        clearTimeout(temporizador_actualizacion_pagina);
        temporizador_actualizacion_pagina = null;

        jInfo(TLNT.Idiomas._("Actualización periódica de tabla desactivada"));

        $('.boton_actualizacion_periodica_tabla_nodos').removeClass("icon-pause");
        $('.boton_actualizacion_periodica_tabla_nodos').addClass("icon-play");
    }
}


function boton_refrescar_tabla_nodo() {
	var params = this.id.split('__');
	var tipo_nodo = params[1];
	var id_nodo = params[2];

    refresca_tabla_nodo(tipo_nodo, id_nodo);
}


function refresca_tabla_nodo(tipo_nodo, id_nodo) {
    // https://stackoverflow.com/questions/2298730/find-html-element-which-id-starts-with
    var prefijo_id_datos = "datosNodo" + tipo_nodo + "__" + id_nodo + "__";
    var id_datos = $('[id^="' + prefijo_id_datos + '"]').attr('id');

    // Nota: Si no se encuentra el identificador de datos es que el nodo no se muestra en la tabla
    // (entonces no hay nada que 'refrescar' y se sale de la función)
    if (id_datos === undefined) {
        return;
    }

    var params = id_datos.split('__');
    var valor_nodo_administrable = params[2];
    var valor_permitir_adicion_nodos = params[3];

    // Se actualiza la información de la fila de la tabla
	$.post("./src/lib/modulos/Nodos/administracion/dame_informacion_fila_tabla_nodo.php", {
        tipo_nodo: tipo_nodo,
        id_nodo: id_nodo,
        valor_nodo_administrable: valor_nodo_administrable,
        valor_permitir_adicion_nodos: valor_permitir_adicion_nodos
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

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


function actualiza_lista_grupos_nodos_filtro(tipo_nodo) {
	switch (tipo_nodo) {
        case TIPO_NODO_SENSOR: {
            var id_lista = "id_grupo_sensores_sensores_filtro_sensores_tabla";
            $.post("./src/lib/modulos/Nodos/administracion/dame_lista_grupos_sensores.php", {
                clase_sensor: CLASE_TODAS,
                id_grupo_seleccionado: $("#" + id_lista).val(),
                opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO
            },
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                $("#" + id_lista).html(resultado.html);
            });
            break;
		}
        case TIPO_NODO_ACTUADOR: {
            var id_lista = "id_grupo_actuadores_actuadores_filtro_actuadores_tabla";
            $.post("./src/lib/modulos/Nodos/administracion/dame_lista_grupos_actuadores.php", {
                clase_actuador: CLASE_TODAS,
                id_grupo_seleccionado: $("#" + id_lista).val(),
                opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO
            },
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                $("#" + id_lista).html(resultado.html);
            });
            break;
		}
    }
}


function boton_mostrar_ventana_anyadir_modificar_nodo(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var tipo_nodo = params[1];
    var id_nodo = params[2];
    var tipo_operacion_administracion = params[3];

    $.post("./src/lib/modulos/Nodos/administracion/muestra_ventana_anyadir_modificar_nodo.php", {
		tipo_nodo: tipo_nodo,
		id_nodo: id_nodo,
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


function boton_mostrar_ventana_modificar_red_parcial(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_red = params[1];

    $.post("./src/lib/modulos/Nodos/administracion/muestra_ventana_modificar_red_parcial.php", {
		id_red: id_red
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
        // (los eventos de la ventana de red 'reducida' son los mismos que los eventos de la ventana 'estándar')
        // (es otro módulo, por lo que se llaman a los eventos de las ventana modales de los módulos especificados)
        TLNT.Navegacion.establece_eventos_ventanas_modales_administracion();
        TLNT.Navegacion.establece_eventos_ventanas_modales_modulos();
	});
}


function boton_eliminar_nodo(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var tipo_nodo = params[1];
	var id_nodo = params[2];
	var nombre_nodo = $(this).attr('nombre_nodo');

    switch (tipo_nodo) {
        case TIPO_NODO_RED: {
			elimina_red(id_nodo, nombre_nodo);
			break;
        }
        case TIPO_NODO_DISPOSITIVO: {
			elimina_dispositivo(id_nodo, nombre_nodo);
			break;
        }
        case TIPO_NODO_AXON: {
			elimina_axon(id_nodo, nombre_nodo);
			break;
        }
        case TIPO_NODO_SENSOR: {
			elimina_sensor(id_nodo, nombre_nodo);
			break;
        }
        case TIPO_NODO_GRUPO_SENSORES: {
			elimina_grupo_sensores(id_nodo, nombre_nodo);
			break;
        }
        case TIPO_NODO_ACTUADOR: {
			elimina_actuador(id_nodo, nombre_nodo);
			break;
        }
        case TIPO_NODO_GRUPO_ACTUADORES: {
			elimina_grupo_actuadores(id_nodo, nombre_nodo);
			break;
        }
	}
}


function elimina_red(id_nodo, nombre_nodo) {
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la red?") + "\n(" + escapeHtml(nombre_nodo) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
            // Se crean los datos del formulario
            var datos_formulario = new FormData();
            datos_formulario.append("id_red", id_nodo);

            // Llamada 'ajax' POST
            $.ajax({
                url: "./src/lib/modulos/Nodos/administracion/elimina_red.php",
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
                    actualiza_tabla_clientes();
                    actualiza_tabla_nodos(TIPO_NODO_RED);
                },
                error: function(request, status, err) {
                    if (status == "timeout") {
                        error_ajax_capturado = true;

                        jInfo(TLNT.Idiomas._("La eliminación de la red se está realizado en segundo plano"));
                        actualiza_tabla_clientes();
                        actualiza_tabla_nodos(TIPO_NODO_RED);
                    }
                }
            });
		}
	});
}


function elimina_dispositivo(id_nodo, nombre_nodo) {
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el dispositivo?") + "\n(" + escapeHtml(nombre_nodo) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/Nodos/administracion/elimina_dispositivo.php", {
				id_dispositivo: id_nodo
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
                actualiza_informacion_red();
				actualiza_tabla_nodos(TIPO_NODO_DISPOSITIVO);
			});
		}
	});
}


function elimina_axon(id_nodo, nombre_nodo) {
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el axón?") + "\n(" + escapeHtml(nombre_nodo) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/Nodos/administracion/elimina_axon.php", {
				id_axon: id_nodo
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_tabla_nodos(TIPO_NODO_DISPOSITIVO);
                actualiza_tabla_nodos(TIPO_NODO_AXON);
			});
		}
	});
}


function elimina_sensor(id_nodo, nombre_nodo) {
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el sensor?") + "\n(" + escapeHtml(nombre_nodo) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            // Se crean los datos del formulario
            var datos_formulario = new FormData();
            datos_formulario.append("id_sensor", id_nodo);

            // Llamada 'ajax' POST
            $.ajax({
                url: "./src/lib/modulos/Nodos/administracion/elimina_sensor.php",
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
                    actualiza_tabla_nodos(TIPO_NODO_SENSOR);
                    actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);
                },
                error: function(request, status, err) {
                    if (status == "timeout") {
                        error_ajax_capturado = true;

                        jInfo(TLNT.Idiomas._("La eliminación del sensor se está realizado en segundo plano"));
                        actualiza_tabla_nodos(TIPO_NODO_SENSOR);
                        actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);
                    }
                }
            });
        }
	});
}


function elimina_grupo_sensores(id_nodo, nombre_nodo) {
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el grupo?") + "\n(" + escapeHtml(nombre_nodo) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/Nodos/administracion/elimina_grupo_sensores.php", {
                id_grupo_sensores: id_nodo
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_lista_grupos_nodos_filtro(TIPO_NODO_SENSOR);
                actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);
			});
		}
	});
}


function elimina_actuador(id_nodo, nombre_nodo) {
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el actuador?") + "\n(" + escapeHtml(nombre_nodo) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/Nodos/administracion/elimina_actuador.php", {
                id_actuador: id_nodo
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
                actualiza_tabla_nodos(TIPO_NODO_ACTUADOR);
                actualiza_tabla_nodos(TIPO_NODO_GRUPO_ACTUADORES);
			});
		}
	});
}


function elimina_grupo_actuadores(id_nodo, nombre_nodo) {
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el grupo?") + "\n(" + escapeHtml(nombre_nodo) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/Nodos/administracion/elimina_grupo_actuadores.php", {
                id_grupo_actuadores: id_nodo
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
                actualiza_lista_grupos_nodos_filtro(TIPO_NODO_ACTUADOR);
                actualiza_tabla_nodos(TIPO_NODO_GRUPO_ACTUADORES);
			});
		}
	});
}


function boton_anyadir_modificar_nodo() {
    // Tipo de nodo
    var tipo_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("tipo_nodo");
    switch (tipo_nodo) {
        // Nota: La validación de los controles se realiza en la función 'anyade_modifica_sensor'
        // (sólo se validan las pestañas visibles)
        case TIPO_NODO_SENSOR:
        {
            break;
        }
        default:
        {
            if (TLNT.Check.inputs('contenido_modal')) {
                jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
                return;
            }
        }
    }

    // Parámetros de la ventana
	var anyadir_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("anyadir_nodo");
	var id_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("id_nodo");

    // Tipo de nodo
	switch (tipo_nodo) {
        case TIPO_NODO_RED: {
			anyade_modifica_red(anyadir_nodo, id_nodo);
			break;
        }
        case TIPO_NODO_DISPOSITIVO: {
			anyade_modifica_dispositivo(anyadir_nodo, id_nodo);
			break;
        }
		case TIPO_NODO_AXON: {
			anyade_modifica_axon(anyadir_nodo, id_nodo);
			break;
        }
        case TIPO_NODO_SENSOR: {
			anyade_modifica_sensor(anyadir_nodo, id_nodo);
			break;
        }
        case TIPO_NODO_GRUPO_SENSORES: {
			anyade_modifica_grupo_sensores(anyadir_nodo, id_nodo);
			break;
        }
        case TIPO_NODO_ACTUADOR: {
			anyade_modifica_actuador(anyadir_nodo, id_nodo);
			break;
        }
        case TIPO_NODO_GRUPO_ACTUADORES: {
			anyade_modifica_grupo_actuadores(anyadir_nodo, id_nodo);
			break;
        }
	}
}


function anyade_modifica_red(anyadir_nodo, id_nodo) {
    // Id y nombre
    var id_red = $('#id_red').val();
    var nombre = $('#nombre_red').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_red').addClass('data-check-failed');
        return;
    }

    // Comprobación de clientes y zonas horarias disponibles
    var id_cliente = $("#id_cliente_red").val();
    if (id_cliente == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay cliente seleccionado'));
        return;
    }
    var zona_horaria = $("#zona_horaria_red").val();
    if (zona_horaria == null) {
        jAlert(TLNT.Idiomas._('No hay zonas horarias disponibles'));
        return;
    }

    // Idioma
    var idioma = $('#idioma_red').val();

    // Parámetros locales
    var tipo_formato_fecha_local = $("#tipo_formato_fecha_local_red").val();
    var id_separador_miles = $("#id_separador_miles_red").val();
    var id_punto_decimal = $("#id_punto_decimal_red").val();
    if (id_separador_miles == id_punto_decimal) {
        jAlert(TLNT.Idiomas._("El separador de miles y el punto decimal deben ser diferentes"));
        return;
    }
    var separador_miles = null;
    switch (id_separador_miles) {
        case ID_SEPARADOR_MILES_COMA: {
            separador_miles = ",";
            break;
        }
        case ID_SEPARADOR_MILES_PUNTO: {
            separador_miles = ".";
            break;
        }
        case ID_SEPARADOR_MILES_ESPACIO: {
            separador_miles = " ";
            break;
        }
    }
    var punto_decimal = null;
    switch (id_punto_decimal) {
        case ID_PUNTO_DECIMAL_PUNTO: {
            punto_decimal = ".";
            break;
        }
        case ID_PUNTO_DECIMAL_COMA: {
            punto_decimal = ",";
            break;
        }
    }
    var moneda = $("#moneda_red").val();
    var unidad_medida_temperatura = $("#unidad_medida_temperatura_red").val();
    var unidad_medida_velocidad = $("#unidad_medida_velocidad_red").val();
    var unidades_medida = [
        moneda,
        unidad_medida_temperatura,
        unidad_medida_velocidad].join(SEPARADOR_PARAMETROS_SIMPLES);
    var pais_tarifas_electricas = $("#pais_tarifas_electricas_red").val();
    var pais_tarifas_gas = $("#pais_tarifas_gas_red").val();
    var pais_tarifas_agua = $("#pais_tarifas_agua_red").val();
    var paises_tarifas = [
        pais_tarifas_electricas,
        pais_tarifas_gas,
        pais_tarifas_agua].join(SEPARADOR_PARAMETROS_SIMPLES);
    var medicion_defecto = $("#medicion_defecto_red").val();

    // Comprobación de medición por defecto con país de tarifas seleccionado
    var pais_tarifas_medicion_defecto_seleccionado = null;
    switch (medicion_defecto) {
        case MEDICION_NINGUNA: {
            if ((pais_tarifas_electricas != PAIS_NINGUNO) ||
                (pais_tarifas_gas != PAIS_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay medición por defecto seleccionada"));
                return;
            }
            break;
        }
        case MEDICION_ELECTRICIDAD: {
            pais_tarifas_medicion_defecto_seleccionado = pais_tarifas_electricas;
            break;
        }
        case MEDICION_GAS: {
            pais_tarifas_medicion_defecto_seleccionado = pais_tarifas_gas;
            break;
        }
        case MEDICION_AGUA: {
            pais_tarifas_medicion_defecto_seleccionado = pais_tarifas_agua;
            break;
        }
    }
    if (pais_tarifas_medicion_defecto_seleccionado == PAIS_NINGUNO) {
        jAlert(TLNT.Idiomas._("La medición por defecto debe tener tarifas seleccionadas"));
        return;
    }

    // Procesado cuartohorario
    var procesado_cuartohorario = $("#procesado_cuartohorario_red").val();

    // Parámetros de caducidad
    var numero_meses_valores_tiempo_real = parseInt($("#numero_meses_valores_tiempo_real_red").val());
    if ((numero_meses_valores_tiempo_real < NUMERO_MINIMO_MESES_VALORES_TIEMPO_REAL_CADUCIDAD_VALORES) ||
        (numero_meses_valores_tiempo_real > NUMERO_MAXIMO_MESES_VALORES_TIEMPO_REAL_CADUCIDAD_VALORES)) {
        jAlert(TLNT.Idiomas._("El número de meses de valores en tiempo real es incorrecto") + " (" + TLNT.Idiomas._("rango de meses") + ": " +
            NUMERO_MINIMO_MESES_VALORES_TIEMPO_REAL_CADUCIDAD_VALORES + " - " + NUMERO_MAXIMO_MESES_VALORES_TIEMPO_REAL_CADUCIDAD_VALORES + ")");
        return;
    }
    var numero_meses_valores_cuartoshora = parseInt($("#numero_meses_valores_cuartoshora_red").val());
    if ((numero_meses_valores_cuartoshora < NUMERO_MINIMO_MESES_VALORES_CUARTOSHORA_CADUCIDAD_VALORES) ||
        (numero_meses_valores_cuartoshora > NUMERO_MAXIMO_MESES_VALORES_CUARTOSHORA_CADUCIDAD_VALORES)) {
        jAlert(TLNT.Idiomas._("El número de meses de valores cuartohorarios es incorrecto") + " (" + TLNT.Idiomas._("rango de meses") + ": " +
            NUMERO_MINIMO_MESES_VALORES_CUARTOSHORA_CADUCIDAD_VALORES + " - " + NUMERO_MAXIMO_MESES_VALORES_CUARTOSHORA_CADUCIDAD_VALORES + ")");
        return;
    }
    if (numero_meses_valores_cuartoshora < numero_meses_valores_tiempo_real) {
        jAlert(TLNT.Idiomas._("El número de meses de valores cuartohorarios debe ser mayor o igual que el número de meses de valores en tiempo real"));
        return;
    }
    var numero_meses_valores_horas = parseInt($("#numero_meses_valores_horas_red").val());
    if ((numero_meses_valores_horas < NUMERO_MINIMO_MESES_VALORES_HORAS_CADUCIDAD_VALORES) ||
        (numero_meses_valores_horas > NUMERO_MAXIMO_MESES_VALORES_HORAS_CADUCIDAD_VALORES)) {
        jAlert(TLNT.Idiomas._("El número de meses de valores horarios es incorrecto") + " (" + TLNT.Idiomas._("rango de meses") + ": " +
            NUMERO_MINIMO_MESES_VALORES_HORAS_CADUCIDAD_VALORES + " - " + NUMERO_MAXIMO_MESES_VALORES_HORAS_CADUCIDAD_VALORES + ")");
        return;
    }
    if (numero_meses_valores_horas < numero_meses_valores_cuartoshora) {
        jAlert(TLNT.Idiomas._("El número de meses de valores horarios debe ser mayor o igual que el número de meses de valores cuartohorarios"));
        return;
    }
    var numero_meses_valores_dias = parseInt($("#numero_meses_valores_dias_red").val());
    if ((numero_meses_valores_dias < NUMERO_MINIMO_MESES_VALORES_DIAS_CADUCIDAD_VALORES) ||
        (numero_meses_valores_dias > NUMERO_MAXIMO_MESES_VALORES_DIAS_CADUCIDAD_VALORES)) {
        jAlert(TLNT.Idiomas._("El número de meses de valores diarios es incorrecto") + " (" + TLNT.Idiomas._("rango de meses") + ": " +
            NUMERO_MINIMO_MESES_VALORES_DIAS_CADUCIDAD_VALORES + " - " + NUMERO_MAXIMO_MESES_VALORES_DIAS_CADUCIDAD_VALORES + ")");
        return;
    }
    if (numero_meses_valores_dias < numero_meses_valores_horas) {
        jAlert(TLNT.Idiomas._("El número de meses de valores diarios debe ser mayor o igual que el número de meses de valores horarios"));
        return;
    }
    var numero_meses_valores_meses = parseInt($("#numero_meses_valores_meses_red").val());
    if ((numero_meses_valores_meses < NUMERO_MINIMO_MESES_VALORES_MESES_CADUCIDAD_VALORES) ||
        (numero_meses_valores_meses > NUMERO_MAXIMO_MESES_VALORES_MESES_CADUCIDAD_VALORES)) {
        jAlert(TLNT.Idiomas._("El número de meses de valores mensuales es incorrecto") + " (" + TLNT.Idiomas._('rango de meses') + ": " +
            NUMERO_MINIMO_MESES_VALORES_MESES_CADUCIDAD_VALORES + " - " + NUMERO_MAXIMO_MESES_VALORES_MESES_CADUCIDAD_VALORES + ")");
        return;
    }
    if (numero_meses_valores_meses < numero_meses_valores_dias) {
        jAlert(TLNT.Idiomas._("El número de meses de valores mensuales debe ser mayor o igual que el número de meses de valores diarios"));
        return;
    }
    var enviar_valores_caducados_tiempo_real = $("#enviar_valores_caducados_tiempo_real_red").val();
    var enviar_valores_caducados_cuartoshora = $("#enviar_valores_caducados_cuartoshora_red").val();
    var enviar_valores_caducados_horas = $("#enviar_valores_caducados_horas_red").val();
    var cadena_direcciones_email_envio_valores_caducados = $("#direccion_email_envio_valores_caducados_red").val();
    if (cadena_direcciones_email_envio_valores_caducados != "") {
        if (comprueba_longitud_cadena(cadena_direcciones_email_envio_valores_caducados, NUMERO_MAXIMO_CARACTERES_DIRECCIONES_EMAIL) == false) {
            $('#direccion_email_envio_valores_caducados_red').addClass('data-check-failed');
            return;
        }
        var direcciones_email_envio_valores_caducados = cadena_direcciones_email_envio_valores_caducados.split(SEPARADOR_DIRECCIONES_EMAIL);
        for (var i = 0; i < direcciones_email_envio_valores_caducados.length; i++) {
            direcciones_email_envio_valores_caducados[i] = direcciones_email_envio_valores_caducados[i].trim();
            if (PATRON_DIRECCION_EMAIL.test(direcciones_email_envio_valores_caducados[i]) == false) {
                jAlert(TLNT.Idiomas._("Las direcciones e-mail de destino deben ser correctas y separadas por punto y coma"));
                $('#direccion_email_envio_valores_caducados_red').addClass('data-check-failed');
                return;
            }
        }
        cadena_direcciones_email_envio_valores_caducados = direcciones_email_envio_valores_caducados.join(SEPARADOR_DIRECCIONES_EMAIL);
    }
    if (((enviar_valores_caducados_tiempo_real == VALOR_SI) ||
        (enviar_valores_caducados_cuartoshora == VALOR_SI) ||
        (enviar_valores_caducados_horas == VALOR_SI)) &&
        (cadena_direcciones_email_envio_valores_caducados == "")) {
        jAlert(TLNT.Idiomas._("No hay dirección de envío de valores caducados configurada"));
        return;
    }
    var numero_meses_acciones_usuario = parseInt($("#numero_meses_acciones_usuario_red").val());
    if ((numero_meses_acciones_usuario < NUMERO_MINIMO_MESES_CADUCIDAD_VALORES) ||
        (numero_meses_acciones_usuario > NUMERO_MAXIMO_MESES_CADUCIDAD_VALORES)) {
        jAlert(TLNT.Idiomas._("El número de meses de acciones de usuario es incorrecto") + " (" + TLNT.Idiomas._("rango de meses") + ": " +
            NUMERO_MINIMO_MESES_CADUCIDAD_VALORES + " - " + NUMERO_MAXIMO_MESES_CADUCIDAD_VALORES + ")");
        return;
    }
    var numero_meses_activaciones = parseInt($("#numero_meses_activaciones_red").val());
    if ((numero_meses_activaciones < NUMERO_MINIMO_MESES_CADUCIDAD_VALORES) ||
        (numero_meses_activaciones > NUMERO_MAXIMO_MESES_CADUCIDAD_VALORES)) {
        jAlert(TLNT.Idiomas._("El número de meses de activaciones es incorrecto") + " (" + TLNT.Idiomas._("rango de meses") + ": " +
            NUMERO_MINIMO_MESES_CADUCIDAD_VALORES + " - " + NUMERO_MAXIMO_MESES_CADUCIDAD_VALORES + ")");
        return;
    }
    var parametros_caducidad_valores = [
        numero_meses_valores_tiempo_real,
        numero_meses_valores_cuartoshora,
        numero_meses_valores_horas,
        numero_meses_valores_dias,
        numero_meses_valores_meses,
        enviar_valores_caducados_tiempo_real,
        enviar_valores_caducados_cuartoshora,
        enviar_valores_caducados_horas,
        cadena_direcciones_email_envio_valores_caducados,
        numero_meses_acciones_usuario,
        numero_meses_activaciones].join(SEPARADOR_PARAMETROS_SIMPLES);

    // Notificaciones
    var cadena_direcciones_email_envio_validaciones_automaticas_facturas = $("#direccion_email_envio_validaciones_automaticas_facturas_red").val();
    if (cadena_direcciones_email_envio_validaciones_automaticas_facturas != "") {
        if (comprueba_longitud_cadena(cadena_direcciones_email_envio_validaciones_automaticas_facturas, NUMERO_MAXIMO_CARACTERES_DIRECCIONES_EMAIL) == false) {
            $('#direccion_email_envio_validaciones_automaticas_facturas_red').addClass('data-check-failed');
            return;
        }
        var direcciones_email_envio_validaciones_automaticas_facturas = cadena_direcciones_email_envio_validaciones_automaticas_facturas.split(SEPARADOR_DIRECCIONES_EMAIL);
        for (var i = 0; i < direcciones_email_envio_validaciones_automaticas_facturas.length; i++) {
            direcciones_email_envio_validaciones_automaticas_facturas[i] = direcciones_email_envio_validaciones_automaticas_facturas[i].trim();
            if (PATRON_DIRECCION_EMAIL.test(direcciones_email_envio_validaciones_automaticas_facturas[i]) == false) {
                jAlert(TLNT.Idiomas._("Las direcciones e-mail de destino deben ser correctas y separadas por punto y coma"));
                $('#direccion_email_envio_validaciones_automaticas_facturas_red').addClass('data-check-failed');
                return;
            }
        }
        cadena_direcciones_email_envio_validaciones_automaticas_facturas = direcciones_email_envio_validaciones_automaticas_facturas.join(SEPARADOR_DIRECCIONES_EMAIL);
    }
    var cadena_direcciones_email_envio_avisos_expiraciones_tarifas = $("#direccion_email_envio_avisos_expiraciones_tarifas_red").val();
    if (cadena_direcciones_email_envio_avisos_expiraciones_tarifas != "") {
        if (comprueba_longitud_cadena(cadena_direcciones_email_envio_avisos_expiraciones_tarifas, NUMERO_MAXIMO_CARACTERES_DIRECCIONES_EMAIL) == false) {
            $('#direccion_email_envio_avisos_expiraciones_tarifas_red').addClass('data-check-failed');
            return;
        }
        var direcciones_email_envio_avisos_expiraciones_tarifas = cadena_direcciones_email_envio_avisos_expiraciones_tarifas.split(SEPARADOR_DIRECCIONES_EMAIL);
        for (var i = 0; i < direcciones_email_envio_avisos_expiraciones_tarifas.length; i++) {
            direcciones_email_envio_avisos_expiraciones_tarifas[i] = direcciones_email_envio_avisos_expiraciones_tarifas[i].trim();
            if (PATRON_DIRECCION_EMAIL.test(direcciones_email_envio_avisos_expiraciones_tarifas[i]) == false) {
                jAlert(TLNT.Idiomas._("Las direcciones e-mail de destino deben ser correctas y separadas por punto y coma"));
                $('#direccion_email_envio_avisos_expiraciones_tarifas_red').addClass('data-check-failed');
                return;
            }
        }
        cadena_direcciones_email_envio_avisos_expiraciones_tarifas = direcciones_email_envio_avisos_expiraciones_tarifas.join(SEPARADOR_DIRECCIONES_EMAIL);
    }
    var cadena_direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores = $("#direccion_email_envio_avisos_timeouts_envio_sensores_error_valores_red").val();
    if (cadena_direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores != "") {
        if (comprueba_longitud_cadena(cadena_direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores, NUMERO_MAXIMO_CARACTERES_DIRECCIONES_EMAIL) == false) {
            $('#direccion_email_envio_avisos_timeouts_envio_sensores_red').addClass('data-check-failed');
            return;
        }
        var direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores = cadena_direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores.split(SEPARADOR_DIRECCIONES_EMAIL);
        for (var i = 0; i < direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores.length; i++) {
            direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores[i] = direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores[i].trim();
            if (PATRON_DIRECCION_EMAIL.test(direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores[i]) == false) {
                jAlert(TLNT.Idiomas._("Las direcciones e-mail de destino deben ser correctas y separadas por punto y coma"));
                $('#direccion_email_envio_avisos_timeouts_envio_sensores_error_valores_red').addClass('data-check-failed');
                return;
            }
        }
        cadena_direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores = direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores.join(SEPARADOR_DIRECCIONES_EMAIL);
    }
    var cadena_direcciones_email_envio_avisos_eventos_activados = $("#direccion_email_envio_avisos_eventos_activados_red").val();
    if (cadena_direcciones_email_envio_avisos_eventos_activados != "") {
        if (comprueba_longitud_cadena(cadena_direcciones_email_envio_avisos_eventos_activados, NUMERO_MAXIMO_CARACTERES_DIRECCIONES_EMAIL) == false) {
            $('#direccion_email_envio_avisos_eventos_activados_red').addClass('data-check-failed');
            return;
        }
        var direcciones_email_envio_avisos_eventos_activados = cadena_direcciones_email_envio_avisos_eventos_activados.split(SEPARADOR_DIRECCIONES_EMAIL);
        for (var i = 0; i < direcciones_email_envio_avisos_eventos_activados.length; i++) {
            direcciones_email_envio_avisos_eventos_activados[i] = direcciones_email_envio_avisos_eventos_activados[i].trim();
            if (PATRON_DIRECCION_EMAIL.test(direcciones_email_envio_avisos_eventos_activados[i]) == false) {
                jAlert(TLNT.Idiomas._("Las direcciones e-mail de destino deben ser correctas y separadas por punto y coma"));
                $('#direccion_email_envio_avisos_eventos_activados_red').addClass('data-check-failed');
                return;
            }
        }
        cadena_direcciones_email_envio_avisos_eventos_activados = direcciones_email_envio_avisos_eventos_activados.join(SEPARADOR_DIRECCIONES_EMAIL);
    }
    var cadena_direcciones_email_envio_avisos_actuadores_error_reglas_activadas = $("#direccion_email_envio_avisos_actuadores_error_reglas_activadas_red").val();
    if (cadena_direcciones_email_envio_avisos_actuadores_error_reglas_activadas != "") {
        if (comprueba_longitud_cadena(cadena_direcciones_email_envio_avisos_actuadores_error_reglas_activadas, NUMERO_MAXIMO_CARACTERES_DIRECCIONES_EMAIL) == false) {
            $('#direccion_email_envio_avisos_actuadores_error_reglas_activadas_red').addClass('data-check-failed');
            return;
        }
        var direcciones_email_envio_avisos_actuadores_error_reglas_activadas = cadena_direcciones_email_envio_avisos_actuadores_error_reglas_activadas.split(SEPARADOR_DIRECCIONES_EMAIL);
        for (var i = 0; i < direcciones_email_envio_avisos_actuadores_error_reglas_activadas.length; i++) {
            direcciones_email_envio_avisos_actuadores_error_reglas_activadas[i] = direcciones_email_envio_avisos_actuadores_error_reglas_activadas[i].trim();
            if (PATRON_DIRECCION_EMAIL.test(direcciones_email_envio_avisos_actuadores_error_reglas_activadas[i]) == false) {
                jAlert(TLNT.Idiomas._("Las direcciones e-mail de destino deben ser correctas y separadas por punto y coma"));
                $('#direccion_email_envio_avisos_actuadores_error_reglas_activadas_red').addClass('data-check-failed');
                return;
            }
        }
        cadena_direcciones_email_envio_avisos_actuadores_error_reglas_activadas = direcciones_email_envio_avisos_actuadores_error_reglas_activadas.join(SEPARADOR_DIRECCIONES_EMAIL);
    }
    var direcciones_email_envio_notificaciones = [
        cadena_direcciones_email_envio_validaciones_automaticas_facturas,
        cadena_direcciones_email_envio_avisos_expiraciones_tarifas,
        cadena_direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores,
        cadena_direcciones_email_envio_avisos_eventos_activados,
        cadena_direcciones_email_envio_avisos_actuadores_error_reglas_activadas].join(SEPARADOR_PARAMETROS_SIMPLES);

    // Dirección origen 'e-mail' de informes automáticos
    var direccion_origen_email_informes_automaticos = $("#direccion_origen_email_informes_automaticos_red").val();
    if (direccion_origen_email_informes_automaticos != "") {
        if (comprueba_longitud_cadena(direccion_origen_email_informes_automaticos, NUMERO_MAXIMO_CARACTERES_DIRECCION_EMAIL) == false) {
            $('#cadena_direccion_origen_email_informes_automaticos').addClass('data-check-failed');
            return;
        }
        if (PATRON_DIRECCION_EMAIL.test(direccion_origen_email_informes_automaticos) == false) {
            jAlert(TLNT.Idiomas._("La dirección de e-mail no tiene el formato correcto"));
            $('#direccion_origen_email_informes_automaticos_red').addClass('data-check-failed');
            return;
        }
    }
    var direcciones_email_envio_notificaciones = [
        cadena_direcciones_email_envio_validaciones_automaticas_facturas,
        cadena_direcciones_email_envio_avisos_expiraciones_tarifas,
        cadena_direcciones_email_envio_avisos_timeouts_envio_sensores_error_valores,
        cadena_direcciones_email_envio_avisos_eventos_activados,
        cadena_direcciones_email_envio_avisos_actuadores_error_reglas_activadas].join(SEPARADOR_PARAMETROS_SIMPLES);

    // Dispositivos
    var version_fuentes = $('#version_fuentes_red').val();
    var version_fuentes_web = $('#version_fuentes_web_red').val();

    // Preferencias
    var logo_personalizado = $('#logo_personalizado_red').val();
    var nombre_logo = $('#nombre_logo_red').val();
    if (comprueba_longitud_cadena(nombre_logo, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_logo_red').addClass('data-check-failed');
        return;
    }
    var url_logo = $('#url_logo_red').val();
    var titulo_web = $('#titulo_web_red').val();
    if (comprueba_longitud_cadena(titulo_web, NUMERO_MAXIMO_CARACTERES_TITULO) == false) {
        $('#titulo_web_red').addClass('data-check-failed');
        return;
    }
    var tema = $('#tema_red').val();
    var paleta_colores_graficas = $('#paleta_colores_graficas_red').val();
    var periodo_completo_informes_defecto = $('#periodo_completo_informes_defecto_red').val();

    // Opciones de mapa
    var tipo_mapa = $('#tipo_mapa_red').val();
    var nombre_mapa = $('#nombre_mapa_red').val();
    var factor_reduccion_imagen_mapa_local = factor_reduccion_imagen_mapa_local = $('#factor_reduccion_imagen_mapa_local_red').val();
    if (parseFloat(factor_reduccion_imagen_mapa_local) < 1) {
        jAlert(TLNT.Idiomas._('El factor de reducción de imagen de mapa debe ser mayor o igual que 1'));
        return;
    }
    var etiquetas_mapa = $('#etiquetas_mapa_red').val();

    // Mapa (posición y zoom por defecto)
    var latitud_mapa_defecto = $('#latitud_mapa_defecto').val();
    var longitud_mapa_defecto = $('#longitud_mapa_defecto').val();
    var zoom_mapa_defecto = $('#zoom_mapa_defecto').val();

    // Parámetros de la ventana
    var pais_tarifas_electricas_anterior = $("#parametros_ventana_anyadir_modificar_red").attr("pais_tarifas_electricas");
    var pais_tarifas_gas_anterior = $("#parametros_ventana_anyadir_modificar_red").attr("pais_tarifas_gas");
    var pais_tarifas_agua_anterior = $("#parametros_ventana_anyadir_modificar_red").attr("pais_tarifas_agua");
    var logo_personalizado_anterior = $("#parametros_ventana_anyadir_modificar_red").attr("logo_personalizado");
    var tipo_mapa_anterior = $("#parametros_ventana_anyadir_modificar_red").attr("tipo_mapa");
    var nombre_mapa_anterior = $("#parametros_ventana_anyadir_modificar_red").attr("nombre_mapa");
    var factor_reduccion_imagen_mapa_local_anterior = $("#parametros_ventana_anyadir_modificar_red").attr("factor_reduccion_imagen_mapa_local");

    // Se añade o modifica la red
    if (anyadir_nodo == true) {
        // Flag de duplicar red
        var id_red_anterior = id_nodo;
        var duplicar_red = (id_red_anterior != ID_NINGUNO);

        // Se comprueban las imágenes de los logos
        var duplicar_imagen_logo = false;
        var duplicar_imagen_logo_pdf = false;
        if (logo_personalizado == VALOR_SI) {
            if ($('#fichero_logo_red_text').val() == "") {
                if ((duplicar_red == true) &&
                    (logo_personalizado_anterior == VALOR_SI)) {
                    // Nota: Es un duplicado y ya había imagen de logo: no hace faltar subir un nuevo fichero de imagen,
                    // se duplicará la imagen anterior
                    duplicar_imagen_logo = true;
                }
                else {
                    jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo"));
                    return;
                }
            }
            else {
                var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_RED_LOGO, "fichero_logo_red_file");
                if (imagen_correcta == false) {
                    $('#fichero_logo_red_text').addClass('data-check-failed');
                    $('#fichero_logo_red_text').val("");
                    return;
                }
            }
            if ($('#fichero_logo_pdf_red_text').val() == "") {
                if ((duplicar_red == true) &&
                    (logo_personalizado_anterior == VALOR_SI)) {
                    // Nota: Es un duplicado y ya había imagen de logo PDF: no hace faltar subir un nuevo fichero de imagen,
                    // se duplicará la imagen anterior
                    duplicar_imagen_logo_pdf = true;
                }
                else {
                    jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo PDF"));
                    return;
                }
            }
            else {
                var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_RED_LOGO_PDF, "fichero_logo_pdf_red_file");
                if (imagen_correcta == false) {
                    $('#fichero_logo_pdf_red_text').addClass('data-check-failed');
                    $('#fichero_logo_pdf_red_text').val("");
                    return;
                }
            }
        }

        // Se comprueba la imagen del mapa local
        var duplicar_imagen_mapa_local = false;
        if (tipo_mapa == TIPO_MAPA_LOCAL) {
            if ($('#fichero_imagen_mapa_red_text').val() == "") {
                if ((duplicar_red == true) &&
                    (tipo_mapa_anterior == TIPO_MAPA_LOCAL)) {
                    // Nota: Es un duplicado y ya había imagen de mapa local: no hace faltar subir un nuevo fichero de imagen,
                    // se duplicará la imagen anterior
                    duplicar_imagen_mapa_local = true;
                }
                else {
                    jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen para el mapa local"));
                    return;
                }
            }
            else {
                var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_RED_MAPA, "fichero_imagen_mapa_red_file");
                if (imagen_correcta == false) {
                    $('#fichero_imagen_mapa_red_text').addClass('data-check-failed');
                    $('#fichero_imagen_mapa_red_text').val("");
                    return;
                }
            }
        }

        // Se añade la red
        $.post("./src/lib/modulos/Nodos/administracion/anyade_red.php", {
            id_red: id_red,
            nombre: nombre,
            id_cliente: id_cliente,
            zona_horaria: zona_horaria,
            idioma: idioma,
            tipo_formato_fecha_local: tipo_formato_fecha_local,
            separador_miles: separador_miles,
            punto_decimal: punto_decimal,
            unidades_medida: unidades_medida,
            paises_tarifas: paises_tarifas,
            medicion_defecto: medicion_defecto,
            procesado_cuartohorario: procesado_cuartohorario,
            parametros_caducidad_valores: parametros_caducidad_valores,
            direcciones_email_envio_notificaciones: direcciones_email_envio_notificaciones,
            direccion_origen_email_informes_automaticos: direccion_origen_email_informes_automaticos,
            version_fuentes: version_fuentes,
            version_fuentes_web: version_fuentes_web,
            logo_personalizado: logo_personalizado,
            nombre_logo: nombre_logo,
            url_logo: url_logo,
            titulo_web: titulo_web,
            tema: tema,
            paleta_colores_graficas: paleta_colores_graficas,
            periodo_completo_informes_defecto: periodo_completo_informes_defecto,
            tipo_mapa: tipo_mapa,
            nombre_mapa: nombre_mapa,
            factor_reduccion_imagen_mapa_local: factor_reduccion_imagen_mapa_local,
            etiquetas_mapa: etiquetas_mapa,
            latitud_mapa_defecto: latitud_mapa_defecto,
            longitud_mapa_defecto: longitud_mapa_defecto,
            zoom_mapa_defecto: zoom_mapa_defecto
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Identificador de red añadida
            var id_red = resultado.id_red;

            // Se guardan las imágenes de los logos
            if (logo_personalizado == VALOR_SI) {
                if (duplicar_imagen_logo == false) {
                    var control_fichero_imagen = $('#fichero_logo_red_file')[0];
                    var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_RED_LOGO, id_red, control_fichero_imagen);
                    if (imagen_guardada_correcta == false) {
                        return;
                    }
                }
                else {
                    var imagen_duplicada_correcta = duplica_imagen_base_datos(ORIGEN_IMAGEN_RED_LOGO, id_red_anterior, id_red);
                    if (imagen_duplicada_correcta == false) {
                        return;
                    }
                }
                if (duplicar_imagen_logo_pdf == false) {
                    var control_fichero_imagen = $('#fichero_logo_pdf_red_file')[0];
                    var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_RED_LOGO_PDF, id_red, control_fichero_imagen);
                    if (imagen_guardada_correcta == false) {
                        return;
                    }
                }
                else {
                    var imagen_duplicada_correcta = duplica_imagen_base_datos(ORIGEN_IMAGEN_RED_LOGO_PDF, id_red_anterior, id_red);
                    if (imagen_duplicada_correcta == false) {
                        return;
                    }
                }
            }

            // Se guarda la imagen del mapa local
            if (tipo_mapa == TIPO_MAPA_LOCAL) {
                if (duplicar_imagen_mapa_local == false) {
                    var control_fichero_imagen = $('#fichero_imagen_mapa_red_file')[0];
                    var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_RED_MAPA, id_red, control_fichero_imagen);
                    if (imagen_guardada_correcta == false) {
                        return;
                    }
                }
                else {
                    var imagen_duplicada_correcta = duplica_imagen_base_datos(ORIGEN_IMAGEN_RED_MAPA, id_red_anterior, id_red);
                    if (imagen_duplicada_correcta == false) {
                        return;
                    }
                }
            }

            // Se muestra el mensaje y se actualiza la tabla de redes
            jInfo(resultado.msg);
            actualiza_tabla_nodos(TIPO_NODO_RED);

            // Se suma uno al identificador de red añadida
            // (para no tener que modificarlo manualmente si se añade más de una red)
            var id_red_siguiente = parseInt(id_red) + 1;
            $("#id_red").val(id_red_siguiente);
        });
    }
    else {
        // Modificación de posición y zoom del mapa por defecto por cambio de tipo de mapa
        if (tipo_mapa_anterior != tipo_mapa) {
            latitud_mapa_defecto = 0.0;
            longitud_mapa_defecto = 0.0;
            zoom_mapa_defecto = ZOOM_MAPA_DEFECTO;
        }

        // Parámetros de la modificación de la red
        var parametros_red = [];
        parametros_red["nombre"] = nombre;
        parametros_red["id_cliente"] = id_cliente;
        parametros_red["zona_horaria"] = zona_horaria;
        parametros_red["idioma"] = idioma;
        parametros_red["tipo_formato_fecha_local"] = tipo_formato_fecha_local;
        parametros_red["separador_miles"] = separador_miles;
        parametros_red["punto_decimal"] = punto_decimal;
        parametros_red["unidades_medida"] = unidades_medida;
        parametros_red["paises_tarifas"] = paises_tarifas;
        parametros_red["medicion_defecto"] = medicion_defecto;
        parametros_red["procesado_cuartohorario"] = procesado_cuartohorario;
        parametros_red["parametros_caducidad_valores"] = parametros_caducidad_valores;
        parametros_red["direcciones_email_envio_notificaciones"] = direcciones_email_envio_notificaciones;
        parametros_red["direccion_origen_email_informes_automaticos"] = direccion_origen_email_informes_automaticos;
        parametros_red["version_fuentes"] = version_fuentes;
        parametros_red["version_fuentes_web"] = version_fuentes_web;
        parametros_red["logo_personalizado"] = logo_personalizado;
        parametros_red["nombre_logo"] = nombre_logo;
        parametros_red["url_logo"] = url_logo;
        parametros_red["titulo_web"] = titulo_web;
        parametros_red["tema"] = tema;
        parametros_red["paleta_colores_graficas"] = paleta_colores_graficas;
        parametros_red["periodo_completo_informes_defecto"] = periodo_completo_informes_defecto;
        parametros_red["tipo_mapa"] = tipo_mapa;
        parametros_red["nombre_mapa"] = nombre_mapa;
        parametros_red["factor_reduccion_imagen_mapa_local"] = factor_reduccion_imagen_mapa_local;
        parametros_red["etiquetas_mapa"] = etiquetas_mapa;
        parametros_red["latitud_mapa_defecto"] = latitud_mapa_defecto;
        parametros_red["longitud_mapa_defecto"] = longitud_mapa_defecto;
        parametros_red["zoom_mapa_defecto"] = zoom_mapa_defecto;
        // Parámetros extra
        parametros_red["pais_tarifas_electricas_anterior"] = pais_tarifas_electricas_anterior;
        parametros_red["pais_tarifas_gas_anterior"] = pais_tarifas_gas_anterior;
        parametros_red["pais_tarifas_agua_anterior"] = pais_tarifas_agua_anterior;
        parametros_red["logo_personalizado_anterior"] = logo_personalizado_anterior;
        parametros_red["tipo_mapa_anterior"] = tipo_mapa_anterior;
        parametros_red["nombre_mapa_anterior"] = nombre_mapa_anterior;
        parametros_red["factor_reduccion_imagen_mapa_local_anterior"] = factor_reduccion_imagen_mapa_local_anterior;

        // Se muestra un mensaje de aviso antes de modificar la red en los siguientes casos:
        // - Eliminación de logo personalizado
        // - Cambio de tipo de mapa de local a internet
        var mensaje_aviso = "";
        if ((logo_personalizado_anterior == VALOR_SI) && (logo_personalizado == VALOR_NO)) {
            mensaje_aviso += "\n(" + TLNT.Idiomas._("se eliminará el logo personalizado") + ")";
        }
        if ((tipo_mapa_anterior == TIPO_MAPA_LOCAL) && (tipo_mapa == TIPO_MAPA_INTERNET)) {
            mensaje_aviso += "\n(" + TLNT.Idiomas._("el tipo de mapa ha cambiado y se eliminará la imagen del mapa local") + ")";
        }
        if (mensaje_aviso == "") {
            modifica_red(id_red, parametros_red);
        }
        else {
            // Se muestra un mensaje de aviso y se confirma la modificación de la red
            mensaje_aviso = TLNT.Idiomas._("¿Está seguro de que desea modificar la red?") + mensaje_aviso;
            jConfirmAcceptCancelAlert(mensaje_aviso, TLNT.Idiomas._("Pregunta"), function(res) {
                if (res == true) {
                    modifica_red(id_red, parametros_red);
                }
            });
        }
    }
}


function modifica_red(id_red, parametros_red) {
    // Se comprueban las imágenes de los logos
    if ((parametros_red["logo_personalizado_anterior"] == VALOR_NO) && (parametros_red["logo_personalizado"] == VALOR_SI)) {
        if ($('#fichero_logo_red_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo"));
            return;
        }
        if ($('#fichero_logo_pdf_red_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo PDF"));
            return;
        }
    }
    if (parametros_red["logo_personalizado"] == VALOR_SI) {
        if ($('#fichero_logo_red_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_RED_LOGO, "fichero_logo_red_file");
            if (imagen_correcta == false) {
                $('#fichero_logo_red_text').addClass('data-check-failed');
                $('#fichero_logo_red_text').val("");
                return;
            }
        }
        if ($('#fichero_logo_pdf_red_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_RED_LOGO_PDF, "fichero_logo_pdf_red_file");
            if (imagen_correcta == false) {
                $('#fichero_logo_pdf_red_text').addClass('data-check-failed');
                $('#fichero_logo_pdf_red_text').val("");
                return;
            }
        }
    }

    // Se comprueba la imagen del mapa local
    if ((parametros_red["tipo_mapa_anterior"] == TIPO_MAPA_INTERNET) && (parametros_red["tipo_mapa"] == TIPO_MAPA_LOCAL)) {
        if ($('#fichero_imagen_mapa_red_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen para el mapa local"));
            return;
        }
    }
    if (parametros_red["tipo_mapa"] == TIPO_MAPA_LOCAL) {
        if ($('#fichero_imagen_mapa_red_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_RED_MAPA, "fichero_imagen_mapa_red_file");
            if (imagen_correcta == false) {
                $('#fichero_imagen_mapa_red_text').addClass('data-check-failed');
                $('#fichero_imagen_mapa_red_text').val("");
                return;
            }
        }
    }

    // Se modifica la red
    $.post("./src/lib/modulos/Nodos/administracion/modifica_red.php", {
        id_red: id_red,
        nombre: parametros_red["nombre"],
        id_cliente: parametros_red["id_cliente"],
        zona_horaria: parametros_red["zona_horaria"],
        idioma: parametros_red["idioma"],
        tipo_formato_fecha_local: parametros_red["tipo_formato_fecha_local"],
        separador_miles: parametros_red["separador_miles"],
        punto_decimal: parametros_red["punto_decimal"],
        unidades_medida: parametros_red["unidades_medida"],
        paises_tarifas: parametros_red["paises_tarifas"],
        medicion_defecto: parametros_red["medicion_defecto"],
        procesado_cuartohorario: parametros_red["procesado_cuartohorario"],
        parametros_caducidad_valores: parametros_red["parametros_caducidad_valores"],
        direcciones_email_envio_notificaciones: parametros_red["direcciones_email_envio_notificaciones"],
        direccion_origen_email_informes_automaticos: parametros_red["direccion_origen_email_informes_automaticos"],
        version_fuentes: parametros_red["version_fuentes"],
        version_fuentes_web: parametros_red["version_fuentes_web"],
        logo_personalizado: parametros_red["logo_personalizado"],
        nombre_logo: parametros_red["nombre_logo"],
        url_logo: parametros_red["url_logo"],
        titulo_web: parametros_red["titulo_web"],
        tema: parametros_red["tema"],
        paleta_colores_graficas: parametros_red["paleta_colores_graficas"],
        periodo_completo_informes_defecto: parametros_red["periodo_completo_informes_defecto"],
        tipo_mapa: parametros_red["tipo_mapa"],
        nombre_mapa: parametros_red["nombre_mapa"],
        factor_reduccion_imagen_mapa_local: parametros_red["factor_reduccion_imagen_mapa_local"],
        etiquetas_mapa: parametros_red["etiquetas_mapa"],
        latitud_mapa_defecto: parametros_red["latitud_mapa_defecto"],
        longitud_mapa_defecto: parametros_red["longitud_mapa_defecto"],
        zoom_mapa_defecto: parametros_red["zoom_mapa_defecto"],
        // Parámetros extra
        pais_tarifas_electricas_anterior: parametros_red["pais_tarifas_electricas_anterior"],
        pais_tarifas_gas_anterior: parametros_red["pais_tarifas_gas_anterior"],
        pais_tarifas_agua_anterior: parametros_red["pais_tarifas_agua_anterior"],
        logo_personalizado_anterior: parametros_red["logo_personalizado_anterior"],
        tipo_mapa_anterior: parametros_red["tipo_mapa_anterior"]
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se guardan las imágenes de los logos
        if (parametros_red["logo_personalizado"] == VALOR_SI) {
            if ($('#fichero_logo_red_text').val() != "") {
                // Nota: La carga de la imagen tarda unos milisegundos (hasta entonces no se muestra la barra de progreso)
                var control_fichero_imagen = $('#fichero_logo_red_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_RED_LOGO, id_red, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
                $('#fichero_logo_red_file').val("");
            }
            if ($('#fichero_logo_pdf_red_text').val() != "") {
                // Nota: La carga de la imagen tarda unos milisegundos (hasta entonces no se muestra la barra de progreso)
                var control_fichero_imagen = $('#fichero_logo_pdf_red_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_RED_LOGO_PDF, id_red, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
                $('#fichero_logo_pdf_red_file').val("");
            }
        }

        // Se guarda la imagen del mapa local
        var tipo_mapa_modificado = false;
        var imagen_mapa_modificada = false;
        var factor_reduccion_imagen_mapa_local_modificado = false;
        if (parametros_red["tipo_mapa_anterior"] != parametros_red["tipo_mapa"]) {
            tipo_mapa_modificado = true;
        }
        switch (parametros_red["tipo_mapa"]) {
            case TIPO_MAPA_LOCAL: {
                if ($('#fichero_imagen_mapa_red_text').val() != "") {
                    // Nota: La carga de la imagen tarda unos milisegundos (hasta entonces no se muestra la barra de progreso)
                    var control_fichero_imagen = $('#fichero_imagen_mapa_red_file')[0];
                    var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_RED_MAPA, id_red, control_fichero_imagen);
                    if (imagen_guardada_correcta == false) {
                        return;
                    }
                    imagen_mapa_modificada = true;
                }
                if ((parametros_red["factor_reduccion_imagen_mapa_local_anterior"] != parametros_red["factor_reduccion_imagen_mapa_local"])) {
                    factor_reduccion_imagen_mapa_local_modificado = true;
                }
                $('#boton_mostrar_imagen_mapa_red').hide();
                break;
            }
            case TIPO_MAPA_INTERNET: {
                if ((parametros_red["nombre_mapa_anterior"] != parametros_red["nombre_mapa"])) {
                    imagen_mapa_modificada = true;
                }
                $('#boton_mostrar_imagen_mapa_red').show();
                break;
            }
        }

        // Se actualiza la red actual (si es necesario)
        if (resultado.red_actual == VALOR_NO) {
            jInfo(resultado.msg);
        }
        else {
            actualiza_red_actual();
        }
        actualiza_tabla_clientes();
        refresca_tabla_nodo(TIPO_NODO_RED, id_red);

        // Si se ha modificado el del mapa de la red no se cierra la ventana
        // (para poder modificar los parámetros del mapa sin tener que volver a abrir la ventana)
        var mantener_ventana_modal_abierta = (tipo_mapa_modificado == true) ||
            (imagen_mapa_modificada == true) ||
            (factor_reduccion_imagen_mapa_local_modificado == true);

        // Si se mantiene abierta la ventana modal, se establecen los parámetros actuales de la ventana
        if (mantener_ventana_modal_abierta == true) {
            $("#parametros_ventana_anyadir_modificar_red").attr("pais_tarifas_electricas", parametros_red["pais_tarifas_electricas"]);
            $("#parametros_ventana_anyadir_modificar_red").attr("pais_tarifas_gas", parametros_red["pais_tarifas_gas"]);
            $("#parametros_ventana_anyadir_modificar_red").attr("pais_tarifas_agua", parametros_red["pais_tarifas_agua"]);
            $("#parametros_ventana_anyadir_modificar_red").attr("logo_personalizado", parametros_red["logo_personalizado"]);
            $("#parametros_ventana_anyadir_modificar_red").attr("tipo_mapa", parametros_red["tipo_mapa"]);
            $("#parametros_ventana_anyadir_modificar_red").attr("nombre_mapa", parametros_red["nombre_mapa"]);
            $("#parametros_ventana_anyadir_modificar_red").attr("factor_reduccion_imagen_mapa_local", parametros_red["factor_reduccion_imagen_mapa_local"]);
            $('#fichero_imagen_mapa_red_text').val("");

            // Si la pestaña activa es la pestaña de mapa, se recarga el mapa
            var href_pestanya_activa = $('#tabs-administracion-red .active > a').attr("href");
            var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
            if (id_pestanya_activa == "mapa") {
                localizador_mapa_visible("_defecto");
            }

            // Si se ha modificado el tipo de mapa, se establecen las coordenadas y el zoom por defecto a 0
            // (ya se han modificado en la base de datos)
            if (tipo_mapa_modificado == true) {
                $("#latitud_mapa_defecto").val(0);
                $("#longitud_mapa_defecto").val(0);
                $("#zoom_mapa_defecto").val(0);
            }

            // Se muestran los botones para mostrar las imágenes de los logos
            if (parametros_red["logo_personalizado"] == VALOR_SI) {
                $('#boton_mostrar_imagen_logo_red').show();
                $('#boton_mostrar_imagen_logo_pdf_red').show();
            }
            else {
                $('#boton_mostrar_imagen_logo_red').hide();
                $('#boton_mostrar_imagen_logo_pdf_red').hide();
            }

            // Se muestra el botón para mostrar la imagen del mapa local
            switch (parametros_red["tipo_mapa"]) {
                case TIPO_MAPA_LOCAL: {
                    $('#boton_mostrar_imagen_mapa_red').show();
                    break;
                }
                case TIPO_MAPA_INTERNET: {
                    $('#boton_mostrar_imagen_mapa_red').hide();
                    break;
                }
            }
        }
        else {
            $('#ventana_modal').modal('hide');
        }
    });
}


function boton_modificar_red_parcial() {
    if (TLNT.Check.inputs('contenido_modal')) {
        jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
        return;
    }

    // Preferencias
    var logo_personalizado = $('#logo_personalizado_red').val();
    var nombre_logo = $('#nombre_logo_red').val();
    var url_logo = $('#url_logo_red').val();
    var titulo_web = $('#titulo_web_red').val();
    var tema = $('#tema_red').val();
    var paleta_colores_graficas = $('#paleta_colores_graficas_red').val();
    var periodo_completo_informes_defecto = $('#periodo_completo_informes_defecto_red').val();

    // Opciones de mapa
    var tipo_mapa = $('#tipo_mapa_red').val();
    var nombre_mapa = $('#nombre_mapa_red').val();
    var factor_reduccion_imagen_mapa_local = factor_reduccion_imagen_mapa_local = $('#factor_reduccion_imagen_mapa_local_red').val();
    if (parseFloat(factor_reduccion_imagen_mapa_local) < 1) {
        jAlert(TLNT.Idiomas._('El factor de reducción de imagen de mapa debe ser mayor o igual que 1'));
        return;
    }
    var etiquetas_mapa = $('#etiquetas_mapa_red').val();

    // Mapa (posición y zoom por defecto)
    var latitud_mapa_defecto = $('#latitud_mapa_defecto').val();
    var longitud_mapa_defecto = $('#longitud_mapa_defecto').val();
    var zoom_mapa_defecto = $('#zoom_mapa_defecto').val();

    // Parámetros de la ventana
    var id_red = $("#parametros_ventana_anyadir_modificar_red_parcial").attr("id_red");
    var logo_personalizado_anterior = $("#parametros_ventana_anyadir_modificar_red_parcial").attr("logo_personalizado");
    var tipo_mapa_anterior = $("#parametros_ventana_anyadir_modificar_red_parcial").attr("tipo_mapa");
    var nombre_mapa_anterior = $("#parametros_ventana_anyadir_modificar_red_parcial").attr("nombre_mapa");
    var factor_reduccion_imagen_mapa_local_anterior = $("#parametros_ventana_anyadir_modificar_red_parcial").attr("factor_reduccion_imagen_mapa_local");

    // Modificación de posición y zoom en mapa por defecto por cambio de tipo de mapa
    if (tipo_mapa_anterior != tipo_mapa) {
        latitud_mapa_defecto = 0.0;
        longitud_mapa_defecto = 0.0;
        zoom_mapa_defecto = ZOOM_MAPA_DEFECTO;
    }

    // Parámetros de la modificación de la red
    var parametros_red = [];
    parametros_red["logo_personalizado"] = logo_personalizado;
    parametros_red["nombre_logo"] = nombre_logo;
    parametros_red["url_logo"] = url_logo;
    parametros_red["titulo_web"] = titulo_web;
    parametros_red["tema"] = tema;
    parametros_red["paleta_colores_graficas"] = paleta_colores_graficas;
    parametros_red["periodo_completo_informes_defecto"] = periodo_completo_informes_defecto;
    parametros_red["tipo_mapa"] = tipo_mapa;
    parametros_red["nombre_mapa"] = nombre_mapa;
    parametros_red["factor_reduccion_imagen_mapa_local"] = factor_reduccion_imagen_mapa_local;
    parametros_red["etiquetas_mapa"] = etiquetas_mapa;
    parametros_red["latitud_mapa_defecto"] = latitud_mapa_defecto;
    parametros_red["longitud_mapa_defecto"] = longitud_mapa_defecto;
    parametros_red["zoom_mapa_defecto"] = zoom_mapa_defecto;
    // Parámetros extra
    parametros_red["logo_personalizado_anterior"] = logo_personalizado_anterior;
    parametros_red["tipo_mapa_anterior"] = tipo_mapa_anterior;
    parametros_red["nombre_mapa_anterior"] = nombre_mapa_anterior;
    parametros_red["factor_reduccion_imagen_mapa_local_anterior"] = factor_reduccion_imagen_mapa_local_anterior;

    // Se muestra un mensaje de aviso antes de modificar la red en los siguientes casos:
    // - Eliminación de logo personalizado
    // - Cambio de tipo de mapa de local a internet
    var mensaje_aviso = "";
    if ((logo_personalizado_anterior == VALOR_SI) && (logo_personalizado == VALOR_NO)) {
        mensaje_aviso += "\n(" + TLNT.Idiomas._("se eliminará el logo personalizado") + ")";
    }
    if ((tipo_mapa_anterior == TIPO_MAPA_LOCAL) && (tipo_mapa == TIPO_MAPA_INTERNET)) {
        mensaje_aviso += "\n(" + TLNT.Idiomas._("el tipo de mapa ha cambiado y se eliminará la imagen del mapa local") + ")";
    }
    if (mensaje_aviso == "") {
        modifica_red_parcial(id_red, parametros_red);
    }
    else {
        // Se muestra un mensaje de aviso y se confirma la modificación de la red
        mensaje_aviso = TLNT.Idiomas._("¿Está seguro de que desea modificar la red?") + mensaje_aviso;
        jConfirmAcceptCancelAlert(mensaje_aviso, TLNT.Idiomas._("Pregunta"), function(res) {
            if (res == true) {
                modifica_red_parcial(id_red, parametros_red);
            }
        });
    }
}


function modifica_red_parcial(id_red, parametros_red) {
    // Se comprueban las imágenes de los logos
    if ((parametros_red["logo_personalizado_anterior"] == VALOR_NO) && (parametros_red["logo_personalizado"] == VALOR_SI)) {
        if ($('#fichero_logo_red_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo"));
            return;
        }
        if ($('#fichero_logo_pdf_red_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo PDF"));
            return;
        }
    }
    if (parametros_red["logo_personalizado"] == VALOR_SI) {
        if ($('#fichero_logo_red_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_RED_LOGO, "fichero_logo_red_file");
            if (imagen_correcta == false) {
                $('#fichero_logo_red_text').addClass('data-check-failed');
                $('#fichero_logo_red_text').val("");
                return;
            }
        }
        if ($('#fichero_logo_pdf_red_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_RED_LOGO_PDF, "fichero_logo_pdf_red_file");
            if (imagen_correcta == false) {
                $('#fichero_logo_pdf_red_text').addClass('data-check-failed');
                $('#fichero_logo_pdf_red_text').val("");
                return;
            }
        }
    }

    // Se comprueba la imagen del mapa local
    if ((parametros_red["tipo_mapa_anterior"] == TIPO_MAPA_INTERNET) && (parametros_red["tipo_mapa"] == TIPO_MAPA_LOCAL)) {
        if ($('#fichero_imagen_mapa_red_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen para el mapa local"));
            return;
        }
    }
    if (parametros_red["tipo_mapa"] == TIPO_MAPA_LOCAL) {
        if ($('#fichero_imagen_mapa_red_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_RED_MAPA, "fichero_imagen_mapa_red_file");
            if (imagen_correcta == false) {
                $('#fichero_imagen_mapa_red_text').addClass('data-check-failed');
                $('#fichero_imagen_mapa_red_text').val("");
                return;
            }
        }
    }

    // Se modifica la red
    $.post("./src/lib/modulos/Nodos/administracion/modifica_red_parcial.php", {
        id_red: id_red,
        logo_personalizado: parametros_red["logo_personalizado"],
        nombre_logo: parametros_red["nombre_logo"],
        url_logo: parametros_red["url_logo"],
        titulo_web: parametros_red["titulo_web"],
        tema: parametros_red["tema"],
        paleta_colores_graficas: parametros_red["paleta_colores_graficas"],
        periodo_completo_informes_defecto: parametros_red["periodo_completo_informes_defecto"],
        tipo_mapa: parametros_red["tipo_mapa"],
        nombre_mapa: parametros_red["nombre_mapa"],
        factor_reduccion_imagen_mapa_local: parametros_red["factor_reduccion_imagen_mapa_local"],
        etiquetas_mapa: parametros_red["etiquetas_mapa"],
        latitud_mapa_defecto: parametros_red["latitud_mapa_defecto"],
        longitud_mapa_defecto: parametros_red["longitud_mapa_defecto"],
        zoom_mapa_defecto: parametros_red["zoom_mapa_defecto"],
        // Parámetros extra
        logo_personalizado_anterior: parametros_red["logo_personalizado_anterior"],
        tipo_mapa_anterior: parametros_red["tipo_mapa_anterior"]
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se guardan las imagenes de los logos
        if (parametros_red["logo_personalizado"] == VALOR_SI) {
            if ($('#fichero_logo_red_text').val() != "") {
                // Nota: La carga de la imagen tarda unos milisegundos (hasta entonces no se muestra la barra de progreso)
                var control_fichero_imagen = $('#fichero_logo_red_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_RED_LOGO, id_red, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
                $('#fichero_logo_red_file').val("");
            }
            if ($('#fichero_logo_pdf_red_text').val() != "") {
                // Nota: La carga de la imagen tarda unos milisegundos (hasta entonces no se muestra la barra de progreso)
                var control_fichero_imagen = $('#fichero_logo_pdf_red_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_RED_LOGO_PDF, id_red, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
                $('#fichero_logo_pdf_red_file').val("");
            }
        }

        // Se guarda la imagen del mapa local
        var tipo_mapa_modificado = false;
        var imagen_mapa_modificada = false;
        var factor_reduccion_imagen_mapa_local_modificado = false;
        if (parametros_red["tipo_mapa_anterior"] != parametros_red["tipo_mapa"]) {
            tipo_mapa_modificado = true;
        }
        switch (parametros_red["tipo_mapa"]) {
            case TIPO_MAPA_LOCAL: {
                if ($('#fichero_imagen_mapa_red_text').val() != "") {
                    // Nota: La carga de la imagen tarda unos milisegundos (hasta entonces no se muestra la barra de progreso)
                    var control_fichero_imagen = $('#fichero_imagen_mapa_red_file')[0];
                    var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_RED_MAPA, id_red, control_fichero_imagen);
                    if (imagen_guardada_correcta == false) {
                        return;
                    }
                    imagen_mapa_modificada = true;
                }
                if ((parametros_red["factor_reduccion_imagen_mapa_local_anterior"] != parametros_red["factor_reduccion_imagen_mapa_local"])) {
                    factor_reduccion_imagen_mapa_local_modificado = true;
                }
                break;
            }
            case TIPO_MAPA_INTERNET: {
                if ((parametros_red["nombre_mapa_anterior"] != parametros_red["nombre_mapa"])) {
                    imagen_mapa_modificada = true;
                }
                break;
            }
        }

        // Se actualiza la red actual
        actualiza_red_actual();

        // Si se ha modificado el del mapa de la red no se cierra la ventana
        // (para poder modificar los parámetros del mapa sin tener que volver a abrir la ventana)
        var mantener_ventana_modal_abierta = (tipo_mapa_modificado == true) ||
            (imagen_mapa_modificada == true) ||
            (factor_reduccion_imagen_mapa_local_modificado == true);

        // Si se mantiene abierta la ventana modal, se establecen los parámetros actuales de la ventana
        if (mantener_ventana_modal_abierta == true) {
            $("#parametros_ventana_anyadir_modificar_red_parcial").attr("logo_personalizado", parametros_red["logo_personalizado"]);
            $("#parametros_ventana_anyadir_modificar_red_parcial").attr("tipo_mapa", parametros_red["tipo_mapa"]);
            $("#parametros_ventana_anyadir_modificar_red_parcial").attr("nombre_mapa", parametros_red["nombre_mapa"]);
            $("#parametros_ventana_anyadir_modificar_red_parcial").attr("factor_reduccion_imagen_mapa_local", parametros_red["factor_reduccion_imagen_mapa_local"]);
            $('#fichero_imagen_mapa_red_text').val("");
            $("#titulo-tab-opciones-mapa").trigger("click");

            // Si se ha modificado el tipo de mapa, se establecen las coordenadas y el zoom por defecto a 0
            // (ya se han modificado en la base de datos)
            if (tipo_mapa_modificado == true) {
                $("#latitud_mapa_defecto").val(0);
                $("#longitud_mapa_defecto").val(0);
                $("#zoom_mapa_defecto").val(0);
            }

            // Se muestra el botón para mostrar la imagen del mapa local
            switch (parametros_red["tipo_mapa"]) {
                case TIPO_MAPA_LOCAL: {
                    $('#boton_mostrar_imagen_mapa_red').show();
                    break;
                }
                case TIPO_MAPA_INTERNET: {
                    $('#boton_mostrar_imagen_mapa_red').hide();
                    break;
                }
            }
        }
        else {
            $('#ventana_modal').modal('hide');
        }
    });
}


function anyade_modifica_dispositivo(anyadir_nodo, id_nodo) {

    var ARQUITECTURA_DISPOSITIVO_RPI = "RPI"
    var ARQUITECTURA_DISPOSITIVO_BABELBOX =  "BABELBOX";
    var ARQUITECTURA_DISPOSITIVO_BYE_RADON =  "BYE RADON";

    // Nombre y descripción
    var nombre = $('#nombre_dispositivo').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_dispositivo').addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_dispositivo').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $('#descripcion_dispositivo').addClass('data-check-failed');
        return;
    }

    // To Do Parámetros DEMO Bye Radon
    var arquitectura = $('#id_arquitectura_dispositivo').val();
    if (arquitectura == ARQUITECTURA_DISPOSITIVO_BYE_RADON){
        var direccion_IMEI = $('#imei_dispositivo').val();
        var localizacion_dispositivo = $('#id_localizacion_dispositivo_radon').val();

    }
    else {
        var direccion_mac = $('#direccion_mac_dispositivo').val();
        var ip_local = $('#ip_local_dispositivo').val();

    }

    var frecuencia_actualizacion = $('#frecuencia_actualizacion_dispositivo').val();
    var frecuencia_envio_estado = $('#frecuencia_envio_estado').val();
/*  DESCOMENTAR PARA FUNCIONAMIENTO NORMAL
    // Parámetros
    var direccion_mac = $('#direccion_mac_dispositivo').val();
    var arquitectura = $('#id_arquitectura_dispositivo').val();
    var ip_local = $('#ip_local_dispositivo').val();
    var frecuencia_actualizacion = $('#frecuencia_actualizacion_dispositivo').val();
    var frecuencia_envio_estado = $('#frecuencia_envio_estado').val();
*/
    // Posición en mapa
    var mostrar_en_mapa = $('#mostrar_en_mapa').val();
    var latitud_mapa = $('#latitud_mapa').val();
    var longitud_mapa = $('#longitud_mapa').val();
    var zoom_mapa = $('#zoom_mapa').val();


    // Se añade o modifica el dispositivo
    if ((anyadir_nodo == true) && (arquitectura == ARQUITECTURA_DISPOSITIVO_BYE_RADON)) {
        $.post("./src/lib/modulos/Nodos/administracion/anyade_dispositivo_bye_radon.php", {
            nombre: nombre,
            descripcion: descripcion,
            arquitectura: arquitectura,
            direccion_IMEI: direccion_IMEI,
            id_localizacion: localizacion_dispositivo,
            frecuencia_actualizacion: frecuencia_actualizacion,
            frecuencia_envio_estado: frecuencia_envio_estado,
            mostrar_en_mapa: mostrar_en_mapa,
            latitud_mapa: latitud_mapa,
            longitud_mapa: longitud_mapa,
            zoom_mapa: zoom_mapa
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_informacion_red();
            actualiza_tabla_nodos(TIPO_NODO_DISPOSITIVO);
        });

    }

    else if (anyadir_nodo == true) {
        $.post("./src/lib/modulos/Nodos/administracion/anyade_dispositivo.php", {
            nombre: nombre,
            descripcion: descripcion,
            direccion_mac: direccion_mac,
            arquitectura: arquitectura,
            ip_local: ip_local,
            frecuencia_actualizacion: frecuencia_actualizacion,
            frecuencia_envio_estado: frecuencia_envio_estado,
            mostrar_en_mapa: mostrar_en_mapa,
            latitud_mapa: latitud_mapa,
            longitud_mapa: longitud_mapa,
            zoom_mapa: zoom_mapa
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_informacion_red();
            actualiza_tabla_nodos(TIPO_NODO_DISPOSITIVO);
        });
    }
    else {
        $.post("./src/lib/modulos/Nodos/administracion/modifica_dispositivo.php", {
            id_dispositivo: id_nodo,
            nombre: nombre,
            descripcion: descripcion,
            direccion_mac: direccion_mac,
            arquitectura: arquitectura,
            ip_local: ip_local,
            frecuencia_actualizacion: frecuencia_actualizacion,
            frecuencia_envio_estado: frecuencia_envio_estado,
            mostrar_en_mapa: mostrar_en_mapa,
            latitud_mapa: latitud_mapa,
            longitud_mapa: longitud_mapa,
            zoom_mapa: zoom_mapa
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            refresca_tabla_nodo(TIPO_NODO_DISPOSITIVO, id_nodo);
            actualiza_tabla_nodos(TIPO_NODO_AXON);
            $('#ventana_modal').modal('hide');
        });
    }
}


function anyade_modifica_axon(anyadir_nodo, id_nodo) {
    // Nombre
    var nombre = $('#nombre_axon').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_axon').addClass('data-check-failed');
        return;
    }

    // Comprobación de dispositivo seleccionado
    var id_dispositivo = $("#id_dispositivo_axon").val();
    if (id_dispositivo == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay dispositivo seleccionado'));
        return;
    }

    // Se añade o modifica el axón
    if (anyadir_nodo == true) {
        $.post("./src/lib/modulos/Nodos/administracion/anyade_axon.php", {
            nombre: nombre,
            id_dispositivo: id_dispositivo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_nodos(TIPO_NODO_DISPOSITIVO);
            actualiza_tabla_nodos(TIPO_NODO_AXON);
        });
    }
    else {
        $.post("./src/lib/modulos/Nodos/administracion/modifica_axon.php", {
            id_axon: id_nodo,
            nombre: nombre,
            id_dispositivo: id_dispositivo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_nodos(TIPO_NODO_DISPOSITIVO);
            refresca_tabla_nodo(TIPO_NODO_AXON, id_nodo);
            $('#ventana_modal').modal('hide');
        });
    }
}

// Funciones relacionadas con las actualizaciones de firmware de los dispositivos
function boton_anyadir_version_firmware() {

    var id_version = $('#id_version').val();
    var nombre_fichero = $('#nombre_fichero').val();
    var servidor = $('#servidor').val();

    $.post("./src/lib/modulos/Nodos/administracion/anyade_version_firmware.php", {
        id_version: id_version,
        nombre_fichero: nombre_fichero,
        servidor: servidor
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }
        jInfo(resultado.msg);
        $('#ventana_modal').modal('hide');
    });
}

function boton_eliminar_dispositivo(){
    var parametros_select = $('#nombre_version_firmware').val().split('__');
    var nombre_fichero = parametros_select[0];
    var servidor = parametros_select[1];
    $.post("./src/lib/modulos/Nodos/administracion/elimina_version_firmware.php", {
        nombre_fichero: nombre_fichero,
        servidor: servidor
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }
        jInfo(resultado.msg);
        $('#ventana_modal').modal('hide');
    });
}

function boton_actualizar_dispositivo(){
    var id_dispositivo = this.id;
    var parametros_select = $('#nombre_version_firmware').val().split('__');
    var id_version = parametros_select[0];
    var nombre_fichero = parametros_select[1];
    var servidor = parametros_select[2];
    $.post("./src/lib/modulos/Nodos/administracion/actualiza_version_firmware.php", {
        id_version: id_version,
        id_dispositivo: id_dispositivo,
        nombre_fichero: nombre_fichero,
        servidor: servidor
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }
        jInfo(resultado.msg);
        $('#ventana_modal').modal('hide');
    });
}

function anyade_modifica_sensor(anyadir_nodo, id_nodo) {
    // Comprobación de clase, tipo y tipos de valores seleccionados
    var clase = $('#clase_sensor').val();
    if (clase == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }
    var tipo = $('#tipo_sensor').val();
    if (tipo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo seleccionado'));
        return;
    }
    var tipo_valores = $('#tipo_valores_sensor').val();
    if (tipo_valores == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo de valores seleccionado'));
        return;
    }

    // Comprobación de datos correctos de las pestañas visibles
    var ids_pestanyas_visibles = ["tab-principal"];
    switch (tipo) {
        case TIPO_SENSOR_REAL: {
            ids_pestanyas_visibles.push("tab-tipo-real");
            ids_pestanyas_visibles.push("tab-envio");
            break;
        }
        case TIPO_SENSOR_VIRTUAL: {
            ids_pestanyas_visibles.push("tab-tipo-virtual");
            ids_pestanyas_visibles.push("tab-envio");
            break;
        }
        case TIPO_SENSOR_PROCESADO: {
            ids_pestanyas_visibles.push("tab-tipo-procesado");
            break;
        }
        case TIPO_SENSOR_EXTERNO: {
            ids_pestanyas_visibles.push("tab-tipo-externo");
            ids_pestanyas_visibles.push("tab-envio");
            break;
        }
    }
    switch (clase) {
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            ids_pestanyas_visibles.push("tab-clase-energia-activa");
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            ids_pestanyas_visibles.push("tab-clase-energia-reactiva");
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            ids_pestanyas_visibles.push("tab-clase-cortes-tension");
            break;
        }
        case CLASE_SENSOR_COMPRA_ENERGIA: {
            ids_pestanyas_visibles.push("tab-clase-compra-energia");
            break;
        }
        case CLASE_SENSOR_GAS: {
            ids_pestanyas_visibles.push("tab-clase-gas");
            break;
        }
        case CLASE_SENSOR_AGUA: {
            ids_pestanyas_visibles.push("tab-clase-agua");
            break;
        }
        case CLASE_SENSOR_GENERICA: {
            ids_pestanyas_visibles.push("tab-clase-generica");
            break;
        }
    }
    var datos_correctos = true;
    for (var i = 0; i < ids_pestanyas_visibles.length; i++) {
        if (TLNT.Check.inputs(ids_pestanyas_visibles[i])) {
            datos_correctos = false;
        }
    }
    if (datos_correctos == false) {
        jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
        return;
    }

    // No se permiten comas (',') en el nombre
    // (en el histórico de eventos se guardan los nombres separados por comas)
    var nombre = $('#nombre_sensor').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_sensor').addClass('data-check-failed');
        return;
    }
    if (nombre.indexOf(",") > -1) {
        jAlert(TLNT.Idiomas._('No se permiten comas en el nombre del sensor'));
        return;
    }

    // Descripción
    var descripcion = $('#descripcion_sensor').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $('#descripcion_sensor').addClass('data-check-failed');
        return;
    }

    // Localización y visible por localizaciones hijas
    var id_localizacion = $('#id_localizacion_sensor').val();
    var visible_localizaciones_hijas = $('#visible_localizaciones_hijas_sensor').val();

    // Grupo de sensores
    var id_grupo = $('#id_grupo_sensor').val();

    // Valores puntuales
    var cambio_valores_puntuales = $('#cambio_valores_puntuales_sensor').val();

    // Incrementos
    var incrementos_tiempo_real_horarios = $('#incrementos_tiempo_real_horarios_sensor').val();
    var incrementos_negativos_validos = $('#incrementos_negativos_validos_sensor').val();

    // Guardar valores en base de datos, notificar todos los eventos y granularidad cuartohoraria
    var guardar_valores_base_datos = $('#guardar_valores_base_datos_sensor').val();
    var notificar_todos_eventos = $('#notificar_todos_eventos_sensor').val();
    var granularidad_cuartohoraria = $('#granularidad_cuartohoraria_sensor').val();

    // Frecuencias de envío y de muestreo
    var frecuencia_muestreo = $('#frecuencia_muestreo_sensor').val();
    var frecuencia_envio = $('#frecuencia_envio_sensor').val();

    // Posición en mapa
    var mostrar_en_mapa = $('#mostrar_en_mapa').val();
    var latitud_mapa = $('#latitud_mapa').val();
    var longitud_mapa = $('#longitud_mapa').val();
    var zoom_mapa = $('#zoom_mapa').val();

    // Se recupera el número de valores de la clase del sensor
    var numero_valores_clase_sensor = dame_numero_valores_clase_sensor(clase);

    // Parámetros de la clase de sensor
    var parametros_clase = "";
    switch (clase) {
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    // Comprobación de longitudes máximas
                    var cups = $('#clase_energia_activa_cups_sensor').val();
                    if (comprueba_longitud_cadena(cups, NUMERO_MAXIMO_CARACTERES_CUPS) == false) {
                        $('#clase_energia_activa_cups_sensor').addClass('data-check-failed');
                        return;
                    }
                    var prefijo_fichero_validacion_facturas = $('#clase_energia_activa_prefijo_fichero_validacion_facturas_sensor').val();
                    if (comprueba_longitud_cadena(prefijo_fichero_validacion_facturas, NUMERO_MAXIMO_CARACTERES_PREFIJOS_FICHEROS) == false) {
                        $('#clase_energia_activa_prefijo_fichero_validacion_facturas_sensor').addClass('data-check-failed');
                        return;
                    }

                    parametros_clase = [
                        $('#clase_energia_activa_id_tarifa_sensor').val(),
                        $('#clase_energia_activa_id_grupo_tarifas_sensor').val(),
                        $('#clase_energia_activa_cups_sensor').val().toUpperCase(),
                        $('#clase_energia_activa_error_maximo_validacion_facturas_sensor_energia_sensor').val(),
                        $('#clase_energia_activa_error_maximo_validacion_facturas_sensor_potencia_sensor').val(),
                        $('#clase_energia_activa_error_maximo_validacion_facturas_sensor_otros_conceptos_coste_total_sensor').val(),
                        $('#clase_energia_activa_tipo_fichero_validacion_facturas_sensor').val(),
                        $('#clase_energia_activa_prefijo_fichero_validacion_facturas_sensor').val()].join(SEPARADOR_PARAMETROS_COMPUESTOS);
                    break;
                }
                case PAIS_PORTUGAL: {
                    // Comprobación de longitudes máximas
                    var cups = $('#clase_energia_activa_cups_sensor').val();

                    parametros_clase = [
                        $('#clase_energia_activa_id_tarifa_sensor').val(),
                        $('#clase_energia_activa_id_grupo_tarifas_sensor').val(),
                        $('#clase_energia_activa_cups_sensor').val().toUpperCase()].join(SEPARADOR_PARAMETROS_COMPUESTOS);
                    break;
                }

            }
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            parametros_clase = [
                $('#clase_energia_reactiva_id_sensor_energia_activa_sensor').val(),
                $('#clase_energia_reactiva_tipo_energia_reactiva').val()].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case CLASE_SENSOR_CORTES_TENSION: {
            parametros_clase = [
                $('#clase_cortes_tension_id_sensor_energia_activa_sensor').val()].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case CLASE_SENSOR_COMPRA_ENERGIA: {
            // Sensores hijos y sensor asociado
            var ids_sensores_hijos = [];
            $("#clase_compra_energia_ids_sensores_hijos_sensor option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores_hijos.push($(this).val());
                }
            });
            if (ids_sensores_hijos.length == 0 ) {
                jAlert(TLNT.Idiomas._('No hay sensores hijos seleccionados'));
                return;
            }
            var id_sensor_asociado = $('#clase_compra_energia_id_sensor_asociado_sensor_no_visible').html();

            // Parámetros de clase
            parametros_clase = [
                ids_sensores_hijos.join(SEPARADOR_PARAMETROS_SIMPLES),
                id_sensor_asociado].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case CLASE_SENSOR_GAS: {
            switch (pais_tarifas_gas) {
                case PAIS_ESPANYA: {
                    // Comprobación de longitudes máximas
                    var cups = $('#clase_gas_cups_sensor').val();
                    if (comprueba_longitud_cadena(cups, NUMERO_MAXIMO_CARACTERES_CUPS) == false) {
                        $('#clase_gas_cups_sensor').addClass('data-check-failed');
                        return;
                    }

                    parametros_clase = [
                        $('#clase_gas_id_tarifa_sensor').val(),
                        $('#clase_gas_id_grupo_tarifas_sensor').val(),
                        $('#clase_gas_cups_sensor').val().toUpperCase()].join(SEPARADOR_PARAMETROS_COMPUESTOS);
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_AGUA: {
            switch (pais_tarifas_agua) {
                case PAIS_ESPANYA: {
                    // Comprobación de longitudes máximas
                    var cups = $('#clase_agua_cups_sensor').val();
                    if (comprueba_longitud_cadena(cups, NUMERO_MAXIMO_CARACTERES_CUPS) == false) {
                        $('#clase_agua_cups_sensor').addClass('data-check-failed');
                        return;
                    }

                    parametros_clase = [
                        $('#clase_agua_id_tarifa_sensor').val(),
                        $('#clase_agua_id_grupo_tarifas_sensor').val(),
                        $('#clase_agua_cups_sensor').val().toUpperCase()].join(SEPARADOR_PARAMETROS_COMPUESTOS);
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_GENERICA: {
            // Comprobación de longitudes máximas
            var nombre_medida = $('#clase_generica_nombre_medida_sensor').val();
            if (comprueba_longitud_cadena(nombre_medida, NUMERO_MAXIMO_CARACTERES_NOMBRE_MEDIDA) == false) {
                $('#clase_generica_nombre_medida_sensor').addClass('data-check-failed');
                return;
            }
            var unidad_medida = $('#clase_generica_unidad_medida_sensor').val();
            if (comprueba_longitud_cadena(unidad_medida, NUMERO_MAXIMO_CARACTERES_UNIDAD_MEDIDA) == false) {
                $('#clase_generica_unidad_medida_sensor').addClass('data-check-failed');
                return;
            }

            parametros_clase = [
                $('#clase_generica_nombre_medida_sensor').val().uncapitalize(),
                $('#clase_generica_unidad_medida_sensor').val(),
                $('#clase_generica_icono_mapa_sensor').val(),
                $('#clase_generica_colores_mapa_calor_valor_sensor').val(),
                $('#clase_generica_colores_mapa_calor_incremento_sensor').val(),
                $('#clase_generica_mostrar_incrementos_calculados_sensor').val()].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
    }

    // Parámetros auxiliares
    var parametros_auxiliares = "";

    // Parámetros del tipo de sensor
    var parametros_tipo = "";
    switch (tipo) {
        case TIPO_SENSOR_REAL: {
            var id_axon = $("#id_axon_sensor").val();
            if (id_axon == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._('No hay axón seleccionado'));
                return;
            }
            var clase_interfaz = $("#clase_interfaz_sensor").val();
            if (clase_interfaz == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._('No hay clase de interfaz seleccionada'));
                return;
            }

            var resultado_ubicacion_interfaz = dame_cadena_ubicacion_interfaz_clase_interfaz_sensor(clase_interfaz);
            if (resultado_ubicacion_interfaz.parametros_correctos == false) {
                if (resultado_ubicacion_interfaz.descripcion_error != "") {
                    jAlert(resultado_ubicacion_interfaz.descripcion_error);
                }
                return;
            }
            var resultado_opciones_interfaz = dame_cadena_opciones_interfaz_clase_interfaz_sensor(clase_interfaz, numero_valores_clase_sensor);
            if (resultado_opciones_interfaz.parametros_correctos == false) {
                if (resultado_opciones_interfaz.descripcion_error != "") {
                    jAlert(resultado_opciones_interfaz.descripcion_error);
                }
                return;
            }
            var ubicacion_interfaz = resultado_ubicacion_interfaz.cadena_ubicacion_interfaz;
            var opciones_interfaz = resultado_opciones_interfaz.cadena_opciones_interfaz;
            parametros_tipo = [
                id_axon,
                clase_interfaz,
                ubicacion_interfaz,
                opciones_interfaz].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_SENSOR_VIRTUAL: {
            var clase_virtual = $("#clase_virtual_sensor").val();
            if (clase_virtual == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._('No hay clase virtual seleccionada'));
                return;
            }
            parametros_tipo = clase_virtual;
            break;
        }
        case TIPO_SENSOR_PROCESADO: {
            var clase_sensor_procesado = $("#clase_procesado_sensor").val();
            if (clase_sensor_procesado == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._('No hay clase de procesado seleccionada'));
                return;
            }

            var funcion_valores_horaria = $("#funcion_valores_horaria_procesado_sensor").val();
            var misma_funcion_valores_cuartohoraria = $("#misma_funcion_valores_cuartohoraria_procesado_sensor").val();
            var funcion_valores_cuartohoraria = $("#funcion_valores_cuartohoraria_procesado_sensor").val();
            if (comprueba_longitud_cadena(funcion_valores_horaria, NUMERO_MAXIMO_CARACTERES_FUNCION_VALORES_SENSOR_PROCESADO) == false) {
                $('#funcion_valores_horaria_procesado_sensor').addClass('data-check-failed');
                return;
            }
            if (comprueba_longitud_cadena(funcion_valores_cuartohoraria, NUMERO_MAXIMO_CARACTERES_FUNCION_VALORES_SENSOR_PROCESADO) == false) {
                $('#funcion_valores_cuartohoraria_procesado_sensor').addClass('data-check-failed');
                return;
            }
            funcion_valores_horaria = formatea_funcion_valores(funcion_valores_horaria);
            funcion_valores_cuartohoraria = formatea_funcion_valores(funcion_valores_cuartohoraria);
            parametros_tipo = [
                clase_sensor_procesado,
                funcion_valores_horaria,
                misma_funcion_valores_cuartohoraria,
                funcion_valores_cuartohoraria].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            var cadena_valores_prueba_funcion_valores = $("#valores_prueba_funcion_valores_procesado_sensor").val();
            if (cadena_valores_prueba_funcion_valores != "") {
                var valores_prueba_funcion_valores = cadena_valores_prueba_funcion_valores.split(",");
                for (var i = 0; i < valores_prueba_funcion_valores.length; i++) {
                    var valor_prueba_funcion_valores = (valores_prueba_funcion_valores[i]).trim();
                    if (PATRON_NUMERO_REAL.test(valor_prueba_funcion_valores) == false) {
                        jAlert(TLNT.Idiomas._('Los valores de prueba deben ser numéricos'));
                        return;
                    }
                }
            }
            parametros_auxiliares = cadena_valores_prueba_funcion_valores;
            break;
        }
        case TIPO_SENSOR_EXTERNO: {
            var clase_sensor_externo = $("#clase_externo_sensor").val();
            var resultado_opciones_generales = dame_cadena_opciones_generales_sensor_externo(clase_sensor_externo, numero_valores_clase_sensor, tipo_valores);
            if (resultado_opciones_generales.parametros_correctos == false) {
                if (resultado_opciones_generales.descripcion_error != "") {
                    jAlert(resultado_opciones_generales.descripcion_error);
                }
                return;
            }
            var resultado_opciones_valores = dame_cadena_opciones_valores_sensor_externo(clase_sensor_externo, numero_valores_clase_sensor);
            if (resultado_opciones_valores.parametros_correctos == false) {
                if (resultado_opciones_valores.descripcion_error != "") {
                    jAlert(resultado_opciones_valores.descripcion_error);
                }
                return;
            }
            var opciones_generales_externo_sensor = resultado_opciones_generales.cadena_opciones_generales;
            var opciones_valores_externo_sensor = resultado_opciones_valores.cadena_opciones_valores;
            var opciones_datadis = resultado_opciones_generales.cadena_parametros_datadis;
            parametros_tipo = [
                $("#id_externo_sensor").val(),
                clase_sensor_externo,
                opciones_generales_externo_sensor,
                opciones_valores_externo_sensor,
                opciones_datadis].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
    }

    // Calibración de valores
    var resultado_calibracion_valores = dame_cadena_calibracion_valores_sensor(tipo, numero_valores_clase_sensor);
    if (resultado_calibracion_valores.parametros_correctos == false) {
        if (resultado_calibracion_valores.descripcion_error != "") {
            jAlert(resultado_calibracion_valores.descripcion_error);
        }
        return;
    }
    var calibracion = resultado_calibracion_valores.cadena_calibracion_valores;

    // Se añade o modifica el sensor
    if (anyadir_nodo == true) {
        // Parámetros de la adición del sensor
        var parametros_sensor = [];
        parametros_sensor["nombre"] = nombre;
        parametros_sensor["descripcion"] = descripcion;
        parametros_sensor["id_localizacion"] = id_localizacion;
        parametros_sensor["visible_localizaciones_hijas"] = visible_localizaciones_hijas;
        parametros_sensor["clase"] = clase;
        parametros_sensor["parametros_clase"] = parametros_clase;
        parametros_sensor["tipo"] = tipo;
        parametros_sensor["parametros_tipo"] = parametros_tipo;
        parametros_sensor["calibracion"] = calibracion;
        parametros_sensor["tipo_valores"] = tipo_valores;
        parametros_sensor["cambio_valores_puntuales"] = cambio_valores_puntuales;
        parametros_sensor["incrementos_tiempo_real_horarios"] = incrementos_tiempo_real_horarios;
        parametros_sensor["incrementos_negativos_validos"] = incrementos_negativos_validos;
        parametros_sensor["guardar_valores_base_datos"] = guardar_valores_base_datos;
        parametros_sensor["notificar_todos_eventos"] = notificar_todos_eventos;
        parametros_sensor["granularidad_cuartohoraria"] = granularidad_cuartohoraria;
        parametros_sensor["id_grupo"] = id_grupo;
        parametros_sensor["frecuencia_muestreo"] = frecuencia_muestreo;
        parametros_sensor["frecuencia_envio"] = frecuencia_envio;
        parametros_sensor["mostrar_en_mapa"] = mostrar_en_mapa;
        parametros_sensor["latitud_mapa"] = latitud_mapa;
        parametros_sensor["longitud_mapa"] = longitud_mapa;
        parametros_sensor["zoom_mapa"] = zoom_mapa;
        // Parámetros extra
        parametros_sensor["id_sensor_anterior"] = id_nodo;

        // Se comprueba la información de valores pendientes de borrado del sensor
        var info_valores_pendientes_borrado_sensor = dame_info_valores_pendientes_borrado_sensor(nombre);
        if (info_valores_pendientes_borrado_sensor == null) {
            return;
        }
        var hay_valores_pendientes_borrado = info_valores_pendientes_borrado_sensor.hay_valores_pendientes_borrado;
        if (hay_valores_pendientes_borrado == true) {
            var clase_sensor_valores_pendientes_borrado = info_valores_pendientes_borrado_sensor.clase_sensor_valores_pendientes_borrado;
            var hora_valores_pendientes_borrado = info_valores_pendientes_borrado_sensor.hora_valores_pendientes_borrado;
            if (clase_sensor_valores_pendientes_borrado == clase) {
                var mensaje_confirmacion = TLNT.Idiomas._("Hay valores pendientes de borrado de un sensor con el mismo nombre de la misma clase. ¿Desea mantenerlos?") +
                    " (" + TLNT.Idiomas._("fecha de borrado") + ": " + hora_valores_pendientes_borrado + ")";
                jConfirmAcceptCancelAlert(mensaje_confirmacion, TLNT.Idiomas._("Pregunta"), function(res) {
                    if (res == true) {
                        // Se elimina la información de valores pendientes de borrado del sensor
                        var info_eliminada = elimina_info_valores_pendientes_borrado_sensor(nombre);
                        if (info_eliminada == false) {
                            return;
                        }

                        // Se añade el sensor
                        anyade_sensor(
                            id_nodo,
                            parametros_sensor);
                    }
                    else {
                        TLNT.Navegacion.muestra_barra_progreso();

                        // Se borran los valores y se añade el sensor
                        // (había valores pendientes de borrado de un sensor con el mismo nombre y misma clase y se ha confirmado la eliminación)
                        borra_valores_sensor_pendientes_borrado_anyade_modifica_sensor(
                            nombre,
                            clase,
                            info_valores_pendientes_borrado_sensor,
                            OPERACION_ADICION,
                            id_nodo,
                            parametros_sensor,
                            null);
                    }
                });
            }
            else {
                var mensaje_confirmacion = TLNT.Idiomas._("Hay valores pendientes de borrado de un sensor con el mismo nombre de diferente clase. ¿Desea continuar y borrarlos?") +
                    " (" + TLNT.Idiomas._("fecha de borrado") + ": " + hora_valores_pendientes_borrado + ")";
                jConfirmAcceptCancelAlert(mensaje_confirmacion, TLNT.Idiomas._("Pregunta"), function(res) {
                    if (res == true) {
                        // Se borran los valores y se añade el sensor
                        // (había valores pendientes de borrado de un sensor con el mismo nombre y diferente clase y se ha confirmado la eliminación)
                        borra_valores_sensor_pendientes_borrado_anyade_modifica_sensor(
                            nombre,
                            clase_sensor_valores_pendientes_borrado,
                            info_valores_pendientes_borrado_sensor,
                            OPERACION_ADICION,
                            id_nodo,
                            parametros_sensor,
                            null);
                    }
                    else {
                        return;
                    }
                });
            }
        }
        else {
            // Se añade el sensor
            anyade_sensor(
                id_nodo,
                parametros_sensor);
        }
    }
    else {
        // Parámetros de la ventana
        var nombre_anterior = $("#parametros_ventana_anyadir_modificar_sensor").attr("nombre");
        var id_localizacion_anterior = $("#parametros_ventana_anyadir_modificar_sensor").attr("id_localizacion");
        var visible_localizaciones_hijas_anterior = $("#parametros_ventana_anyadir_modificar_sensor").attr("visible_localizaciones_hijas");
        var latitud_mapa_anterior = $("#parametros_ventana_anyadir_modificar_sensor").attr("latitud_mapa");
        var longitud_mapa_anterior = $("#parametros_ventana_anyadir_modificar_sensor").attr("longitud_mapa");

        // Parámetros de la modificación del sensor
        var parametros_sensor = [];
        parametros_sensor["nombre"] = nombre;
        parametros_sensor["descripcion"] = descripcion;
        parametros_sensor["id_localizacion"] = id_localizacion;
        parametros_sensor["visible_localizaciones_hijas"] = visible_localizaciones_hijas;
        parametros_sensor["clase"] = clase;
        parametros_sensor["parametros_clase"] = parametros_clase;
        parametros_sensor["tipo"] = tipo;
        parametros_sensor["parametros_tipo"] = parametros_tipo;
        parametros_sensor["calibracion"] = calibracion;
        parametros_sensor["tipo_valores"] = tipo_valores;
        parametros_sensor["cambio_valores_puntuales"] = cambio_valores_puntuales;
        parametros_sensor["incrementos_tiempo_real_horarios"] = incrementos_tiempo_real_horarios;
        parametros_sensor["incrementos_negativos_validos"] = incrementos_negativos_validos;
        parametros_sensor["guardar_valores_base_datos"] = guardar_valores_base_datos;
        parametros_sensor["notificar_todos_eventos"] = notificar_todos_eventos;
        parametros_sensor["granularidad_cuartohoraria"] = granularidad_cuartohoraria;
        parametros_sensor["id_grupo"] = id_grupo;
        parametros_sensor["frecuencia_muestreo"] = frecuencia_muestreo;
        parametros_sensor["frecuencia_envio"] = frecuencia_envio;
        parametros_sensor["mostrar_en_mapa"] = mostrar_en_mapa;
        parametros_sensor["latitud_mapa"] = latitud_mapa;
        parametros_sensor["longitud_mapa"] = longitud_mapa;
        parametros_sensor["zoom_mapa"] = zoom_mapa;
        // Parámetros extra
        parametros_sensor["id_localizacion_anterior"] = id_localizacion_anterior;
        parametros_sensor["visible_localizaciones_hijas_anterior"] = visible_localizaciones_hijas_anterior;
        parametros_sensor["latitud_mapa_anterior"] = latitud_mapa_anterior;
        parametros_sensor["longitud_mapa_anterior"] = longitud_mapa_anterior;

        // Si ha cambiado el nombre, se comprueba la información de valores pendientes de borrado del sensor (con el nuevo nombre)
        if (nombre != nombre_anterior) {
            // Se procesa la información de valores pendientes de borrado del sensor
            var info_valores_pendientes_borrado_sensor = dame_info_valores_pendientes_borrado_sensor(nombre);
            if (info_valores_pendientes_borrado_sensor == null) {
                return;
            }
            var hay_valores_pendientes_borrado = info_valores_pendientes_borrado_sensor.hay_valores_pendientes_borrado;
            if (hay_valores_pendientes_borrado == true) {
                var hora_valores_pendientes_borrado = info_valores_pendientes_borrado_sensor.hora_valores_pendientes_borrado;
                var mensaje_confirmacion = TLNT.Idiomas._("Hay valores pendientes de borrado de un sensor con el mismo nombre. ¿Desea continuar y borrarlos?") +
                    " (" + TLNT.Idiomas._("fecha de borrado") + ": " + hora_valores_pendientes_borrado + ")";
                jConfirmAcceptCancelAlert(mensaje_confirmacion, TLNT.Idiomas._("Pregunta"), function(res) {
                    if (res == true) {
                        // Se borran los valores y se modifica el sensor
                        // (había valores pendientes de borrado de un sensor con el mismo nombre y se ha confirmado la eliminación)
                        borra_valores_sensor_pendientes_borrado_anyade_modifica_sensor(
                            nombre,
                            clase,
                            info_valores_pendientes_borrado_sensor,
                            OPERACION_MODIFICACION,
                            id_nodo,
                            parametros_sensor,
                            parametros_auxiliares);
                    }
                    else {
                        return;
                    }
                });
            }
            else {
                // Se modifica el sensor
                modifica_sensor(
                    id_nodo,
                    parametros_sensor,
                    parametros_auxiliares);
            }
        }
        else {
            // Se modifica el sensor
            modifica_sensor(
                id_nodo,
                parametros_sensor,
                parametros_auxiliares);
        }
    }
}


function dame_info_valores_pendientes_borrado_sensor(nombre_sensor) {
    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("nombre_sensor", nombre_sensor);

    // Llamada 'ajax' POST
    var info_valores_pendientes_borrado_sensor = null;
    $.ajax({
        url: "./src/lib/modulos/Nodos/administracion/dame_info_valores_pendientes_borrado_sensor.php",
        type: "POST",
        async: false,
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            var resultado = dame_resultado_ejecucion_script_php_json(result);
            if (resultado == null) {
                return;
            }

            info_valores_pendientes_borrado_sensor = resultado;
        }
    });
    return (info_valores_pendientes_borrado_sensor);
}


function elimina_info_valores_pendientes_borrado_sensor(nombre_sensor) {
    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("nombre_sensor", nombre_sensor);

    // Llamada 'ajax' POST
    var info_eliminada = false;
    $.ajax({
        url: "./src/lib/modulos/Nodos/administracion/elimina_info_valores_pendientes_borrado_sensor.php",
        type: "POST",
        async: false,
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            var resultado = dame_resultado_ejecucion_script_php_json(result);
            if (resultado == null) {
                return;
            }

            info_eliminada = true;
        }
    });
    return (info_eliminada);
}


function borra_valores_sensor_pendientes_borrado_anyade_modifica_sensor(
    nombre_sensor,
    clase_sensor,
    info_valores_pendientes_borrado_sensor,
    operacion_sensor,
    id_sensor,
    parametros_sensor,
    parametros_auxiliares) {
    // Información del sensor eliminado necesaria para eliminar sus valores pendientes de borrado
    var clase_sensor_valores_pendientes_borrado = info_valores_pendientes_borrado_sensor.clase_sensor_valores_pendientes_borrado;
    var tipo_valores_sensor_valores_pendientes_borrado = info_valores_pendientes_borrado_sensor.tipo_valores_sensor_valores_pendientes_borrado;
    var incrementos_tiempo_real_horarios_sensor_valores_pendientes_borrado = info_valores_pendientes_borrado_sensor.incrementos_tiempo_real_horarios_sensor_valores_pendientes_borrado;

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_sensor", ID_NINGUNO);
    datos_formulario.append("nombre_sensor", nombre_sensor);
    datos_formulario.append("clase_sensor", clase_sensor_valores_pendientes_borrado);
    datos_formulario.append("tipo_sensor", TIPO_NINGUNO);
    datos_formulario.append("borrado_valores_pendientes_borrado", VALOR_SI);
    datos_formulario.append("fecha_hora_inicio", "");
    datos_formulario.append("fecha_hora_fin", "");
    datos_formulario.append("borrar_valores_tiempo_real", VALOR_SI);

    // Parámetros sólo si no hay fechas de valores
    datos_formulario.append("tipo_valores", tipo_valores_sensor_valores_pendientes_borrado);
    datos_formulario.append("incrementos_tiempo_real_horarios", incrementos_tiempo_real_horarios_sensor_valores_pendientes_borrado);

    // Llamada 'ajax' POST
    $.ajax({
        url: "./src/modulos/ModulosWeb/ModuloSensores/borra_valores_sensor.php",
        type: "POST",
        data: datos_formulario,
        processData: false,
        contentType: false,
        timeout: SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX_SEGUNDO_PLANO * 1000,
        success: function(result) {
            var resultado_borrado_valores_sensor = dame_resultado_ejecucion_script_php_json(result);
            if (resultado_borrado_valores_sensor == null) {
                return;
            }

            // Se elimina la información de valores pendientes de borrado del sensor
            var info_eliminada = elimina_info_valores_pendientes_borrado_sensor(nombre_sensor);
            if (info_eliminada == false) {
                return;
            }

            // Se añade o modifica el sensor
            switch (operacion_sensor) {
                case OPERACION_ADICION: {
                    anyade_sensor(
                        id_sensor,
                        parametros_sensor);
                    break;
                }
                case OPERACION_MODIFICACION: {
                    modifica_sensor(
                        id_sensor,
                        parametros_sensor,
                        parametros_auxiliares);
                    break;
                }
            }
        },
        error: function(request, status, err) {
            if (status == "timeout") {
                error_ajax_capturado = true;

                jInfo(TLNT.Idiomas._("El borrado de valores se está realizado en segundo plano"));
            }
        }
    });
}


function anyade_sensor(id_sensor, parametros_sensor) {
    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_sensor", id_sensor);
    datos_formulario.append("nombre", parametros_sensor["nombre"]);
    datos_formulario.append("descripcion", parametros_sensor["descripcion"]);
    datos_formulario.append("id_localizacion", parametros_sensor["id_localizacion"]);
    datos_formulario.append("visible_localizaciones_hijas", parametros_sensor["visible_localizaciones_hijas"]);
    datos_formulario.append("clase", parametros_sensor["clase"]);
    datos_formulario.append("parametros_clase", parametros_sensor["parametros_clase"]);
    datos_formulario.append("tipo", parametros_sensor["tipo"]);
    datos_formulario.append("parametros_tipo", parametros_sensor["parametros_tipo"]);
    datos_formulario.append("calibracion", parametros_sensor["calibracion"]);
    datos_formulario.append("tipo_valores", parametros_sensor["tipo_valores"]);
    datos_formulario.append("cambio_valores_puntuales", parametros_sensor["cambio_valores_puntuales"]);
    datos_formulario.append("incrementos_tiempo_real_horarios", parametros_sensor["incrementos_tiempo_real_horarios"]);
    datos_formulario.append("incrementos_negativos_validos", parametros_sensor["incrementos_negativos_validos"]);
    datos_formulario.append("guardar_valores_base_datos", parametros_sensor["guardar_valores_base_datos"]);
    datos_formulario.append("notificar_todos_eventos", parametros_sensor["notificar_todos_eventos"]);
    datos_formulario.append("granularidad_cuartohoraria", parametros_sensor["granularidad_cuartohoraria"]);
    datos_formulario.append("id_grupo", parametros_sensor["id_grupo"]);
    datos_formulario.append("frecuencia_muestreo", parametros_sensor["frecuencia_muestreo"]);
    datos_formulario.append("frecuencia_envio", parametros_sensor["frecuencia_envio"]);
    datos_formulario.append("mostrar_en_mapa", parametros_sensor["mostrar_en_mapa"]);
    datos_formulario.append("latitud_mapa", parametros_sensor["latitud_mapa"]);
    datos_formulario.append("longitud_mapa", parametros_sensor["longitud_mapa"]);
    datos_formulario.append("zoom_mapa", parametros_sensor["zoom_mapa"]);
    datos_formulario.append("id_sensor_anterior", parametros_sensor["id_sensor_anterior"]);

    // Llamada 'ajax' POST
    $.ajax({
        url: "./src/lib/modulos/Nodos/administracion/anyade_sensor.php",
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

            switch (resultado.tipo_mensaje) {
                case TIPO_MENSAJE_INFORMACION: {
                    jInfo(resultado.msg);
                    break;
                }
                case TIPO_MENSAJE_AVISO: {
                    jAlert(resultado.msg);
                    break;
                }
            }
            actualiza_tabla_nodos(TIPO_NODO_SENSOR);
            actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);

            // Si el sensor era externo, se suma uno al identificador de sensor externo
            // (para no tener que modificarlo manualmente si se añade más de un sensor externo)
            if (parametros_sensor["tipo"] == TIPO_SENSOR_EXTERNO) {
                var id_externo_anterior = parseInt($("#id_externo_sensor").val());
                var id_externo_siguiente = id_externo_anterior + 1;
                $("#id_externo_sensor").val(id_externo_siguiente);
            }
        },
        error: function(request, status, err) {
            if (status == "timeout") {
                error_ajax_capturado = true;

                jInfo(TLNT.Idiomas._("La adición del sensor se está realizado en segundo plano"));
                refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor);
                actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);
                $('#ventana_modal').modal('hide');
            }
        }
    });
}


function modifica_sensor(
    id_sensor,
    parametros_sensor,
    parametros_auxiliares) {
    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_sensor", id_sensor);
    datos_formulario.append("nombre", parametros_sensor["nombre"]);
    datos_formulario.append("descripcion", parametros_sensor["descripcion"]);
    datos_formulario.append("id_localizacion", parametros_sensor["id_localizacion"]);
    datos_formulario.append("visible_localizaciones_hijas", parametros_sensor["visible_localizaciones_hijas"]);
    datos_formulario.append("clase", parametros_sensor["clase"]);
    datos_formulario.append("parametros_clase", parametros_sensor["parametros_clase"]);
    datos_formulario.append("tipo", parametros_sensor["tipo"]);
    datos_formulario.append("parametros_tipo", parametros_sensor["parametros_tipo"]);
    datos_formulario.append("calibracion", parametros_sensor["calibracion"]);
    datos_formulario.append("tipo_valores", parametros_sensor["tipo_valores"]);
    datos_formulario.append("cambio_valores_puntuales", parametros_sensor["cambio_valores_puntuales"]);
    datos_formulario.append("incrementos_tiempo_real_horarios", parametros_sensor["incrementos_tiempo_real_horarios"]);
    datos_formulario.append("incrementos_negativos_validos", parametros_sensor["incrementos_negativos_validos"]);
    datos_formulario.append("guardar_valores_base_datos", parametros_sensor["guardar_valores_base_datos"]);
    datos_formulario.append("notificar_todos_eventos", parametros_sensor["notificar_todos_eventos"]);
    datos_formulario.append("granularidad_cuartohoraria", parametros_sensor["granularidad_cuartohoraria"]);
    datos_formulario.append("id_grupo", parametros_sensor["id_grupo"]);
    datos_formulario.append("frecuencia_muestreo", parametros_sensor["frecuencia_muestreo"]);
    datos_formulario.append("frecuencia_envio", parametros_sensor["frecuencia_envio"]);
    datos_formulario.append("mostrar_en_mapa", parametros_sensor["mostrar_en_mapa"]);
    datos_formulario.append("latitud_mapa", parametros_sensor["latitud_mapa"]);
    datos_formulario.append("longitud_mapa", parametros_sensor["longitud_mapa"]);
    datos_formulario.append("zoom_mapa", parametros_sensor["zoom_mapa"]);

    // Parámetros auxiliares
    datos_formulario.append("parametros_auxiliares", parametros_auxiliares);

    // Llamada 'ajax' POST
    $.ajax({
        url: "./src/lib/modulos/Nodos/administracion/modifica_sensor.php",
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

            switch (resultado.tipo_mensaje) {
                case TIPO_MENSAJE_INFORMACION: {
                    jInfo(resultado.msg);
                    break;
                }
                case TIPO_MENSAJE_AVISO: {
                    jAlert(resultado.msg);
                    break;
                }
            }
            if ((parametros_sensor["id_localizacion_anterior"] != parametros_sensor["id_localizacion"]) ||
                (parametros_sensor["visible_localizaciones_hijas_anterior"] != parametros_sensor["visible_localizaciones_hijas"])) {
                actualiza_tabla_nodos(TIPO_NODO_SENSOR);
            }
            else {
                refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor);

                // Se refresca también las filas de sensores asociados (si es necesario)
                switch (parametros_sensor["clase"]) {
                    case CLASE_SENSOR_COMPRA_ENERGIA: {
                        var parametros_clase = parametros_sensor["parametros_clase"].split(SEPARADOR_PARAMETROS_COMPUESTOS);
                        var id_sensor_asociado = parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];
                        refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor_asociado);
                        break;
                    }
                }
            }
            actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);

            // Si no se han modificado la latitud o longitud se cierra la ventana
            // (si se han modificado no se cierra para poder "afinar" la localización en el mapa)
            var mantener_ventana_modal_abierta = ((parametros_sensor["latitud_mapa_anterior"] != parametros_sensor["latitud_mapa"]) ||
                (parametros_sensor["longitud_mapa_anterior"] != parametros_sensor["longitud_mapa"]));

            // Si se mantiene abierta la ventana modal, se establecen los parámetros actuales de la ventana
            if (mantener_ventana_modal_abierta == true) {
                $("#parametros_ventana_anyadir_modificar_sensor").attr("nombre", parametros_sensor["nombre"]);
                $("#parametros_ventana_anyadir_modificar_sensor").attr("id_localizacion", parametros_sensor["id_localizacion"]);
                $("#parametros_ventana_anyadir_modificar_sensor").attr("visible_localizaciones_hijas", parametros_sensor["visible_localizaciones_hijas"]);
                $("#parametros_ventana_anyadir_modificar_sensor").attr("latitud_mapa", parametros_sensor["latitud_mapa"]);
                $("#parametros_ventana_anyadir_modificar_sensor").attr("longitud_mapa", parametros_sensor["longitud_mapa"]);
            }
            else {
                $('#ventana_modal').modal('hide');
            }
        },
        error: function(request, status, err) {
            if (status == "timeout") {
                error_ajax_capturado = true;

                jInfo(TLNT.Idiomas._("La modificación del sensor se está realizado en segundo plano"));
                refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor);
                actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);
                $('#ventana_modal').modal('hide');
            }
        }
    });
}


function anyade_modifica_grupo_sensores(anyadir_nodo, id_nodo) {
    // Comprobación de clase seleccionada
    var clase = $('#clase_grupo_sensores').val();
    if (clase == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Nombre y descripcion
    var nombre = $('#nombre_grupo_sensores').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_grupo_sensores').addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_grupo_sensores').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $('#descripcion_grupo_sensores').addClass('data-check-failed');
        return;
    }

    // Localización
    var id_localizacion = $('#id_localizacion_grupo_sensores').val();

    // Se añade o modifica el grupo de sensores
    if (anyadir_nodo == true) {
        $.post("./src/lib/modulos/Nodos/administracion/anyade_grupo_sensores.php", {
            nombre: nombre,
            descripcion: descripcion,
            id_localizacion: id_localizacion,
            clase: clase
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_lista_grupos_nodos_filtro(TIPO_NODO_SENSOR);
            actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);
        });
    }
    else {
        $.post("./src/lib/modulos/Nodos/administracion/modifica_grupo_sensores.php", {
            id_grupo_sensores: id_nodo,
            nombre: nombre,
            descripcion: descripcion,
            id_localizacion: id_localizacion,
            clase: clase
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_lista_grupos_nodos_filtro(TIPO_NODO_SENSOR);
            actualiza_tabla_nodos(TIPO_NODO_SENSOR);
            refresca_tabla_nodo(TIPO_NODO_GRUPO_SENSORES, id_nodo);
            $('#ventana_modal').modal('hide');
        });
    }
}


function anyade_modifica_actuador(anyadir_nodo, id_nodo) {
    // Comprobación de clase, tipo y tipos de valores seleccionados
    var clase = $('#clase_actuador').val();
    if (clase == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }
    var tipo = $('#tipo_actuador').val();
    if (tipo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo seleccionado'));
        return;
    }
    var clase_interfaz = $('#clase_interfaz_actuador').val();
    if (clase_interfaz == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase de interfaz seleccionada'));
        return;
    }

    // Nombre y descripción
    var nombre = $('#nombre_actuador').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_actuador').addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_actuador').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $('#descripcion_actuador').addClass('data-check-failed');
        return;
    }

    // Localización y visible por localizaciones hijas
    var id_localizacion = $('#id_localizacion_actuador').val();
    var visible_localizaciones_hijas = $('#visible_localizaciones_hijas_actuador').val();

    // Grupo de actuadores
    var id_grupo = $('#id_grupo_actuador').val();

    // Programación
    var id_programacion = $('#id_programacion_actuador').val();

    // Posición en mapa
    var mostrar_en_mapa = $('#mostrar_en_mapa').val();
    var latitud_mapa = $('#latitud_mapa').val();
    var longitud_mapa = $('#longitud_mapa').val();
    var zoom_mapa = $('#zoom_mapa').val();

    // Se recupera el número de valores de la clase del actuador
    var numero_valores_clase_actuador = dame_numero_valores_clase_actuador(clase);

    // Parámetros de la clase de actuador
    var parametros_clase = "";
    switch (clase) {
        case CLASE_ACTUADOR_GENERICA: {
            parametros_clase = $('#icono_mapa_actuador').val();
            break
        }
    }

    // Parámetros del tipo de actuador
    var parametros_tipo = "";
    var resultado_ubicacion_interfaz = dame_cadena_ubicacion_interfaz_clase_interfaz_actuador(tipo, clase_interfaz);
    if (resultado_ubicacion_interfaz.parametros_correctos == false) {
        if (resultado_ubicacion_interfaz.descripcion_error != "") {
            jAlert(resultado_ubicacion_interfaz.descripcion_error);
        }
        return;
    }
    var resultado_opciones_interfaz = dame_cadena_opciones_interfaz_clase_interfaz_actuador(tipo, clase_interfaz, numero_valores_clase_actuador);
    if (resultado_opciones_interfaz.parametros_correctos == false) {
        if (resultado_opciones_interfaz.descripcion_error != "") {
            jAlert(resultado_opciones_interfaz.descripcion_error);
        }
        return;
    }
    var ubicacion_interfaz = resultado_ubicacion_interfaz.cadena_ubicacion_interfaz;
    var opciones_interfaz = resultado_opciones_interfaz.cadena_opciones_interfaz;
    switch (tipo) {
        case TIPO_ACTUADOR_HARDWARE: {
            var id_axon = $("#id_axon_actuador").val();
            if (id_axon == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._('No hay axón seleccionado'));
                return;
            }

            parametros_tipo = [
                id_axon,
                clase_interfaz,
                ubicacion_interfaz,
                opciones_interfaz].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ACTUADOR_SOFTWARE: {
            parametros_tipo = [
                clase_interfaz,
                ubicacion_interfaz,
                opciones_interfaz].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
    }

    // Calibración de valores
    var resultado_calibracion_valores = dame_cadena_calibracion_valores_actuador(numero_valores_clase_actuador);
    if (resultado_calibracion_valores.parametros_correctos == false) {
        if (resultado_calibracion_valores.descripcion_error != "") {
            jAlert(resultado_calibracion_valores.descripcion_error);
        }
        return;
    }
    var calibracion = resultado_calibracion_valores.cadena_calibracion_valores;

    // Se añade o modifica el actuador
    if (anyadir_nodo == true) {
        $.post("./src/lib/modulos/Nodos/administracion/anyade_actuador.php", {
            nombre: nombre,
            descripcion: descripcion,
            id_localizacion: id_localizacion,
            visible_localizaciones_hijas: visible_localizaciones_hijas,
            clase: clase,
            parametros_clase: parametros_clase,
            tipo: tipo,
            parametros_tipo: parametros_tipo,
            calibracion: calibracion,
            id_grupo: id_grupo,
            id_programacion: id_programacion,
            mostrar_en_mapa: mostrar_en_mapa,
            latitud_mapa: latitud_mapa,
            longitud_mapa: longitud_mapa,
            zoom_mapa: zoom_mapa
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            switch (resultado.tipo_mensaje) {
                case TIPO_MENSAJE_INFORMACION: {
                    jInfo(resultado.msg);
                    break;
                }
                case TIPO_MENSAJE_AVISO: {
                    jAlert(resultado.msg);
                    break;
                }
            }
            actualiza_tabla_nodos(TIPO_NODO_ACTUADOR);
            actualiza_tabla_nodos(TIPO_NODO_GRUPO_ACTUADORES);

            // Se envía la última acción al actuador (de la programacion o del grupo)
            if (id_programacion != ID_NINGUNO) {
                envia_ultima_accion_actuador(resultado.id_nodo, ORIGEN_ULTIMA_ACCION_PROGRAMACION, id_programacion);
            }
            else {
                if (id_grupo != ID_NINGUNO) {
                    envia_ultima_accion_actuador(resultado.id_nodo, ORIGEN_ULTIMA_ACCION_GRUPO_ACTUADORES, id_grupo);
                }
            }
        });
    }
    else {
        // Parámetros de la ventana
        var id_localizacion_anterior = $("#parametros_ventana_anyadir_modificar_actuador").attr("id_localizacion");
        var visible_localizaciones_hijas_anterior = $("#parametros_ventana_anyadir_modificar_actuador").attr("visible_localizaciones_hijas");
        var latitud_mapa_anterior = $("#parametros_ventana_anyadir_modificar_actuador").attr("latitud_mapa");
        var longitud_mapa_anterior = $("#parametros_ventana_anyadir_modificar_actuador").attr("longitud_mapa");

        // Se modifica el actuador
        $.post("./src/lib/modulos/Nodos/administracion/modifica_actuador.php", {
            id_actuador: id_nodo,
            nombre: nombre,
            descripcion: descripcion,
            id_localizacion: id_localizacion,
            visible_localizaciones_hijas: visible_localizaciones_hijas,
            clase: clase,
            parametros_clase: parametros_clase,
            tipo: tipo,
            parametros_tipo: parametros_tipo,
            calibracion: calibracion,
            id_grupo: id_grupo,
            id_programacion: id_programacion,
            mostrar_en_mapa: mostrar_en_mapa,
            latitud_mapa: latitud_mapa,
            longitud_mapa: longitud_mapa,
            zoom_mapa: zoom_mapa
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            switch (resultado.tipo_mensaje) {
                case TIPO_MENSAJE_INFORMACION: {
                    jInfo(resultado.msg);
                    break;
                }
                case TIPO_MENSAJE_AVISO: {
                    jAlert(resultado.msg);
                    break;
                }
            }
            if ((id_localizacion_anterior != id_localizacion) ||
                (visible_localizaciones_hijas_anterior != visible_localizaciones_hijas)) {
                actualiza_tabla_nodos(TIPO_NODO_ACTUADOR);
            }
            else {
                refresca_tabla_nodo(TIPO_NODO_ACTUADOR, id_nodo);
            }
            actualiza_tabla_nodos(TIPO_NODO_GRUPO_ACTUADORES);

            // Se envía la última acción al actuador (de la programacion o del grupo)
            var id_programacion_anterior = $("#parametros_ventana_anyadir_modificar_actuador").attr("id_programacion");
            if ((id_programacion != ID_NINGUNO) && (id_programacion != id_programacion_anterior)) {
                envia_ultima_accion_actuador(id_nodo, ORIGEN_ULTIMA_ACCION_PROGRAMACION, id_programacion);
            }
            else {
                var id_grupo_anterior = $("#parametros_ventana_anyadir_modificar_actuador").attr("id_grupo");
                if ((id_grupo != ID_NINGUNO) && (id_grupo != id_grupo_anterior)) {
                    envia_ultima_accion_actuador(id_nodo, ORIGEN_ULTIMA_ACCION_GRUPO_ACTUADORES, id_grupo);
                }
            }

            // Si no se han modificado la latitud o longitud se cierra la ventana
            // (si se han modificado no se cierra para poder "afinar" la localización en el mapa)
            var mantener_ventana_modal_abierta = ((latitud_mapa_anterior != latitud_mapa) || (longitud_mapa_anterior != longitud_mapa));

            // Si se mantiene abierta la ventana modal, se establecen los parámetros actuales de la ventana
            if (mantener_ventana_modal_abierta == true) {
                $("#parametros_ventana_anyadir_modificar_actuador").attr("id_localizacion", id_localizacion);
                $("#parametros_ventana_anyadir_modificar_actuador").attr("visible_localizaciones_hijas", visible_localizaciones_hijas);
                $("#parametros_ventana_anyadir_modificar_actuador").attr("latitud_mapa", latitud_mapa);
                $("#parametros_ventana_anyadir_modificar_actuador").attr("longitud_mapa", longitud_mapa);
            }
            else {
                $('#ventana_modal').modal('hide');
            }
        });
    }
}


function anyade_modifica_grupo_actuadores(anyadir_nodo, id_nodo) {
    // Comprobación de clase seleccionada
    var clase = $('#clase_grupo_actuadores').val();
    if (clase == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Nombre y descripción
    var nombre = $('#nombre_grupo_actuadores').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_grupo_actuadores').addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_grupo_actuadores').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $('#descripcion_grupo_actuadores').addClass('data-check-failed');
        return;
    }

    // Localización
    var id_localizacion = $('#id_localizacion_grupo_actuadores').val();

    // Programación
    var id_programacion = $('#id_programacion_grupo_actuadores').val();

    // Se añade o modifica el grupo de actuadores
    if (anyadir_nodo == true) {
        $.post("./src/lib/modulos/Nodos/administracion/anyade_grupo_actuadores.php", {
            nombre: nombre,
            descripcion: descripcion,
            id_localizacion: id_localizacion,
            clase: clase,
            id_programacion: id_programacion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_lista_grupos_nodos_filtro(TIPO_NODO_ACTUADOR);
            actualiza_tabla_nodos(TIPO_NODO_ACTUADOR);
            actualiza_tabla_nodos(TIPO_NODO_GRUPO_ACTUADORES);

            // Se envía la última acción al grupo de actuadores (si hay programacion)
            if (id_programacion != ID_NINGUNO) {
                envia_ultima_accion_grupo_actuadores(resultado.id_nodo, id_programacion);
            }
        });
    }
    else {
        $.post("./src/lib/modulos/Nodos/administracion/modifica_grupo_actuadores.php", {
            id_grupo_actuadores: id_nodo,
            nombre: nombre,
            descripcion: descripcion,
            id_localizacion: id_localizacion,
            clase: clase,
            id_programacion: id_programacion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_lista_grupos_nodos_filtro(TIPO_NODO_ACTUADOR);
            actualiza_tabla_nodos(TIPO_NODO_ACTUADOR);
            refresca_tabla_nodo(TIPO_NODO_GRUPO_ACTUADORES, id_nodo);

            // Se envía la última acción al grupo de actuadores (si ha cambiado la programacion)
            var id_programacion_anterior = $("#parametros_ventana_anyadir_modificar_grupo_actuadores").attr("id_programacion");
            if ((id_programacion != ID_NINGUNO) && (id_programacion != id_programacion_anterior)) {
                envia_ultima_accion_grupo_actuadores(id_nodo, id_programacion);
            }

            $('#ventana_modal').modal('hide');
        });
    }
}


function envia_ultima_accion_actuador(id_actuador, origen_ultima_accion, id_origen_ultima_accion) {
    $.post("./src/lib/modulos/Nodos/administracion/dame_ultima_accion.php", {
        origen: origen_ultima_accion,
        id_origen: id_origen_ultima_accion
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Si no hay última acción no hay nada que enviar
        if (resultado.contenido_ultima_accion == "") {
            return;
        }

        // Origen de la acción
        var origen_accion = null;
        switch (origen_ultima_accion) {
            case ORIGEN_ULTIMA_ACCION_PROGRAMACION: {
                origen_accion = ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_PROGRAMACION;
                break;
            }
            case ORIGEN_ULTIMA_ACCION_GRUPO_ACTUADORES: {
                origen_accion = ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_GRUPO_ACTUADORES;
                break;
            }
        }

        // Se envía la acción
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/envia_accion_actuador.php", {
            id_actuador: id_actuador,
            id_accion_predefinida: ID_NINGUNO,
            contenido_accion: resultado.contenido_ultima_accion,
            valor_accion: resultado.valor_ultima_accion,
            fecha_hora_accion: resultado.fecha_hora_ultima_accion,
            origen_accion: origen_accion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }
        });
    });
}


function envia_ultima_accion_grupo_actuadores(id_grupo_actuadores, id_programacion) {
    $.post("./src/lib/modulos/Nodos/administracion/dame_ultima_accion.php", {
        origen: ORIGEN_ULTIMA_ACCION_PROGRAMACION,
        id_origen: id_programacion
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Si no hay última acción no hay nada que enviar
        if (resultado.contenido_ultima_accion == "") {
            return;
        }

        // Origen de la acción
        var origen_accion = ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_PROGRAMACION;

        // Se envía la accion
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/envia_accion_grupo_actuadores.php", {
            id_grupo_actuadores: id_grupo_actuadores,
            id_accion_predefinida: ID_NINGUNO,
            contenido_accion: resultado.contenido_ultima_accion,
            valor_accion: resultado.valor_ultima_accion,
            fecha_hora_accion: resultado.fecha_hora_ultima_accion,
            origen_accion: origen_accion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }
        });
    });
}
