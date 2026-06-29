//
// Funciones de programaciones de acciones de actuadores
//


// Muestra la tabla de programaciones aplicando el filtro
function boton_actuadores_filtro_programaciones_tabla() {
    boton_actuadores_actualizar_tabla_programaciones();
}


//
// Funciones de programaciones de los actuadores y los grupos
//


function boton_actuadores_mostrar_ventana_anyadir_modificar_programacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_programacion = params[1];
    var tipo_operacion_administracion = params[2];

	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/muestra_ventana_anyadir_modificar_programacion.php", {
        id_programacion: id_programacion,
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


function boton_actuadores_eliminar_programacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_programacion = params[1];
    var nombre_programacion = $(this).attr('nombre_programacion');

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la programación?") + "\n(" + escapeHtml(nombre_programacion) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/elimina_programacion.php", {
                id_programacion: id_programacion
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
				actualiza_tabla_programaciones();
			});
		}
	});
}


function boton_actuadores_anyadir_modificar_programacion() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_programacion = $("#parametros_ventana_anyadir_modificar_programacion").attr("anyadir_programacion");
	var id_programacion = $("#parametros_ventana_anyadir_modificar_programacion").attr("id_programacion");

    // Parámetros
    var nombre = $('#nombre_programacion').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_programacion").addClass('data-check-failed');
        return;
    }
    if (nombre == TLNT.Idiomas._("Ninguna")) {
        jAlert(TLNT.Idiomas._('No se puede utilizar este nombre en la programación'));
        return;
    }
    var clase_actuador = $('#clase_actuador_programacion').val();
    if (clase_actuador == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Se añade o modifica la programación
    if (anyadir_programacion == true) {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/anyade_programacion.php", {
            nombre: nombre,
            clase_actuador: clase_actuador,
            id_programacion_anterior: id_programacion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_programaciones();
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/modifica_programacion.php", {
            id_programacion: id_programacion,
            nombre: nombre,
            clase_actuador: clase_actuador
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_programaciones();
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_actuadores_actualizar_tabla_programaciones() {
	actualiza_tabla_programaciones();
}


function actualiza_tabla_programaciones() {
	var filtro = $('#filtro_actuadores_filtro_programaciones_tabla').val();
    var clase_actuador = $('#clase_actuador_actuadores_filtro_programaciones_tabla').val();

    $.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/dame_tabla_programaciones.php", {
		filtro: filtro,
        clase_actuador: clase_actuador
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		$('#tablaProgramaciones').html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


//
// Funciones de acciones de las programaciones
//


function boton_actuadores_mostrar_ventana_anyadir_modificar_accion_programacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_programacion = params[1];
	var id_accion = params[2];

	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/muestra_ventana_anyadir_modificar_accion.php", {
        id_programacion: id_programacion,
        id_accion: id_accion
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

        // Se modifica el estilo de la ventana modal (si es necesario)
        var clase_actuador = $("#parametros_ventana_anyadir_modificar_accion_programacion").attr("clase_actuador");
        switch (clase_actuador) {
            case CLASE_ACTUADOR_INTERRUPTOR: {
                $('#ventana_modal .modal-body').removeClass('mostrar-barra-desplazamiento-y');
                $('#ventana_modal .modal-body').addClass('mostrar-todos-elementos-y');
                break;
            }
        }

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales();
	});
}


function boton_actuadores_eliminar_accion_programacion(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_programacion = params[1];
	var id_accion = params[2];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la acción?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true)
		{
			$.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/elimina_accion.php", {
                id_programacion: id_programacion,
                id_accion: id_accion
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_tabla_acciones_programacion(id_programacion);
			});
		}
	});
}


function boton_actuadores_anyadir_modificar_accion_programacion() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_accion = $("#parametros_ventana_anyadir_modificar_accion_programacion").attr("anyadir_accion");
    var clase_actuador = $("#parametros_ventana_anyadir_modificar_accion_programacion").attr("clase_actuador");
    var id_programacion = $("#parametros_ventana_anyadir_modificar_accion_programacion").attr("id_programacion");
    var id_accion = $("#parametros_ventana_anyadir_modificar_accion_programacion").attr("id_accion");

    // Se recuperan los valores de los controles de la acción
    var valores_controles_accion = dame_valores_controles_accion(clase_actuador);
    if (valores_controles_accion == null) {
        return;
    }
    var id_accion_predefinida = valores_controles_accion["id_accion_predefinida"];
    var contenido = valores_controles_accion["contenido_accion"];
    var valor = valores_controles_accion["valor_accion"];

    // Se recupera el nombre de la acción (sólo si no son acciones predefinidas)
    var nombre = "";
    if (id_accion_predefinida == ID_NINGUNO) {
        nombre = $('#nombre_accion_programacion').val();
        if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
            $("#nombre_accion_programacion").addClass('data-check-failed');
            return;
        }
    }

    // Se recuperan los días de la semana seleccionados
    var dias_semana = [];
    $("#dias_semana_accion_programacion option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            dias_semana.push($(this).val());
        }
    });
    if (dias_semana.length == 0) {
        jAlert(TLNT.Idiomas._("Seleccione al menos un día"));
        return;
    }
    if (dias_semana.length == 7) {
        dias_semana = [-1];
    }

    // Hora de la acción
	var hora = $("#hora_accion_programacion").val();

    // Se añade o modifica la acción
    if (anyadir_accion == true) {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/anyade_accion.php", {
            id_programacion: id_programacion,
			id_accion_predefinida: id_accion_predefinida,
            nombre: nombre,
            contenido: contenido,
            valor: valor,
			dias_semana: dias_semana,
			hora: hora
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_acciones_programacion(id_programacion);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/modifica_accion.php", {
            id_accion: id_accion,
            id_programacion: id_programacion,
            id_accion_predefinida: id_accion_predefinida,
            nombre: nombre,
            contenido: contenido,
            valor: valor,
			dias_semana: dias_semana,
			hora: hora
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_acciones_programacion(id_programacion);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_actuadores_actualizar_tabla_acciones_programacion() {
    var params = this.id.split('__');
    var id_programacion = params[1];

    actualiza_tabla_acciones_programacion(id_programacion);
}


function actualiza_tabla_acciones_programacion(id_programacion) {
	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/dame_tabla_acciones.php", {
		id_programacion: id_programacion
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_acciones_programacion = "acciones-programacion" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_programacion;
        $('#' + id_elemento_acciones_programacion).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


//
// Funciones de excepciones de las programaciones
//


function boton_actuadores_mostrar_ventana_anyadir_modificar_excepcion_programacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_programacion = params[1];
	var id_excepcion = params[2];

	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/muestra_ventana_anyadir_modificar_excepcion.php", {
        id_programacion: id_programacion,
        id_excepcion: id_excepcion
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


function boton_actuadores_eliminar_excepcion_programacion(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_programacion = params[1];
	var id_excepcion = params[2];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la excepción?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true)
		{
			$.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/elimina_excepcion.php", {
                id_excepcion: id_excepcion,
				programacion: id_programacion
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
                actualiza_tabla_excepciones_programacion(id_programacion);
			});
		}
	});
}


function boton_actuadores_anyadir_modificar_excepcion_programacion() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_excepcion = $("#parametros_ventana_anyadir_modificar_excepcion_programacion").attr("anyadir_excepcion");
    var id_programacion = $("#parametros_ventana_anyadir_modificar_excepcion_programacion").attr("id_programacion");
    var id_excepcion = $("#parametros_ventana_anyadir_modificar_excepcion_programacion").attr("id_excepcion");

    var nombre = $("#nombre_excepcion_programacion").val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_excepcion_programacion").addClass('data-check-failed');
        return;
    }
    var tipo = $("#tipo_excepcion_programacion :selected").val();
	var fecha = $("#fecha_excepcion_programacion").val();
    var fecha_inicio = $("#fecha_inicio_excepcion_programacion").val();
    var fecha_fin = $("#fecha_fin_excepcion_programacion").val();
    var dia_anyo = $("#dia_anyo_excepcion_programacion").val();
    var dia_anyo_inicio = $("#dia_anyo_inicio_excepcion_programacion").val();
    var dia_anyo_fin = $("#dia_anyo_fin_excepcion_programacion").val();
    var dia_semana = $("#dia_semana_excepcion_programacion").val();

    // Comprobaciones de fechas/días de inicio y fin correctos
    switch (tipo) {
        case TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS: {
            var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
            if (fechas_correctas == false) {
                return;
            }
            break;
        }
        case TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO: {
            var dias_anyo_correctos = comprueba_dias_anyo_inicio_fin_correctos(dia_anyo_inicio, dia_anyo_fin);
            if (dias_anyo_correctos == false) {
                return;
            }
            break;
        }
    }

    // Se añade o modifica la excepción
    if (anyadir_excepcion == true) {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/anyade_excepcion.php", {
            id_programacion: id_programacion,
            nombre: nombre,
            tipo: tipo,
			fecha: fecha,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            dia_anyo: dia_anyo,
            dia_anyo_inicio: dia_anyo_inicio,
            dia_anyo_fin: dia_anyo_fin,
            dia_semana: dia_semana
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_excepciones_programacion(id_programacion);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/modifica_excepcion.php", {
            id_excepcion: id_excepcion,
            id_programacion: id_programacion,
            nombre: nombre,
            tipo: tipo,
			fecha: fecha,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            dia_anyo: dia_anyo,
            dia_anyo_inicio: dia_anyo_inicio,
            dia_anyo_fin: dia_anyo_fin,
            dia_semana: dia_semana
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_excepciones_programacion(id_programacion);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_actuadores_actualizar_tabla_excepciones_programacion() {
    var params = this.id.split('__');
    var id_programacion = params[1];

    actualiza_tabla_excepciones_programacion(id_programacion);
}


function actualiza_tabla_excepciones_programacion(id_programacion) {
	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Programaciones/dame_tabla_excepciones.php", {
		id_programacion: id_programacion
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_excepciones_programacion = "excepciones-programacion" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_programacion;
        $('#' + id_elemento_excepciones_programacion).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}
