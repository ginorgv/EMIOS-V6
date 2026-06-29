// Eventos de los botones (por funcionalidad)
TLNT.Navegacion.botones_secciones_actuadores = [
    // Actuadores
    {	selector: '.boton_actuadores_mostrar_ventana_envio_accion',
		funcion: 	boton_actuadores_mostrar_ventana_envio_accion
	},
    {	selector: '.boton_actuadores_mostrar_ventana_borrado_acciones_enviadas',
		funcion: 	boton_actuadores_mostrar_ventana_borrado_acciones_enviadas
	},
    {	selector: '.boton_actuadores_mostrar_ventana_asignacion_localizacion',
		funcion: 	boton_actuadores_mostrar_ventana_asignacion_localizacion
	},
    {	selector: '.boton_actuadores_mostrar_ventana_asignacion_grupo',
		funcion: 	boton_actuadores_mostrar_ventana_asignacion_grupo
	},
    {	selector: '#boton_actuadores_filtro_actuadores_tabla',
		funcion: 	boton_actuadores_filtro_actuadores_tabla
	},
    {	selector: '#boton_actuadores_filtro_grupos_tabla',
		funcion: 	boton_actuadores_filtro_grupos_tabla
	},
    // Programaciones
    {	selector: '#boton_actuadores_filtro_programaciones_tabla',
		funcion: 	boton_actuadores_filtro_programaciones_tabla
	},
    // Reglas
    {	selector: '#boton_actuadores_filtro_reglas_tabla',
		funcion: 	boton_actuadores_filtro_reglas_tabla
	},
    {	selector: '#boton_actuadores_filtro_historico_reglas',
		funcion: 	boton_actuadores_filtro_historico_reglas
	},
    {	selector: '.boton_actuadores_envia_accion_herramientas_reglas',
		funcion: 	boton_actuadores_envia_accion_herramientas_reglas
	},
    // Información
    {	selector: '#boton_actuadores_informacion_acciones_enviadas_ver_informe',
		funcion: 	boton_actuadores_informacion_acciones_enviadas_ver_informe
	},
    {	selector: '#boton_actuadores_informacion_acciones_enviadas_generar_pdf',
		funcion: 	boton_actuadores_informacion_acciones_enviadas_generar_pdf
	},
    {	selector: '#boton_actuadores_informacion_acciones_enviadas_anyadir_informe_automatico',
		funcion: 	boton_actuadores_informacion_acciones_enviadas_anyadir_informe_automatico
	},
    // Mapa
    {	selector: '#boton_actuadores_filtro_actuadores_mapa',
		funcion: 	boton_actuadores_filtro_actuadores_mapa
	}
];


TLNT.Navegacion.botones_tablas_datos_actuadores = [
    // Programaciones
    {	selector: '.boton_actuadores_mostrar_ventana_anyadir_modificar_programacion',
		funcion: 	boton_actuadores_mostrar_ventana_anyadir_modificar_programacion
	},
    {	selector: '.boton_actuadores_actualizar_tabla_programaciones',
		funcion: 	boton_actuadores_actualizar_tabla_programaciones
	},
    {	selector: '.boton_actuadores_eliminar_programacion',
		funcion: 	boton_actuadores_eliminar_programacion
	},
    // Reglas
    {	selector: '.boton_actuadores_mostrar_ventana_anyadir_modificar_regla',
		funcion: 	boton_actuadores_mostrar_ventana_anyadir_modificar_regla
	},
    {	selector: '.boton_actuadores_actualizar_tabla_reglas',
		funcion: 	boton_actuadores_actualizar_tabla_reglas
	},
    {	selector: '.boton_actuadores_actualizacion_periodica_tabla_reglas',
		funcion: 	boton_actuadores_actualizacion_periodica_tabla_reglas
	},
    {	selector: '.boton_actuadores_eliminar_regla',
		funcion: 	boton_actuadores_eliminar_regla
	},
    // Ayuda (programaciones)
    {	selector: '.boton_actuadores_ayuda_tabla_programaciones',
		funcion: 	boton_actuadores_ayuda_tabla_programaciones
	},
    // Ayuda (reglas)
    {	selector: '.boton_actuadores_ayuda_tabla_reglas',
		funcion: 	boton_actuadores_ayuda_tabla_reglas
	}
];


TLNT.Navegacion.botones_detalles_tablas_datos_actuadores = [
    // Comentarios
    {   selector: '.boton_mostrar_ventana_anyadir_modificar_comentario',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_comentario
	},
    // Envío de acciones
    {	selector: '.boton_actuadores_mostrar_ventana_envio_accion_actuador',
		funcion: 	boton_actuadores_mostrar_ventana_envio_accion_actuador
	},
    {	selector: '.boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_actuador',
		funcion: 	boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_actuador
	},
    {	selector: '.boton_actuadores_mostrar_ventana_envio_accion_grupo_actuadores',
		funcion: 	boton_actuadores_mostrar_ventana_envio_accion_grupo_actuadores
	},
    {	selector: '.boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_grupo_actuadores',
		funcion: 	boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_grupo_actuadores
	},
    // Acciones de programaciones
	{	selector: '.boton_actuadores_mostrar_ventana_anyadir_modificar_accion_programacion',
		funcion: 	boton_actuadores_mostrar_ventana_anyadir_modificar_accion_programacion
	},
    {	selector: '.boton_actuadores_actualizar_tabla_acciones_programacion',
		funcion: 	boton_actuadores_actualizar_tabla_acciones_programacion
	},
	{	selector: '.boton_actuadores_eliminar_accion_programacion',
		funcion: 	boton_actuadores_eliminar_accion_programacion
	},
    // Excepciones de programaciones
    {	selector: '.boton_actuadores_mostrar_ventana_anyadir_modificar_excepcion_programacion',
		funcion: 	boton_actuadores_mostrar_ventana_anyadir_modificar_excepcion_programacion
	},
    {	selector: '.boton_actuadores_actualizar_tabla_excepciones_programacion',
		funcion: 	boton_actuadores_actualizar_tabla_excepciones_programacion
	},
	{	selector: '.boton_actuadores_eliminar_excepcion_programacion',
		funcion: 	boton_actuadores_eliminar_excepcion_programacion
	},
    // Reglas
	{	selector: '.boton_actuadores_refrescar_tabla_regla',
		funcion: 	boton_actuadores_refrescar_tabla_regla
	},
    {	selector: '.boton_actuadores_envia_accion_herramientas_regla',
		funcion: 	boton_actuadores_envia_accion_herramientas_regla
	},
    // Sucesos de reglas
    {	selector: '.boton_actuadores_mostrar_ventana_anyadir_modificar_suceso_regla',
		funcion: 	boton_actuadores_mostrar_ventana_anyadir_modificar_suceso_regla
	},
    {	selector: '.boton_actuadores_actualizar_tabla_sucesos_regla',
		funcion: 	boton_actuadores_actualizar_tabla_sucesos_regla
	},
	{	selector: '.boton_actuadores_eliminar_suceso_regla',
		funcion: 	boton_actuadores_eliminar_suceso_regla
	},
    // Acciones de reglas
    {	selector: '.boton_actuadores_mostrar_ventana_anyadir_modificar_accion_regla',
		funcion: 	boton_actuadores_mostrar_ventana_anyadir_modificar_accion_regla
	},
    {	selector: '.boton_actuadores_actualizar_tabla_acciones_regla',
		funcion: 	boton_actuadores_actualizar_tabla_acciones_regla
	},
    {	selector: '.boton_actuadores_eliminar_accion_regla',
		funcion: 	boton_actuadores_eliminar_accion_regla
	}
];


TLNT.Navegacion.botones_ventanas_modales_actuadores = [
    // Actuadores
    {	selector: '.boton_actuadores_enviar_accion',
		funcion: 	boton_actuadores_enviar_accion
	},
    {	selector: '.boton_actuadores_borrar_acciones_enviadas',
		funcion: 	boton_actuadores_borrar_acciones_enviadas
	},
    // Ayuda (actuadores)
    {	selector: '#boton_actuadores_ayuda_calibracion_actuador',
		funcion: 	boton_actuadores_ayuda_calibracion_actuador
	},
    // Programaciones
    {	selector: '.boton_actuadores_anyadir_modificar_programacion',
		funcion: 	boton_actuadores_anyadir_modificar_programacion
	},
    // Acciones de programaciones
    {	selector: '.boton_actuadores_anyadir_modificar_accion_programacion',
		funcion: 	boton_actuadores_anyadir_modificar_accion_programacion
	},
    // Excepciones de programaciones
    {	selector: '.boton_actuadores_anyadir_modificar_excepcion_programacion',
		funcion: 	boton_actuadores_anyadir_modificar_excepcion_programacion
	},
    // Reglas
	{	selector: '.boton_actuadores_anyadir_modificar_regla',
		funcion: 	boton_actuadores_anyadir_modificar_regla
	},
    // Ayuda (reglas)
    {	selector: '#boton_actuadores_ayuda_numero_dias_caducidad_acciones_regla',
		funcion: 	boton_actuadores_ayuda_numero_dias_caducidad_acciones_regla
	},
    // Sucesos de reglas
    {	selector: '.boton_actuadores_anyadir_modificar_suceso_regla',
		funcion: 	boton_actuadores_anyadir_modificar_suceso_regla
	},
    // Acciones de reglas
    {	selector: '.boton_actuadores_anyadir_modificar_accion_regla',
		funcion: 	boton_actuadores_anyadir_modificar_accion_regla
	},
    // Ayuda (acciones de reglas)
    {	selector: '#boton_actuadores_ayuda_comodines_mensajes_texto_acciones_reglas',
		funcion: 	boton_actuadores_ayuda_comodines_mensajes_texto_acciones_reglas
	},
    {	selector: '#boton_actuadores_ayuda_comodines_mensajes_texto_acciones_manuales_programaciones',
		funcion: 	boton_actuadores_ayuda_comodines_mensajes_texto_acciones_manuales_programaciones
	}
];


TLNT.Navegacion.botones_controles_interfaces_actuadores = [
    // Ayuda
    {	selector: '#boton_actuadores_ayuda_tipos_registro_modbus_actuador',
		funcion: 	boton_actuadores_ayuda_tipos_registro_modbus_actuador
	},
    {	selector: '#boton_actuadores_ayuda_tipos_dato_modbus_actuador',
		funcion: 	boton_actuadores_ayuda_tipos_dato_modbus_actuador
	}
];


TLNT.Navegacion.botones_tablas_datos_informes_actuadores = [
    // Comentarios
    {   selector: '.boton_mostrar_ventana_anyadir_modificar_comentario',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_comentario
	},
    {	selector: '.boton_eliminar_comentario',
		funcion: 	boton_eliminar_comentario
	}
];


//
// Funciones de establecimiento de eventos (por funcionalidad)
//


TLNT.Navegacion.establece_eventos_secciones_actuadores = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_secciones_actuadores);

    establece_eventos_secciones_actuadores_informes();
};


TLNT.Navegacion.establece_eventos_tablas_datos_actuadores = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_actuadores);
};


TLNT.Navegacion.establece_eventos_detalles_tablas_datos_actuadores = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_detalles_tablas_datos_actuadores);
};


TLNT.Navegacion.establece_eventos_tablas_datos_informes_actuadores = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_informes_actuadores);
};


TLNT.Navegacion.establece_eventos_ventanas_modales_actuadores = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_ventanas_modales_actuadores);

    establece_eventos_ventanas_modales_actuadores_actuadores();
    establece_eventos_ventanas_modales_actuadores_interfaces_actuadores();
    establece_eventos_ventanas_modales_actuadores_controles_interfaces_actuadores();
    establece_eventos_ventanas_modales_actuadores_grupos_actuadores();
    establece_eventos_ventanas_modales_actuadores_programaciones();
    establece_eventos_ventanas_modales_actuadores_reglas();
};


//
// Funciones auxiliares para establecer las acciones de los controles
//


establece_eventos_secciones_actuadores_informes = function() {
    establece_eventos_secciones_actuadores_informes_informacion();
};


establece_eventos_secciones_actuadores_informes_informacion = function() {
    // Desactivación de eventos anteriores
    $("#clase_actuador_actuadores_informacion_acciones_enviadas").off();
    $("#destino_accion_actuadores_informacion_acciones_enviadas").off();
    $("#id_destino_accion_actuadores_informacion_acciones_enviadas").off();
    $("#clase_sensor_actuadores_informacion_acciones_enviadas").off();
    $("#campo_actuadores_informacion_acciones_enviadas").off();
    $("#intervalo_valores_actuadores_informacion_acciones_enviadas").off();

    // Funciones de recarga de listas de parámetros de acciones enviadas
    var funcion_recarga_ids_destinos_accion_acciones_enviadas = function() {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/dame_lista_ids_destinos_accion.php", {
			clase_actuador: $("#clase_actuador_actuadores_informacion_acciones_enviadas").val(),
            destino: $("#destino_accion_actuadores_informacion_acciones_enviadas").val(),
            id_destino: $("#id_destino_accion_actuadores_informacion_acciones_enviadas").val()
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_destino_accion_actuadores_informacion_acciones_enviadas").html(resultado.html);
            $("#id_destino_accion_actuadores_informacion_acciones_enviadas").trigger('change').trigger("chosen:updated");
            funcion_habilita_id_destino_accion_acciones_enviadas();
		});
    };
    var funcion_habilita_id_destino_accion_acciones_enviadas = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_destinos = $("select#id_destino_accion_actuadores_informacion_acciones_enviadas" + " option").length;
        if (numero_destinos <= 1) {
            $("#id_destino_accion_actuadores_informacion_acciones_enviadas").attr('disabled', true);
        }
        else {
            $("#id_destino_accion_actuadores_informacion_acciones_enviadas").removeAttr('disabled');
        }
        $("#id_destino_accion_actuadores_informacion_acciones_enviadas").trigger("chosen:updated");
    };

    // Acciones a realizar al modificar la clase de actuador de acciones enviadas
    var funcion_realiza_acciones_clase_actuador_acciones_enviadas = function() {
        funcion_recarga_ids_destinos_accion_acciones_enviadas();
    };
    $("#clase_actuador_actuadores_informacion_acciones_enviadas").change(funcion_realiza_acciones_clase_actuador_acciones_enviadas);

    // Acciones a realizar al modificar el destino de acción de acciones enviadas
    var funcion_realiza_acciones_destino_accion_acciones_enviadas = function() {
        funcion_recarga_ids_destinos_accion_acciones_enviadas();
    };
    $("#destino_accion_actuadores_informacion_acciones_enviadas").change(funcion_realiza_acciones_destino_accion_acciones_enviadas);

    // Acciones a realizar al mostrar el identificador de destino de acción de acciones enviadas
    $("#id_destino_accion_actuadores_informacion_acciones_enviadas").show(funcion_habilita_id_destino_accion_acciones_enviadas);

    // Recarga de los sensores de una clase de los controles especificados
    var funcion_recarga_sensores_clase = function(id_controles) {
        var clase_sensor = $("#clase_sensor_" + id_controles).val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores.php", {
            clase_sensor: clase_sensor,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_sensor_" + id_controles).html(resultado.html);
            $("#id_sensor_" + id_controles).trigger("chosen:updated");
            switch (clase_sensor) {
                case CLASE_NINGUNA: {
                    $("#id_sensor_" + id_controles).attr('disabled', true).trigger("chosen:updated");
                    break;
                }
                default: {
                    $("#id_sensor_" + id_controles).removeAttr('disabled').trigger("chosen:updated");
                    break;
                }
            }
        });
    };

    // Recarga de los campos de una clase de sensor de los controles especificados
    var funcion_recarga_campos_clase_sensor = function(id_controles, tipo_agrupacion_valores) {
        var clase_sensor = $("#clase_sensor_" + id_controles).val();
        var intervalo_valores = $("#intervalo_valores_" + id_controles).val();
        var campo = $("#campo_" + id_controles).val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_campos_clase_sensor_parametros_extra.php", {
            clase_sensor: clase_sensor,
            tipo_agrupacion_valores: tipo_agrupacion_valores,
            intervalo_valores: intervalo_valores,
            campo: campo
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#campo_" + id_controles).html(resultado.html);

            // Se deshabilita si sólo hay un valor para elegir
            var numero_campos = $("select#campo_" + id_controles + " option").length;
            if (numero_campos <= 1) {
                $("#campo_" + id_controles).attr('disabled', true);
            }
            else {
                $("#campo_" + id_controles).removeAttr('disabled');
            }

            // Valor seleccionado por defecto
            switch (clase_sensor) {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                case CLASE_SENSOR_AGUA: {
                    $("#campo_" + id_controles).val(CAMPO_INCREMENTO);
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA: {
                    $("#campo_" + id_controles).val(CAMPO_CONSUMO_ESTIMADO);
                    break;
                }
                case CLASE_SENSOR_GAS: {
                    $("#campo_" + id_controles).val(CAMPO_CONSUMO);
                    break;
                }
            }

            $("#campo_" + id_controles).trigger('change');
        });
    };

    // Habilita los intervalos de valores de sensor de acciones enviadas
    var funcion_habilita_intervalos_valores_sensor_acciones_enviadas = function() {
        var clase_sensor = $("#clase_sensor_actuadores_informacion_acciones_enviadas").val();
        switch (clase_sensor) {
            case CLASE_NINGUNA: {
                $("#intervalo_valores_actuadores_informacion_acciones_enviadas").attr('disabled', true);
                break;
            }
            default: {
                $("#intervalo_valores_actuadores_informacion_acciones_enviadas").removeAttr('disabled');
                break;
            }
        }
    };

    // Recarga de los sensores de una clase del informe de acciones enviadas
    var funcion_recarga_sensores_clase_acciones_enviadas = function() {
        funcion_recarga_sensores_clase("actuadores_informacion_acciones_enviadas");
        funcion_recarga_campos_clase_sensor("actuadores_informacion_acciones_enviadas", false);
        funcion_habilita_intervalos_valores_sensor_acciones_enviadas();
    };
    $("#clase_sensor_actuadores_informacion_acciones_enviadas").show(funcion_habilita_intervalos_valores_sensor_acciones_enviadas);
    $("#clase_sensor_actuadores_informacion_acciones_enviadas").change(funcion_recarga_sensores_clase_acciones_enviadas);

    // Recarga la lista de intervalos de valores de sensor en el informe de acciones enviadas
    var funcion_recarga_intervalos_valores_sensor_acciones_enviadas = function() {
        var clase_sensor = $("#clase_sensor_actuadores_informacion_acciones_enviadas").val();
        var campo = $("#campo_actuadores_informacion_acciones_enviadas").val();

        // Intervalo de valores de sensor por defecto
        var intervalo_valores = null;
        switch (clase_sensor) {
            case CLASE_NINGUNA: {
                intervalo_valores = INTERVALO_VALORES_NINGUNO;
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA: {
                intervalo_valores = INTERVALO_VALORES_CUARTOHORA;
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_COMPRA_ENERGIA:
            case CLASE_SENSOR_GAS:
            case CLASE_SENSOR_AGUA: {
                intervalo_valores = INTERVALO_VALORES_HORA;
                break;
            }
            case CLASE_SENSOR_GENERICA: {
                intervalo_valores = $("#intervalo_valores_actuadores_informacion_acciones_enviadas").val();
                break;
            }
            default: {
                intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL_LINEAS;
                break;
            }
        }

        // Se recupera la lista de intervalos
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_intervalos_valores_informes_informacion_comparacion_clase_sensor_campo.php", {
            clase_sensor: clase_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores,
            opciones_extra: OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_NINGUNO
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#intervalo_valores_actuadores_informacion_acciones_enviadas").html(resultado.html);
        });
    };

    // Acciones a realizar al modificar el campo de sensor de acciones enviadas
    $("#campo_actuadores_informacion_acciones_enviadas").change(funcion_recarga_intervalos_valores_sensor_acciones_enviadas);

    // Control de parámetros extra de campo
    var funcion_muestra_control_referencia_acciones_enviadas = function() {
        var clase_sensor = $("#clase_sensor_actuadores_informacion_acciones_enviadas").val();
        funcion_muestra_control_parametros_extra_campo_informe("actuadores_informacion_acciones_enviadas", clase_sensor);
    };
    $("#campo_actuadores_informacion_acciones_enviadas").show(funcion_muestra_control_referencia_acciones_enviadas);
    $("#campo_actuadores_informacion_acciones_enviadas").change(funcion_muestra_control_referencia_acciones_enviadas);

    // Recarga los campos de valores de sensor según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_acciones_enviadas = function() {
        var id_controles = "actuadores_informacion_acciones_enviadas";
        var clase_sensor = $("#clase_sensor_" + id_controles).val();
        if (clase_sensor == CLASE_SENSOR_GENERICA) {
            funcion_recarga_campos_clase_sensor(id_controles, false);
        }
    };
    $("#intervalo_valores_actuadores_informacion_acciones_enviadas").change(funcion_recarga_campos_intervalo_valores_acciones_enviadas);
};


establece_eventos_ventanas_modales_actuadores_actuadores = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_actuador").off();
    $("#clase_actuador_envio_accion").off();
    $("#destino_accion_envio_accion").off();
    $("#id_destino_accion_envio_accion").off();
    $("#clase_actuador_borrado_acciones_enviadas").off();
    $("#destino_accion_borrado_acciones_enviadas").off();
    $("#id_destino_accion_borrado_acciones_enviadas").off();
    $("#clase_actuador").off();
    $("#tipo_actuador").off();
    $("#id_grupo_actuador").off();
    $("#id_localizacion_actuador").off();
    $("#id_programacion_actuador").off();
    $("#clase_grupo_actuadores").off();
    $("#clase_actuador_accion_regla").off();
    $("#contenido_mensaje").off();

    // Contador de caracteres de descripción de actuador
    $("#descripcion_actuador").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Funciones de recarga de destinos de acción
    var funcion_recarga_ids_destinos_accion_actuadores = function(id_controles) {
        var clase_actuador = $("#clase_actuador_" + id_controles).val();
        var destino = $("#destino_accion_" + id_controles).val();
        var id_destino = $("#id_destino_accion_" + id_controles).val();
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/dame_lista_ids_destinos_accion.php", {
			clase_actuador: clase_actuador,
            destino: destino,
            id_destino: id_destino
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_destino_accion_" + id_controles).html(resultado.html);
            $("#id_destino_accion_" + id_controles).trigger("chosen:updated");
            funcion_habilita_id_destino_accion_actuadores(id_controles);

            $("#id_destino_accion_" + id_controles).trigger('change');
		});
    };
    var funcion_habilita_id_destino_accion_actuadores = function(id_controles) {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_destinos = $("select#id_destino_accion_" + id_controles + " option").length;
        if (numero_destinos <= 1) {
            $("#id_destino_accion_" + id_controles).prop('disabled', 'disabled');
        }
        else {
            $("#id_destino_accion_" + id_controles).prop('disabled', false);
        }
        $("#id_destino_accion_" + id_controles).trigger("chosen:updated");
    };

    // Acciones a realizar al modificar la clase de actuador
    var funcion_realiza_acciones_clase_actuador_actuadores = function(id_controles) {
        funcion_recarga_ids_destinos_accion_actuadores(id_controles);
    };
    var funcion_realiza_acciones_clase_actuador_envio_accion = function() {
        funcion_realiza_acciones_clase_actuador_actuadores("envio_accion");
    };
    var funcion_realiza_acciones_clase_actuador_borrado_acciones_enviadas = function(id_controles) {
        funcion_realiza_acciones_clase_actuador_actuadores("borrado_acciones_enviadas");
    };
    $("#clase_actuador_envio_accion").change(funcion_realiza_acciones_clase_actuador_envio_accion);
    $("#clase_actuador_borrado_acciones_enviadas").change(funcion_realiza_acciones_clase_actuador_borrado_acciones_enviadas);

    // Acciones a realizar al modificar el destino de acción
    var funcion_realiza_acciones_destino_accion_actuadores = function(id_controles) {
        funcion_recarga_ids_destinos_accion_actuadores(id_controles);
    };
    var funcion_realiza_acciones_destino_accion_envio_accion = function() {
        funcion_realiza_acciones_destino_accion_actuadores("envio_accion");
    };
    var funcion_realiza_acciones_destino_accion_borrado_acciones_enviadas = function() {
        funcion_realiza_acciones_destino_accion_actuadores("borrado_acciones_enviadas");
    };
    $("#destino_accion_envio_accion").change(funcion_realiza_acciones_destino_accion_envio_accion);
    $("#destino_accion_borrado_acciones_enviadas").change(funcion_realiza_acciones_destino_accion_borrado_acciones_enviadas);

    // Acciones a realizar al mostrar el identificador de destino de acción
    var funcion_habilita_id_destino_accion_envio_accion = function() {
        funcion_habilita_id_destino_accion_actuadores("envio_accion");
    };
    var funcion_habilita_id_destino_accion_borrado_acciones_enviadas = function() {
        funcion_habilita_id_destino_accion_actuadores("borrado_acciones_enviadas");
    };
    $("#id_destino_accion_envio_accion").show(funcion_habilita_id_destino_accion_envio_accion);
    $("#id_destino_accion_borrado_acciones_enviadas").show(funcion_habilita_id_destino_accion_borrado_acciones_enviadas);

    // Recarga los controles de acción del actuador o grupo de actuadores
    var funcion_recarga_controles_accion_envio_accion = function() {
        var clase_actuador = $("#clase_actuador_envio_accion").val();
        var destino = $("#destino_accion_envio_accion").val();
        var id_destino = $("#id_destino_accion_envio_accion").val();
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/dame_controles_accion.php", {
			clase_actuador: clase_actuador,
            destino: destino,
            id_destino: id_destino,
            origen_controles_accion: ORIGEN_CONTROLES_ACCION_ENVIO_ACCION
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#controles_accion_envio_accion").html(resultado.html);

            // Nota: Se 'restablecen' los eventos de los botones para los botones de ayuda
            TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_ventanas_modales_actuadores);

            // Eventos específicos de controles de acciones de tipos de actuador
            switch (clase_actuador) {
                case CLASE_ACTUADOR_MENSAJE: {
                    $("#contenido_mensaje").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
                    break;
                }
            }
		});
    };
    $("#id_destino_accion_envio_accion").change(funcion_recarga_controles_accion_envio_accion);

    // Mostrar lista doble para los días de la semana
    if ($('#select_dias_semana_accion_programacion_no_visible').length) {
        $('#select_dias_semana_accion_programacion_no_visible').attr("id", "select_dias_semana_accion_programacion_visible");
        TLNT.Navegacion.convierte_lista_doble("dias_semana_accion_programacion", false);
    }

    // Habilita la lista de identificadores de grupos de un actuador
    var funcion_habilita_lista_ids_grupos_actuador = function() {
        if ($("select#id_grupo_actuador option").length <= 1) {
            $("#id_grupo_actuador").prop('disabled', 'disabled');
        }
        else {
            $("#id_grupo_actuador").prop('disabled', false);
        }
        $("#id_grupo_actuador").trigger("chosen:updated");
    };

    // Habilita la lista de identificadores de programaciones de un actuador
    var funcion_habilita_lista_ids_programaciones_actuador = function() {
        if ($("select#id_programacion_actuador option").length <= 1) {
            $("#id_programacion_actuador").prop('disabled', 'disabled');
        }
        else {
            $("#id_programacion_actuador").prop('disabled', false);
        }
        $("#id_programacion_actuador").trigger("chosen:updated");
    };

    // Muestra los controles de localización de un actuador
    var funcion_muestra_controles_localizacion_actuador = function() {
        var mostrar_controles_localizaciones = $("#parametros_ventana_anyadir_modificar_nodo").attr("mostrar_controles_localizaciones");
        if (mostrar_controles_localizaciones == VALOR_SI) {
            $("#control_id_localizacion_actuador").show();
        }
        else {
            $("#control_id_localizacion_actuador").hide();
            $("#control_visible_localizaciones_hijas_actuador").hide();
        }
    };

    // Habilita la lista de identificadores de localizaciones de un actuador
    var funcion_habilita_lista_ids_localizaciones_actuador = function() {
        if ($("select#id_localizacion_actuador option").length <= 1) {
            $("#id_localizacion_actuador").prop('disabled', 'disabled');
        }
        else {
            $("#id_localizacion_actuador").prop('disabled', false);
        }
        $("#id_localizacion_actuador").trigger("chosen:updated");
    };

    // Habilitación y mostrado de controles dependientes de la clase de actuador
    var funcion_habilita_muestra_controles_clase_actuador = function() {
        var clase_actuador = $("#clase_actuador").val();
        switch (clase_actuador) {
            case CLASE_ACTUADOR_GENERICA: {
                $("#titulo-tab-clase-generica").show();
                break;
            }
            default: {
                $("#titulo-tab-clase-generica").hide();
            }
        }

        // Habilita el identificador de grupo de actuador
        funcion_habilita_lista_ids_grupos_actuador();

        // Muestra los controles de localización del actuador
        funcion_muestra_controles_localizacion_actuador();

        // Habilita el identificador de localización de actuador
        funcion_habilita_lista_ids_localizaciones_actuador();

        // Habilita el identificador de programación de actuador
        funcion_habilita_lista_ids_programaciones_actuador();
    };
    $("#clase_actuador").show(funcion_habilita_muestra_controles_clase_actuador);

    // Si se modifica la clase de actuador:
    // - Hay que actualizar las listas de grupos y de programaciones correspondientes a la nueva clase de actuador
    var funcion_realiza_acciones_clase_actuador_modificada = function() {
        // Se actualiza la lista de grupos de actuadores
        $.post("./src/lib/modulos/Nodos/administracion/dame_lista_grupos_actuadores.php", {
			clase_actuador: $("#clase_actuador").val(),
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_grupo_actuador").html(resultado.html);

            // Se actualiza la lista de programaciones de clase de actuador
            $.post("./src/lib/modulos/Nodos/administracion/dame_lista_programaciones_clase_actuador.php", {
                clase_actuador: $("#clase_actuador").val()
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                $("#id_programacion_actuador").html(resultado.html);

                // Habilita y muestra controles según la clase de actuador()
                funcion_habilita_muestra_controles_clase_actuador();
            });
		});
    };
    $("#clase_actuador").change(funcion_realiza_acciones_clase_actuador_modificada);

    // Se muestran y ocultan controles dependientes del tipo de actuador
    var funcion_habilita_muestra_controles_tipo_actuador = function() {
        var tipo_actuador = $("#tipo_actuador").val();
        switch (tipo_actuador) {
            case TIPO_ACTUADOR_HARDWARE: {
                $("#titulo-tab-interfaz").show();
                $("#control_id_axon_actuador").show();
                $("#id_axon_actuador").removeAttr('disabled');
                break;
            }
            case TIPO_ACTUADOR_SOFTWARE: {
                $("#titulo-tab-interfaz").show();
                $("#control_id_axon_actuador").hide();
                $("#id_axon_actuador").val(ID_NINGUNO);
                $("#id_axon_actuador").attr('disabled', 'disabled');
                break;
            }
            case TIPO_NINGUNO: {
                $("#titulo-tab-interfaz").hide();
                break;
            }
        }
    };
    $("#tipo_actuador").show(funcion_habilita_muestra_controles_tipo_actuador);

    // Si se modifica el tipo de actuador:
    // - Hay que actualizar las listas de clases de interfaz (y recargar los controles correspondientes a la nueva clase de interfaz)
    var funcion_realiza_acciones_tipo_actuador_modificado = function() {
        $.post("./src/lib/modulos/Nodos/administracion/dame_lista_clases_interfaz_tipo_actuador.php", {
			tipo: $("#tipo_actuador").val()
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#clase_interfaz_actuador").html(resultado.html);
            $("#clase_interfaz_actuador").trigger('change');

            // Habilita y muestra controles según el tipo de actuador
            funcion_habilita_muestra_controles_tipo_actuador();
		});
    };
    $("#tipo_actuador").change(funcion_realiza_acciones_tipo_actuador_modificado);

    // Si se modifica el grupo de actuadores:
    // - Si hay grupo, se deselecciona la programación seleccionada
    var funcion_realiza_acciones_grupo_actuadores_modificado = function() {
        if ($("#id_grupo_actuador").val() != ID_NINGUNO) {
            $("#id_programacion_actuador").val(ID_NINGUNO).trigger("chosen:updated");
        }
    };
    $("#id_grupo_actuador").change(funcion_realiza_acciones_grupo_actuadores_modificado);

    // Habilita y muestra los controles dependientes de la localización del actuador
    var funcion_habilita_muestra_controles_localizacion_actuador = function() {
        var id_localizacion_actuador = $("#id_localizacion_actuador").val();
        switch (id_localizacion_actuador) {
            case ID_NINGUNO.toString(): {
                $("#control_visible_localizaciones_hijas_actuador").hide();
                break;
            }
            default: {
                var mostrar_controles_localizaciones = $("#parametros_ventana_anyadir_modificar_nodo").attr("mostrar_controles_localizaciones");
                if (mostrar_controles_localizaciones == VALOR_SI) {
                    $("#control_visible_localizaciones_hijas_actuador").show();
                }
                break;
            }
        }
    };
    $("#id_localizacion_actuador").show(funcion_habilita_muestra_controles_localizacion_actuador);
    $("#id_localizacion_actuador").change(funcion_habilita_muestra_controles_localizacion_actuador);

    // Si se modifica la programación del actuador
    // - Si hay programación, se deselecciona el grupo de actuadores seleccionado
    var funcion_realiza_acciones_programacion_actuador_modificada = function() {
        if ($("#id_programacion_actuador").val() != ID_NINGUNO) {
            $("#id_grupo_actuador").val(ID_NINGUNO).trigger("chosen:updated");
        }
    };
    $("#id_programacion_actuador").change(funcion_realiza_acciones_programacion_actuador_modificada);

    // Muestra los controles de localización de un grupo de actuadores
    var funcion_muestra_controles_localizacion_grupo_actuadores = function() {
        var mostrar_controles_localizaciones = $("#parametros_ventana_anyadir_modificar_nodo").attr("mostrar_controles_localizaciones");
        if (mostrar_controles_localizaciones == VALOR_SI) {
            $("#control_id_localizacion_grupo_actuadores").show();
        }
        else {
            $("#control_id_localizacion_grupo_actuadores").hide();
        }
    };

    // Habilita la lista de identificadores de localizaciones de un grupo de actuadores
    var funcion_habilita_lista_ids_localizaciones_grupo_actuadores = function() {
        if ($("select#id_localizacion_grupo_actuadores option").length <= 1) {
            $("#id_localizacion_grupo_actuadores").prop('disabled', 'disabled');
        }
        else {
            $("#id_localizacion_grupo_actuadores").prop('disabled', false);
        }
        $("#id_localizacion_grupo_actuadores").trigger("chosen:updated");
    };

    // Habilita la lista de identificadores de programaciones de un actuador
    var funcion_habilita_lista_ids_programaciones_grupo_actuadores = function() {
        if ($("select#id_programacion_grupo_actuadores option").length <= 1) {
            $("#id_programacion_grupo_actuadores").prop('disabled', 'disabled');
        }
        else {
            $("#id_programacion_grupo_actuadores").prop('disabled', false);
        }
        $("#id_programacion_grupo_actuadores").trigger("chosen:updated");
    };

    // Se habilitan y muestran controles dependientes de la clase de grupo de actuadores
    var funcion_habilita_muestra_controles_clase_grupo_actuadores = function() {
        // Muestra los controles de localización del grupo de actuadores
        funcion_muestra_controles_localizacion_grupo_actuadores();

        // Habilita el identificador de localización del grupo de actuadores
        funcion_habilita_lista_ids_localizaciones_grupo_actuadores();

        // Habilita el identificador de programación del grupo de actuadores
        funcion_habilita_lista_ids_programaciones_grupo_actuadores();
    };
    $("#clase_grupo_actuadores").show(funcion_habilita_muestra_controles_clase_grupo_actuadores);

    // Si se modifica la clase de grupo de actuadores:
    // - Hay que actualizar la lista de programaciones correspondientes a la nueva clase de actuador
    var funcion_realiza_acciones_clase_grupo_actuadores_modificada = function() {
        $.post("./src/lib/modulos/Nodos/administracion/dame_lista_programaciones_clase_actuador.php", {
			clase_actuador: $("#clase_grupo_actuadores").val()
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_programacion_grupo_actuadores").html(resultado.html);

            // Habilita y muestra controles según la clase de grupo de actuadores
            funcion_habilita_muestra_controles_clase_grupo_actuadores();
		});
    };
    $("#clase_grupo_actuadores").change(funcion_realiza_acciones_clase_grupo_actuadores_modificada);

    // Recarga de las acciones predefinidas de una clase de actuador
    var funcion_recarga_acciones_predefinidas = function() {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/dame_botones_opcion_acciones_predefinidas.php", {
            clase_actuador: $("#clase_actuador_accion_regla").val(),
            contenido_accion: ""
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#botones_opcion_acciones_predefinidas_regla").html(resultado.html);
        });
    };
    $("#clase_actuador_accion_regla").change(funcion_recarga_acciones_predefinidas);

    // Contador de caracteres de contenido de mensaje
    $("#contenido_mensaje").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_actuadores_interfaces_actuadores = function() {
    // Desactivación de eventos anteriores
    $("#clase_interfaz_actuador").off();

    // Se muestran los controles correspondientes a cada clase de interfaz
    var funcion_realiza_acciones_clase_interfaz_actuador_modificada = function() {
        var tipo_actuador = $("#tipo_actuador").val();
        var clase_interfaz_actuador = $("#clase_interfaz_actuador").val();

        $.post("./src/lib/modulos/Nodos/administracion/dame_controles_clase_interfaz_actuador.php", {
            tipo_actuador: tipo_actuador,
			clase_interfaz: clase_interfaz_actuador
		},
		function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_controles_clase_interfaz_actuador").html(resultado.html);

            // Se establecen los eventos de los controles de interfaces de actuadores
            establece_eventos_ventanas_modales_actuadores_controles_interfaces_actuadores();
		});
    };
    $("#clase_interfaz_actuador").change(funcion_realiza_acciones_clase_interfaz_actuador_modificada);
};


establece_eventos_ventanas_modales_actuadores_controles_interfaces_actuadores = function() {
    // Botones de ayuda
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_controles_interfaces_actuadores);
};



establece_eventos_ventanas_modales_actuadores_grupos_actuadores = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_grupo_actuadores").off();

    // Contador de caracteres de descripción de grupo de actuadores
    $("#descripcion_grupo_actuadores").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_actuadores_programaciones = function() {
    // Desactivación de eventos anteriores
    $("#tipo_excepcion_programacion").off();

    // Muestra los controles correspondientes dependiendo del tipo de excepción de la programación
    var funcion_habilita_muestra_controles_tipo_excepcion_programacion = function() {
        var tipo_excepcion_programacion = $("#tipo_excepcion_programacion").val();
        switch (tipo_excepcion_programacion) {
            case TIPO_EXCEPCION_PROGRAMACION_FECHA: {
                $("#contenedor_fecha_excepcion_programacion").show();
                $("#contenedor_rango_fechas_excepcion_programacion").hide();
                $("#contenedor_dia_anyo_excepcion_programacion").hide();
                $("#contenedor_rango_dias_anyo_excepcion_programacion").hide();
                $("#contenedor_dia_semana_excepcion_programacion").hide();
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS: {
                $("#contenedor_fecha_excepcion_programacion").hide();
                $("#contenedor_rango_fechas_excepcion_programacion").show();
                $("#contenedor_dia_anyo_excepcion_programacion").hide();
                $("#contenedor_rango_dias_anyo_excepcion_programacion").hide();
                $("#contenedor_dia_semana_excepcion_programacion").hide();
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO: {
                $("#contenedor_fecha_excepcion_programacion").hide();
                $("#contenedor_rango_fechas_excepcion_programacion").hide();
                $("#contenedor_dia_anyo_excepcion_programacion").show();
                $("#contenedor_rango_dias_anyo_excepcion_programacion").hide();
                $("#contenedor_dia_semana_excepcion_programacion").hide();
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO: {
                $("#contenedor_fecha_excepcion_programacion").hide();
                $("#contenedor_rango_fechas_excepcion_programacion").hide();
                $("#contenedor_dia_anyo_excepcion_programacion").hide();
                $("#contenedor_rango_dias_anyo_excepcion_programacion").show();
                $("#contenedor_dia_semana_excepcion_programacion").hide();
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA: {
                $("#contenedor_fecha_excepcion_programacion").hide();
                $("#contenedor_rango_fechas_excepcion_programacion").hide();
                $("#contenedor_dia_anyo_excepcion_programacion").hide();
                $("#contenedor_rango_dias_anyo_excepcion_programacion").hide();
                $("#contenedor_dia_semana_excepcion_programacion").show();
                break;
            }
        }
    };
    $("#tipo_excepcion_programacion").show(funcion_habilita_muestra_controles_tipo_excepcion_programacion);
    $("#tipo_excepcion_programacion").change(funcion_habilita_muestra_controles_tipo_excepcion_programacion);
};


establece_eventos_ventanas_modales_actuadores_reglas = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_regla").off();
    $("#tipo_regla").off();
    $("#causa_suceso_regla").off();
    $("#id_causa_suceso_regla").off();
    $("#origen_suceso_regla").off();
    $("#id_origen_suceso_regla").off();
    $("#modo_activacion_suceso_regla").off();
    $("#tabs-administracion-accion-reglas").off();
    $("#clase_actuador_accion_regla").off();
    $("#destino_accion_regla").off();

    // Contador de caracteres de descripción de regla
    $("#descripcion_regla").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Se habilitan y muestran controles dependientes del tipo de regla
    var funcion_habilita_muestra_controles_tipo_regla = function() {
        var tipo_regla = $("#tipo_regla").val();
        switch (tipo_regla) {
            case TIPO_REGLA_UNICA: {
                $("#modo_activacion_regla").removeAttr('disabled');
                break;
            }
            case TIPO_REGLA_MULTIPLE: {
                $("#modo_activacion_regla").val(MODO_ACTIVACION_REGLA_CUALQUIER_SUCESO);
                $("#modo_activacion_regla").attr('disabled', 'disabled');
                break;
            }
        }
    };
    $("#tipo_regla").show(funcion_habilita_muestra_controles_tipo_regla);
    $("#tipo_regla").change(funcion_habilita_muestra_controles_tipo_regla);

    // Se habilitan y muestran controles dependientes del origen del suceso de una regla
    var funcion_habilita_muestra_controles_origen_suceso_regla = function() {
        var origen_suceso_regla = $("#origen_suceso_regla").val();
        switch (origen_suceso_regla) {
            case ORIGEN_SUCESO_SENSOR: {
                $("#numero_activaciones_suceso_regla").val("1");
                $("#numero_activaciones_suceso_regla").prop('disabled', 'disabled');
                break;
            }
            case ORIGEN_SUCESO_GRUPO_SENSORES: {
                $("#numero_activaciones_suceso_regla").prop('disabled', false);
                break;
            }
        }
    };

    // Recarga de los números de activaciones del suceso de una regla
    var funcion_recarga_numero_activaciones_suceso_regla = function() {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/dame_lista_numero_activaciones_suceso.php", {
            origen: $("#origen_suceso_regla").val(),
            id_origen: $("#id_origen_suceso_regla").val(),
            numero_activaciones: $("#numero_activaciones_suceso_regla").val()
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#numero_activaciones_suceso_regla").html(resultado.html);

            // Habilitación de controles
            funcion_habilita_muestra_controles_origen_suceso_regla();
        });
    };

    // Recarga de los identificadores de orígenes de suceso de una regla
    var funcion_recarga_ids_origenes_suceso_regla = function() {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/dame_lista_ids_origenes_suceso.php", {
            causa: $("#causa_suceso_regla").val(),
            id_causa: $("#id_causa_suceso_regla").val(),
            origen: $("#origen_suceso_regla").val(),
            id_origen: $("#id_origen_suceso_regla").val()
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_origen_suceso_regla").html(resultado.html);
            $("#id_origen_suceso_regla").trigger("chosen:updated");

            // Número de activaciones
            funcion_recarga_numero_activaciones_suceso_regla();
        });
    };

    // Recarga de orígenes de un suceso de una regla
    var funcion_recarga_origenes_suceso_regla = function() {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/dame_lista_origenes_suceso.php", {
            causa: $("#causa_suceso_regla").val(),
            id_causa: $("#id_causa_suceso_regla").val(),
            origen: ID_NINGUNO
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#origen_suceso_regla").html(resultado.html);

            // Se recargan los identificadores de los orígenes del suceso
            funcion_recarga_ids_origenes_suceso_regla();
        });
    };

    // Habilita y muestra los controles dependientes de la causa del suceso de una regla
    var funcion_habilita_muestra_controles_causa_suceso_regla = function() {
        var causa = $("#causa_suceso_regla").val();

        // Habilitación de controles
        switch (causa) {
            case ID_NINGUNO.toString(): {
                $("#id_causa_suceso_regla").prop('disabled', 'disabled');
                $("#origen_suceso_regla").prop('disabled', 'disabled');
                $("#id_origen_suceso_regla").prop('disabled', 'disabled');
                break;
            }
            case CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR: {
                $("#id_causa_suceso_regla").prop('disabled', 'disabled');
                $("#origen_suceso_regla").prop('disabled', false);
                $("#id_origen_suceso_regla").prop('disabled', false);
                break;
            }
            default: {
                $("#id_causa_suceso_regla").prop('disabled', false);
                $("#origen_suceso_regla").prop('disabled', 'disabled');
                $("#id_origen_suceso_regla").prop('disabled', 'disabled');
                break;
            }
        }
        $("#id_causa_suceso_regla").trigger("chosen:updated");
        $("#id_origen_suceso_regla").trigger("chosen:updated");
    };
    $("#causa_suceso_regla").show(funcion_habilita_muestra_controles_causa_suceso_regla());

    // Realiza acciones al cambiar la causa de un suceso de una regla
    var funcion_realiza_acciones_causa_suceso_regla_modificada = function() {
        var causa = $("#causa_suceso_regla").val();

        // Se recargan los identificadores de causa del suceso
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/dame_lista_ids_causas_suceso.php", {
			causa: causa,
            id_causa: $("#id_causa_suceso_regla").val(),
            id_regla: $("#parametros_ventana_anyadir_modificar_suceso_regla").attr("id_regla")
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_causa_suceso_regla").html(resultado.html);
            $("#id_causa_suceso_regla").trigger("chosen:updated");

            // Se establece el identificador de origen a ninguno
            $("#id_origen_suceso_regla").val(ID_NINGUNO);
            $("#id_origen_suceso_regla").trigger("chosen:updated");

            // Se recargan los orígenes del suceso
            funcion_recarga_origenes_suceso_regla();

            // Se habilitan los controles de causas de suceso
            funcion_habilita_muestra_controles_causa_suceso_regla();
        });
    };
    $("#causa_suceso_regla").change(funcion_realiza_acciones_causa_suceso_regla_modificada);

    // Si se modifica la causa en la ventana de añadir/modificar suceso, hay que recargar los orígenes
    $("#id_causa_suceso_regla").change(funcion_recarga_origenes_suceso_regla);

    // Si se modifica el tipo de origen en la ventana de añadir/modificar suceso, hay que recargar los identificadore de los orígenes
    $("#origen_suceso_regla").change(funcion_recarga_ids_origenes_suceso_regla);

    // Habilitación de número de activaciones de suceso de regla
    $("#id_origen_suceso_regla").show(funcion_habilita_muestra_controles_origen_suceso_regla);

    // Si se modifica el origen en la ventana de añadir/modificar suceso, hay que recargar el número de activaciones
    $("#id_origen_suceso_regla").change(funcion_recarga_numero_activaciones_suceso_regla);

    // Se habilitan y muestran controles dependientes del modo de activación de suceso de una regla
    var funcion_habilita_muestra_controles_modo_activacion_suceso_regla = function() {
        var modo_activacion_suceso_regla = $("#modo_activacion_suceso_regla").val();
        switch (modo_activacion_suceso_regla) {
            case MODO_ACTIVACION_SUCESO_NORMAL: {
                $("#numero_horas_activacion_suceso_regla").removeClass('TLNT_input_mandatory TLNT_input_float');
                $("#numero_repeticiones_activacion_suceso_regla").removeClass('TLNT_input_mandatory TLNT_input_integer');
                $("#control_numero_horas_activacion_suceso_regla").hide();
                $("#control_periodo_tiempo_activacion_suceso_regla").hide();
                $("#control_numero_repeticiones_activacion_suceso_regla").hide();
                break;
            }
            case MODO_ACTIVACION_SUCESO_TIEMPO_MINIMO: {
                $("#numero_horas_activacion_suceso_regla").addClass('TLNT_input_mandatory TLNT_input_float');
                $("#numero_repeticiones_activacion_suceso_regla").removeClass('TLNT_input_mandatory TLNT_input_integer');
                $("#control_numero_horas_activacion_suceso_regla").show();
                $("#control_periodo_tiempo_activacion_suceso_regla").hide();
                $("#control_numero_repeticiones_activacion_suceso_regla").hide();
                break;
            }
            case MODO_ACTIVACION_SUCESO_REPETICIONES_MINIMAS_PERIODO_TIEMPO: {
                $("#numero_horas_activacion_suceso_regla").removeClass('TLNT_input_mandatory TLNT_input_float');
                $("#numero_repeticiones_activacion_suceso_regla").addClass('TLNT_input_mandatory TLNT_input_integer');
                $("#control_numero_horas_activacion_suceso_regla").hide();
                $("#control_periodo_tiempo_activacion_suceso_regla").show();
                $("#control_numero_repeticiones_activacion_suceso_regla").show();
                break;
            }
        }
    };
    $("#modo_activacion_suceso_regla").show(funcion_habilita_muestra_controles_modo_activacion_suceso_regla);
    $("#modo_activacion_suceso_regla").change(funcion_habilita_muestra_controles_modo_activacion_suceso_regla);

    // Habilita y muestra los controles dependientes de la clase de actuador de la acción de la regla
    var funcion_habilita_muestra_controles_clase_actuador_accion_regla = function() {
        var clase_actuador = $("#clase_actuador_accion_regla").val();
        switch (clase_actuador) {
            case CLASE_ACTUADOR_MENSAJE:
            case CLASE_ACTUADOR_INTERRUPTOR:
            case CLASE_ACTUADOR_TELEPOSTE:
            case CLASE_ACTUADOR_LUZ_GRADUAL_4:
            case CLASE_ACTUADOR_GENERICA: {
                $("#titulo-tab-accion").show();
                break;
            }
            default: {
                $("#titulo-tab-accion").hide();
                break;
            }
        }
    };
    var funcion_recarga_controles_clase_actuador_accion_regla = function() {
        var clase_actuador = $("#clase_actuador_accion_regla").val();
        var destino = $("#destino_accion_regla").val();
        var id_destino = $("#id_destino_accion_regla").val();
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/dame_controles_accion.php", {
			clase_actuador: clase_actuador,
            destino: destino,
            id_destino: id_destino,
            origen_controles_accion: ORIGEN_CONTROLES_ACCION_REGLA
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#controles_accion_regla").html(resultado.html);

            // Nota: Se 'restablecen' los eventos de los botones para los botones de ayuda
            TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_ventanas_modales_actuadores);

            // Se habilitan los controles de acciones
            funcion_habilita_muestra_controles_clase_actuador_accion_regla();

            // Eventos específicos de controles de acciones de tipos de actuador
            switch (clase_actuador) {
                case CLASE_ACTUADOR_MENSAJE: {
                    $("#contenido_mensaje").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
                    break;
                }
            }
		});
    };
    $("#clase_actuador_accion_regla").show(funcion_habilita_muestra_controles_clase_actuador_accion_regla);
    $("#clase_actuador_accion_regla").change(funcion_recarga_controles_clase_actuador_accion_regla);

    // Habilita la lista de identificadores de destinos de una accion
    var funcion_habilita_lista_ids_destinos_accion = function() {
        if ($("select#id_destino_accion_regla option").length <= 1) {
            $("#id_destino_accion_regla").prop('disabled', 'disabled');
        }
        else {
            $("#id_destino_accion_regla").prop('disabled', false);
        }
        $("#id_destino_accion_regla").trigger("chosen:updated");
    };
    $("#id_destino_accion_regla").show(funcion_habilita_lista_ids_destinos_accion);

    // Recarga la lista de identificadores de destinos de una accion
    var funcion_recarga_lista_ids_destinos_accion = function() {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/dame_lista_ids_destinos_accion.php", {
            clase_actuador: $("#clase_actuador_accion_regla").val(),
            destino: $("#destino_accion_regla").val(),
            id_destino: $("#id_destino_accion_regla").val()
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_destino_accion_regla").html(resultado.html);
            $("#id_destino_accion_regla").trigger("chosen:updated");
            funcion_habilita_lista_ids_destinos_accion();
        });
    };
    $("#clase_actuador_accion_regla").change(funcion_recarga_lista_ids_destinos_accion);
    $("#destino_accion_regla").change(funcion_recarga_lista_ids_destinos_accion);

    // Recarga de las acciones predefinidas de una clase de actuador
    var funcion_recarga_acciones_predefinidas = function() {
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/dame_botones_opcion_acciones_predefinidas.php", {
            clase_actuador: $("#clase_actuador_accion_regla").val(),
            contenido_accion: ""
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#botones_opcion_acciones_predefinidas_regla").html(resultado.html);
        });
    };
    $("#clase_actuador_accion_regla").change(funcion_recarga_acciones_predefinidas);
};
