//
// Funciones de acciones de las reglas
//


function boton_actuadores_mostrar_ventana_anyadir_modificar_accion_regla(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_regla = params[1];
    var tipo = params[2];
	var id_accion = params[3];
    var tipo_operacion_administracion = params[4];

    $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/muestra_ventana_anyadir_modificar_accion.php", {
        id_regla: id_regla,
        tipo: tipo,
        id_accion: id_accion,
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

        // Se modifica el estilo de la ventana modal
        $('#ventana_modal .modal-body').removeClass('mostrar-barra-desplazamiento-y');
        $('#ventana_modal .modal-body').addClass('mostrar-todos-elementos-y');

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales();
	});
}


function boton_actuadores_eliminar_accion_regla(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_regla = params[1];
    var tipo = params[2];
	var id_accion = params[3];
    var nombre_accion = $(this).attr('nombre_accion');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la acción?") + "\n(" + escapeHtml(nombre_accion) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/elimina_accion.php", {
                id_regla: id_regla,
                id_accion: id_accion
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_acciones_regla(id_regla, tipo);
			});
		}
	});
}


function boton_actuadores_anyadir_modificar_accion_regla() {
    // Comprobación de datos correctos de las pestañas visibles
    var clase_actuador = $('#clase_actuador_accion_regla').val();
    var ids_pestanyas_visibles = ["tab-principal"];
    switch (clase_actuador) {
        case CLASE_ACTUADOR_MENSAJE:
        case CLASE_ACTUADOR_INTERRUPTOR:
        case CLASE_ACTUADOR_TELEPOSTE:
        case CLASE_ACTUADOR_LUZ_GRADUAL_4:
        case CLASE_ACTUADOR_GENERICA: {
            ids_pestanyas_visibles.push("tab-accion");
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
	var anyadir_accion = $("#parametros_ventana_anyadir_modificar_accion_regla").attr("anyadir_accion");
	var id_regla = $("#parametros_ventana_anyadir_modificar_accion_regla").attr("id_regla");
    var tipo = $("#parametros_ventana_anyadir_modificar_accion_regla").attr("tipo");
    var id_accion = $("#parametros_ventana_anyadir_modificar_accion_regla").attr("id_accion");

    // Nombre y causa de envío de la acción
    var nombre = $('#nombre_accion_regla').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_accion_regla").addClass('data-check-failed');
        return;
    }
    var causa = $('#causa_accion_regla').val();

    // Se comprueba si hay clase seleccionada
    if (clase_actuador == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Se comprueba si hay destino seleccionado
    var destino = $("#destino_accion_regla").val();
    var id_destino = $('#id_destino_accion_regla').val();
    if (id_destino == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay destino seleccionado"));
        return;
	}

    // Se recuperan los valores de los controles de la acción
    var valores_controles_accion = dame_valores_controles_accion(clase_actuador);
    if (valores_controles_accion == null) {
        return;
    }
    var id_accion_predefinida = valores_controles_accion["id_accion_predefinida"];
    var contenido_accion = valores_controles_accion["contenido_accion"];
    var valor_accion = valores_controles_accion["valor_accion"];

    // Se añade o modifica la acción
    if (anyadir_accion == true) {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/anyade_accion.php", {
            nombre: nombre,
            id_regla: id_regla,
            tipo: tipo,
            causa: causa,
            clase_actuador: clase_actuador,
            destino: destino,
            id_destino: id_destino,
            id_accion_predefinida: id_accion_predefinida,
            contenido_accion: contenido_accion,
            valor_accion: valor_accion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_acciones_regla(id_regla, tipo);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/modifica_accion.php", {
            id_accion: id_accion,
            nombre: nombre,
            id_regla: id_regla,
            tipo: tipo,
            causa: causa,
            clase_actuador: clase_actuador,
            destino: destino,
            id_destino: id_destino,
            id_accion_predefinida: id_accion_predefinida,
            contenido_accion: contenido_accion,
            valor_accion: valor_accion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_acciones_regla(id_regla, tipo);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_actuadores_actualizar_tabla_acciones_regla() {
    var params = this.id.split('__');
	var id_regla = params[1];
    var tipo = params[2];

	actualiza_tabla_acciones_regla(id_regla, tipo);
}


function actualiza_tabla_acciones_regla(id_regla, tipo) {
	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/dame_tabla_acciones.php", {
		id_regla: id_regla,
        tipo: tipo
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_acciones = "acciones-regla-" + tipo + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_regla;
        $('#' + id_elemento_acciones).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}