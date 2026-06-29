/*
 * Funciones de localizaciones
 *
 */


// Muestra la tabla de ratios aplicando el filtro
function boton_localizaciones_filtro_ratios_tabla() {
    boton_localizaciones_actualizar_tabla_ratios();
}


function boton_localizaciones_mostrar_ventana_anyadir_modificar_ratio(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_ratio = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/muestra_ventana_anyadir_modificar_ratio.php", {
        id_ratio: id_ratio
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


function boton_localizaciones_eliminar_ratio(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_ratio = params[1];
    var nombre_ratio = $(this).attr('nombre_ratio');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el ratio?") + "\n(" + escapeHtml(nombre_ratio) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/elimina_ratio.php", {
                id_ratio: id_ratio
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_ratios();
			});
		}
	});
}


function boton_localizaciones_anyadir_modificar_ratio() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Nombre y descripción
    var nombre = $('#nombre_ratio').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_ratio").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_ratio').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_ratio").addClass('data-check-failed');
        return;
    }

    // Unidad de medida
    var sustituir_unidad_medida_sensor = $('#sustituir_unidad_medida_sensor_ratio').val();
    var unidad_medida = $('#unidad_medida_ratio').val();

    // Tipo de ratio
    var tipo = $('#tipo_ratio').val();

    // Clase y campo de sensor
    var clase_sensor = $('#clase_sensor_ratio').val();
    if (tipo == TIPO_RATIO_VARIABLE) {
        if (clase_sensor == CLASE_NINGUNA) {
            jAlert(TLNT.Idiomas._("No hay clase de sensor seleccionada"));
            return;
        }
    }
    var campo_sensor = $('#campo_sensor_ratio').val();

    // Valor y sensor por defecto
    var valor_defecto = $('#valor_defecto_ratio').val();
    if (tipo == TIPO_RATIO_FIJO) {
        if (valor_defecto != "") {
            if (valor_defecto <= 0) {
                jAlert(TLNT.Idiomas._("El valor por defecto de los ratios debe ser mayor que 0"));
                return;
            }
        }
    }
    var id_sensor_defecto = $('#id_sensor_defecto_ratio').val();

    // Parámetros de la ventana
	var anyadir_ratio = $("#parametros_ventana_anyadir_modificar_ratio").attr("anyadir_ratio");
	var id_ratio = $("#parametros_ventana_anyadir_modificar_ratio").attr("id_ratio");

    // Se añade o modifica el ratio
    if (anyadir_ratio == true) {
        $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/anyade_ratio.php", {
            nombre: nombre,
            descripcion: descripcion,
            sustituir_unidad_medida_sensor: sustituir_unidad_medida_sensor,
            unidad_medida: unidad_medida,
            tipo: tipo,
            clase_sensor: clase_sensor,
            campo_sensor: campo_sensor,
            valor_defecto: valor_defecto,
            id_sensor_defecto: id_sensor_defecto
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_ratios(null);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/modifica_ratio.php", {
            id_ratio: id_ratio,
            nombre: nombre,
            descripcion: descripcion,
            sustituir_unidad_medida_sensor: sustituir_unidad_medida_sensor,
            unidad_medida: unidad_medida,
            tipo: tipo,
            clase_sensor: clase_sensor,
            campo_sensor: campo_sensor,
            valor_defecto: valor_defecto,
            id_sensor_defecto: id_sensor_defecto
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_ratios(null);
            $('#ventana_modal').modal('hide');
        });
    }
}


// Actualización de la tabla de ratios
function boton_localizaciones_actualizar_tabla_ratios() {
    actualiza_tabla_ratios();
}


function actualiza_tabla_ratios() {
	$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/dame_tabla_ratios.php", {
        filtro: $('#filtro_localizaciones_filtro_ratios_tabla').val()
    },
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaRatios').html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


