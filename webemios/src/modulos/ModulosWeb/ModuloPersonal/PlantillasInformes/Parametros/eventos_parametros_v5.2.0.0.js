//
// Funciones de elementos de las plantillas de informes
//


function boton_personal_mostrar_ventana_anyadir_modificar_parametro_plantilla_informe(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_plantilla_informe = params[1];
    var id_parametro = params[2];
    var tipo_operacion_administracion = params[3];

    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/muestra_ventana_anyadir_modificar_parametro.php", {
        id_plantilla_informe: id_plantilla_informe,
        id_parametro: id_parametro,
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


function boton_personal_eliminar_parametro_plantilla_informe(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_plantilla_informe = params[1];
	var id_parametro = params[2];
    var nombre_parametro = $(this).attr('nombre_parametro');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el parámetro?") + "\n(" + escapeHtml(nombre_parametro) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/elimina_parametro.php", {
                id_plantilla_informe: id_plantilla_informe,
                id_parametro: id_parametro
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_parametros_plantilla_informe(id_plantilla_informe);
                recarga_controles_parametros_informe_plantilla_informe(id_plantilla_informe);
			});
		}
	});
}


function boton_personal_anyadir_modificar_parametro_plantilla_informe() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Tipo de parámetro
    var tipo = $('#tipo_parametro_plantilla_informe').val();
    if (tipo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo de parámetro seleccionado'));
        return;
    }

    // Parámetros de tipo
    var parametros_tipo = "";
    switch (tipo) {
        case TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR: {
            // Clase de sensor e identificador de parámetro sensor asociado
            var clase_sensor = $('#clase_sensor_parametro_plantilla_informe_sensor').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
                return;
            }
            var id_parametro_sensor_asociado = $('#id_parametro_sensor_asociado_parametro_plantilla_informe_sensor').val();

            parametros_tipo = [
                clase_sensor,
                id_parametro_sensor_asociado].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES: {
            // Clase de sensor
            var clase_sensor = $('#clase_sensor_parametro_plantilla_informe_grupo_sensores').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
                return;
            }

            parametros_tipo = [
                clase_sensor].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR: {
            // Clase de actuador
            var clase_actuador = $('#clase_actuador_parametro_plantilla_informe_actuador').val();
            if (clase_actuador == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
                return;
            }

            parametros_tipo = [
                clase_actuador].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES: {
            // Clase de actuador
            var clase_actuador = $('#clase_actuador_parametro_plantilla_informe_grupo_actuadores').val();
            if (clase_actuador == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
                return;
            }

            parametros_tipo = [
                clase_actuador].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
    }

    // Parámetros de la ventana
    var anyadir_parametro = $("#parametros_ventana_anyadir_modificar_parametro_plantilla_informe").attr("anyadir_parametro");
    var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_parametro_plantilla_informe").attr("id_plantilla_informe");
    var id_parametro = $("#parametros_ventana_anyadir_modificar_parametro_plantilla_informe").attr("id_parametro");

    // Nombre
    var nombre = $('#nombre_parametro_plantilla_informe').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_parametro_plantilla_informe").addClass('data-check-failed');
        return;
    }

    // Se añade o modifica el parámetro
    if (anyadir_parametro == true) {
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/anyade_parametro.php", {
            nombre: nombre,
            id_plantilla_informe: id_plantilla_informe,
            tipo: tipo,
            parametros_tipo: parametros_tipo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_parametros_plantilla_informe(id_plantilla_informe);
            recarga_controles_parametros_informe_plantilla_informe(id_plantilla_informe);
        });
    }
    else {
        // Tipo y parámetros de tipo anteriores
        var tipo_anterior = $("#parametros_ventana_anyadir_modificar_parametro_plantilla_informe").attr("tipo");
        var parametros_tipo_anteriores = $("#parametros_ventana_anyadir_modificar_parametro_plantilla_informe").attr("parametros_tipo");

        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/modifica_parametro.php", {
            id_parametro: id_parametro,
            nombre: nombre,
            id_plantilla_informe: id_plantilla_informe,
            tipo: tipo,
            parametros_tipo: parametros_tipo,
            tipo_anterior: tipo_anterior,
            parametros_tipo_anteriores: parametros_tipo_anteriores
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_parametros_plantilla_informe(id_plantilla_informe);
            recarga_controles_parametros_informe_plantilla_informe(id_plantilla_informe);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_personal_actualizar_tabla_parametros_plantilla_informe() {
    var params = this.id.split('__');
	var id_plantilla_informe = params[1];

	actualiza_tabla_parametros_plantilla_informe(id_plantilla_informe);
}


function actualiza_tabla_parametros_plantilla_informe(id_plantilla_informe) {
	$.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/dame_tabla_parametros.php", {
		id_plantilla_informe: id_plantilla_informe
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_parametros = "parametros-plantilla-informe" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_plantilla_informe;
        $('#' + id_elemento_parametros).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


function boton_personal_mostrar_ventana_modificar_posiciones_parametros_plantilla_informe(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_plantilla_informe = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/muestra_ventana_modificar_posiciones_parametros.php", {
        id_plantilla_informe: id_plantilla_informe
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


// Modifica las posiciones de los parámetros
function boton_modificar_posiciones_parametros_plantilla_informe() {
	// Identificador de plantilla de informes
    var params = this.id.split('__');
	var id_plantilla_informe = params[1];

    // Se recuperan los identificadores de los parámetros (ordenados)
    var ids_parametros = [];
    $("#posicion_parametros option").each(function() {
        ids_parametros.push($(this).attr("id"));
    });

    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/modifica_posiciones_parametros.php", {
        id_plantilla_informe: id_plantilla_informe,
        ids_parametros: ids_parametros
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
        actualiza_tabla_parametros_plantilla_informe(id_plantilla_informe);
        $('#ventana_modal').modal('hide');
    });
}