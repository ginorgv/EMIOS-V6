/*
 * Funciones de localizaciones
 *
 */


// Muestra la tabla de localizaciones aplicando el filtro
function boton_localizaciones_filtro_localizaciones_tabla() {
    boton_localizaciones_actualizar_tabla_localizaciones();
}


function boton_localizaciones_mostrar_ventana_anyadir_modificar_localizacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_localizacion = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/muestra_ventana_anyadir_modificar_localizacion.php", {
        id_localizacion: id_localizacion,
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


function boton_localizaciones_eliminar_localizacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_localizacion = params[1];
    var nombre_localizacion = $(this).attr('nombre_localizacion');

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la localización?") + "\n(" + escapeHtml(nombre_localizacion) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/elimina_localizacion.php", {
                id_localizacion: id_localizacion
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
				actualiza_tabla_localizaciones(null);
			});
		}
	});
}


function boton_localizaciones_anyadir_modificar_localizacion() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Nombre y descripción
    var nombre = $('#nombre_localizacion').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_localizacion").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_localizacion').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_localizacion").addClass('data-check-failed');
        return;
    }

    // Opciones de mapa
    var mapa_personalizado = $('#mapa_personalizado_localizacion').val();
    var tipo_mapa = $('#tipo_mapa_localizacion').val();
    var nombre_mapa = $('#nombre_mapa_localizacion').val();
    var factor_reduccion_imagen_mapa_local = factor_reduccion_imagen_mapa_local = $('#factor_reduccion_imagen_mapa_local_localizacion').val();
    if (parseFloat(factor_reduccion_imagen_mapa_local) < 1) {
        jAlert(TLNT.Idiomas._('El factor de reducción de imagen de mapa debe ser mayor o igual que 1'));
        return;
    }
    var etiquetas_mapa = $('#etiquetas_mapa_localizacion').val();

    // Mapa (posición y zoom por defecto)
    var latitud_mapa_defecto = $('#latitud_mapa_defecto').val();
    var longitud_mapa_defecto = $('#longitud_mapa_defecto').val();
    var zoom_mapa_defecto = $('#zoom_mapa_defecto').val();

    // Posición en mapa
    var mostrar_en_mapa = $('#mostrar_en_mapa').val();
    var latitud_mapa = $('#latitud_mapa').val();
    var longitud_mapa = $('#longitud_mapa').val();
    var zoom_mapa = $('#zoom_mapa').val();

    // Se recupera la información de los ratios
    var info_ratios = [];
    var numero_ratios_localizacion = parseInt($("#parametros_ratios").attr("numero_ratios"));
    var ids_ratios_localizacion = $("#parametros_ratios").attr("ids_ratios").split(",");
    var tipos_ratios_localizacion = $("#parametros_ratios").attr("tipos_ratios").split(",");
    for (var i = 0; i < numero_ratios_localizacion; i++) {
        var id_ratio = ids_ratios_localizacion[i];
        var tipo_ratio = tipos_ratios_localizacion[i];
        var valor_sensor_ratio = $("#valor_sensor_ratio__" + id_ratio).val();
        switch (tipo_ratio) {
            case TIPO_RATIO_FIJO: {
                if (valor_sensor_ratio == "") {
                    continue;
                }
                if (valor_sensor_ratio <= 0) {
                    jAlert(TLNT.Idiomas._("Los valores de los ratios deben ser mayores que 0"));
                    return;
                }
                break;
            }
            case TIPO_RATIO_VARIABLE: {
                if (valor_sensor_ratio == ID_NINGUNO) {
                    continue;
                }
                break;
            }
        }
        var info_ratio = {};
        info_ratio["id"] = id_ratio;
        info_ratio["tipo"] = tipo_ratio;
        info_ratio["valor_sensor"] = valor_sensor_ratio;
        info_ratios.push(info_ratio);
    }

    // Parámetros de la ventana
    var anyadir_localizacion = $("#parametros_ventana_anyadir_modificar_localizacion").attr("anyadir_localizacion");
	var id_localizacion = $("#parametros_ventana_anyadir_modificar_localizacion").attr("id_localizacion");
    var mapa_personalizado_anterior = $("#parametros_ventana_anyadir_modificar_localizacion").attr("mapa_personalizado");
    var tipo_mapa_anterior = $("#parametros_ventana_anyadir_modificar_localizacion").attr("tipo_mapa");
    var nombre_mapa_anterior = $("#parametros_ventana_anyadir_modificar_localizacion").attr("nombre_mapa");
    var factor_reduccion_imagen_mapa_local_anterior = $("#parametros_ventana_anyadir_modificar_localizacion").attr("factor_reduccion_imagen_mapa_local");
    var latitud_mapa_anterior = $("#parametros_ventana_anyadir_modificar_localizacion").attr("latitud_mapa");
    var longitud_mapa_anterior = $("#parametros_ventana_anyadir_modificar_localizacion").attr("longitud_mapa");

    // Se añade o modifica la localización
    if (anyadir_localizacion == true) {
        // Flag de duplicar localización
        var id_localizacion_anterior = id_localizacion;
        var duplicar_localizacion = (id_localizacion_anterior != ID_NINGUNO);

        // Se comprueba la imagen del mapa local
        var duplicar_imagen_mapa_local = false;
        if ((mapa_personalizado == VALOR_SI) && (tipo_mapa == TIPO_MAPA_LOCAL)) {
            if ($('#fichero_imagen_mapa_localizacion_text').val() == "") {
                if ((duplicar_localizacion == true) &&
                    (mapa_personalizado_anterior == VALOR_SI) && (tipo_mapa_anterior == TIPO_MAPA_LOCAL)) {
                    // Nota: Es un duplicado y ya había imagen de mapa local: no hace faltar subir un nuevo fichero de imagen,
                    // se duplicará la imagen anterior
                    duplicar_imagen_mapa_local = true;
                }
                else {
                    jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen para el mapa local"));
                    return;
                }
            }
            else {
                var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_LOCALIZACION_MAPA, "fichero_imagen_mapa_localizacion_file");
                if (imagen_correcta == false) {
                    $('#fichero_imagen_mapa_localizacion_text').addClass('data-check-failed');
                    $('#fichero_imagen_mapa_localizacion_text').val("");
                    return;
                }
            }
        }

        // Se añade la localización
        $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/anyade_localizacion.php", {
            nombre: nombre,
            descripcion: descripcion,
            mapa_personalizado: mapa_personalizado,
            tipo_mapa: tipo_mapa,
            nombre_mapa: nombre_mapa,
            factor_reduccion_imagen_mapa_local: factor_reduccion_imagen_mapa_local,
            etiquetas_mapa: etiquetas_mapa,
            latitud_mapa_defecto: latitud_mapa_defecto,
            longitud_mapa_defecto: longitud_mapa_defecto,
            zoom_mapa_defecto: zoom_mapa_defecto,
            mostrar_en_mapa: mostrar_en_mapa,
            latitud_mapa: latitud_mapa,
            longitud_mapa: longitud_mapa,
            zoom_mapa: zoom_mapa,
            info_ratios: info_ratios,
            id_localizacion_anterior: id_localizacion_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Identificador de localización añadida
            var id_localizacion = resultado.id_localizacion;

            // Se guarda la imagen del mapa local
            if ((mapa_personalizado == VALOR_SI) && (tipo_mapa == TIPO_MAPA_LOCAL)) {
                if (duplicar_imagen_mapa_local == false) {
                    var control_fichero_imagen = $('#fichero_imagen_mapa_localizacion_file')[0];
                    var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_LOCALIZACION_MAPA, id_localizacion, control_fichero_imagen);
                    if (imagen_guardada_correcta == false) {
                        return;
                    }
                }
                else {
                    var imagen_duplicada_correcta = duplica_imagen_base_datos(ORIGEN_IMAGEN_LOCALIZACION_MAPA, id_localizacion_anterior, id_localizacion);
                    if (imagen_duplicada_correcta == false) {
                        return;
                    }
                }
            }

            // Se muestra el mensaje y se actualiza la tabla de localizaciones
            jInfo(resultado.msg);
            actualiza_tabla_localizaciones(null);
        });
    }
    else {
        // Modificación de posición y zoom del mapa por defecto por cambio de mapa personalizado o tipo de mapa
        if ((mapa_personalizado_anterior != mapa_personalizado) || (tipo_mapa_anterior != tipo_mapa)) {
            latitud_mapa_defecto = 0.0;
            longitud_mapa_defecto = 0.0;
            zoom_mapa_defecto = ZOOM_MAPA_DEFECTO;
        }

        // Parámetros de la modificación de la localizacion
        var parametros_localizacion = [];
        parametros_localizacion["nombre"] = nombre;
        parametros_localizacion["descripcion"] = descripcion;
        parametros_localizacion["mapa_personalizado"] = mapa_personalizado;
        parametros_localizacion["tipo_mapa"] = tipo_mapa;
        parametros_localizacion["nombre_mapa"] = nombre_mapa;
        parametros_localizacion["factor_reduccion_imagen_mapa_local"] = factor_reduccion_imagen_mapa_local;
        parametros_localizacion["etiquetas_mapa"] = etiquetas_mapa;
        parametros_localizacion["latitud_mapa_defecto"] = latitud_mapa_defecto;
        parametros_localizacion["longitud_mapa_defecto"] = longitud_mapa_defecto;
        parametros_localizacion["zoom_mapa_defecto"] = zoom_mapa_defecto;
        parametros_localizacion["mostrar_en_mapa"] = mostrar_en_mapa;
        parametros_localizacion["latitud_mapa"] = latitud_mapa;
        parametros_localizacion["longitud_mapa"] = longitud_mapa;
        parametros_localizacion["zoom_mapa"] = zoom_mapa;
        parametros_localizacion["info_ratios"] = info_ratios;
        // Parámetros extra
        parametros_localizacion["mapa_personalizado_anterior"] = mapa_personalizado_anterior;
        parametros_localizacion["tipo_mapa_anterior"] = tipo_mapa_anterior;
        parametros_localizacion["nombre_mapa_anterior"] = nombre_mapa_anterior;
        parametros_localizacion["factor_reduccion_imagen_mapa_local_anterior"] = factor_reduccion_imagen_mapa_local_anterior;
        parametros_localizacion["latitud_mapa_anterior"] = latitud_mapa_anterior;
        parametros_localizacion["longitud_mapa_anterior"] = longitud_mapa_anterior;

        // Se muestra un mensaje de aviso antes de modificar la localización en los siguientes casos:
        // - Cambio de tipo de mapa de local a internet
        // - Eliminación de mapa personalizado con mapa local (anterior)
        var mensaje_aviso = "";
        if ((mapa_personalizado_anterior == VALOR_SI) && (mapa_personalizado == VALOR_SI) &&
            (tipo_mapa_anterior == TIPO_MAPA_LOCAL) && (tipo_mapa == TIPO_MAPA_INTERNET)) {
            mensaje_aviso += "\n(" + TLNT.Idiomas._("el tipo de mapa ha cambiado y se eliminará la imagen del mapa local") + ")";
        }
        if ((mapa_personalizado_anterior == VALOR_SI) && (mapa_personalizado == VALOR_NO) &&
            (tipo_mapa_anterior == TIPO_MAPA_LOCAL)) {
            mensaje_aviso += "\n(" + TLNT.Idiomas._("se ha desactivado el mapa personalizado y se eliminará la imagen del mapa local") + ")";
        }
        if (mensaje_aviso == "") {
            modifica_localizacion(id_localizacion, parametros_localizacion);
        }
        else {
            // Se muestra un mensaje de aviso y se confirma la modificación de la localización
            mensaje_aviso = TLNT.Idiomas._("¿Está seguro de que desea modificar la localización?") + mensaje_aviso;
            jConfirmAcceptCancelAlert(mensaje_aviso, TLNT.Idiomas._("Pregunta"), function(res) {
                if (res == true) {
                    modifica_localizacion(id_localizacion, parametros_localizacion);
                }
            });
        }
    }
}


function modifica_localizacion(id_localizacion, parametros_localizacion) {
    // Se comprueba la imagen del mapa local
    if (((parametros_localizacion["mapa_personalizado_anterior"] == VALOR_NO) || (parametros_localizacion["tipo_mapa_anterior"] == TIPO_MAPA_INTERNET)) &&
        ((parametros_localizacion["mapa_personalizado"] == VALOR_SI) && (parametros_localizacion["tipo_mapa"] == TIPO_MAPA_LOCAL))) {
        if ($('#fichero_imagen_mapa_localizacion_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen para el mapa local"));
            return;
        }
    }
    if ((parametros_localizacion["mapa_personalizado"] == VALOR_SI) && (parametros_localizacion["tipo_mapa"] == TIPO_MAPA_LOCAL)) {
        if ($('#fichero_imagen_mapa_localizacion_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_LOCALIZACION_MAPA, "fichero_imagen_mapa_localizacion_file");
            if (imagen_correcta == false) {
                $('#fichero_imagen_mapa_localizacion_text').addClass('data-check-failed');
                $('#fichero_imagen_mapa_localizacion_text').val("");
                return;
            }
        }
    }

    // Se modifica la localización
    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/modifica_localizacion.php", {
        id_localizacion: id_localizacion,
        nombre: parametros_localizacion["nombre"],
        descripcion: parametros_localizacion["descripcion"],
        mapa_personalizado: parametros_localizacion["mapa_personalizado"],
        tipo_mapa: parametros_localizacion["tipo_mapa"],
        nombre_mapa: parametros_localizacion["nombre_mapa"],
        factor_reduccion_imagen_mapa_local: parametros_localizacion["factor_reduccion_imagen_mapa_local"],
        etiquetas_mapa: parametros_localizacion["etiquetas_mapa"],
        latitud_mapa_defecto: parametros_localizacion["latitud_mapa_defecto"],
        longitud_mapa_defecto: parametros_localizacion["longitud_mapa_defecto"],
        zoom_mapa_defecto: parametros_localizacion["zoom_mapa_defecto"],
        mostrar_en_mapa: parametros_localizacion["mostrar_en_mapa"],
        latitud_mapa: parametros_localizacion["latitud_mapa"],
        longitud_mapa: parametros_localizacion["longitud_mapa"],
        zoom_mapa: parametros_localizacion["zoom_mapa"],
        info_ratios: parametros_localizacion["info_ratios"],
        // Parámetros extra
        mapa_personalizado_anterior: parametros_localizacion["mapa_personalizado_anterior"],
        tipo_mapa_anterior: parametros_localizacion["tipo_mapa_anterior"]
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se guarda la imagen del mapa local
        var mapa_personalizado_modificado = false;
        var tipo_mapa_modificado = false;
        var imagen_mapa_modificada = false;
        var factor_reduccion_imagen_mapa_local_modificado = false;
        if (parametros_localizacion["mapa_personalizado_anterior"] != parametros_localizacion["mapa_personalizado"]) {
            mapa_personalizado_modificado = true;
        }
        if (parametros_localizacion["mapa_personalizado"] == VALOR_SI) {
            if (parametros_localizacion["tipo_mapa_anterior"] != parametros_localizacion["tipo_mapa"]) {
                tipo_mapa_modificado = true;
            }
            switch (parametros_localizacion["tipo_mapa"]) {
                case TIPO_MAPA_LOCAL: {
                    if ($('#fichero_imagen_mapa_localizacion_text').val() != "") {
                        // Nota: La carga de la imagen tarda unos milisegundos (hasta entonces no se muestra la barra de progreso)
                        var control_fichero_imagen = $('#fichero_imagen_mapa_localizacion_file')[0];
                        var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_LOCALIZACION_MAPA, id_localizacion, control_fichero_imagen);
                        if (imagen_guardada_correcta == false) {
                            return;
                        }
                        imagen_mapa_modificada = true;
                    }
                    if ((parametros_localizacion["factor_reduccion_imagen_mapa_local_anterior"] != parametros_localizacion["factor_reduccion_imagen_mapa_local"])) {
                        factor_reduccion_imagen_mapa_local_modificado = true;
                    }
                    break;
                }
                case TIPO_MAPA_INTERNET: {
                    if ((parametros_localizacion["nombre_mapa_anterior"] != parametros_localizacion["nombre_mapa"])) {
                        imagen_mapa_modificada = true;
                    }
                    break;
                }
            }
        }

        // Se muestra el mensaje y se actualiza la tabla de localizaciones
        jInfo(resultado.msg);
        actualiza_tabla_localizacion_detalles(id_localizacion);

        // Si se ha modificado el del mapa de la localización no se cierra la ventana
        // (para poder modificar los parámetros del mapa sin tener que volver a abrir la ventana)
        var mapa_modificado = ((parametros_localizacion["mapa_personalizado"] == VALOR_SI) && (mapa_personalizado_modificado == true)) ||
            (tipo_mapa_modificado == true) ||
            (imagen_mapa_modificada == true) ||
            (factor_reduccion_imagen_mapa_local_modificado == true);
        var mantener_ventana_modal_abierta = (mapa_modificado == true) ||
            (parametros_localizacion["latitud_mapa_anterior"] != parametros_localizacion["latitud_mapa"]) ||
            (parametros_localizacion["longitud_mapa_anterior"] != parametros_localizacion["longitud_mapa"]);

        // Si se mantiene abierta la ventana modal, se establecen los parámetros actuales de la ventana
        if (mantener_ventana_modal_abierta == true) {
            $("#parametros_ventana_anyadir_modificar_localizacion").attr("mapa_personalizado", parametros_localizacion["mapa_personalizado"]);
            $("#parametros_ventana_anyadir_modificar_localizacion").attr("tipo_mapa", parametros_localizacion["tipo_mapa"]);
            $("#parametros_ventana_anyadir_modificar_localizacion").attr("nombre_mapa", parametros_localizacion["nombre_mapa"]);
            $("#parametros_ventana_anyadir_modificar_localizacion").attr("factor_reduccion_imagen_mapa_local", parametros_localizacion["factor_reduccion_imagen_mapa_local"]);
            $("#parametros_ventana_anyadir_modificar_localizacion").attr("longitud_mapa", parametros_localizacion["longitud_mapa"]);
            $("#parametros_ventana_anyadir_modificar_localizacion").attr("latitud_mapa", parametros_localizacion["latitud_mapa"]);
            $('#fichero_imagen_mapa_localizacion_text').val("");

            // Se muestra u oculta la pestaña de mapa
            if (parametros_localizacion["mapa_personalizado"] == VALOR_SI) {
                $("#titulo-tab-mapa").show();
            }
            else {
                $("#titulo-tab-mapa").hide();
            }

            // Si la pestaña activa es la pestaña de mapa, se recarga el mapa
            if (mapa_modificado == true) {
                var href_pestanya_activa = $('#tabs-administracion-localizacion .active > a').attr("href");
                var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
                if (id_pestanya_activa == "mapa") {
                    localizador_mapa_visible("_defecto");
                }
            }

            // Si se ha modificado el mapa personalizado o el tipo de mapa, se establecen las coordenadas y el zoom por defecto a 0
            // (ya se han modificado en la base de datos)
            if ((mapa_personalizado_modificado == true) || (tipo_mapa_modificado == true)) {
                $("#latitud_mapa_defecto").val(0);
                $("#longitud_mapa_defecto").val(0);
                $("#zoom_mapa_defecto").val(0);
            }

            // Se muestra el botón para mostrar la imagen del mapa local
            if (parametros_localizacion["mapa_personalizado"] == VALOR_SI) {
                switch (parametros_localizacion["tipo_mapa"]) {
                    case TIPO_MAPA_LOCAL: {
                        $('#boton_mostrar_imagen_mapa_localizacion').show();
                        break;
                    }
                    case TIPO_MAPA_INTERNET: {
                        $('#boton_mostrar_imagen_mapa_localizacion').hide();
                        break;
                    }
                }
            }
            else {
                $('#boton_mostrar_imagen_mapa_localizacion').hide();
            }
        }
        else {
            $('#ventana_modal').modal('hide');
        }
    });
}


// Actualización de la tabla de localizaciones
function boton_localizaciones_actualizar_tabla_localizaciones() {
    actualiza_tabla_localizaciones(null);
}


function actualiza_tabla_localizaciones(id_localizacion_detalles) {
	$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/dame_tabla_localizaciones.php", {
		filtro: $('#filtro_localizaciones_filtro_localizaciones_tabla').val(),
        id_localizacion_detalles: id_localizacion_detalles
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaLocalizaciones').html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();

        // Para activar los eventos de los posibles controles mostrados en el detalle
        if (id_localizacion_detalles != null) {
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        }
	});
}


function boton_localizaciones_refrescar_tabla_localizacion() {
    var params = this.id.split('__');
	var id_localizacion = params[1];

    actualiza_tabla_localizacion_detalles(id_localizacion);
}


function actualiza_tabla_localizacion_detalles(id_localizacion) {
    // Se actualiza la información de la fila de la tabla
	$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/dame_informacion_fila_tabla_localizacion.php", {
        id_localizacion: id_localizacion
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_datos = "datosLocalizacion__" + id_localizacion;
        $("#fila_" + id_datos).html(resultado.fila);

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();

        // Se actualiza la información detallada (si está visible)
        var detalles_tabla_visibles = dame_elemento_visible(id_datos + " .detalle-tabla-datos");
        if (detalles_tabla_visibles == true) {
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
	});
}


