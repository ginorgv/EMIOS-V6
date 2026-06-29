//
// Funciones de sucesos de las reglas
//


function boton_actuadores_mostrar_ventana_anyadir_modificar_suceso_regla(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_regla = params[1];
	var id_suceso = params[2];
    var tipo_operacion_administracion = params[3];

    $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/muestra_ventana_anyadir_modificar_suceso.php", {
        id_regla: id_regla,
        id_suceso: id_suceso,
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


function boton_actuadores_eliminar_suceso_regla(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_regla = params[1];
	var id_suceso = params[2];
    var nombre_suceso = $(this).attr('nombre_suceso');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el suceso?") + "\n(" + escapeHtml(nombre_suceso) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/elimina_suceso.php", {
                id_regla: id_regla,
                id_suceso: id_suceso
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_sucesos_regla(id_regla);
			});
		}
	});
}


function boton_actuadores_anyadir_modificar_suceso_regla() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros del suceso de la regla
    var nombre = $('#nombre_suceso_regla').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_suceso_regla").addClass('data-check-failed');
        return;
    }
    var causa = $('#causa_suceso_regla').val();
    if (causa == ID_NINGUNO.toString())
    {
        jAlert(TLNT.Idiomas._('No hay tipo de causa seleccionado'));
        return;
    }
    var id_causa = $('#id_causa_suceso_regla').val();
    var origen = $('#origen_suceso_regla').val();
    var id_origen = $('#id_origen_suceso_regla').val();
    var numero_activaciones = $('#numero_activaciones_suceso_regla').val();

    // Modo de activación del suceso de la regla
    var modo_activacion = $('#modo_activacion_suceso_regla').val();
    var parametros_modo_activacion = null;
    switch (modo_activacion) {
        case MODO_ACTIVACION_SUCESO_NORMAL: {
            parametros_modo_activacion = "";
            break;
        }
        case MODO_ACTIVACION_SUCESO_TIEMPO_MINIMO: {
            var numero_horas_activacion = $('#numero_horas_activacion_suceso_regla').val();
            if (numero_horas_activacion <= 0) {
                jAlert(TLNT.Idiomas._('Las horas de activación deben ser mayores que 0'));
                return;
            }
            parametros_modo_activacion = [
                numero_horas_activacion].join(SEPARADOR_PARAMETROS_SIMPLES);
            break;
        }
        case MODO_ACTIVACION_SUCESO_REPETICIONES_MINIMAS_PERIODO_TIEMPO: {
            var periodo_tiempo_activacion = $('#periodo_tiempo_activacion_suceso_regla').val();
            var numero_repeticiones_activacion = $('#numero_repeticiones_activacion_suceso_regla').val();
            if (numero_horas_activacion <= 0) {
                jAlert(TLNT.Idiomas._('Las repeticiones de activación deben ser mayores que 0'));
                return;
            }
            parametros_modo_activacion = [
                periodo_tiempo_activacion,
                numero_repeticiones_activacion].join(SEPARADOR_PARAMETROS_SIMPLES);
            break;
        }
    }

    // Parámetros de la ventana
	var anyadir_suceso = $("#parametros_ventana_anyadir_modificar_suceso_regla").attr("anyadir_suceso");
	var id_regla = $("#parametros_ventana_anyadir_modificar_suceso_regla").attr("id_regla");
    var id_suceso = $("#parametros_ventana_anyadir_modificar_suceso_regla").attr("id_suceso");
    var causa_anterior = $("#parametros_ventana_anyadir_modificar_suceso_regla").attr("causa");
    var id_causa_anterior = $("#parametros_ventana_anyadir_modificar_suceso_regla").attr("id_causa");

    // Se añade o modifica el suceso
    if (anyadir_suceso == true) {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/anyade_suceso.php", {
            nombre: nombre,
            id_regla: id_regla,
            causa: causa,
            id_causa: id_causa,
            origen: origen,
            id_origen: id_origen,
            modo_activacion: modo_activacion,
            parametros_modo_activacion: parametros_modo_activacion,
            numero_activaciones: numero_activaciones
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_sucesos_regla(id_regla);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/modifica_suceso.php", {
            id_suceso: id_suceso,
            nombre: nombre,
            id_regla: id_regla,
            causa: causa,
            id_causa: id_causa,
            origen: origen,
            id_origen: id_origen,
            modo_activacion: modo_activacion,
            parametros_modo_activacion: parametros_modo_activacion,
            numero_activaciones: numero_activaciones,
            causa_anterior: causa_anterior,
            id_causa_anterior: id_causa_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_sucesos_regla(id_regla);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_actuadores_actualizar_tabla_sucesos_regla() {
    var params = this.id.split('__');
	var id_regla = params[1];

	actualiza_tabla_sucesos_regla(id_regla);
}


function actualiza_tabla_sucesos_regla(id_regla) {
	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/dame_tabla_sucesos.php", {
		id_regla: id_regla
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_sucesos = "sucesos-regla" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_regla;
        $('#' + id_elemento_sucesos).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}