//
// Funciones de rangos de días (eventos y reglas)
//


function boton_mostrar_ventana_anyadir_modificar_rango_dias(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var origen = params[1];
	var id_origen = params[2];
    var id_rango_dias = params[3];

    $.post("./src/lib/modulos/RangosDias/muestra_ventana_anyadir_modificar_rango_dias.php", {
        origen: origen,
        id_origen: id_origen,
        id_rango_dias: id_rango_dias
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


function boton_eliminar_rango_dias(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var origen = params[1];
	var id_origen = params[2];
    var id_rango_dias = params[3];

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el rango de días?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/RangosDias/elimina_rango_dias.php", {
                origen: origen,
                id_origen: id_origen,
                id_rango_dias: id_rango_dias
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_tabla_rangos_dias(origen, id_origen);
			});
		}
	});
}


function boton_anyadir_modificar_rango_dias() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
	var anyadir_rango_dias = $("#parametros_ventana_anyadir_modificar_rango_dias").attr("anyadir_rango_dias");
	var origen = $("#parametros_ventana_anyadir_modificar_rango_dias").attr("origen");
	var id_origen = $("#parametros_ventana_anyadir_modificar_rango_dias").attr("id_origen");
    var id_rango_dias = $("#parametros_ventana_anyadir_modificar_rango_dias").attr("id_rango_dias");
    var dia_anyo_inicio = $('#dia_anyo_inicio_rango_dias').val();
    var dia_anyo_fin = $('#dia_anyo_fin_rango_dias').val();

    // Comprobaciones de días de inicio y fin correctos
    var dia_mes_inicio = dame_dia_mes(dia_anyo_inicio, formato_dia_anyo_local);
    var dia_inicio = dia_mes_inicio[0];
    var mes_inicio = dia_mes_inicio[1];
    var dia_mes_fin = dame_dia_mes(dia_anyo_fin, formato_dia_anyo_local);
    var dia_fin = dia_mes_fin[0];
    var mes_fin = dia_mes_fin[1];
    if (mes_inicio > mes_fin) {
        jAlert(TLNT.Idiomas._('El mes de fin debe ser igual o mayor que el mes de inicio'));
        return;
    }
    if ((mes_inicio == mes_fin) && (dia_inicio > dia_fin)) {
        jAlert(TLNT.Idiomas._('El día de fin debe ser igual o mayor que el día de inicio'));
        return;
    }

    // Se añade o modifica el rango de días
    if (anyadir_rango_dias == true) {
        $.post("./src/lib/modulos/RangosDias/anyade_rango_dias.php", {
            origen: origen,
            id_origen: id_origen,
            dia_anyo_inicio: $('#dia_anyo_inicio_rango_dias').val(),
            dia_anyo_fin: $('#dia_anyo_fin_rango_dias').val()
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_rangos_dias(origen, id_origen);
        });
    }
    else {
        $.post("./src/lib/modulos/RangosDias/modifica_rango_dias.php", {
            id_rango_dias: id_rango_dias,
            origen: origen,
            id_origen: id_origen,
            dia_anyo_inicio: $('#dia_anyo_inicio_rango_dias').val(),
            dia_anyo_fin: $('#dia_anyo_fin_rango_dias').val()
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_rangos_dias(origen, id_origen);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_actualizar_tabla_rangos_dias() {
    var params = this.id.split('__');
	var origen = params[1];
	var id_origen = params[2];

	actualiza_tabla_rangos_dias(origen, id_origen);
}


function actualiza_tabla_rangos_dias(origen, id_origen) {
	$.post("./src/lib/modulos/RangosDias/dame_tabla_rangos_dias.php", {
		origen: origen,
        id_origen: id_origen
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_rangos_dias = "rangos-dias-" + origen + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_origen;
        $('#' + id_elemento_rangos_dias).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}