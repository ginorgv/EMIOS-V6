//
// Funciones de preferencias
//


function boton_administracion_mostrar_ventana_anyadir_modificar_preferencias(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_preferencias = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Preferencias/muestra_ventana_anyadir_modificar_preferencias.php", {
		id_preferencias: id_preferencias
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


function boton_administracion_eliminar_preferencias(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_preferencias = params[1];
	var url_preferencias = $(this).attr('url_preferencias');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar las preferencias?") + "\n(" + escapeHtml(url_preferencias) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloAdministracion/Preferencias/elimina_preferencias.php", {
				id_preferencias: id_preferencias
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				actualiza_tabla_preferencias();

                // Se actualiza el tema por defecto (si es necesario)
                actualiza_tema_defecto(resultado.msg);
			});
		}
	});
}


function boton_administracion_anyadir_modificar_preferencias() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
	var anyadir_preferencias = $("#parametros_ventana_anyadir_modificar_preferencias").attr("anyadir_preferencias");
	var id_preferencias = $("#parametros_ventana_anyadir_modificar_preferencias").attr("id_preferencias");

    // Preferencias
    var url = $('#url_preferencias').val();
    var logo_personalizado = $('#logo_personalizado_preferencias').val();
    var nombre_logo = $('#nombre_logo_preferencias').val();
    if (comprueba_longitud_cadena(nombre_logo, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_logo_preferencias').addClass('data-check-failed');
        return;
    }
    var url_logo = $('#url_logo_preferencias').val();
    var titulo_web = $('#titulo_web_preferencias').val();
    if (comprueba_longitud_cadena(titulo_web, NUMERO_MAXIMO_CARACTERES_TITULO) == false) {
        $('#titulo_web_preferencias').addClass('data-check-failed');
        return;
    }
    var tema = $('#tema_preferencias').val();
    var paleta_colores_graficas = $('#paleta_colores_graficas_preferencias').val();

    // Se añaden o modifican las preferencias
    if (anyadir_preferencias == true) {
        // Se comprueban las imágenes de los logos
        if (logo_personalizado == VALOR_SI) {
            if ($('#fichero_logo_preferencias_text').val() == "") {
                jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo"));
                return;
            }
            else {
                var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_PREFERENCIAS_LOGO, "fichero_logo_preferencias_file");
                if (imagen_correcta == false) {
                    $('#fichero_logo_preferencias_text').addClass('data-check-failed');
                    $('#fichero_logo_preferencias_text').val("");
                    return;
                }
            }
            if ($('#fichero_logo_pdf_preferencias_text').val() == "") {
                jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo PDF"));
                return;
            }
            else {
                var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF, "fichero_logo_pdf_preferencias_file");
                if (imagen_correcta == false) {
                    $('#fichero_logo_pdf_preferencias_text').addClass('data-check-failed');
                    $('#fichero_logo_pdf_preferencias_text').val("");
                    return;
                }
            }
        }

        // Se añaden las preferencias
        $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Preferencias/anyade_preferencias.php", {
            url: url,
            logo_personalizado: logo_personalizado,
            nombre_logo: nombre_logo,
            url_logo: url_logo,
            titulo_web: titulo_web,
            tema: tema,
            paleta_colores_graficas: paleta_colores_graficas
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Identificador de preferencias añadidas
            var id_preferencias = resultado.id_preferencias;

            // Se guardan las imágenes de los logos
            if (logo_personalizado == VALOR_SI) {
                var control_fichero_imagen = $('#fichero_logo_preferencias_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_PREFERENCIAS_LOGO, id_preferencias, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
                var control_fichero_imagen = $('#fichero_logo_pdf_preferencias_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF, id_preferencias, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
            }

            // Nota: El mensaje se actualiza después de actualizar las preferencias
            actualiza_tabla_preferencias();

            // Se actualiza el tema por defecto (si es necesario)
            actualiza_tema_defecto(resultado.msg);
        });
    }
    else {
        // Parametros de la ventana
        var logo_personalizado_anterior = $("#parametros_ventana_anyadir_modificar_preferencias").attr("logo_personalizado");

        // Parámetros de la modificación de las preferencias
        var parametros_preferencias = [];
        parametros_preferencias["url"] = url;
        parametros_preferencias["logo_personalizado"] = logo_personalizado;
        parametros_preferencias["nombre_logo"] = nombre_logo;
        parametros_preferencias["url_logo"] = url_logo;
        parametros_preferencias["titulo_web"] = titulo_web;
        parametros_preferencias["tema"] = tema;
        parametros_preferencias["paleta_colores_graficas"] = paleta_colores_graficas;
        // Parámetros extra
        parametros_preferencias["logo_personalizado_anterior"] = logo_personalizado_anterior;

        // Se muestra un mensaje de aviso antes de modificar las preferencias en los siguientes casos:
        // - Eliminación de logo personalizado
        var mensaje_aviso = "";
        if ((logo_personalizado_anterior == VALOR_SI) && (logo_personalizado == VALOR_NO)) {
            mensaje_aviso += "\n(" + TLNT.Idiomas._("se eliminará el logo personalizado") + ")";
        }
        if (mensaje_aviso == "") {
            modifica_preferencias(id_preferencias, parametros_preferencias);
        }
        else {
            // Se muestra un mensaje de aviso y se confirma la modificación de las preferencias
            mensaje_aviso = TLNT.Idiomas._("¿Está seguro de que desea modificar las preferencias?") + mensaje_aviso;
            jConfirmAcceptCancelAlert(mensaje_aviso, TLNT.Idiomas._("Pregunta"), function(res) {
                if (res == true) {
                    modifica_preferencias(id_preferencias, parametros_preferencias);
                }
            });
        }
    }
}


function modifica_preferencias(id_preferencias, parametros_preferencias) {
    // Se comprueban las imágenes de los logos
    if ((parametros_preferencias["logo_personalizado_anterior"] == VALOR_NO) && (parametros_preferencias["logo_personalizado"] == VALOR_SI)) {
        if ($('#fichero_logo_preferencias_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo"));
            return;
        }
        if ($('#fichero_logo_pdf_preferencias_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo PDF"));
            return;
        }
    }
    if (parametros_preferencias["logo_personalizado"] == VALOR_SI) {
        if ($('#fichero_logo_preferencias_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_PREFERENCIAS_LOGO, "fichero_logo_preferencias_file");
            if (imagen_correcta == false) {
                $('#fichero_logo_preferencias_text').addClass('data-check-failed');
                $('#fichero_logo_preferencias_text').val("");
                return;
            }
        }
        if ($('#fichero_logo_pdf_preferencias_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF, "fichero_logo_pdf_preferencias_file");
            if (imagen_correcta == false) {
                $('#fichero_logo_pdf_preferencias_text').addClass('data-check-failed');
                $('#fichero_logo_pdf_preferencias_text').val("");
                return;
            }
        }
    }

    // Se modifican las preferencias
    $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Preferencias/modifica_preferencias.php", {
        id_preferencias: id_preferencias,
        url: parametros_preferencias["url"],
        logo_personalizado: parametros_preferencias["logo_personalizado"],
        nombre_logo: parametros_preferencias["nombre_logo"],
        url_logo: parametros_preferencias["url_logo"],
        titulo_web: parametros_preferencias["titulo_web"],
        tema: parametros_preferencias["tema"],
        paleta_colores_graficas: parametros_preferencias["paleta_colores_graficas"],
        // Parámetros extra
        logo_personalizado_anterior: parametros_preferencias["logo_personalizado_anterior"]
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se guardan las imagenes de los logos
        if (parametros_preferencias["logo_personalizado"] == VALOR_SI) {
            if ($('#fichero_logo_preferencias_text').val() != "") {
                var control_fichero_imagen = $('#fichero_logo_preferencias_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_PREFERENCIAS_LOGO, id_preferencias, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
            }
            if ($('#fichero_logo_pdf_preferencias_text').val() != "") {
                var control_fichero_imagen = $('#fichero_logo_pdf_preferencias_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF, id_preferencias, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
            }
        }

        // Nota: El mensaje se actualiza después de actualizar las preferencias
        actualiza_tabla_preferencias();
        $('#ventana_modal').modal('hide');

        // Se actualiza el tema por defecto (si es necesario)
        actualiza_tema_defecto(resultado.msg);
    });
}


function actualiza_tema_defecto(mensaje) {
	$.post("./src/modulos/ModulosWeb/ModuloAdministracion/actualiza_red_actual.php", {},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se actualizan la descripción del usuario, el logo Web y el texto del pie de página
        $("#descripcion-usuario").html(resultado.html_descripcion_usuario);
        $("#logo-web").html(resultado.html_logo);
        $("#texto-pie-pagina").html(resultado.texto_pie_pagina);

        // - Si no se ha modificado el tema, se muestra el mensaje
        // - Si se ha modificado el tema, se modifican los estilos y se muestra el mensaje
        if (resultado.tema_modificado == false) {
            jInfo(mensaje);
        }
        else {
            TLNT.Navegacion.recupera_informacion_preferencias_resultado(resultado);
            TLNT.Navegacion.recarga_estilos(TIPO_MENSAJE_INFORMACION, mensaje);

            // Se actualiza el color de fondo del menú de módulos y del pie de página
            if (TLNT.Navegacion.perfil_actual == PERFIL_USUARIO_ESTANDAR) {
                var color_fondo = color_tema_oscuro;
                $("#menu-modulos").css('background-color', color_fondo);
                $("#pie-pagina").css('background-color', color_fondo);
            }
        }
	});
}


function boton_administracion_actualizar_tabla_preferencias() {
    actualiza_tabla_preferencias();
}


function actualiza_tabla_preferencias() {
	$.post("./src/modulos/ModulosWeb/ModuloAdministracion/Preferencias/dame_tabla_preferencias.php", {},
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		$('#tablaPreferencias').html(resultado.html);

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
	});
}
