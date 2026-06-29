/*
 * Módulo Sensores
 *
 */


// Botones de envío de acciones de herramientas del módulo
function boton_sensores_envia_accion_herramientas_sensores() {
	$.post("./src/modulos/ModulosWeb/ModuloSensores/envia_accion_herramientas_sensores.php", {
		boton: this.id
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		jInfo(resultado.msg);
	});
}


// Ventana de importación de valores de un sensor
function boton_sensores_mostrar_ventana_importacion_valores_sensor() {
    var params = this.id.split('__');
	var id_sensor = params[1];
    var clase_sensor = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloSensores/muestra_ventana_importacion_valores_sensor.php", {
        clase_sensor: clase_sensor,
        id_sensor: id_sensor
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

        // Botón de ayuda
        $('#boton_ayuda_ventana_modal').addClass("boton_sensores_ayuda_importacion_valores_sensor");
        $('#boton_ayuda_ventana_modal').show();

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales();
	});
}


// Importación de valores de un sensor
function boton_sensores_importar_valores_sensor() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Clase de sensor
    var clase_sensor = $("#clase_sensor_importacion_valores_sensor").val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_importacion_valores_sensor').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return;
	}
    var nombre_sensor = $('#id_sensor_importacion_valores_sensor :selected').text();

    // Número de valores de la clase de sensor
    var numero_valores_clase_sensor = dame_numero_valores_clase_sensor(clase_sensor);

    // Se comprueba el tamaño del fichero
    var tamanyo_fichero_importacion_bytes = document.getElementById('fichero_importacion_valores_sensor_file').files[0].size;
    var tamanyo_fichero_importacion_kbs = (tamanyo_fichero_importacion_bytes / 1024);
    if (tamanyo_fichero_importacion_kbs > TAMANYO_MAXIMO_FICHERO_VALORES_KBS) {
        jAlert(TLNT.Idiomas._("El tamaño del fichero de valores es demasiado grande") + "\n" +
            "(" + TLNT.Idiomas._("tamaño máximo") + ": " + formatea_numero(TAMANYO_MAXIMO_FICHERO_VALORES_KBS, 0) + " " + TLNT.Idiomas._("KBs") + ") " +
            "(" + TLNT.Idiomas._("tamaño actual") + ": " + formatea_numero(tamanyo_fichero_importacion_kbs, 0) + " " + TLNT.Idiomas._("KBs") + ")");
        return;
    }

    // Opciones y opciones de valores de fichero
    var resultado_opciones_fichero = dame_cadena_opciones_fichero_importacion_valores_sensor();
    if (resultado_opciones_fichero.parametros_correctos == false) {
        jAlert(resultado_opciones_fichero.descripcion_error);
        return;
    }
    var resultado_opciones_valores_fichero = dame_cadena_opciones_valores_fichero_importacion_valores_sensor(numero_valores_clase_sensor);
    if (resultado_opciones_valores_fichero.parametros_correctos == false) {
        jAlert(resultado_opciones_valores_fichero.descripcion_error);
        return;
    }

    // Pregunta de confirmación
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("Los valores guardados del sensor en el rango de fechas a importar se borrarán. ¿Está seguro?"), TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            // http://stackoverflow.com/questions/4069982/document-getelementbyid-vs-jquery
            var control_seleccion_fichero_importacion = $('#fichero_importacion_valores_sensor_file')[0];

            // Se crean los datos del formulario
            var datos_formulario = new FormData();
            datos_formulario.append("id_sensor", id_sensor);
            datos_formulario.append("nombre_sensor", nombre_sensor);
            datos_formulario.append("clase_sensor", clase_sensor);
            datos_formulario.append("aplicar_calibracion", $("#aplicar_calibracion_importacion_valores_sensor").val());
            datos_formulario.append("tipo_valores", $("#tipo_valores_sensor_importacion_valores_sensor").val());
            datos_formulario.append("fichero_valores", control_seleccion_fichero_importacion.files[0]);
            datos_formulario.append("opciones_fichero_valores", resultado_opciones_fichero.cadena_opciones_fichero);
            datos_formulario.append("opciones_valores_fichero_valores", resultado_opciones_valores_fichero.cadena_opciones_valores_fichero);

            // Llamada 'ajax' POST
            $.ajax({
                url: "./src/modulos/ModulosWeb/ModuloSensores/anyade_importacion_valores_sensor_pendiente.php",
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

                    jInfo(resultado.msg);
                    refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor);
                    boton_actualizar_tabla_operaciones_datos_sensores();
                }
            });
        }
    });
}


// Ventana de exportación de valores de un sensor
function boton_sensores_mostrar_ventana_exportacion_valores_sensor() {
    var params = this.id.split('__');
	var id_sensor = params[1];
    var clase_sensor = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloSensores/muestra_ventana_exportacion_valores_sensor.php", {
        clase_sensor: clase_sensor,
        id_sensor: id_sensor
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


// Exportación de valores de un sensor
function boton_sensores_exportar_valores_sensor() {
    // Clase de sensor
    var clase_sensor = $("#clase_sensor_exportacion_valores_sensor").val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_exportacion_valores_sensor').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return;
	}
    var nombre_sensor = $('#id_sensor_exportacion_valores_sensor :selected').text();

    // Intervalo de valores
    var intervalo_valores = $('#intervalo_valores_exportacion_valores_sensor').val();
    if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay intervalo de valores seleccionado"));
        return;
	}

    // Tipo de incrementos de valores
    var tipo_incrementos_valores = $('#tipo_incrementos_valores_exportacion_valores_sensor').val();

    // Valores de clase de sensor
    var valores_clase_sensor = $("#valores_clase_sensor_exportacion_valores_sensor").val();

    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_exportacion_valores_sensor').val();
    var hora_inicio = $('#hora_inicio_exportacion_valores_sensor').val();
    var fecha_fin = $('#fecha_fin_exportacion_valores_sensor').val();
    var hora_fin = $('#hora_fin_exportacion_valores_sensor').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    // Punto decimal
    var id_punto_decimal = $("#id_punto_decimal_exportacion_valores_sensor").val();
    var punto_decimal = null;
    switch (id_punto_decimal) {
        case ID_PUNTO_DECIMAL_PUNTO: {
            punto_decimal = ".";
            break;
        }
        case ID_PUNTO_DECIMAL_COMA: {
            punto_decimal = ",";
            break;
        }
    }

    // Zona horaria
    var zona_horaria = $("#zona_horaria_exportacion_valores_sensor").val();

    // Se exportan los valores del sensor
    $.post("./src/modulos/ModulosWeb/ModuloSensores/exporta_valores_sensor.php", {
        clase_sensor: clase_sensor,
        nombre_sensor: nombre_sensor,
        id_sensor: id_sensor,
        intervalo_valores: intervalo_valores,
        tipo_incrementos_valores: tipo_incrementos_valores,
        valores_clase_sensor: valores_clase_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        punto_decimal: punto_decimal,
        zona_horaria: zona_horaria
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);

        // Se guardan los ficheros de los valores exportados
        if (resultado.rutas_ficheros_valores_exportados.length == 1) {
            var ruta_fichero_valores_exportados = resultado.rutas_ficheros_valores_exportados[0];
            window.location.href = ruta_fichero_valores_exportados;
        }
        else {
            for (var i = 0; i < resultado.rutas_ficheros_valores_exportados.length; i++) {
                var ruta_fichero_valores_exportados = resultado.rutas_ficheros_valores_exportados[i];
                window.open(ruta_fichero_valores_exportados);
            }
        }
    });
}


// Ventana de borrado de valores de un sensor
function boton_sensores_mostrar_ventana_borrado_valores_sensor() {
    var params = this.id.split('__');
	var id_sensor = params[1];
    var clase_sensor = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloSensores/muestra_ventana_borrado_valores_sensor.php", {
        clase_sensor: clase_sensor,
        id_sensor: id_sensor
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


// Borrado de valores de un sensor
function boton_sensores_borrar_valores_sensor() {
    // Clase de sensor
    var clase_sensor = $("#clase_sensor_borrado_valores_sensor").val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_borrado_valores_sensor').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return;
	}
    var nombre_sensor = $('#id_sensor_borrado_valores_sensor :selected').text();

    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_borrado_valores_sensor').val();
    var hora_inicio = $('#hora_inicio_borrado_valores_sensor').val();
    var fecha_fin = $('#fecha_fin_borrado_valores_sensor').val();
    var hora_fin = $('#hora_fin_borrado_valores_sensor').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    // Borrar valores en tiempo real
    var borrar_valores_tiempo_real = $('#borrar_valores_tiempo_real_borrado_valores_sensor').val();

    // Se borran los valores del sensor
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("Los valores guardados del sensor en el rango de fechas se borrarán. ¿Está seguro?"), TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            // Se crean los datos del formulario
            var datos_formulario = new FormData();
            datos_formulario.append("clase_sensor", clase_sensor);
            datos_formulario.append("id_sensor", id_sensor);
            datos_formulario.append("nombre_sensor", nombre_sensor);
            datos_formulario.append("borrado_valores_pendientes_borrado", VALOR_NO);
            datos_formulario.append("fecha_hora_inicio", fecha_hora_inicio);
            datos_formulario.append("fecha_hora_fin", fecha_hora_fin);
            datos_formulario.append("borrar_valores_tiempo_real", borrar_valores_tiempo_real);

            // Llamada 'ajax' POST
            $.ajax({
                url: "./src/modulos/ModulosWeb/ModuloSensores/borra_valores_sensor.php",
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

                    jInfo(resultado.msg, TLNT.Idiomas._("Información"), function(res) {
                        refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor);
                    });
                },
                error: function(request, status, err) {
                    if (status == "timeout") {
                        error_ajax_capturado = true;

                        jInfo(TLNT.Idiomas._("El borrado de valores se está realizado en segundo plano"));
                    }
                }
            });
        }
    });
}


// Ventana de recálculo de valores de clase de un sensor
function boton_sensores_mostrar_ventana_recalculo_valores_clase_sensor() {
    var params = this.id.split('__');
	var id_sensor = params[1];
    var clase_sensor = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloSensores/muestra_ventana_recalculo_valores_clase_sensor.php", {
        clase_sensor: clase_sensor,
        id_sensor: id_sensor
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


// Recálculo de valores de clase de un sensor
function boton_sensores_recalcular_valores_clase_sensor() {
    // Clase de sensor
    var clase_sensor = $("#clase_sensor_recalculo_valores_clase_sensor").val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_recalculo_valores_clase_sensor').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return;
	}
    var nombre_sensor = $('#id_sensor_recalculo_valores_clase_sensor :selected').text();

    // Fecha
    var fecha = $('#fecha_inicio_recalculo_valores_clase_sensor').val();

    // Se borran los valores del sensor
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("Los valores de clase de sensor posteriores a la fecha seleccionada se recalcularán. ¿Está seguro?"), TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            var fecha_hora = fecha + ", " + "00:00:00";
            $.post("./src/modulos/ModulosWeb/ModuloSensores/guarda_fecha_recalculo_valores_clase_sensor.php", {
                clase_sensor: clase_sensor,
                nombre_sensor: nombre_sensor,
                id_sensor: id_sensor,
                fecha_hora: fecha_hora
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor);
            });
        }
    });
}


// Ventana de envío de valores manuales
function boton_sensores_mostrar_ventana_envio_valores_manuales() {
    boton_sensores_mostrar_ventana_envio_valores_manuales_general(
        CLASE_NINGUNA,
        ID_NINGUNO,
        ORIGEN_ENVIO_VALORES_MANUALES_HERRAMIENTAS_SENSORES,
        null);
}


// Ventana de envío de valores manuales de un sensor
function boton_sensores_mostrar_ventana_envio_valores_manuales_sensor() {
    var params = this.id.split('__');
	var id_sensor = params[1];
    var clase_sensor = params[2];

    boton_sensores_mostrar_ventana_envio_valores_manuales_general(
        clase_sensor,
        id_sensor,
        ORIGEN_ENVIO_VALORES_MANUALES_DETALLES_TABLA_SENSORES,
        null);
}


// Ventana de envío de valores manuales de un sensor (widget de valor digital de sensor)
function boton_sensores_mostrar_ventana_envio_valores_manuales_sensor_widget() {
    var params = this.id.split('__');
	var id_sensor = params[1];
    var clase_sensor = params[2];
    var id_pestanya_widgets = params[3];

    boton_sensores_mostrar_ventana_envio_valores_manuales_general(
        clase_sensor,
        id_sensor,
        ORIGEN_ENVIO_VALORES_MANUALES_WIDGET,
        id_pestanya_widgets);
}


// Ventana de envío de valores manuales de un sensor (mapa)
function boton_sensores_mostrar_ventana_envio_valores_manuales_sensor_mapa() {
    var params = this.id.split('__');
	var id_sensor = params[1];
    var clase_sensor = params[2];
    var id_mapa = params[3];

    boton_sensores_mostrar_ventana_envio_valores_manuales_general(
        clase_sensor,
        id_sensor,
        ORIGEN_ENVIO_VALORES_MANUALES_MAPA,
        id_mapa);
}


// Ventana de envío de valores manuales general
function boton_sensores_mostrar_ventana_envio_valores_manuales_general(clase_sensor, id_sensor, origen_envio_valores_manuales, id_origen_envio_valores_manuales) {
    $.post("./src/modulos/ModulosWeb/ModuloSensores/muestra_ventana_envio_valores_manuales_sensor.php", {
        clase_sensor: clase_sensor,
        id_sensor: id_sensor,
        origen_envio_valores_manuales: origen_envio_valores_manuales,
        id_origen_envio_valores_manuales: id_origen_envio_valores_manuales
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
        // (se establece directamente los eventos de las ventanas modales de sensores,
        //  porque si no desde el widget, sólo establece los eventos de las ventanas del módulo Personal, no de Sensores)
        TLNT.Navegacion.establece_eventos_ventanas_modales_sensores();
        TLNT.Navegacion.establece_eventos_ventanas_modales_modulos();
	});
}


// Envío de valores manuales de un sensor
function boton_sensores_enviar_valores_manuales_sensor() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Origen del envío de los valores manuales
    var origen_envio_valores_manuales = $("#parametros_ventana_envio_valores_manuales").attr("origen_envio_valores_manuales");
    var id_origen_envio_valores_manuales = $("#parametros_ventana_envio_valores_manuales").attr("id_origen_envio_valores_manuales");

    // Clase de sensor
    var clase_sensor = $("#clase_sensor_envio_valores_manuales_sensor").val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Identificador de sensor
    var id_sensor = $('#id_sensor_envio_valores_manuales_sensor').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return;
	}

    // Fecha y valor
    var fecha = $('#fecha_envio_valores_manuales_sensor').val();
    var hora = $('#hora_envio_valores_manuales_sensor').val();
    var fecha_hora = fecha + ", " + hora + ":00";
    var cadena_valores = $('#valores_envio_valores_manuales_sensor').val();
    var cadena_incrementos = $('#incrementos_envio_valores_manuales_sensor').val();
    var tipo_incrementos = $("#tipo_incrementos_envio_valores_manuales_sensor").val();
    var tipo_horas_incrementos = $("#tipo_horas_incrementos_envio_valores_manuales_sensor").val();
    var horas_incrementos = $('#horas_incrementos_envio_valores_manuales_sensor').val();

    // Tipo de valores del sensor
    var tipo_valores_sensor = $("#parametros_ventana_envio_valores_manuales").attr("tipo_valores_sensor");
    var numero_valores_clase_sensor = dame_numero_valores_clase_sensor(clase_sensor);
    switch (tipo_valores_sensor) {
        case TIPO_VALORES_SENSOR_PUNTUALES: {
            var valores = cadena_valores.split(SEPARADOR_PARAMETROS_VALORES);
            if (valores.length != numero_valores_clase_sensor) {
                jAlert(TLNT.Idiomas._("El número de valores a enviar no coincide con el número de valores del sensor") +
                    " (" + numero_valores_clase_sensor + ")");
                return;
            }
            for (var i = 0; i < valores.length; i++) {
                var valor = valores[i];
                if (PATRON_NUMERO_REAL.test(valor) == false) {
                    jAlert(TLNT.Idiomas._("Los valores a enviar deben ser números reales"));
                    return;
                }
            }
            break;
        }
        case TIPO_VALORES_SENSOR_INCREMENTALES: {
            var incrementos = cadena_incrementos.split(SEPARADOR_PARAMETROS_VALORES);
            if (incrementos.length != numero_valores_clase_sensor) {
                jAlert(TLNT.Idiomas._("El número de incrementos a enviar no coincide con el número de valores del sensor") +
                    " (" + numero_valores_clase_sensor + ")");
                return;
            }
            for (var i = 0; i < incrementos.length; i++) {
                var incremento = incrementos[i];
                if (PATRON_NUMERO_REAL.test(incremento) == false) {
                    jAlert(TLNT.Idiomas._("Los incrementos a enviar deben ser números reales"));
                    return;
                }
            }
            var segundos_incrementos = Math.round(horas_incrementos * 3600);
            switch (tipo_horas_incrementos) {
                case TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO: {
                    if (segundos_incrementos <= 0) {
                        jAlert(TLNT.Idiomas._("El número de horas de incrementos debe ser mayor que 0"));
                        return;
                    }
                    break;
                }
            }
            switch (tipo_incrementos) {
                case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL: {
                    if (segundos_incrementos == 0) {
                        jAlert(TLNT.Idiomas._("El tipo de incrementos debe ser fecha final si las horas de incrementos son variables"));
                        return;
                    }
                    break;
                }
            }
        }
    }

    // Se envían los valores manuales al sensor
    $.post("./src/modulos/ModulosWeb/ModuloSensores/envia_valores_manuales_sensor.php", {
        id_sensor: id_sensor,
        fecha_hora: fecha_hora,
        valores: cadena_valores,
        incrementos: cadena_incrementos,
        tipo_incrementos: tipo_incrementos,
        horas_incrementos: horas_incrementos
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg, TLNT.Idiomas._("Información"), function(res) {
            switch (origen_envio_valores_manuales) {
                case ORIGEN_ENVIO_VALORES_MANUALES_HERRAMIENTAS_SENSORES:
                case ORIGEN_ENVIO_VALORES_MANUALES_DETALLES_TABLA_SENSORES: {
                    refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor);
                    break;
                }
                case ORIGEN_ENVIO_VALORES_MANUALES_MAPA: {
                    switch (id_origen_envio_accion) {
                        case ID_MAPA_MAPA_SECCION: {
                            boton_actualizar_mapa();
                            break;
                        }
                        case ID_MAPA_MAPA_INSTALACIONES: {
                            boton_localizaciones_actualizar_mapa_instalaciones();
                            break;
                        }
                        case ID_MAPA_IMAGEN_INSTALACION: {
                            boton_localizaciones_actualizar_imagen_instalacion();
                            break;
                        }
                    };
                    break;
                }
                case ORIGEN_ENVIO_VALORES_MANUALES_WIDGET: {
                    actualiza_cuadricula_widgets(id_origen_envio_valores_manuales, false);
                    break;
                }
            }
        });
    });
}


// Botones de envío de acciones de herramientas de los sensores
function boton_sensores_envia_accion_herramientas_sensor() {
	var params = this.id.split('__');
    var boton = params[0];
    var id_sensor = params[1];
    var tipo_sensor = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloSensores/envia_accion_herramientas_sensor.php", {
		boton: boton,
		id_sensor: id_sensor,
        tipo_sensor: tipo_sensor
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
	});
}


// Muestra la tabla de sensores aplicando el filtro
function boton_sensores_filtro_sensores_tabla() {
    actualiza_tabla_nodos(TIPO_NODO_SENSOR);
}


// Muestra la tabla de grupos aplicando el filtro
function boton_sensores_filtro_grupos_tabla() {
    actualiza_tabla_nodos(TIPO_NODO_GRUPO_SENSORES);
}


// Muestra la ventana de asignación de localización
function boton_sensores_mostrar_ventana_asignacion_localizacion() {
    boton_mostrar_ventana_asignacion_localizacion_nodos(TIPO_NODO_SENSOR);
}


// Muestra la ventana de asignación de grupo
function boton_sensores_mostrar_ventana_asignacion_grupo() {
    boton_mostrar_ventana_asignacion_grupo_nodos(TIPO_NODO_SENSOR);
}


// Muestra el mapa de sensores aplicando el filtro
function boton_sensores_filtro_sensores_mapa() {
    boton_actualizar_mapa();
}