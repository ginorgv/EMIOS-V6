//
// Funciones de periodos (eventos y reglas)
//


function boton_mostrar_ventana_anyadir_modificar_periodo(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var origen = params[1];
	var id_origen = params[2];
    var id_periodo = params[3];

    $.post("./src/lib/modulos/Periodos/muestra_ventana_anyadir_modificar_periodo.php", {
        origen: origen,
        id_origen: id_origen,
        id_periodo: id_periodo
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

        // Se modifica el estilo de la ventana modal
        $('#ventana_modal .modal-body').removeClass('mostrar-barra-desplazamiento-y');
        $('#ventana_modal .modal-body').addClass('mostrar-todos-elementos-y');

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales();
	});
}


function boton_eliminar_periodo(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var origen = params[1];
	var id_origen = params[2];
    var id_periodo = params[3];

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el periodo?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/Periodos/elimina_periodo.php", {
                origen: origen,
                id_origen: id_origen,
                id_periodo: id_periodo
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_tabla_periodos(origen, id_origen);
			});
		}
	});
}


function boton_anyadir_modificar_periodo() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
	var anyadir_periodo = $("#parametros_ventana_anyadir_modificar_periodo").attr("anyadir_periodo");
	var origen = $("#parametros_ventana_anyadir_modificar_periodo").attr("origen");
	var id_origen = $("#parametros_ventana_anyadir_modificar_periodo").attr("id_origen");
    var id_periodo = $("#parametros_ventana_anyadir_modificar_periodo").attr("id_periodo");

    // Se añade o modifica el periodo
    if (anyadir_periodo == true) {
        $.post("./src/lib/modulos/Periodos/anyade_periodo.php", {
            origen: origen,
            id_origen: id_origen,
            dia_inicio: $('#dia_inicio_periodo').val(),
            dia_fin: $('#dia_fin_periodo').val(),
            hora_inicio: $('#hora_inicio_periodo').val(),
            hora_fin: $('#hora_fin_periodo').val()
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_periodos(origen, id_origen);
        });
    }
    else {
        $.post("./src/lib/modulos/Periodos/modifica_periodo.php", {
            id_periodo: id_periodo,
            origen: origen,
            id_origen: id_origen,
            dia_inicio: $('#dia_inicio_periodo').val(),
            dia_fin: $('#dia_fin_periodo').val(),
            hora_inicio: $('#hora_inicio_periodo').val(),
            hora_fin: $('#hora_fin_periodo').val()
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_periodos(origen, id_origen);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_actualizar_tabla_periodos() {
    var params = this.id.split('__');
	var origen = params[1];
	var id_origen = params[2];

	actualiza_tabla_periodos(origen, id_origen);
}


function actualiza_tabla_periodos(origen, id_origen) {
	$.post("./src/lib/modulos/Periodos/dame_tabla_periodos.php", {
		origen: origen,
        id_origen: id_origen
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_periodos = "periodos-" + origen + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_origen;
        $('#' + id_elemento_periodos).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}