//
// Funciones de facturas
//


// Simulación de factura
function boton_smartmeter_simulador_factura_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_factura(false);
    if (parametros_informe == null) {
        return;
    }

    // Selección de medición y país
    switch (medicion) {
        case MEDICION_ELECTRICIDAD: {
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    boton_smartmeter_simulador_factura_ver_informe_electricidad_Espanya(parametros_informe);
                    break;
                }
                case PAIS_PORTUGAL: {
                    boton_smartmeter_simulador_factura_ver_informe_electricidad_Portugal(parametros_informe);
                    break;
                }
            }
            break;
        }
        case MEDICION_GAS: {
            switch (pais_tarifas_gas) {
                case PAIS_ESPANYA: {
                    boton_smartmeter_simulador_factura_ver_informe_gas_Espanya(parametros_informe);
                    break;
                }
            }
            break;
        }
        case MEDICION_AGUA: {
            switch (pais_tarifas_agua) {
                case PAIS_ESPANYA: {
                    boton_smartmeter_simulador_factura_ver_informe_agua_Espanya(parametros_informe);
                    break;
                }
            }
            break;
        }
    }
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de simulación de factura
function dame_parametros_informe_smartmeter_simulador_factura(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador de sensor
    var id_sensor = $('#id_sensor_smartmeter_simulador_factura').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}

    // Nombre del sensor
    var nombre_sensor = $('#id_sensor_smartmeter_simulador_factura :selected').text();

    // Identificador de tarifa
    // (puede ser ninguno, que se corresponde con la tarifa actual)
    var id_tarifa = $('#id_tarifa_smartmeter_simulador_factura').val();

    // Identificadores y nombres de sensores de reparto de costes
    var ids_sensores_reparto_costes = [];
    var nombres_sensores_reparto_costes = [];
    $("#ids_sensores_reparto_costes_smartmeter_simulador_factura option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores_reparto_costes.push($(this).val());
            nombres_sensores_reparto_costes.push($(this).text());
        }
    });

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["ids_sensores_reparto_costes"] = ids_sensores_reparto_costes;
    parametros_informe["nombres_sensores_reparto_costes"] = nombres_sensores_reparto_costes;

    // Se recupera la exclusión de fechas si no es informe automático
    if (informe_automatico == false) {
        var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_simulador_factura");
        if (exclusion_fechas.correcto == false) {
            return (null);
        }
        parametros_informe["exclusion_fechas"] = exclusion_fechas;
    }

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_simulador_factura').val();
        var fecha_fin = $('#fecha_fin_smartmeter_simulador_factura').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
        var hora_inicio = "00:00:00";
        var hora_fin = "23:59:59";
        var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
        var fecha_hora_fin = fecha_fin + ", " + hora_fin;

        parametros_informe["fecha_inicio"] = fecha_inicio;
        parametros_informe["fecha_fin"] = fecha_fin;
        parametros_informe["hora_inicio"] = hora_inicio;
        parametros_informe["hora_fin"] = hora_fin;
        parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
        parametros_informe["fecha_hora_fin"] = fecha_hora_fin;
    }

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


//
// Funciones de validaciones de facturas
//


// Ventana de validación de facturas
function boton_smartmeter_mostrar_ventana_validacion_facturas() {
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/muestra_ventana_validacion_facturas.php", {
        medicion: medicion
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


// Validación de facturas
function boton_smartmeter_validar_facturas() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Tipo de ficheros
    var tipo_ficheros = $('#tipo_ficheros_validacion_facturas').val();
    if (tipo_ficheros == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo seleccionado'));
        return;
	}

    // http://stackoverflow.com/questions/4069982/document-getelementbyid-vs-jquery
    var control_seleccion_ficheros_validacion_facturas = $('#ficheros_validacion_facturas_files').get(0);

    // Número de ficheros
    var numero_ficheros = control_seleccion_ficheros_validacion_facturas.files.length;
    if (numero_ficheros > NUMERO_MAXIMO_FICHEROS_VALIDACION_FACTURAS) {
        jAlert(TLNT.Idiomas._("Número máximo de ficheros de facturas y cierres superado") +
            " (" + NUMERO_MAXIMO_FICHEROS_VALIDACION_FACTURAS + ")");
        return;
    }

    // Se comprueba el tamaño de los ficheros
    for (var i = 0; i < numero_ficheros; i++) {
        var tamanyo_fichero_facturas_cierres_bytes = control_seleccion_ficheros_validacion_facturas.files[i].size;
        var tamanyo_fichero_facturas_cierres_kbs = (tamanyo_fichero_facturas_cierres_bytes / 1024);
        if (tamanyo_fichero_facturas_cierres_kbs > TAMANYO_MAXIMO_FICHERO_FACTURAS_CIERRES_KBS) {
            jAlert(TLNT.Idiomas._("El tamaño de los ficheros de facturas y cierres es demasiado grande") + "\n" +
                "(" + TLNT.Idiomas._("tamaño máximo") + ": " + TAMANYO_MAXIMO_FICHERO_FACTURAS_CIERRES_KBS + " " + TLNT.Idiomas._("KBs") + ") " +
                "(" + TLNT.Idiomas._("tamaño actual") + ": " + formatea_numero(tamanyo_fichero_facturas_cierres_kbs, 0) + " " + TLNT.Idiomas._("KBs") + ")");
            return;
        }
    }

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    for (var i = 0; i < numero_ficheros; i++) {
        var nombre_parametro_fichero = "fichero_factura_" + (i + 1);
        datos_formulario.append(nombre_parametro_fichero, control_seleccion_ficheros_validacion_facturas.files[i]);
    }
    datos_formulario.append("medicion", medicion);
    datos_formulario.append("numero_ficheros_facturas", numero_ficheros);
    datos_formulario.append("tipo_ficheros_facturas", tipo_ficheros);

    // Llamada 'ajax' POST
    $.ajax({
        url: "./src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/valida_facturas.php",
        type: "POST",
        data: datos_formulario,
        processData: false,
        contentType: false,
        timeout: SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX_SEGUNDO_PLANO * 1000,
        success: function(result) {
            var resultado = dame_resultado_ejecucion_script_php_json(result);
            if (resultado == null) {
                return;
            }

            if (resultado.hay_ficheros_incorrectos == false) {
                jInfo(resultado.msg);
            }
            else {
                jAlert(resultado.msg);
            }
            actualiza_tabla_validaciones_facturas();
            $('#ventana_modal').modal('hide');
        },
        error: function(request, status, err) {
            if (status == "timeout") {
                error_ajax_capturado = true;

                jInfo(TLNT.Idiomas._("La validación de facturas y cierres se está realizado en segundo plano"));
            }
            actualiza_tabla_validaciones_facturas();
            $('#ventana_modal').modal('hide');
        }
    });
}


// Realiza el filtrado de validaciones de facturas
function boton_smartmeter_filtro_validaciones_facturas() {
    actualiza_tabla_validaciones_facturas();
}


// Actualiza la tabla de validaciones de facturas
function actualiza_tabla_validaciones_facturas() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_smartmeter_filtro_validaciones_facturas').val();
    var hora_inicio = $('#hora_inicio_smartmeter_filtro_validaciones_facturas').val();
    var fecha_fin = $('#fecha_fin_smartmeter_filtro_validaciones_facturas').val();
    var hora_fin = $('#hora_fin_smartmeter_filtro_validaciones_facturas').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/dame_tabla_validaciones_facturas.php", {
        medicion: medicion,
        filtro: $('#filtro_smartmeter_filtro_validaciones_facturas').val(),
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#tablaValidacionesFacturas").html(resultado.html);
        if (resultado.limite_elementos_tabla_superado == true) {
            jAlert(TLNT.Idiomas._("Número máximo de validaciones superado (se muestran las más recientes)"));
        }

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Elimina una validación de factura
function boton_smartmeter_eliminar_validacion_factura(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_validacion_factura = params[1];
    var hora = $(this).attr('hora');
    var nombre_sensor = $(this).attr('nombre_sensor');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la validación de la factura o cierre?") + "\n(" +
        TLNT.Idiomas._("hora") + ": " + hora + ", " +
        TLNT.Idiomas._("sensor") + ": " + nombre_sensor + ")",
        TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/elimina_validacion_factura.php", {
                medicion: medicion,
				id_validacion_factura: id_validacion_factura
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
				actualiza_tabla_validaciones_facturas();
			});
		}
	});
}


// Muestra la ventana de modificar las observaciones de una validación de factura
function boton_smartmeter_mostrar_ventana_modificar_observaciones_validacion_factura(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_validacion_factura = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/muestra_ventana_modificar_observaciones_validacion_factura.php", {
        medicion: medicion,
		id_validacion_factura: id_validacion_factura
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


// Modificación de las observaciones de una validación de factura
function boton_smartmeter_modificar_observaciones_validacion_factura() {
    // Parámetros de la ventana
	var id_validacion_factura = $("#parametros_ventana_modificar_observaciones_validacion_factura").attr("id_validacion_factura");
    var observaciones_anteriores = $("#parametros_ventana_modificar_observaciones_validacion_factura").attr("observaciones_anteriores");

    // Observaciones
    var observaciones = $('#observaciones_validacion_factura').val();
    if (comprueba_longitud_cadena(observaciones, NUMERO_MAXIMO_CARACTERES_OBSERVACIONES) == false) {
        $("#observaciones_validacion_factura").addClass('data-check-failed');
        return;
    }

    // Se modifican las observaciones de la acción
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/modifica_observaciones_validacion_factura.php", {
        medicion: medicion,
        id_validacion_factura: id_validacion_factura,
        observaciones: observaciones,
        observaciones_anteriores: observaciones_anteriores
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
        actualiza_detalles_validacion_factura(id_validacion_factura);
        $('#ventana_modal').modal('hide');
    });
}


// Actualiza los detalles de la validación de factura
function actualiza_detalles_validacion_factura(id_validacion_factura) {
    // Id de los datos de la fila de la validación de factura
    var id_datos = null;
    switch (medicion) {
        case MEDICION_ELECTRICIDAD: {
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    id_datos = "datosValidacionFacturaElectrica_Espanya__" + id_validacion_factura;
                    break;
                }
            }
            break;
        }
    }

    // Se actualiza la información detallada
    $.post("./comun/src/lib/modulos/dame_detalles_tabla.php", {
        id_datos: id_datos
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#' + id_datos + " .detalle-tabla-datos").html(resultado.html);

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_detalles_tablas_datos();

        // Acciones 'extra' a realizar en los detalles de la tabla de datos
        TLNT.Navegacion.realiza_acciones_mostrado_detalle_tabla_datos(resultado);
    });
}
