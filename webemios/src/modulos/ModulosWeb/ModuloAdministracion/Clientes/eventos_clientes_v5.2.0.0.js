//
// Funciones de clientes
//


function boton_administracion_mostrar_ventana_anyadir_modificar_cliente(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_cliente = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Clientes/muestra_ventana_anyadir_modificar_cliente.php", {
		id_cliente: id_cliente
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


function boton_administracion_eliminar_cliente(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_cliente = params[1];
	var nombre_cliente = $(this).attr('nombre_cliente');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el cliente?") + "\n(" + escapeHtml(nombre_cliente) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloAdministracion/Clientes/elimina_cliente.php", {
				id_cliente: id_cliente
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_clientes();
			});
		}
	});
}


function boton_administracion_anyadir_modificar_cliente() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
	var anyadir_cliente = $("#parametros_ventana_anyadir_modificar_cliente").attr("anyadir_cliente");
	var id_cliente = $("#parametros_ventana_anyadir_modificar_cliente").attr("id_cliente");

    // Nombre
    var nombre = $('#nombre_cliente').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_cliente").addClass('data-check-failed');
        return;
    }

    // Se añade o modifica el cliente
    if (anyadir_cliente == true) {
        $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Clientes/anyade_cliente.php", {
            nombre: nombre
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_clientes();
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Clientes/modifica_cliente.php", {
            id_cliente: id_cliente,
            nombre: nombre
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_clientes();
            actualiza_tabla_nodos(TIPO_NODO_RED);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_administracion_actualizar_tabla_clientes() {
    actualiza_tabla_clientes();
    actualiza_tabla_nodos(TIPO_NODO_RED);
}


function actualiza_tabla_clientes() {
	$.post("./src/modulos/ModulosWeb/ModuloAdministracion/Clientes/dame_tabla_clientes.php", {},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		$('#tablaClientes').html(resultado.html);

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}
