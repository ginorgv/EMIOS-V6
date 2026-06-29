//
// Funciones de licencias
//


function boton_administracion_mostrar_ventana_anyadir_modificar_licencia(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_licencia = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Licencias/muestra_ventana_anyadir_modificar_licencia.php", {
        id_licencia: id_licencia
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


function boton_administracion_eliminar_licencia(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_licencia = params[1];

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la licencia?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloAdministracion/Licencias/elimina_licencia.php", {
				id_licencia: id_licencia
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_tabla_licencias();
                actualiza_tabla_usuarios();

                // Se actualiza el menú de módulos
                TLNT.Navegacion.actualiza_menu_modulos(resultado.html_menu_modulos);
			});
		}
	});
}


function boton_administracion_anyadir_modificar_licencia() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Se recupera el módulo de la licencia
    var modulo = $('#modulo_licencia').val();
    if (modulo == ID_NINGUNO.toString()) {
        jAlert(TLNT.Idiomas._("No hay módulo seleccionado"));
        return;
    }

    // Activada y número máximo de elementos
    var activada = $('#activada_licencia').val();
    var numero_maximo_elementos = $('#numero_maximo_elementos_licencia').val();

    // Parámetros de la ventana
	var anyadir_licencia = $("#parametros_ventana_anyadir_modificar_licencia").attr("anyadir_licencia");
	var id_licencia = $("#parametros_ventana_anyadir_modificar_licencia").attr("id_licencia");

    // Se añade o modifica la licencia
    if (anyadir_licencia == true) {
        $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Licencias/anyade_licencia.php", {
            modulo: modulo,
            activada: activada,
            numero_maximo_elementos: numero_maximo_elementos
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_licencias();

            // Se actualiza el menú de módulos
            TLNT.Navegacion.actualiza_menu_modulos(resultado.html_menu_modulos);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Licencias/modifica_licencia.php", {
            id_licencia: id_licencia,
            modulo: modulo,
            activada: activada,
            numero_maximo_elementos: numero_maximo_elementos
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_licencias();
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_administracion_actualizar_tabla_licencias() {
	actualiza_tabla_licencias();
}


function actualiza_tabla_licencias() {
	$.post("./src/modulos/ModulosWeb/ModuloAdministracion/Licencias/dame_tabla_licencias.php", {},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaLicencias').html(resultado.html);

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}
