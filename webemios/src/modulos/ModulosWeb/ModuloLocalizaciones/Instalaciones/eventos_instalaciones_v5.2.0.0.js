//
// Funciones de instalaciones
//


// Muestra la tabla de instalaciones aplicando el filtro
function boton_localizaciones_filtro_instalaciones_tabla() {
    boton_localizaciones_actualizar_tabla_instalaciones();
}


function boton_localizaciones_mostrar_ventana_anyadir_modificar_instalacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_instalacion = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/muestra_ventana_anyadir_modificar_instalacion.php", {
        id_instalacion: id_instalacion,
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


function boton_localizaciones_eliminar_instalacion(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_instalacion = params[1];
    var nombre_instalacion = $(this).attr('nombre_instalacion');

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la instalación?") + "\n(" + escapeHtml(nombre_instalacion) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/elimina_instalacion.php", {
                id_instalacion: id_instalacion
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
                actualiza_tabla_instalaciones();
			});
		}
	});
}


function boton_localizaciones_anyadir_modificar_instalacion() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
	var anyadir_instalacion = $("#parametros_ventana_anyadir_modificar_instalacion").attr("anyadir_instalacion");
	var id_instalacion = $("#parametros_ventana_anyadir_modificar_instalacion").attr("id_instalacion");

    // Nombre y descripción
    var nombre = $('#nombre_instalacion').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_instalacion").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_instalacion').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_instalacion").addClass('data-check-failed');
        return;
    }

    // Localización
    var id_localizacion = $('#id_localizacion_instalacion').val();
    if (id_localizacion == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._("No hay localización seleccionada"));
        return;
    }

    // Opciones de imagen (mapa)
    var imagen = $('#imagen_instalacion').val();
    var nombre_imagen = $('#nombre_imagen_instalacion').val();
    var factor_reduccion_imagen = $('#factor_reduccion_imagen_instalacion').val();
    if (parseFloat(factor_reduccion_imagen) < 1) {
        jAlert(TLNT.Idiomas._('El factor de reducción de imagen debe ser mayor o igual que 1'));
        return;
    }

    // Imagen (mapa) (posición y zoom por defecto)
    var latitud_imagen_defecto = $('#latitud_mapa_defecto').val();
    var longitud_imagen_defecto = $('#longitud_mapa_defecto').val();
    var zoom_imagen_defecto = $('#zoom_mapa_defecto').val();

    // Posición en mapa (de la localización correspondiente - o de la red si no hay mapa personalizado en la localización)
    var mostrar_en_mapa = $('#mostrar_en_mapa').val();
    var latitud_mapa = $('#latitud_mapa').val();
    var longitud_mapa = $('#longitud_mapa').val();
    var zoom_mapa = $('#zoom_mapa').val();

    // Parámetros de la ventana
    var id_localizacion_anterior = $("#parametros_ventana_anyadir_modificar_instalacion").attr("id_localizacion");
    var imagen_anterior = $("#parametros_ventana_anyadir_modificar_instalacion").attr("imagen");
    var factor_reduccion_imagen_anterior = $("#parametros_ventana_anyadir_modificar_instalacion").attr("factor_reduccion_imagen");
    var latitud_mapa_anterior = $("#parametros_ventana_anyadir_modificar_instalacion").attr("latitud_mapa");
    var longitud_mapa_anterior = $("#parametros_ventana_anyadir_modificar_instalacion").attr("longitud_mapa");

    // Se añade o modifica la instalación
    if (anyadir_instalacion == true) {
        // Flag de duplicar instalación
        var id_instalacion_anterior = id_instalacion;
        var duplicar_instalacion = (id_instalacion_anterior != ID_NINGUNO);

        // Se comprueba la imagen
        var duplicar_imagen = false;
        if (imagen == VALOR_SI) {
            if ($('#fichero_imagen_instalacion_text').val() == "") {
                if ((duplicar_instalacion == true) && (imagen_anterior != "")) {
                    // Nota: Es un duplicado y ya había imagen: no hace faltar subir un nuevo fichero de imagen,
                    // se duplicará la imagen anterior
                    duplicar_imagen = true;
                }
                else {
                    jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen"));
                    return;
                }
            }
            else
            {
                var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_INSTALACION_IMAGEN, "fichero_imagen_instalacion_file");
                if (imagen_correcta == false) {
                    $('#fichero_imagen_instalacion_text').addClass('data-check-failed');
                    $('#fichero_imagen_instalacion_text').val("");
                    return;
                }
            }
        }

        // Se añade la instalación
        $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/anyade_instalacion.php", {
            nombre: nombre,
            descripcion: descripcion,
            id_localizacion: id_localizacion,
            imagen: imagen,
            nombre_imagen: nombre_imagen,
            factor_reduccion_imagen: factor_reduccion_imagen,
            latitud_imagen_defecto: latitud_imagen_defecto,
            longitud_imagen_defecto: longitud_imagen_defecto,
            zoom_imagen_defecto: zoom_imagen_defecto,
            mostrar_en_mapa: mostrar_en_mapa,
            latitud_mapa: latitud_mapa,
            longitud_mapa: longitud_mapa,
            zoom_mapa: zoom_mapa,
            id_instalacion_anterior: id_instalacion_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Identificador de instalación añadida
            var id_instalacion = resultado.id_instalacion;

            // Se guarda la imagen
            if (imagen == VALOR_SI) {
                if (duplicar_imagen == false) {
                    var control_fichero_imagen = $('#fichero_imagen_instalacion_file')[0];
                    var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_INSTALACION_IMAGEN, id_instalacion, control_fichero_imagen);
                    if (imagen_guardada_correcta == false) {
                        return;
                    }
                }
                else {
                    var imagen_duplicada_correcta = duplica_imagen_base_datos(ORIGEN_IMAGEN_INSTALACION_IMAGEN, id_instalacion_anterior, id_instalacion);
                    if (imagen_duplicada_correcta == false) {
                        return;
                    }
                }
            }

            // Se muestra el mensaje y se actualiza la tabla de instalaciones
            jInfo(resultado.msg);
            actualiza_tabla_instalaciones();
        });
    }
    else {
        // Parámetros de la modificación de la instalación
        var parametros_instalacion = [];
        parametros_instalacion["nombre"] = nombre;
        parametros_instalacion["descripcion"] = descripcion;
        parametros_instalacion["id_localizacion"] = id_localizacion;
        parametros_instalacion["imagen"] = imagen;
        parametros_instalacion["nombre_imagen"] = nombre_imagen;
        parametros_instalacion["factor_reduccion_imagen"] = factor_reduccion_imagen;
        parametros_instalacion["latitud_imagen_defecto"] = latitud_imagen_defecto;
        parametros_instalacion["longitud_imagen_defecto"] = longitud_imagen_defecto;
        parametros_instalacion["zoom_imagen_defecto"] = zoom_imagen_defecto;
        parametros_instalacion["mostrar_en_mapa"] = mostrar_en_mapa;
        parametros_instalacion["latitud_mapa"] = latitud_mapa;
        parametros_instalacion["longitud_mapa"] = longitud_mapa;
        parametros_instalacion["zoom_mapa"] = zoom_mapa;
        // Parámetros extra
        parametros_instalacion["id_localizacion_anterior"] = id_localizacion_anterior;
        parametros_instalacion["imagen_anterior"] = imagen_anterior;
        parametros_instalacion["factor_reduccion_imagen_anterior"] = factor_reduccion_imagen_anterior;
        parametros_instalacion["latitud_mapa_anterior"] = latitud_mapa_anterior;
        parametros_instalacion["longitud_mapa_anterior"] = longitud_mapa_anterior;

        // Se muestra un mensaje de aviso antes de modificar la instalación en los siguientes casos:
        // - Eliminación de imagen
        var mensaje_aviso = "";
        if ((imagen_anterior == VALOR_SI) && (imagen == VALOR_NO)) {
            mensaje_aviso += "\n(" + TLNT.Idiomas._("se eliminará la imagen") + ")";
        }
        if (mensaje_aviso == "") {
            modifica_instalacion(id_instalacion, parametros_instalacion);
        }
        else {
            // Se muestra un mensaje de aviso y se confirma la modificación de la instalación
            mensaje_aviso = TLNT.Idiomas._("¿Está seguro de que desea modificar la instalación?") + mensaje_aviso;
            jConfirmAcceptCancelAlert(mensaje_aviso, TLNT.Idiomas._("Pregunta"), function(res) {
                if (res == true) {
                    modifica_instalacion(id_instalacion, parametros_instalacion);
                }
            });
        }
    }
}


function modifica_instalacion(id_instalacion, parametros_instalacion) {
    // Se comprueba la imagen
    if ((parametros_instalacion["imagen_anterior"] == VALOR_NO) &&
        (parametros_instalacion["imagen"] == VALOR_SI)) {
        if ($('#fichero_imagen_instalacion_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen"));
            return;
        }
    }
    if (parametros_instalacion["imagen"] == VALOR_SI) {
        if ($('#fichero_imagen_instalacion_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_INSTALACION_IMAGEN, "fichero_imagen_instalacion_file");
            if (imagen_correcta == false) {
                $('#fichero_imagen_instalacion_text').addClass('data-check-failed');
                $('#fichero_imagen_instalacion_text').val("");
                return;
            }
        }
    }

    // Se modifica la instalacion
    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/modifica_instalacion.php", {
        id_instalacion: id_instalacion,
        nombre: parametros_instalacion["nombre"],
        descripcion: parametros_instalacion["descripcion"],
        id_localizacion: parametros_instalacion["id_localizacion"],
        imagen: parametros_instalacion["imagen"],
        nombre_imagen: parametros_instalacion["nombre_imagen"],
        factor_reduccion_imagen: parametros_instalacion["factor_reduccion_imagen"],
        latitud_imagen_defecto: parametros_instalacion["latitud_imagen_defecto"],
        longitud_imagen_defecto: parametros_instalacion["longitud_imagen_defecto"],
        zoom_imagen_defecto: parametros_instalacion["zoom_imagen_defecto"],
        mostrar_en_mapa: parametros_instalacion["mostrar_en_mapa"],
        latitud_mapa: parametros_instalacion["latitud_mapa"],
        longitud_mapa: parametros_instalacion["longitud_mapa"],
        zoom_mapa: parametros_instalacion["zoom_mapa"],
        // Parámetros extra
        id_localizacion_anterior: parametros_instalacion["id_localizacion_anterior"],
        imagen_anterior: parametros_instalacion["imagen_anterior"]
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se guarda la imagen
        var imagen_imagen_modificada = false;
        var factor_reduccion_imagen_modificado = false;
        if (parametros_instalacion["imagen"] == VALOR_SI) {
            if ($('#fichero_imagen_instalacion_text').val() != "") {
                var control_fichero_imagen = $('#fichero_imagen_instalacion_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_INSTALACION_IMAGEN, id_instalacion, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
                imagen_imagen_modificada = true;
            }
            if (parametros_instalacion["factor_reduccion_imagen_anterior"] != parametros_instalacion["factor_reduccion_imagen"]) {
                factor_reduccion_imagen_modificado = true;
            }
        }

        // Se muestra el mensaje y se actualiza la tabla de instalaciones
        jInfo(resultado.msg);
        actualiza_tabla_instalacion_detalles(id_instalacion);

        // Si se ha modificado el del mapa de la localización no se cierra la ventana
        // (para poder modificar los parámetros del mapa sin tener que volver a abrir la ventana)
        var imagen_modificada = (imagen_imagen_modificada == true) ||
            (factor_reduccion_imagen_modificado == true);
        var mantener_ventana_modal_abierta = (imagen_modificada == true)  ||
            (parametros_instalacion["latitud_mapa_anterior"] != parametros_instalacion["latitud_mapa"]) ||
            (parametros_instalacion["longitud_mapa_anterior"] != parametros_instalacion["longitud_mapa"]);

        // Si se mantiene abierta la ventana modal, se establecen los parámetros actuales de la ventana
        if (mantener_ventana_modal_abierta == true) {
            $("#parametros_ventana_anyadir_modificar_instalacion").attr("id_localizacion", parametros_instalacion["id_localizacion"]);
            $("#parametros_ventana_anyadir_modificar_instalacion").attr("imagen", parametros_instalacion["imagen"]);
            $("#parametros_ventana_anyadir_modificar_instalacion").attr("factor_reduccion_imagen", parametros_instalacion["factor_reduccion_imagen"]);
            $("#parametros_ventana_anyadir_modificar_instalacion").attr("latitud_mapa", parametros_instalacion["latitud_mapa"]);
            $("#parametros_ventana_anyadir_modificar_instalacion").attr("longitud_mapa", parametros_instalacion["longitud_mapa"]);
            $('#fichero_imagen_instalacion_text').val("");

            // Se muestra u oculta la pestaña de imagen
            if (parametros_instalacion["imagen"] == VALOR_SI) {
                $("#titulo-tab-imagen").show();
            }
            else {
                $("#titulo-tab-imagen").hide();
            }

            // Si la pestaña activa es la pestaña de imagen, se recarga la imagen
            if (imagen_modificada == true) {
                var href_pestanya_activa = $('#tabs-administracion-instalacion .active > a').attr("href");
                var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
                if (id_pestanya_activa == "imagen") {
                    localizador_mapa_visible("_defecto");
                }
            }
        }
        else {
            $('#ventana_modal').modal('hide');
        }
    });
}


// Actualización de la tabla de instalaciones
function boton_localizaciones_actualizar_tabla_instalaciones() {
    actualiza_tabla_instalaciones();
}


function actualiza_tabla_instalaciones() {
	$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/dame_tabla_instalaciones.php", {
		filtro: $('#filtro_localizaciones_filtro_instalaciones_tabla').val(),
        id_localizacion: $('#id_localizacion_localizaciones_filtro_instalaciones_tabla').val(),
        incluir_localizaciones_descendientes: $('#incluir_localizaciones_descendientes_localizaciones_filtro_instalaciones_tabla').val()
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaInstalaciones').html(resultado.html);

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();

        // Se actualiza la lista de instalaciones de la pestaña 'imagen de instalación'
        actualiza_lista_instalaciones_seleccion_imagen_instalacion();
	});
}


function boton_localizaciones_refrescar_tabla_instalacion() {
	var params = this.id.split('__');
	var id_instalacion = params[1];

    actualiza_tabla_instalacion_detalles(id_instalacion);
}


function actualiza_tabla_instalacion_detalles(id_instalacion) {
    // Se actualiza la información de la fila de la tabla
	$.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/dame_informacion_fila_tabla_instalacion.php", {
        id_instalacion: id_instalacion
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_datos = "datosInstalacion__" + id_instalacion;
        $("#fila_" + id_datos).html(resultado.fila);

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();

        // Se actualiza la lista de instalaciones de la pestaña 'imagen de instalación'
        actualiza_lista_instalaciones_seleccion_imagen_instalacion();

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


function actualiza_lista_instalaciones_seleccion_imagen_instalacion() {
	$("#id_localizacion_localizaciones_seleccion_imagen_instalacion").trigger('change');
}


//
// Funciones de mapa de instalaciones
//


// Muestra el mapa de instalaciones de la localización seleccionada (al mostrarse el mapa de instalaciones)
function boton_localizaciones_muestra_mapa_instalaciones_localizacion_seleccionada() {
    // Se recupera la localización seleccionada
    var id_localizacion = $('#id_localizacion_localizaciones_seleccion_mapa_instalaciones').val();
    if (id_localizacion == ID_NINGUNO) {
        // Se muestra el texto de que no hay localización seleccionada y se oculta el mapa de instalaciones
        muestra_elemento("texto-mapa-instalaciones");
        oculta_elemento("mapa-mapa-instalaciones");
    }
    else {
        boton_localizaciones_seleccion_mapa_instalaciones();
    }
}


// Selección de localizacion del mapa de instalaciones
function boton_localizaciones_seleccion_mapa_instalaciones() {
    mostrar_mapa_instalaciones(false);
}


// Muestra el mapa de instalaciones de la localización
function mostrar_mapa_instalaciones(actualizacion_mapa) {
    // Se recupera la localización seleccionada
    var id_localizacion = $('#id_localizacion_localizaciones_seleccion_mapa_instalaciones').val();
    if (id_localizacion == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._("No hay localización seleccionada"));
        return;
    }

    // Parámetros ocultos del mapa de instalaciones
    var id_localizacion_mapa_instalaciones_actual = $("#parametros_mapa_instalaciones").attr("id_localizacion");
    var etiquetas_mapa_instalaciones = $("#parametros_mapa_instalaciones").attr("etiquetas");

    // Si ha cambiado la localización, no puede ser una actualización
    if ((actualizacion_mapa == true) && (id_localizacion != id_localizacion_mapa_instalaciones_actual)) {
        actualizacion_mapa = false;
    }

    // Se oculta el texto de que no hay localización seleccionada y se muestra el mapa de instalaciones
    oculta_elemento("texto-mapa-instalaciones");
    muestra_elemento("mapa-mapa-instalaciones");

    // Se recupera la información del mapa de instalaciones
    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/dame_info_mapa_instalaciones.php", {
        id_localizacion: id_localizacion,
        etiquetas_mapa_instalaciones: etiquetas_mapa_instalaciones
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se establece la altura del mapa
        var altura_mapa = ($(window).height() - MARGEN_ALTURA_MAPA_INSTALACIONES);
        if (altura_mapa < MIN_ALTURA_MAPA_INSTALACIONES) {
            altura_mapa = MIN_ALTURA_MAPA_INSTALACIONES;
        }
        $('#mapa-mapa-instalaciones').height(altura_mapa + "px");

        // Información de las capas de elementos
        var info_capas_elementos = [];
        info_capas_elementos.push({
            titulo: TLNT.Idiomas._("Instalaciones"),
            info_elementos: resultado.info_mapa_instalaciones,
            activada: true});
        info_capas_elementos.push({
            titulo: TLNT.Idiomas._("Equipos"),
            info_elementos: resultado.info_mapa_equipos_instalaciones,
            activada: false});

        // Parámetros del mapa
        var parametros_mapa = {};
        parametros_mapa["actualizacion_mapa"] = actualizacion_mapa;
        parametros_mapa["info_capas_elementos"] = info_capas_elementos;
        parametros_mapa["info_capas_calor"] = [];
        parametros_mapa["multiplicador_distancia_cluster"] = MULTIPLICADOR_DISTANCIA_CLUSTER_MAPA_INSTALACIONES;

        // Origen del mapa
        var origen_mapa = ORIGEN_MAPA_RED_LOCALIZACION;
        var id_origen_mapa_final = id_localizacion;

        // Se recuperan la posición y zoom del mapa
        $.post("./src/lib/modulos/mapas/dame_info_mapa.php", {
            origen_mapa: origen_mapa,
            id_origen_mapa: id_origen_mapa_final
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            var opciones_mapa = {
                tipo_mapa: resultado.tipo_mapa,
                nombre_mapa: resultado.nombre_mapa,
                ruta_fichero_imagen_mapa_local: null,
                anchura_imagen_mapa_local: null,
                altura_imagen_mapa_local: null,
                factor_reduccion_imagen_mapa_local: null
            };
            switch (resultado.tipo_mapa) {
                case TIPO_MAPA_LOCAL: {
                    // Origen de la imagen del mapa
                    var origen_imagen = resultado.origen_imagen;
                    var id_origen_imagen = resultado.id_origen_imagen;

                    // Se carga la imagen del mapa local de la red
                    var res_carga_imagen = carga_imagen_base_datos(origen_imagen, id_origen_imagen, null);
                    var imagen_cargada_correcta = res_carga_imagen.imagen_cargada_correcta;
                    if (imagen_cargada_correcta == false) {
                        return;
                    }
                    opciones_mapa.ruta_fichero_imagen_mapa_local = res_carga_imagen.ruta_fichero_imagen;
                    opciones_mapa.anchura_imagen_mapa_local = res_carga_imagen.anchura_imagen;
                    opciones_mapa.altura_imagen_mapa_local = res_carga_imagen.altura_imagen;
                    opciones_mapa.factor_reduccion_imagen_mapa_local = resultado.factor_reduccion_imagen_mapa_local;
                    break;
                }
            }

            // Se guardan la posición y el zoom del mapa por defecto en variables globales
            latitud_mapa_defecto = resultado.latitud_mapa_defecto;
            longitud_mapa_defecto = resultado.longitud_mapa_defecto;
            zoom_mapa_defecto = resultado.zoom_mapa_defecto;

            // Se crea o actualiza el mapa
            if (actualizacion_mapa == false) {
                crear_mapa("mapa-mapa-instalaciones", opciones_mapa, mostrar_mapa_personalizado, parametros_mapa);

                // Se guarda la localización actual del mapa
                $("#parametros_mapa_instalaciones").attr("id_localizacion", id_localizacion);
            }
            else {
                mostrar_mapa_personalizado(parametros_mapa);
            }
        });
    });
}


// Actualizar el mapa de instalaciones
function boton_localizaciones_actualizar_mapa_instalaciones() {
    mostrar_mapa_instalaciones(true);
}


// Centra el mapa de las instalaciones
function boton_localizaciones_centrar_mapa_instalaciones() {
    boton_centrar_mapa();
}


// Cambiar las etiquetas del mapa de instalaciones
function boton_localizaciones_etiquetas_mapa_instalaciones() {
    var etiquetas_mapa_instalaciones = parseInt($("#parametros_mapa_instalaciones").attr("etiquetas"));
    switch (etiquetas_mapa_instalaciones) {
        case VALOR_SI: {
            etiquetas_mapa_instalaciones = VALOR_NO;
            break;
        }
        case VALOR_NO: {
            etiquetas_mapa_instalaciones = VALOR_SI;
            break;
        }
    }
    $("#parametros_mapa_instalaciones").attr("etiquetas", etiquetas_mapa_instalaciones);
    boton_localizaciones_actualizar_mapa_instalaciones();
}


//
// Funciones de imágenes de instalaciones
//


// Muestra la imagen de la instalación de la instalación seleccionada (al mostrarse la imagen de la instalación)
function boton_localizaciones_muestra_imagen_instalacion_instalacion_seleccionada() {
    // Se recupera la instalación seleccionada
    var id_instalacion = $('#id_instalacion_localizaciones_seleccion_imagen_instalacion').val();
    if (id_instalacion == ID_NINGUNO) {
        // Se muestra el texto de que no hay instalación seleccionada y se oculta la imagen de la instalación
        muestra_elemento("texto-imagen-instalacion");
        oculta_elemento("mapa-imagen-instalacion");
    }
    else {
        boton_localizaciones_seleccion_imagen_instalacion();
    }
}


// Selección de instalación de la imagen de instalación
function boton_localizaciones_seleccion_imagen_instalacion() {
    mostrar_imagen_instalacion(false);
}


// Muestra la imagen de la instalación
function mostrar_imagen_instalacion(actualizacion_mapa) {
    // Se recuperan la localización y la instalación seleccionada
    var id_instalacion = $('#id_instalacion_localizaciones_seleccion_imagen_instalacion').val();
    if (id_instalacion == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._("No hay instalación seleccionada"));
        return;
    }

    // Parámetros ocultos del mapa de instalaciones
    var id_instalacion_imagen_instalacion_actual = $("#parametros_imagen_instalacion").attr("id_instalacion");
    var etiquetas_imagen_instalacion = $("#parametros_imagen_instalacion").attr("etiquetas");

    // Si ha cambiado la instalación, no puede ser una actualización
    if ((actualizacion_mapa == true) && (id_instalacion != id_instalacion_imagen_instalacion_actual)) {
        actualizacion_mapa = false;
    }

    // Se oculta el texto de que no hay instalación seleccionada y se muestra la imagen de la instalación
    oculta_elemento("texto-imagen-instalacion");
    muestra_elemento("mapa-imagen-instalacion");

    // Se recupera la información de la imagen de la instalación
    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/dame_info_imagen_instalacion.php", {
        id_instalacion: id_instalacion,
        etiquetas_imagen_instalacion: etiquetas_imagen_instalacion
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se establece la altura de la imagen (mapa)
        var altura_imagen = ($(window).height() - MARGEN_ALTURA_IMAGEN_INSTALACION);
        if (altura_imagen < MIN_ALTURA_IMAGEN_INSTALACION) {
            altura_imagen = MIN_ALTURA_IMAGEN_INSTALACION;
        }
        $('#mapa-imagen-instalacion').height(altura_imagen + "px");

        // Información de las capas de elementos
        var info_capas_elementos = [];
        info_capas_elementos.push({
            titulo: TLNT.Idiomas._("Equipos"),
            info_elementos: resultado.info_mapa_equipos_instalacion,
            activada: true});
        if (resultado.info_mapa_sensores_equipos_instalacion.length > 0) {
            info_capas_elementos.push({
                titulo: TLNT.Idiomas._("Sensores"),
                info_elementos: resultado.info_mapa_sensores_equipos_instalacion,
                activada: false});
        }
        if (resultado.info_mapa_actuadores_equipos_instalacion.length > 0) {
            info_capas_elementos.push({
                titulo: TLNT.Idiomas._("Actuadores"),
                info_elementos: resultado.info_mapa_actuadores_equipos_instalacion,
                activada: false});
        }

        // Parámetros del mapa
        var parametros_mapa = {};
        parametros_mapa["actualizacion_mapa"] = actualizacion_mapa;
        parametros_mapa["info_capas_elementos"] = info_capas_elementos;
        parametros_mapa["info_capas_calor"] = [];
        parametros_mapa["multiplicador_distancia_cluster"] = MULTIPLICADOR_DISTANCIA_CLUSTER_IMAGEN_INSTALACION;

        // Origen del mapa
        var origen_mapa = ORIGEN_MAPA_INSTALACION;
        var id_origen_mapa_final = id_instalacion;

        // Se recuperan la posición y zoom del mapa
        $.post("./src/lib/modulos/mapas/dame_info_mapa.php", {
            origen_mapa: origen_mapa,
            id_origen_mapa: id_origen_mapa_final
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            var opciones_mapa = {
                tipo_mapa: resultado.tipo_mapa,
                nombre_mapa: resultado.nombre_mapa,
                ruta_fichero_imagen_mapa_local: null,
                anchura_imagen_mapa_local: null,
                altura_imagen_mapa_local: null,
                factor_reduccion_imagen_mapa_local: null
            };
            switch (resultado.tipo_mapa) {
                case TIPO_MAPA_LOCAL: {
                    // Origen de la imagen del mapa
                    var origen_imagen = resultado.origen_imagen;
                    var id_origen_imagen = resultado.id_origen_imagen;

                    // Se carga la imagen del mapa local de la instalación
                    var res_carga_imagen = carga_imagen_base_datos(origen_imagen, id_origen_imagen, null);
                    var imagen_cargada_correcta = res_carga_imagen.imagen_cargada_correcta;
                    if (imagen_cargada_correcta == false) {
                        return;
                    }
                    opciones_mapa.ruta_fichero_imagen_mapa_local = res_carga_imagen.ruta_fichero_imagen;
                    opciones_mapa.anchura_imagen_mapa_local = res_carga_imagen.anchura_imagen;
                    opciones_mapa.altura_imagen_mapa_local = res_carga_imagen.altura_imagen;
                    opciones_mapa.factor_reduccion_imagen_mapa_local = resultado.factor_reduccion_imagen_mapa_local;
                    break;
                }
            }

            // Se guardan la posición y el zoom del mapa por defecto en variables globales
            latitud_mapa_defecto = resultado.latitud_mapa_defecto;
            longitud_mapa_defecto = resultado.longitud_mapa_defecto;
            zoom_mapa_defecto = resultado.zoom_mapa_defecto;

            // Se crea o actualiza el mapa
            if (actualizacion_mapa == false) {
                crear_mapa("mapa-imagen-instalacion", opciones_mapa, mostrar_mapa_personalizado, parametros_mapa);

                // Se guarda la instalación actual del mapa
                $("#parametros_imagen_instalacion").attr("id_instalacion", id_instalacion);
            }
            else {
                mostrar_mapa_personalizado(parametros_mapa);
            }
        });
    });
}


// Actualizar la imagen de la instalación
function boton_localizaciones_actualizar_imagen_instalacion() {
    mostrar_imagen_instalacion(true);
}


// Centra la imagen de la instalación
function boton_localizaciones_centrar_imagen_instalacion() {
    boton_centrar_mapa();
}


// Cambiar las etiquetas de la imagen de la instalación
function boton_localizaciones_etiquetas_imagen_instalacion() {
    var etiquetas_imagen_instalacion = parseInt($("#parametros_imagen_instalacion").attr("etiquetas"));
    switch (etiquetas_imagen_instalacion) {
        case VALOR_SI: {
            etiquetas_imagen_instalacion = VALOR_NO;
            break;
        }
        case VALOR_NO: {
            etiquetas_imagen_instalacion = VALOR_SI;
            break;
        }
    }
    $("#parametros_imagen_instalacion").attr("etiquetas", etiquetas_imagen_instalacion);
    boton_localizaciones_actualizar_imagen_instalacion();
}