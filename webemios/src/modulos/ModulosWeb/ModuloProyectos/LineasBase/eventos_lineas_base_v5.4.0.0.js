/*
 * Funciones de líneas base
 *
 */


// Muestra la tabla de líneas base aplicando el filtro
function boton_proyectos_filtro_lineas_base_tabla() {
    boton_proyectos_actualizar_tabla_lineas_base();
}


function boton_proyectos_mostrar_ventana_anyadir_modificar_linea_base(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_linea_base = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/muestra_ventana_anyadir_modificar_linea_base.php", {
        id_linea_base: id_linea_base,
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


function boton_proyectos_eliminar_linea_base(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_linea_base = params[1];
    var nombre_linea_base = $(this).attr('nombre_linea_base');

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la línea base?") + "\n(" + escapeHtml(nombre_linea_base) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/elimina_linea_base.php", {
                id_linea_base: id_linea_base
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_lineas_base();

                // Se recarga la lista de líneas base del informe de simulación de línea base
                recarga_lista_lineas_base_simulador_linea_base(false);
			});
		}
	});
}


function boton_proyectos_anyadir_modificar_linea_base() {
    // Comprobación de datos correctos de las pestañas visibles
    var ids_pestanyas_visibles = [];
    ids_pestanyas_visibles.push("tab-principal");
    ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
    switch (tipo) {
        case TIPO_LINEA_BASE_PERIODICA: {
            ids_pestanyas_visibles.push("tab-tipo-periodica");
            break;
        }
        case TIPO_LINEA_BASE_FUNCIONAL: {
            ids_pestanyas_visibles.push("tab-tipo-funcional");
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

    // Parámetros de la ventana
	var anyadir_linea_base = $("#parametros_ventana_anyadir_modificar_linea_base").attr("anyadir_linea_base");
	var id_linea_base = $("#parametros_ventana_anyadir_modificar_linea_base").attr("id_linea_base");

    // Nombre y descripción
    var nombre = $('#nombre_linea_base').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_linea_base").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_linea_base').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_linea_base").addClass('data-check-failed');
        return;
    }

    // Comprobación de clase y sensor seleccionado
    var clase_sensor = $('#clase_sensor_linea_base').val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }
    var id_sensor = $('#id_sensor_linea_base').val();
    if (id_sensor == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay sensor seleccionado'));
        return;
    }

    // Campo y parámetros extra de sensor
    var campo = $('#campo_linea_base').val();
    var parametros_extra_campo = $('#parametros_extra_campo_linea_base').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return;
    }
    var campo_parametros_extra = campo;
    if (parametros_extra_campo != "") {
        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
    }

    // Comprobación de tipo e intervalo de valores seleccionados
    var tipo = $('#tipo_linea_base').val();
    if (tipo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo seleccionado'));
        return;
    }
    var intervalo_valores = $('#intervalo_valores_linea_base').val();
    if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay intervalo de valores seleccionado'));
        return;
    }

    // Fechas de inicio y fin de periodo de referencia
    var fecha_inicio_periodo_referencia = $('#fecha_inicio_periodo_referencia_linea_base').val();
    var fecha_fin_periodo_referencia = $('#fecha_fin_periodo_referencia_linea_base').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio_periodo_referencia, null, fecha_fin_periodo_referencia, null);
    if (fechas_correctas == false) {
        return;
    }

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("linea_base", false);
    if (horario_semanal.correcto == false) {
        return;
    }
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

    // Exclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_linea_base");
    if (exclusion_fechas.correcto == false) {
        return;
    }
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);

    // Parámetros auxiliares
    var parametros_auxiliares = "";

    // Parámetros del tipo de línea base y error estándar
    var parametros_tipo = "";
    var parametros_tipo_json = "";
    var error_estandar = null;
    var coeficiente_variacion = null;
    var coeficiente_correlacion = null;
    switch (tipo) {
        case TIPO_LINEA_BASE_PERIODICA: {
            // Comprobación de periodicidad de valores y tipo de cálculo de valores
            var periodicidad_valores = $('#periodicidad_valores_linea_base').val();
            if (periodicidad_valores == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._('No hay periodicidad de valores seleccionada'));
                return;
            }
            var tipo_calculo_valores = $('#tipo_calculo_valores_linea_base').val();
            if (tipo_calculo_valores == TIPO_NINGUNO) {
                jAlert(TLNT.Idiomas._('No hay tipo de cálculo de valores seleccionado'));
                return;
            }
            parametros_tipo = [
                periodicidad_valores,
                tipo_calculo_valores].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            error_estandar = -1;
            coeficiente_variacion = 0;
            coeficiente_correlacion = 0;
            break;
        }
        case TIPO_LINEA_BASE_FUNCIONAL: {
            var funcion_valores = $("#funcion_valores_linea_base").val();
            if (comprueba_longitud_cadena(funcion_valores, NUMERO_MAXIMO_CARACTERES_FUNCION_VALORES_LINEA_BASE_FUNCIONAL) == false) {
                $('#funcion_valores_linea_base').addClass('data-check-failed');
                return;
            }
            funcion_valores = formatea_funcion_valores(funcion_valores);
            parametros_tipo = [
                funcion_valores].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            var cadena_valores_prueba_funcion_valores = $("#valores_prueba_funcion_valores_linea_base").val();
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
            error_estandar = $("#error_estandar_linea_base").val();
            coeficiente_variacion = $("#coeficiente_variacion_linea_base").val();
            coeficiente_correlacion = $("#coeficiente_correlacion_linea_base").val();
            break;
        }
    }

    // Parámetros de la ventana
    var id_sensor_anterior = $("#parametros_ventana_anyadir_modificar_linea_base").attr("id_sensor");
    var campo_anterior = $("#parametros_ventana_anyadir_modificar_linea_base").attr("campo");
    var intervalo_valores_anterior = $("#parametros_ventana_anyadir_modificar_linea_base").attr("intervalo_valores");
    var fecha_inicio_periodo_referencia_anterior = $("#parametros_ventana_anyadir_modificar_linea_base").attr("fecha_inicio_periodo_referencia");
    var fecha_fin_periodo_referencia_anterior = $("#parametros_ventana_anyadir_modificar_linea_base").attr("fecha_fin_periodo_referencia");

    // Se añade o modifica la línea base
    if (anyadir_linea_base == true) {
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/anyade_linea_base.php", {
            nombre: nombre,
            descripcion: descripcion,
            clase_sensor: clase_sensor,
            id_sensor: id_sensor,
            campo_parametros_extra: campo_parametros_extra,
            tipo: tipo,
            parametros_tipo: parametros_tipo,
            parametros_tipo_json: parametros_tipo_json,
            intervalo_valores: intervalo_valores,
            fecha_inicio_periodo_referencia: fecha_inicio_periodo_referencia,
            fecha_fin_periodo_referencia: fecha_fin_periodo_referencia,
            error_estandar: error_estandar,
            coeficiente_variacion: coeficiente_variacion,
            coeficiente_correlacion: coeficiente_correlacion,
            horario_semanal: cadena_horario_semanal,
            intervalo_valores_anterior: intervalo_valores_anterior,
            exclusion_fechas: cadena_exclusion_fechas,
            id_linea_base_anterior: id_linea_base
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_lineas_base();

            // Se recarga la lista de líneas base del informe de simulación de línea base
            recarga_lista_lineas_base_simulador_linea_base(false);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/modifica_linea_base.php", {
            id_linea_base: id_linea_base,
            nombre: nombre,
            descripcion: descripcion,
            clase_sensor: clase_sensor,
            id_sensor: id_sensor,
            campo_parametros_extra: campo_parametros_extra,
            tipo: tipo,
            parametros_tipo: parametros_tipo,
            parametros_tipo_json: parametros_tipo_json,
            intervalo_valores: intervalo_valores,
            fecha_inicio_periodo_referencia: fecha_inicio_periodo_referencia,
            fecha_fin_periodo_referencia: fecha_fin_periodo_referencia,
            error_estandar: error_estandar,
            coeficiente_variacion: coeficiente_variacion,
            coeficiente_correlacion: coeficiente_correlacion,
            horario_semanal: cadena_horario_semanal,
            exclusion_fechas: cadena_exclusion_fechas,
            id_sensor_anterior: id_sensor_anterior,
            campo_anterior: campo_anterior,
            intervalo_valores_anterior: intervalo_valores_anterior,
            parametros_auxiliares: parametros_auxiliares
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_linea_base_detalles(id_linea_base);
            if (resultado.cerrar_ventana == true) {
                $('#ventana_modal').modal('hide');
            }

            // Se recarga la lista de líneas base del informe de simulación de línea base
            var actualizar_fechas_inicio_fin_linea_base =
                (fecha_inicio_periodo_referencia_anterior != fecha_inicio_periodo_referencia) ||
                (fecha_fin_periodo_referencia_anterior != fecha_fin_periodo_referencia);
            recarga_lista_lineas_base_simulador_linea_base(actualizar_fechas_inicio_fin_linea_base);
        });
    }
}


// Recarga la lista de líneas base del informe de simulación de línea base
function recarga_lista_lineas_base_simulador_linea_base(actualizar_fechas_inicio_fin_linea_base) {
    var id_linea_base = $("#id_linea_base_proyectos_simulador_linea_base").val();
    $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_lista_lineas_base.php", {
        id_linea_base: id_linea_base
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#id_linea_base_proyectos_simulador_linea_base").html(resultado.html);
        $("#id_linea_base_proyectos_simulador_linea_base").trigger("chosen:updated");

        // Se actualizan las fechas de inicio y fin de la línea base
        if (actualizar_fechas_inicio_fin_linea_base == true) {
            $("#id_linea_base_proyectos_simulador_linea_base").trigger("change");
        }
    });
}


// Actualización de la tabla de líneas base
function boton_proyectos_actualizar_tabla_lineas_base() {
    actualiza_tabla_lineas_base();

    // Se recarga la lista de líneas base del informe de simulación de línea base
    recarga_lista_lineas_base_simulador_linea_base();
}


function actualiza_tabla_lineas_base() {
	$.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_tabla_lineas_base.php", {
		filtro: $('#filtro_proyectos_filtro_lineas_base_tabla').val(),
        tipo: $('#tipo_linea_base_proyectos_filtro_lineas_base_tabla').val(),
        intervalo_valores: $('#intervalo_valores_linea_base_proyectos_filtro_lineas_base_tabla').val()
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaLineasBase').html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


function boton_proyectos_refrescar_tabla_linea_base() {
    var params = this.id.split('__');
	var id_linea_base = params[1];

    actualiza_tabla_linea_base_detalles(id_linea_base);
}


function actualiza_tabla_linea_base_detalles(id_linea_base) {
    // Se actualiza la información de la fila de la tabla
	$.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_informacion_fila_tabla_linea_base.php", {
        id_linea_base: id_linea_base
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_datos = "datosLineaBase__" + id_linea_base;
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
// Funciones de variables de líneas base
//


function boton_proyectos_mostrar_ventana_anyadir_modificar_variable_linea_base(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_linea_base = params[1];
    var id_variable = params[2];

	$.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/muestra_ventana_anyadir_modificar_variable.php", {
		id_linea_base: id_linea_base,
        id_variable: id_variable
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


function boton_proyectos_eliminar_variable_linea_base(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_linea_base = params[1];
	var id_variable = params[2];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la variable?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/elimina_variable.php", {
                id_linea_base: id_linea_base,
                id_variable: id_variable
			},
			function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_tabla_variables_linea_base(id_linea_base);
			});
		}
	});
}


function boton_proyectos_anyadir_modificar_variable_linea_base() {
    // Comprobación de datos correctos
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_variable = $("#parametros_ventana_anyadir_modificar_variable_linea_base").attr("anyadir_variable");
    var id_linea_base = $("#parametros_ventana_anyadir_modificar_variable_linea_base").attr("id_linea_base");
    var id_variable = $("#parametros_ventana_anyadir_modificar_variable_linea_base").attr("id_variable");

    // Nombre
    var nombre = $('#nombre_variable_linea_base').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_variable_linea_base").addClass('data-check-failed');
        return;
    }

    // Clase de sensor
    var clase_sensor = $('#clase_sensor_variable_linea_base').val();
    if (clase_sensor == CLASE_NINGUNA) {
		jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
        return;
	}

    // Identificador de sensor
    var id_sensor = $('#id_sensor_variable_linea_base').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return;
	}

    // Campo y parámetros extra de sensor
    var campo = $('#campo_variable_linea_base').val();
    var parametros_extra_campo = $('#parametros_extra_campo_variable_linea_base').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return;
    }
    var campo_parametros_extra = campo;
    if (parametros_extra_campo != "") {
        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
    }

    // Se añade o modifica la variable de la línea base
    if (anyadir_variable == true) {
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/anyade_variable.php", {
            id_linea_base: id_linea_base,
            nombre: nombre,
            clase_sensor: clase_sensor,
            id_sensor: id_sensor,
            campo_parametros_extra: campo_parametros_extra
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_variables_linea_base(id_linea_base);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/modifica_variable.php", {
            id_variable: id_variable,
            id_linea_base: id_linea_base,
            nombre: nombre,
            clase_sensor: clase_sensor,
            id_sensor: id_sensor,
            campo_parametros_extra: campo_parametros_extra
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_variables_linea_base(id_linea_base);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_proyectos_actualizar_tabla_variables_linea_base() {
    var params = this.id.split('__');
    var id_linea_base = params[1];

    actualiza_tabla_variables_linea_base(id_linea_base);
}


function actualiza_tabla_variables_linea_base(id_linea_base) {
	$.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_tabla_variables.php", {
		id_linea_base: id_linea_base
	},
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_variables_linea_base = "variables-linea-base" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_linea_base;
        $('#' + id_elemento_variables_linea_base).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


//
// Funciones de excepciones de líneas base
//


function boton_proyectos_mostrar_ventana_anyadir_modificar_excepcion_linea_base(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_linea_base_padre = params[1];
    var id_linea_base_hija = params[2];
    var id_excepcion = params[3];

	$.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/muestra_ventana_anyadir_modificar_excepcion.php", {
		id_linea_base_padre: id_linea_base_padre,
        id_linea_base_hija: id_linea_base_hija,
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


function boton_proyectos_eliminar_excepcion_linea_base(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_linea_base_padre = params[1];
    var id_linea_base_hija = params[2];
	var id_excepcion = params[3];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la excepción?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/elimina_excepcion.php", {
                id_excepcion: id_excepcion,
                id_linea_base_padre: id_linea_base_padre,
                id_linea_base_hija: id_linea_base_hija
			},
			function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_tabla_excepciones_linea_base(id_linea_base_padre);
			});
		}
	});
}


function boton_proyectos_anyadir_modificar_excepcion_linea_base() {
    // Comprobación de datos correctos
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_excepcion = $("#parametros_ventana_anyadir_modificar_excepcion_linea_base").attr("anyadir_excepcion");
    var id_linea_base_padre = $("#parametros_ventana_anyadir_modificar_excepcion_linea_base").attr("id_linea_base_padre");
    var id_linea_base_hija_anterior = $("#parametros_ventana_anyadir_modificar_excepcion_linea_base").attr("id_linea_base_hija");
    var id_excepcion = $("#parametros_ventana_anyadir_modificar_excepcion_linea_base").attr("id_excepcion");

    // Nombre y descripción
    var nombre = $('#nombre_excepcion_linea_base').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_excepcion_linea_base").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_excepcion_linea_base').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_excepcion_linea_base").addClass('data-check-failed');
        return;
    }

    // Se comprueba si hay línea base hija seleccionada
    var id_linea_base_hija = $('#id_linea_base_hija_excepcion_linea_base').val();
    if (id_linea_base_hija == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay línea base seleccionada"));
        return;
	}

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("excepcion_linea_base", true);
    if (horario_semanal.correcto == false) {
        return;
    }
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

    // Inclusión de fechas
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_excepcion_linea_base");
    if (inclusion_fechas.correcto == false) {
        return;
    }
    var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

    // Se añade o modifica la excepción de la línea base
    if (anyadir_excepcion == true) {
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/anyade_excepcion.php", {
            id_linea_base_padre: id_linea_base_padre,
            nombre: nombre,
            descripcion: descripcion,
            id_linea_base_hija: id_linea_base_hija,
            horario_semanal: cadena_horario_semanal,
            inclusion_fechas: cadena_inclusion_fechas
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_excepciones_linea_base(id_linea_base_padre);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/modifica_excepcion.php", {
            id_excepcion: id_excepcion,
            id_linea_base_padre: id_linea_base_padre,
            id_linea_base_hija_anterior: id_linea_base_hija_anterior,
            nombre: nombre,
            descripcion: descripcion,
            id_linea_base_hija: id_linea_base_hija,
            horario_semanal: cadena_horario_semanal,
            inclusion_fechas: cadena_inclusion_fechas
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_excepciones_linea_base(id_linea_base_padre);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_proyectos_actualizar_tabla_excepciones_linea_base() {
    var params = this.id.split('__');
    var id_linea_base = params[1];

    actualiza_tabla_excepciones_linea_base(id_linea_base);
}


function actualiza_tabla_excepciones_linea_base(id_linea_base) {
	$.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_tabla_excepciones.php", {
		id_linea_base: id_linea_base
	},
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_excepciones_linea_base = "excepciones-linea-base" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_linea_base;
        $('#' + id_elemento_excepciones_linea_base).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


//
// Informe de simulación de línea base
//


// Muestra el informe de simulación de línea base
function boton_proyectos_simulador_linea_base_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_proyectos_simulador_linea_base();
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_linea_base = parametros_informe["id_linea_base"];
    var comentarios = parametros_informe["comentarios"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_simulacion_linea_base.php", {
        id_linea_base: id_linea_base,
        comentarios: comentarios,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-proyectos-simulador-linea-base").hide();
        $("#informe-proyectos-simulador-linea-base").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-valores-simulador-linea-base",
            "grafica-diferencias-simulador-linea-base",
            "grafica-diferencias-acumuladas-simulador-linea-base",
            "descripcion-sensor-simulador-linea-base",
            "contenedor-tabla-error-coeficientes-linea-base-simulador-linea-base",
            "contenedor-tabla-errores-coeficientes-lineas-base-excepciones-simulador-linea-base",
            "contenedor-tabla-comentarios-simulador-linea-base"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-simulador-linea-base",
            id_grafica_valores: "grafica-valores-simulador-linea-base",
            id_grafica_diferencias: "grafica-diferencias-simulador-linea-base",
            id_grafica_diferencias_acumuladas: "grafica-diferencias-acumuladas-simulador-linea-base",
            id_descripcion_sensor: "descripcion-sensor-simulador-linea-base",
            id_contenedor_tabla_error_coeficientes_linea_base: "contenedor-tabla-error-coeficientes-linea-base-simulador-linea-base",
            id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones: "contenedor-tabla-errores-coeficientes-lineas-base-excepciones-simulador-linea-base",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-simulador-linea-base"};
        dibuja_informe_proyectos_simulador_linea_base(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de simulación de línea base
function dame_parametros_informe_proyectos_simulador_linea_base() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Se comprueba si hay línea base seleccionada
    var id_linea_base = $('#id_linea_base_proyectos_simulador_linea_base').val();
    if (id_linea_base == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay línea base seleccionada"));
        return (null);
	}

    // Comentarios
    var comentarios = $('#comentarios_proyectos_simulador_linea_base').val();

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_linea_base"] = id_linea_base;
    parametros_informe["comentarios"] = comentarios;

    // Se recuperan las fechas
    var fecha_inicio = $('#fecha_inicio_proyectos_simulador_linea_base').val();
    var hora_inicio = $('#hora_inicio_proyectos_simulador_linea_base').val();
    var fecha_fin = $('#fecha_fin_proyectos_simulador_linea_base').val();
    var hora_fin = $('#hora_fin_proyectos_simulador_linea_base').val();
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}
