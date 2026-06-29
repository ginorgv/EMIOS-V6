/*
 * Funciones de administración de hijas de localizaciones
 *
 */


function boton_localizaciones_mostrar_ventana_anyadir_modificar_hija_localizacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_localizacion_padre = params[1];
    var id_localizacion_hija = params[2];
	var id_hija_localizacion = params[3];

	$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/muestra_ventana_anyadir_modificar_hija_localizacion.php", {
        id_localizacion_padre: id_localizacion_padre,
        id_localizacion_hija: id_localizacion_hija,
        id_hija_localizacion: id_hija_localizacion
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


function boton_localizaciones_eliminar_hija_localizacion(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_localizacion_padre = params[1];
	var id_localizacion_hija = params[2];
    var id_hija_localizacion = params[3];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la localización hija?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/elimina_hija_localizacion.php", {
                modulo: $("#modulo").attr("name"),
                id_localizacion_padre: id_localizacion_padre,
				id_localizacion_hija: id_localizacion_hija,
                id_hija_localizacion: id_hija_localizacion
			},
			function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_localizaciones_administracion_localizaciones_hijas(
                    resultado.numero_localizaciones_actualizadas,
                    id_localizacion_padre);
			});
		}
	});
}


function boton_localizaciones_anyadir_modificar_hija_localizacion() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Se comprueba si hay localización hija seleccionada
    var id_localizacion_hija = $('#id_localizacion_hija').val();
    if (id_localizacion_hija == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay localización seleccionada"));
        return;
	}

    // Parámetros de la ventana
    var anyadir_hija = $("#parametros_ventana_anyadir_modificar_hija_localizacion").attr("anyadir_hija");
    var id_localizacion_padre = $("#parametros_ventana_anyadir_modificar_hija_localizacion").attr("id_localizacion_padre");
    var id_localizacion_hija_anterior = $("#parametros_ventana_anyadir_modificar_hija_localizacion").attr("id_localizacion_hija");
    var id_hija_localizacion = $("#parametros_ventana_anyadir_modificar_hija_localizacion").attr("id_hija_localizacion");

    // Se añade o modifica la hija de localización
    if (anyadir_hija == true) {
        $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/anyade_hija_localizacion.php", {
            id_localizacion_padre: id_localizacion_padre,
            id_localizacion_hija: id_localizacion_hija
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_localizaciones_administracion_localizaciones_hijas(
                resultado.numero_localizaciones_actualizadas,
                id_localizacion_padre);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/modifica_hija_localizacion.php", {
            id_hija_localizacion: id_hija_localizacion,
            id_localizacion_padre: id_localizacion_padre,
            id_localizacion_hija: id_localizacion_hija,
            id_localizacion_hija_anterior: id_localizacion_hija_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_localizaciones_administracion_localizaciones_hijas(
                resultado.numero_localizaciones_actualizadas,
                id_localizacion_padre);
            $('#ventana_modal').modal('hide');
        });
    }
}


function actualiza_localizaciones_administracion_localizaciones_hijas(numero_localizaciones_actualizadas, id_localizacion_padre) {
    switch (numero_localizaciones_actualizadas) {
        case 0: {
            actualiza_tabla_hijas_localizacion(id_localizacion_padre);
            break;
        }
        case 1: {
            actualiza_tabla_localizacion_detalles(id_localizacion_padre);
            break;
        }
        default: {
            actualiza_tabla_localizaciones(id_localizacion_padre);
            break;
        }
    }
}


function boton_actualizar_tabla_hijas_localizacion() {
    var params = this.id.split('__');
    var id_localizacion_padre = params[1];

    actualiza_tabla_hijas_localizacion(id_localizacion_padre);
}


function actualiza_tabla_hijas_localizacion(id_localizacion_padre) {
	$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/dame_tabla_hijas_localizacion.php", {
		id_localizacion: id_localizacion_padre
	},
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_hijas_localizacion = "hijas-localizacion" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_localizacion_padre;
        $('#' + id_elemento_hijas_localizacion).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}

