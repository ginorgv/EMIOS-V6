//
// Funciones de plantillas de informes
//


// Muestra la tabla de plantillas de informes aplicando el filtro
function boton_personal_filtro_plantillas_informes_tabla() {
    boton_personal_actualizar_tabla_plantillas_informes();
}


function boton_personal_mostrar_ventana_anyadir_modificar_plantilla_informe(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_plantilla_informe = params[1];
    var tipo_operacion_administracion = params[2];

	$.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/muestra_ventana_anyadir_modificar_plantilla_informe.php", {
        id_plantilla_informe: id_plantilla_informe,
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


function boton_personal_eliminar_plantilla_informe(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_plantilla_informe = params[1];
    var nombre_plantilla_informe = $(this).attr('nombre_plantilla_informe');

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la plantilla de informe?") + "\n(" + escapeHtml(nombre_plantilla_informe) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/elimina_plantilla_informe.php", {
                id_plantilla_informe: id_plantilla_informe
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
				actualiza_tabla_plantillas_informes();

                // Se recarga la lista de plantillas de informe del informe de plantilla de informe
                recarga_lista_plantillas_informes_informe_plantilla_informe(true);
			});
		}
	});
}


function boton_personal_anyadir_modificar_plantilla_informe() {
    // Comprobación de datos correctos
	if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
	var anyadir_plantilla_informe = $("#parametros_ventana_anyadir_modificar_plantilla_informe").attr("anyadir_plantilla_informe");
	var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_plantilla_informe").attr("id_plantilla_informe");

    // Nombre y descripción de la plantilla de informe
    var nombre = $('#nombre_plantilla_informe').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_plantilla_informe").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_plantilla_informe').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_plantilla_informe").addClass('data-check-failed');
        return;
    }

    // Tipo de la plantilla de informe
    var tipo = $('#tipo_plantilla_informe').val();

    // Título del informe
    var titulo_informe = $('#titulo_informe_plantilla_informe').val();

    // Periodo de tiempo por defecto
    var periodo_tiempo_defecto = $('#periodo_tiempo_defecto_plantilla_informe').val();
    var iniciar_comienzo_periodo_tiempo_defecto = $('#iniciar_comienzo_periodo_tiempo_defecto_plantilla_informe').val();

    // Tipo de selección de horario semanal y fechas
    var tipo_seleccion_horario_semanal_fechas = $('#tipo_seleccion_horario_semanal_fechas_plantilla_informe').val();

    // Logo personalizado
    var logo_personalizado = $('#logo_personalizado_plantilla_informe').val();
    var nombre_logo = $('#nombre_logo_plantilla_informe').val();
    if (comprueba_longitud_cadena(nombre_logo, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $('#nombre_logo_plantilla_informe').addClass('data-check-failed');
        return;
    }

    // Tema
    var tema = $('#tema_plantilla_informe').val();

    // Red destino
    var id_red_destino = $('#id_red_destino_plantilla_informe').val();

    // Usuario destino
    var id_usuario_destino = $('#id_usuario_destino_plantilla_informe').val();

    // Parametros de la ventana
    var periodo_tiempo_defecto_anterior = $("#parametros_ventana_anyadir_modificar_plantilla_informe").attr("periodo_tiempo_defecto");
    var logo_personalizado_anterior = $("#parametros_ventana_anyadir_modificar_plantilla_informe").attr("logo_personalizado");

    // Se añade o modifica la plantilla de informe
    if (anyadir_plantilla_informe == true) {
        // Flag de duplicar plantilla de informe
        var id_plantilla_informe_anterior = id_plantilla_informe;
        var duplicar_plantilla_informe = (id_plantilla_informe_anterior != ID_NINGUNO);

        // Se comprueba la imagen del logo
        var duplicar_imagen = false;
        if (logo_personalizado == VALOR_SI) {
            if ($('#fichero_logo_pdf_plantilla_informe_text').val() == "") {
                if ((duplicar_plantilla_informe == true) && (logo_personalizado_anterior != "")) {
                    // Nota: Es un duplicado y ya había imagen: no hace faltar subir un nuevo fichero de imagen,
                    // se duplicará la imagen anterior
                    duplicar_imagen = true;
                }
                else {
                    jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo PDF"));
                    return;
                }
            }
            else {
                var imagen_correcta = comprueba_imagen_correcta(
                    ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF, "fichero_logo_pdf_plantilla_informe_file");
                if (imagen_correcta == false) {
                    $('#fichero_logo_pdf_plantilla_informe_text').addClass('data-check-failed');
                    $('#fichero_logo_pdf_plantilla_informe_text').val("");
                    return;
                }
            }
        }

        // Se añade la plantilla de informe
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/anyade_plantilla_informe.php", {
            nombre: nombre,
            descripcion: descripcion,
            tipo: tipo,
            titulo_informe: titulo_informe,
            periodo_tiempo_defecto: periodo_tiempo_defecto,
            iniciar_comienzo_periodo_tiempo_defecto: iniciar_comienzo_periodo_tiempo_defecto,
            tipo_seleccion_horario_semanal_fechas: tipo_seleccion_horario_semanal_fechas,
            logo_personalizado: logo_personalizado,
            nombre_logo: nombre_logo,
            tema: tema,
            id_plantilla_informe_anterior: id_plantilla_informe,
            id_red_destino: id_red_destino,
            id_usuario_destino: id_usuario_destino
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Identificador de plantilla de informe añadida
            var id_plantilla_informe = resultado.id_plantilla_informe;

            // Se guarda la imagen del logo (o se duplica la anterior si corresponde)
            if (logo_personalizado == VALOR_SI) {
                if (duplicar_imagen == false) {
                    var control_fichero_imagen = $('#fichero_logo_pdf_plantilla_informe_file')[0];
                    var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF, id_plantilla_informe, control_fichero_imagen);
                    if (imagen_guardada_correcta == false) {
                        return;
                    }
                }
                else {
                    var imagen_duplicada_correcta = duplica_imagen_base_datos(ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF, id_plantilla_informe_anterior, id_plantilla_informe);
                    if (imagen_duplicada_correcta == false) {
                        return;
                    }
                }
            }

            // Se muestra el mensaje y se actualiza la tabla de plantillas de informe
            jInfo(resultado.msg);
            actualiza_tabla_plantillas_informes();

            // Se recarga la lista de plantillas de informe del informe de plantilla de informe
            recarga_lista_plantillas_informes_informe_plantilla_informe(false);
        });
    }
    else {
        // Parámetros de la modificación de la plantilla de informe
        var parametros_plantilla_informe = [];
        parametros_plantilla_informe["nombre"] = nombre;
        parametros_plantilla_informe["descripcion"] = descripcion;
        parametros_plantilla_informe["tipo"] = tipo;
        parametros_plantilla_informe["titulo_informe"] = titulo_informe;
        parametros_plantilla_informe["periodo_tiempo_defecto"] = periodo_tiempo_defecto;
        parametros_plantilla_informe["iniciar_comienzo_periodo_tiempo_defecto"] = iniciar_comienzo_periodo_tiempo_defecto;
        parametros_plantilla_informe["tipo_seleccion_horario_semanal_fechas"] = tipo_seleccion_horario_semanal_fechas;
        parametros_plantilla_informe["logo_personalizado"] = logo_personalizado;
        parametros_plantilla_informe["nombre_logo"] = nombre_logo;
        parametros_plantilla_informe["tema"] = tema;
        // Parámetros extra
        parametros_plantilla_informe["periodo_tiempo_defecto_anterior"] = periodo_tiempo_defecto_anterior;
        parametros_plantilla_informe["logo_personalizado_anterior"] = logo_personalizado_anterior;

        // Se muestra un mensaje de aviso antes de modificar la plantilla de informe en los siguientes casos:
        // - Eliminación de logo personalizado
        var mensaje_aviso = "";
        if ((logo_personalizado_anterior == VALOR_SI) && (logo_personalizado == VALOR_NO)) {
            mensaje_aviso += "\n(" + TLNT.Idiomas._("se eliminará el logo personalizado") + ")";
        }
        if (mensaje_aviso == "") {
            modifica_plantilla_informe(id_plantilla_informe, parametros_plantilla_informe);
        }
        else {
            // Se muestra un mensaje de aviso y se confirma la modificación de la plantilla de informe
            mensaje_aviso = TLNT.Idiomas._("¿Está seguro de que desea modificar la plantilla de informe?") + mensaje_aviso;
            jConfirmAcceptCancelAlert(mensaje_aviso, TLNT.Idiomas._("Pregunta"), function(res) {
                if (res == true) {
                    modifica_plantilla_informe(id_plantilla_informe, parametros_plantilla_informe);
                }
            });
        }
    }
}


function modifica_plantilla_informe(id_plantilla_informe, parametros_plantilla_informe) {
    // Se comprueba la imagen del logo
    if ((parametros_plantilla_informe["logo_personalizado_anterior"] == VALOR_NO) &&
        (parametros_plantilla_informe["logo_personalizado"] == VALOR_SI)) {
        if ($('#fichero_logo_pdf_plantilla_informe_text').val() == "") {
            jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de logo PDF"));
            return;
        }
    }
    if (parametros_plantilla_informe["logo_personalizado"] == VALOR_SI) {
        if ($('#fichero_logo_pdf_plantilla_informe_text').val() != "") {
            var imagen_correcta = comprueba_imagen_correcta(
                ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF, "fichero_logo_pdf_plantilla_informe_file");
            if (imagen_correcta == false) {
                $('#fichero_logo_pdf_plantilla_informe_text').addClass('data-check-failed');
                $('#fichero_logo_pdf_plantilla_informe_text').val("");
                return;
            }
        }
    }

    // Se modifica la plantilla de informe
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/modifica_plantilla_informe.php", {
        id_plantilla_informe: id_plantilla_informe,
        nombre: parametros_plantilla_informe["nombre"],
        descripcion: parametros_plantilla_informe["descripcion"],
        tipo: parametros_plantilla_informe["tipo"],
        titulo_informe: parametros_plantilla_informe["titulo_informe"],
        periodo_tiempo_defecto: parametros_plantilla_informe["periodo_tiempo_defecto"],
        iniciar_comienzo_periodo_tiempo_defecto: parametros_plantilla_informe["iniciar_comienzo_periodo_tiempo_defecto"],
        tipo_seleccion_horario_semanal_fechas: parametros_plantilla_informe["tipo_seleccion_horario_semanal_fechas"],
        logo_personalizado: parametros_plantilla_informe["logo_personalizado"],
        nombre_logo: parametros_plantilla_informe["nombre_logo"],
        tema: parametros_plantilla_informe["tema"]
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se guarda la imagen del logo
        if (parametros_plantilla_informe["logo_personalizado"] == VALOR_SI) {
            if ($('#fichero_logo_pdf_plantilla_informe_text').val() != "") {
                var control_fichero_imagen = $('#fichero_logo_pdf_plantilla_informe_file')[0];
                var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF, id_plantilla_informe, control_fichero_imagen);
                if (imagen_guardada_correcta == false) {
                    return;
                }
            }
        }

        // Mensaje de información
        jInfo(resultado.msg);

        // Se actualiza la tabla de plantillas de informes
        actualiza_tabla_plantillas_informes();
        $('#ventana_modal').modal('hide');

        // Se recarga la lista de plantillas de informe del informe de plantilla de informe
        var actualizar_fechas_inicio_fin_plantilla_informe =
            (parametros_plantilla_informe["periodo_tiempo_defecto_anterior"] != parametros_plantilla_informe["periodo_tiempo_defecto"]);
        recarga_lista_plantillas_informes_informe_plantilla_informe(actualizar_fechas_inicio_fin_plantilla_informe);
    });
}


// Actualización de la tabla de plantillas de informes
function boton_personal_actualizar_tabla_plantillas_informes() {
	actualiza_tabla_plantillas_informes();

    // Se recarga la lista de plantillas de informe del informe de plantilla de informe
    recarga_lista_plantillas_informes_informe_plantilla_informe(true);
}


function actualiza_tabla_plantillas_informes() {
	var filtro = $('#filtro_personal_filtro_plantillas_informes_tabla').val();

    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/dame_tabla_plantillas_informes.php", {
		filtro: filtro
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		$('#tablaPlantillasInformes').html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


//
// Informe de plantilla de informe
//


// Recarga la lista de plantillas de informes del informe de plantilla de informe
function recarga_lista_plantillas_informes_informe_plantilla_informe(actualizar_fechas_inicio_fin_plantilla_informe) {
    var id_plantilla_informe = $("#id_plantilla_informe_personal_informe_plantilla_informe").val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/dame_lista_plantillas_informes.php", {
        id_plantilla_informe: id_plantilla_informe,
        opciones_extra: OPCIONES_EXTRA_LISTA_PLANTILLAS_INFORMES_SIN_OPCIONES_EXTRA
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#id_plantilla_informe_personal_informe_plantilla_informe").html(resultado.html);
        $("#id_plantilla_informe_personal_informe_plantilla_informe").trigger("chosen:updated");

        // Se actualizan las fechas de inicio y fin de la plantilla de informe
        if (actualizar_fechas_inicio_fin_plantilla_informe == true) {
            $("#id_plantilla_informe_personal_informe_plantilla_informe").trigger("change");
        }
    });
}


// Recarga los controles de parámetros del informe de plantilla de informe
function recarga_controles_parametros_informe_plantilla_informe(id_plantilla_informe) {
    var id_plantilla_informe_seleccionada = $("#id_plantilla_informe_personal_informe_plantilla_informe").val();
    if (id_plantilla_informe == id_plantilla_informe_seleccionada) {
        // Se actualizan los controles de parámetros del informe
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/dame_controles_parametros_informe_plantilla_informe.php", {
            id_plantilla_informe: id_plantilla_informe
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Si hay parámetros se muestra la cabecera y el contenido con los controles
            if (resultado.html != "") {
                // Se recuperan los parámetros del informe
                var parametros_informe = dame_parametros_informe_personal_informe_plantilla_informe(false, false);
                if (parametros_informe == null) {
                    return;
                }

                // Acciones al recargar los controles de parámetros de la plantilla de informe
                // (se realizan después de recuperar los parámetros del informe 'anteriores')
                realiza_acciones_recarga_controles_parametros_informe_plantilla_informe(resultado.html);

                // Identificadores de parametros actuales
                var cadena_ids_parametros_actuales = $("#parametros_informe_plantilla_informe").attr("ids_parametros");
                var ids_parametros_actuales = cadena_ids_parametros_actuales.split(SEPARADOR_PARAMETROS_SIMPLES);

                // Se restablecen los parámetros que siguen existiendo
                var ids_parametros = parametros_informe["ids_parametros"];
                var valores_parametros = parametros_informe["valores_parametros"];
                for (var i = 0; i < ids_parametros.length; i++) {
                    var id_parametro = ids_parametros[i];
                    if (ids_parametros_actuales.indexOf(id_parametro) > -1) {
                        var valor_parametro = valores_parametros[i];
                        $("#valor_parametro_plantilla_informe_" + id_parametro).val(valor_parametro);
                        $("#valor_parametro_plantilla_informe_" + id_parametro).trigger("chosen:updated");
                    }
                }
            }
            else {
                realiza_acciones_recarga_controles_parametros_informe_plantilla_informe(resultado.html);
            }
        });
    }
}


// Recarga los controles de subtítulos de portadas del informe de plantilla de informe
function recarga_controles_subtitulos_portadas_informe_plantilla_informe(id_plantilla_informe) {
    var id_plantilla_informe_seleccionada = $("#id_plantilla_informe_personal_informe_plantilla_informe").val();
    if (id_plantilla_informe == id_plantilla_informe_seleccionada) {
        // Se actualizan los controles de subtítulos de portadas del informe
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/dame_controles_subtitulos_portadas_informe_plantilla_informe.php", {
            id_plantilla_informe: id_plantilla_informe
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Si hay subtítulos de portadas se muestra la cabecera y el contenido con los controles
            if (resultado.html != "") {
                // Se recuperan los parámetros del informe
                var parametros_informe = dame_parametros_informe_personal_informe_plantilla_informe(false, false);
                if (parametros_informe == null) {
                    return;
                }

                // Acciones al recargar los controles de subtítulos de portadas de la plantilla de informe
                // (se realizan después de recuperar los parámetros del informe 'anteriores')
                realiza_acciones_recarga_controles_subtitulos_portadas_informe_plantilla_informe(resultado.html);

                // Identificador de elementos actuales
                var cadena_ids_elementos_portada_actuales = $("#subtitulos_portadas_informe_plantilla_informe").attr("ids_elementos_portada");
                var ids_elementos_portada_actuales = cadena_ids_elementos_portada_actuales.split(SEPARADOR_PARAMETROS_SIMPLES);

                // Se restablecen los subtítulos de portadas que siguen existiendo
                var ids_elementos_portada = parametros_informe["ids_elementos_portada"];
                var subtitulos_elementos_portada = parametros_informe["subtitulos_elementos_portada"];
                for (var i = 0; i < ids_elementos_portada.length; i++) {
                    var id_elemento_portada = ids_elementos_portada[i];
                    if (ids_elementos_portada_actuales.indexOf(id_elemento_portada) > -1) {
                        var subtitulo_elemento_portada = subtitulos_elementos_portada[i];
                        $("#subtitulo_portada_plantilla_informe_" + id_elemento_portada).val(subtitulo_elemento_portada);
                    }
                }
            }
            else {
                realiza_acciones_recarga_controles_subtitulos_portadas_informe_plantilla_informe(resultado.html);
            }
        });
    }
}


// Recarga los controles de títulos del informe de plantilla de informe
function recarga_controles_titulos_informe_plantilla_informe(id_plantilla_informe) {
    var id_plantilla_informe_seleccionada = $("#id_plantilla_informe_personal_informe_plantilla_informe").val();
    if (id_plantilla_informe == id_plantilla_informe_seleccionada) {
        // Se actualizan los controles de títulos del informe
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/dame_controles_titulos_informe_plantilla_informe.php", {
            id_plantilla_informe: id_plantilla_informe
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Si hay títulos se muestra la cabecera y el contenido con los controles
            if (resultado.html != "") {
                // Se recuperan los parámetros del informe
                var parametros_informe = dame_parametros_informe_personal_informe_plantilla_informe(false, false);
                if (parametros_informe == null) {
                    return;
                }

                // Acciones al recargar los controles de subtítulos de portadas de la plantilla de informe
                // (se realizan después de recuperar los parámetros del informe 'anteriores')
                realiza_acciones_recarga_controles_titulos_informe_plantilla_informe(resultado.html);

                // Identificador de elementos actuales
                var cadena_ids_elementos_titulo_actuales = $("#titulos_informe_plantilla_informe").attr("ids_elementos_titulo");
                var ids_elementos_titulo_actuales = cadena_ids_elementos_titulo_actuales.split(SEPARADOR_PARAMETROS_SIMPLES);

                // Se restablecen los títulos que siguen existiendo
                var ids_elementos_titulo = parametros_informe["ids_elementos_titulo"];
                var titulos_elementos_titulo = parametros_informe["titulos_elementos_titulo"];
                for (var i = 0; i < ids_elementos_titulo.length; i++) {
                    var id_elemento_titulo = ids_elementos_titulo[i];
                    if (ids_elementos_titulo_actuales.indexOf(id_elemento_titulo) > -1) {
                        var titulo_elemento_titulo = titulos_elementos_titulo[i];
                        $("#titulo_plantilla_informe_" + id_elemento_titulo).val(titulo_elemento_titulo);
                    }
                }
            }
            else {
                realiza_acciones_recarga_controles_titulos_informe_plantilla_informe(resultado.html);
            }
        });
    }
}


// Recarga los controles de textos del informe de plantilla de informe
function recarga_controles_textos_informe_plantilla_informe(id_plantilla_informe) {
    var id_plantilla_informe_seleccionada = $("#id_plantilla_informe_personal_informe_plantilla_informe").val();
    if (id_plantilla_informe == id_plantilla_informe_seleccionada) {
        // Se actualizan los controles de textos del informe
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/dame_controles_textos_informe_plantilla_informe.php", {
            id_plantilla_informe: id_plantilla_informe
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Si hay textos se muestra la cabecera y el contenido con los controles
            if (resultado.html != "") {
                // Se recuperan los parámetros del informe
                var parametros_informe = dame_parametros_informe_personal_informe_plantilla_informe(false, false);
                if (parametros_informe == null) {
                    return;
                }

                // Acciones al recargar los controles de texto de la plantilla de informe
                // (se realizan después de recuperar los parámetros del informe 'anteriores')
                realiza_acciones_recarga_controles_textos_informe_plantilla_informe(resultado.html);

                // Identificador de elementos actuales
                var cadena_ids_elementos_texto_actuales = $("#textos_informe_plantilla_informe").attr("ids_elementos_texto");
                var ids_elementos_texto_actuales = cadena_ids_elementos_texto_actuales.split(SEPARADOR_PARAMETROS_SIMPLES);

                // Se restablecen los textos que siguen existiendo
                var ids_elementos_texto = parametros_informe["ids_elementos_texto"];
                var textos_elementos_texto = parametros_informe["textos_elementos_texto"];
                for (var i = 0; i < ids_elementos_texto.length; i++) {
                    var id_elemento_texto = ids_elementos_texto[i];
                    if (ids_elementos_texto_actuales.indexOf(id_elemento_texto) > -1) {
                        var texto_elemento_texto = textos_elementos_texto[i];
                        $("#texto_plantilla_informe_" + id_elemento_texto).val(texto_elemento_texto);
                    }
                }

                // Nota: Se ajustará el tamaño de los textos al cambiar de pestaña (no funciona si los controles no están visibles)
            }
            else {
                realiza_acciones_recarga_controles_textos_informe_plantilla_informe(resultado.html);
            }
        });
    }
}


// Recarga los controles de imágenes del informe de plantilla de informe
function recarga_controles_imagenes_informe_plantilla_informe(id_plantilla_informe) {
    var id_plantilla_informe_seleccionada = $("#id_plantilla_informe_personal_informe_plantilla_informe").val();
    if (id_plantilla_informe == id_plantilla_informe_seleccionada) {
        // Se actualizan los controles de imágenes del informe
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/dame_controles_imagenes_informe_plantilla_informe.php", {
            id_plantilla_informe: id_plantilla_informe
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Si hay imágenes se muestra la cabecera y el contenido con los controles
            if (resultado.html != "") {
                // Se recuperan los parámetros del informe
                var parametros_informe = dame_parametros_informe_personal_informe_plantilla_informe(false, false);
                if (parametros_informe == null) {
                    return;
                }

                // Acciones al recargar los controles de imágenes de la plantilla de informe
                // (se realizan después de recuperar los parámetros del informe 'anteriores')
                realiza_acciones_recarga_controles_imagenes_informe_plantilla_informe(resultado.html);

                // Identificador de elementos actuales
                var cadena_ids_elementos_imagen_actuales = $("#imagenes_informe_plantilla_informe").attr("ids_elementos_imagen");
                var ids_elementos_imagen_actuales = cadena_ids_elementos_imagen_actuales.split(SEPARADOR_PARAMETROS_SIMPLES);

                // No se pueden restablecer los ficheros seleccionados en los controles 'recargados'
                // (se muestra un aviso)
                var mostrar_aviso_ficheros_imagenes_deseleccionados_informe_plantilla_informe = false;

                // Se restablecen las imágenes que siguen existiendo
                var ids_elementos_imagen = parametros_informe["ids_elementos_imagen"];
                var ficheros_imagenes_elementos_imagen_texts = parametros_informe["ficheros_imagenes_elementos_imagen_texts"];
                for (var i = 0; i < ids_elementos_imagen.length; i++) {
                    var id_elemento_imagen = ids_elementos_imagen[i];
                    if (ids_elementos_imagen_actuales.indexOf(id_elemento_imagen) > -1) {
                        var fichero_imagen_elemento_imagen_text = ficheros_imagenes_elementos_imagen_texts[i];
                        if (fichero_imagen_elemento_imagen_text == "") {
                            $('#fichero_imagen_plantilla_informe_text_' + id_elemento_imagen).hide();
                            $('#boton_imagen_plantilla_informe_deseleccionar_fichero_imagen_' + id_elemento_imagen).hide();
                        }
                        else {
                            mostrar_aviso_ficheros_imagenes_deseleccionados_informe_plantilla_informe = true;
                        }
                    }
                }
                if (mostrar_aviso_ficheros_imagenes_deseleccionados_informe_plantilla_informe == true)
                {
                    jAlert(TLNT.Idiomas._("Se han deseleccionado los ficheros de imágenes en el informe de la plantilla de informe"));
                }
            }
            else {
                // Acciones al recargar los controles de imágenes de la plantilla de informe
                realiza_acciones_recarga_controles_imagenes_informe_plantilla_informe(resultado.html);
            }
        });
    }
}


// Muestra el informe de una plantilla de informe
// (Nota: Se separa en 2 funciones porque no funcionan parametros con valores por defecto en WebKit)
function boton_personal_informe_plantilla_informe_ver_informe() {
    muestra_actualiza_informe_plantilla_informe(null, null)
}


// Muestra o actualiza el informe de plantilla de informe
function muestra_actualiza_informe_plantilla_informe(ids_textos_notas, textos_textos_notas) {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_personal_informe_plantilla_informe(false, false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_plantilla_informe = parametros_informe["id_plantilla_informe"];
    var subtitulo_portada = parametros_informe["subtitulo_portada"];
    var ids_parametros = parametros_informe["ids_parametros"];
    var valores_parametros = parametros_informe["valores_parametros"];
    var ids_elementos_portada = parametros_informe["ids_elementos_portada"];
    var subtitulos_elementos_portada = parametros_informe["subtitulos_elementos_portada"];
    var ids_elementos_titulo = parametros_informe["ids_elementos_titulo"];
    var titulos_elementos_titulo = parametros_informe["titulos_elementos_titulo"];
    var ids_elementos_texto = parametros_informe["ids_elementos_texto"];
    var textos_elementos_texto = parametros_informe["textos_elementos_texto"];
    var ids_elementos_imagen = parametros_informe["ids_elementos_imagen"];
    var ficheros_imagenes_elementos_imagen_texts = parametros_informe["ficheros_imagenes_elementos_imagen_texts"];
    var ficheros_imagenes_elementos_imagen_files = parametros_informe["ficheros_imagenes_elementos_imagen_files"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Se cargan las imágenes en el servidor
    var rutas_ficheros_imagenes_elementos_imagen = [];
    for (var i = 0; i < ids_elementos_imagen.length; i++) {
        var id_elemento_imagen = ids_elementos_imagen[i];
        var fichero_imagen_elemento_imagen_text = ficheros_imagenes_elementos_imagen_texts[i];
        var fichero_imagen_elemento_imagen_file = ficheros_imagenes_elementos_imagen_files[i];
        var ruta_fichero_imagen = null;
        if (fichero_imagen_elemento_imagen_text == "") {
            var id_origen = [
                id_plantilla_informe,
                id_elemento_imagen].join(SEPARADOR_PARAMETROS_SIMPLES);
            var res_carga_imagen = carga_imagen_base_datos(ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN, id_origen, null);
            var imagen_cargada_correcta = res_carga_imagen.imagen_cargada_correcta;
            if (imagen_cargada_correcta == false) {
                return;
            }
            ruta_fichero_imagen = res_carga_imagen.ruta_fichero_imagen;
        }
        else {
            ruta_fichero_imagen = guarda_imagen_servidor_fichero_imagen(fichero_imagen_elemento_imagen_file);
            if (ruta_fichero_imagen == null) {
                return;
            }
        }
        rutas_ficheros_imagenes_elementos_imagen.push(ruta_fichero_imagen);
    }

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_plantilla_informe", id_plantilla_informe);
    datos_formulario.append("subtitulo_portada", subtitulo_portada);
    datos_formulario.append("ids_parametros", JSON.stringify(ids_parametros));
    datos_formulario.append("valores_parametros", JSON.stringify(valores_parametros));
    datos_formulario.append("ids_elementos_portada", JSON.stringify(ids_elementos_portada));
    datos_formulario.append("subtitulos_elementos_portada", JSON.stringify(subtitulos_elementos_portada));
    datos_formulario.append("ids_elementos_titulo", JSON.stringify(ids_elementos_titulo));
    datos_formulario.append("titulos_elementos_titulo", JSON.stringify(titulos_elementos_titulo));
    datos_formulario.append("ids_elementos_texto", JSON.stringify(ids_elementos_texto));
    datos_formulario.append("textos_elementos_texto", JSON.stringify(textos_elementos_texto));
    datos_formulario.append("ids_elementos_imagen", JSON.stringify(ids_elementos_imagen));
    datos_formulario.append("rutas_ficheros_imagenes_elementos_imagen", JSON.stringify(rutas_ficheros_imagenes_elementos_imagen));
    datos_formulario.append("fecha_hora_inicio", fecha_hora_inicio);
    datos_formulario.append("fecha_hora_fin", fecha_hora_fin);
    datos_formulario.append("horario_semanal", JSON.stringify(horario_semanal));
    datos_formulario.append("exclusion_fechas", JSON.stringify(exclusion_fechas));
    datos_formulario.append("inclusion_fechas", JSON.stringify(inclusion_fechas));
    datos_formulario.append("minutos_desfase_utc", minutos_desfase_utc);
    datos_formulario.append("tipo_informe", TIPO_INFORME_WEB_EMIOS);

    // Llamada 'ajax' POST (se recuperan los datos para el informe)
    $.ajax({
        url: "./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/dame_datos_informe_plantilla_informe.php",
        type: "POST",
        data: datos_formulario,
        processData: false,
        contentType: false,
        timeout: SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX_GENERACION_INFORME_PLANTILLA_INFORME * 1000,
        success: function(result) {
            var resultado = dame_resultado_ejecucion_script_php_json(result);
            if (resultado == null) {
                return;
            }

            // Comprobación de elementos en la plantilla de informe
            var hay_elementos = resultado.hay_elementos;
            if (hay_elementos == false) {
                jAlert(TLNT.Idiomas._("No hay elementos en la plantilla de informe"));
                return;
            }

            // Comprobación de elementos visibles en la plantilla de informe
            var hay_elementos_visibles = resultado.hay_elementos_visibles;
            if (hay_elementos_visibles == false) {
                jAlert(TLNT.Idiomas._("No hay elementos visibles en el informe"));
                return;
            }

            // Dibujado del informe:
            // - 1. Establecer los controles (id: 'informe-personal-informe-plantilla-informe')
            // - 2. Dibujar cada uno de los elementos utilizando funciones de dibujado específicas
            //   (se utilizará la misma función para dibujar que en los informes en los cuales está cada uno de los elementos y se le pasará el tipo de informe: WebEmios o fichero)

            // Recuperación de datos del resultado
            var info_elementos = resultado.info_elementos;
            var html_elementos = resultado.html_elementos;
            var datos_elementos = resultado.datos_elementos;
            var claves_datos_elementos = resultado.claves_datos_elementos;
            var elementos_informes_elementos = resultado.elementos_informes_elementos;

            // Se establece el html del informe
            $("#informe-personal-informe-plantilla-informe").html(html_elementos);

            // Se muestra el informe
            $("#informe-sin-datos-personal-informe-plantilla-informe").hide();
            $("#informe-personal-informe-plantilla-informe").show();

            // Se recorren cada uno de los elementos y se muestran en los controles correspondientes
            for (var i = 0; i < info_elementos.length; i++) {
                var info_elemento = info_elementos[i];
                var clave_datos_elemento = claves_datos_elementos[i];
                var datos_elemento = datos_elementos[clave_datos_elemento];
                var elementos_informe_elemento = elementos_informes_elementos[i];

                // Fechas de inicio y fin de consulta
                // (la fecha de inicio puede haberse modificado en el elemento por los parámetros de periodo de tiempo)
                var fecha_inicio_elemento = fecha_inicio;
                var hora_inicio_elemento = hora_inicio;
                if (datos_elemento["fecha_inicio"] !== undefined) {
                    fecha_inicio_elemento = datos_elemento["fecha_inicio"];
                }
                if (datos_elemento["hora_inicio"] !== undefined) {
                    hora_inicio_elemento = datos_elemento["hora_inicio"];
                }
                var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio_elemento, hora_inicio_elemento);
                var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

                // Parámetros del elemento
                var parametros_elemento = {
                    fecha_inicio: fecha_inicio_elemento,
                    fecha_fin: fecha_fin,
                    hora_inicio: hora_inicio_elemento,
                    hora_fin: hora_fin,
                    fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                    fecha_hora_fin_consulta: fecha_hora_fin_consulta};

                // Se dibuja el elemento
                dibuja_elemento_plantilla_informe(
                   info_elemento,
                   datos_elemento,
                   elementos_informe_elemento,
                   parametros_elemento,
                   TIPO_INFORME_WEB_EMIOS);
            }

            // Establecimiento de eventos
            TLNT.Navegacion.establece_eventos_contenido_informes();

            // Se restablecen los textos de las notas (si existen)
            if (ids_textos_notas != null) {
                for (var i = 0; i < ids_textos_notas.length; i++) {
                    var id_texto_notas = ids_textos_notas[i];
                    var texto_texto_notas = textos_textos_notas[i];
                    $("#" + id_texto_notas).val(texto_texto_notas);
                    $("#" + id_texto_notas).trigger("input");
                }
            }
        },
        error: function(request, status, err) {
            if (status == "timeout") {
                error_ajax_capturado = true;

                jInfo(TLNT.Idiomas._("El informe tarda demasiado tiempo en generarse") +
                    "\n(" + TLNT.Idiomas._("divida el informe en varios informes más pequeños o utilice informes configurables") + ")");
            }
        }
    });
}


// Dibuja el elemento de una plantilla de informe
function dibuja_elemento_plantilla_informe(
    info_elemento,
    datos_elemento,
    elementos_informe_elemento,
    parametros_elemento,
    tipo_informe) {
    // Establece la información de dibujado del elemento de plantilla de informe
    establece_informacion_dibujado_elemento_plantilla_informe(info_elemento);

    try {
        // Información del elemento
        var numero_elemento = info_elemento["numero_elemento"];
        var tipo = info_elemento["tipo"];

        // Prefijo del elemento (para los controles)
        var prefijo_elemento = "elemento" + numero_elemento + "-";

        // Selección de tipo de elemento
        switch (tipo) {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA: {
                var titulo = datos_elemento["titulo"];
                var subtitulo = datos_elemento["subtitulo"];
                var cadena_fechas = datos_elemento["cadena_fechas"];

                $("#" + prefijo_elemento + "titulo-portada").html(titulo);
                if (subtitulo != "") {
                    $("#" + prefijo_elemento + "subtitulo-portada").html(subtitulo);
                }
                else {
                    oculta_elemento(prefijo_elemento + "subtitulo-portada");
                }
                $("#" + prefijo_elemento + "fechas-portada").html(cadena_fechas);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO: {
                var titulo = datos_elemento["titulo"];
                $("#" + prefijo_elemento + "titulo-titulo").html(titulo);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO: {
                var texto = datos_elemento["texto"];
                switch (tipo_informe) {
                    case TIPO_INFORME_WEB_EMIOS: {
                        $("#" + prefijo_elemento + "texto-texto").val(texto);
                        TLNT.Navegacion.redimensiona_textarea("#" + prefijo_elemento + "texto-texto");
                        break;
                    }
                    case TIPO_INFORME_FICHERO: {
                        texto = formatea_texto_informe_html(texto);
                        $("#" + prefijo_elemento + "texto-texto").html(texto);
                        break;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS: {
                var texto = datos_elemento["texto"];
                switch (tipo_informe) {
                    case TIPO_INFORME_WEB_EMIOS: {
                        $("#" + prefijo_elemento + "texto-notas").val(texto);
                        TLNT.Navegacion.redimensiona_textarea("#" + prefijo_elemento + "texto-notas");
                        break;
                    }
                    case TIPO_INFORME_FICHERO: {
                        if (texto != "") {
                            texto = formatea_texto_informe_html(texto);
                            $("#" + prefijo_elemento + "texto-notas").html(texto);
                        }
                        else {
                            oculta_elemento(prefijo_elemento + "texto-notas");
                        }
                        break;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                var ruta_fichero_imagen = datos_elemento["ruta_fichero_imagen"];
                $("#" + prefijo_elemento + "imagen").attr("src", ruta_fichero_imagen);
                break;
            }
            // Elementos de varios módulos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS: {
                dibuja_elemento_plantilla_informe_comentarios(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS: {
                dibuja_elemento_plantilla_informe_sensores_activaciones_eventos(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION: {
                dibuja_elemento_plantilla_informe_sensores_informacion(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO: {
                dibuja_elemento_plantilla_informe_sensores_analisis_horario(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO: {
                dibuja_elemento_plantilla_informe_sensores_analisis_diario(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO: {
                dibuja_elemento_plantilla_informe_sensores_analisis_comportamiento(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS: {
                dibuja_elemento_plantilla_informe_sensores_comparacion_periodos(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO: {
                dibuja_elemento_plantilla_informe_sensores_comparacion_perfil_horario(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES: {
                dibuja_elemento_plantilla_informe_sensores_comparacion_campos_iguales(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES: {
                dibuja_elemento_plantilla_informe_sensores_comparacion_campos_diferentes(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO: {
                dibuja_elemento_plantilla_informe_sensores_analisis_comparativo(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES: {
                dibuja_elemento_plantilla_informe_sensores_valores_generales(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES: {
                dibuja_elemento_plantilla_informe_sensores_incrementos_totales(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA: {
                dibuja_elemento_plantilla_informe_sensores_histograma(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION: {
                dibuja_elemento_plantilla_informe_sensores_correlacion(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS: {
                dibuja_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
                dibuja_elemento_plantilla_informe_smartmeter_consumos_costes_generales(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES: {
                dibuja_elemento_plantilla_informe_smartmeter_consumos_costes_totales(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS: {
                dibuja_elemento_plantilla_informe_smartmeter_comparacion_periodos(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS: {
                dibuja_elemento_plantilla_informe_smartmeter_simulador_tarifas(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD: {
                dibuja_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD: {
                dibuja_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD: {
                dibuja_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD: {
                dibuja_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS: {
                dibuja_elemento_plantilla_informe_smartmeter_excesos_caudal_gas(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de SmartMeter (Compra de energía)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA: {
                dibuja_elemento_plantilla_informe_smartmeter_desvios_compra_energia(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA: {
                dibuja_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de SmartMeter (Facturas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA: {
                dibuja_elemento_plantilla_informe_smartmeter_simulador_factura(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de SmartMeter (Tarifas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION: {
                dibuja_elemento_plantilla_informe_smartmeter_instalacion(
                    info_elemento,
                    datos_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de proyectos (Líneas base)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE: {
                dibuja_elemento_plantilla_informe_proyectos_simulador_linea_base(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
            // Elementos de proyectos (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO: {
                dibuja_elemento_plantilla_informe_proyectos_informacion_proyecto(
                    info_elemento,
                    datos_elemento,
                    elementos_informe_elemento,
                    parametros_elemento,
                    tipo_informe);
                break;
            }
        }
    }
    finally {
        // Borra la información de dibujado del elemento de plantilla de informe
        borra_informacion_dibujado_elemento_plantilla_informe();
    }
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de plantilla de informe
function dame_parametros_informe_personal_informe_plantilla_informe(informe_fichero, informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Se comprueba si hay plantilla de informe seleccionada
    var id_plantilla_informe = $('#id_plantilla_informe_personal_informe_plantilla_informe').val();
    if (id_plantilla_informe == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay plantilla de informe seleccionada"));
        return (null);
	}

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("personal_informe_plantilla_informe", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_personal_informe_plantilla_informe");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_personal_informe_plantilla_informe");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_plantilla_informe"] = id_plantilla_informe;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan los identificadores y los valores de los parámetros (si la plantilla es configurable)
    if ($('#parametros_informe_plantilla_informe').length > 0) {
        var cadena_ids_parametros = $("#parametros_informe_plantilla_informe").attr("ids_parametros");
        if (cadena_ids_parametros == "") {
            parametros_informe["ids_parametros"] = [];
            parametros_informe["valores_parametros"] = [];
        }
        else {
            var ids_parametros = cadena_ids_parametros.split(SEPARADOR_PARAMETROS_SIMPLES);
            var valores_parametros = [];
            for (var i = 0; i < ids_parametros.length; i++) {
                var id_parametro = ids_parametros[i];
                var valor_parametro = $("#valor_parametro_plantilla_informe_" + id_parametro).val();
                valores_parametros.push(valor_parametro);
            }
            parametros_informe["ids_parametros"] = ids_parametros;
            parametros_informe["valores_parametros"] = valores_parametros;
        }
    }
    else {
        parametros_informe["subtitulo_portada"] = "";
        parametros_informe["ids_parametros"] = [];
        parametros_informe["valores_parametros"] = [];
    }

    // Se recuperan los identificadores y los textos de los elementos de tipo portada
    if ($('#subtitulos_portadas_informe_plantilla_informe').length > 0) {
        var cadena_ids_elementos_portada = $("#subtitulos_portadas_informe_plantilla_informe").attr("ids_elementos_portada");
        var ids_elementos_portada = cadena_ids_elementos_portada.split(SEPARADOR_PARAMETROS_SIMPLES);
        var subtitulos_elementos_portada = [];
        for (var i = 0; i < ids_elementos_portada.length; i++) {
            var id_elemento_portada = ids_elementos_portada[i];
            var subtitulo_elemento_portada = $("#subtitulo_portada_plantilla_informe_" + id_elemento_portada).val();
            if (comprueba_longitud_cadena(subtitulo_elemento_portada, NUMERO_MAXIMO_CARACTERES_TITULO) == false) {
                $("#subtitulo_portada_plantilla_informe_" + id_elemento_portada).addClass('data-check-failed');
                return (null);
            }
            subtitulos_elementos_portada.push(subtitulo_elemento_portada);
        }
        parametros_informe["ids_elementos_portada"] = ids_elementos_portada;
        parametros_informe["subtitulos_elementos_portada"] = subtitulos_elementos_portada;
    }
    else {
        parametros_informe["ids_elementos_portada"] = [];
        parametros_informe["subtitulos_elementos_portada"] = [];
    }

    // Se recuperan los identificadores y los títulos de los elementos de tipo título
    if ($('#titulos_informe_plantilla_informe').length > 0) {
        var cadena_ids_elementos_titulo = $("#titulos_informe_plantilla_informe").attr("ids_elementos_titulo");
        var ids_elementos_titulo = cadena_ids_elementos_titulo.split(SEPARADOR_PARAMETROS_SIMPLES);
        var titulos_elementos_titulo = [];
        for (var i = 0; i < ids_elementos_titulo.length; i++) {
            var id_elemento_titulo = ids_elementos_titulo[i];
            var titulo_elemento_titulo = $("#titulo_plantilla_informe_" + id_elemento_titulo).val();
            if (comprueba_longitud_cadena(titulo_elemento_titulo, NUMERO_MAXIMO_CARACTERES_TITULO) == false) {
                $("#titulo_plantilla_informe_" + id_elemento_titulo).addClass('data-check-failed');
                return (null);
            }
            if (titulo_elemento_titulo == "") {
                jAlert(TLNT.Idiomas._("Rellene los títulos"));
                return (null);
            }
            titulos_elementos_titulo.push(titulo_elemento_titulo);
        }
        parametros_informe["ids_elementos_titulo"] = ids_elementos_titulo;
        parametros_informe["titulos_elementos_titulo"] = titulos_elementos_titulo;
    }
    else {
        parametros_informe["ids_elementos_titulo"] = [];
        parametros_informe["titulos_elementos_titulo"] = [];
    }

    // Se recuperan los identificadores y los textos de los elementos de tipo texto
    if ($('#textos_informe_plantilla_informe').length > 0) {
        var cadena_ids_elementos_texto = $("#textos_informe_plantilla_informe").attr("ids_elementos_texto");
        var ids_elementos_texto = cadena_ids_elementos_texto.split(SEPARADOR_PARAMETROS_SIMPLES);
        var textos_elementos_texto = [];
        for (var i = 0; i < ids_elementos_texto.length; i++) {
            var id_elemento_texto = ids_elementos_texto[i];
            var texto_elemento_texto = $("#texto_plantilla_informe_" + id_elemento_texto).val();
            if (comprueba_longitud_cadena(texto_elemento_texto, NUMERO_MAXIMO_CARACTERES_TEXTO) == false) {
                $("#texto_plantilla_informe_" + id_elemento_texto).addClass('data-check-failed');
                return (null);
            }
            if (texto_elemento_texto == "") {
                jAlert(TLNT.Idiomas._("Rellene los textos"));
                return (null);
            }
            textos_elementos_texto.push(texto_elemento_texto);
        }
        parametros_informe["ids_elementos_texto"] = ids_elementos_texto;
        parametros_informe["textos_elementos_texto"] = textos_elementos_texto;
    }
    else {
        parametros_informe["ids_elementos_texto"] = [];
        parametros_informe["textos_elementos_texto"] = [];
    }

    // Se recuperan los identificadores y las imágenes de los elementos de tipo imagen
    if ($('#imagenes_informe_plantilla_informe').length > 0) {
        var cadena_ids_elementos_imagen = $("#imagenes_informe_plantilla_informe").attr("ids_elementos_imagen");
        var ids_elementos_imagen = cadena_ids_elementos_imagen.split(SEPARADOR_PARAMETROS_SIMPLES);
        var ficheros_imagenes_elementos_imagen_texts = [];
        var ficheros_imagenes_elementos_imagen_files = [];
        for (var i = 0; i < ids_elementos_imagen.length; i++) {
            var id_elemento_imagen = ids_elementos_imagen[i];
            var fichero_imagen_elementos_imagen_text = $("#fichero_imagen_plantilla_informe_text_" + id_elemento_imagen).val();
            var fichero_imagen_elementos_imagen_file = null;
            if (fichero_imagen_elementos_imagen_text != "") {
                fichero_imagen_elementos_imagen_file = $("#fichero_imagen_plantilla_informe_file_" + id_elemento_imagen)[0];
            }
            ficheros_imagenes_elementos_imagen_texts.push(fichero_imagen_elementos_imagen_text);
            ficheros_imagenes_elementos_imagen_files.push(fichero_imagen_elementos_imagen_file);

            // Se comprueba la imagen personalizada
            if (fichero_imagen_elementos_imagen_text != "") {
                var imagen_correcta = comprueba_imagen_correcta(
                    ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN, "fichero_imagen_plantilla_informe_file_" + id_elemento_imagen);
                if (imagen_correcta == false) {
                    $('#boton_imagen_plantilla_informe_deseleccionar_fichero_imagen_' + id_elemento_imagen).click();
                    $("#nombre_imagen_plantilla_informe_" + id_elemento_imagen).addClass('data-check-failed');
                    return;
                }
            }
        }
        parametros_informe["ids_elementos_imagen"] = ids_elementos_imagen;
        parametros_informe["ficheros_imagenes_elementos_imagen_texts"] = ficheros_imagenes_elementos_imagen_texts;
        parametros_informe["ficheros_imagenes_elementos_imagen_files"] = ficheros_imagenes_elementos_imagen_files;
    }
    else {
        parametros_informe["ids_elementos_imagen"] = [];
        parametros_informe["ficheros_imagenes_elementos_imagen_texts"] = [];
        parametros_informe["ficheros_imagenes_elementos_imagen_files"] = [];
    }

    // Se recuperan las fechas
    var fecha_inicio = $('#fecha_inicio_personal_informe_plantilla_informe').val();
    var hora_inicio = $('#hora_inicio_personal_informe_plantilla_informe').val();
    var fecha_fin = $('#fecha_fin_personal_informe_plantilla_informe').val();
    var hora_fin = $('#hora_fin_personal_informe_plantilla_informe').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return (null);
    }
    hora_inicio += ":00";
    hora_fin += ":59";
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
    var fecha_hora_fin = fecha_fin + ", " + hora_fin;

    parametros_informe["fecha_inicio"] = fecha_inicio;
    parametros_informe["fecha_fin"] = fecha_fin;
    parametros_informe["hora_inicio"] = hora_inicio;
    parametros_informe["hora_fin"] = hora_fin;
    parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
    parametros_informe["fecha_hora_fin"] = fecha_hora_fin;

    // Parámetros para el pie de página
    var mostrar_numeros_pagina = $('#mostrar_numeros_pagina_personal_informe_plantilla_informe').val();
    var numero_pagina_inicial = $('#numero_numero_pagina_inicial_personal_informe_plantilla_informe').val();
    var mostrar_numero_paginas_totales = $('#mostrar_numero_paginas_totales_personal_informe_plantilla_informe').val();
    var numero_paginas_totales_automatico = $('#numero_paginas_totales_automatico_personal_informe_plantilla_informe').val();
    var numero_paginas_totales_manual = $('#numero_numero_paginas_totales_manual_personal_informe_plantilla_informe').val();
    var texto_titulo_izquierda_pie_pagina = $('#cadena_texto_titulo_izquierda_pie_pagina_personal_informe_plantilla_informe').val();
    var texto_titulo_derecha_pie_pagina = $('#cadena_texto_titulo_derecha_pie_pagina_personal_informe_plantilla_informe').val();
    parametros_informe["mostrar_numeros_pagina"] = mostrar_numeros_pagina;
    parametros_informe["numero_pagina_inicial"] = numero_pagina_inicial;
    parametros_informe["mostrar_numero_paginas_totales"] = mostrar_numero_paginas_totales;
    parametros_informe["numero_paginas_totales_automatico"] = numero_paginas_totales_automatico;
    parametros_informe["numero_paginas_totales_manual"] = numero_paginas_totales_manual;
    parametros_informe["texto_titulo_izquierda_pie_pagina"] = texto_titulo_izquierda_pie_pagina;
    parametros_informe["texto_titulo_derecha_pie_pagina"] = texto_titulo_derecha_pie_pagina;

    // Conversión de parámetros de tipo cadena a json (si es informe fichero o informe automático)
    if ((informe_fichero == true) || (informe_automatico == true)) {
        // Cadenas de la plantilla de informe
        var cadenas_plantilla_informe = {};

        // Se añaden los subtítulos de elementos de tipo portada
        for (var i = 0; i < parametros_informe["ids_elementos_portada"].length; i++) {
            var id_elemento_portada = parametros_informe["ids_elementos_portada"][i];
            cadenas_plantilla_informe["subtitulo_elemento_portada_" + id_elemento_portada] = parametros_informe["subtitulos_elementos_portada"][i];
        }
        delete parametros_informe["subtitulos_elementos_portada"];

        // Se añaden los títulos de elementos de tipo título
        for (var i = 0; i < parametros_informe["ids_elementos_titulo"].length; i++) {
            var id_elemento_titulo = parametros_informe["ids_elementos_titulo"][i];
            cadenas_plantilla_informe["titulo_elemento_titulo_" + id_elemento_titulo] = parametros_informe["titulos_elementos_titulo"][i];
        }
        delete parametros_informe["titulos_elementos_titulo"];

        // Se añaden los textos de elementos de tipo texto
        for (var i = 0; i < parametros_informe["ids_elementos_texto"].length; i++) {
            var id_elemento_texto = parametros_informe["ids_elementos_texto"][i];
            cadenas_plantilla_informe["texto_elemento_texto_" + id_elemento_texto] = parametros_informe["textos_elementos_texto"][i];
        }
        delete parametros_informe["textos_elementos_texto"];

        // Se recuperan los identificadores y los textos de los elementos de tipo notas
        // (sólo si es informe fichero y no es informe automático)
        var salir_funcion = false;
        if ((informe_fichero == true) && (informe_automatico == false)) {
            var ids_elementos_notas = [];
            $('.texto-elemento-notas-plantilla-informe').each(function() {
                var id_elemento_notas = $(this).attr("id_elemento_plantilla_informe");
                var texto_elemento_notas = $(this).val();
                if (comprueba_longitud_cadena(texto_elemento_notas, NUMERO_MAXIMO_CARACTERES_NOTAS) == false) {
                    $(this).addClass('data-check-failed');
                    salir_funcion = true;
                }
                if (texto_elemento_notas != "") {
                    ids_elementos_notas.push(id_elemento_notas);
                    cadenas_plantilla_informe["texto_elemento_notas_" + id_elemento_notas] = texto_elemento_notas;
                }
            });
            if (salir_funcion == true) {
                return (null);
            }
            parametros_informe["ids_elementos_notas"] = ids_elementos_notas;
        }

        // Se cargan las imágenes
        if ($('#imagenes_informe_plantilla_informe').length > 0) {
            if ((informe_fichero == true) && (informe_automatico == false)) {
                for (var i = 0; i < ids_elementos_imagen.length; i++) {
                    var id_elemento_imagen = ids_elementos_imagen[i];
                    var fichero_imagen_elemento_imagen_file = ficheros_imagenes_elementos_imagen_files[i];
                    var ruta_fichero_imagen = null;
                    if (fichero_imagen_elemento_imagen_file == null) {
                        var id_origen = [
                            id_plantilla_informe,
                            id_elemento_imagen].join(SEPARADOR_PARAMETROS_SIMPLES);
                        var res_carga_imagen = carga_imagen_base_datos(ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN, id_origen, null);
                        var imagen_cargada_correcta = res_carga_imagen.imagen_cargada_correcta;
                        if (imagen_cargada_correcta == false) {
                            return;
                        }
                        ruta_fichero_imagen = res_carga_imagen.ruta_fichero_imagen;
                    }
                    else {
                        ruta_fichero_imagen = guarda_imagen_servidor_fichero_imagen(fichero_imagen_elemento_imagen_file);
                        if (ruta_fichero_imagen == null) {
                            salir_funcion = true;
                        }
                    }
                    cadenas_plantilla_informe["ruta_fichero_imagen_elemento_imagen_" + id_elemento_imagen] = ruta_fichero_imagen;
                }
                if (salir_funcion == true) {
                    return (null);
                }

                // Nota: Se elimina este parámetro porque ocurren errores al generar el informe fichero
                // (al pasarlo como parámetro a PHP, se 'disparan' eventos relacionados con cambio del control de fichero ('..._file') y no funciona)
                parametros_informe["ficheros_imagenes_elementos_imagen_files"] = [];
            }
        }

        // Se guardan los parámetros de tipo json
        var parametros_tipo_json = JSON.stringify(cadenas_plantilla_informe);
        parametros_informe["parametros_tipo_json"] = parametros_tipo_json;
    };

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}

