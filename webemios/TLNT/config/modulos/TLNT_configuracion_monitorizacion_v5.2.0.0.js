// Eventos de los botones (por funcionalidad)
TLNT.Navegacion.botones_secciones_monitorizacion = [
    {	selector: '#boton_monitorizacion_filtro_historico_procesado',
		funcion: 	boton_monitorizacion_filtro_historico_procesado
	},
    {	selector: '#boton_monitorizacion_tiempos_ejecucion_procesado_ver_informe',
		funcion: 	boton_monitorizacion_tiempos_ejecucion_procesado_ver_informe
	},
    {	selector: '#boton_monitorizacion_ejecucion_manual_procesado',
		funcion: 	boton_monitorizacion_ejecucion_manual_procesado
	},
    {	selector: '#boton_monitorizacion_eliminar_operaciones_datos_sensores',
		funcion: 	boton_monitorizacion_eliminar_operaciones_datos_sensores
	},
    {	selector: '#boton_monitorizacion_pausar_procesado',
		funcion: 	boton_monitorizacion_pausar_procesado
	},
    {	selector: '#boton_monitorizacion_reanudar_procesado',
		funcion: 	boton_monitorizacion_reanudar_procesado
	},
    {	selector: '#boton_monitorizacion_filtro_alarmas',
		funcion: 	boton_monitorizacion_filtro_alarmas
	},
    {	selector: '#boton_monitorizacion_filtro_acciones_usuario',
		funcion: 	boton_monitorizacion_filtro_acciones_usuario
	},
    {	selector: '#boton_monitorizacion_exportar_acciones_usuario',
		funcion: 	boton_monitorizacion_exportar_acciones_usuario
	}
];


//
// Funciones de establecimiento de eventos (por funcionalidad)
//


TLNT.Navegacion.establece_eventos_secciones_monitorizacion = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_secciones_monitorizacion);

    establece_eventos_secciones_monitorizacion_informes();
};


TLNT.Navegacion.establece_eventos_tablas_datos_monitorizacion = function() {};


TLNT.Navegacion.establece_eventos_detalles_tablas_datos_monitorizacion = function() {};


TLNT.Navegacion.establece_eventos_ventanas_modales_monitorizacion = function() {};


//
// Funciones auxiliares para establecer las acciones de los controles
//


establece_eventos_secciones_monitorizacion_informes = function() {
    establece_eventos_secciones_monitorizacion_informes_procesado();
};


establece_eventos_secciones_monitorizacion_informes_procesado = function() {
    // Desactivación de eventos anteriores
    $("#tipo_ejecucion_procesado_monitorizacion_filtro_historico_procesado").off();
    $("#clase_sensor_monitorizacion_filtro_historico_procesado").off();
    $("#tipo_sensor_monitorizacion_filtro_historico_procesado").off();
    $("#tipo_ejecucion_procesado_monitorizacion_tiempos_ejecucion_procesado").off();
    $("#clase_sensor_monitorizacion_tiempos_ejecucion_procesado").off();
    $("#tipo_sensor_monitorizacion_tiempos_ejecucion_procesado").off();

    // Establece la granularidad correspondiente
    var funcion_establece_granularidad_filtro_historico_procesado = function() {
        var granularidad_cuartohoraria = false;
        var tipo_ejecucion_procesado = $("#tipo_ejecucion_procesado_monitorizacion_filtro_historico_procesado").val();
        switch (tipo_ejecucion_procesado) {
            case TIPO_EJECUCION_PROCESADO_TODOS:
            case TIPO_EJECUCION_PROCESADO_RECALCULOS: {
                $("#granularidad_monitorizacion_filtro_historico_procesado").val(GRANULARIDAD_TODAS);
                $("#granularidad_monitorizacion_filtro_historico_procesado").attr('disabled', true);
                break;
            }
            case TIPO_EJECUCION_PROCESADO_NORMAL: {
                var clase_sensor = $("#clase_sensor_monitorizacion_filtro_historico_procesado").val();
                switch (clase_sensor) {
                    case CLASE_TODAS: {
                        granularidad_cuartohoraria = true;
                        break;
                    }
                    default: {
                        var caracteristicas_clase_sensor = dame_caracteristicas_clase_sensor(clase_sensor);
                        granularidad_cuartohoraria = caracteristicas_clase_sensor["granularidad_cuartohoraria"];
                        break;
                    }
                }
                var tipo_sensor = $("#tipo_sensor_monitorizacion_filtro_historico_procesado").val();
                switch (tipo_sensor) {
                    case TIPO_TODOS:
                    case TIPO_SENSOR_PROCESADO: {
                        granularidad_cuartohoraria = true;
                        break;
                    }
                }
                if (granularidad_cuartohoraria == true) {
                    $("#granularidad_monitorizacion_filtro_historico_procesado").val(GRANULARIDAD_TODAS);
                    $("#granularidad_monitorizacion_filtro_historico_procesado").removeAttr('disabled');
                }
                else {
                    $("#granularidad_monitorizacion_filtro_historico_procesado").val(GRANULARIDAD_HORARIA);
                    $("#granularidad_monitorizacion_filtro_historico_procesado").attr('disabled', true);
                }
                break;
            }
        }
    };
    $("#tipo_ejecucion_procesado_monitorizacion_filtro_historico_procesado").change(funcion_establece_granularidad_filtro_historico_procesado);

    // Realiza acciones dependiendo de la clase de sensor seleccionada en filtro de histórico de procesado
    var funcion_realiza_acciones_clase_sensor_filtro_historico_procesado = function() {
        var clase_sensor = $("#clase_sensor_monitorizacion_filtro_historico_procesado").val();
        switch (clase_sensor) {
            case CLASE_TODAS:
            case CLASE_NINGUNA: {
                break;
            }
            default: {
                $("#tipo_sensor_monitorizacion_filtro_historico_procesado").val(TIPO_NINGUNO);
                break;
            }
        }

        // Se establece la granularidad correspondiente
        funcion_establece_granularidad_filtro_historico_procesado();
    };
    $("#clase_sensor_monitorizacion_filtro_historico_procesado").show(funcion_realiza_acciones_clase_sensor_filtro_historico_procesado);
    $("#clase_sensor_monitorizacion_filtro_historico_procesado").change(funcion_realiza_acciones_clase_sensor_filtro_historico_procesado);

    // Realiza acciones dependiendo del tipo de sensor seleccionado en filtro de histórico de procesado
    var funcion_realiza_acciones_tipo_sensor_filtro_historico_procesado = function() {
        var tipo_sensor = $("#tipo_sensor_monitorizacion_filtro_historico_procesado").val();
        switch (tipo_sensor) {
            case TIPO_TODOS:
            case TIPO_NINGUNO: {
                break;
            }
            default: {
                $("#clase_sensor_monitorizacion_filtro_historico_procesado").val(CLASE_NINGUNA);
                break;
            }
        }

        // Se establece la granularidad correspondiente
        funcion_establece_granularidad_filtro_historico_procesado();
    };
    $("#tipo_sensor_monitorizacion_filtro_historico_procesado").show(funcion_realiza_acciones_tipo_sensor_filtro_historico_procesado);
    $("#tipo_sensor_monitorizacion_filtro_historico_procesado").change(funcion_realiza_acciones_tipo_sensor_filtro_historico_procesado);

    // Establece la granularidad correspondiente
    var funcion_establece_granularidad_tiempos_ejecucion_procesado = function() {
        var granularidad_cuartohoraria = false;
        var tipo_ejecucion_procesado = $("#tipo_ejecucion_procesado_monitorizacion_tiempos_ejecucion_procesado").val();
        switch (tipo_ejecucion_procesado) {
            case TIPO_EJECUCION_PROCESADO_TODOS:
            case TIPO_EJECUCION_PROCESADO_RECALCULOS: {
                $("#granularidad_monitorizacion_tiempos_ejecucion_procesado").val(GRANULARIDAD_NINGUNA);
                $("#granularidad_monitorizacion_tiempos_ejecucion_procesado").attr('disabled', true);
                break;
            }
            case TIPO_EJECUCION_PROCESADO_NORMAL: {
                var clase_sensor = $("#clase_sensor_monitorizacion_tiempos_ejecucion_procesado").val();
                switch (clase_sensor) {
                    case CLASE_TODAS: {
                        granularidad_cuartohoraria = true;
                        break;
                    }
                    default: {
                        var caracteristicas_clase_sensor = dame_caracteristicas_clase_sensor(clase_sensor);
                        granularidad_cuartohoraria = caracteristicas_clase_sensor["granularidad_cuartohoraria"];
                        break;
                    }
                }
                var tipo_sensor = $("#tipo_sensor_monitorizacion_tiempos_ejecucion_procesado").val();
                switch (tipo_sensor) {
                    case TIPO_TODOS:
                    case TIPO_SENSOR_PROCESADO: {
                        granularidad_cuartohoraria = true;
                        break;
                    }
                }
                if (granularidad_cuartohoraria == true) {
                    $("#granularidad_monitorizacion_tiempos_ejecucion_procesado").val(GRANULARIDAD_NINGUNA);
                    $("#granularidad_monitorizacion_tiempos_ejecucion_procesado").removeAttr('disabled');
                }
                else {
                    $("#granularidad_monitorizacion_tiempos_ejecucion_procesado").val(GRANULARIDAD_HORARIA);
                    $("#granularidad_monitorizacion_tiempos_ejecucion_procesado").attr('disabled', true);
                }
                break;
            }
        }
    };
    $("#tipo_ejecucion_procesado_monitorizacion_tiempos_ejecucion_procesado").change(funcion_establece_granularidad_tiempos_ejecucion_procesado);

    // Realiza acciones dependiendo de la clase de sensor seleccionada en tiempos de ejecución de procesado
    var funcion_realiza_acciones_clase_sensor_tiempos_ejecucion_procesado = function() {
        var clase_sensor = $("#clase_sensor_monitorizacion_tiempos_ejecucion_procesado").val();
        switch (clase_sensor) {
            case CLASE_NINGUNA: {
                break;
            }
            default: {
                $("#tipo_sensor_monitorizacion_tiempos_ejecucion_procesado").val(TIPO_NINGUNO);
                break;
            }
        }

        // Se establece la granularidad correspondiente
        funcion_establece_granularidad_tiempos_ejecucion_procesado();
    };
    $("#clase_sensor_monitorizacion_tiempos_ejecucion_procesado").show(funcion_realiza_acciones_clase_sensor_tiempos_ejecucion_procesado);
    $("#clase_sensor_monitorizacion_tiempos_ejecucion_procesado").change(funcion_realiza_acciones_clase_sensor_tiempos_ejecucion_procesado);

    // Realiza acciones dependiendo del tipo de sensor seleccionado en tiempos de ejecución de procesado
    var funcion_realiza_acciones_tipo_sensor_tiempos_ejecucion_procesado = function() {
        var tipo_sensor = $("#tipo_sensor_monitorizacion_tiempos_ejecucion_procesado").val();
        switch (tipo_sensor) {
            case TIPO_NINGUNO: {
                break;
            }
            default: {
                $("#clase_sensor_monitorizacion_tiempos_ejecucion_procesado").val(CLASE_NINGUNA);
                break;
            }
        }

        // Se establece la granularidad correspondiente
        funcion_establece_granularidad_tiempos_ejecucion_procesado();
    };
    $("#tipo_sensor_monitorizacion_tiempos_ejecucion_procesado").show(funcion_realiza_acciones_tipo_sensor_tiempos_ejecucion_procesado);
    $("#tipo_sensor_monitorizacion_tiempos_ejecucion_procesado").change(funcion_realiza_acciones_tipo_sensor_tiempos_ejecucion_procesado);

    // Realiza acciones dependiendo de la clase de sensor seleccionada en ejecución manual de procesado
    var funcion_realiza_acciones_clase_sensor_ejecucion_manual_procesado = function() {
        var clase_sensor = $("#clase_sensor_monitorizacion_ejecucion_manual_procesado").val();
        switch (clase_sensor) {
            case CLASE_NINGUNA:
            case CLASE_TODAS: {
                break;
            }
            default: {
                $("#tipo_sensor_monitorizacion_ejecucion_manual_procesado").val(TIPO_NINGUNO);
                break;
            }
        }
    };
    $("#clase_sensor_monitorizacion_ejecucion_manual_procesado").show(funcion_realiza_acciones_clase_sensor_ejecucion_manual_procesado);
    $("#clase_sensor_monitorizacion_ejecucion_manual_procesado").change(funcion_realiza_acciones_clase_sensor_ejecucion_manual_procesado);

    // Realiza acciones dependiendo del tipo de sensor seleccionado en ejecución manual de procesado
    var funcion_realiza_acciones_tipo_sensor_ejecucion_manual_procesado = function() {
        var tipo_sensor = $("#tipo_sensor_monitorizacion_ejecucion_manual_procesado").val();
        switch (tipo_sensor) {
            case TIPO_NINGUNO:
            case TIPO_TODOS: {
                break;
            }
            default: {
                $("#clase_sensor_monitorizacion_ejecucion_manual_procesado").val(CLASE_NINGUNA);
                break;
            }
        }
    };
    $("#tipo_sensor_monitorizacion_ejecucion_manual_procesado").show(funcion_realiza_acciones_tipo_sensor_ejecucion_manual_procesado);
    $("#tipo_sensor_monitorizacion_ejecucion_manual_procesado").change(funcion_realiza_acciones_tipo_sensor_ejecucion_manual_procesado);
};