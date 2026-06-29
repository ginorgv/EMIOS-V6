// Eventos de los botones (por funcionalidad)
TLNT.Navegacion.botones_secciones_proyectos = [
    // Proyectos
    {	selector: '#boton_proyectos_filtro_proyectos_tabla',
		funcion: 	boton_proyectos_filtro_proyectos_tabla
	},
    // Líneas base
    {	selector: '#boton_proyectos_filtro_lineas_base_tabla',
		funcion: 	boton_proyectos_filtro_lineas_base_tabla
	},
    {	selector: '#boton_proyectos_simulador_linea_base_ver_informe',
		funcion: 	boton_proyectos_simulador_linea_base_ver_informe
	},
    {	selector: '#boton_proyectos_simulador_linea_base_generar_pdf',
		funcion: 	boton_proyectos_simulador_linea_base_generar_pdf
	},
    {	selector: '#boton_proyectos_simulador_linea_base_anyadir_informe_automatico',
		funcion: 	boton_proyectos_simulador_linea_base_anyadir_informe_automatico
	},
    // Información
    {	selector: '#boton_proyectos_informacion_proyecto_ver_informe',
		funcion: 	boton_proyectos_informacion_proyecto_ver_informe
	},
    {	selector: '#boton_proyectos_informacion_proyecto_generar_pdf',
		funcion: 	boton_proyectos_informacion_proyecto_generar_pdf
	},
    {	selector: '#boton_proyectos_informacion_proyecto_anyadir_informe_automatico',
		funcion: 	boton_proyectos_informacion_proyecto_anyadir_informe_automatico
	}
];


TLNT.Navegacion.botones_tablas_datos_proyectos = [
    // Proyectos
    {	selector: '.boton_proyectos_mostrar_ventana_anyadir_modificar_proyecto',
		funcion: 	boton_proyectos_mostrar_ventana_anyadir_modificar_proyecto
	},
    {	selector: '.boton_proyectos_actualizar_tabla_proyectos',
		funcion: 	boton_proyectos_actualizar_tabla_proyectos
	},
    {	selector: '.boton_proyectos_eliminar_proyecto',
		funcion: 	boton_proyectos_eliminar_proyecto
	},
    // Líneas base
    {	selector: '.boton_proyectos_mostrar_ventana_anyadir_modificar_linea_base',
		funcion: 	boton_proyectos_mostrar_ventana_anyadir_modificar_linea_base
	},
    {	selector: '.boton_proyectos_actualizar_tabla_lineas_base',
		funcion: 	boton_proyectos_actualizar_tabla_lineas_base
	},
    {	selector: '.boton_proyectos_eliminar_linea_base',
		funcion: 	boton_proyectos_eliminar_linea_base
	},
    // Ayuda (tablas)
    {	selector: '.boton_proyectos_ayuda_tabla_proyectos',
		funcion: 	boton_proyectos_ayuda_tabla_proyectos
	},
    {	selector: '.boton_proyectos_ayuda_tabla_lineas_base',
		funcion: 	boton_proyectos_ayuda_tabla_lineas_base
	}
];


TLNT.Navegacion.botones_detalles_tablas_datos_proyectos = [
    // Proyectos
    {	selector: '.boton_proyectos_actualizar_proyecto',
		funcion: 	boton_proyectos_actualizar_proyecto
	},
    {	selector: '.boton_proyectos_refrescar_tabla_proyecto',
		funcion: 	boton_proyectos_refrescar_tabla_proyecto
	},
    // Valores adicionales de proyectos
    {	selector: '.boton_proyectos_mostrar_ventana_anyadir_modificar_valor_adicional_proyecto',
		funcion: 	boton_proyectos_mostrar_ventana_anyadir_modificar_valor_adicional_proyecto
	},
    {	selector: '.boton_proyectos_actualizar_tabla_valores_adicionales_proyecto',
		funcion: 	boton_proyectos_actualizar_tabla_valores_adicionales_proyecto
	},
    {	selector: '.boton_proyectos_eliminar_valor_adicional_proyecto',
		funcion: 	boton_proyectos_eliminar_valor_adicional_proyecto
	},
    // Líneas base
    {	selector: '.boton_proyectos_refrescar_tabla_linea_base',
		funcion: 	boton_proyectos_refrescar_tabla_linea_base
	},
    // Variables de líneas base
    {	selector: '.boton_proyectos_mostrar_ventana_anyadir_modificar_variable_linea_base',
		funcion: 	boton_proyectos_mostrar_ventana_anyadir_modificar_variable_linea_base
	},
    {	selector: '.boton_proyectos_actualizar_tabla_variables_linea_base',
		funcion: 	boton_proyectos_actualizar_tabla_variables_linea_base
	},
    {	selector: '.boton_proyectos_eliminar_variable_linea_base',
		funcion: 	boton_proyectos_eliminar_variable_linea_base
	},
    // Excepciones de líneas base
    {	selector: '.boton_proyectos_mostrar_ventana_anyadir_modificar_excepcion_linea_base',
		funcion: 	boton_proyectos_mostrar_ventana_anyadir_modificar_excepcion_linea_base
	},
    {	selector: '.boton_proyectos_actualizar_tabla_excepciones_linea_base',
		funcion: 	boton_proyectos_actualizar_tabla_excepciones_linea_base
	},
    {	selector: '.boton_proyectos_eliminar_excepcion_linea_base',
		funcion: 	boton_proyectos_eliminar_excepcion_linea_base
	}
];


TLNT.Navegacion.botones_ventanas_modales_proyectos = [
    // Proyectos
	{	selector: '.boton_proyectos_anyadir_modificar_proyecto',
		funcion: 	boton_proyectos_anyadir_modificar_proyecto
	},
    // Valores adicionales de proyectos
	{	selector: '.boton_proyectos_anyadir_modificar_valor_adicional_proyecto',
		funcion: 	boton_proyectos_anyadir_modificar_valor_adicional_proyecto
	},
    // Líneas base
	{	selector: '.boton_proyectos_anyadir_modificar_linea_base',
		funcion: 	boton_proyectos_anyadir_modificar_linea_base
	},
    // Ayuda (líneas base)
    {	selector: '#boton_ayuda_exclusion_fechas_linea_base',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_proyectos_ayuda_funcion_valores_linea_base',
		funcion: 	boton_proyectos_ayuda_funcion_valores_linea_base
	},
    {	selector: '#boton_proyectos_ayuda_valores_prueba_funcion_valores_linea_base',
		funcion: 	boton_proyectos_ayuda_valores_prueba_funcion_valores_linea_base
	},
    // Variables de líneas base
	{	selector: '.boton_proyectos_anyadir_modificar_variable_linea_base',
		funcion: 	boton_proyectos_anyadir_modificar_variable_linea_base
	},
    // Excepciones de líneas base
	{	selector: '.boton_proyectos_anyadir_modificar_excepcion_linea_base',
		funcion: 	boton_proyectos_anyadir_modificar_excepcion_linea_base
	},
    // Ayuda (valores adicionales de proyectos)
    {	selector: '#boton_proyectos_ayuda_fecha_inicio_valor_adicional_proyecto',
		funcion: 	boton_ayuda_fecha
	},
    {	selector: '#boton_proyectos_ayuda_fecha_fin_valor_adicional_proyecto',
		funcion: 	boton_ayuda_fecha
	},
    // Ayuda (excepciones de líneas base)
    {	selector: '#boton_ayuda_inclusion_fechas_excepcion_linea_base',
		funcion: 	boton_ayuda_fechas
	}
];


//
// Funciones de establecimiento de eventos (por funcionalidad)
//


TLNT.Navegacion.establece_eventos_secciones_proyectos = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_secciones_proyectos);

    establece_eventos_secciones_proyectos_informes();
};


TLNT.Navegacion.establece_eventos_tablas_datos_proyectos = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_proyectos);
};


TLNT.Navegacion.establece_eventos_detalles_tablas_datos_proyectos = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_detalles_tablas_datos_proyectos);
};


TLNT.Navegacion.establece_eventos_ventanas_modales_proyectos = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_ventanas_modales_proyectos);

    establece_eventos_ventanas_modales_proyectos_lineas_base();
};


//
// Funciones auxiliares para establecer las acciones de los controles
//


establece_eventos_secciones_proyectos_informes = function() {
    establece_eventos_secciones_proyectos_informes_lineas_base();
};


establece_eventos_secciones_proyectos_informes_lineas_base = function() {
    $("#id_linea_base_proyectos_simulador_linea_base").off();
    $("#id_proyecto_proyectos_informacion_proyecto").off();

    // Recarga las fechas de inicio y fin de la simulación de línea base
    var funcion_recarga_fechas_inicio_fin_simulacion_linea_base = function() {
        var id_linea_base = $("#id_linea_base_proyectos_simulador_linea_base").val();
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_fechas_inicio_fin_periodo_referencia_linea_base.php", {
            id_linea_base: id_linea_base
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            var fecha_inicio_periodo_referencia = resultado.fecha_inicio_periodo_referencia;
            var fecha_fin_periodo_referencia = resultado.fecha_fin_periodo_referencia;

            TLNT.Navegacion.establece_fecha_control("fecha_inicio_proyectos_simulador_linea_base", fecha_inicio_periodo_referencia);
            TLNT.Navegacion.establece_fecha_control("fecha_fin_proyectos_simulador_linea_base", fecha_fin_periodo_referencia);
            $("#hora_inicio_proyectos_simulador_linea_base").val("00:00");
            $("#hora_fin_proyectos_simulador_linea_base").val("23:59");
        });
    };
    $("#id_linea_base_proyectos_simulador_linea_base").change(funcion_recarga_fechas_inicio_fin_simulacion_linea_base);

    // Recarga las fechas de inicio y fin de la información de proyecto
    var funcion_recarga_fechas_inicio_fin_informacion_proyecto = function() {
        var id_proyecto = $("#id_proyecto_proyectos_informacion_proyecto").val();
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/Informacion/dame_fechas_inicio_fin_proyecto.php", {
            id_proyecto: id_proyecto
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            var fecha_inicio = resultado.fecha_inicio;
            var fecha_fin = resultado.fecha_fin;

            $('#fecha_inicio_proyectos_informacion_proyecto').datepicker("update", new Date(fecha_inicio));
            $('#fecha_fin_proyectos_informacion_proyecto').datepicker("update", new Date(fecha_fin));
        });
    };
    $("#id_proyecto_proyectos_informacion_proyecto").change(funcion_recarga_fechas_inicio_fin_informacion_proyecto);
};


establece_eventos_ventanas_modales_proyectos_lineas_base = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_proyecto").off();
    $("#descripcion_linea_base").off();
    $("#descripcion_excepcion_linea_base").off();
    $("#clase_sensor_proyecto").off();
    $("#id_sensor_proyecto").off();
    $("#campo_proyecto").off();
    $("#intervalo_valores_proyecto").off();
    $("#id_linea_base_proyecto").off();
    $("#intervalo_valores_linea_base").off();
    $("#periodicidad_valor_adicional_proyecto").off();
    $("#tipo_valor_objetivo_proyecto").off();
    $("#clase_sensor_linea_base").off();
    $("#id_sensor_linea_base").off();
    $("#campo_linea_base").off();
    $("#clase_sensor_variable_linea_base").off();
    $("#id_sensor_variable_linea_base").off();
    $("#campo_variable_linea_base").off();
    $("#tipo_linea_base").off();
    $("#funcion_valores_linea_base").off();

    // Contadores de caracteres de descripciones de proyecto, línea base y de excepción de línea base
    $("#descripcion_proyecto").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
    $("#descripcion_linea_base").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
    $("#descripcion_excepcion_linea_base").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Habilitación de sensor
    var funcion_habilita_sensor = function(id_controles) {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_sensores = $("select#id_sensor_" + id_controles + " option").length;
        if (numero_sensores <= 1) {
            $("#id_sensor_" + id_controles).attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#id_sensor_" + id_controles).removeAttr('disabled').trigger("chosen:updated");
        }
    };
    var funcion_habilita_sensor_proyecto = function() {
        funcion_habilita_sensor("proyecto");
    };
    $("#id_sensor_proyecto").show(funcion_habilita_sensor_proyecto);
    var funcion_habilita_sensor_linea_base = function() {
        funcion_habilita_sensor("linea_base");
    };
    $("#id_sensor_linea_base").show(funcion_habilita_sensor_linea_base);
    var funcion_habilita_sensor_variable_linea_base = function() {
        funcion_habilita_sensor("variable_linea_base");
    };
    $("#id_sensor_variable_linea_base").show(funcion_habilita_sensor_variable_linea_base);

    // Recarga de los sensores de una clase de sensor
    var funcion_recarga_sensores_clase_sensor = function(id_controles) {
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

            // Se habilita el sensor
            funcion_habilita_sensor(id_controles);
        });
    };
    var funcion_recarga_sensores_clase_sensor_proyecto = function() {
        funcion_recarga_sensores_clase_sensor("proyecto");
    };
    $("#clase_sensor_proyecto").change(funcion_recarga_sensores_clase_sensor_proyecto);
    var funcion_recarga_sensores_clase_sensor_linea_base = function() {
        funcion_recarga_sensores_clase_sensor("linea_base");
    };
    $("#clase_sensor_linea_base").change(funcion_recarga_sensores_clase_sensor_linea_base);
    var funcion_recarga_sensores_clase_sensor_variable_linea_base = function() {
        funcion_recarga_sensores_clase_sensor("variable_linea_base");
    };
    $("#clase_sensor_variable_linea_base").change(funcion_recarga_sensores_clase_sensor_variable_linea_base);

    // Habilitación de campo de sensor
    var funcion_habilita_campo = function(id_controles) {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_campos = $("select#campo_" + id_controles + " option").length;
        if (numero_campos <= 1) {
            $("#campo_" + id_controles).attr('disabled', true);
        }
        else {
            $("#campo_" + id_controles).removeAttr('disabled');
        }
    };
    var funcion_habilita_campo_proyecto = function() {
        funcion_habilita_campo("proyecto");
    };
    $("#campo_proyecto").show(funcion_habilita_campo_proyecto);
    var funcion_habilita_campo_linea_base = function() {
        funcion_habilita_campo("linea_base");
    };
    $("#campo_linea_base").show(funcion_habilita_campo_linea_base);
    var funcion_habilita_campo_variable_linea_base = function() {
        funcion_habilita_campo("variable_linea_base");
    };
    $("#campo_variable_linea_base").show(funcion_habilita_campo_variable_linea_base);

    // Recarga de los campos de una clase de sensor de las ventanas de administración
    var funcion_recarga_campos_sensor_clase_sensor = function(id_controles, intervalo_valores) {
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_campos_clase_sensor_parametros_extra.php", {
			clase_sensor: $("#clase_sensor_" + id_controles).val(),
            tipo_agrupacion_valores: true,
            intervalo_valores: intervalo_valores,
            campo: $("#campo_" + id_controles).val()
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
        });
    };
    var funcion_recarga_campos_sensor_clase_sensor_proyecto = function() {
        var intervalo_valores = $("#intervalo_valores_proyecto").val();
        funcion_recarga_campos_sensor_clase_sensor("proyecto", intervalo_valores);
    };
    $("#clase_sensor_proyecto").change(funcion_recarga_campos_sensor_clase_sensor_proyecto);
    var funcion_recarga_campos_sensor_clase_sensor_linea_base = function() {
        var intervalo_valores = $("#intervalo_valores_linea_base").val();
        funcion_recarga_campos_sensor_clase_sensor("linea_base", intervalo_valores);
    };
    $("#clase_sensor_linea_base").change(funcion_recarga_campos_sensor_clase_sensor_linea_base);
    $("#intervalo_valores_linea_base").change(funcion_recarga_campos_sensor_clase_sensor_linea_base);

    // Habilitación inicial de lineas base del proyecto
    var funcion_habilita_id_linea_base_proyecto = function() {
        if ($("select#id_linea_base_proyecto option").length <= 1) {
            $("#id_linea_base_proyecto").prop('disabled', 'disabled');
        }
        else {
            $("#id_linea_base_proyecto").prop('disabled', false);
        }
        $("#id_linea_base_proyecto").trigger("chosen:updated");
    };
    $("#id_linea_base_proyecto").show(funcion_habilita_id_linea_base_proyecto);

    // Recarga las líneas base del proyecto
    var funcion_recarga_lineas_base_proyecto = function() {
        var intervalo_valores_proyecto = $("#intervalo_valores_proyecto").val();
        var id_linea_base_proyecto = $("#id_linea_base_proyecto").val();
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_lista_lineas_base_intervalo_valores.php", {
            intervalo_valores: intervalo_valores_proyecto,
            id_linea_base: id_linea_base_proyecto
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_linea_base_proyecto").html(resultado.html);
            $("#id_linea_base_proyecto").trigger("chosen:updated");
            funcion_habilita_id_linea_base_proyecto();
        });
    };

    // Acciones a realizar al modificar el intervalo de valores de proyectos
    var funcion_realiza_acciones_intervalo_valores_proyecto = function() {
        funcion_recarga_campos_sensor_clase_sensor_proyecto();
        funcion_recarga_lineas_base_proyecto();
    };
    $("#intervalo_valores_proyecto").change(funcion_realiza_acciones_intervalo_valores_proyecto);

    // Habilita y muestra los controles dependientes del tipo de la línea base
    var funcion_habilita_muestra_controles_tipo_linea_base = function() {
        $("#titulo-tab-tipo-periodica").hide();
        $("#titulo-tab-tipo-funcional").hide();
        var tipo_linea_base = $("#tipo_linea_base").val();
        var anyadir_linea_base = $("#parametros_ventana_anyadir_modificar_linea_base").attr("anyadir_linea_base");
        switch (tipo_linea_base) {
            case TIPO_LINEA_BASE_PERIODICA: {
                $("#titulo-tab-tipo-periodica").show();
                break;
            }
            case TIPO_LINEA_BASE_FUNCIONAL: {
                $("#titulo-tab-tipo-funcional").show();

                if (anyadir_linea_base == VALOR_SI) {
                    $("#control_funcion_valores_linea_base").hide();
                    $("#control_valores_prueba_funcion_valores_linea_base").hide();
                    $("#funcion_valores_linea_base").removeClass('TLNT_input_mandatory');
                }
                else {
                    var error_estandar = $("#error_estandar_linea_base").val();
                    if (error_estandar == -1) {
                        $("#error_estandar_linea_base").val("");
                    }

                    $("#control_funcion_valores_linea_base").show();
                    $("#control_valores_prueba_funcion_valores_linea_base").show();
                    $("#funcion_valores_linea_base").addClass('TLNT_input_mandatory');
                }
                break;
            }
        }
    };
    $("#tipo_linea_base").show(funcion_habilita_muestra_controles_tipo_linea_base);
    $("#tipo_linea_base").change(funcion_habilita_muestra_controles_tipo_linea_base);

    // Muestra u oculta los controles de fechas de valor adicional de proyecto
    var funcion_habilita_muestra_controles_periodicidad_valor_adicional_proyecto = function() {
        var periodicidad_valor_adicional_proyecto = $("#periodicidad_valor_adicional_proyecto").val();
        switch (periodicidad_valor_adicional_proyecto) {
            case PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_NINGUNA: {
                $("#control_fecha_inicio_valor_adicional_proyecto").hide();
                $("#fecha_inicio_valor_adicional_proyecto").val("");
                $("#control_fecha_fin_valor_adicional_proyecto").hide();
                $("#fecha_fin_valor_adicional_proyecto").val("");
                break;
            }
            case PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_PUNTUAL: {
                $("#control_fecha_inicio_valor_adicional_proyecto").show();
                $("#control_fecha_fin_valor_adicional_proyecto").hide();
                $("#fecha_fin_valor_adicional_proyecto").val("");
                break;
            }
            default: {
                $("#control_fecha_inicio_valor_adicional_proyecto").show();
                $("#control_fecha_fin_valor_adicional_proyecto").show();
                break;
            }
        }
    };
    $("#periodicidad_valor_adicional_proyecto").show(funcion_habilita_muestra_controles_periodicidad_valor_adicional_proyecto);
    $("#periodicidad_valor_adicional_proyecto").change(funcion_habilita_muestra_controles_periodicidad_valor_adicional_proyecto);

    // Muestra u oculta los controles dependientes del tipo de valor de objetivo de un proyecto
    var funcion_habilita_muestra_controles_tipo_valor_objetivo_proyecto = function() {
        var tipo_valor_objetivo_proyecto = $("#tipo_valor_objetivo_proyecto").val();
        switch (tipo_valor_objetivo_proyecto) {
            case TIPO_NINGUNO: {
                $("#control_valor_objetivo_proyecto").hide();
                $("#valor_objetivo_proyecto").removeClass('TLNT_input_mandatory');
                $("#valor_objetivo_proyecto").val("");
                break;
            }
            default: {
                $("#control_valor_objetivo_proyecto").show();
                $("#valor_objetivo_proyecto").addClass('TLNT_input_mandatory');
                break;
            }
        }
    };
    $("#tipo_valor_objetivo_proyecto").show(funcion_habilita_muestra_controles_tipo_valor_objetivo_proyecto);
    $("#tipo_valor_objetivo_proyecto").change(funcion_habilita_muestra_controles_tipo_valor_objetivo_proyecto);

    // Recarga los intervalos de valores de la línea base
    var funcion_recarga_intervalos_valores_linea_base = function() {
        var tipo_linea_base = $("#tipo_linea_base").val();
        var intervalo_valores_linea_base = $("#intervalo_valores_linea_base").val();
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_lista_intervalos_valores_linea_base.php", {
            tipo: tipo_linea_base,
            opciones_extra: OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_NINGUNO,
            intervalo_valores: intervalo_valores_linea_base
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#intervalo_valores_linea_base").html(resultado.html);
            if ($("select#intervalo_valores_linea_base option").length <= 1) {
                $("#intervalo_valores_linea_base").prop('disabled', 'disabled');
            }
            else {
                $("#intervalo_valores_linea_base").prop('disabled', false);
            }
        });
    };

    // Acciones a realizar al modificar el tipo de línea base
    var funcion_realiza_acciones_tipo_linea_base = function() {
        funcion_recarga_intervalos_valores_linea_base();
    };
    $("#tipo_linea_base").change(funcion_realiza_acciones_tipo_linea_base);

    // Recarga de los campos de una clase de sensor con parámetros extra de las ventanas de administración
    var funcion_recarga_campos_sensor_clase_sensor_parametros_extra = function(id_controles, intervalo_valores) {
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_campos_clase_sensor_parametros_extra.php", {
			clase_sensor: $("#clase_sensor_" + id_controles).val(),
            tipo_agrupacion_valores: true,
            intervalo_valores: intervalo_valores,
            campo: $("#campo_" + id_controles).val()
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
        });
    };
    var funcion_recarga_campos_sensor_clase_sensor_variable_linea_base = function() {
        funcion_recarga_campos_sensor_clase_sensor_parametros_extra("variable_linea_base", INTERVALO_VALORES_HORA);
    };
    $("#clase_sensor_variable_linea_base").change(funcion_recarga_campos_sensor_clase_sensor_variable_linea_base);

    var funcion_muestra_control_parametros_extra_campo_linea_base = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("linea_base");
    };
    $("#campo_linea_base").show(funcion_muestra_control_parametros_extra_campo_linea_base);
    $("#campo_linea_base").change(funcion_muestra_control_parametros_extra_campo_linea_base);

    var funcion_muestra_control_parametros_extra_campo_variable_linea_base = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("variable_linea_base");
    };
    $("#campo_variable_linea_base").show(funcion_muestra_control_parametros_extra_campo_variable_linea_base);
    $("#campo_variable_linea_base").change(funcion_muestra_control_parametros_extra_campo_variable_linea_base);

    // Contador de caracteres de función de valores
    $("#funcion_valores_linea_base").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};




