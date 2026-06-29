//
// Funciones de equipos de las instalaciones
//


function boton_localizaciones_mostrar_ventana_anyadir_modificar_equipo_instalacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_instalacion = params[1];
	var id_equipo = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/muestra_ventana_anyadir_modificar_equipo.php", {
        id_instalacion: id_instalacion,
        id_equipo: id_equipo
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


function boton_localizaciones_eliminar_equipo_instalacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_instalacion = params[1];
	var id_equipo = params[2];
    var nombre_equipo = $(this).attr('nombre_equipo');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el equipo?") + "\n(" + escapeHtml(nombre_equipo) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/elimina_equipo.php", {
                id_instalacion: id_instalacion,
                id_equipo: id_equipo
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_instalacion_detalles(id_instalacion);
			});
		}
	});
}


function boton_localizaciones_anyadir_modificar_equipo_instalacion() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Nombre y descripción
    var nombre = $('#nombre_equipo_instalacion').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_equipo_instalacion").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_equipo_instalacion').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_equipo_instalacion").addClass('data-check-failed');
        return;
    }

    // Equipo padre
    var id_equipo_padre = $('#id_equipo_padre_equipo_instalacion').val();

    // Se recuperan los identificadores de los sensores y actuadores seleccionados
    var ids_sensores = [];
    $("#ids_sensores_equipo_instalacion option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores.push($(this).val());
        }
    });
    var ids_actuadores = [];
    $("#ids_actuadores_equipo_instalacion option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_actuadores.push($(this).val());
        }
    });

    // Estado
    var estado = $('#estado_equipo_instalacion').val();

    // Observaciones
    var observaciones = $('#observaciones_equipo_instalacion').val();
    if (comprueba_longitud_cadena(observaciones, NUMERO_MAXIMO_CARACTERES_OBSERVACIONES) == false) {
        $("#observaciones_equipo_instalacion").addClass('data-check-failed');
        return;
    }

    // Icono de imagen (mapa)
    var icono_imagen = $('#icono_imagen').val();

    // Posición en imagen (mapa)
    var mostrar_en_imagen = $('#mostrar_en_mapa').val();
    var latitud_imagen = $('#latitud_mapa').val();
    var longitud_imagen = $('#longitud_mapa').val();
    var zoom_imagen = $('#zoom_mapa').val();

    // Parámetros de la ventana
	var anyadir_equipo = $("#parametros_ventana_anyadir_modificar_equipo_instalacion").attr("anyadir_equipo");
    var id_instalacion = $("#parametros_ventana_anyadir_modificar_equipo_instalacion").attr("id_instalacion");
    var id_equipo = $("#parametros_ventana_anyadir_modificar_equipo_instalacion").attr("id_equipo");
    var id_equipo_padre_anterior = $("#parametros_ventana_anyadir_modificar_equipo_instalacion").attr("id_equipo_padre");

    // Se añade o modifica el equipo
    if (anyadir_equipo == true) {
        $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/anyade_equipo.php", {
            nombre: nombre,
            descripcion: descripcion,
            id_instalacion: id_instalacion,
            id_equipo_padre: id_equipo_padre,
            ids_sensores: ids_sensores,
            ids_actuadores: ids_actuadores,
            estado: estado,
            observaciones: observaciones,
            icono_imagen: icono_imagen,
            mostrar_en_imagen: mostrar_en_imagen,
            latitud_imagen: latitud_imagen,
            longitud_imagen: longitud_imagen,
            zoom_imagen: zoom_imagen
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_instalacion_detalles(id_instalacion);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/modifica_equipo.php", {
            id_equipo: id_equipo,
            nombre: nombre,
            descripcion: descripcion,
            id_instalacion: id_instalacion,
            id_equipo_padre: id_equipo_padre,
            ids_sensores: ids_sensores,
            ids_actuadores: ids_actuadores,
            estado: estado,
            observaciones: observaciones,
            icono_imagen: icono_imagen,
            mostrar_en_imagen: mostrar_en_imagen,
            latitud_imagen: latitud_imagen,
            longitud_imagen: longitud_imagen,
            zoom_imagen: zoom_imagen,
            id_equipo_padre_anterior: id_equipo_padre_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_instalacion_detalles(id_instalacion);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_localizaciones_actualizar_tabla_equipos_instalacion() {
    var params = this.id.split('__');
	var id_instalacion = params[1];

	actualiza_tabla_equipos_instalacion(id_instalacion);
}


function actualiza_tabla_equipos_instalacion(id_instalacion) {
	$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/dame_tabla_equipos.php", {
		id_instalacion: id_instalacion
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_equipos = "equipos-instalacion" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_instalacion;
        $('#' + id_elemento_equipos).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


//
// Funciones de anotaciones de equipos
//


function boton_localizaciones_mostrar_ventana_anyadir_modificar_anotacion_equipo_instalacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_instalacion = params[1];
    var id_equipo = params[2];
	var id_anotacion = params[3];

    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/muestra_ventana_anyadir_modificar_anotacion.php", {
        id_instalacion: id_instalacion,
        id_equipo: id_equipo,
        id_anotacion: id_anotacion
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


function boton_localizaciones_eliminar_anotacion_equipo_instalacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_instalacion = params[1];
	var id_equipo = params[2];
    var id_anotacion = params[3];
    var nombre_equipo = $(this).attr('nombre_equipo');
    var hora_anotacion = $(this).attr('hora_anotacion');

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la anotación?") + "\n(" + escapeHtml(nombre_equipo + " (" + hora_anotacion + ")") + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/elimina_anotacion.php", {
                id_instalacion: id_instalacion,
                id_equipo: id_equipo,
                id_anotacion: id_anotacion
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_anotaciones_equipos_instalacion(id_instalacion, id_equipo);
			});
		}
	});
}


function boton_localizaciones_anyadir_modificar_anotacion_equipo_instalacion() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Fecha y hora
    var fecha = $('#fecha_anotacion_equipo_instalacion').val();
    var hora = $('#hora_anotacion_equipo_instalacion').val();
    var fecha_hora = fecha + ", " + hora + ":00";

    // Texto
    var texto = $('#texto_anotacion_equipo_instalacion').val();
    if (comprueba_longitud_cadena(texto, NUMERO_MAXIMO_CARACTERES_TEXTO_ANOTACION_EQUIPO_INSTALACION) == false) {
        $("#texto_anotacion_equipo_instalacion").addClass('data-check-failed');
        return;
    }

    // Foto
    var foto = $('#foto_anotacion_equipo_instalacion').val();

    // Parámetros de la ventana
	var anyadir_anotacion = $("#parametros_ventana_anyadir_modificar_anotacion_equipo_instalacion").attr("anyadir_anotacion");
    var id_instalacion = $("#parametros_ventana_anyadir_modificar_anotacion_equipo_instalacion").attr("id_instalacion");
    var id_equipo = $("#parametros_ventana_anyadir_modificar_anotacion_equipo_instalacion").attr("id_equipo");
    var id_anotacion = $("#parametros_ventana_anyadir_modificar_anotacion_equipo_instalacion").attr("id_anotacion");
    var foto_anterior = $("#parametros_ventana_anyadir_modificar_anotacion_equipo_instalacion").attr("foto");

    // Se añade o modifica la anotación
    if (anyadir_anotacion == true) {
        // Se comprueba la foto
        if (foto == VALOR_SI) {
            if ($('#fichero_foto_anotacion_equipo_instalacion_text').val() == "") {
                jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen"));
                return;
            }
            else
            {
                var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO, "fichero_foto_anotacion_equipo_instalacion_file");
                if (imagen_correcta == false) {
                    $('#fichero_foto_anotacion_equipo_instalacion_text').addClass('data-check-failed');
                    $('#fichero_foto_anotacion_equipo_instalacion_text').val("");
                    return;
                }
            }
        }

        // Se añade la anotación
        $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/anyade_anotacion.php", {
            id_instalacion: id_instalacion,
            id_equipo: id_equipo,
            fecha_hora: fecha_hora,
            texto: texto,
            foto: foto
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Identificador de anotación añadida
            var id_anotacion = resultado.id_anotacion;

            // Se guarda la imagen
            if (foto == VALOR_SI) {
                var id_origen = [
                    id_instalacion,
                    id_equipo,
                    id_anotacion].join(SEPARADOR_PARAMETROS_SIMPLES);
                var control_fichero_imagen = $('#fichero_foto_anotacion_equipo_instalacion_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO, id_origen, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
            }

            jInfo(resultado.msg);
            actualiza_tabla_anotaciones_equipos_instalacion(id_instalacion, id_equipo);
        });
    }
    else {
        // Parámetros de la modificación de la anotación
        var parametros_anotacion = [];
        parametros_anotacion["id_instalacion"] = id_instalacion;
        parametros_anotacion["id_equipo"] = id_equipo;
        parametros_anotacion["fecha_hora"] = fecha_hora;
        parametros_anotacion["texto"] = texto;
        parametros_anotacion["foto"] = foto;
        // Parámetros extra
        parametros_anotacion["foto_anterior"] = foto_anterior;

        // Se muestra un mensaje de aviso antes de modificar la anotación en los siguientes casos:
        // - Eliminación de foto
        var mensaje_aviso = "";
        if ((foto_anterior == VALOR_SI) && (foto == VALOR_NO)) {
            mensaje_aviso += "\n(" + TLNT.Idiomas._("se eliminará la foto") + ")";
        }
        if (mensaje_aviso == "") {
            modifica_anotacion_equipo_instalacion(id_anotacion, parametros_anotacion);
        }
        else {
            // Se muestra un mensaje de aviso y se confirma la modificación de la anotación
            mensaje_aviso = TLNT.Idiomas._("¿Está seguro de que desea modificar la anotación?") + mensaje_aviso;
            jConfirmAcceptCancelAlert(mensaje_aviso, TLNT.Idiomas._("Pregunta"), function(res) {
                if (res == true) {
                    modifica_anotacion_equipo_instalacion(id_anotacion, parametros_anotacion);
                }
            });
        }
    }
}


function modifica_anotacion_equipo_instalacion(id_anotacion, parametros_anotacion) {
    // Se comprueba la foto
    if ((parametros_anotacion["foto_anterior"] == VALOR_NO) &&
        (parametros_anotacion["foto"] == VALOR_SI)) {
        if ($('#fichero_foto_anotacion_equipo_instalacion_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de foto"));
            return;
        }
    }
    if (parametros_anotacion["foto"] == VALOR_SI) {
        if ($('#fichero_foto_anotacion_equipo_instalacion_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO, "fichero_foto_anotacion_equipo_instalacion_file");
            if (imagen_correcta == false) {
                $('#fichero_foto_anotacion_equipo_instalacion_text').addClass('data-check-failed');
                $('#fichero_foto_anotacion_equipo_instalacion_text').val("");
                return;
            }
        }
    }

    // Se modifica la anotación
    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/modifica_anotacion.php", {
        id_anotacion: id_anotacion,
        id_instalacion: parametros_anotacion["id_instalacion"],
        id_equipo: parametros_anotacion["id_equipo"],
        fecha_hora: parametros_anotacion["fecha_hora"],
        texto: parametros_anotacion["texto"],
        foto: parametros_anotacion["foto"],
        // Parámetros extra
        foto_anterior: parametros_anotacion["foto_anterior"]
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se guarda la foto
        if (parametros_anotacion["foto"] == VALOR_SI) {
            if ($('#fichero_foto_anotacion_equipo_instalacion_text').val() != "") {
                var id_origen = [
                    parametros_anotacion["id_instalacion"],
                    parametros_anotacion["id_equipo"],
                    id_anotacion].join(SEPARADOR_PARAMETROS_SIMPLES);
                var control_fichero_imagen = $('#fichero_foto_anotacion_equipo_instalacion_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO, id_origen, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
            }
        }

        // Se muestra el mensaje y se actualiza la tabla de anotaciones
        jInfo(resultado.msg);
        actualiza_tabla_anotaciones_equipos_instalacion(parametros_anotacion["id_instalacion"], parametros_anotacion["id_equipo"])
        $('#ventana_modal').modal('hide');
    });
}


function boton_localizaciones_actualizar_tabla_anotaciones_equipo_instalacion() {
    var params = this.id.split('__');
	var id_instalacion = params[1];
    var id_equipo = params[2];

	actualiza_tabla_anotaciones_equipos_instalacion(id_instalacion, id_equipo);
}


function actualiza_tabla_anotaciones_equipos_instalacion(id_instalacion, id_equipo) {
	$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/dame_tabla_anotaciones.php", {
        id_instalacion: id_instalacion,
		id_equipo: id_equipo
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_anotaciones = "anotaciones-equipo" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_equipo;
        $('#' + id_elemento_anotaciones).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}
