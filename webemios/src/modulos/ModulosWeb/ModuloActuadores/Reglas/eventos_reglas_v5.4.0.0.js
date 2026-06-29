//
// Funciones de reglas
//


// Botones de envío de acciones de herramientas de las reglas
function boton_actuadores_envia_accion_herramientas_reglas() {
	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/envia_accion_herramientas_reglas.php", {
		boton: this.id
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
	});
}


// Botones de envío de acciones de herramientas de las reglas
function boton_actuadores_envia_accion_herramientas_regla() {
    var params = this.id.split('__');
    var boton = params[0];
    var id_regla = params[1];

	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/envia_accion_herramientas_regla.php", {
		boton: boton,
		id_regla: id_regla
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		jInfo(resultado.msg);
	});
}


// Muestra la tabla de reglas aplicando el filtro
function boton_actuadores_filtro_reglas_tabla() {
    boton_actuadores_actualizar_tabla_reglas();
}


// Realiza el filtrado de histórico de reglas
function boton_actuadores_filtro_historico_reglas() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_actuadores_filtro_historico_reglas').val();
    var hora_inicio = $('#hora_inicio_actuadores_filtro_historico_reglas').val();
    var fecha_fin = $('#fecha_fin_actuadores_filtro_historico_reglas').val();
    var hora_fin = $('#hora_fin_actuadores_filtro_historico_reglas').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/dame_tabla_historico_reglas.php", {
        filtro: $('#filtro_actuadores_filtro_historico_reglas').val(),
		fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#tablaHistoricoReglas").html(resultado.html);
        if (resultado.limite_elementos_tabla_superado == true) {
            jAlert(TLNT.Idiomas._("Número máximo de históricos de reglas superado (se muestran los más recientes)"));
        }

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


function boton_actuadores_mostrar_ventana_anyadir_modificar_regla(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_regla = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/muestra_ventana_anyadir_modificar_regla.php", {
        id_regla: id_regla,
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


function boton_actuadores_eliminar_regla(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_regla = params[1];
    var nombre_regla = $(this).attr('nombre_regla');

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la regla?") + "\n(" + escapeHtml(nombre_regla) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/elimina_regla.php", {
                id_regla: id_regla
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_reglas();
			});
		}
	});
}


function boton_actuadores_anyadir_modificar_regla() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
	var anyadir_regla = $("#parametros_ventana_anyadir_modificar_regla").attr("anyadir_regla");
	var id_regla = $("#parametros_ventana_anyadir_modificar_regla").attr("id_regla");

    // Nombre y descripción
    var nombre = $('#nombre_regla').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_regla").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_regla').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_regla").addClass('data-check-failed');
        return;
    }

    // Tipo de regla y modo de activación
    var tipo = $('#tipo_regla').val();
    if (tipo == TIPO_NINGUNO) {
        var descripcion_error = TLNT.Idiomas._('No hay tipo seleccionado');
        jAlert(descripcion_error);
        return;
    }
    var modo_activacion = $('#modo_activacion_regla').val();

    // Número de días de caducidad de acciones
    var numero_dias_caducidad_acciones = $('#numero_dias_caducidad_acciones_regla').val();
    if (parseInt(numero_dias_caducidad_acciones) < 0) {
        var descripcion_error = TLNT.Idiomas._('El número de días de caducidad de acciones debe ser mayor o igual que 0');
        jAlert(descripcion_error);
        return;
    }

    // Se añade o modifica la regla
    if (anyadir_regla == true) {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/anyade_regla.php", {
            nombre: nombre,
            descripcion: descripcion,
            tipo: tipo,
            modo_activacion: modo_activacion,
            numero_dias_caducidad_acciones: numero_dias_caducidad_acciones,
            id_regla_anterior: id_regla
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_reglas();
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/modifica_regla.php", {
            id_regla: id_regla,
            nombre: nombre,
            descripcion: descripcion,
            tipo: tipo,
            modo_activacion: modo_activacion,
            numero_dias_caducidad_acciones: numero_dias_caducidad_acciones
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_regla_detalles(id_regla);
            $('#ventana_modal').modal('hide');
        });
    }
}


// Actualización de la tabla de reglas
function boton_actuadores_actualizar_tabla_reglas() {
    actualiza_tabla_reglas();
}


function actualiza_tabla_reglas() {
    var filtro = $('#filtro_actuadores_filtro_reglas_tabla').val();
    var habilitacion = $('#habilitacion_regla_actuadores_filtro_reglas_tabla').val();
    var activacion = $('#activacion_regla_actuadores_filtro_reglas_tabla').val();

	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/dame_tabla_reglas.php", {
		filtro: filtro,
        habilitacion: habilitacion,
        activacion: activacion,
        actualizacion_periodica_activada: actualizacion_periodica_reglas_activada
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaReglas').html(resultado.html);

        // Se actualiza la fecha de actualización de la tabla de reglas
        actualiza_fecha_actualizacion_tabla_reglas();

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Actualiza en el pie de la página la fecha de actualización de la tabla de reglas
function actualiza_fecha_actualizacion_tabla_reglas() {
    var fecha_actual = new Date();
    var cadena_fecha_actual = convierte_fecha_a_cadena(fecha_actual, formato_fecha_local_jquery_ui);
    cadena_fecha_actual += ", " + dame_cadena_hora(fecha_actual);
    var texto_actualizado_hora_actual = TLNT.Idiomas._("hora de actualización de tabla") + ": " + cadena_fecha_actual;
    actualiza_texto_pie_pagina(texto_actualizado_hora_actual);
}


// Actualización periódica de la tabla de reglas
function boton_actuadores_actualizacion_periodica_tabla_reglas() {
    inicia_actualizacion_periodica_tabla_reglas();
}


// Inicia la actualización periódica de la tabla de reglas
function inicia_actualizacion_periodica_tabla_reglas() {
    // Se activa o desactiva la actualización periódica de la tabla de reglas
    if (temporizador_actualizacion_pagina == null) {
        jPrompt(TLNT.Idiomas._("Intervalo de actualización periódica de reglas") + " (" + TLNT.Idiomas._("segundos") + ")",
            SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_REGLAS_DEFECTO,
            TLNT.Idiomas._("Pregunta"),
            function(valor) {
                if (valor != null) {
                    if ((isNaN(valor) == true) || (
                        (valor < MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_REGLAS_DEFECTO) ||
                        (valor > MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_REGLAS_DEFECTO))) {
                        var mensaje_aviso = TLNT.Idiomas._("Intervalo de actualización periódica de reglas no válido") +
                            " (" + MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_REGLAS_DEFECTO + " - " + MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_TABLA_REGLAS_DEFECTO + ")";
                        jAlert(mensaje_aviso, TLNT.Idiomas._("Aviso"), function(res) {
                            inicia_actualizacion_periodica_tabla_reglas();
                        });
                    }
                    else {
                        actualizacion_periodica_reglas_activada = true;
                        segundos_intervalo_actualizacion_tabla_reglas = valor;
                        temporizador_actualizacion_pagina = setTimeout(
                            expiracion_timeout_actualizacion_periodica_tabla_reglas,
                            segundos_intervalo_actualizacion_tabla_reglas * 1000);
                        jInfo(TLNT.Idiomas._("Actualización periódica de reglas activada"));

                        // Actualizar el icono de actualización periódica
                        $('#boton_actualizacion_periodica_tabla_reglas').removeClass("icon-play");
                        $('#boton_actualizacion_periodica_tabla_reglas').addClass("icon-pause");

                        // Se actualiza la tabla de reglas
                        actualiza_tabla_reglas();
                    }
                }
            }
        );
    }
    else {
        desactiva_actualizacion_periodica_tabla_reglas();
    }
}


// Expiración del timeout para actualización periódica de la tabla de reglas
function expiracion_timeout_actualizacion_periodica_tabla_reglas() {
    actualiza_tabla_reglas();
    temporizador_actualizacion_pagina = setTimeout(
        expiracion_timeout_actualizacion_periodica_tabla_reglas,
        segundos_intervalo_actualizacion_tabla_reglas * 1000);
}


// Desactiva la actualización periódica de la tabla de reglas
function desactiva_actualizacion_periodica_tabla_reglas() {
    if (temporizador_actualizacion_pagina != null) {
        actualizacion_periodica_reglas_activada = false;
        clearTimeout(temporizador_actualizacion_pagina);
        temporizador_actualizacion_pagina = null;

        jInfo(TLNT.Idiomas._("Actualización periódica de reglas desactivada"));

        $('.boton_actuadores_actualizacion_periodica_tabla_reglas').removeClass("icon-pause");
        $('.boton_actuadores_actualizacion_periodica_tabla_reglas').addClass("icon-play");
    }
}


function boton_actuadores_refrescar_tabla_regla() {
	var params = this.id.split('__');
	var id_regla = params[1];

    actualiza_tabla_regla_detalles(id_regla);
}


function actualiza_tabla_regla_detalles(id_regla) {
    // Se actualiza la información de la fila de la tabla
	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/dame_informacion_fila_tabla_regla.php", {
        id_regla: id_regla
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_datos = "datosRegla__" + id_regla;
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