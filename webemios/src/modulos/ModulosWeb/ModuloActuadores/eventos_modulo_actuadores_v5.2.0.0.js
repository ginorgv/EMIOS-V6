/*
 * Módulo Actuadores
 *
 */


// Ventana de envio de acción (herramientas)
function boton_actuadores_mostrar_ventana_envio_accion() {
    var destino_accion = null;
    var href_pestanya_activa = $('#pestanyas-principal-actuadores .active > a').attr('href');
    var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
    switch (id_pestanya_activa) {
        case "actuadores": {
            destino_accion = DESTINO_ACCION_ACTUADOR;
            break;
        }
        case "grupos-actuadores": {
            destino_accion = DESTINO_ACCION_GRUPO_ACTUADORES;
            break;
        }
    }

    boton_actuadores_mostrar_ventana_envio_accion_general(
        destino_accion,
        ID_NINGUNO,
        ORIGEN_ENVIO_ACCION_HERRAMIENTAS_ACTUADORES,
        null);
}


// Ventana de envio de acción (con origen de envío de acción)
function boton_actuadores_mostrar_ventana_envio_accion_actuador() {
    var id_actuador = $(this).attr('id_actuador');
    var origen_envio_accion = $(this).attr('origen_envio_accion');

    boton_actuadores_mostrar_ventana_envio_accion_general(
        DESTINO_ACCION_ACTUADOR,
        id_actuador,
        origen_envio_accion,
        null);
}


// Ventana de envio de acción (mapa)
function boton_actuadores_mostrar_ventana_envio_accion_actuador_mapa() {
    var params = this.id.split('__');
	var id_actuador = params[1];
    var id_mapa = params[2];

    boton_actuadores_mostrar_ventana_envio_accion_general(
        DESTINO_ACCION_ACTUADOR,
        id_actuador,
        ORIGEN_ENVIO_ACCION_MAPA,
        id_mapa);
}


// Ventana de envio de acción (widget de información de actuador)
function boton_actuadores_mostrar_ventana_envio_accion_actuador_widget() {
    var params = this.id.split('__');
	var id_actuador = params[1];
    var id_pestanya_widgets = params[2];

    boton_actuadores_mostrar_ventana_envio_accion_general(
        DESTINO_ACCION_ACTUADOR,
        id_actuador,
        ORIGEN_ENVIO_ACCION_WIDGET,
        id_pestanya_widgets);
}


// Ventana de envio de acción (con origen de envío de acción)
function boton_actuadores_mostrar_ventana_envio_accion_grupo_actuadores() {
    var id_grupo_actuadores = $(this).attr('id_grupo_actuadores');
    var origen_envio_accion = $(this).attr('origen_envio_accion');

    boton_actuadores_mostrar_ventana_envio_accion_general(
        DESTINO_ACCION_GRUPO_ACTUADORES,
        id_grupo_actuadores,
        origen_envio_accion,
        null);
}


// Ventana de envio de acción (widget de información de grupo de actuadores)
function boton_actuadores_mostrar_ventana_envio_accion_grupo_actuadores_widget() {
    var params = this.id.split('__');
	var id_grupo_actuadores = params[1];
    var id_widget = params[2];

    boton_actuadores_mostrar_ventana_envio_accion_general(
        DESTINO_ACCION_GRUPO_ACTUADORES,
        id_grupo_actuadores,
        ORIGEN_ENVIO_ACCION_WIDGET,
        id_widget);
}


// Ventana de envio de acción general
function boton_actuadores_mostrar_ventana_envio_accion_general(destino, id_destino, origen_envio_accion, id_origen_envio_accion) {
    $.post("./src/modulos/ModulosWeb/ModuloActuadores/muestra_ventana_envio_accion.php", {
        destino: destino,
        id_destino: id_destino,
        origen_envio_accion: origen_envio_accion,
        id_origen_envio_accion: id_origen_envio_accion
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
        // (se establecen directamente los eventos de las ventanas modales de actuadores,
        //  porque si no desde el widget, sólo establece los eventos de las ventanas del módulo Personal, no de Actuadores)
        TLNT.Navegacion.establece_eventos_ventanas_modales_actuadores();
        TLNT.Navegacion.establece_eventos_ventanas_modales_modulos();
	});
}


// Envío de acción
function boton_actuadores_enviar_accion() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Origen del envío de la acción
    var origen_envio_accion = $("#parametros_ventana_envio_accion").attr("origen_envio_accion");
    var id_origen_envio_accion = $("#parametros_ventana_envio_accion").attr("id_origen_envio_accion");

    // Se comprueba si hay clase seleccionada
    var clase_actuador = $("#clase_actuador_envio_accion").val();
    if (clase_actuador == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return;
    }

    // Se comprueba si hay destino seleccionado
    var destino_accion = $("#destino_accion_envio_accion").val();
    var id_destino_accion = $('#id_destino_accion_envio_accion').val();
    if (id_destino_accion == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay destino seleccionado"));
        return;
	}

    // Se recuperan los valores de los controles de la acción
    var valores_controles_accion = dame_valores_controles_accion(clase_actuador);
    if (valores_controles_accion == null) {
        return;
    }
    var id_accion_predefinida = valores_controles_accion["id_accion_predefinida"];
    var contenido_accion = valores_controles_accion["contenido_accion"];
    var valor_accion = valores_controles_accion["valor_accion"];

    // Se envía la acción
    switch (destino_accion) {
        case DESTINO_ACCION_ACTUADOR: {
            $.post("./src/modulos/ModulosWeb/ModuloActuadores/envia_accion_actuador.php", {
                id_actuador: id_destino_accion,
                id_accion_predefinida: id_accion_predefinida,
                contenido_accion: contenido_accion,
                valor_accion: valor_accion,
                origen_accion: ORIGEN_ACCION_MANUAL
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg, TLNT.Idiomas._("Información"), function(res) {
                    switch (origen_envio_accion) {
                        case ORIGEN_ENVIO_ACCION_HERRAMIENTAS_ACTUADORES:
                        case ORIGEN_ENVIO_ACCION_DETALLES_TABLA_ACTUADORES: {
                            refresca_tabla_nodo(TIPO_NODO_ACTUADOR, id_destino_accion);
                            break;
                        }
                        case ORIGEN_ENVIO_ACCION_MAPA: {
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
                        case ORIGEN_ENVIO_ACCION_WIDGET: {
                            actualiza_cuadricula_widgets(id_origen_envio_accion, false);
                            break;
                        }
                    }
                });
                $('#ventana_modal').modal('hide');
            });
            break;
        }
        case DESTINO_ACCION_GRUPO_ACTUADORES: {
            $.post("./src/modulos/ModulosWeb/ModuloActuadores/envia_accion_grupo_actuadores.php", {
                id_grupo_actuadores: id_destino_accion,
                id_accion_predefinida: id_accion_predefinida,
                contenido_accion: contenido_accion,
                valor_accion: valor_accion,
                origen_accion: ORIGEN_ACCION_MANUAL
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg, TLNT.Idiomas._("Información"), function(res) {
                    switch (origen_envio_accion) {
                        case ORIGEN_ENVIO_ACCION_HERRAMIENTAS_ACTUADORES: {
                            actualiza_tablas_nodos(TIPO_NODO_GRUPO_ACTUADORES);
                            break;
                        }
                        case ORIGEN_ENVIO_ACCION_DETALLES_TABLA_GRUPOS_ACTUADORES: {
                            refresca_tabla_nodo(TIPO_NODO_GRUPO_ACTUADORES, id_destino_accion);
                            actualiza_tabla_nodos(TIPO_NODO_ACTUADOR);
                            break;
                        }
                        case ORIGEN_ENVIO_ACCION_WIDGET: {
                            actualiza_cuadricula_widgets(id_origen_envio_accion, false);
                            break;
                        }
                    }
                });
                $('#ventana_modal').modal('hide');
            });
            break;
        }
    }
}


// Ventana de borrado de acciones enviadas (herramientas)
function boton_actuadores_mostrar_ventana_borrado_acciones_enviadas() {
    var destino_accion = null;
    var href_pestanya_activa = $('#pestanyas-principal-actuadores .active > a').attr('href');
    var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
    switch (id_pestanya_activa) {
        case "actuadores": {
            destino_accion = DESTINO_ACCION_ACTUADOR;
            break;
        }
        case "grupos-actuadores": {
            destino_accion = DESTINO_ACCION_GRUPO_ACTUADORES;
            break;
        }
    }

    boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_destino(
        CLASE_NINGUNA,
        destino_accion,
        ID_NINGUNO);
}


// Ventana de borrado de acciones enviadas (tabla de actuadores)
function boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_actuador() {
    var params = this.id.split('__');
	var id_actuador = params[1];
    var clase_actuador = params[2];

    boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_destino(
        clase_actuador,
        DESTINO_ACCION_ACTUADOR,
        id_actuador);
}


// Ventana de borrado de acciones enviadas (tabla de grupos de actuadores)
function boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_grupo_actuadores() {
    var params = this.id.split('__');
	var id_grupo_actuadores = params[1];
    var clase_actuador = params[2];

    boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_destino(
        clase_actuador,
        DESTINO_ACCION_GRUPO_ACTUADORES,
        id_grupo_actuadores);
}


// Ventana de borrado de acciones enviadas a un destino especificado
function boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_destino(clase_actuador, destino, id_destino) {
    $.post("./src/modulos/ModulosWeb/ModuloActuadores/muestra_ventana_borrado_acciones_enviadas.php", {
        clase_actuador: clase_actuador,
        destino: destino,
        id_destino: id_destino
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


// Borrado de acciones enviadas
function boton_actuadores_borrar_acciones_enviadas() {
    // Se comprueba si hay destino seleccionado
    var id_destino_accion = $('#id_destino_accion_borrado_acciones_enviadas').val();
    if (id_destino_accion == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay destino seleccionado"));
        return;
	}

    // Se recupera el nombre del destino seleccionado
    var nombre_destino_accion = $('#id_destino_accion_borrado_acciones_enviadas :selected').text();

    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_borrado_acciones_enviadas').val();
    var hora_inicio = $('#hora_inicio_borrado_acciones_enviadas').val();
    var fecha_fin = $('#fecha_fin_borrado_acciones_enviadas').val();
    var hora_fin = $('#hora_fin_borrado_acciones_enviadas').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    // Se borran las acciones enviadas
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("Las acciones enviadas al destino en el rango de fechas se borrarán. ¿Está seguro?"), TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            // Se crean los datos del formulario
            var datos_formulario = new FormData();
            datos_formulario.append("clase_actuador", $("#clase_actuador_borrado_acciones_enviadas").val());
            datos_formulario.append("destino_accion", $("#destino_accion_borrado_acciones_enviadas").val());
            datos_formulario.append("id_destino_accion", id_destino_accion);
            datos_formulario.append("nombre_destino_accion", nombre_destino_accion);
            datos_formulario.append("fecha_hora_inicio", fecha_hora_inicio);
            datos_formulario.append("fecha_hora_fin", fecha_hora_fin);

            // Llamada 'ajax' POST
            $.ajax({
                url: "./src/modulos/ModulosWeb/ModuloActuadores/borra_acciones_enviadas.php",
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
                },
                error: function(request, status, err) {
                    if (status == "timeout") {
                        error_ajax_capturado = true;

                        jInfo(TLNT.Idiomas._("El borrado de acciones enviadas se está realizado en segundo plano"));
                    }
                }
            });
        }
    });
}


// Muestra la tabla de actuadores aplicando el filtro
function boton_actuadores_filtro_actuadores_tabla() {
    actualiza_tabla_nodos(TIPO_NODO_ACTUADOR);
}


// Muestra la tabla de grupos aplicando el filtro
function boton_actuadores_filtro_grupos_tabla() {
    actualiza_tabla_nodos(TIPO_NODO_GRUPO_ACTUADORES);
}


// Muestra la ventana de asignación de localización
function boton_actuadores_mostrar_ventana_asignacion_localizacion() {
    boton_mostrar_ventana_asignacion_localizacion_nodos(TIPO_NODO_ACTUADOR);
}


// Muestra la ventana de asignación de grupo
function boton_actuadores_mostrar_ventana_asignacion_grupo() {
    boton_mostrar_ventana_asignacion_grupo_nodos(TIPO_NODO_ACTUADOR);
}


// Muestra el mapa de actuadores aplicando el filtro
function boton_actuadores_filtro_actuadores_mapa() {
    boton_actualizar_mapa();
}


