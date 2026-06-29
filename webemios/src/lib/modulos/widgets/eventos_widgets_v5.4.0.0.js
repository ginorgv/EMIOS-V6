/*
 * Módulo Personal
 *
 */


// Se muestra la pestaña de widgets especificada
function muestra_pestanya_widgets(id_pestanya) {
    // Si estaba activada la actualización periódica y es una pestaña periódica se mantiene la actualización periódica
    if (temporizador_actualizacion_pagina != null) {
        var indice_id_pestanya_actualizacion_periodica_widgets = ids_pestanyas_actualizacion_periodica_widgets.indexOf(id_pestanya);
        if (indice_id_pestanya_actualizacion_periodica_widgets == -1) {
            desactiva_actualizacion_periodica_cuadricula_widgets(false);
        }
    }

    // Se actualiza la cuadrícula de widgets
    actualiza_cuadricula_widgets(id_pestanya, false);

    // Establecimiento de eventos
    TLNT.Navegacion.establece_eventos_tablas_datos();
}


// Muestra la ventana para añadir o modificiar una pestaña de widgets
function boton_mostrar_ventana_anyadir_modificar_pestanya_widgets(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_pestanya = params[1];
    var tipo_operacion_administracion = params[2];

    var modulo = $('#modulo').attr("name");
    $.post("./src/lib/modulos/widgets/muestra_ventana_anyadir_modificar_pestanya_widgets.php", {
        id_pestanya: id_pestanya,
        tipo_operacion_administracion: tipo_operacion_administracion,
        modulo: modulo
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


// Elimina una pestaña de widgets
function boton_eliminar_pestanya_widgets(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    // Identificador y nombre de pestaña (activa)
    var params = this.id.split('__');
    var id_pestanya = params[1];
    var nombre_pestanya = $('#tabs-pestanyas-widgets .active > a').html();

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la pestaña de widgets?") + "\n(" + escapeHtml(nombre_pestanya) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            $.post("./src/lib/modulos/widgets/elimina_pestanya_widgets.php", {
                id_pestanya: id_pestanya,
                modulo: $("#modulo").attr("name")
            },
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                // Se elimina la pestaña seleccionada
                $("#tab-pestanya-widgets__" + id_pestanya).remove();

                // Se actualiza el contenido de la sección
                TLNT.Navegacion.recarga_contenido_seccion_actual();

                // Mensaje de información
                jInfo(resultado.msg);

                // Se desactiva la actualización periódica
                desactiva_actualizacion_periodica_cuadricula_widgets(false);
            });
        }
    });
}


// Añade o modifica una pestaña de widgets
function boton_anyadir_modificar_pestanya_widgets() {
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Módulo
    var modulo = $("#modulo").attr("name");

    // Identificador y nombre de pestaña de widgets
	var id_pestanya = $("#parametros_ventana_anyadir_modificar_pestanya_widgets").attr("id_pestanya");
    var nombre = $("#nombre_pestanya_widgets").val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_pestanya_widgets").addClass('data-check-failed');
        return;
    }

    // Posición de pestaña anterior y actualización periódica rotatoria
    var posicion_pestanya_anterior =  $("#posicion_pestanya_anterior_pestanya_widgets").val();
    var actualizacion_periodica_rotatoria = $("#actualizacion_periodica_rotatoria_pestanya_widgets").val();

    // Números de columnas de filas de widgets
    var cadena_numeros_columnas_filas_widgets = elimina_espacios($("#numeros_columnas_filas_widgets_pestanya_widgets").val());
    var numeros_columnas_filas_widgets = cadena_numeros_columnas_filas_widgets.split(',');
    for (var i = 0; i < numeros_columnas_filas_widgets.length; i++) {
        var numero_columnas_fila_widgets = numeros_columnas_filas_widgets[i];
        if (PATRON_NUMERO_ENTERO.test(numero_columnas_fila_widgets) == false) {
            jAlert(TLNT.Idiomas._("Los números de columnas deben ser valores enteros"));
            return;
        }
        if ((parseInt(numero_columnas_fila_widgets) < 1) ||
            (parseInt(numero_columnas_fila_widgets) > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS)) {
            var mensaje_error = TLNT.Idiomas._('Los números de columnas son incorrectos') +
                " (" + TLNT.Idiomas._('rango de valores') + ": " +
                "1" + " - " + NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS + ")";
            jAlert(mensaje_error);
            return;
        }
    }

    // Títulos de filas de widgets y ajustar altura de widgets
    var titulos_filas_widgets = $("#titulos_filas_widgets_pestanya_widgets").val();
    if (comprueba_longitud_cadena(titulos_filas_widgets, NUMERO_MAXIMO_CARACTERES_TITULO_FILA_WIDGETS) == false) {
        $("#titulos_filas_widgets_pestanya_widgets").addClass('data-check-failed');
        return;
    }
    var ajustar_altura_widgets = $("#ajustar_altura_widgets_pestanya_widgets").val();

    // Parámetros de apariencia de pestaña
    var imagen_fondo_apariencia_pestanya = $("#imagen_fondo_apariencia_pestanya_pestanya_widgets").val();
    var nombre_imagen_fondo_apariencia_pestanya = $("#nombre_imagen_fondo_apariencia_pestanya_pestanya_widgets").val();
    var mostrar_cabecera_apariencia_pestanya = $("#mostrar_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var mostrar_hora_cabecera_apariencia_pestanya = $("#mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var color_hora_cabecera_apariencia_pestanya = $("#color_hora_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var mostrar_fecha_cabecera_apariencia_pestanya = $("#mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var color_fecha_cabecera_apariencia_pestanya = $("#color_fecha_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var mostrar_titulo_cabecera_apariencia_pestanya = $("#mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var color_titulo_cabecera_apariencia_pestanya = $("#color_titulo_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var prefijo_titulo_cabecera_apariencia_pestanya = $("#prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var color_prefijo_titulo_cabecera_apariencia_pestanya = $("#color_prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var sufijo_titulo_cabecera_apariencia_pestanya = $("#sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var color_sufijo_titulo_cabecera_apariencia_pestanya = $("#color_sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var numero_lineas_separacion_cabecera_apariencia_pestanya = $("#numero_lineas_separacion_cabecera_apariencia_pestanya_pestanya_widgets").val();
    var modificar_color_titulo_filas_widgets_apariencia_pestanya = $("#modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets").val();
    var color_titulo_filas_widgets_apariencia_pestanya = $("#color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets").val();
    var mostrar_pie_apariencia_pestanya = $("#mostrar_pie_apariencia_pestanya_pestanya_widgets").val();
    var numero_lineas_separacion_pie_apariencia_pestanya = $("#numero_lineas_separacion_pie_apariencia_pestanya_pestanya_widgets").val();

    // Comprobaciónd de longitudes de nombre de imagen, prefijo y sufijo de título de cabecera
    if (comprueba_longitud_cadena(nombre_imagen_fondo_apariencia_pestanya, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_imagen_fondo_apariencia_pestanya_pestanya_widgets").addClass('data-check-failed');
        return;
    }
    if (comprueba_longitud_cadena(prefijo_titulo_cabecera_apariencia_pestanya, NUMERO_MAXIMO_CARACTERES_PREFIJO_SUFIJO_TITULO_CABECERA_PESTANYA_WIDGETS) == false) {
        $("#prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").addClass('data-check-failed');
        return;
    }
    if (comprueba_longitud_cadena(sufijo_titulo_cabecera_apariencia_pestanya, NUMERO_MAXIMO_CARACTERES_PREFIJO_SUFIJO_TITULO_CABECERA_PESTANYA_WIDGETS) == false) {
        $("#sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").addClass('data-check-failed');
        return;
    }

    // Comprobación de números de líneas de separación
    if ((parseInt(numero_lineas_separacion_cabecera_apariencia_pestanya) < 1) ||
        (parseInt(numero_lineas_separacion_cabecera_apariencia_pestanya) > NUMERO_MAXIMO_LINEAS_SEPARACION_CABECERA_PIE_PESTANYA_WIDGETS)) {
        var mensaje_error = TLNT.Idiomas._('El número de líneas de separación de cabecera es incorrecto') +
            " (" + TLNT.Idiomas._('rango de valores') + ": " +
            "1" + " - " + NUMERO_MAXIMO_LINEAS_SEPARACION_CABECERA_PIE_PESTANYA_WIDGETS + ")";
        jAlert(mensaje_error);
        return;
    }
    if ((parseInt(numero_lineas_separacion_pie_apariencia_pestanya) < 1) ||
        (parseInt(numero_lineas_separacion_pie_apariencia_pestanya) > NUMERO_MAXIMO_LINEAS_SEPARACION_CABECERA_PIE_PESTANYA_WIDGETS)) {
        var mensaje_error = TLNT.Idiomas._('El número de líneas de separación de pie es incorrecto') +
            " (" + TLNT.Idiomas._('rango de valores') + ": " +
            "1" + " - " + NUMERO_MAXIMO_LINEAS_SEPARACION_CABECERA_PIE_PESTANYA_WIDGETS + ")";
        jAlert(mensaje_error);
        return;
    }

    // Cadena de parámetros de apariencia de pestaña
    var parametros_apariencia_pestanya = [
        imagen_fondo_apariencia_pestanya,
        nombre_imagen_fondo_apariencia_pestanya,
        mostrar_cabecera_apariencia_pestanya,
        mostrar_hora_cabecera_apariencia_pestanya,
        color_hora_cabecera_apariencia_pestanya,
        mostrar_fecha_cabecera_apariencia_pestanya,
        color_fecha_cabecera_apariencia_pestanya,
        mostrar_titulo_cabecera_apariencia_pestanya,
        color_titulo_cabecera_apariencia_pestanya,
        prefijo_titulo_cabecera_apariencia_pestanya,
        color_prefijo_titulo_cabecera_apariencia_pestanya,
        sufijo_titulo_cabecera_apariencia_pestanya,
        color_sufijo_titulo_cabecera_apariencia_pestanya,
        numero_lineas_separacion_cabecera_apariencia_pestanya,
        modificar_color_titulo_filas_widgets_apariencia_pestanya,
        color_titulo_filas_widgets_apariencia_pestanya,
        mostrar_pie_apariencia_pestanya,
        numero_lineas_separacion_pie_apariencia_pestanya].join(SEPARADOR_PARAMETROS_SIMPLES);

    // Parámetros de apariencia de widgets
    var mostrar_opciones_widgets_apariencia_widgets = $("#mostrar_opciones_widgets_apariencia_widgets_pestanya_widgets").val();
    var mostrar_fechas_widgets_apariencia_widgets = $("#mostrar_fechas_widgets_apariencia_widgets_pestanya_widgets").val();
    var mostrar_botones_widgets_apariencia_widgets = $("#mostrar_botones_widgets_apariencia_widgets_pestanya_widgets").val();
    var estilo_fuente_widgets_apariencia_widgets = $("#estilo_fuente_widgets_apariencia_widgets_pestanya_widgets").val();
    var modificar_borde_widgets_apariencia_widgets = $("#modificar_borde_widgets_apariencia_widgets_pestanya_widgets").val();
    var mostrar_borde_widgets_apariencia_widgets = $("#mostrar_borde_widgets_apariencia_widgets_pestanya_widgets").val();
    var color_borde_widgets_apariencia_widgets = $("#color_borde_widgets_apariencia_widgets_pestanya_widgets").val();
    var modificar_colores_titulo_widgets_apariencia_widgets = $("#modificar_colores_titulo_widgets_apariencia_widgets_pestanya_widgets").val();
    var color_titulo_widgets_apariencia_widgets = $("#color_titulo_widgets_apariencia_widgets_pestanya_widgets").val();
    var color_fondo_titulo_widgets_apariencia_widgets = $("#color_fondo_titulo_widgets_apariencia_widgets_pestanya_widgets").val();
    var transparencia_fondo_titulo_widgets_apariencia_widgets = $("#transparencia_fondo_titulo_widgets_apariencia_widgets_pestanya_widgets").val();
    var modificar_colores_widgets_apariencia_widgets = $("#modificar_colores_widgets_apariencia_widgets_pestanya_widgets").val();
    var color_widgets_apariencia_widgets = $("#color_widgets_apariencia_widgets_pestanya_widgets").val();
    var color_fondo_widgets_apariencia_widgets = $("#color_fondo_widgets_apariencia_widgets_pestanya_widgets").val();
    var transparencia_fondo_widgets_apariencia_widgets = $("#transparencia_fondo_widgets_apariencia_widgets_pestanya_widgets").val();
    var color_icono_widgets_apariencia_widgets = $("#color_icono_widgets_apariencia_widgets_pestanya_widgets").val();
    var transparencia_icono_widgets_apariencia_widgets = $("#transparencia_icono_widgets_apariencia_widgets_pestanya_widgets").val();
    var transparencia_fondo_graficas_widgets_apariencia_widgets = $("#transparencia_fondo_graficas_widgets_apariencia_widgets_pestanya_widgets").val();

    // Comprobación de valores de transparencias
    if ((parseFloat(transparencia_fondo_titulo_widgets_apariencia_widgets) < 0) ||
        (parseFloat(transparencia_fondo_titulo_widgets_apariencia_widgets) > 1)) {
        var mensaje_error = TLNT.Idiomas._('La transparencia de fondo de título de widgets es incorrecta') +
            " (" + TLNT.Idiomas._('rango de valores') + ": " + "0 - 1" + ")";
        jAlert(mensaje_error);
        return;
    }
    if ((parseFloat(transparencia_fondo_widgets_apariencia_widgets) < 0) ||
        (parseFloat(transparencia_fondo_widgets_apariencia_widgets) > 1)) {
        var mensaje_error = TLNT.Idiomas._('La transparencia de fondo de widgets es incorrecta') +
            " (" + TLNT.Idiomas._('rango de valores') + ": " + "0 - 1" + ")";
        jAlert(mensaje_error);
        return;
    }
    if ((parseFloat(transparencia_icono_widgets_apariencia_widgets) < 0) ||
        (parseFloat(transparencia_icono_widgets_apariencia_widgets) > 1)) {
        var mensaje_error = TLNT.Idiomas._('La transparencia de icono de widgets es incorrecta') +
            " (" + TLNT.Idiomas._('rango de valores') + ": " + "0 - 1" + ")";
        jAlert(mensaje_error);
        return;
    }
    if ((parseFloat(transparencia_fondo_graficas_widgets_apariencia_widgets) < 0) ||
        (parseFloat(transparencia_fondo_graficas_widgets_apariencia_widgets) > 1)) {
        var mensaje_error = TLNT.Idiomas._('La transparencia de fondo de gráficas de widgets es incorrecta') +
            " (" + TLNT.Idiomas._('rango de valores') + ": " + "0 - 1" + ")";
        jAlert(mensaje_error);
        return;
    }

    // Cadena de parámetros de apariencia de widgets
    var parametros_apariencia_widgets = [
        mostrar_opciones_widgets_apariencia_widgets,
        mostrar_fechas_widgets_apariencia_widgets,
        mostrar_botones_widgets_apariencia_widgets,
        estilo_fuente_widgets_apariencia_widgets,
        modificar_borde_widgets_apariencia_widgets,
        mostrar_borde_widgets_apariencia_widgets,
        color_borde_widgets_apariencia_widgets,
        modificar_colores_titulo_widgets_apariencia_widgets,
        color_titulo_widgets_apariencia_widgets,
        color_fondo_titulo_widgets_apariencia_widgets,
        transparencia_fondo_titulo_widgets_apariencia_widgets,
        modificar_colores_widgets_apariencia_widgets,
        color_widgets_apariencia_widgets,
        color_fondo_widgets_apariencia_widgets,
        transparencia_fondo_widgets_apariencia_widgets,
        color_icono_widgets_apariencia_widgets,
        transparencia_icono_widgets_apariencia_widgets,
        transparencia_fondo_graficas_widgets_apariencia_widgets].join(SEPARADOR_PARAMETROS_SIMPLES);

    // Parámetros de opciones de pantalla completa
    var modificar_opciones_pantalla_completa = $("#modificar_opciones_pantalla_completa_pestanya_widgets").val();
    var mostrar_opciones_opciones_pantalla_completa = $("#mostrar_opciones_opciones_pantalla_completa_pestanya_widgets").val();
    var estilo_fuente_titulo_opciones_pantalla_completa = $("#estilo_fuente_titulo_opciones_pantalla_completa_pestanya_widgets").val();
    var color_opciones_pantalla_completa = $("#color_opciones_pantalla_completa_pestanya_widgets").val();
    var color_fondo_opciones_pantalla_completa = $("#color_fondo_opciones_pantalla_completa_pestanya_widgets").val();
    var mostrar_pie_pagina_opciones_pantalla_completa = $("#mostrar_pie_pagina_opciones_pantalla_completa_pestanya_widgets").val();

    // Cadena de parámetros de opciones de pantalla completa
    var parametros_opciones_pantalla_completa = [
        modificar_opciones_pantalla_completa,
        mostrar_opciones_opciones_pantalla_completa,
        estilo_fuente_titulo_opciones_pantalla_completa,
        color_opciones_pantalla_completa,
        color_fondo_opciones_pantalla_completa,
        mostrar_pie_pagina_opciones_pantalla_completa].join(SEPARADOR_PARAMETROS_SIMPLES);

    // Parámetros de la ventana
    var imagen_fondo_apariencia_pestanya_anterior = $("#parametros_ventana_anyadir_modificar_pestanya_widgets").attr("imagen_fondo_apariencia_pestanya");

    // Se añade o modifica la pestaña de widgets
    var anyadir_pestanya = $("#parametros_ventana_anyadir_modificar_pestanya_widgets").attr("anyadir_pestanya");
    if (anyadir_pestanya == true) {
        // Flag de duplicar pestaña
        var id_pestanya_anterior = id_pestanya;
        var duplicar_pestanya = (id_pestanya_anterior != ID_NINGUNO);

        // Se comprueba la imagen de fondo
        var duplicar_imagen_fondo = false;
        if (imagen_fondo_apariencia_pestanya == VALOR_SI) {
            if ($('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text').val() == "") {
                if ((duplicar_pestanya == true) && (imagen_fondo_apariencia_pestanya_anterior != "")) {
                    // Nota: Es un duplicado y ya había imagen de fondo: no hace faltar subir un nuevo fichero de imagen,
                    // se duplicará la imagen anterior
                    duplicar_imagen_fondo = true;
                }
                else {
                    jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen de fondo"));
                    return;
                }
            }
            else {
                var imagen_correcta = comprueba_imagen_correcta(
                    ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO, "fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_file");
                if (imagen_correcta == false) {
                    $('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text').addClass('data-check-failed');
                    $('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text').val("");
                    return;
                }
            }
        }

        // Se recupera el usuario 'destino' de la pestaña (sólo si es un duplicado)
        var id_usuario = null;
        if (duplicar_pestanya == true) {
            var perfil_usuario_actual = $("#parametros_ventana_anyadir_modificar_pestanya_widgets").attr("perfil_usuario_actual");
            if (perfil_usuario_actual != PERFIL_USUARIO_ESTANDAR) {
                id_usuario = $("#usuario_pestanya_widgets").val();
            }
        }

        // Se añade la pestaña de widgets
        $.post("./src/lib/modulos/widgets/anyade_pestanya_widgets.php", {
            nombre: nombre,
            posicion_pestanya_anterior: posicion_pestanya_anterior,
            actualizacion_periodica_rotatoria: actualizacion_periodica_rotatoria,
            numeros_columnas_filas_widgets: cadena_numeros_columnas_filas_widgets,
            titulos_filas_widgets: titulos_filas_widgets,
            ajustar_altura_widgets: ajustar_altura_widgets,
            parametros_apariencia_pestanya: parametros_apariencia_pestanya,
            parametros_apariencia_widgets: parametros_apariencia_widgets,
            parametros_opciones_pantalla_completa: parametros_opciones_pantalla_completa,
            id_usuario: id_usuario,
            modulo: modulo,
            id_pestanya_anterior: id_pestanya_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Identificador de pestaña añadida
            var id_pestanya = resultado.id_pestanya;

            // Se guarda la imagen de fondo (o se duplica la anterior si corresponde)
            if (imagen_fondo_apariencia_pestanya == VALOR_SI) {
                if (duplicar_imagen_fondo == false) {
                    var control_fichero_imagen = $('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_file')[0];
                    var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO, id_pestanya, control_fichero_imagen);
                    if (imagen_guardada_correcta == false) {
                        return;
                    }
                }
                else {
                    var imagen_duplicada_correcta = duplica_imagen_base_datos(ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO, id_pestanya_anterior, id_pestanya);
                    if (imagen_duplicada_correcta == false) {
                        return;
                    }
                }
            }

            // Si se ha añadido la pestaña o duplicado en el usuario actual, se actualizan las pestañas de widgets
            if ((duplicar_pestanya == false) ||
                (perfil_usuario_actual == PERFIL_USUARIO_ESTANDAR) ||
                (id_usuario == ID_NINGUNO)) {
                // Se recuperan las pestañas de widgets
                var parametros_extra = {};
                if ($('#tabla-seleccion-localizacion-actual').length > 0) {
                    var seleccion_localizacion_actual_desplegada = $('#tabla-seleccion-localizacion-actual .titulo-tabla-datos').attr('desplegado');
                    parametros_extra["seleccion_localizacion_actual_desplegada"] = seleccion_localizacion_actual_desplegada;
                }
                $.post("./src/lib/modulos/widgets/dame_contenido_seccion_widgets_modulo.php", {
                    modulo: modulo,
                    id_pestanya: id_pestanya,
                    parametros_extra: parametros_extra
                },
                function(data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    // Se establece el contenido de la sección
                    $("#contenido-seccion").html(resultado.contenido);

                    // Establecimiento de eventos
                    TLNT.Navegacion.establece_eventos_secciones_modulos();
                });
            }

            // Mensaje de información
            jInfo(resultado.msg);

            // Se desactiva la actualización periódica
            desactiva_actualizacion_periodica_cuadricula_widgets(false);
        });
    }
    else {
        // Parámetros de la modificación de la pestaña de widgets
        var parametros_pestanya = [];
        parametros_pestanya["nombre"] = nombre;
        parametros_pestanya["posicion_pestanya_anterior"] = posicion_pestanya_anterior;
        parametros_pestanya["actualizacion_periodica_rotatoria"] = actualizacion_periodica_rotatoria;
        parametros_pestanya["numeros_columnas_filas_widgets"] = cadena_numeros_columnas_filas_widgets;
        parametros_pestanya["titulos_filas_widgets"] = titulos_filas_widgets;
        parametros_pestanya["ajustar_altura_widgets"] = ajustar_altura_widgets;
        parametros_pestanya["parametros_apariencia_pestanya"] = parametros_apariencia_pestanya;
        parametros_pestanya["parametros_apariencia_widgets"] = parametros_apariencia_widgets;
        parametros_pestanya["parametros_opciones_pantalla_completa"] = parametros_opciones_pantalla_completa;
        parametros_pestanya["modulo"] = modulo;
        // Parámetros extra
        parametros_pestanya["imagen_fondo_apariencia_pestanya_anterior"] = imagen_fondo_apariencia_pestanya_anterior;
        parametros_pestanya["imagen_fondo_apariencia_pestanya"] = imagen_fondo_apariencia_pestanya;

        // Se muestra un mensaje de aviso antes de modificar la pestaña de widgets en los siguientes casos:
        // - Eliminación de imagen de fondo
        var mensaje_aviso = "";
        if ((imagen_fondo_apariencia_pestanya_anterior == VALOR_SI) && (imagen_fondo_apariencia_pestanya == VALOR_NO)) {
            mensaje_aviso += "\n(" + TLNT.Idiomas._("se eliminará la imagen de fondo") + ")";
        }
        if (mensaje_aviso == "") {
            modifica_pestanya_widgets(id_pestanya, parametros_pestanya);
        }
        else {
            // Se muestra un mensaje de aviso y se confirma la modificación de la localización
            mensaje_aviso = TLNT.Idiomas._("¿Está seguro de que desea modificar la pestaña de widgets?") + mensaje_aviso;
            jConfirmAcceptCancelAlert(mensaje_aviso, TLNT.Idiomas._("Pregunta"), function(res) {
                if (res == true) {
                    modifica_pestanya_widgets(id_pestanya, parametros_pestanya);
                }
            });
        }
    }
}


function modifica_pestanya_widgets(id_pestanya, parametros_pestanya) {
    // Se comprueba la imagen de fondo
    if ((parametros_pestanya["imagen_fondo_apariencia_pestanya_anterior"] == VALOR_NO) &&
        (parametros_pestanya["imagen_fondo_apariencia_pestanya"] == VALOR_SI)) {
        if ($('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen de fondo"));
            return;
        }
    }
    if (parametros_pestanya["imagen_fondo_apariencia_pestanya"] == VALOR_SI) {
        if ($('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(
                ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO, "fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_file");
            if (imagen_correcta == false) {
                $('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text').addClass('data-check-failed');
                $('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text').val("");
                return;
            }
        }
    }

    // Se recuperan los identificadores de los widgets (en las posiciones seleccionadas)
    var ids_widgets = [];
    $("#posicion_widgets option").each(function() {
        ids_widgets.push($(this).attr("id"));
    });

    // Se modifica la pestaña de widgets
    $.post("./src/lib/modulos/widgets/modifica_pestanya_widgets.php", {
        id_pestanya: id_pestanya,
        nombre: parametros_pestanya["nombre"],
        posicion_pestanya_anterior: parametros_pestanya["posicion_pestanya_anterior"],
        actualizacion_periodica_rotatoria: parametros_pestanya["actualizacion_periodica_rotatoria"],
        numeros_columnas_filas_widgets: parametros_pestanya["numeros_columnas_filas_widgets"],
        titulos_filas_widgets: parametros_pestanya["titulos_filas_widgets"],
        ajustar_altura_widgets: parametros_pestanya["ajustar_altura_widgets"],
        parametros_apariencia_pestanya: parametros_pestanya["parametros_apariencia_pestanya"],
        parametros_apariencia_widgets: parametros_pestanya["parametros_apariencia_widgets"],
        parametros_opciones_pantalla_completa: parametros_pestanya["parametros_opciones_pantalla_completa"],
        modulo: parametros_pestanya["modulo"],
        ids_widgets: ids_widgets,
        // Parámetros extra
        imagen_fondo_apariencia_pestanya_anterior: parametros_pestanya["imagen_fondo_apariencia_pestanya_anterior"],
        imagen_fondo_apariencia_pestanya: parametros_pestanya["imagen_fondo_apariencia_pestanya"]
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se guarda la imagen de fondo
        if (parametros_pestanya["imagen_fondo_apariencia_pestanya"] == VALOR_SI) {
            if ($('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text').val() != "") {
                var control_fichero_imagen = $('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO, id_pestanya, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
            }
        }

        // Se recuperan las pestañas de widgets
        var parametros_extra = {};
        if ($('#tabla-seleccion-localizacion-actual').length > 0) {
            var seleccion_localizacion_actual_desplegada = $('#tabla-seleccion-localizacion-actual .titulo-tabla-datos').attr('desplegado');
            parametros_extra["seleccion_localizacion_actual_desplegada"] = seleccion_localizacion_actual_desplegada;
        }
        $.post("./src/lib/modulos/widgets/dame_contenido_seccion_widgets_modulo.php", {
            modulo: parametros_pestanya["modulo"],
            id_pestanya: id_pestanya,
            parametros_extra: parametros_extra
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Se establece el contenido de la sección
            $("#contenido-seccion").html(resultado.contenido);

            // Establecimiento de eventos
            TLNT.Navegacion.establece_eventos_secciones_modulos();
        });

        // Mensaje de información
        // (no se cierra la ventana de administración para poder modificar varias veces sin tener que volver a abrir la ventana)
        jInfo(resultado.msg);

        // Se desactiva la actualización periódica
        desactiva_actualizacion_periodica_cuadricula_widgets(false);
    });
}


// Actualiza la cuadrícula de widgets
function boton_actualizar_cuadricula_widgets(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_pestanya = params[1];

    actualiza_cuadricula_widgets(id_pestanya, false);
}


// Actualización periódica de la cuadrícula de widgets
function boton_actualizacion_periodica_cuadricula_widgets() {
    var params = this.id.split('__');
    var id_pestanya = params[1];

    inicia_actualizacion_periodica_cuadricula_widgets(id_pestanya);
}


// Inicia la actualización periódica de la cuadrícula de widgets
function inicia_actualizacion_periodica_cuadricula_widgets(id_pestanya) {
    console.log("Se inicia el método inicia_actualizacion_periodica");
    // Se activa o desactiva la actualización periódica de widgets
    if (temporizador_actualizacion_pagina == null) {
        jPrompt(TLNT.Idiomas._("Intervalo de actualización periódica de widgets") + " (" + TLNT.Idiomas._("segundos") + ")",
            SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_WIDGETS_DEFECTO,
            TLNT.Idiomas._("Pregunta"),
            function(valor) {
                if (valor != null) {
                    if ((isNaN(valor) == true) || (
                        (valor < MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_WIDGETS_DEFECTO) ||
                        (valor > MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_WIDGETS_DEFECTO))) {
                        var mensaje_aviso = TLNT.Idiomas._("Intervalo de actualización periódica de widgets no válido") +
                            " (" + MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_WIDGETS_DEFECTO + " - " + MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_WIDGETS_DEFECTO + ")";
                        jAlert(mensaje_aviso, TLNT.Idiomas._("Aviso"), function(res) {
                            inicia_actualizacion_periodica_cuadricula_widgets(id_pestanya);
                        });
                    }
                    else {
                        activa_actualizacion_periodica_cuadricula_widgets(id_pestanya, valor, true);
                    }
                }
            }
        );
    }
    else {
        desactiva_actualizacion_periodica_cuadricula_widgets(true);
    }
}

// Expiración del timeout para actualización periódica de la cuadrícula de la pestaña de widgets actual
function expiracion_timeout_actualizacion_periodica_widgets(id_pestanya) {
    console.log("Inicio del método expiración timeout actualización periódica"); 
    // Si se ha superado el número máximo de actualizaciones periódicas
    // - Se guarda la acción inicial en la sesión
    // - Se recarga la pestaña
    // (Nota: Después de recargar la pestaña, se recupera la acción inicial y se establece ...
    //  es necesario porque hay 'memory leaks' en los navegadores y la memoria ocupada por la pestaña sube sin límite)
    console.log("(expiracion_timeout_actualizacion_periodica_widgets) " +
        "Número de actualización periódica de widgets: '" + numero_actualizacion_periodica_widgets + "'");
    numero_actualizacion_periodica_widgets += 1;
    if (numero_actualizacion_periodica_widgets > NUMERO_MAXIMO_ACTUALIZACIONES_PERIODICAS_WIDGETS_RECARGA_PAGINA) {
        var accion_inicial = ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS;
        var parametros_accion_inicial = [
            pantalla_completa_activada,
            id_pestanya,
            segundos_intervalo_actualizacion_widgets
        ];

        $.ajax({
            type: "POST",
            url: "./comun/src/lib/herramientas/guarda_accion_inicial_sesion.php",
            data: {
                accion_inicial: accion_inicial,
                parametros_accion_inicial: parametros_accion_inicial
            },
            success: function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);

                if (resultado == null) {                   
                    return;
                }
                clearTimeout(temporizador_actualizacion_pagina_primero)
                temporizador_actualizacion_pagina = setTimeout(function () {
                    console.log("El temporizador después de la recarga es: " + segundos_intervalo_actualizacion_widgets);
                    window.location.href = window.location.href;
                    expiracion_timeout_actualizacion_periodica_widgets(id_pestanya);
                }, segundos_intervalo_actualizacion_widgets * 1000);
                numero_actualizacion_periodica_widgets = 0;
                // Almacena el temporizador después de recargar la página
                localStorage.setItem('temporizador_actualizacion_pagina', temporizador_actualizacion_pagina);

            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud AJAX: " + status + ", " + error);
            }
        });

    } else {
        // Se busca la siguiente pestaña de actualización periódica:
        // - Si es la misma se actualiza (sólo hay una pestaña)
        // - Si no se simula un 'click'
        if (ids_pestanyas_actualizacion_periodica_widgets.length == 1) {
            actualiza_cuadricula_widgets(id_pestanya, false);
        } 
        else {
            var indice_id_siguiente_pestanya_actualizacion_periodica = ids_pestanyas_actualizacion_periodica_widgets.indexOf(id_pestanya);
            indice_id_siguiente_pestanya_actualizacion_periodica += 1;
            if (indice_id_siguiente_pestanya_actualizacion_periodica == ids_pestanyas_actualizacion_periodica_widgets.length) {
                indice_id_siguiente_pestanya_actualizacion_periodica = 0;
            }
            id_pestanya = ids_pestanyas_actualizacion_periodica_widgets[indice_id_siguiente_pestanya_actualizacion_periodica];
            $("a[href='#tab-pestanya-widgets__" + id_pestanya + "']").click();
        }

        // Si existe el temporizador, porque se ha iniciado justo después de la recarga de la página
        // se elimina en este punto dado que se va a crear uno nuevo 
        var storedTemporizador = localStorage.getItem('temporizador_actualizacion_pagina');        
        if (storedTemporizador != null){
            console.log('Se limpia el temporizador iniciado después de la recarga de la página');
            clearTimeout(temporizador_actualizacion_pagina);
        }
        
        temporizador_actualizacion_pagina_primero = setTimeout(function () {
            console.log("El temporizador es: " + segundos_intervalo_actualizacion_widgets);
            expiracion_timeout_actualizacion_periodica_widgets(id_pestanya);
        }, segundos_intervalo_actualizacion_widgets * 1000);              
    }
}


function activa_actualizacion_periodica_cuadricula_widgets(id_pestanya, segundos_intervalo_actualizacion, mostrar_mensaje_informacion) {
    if (temporizador_actualizacion_pagina == null) {
        // Módulo
        var modulo = $("#modulo").attr("name");

        // Devuelve los identificadores de pestañas correspondientes a la actualización periódica
        $.post("./src/lib/modulos/widgets/dame_info_pestanyas_widgets_modulo_actualizacion_periodica.php", {
            modulo: modulo,
            id_pestanya: id_pestanya
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            var info_pestanyas = resultado.info_pestanyas;
            var ids_pestanyas = info_pestanyas.ids_pestanyas;
            var nombres_pestanyas = info_pestanyas.nombres_pestanyas;

            // Si no hay pestañas de widgets no se hace nada
            if (ids_pestanyas.length == 0) {
                return;
            }

            // Pestañas de actualización periódica
            for (var i = 0; i < ids_pestanyas.length; i++) {
                var id_pestanya_actualizacion_periodica = ids_pestanyas[i];
                $('#boton_actualizacion_periodica_cuadricula_widgets__' + id_pestanya_actualizacion_periodica).removeClass("icon-play");
                $('#boton_actualizacion_periodica_cuadricula_widgets__' + id_pestanya_actualizacion_periodica).addClass("icon-pause");
            }
            ids_pestanyas_actualizacion_periodica_widgets = ids_pestanyas;

            // Se muestra el mensaje de información
            if (mostrar_mensaje_informacion == true) {
                var mensaje_actualizacion_periodica_activada = "";
                if (nombres_pestanyas.length > 1) {
                    mensaje_actualizacion_periodica_activada = TLNT.Idiomas._("Actualización periódica rotatoria de widgets activada") + ":";
                    for (var i = 0; i < nombres_pestanyas.length; i++) {
                        mensaje_actualizacion_periodica_activada += "\n- " + nombres_pestanyas[i];
                    }
                }
                else {
                    mensaje_actualizacion_periodica_activada = TLNT.Idiomas._("Actualización periódica de widgets activada");
                }
                jInfo(mensaje_actualizacion_periodica_activada);
            }

            // Se actualiza la cuadrícula de widgets
            // - Si no hay pestaña (es acción inicial), se recupera la primera pestaña de la información de pestañas
            // - Si la pestanya activa es la que hay que actualizar, se actualiza
            // - Si no lo es, si "simula" un click en la pestaña que hay que actualizar
            if (id_pestanya == ID_NINGUNO) {
                id_pestanya = ids_pestanyas[0];
            }
            var href_pestanya = $('#tabs-pestanyas-widgets .active > a').attr("href");
            if (href_pestanya !== undefined) {
                var id_pestanya_activa = href_pestanya.split("__")[1];
                if (id_pestanya_activa == id_pestanya) {
                    actualiza_cuadricula_widgets(id_pestanya, false);
                }
                else {
                    $("a[href='#tab-pestanya-widgets__" + id_pestanya + "']").click();
                }
            }

            // Se inicia el temporizador para la actualización periódica de widgets
            segundos_intervalo_actualizacion_widgets = segundos_intervalo_actualizacion;
            temporizador_actualizacion_pagina = setTimeout(
                expiracion_timeout_actualizacion_periodica_widgets,
                segundos_intervalo_actualizacion_widgets * 1000,
                id_pestanya);
            console.log("Se inicia el temporizador para la actualización periódica de widgets");
            // Se inicia el contador de número de actualización periódica de widgets
            numero_actualizacion_periodica_widgets = 1;
        });
    }
}


// Desactiva la actualización periódica de la cuadrícula de widgets
function desactiva_actualizacion_periodica_cuadricula_widgets(muestra_mensaje_informacion) {
    console.log("Entra en el método desactiva_actualizacion_periodica");
    if (temporizador_actualizacion_pagina != null) {
        clearTimeout(temporizador_actualizacion_pagina);
        temporizador_actualizacion_pagina = null;

        // Se actualiza el icono en todas las pestanyas con actualización periódica
        for (var i = 0; i < ids_pestanyas_actualizacion_periodica_widgets.length; i++) {
            var id_pestanya_actualizacion_periodica_widgets = ids_pestanyas_actualizacion_periodica_widgets[i];
            $('#boton_actualizacion_periodica_cuadricula_widgets__' + id_pestanya_actualizacion_periodica_widgets).removeClass("icon-pause");
            $('#boton_actualizacion_periodica_cuadricula_widgets__' + id_pestanya_actualizacion_periodica_widgets).addClass("icon-play");
        }

        // Mensaje de información
        if (muestra_mensaje_informacion == true) {
            if (ids_pestanyas_actualizacion_periodica_widgets.length > 1) {
                jInfo(TLNT.Idiomas._("Actualización periódica rotatoria de widgets desactivada"));
            }
            else {
                jInfo(TLNT.Idiomas._("Actualización periódica de widgets desactivada"));
            }
        }

        // Identificadores de pestañas de actualización periódica
        ids_pestanyas_actualizacion_periodica_widgets = [];
    }
}


// Muestra la ventana para añadir o modificar un widget
function boton_mostrar_ventana_anyadir_modificar_widget(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_widget = params[1];
    var tipo_operacion_administracion = params[2];

    // Identificador de pestaña actual
    var href_pestanya = $('#tabs-pestanyas-widgets .active > a').attr("href");
    var id_pestanya = href_pestanya.split("__")[1];

    $.post("./src/lib/modulos/widgets/muestra_ventana_anyadir_modificar_widget.php", {
		id_widget: id_widget,
        id_pestanya: id_pestanya,
        tipo_operacion_administracion: tipo_operacion_administracion,
        modulo: $("#modulo").attr("name")
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


// Elimina un widget de la cuadrícula de widgets
function boton_eliminar_widget(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_pestanya = params[1];
    var id_widget = params[2];
    var tipo = params[3];
    var nombre_widget = $(this).attr('nombre_widget');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el widget?") + "\n(" + escapeHtml(nombre_widget) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/lib/modulos/widgets/elimina_widget.php", {
                id_widget: id_widget,
                tipo: tipo
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				actualiza_cuadricula_widgets(id_pestanya, true);
                jInfo(resultado.msg);
			});
		}
	});
}


// Añade o modifica un widget
function boton_anyadir_modificar_widget() {
    // Número de columnas
    var cadena_numero_columnas = $('#numero_columnas_widget').val();
    var parametros_numero_columnas = cadena_numero_columnas.split(SEPARADOR_PARAMETROS_SIMPLES);
    switch (parametros_numero_columnas.length) {
        case 1: {
            var numero_columnas_contenido_widget = parseInt(parametros_numero_columnas[0]);
            if ((numero_columnas_contenido_widget < 1) ||
                (numero_columnas_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS)) {
                jAlert(TLNT.Idiomas._('El número de columnas es incorrecto') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    1 + " - " + NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS + ")");
                return;
            }
            break;
        }
        case 3: {
            var numero_columnas_vacias_izquierda_widget = parseInt(parametros_numero_columnas[0]);
            var numero_columnas_contenido_widget = parseInt(parametros_numero_columnas[1]);
            var numero_columnas_vacias_derecha_widget = parseInt(parametros_numero_columnas[2]);
            if ((numero_columnas_contenido_widget < 1) ||
                (numero_columnas_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS)) {
                jAlert(TLNT.Idiomas._('El número de columnas del contenido del widget es incorrecto') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    1 + " - " + NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS + ")");
                return;
            }
            var numero_columnas_totales_widget =
                numero_columnas_vacias_izquierda_widget +
                numero_columnas_contenido_widget +
                numero_columnas_vacias_derecha_widget;
            if (numero_columnas_totales_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS) {
                jAlert(TLNT.Idiomas._('La suma de los números de columnas es incorrecto') +
                    " (" + TLNT.Idiomas._('valor máximo') + ": " + NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS + ")");
                return;
            }
            break;
        }
        default: {
            jAlert(TLNT.Idiomas._('El número de parámetros de número de columnas es incorrecto'));
            return;
        }
    }

    // Tipo de widget
    var tipo = $('#tipo_widget').val();
    if (tipo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo de widget seleccionado'));
        return;
    }

    // Comprobación de datos correctos de las pestañas visibles
    var ids_pestanyas_visibles = ["tab-principal"];
    switch (tipo) {
        // Widgets generales (sin módulo asociado)
        case TIPO_WIDGET_IMAGEN: {
            ids_pestanyas_visibles.push("tab-tipo-imagen");
            break;
        }
        // Widgets del módulo Localizaciones
        case TIPO_WIDGET_VALOR_RATIO: {
            ids_pestanyas_visibles.push("tab-tipo-valor-ratio");
            break;
        }
        // Widgets del módulo Sensores
        case TIPO_WIDGET_VALOR_DIGITAL_SENSOR: {
            ids_pestanyas_visibles.push("tab-tipo-valor-digital-sensor");
            break;
        }
        case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR: {
            ids_pestanyas_visibles.push("tab-tipo-valor-digital-medio-acumulado-sensor");
            break;
        }
        case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR: {
            ids_pestanyas_visibles.push("tab-tipo-valor-analogico-sensor");
            break;
        }
        case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR: {
            ids_pestanyas_visibles.push("tab-tipo-valor-analogico-medio-acumulado-sensor");
            break;
        }
        case TIPO_WIDGET_GRAFICA_VALORES_SENSOR: {
            ids_pestanyas_visibles.push("tab-tipo-grafica-valores-sensor");
            break;
        }
        case TIPO_WIDGET_MAPA_CALOR_SENSOR: {
            ids_pestanyas_visibles.push("tab-tipo-mapa-calor-sensor");
            break;
        }
        case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR: {
            ids_pestanyas_visibles.push("tab-tipo-grafica-comparacion-periodos-sensor");
            break;
        }
        case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR: {
            ids_pestanyas_visibles.push("tab-tipo-evolucion-valores-comparacion-periodos-sensor");
            break;
        }
        case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-comparacion-campos-iguales-sensores-principal");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-comparacion-campos-iguales-sensores-sensores");
            break;
        }
        case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-principal");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-1");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-2");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-3");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-4");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-5");
            break;
        }
        case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-valores-generales-sensores-principal");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-valores-generales-sensores-campo-1");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-valores-generales-sensores-campo-2");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-valores-generales-sensores-campo-3");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-valores-generales-sensores-sensores");
            break;
        }
        case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-valor-agregado-valores-generales-sensores-principal");
            ids_pestanyas_visibles.push("titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-1");
            ids_pestanyas_visibles.push("titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-2");
            ids_pestanyas_visibles.push("titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-3");
            ids_pestanyas_visibles.push("titulo-tab-tipo-valor-agregado-valores-generales-sensores-sensores");
            break;
        }
        case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-incrementos-totales-sensores-principal");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-1");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-2");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-3");
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-incrementos-totales-sensores-sensores");
            break;
        }
        // Widgets del módulo Actuadores
        case TIPO_WIDGET_INFORMACION_ACTUADOR: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-informacion-actuador");
            break;
        }
        case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-informacion-grupo-actuadores");
            break;
        }
        // Widgets del módulo Smartmeter
        case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-grafica-consumos-costes-tramos-sensor");
            break;
        }
        case TIPO_WIDGET_COSTE_FACTURA_SENSOR: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-coste-factura-electrica-sensor");
            break;
        }
        // Widgets del módulo Proyectos
        case TIPO_WIDGET_SIMULADOR_LINEA_BASE: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-simulador-linea-base");
            break;
        }
        case TIPO_WIDGET_INFORMACION_PROYECTO: {
            ids_pestanyas_visibles.push("titulo-tab-tipo-informacion-proyecto");
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

    // Comprobación de parámetros correctos
    var parametros_tipo = "";
    switch (tipo) {
        case TIPO_WIDGET_IMAGEN: {
            // Altura máxima
            var altura_maxima = $('#altura_maxima_widget_imagen').val();
            if ((parseInt(altura_maxima) < ALTURA_MAXIMA_MINIMA_WIDGET_IMAGEN) ||
                (parseInt(altura_maxima) > ALTURA_MAXIMA_MAXIMA_WIDGET_IMAGEN)) {
                var descripcion_error = TLNT.Idiomas._('La altura máxima es incorrecta') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    ALTURA_MAXIMA_MINIMA_WIDGET_IMAGEN + " - " + ALTURA_MAXIMA_MAXIMA_WIDGET_IMAGEN + ")";
                jAlert(descripcion_error);
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                altura_maxima].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_VALOR_RATIO: {
            // Ratio y localización
            var id_ratio = $('#id_ratio_widget_valor_ratio').val();
            if (id_ratio == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay ratio seleccionado"));
                return;
            }
            var id_localizacion = $('#id_localizacion_widget_valor_ratio').val();

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_valor_ratio').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_valor_ratio').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_valor_ratio').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Icono
            var icono = $('#icono_widget_valor_ratio').val();

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                id_localizacion,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                icono,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Widgets del módulo Sensores
        case TIPO_WIDGET_VALOR_DIGITAL_SENSOR: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_valor_digital_sensor').val();

            // Clase de sensor, identificador de sensor y granularidad
            var clase_sensor = $('#clase_sensor_widget_valor_digital_sensor').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var id_sensor = $('#id_sensor_widget_valor_digital_sensor').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }
            var granularidad_sensor = $('#granularidad_sensor_widget_valor_digital_sensor').val();

            // Campo y parámetros extra de sensor
            var campo = $('#campo_widget_valor_digital_sensor').val();
            var parametros_extra_campo = $('#parametros_extra_campo_widget_valor_digital_sensor').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Colores de fondo y límites de colores de fondo
            var parametros_controles_fondo = dame_parametros_colores_fondo_widget("widget_valor_digital_sensor", null, null);
            if (parametros_controles_fondo == null) {
                return;
            }
            var utilizar_colores_fondo = parametros_controles_fondo["utilizar_colores_fondo"];
            var colores_fondo = parametros_controles_fondo["colores_fondo"];
            var valor_limite_colores_fondo_1 = parametros_controles_fondo["valor_limite_colores_fondo_1"];
            var valor_limite_colores_fondo_2 = parametros_controles_fondo["valor_limite_colores_fondo_2"];

            // Icono
            var icono = $('#icono_widget_valor_digital_sensor').val();

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                granularidad_sensor,
                campo_parametros_extra,
                id_sensor,
                utilizar_colores_fondo,
                colores_fondo,
                valor_limite_colores_fondo_1,
                valor_limite_colores_fondo_2,
                icono].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_valor_digital_medio_acumulado_sensor').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_widget_valor_digital_medio_acumulado_sensor').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var id_sensor = $('#id_sensor_widget_valor_digital_medio_acumulado_sensor').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_valor_digital_medio_acumulado_sensor').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_valor_digital_medio_acumulado_sensor').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_valor_digital_medio_acumulado_sensor').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Campo y parámetros extra de sensor
            var campo = $('#campo_widget_valor_digital_medio_acumulado_sensor').val();
            var parametros_extra_campo = $('#parametros_extra_campo_widget_valor_digital_medio_acumulado_sensor').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Colores de fondo y límites de colores de fondo
            var parametros_controles_fondo = dame_parametros_colores_fondo_widget("widget_valor_digital_medio_acumulado_sensor", null, null);
            if (parametros_controles_fondo == null) {
                return;
            }
            var utilizar_colores_fondo = parametros_controles_fondo["utilizar_colores_fondo"];
            var colores_fondo = parametros_controles_fondo["colores_fondo"];
            var valor_limite_colores_fondo_1 = parametros_controles_fondo["valor_limite_colores_fondo_1"];
            var valor_limite_colores_fondo_2 = parametros_controles_fondo["valor_limite_colores_fondo_2"];

            // Icono
            var icono = $('#icono_widget_valor_digital_medio_acumulado_sensor').val();

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                id_sensor,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                utilizar_colores_fondo,
                colores_fondo,
                valor_limite_colores_fondo_1,
                valor_limite_colores_fondo_2,
                icono,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR: {
            // Tipo de gráfico
            var tipo_grafico = $('#tipo_grafico_widget_valor_analogico_sensor').val();

            // Ratio
            var id_ratio = $('#id_ratio_widget_valor_analogico_sensor').val();

            // Clase de sensor, identificador de sensor, granularidad y valor digital
            var clase_sensor = $('#clase_sensor_widget_valor_analogico_sensor').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var id_sensor = $('#id_sensor_widget_valor_analogico_sensor').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }
            var granularidad_sensor = $('#granularidad_sensor_widget_valor_analogico_sensor').val();
            var valor_digital = $('#valor_digital_widget_valor_analogico_sensor').val();

            // Campo y parámetros extra de sensor
            var campo = $('#campo_widget_valor_analogico_sensor').val();
            var parametros_extra_campo = $('#parametros_extra_campo_widget_valor_analogico_sensor').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Se recuperan el valor mínimo y máximo del indicador
            var valor_minimo_indicador = parseInt($('#valor_minimo_indicador_widget_valor_analogico_sensor').val());
            var valor_maximo_indicador = parseInt($('#valor_maximo_indicador_widget_valor_analogico_sensor').val());
            if (valor_minimo_indicador >= valor_maximo_indicador) {
                jAlert(TLNT.Idiomas._('Por favor, compruebe que el valor máximo del indicador sea mayor que el valor mínimo'));
                return;
            }

            // Colores de fondo y límites de colores de fondo
            var parametros_controles_fondo = dame_parametros_colores_fondo_widget(
                "widget_valor_analogico_sensor",
                valor_minimo_indicador,
                valor_maximo_indicador);
            if (parametros_controles_fondo == null) {
                return;
            }
            var utilizar_colores_fondo = parametros_controles_fondo["utilizar_colores_fondo"];
            var colores_fondo = parametros_controles_fondo["colores_fondo"];
            var valor_limite_colores_fondo_1 = parametros_controles_fondo["valor_limite_colores_fondo_1"];
            var valor_limite_colores_fondo_2 = parametros_controles_fondo["valor_limite_colores_fondo_2"];

            // Si el tipo de gráfico es circular, debe haber colores de fondo
            if ((tipo_grafico == TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_CIRCULAR) && (utilizar_colores_fondo == false)) {
                jAlert(TLNT.Idiomas._('Deben definirse colores de fondo con el tipo de gráfico circular'));
                return;
            }

            // Icono
            var icono = $('#icono_widget_valor_analogico_sensor').val();

            // Parámetros de tipo
            parametros_tipo = [
                tipo_grafico,
                id_ratio,
                clase_sensor,
                granularidad_sensor,
                campo_parametros_extra,
                id_sensor,
                valor_minimo_indicador,
                valor_maximo_indicador,
                utilizar_colores_fondo,
                colores_fondo,
                valor_limite_colores_fondo_1,
                valor_limite_colores_fondo_2,
                valor_digital,
                icono].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR: {
            // Tipo de gráfico
            var tipo_grafico = $('#tipo_grafico_widget_valor_analogico_medio_acumulado_sensor').val();

            // Ratio
            var id_ratio = $('#id_ratio_widget_valor_analogico_medio_acumulado_sensor').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_widget_valor_analogico_medio_acumulado_sensor').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var id_sensor = $('#id_sensor_widget_valor_analogico_medio_acumulado_sensor').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Valor digital
            var valor_digital = $('#valor_digital_widget_valor_analogico_medio_acumulado_sensor').val();

            // Campo y parámetros extra de sensor
            var campo = $('#campo_widget_valor_analogico_medio_acumulado_sensor').val();
            var parametros_extra_campo = $('#parametros_extra_campo_widget_valor_analogico_medio_acumulado_sensor').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Se recuperan el valor mínimo y máximo del indicador
            var valor_minimo_indicador = parseInt($('#valor_minimo_indicador_widget_valor_analogico_medio_acumulado_sensor').val());
            var valor_maximo_indicador = parseInt($('#valor_maximo_indicador_widget_valor_analogico_medio_acumulado_sensor').val());
            if (valor_minimo_indicador >= valor_maximo_indicador) {
                jAlert(TLNT.Idiomas._('Por favor, compruebe que el valor máximo de la aguja sea mayor que el valor mínimo'));
                return;
            }

            // Colores de fondo y límites de colores de fondo
            var parametros_controles_fondo = dame_parametros_colores_fondo_widget(
                "widget_valor_analogico_medio_acumulado_sensor",
                valor_minimo_indicador,
                valor_maximo_indicador);
            if (parametros_controles_fondo == null) {
                return;
            }
            var utilizar_colores_fondo = parametros_controles_fondo["utilizar_colores_fondo"];
            var colores_fondo = parametros_controles_fondo["colores_fondo"];
            var valor_limite_colores_fondo_1 = parametros_controles_fondo["valor_limite_colores_fondo_1"];
            var valor_limite_colores_fondo_2 = parametros_controles_fondo["valor_limite_colores_fondo_2"];

            // Si el tipo de gráfico es circular, debe haber colores de fondo
            if ((tipo_grafico == TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_CIRCULAR) && (utilizar_colores_fondo == false)) {
                jAlert(TLNT.Idiomas._('Deben definirse colores de fondo con el tipo de gráfico circular'));
                return;
            }

            // Icono
            var icono = $('#icono_widget_valor_analogico_medio_acumulado_sensor').val();

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                tipo_grafico,
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                id_sensor,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                valor_minimo_indicador,
                valor_maximo_indicador,
                utilizar_colores_fondo,
                colores_fondo,
                valor_limite_colores_fondo_1,
                valor_limite_colores_fondo_2,
                valor_digital,
                icono,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_GRAFICA_VALORES_SENSOR: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_grafica_valores_sensor').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_widget_grafica_valores_sensor').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var id_sensor = $('#id_sensor_widget_grafica_valores_sensor').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_grafica_valores_sensor').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_grafica_valores_sensor').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_grafica_valores_sensor').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Intervalo de valores
            var intervalo_valores = $('#intervalo_valores_widget_grafica_valores_sensor').val();

            // Campo y parámetros extra de sensor
            var campo = $('#campo_widget_grafica_valores_sensor').val();
            var parametros_extra_campo = $('#parametros_extra_campo_widget_grafica_valores_sensor').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                id_sensor,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                intervalo_valores,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_MAPA_CALOR_SENSOR: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_mapa_calor_sensor').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_widget_mapa_calor_sensor').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var id_sensor = $('#id_sensor_widget_mapa_calor_sensor').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_mapa_calor_sensor').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_mapa_calor_sensor').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_mapa_calor_sensor').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Campo y parámetros extra de sensor
            var campo = $('#campo_widget_mapa_calor_sensor').val();
            var parametros_extra_campo = $('#parametros_extra_campo_widget_mapa_calor_sensor').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Colores y tipo de mapa de calor
            var colores_mapa_calor = $('#colores_mapa_calor_widget_mapa_calor_sensor').val();
            var tipo_mapa_calor = $('#tipo_mapa_calor_widget_mapa_calor_sensor').val();
            if (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay tipo de mapa de calor seleccionado"));
                return;
            }

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                id_sensor,
                colores_mapa_calor,
                tipo_mapa_calor,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_grafica_comparacion_periodos_sensor').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_widget_grafica_comparacion_periodos_sensor').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var id_sensor = $('#id_sensor_widget_grafica_comparacion_periodos_sensor').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Periodo de tiempo e intervalo de valores
            var periodo_tiempo = $('#periodo_tiempo_widget_grafica_comparacion_periodos_sensor').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_grafica_comparacion_periodos_sensor').val();
            var intervalo_valores = $('#intervalo_valores_widget_grafica_comparacion_periodos_sensor').val();

            // Campo y parámetros extra de sensor
            var campo = $('#campo_widget_grafica_comparacion_periodos_sensor').val();
            var parametros_extra_campo = $('#parametros_extra_campo_widget_grafica_comparacion_periodos_sensor').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Horario semanal y exclusión de fechas
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                id_sensor,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                intervalo_valores,
                cadena_horario_semanal,
                cadena_exclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_evolucion_valores_comparacion_periodos_sensor').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_widget_evolucion_valores_comparacion_periodos_sensor').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var id_sensor = $('#id_sensor_widget_evolucion_valores_comparacion_periodos_sensor').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Periodo de tiempo e intervalo de valores
            var periodo_tiempo = $('#periodo_tiempo_widget_evolucion_valores_comparacion_periodos_sensor').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_evolucion_valores_comparacion_periodos_sensor').val();
            var intervalo_valores = $('#intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor').val();

            // Campo y parámetros extra de sensor
            var campo = $('#campo_widget_evolucion_valores_comparacion_periodos_sensor').val();
            var parametros_extra_campo = $('#parametros_extra_campo_widget_evolucion_valores_comparacion_periodos_sensor').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Colores de fondo y límites de colores de fondo
            var parametros_controles_fondo = dame_parametros_colores_fondo_widget("widget_evolucion_valores_comparacion_periodos_sensor", null, null);
            if (parametros_controles_fondo == null) {
                return;
            }
            var utilizar_colores_fondo = parametros_controles_fondo["utilizar_colores_fondo"];
            var colores_fondo = parametros_controles_fondo["colores_fondo"];
            var valor_limite_colores_fondo_1 = parametros_controles_fondo["valor_limite_colores_fondo_1"];
            var valor_limite_colores_fondo_2 = parametros_controles_fondo["valor_limite_colores_fondo_2"];

            // Tipo de valores de límite de colores de fondo
            var tipo_valores_limite_colores_fondo = $('#tipo_valores_limite_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor').val();
            if (utilizar_colores_fondo == true) {
                if (tipo_valores_limite_colores_fondo == TIPO_VALORES_LIMITE_COLORES_FONDO_WIDGET_NINGUNO) {
                    jAlert(TLNT.Idiomas._('Seleccione un tipo de valores límite de colores de fondo'));
                    return;
                }
            }

            // Icono
            var icono = $('#icono_widget_evolucion_valores_comparacion_periodos_sensor').val();

            // Horario semanal y exclusión de fechas
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                id_sensor,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                intervalo_valores,
                utilizar_colores_fondo,
                colores_fondo,
                tipo_valores_limite_colores_fondo,
                valor_limite_colores_fondo_1,
                valor_limite_colores_fondo_2,
                icono,
                cadena_horario_semanal,
                cadena_exclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_grafica_comparacion_campos_iguales_sensores').val();

            // Se comprueba si hay clase de sensor y sensores seleccionados
            var clase_sensor = $('#clase_sensor_widget_grafica_comparacion_campos_iguales_sensores').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var ids_sensores = [];
            $("#ids_sensores_widget_grafica_comparacion_campos_iguales_sensores option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores.push($(this).val());
                }
            });
            if (ids_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Intervalo de valores
            var intervalo_valores = $('#intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores').val();

            // Campo y parámetros extra de sensor
            var campo = $('#campo_widget_grafica_comparacion_campos_iguales_sensores').val();
            var parametros_extra_campo = $('#parametros_extra_campo_widget_grafica_comparacion_campos_iguales_sensores').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                intervalo_valores,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_grafica_comparacion_campos_diferentes_sensores').val();

            // Parámetros de sensores
            var clases_sensores = [];
            var ids_sensores = [];
            var campos_parametros_extra = [];
            var salir_funcion = false;
            for (var i = 0; i < NUMERO_SENSORES_COMPARACION_CAMPOS_DIFERENTES; i++) {
                var numero_sensor = i + 1;
                var id_lista_clases_sensor = "clase_sensor_" + numero_sensor + "_widget_grafica_comparacion_campos_diferentes_sensores";
                var id_lista_sensores = "id_sensor_" + numero_sensor + "_widget_grafica_comparacion_campos_diferentes_sensores";
                var id_lista_campos = "campo_" + numero_sensor + "_widget_grafica_comparacion_campos_diferentes_sensores";
                var id_lista_parametros_extra_campo = "parametros_extra_campo_" + numero_sensor + "_widget_grafica_comparacion_campos_diferentes_sensores";

                var clase_sensor = $('#' + id_lista_clases_sensor).val();
                if (clase_sensor != CLASE_NINGUNA) {
                    clases_sensores.push(clase_sensor);

                    // Identificador de sensor
                    var id_sensor = $('#' + id_lista_sensores).val();
                    if (id_sensor == ID_NINGUNO) {
                        jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                        salir_funcion = true;
                        return;
                    }
                    ids_sensores.push(id_sensor);

                    // Campo y parámetros extra
                    var campo = $('#' + id_lista_campos).val();
                    var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
                    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
                    if (parametros_extra_campo_correctos == false) {
                        salir_funcion = true;
                        return;
                    }
                    var campo_parametros_extra = campo;
                    if (parametros_extra_campo != "") {
                        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
                    }
                    campos_parametros_extra.push(campo_parametros_extra);
                };
            }
            if (salir_funcion == true) {
                return;
            }
            if (clases_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Intervalo de valores y unificar escalas
            var intervalo_valores = $('#intervalo_valores_widget_grafica_comparacion_campos_diferentes_sensores').val();
            var unificar_escalas = $('#unificar_escalas_widget_grafica_comparacion_campos_diferentes_sensores').val();

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clases_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                campos_parametros_extra.join(SEPARADOR_PARAMETROS_SIMPLES),
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                intervalo_valores,
                unificar_escalas,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_grafica_valores_generales_sensores').val();

            // Se recuperan las clases de sensor y campos seleccionados
            var clases_sensor = [];
            var campos_parametros_extra = [];
            var salir_funcion = false;
            for (var i = 0; i < NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES; i++) {
                var numero_campo = i + 1;
                var id_lista_clases_sensor = "clase_sensor_" + numero_campo + "_widget_grafica_valores_generales_sensores";
                var id_lista_campos = "campo_" + numero_campo + "_widget_grafica_valores_generales_sensores";
                var id_lista_parametros_extra_campo = "parametros_extra_campo_" + numero_campo + "_widget_grafica_valores_generales_sensores";

                var clase_sensor = $('#' + id_lista_clases_sensor).val();
                if (clase_sensor != CLASE_NINGUNA) {
                    clases_sensor.push(clase_sensor);

                    // Se recupera el campo y los parámetros extra
                    var campo = $('#' + id_lista_campos).val();
                    var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
                    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
                    if (parametros_extra_campo_correctos == false) {
                        salir_funcion = true;
                        return;
                    }
                    var campo_parametros_extra = campo;
                    if (parametros_extra_campo != "") {
                        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
                    }
                    campos_parametros_extra.push(campo_parametros_extra);
                }
                else {
                    // Nota: La primera clase debe estar seleccionada (es la que determina el intervalo y la agregación)
                    if (i == 0) {
                        jAlert(TLNT.Idiomas._("Debe seleccionar la primera clase de sensor"));
                        salir_funcion = true;
                        return;
                    }
                }
            }
            if (salir_funcion == true) {
                return;
            }
            if (clases_sensor.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos una clase de sensor"));
                return;
            }

            // Se comprueba si hay sensores seleccionados
            var ids_sensores = [];
            $("#ids_sensores_widget_grafica_valores_generales_sensores option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores.push($(this).val());
                }
            });
            if (ids_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_grafica_valores_generales_sensores').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_grafica_valores_generales_sensores').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_grafica_valores_generales_sensores').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Intervalo de valores y agregación
            var intervalo_valores = $('#intervalo_valores_widget_grafica_valores_generales_sensores').val();
            var agregacion = $('#agregacion_widget_grafica_valores_generales_sensores').val();

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clases_sensor.join(SEPARADOR_PARAMETROS_SIMPLES),
                campos_parametros_extra.join(SEPARADOR_PARAMETROS_SIMPLES),
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                intervalo_valores,
                agregacion,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_valor_agregado_valores_generales_sensores').val();

            // Se recuperan las clases de sensor y campos seleccionados
            var clases_sensor = [];
            var campos_parametros_extra = [];
            var salir_funcion = false;
            for (var i = 0; i < NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES; i++) {
                var numero_campo = i + 1;
                var id_lista_clases_sensor = "clase_sensor_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores";
                var id_lista_campos = "campo_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores";
                var id_lista_parametros_extra_campo = "parametros_extra_campo_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores";

                var clase_sensor = $('#' + id_lista_clases_sensor).val();
                if (clase_sensor != CLASE_NINGUNA) {
                    clases_sensor.push(clase_sensor);

                    // Se recupera el campo y los parámetros extra
                    var campo = $('#' + id_lista_campos).val();
                    var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
                    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
                    if (parametros_extra_campo_correctos == false) {
                        salir_funcion = true;
                        return;
                    }
                    var campo_parametros_extra = campo;
                    if (parametros_extra_campo != "") {
                        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
                    }
                    campos_parametros_extra.push(campo_parametros_extra);
                }
                else {
                    // Nota: La primera clase debe estar seleccionada (es la que determina el intervalo y la agregación)
                    if (i == 0) {
                        jAlert(TLNT.Idiomas._("Debe seleccionar la primera clase de sensor"));
                        salir_funcion = true;
                        return;
                    }
                }
            }
            if (salir_funcion == true) {
                return;
            }
            if (clases_sensor.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos una clase de sensor"));
                return;
            }

            // Se comprueba si hay sensores seleccionados
            var ids_sensores = [];
            $("#ids_sensores_widget_valor_agregado_valores_generales_sensores option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores.push($(this).val());
                }
            });
            if (ids_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_valor_agregado_valores_generales_sensores').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_valor_agregado_valores_generales_sensores').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_valor_agregado_valores_generales_sensores').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Intervalo de valores y agregación
            var intervalo_valores = $('#intervalo_valores_widget_valor_agregado_valores_generales_sensores').val();
            var agregacion = $('#agregacion_widget_valor_agregado_valores_generales_sensores').val();
            if (agregacion == AGREGACION_NINGUNA) {
                jAlert(TLNT.Idiomas._("La agregación no puede ser ninguna"));
                return;
            }

            // Colores de fondo y límites de colores de fondo
            var parametros_controles_fondo = dame_parametros_colores_fondo_widget("widget_valor_agregado_valores_generales_sensores", null, null);
            if (parametros_controles_fondo == null) {
                return;
            }
            var utilizar_colores_fondo = parametros_controles_fondo["utilizar_colores_fondo"];
            var colores_fondo = parametros_controles_fondo["colores_fondo"];
            var valor_limite_colores_fondo_1 = parametros_controles_fondo["valor_limite_colores_fondo_1"];
            var valor_limite_colores_fondo_2 = parametros_controles_fondo["valor_limite_colores_fondo_2"];

            // Icono
            var icono = $('#icono_widget_valor_agregado_valores_generales_sensores').val();

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clases_sensor.join(SEPARADOR_PARAMETROS_SIMPLES),
                campos_parametros_extra.join(SEPARADOR_PARAMETROS_SIMPLES),
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                intervalo_valores,
                agregacion,
                utilizar_colores_fondo,
                colores_fondo,
                valor_limite_colores_fondo_1,
                valor_limite_colores_fondo_2,
                icono,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES: {
            // Tipo de gráfica
            var tipo_grafica = $('#tipo_grafica_widget_grafica_incrementos_totales_sensores').val();

            // Ratio
            var id_ratio = $('#id_ratio_widget_grafica_incrementos_totales_sensores').val();

            // Se recuperan las clases de sensor y campos seleccionados
            var clases_sensor = [];
            var campos_parametros_extra = [];
            var salir_funcion = false;
            for (var i = 0; i < NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES; i++) {
                var numero_campo = i + 1;
                var id_lista_clases_sensor = "clase_sensor_" + numero_campo + "_widget_grafica_incrementos_totales_sensores";
                var id_lista_campos = "campo_" + numero_campo + "_widget_grafica_incrementos_totales_sensores";
                var id_lista_parametros_extra_campo = "parametros_extra_campo_" + numero_campo + "_widget_grafica_incrementos_totales_sensores";

                var clase_sensor = $('#' + id_lista_clases_sensor).val();
                if (clase_sensor != CLASE_NINGUNA) {
                    clases_sensor.push(clase_sensor);

                    // Se recupera el campo y los parámetros extra
                    var campo = $('#' + id_lista_campos).val();
                    var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
                    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
                    if (parametros_extra_campo_correctos == false) {
                        salir_funcion = true;
                        return;
                    }
                    var campo_parametros_extra = campo;
                    if (parametros_extra_campo != "") {
                        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
                    }
                    campos_parametros_extra.push(campo_parametros_extra);
                }
                else {
                    // Nota: La primera clase debe estar seleccionada (es la que determina el intervalo y la agregación)
                    if (i == 0) {
                        jAlert(TLNT.Idiomas._("Debe seleccionar la primera clase de sensor"));
                        salir_funcion = true;
                        return;
                    }
                }
            }
            if (salir_funcion == true) {
                return;
            }
            if (clases_sensor.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos una clase de sensor"));
                return;
            }

            // Se comprueba si hay sensores seleccionados
            var ids_sensores = [];
            $("#ids_sensores_widget_grafica_incrementos_totales_sensores option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores.push($(this).val());
                }
            });
            if (ids_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_grafica_incrementos_totales_sensores').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_grafica_incrementos_totales_sensores').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_grafica_incrementos_totales_sensores').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Intervalo de valores y agregación
            var intervalo_valores = $('#intervalo_valores_widget_grafica_incrementos_totales_sensores').val();
            var agregacion = $('#agregacion_widget_grafica_incrementos_totales_sensores').val();

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                tipo_grafica,
                id_ratio,
                clases_sensor.join(SEPARADOR_PARAMETROS_SIMPLES),
                campos_parametros_extra.join(SEPARADOR_PARAMETROS_SIMPLES),
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                intervalo_valores,
                agregacion,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Widgets del módulo Actuadores
        case TIPO_WIDGET_INFORMACION_ACTUADOR: {
            // Se comprueba si hay clase de actuador y actuador seleccionados
            var clase_actuador = $('#clase_actuador_widget_informacion_actuador').val();
            if (clase_actuador == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var id_actuador = $('#id_actuador_widget_informacion_actuador').val();
            if (id_actuador == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay actuador seleccionado"));
                return;
            }

            // Icono
            var icono = $('#icono_widget_informacion_actuador').val();

            // Parámetros de tipo
            parametros_tipo = [
                clase_actuador,
                id_actuador,
                icono].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES: {
            // Se comprueba si hay clase de actuador y grupo de actuadores seleccionados
            var clase_actuador = $('#clase_actuador_widget_informacion_grupo_actuadores').val();
            if (clase_actuador == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var id_grupo_actuadores = $('#id_grupo_actuadores_widget_informacion_grupo_actuadores').val();
            if (id_grupo_actuadores == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay grupo de actuadores seleccionado"));
                return;
            }

            // Icono
            var icono = $('#icono_widget_informacion_grupo_actuadores').val();

            // Parámetros de tipo
            parametros_tipo = [
                clase_actuador,
                id_grupo_actuadores,
                icono].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Widgets del módulo Smartmeter
        case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR: {
            // Ratio
            var id_ratio = $('#id_ratio_widget_grafica_consumos_costes_tramos_sensor').val();

            // Identificador de sensor
            var id_sensor = $('#id_sensor_widget_grafica_consumos_costes_tramos_sensor').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Valor y agrupación de valores
            var valor = $('#valor_widget_grafica_consumos_costes_tramos_sensor').val();
            var agrupacion_valores = $('#agrupacion_valores_widget_grafica_consumos_costes_tramos_sensor').val();

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Horario semanal
            var horario_semanal = dame_horario_semanal_controles("widget", false);
            if (horario_semanal.correcto == false) {
                return;
            }
            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

            // Exclusión e inclusión de fechas
            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_widget");
            if (exclusion_fechas.correcto == false) {
                return;
            }
            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
            var inclusion_fechas = dame_fechas_controles("inclusion_fechas_widget");
            if (inclusion_fechas.correcto == false) {
                return;
            }
            var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                id_sensor,
                valor,
                agrupacion_valores,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_COSTE_FACTURA_SENSOR: {
            // Identificador de sensor
            var id_sensor = $('#id_sensor_widget_coste_factura_sensor').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Medición y concepto de factura
            var medicion = $('#medicion_widget_coste_factura_sensor').val();
            var concepto_factura = $('#concepto_factura_widget_coste_factura_sensor').val();

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_coste_factura_sensor').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_coste_factura_sensor').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_coste_factura_sensor').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Colores de fondo y límites de colores de fondo
            var parametros_controles_fondo = dame_parametros_colores_fondo_widget("widget_coste_factura_sensor", null, null);
            if (parametros_controles_fondo == null) {
                return;
            }
            var utilizar_colores_fondo = parametros_controles_fondo["utilizar_colores_fondo"];
            var colores_fondo = parametros_controles_fondo["colores_fondo"];
            var valor_limite_colores_fondo_1 = parametros_controles_fondo["valor_limite_colores_fondo_1"];
            var valor_limite_colores_fondo_2 = parametros_controles_fondo["valor_limite_colores_fondo_2"];

            // Icono
            var icono = $('#icono_widget_coste_factura_sensor').val();

            // Parámetros de tipo
            parametros_tipo = [
                medicion,
                id_sensor,
                concepto_factura,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                utilizar_colores_fondo,
                colores_fondo,
                valor_limite_colores_fondo_1,
                valor_limite_colores_fondo_2,
                icono].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Widgets del módulo Proyectos
        case TIPO_WIDGET_SIMULADOR_LINEA_BASE: {
            // Se comprueba si hay línea base seleccionada
            var id_linea_base = $('#id_linea_base_widget_simulador_linea_base').val();
            if (id_linea_base == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay línea base seleccionada"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_simulador_linea_base').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_simulador_linea_base').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_simulador_linea_base').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Parámetros de tipo
            parametros_tipo = [
                id_linea_base,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_WIDGET_INFORMACION_PROYECTO: {
            // Se comprueba si hay proyecto seleccionado
            var id_proyecto = $('#id_proyecto_widget_informacion_proyecto').val();
            if (id_proyecto == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay proyecto seleccionado"));
                return;
            }

            // Periodo de tiempo
            var periodo_tiempo = $('#periodo_tiempo_widget_informacion_proyecto').val();
            var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_widget_informacion_proyecto').val();
            var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_widget_informacion_proyecto').val();
            var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Icono
            var icono = $('#icono_widget_informacion_proyecto').val();

            // Parámetros de tipo
            parametros_tipo = [
                id_proyecto,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                icono].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
    }

    // Parámetros de la ventana
	var id_widget = $("#parametros_ventana_anyadir_modificar_widget").attr("id_widget");
    var nombre_anterior = $("#parametros_ventana_anyadir_modificar_widget").attr("nombre");
    var id_pestanya_anterior = $("#parametros_ventana_anyadir_modificar_widget").attr("id_pestanya");
    var cadena_numero_columnas_anterior = $("#parametros_ventana_anyadir_modificar_widget").attr("numero_columnas");

    // Nombre del widget e identificador de pestaña
	var nombre = $("#nombre_widget").val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_widget").addClass('data-check-failed');
        return;
    }
    var id_pestanya = $('#pestanya_widget').val();

    // Se añade o modifica el widget
    var anyadir_widget = $("#parametros_ventana_anyadir_modificar_widget").attr("anyadir_widget");
    if (anyadir_widget == true) {
        // Flag de duplicar widget
        var id_widget_anterior = id_widget;
        var duplicar_widget = (id_widget_anterior != ID_NINGUNO);

        // Se comprueba la imagen
        switch (tipo) {
            case TIPO_WIDGET_IMAGEN: {
                var duplicar_imagen = false;
                if ($('#fichero_imagen_widget_imagen_text').val() == "") {
                    if (duplicar_widget == true) {
                        // Nota: Es un duplicado y ya había imagen: no hace faltar subir un nuevo fichero de imagen,
                        // se duplicará la imagen anterior
                        duplicar_imagen = true;
                    }
                    else {
                        jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen"));
                        return;
                    }
                }
                else {
                    var imagen_correcta = comprueba_imagen_correcta(ORIGEN_IMAGEN_WIDGET_IMAGEN, "fichero_imagen_widget_imagen_file");
                    if (imagen_correcta == false) {
                        $('#fichero_imagen_widget_imagen_text').addClass('data-check-failed');
                        $('#fichero_imagen_widget_imagen_text').val("");
                        return;
                    }
                }
                break;
            }
        }

        // Se añade el widget
        $.post("./src/lib/modulos/widgets/anyade_widget.php", {
            nombre: nombre,
            tipo: tipo,
            parametros_tipo: parametros_tipo,
            id_pestanya: id_pestanya,
            numero_columnas: cadena_numero_columnas
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Identificador de widget añadido
            var id_widget = resultado.id_widget;

            // Se guarda la imagen (o se duplica la anterior si corresponde)
            switch (tipo) {
                case TIPO_WIDGET_IMAGEN: {
                    if (duplicar_imagen == false) {
                        var id_origen = [
                            id_pestanya,
                            id_widget].join(SEPARADOR_PARAMETROS_SIMPLES);
                        var control_fichero_imagen = $('#fichero_imagen_widget_imagen_file')[0];
                        var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_WIDGET_IMAGEN, id_origen, control_fichero_imagen);
                        if (imagen_guardada_correcta == false) {
                            return;
                        }
                    }
                    else {
                        var id_origen = [
                            id_pestanya,
                            id_widget].join(SEPARADOR_PARAMETROS_SIMPLES);
                        var id_origen_anterior = [
                            id_pestanya_anterior,
                            id_widget_anterior].join(SEPARADOR_PARAMETROS_SIMPLES);
                        var control_fichero_imagen = $('#fichero_imagen_widget_imagen_file')[0];
                        var imagen_duplicada_correcta = duplica_imagen_base_datos(ORIGEN_IMAGEN_WIDGET_IMAGEN, id_origen_anterior, id_origen);
                        if (imagen_duplicada_correcta == false) {
                            return;
                        }
                    }
                    break;
                }
            }

            // Se actualiza la cuadrícula de widgets correspondiente
            actualiza_cuadricula_widgets(id_pestanya, true);
            jInfo(resultado.msg);
        });
    }
    else {
        $.post("./src/lib/modulos/widgets/modifica_widget.php", {
            id_widget: id_widget,
            nombre: nombre,
            tipo: tipo,
            parametros_tipo: parametros_tipo,
            id_pestanya: id_pestanya,
            numero_columnas: cadena_numero_columnas,
            id_pestanya_anterior: id_pestanya_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Se guarda la imagen
            switch (tipo) {
                case TIPO_WIDGET_IMAGEN: {
                    if ($('#fichero_imagen_widget_imagen_text').val() != "") {
                        var id_origen = [
                            id_pestanya,
                            id_widget].join(SEPARADOR_PARAMETROS_SIMPLES);
                        var control_fichero_imagen = $('#fichero_imagen_widget_imagen_file')[0];
                        var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_WIDGET_IMAGEN, id_origen, control_fichero_imagen);
                        if (imagen_guardada_correcta == false) {
                            return;
                        }
                    }
                    break;
                }
            }

            // Se actualiza el widget o la cuadrícula de widgets según corresponda
            var actualizar_cuadricula_widgets =
                (nombre != nombre_anterior) ||
                (id_pestanya != id_pestanya_anterior) ||
                (cadena_numero_columnas != cadena_numero_columnas_anterior);
            if (actualizar_cuadricula_widgets == true) {
                actualiza_cuadricula_widgets(id_pestanya_anterior, true);
            }
            else {
                actualiza_widget(id_widget);
            }
            $('#ventana_modal').modal('hide');

            // Mensaje de información
            jInfo(resultado.msg);
        });
    }
}


// Actualiza el contenido de un widget al pulsar su botón de actualización
function boton_actualizar_widget() {
    var params = this.id.split('__');
    var id_widget = params[1];

    actualiza_widget(id_widget);
}


// Actualiza el widget especificado
function actualiza_widget(id_widget) {
    // Se actualiza el contenido de un widget
    var numero_widget = $("#parametros_widget__" + id_widget).attr("numero_widget");
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);
    $.post("./src/lib/modulos/widgets/dame_informacion_widget.php", {
		id_widget: id_widget,
        numero_widget: numero_widget,
        minutos_desfase_utc: minutos_desfase_utc
	},
	function (data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se recupera la informacion y se dibuja el contenido del widget
        var info_widget = resultado.info_widget;
        var id_pestanya = info_widget.id_pestanya;
        var nombre = info_widget.nombre;
        var tipo = info_widget.tipo;
        var parametros_tipo = info_widget.parametros_tipo;
        var datos_widget = info_widget.datos_widget;
        var numero_columnas_fila_widget = info_widget.numero_columnas_fila_widget;
        var numero_columnas_widget = info_widget.numero_columnas_widget;

        dibuja_widget_tipo(
            id_pestanya,
            id_widget,
            nombre,
            tipo,
            parametros_tipo,
            datos_widget,
            numero_columnas_fila_widget,
            numero_columnas_widget);

        // Se establecen los eventos
        TLNT.Navegacion.establece_eventos_contenido_widgets();
    });
}


// Actualiza en el pie de la página la fecha de actualización de los widgets
function actualiza_fecha_actualizacion_widgets() {
    var fecha_actual = new Date();
    var cadena_fecha_actual = convierte_fecha_a_cadena(fecha_actual, formato_fecha_local_jquery_ui);
    cadena_fecha_actual += ", " + dame_cadena_hora(fecha_actual);
    var texto_actualizado_hora_actual = TLNT.Idiomas._("hora de actualización de widgets") + ": " + cadena_fecha_actual;
    actualiza_texto_pie_pagina(texto_actualizado_hora_actual);
}


// Actualiza la cuadrícula de widgets de la pestaña especificada
function   actualiza_cuadricula_widgets(id_pestanya, forzar_actualizacion_html_cuadricula_widgets) {
    console.log("Entra en el método que actualiza la cuadrícula de widgets");
    $.post("./src/lib/modulos/widgets/dame_cuadricula_widgets.php", {
        id_pestanya: id_pestanya
	},
	function (data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Información de la cuadrícula de iwdgets
        var html_cuadricula_widgets = resultado.html_cuadricula_widgets;
        var ids_widgets = resultado.ids_widgets;
        var numeros_columnas_widgets = resultado.numeros_columnas_widgets;
        var numeros_columnas_filas_widgets = resultado.numeros_columnas_filas_widgets;
        var ajustar_altura_widgets = resultado.ajustar_altura_widgets;
        var cadena_parametros_apariencia_pestanya = resultado.cadena_parametros_apariencia_pestanya;
        var cadena_parametros_apariencia_widgets = resultado.cadena_parametros_apariencia_widgets;
        var cadena_parametros_opciones_pantalla_completa = resultado.cadena_parametros_opciones_pantalla_completa;
        var imagen_fondo = resultado.imagen_fondo;
        var mostrar_cabecera = resultado.mostrar_cabecera;
        var mostrar_hora_cabecera = resultado.mostrar_hora_cabecera;
        var mostrar_fecha_cabecera = resultado.mostrar_fecha_cabecera;

        // Se dibujan los widgets
        var numero_widgets = ids_widgets.length;
        if (numero_widgets > 0) {
            // Desfase horario respecto a la hora UTC
            var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);
            $.post("./src/lib/modulos/widgets/dame_informacion_widgets.php", {
                id_pestanya: id_pestanya,
                ids_widgets: ids_widgets,
                numeros_columnas_widgets: numeros_columnas_widgets,
                numeros_columnas_filas_widgets: numeros_columnas_filas_widgets,
                minutos_desfase_utc: minutos_desfase_utc
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                // Se comprueba si hay que actualizar el html de la cuadrícula de widgets
                var actualizar_html_cuadricula_widgets = true;
                if (forzar_actualizacion_html_cuadricula_widgets == false) {
                    if ($("#parametros_cuadricula_widgets__" + id_pestanya).length > 0) {
                        // Se recuperan los parámetros de la cuadrícula de widgets
                        var cadena_ids_widgets_anterior = $("#parametros_cuadricula_widgets__" + id_pestanya).attr("ids_widgets");
                        var cadena_numeros_columnas_filas_widgets_anterior = $("#parametros_cuadricula_widgets__" + id_pestanya).attr("numeros_columnas_filas_widgets");
                        var ajustar_altura_widgets_anterior = $("#parametros_cuadricula_widgets__" + id_pestanya).attr("ajustar_altura_widgets");
                        var cadena_parametros_apariencia_pestanya_anterior = $("#parametros_cuadricula_widgets__" + id_pestanya).attr("cadena_parametros_apariencia_pestanya");
                        var cadena_parametros_apariencia_widgets_anterior = $("#parametros_cuadricula_widgets__" + id_pestanya).attr("cadena_parametros_apariencia_widgets");
                        var cadena_parametros_opciones_pantalla_completa_anterior = $("#parametros_cuadricula_widgets__" + id_pestanya).attr("cadena_parametros_opciones_pantalla_completa");

                        // Si los parámetros son los mismos no se actualiza la cuadrícula de widgets (sólo de redibujan los widgets)
                        var cadena_ids_widgets = ids_widgets.join(",");
                        var cadena_numeros_columnas_filas_widgets = "";
                        if (numeros_columnas_filas_widgets != null) {
                            cadena_numeros_columnas_filas_widgets = numeros_columnas_filas_widgets.join(",");
                        }
                        if ((cadena_ids_widgets_anterior == cadena_ids_widgets) &&
                            (cadena_numeros_columnas_filas_widgets_anterior == cadena_numeros_columnas_filas_widgets) &&
                            (ajustar_altura_widgets_anterior == ajustar_altura_widgets) &&
                            (cadena_parametros_apariencia_pestanya_anterior == cadena_parametros_apariencia_pestanya) &&
                            (cadena_parametros_apariencia_widgets_anterior == cadena_parametros_apariencia_widgets) &&
                            (cadena_parametros_opciones_pantalla_completa_anterior == cadena_parametros_opciones_pantalla_completa)) {
                            actualizar_html_cuadricula_widgets = false;
                        }
                    }
                }

                // Se actualiza el html de la cuadrícula de widgets
                if (actualizar_html_cuadricula_widgets == true) {
                    $("#cuadricula-widgets-pestanya__" + id_pestanya).html(html_cuadricula_widgets);

                    // Se carga la imagen de fondo (si existe)
                    if (parseInt(imagen_fondo) == VALOR_SI) {
                        var res_carga_imagen = carga_imagen_base_datos(ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO, id_pestanya, null);
                        var imagen_cargada_correcta = res_carga_imagen.imagen_cargada_correcta;
                        if (imagen_cargada_correcta == true) {
                            $("#contenedor-cuadricula-widgets-pestanya__" + id_pestanya).css('background-image', 'url("' + res_carga_imagen.ruta_fichero_imagen + '")');
                            $("#contenedor-cuadricula-widgets-pestanya__" + id_pestanya).css('background-repeat', 'no-repeat');
                            $("#contenedor-cuadricula-widgets-pestanya__" + id_pestanya).css('background-size', 'cover');
                            $("#contenedor-cuadricula-widgets-pestanya__" + id_pestanya).css('background-position', '50% 50%');
                        }
                    }

                    // Se establece el timeout para la actualización de la fecha y hora de la pestaña de widgets
                    if ((mostrar_cabecera == VALOR_SI) &&
                        ((mostrar_hora_cabecera == VALOR_SI) || (mostrar_fecha_cabecera == VALOR_SI))) {
                        expiracion_timeout_actualizacion_periodica_fecha_hora_pestanya_widgets();
                    }
                }

                // Se recupera la información y se dibuja el contenido de los widgets
                var info_widgets = resultado.info_widgets;
                for (var i = 0 ; i < numero_widgets; i++) {
                    var id_widget = ids_widgets[i];
                    var nombre = info_widgets[i].nombre;
                    var tipo = info_widgets[i].tipo;
                    var parametros_tipo = info_widgets[i].parametros_tipo;
                    var datos_widget = info_widgets[i].datos_widget;
                    var numero_columnas_fila_widget = info_widgets[i].numero_columnas_fila_widget;
                    var numero_columnas_widget = info_widgets[i].numero_columnas_widget;

                    dibuja_widget_tipo(
                        id_pestanya,
                        id_widget,
                        nombre,
                        tipo,
                        parametros_tipo,
                        datos_widget,
                        numero_columnas_fila_widget,
                        numero_columnas_widget);
                }

                // Establecimiento de eventos
                TLNT.Navegacion.establece_eventos_tablas_datos();
                TLNT.Navegacion.establece_eventos_contenido_widgets();
            });
        }
        else {
            var texto_cuadricula_widgets = "";
            texto_cuadricula_widgets += "<div class='texto-cuadricula-widgets-vacia elemento-no-seleccionable'>";
            texto_cuadricula_widgets += "    <i class='icon-info-sign color-azul'></i> " + TLNT.Idiomas._("No hay widgets configurados");
            texto_cuadricula_widgets += "</div>";
            $('#cuadricula-widgets-pestanya__' + id_pestanya).html(texto_cuadricula_widgets);
        }

        // Se actualiza la fecha de actualización de los widgets
        actualiza_fecha_actualizacion_widgets();
    });
}


// Expiración del timeout para actualización de la fecha y hora de la pestaña de widgets
function expiracion_timeout_actualizacion_periodica_fecha_hora_pestanya_widgets() {
    if (temporizador_actualizacion_fecha_hora_pestanya_widgets != null) {
        clearTimeout(temporizador_actualizacion_fecha_hora_pestanya_widgets);
        temporizador_actualizacion_fecha_hora_pestanya_widgets = null;
    }

    // Se crean las cadenas de fecha y hora actuales
    var fecha_actual = new Date();
    var horas = fecha_actual.getHours();
    var minutos = fecha_actual.getMinutes();
    var segundos = fecha_actual.getSeconds();
    if (minutos < 10) {
        minutos = "0" + minutos;
    }
    if (segundos < 10) {
        segundos = "0" + segundos;
    }
    var cadena_hora = horas + ":" + minutos + ":" + segundos;
    $(".hora-cabecera-pestanya-widgets").html(cadena_hora);

    // Día de la semana y fecha
    var dia_semana = fecha_actual.getDay();
    var dia_mes = fecha_actual.getDate();
    var mes = fecha_actual.getMonth();
    var anyo = fecha_actual.getFullYear();

    var nombres_dias_semana = dame_nombres_dias_semana();
    var nombre_dia_semana = nombres_dias_semana[dia_semana - 1];
    var nombres_meses = dame_nombres_meses();
    var nombre_mes = nombres_meses[mes].toLowerCase();
    var cadena_fecha = nombre_dia_semana + ", " +
        dia_mes + " " + TLNT.Idiomas._("de") + " " + nombre_mes + " " + TLNT.Idiomas._("de") + " " + anyo;
    $(".fecha-cabecera-pestanya-widgets").html(cadena_fecha);

    // Se restablece el timeout
    temporizador_actualizacion_fecha_hora_pestanya_widgets = setTimeout(expiracion_timeout_actualizacion_periodica_fecha_hora_pestanya_widgets, 1000);
}


//
// Funciones auxiliares
//


// Devuelve los parámetros de colores de fondo
function dame_parametros_colores_fondo_widget(id_controles, valor_minimo, valor_maximo) {
    var utilizar_colores_fondo = $('#utilizar_colores_fondo_' + id_controles).val();
    var color_fondo_1 = $('#color_fondo_1_' + id_controles).val();
    var color_fondo_2 = $('#color_fondo_2_' + id_controles).val();
    var color_fondo_3 = $('#color_fondo_3_' + id_controles).val();
    if (utilizar_colores_fondo == true) {
        if ((color_fondo_1 == "") || (color_fondo_2 == "") || (color_fondo_3 == "")) {
            jAlert(TLNT.Idiomas._("Hay que establecer todos los colores de fondo"));
            return (null);
        }
    }
    var colores_fondo = [
        color_fondo_1,
        color_fondo_2,
        color_fondo_3].join(SEPARADOR_PARAMETROS_SIMPLES);
    var cadena_valor_limite_colores_fondo_1 = $('#valor_limite_colores_fondo_1_' + id_controles).val();
    var cadena_valor_limite_colores_fondo_2 = $('#valor_limite_colores_fondo_2_' + id_controles).val();
    if (utilizar_colores_fondo == true) {
        if ((cadena_valor_limite_colores_fondo_1 == "") || (cadena_valor_limite_colores_fondo_2 == "")) {
            jAlert(TLNT.Idiomas._("Hay que establecer los valores de límites de colores de fondo"));
            return (null);
        }
        var valor_limite_colores_fondo_1 = parseFloat(cadena_valor_limite_colores_fondo_1);
        var valor_limite_colores_fondo_2 = parseFloat(cadena_valor_limite_colores_fondo_2);
        if (valor_minimo != null) {
            if (valor_minimo >= valor_limite_colores_fondo_1) {
                jAlert(TLNT.Idiomas._('Por favor, compruebe que el valor límite de colores de fondo 1 sea mayor que el valor mínimo del indicador'));
                return (null);
            }
        }
        if (valor_limite_colores_fondo_1 > valor_limite_colores_fondo_2) {
            jAlert(TLNT.Idiomas._('Por favor, compruebe que el valor límite de colores de fondo 2 sea mayor o igual que el valor límite de colores 1'));
            return (null);
        }
        if (valor_maximo != null) {
            if (valor_limite_colores_fondo_2 >= valor_maximo) {
                jAlert(TLNT.Idiomas._('Por favor, compruebe que el valor máximo del indicador sea mayor que el valor límite de colores de fondo 2'));
                return (null);
            }
        }
    }

    // Parámetros recuperados
    var parametros_colores_fondo = {};
    parametros_colores_fondo["utilizar_colores_fondo"] = utilizar_colores_fondo;
    parametros_colores_fondo["colores_fondo"] = colores_fondo;
    parametros_colores_fondo["valor_limite_colores_fondo_1"] = cadena_valor_limite_colores_fondo_1;
    parametros_colores_fondo["valor_limite_colores_fondo_2"] = cadena_valor_limite_colores_fondo_2;

    // Se devuelven los parámetros recuperados
    return (parametros_colores_fondo);
}
