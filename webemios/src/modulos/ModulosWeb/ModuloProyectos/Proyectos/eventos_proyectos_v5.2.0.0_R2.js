/*
 * Funciones de proyectos
 *
 */


// Muestra la tabla de proyectos aplicando el filtro
function boton_proyectos_filtro_proyectos_tabla() {
    boton_proyectos_actualizar_tabla_proyectos();
}


function boton_proyectos_mostrar_ventana_anyadir_modificar_proyecto(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_proyecto = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/muestra_ventana_anyadir_modificar_proyecto.php", {
        id_proyecto: id_proyecto,
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


function boton_proyectos_eliminar_proyecto(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_proyecto = params[1];
    var nombre_proyecto = $(this).attr('nombre_proyecto');

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el proyecto?") + "\n(" + escapeHtml(nombre_proyecto) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/elimina_proyecto.php", {
                id_proyecto: id_proyecto
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_proyectos();
			});
		}
	});
}


function boton_proyectos_anyadir_modificar_proyecto() {
    // Comprobación de datos correctos
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
	var anyadir_proyecto = $("#parametros_ventana_anyadir_modificar_proyecto").attr("anyadir_proyecto");
	var id_proyecto = $("#parametros_ventana_anyadir_modificar_proyecto").attr("id_proyecto");

    // Nombre y descripción
    var nombre = $('#nombre_proyecto').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_proyecto").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_proyecto').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_proyecto").addClass('data-check-failed');
        return;
    }

    // Comprobación de clase y sensor seleccionado
    var clase_sensor = $('#clase_sensor_proyecto').val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }
    var id_sensor = $('#id_sensor_proyecto').val();
    if (id_sensor == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay sensor seleccionado'));
        return;
    }
    var campo = $('#campo_proyecto').val();

    // Intervalo de valores y línea base
    var intervalo_valores = $('#intervalo_valores_proyecto').val();
    if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay intervalo de valores seleccionado'));
        return;
    }
    var id_linea_base = $('#id_linea_base_proyecto').val();

    // Tipo de objetivo, tipo de valor de objetivo y valor del objetivo
    var tipo_objetivo = $('#tipo_objetivo_proyecto').val();
    if (tipo_objetivo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo de objetivo seleccionado'));
        return;
    }
    var tipo_valor_objetivo = $('#tipo_valor_objetivo_proyecto').val();
    var valor_objetivo = $('#valor_objetivo_proyecto').val();

    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_proyecto').val();
    var fecha_fin = $('#fecha_fin_proyecto').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
    if (fechas_correctas == false) {
        return;
    }

    // Se añade o modifica el proyecto
    if (anyadir_proyecto == true) {
        // Identificador de proyecto anterior
        var id_proyecto_anterior = id_proyecto;

        // Se añade el proyecto
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/anyade_proyecto.php", {
            nombre: nombre,
            descripcion: descripcion,
            clase_sensor: clase_sensor,
            id_sensor: id_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores,
            id_linea_base: id_linea_base,
            tipo_objetivo: tipo_objetivo,
            tipo_valor_objetivo: tipo_valor_objetivo,
            valor_objetivo: valor_objetivo,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            id_proyecto_anterior: id_proyecto_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_proyectos();
        });
    }
    else {
        // Se modifica el proyecto
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/modifica_proyecto.php", {
            id_proyecto: id_proyecto,
            nombre: nombre,
            descripcion: descripcion,
            clase_sensor: clase_sensor,
            id_sensor: id_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores,
            id_linea_base: id_linea_base,
            tipo_objetivo: tipo_objetivo,
            tipo_valor_objetivo: tipo_valor_objetivo,
            valor_objetivo: valor_objetivo,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_proyecto_detalles(id_proyecto);
            $('#ventana_modal').modal('hide');
        });
    }
}


// Actualización de la tabla de proyectos
function boton_proyectos_actualizar_tabla_proyectos() {
    actualiza_tabla_proyectos();
}


function actualiza_tabla_proyectos() {
	$.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/dame_tabla_proyectos.php", {
		filtro: $('#filtro_proyectos_filtro_proyectos_tabla').val(),
        intervalo_valores: $('#intervalo_valores_proyecto_proyectos_filtro_proyectos_tabla').val(),
        estado_avance: $('#estado_avance_proyecto_proyectos_filtro_proyectos_tabla').val(),
        estado: $('#estado_proyecto_proyectos_filtro_proyectos_tabla').val()
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaProyectos').html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


function boton_proyectos_refrescar_tabla_proyecto() {
    var params = this.id.split('__');
	var id_proyecto = params[1];

    actualiza_tabla_proyecto_detalles(id_proyecto);
}


function actualiza_tabla_proyecto_detalles(id_proyecto) {
    // Se actualiza la información de la fila de la tabla
	$.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/dame_informacion_fila_tabla_proyecto.php", {
        id_proyecto: id_proyecto
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_datos = "datosProyecto__" + id_proyecto;
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


function boton_proyectos_actualizar_proyecto() {
    var params = this.id.split('__');
	var id_proyecto = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/calcula_avance_estado_proyecto.php", {
        id_proyecto: id_proyecto
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
        actualiza_tabla_proyecto_detalles(id_proyecto);
    });
}


//
// Funciones de valores adicionales de proyectos
//


function boton_proyectos_mostrar_ventana_anyadir_modificar_valor_adicional_proyecto(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_proyecto = params[1];
    var id_valor_adicional = params[2];

	$.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/muestra_ventana_anyadir_modificar_valor_adicional.php", {
		id_proyecto: id_proyecto,
        id_valor_adicional: id_valor_adicional
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


function boton_proyectos_eliminar_valor_adicional_proyecto(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_proyecto = params[1];
	var id_valor_adicional = params[2];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el valor adicional?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/elimina_valor_adicional.php", {
                id_proyecto: id_proyecto,
                id_valor_adicional: id_valor_adicional
			},
			function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_tabla_proyecto_detalles(id_proyecto);
			});
		}
	});
}


function boton_proyectos_anyadir_modificar_valor_adicional_proyecto() {
    // Comprobación de datos correctos
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_valor_adicional = $("#parametros_ventana_anyadir_modificar_valor_adicional_proyecto").attr("anyadir_valor_adicional");
    var id_proyecto = $("#parametros_ventana_anyadir_modificar_valor_adicional_proyecto").attr("id_proyecto");
    var id_valor_adicional = $("#parametros_ventana_anyadir_modificar_valor_adicional_proyecto").attr("id_valor_adicional");

    // Nombre
    var nombre = $('#nombre_valor_adicional_proyecto').val();

    // Destino y valor
    var destino = $('#destino_valor_adicional_proyecto').val();
    var valor = $('#valor_valor_adicional_proyecto').val();

    // Se comprueba si hay periodicidad
    var periodicidad = $('#periodicidad_valor_adicional_proyecto').val();
    if (periodicidad == PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_NINGUNA) {
		jAlert(TLNT.Idiomas._("No hay periodicidad seleccionada"));
        return;
	}

    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_valor_adicional_proyecto').val();
    var fecha_fin = $('#fecha_fin_valor_adicional_proyecto').val();
    if ((fecha_inicio != "") && (dame_fecha_valida(fecha_inicio, formato_fecha_local) == false)) {
        jAlert(TLNT.Idiomas._("Fecha de inicio incorrecta"));
        return;
    }
    if ((fecha_fin != "") && (dame_fecha_valida(fecha_fin, formato_fecha_local) == false)) {
        jAlert(TLNT.Idiomas._("Fecha de fin incorrecta"));
        return;
    }
    if ((fecha_inicio == "") && (fecha_fin != "")) {
        jAlert(TLNT.Idiomas._("No hay fecha de inicio"));
        return;
    }
    if ((fecha_inicio != "") && (fecha_fin != "")) {
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
        if (fechas_correctas == false) {
            return;
        }
    }

    // Aplicar en intervalos sin valores
    var aplicar_intervalos_sin_valores = $('#aplicar_intervalos_sin_valores_valor_adicional_proyecto').val();

    // Se añade o modifica el valor adicional del proyecto
    if (anyadir_valor_adicional == true) {
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/anyade_valor_adicional.php", {
            id_proyecto: id_proyecto,
            nombre: nombre,
            destino: destino,
            valor: valor,
            periodicidad: periodicidad,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            aplicar_intervalos_sin_valores: aplicar_intervalos_sin_valores
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_proyecto_detalles(id_proyecto);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/modifica_valor_adicional.php", {
            id_valor_adicional: id_valor_adicional,
            id_proyecto: id_proyecto,
            nombre: nombre,
            destino: destino,
            valor: valor,
            periodicidad: periodicidad,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            aplicar_intervalos_sin_valores: aplicar_intervalos_sin_valores
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_proyecto_detalles(id_proyecto);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_proyectos_actualizar_tabla_valores_adicionales_proyecto() {
    var params = this.id.split('__');
    var id_proyecto = params[1];

    actualiza_tabla_valores_adicionales_proyecto(id_proyecto);
}


function actualiza_tabla_valores_adicionales_proyecto(id_proyecto) {
	$.post("./src/modulos/ModulosWeb/ModuloProyectos/Proyectos/dame_tabla_valores_adicionales.php", {
		id_proyecto: id_proyecto
	},
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_valores_adicionales_proyecto = "valores-adicionales-proyecto" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_proyecto;
        $('#' + id_elemento_valores_adicionales_proyecto).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}
