//
// Funciones de comentarios
//


// Botón de mostrar la ventana de añadir o modificar un comentario
function boton_mostrar_ventana_anyadir_modificar_comentario(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    var modulo = $('#modulo').attr("name");
    var params = this.id.split('__');
    var id_comentario = params[1];
    var tipo_comentario = $(this).attr('tipo_comentario');
    var visibilidad_comentario = $(this).attr('visibilidad_comentario');
    var origen_comentario = $(this).attr('origen_comentario');
    var parametros_origen_comentario = $(this).attr('parametros_origen_comentario');
    var objeto = $(this).attr('objeto');

    muestra_ventana_anyadir_modificar_comentario(
        modulo,
        id_comentario,
        tipo_comentario,
        visibilidad_comentario,
        origen_comentario,
        parametros_origen_comentario,
        null,
        objeto);
}


// Muestra la ventana de añadir o modificar un comentario
function muestra_ventana_anyadir_modificar_comentario(
    modulo,
    id_comentario,
    tipo_comentario,
    visibilidad_comentario,
    origen_comentario,
    parametros_origen_comentario,
    fecha_hora,
    objeto) {
    $.post("./src/lib/modulos/Comentarios/muestra_ventana_anyadir_modificar_comentario.php", {
        modulo: modulo,
        id_comentario: id_comentario,
        tipo_comentario: tipo_comentario,
        visibilidad_comentario: visibilidad_comentario,
        origen_comentario: origen_comentario,
        parametros_origen_comentario: parametros_origen_comentario,
        fecha_hora: fecha_hora,
        objeto: objeto
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

        // Se establece el foco en la descripción del comentario
        // (https://stackoverflow.com/questions/15247849/how-to-set-focus-to-first-text-input-in-a-bootstrap-modal-after-shown/15252413)
        try {
            $('#ventana_modal').on('shown.bs.modal', function () {
                setTimeout(function() {
                    var elemento = $('#descripcion_comentario');
                    elemento.focus();
                    var longitud_texto = elemento.val().length;
                    if (longitud_texto > 0) {
                        elemento[0].setSelectionRange(longitud_texto, longitud_texto);
                    }
                    $('#ventana_modal').off('shown.bs.modal');
                }, 0);
            });
        } catch (err) {}
    });
}


// Botón de eliminar un comentario
function boton_eliminar_comentario(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    var modulo = $('#modulo').attr("name");
    var params = this.id.split('__');
    var id_comentario = params[1];

    // Parámetros del comentario
    var objeto = $(this).attr('objeto');
    var fecha_hora = $(this).attr('fecha_hora');

    // Origen del comentario
    var origen_comentario = $(this).attr('origen_comentario');
    var cadena_parametros_origen_comentario = $(this).attr('parametros_origen_comentario');
    var parametros_origen_comentario = cadena_parametros_origen_comentario.split(",");

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el comentario?") + "\n(" + escapeHtml(objeto) + " [" + fecha_hora + "])", TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            $.post("./src/lib/modulos/Comentarios/elimina_comentario.php", {
                modulo: modulo,
                id_comentario: id_comentario
            },
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                switch (origen_comentario) {
                    case ORIGEN_COMENTARIOS_TABLA_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS: {
                        actualiza_informe_plantilla_informe();
                        break;
                    }
                    case ORIGEN_COMENTARIOS_TABLA_COMENTARIOS_RED: {
                        boton_red_filtro_comentarios();
                        break;
                    }
                    case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION:
                    case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION: {
                        var tipo_informe_informacion = parametros_origen_comentario[0];
                        actualiza_informe_sensores_informacion(tipo_informe_informacion);
                        break;
                    }
                    case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                    case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
                        actualiza_informe_smartmeter_consumos_costes_generales();
                        break;
                    }
                    case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                    case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE: {
                        actualiza_informe_proyectos_simulador_linea_base();
                        break;
                    }
                    case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                    case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                    case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                    case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                    case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                    case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME: {
                        actualiza_informe_plantilla_informe();
                        break;
                    }
                    case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                    case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                    case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                    case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES: {
                        var tipo_informe_informacion = parametros_origen_comentario[0];
                        actualiza_informe_actuadores_informacion(tipo_informe_informacion);
                        break;
                    }
                    case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                    case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                    case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                    case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES: {
                        actualiza_informe_plantilla_informe();
                        break;
                    }
                }
            });
        }
    });
}


// Botón de añadir comentario de la ventana modal de añadir/modificar comentario
function boton_anyadir_modificar_comentario() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Módulo
    var modulo = $('#modulo').attr("name");

    // Parámetros de la ventana
    var anyadir_comentario = $("#parametros_ventana_anyadir_modificar_comentario").attr("anyadir_comentario");
    var id_comentario = $("#parametros_ventana_anyadir_modificar_comentario").attr("id_comentario");
    var origen_comentario = $("#parametros_ventana_anyadir_modificar_comentario").attr("origen_comentario");
    var cadena_parametros_origen_comentario = $("#parametros_ventana_anyadir_modificar_comentario").attr("parametros_origen_comentario");
    var parametros_origen_comentario = cadena_parametros_origen_comentario.split(",");

    // Parámetros del comentario
    var fecha = $('#fecha_comentario').val();
    var hora = $('#hora_comentario').val();
    var tipo = $('#tipo_comentario').val();
    var visibilidad = $('#visibilidad_comentario').val();
    var objeto = $("#objeto_comentario").val();
    var descripcion = $('#descripcion_comentario').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $('#descripcion_comentario').addClass('data-check-failed');
        return;
    }

    // Se comprueba la fecha del comentario (si es necesario)
    var fecha_hora = fecha + ", " + hora + ":00";
    switch (origen_comentario) {
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME: {
            var tipo_informe_informacion = parametros_origen_comentario[0];
            var id_elemento_plantilla_informe = null;
            if ((origen_comentario == ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME) ||
                (origen_comentario == ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME)) {
                id_elemento_plantilla_informe = parametros_origen_comentario[1];
            }
            var fecha_correcta = comprueba_fecha_correcta_comentario_informe_sensores_informacion(
                fecha,
                hora,
                tipo_informe_informacion,
                id_elemento_plantilla_informe);
            if (fecha_correcta == false) {
                return;
            }
            break;
        }
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES: {
            var tipo_informe_informacion = parametros_origen_comentario[0];
            var id_elemento_plantilla_informe = null;
            if ((origen_comentario == ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR) ||
                (origen_comentario == ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR) ||
                (origen_comentario == ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES) ||
                (origen_comentario == ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES)) {
                id_elemento_plantilla_informe = parametros_origen_comentario[1];
            }
            var fecha_correcta = comprueba_fecha_correcta_comentario_informe_actuadores_informacion(
                fecha,
                hora,
                tipo_informe_informacion,
                id_elemento_plantilla_informe);
            if (fecha_correcta == false) {
                return;
            }
            break;
        }
        // Nota: Desde la gráfica del informe de consumos y costes sólo se pueden añadir comentarios (no un sólo comentario)
        case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME: {
            var id_elemento_plantilla_informe = null;
            if (origen_comentario == ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME) {
                id_elemento_plantilla_informe = parametros_origen_comentario[1];
            }
            var fecha_correcta = comprueba_fecha_correcta_comentario_informe_smartmeter_consumos_costes_generales(
                fecha,
                hora,
                id_elemento_plantilla_informe);
            if (fecha_correcta == false) {
                return;
            }
            break;
        }
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME: {
            var id_elemento_plantilla_informe = null;
            if ((origen_comentario == ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME) ||
                (origen_comentario == ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME)) {
                id_elemento_plantilla_informe = parametros_origen_comentario[0];
            }
            var fecha_correcta = comprueba_fecha_correcta_comentario_informe_proyectos_simulador_linea_base(
                fecha,
                hora,
                id_elemento_plantilla_informe);
            if (fecha_correcta == false) {
                return;
            }
            break;
        }
    }

    // Se añade o modifica el comentario
    if (anyadir_comentario == true) {
        $.post("./src/lib/modulos/Comentarios/anyade_comentario.php", {
            modulo: modulo,
            fecha_hora: fecha_hora,
            hora: hora,
            tipo: tipo,
            visibilidad: visibilidad,
            objeto: objeto,
            descripcion: descripcion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            switch (origen_comentario) {
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_SENSORES: {
                    var nombre_sensor = objeto;
                    refresca_tabla_sensor(nombre_sensor);
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION: {
                    var tipo_informe_informacion = parametros_origen_comentario[0];
                    actualiza_informe_sensores_informacion(tipo_informe_informacion);
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
                    actualiza_informe_smartmeter_consumos_costes_generales();
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE: {
                    actualiza_informe_proyectos_simulador_linea_base();
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME: {
                    actualiza_informe_plantilla_informe();
                    break;
                }
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_ACTUADORES: {
                    var nombre_actuador = objeto;
                    refresca_tabla_actuador(nombre_actuador);
                    break;
                }
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_GRUPOS_ACTUADORES: {
                    var nombre_grupo_actuadores = objeto;
                    refresca_tabla_grupo_actuadores(nombre_grupo_actuadores);
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES: {
                    var tipo_informe_informacion = parametros_origen_comentario[0];
                    actualiza_informe_actuadores_informacion(tipo_informe_informacion);
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES: {
                    actualiza_informe_plantilla_informe();
                    break;
                }
            }

            // Se cierra la ventana si se ha añadido el comentario desde una gráfica
            switch (origen_comentario) {
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES: {
                    $('#ventana_modal').modal('hide');
                    break;
                }
            }
        });
    }
    else {
        $.post("./src/lib/modulos/Comentarios/modifica_comentario.php", {
            modulo: modulo,
            id_comentario: id_comentario,
            fecha_hora: fecha_hora,
            hora: hora,
            tipo: tipo,
            visibilidad: visibilidad,
            objeto: objeto,
            descripcion: descripcion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            switch (origen_comentario) {
                case ORIGEN_COMENTARIOS_TABLA_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS: {
                    actualiza_informe_plantilla_informe();
                    break;
                }
                case ORIGEN_COMENTARIOS_TABLA_COMENTARIOS_RED: {
                    boton_red_filtro_comentarios();
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION: {
                    var tipo_informe_informacion = parametros_origen_comentario[0];
                    actualiza_informe_sensores_informacion(tipo_informe_informacion);
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
                    actualiza_informe_smartmeter_consumos_costes_generales();
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE: {
                    actualiza_informe_proyectos_simulador_linea_base();
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME: {
                    actualiza_informe_plantilla_informe();
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES: {
                    var tipo_informe_informacion = parametros_origen_comentario[0];
                    actualiza_informe_actuadores_informacion(tipo_informe_informacion);
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES: {
                    actualiza_informe_plantilla_informe();
                    break;
                }
            }
            $('#ventana_modal').modal('hide');
        });
    }
}


// Botón de mostrar la ventana de añadir comentarios
function boton_mostrar_ventana_anyadir_comentarios(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    var modulo = $('#modulo').attr("name");
    var ids_objetos = $(this).attr('ids_objetos');
    var ids_sensores = $(this).attr('ids_sensores');
    var ids_actuadores = $(this).attr('ids_actuadores');
    var ids_grupos_actuadores = $(this).attr('ids_grupos_actuadores');
    var origen_comentarios = $(this).attr('origen_comentarios');
    var parametros_origen_comentarios = $(this).attr('parametros_origen_comentarios');

    muestra_ventana_anyadir_comentarios(
        modulo,
        ids_objetos,
        ids_sensores,
        ids_actuadores,
        ids_grupos_actuadores,
        origen_comentarios,
        parametros_origen_comentarios,
        null,
        null);
}


// Muestra la ventana de añadir comentarios
function muestra_ventana_anyadir_comentarios(
    modulo,
    ids_objetos,
    ids_sensores,
    ids_actuadores,
    ids_grupos_actuadores,
    origen_comentarios,
    parametros_origen_comentarios,
    fecha_hora,
    objeto) {
    $.post("./src/lib/modulos/Comentarios/muestra_ventana_anyadir_comentarios.php", {
        modulo: modulo,
        ids_objetos: ids_objetos,
        ids_sensores: ids_sensores,
        ids_actuadores: ids_actuadores,
        ids_grupos_actuadores: ids_grupos_actuadores,
        origen_comentarios: origen_comentarios,
        parametros_origen_comentarios: parametros_origen_comentarios,
        fecha_hora: fecha_hora,
        objeto: objeto
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


// Botón de añadir comentarios de la ventana modal de añadir comentarios
function boton_anyadir_comentarios() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Módulo
    var modulo = $('#modulo').attr("name");

    // Parámetros de la ventana
    var origen_comentarios = $("#parametros_ventana_anyadir_comentarios").attr("origen_comentarios");
    var cadena_parametros_origen_comentarios = $("#parametros_ventana_anyadir_comentarios").attr("parametros_origen_comentarios");
    var parametros_origen_comentarios = cadena_parametros_origen_comentarios.split(",");

    // Parámetros del comentario
    var fecha = $('#fecha_comentarios').val();
    var hora = $('#hora_comentarios').val();
    var tipo = $('#tipo_comentarios').val();
    var visibilidad = $('#visibilidad_comentarios').val();

    // Se recuperan los objetos seleccionados
    var objetos = [];
    $("#ids_objetos_comentarios option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            objetos.push($(this).text());
        }
    });
    if (objetos.length == 0) {
        switch (tipo) {
            case TIPO_COMENTARIO_ANOTACION_SENSOR:
            case TIPO_COMENTARIO_INTERVENCION_SENSOR: {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                break;
            }
            case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
            case TIPO_COMENTARIO_INTERVENCION_ACTUADOR: {
                jAlert(TLNT.Idiomas._("Seleccione al menos un actuador"));
                break;
            }
            case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
            case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES: {
                jAlert(TLNT.Idiomas._("Seleccione al menos un grupo de actuadores"));
                break;
            }
        }
		return (null);
	}
    var descripcion = $('#descripcion_comentarios').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $('#descripcion_comentarios').addClass('data-check-failed');
        return;
    }

    // Se comprueba la fecha del comentario (si es necesario)
    var fecha_hora = fecha + ", " + hora + ":00";
    switch (origen_comentarios) {
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
        case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
        case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME: {
            var id_elemento_plantilla_informe = null;
            if ((origen_comentarios == ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME) ||
                (origen_comentarios == ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME)) {
                id_elemento_plantilla_informe = parametros_origen_comentarios[0];
            }
            var fecha_correcta = comprueba_fecha_correcta_comentario_informe_smartmeter_consumos_costes_generales(
                fecha,
                hora,
                id_elemento_plantilla_informe);
            if (fecha_correcta == false) {
                return;
            }
            break;
        }
    }

    // Se añaden los comentarios
    $.post("./src/lib/modulos/Comentarios/anyade_comentarios.php", {
        modulo: modulo,
        fecha_hora: fecha_hora,
        hora: hora,
        tipo: tipo,
        visibilidad: visibilidad,
        objetos: objetos,
        descripcion: descripcion
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
        switch (origen_comentarios) {
            case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
                actualiza_informe_smartmeter_consumos_costes_generales();
                break;
            }
            case ORIGEN_COMENTARIOS_TABLA_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
            case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME: {
                actualiza_informe_plantilla_informe();
                break;
            }
        }
        $('#ventana_modal').modal('hide');
    });
}


//
// Funciones de administración de comentarios según su origen
//


// Comprueba que la fecha sea correcta según el informe
function comprueba_fecha_correcta_comentario_informe_sensores_informacion(
    fecha,
    hora,
    tipo_informe_informacion,
    id_elemento_plantilla_informe) {
    // Se recuperan las fechas de inicio y fin de valores
    var id_parametros_resultado_informe = "";
    if (id_elemento_plantilla_informe != null) {
        id_parametros_resultado_informe = "elemento" + id_elemento_plantilla_informe + "-";
    }
    switch (tipo_informe_informacion) {
        case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-temperatura";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-humedad";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-luz-interior";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_VIENTO: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-viento";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-energia-activa";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-energia-reactiva";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-cortes-tension";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-compra-energia";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_GAS: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-gas";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_AGUA: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-agua";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_GENERICA: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-generica";
            break;
        }
    }
    var fecha_inicio_valores = $("#" + id_parametros_resultado_informe).attr("fecha_inicio_valores");
    var hora_inicio_valores = $("#" + id_parametros_resultado_informe).attr("hora_inicio_valores");
    var fecha_fin_valores = $("#" + id_parametros_resultado_informe).attr("fecha_fin_valores");
    var hora_fin_valores = $("#" + id_parametros_resultado_informe).attr("hora_fin_valores");

    // Se comprueba que la fecha esté dentro del rango de fechas
    hora += ":00";
    var fecha_correcta = comprueba_fecha_dentro_rango_fechas(
        fecha, hora,
        fecha_inicio_valores, hora_inicio_valores,
        fecha_fin_valores, hora_fin_valores);
    return (fecha_correcta);
}


// Comprueba que la fecha sea correcta según el informe
function comprueba_fecha_correcta_comentario_informe_actuadores_informacion(
    fecha,
    hora,
    tipo_informe_informacion,
    id_elemento_plantilla_informe) {
    // Se recuperan las fechas de inicio y fin de valores
    var id_parametros_resultado_informe = "";
    if (id_elemento_plantilla_informe != null) {
        id_parametros_resultado_informe = "elemento" + id_elemento_plantilla_informe + "-";
    }
    switch (tipo_informe_informacion) {
        case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS: {
            id_parametros_resultado_informe += "parametros-resultado-informe-informacion-acciones-enviadas";
            break;
        }
    }
    var fecha_inicio_acciones = $("#" + id_parametros_resultado_informe).attr("fecha_inicio_acciones");
    var hora_inicio_acciones = $("#" + id_parametros_resultado_informe).attr("hora_inicio_acciones");
    var fecha_fin_acciones = $("#" + id_parametros_resultado_informe).attr("fecha_fin_acciones");
    var hora_fin_acciones = $("#" + id_parametros_resultado_informe).attr("hora_fin_acciones");

    // Se comprueba que la fecha esté dentro del rango de fechas
    hora += ":00";
    var fecha_correcta = comprueba_fecha_dentro_rango_fechas(
        fecha, hora,
        fecha_inicio_acciones, hora_inicio_acciones,
        fecha_fin_acciones, hora_fin_acciones);
    return (fecha_correcta);
}


// Comprueba que la fecha sea correcta según el informe
function comprueba_fecha_correcta_comentario_informe_smartmeter_consumos_costes_generales(
    fecha,
    hora,
    id_elemento_plantilla_informe) {
    // Se recuperan las fechas de inicio y fin de valores
    var id_parametros_resultado_informe = "";
    if (id_elemento_plantilla_informe != null) {
        id_parametros_resultado_informe = "elemento" + id_elemento_plantilla_informe + "-";
    }
    id_parametros_resultado_informe += "parametros-resultado-informe-consumos-costes-generales";
    var fecha_inicio_consumos = $("#" + id_parametros_resultado_informe).attr("fecha_inicio_consumos");
    var hora_inicio_consumos = $("#" + id_parametros_resultado_informe).attr("hora_inicio_consumos");
    var fecha_fin_consumos = $("#" + id_parametros_resultado_informe).attr("fecha_fin_consumos");
    var hora_fin_consumos = $("#" + id_parametros_resultado_informe).attr("hora_fin_consumos");

    // Se comprueba que la fecha esté dentro del rango de fechas
    hora += ":00";
    var fecha_correcta = comprueba_fecha_dentro_rango_fechas(
        fecha, hora,
        fecha_inicio_consumos, hora_inicio_consumos,
        fecha_fin_consumos, hora_fin_consumos);
    return (fecha_correcta);
}


// Comprueba que la fecha sea correcta según el informe
function comprueba_fecha_correcta_comentario_informe_proyectos_simulador_linea_base(
    fecha,
    hora,
    id_elemento_plantilla_informe) {
    // Se recuperan las fechas de inicio y fin de valores
    var id_parametros_resultado_informe = "";
    if (id_elemento_plantilla_informe != null) {
        id_parametros_resultado_informe = "elemento" + id_elemento_plantilla_informe + "-";
    }
    id_parametros_resultado_informe += "parametros-resultado-informe-simulador-linea-base";
    var fecha_inicio_acciones = $("#" + id_parametros_resultado_informe).attr("fecha_inicio_valores");
    var hora_inicio_acciones = $("#" + id_parametros_resultado_informe).attr("hora_inicio_valores");
    var fecha_fin_acciones = $("#" + id_parametros_resultado_informe).attr("fecha_fin_valores");
    var hora_fin_acciones = $("#" + id_parametros_resultado_informe).attr("hora_fin_valores");

    // Se comprueba que la fecha esté dentro del rango de fechas
    hora += ":00";
    var fecha_correcta = comprueba_fecha_dentro_rango_fechas(
        fecha, hora,
        fecha_inicio_acciones, hora_inicio_acciones,
        fecha_fin_acciones, hora_fin_acciones);
    return (fecha_correcta);
}


// Refresca los detalles de la tabla del sensor
function refresca_tabla_sensor(sensor) {
    $.post("./src/lib/modulos/dame_id_nodo.php", {
        tipo_nodo: TIPO_NODO_SENSOR,
        nombre_nodo: sensor
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_sensor = resultado.id;
        refresca_tabla_nodo(TIPO_NODO_SENSOR, id_sensor);
    });
}


// Refresca los detalles de la tabla del actuador
function refresca_tabla_actuador(actuador) {
    $.post("./src/lib/modulos/dame_id_nodo.php", {
        tipo_nodo: TIPO_NODO_ACTUADOR,
        nombre_nodo: actuador
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_sensor = resultado.id;
        refresca_tabla_nodo(TIPO_NODO_ACTUADOR, id_sensor);
    });
}


// Refresca los detalles de la tabla del grupo de actuadores
function refresca_tabla_grupo_actuadores(grupo_actuadores) {
    $.post("./src/lib/modulos/dame_id_nodo.php", {
        tipo_nodo: TIPO_NODO_GRUPO_ACTUADORES,
        nombre_nodo: grupo_actuadores
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_sensor = resultado.id;
        refresca_tabla_nodo(TIPO_NODO_GRUPO_ACTUADORES, id_sensor);
    });
}


// Actualiza el informe de la plantilla de informe
function actualiza_informe_plantilla_informe() {
    // Se recuperar los textos de los elementos notas del informe y se restauran después de redibujar el informe
    // (para no perder las notas introducidas al actualizar el informe)
    var ids_textos_notas = [];
    var textos_textos_notas = [];
    $('.texto-elemento-notas-plantilla-informe').each(function() {
        var id_texto_notas = this.id;
        var texto_texto_notas = $("#" + id_texto_notas).val();
        if (texto_texto_notas != "") {
            ids_textos_notas.push(id_texto_notas);
            textos_textos_notas.push(texto_texto_notas);
        }
    });
    muestra_actualiza_informe_plantilla_informe(ids_textos_notas, textos_textos_notas);
}


// Actualiza el informe de información de sensores correspondiente
function actualiza_informe_sensores_informacion(tipo_informe_informacion) {
    switch (tipo_informe_informacion) {
        case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA: {
            boton_sensores_informacion_temperatura_ver_informe();
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD: {
            boton_sensores_informacion_humedad_ver_informe();
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR: {
            boton_sensores_informacion_luz_interior_ver_informe();
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_VIENTO: {
            boton_sensores_informacion_viento_ver_informe();
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA: {
            boton_sensores_informacion_energia_activa_ver_informe();
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA: {
            boton_sensores_informacion_energia_reactiva_ver_informe();
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION: {
            boton_sensores_informacion_cortes_tension_ver_informe();
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA: {
            boton_sensores_informacion_compra_energia_ver_informe();
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_GAS: {
            boton_sensores_informacion_gas_ver_informe();
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_AGUA: {
            boton_sensores_informacion_agua_ver_informe();
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_GENERICA: {
            boton_sensores_informacion_generica_ver_informe();
            break;
        }
    }
}


// Actualiza el informe de información de actuadores correspondiente
function actualiza_informe_actuadores_informacion(tipo_informe_informacion) {
    switch (tipo_informe_informacion) {
        case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS: {
            boton_actuadores_informacion_acciones_enviadas_ver_informe();
            break;
        }
    }
}


// Actualiza el informe de consumos y costes generales
function actualiza_informe_smartmeter_consumos_costes_generales() {
    boton_smartmeter_consumos_costes_generales_ver_informe();
}


// Actualiza el informe de simulación de línea base
function actualiza_informe_proyectos_simulador_linea_base() {
    boton_proyectos_simulador_linea_base_ver_informe();
}
