/*
 * Funciones de administración de hijos de sensores
 *
 */


function boton_mostrar_ventana_anyadir_modificar_hijo_sensor(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_sensor_padre = params[1];
    var id_sensor_hijo = params[2];
	var id_hijo_sensor = params[3];

	$.post("./src/lib/modulos/Nodos/administracion/muestra_ventana_anyadir_modificar_hijo_sensor.php", {
		id_sensor_padre: id_sensor_padre,
        id_sensor_hijo: id_sensor_hijo,
        id_hijo_sensor: id_hijo_sensor
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


function boton_eliminar_hijo_sensor(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_sensor_padre = params[1];
	var id_sensor_hijo = params[2];
    var id_hijo_sensor = params[3];
    var tipo_sensor_padre = params[4];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el sensor hijo?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/Nodos/administracion/elimina_hijo_sensor.php", {
                id_sensor_padre: id_sensor_padre,
				id_sensor_hijo: id_sensor_hijo,
                id_hijo_sensor: id_hijo_sensor,
                tipo_sensor_padre: tipo_sensor_padre
			},
			function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor_padre);
			});
		}
	});
}


function boton_anyadir_modificar_hijo_sensor() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Identificador de sensor hijo
    var id_sensor_hijo = $('#id_sensor_hijo').val();
    if (id_sensor_hijo == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return;
	}

    // Parámetros de la ventana
    var anyadir_hijo = $("#parametros_ventana_anyadir_modificar_hijo_sensor").attr("anyadir_hijo");
    var id_sensor_padre = $("#parametros_ventana_anyadir_modificar_hijo_sensor").attr("id_sensor_padre");
    var id_sensor_hijo_anterior = $("#parametros_ventana_anyadir_modificar_hijo_sensor").attr("id_sensor_hijo");
    var id_hijo_sensor = $("#parametros_ventana_anyadir_modificar_hijo_sensor").attr("id_hijo_sensor");
    var tipo_sensor_padre = $("#parametros_ventana_anyadir_modificar_hijo_sensor").attr("tipo_sensor_padre");
    var numero_campos_sensor_padre = $("#parametros_ventana_anyadir_modificar_hijo_sensor").attr("numero_campos_sensor_padre");

    // Parámetros de tipo
    var clase_sensor_hijo = "";
    var parametros_tipo = "";
    switch (tipo_sensor_padre) {
        case TIPO_SENSOR_VIRTUAL: {
            parametros_tipo = $("#operacion_hijo_sensor_virtual").val();
            break;
        }
        case TIPO_SENSOR_PROCESADO: {
            clase_sensor_hijo = $('#clase_sensor_hijo_sensor_procesado').val();
            var campos_hijo_sensor_procesado = [];
            for (var i = 0; i < numero_campos_sensor_padre; i++) {
                campos_hijo_sensor_procesado.push($("#campo_hijo_sensor_procesado_" + i).val());
            }
            var cadena_campos_hijo_sensor_procesado = campos_hijo_sensor_procesado.join(SEPARADOR_PARAMETROS_SIMPLES);
            var funcion_hijo_sensor_procesado = $("#funcion_hijo_sensor_procesado").val();
            var resultado_parametros_funcion_hijo_sensor_procesado = dame_cadena_parametros_funcion_hijo_sensor_procesado(funcion_hijo_sensor_procesado);
            if (resultado_parametros_funcion_hijo_sensor_procesado.parametros_correctos == false) {
                jAlert(resultado_parametros_funcion_hijo_sensor_procesado.descripcion_error);
                return;
            }
            var parametros_funcion_hijo_sensor_procesado = resultado_parametros_funcion_hijo_sensor_procesado.cadena_parametros_funcion;
            var variable_hijo_sensor_procesado = $("#variable_hijo_sensor_procesado").val();
            var valores_obligatorios_hijo_sensor_procesado = $("#valores_obligatorios_hijo_sensor_procesado").val();
            parametros_tipo = [
                cadena_campos_hijo_sensor_procesado,
                funcion_hijo_sensor_procesado,
                parametros_funcion_hijo_sensor_procesado,
                variable_hijo_sensor_procesado,
                valores_obligatorios_hijo_sensor_procesado].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
    }

    // Se añade o modifica el hijo de sensor
    if (anyadir_hijo == true) {
        $.post("./src/lib/modulos/Nodos/administracion/anyade_hijo_sensor.php", {
            id_sensor_padre: id_sensor_padre,
            clase_sensor_hijo: clase_sensor_hijo,
            id_sensor_hijo: id_sensor_hijo,
            parametros_tipo: parametros_tipo,
            tipo_sensor_padre: tipo_sensor_padre
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor_padre);
        });
    }
    else {
        $.post("./src/lib/modulos/Nodos/administracion/modifica_hijo_sensor.php", {
            id_hijo_sensor: id_hijo_sensor,
            id_sensor_padre: id_sensor_padre,
            clase_sensor_hijo: clase_sensor_hijo,
            id_sensor_hijo: id_sensor_hijo,
            parametros_tipo: parametros_tipo,
            tipo_sensor_padre: tipo_sensor_padre,
            id_sensor_hijo_anterior: id_sensor_hijo_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor_padre);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_actualizar_tabla_hijos_sensor() {
    var params = this.id.split('__');
    var id_sensor = params[1];

    actualiza_tabla_hijos_sensor(id_sensor);
}


function actualiza_tabla_hijos_sensor(id_sensor) {
	$.post("./src/lib/modulos/Nodos/administracion/dame_tabla_hijos_sensor.php", {
		id_sensor: id_sensor
	},
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_hijos_sensor = "hijos-sensor" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_sensor;
        $('#' + id_elemento_hijos_sensor).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}

