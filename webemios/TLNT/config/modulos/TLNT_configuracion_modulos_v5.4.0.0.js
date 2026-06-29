// Acciones cuando se visualizan controles
TLNT.Navegacion.acciones_visualizacion_controles_modulos = [
    // Mapas
	{	selector: '#mapa_seccion',
		funcion: 	mapa_seccion_visible
	}
];


// Eventos de los botones (por funcionalidad)
TLNT.Navegacion.botones_secciones_modulos = [
    // Operaciones de datos de sensores
    {	selector: '#boton_filtro_historico_importaciones_valores_sensores',
		funcion: 	boton_filtro_historico_importaciones_valores_sensores
	},
    // Comentarios
    {	selector: '.boton_mostrar_ventana_anyadir_comentarios',
		funcion: 	boton_mostrar_ventana_anyadir_comentarios
	},
    // Mapa
    {	selector: '#boton_etiquetas_mapa',
		funcion: 	boton_etiquetas_mapa
	},
    {	selector: '#boton_centrar_mapa',
		funcion: 	boton_centrar_mapa
	},
    {	selector: '#boton_actualizar_mapa',
		funcion: 	boton_actualizar_mapa
	},
    {	selector: '#boton_actualizacion_periodica_mapa',
		funcion: 	boton_actualizacion_periodica_mapa
	},
    // Selección de localización actual
    {	selector: '#boton_seleccion_localizacion_actual',
		funcion: 	boton_seleccion_localizacion_actual
    },
    // Informes automáticos
    {	selector: '#boton_filtro_informes_automaticos_tabla',
		funcion: 	boton_filtro_informes_automaticos_tabla
	}
];


TLNT.Navegacion.botones_tablas_datos_modulos = [
    // Tablas de nodos
	{	selector: '.boton_mostrar_ventana_anyadir_modificar_nodo',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_nodo
	},
    {	selector: '.boton_mostrar_ventana_modificar_red_parcial',
		funcion: 	boton_mostrar_ventana_modificar_red_parcial
	},
	{	selector: '.boton_actualizar_tabla_nodos',
		funcion: 	boton_actualizar_tabla_nodos
	},
    {	selector: '.boton_actualizacion_periodica_tabla_nodos',
		funcion: 	boton_actualizacion_periodica_tabla_nodos
	},
	{	selector: '.boton_eliminar_nodo',
		funcion: 	boton_eliminar_nodo
	},
    // Tabla de comentarios
    {	selector: '.boton_mostrar_ventana_anyadir_modificar_comentario',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_comentario
	},
    {	selector: '.boton_eliminar_comentario',
		funcion: 	boton_eliminar_comentario
	},
    // Widgets
    {   selector:   '#tab-pestanya-widgets-anyadir-pestanya',
        funcion:    boton_mostrar_ventana_anyadir_modificar_pestanya_widgets
    },
    {	selector: '.boton_actualizar_cuadricula_widgets',
		funcion: 	boton_actualizar_cuadricula_widgets
	},
    {	selector: '.boton_actualizacion_periodica_cuadricula_widgets',
		funcion: 	boton_actualizacion_periodica_cuadricula_widgets
	},
    {	selector: '.boton_mostrar_ventana_anyadir_modificar_pestanya_widgets',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_pestanya_widgets
	},
    {   selector: '.boton_eliminar_pestanya_widgets',
        funcion:    boton_eliminar_pestanya_widgets
    },
    {   selector: '.boton_actualizar_widget',
        funcion: 	boton_actualizar_widget
    },
    {	selector: '.boton_mostrar_ventana_anyadir_modificar_widget',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_widget
	},
    {   selector: '.boton_eliminar_widget',
        funcion: 	boton_eliminar_widget
    },
    // Administración de informes automáticos
    {	selector: '.boton_actualizar_tabla_informes_automaticos',
		funcion: 	boton_actualizar_tabla_informes_automaticos
	},
    {	selector: '.boton_mostrar_ventana_modificar_informe_automatico',
		funcion: 	boton_mostrar_ventana_modificar_informe_automatico
	},
    {	selector: '.boton_eliminar_informe_automatico',
		funcion: 	boton_eliminar_informe_automatico
	},
    // Operaciones de datos de sensores
    {	selector: '.boton_actualizar_tabla_operaciones_datos_sensores',
		funcion:    boton_actualizar_tabla_operaciones_datos_sensores
	},
    {	selector: '.boton_actualizacion_periodica_tabla_operaciones_datos_sensores',
		funcion: 	boton_actualizacion_periodica_tabla_operaciones_datos_sensores
	},
    {	selector: '.boton_eliminar_importacion_valores_sensor_pendiente',
		funcion: 	boton_eliminar_importacion_valores_sensor_pendiente
	},
    // Ayuda (tablas)
    {	selector: '.boton_ayuda_tabla_comentarios',
		funcion: 	boton_ayuda_tabla_comentarios
	}
];


TLNT.Navegacion.botones_detalles_tablas_datos_modulos = [
    // Nodos
	{	selector: '.boton_refrescar_tabla_nodo',
		funcion: 	boton_refrescar_tabla_nodo
	},
    // Tablas de hijos de sensores (virtuales y de procesado)
    {	selector: '.boton_mostrar_ventana_anyadir_modificar_hijo_sensor',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_hijo_sensor
	},
    {	selector: '.boton_actualizar_tabla_hijos_sensor',
		funcion: 	boton_actualizar_tabla_hijos_sensor
	},
    {	selector: '.boton_eliminar_hijo_sensor',
		funcion: 	boton_eliminar_hijo_sensor
	},
    // Observaciones de acciones de usuario
    {	selector: '.boton_mostrar_ventana_modificar_observaciones_accion_usuario',
		funcion: 	boton_mostrar_ventana_modificar_observaciones_accion_usuario
	},
    // Tablas de rangos de días
    {	selector: '.boton_mostrar_ventana_anyadir_modificar_rango_dias',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_rango_dias
	},
    {	selector: '.boton_actualizar_tabla_rangos_dias',
		funcion: 	boton_actualizar_tabla_rangos_dias
	},
	{	selector: '.boton_eliminar_rango_dias',
		funcion: 	boton_eliminar_rango_dias
	},
	// Tablas de periodos
    {	selector: '.boton_mostrar_ventana_anyadir_modificar_periodo',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_periodo
	},
    {	selector: '.boton_actualizar_tabla_periodos',
		funcion: 	boton_actualizar_tabla_periodos
	},
	{	selector: '.boton_eliminar_periodo',
		funcion: 	boton_eliminar_periodo
	},
    // Imágenes
    {	selector: '.boton_mostrar_imagen_base_datos_ventana',
		funcion: 	boton_mostrar_imagen_base_datos_ventana
	},
    // Histórico de procesado
    {	selector: '.boton_refrescar_tabla_historico_procesado',
		funcion:    boton_refrescar_tabla_historico_procesado
	},
    // Repetir importación de valores del sensor
    {	selector: '.boton_mostrar_ventana_repetir_importacion_valores_sensor',
		funcion: 	boton_mostrar_ventana_repetir_importacion_valores_sensor
	}
];


TLNT.Navegacion.botones_ventanas_modales_modulos = [
    // Herramientas de nodos
    {	selector: '.boton_asignar_localizacion_nodos',
		funcion: 	boton_asignar_localizacion_nodos
	},
    {	selector: '.boton_asignar_grupo_nodos',
		funcion: 	boton_asignar_grupo_nodos
	},
    // Nodos
	{	selector: '.boton_anyadir_modificar_nodo',
		funcion: 	boton_anyadir_modificar_nodo
	},
    // Acciones de usuario
    {	selector: '.boton_modificar_observaciones_accion_usuario',
		funcion: 	boton_modificar_observaciones_accion_usuario
	},
    // Comentarios
    {	selector: '.boton_anyadir_modificar_comentario',
		funcion: 	boton_anyadir_modificar_comentario
	},
    {	selector: '.boton_anyadir_comentarios',
		funcion: 	boton_anyadir_comentarios
	},
    // Hijos de sensores (virtuales y de procesado)
    {	selector: '.boton_anyadir_modificar_hijo_sensor',
		funcion: 	boton_anyadir_modificar_hijo_sensor
	},
    // Rangos de días
	{	selector: '.boton_anyadir_modificar_rango_dias',
		funcion: 	boton_anyadir_modificar_rango_dias
	},
	// Periodos
	{	selector: '.boton_anyadir_modificar_periodo',
		funcion: 	boton_anyadir_modificar_periodo
	},
    // Widgets
    {
        selector: '.boton_anyadir_modificar_pestanya_widgets',
		funcion: 	boton_anyadir_modificar_pestanya_widgets
	},
    {	selector: '.boton_anyadir_modificar_widget',
		funcion: 	boton_anyadir_modificar_widget
	},
    // Imágenes
    {	selector: '.boton_mostrar_imagen_base_datos_ventana',
		funcion: 	boton_mostrar_imagen_base_datos_ventana
	},
    // Informes automáticos
    {	selector: '.boton_anyadir_modificar_informe_automatico',
		funcion: 	boton_anyadir_modificar_informe_automatico
	},
    // Ayuda (elementos de plantillas de informes)
    {	selector: '#boton_ayuda_exclusion_fechas_widget',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_widget',
		funcion: 	boton_ayuda_fechas
	},
    // Version firmware dispositivos radon
    {
        selector: '.boton_anyadir_version_firmware',
        funcion:    boton_anyadir_version_firmware
    },
    {
        selector: '.boton_eliminar_dispositivo',
        funcion:    boton_eliminar_dispositivo
    },
    {
        selector:  '.boton_actualizar_dispositivo',
        funcion:    boton_actualizar_dispositivo
    }
];


// Eventos de los botones del contenido de los widgets
TLNT.Navegacion.botones_contenido_widgets = [
    // Widgets
    {   selector: '.boton_sensores_mostrar_ventana_envio_valores_manuales_sensor_widget',
		funcion: 	boton_sensores_mostrar_ventana_envio_valores_manuales_sensor_widget
	},
    {   selector: '.boton_actuadores_mostrar_ventana_envio_accion_actuador_widget',
		funcion: 	boton_actuadores_mostrar_ventana_envio_accion_actuador_widget
	},
    {   selector: '.boton_actuadores_mostrar_ventana_envio_accion_grupo_actuadores_widget',
		funcion: 	boton_actuadores_mostrar_ventana_envio_accion_grupo_actuadores_widget
	}
];


// Eventos de los botones del contenido de los tooltips del mapa
TLNT.Navegacion.botones_contenido_tooltips_mapa = [
    // Mapa
    {   selector: '.boton_sensores_mostrar_ventana_envio_valores_manuales_sensor_mapa',
		funcion: 	boton_sensores_mostrar_ventana_envio_valores_manuales_sensor_mapa
	},
    {   selector: '.boton_actuadores_mostrar_ventana_envio_accion_actuador_mapa',
		funcion: 	boton_actuadores_mostrar_ventana_envio_accion_actuador_mapa
	}
];


//
// Funciones de establecimiento de eventos (por funcionalidad)
//


TLNT.Navegacion.establece_eventos_secciones_modulos = function() {
    TLNT.Navegacion.realiza_acciones_visualizacion_controles(TLNT.Navegacion.acciones_visualizacion_controles_modulos);
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_secciones_modulos);

    // Widgets y selección de localización
    establece_eventos_secciones_modulos_widgets();
    establece_eventos_secciones_modulos_seleccion_localizacion();

    // Nota: Las funciones de controles generales (p.e. chosen) deben ir siempre al final
    // (después del resto de establecimiento de eventos, si no pueden no funcionar correctamente)
    establece_eventos_secciones_modulos_generales();

    // Evento de pestaña inicial visible (si no no se activa el evento de pestaña mostrada al mostrar las pestañas inicialmente)
    TLNT.Navegacion.lanza_evento_pestanya_inicial_visible();
};


TLNT.Navegacion.establece_eventos_tablas_datos_modulos = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_modulos);
};


TLNT.Navegacion.establece_eventos_detalles_tablas_datos_modulos = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_detalles_tablas_datos_modulos);

    establece_eventos_detalles_informes_automaticos();
};


TLNT.Navegacion.establece_eventos_ventanas_modales_modulos = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_ventanas_modales_modulos);

    establece_eventos_ventanas_modales_modulos_posicion_mapa();
    establece_eventos_ventanas_modales_modulos_administracion_pestanyas_widgets();
    establece_eventos_ventanas_modales_modulos_administracion_widgets();
    establece_eventos_ventanas_modales_modulos_administracion_informes_automaticos();
    establece_eventos_ventanas_modales_modulos_acciones_usuario();
    establece_eventos_ventanas_modales_modulos_administracion_comentarios();
    establece_eventos_ventanas_modales_modulos_administracion_nodos();

    // Nota: Las funciones de controles generales (p.e. chosen) deben ir siempre al final
    // (después del resto de establecimiento de eventos, si no pueden no funcionar correctamente)
    establece_eventos_ventanas_modales_modulos_generales();
};


TLNT.Navegacion.establece_eventos_contenido_widgets = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_contenido_widgets);
};


TLNT.Navegacion.establece_eventos_contenido_tooltips_mapa = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_contenido_tooltips_mapa);
};


//
// Funciones auxiliares para establecer las acciones de los controles
//


establece_eventos_secciones_modulos_widgets = function() {
    // Pestaña de widgets
    $('#tabs-pestanyas-widgets').off('shown.bs.tab');
    $('#tabs-pestanyas-widgets').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var href_pestanya = $('#tabs-pestanyas-widgets .active > a').attr("href");
        if (href_pestanya !== undefined) {
            var id_pestanya = href_pestanya.split("__")[1];
            muestra_pestanya_widgets(id_pestanya);
        }
    });
},


establece_eventos_secciones_modulos_seleccion_localizacion = function() {
    // Desactivación de eventos anteriores
    $("#id_localizacion_seleccion_localizacion_actual").off();

    // Habilita y muestra los controles dependientes de la localización actual
    var funcion_habilita_muestra_controles_localizacion_actual = function() {
        var id_localizacion = parseInt($("#id_localizacion_seleccion_localizacion_actual").val());
        switch (id_localizacion) {
            case ID_DESACTIVADO:
            case ID_NINGUNO:
            case ID_TODOS: {
                $("#control_lista_doble_localizaciones_seleccion_localizacion_actual").hide();
                $("#control_lista_doble_localizaciones_seleccion_localizacion_actual").parent().hide();
                break;
            }
            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
            case ID_LOCALIZACIONES_SELECCIONADAS_AND: {
                $("#control_lista_doble_localizaciones_seleccion_localizacion_actual").parent().show();
                $("#control_lista_doble_localizaciones_seleccion_localizacion_actual").show();
                break;
            }
        }
    };
    $("#id_localizacion_seleccion_localizacion_actual").show(funcion_habilita_muestra_controles_localizacion_actual);
    $("#id_localizacion_seleccion_localizacion_actual").change(funcion_habilita_muestra_controles_localizacion_actual);
},


establece_eventos_secciones_modulos_generales = function() {

    var ARQUITECTURA_DISPOSITIVO_RPI = "RPI"
    var ARQUITECTURA_DISPOSITIVO_BABELBOX =  "BABELBOX";
    var ARQUITECTURA_DISPOSITIVO_BYE_RADON =  "BYE RADON";

    // Se muestra la localización del mapa
    $(document).off('shown.bs.tab');
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var tab = $(e.target);
        var id_pestanya = tab.attr("href");
        var funcion_control_opciones_dispositivo = function(){

            if (id_pestanya == "#tab-datos"){

                var opcion_seleccionada_anyadir_nodo = $("#id_arquitectura_dispositivo").val();
                switch (opcion_seleccionada_anyadir_nodo) {
                    case ARQUITECTURA_DISPOSITIVO_BYE_RADON:
                        $('#control_contenedor_imei_dispositivo').show();
                        $('#control_contenedor_direccion_mac').hide();
                        $('#control_contenedor_direccion_ip').hide();
                        $('#control_contenedor_localizacion_dispositivo').show();
                        break;
                    case ARQUITECTURA_DISPOSITIVO_BABELBOX:
                    case ARQUITECTURA_DISPOSITIVO_RPI:
                        $('#control_contenedor_imei_dispositivo').hide();
                        $('#control_contenedor_direccion_mac').show();
                        $('#control_contenedor_direccion_ip').show();
                        $('#control_contenedor_localizacion_dispositivo').hide();
                        break;
                    default:
                        $('#control_contenedor_imei_dispositivo').hide();
                        $('#control_contenedor_direccion_mac').show();
                        $('#control_contenedor_direccion_ip').show();
                        $('#control_contenedor_localizacion_dispositivo').hide();
                        break;
                }
            }
        };
        $("#tabs-administracion-dispositivo").show(funcion_control_opciones_dispositivo);
        $("#tabs-administracion-dispositivo").change(funcion_control_opciones_dispositivo);

        if ((id_pestanya == "#tab-posicion-mapa") || (id_pestanya == "#tab-posicion-imagen")) {
            var mostrar_en_mapa = parseInt($("#mostrar_en_mapa").val());
            if (mostrar_en_mapa == VALOR_SI) {
                if ($('#localizador_mapa_oculto').length) {
                    $('#localizador_mapa_oculto').attr("id", "localizador_mapa");
                    localizador_mapa_visible("");
                }
                $('#localizador_mapa').show();
            }
        }
        else {
            if ($('#localizador_mapa').length) {
                $('#localizador_mapa').attr("id", "localizador_mapa_oculto");
                $('#localizador_mapa_oculto').html("");
            }
        }
        var sufijo_controles_defecto = "_defecto";
        if ((id_pestanya == "#tab-mapa") || (id_pestanya == "#tab-imagen")) {
            if ($('#localizador_mapa_oculto' + sufijo_controles_defecto).length) {
                $('#localizador_mapa_oculto' + sufijo_controles_defecto).attr("id", "localizador_mapa" + sufijo_controles_defecto);
                localizador_mapa_visible(sufijo_controles_defecto);
                $('#localizador_mapa' + sufijo_controles_defecto).show();
            }
        }
        else {
            if ($('#localizador_mapa' + sufijo_controles_defecto).length) {
                $('#localizador_mapa' + sufijo_controles_defecto).attr("id", "localizador_mapa_oculto" + sufijo_controles_defecto);
                $('#localizador_mapa_oculto' + sufijo_controles_defecto).html("");
            }
        }

        // Log
        //console.log(id_pestanya);
    });

    // Mostrar lista doble para la selección de localizaciones
    if ($('#select_localizaciones_no_visible_seleccion_localizacion_actual').length) {
        $('#select_localizaciones_no_visible_seleccion_localizacion_actual').attr("id", "select_localizaciones_visible_seleccion_localizacion_actual");
        TLNT.Navegacion.convierte_lista_doble("ids_localizaciones_seleccion_localizacion_actual", true);
    };

    // Nota: Las funciones de controles generales (p.e. chosen) deben ir siempre al final
    // (después del resto de establecimiento de eventos, si no pueden no funcionar correctamente)

    // Establecimiento de formatos de fecha y hora
    TLNT.Navegacion.establece_formatos_fecha_hora();

    // Convierte los desplegables 'chosen'
    convierte_desplegables_chosen();

    // Establecimiento de eventos de tablas de datos
    TLNT.Navegacion.establece_eventos_mostrar_ocultar_contenido_tablas_datos();
    TLNT.Navegacion.establece_eventos_mostrar_ocultar_elementos_desplegables_cabeceras_tablas_datos();
},


convierte_desplegables_chosen = function() {
    // Select con filtro
    $(".chosen-select").chosen({
        search_contains: true,
        no_results_text: TLNT.Idiomas._('Sin resultados') + ": ",
        inherit_select_classes: true});

    // Nota: Si se llama más de una vez a esta función, se muestra el desplegable original
    // (posible bug de la librería ...) (con esto se oculta si se ha mostrado)
    $(".chosen-select").each(function() {
        var id = this.id;
        var hidden = $(this).attr("hidden");
        if (hidden !== undefined) {
            if ($(this).css('display') != 'none') {
                oculta_elemento(id);
            }
        }
    });
};


establece_eventos_detalles_informes_automaticos = function() {
    TLNT.Navegacion.redimensiona_textarea(".area-entrada-texto-detalles-informe");
};


establece_eventos_ventanas_modales_modulos_generales = function() {
    // Nota: Las funciones de controles generales (p.e. chosen) deben ir siempre al final
    // (después del resto de establecimiento de eventos, si no pueden no funcionar correctamente)

    // Establecimiento de formatos de fecha y hora
    TLNT.Navegacion.establece_formatos_fecha_hora();

    // Convierte los desplegables 'chosen' de administración
    convierte_desplegables_chosen_administracion();
};


convierte_desplegables_chosen_administracion = function() {
    // Select con filtro
    fixChosenClippingBeforeCreation($('.chosen-select-administracion'), $('#contenido_modal'));
    $(".chosen-select-administracion").chosen({
        search_contains: true,
        no_results_text: TLNT.Idiomas._('Sin resultados') + ": ",
        inherit_select_classes: true});
    fixChosenClippingAfterCreation($('.chosen-select-administracion'));
};


establece_eventos_ventanas_modales_modulos_posicion_mapa = function() {
    // Desactivación de eventos anteriores
    $("#mostrar_en_mapa").off();

    // Habilita y muestra los controles dependientes del mostrado en mapa
    var funcion_habilita_muestra_controles_mostrar_en_mapa_visible = function() {
        var mostrar_en_mapa = parseInt($("#mostrar_en_mapa").val());
        switch (mostrar_en_mapa) {
            case VALOR_SI: {
                $("#controles_localizador_mapa").show();
                $("#control_icono_imagen").show();
                break;
            }
            case VALOR_NO: {
                $("#controles_localizador_mapa").hide();
                $("#control_icono_imagen").hide();
                break;
            }
        }
    };
    $("#mostrar_en_mapa").show(funcion_habilita_muestra_controles_mostrar_en_mapa_visible);
    var funcion_habilita_muestra_controles_mostrar_en_mapa_modificado = function() {
        var mostrar_en_mapa = parseInt($("#mostrar_en_mapa").val());
        switch (mostrar_en_mapa) {
            case VALOR_SI: {
                if ($('#localizador_mapa_oculto').length) {
                    $('#localizador_mapa_oculto').attr("id", "localizador_mapa");
                    localizador_mapa_visible("");
                }
                $("#localizador_mapa").show();
                $("#controles_localizador_mapa").show();
                $("#control_icono_imagen").show();
                break;
            }
            case VALOR_NO: {
                if ($('#localizador_mapa_oculto').length) {
                    $("#localizador_mapa_oculto").hide();
                }
                else {
                    $("#localizador_mapa").hide();
                }
                $("#controles_localizador_mapa").hide();
                $("#control_icono_imagen").hide();
                break;
            }
        }
    };
    $("#mostrar_en_mapa").change(funcion_habilita_muestra_controles_mostrar_en_mapa_modificado);
};


establece_eventos_ventanas_modales_modulos_administracion_pestanyas_widgets = function() {
    // Desactivación de eventos anteriores
    $("imagen_fondo_apariencia_pestanya_pestanya_widgets").off();
    $("#mostrar_cabecera_apariencia_pestanya_pestanya_widgets").off();
    $("#mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets").off();
    $("#mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets").off();
    $("#mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets").off();
    $("#modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets").off();
    $("#mostrar_pie_apariencia_pestanya_pestanya_widgets").off();
    $("#modificar_borde_widgets_apariencia_widgets_pestanya_widgets").off();
    $("#mostrar_borde_widgets_apariencia_widgets_pestanya_widgets").off();
    $("#modificar_colores_titulo_widgets_apariencia_widgets_pestanya_widgets").off();
    $("#modificar_colores_widgets_apariencia_widgets_pestanya_widgets").off();
    $("#modificar_opciones_pantalla_completa_pestanya_widgets").off();
    $("#titulos_filas_widgets_pestanya_widgets").off();

    // Selección de fichero de imagen de fondo
    $("#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text").show(function() {
        $('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_file').hide();
    });
    $('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_text').val(fichero);
    });
    $('#boton_anyadir_modificar_pestanya_widgets_seleccionar_fichero_imagen_fondo_apariencia_pestanya').click(function() {
        $('#fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets_file').click();
    });

    // Pestaña de apariencia de pestaña de widgets

    // Mostrado de controles de imagen de fondo
    var funcion_muestra_controles_imagen_fondo_apariencia_pestanya_pestanya_widgets = function() {
        var imagen_fondo_apariencia_pestanya_pestanya_widgets = parseInt($("#imagen_fondo_apariencia_pestanya_pestanya_widgets").val());
        switch (imagen_fondo_apariencia_pestanya_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_nombre_imagen_fondo_apariencia_pestanya_pestanya_widgets").show();
                $("#nombre_imagen_fondo_apariencia_pestanya_pestanya_widgets").addClass('TLNT_input_mandatory');
                $("#control_fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets").show();
                break;
            }
            case VALOR_NO: {
                $("#control_nombre_imagen_fondo_apariencia_pestanya_pestanya_widgets").hide();
                $("#nombre_imagen_fondo_apariencia_pestanya_pestanya_widgets").removeClass('TLNT_input_mandatory');
                $("#control_fichero_imagen_fondo_apariencia_pestanya_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#imagen_fondo_apariencia_pestanya_pestanya_widgets").show(funcion_muestra_controles_imagen_fondo_apariencia_pestanya_pestanya_widgets);
    $("#imagen_fondo_apariencia_pestanya_pestanya_widgets").change(funcion_muestra_controles_imagen_fondo_apariencia_pestanya_pestanya_widgets);

    // Mostrado de controles de hora en cabecera de pestaña de widgets
    var funcion_muestra_controles_mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets = function() {
        var mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets = parseInt($("#mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets").val());
        switch (mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_color_hora_cabecera_apariencia_pestanya_pestanya_widgets").show();
                break;
            }
            case VALOR_NO: {
                $("#control_color_hora_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets").change(funcion_muestra_controles_mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets);

    // Mostrado de controles de fecha en cabecera de pestaña de widgets
    var funcion_muestra_controles_mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets = function() {
        var mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets = parseInt($("#mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets").val());
        switch (mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_color_fecha_cabecera_apariencia_pestanya_pestanya_widgets").show();
                break;
            }
            case VALOR_NO: {
                $("#control_color_fecha_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets").change(funcion_muestra_controles_mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets);

    // Mostrado de controles de título en cabecera de pestaña de widgets
    var funcion_muestra_controles_mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets = function() {
        var mostrar_titulo_cabecera_pestanya_widgets = parseInt($("#mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets").val());
        switch (mostrar_titulo_cabecera_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_color_titulo_cabecera_apariencia_pestanya_pestanya_widgets").show();
                $("#control_prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").show();
                $("#control_color_prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").show();
                $("#control_sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").show();
                $("#control_color_sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").show();
                break;
            }
            case VALOR_NO: {
                $("#control_color_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_color_prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_color_sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets").change(funcion_muestra_controles_mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets);

    // Mostrado de controles de cabecera de pestaña de widgets
    var funcion_muestra_controles_mostrar_cabecera_apariencia_pestanya_pestanya_widgets = function() {
        var mostrar_cabecera_apariencia_pestanya_pestanya_widgets = parseInt($("#mostrar_cabecera_apariencia_pestanya_pestanya_widgets").val());
        switch (mostrar_cabecera_apariencia_pestanya_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_numero_lineas_separacion_cabecera_apariencia_pestanya_pestanya_widgets").show();
                $("#control_mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets").show();
                funcion_muestra_controles_mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets();
                $("#control_mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets").show();
                funcion_muestra_controles_mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets();
                $("#control_mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets").show();
                funcion_muestra_controles_mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets();
                break;
            }
            case VALOR_NO: {
                $("#control_mostrar_hora_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_color_hora_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_mostrar_fecha_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_color_fecha_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_mostrar_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_color_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_color_prefijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_color_sufijo_titulo_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                $("#control_numero_lineas_separacion_cabecera_apariencia_pestanya_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#mostrar_cabecera_apariencia_pestanya_pestanya_widgets").show(funcion_muestra_controles_mostrar_cabecera_apariencia_pestanya_pestanya_widgets);
    $("#mostrar_cabecera_apariencia_pestanya_pestanya_widgets").change(funcion_muestra_controles_mostrar_cabecera_apariencia_pestanya_pestanya_widgets);

    // Mostrado de controles de color de título de filas de widgets
    var funcion_muestra_controles_modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets = function() {
        var modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets = parseInt($("#modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets").val());
        switch (modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets").show();
                break;
            }
            case VALOR_NO: {
                $("#control_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets").show(funcion_muestra_controles_modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets);
    $("#modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets").change(funcion_muestra_controles_modificar_color_titulo_filas_widgets_apariencia_pestanya_pestanya_widgets);

    // Mostrado de controles de pie de pestaña de widgets
    var funcion_muestra_controles_mostrar_pie_apariencia_pestanya_pestanya_widgets = function() {
        var mostrar_pie_apariencia_pestanya_pestanya_widgets = parseInt($("#mostrar_pie_apariencia_pestanya_pestanya_widgets").val());
        switch (mostrar_pie_apariencia_pestanya_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_numero_lineas_separacion_pie_apariencia_pestanya_pestanya_widgets").show();
                break;
            }
            case VALOR_NO: {
                $("#control_numero_lineas_separacion_pie_apariencia_pestanya_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#mostrar_pie_apariencia_pestanya_pestanya_widgets").show(funcion_muestra_controles_mostrar_pie_apariencia_pestanya_pestanya_widgets);
    $("#mostrar_pie_apariencia_pestanya_pestanya_widgets").change(funcion_muestra_controles_mostrar_pie_apariencia_pestanya_pestanya_widgets);

    // Pestaña de apariencia de widgets

    // Mostrado de controles de borde de widgets
    var funcion_muestra_controles_mostrar_borde_widgets_apariencia_widgets_pestanya_widgets = function() {
        var mostrar_borde_widgets_apariencia_widgets_pestanya_widgets = parseInt($("#mostrar_borde_widgets_apariencia_widgets_pestanya_widgets").val());
        switch (mostrar_borde_widgets_apariencia_widgets_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_color_borde_widgets_apariencia_widgets_pestanya_widgets").show();
                break;
            }
            case VALOR_NO: {
                $("#control_color_borde_widgets_apariencia_widgets_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#mostrar_borde_widgets_apariencia_widgets_pestanya_widgets").change(funcion_muestra_controles_mostrar_borde_widgets_apariencia_widgets_pestanya_widgets);

    // Mostrado de controles de borde de widgets
    var funcion_muestra_controles_borde_widgets_apariencia_widgets_pestanya_widgets = function() {
        var modificar_borde_widgets_apariencia_widgets_pestanya_widgets = parseInt($("#modificar_borde_widgets_apariencia_widgets_pestanya_widgets").val());
        switch (modificar_borde_widgets_apariencia_widgets_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_mostrar_borde_widgets_apariencia_widgets_pestanya_widgets").show();
                funcion_muestra_controles_mostrar_borde_widgets_apariencia_widgets_pestanya_widgets();
                break;
            }
            case VALOR_NO: {
                $("#control_mostrar_borde_widgets_apariencia_widgets_pestanya_widgets").hide();
                $("#control_color_borde_widgets_apariencia_widgets_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#modificar_borde_widgets_apariencia_widgets_pestanya_widgets").show(funcion_muestra_controles_borde_widgets_apariencia_widgets_pestanya_widgets);
    $("#modificar_borde_widgets_apariencia_widgets_pestanya_widgets").change(funcion_muestra_controles_borde_widgets_apariencia_widgets_pestanya_widgets);

    // Mostrado de controles de colores de título de widgets
    var funcion_muestra_controles_colores_titulo_widgets_apariencia_widgets_pestanya_widgets = function() {
        var modificar_colores_titulo_widgets_apariencia_widgets_pestanya_widgets = parseInt($("#modificar_colores_titulo_widgets_apariencia_widgets_pestanya_widgets").val());
        switch (modificar_colores_titulo_widgets_apariencia_widgets_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_color_titulo_widgets_apariencia_widgets_pestanya_widgets").show();
                $("#control_color_fondo_titulo_widgets_apariencia_widgets_pestanya_widgets").show();
                break;
            }
            case VALOR_NO: {
                $("#control_color_titulo_widgets_apariencia_widgets_pestanya_widgets").hide();
                $("#control_color_fondo_titulo_widgets_apariencia_widgets_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#modificar_colores_titulo_widgets_apariencia_widgets_pestanya_widgets").show(funcion_muestra_controles_colores_titulo_widgets_apariencia_widgets_pestanya_widgets);
    $("#modificar_colores_titulo_widgets_apariencia_widgets_pestanya_widgets").change(funcion_muestra_controles_colores_titulo_widgets_apariencia_widgets_pestanya_widgets);

    // Mostrado de controles de colores de widgets
    var funcion_muestra_controles_colores_widgets_apariencia_widgets_pestanya_widgets = function() {
        var modificar_colores_widgets_apariencia_widgets_pestanya_widgets = parseInt($("#modificar_colores_widgets_apariencia_widgets_pestanya_widgets").val());
        switch (modificar_colores_widgets_apariencia_widgets_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_color_widgets_apariencia_widgets_pestanya_widgets").show();
                $("#control_color_fondo_widgets_apariencia_widgets_pestanya_widgets").show();
                break;
            }
            case VALOR_NO: {
                $("#control_color_widgets_apariencia_widgets_pestanya_widgets").hide();
                $("#control_color_fondo_widgets_apariencia_widgets_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#modificar_colores_widgets_apariencia_widgets_pestanya_widgets").show(funcion_muestra_controles_colores_widgets_apariencia_widgets_pestanya_widgets);
    $("#modificar_colores_widgets_apariencia_widgets_pestanya_widgets").change(funcion_muestra_controles_colores_widgets_apariencia_widgets_pestanya_widgets);

    // Pestaña de opciones de pantalla completa

    // Mostrado de controles de opciones de pantalla completa
    var funcion_muestra_controles_modificar_opciones_pantalla_completa_pestanya_widgets = function() {
        var modificar_opciones_pantalla_completa_pestanya_widgets = parseInt($("#modificar_opciones_pantalla_completa_pestanya_widgets").val());
        switch (modificar_opciones_pantalla_completa_pestanya_widgets) {
            case VALOR_SI: {
                $("#control_mostrar_opciones_opciones_pantalla_completa_pestanya_widgets").show();
                $("#control_estilo_fuente_titulo_opciones_pantalla_completa_pestanya_widgets").show();
                $("#control_color_opciones_pantalla_completa_pestanya_widgets").show();
                $("#control_color_fondo_opciones_pantalla_completa_pestanya_widgets").show();
                $("#control_mostrar_pie_pagina_opciones_pantalla_completa_pestanya_widgets").show();
                break;
            }
            case VALOR_NO: {
                $("#control_mostrar_opciones_opciones_pantalla_completa_pestanya_widgets").hide();
                $("#control_estilo_fuente_titulo_opciones_pantalla_completa_pestanya_widgets").hide();
                $("#control_color_opciones_pantalla_completa_pestanya_widgets").hide();
                $("#control_color_fondo_opciones_pantalla_completa_pestanya_widgets").hide();
                $("#control_mostrar_pie_pagina_opciones_pantalla_completa_pestanya_widgets").hide();
                break;
            }
        }
    };
    $("#modificar_opciones_pantalla_completa_pestanya_widgets").show(funcion_muestra_controles_modificar_opciones_pantalla_completa_pestanya_widgets);
    $("#modificar_opciones_pantalla_completa_pestanya_widgets").change(funcion_muestra_controles_modificar_opciones_pantalla_completa_pestanya_widgets);

    // Contador de caracteres de títulos de filas de widgets
    $("#titulos_filas_widgets_pestanya_widgets").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_modulos_administracion_widgets = function() {
    // Desactivación de eventos anteriores
    $("#tipo_widget").off();

    // Botones para subir/bajar los widgets en la lista de posiciones de widgets de pestaña de widgets
    // http://stackoverflow.com/questions/6713702/move-item-up-and-down-in-select-using-button
    // http://jsfiddle.net/Shef/Aq8s3/
    $('#boton_subir_posicion_widget').unbind('click');
    $('#boton_subir_posicion_widget').click(function() {
        var widget_seleccionado = $('#posicion_widgets option:selected');
        widget_seleccionado.first().prev().before(widget_seleccionado);
    });
    $('#boton_bajar_posicion_widget').unbind('click');
    $('#boton_bajar_posicion_widget').click(function() {
        var widget_seleccionado = $('#posicion_widgets option:selected');
        widget_seleccionado.last().next().after(widget_seleccionado);
    });

    // Muestra las pestañas del tipo de widget correspondiente
    var funcion_muestra_pestanyas_tipo_widget = function() {
        $("#titulo-tab-tipo-imagen").hide();
        $("#titulo-tab-tipo-valor-ratio").hide();
        $("#titulo-tab-tipo-valor-digital-sensor").hide();
        $("#titulo-tab-tipo-valor-digital-medio-acumulado-sensor").hide();
        $("#titulo-tab-tipo-valor-analogico-sensor").hide();
        $("#titulo-tab-tipo-valor-analogico-medio-acumulado-sensor").hide();
        $("#titulo-tab-tipo-grafica-valores-sensor").hide();
         $("#titulo-tab-tipo-mapa-calor-sensor").hide();
        $("#titulo-tab-tipo-grafica-comparacion-periodos-sensor").hide();
        $("#titulo-tab-tipo-evolucion-valores-comparacion-periodos-sensor").hide();
        $("#titulo-tab-tipo-grafica-comparacion-campos-iguales-sensores-principal").hide();
        $("#titulo-tab-tipo-grafica-comparacion-campos-iguales-sensores-sensores").hide();
        $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-principal").hide();
        $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-1").hide();
        $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-2").hide();
        $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-3").hide();
        $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-4").hide();
        $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-5").hide();
        $("#titulo-tab-tipo-grafica-valores-generales-sensores-principal").hide();
        $("#titulo-tab-tipo-grafica-valores-generales-sensores-campo-1").hide();
        $("#titulo-tab-tipo-grafica-valores-generales-sensores-campo-2").hide();
        $("#titulo-tab-tipo-grafica-valores-generales-sensores-campo-3").hide();
        $("#titulo-tab-tipo-grafica-valores-generales-sensores-sensores").hide();
        $("#titulo-tab-tipo-valor-agregado-valores-generales-sensores-principal").hide();
        $("#titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-1").hide();
        $("#titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-2").hide();
        $("#titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-3").hide();
        $("#titulo-tab-tipo-valor-agregado-valores-generales-sensores-sensores").hide();
        $("#titulo-tab-tipo-grafica-incrementos-totales-sensores-principal").hide();
        $("#titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-1").hide();
        $("#titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-2").hide();
        $("#titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-3").hide();
        $("#titulo-tab-tipo-grafica-incrementos-totales-sensores-sensores").hide();
        $("#titulo-tab-tipo-informacion-actuador").hide();
        $("#titulo-tab-tipo-informacion-grupo-actuadores").hide();
        $("#titulo-tab-tipo-grafica-consumos-costes-tramos-sensor").hide();
        $("#titulo-tab-tipo-coste-factura-sensor").hide();
        $("#titulo-tab-tipo-simulador-linea-base").hide();
        $("#titulo-tab-tipo-informacion-proyecto").hide();

        var mostrar_pestanya_horario_semanal_fechas = false;
        var mostrar_horario_semanal = true;
        var mostrar_exclusion_fechas = true;
        var mostrar_inclusion_fechas = true;

        var tipo_widget = $("#tipo_widget").val();
        switch (tipo_widget) {
            case TIPO_NINGUNO: {
                break;
            }
            case TIPO_WIDGET_IMAGEN: {
                $("#titulo-tab-tipo-imagen").show();
                break;
            }
            case TIPO_WIDGET_VALOR_RATIO: {
                $("#titulo-tab-tipo-valor-ratio").show();
                break;
            }
            case TIPO_WIDGET_VALOR_DIGITAL_SENSOR: {
                $("#titulo-tab-tipo-valor-digital-sensor").show();
                break;
            }
            case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR: {
                $("#titulo-tab-tipo-valor-digital-medio-acumulado-sensor").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR: {
                $("#titulo-tab-tipo-valor-analogico-sensor").show();
                break;
            }
            case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR: {
                $("#titulo-tab-tipo-valor-analogico-medio-acumulado-sensor").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_SENSOR: {
                $("#titulo-tab-tipo-grafica-valores-sensor").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            case TIPO_WIDGET_MAPA_CALOR_SENSOR: {
                $("#titulo-tab-tipo-mapa-calor-sensor").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR: {
                $("#titulo-tab-tipo-grafica-comparacion-periodos-sensor").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_inclusion_fechas = false;
                break;
            }
            case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR: {
                $("#titulo-tab-tipo-evolucion-valores-comparacion-periodos-sensor").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_inclusion_fechas = false;
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES: {
                $("#titulo-tab-tipo-grafica-comparacion-campos-iguales-sensores-principal").show();
                $("#titulo-tab-tipo-grafica-comparacion-campos-iguales-sensores-sensores").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES: {
                $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-principal").show();
                $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-1").show();
                $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-2").show();
                $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-3").show();
                $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-4").show();
                $("#titulo-tab-tipo-grafica-comparacion-campos-diferentes-sensores-sensor-5").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES: {
                $("#titulo-tab-tipo-grafica-valores-generales-sensores-principal").show();
                $("#titulo-tab-tipo-grafica-valores-generales-sensores-campo-1").show();
                $("#titulo-tab-tipo-grafica-valores-generales-sensores-campo-2").show();
                $("#titulo-tab-tipo-grafica-valores-generales-sensores-campo-3").show();
                $("#titulo-tab-tipo-grafica-valores-generales-sensores-sensores").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES: {
                $("#titulo-tab-tipo-valor-agregado-valores-generales-sensores-principal").show();
                $("#titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-1").show();
                $("#titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-2").show();
                $("#titulo-tab-tipo-valor-agregado-valores-generales-sensores-campo-3").show();
                $("#titulo-tab-tipo-valor-agregado-valores-generales-sensores-sensores").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES: {
                $("#titulo-tab-tipo-grafica-incrementos-totales-sensores-principal").show();
                $("#titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-1").show();
                $("#titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-2").show();
                $("#titulo-tab-tipo-grafica-incrementos-totales-sensores-campo-3").show();
                $("#titulo-tab-tipo-grafica-incrementos-totales-sensores-sensores").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            case TIPO_WIDGET_INFORMACION_ACTUADOR: {
                $("#titulo-tab-tipo-informacion-actuador").show();
                break;
            }
            case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES: {
                $("#titulo-tab-tipo-informacion-grupo-actuadores").show();
                break;
            }
            case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR: {
                $("#titulo-tab-tipo-grafica-consumos-costes-tramos-sensor").show();
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            case TIPO_WIDGET_COSTE_FACTURA_SENSOR: {
                $("#titulo-tab-tipo-coste-factura-sensor").show();
                break;
            }
            case TIPO_WIDGET_SIMULADOR_LINEA_BASE: {
                $("#titulo-tab-tipo-simulador-linea-base").show();
                break;
            }
            case TIPO_WIDGET_INFORMACION_PROYECTO: {
                $("#titulo-tab-tipo-informacion-proyecto").show();
                break;
            }
        }

        // Pestaña de horario semanal y fechas
        if (mostrar_pestanya_horario_semanal_fechas == true) {
            $("#titulo-tab-horario-semanal-fechas").show();
            if (mostrar_horario_semanal == true) {
                $("#control_horario_semanal_widget").show();
            }
            else {
                $("#control_horario_semanal_widget").hide();
            }
            if (mostrar_exclusion_fechas == true) {
                $("#control_exclusion_fechas_widget").show();
            }
            else {
                $("#control_exclusion_fechas_widget").hide();
            }
            if (mostrar_inclusion_fechas == true) {
                $("#control_inclusion_fechas_widget").show();
            }
            else {
                $("#control_inclusion_fechas_widget").hide();
            }
        }
        else {
            $("#titulo-tab-horario-semanal-fechas").hide();
        }

        // Lanzamiento de eventos "manuales"
        switch (tipo_widget) {
            case TIPO_WIDGET_VALOR_RATIO: {
                $("#id_ratio_widget_valor_ratio").trigger('change');
                break;
            }
        }
    };
    $("#tipo_widget").show(funcion_muestra_pestanyas_tipo_widget);
    $("#tipo_widget").change(funcion_muestra_pestanyas_tipo_widget);

    // Establecimiento de eventos de cada uno de los tipos de widgets
    establece_eventos_ventanas_modales_modulos_administracion_widget_imagen();
    establece_eventos_ventanas_modales_modulos_administracion_widget_valor_ratio();
    establece_eventos_ventanas_modales_modulos_administracion_widget_valor_digital_sensor();
    establece_eventos_ventanas_modales_modulos_administracion_widget_valor_digital_medio_acumulado_sensor();
    establece_eventos_ventanas_modales_modulos_administracion_widget_valor_analogico_sensor();
    establece_eventos_ventanas_modales_modulos_administracion_widget_valor_analogico_medio_acumulado_sensor();
    establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_valores_sensor();
    establece_eventos_ventanas_modales_modulos_administracion_widget_mapa_calor_sensor();
    establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_comparacion_periodos_sensor();
    establece_eventos_ventanas_modales_modulos_administracion_widget_evolucion_valores_comparacion_periodos_sensor();
    establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_comparacion_campos_iguales_sensores();
    establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_comparacion_campos_diferentes_sensores();
    establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_valores_generales_sensores();
    establece_eventos_ventanas_modales_modulos_administracion_widget_valor_agregado_valores_generales_sensores();
    establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_incrementos_totales_sensores();
    establece_eventos_ventanas_modales_modulos_administracion_widget_informacion_actuador();
    establece_eventos_ventanas_modales_modulos_administracion_widget_informacion_grupo_actuadores();
    establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_consumos_costes_tramos_sensor();
    establece_eventos_ventanas_modales_modulos_administracion_widget_coste_factura_sensor();
    establece_eventos_ventanas_modales_modulos_administracion_widget_simulador_linea_base();
    establece_eventos_ventanas_modales_modulos_administracion_widget_informacion_proyecto();
};


establece_eventos_ventanas_modales_modulos_administracion_widget_imagen = function() {
    // - Selección de fichero de imagen
    $("#fichero_imagen_widget_imagen_text").show(function() {
        $('#fichero_imagen_widget_imagen_file').hide();
    });
    $('#fichero_imagen_widget_imagen_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_imagen_widget_imagen_text').val(fichero);
    });
    $('#boton_anyadir_modificar_widget_imagen_seleccionar_fichero_imagen').click(function() {
        $('#fichero_imagen_widget_imagen_file').click();
    });
};


establece_eventos_ventanas_modales_modulos_administracion_widget_valor_ratio = function() {
    $("#id_ratio_widget_valor_ratio").off();
    $("#periodo_tiempo_widget_valor_ratio").off();

    // Realiza acciones según la configuración del ratio del widget
    var funcion_realiza_acciones_configuracion_ratio_widget_valor_ratio = function() {
        var tipo_widget = $("#tipo_widget").val();
        if (tipo_widget != TIPO_WIDGET_VALOR_RATIO) {
            return;
        }

        var id_ratio = $("#id_ratio_widget_valor_ratio").val();
        if (id_ratio == ID_NINGUNO.toString()) {
            $("#control_periodo_tiempo_widget_valor_ratio").hide();
            $("#control_iniciar_comienzo_periodo_tiempo_widget_valor_ratio").hide();
            $("#control_fecha_inicio_periodo_tiempo_widget_valor_ratio").hide();
            $("#titulo-tab-horario-semanal-fechas").hide();
        }
        else {
            $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/dame_fila_ratio.php", {
                id_ratio: $("#id_ratio_widget_valor_ratio").val()
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                // Tipo y campo del ratio
                var fila_ratio = resultado.fila_ratio;
                var tipo_ratio = fila_ratio["tipo"];
                var campo_sensor_ratio = fila_ratio["campo_sensor"];

                // Se muestran los controles según la configuración del ratio
                if ((tipo_ratio == TIPO_RATIO_VARIABLE) && (campo_sensor_ratio == CAMPO_INCREMENTO)) {
                    $("#control_periodo_tiempo_widget_valor_ratio").show();
                    $("#titulo-tab-horario-semanal-fechas").show();

                    // Se lanza el evento de 'change'
                    $("#periodo_tiempo_widget_valor_ratio").trigger('change');
                }
                else {
                    $("#control_periodo_tiempo_widget_valor_ratio").hide();
                    $("#control_iniciar_comienzo_periodo_tiempo_widget_valor_ratio").hide();
                    $("#control_fecha_inicio_periodo_tiempo_widget_valor_ratio").hide();
                    $("#titulo-tab-horario-semanal-fechas").hide();
                }
            });
        }
    };
    $("#id_ratio_widget_valor_ratio").show(funcion_realiza_acciones_configuracion_ratio_widget_valor_ratio);
    $("#id_ratio_widget_valor_ratio").change(funcion_realiza_acciones_configuracion_ratio_widget_valor_ratio);

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_valor_ratio = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_valor_ratio");
    };
    $("#periodo_tiempo_widget_valor_ratio").show(funcion_muestra_controles_periodo_tiempo_widget_valor_ratio);
    $("#periodo_tiempo_widget_valor_ratio").change(funcion_muestra_controles_periodo_tiempo_widget_valor_ratio);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_valor_digital_sensor = function() {
    $("#clase_sensor_widget_valor_digital_sensor").off();
    $("#id_sensor_widget_valor_digital_sensor").off();
    $("#campo_widget_valor_digital_sensor").off();
    $("#granularidad_sensor_widget_valor_digital_sensor").off();
    $("#utilizar_colores_fondo_widget_valor_digital_sensor").off();

    // Muestra el control del ratio
    var funcion_muestra_control_ratio_widget_tipo_valor_digital_sensor = function() {
        funcion_muestra_control_ratio_widget("widget_valor_digital_sensor");
    };
    $("#clase_sensor_widget_valor_digital_sensor").show(funcion_muestra_control_ratio_widget_tipo_valor_digital_sensor);
    $("#clase_sensor_widget_valor_digital_sensor").change(funcion_muestra_control_ratio_widget_tipo_valor_digital_sensor);

    // Recarga los sensores del widget
    var funcion_recarga_sensores_clase_sensor_widget_tipo_valor_digital_sensor = function() {
        funcion_recarga_sensores_clase_sensor_widget("widget_valor_digital_sensor");
    };
    $("#clase_sensor_widget_valor_digital_sensor").change(funcion_recarga_sensores_clase_sensor_widget_tipo_valor_digital_sensor);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_widget_tipo_valor_digital_sensor = function() {
        funcion_habilita_sensor_widget("widget_valor_digital_sensor");
    };
    $("#id_sensor_widget_valor_digital_sensor").show(funcion_habilita_sensor_widget_tipo_valor_digital_sensor);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_widget_tipo_valor_digital_sensor = function() {
        funcion_habilita_campo_widget("widget_valor_digital_sensor");
    };
    $("#campo_widget_valor_digital_sensor").show(funcion_habilita_campo_widget_tipo_valor_digital_sensor);

    // Recarga los campos y las granularidades de una clase de sensor del widget
    // (en tipos de widget con granularidad, al cambiar la granularidad se recargarán los campos de sensor correspondientes)
    var funcion_recarga_granularidades_sensor_clase_sensor_widget_tipo_valor_digital_sensor = function() {
        funcion_recarga_lista_granularidades_sensor_clase_sensor_widget("widget_valor_digital_sensor");
    };
    $("#clase_sensor_widget_valor_digital_sensor").change(funcion_recarga_granularidades_sensor_clase_sensor_widget_tipo_valor_digital_sensor);

    // Habilitación de granularidad de sensor del widget
    var funcion_habilita_granularidad_sensor_widget_tipo_valor_digital_sensor = function() {
        funcion_habilita_granularidad_sensor_widget("widget_valor_digital_sensor");
    };
    $("#granularidad_sensor_widget_valor_digital_sensor").show(funcion_habilita_granularidad_sensor_widget_tipo_valor_digital_sensor);
    $("#granularidad_sensor_widget_valor_digital_sensor").change(funcion_habilita_granularidad_sensor_widget_tipo_valor_digital_sensor);

    // Funciones a realizar según la granularidad del sensor del widget
    var funcion_realiza_acciones_granularidad_sensor_widget_tipo_valor_digital = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_valor_digital_sensor", "widget_valor_digital_sensor");
    };
    $("#granularidad_sensor_widget_valor_digital_sensor").change(funcion_realiza_acciones_granularidad_sensor_widget_tipo_valor_digital);

    // Habilita y muestra los controles dependientes de utilizar los colores de fondo de valores del widget
    var funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_digital_sensor = function() {
        funcion_habilita_muestra_controles_utilizar_colores_fondo_widgets_valores_sensor("widget_valor_digital_sensor");
    };
    $("#utilizar_colores_fondo_widget_valor_digital_sensor").show(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_digital_sensor);
    $("#utilizar_colores_fondo_widget_valor_digital_sensor").change(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_digital_sensor);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_digital_sensor = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("widget_valor_digital_sensor");
    };
    $("#campo_widget_valor_digital_sensor").show(funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_digital_sensor);
    $("#campo_widget_valor_digital_sensor").change(funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_digital_sensor);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_valor_digital_medio_acumulado_sensor = function() {
    $("#clase_sensor_widget_valor_digital_medio_acumulado_sensor").off();
    $("#id_sensor_widget_valor_digital_medio_acumulado_sensor").off();
    $("#campo_widget_valor_digital_medio_acumulado_sensor").off();
    $("#utilizar_colores_fondo_widget_valor_digital_medio_acumulado_sensor").off();
    $("#periodo_tiempo_widget_valor_digital_medio_acumulado_sensor").off();

    // Muestra el control del ratio
    var funcion_muestra_control_ratio_widget_tipo_valor_digital_medio_acumulado_sensor = function() {
        funcion_muestra_control_ratio_widget("widget_valor_digital_medio_acumulado_sensor");
    };
    $("#clase_sensor_widget_valor_digital_medio_acumulado_sensor").show(funcion_muestra_control_ratio_widget_tipo_valor_digital_medio_acumulado_sensor);
    $("#clase_sensor_widget_valor_digital_medio_acumulado_sensor").change(funcion_muestra_control_ratio_widget_tipo_valor_digital_medio_acumulado_sensor);

    // Recarga los sensores del widget
    var funcion_recarga_sensores_clase_sensor_widget_tipo_valor_digital_medio_acumulado_sensor = function() {
        funcion_recarga_sensores_clase_sensor_widget("widget_valor_digital_medio_acumulado_sensor");
    };
    $("#clase_sensor_widget_valor_digital_medio_acumulado_sensor").change(funcion_recarga_sensores_clase_sensor_widget_tipo_valor_digital_medio_acumulado_sensor);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_widget_tipo_valor_digital_medio_acumulado_sensor = function() {
        funcion_habilita_sensor_widget("widget_valor_digital_medio_acumulado_sensor");
    };
    $("#id_sensor_widget_valor_digital_medio_acumulado_sensor").show(funcion_habilita_sensor_widget_tipo_valor_digital_medio_acumulado_sensor);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_widget_tipo_valor_digital_medio_acumulado_sensor = function() {
        funcion_habilita_campo_widget("widget_valor_digital_medio_acumulado_sensor");
    };
    $("#campo_widget_valor_digital_medio_acumulado_sensor").show(funcion_habilita_campo_widget_tipo_valor_digital_medio_acumulado_sensor);

    // Recarga los campos de una clase de sensor del widget (en tipos de widget sin granularidad)
    var funcion_recarga_campos_sensor_clase_sensor_widget_tipo_valor_digital_medio_acumulado_sensor = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_valor_digital_medio_acumulado_sensor", "widget_valor_digital_medio_acumulado_sensor");
    };
    $("#clase_sensor_widget_valor_digital_medio_acumulado_sensor").change(funcion_recarga_campos_sensor_clase_sensor_widget_tipo_valor_digital_medio_acumulado_sensor);

    // Habilita y muestra los controles dependientes de utilizar los colores de fondo de valores del widget
    var funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_digital_medio_acumulado_sensor = function() {
        funcion_habilita_muestra_controles_utilizar_colores_fondo_widgets_valores_sensor("widget_valor_digital_medio_acumulado_sensor");
    };
    $("#utilizar_colores_fondo_widget_valor_digital_medio_acumulado_sensor").show(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_digital_medio_acumulado_sensor);
    $("#utilizar_colores_fondo_widget_valor_digital_medio_acumulado_sensor").change(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_digital_medio_acumulado_sensor);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_digital_medio_acumulado_sensor = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("widget_valor_digital_medio_acumulado_sensor");
    };
    $("#campo_widget_valor_digital_medio_acumulado_sensor").show(funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_digital_medio_acumulado_sensor);
    $("#campo_widget_valor_digital_medio_acumulado_sensor").change(funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_digital_medio_acumulado_sensor);

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_valor_digital_medio_acumulado_sensor = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_valor_digital_medio_acumulado_sensor");
    };
    $("#periodo_tiempo_widget_valor_digital_medio_acumulado_sensor").show(funcion_muestra_controles_periodo_tiempo_widget_valor_digital_medio_acumulado_sensor);
    $("#periodo_tiempo_widget_valor_digital_medio_acumulado_sensor").change(funcion_muestra_controles_periodo_tiempo_widget_valor_digital_medio_acumulado_sensor);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_valor_analogico_sensor = function() {
    $("#clase_sensor_widget_valor_analogico_sensor").off();
    $("#id_sensor_widget_valor_analogico_sensor").off();
    $("#campo_widget_valor_analogico_sensor").off();
    $("#granularidad_sensor_widget_valor_analogico_sensor").off();
    $("#utilizar_colores_fondo_widget_valor_analogico_sensor").off();

    // Muestra el control del ratio
    var funcion_muestra_control_ratio_widget_tipo_valor_analogico_sensor = function() {
        funcion_muestra_control_ratio_widget("widget_valor_analogico_sensor");
    };
    $("#clase_sensor_widget_valor_analogico_sensor").show(funcion_muestra_control_ratio_widget_tipo_valor_analogico_sensor);
    $("#clase_sensor_widget_valor_analogico_sensor").change(funcion_muestra_control_ratio_widget_tipo_valor_analogico_sensor);

    // Recarga los sensores del widget
    var funcion_recarga_sensores_clase_sensor_widget_tipo_valor_analogico_sensor = function() {
        funcion_recarga_sensores_clase_sensor_widget("widget_valor_analogico_sensor");
    };
    $("#clase_sensor_widget_valor_analogico_sensor").change(funcion_recarga_sensores_clase_sensor_widget_tipo_valor_analogico_sensor);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_widget_tipo_valor_analogico_sensor = function() {
        funcion_habilita_sensor_widget("widget_valor_analogico_sensor");
    };
    $("#id_sensor_widget_valor_analogico_sensor").show(funcion_habilita_sensor_widget_tipo_valor_analogico_sensor);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_widget_tipo_valor_analogico_sensor = function() {
        funcion_habilita_campo_widget("widget_valor_analogico_sensor");
    };
    $("#campo_widget_valor_analogico_sensor").show(funcion_habilita_campo_widget_tipo_valor_analogico_sensor);

    // Habilitación de granularidad de sensor del widget
    var funcion_habilita_granularidad_sensor_widget_tipo_valor_analogico_sensor = function() {
        funcion_habilita_granularidad_sensor_widget("widget_valor_analogico_sensor");
    };
    $("#granularidad_sensor_widget_valor_analogico_sensor").show(funcion_habilita_granularidad_sensor_widget_tipo_valor_analogico_sensor);
    $("#granularidad_sensor_widget_valor_analogico_sensor").change(funcion_habilita_granularidad_sensor_widget_tipo_valor_analogico_sensor);

    // Recarga los campos y las granularidades de una clase de sensor del widget
    // (en tipos de widget con granularidad, al cambiar la granularidad se recargarán los campos de sensor correspondientes)
    var funcion_recarga_granularidades_sensor_clase_sensor_widget_tipo_valor_analogico_sensor = function() {
        funcion_recarga_lista_granularidades_sensor_clase_sensor_widget("widget_valor_analogico_sensor");
    };
    $("#clase_sensor_widget_valor_analogico_sensor").change(funcion_recarga_granularidades_sensor_clase_sensor_widget_tipo_valor_analogico_sensor);

    // Funciones a realizar según la granularidad del sensor del widget
    var funcion_realiza_acciones_granularidad_sensor_widget_tipo_valor_analogico = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_valor_analogico_sensor", "widget_valor_analogico_sensor");
    };
    $("#granularidad_sensor_widget_valor_analogico_sensor").change(funcion_realiza_acciones_granularidad_sensor_widget_tipo_valor_analogico);

    // Realiza acciones al cambiar la clase de sensor del widget
    var funcion_realiza_acciones_clase_sensor_widget_valor_analogico_sensor = function() {
        $.post("./src/lib/modulos/widgets/dame_lista_valores_digitales_tipo_widget_valor_analogico_sensor.php", {
			clase_sensor: $("#clase_sensor_widget_valor_analogico_sensor").val(),
            valor_digital: $("#valor_digital_widget_valor_analogico_sensor").val()
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#valor_digital_widget_valor_analogico_sensor").html(resultado.html);
		});
    };
    $("#clase_sensor_widget_valor_analogico_sensor").change(funcion_realiza_acciones_clase_sensor_widget_valor_analogico_sensor);

    // Habilita y muestra los controles dependientes de utilizar los colores de fondo de valores del widget
    var funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_analogico_sensor = function() {
        funcion_habilita_muestra_controles_utilizar_colores_fondo_widgets_valores_sensor("widget_valor_analogico_sensor");
    };
    $("#utilizar_colores_fondo_widget_valor_analogico_sensor").show(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_analogico_sensor);
    $("#utilizar_colores_fondo_widget_valor_analogico_sensor").change(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_analogico_sensor);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_analogico_sensor = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("widget_valor_analogico_sensor");
    };
    $("#campo_widget_valor_analogico_sensor").show(funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_analogico_sensor);
    $("#campo_widget_valor_analogico_sensor").change(funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_analogico_sensor);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_valor_analogico_medio_acumulado_sensor = function() {
    $("#clase_sensor_widget_valor_analogico_medio_acumulado_sensor").off();
    $("#id_sensor_widget_valor_analogico_medio_acumulado_sensor").off();
    $("#campo_widget_valor_analogico_medio_acumulado_sensor").off();
    $("#utilizar_colores_fondo_widget_valor_analogico_medio_acumulado_sensor").off();
    $("#periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor").off();

    // Muestra el control del ratio
    var funcion_muestra_control_ratio_widget_tipo_valor_analogico_medio_acumulado_sensor = function() {
        funcion_muestra_control_ratio_widget("widget_valor_analogico_medio_acumulado_sensor");
    };
    $("#clase_sensor_widget_valor_analogico_medio_acumulado_sensor").show(funcion_muestra_control_ratio_widget_tipo_valor_analogico_medio_acumulado_sensor);
    $("#clase_sensor_widget_valor_analogico_medio_acumulado_sensor").change(funcion_muestra_control_ratio_widget_tipo_valor_analogico_medio_acumulado_sensor);

    // Recarga los sensores del widget
    var funcion_recarga_sensores_clase_sensor_widget_tipo_valor_analogico_medio_acumulado_sensor = function() {
        funcion_recarga_sensores_clase_sensor_widget("widget_valor_analogico_medio_acumulado_sensor");
    };
    $("#clase_sensor_widget_valor_analogico_medio_acumulado_sensor").change(funcion_recarga_sensores_clase_sensor_widget_tipo_valor_analogico_medio_acumulado_sensor);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_widget_tipo_valor_analogico_medio_acumulado_sensor = function() {
        funcion_habilita_sensor_widget("widget_valor_analogico_medio_acumulado_sensor");
    };
    $("#id_sensor_widget_valor_analogico_medio_acumulado_sensor").show(funcion_habilita_sensor_widget_tipo_valor_analogico_medio_acumulado_sensor);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_widget_tipo_valor_analogico_medio_acumulado_sensor = function() {
        funcion_habilita_campo_widget("widget_valor_analogico_medio_acumulado_sensor");
    };
    $("#campo_widget_valor_analogico_medio_acumulado_sensor").show(funcion_habilita_campo_widget_tipo_valor_analogico_medio_acumulado_sensor);

    // Recarga los campos de una clase de sensor del widget (en tipos de widget sin granularidad)
    var funcion_recarga_campos_sensor_clase_sensor_widget_tipo_valor_analogico_medio_acumulado_sensor = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_valor_analogico_medio_acumulado_sensor", "widget_valor_analogico_medio_acumulado_sensor");
    };
    $("#clase_sensor_widget_valor_analogico_medio_acumulado_sensor").change(funcion_recarga_campos_sensor_clase_sensor_widget_tipo_valor_analogico_medio_acumulado_sensor);

    // Habilita y muestra los controles dependientes de utilizar los colores de fondo de valores del widget
    var funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_analogico_medio_acumulado_sensor = function() {
        funcion_habilita_muestra_controles_utilizar_colores_fondo_widgets_valores_sensor("widget_valor_analogico_medio_acumulado_sensor");
    };
    $("#utilizar_colores_fondo_widget_valor_analogico_medio_acumulado_sensor").show(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_analogico_medio_acumulado_sensor);
    $("#utilizar_colores_fondo_widget_valor_analogico_medio_acumulado_sensor").change(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_analogico_medio_acumulado_sensor);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_analogico_medio_acumulado_sensor = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("widget_valor_analogico_medio_acumulado_sensor");
    };
    $("#campo_widget_valor_analogico_medio_acumulado_sensor").show(funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_analogico_medio_acumulado_sensor);
    $("#campo_widget_valor_analogico_medio_acumulado_sensor").change(funcion_muestra_control_parametros_extra_campo_administracion_tipo_valor_analogico_medio_acumulado_sensor);

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_valor_analogico_medio_acumulado_sensor");
    };
    $("#periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor").show(funcion_muestra_controles_periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor);
    $("#periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor").change(funcion_muestra_controles_periodo_tiempo_widget_valor_analogico_medio_acumulado_sensor);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_valores_sensor = function() {
    $("#clase_sensor_widget_grafica_valores_sensor").off();
    $("#id_sensor_widget_grafica_valores_sensor").off();
    $("#campo_widget_grafica_valores_sensor").off();
    $("#periodo_tiempo_widget_grafica_valores_sensor").off();
    $("#intervalo_valores_widget_grafica_valores_sensor").off();
    $("#periodo_tiempo_widget_grafica_valores_sensor").off();

    // Muestra el control del ratio
    var funcion_muestra_control_ratio_widget_tipo_grafica_valores_sensor = function() {
        funcion_muestra_control_ratio_widget("widget_grafica_valores_sensor");
    };
    $("#clase_sensor_widget_grafica_valores_sensor").show(funcion_muestra_control_ratio_widget_tipo_grafica_valores_sensor);
    $("#clase_sensor_widget_grafica_valores_sensor").change(funcion_muestra_control_ratio_widget_tipo_grafica_valores_sensor);

    // Recarga los sensores del widget
    var funcion_recarga_sensores_clase_sensor_widget_tipo_grafica_valores_sensor = function() {
        funcion_recarga_sensores_clase_sensor_widget("widget_grafica_valores_sensor");
    };
    $("#clase_sensor_widget_grafica_valores_sensor").change(funcion_recarga_sensores_clase_sensor_widget_tipo_grafica_valores_sensor);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_widget_tipo_grafica_valores_sensor = function() {
        funcion_habilita_sensor_widget("widget_grafica_valores_sensor");
    };
    $("#id_sensor_widget_grafica_valores_sensor").show(funcion_habilita_sensor_widget_tipo_grafica_valores_sensor);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_widget_tipo_grafica_valores_sensor = function() {
        funcion_habilita_campo_widget("widget_grafica_valores_sensor");
    };
    $("#campo_widget_grafica_valores_sensor").show(funcion_habilita_campo_widget_tipo_grafica_valores_sensor);

    // Recarga los campos de una clase de sensor del widget (en tipos de widget sin granularidad)
    var funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_valores_sensor = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_grafica_valores_sensor", "widget_grafica_valores_sensor");
    };
    $("#clase_sensor_widget_grafica_valores_sensor").change(funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_valores_sensor);

    // Habilita y muestra los controles dependientes de la clase de sensor del widget
    var funcion_habilita_muestra_controles_clase_sensor_graficas_sensor_widget = function(id_controles) {
        var clase_sensor = $("#clase_sensor_" + id_controles).val();
        switch (clase_sensor) {
            case CLASE_NINGUNA: {
                $("#control_campo_" + id_controles).hide();
                $("#control_intervalo_valores_" + id_controles).hide();
                break;
            }
            default: {
                $("#control_campo_" + id_controles).show();
                $("#control_intervalo_valores_" + id_controles).show();
                break;
            }
        }
    };
    var funcion_habilita_muestra_controles_clase_sensor_widget_grafica_valores_sensor = function() {
        funcion_habilita_muestra_controles_clase_sensor_graficas_sensor_widget("widget_grafica_valores_sensor");
    };
    $("#clase_sensor_widget_grafica_valores_sensor").show(funcion_habilita_muestra_controles_clase_sensor_widget_grafica_valores_sensor);
    $("#clase_sensor_widget_grafica_valores_sensor").change(funcion_habilita_muestra_controles_clase_sensor_widget_grafica_valores_sensor);

    // Realiza acciones al cambiar el campo de sensor del widget
    var funcion_realiza_acciones_campo_widget_grafica_valores_sensor = function() {
        funcion_recarga_intervalos_valores_sensor_widget("widget_grafica_valores_sensor", "widget_grafica_valores_sensor");
    };
    $("#campo_widget_grafica_valores_sensor").change(funcion_realiza_acciones_campo_widget_grafica_valores_sensor);

    // Realiza acciones al cambiar el periodo de tiempo del widget
    var funcion_realiza_acciones_periodo_tiempo_widget_grafica_valores_sensor = function() {
        funcion_recarga_intervalos_valores_sensor_widget("widget_grafica_valores_sensor", "widget_grafica_valores_sensor");
    };
    $("#periodo_tiempo_widget_grafica_valores_sensor").change(funcion_realiza_acciones_periodo_tiempo_widget_grafica_valores_sensor);

    // Habilitación del selector de intervalo de valores del widget
    var funcion_habilita_intervalo_valores_widget_grafica_valores_sensor  = function() {
        funcion_habilita_intervalo_valores_widget("widget_grafica_valores_sensor");
    };
    $("#intervalo_valores_widget_grafica_valores_sensor ").show(funcion_habilita_intervalo_valores_widget_grafica_valores_sensor);
    $("#intervalo_valores_widget_grafica_valores_sensor ").change(funcion_habilita_intervalo_valores_widget_grafica_valores_sensor);

    // Realiza acciones al cambiar el intervalo de valores del widget
    var funcion_recarga_campos_intervalo_valores_widget_grafica_valores_sensor = function() {
        funcion_recarga_campos_intervalo_valores_widget("widget_grafica_valores_sensor", "widget_grafica_valores_sensor");
    };
    $("#intervalo_valores_widget_grafica_valores_sensor").change(funcion_recarga_campos_intervalo_valores_widget_grafica_valores_sensor);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_administracion_tipo_grafica_valores_sensor = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("widget_grafica_valores_sensor");
    };
    $("#campo_widget_grafica_valores_sensor").show(funcion_muestra_control_parametros_extra_campo_administracion_tipo_grafica_valores_sensor);
    $("#campo_widget_grafica_valores_sensor").change(funcion_muestra_control_parametros_extra_campo_administracion_tipo_grafica_valores_sensor);

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_grafica_valores_sensor = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_grafica_valores_sensor");
    };
    $("#periodo_tiempo_widget_grafica_valores_sensor").show(funcion_muestra_controles_periodo_tiempo_widget_grafica_valores_sensor);
    $("#periodo_tiempo_widget_grafica_valores_sensor").change(funcion_muestra_controles_periodo_tiempo_widget_grafica_valores_sensor);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_mapa_calor_sensor = function() {
    $("#clase_sensor_widget_mapa_calor_sensor").off();
    $("#id_sensor_widget_mapa_calor_sensor").off();
    $("#campo_widget_mapa_calor_sensor").off();
    $("#periodo_tiempo_widget_mapa_calor_sensor").off();
    $("#intervalo_valores_widget_mapa_calor_sensor").off();
    $("#periodo_tiempo_widget_mapa_calor_sensor").off();

    // Muestra el control del ratio
    var funcion_muestra_control_ratio_widget_tipo_mapa_calor_sensor = function() {
        funcion_muestra_control_ratio_widget("widget_mapa_calor_sensor");
    };
    $("#clase_sensor_widget_mapa_calor_sensor").show(funcion_muestra_control_ratio_widget_tipo_mapa_calor_sensor);
    $("#clase_sensor_widget_mapa_calor_sensor").change(funcion_muestra_control_ratio_widget_tipo_mapa_calor_sensor);

    // Recarga los sensores del widget
    var funcion_recarga_sensores_clase_sensor_widget_tipo_mapa_calor_sensor = function() {
        funcion_recarga_sensores_clase_sensor_widget("widget_mapa_calor_sensor");
    };
    $("#clase_sensor_widget_mapa_calor_sensor").change(funcion_recarga_sensores_clase_sensor_widget_tipo_mapa_calor_sensor);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_widget_tipo_mapa_calor_sensor = function() {
        funcion_habilita_sensor_widget("widget_mapa_calor_sensor");
    };
    $("#id_sensor_widget_mapa_calor_sensor").show(funcion_habilita_sensor_widget_tipo_mapa_calor_sensor);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_widget_tipo_mapa_calor_sensor = function() {
        funcion_habilita_campo_widget("widget_mapa_calor_sensor");
    };
    $("#campo_widget_mapa_calor_sensor").show(funcion_habilita_campo_widget_tipo_mapa_calor_sensor);

    // Recarga los campos de una clase de sensor del widget (en tipos de widget sin granularidad)
    var funcion_recarga_campos_sensor_clase_sensor_widget_tipo_mapa_calor_sensor = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_mapa_calor_sensor", "widget_mapa_calor_sensor");
    };
    $("#clase_sensor_widget_mapa_calor_sensor").change(funcion_recarga_campos_sensor_clase_sensor_widget_tipo_mapa_calor_sensor);

    // Realiza acciones al cambiar el periodo de tiempo del widget
    var funcion_realiza_acciones_periodo_tiempo_widget_mapa_calor_sensor = function() {
        funcion_recarga_intervalos_valores_sensor_widget("widget_mapa_calor_sensor", "widget_mapa_calor_sensor");
    };
    $("#periodo_tiempo_widget_mapa_calor_sensor").change(funcion_realiza_acciones_periodo_tiempo_widget_mapa_calor_sensor);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_administracion_tipo_mapa_calor_sensor = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("widget_mapa_calor_sensor");
    };
    $("#campo_widget_mapa_calor_sensor").show(funcion_muestra_control_parametros_extra_campo_administracion_tipo_mapa_calor_sensor);
    $("#campo_widget_mapa_calor_sensor").change(funcion_muestra_control_parametros_extra_campo_administracion_tipo_mapa_calor_sensor);

    // Realiza acciones dependiendo del periodo de tiempo del widget
    var funcion_realiza_acciones_periodo_tiempo_widget_mapa_calor_sensor = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_mapa_calor_sensor");

        // Establece el tipo de mapa de calor correspondiente
        var periodo_tiempo = $("#periodo_tiempo_widget_mapa_calor_sensor").val();
        switch (periodo_tiempo) {
            case PERIODO_TIEMPO_ANYO:
            case PERIODO_TIEMPO_FECHA_INICIO: {
                $("#tipo_mapa_calor_widget_mapa_calor_sensor").val(TIPO_MAPA_CALOR_SEMANAL);
                $("#tipo_mapa_calor_widget_mapa_calor_sensor").attr('disabled', true);
                break;
            }
            default: {
                $("#tipo_mapa_calor_widget_mapa_calor_sensor").removeAttr('disabled');
                break;
            }
        }
    };
    $("#periodo_tiempo_widget_mapa_calor_sensor").show(funcion_realiza_acciones_periodo_tiempo_widget_mapa_calor_sensor);
    $("#periodo_tiempo_widget_mapa_calor_sensor").change(funcion_realiza_acciones_periodo_tiempo_widget_mapa_calor_sensor);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_comparacion_periodos_sensor = function() {
    $("#clase_sensor_widget_grafica_comparacion_periodos_sensor").off();
    $("#id_sensor_widget_grafica_comparacion_periodos_sensor").off();
    $("#campo_widget_grafica_comparacion_periodos_sensor").off();
    $("#periodo_tiempo_widget_grafica_comparacion_periodos_sensor").off();
    $("#intervalo_valores_widget_grafica_comparacion_periodos_sensor").off();

    // Recarga los sensores del widget
    var funcion_recarga_sensores_clase_sensor_widget_tipo_grafica_comparacion_periodos_sensor = function() {
        funcion_recarga_sensores_clase_sensor_widget("widget_grafica_comparacion_periodos_sensor");
    };
    $("#clase_sensor_widget_grafica_comparacion_periodos_sensor").change(funcion_recarga_sensores_clase_sensor_widget_tipo_grafica_comparacion_periodos_sensor);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_widget_tipo_grafica_comparacion_periodos_sensor = function() {
        funcion_habilita_sensor_widget("widget_grafica_comparacion_periodos_sensor");
    };
    $("#id_sensor_widget_grafica_comparacion_periodos_sensor").show(funcion_habilita_sensor_widget_tipo_grafica_comparacion_periodos_sensor);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_widget_tipo_grafica_comparacion_periodos_sensor = function() {
        funcion_habilita_campo_widget("widget_grafica_comparacion_periodos_sensor");
    };
    $("#campo_widget_grafica_comparacion_periodos_sensor").show(funcion_habilita_campo_widget_tipo_grafica_comparacion_periodos_sensor);

    // Recarga los campos de una clase de sensor del widget (en tipos de widget sin granularidad)
    var funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_comparacion_periodos_sensor = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_grafica_comparacion_periodos_sensor", "widget_grafica_comparacion_periodos_sensor");
    };
    $("#clase_sensor_widget_grafica_comparacion_periodos_sensor").change(funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_comparacion_periodos_sensor);

    // Realiza acciones al cambiar el campo de sensor del widget
    var funcion_realiza_acciones_campo_widget_grafica_comparacion_periodos_sensor = function() {
        funcion_recarga_intervalos_valores_sensor_widget("widget_grafica_comparacion_periodos_sensor", "widget_grafica_comparacion_periodos_sensor");
    };
    $("#campo_widget_grafica_comparacion_periodos_sensor").change(funcion_realiza_acciones_campo_widget_grafica_comparacion_periodos_sensor);

    // Realiza acciones al cambiar el periodo de tiempo del widget
    var funcion_realiza_acciones_periodo_tiempo_widget_grafica_comparacion_periodos_sensor = function() {
        funcion_recarga_intervalos_valores_sensor_widget("widget_grafica_comparacion_periodos_sensor", "widget_grafica_comparacion_periodos_sensor");
    };
    $("#periodo_tiempo_widget_grafica_comparacion_periodos_sensor").change(funcion_realiza_acciones_periodo_tiempo_widget_grafica_comparacion_periodos_sensor);

    // Habilitación del selector de intervalo de valores del widget
    var funcion_habilita_intervalo_valores_widget_grafica_comparacion_periodos_sensor  = function() {
        funcion_habilita_intervalo_valores_widget("widget_grafica_comparacion_periodos_sensor");
    };
    $("#intervalo_valores_widget_grafica_comparacion_periodos_sensor ").show(funcion_habilita_intervalo_valores_widget_grafica_comparacion_periodos_sensor);
    $("#intervalo_valores_widget_grafica_comparacion_periodos_sensor ").change(funcion_habilita_intervalo_valores_widget_grafica_comparacion_periodos_sensor);

    // Realiza acciones al cambiar el intervalo de valores del widget
    var funcion_recarga_campos_intervalo_valores_widget_grafica_comparacion_periodos_sensor = function() {
        funcion_recarga_campos_intervalo_valores_widget("widget_grafica_comparacion_periodos_sensor", "widget_grafica_comparacion_periodos_sensor");
    };
    $("#intervalo_valores_widget_grafica_comparacion_periodos_sensor").change(funcion_recarga_campos_intervalo_valores_widget_grafica_comparacion_periodos_sensor);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_administracion_tipo_grafica_comparacion_periodos_sensor = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("widget_grafica_comparacion_periodos_sensor");
    };
    $("#campo_widget_grafica_comparacion_periodos_sensor").show(funcion_muestra_control_parametros_extra_campo_administracion_tipo_grafica_comparacion_periodos_sensor);
    $("#campo_widget_grafica_comparacion_periodos_sensor").change(funcion_muestra_control_parametros_extra_campo_administracion_tipo_grafica_comparacion_periodos_sensor);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_evolucion_valores_comparacion_periodos_sensor = function() {
    $("#clase_sensor_widget_evolucion_valores_comparacion_periodos_sensor").off();
    $("#id_sensor_widget_evolucion_valores_comparacion_periodos_sensor").off();
    $("#campo_widget_evolucion_valores_comparacion_periodos_sensor").off();
    $("#periodo_tiempo_widget_evolucion_valores_comparacion_periodos_sensor").off();
    $("#intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor").off();
    $("#utilizar_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor").off();

    // Recarga los sensores del widget
    var funcion_recarga_sensores_clase_sensor_widget_tipo_evolucion_valores_comparacion_periodos_sensor = function() {
        funcion_recarga_sensores_clase_sensor_widget("widget_evolucion_valores_comparacion_periodos_sensor");
    };
    $("#clase_sensor_widget_evolucion_valores_comparacion_periodos_sensor").change(funcion_recarga_sensores_clase_sensor_widget_tipo_evolucion_valores_comparacion_periodos_sensor);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_widget_evolucion_valores_comparacion_periodos_sensor = function() {
        funcion_habilita_sensor_widget("widget_evolucion_valores_comparacion_periodos_sensor");
    };
    $("#id_sensor_widget_evolucion_valores_comparacion_periodos_sensor").show(funcion_habilita_sensor_widget_evolucion_valores_comparacion_periodos_sensor);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_widget_tipo_evolucion_valores_comparacion_periodos_sensor = function() {
        funcion_habilita_campo_widget("widget_evolucion_valores_comparacion_periodos_sensor");
    };
    $("#campo_widget_evolucion_valores_comparacion_periodos_sensor").show(funcion_habilita_campo_widget_tipo_evolucion_valores_comparacion_periodos_sensor);

    // Recarga los campos de una clase de sensor del widget (en tipos de widget sin granularidad)
    var funcion_recarga_campos_sensor_clase_sensor_widget_tipo_evolucion_valores_comparacion_periodos_sensor = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_evolucion_valores_comparacion_periodos_sensor", "widget_evolucion_valores_comparacion_periodos_sensor");
    };
    $("#clase_sensor_widget_evolucion_valores_comparacion_periodos_sensor").change(funcion_recarga_campos_sensor_clase_sensor_widget_tipo_evolucion_valores_comparacion_periodos_sensor);

    // Realiza acciones al cambiar el campo de sensor del widget
    var funcion_realiza_acciones_campo_widget_evolucion_valores_comparacion_periodos_sensor = function() {
        funcion_recarga_intervalos_valores_sensor_widget("widget_evolucion_valores_comparacion_periodos_sensor", "widget_evolucion_valores_comparacion_periodos_sensor");
    };
    $("#campo_widget_evolucion_valores_comparacion_periodos_sensor").change(funcion_realiza_acciones_campo_widget_evolucion_valores_comparacion_periodos_sensor);

    // Realiza acciones al cambiar el periodo de tiempo del widget
    var funcion_realiza_acciones_periodo_tiempo_widget_evolucion_valores_comparacion_periodos_sensor = function() {
        funcion_recarga_intervalos_valores_sensor_widget("widget_evolucion_valores_comparacion_periodos_sensor", "widget_evolucion_valores_comparacion_periodos_sensor");
    };
    $("#periodo_tiempo_widget_evolucion_valores_comparacion_periodos_sensor").change(funcion_realiza_acciones_periodo_tiempo_widget_evolucion_valores_comparacion_periodos_sensor);

    // Habilitación del selector de intervalo de valores del widget
    var funcion_habilita_intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor  = function() {
        funcion_habilita_intervalo_valores_widget("widget_evolucion_valores_comparacion_periodos_sensor");
    };
    $("#intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor ").show(funcion_habilita_intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor);
    $("#intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor ").change(funcion_habilita_intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor);

    // Realiza acciones al cambiar el intervalo de valores del widget
    var funcion_recarga_campos_intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor = function() {
        funcion_recarga_campos_intervalo_valores_widget("widget_evolucion_valores_comparacion_periodos_sensor", "widget_evolucion_valores_comparacion_periodos_sensor");
    };
    $("#intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor").change(funcion_recarga_campos_intervalo_valores_widget_evolucion_valores_comparacion_periodos_sensor);

    // Realiza acciones acciones al mostrar o cambiar el campo del widget
    var funcion_realiza_acciones_campo_widget_evolucion_valores_comparacion_periodos_sensor = function() {
        // Muestra el control de parametros extra de campo de sensor del widget
        funcion_muestra_control_parametros_extra_campo_administracion("widget_evolucion_valores_comparacion_periodos_sensor");

        // Tipo de valores de límite de colores
        var clase_sensor = $("#clase_sensor_widget_evolucion_valores_comparacion_periodos_sensor").val();
        var campo = $("#campo_widget_evolucion_valores_comparacion_periodos_sensor").val();
        if (clase_sensor != CLASE_NINGUNA) {
            $.post("./src/lib/modulos/dame_tipo_valores_campo_clase_sensor.php", {
                clase_sensor: clase_sensor,
                campo: campo
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                switch (resultado.tipo_valores) {
                    case TIPO_VALORES_SENSOR_PUNTUALES: {
                        $("#tipo_valores_limite_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor").val(TIPO_VALORES_LIMITE_COLORES_FONDO_WIDGET_ABSOLUTO);
                        $("#tipo_valores_limite_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor").attr('disabled', true);
                        break;
                    }
                    case TIPO_VALORES_SENSOR_INCREMENTALES: {
                        $("#tipo_valores_limite_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor").removeAttr('disabled');
                        break;
                    }
                }
            });
        }
    };
    $("#campo_widget_evolucion_valores_comparacion_periodos_sensor").show(funcion_realiza_acciones_campo_widget_evolucion_valores_comparacion_periodos_sensor);
    $("#campo_widget_evolucion_valores_comparacion_periodos_sensor").change(funcion_realiza_acciones_campo_widget_evolucion_valores_comparacion_periodos_sensor);

    // Habilita y muestra los controles dependientes de utilizar los colores de fondo de valores del widget
    var funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor = function() {
        funcion_habilita_muestra_controles_utilizar_colores_fondo_widgets_valores_sensor("widget_evolucion_valores_comparacion_periodos_sensor");

        // Control de tipo de valores límite de colores de fondo
        var utilizar_colores_fondo = parseInt($("#utilizar_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor").val());
        switch (utilizar_colores_fondo) {
            case VALOR_SI: {
                $("#control_tipo_valores_limite_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor").show();
                break;
            }
            case VALOR_NO: {
                $("#control_tipo_valores_limite_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor").hide();
                break;
            }
        }
    };
    $("#utilizar_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor").show(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor);
    $("#utilizar_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor").change(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_evolucion_valores_comparacion_periodos_sensor);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_comparacion_campos_iguales_sensores = function() {
    $("#clase_sensor_widget_grafica_comparacion_campos_iguales_sensores").off();
    $("#id_sensor_widget_grafica_comparacion_campos_iguales_sensores").off();
    $("#campo_widget_grafica_comparacion_campos_iguales_sensores").off();
    $("#periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores").off();
    $("#intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores").off();

    // Mostrar lista doble para las listas dobles de sensores en los widgets de sensores
    $("#contenido_modal").on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var tab = $(e.target);
        var id_pestanya = tab.attr("href");

        if (id_pestanya == "#tab-tipo-grafica-comparacion-campos-iguales-sensores-sensores") {
            if ($('#select_sensores_widget_grafica_comparacion_campos_iguales_sensores_no_visible').length) {
                $('#select_sensores_widget_grafica_comparacion_campos_iguales_sensores_no_visible').attr("id", "select_sensores_widget_grafica_comparacion_campos_iguales_sensores_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_sensores_widget_grafica_comparacion_campos_iguales_sensores", false);
            }
        }
    });

    // Recarga la lista de sensores del widget
    var funcion_recarga_lista_sensores_tipo_widget_grafica_comparacion_campos_iguales_sensores = function() {
        var clase_sensor = $("#clase_sensor_widget_grafica_comparacion_campos_iguales_sensores").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores.php", {
			clase_sensor: clase_sensor,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Recarga de lista doble
            // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
            if ($('#select_sensores_widget_grafica_comparacion_campos_iguales_sensores_no_visible').length) {
                $("#ids_sensores_widget_grafica_comparacion_campos_iguales_sensores").html(resultado.html);
            }
            else {
                $("#ids_sensores_widget_grafica_comparacion_campos_iguales_sensores").multiselect2side('destroy');
                $("#ids_sensores_widget_grafica_comparacion_campos_iguales_sensores").html(resultado.html);
                TLNT.Navegacion.convierte_lista_doble("ids_sensores_widget_grafica_comparacion_campos_iguales_sensores", true);
            }
        });
    };
    $("#clase_sensor_widget_grafica_comparacion_campos_iguales_sensores").change(funcion_recarga_lista_sensores_tipo_widget_grafica_comparacion_campos_iguales_sensores);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_widget_grafica_comparacion_campos_iguales_sensores = function() {
        funcion_habilita_sensor_widget("widget_grafica_comparacion_campos_iguales_sensores");
    };
    $("#id_sensor_widget_grafica_comparacion_campos_iguales_sensores").show(funcion_habilita_sensor_widget_grafica_comparacion_campos_iguales_sensores);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_widget_tipo_comparacion_campos_iguales_sensores = function() {
        funcion_habilita_campo_widget("widget_grafica_comparacion_campos_iguales_sensores");
    };
    $("#campo_widget_grafica_comparacion_campos_iguales_sensores").show(funcion_habilita_campo_widget_tipo_comparacion_campos_iguales_sensores);

    // Recarga los campos de una clase de sensor del widget (en tipos de widget sin granularidad)
    var funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_comparacion_campos_iguales_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_grafica_comparacion_campos_iguales_sensores", "widget_grafica_comparacion_campos_iguales_sensores");
    };
    $("#clase_sensor_widget_grafica_comparacion_campos_iguales_sensores").change(funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_comparacion_campos_iguales_sensores);

    // Realiza acciones al cambiar el campo de sensor del widget
    var funcion_realiza_acciones_campo_widget_grafica_comparacion_campos_iguales_sensores = function() {
        funcion_recarga_intervalos_valores_sensor_widget("widget_grafica_comparacion_campos_iguales_sensores", "widget_grafica_comparacion_campos_iguales_sensores");
    };
    $("#campo_widget_grafica_comparacion_campos_iguales_sensores").change(funcion_realiza_acciones_campo_widget_grafica_comparacion_campos_iguales_sensores);

    // Realiza acciones al cambiar el periodo de tiempo del widget
    var funcion_realiza_acciones_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores = function() {
        funcion_recarga_intervalos_valores_sensor_widget("widget_grafica_comparacion_campos_iguales_sensores", "widget_grafica_comparacion_campos_iguales_sensores");
    };
    $("#periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores").change(funcion_realiza_acciones_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores);

    // Realiza acciones al cambiar el intervalo de valores del widget
    var funcion_recarga_campos_intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores = function() {
        funcion_recarga_campos_intervalo_valores_widget("widget_grafica_comparacion_campos_iguales_sensores", "widget_grafica_comparacion_campos_iguales_sensores");
    };
    $("#intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores").change(funcion_recarga_campos_intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_administracion_tipo_comparacion_campos_iguales_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("widget_grafica_comparacion_campos_iguales_sensores");
    };
    $("#campo_widget_grafica_comparacion_campos_iguales_sensores").show(funcion_muestra_control_parametros_extra_campo_administracion_tipo_comparacion_campos_iguales_sensores);
    $("#campo_widget_grafica_comparacion_campos_iguales_sensores").change(funcion_muestra_control_parametros_extra_campo_administracion_tipo_comparacion_campos_iguales_sensores);

    // Habilitación del selector de intervalo de valores de comparación de valores generales
    var funcion_habilita_intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores = function() {
        funcion_habilita_intervalo_valores_widget("widget_grafica_comparacion_campos_iguales_sensores");
    };
    $("#intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores").show(funcion_habilita_intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores);
    $("#intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores").change(funcion_habilita_intervalo_valores_widget_grafica_comparacion_campos_iguales_sensores);

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_grafica_comparacion_campos_iguales_sensores");
    };
    $("#periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores").show(funcion_muestra_controles_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores);
    $("#periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores").change(funcion_muestra_controles_periodo_tiempo_widget_grafica_comparacion_campos_iguales_sensores);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_comparacion_campos_diferentes_sensores = function() {
    $("#clase_sensor_1_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#clase_sensor_2_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#clase_sensor_3_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#clase_sensor_4_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#clase_sensor_5_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#id_sensor_1_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#id_sensor_2_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#id_sensor_3_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#id_sensor_4_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#id_sensor_5_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#campo_1_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#campo_2_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#campo_3_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#campo_4_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#campo_5_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores").off();
    $("#periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores").off();

    // Recarga los sensores del widget
    var funcion_recarga_sensores_clase_sensor_1_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_recarga_sensores_clase_sensor_widget("1_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#clase_sensor_1_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_recarga_sensores_clase_sensor_1_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_recarga_sensores_clase_sensor_2_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_recarga_sensores_clase_sensor_widget("2_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#clase_sensor_2_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_recarga_sensores_clase_sensor_2_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_recarga_sensores_clase_sensor_3_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_recarga_sensores_clase_sensor_widget("3_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#clase_sensor_3_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_recarga_sensores_clase_sensor_3_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_recarga_sensores_clase_sensor_4_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_recarga_sensores_clase_sensor_widget("4_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#clase_sensor_4_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_recarga_sensores_clase_sensor_4_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_recarga_sensores_clase_sensor_5_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_recarga_sensores_clase_sensor_widget("5_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#clase_sensor_5_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_recarga_sensores_clase_sensor_5_widget_tipo_grafica_comparacion_campos_diferentes_sensores);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_1_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_habilita_sensor_widget("1_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#id_sensor_1_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_sensor_1_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_sensor_2_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_habilita_sensor_widget("2_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#id_sensor_2_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_sensor_2_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_sensor_3_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_habilita_sensor_widget("3_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#id_sensor_3_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_sensor_3_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_sensor_4_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_habilita_sensor_widget("4_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#id_sensor_4_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_sensor_4_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_sensor_5_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_habilita_sensor_widget("5_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#id_sensor_5_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_sensor_5_widget_tipo_grafica_comparacion_campos_diferentes_sensores);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_1_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_habilita_campo_widget("1_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#campo_1_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_campo_1_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_campo_2_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_habilita_campo_widget("2_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#campo_2_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_campo_2_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_campo_3_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_habilita_campo_widget("3_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#campo_3_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_campo_3_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_campo_4_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_habilita_campo_widget("4_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#campo_4_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_campo_4_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_campo_5_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_habilita_campo_widget("5_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#campo_5_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_campo_5_widget_tipo_grafica_comparacion_campos_diferentes_sensores);

    // Recarga los campos de una clase de sensor del widget (en tipos de widget sin granularidad)
    var funcion_recarga_campos_sensor_1_clase_sensor_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_grafica_comparacion_campos_diferentes_sensores", "1_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#clase_sensor_1_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_recarga_campos_sensor_1_clase_sensor_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_recarga_campos_sensor_2_clase_sensor_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_grafica_comparacion_campos_diferentes_sensores", "2_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#clase_sensor_2_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_recarga_campos_sensor_2_clase_sensor_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_recarga_campos_sensor_3_clase_sensor_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_grafica_comparacion_campos_diferentes_sensores", "3_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#clase_sensor_3_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_recarga_campos_sensor_3_clase_sensor_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_recarga_campos_sensor_4_clase_sensor_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_grafica_comparacion_campos_diferentes_sensores", "4_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#clase_sensor_4_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_recarga_campos_sensor_4_clase_sensor_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_recarga_campos_sensor_5_clase_sensor_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget("widget_grafica_comparacion_campos_diferentes_sensores", "5_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#clase_sensor_5_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_recarga_campos_sensor_5_clase_sensor_widget_tipo_grafica_comparacion_campos_diferentes_sensores);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_habilita_campo_1_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("1_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#campo_1_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_campo_1_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    $("#campo_1_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_habilita_campo_1_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_campo_2_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("2_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#campo_2_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_campo_2_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    $("#campo_2_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_habilita_campo_2_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_campo_3_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("3_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#campo_3_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_campo_3_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    $("#campo_3_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_habilita_campo_3_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_campo_4_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("4_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#campo_4_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_campo_4_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    $("#campo_4_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_habilita_campo_4_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    var funcion_habilita_campo_5_widget_tipo_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("5_widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#campo_5_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_habilita_campo_5_widget_tipo_grafica_comparacion_campos_diferentes_sensores);
    $("#campo_5_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_habilita_campo_5_widget_tipo_grafica_comparacion_campos_diferentes_sensores);

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_grafica_comparacion_campos_diferentes_sensores");
    };
    $("#periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores").show(funcion_muestra_controles_periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores);
    $("#periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores").change(funcion_muestra_controles_periodo_tiempo_widget_grafica_comparacion_campos_diferentes_sensores);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_valores_generales_sensores = function() {
    $("#clase_sensor_1_widget_grafica_valores_generales_sensores").off();
    $("#clase_sensor_2_widget_grafica_valores_generales_sensores").off();
    $("#clase_sensor_3_widget_grafica_valores_generales_sensores").off();
    $("#campo_1_widget_grafica_valores_generales_sensores").off();
    $("#campo_2_widget_grafica_valores_generales_sensores").off();
    $("#campo_3_widget_grafica_valores_generales_sensores").off();
    $("#intervalo_valores_widget_grafica_valores_generales_sensores").off();
    $("#agregacion_widget_grafica_valores_generales_sensores").off();
    $("#periodo_tiempo_widget_grafica_valores_generales_sensores").off();

    // Mostrar lista doble para las listas dobles de sensores en los widgets de sensores
    $("#contenido_modal").on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var tab = $(e.target);
        var id_pestanya = tab.attr("href");

        if (id_pestanya == "#tab-tipo-grafica-valores-generales-sensores-sensores") {
            if ($('#select_sensores_widget_grafica_valores_generales_sensores_no_visible').length) {
                $('#select_sensores_widget_grafica_valores_generales_sensores_no_visible').attr("id", "select_sensores_widget_grafica_valores_generales_sensores_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_sensores_widget_grafica_valores_generales_sensores", false);
            }
        }
    });

    // Recarga la lista de sensores del widget
    var funcion_recarga_lista_sensores_tipo_widget_grafica_valores_generales_sensores = function() {
        var clases_sensor = [];
        for (var i = 0; i < NUMERO_CLASES_SENSOR_VALORES_GENERALES; i++) {
            var numero_clase_sensor = i + 1;
            var id_lista_clases_sensor = "clase_sensor_" + numero_clase_sensor + "_widget_grafica_valores_generales_sensores";
            var clase_sensor = $("#" + id_lista_clases_sensor).val();
            clases_sensor.push(clase_sensor);
        }
        var ids_sensores = [];
        $("#ids_sensores_widget_grafica_valores_generales_sensores option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_sensores.push($(this).val());
            }
        });
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores_clases.php", {
			clases_sensor: clases_sensor,
            ids_sensores: ids_sensores,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Recarga de lista doble
            if ($('#select_sensores_widget_grafica_valores_generales_sensores_no_visible').length) {
                $("#ids_sensores_widget_grafica_valores_generales_sensores").html(resultado.html);
            }
            else {
                // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
                $("#ids_sensores_widget_grafica_valores_generales_sensores").multiselect2side('destroy');
                $("#ids_sensores_widget_grafica_valores_generales_sensores").html(resultado.html);
                TLNT.Navegacion.convierte_lista_doble("ids_sensores_widget_grafica_valores_generales_sensores", true);
            }
        });
    };
    $("#clase_sensor_1_widget_grafica_valores_generales_sensores").change(funcion_recarga_lista_sensores_tipo_widget_grafica_valores_generales_sensores);
    $("#clase_sensor_2_widget_grafica_valores_generales_sensores").change(funcion_recarga_lista_sensores_tipo_widget_grafica_valores_generales_sensores);
    $("#clase_sensor_3_widget_grafica_valores_generales_sensores").change(funcion_recarga_lista_sensores_tipo_widget_grafica_valores_generales_sensores);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_1_widget_tipo_grafica_valores_generales_sensores = function() {
        funcion_habilita_campo_widget("1_widget_grafica_valores_generales_sensores");
    };
    $("#campo_1_widget_grafica_valores_generales_sensores").show(funcion_habilita_campo_1_widget_tipo_grafica_valores_generales_sensores);
    var funcion_habilita_campo_2_widget_tipo_grafica_valores_generales_sensores = function() {
        funcion_habilita_campo_widget("2_widget_grafica_valores_generales_sensores");
    };
    $("#campo_2_widget_grafica_valores_generales_sensores").show(funcion_habilita_campo_2_widget_tipo_grafica_valores_generales_sensores);
    var funcion_habilita_campo_3_widget_tipo_grafica_valores_generales_sensores = function() {
        funcion_habilita_campo_widget("3_widget_grafica_valores_generales_sensores");
    };
    $("#campo_3_widget_grafica_valores_generales_sensores").show(funcion_habilita_campo_3_widget_tipo_grafica_valores_generales_sensores);

    // Recarga los campos de una clase de sensor del widget
    var funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_valores_generales_sensores = function(numero_campo) {
        var clase_sensor = $("#clase_sensor_" + numero_campo + "_widget_grafica_valores_generales_sensores").val();
        var intervalo_valores = $("#intervalo_valores_widget_grafica_valores_generales_sensores").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_campos_clase_sensor_parametros_extra.php", {
            clase_sensor: clase_sensor,
            tipo_agrupacion_valores: true,
            intervalo_valores: intervalo_valores,
            campo: ""
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#campo_" + numero_campo + "_widget_grafica_valores_generales_sensores").html(resultado.html);

            // Se deshabilita si sólo hay un valor para elegir
            var numero_campos = $("select#campo_" + numero_campo + "_widget_grafica_valores_generales_sensores option").length;
            if (numero_campos <= 1) {
                $("#campo_" + numero_campo + "_widget_grafica_valores_generales_sensores").attr('disabled', true);
            }
            else {
                $("#campo_" + numero_campo + "_widget_grafica_valores_generales_sensores").removeAttr('disabled');
            }

            // Valor seleccionado por defecto
            switch (clase_sensor) {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                case CLASE_SENSOR_AGUA: {
                    $("#campo_" + numero_campo + "_widget_grafica_valores_generales_sensores").val(CAMPO_INCREMENTO);
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA: {
                    $("#campo_" + numero_campo + "_widget_grafica_valores_generales_sensores").val(CAMPO_CONSUMO_ESTIMADO);
                    break;
                }
                case CLASE_SENSOR_GAS: {
                    $("#campo_" + numero_campo + "_widget_grafica_valores_generales_sensores").val(CAMPO_CONSUMO);
                    break;
                }
            }

            $("#campo_" + numero_campo + "_widget_grafica_valores_generales_sensores").trigger('change');
        });
    };
    var funcion_recarga_campos_sensor_clase_sensor_1_widget_tipo_grafica_valores_generales_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_valores_generales_sensores(1);
    };
    $("#clase_sensor_1_widget_grafica_valores_generales_sensores").change(funcion_recarga_campos_sensor_clase_sensor_1_widget_tipo_grafica_valores_generales_sensores);
    var funcion_recarga_campos_sensor_clase_sensor_2_widget_tipo_grafica_valores_generales_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_valores_generales_sensores(2);
    };
    $("#clase_sensor_2_widget_grafica_valores_generales_sensores").change(funcion_recarga_campos_sensor_clase_sensor_2_widget_tipo_grafica_valores_generales_sensores);
    var funcion_recarga_campos_sensor_clase_sensor_3_widget_tipo_grafica_valores_generales_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_valores_generales_sensores(3);
    };
    $("#clase_sensor_3_widget_grafica_valores_generales_sensores").change(funcion_recarga_campos_sensor_clase_sensor_3_widget_tipo_grafica_valores_generales_sensores);

    // Recarga la lista de intervalos de valores del widget
    var funcion_recarga_intervalos_valores_sensor_widget_grafica_valores_generales_sensores = function() {
        var clase_sensor = $("#clase_sensor_1_widget_grafica_valores_generales_sensores").val();
        var campo = $("#campo_1_widget_grafica_valores_generales_sensores").val();
        var intervalo_valores = $("#intervalo_valores_widget_grafica_valores_generales_sensores").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_intervalos_valores_informe_valores_generales_clase_sensor_campo.php", {
            clase_sensor: clase_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#intervalo_valores_widget_grafica_valores_generales_sensores").html(resultado.html);

            // Intervalo de valores por defecto
            if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
                switch (clase_sensor) {
                    case CLASE_SENSOR_CORTES_TENSION: {
                        intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL_LINEAS;
                        break;
                    }
                    default: {
                        intervalo_valores = INTERVALO_VALORES_HORA;
                        break;
                    }
                }
                $("#intervalo_valores_widget_grafica_valores_generales_sensores").val(intervalo_valores);
            }

            $("#intervalo_valores_widget_grafica_valores_generales_sensores").trigger("change");
        });
    };
    $("#campo_1_widget_grafica_valores_generales_sensores").change(funcion_recarga_intervalos_valores_sensor_widget_grafica_valores_generales_sensores);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_1_widget_tipo_grafica_valores_generales_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("1_widget_grafica_valores_generales_sensores");
    };
    $("#campo_1_widget_grafica_valores_generales_sensores").show(funcion_muestra_control_parametros_extra_campo_1_widget_tipo_grafica_valores_generales_sensores);
    $("#campo_1_widget_grafica_valores_generales_sensores").change(funcion_muestra_control_parametros_extra_campo_1_widget_tipo_grafica_valores_generales_sensores);
    var funcion_muestra_control_parametros_extra_campo_2_widget_tipo_grafica_valores_generales_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("2_widget_grafica_valores_generales_sensores");
    };
    $("#campo_2_widget_grafica_valores_generales_sensores").show(funcion_muestra_control_parametros_extra_campo_2_widget_tipo_grafica_valores_generales_sensores);
    $("#campo_2_widget_grafica_valores_generales_sensores").change(funcion_muestra_control_parametros_extra_campo_2_widget_tipo_grafica_valores_generales_sensores);
    var funcion_muestra_control_parametros_extra_campo_3_widget_tipo_grafica_valores_generales_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("3_widget_grafica_valores_generales_sensores");
    };
    $("#campo_3_widget_grafica_valores_generales_sensores").show(funcion_muestra_control_parametros_extra_campo_3_widget_tipo_grafica_valores_generales_sensores);
    $("#campo_3_widget_grafica_valores_generales_sensores").change(funcion_muestra_control_parametros_extra_campo_3_widget_tipo_grafica_valores_generales_sensores);

    // Habilitación del selector de intervalo de valores de comparación de incrementos totales
    var funcion_habilita_intervalo_valores_widget_grafica_valores_generales_sensores = function() {
        funcion_habilita_intervalo_valores_widget("widget_grafica_valores_generales_sensores");
    };
    $("#intervalo_valores_widget_grafica_valores_generales_sensores").show(funcion_habilita_intervalo_valores_widget_grafica_valores_generales_sensores);
    $("#intervalo_valores_widget_grafica_valores_generales_sensores").change(funcion_habilita_intervalo_valores_widget_grafica_valores_generales_sensores);

    // Habilitación del selector de agregación de comparación de valores generales
    var funcion_habilita_agregacion_widget_grafica_valores_generales_sensores = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_agregaciones = $("select#agregacion_widget_grafica_valores_generales_sensores option").length;
        if (numero_agregaciones <= 1) {
            $("#agregacion_widget_grafica_valores_generales_sensores").attr('disabled', true);
        }
        else {
            $("#agregacion_widget_grafica_valores_generales_sensores").removeAttr('disabled');
        }
    };
    $("#agregacion_widget_grafica_valores_generales_sensores").show(funcion_habilita_agregacion_widget_grafica_valores_generales_sensores);
    $("#agregacion_widget_grafica_valores_generales_sensores").change(funcion_habilita_agregacion_widget_grafica_valores_generales_sensores);

    // Recarga las agregaciones según la clase y el campo en el elemento de plantilla de informe
    var funcion_recarga_agregaciones_widget_grafica_valores_generales_sensores = function() {
        var clase_sensor = $("#clase_sensor_1_widget_grafica_valores_generales_sensores").val();
        var campo = $("#campo_1_widget_grafica_valores_generales_sensores").val();
        var intervalo_valores = $("#intervalo_valores_widget_grafica_valores_generales_sensores").val();
        var agregacion = $("#agregacion_widget_grafica_valores_generales_sensores").val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_NINGUNO:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS: {
                $("#agregacion_widget_grafica_valores_generales_sensores").val(AGREGACION_NINGUNA);
                $("#agregacion_widget_grafica_valores_generales_sensores").attr('disabled', true);
                break;
            }
            default: {
                $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_agregaciones_campo_clase_sensor.php", {
                    clase_sensor: clase_sensor,
                    campo: campo,
                    tipos_agregacion: TIPOS_AGREGACION_TODOS,
                    agregacion: agregacion
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }
                    $("#agregacion_widget_grafica_valores_generales_sensores").html(resultado.html);
                    $("#agregacion_widget_grafica_valores_generales_sensores").trigger("change");
                });
                break;
            }
        }
    };
    $("#intervalo_valores_widget_grafica_valores_generales_sensores").change(funcion_recarga_agregaciones_widget_grafica_valores_generales_sensores);

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_grafica_valores_generales_sensores = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_grafica_valores_generales_sensores");
    };
    $("#periodo_tiempo_widget_grafica_valores_generales_sensores").show(funcion_muestra_controles_periodo_tiempo_widget_grafica_valores_generales_sensores);
    $("#periodo_tiempo_widget_grafica_valores_generales_sensores").change(funcion_muestra_controles_periodo_tiempo_widget_grafica_valores_generales_sensores);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_valor_agregado_valores_generales_sensores = function() {
    $("#clase_sensor_1_widget_valor_agregado_valores_generales_sensores").off();
    $("#clase_sensor_2_widget_valor_agregado_valores_generales_sensores").off();
    $("#clase_sensor_3_widget_valor_agregado_valores_generales_sensores").off();
    $("#campo_1_widget_valor_agregado_valores_generales_sensores").off();
    $("#campo_2_widget_valor_agregado_valores_generales_sensores").off();
    $("#campo_3_widget_valor_agregado_valores_generales_sensores").off();
    $("#intervalo_valores_widget_valor_agregado_valores_generales_sensores").off();
    $("#agregacion_widget_valor_agregado_valores_generales_sensores").off();
    $("#utilizar_colores_fondo_widget_valor_agregado_valores_generales_sensores").off();
    $("#periodo_tiempo_widget_valor_agregado_valores_generales_sensores").off();

    // Mostrar lista doble para las listas dobles de sensores en los widgets de sensores
    $("#contenido_modal").on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var tab = $(e.target);
        var id_pestanya = tab.attr("href");

        if (id_pestanya == "#tab-tipo-valor-agregado-valores-generales-sensores-sensores") {
            if ($('#select_sensores_widget_valor_agregado_valores_generales_sensores_no_visible').length) {
                $('#select_sensores_widget_valor_agregado_valores_generales_sensores_no_visible').attr("id", "select_sensores_widget_valor_agregado_valores_generales_sensores_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_sensores_widget_valor_agregado_valores_generales_sensores", false);
            }
        }
    });

    // Recarga la lista de sensores del widget
    var funcion_recarga_lista_sensores_tipo_widget_valor_agregado_valores_generales_sensores = function() {
        var clases_sensor = [];
        for (var i = 0; i < NUMERO_CLASES_SENSOR_VALORES_GENERALES; i++) {
            var numero_clase_sensor = i + 1;
            var id_lista_clases_sensor = "clase_sensor_" + numero_clase_sensor + "_widget_valor_agregado_valores_generales_sensores";
            var clase_sensor = $("#" + id_lista_clases_sensor).val();
            clases_sensor.push(clase_sensor);
        }
        var ids_sensores = [];
        $("#ids_sensores_widget_valor_agregado_valores_generales_sensores option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_sensores.push($(this).val());
            }
        });
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores_clases.php", {
			clases_sensor: clases_sensor,
            ids_sensores: ids_sensores,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Recarga de lista doble
            if ($('#select_sensores_widget_valor_agregado_valores_generales_sensores_no_visible').length) {
                $("#ids_sensores_widget_valor_agregado_valores_generales_sensores").html(resultado.html);
            }
            else {
                // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
                $("#ids_sensores_widget_valor_agregado_valores_generales_sensores").multiselect2side('destroy');
                $("#ids_sensores_widget_valor_agregado_valores_generales_sensores").html(resultado.html);
                TLNT.Navegacion.convierte_lista_doble("ids_sensores_widget_valor_agregado_valores_generales_sensores", true);
            }
        });
    };
    $("#clase_sensor_1_widget_valor_agregado_valores_generales_sensores").change(funcion_recarga_lista_sensores_tipo_widget_valor_agregado_valores_generales_sensores);
    $("#clase_sensor_2_widget_valor_agregado_valores_generales_sensores").change(funcion_recarga_lista_sensores_tipo_widget_valor_agregado_valores_generales_sensores);
    $("#clase_sensor_3_widget_valor_agregado_valores_generales_sensores").change(funcion_recarga_lista_sensores_tipo_widget_valor_agregado_valores_generales_sensores);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_1_widget_tipo_valor_agregado_valores_generales_sensores = function() {
        funcion_habilita_campo_widget("1_widget_valor_agregado_valores_generales_sensores");
    };
    $("#campo_1_widget_valor_agregado_valores_generales_sensores").show(funcion_habilita_campo_1_widget_tipo_valor_agregado_valores_generales_sensores);
    var funcion_habilita_campo_2_widget_tipo_valor_agregado_valores_generales_sensores = function() {
        funcion_habilita_campo_widget("2_widget_valor_agregado_valores_generales_sensores");
    };
    $("#campo_2_widget_valor_agregado_valores_generales_sensores").show(funcion_habilita_campo_2_widget_tipo_valor_agregado_valores_generales_sensores);
    var funcion_habilita_campo_3_widget_tipo_valor_agregado_valores_generales_sensores = function() {
        funcion_habilita_campo_widget("3_widget_valor_agregado_valores_generales_sensores");
    };
    $("#campo_3_widget_valor_agregado_valores_generales_sensores").show(funcion_habilita_campo_3_widget_tipo_valor_agregado_valores_generales_sensores);

    // Recarga los campos de una clase de sensor del widget
    var funcion_recarga_campos_sensor_clase_sensor_widget_tipo_valor_agregado_valores_generales_sensores = function(numero_campo) {
        var clase_sensor = $("#clase_sensor_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores").val();
        var intervalo_valores = $("#intervalo_valores_widget_valor_agregado_valores_generales_sensores").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_campos_clase_sensor_parametros_extra.php", {
            clase_sensor: clase_sensor,
            tipo_agrupacion_valores: true,
            intervalo_valores: intervalo_valores,
            campo: ""
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#campo_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores").html(resultado.html);

            // Se deshabilita si sólo hay un valor para elegir
            var numero_campos = $("select#campo_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores option").length;
            if (numero_campos <= 1) {
                $("#campo_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores").attr('disabled', true);
            }
            else {
                $("#campo_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores").removeAttr('disabled');
            }

            // Valor seleccionado por defecto
            switch (clase_sensor) {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                case CLASE_SENSOR_AGUA: {
                    $("#campo_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores").val(CAMPO_INCREMENTO);
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA: {
                    $("#campo_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores").val(CAMPO_CONSUMO_ESTIMADO);
                    break;
                }
                case CLASE_SENSOR_GAS: {
                    $("#campo_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores").val(CAMPO_CONSUMO);
                    break;
                }
            }

            $("#campo_" + numero_campo + "_widget_valor_agregado_valores_generales_sensores").trigger('change');
        });
    };
    var funcion_recarga_campos_sensor_clase_sensor_1_widget_tipo_valor_agregado_valores_generales_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget_tipo_valor_agregado_valores_generales_sensores(1);
    };
    $("#clase_sensor_1_widget_valor_agregado_valores_generales_sensores").change(funcion_recarga_campos_sensor_clase_sensor_1_widget_tipo_valor_agregado_valores_generales_sensores);
    var funcion_recarga_campos_sensor_clase_sensor_2_widget_tipo_valor_agregado_valores_generales_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget_tipo_valor_agregado_valores_generales_sensores(2);
    };
    $("#clase_sensor_2_widget_valor_agregado_valores_generales_sensores").change(funcion_recarga_campos_sensor_clase_sensor_2_widget_tipo_valor_agregado_valores_generales_sensores);
    var funcion_recarga_campos_sensor_clase_sensor_3_widget_tipo_valor_agregado_valores_generales_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget_tipo_valor_agregado_valores_generales_sensores(3);
    };
    $("#clase_sensor_3_widget_valor_agregado_valores_generales_sensores").change(funcion_recarga_campos_sensor_clase_sensor_3_widget_tipo_valor_agregado_valores_generales_sensores);

    // Recarga la lista de intervalos de valores del widget
    var funcion_recarga_intervalos_valores_sensor_widget_valor_agregado_valores_generales_sensores = function() {
        var clase_sensor = $("#clase_sensor_1_widget_valor_agregado_valores_generales_sensores").val();
        var campo = $("#campo_1_widget_valor_agregado_valores_generales_sensores").val();
        var intervalo_valores = $("#intervalo_valores_widget_valor_agregado_valores_generales_sensores").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_intervalos_valores_informe_valores_generales_clase_sensor_campo.php", {
            clase_sensor: clase_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#intervalo_valores_widget_valor_agregado_valores_generales_sensores").html(resultado.html);

            // Intervalo de valores por defecto
            if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
                switch (clase_sensor) {
                    case CLASE_SENSOR_CORTES_TENSION: {
                        intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL_LINEAS;
                        break;
                    }
                    default: {
                        intervalo_valores = INTERVALO_VALORES_HORA;
                        break;
                    }
                }
                $("#intervalo_valores_widget_valor_agregado_valores_generales_sensores").val(intervalo_valores);
            }

            $("#intervalo_valores_widget_valor_agregado_valores_generales_sensores").trigger("change");
        });
    };
    $("#campo_1_widget_valor_agregado_valores_generales_sensores").change(funcion_recarga_intervalos_valores_sensor_widget_valor_agregado_valores_generales_sensores);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_1_widget_tipo_valor_agregado_valores_generales_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("1_widget_valor_agregado_valores_generales_sensores");
    };
    $("#campo_1_widget_valor_agregado_valores_generales_sensores").show(funcion_muestra_control_parametros_extra_campo_1_widget_tipo_valor_agregado_valores_generales_sensores);
    $("#campo_1_widget_valor_agregado_valores_generales_sensores").change(funcion_muestra_control_parametros_extra_campo_1_widget_tipo_valor_agregado_valores_generales_sensores);
    var funcion_muestra_control_parametros_extra_campo_2_widget_tipo_valor_agregado_valores_generales_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("2_widget_valor_agregado_valores_generales_sensores");
    };
    $("#campo_2_widget_valor_agregado_valores_generales_sensores").show(funcion_muestra_control_parametros_extra_campo_2_widget_tipo_valor_agregado_valores_generales_sensores);
    $("#campo_2_widget_valor_agregado_valores_generales_sensores").change(funcion_muestra_control_parametros_extra_campo_2_widget_tipo_valor_agregado_valores_generales_sensores);
    var funcion_muestra_control_parametros_extra_campo_3_widget_tipo_valor_agregado_valores_generales_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("3_widget_valor_agregado_valores_generales_sensores");
    };
    $("#campo_3_widget_valor_agregado_valores_generales_sensores").show(funcion_muestra_control_parametros_extra_campo_3_widget_tipo_valor_agregado_valores_generales_sensores);
    $("#campo_3_widget_valor_agregado_valores_generales_sensores").change(funcion_muestra_control_parametros_extra_campo_3_widget_tipo_valor_agregado_valores_generales_sensores);

    // Habilitación del selector de intervalo de valores de comparación de incrementos totales
    var funcion_habilita_intervalo_valores_widget_valor_agregado_valores_generales_sensores = function() {
        funcion_habilita_intervalo_valores_widget("widget_valor_agregado_valores_generales_sensores");
    };
    $("#intervalo_valores_widget_valor_agregado_valores_generales_sensores").show(funcion_habilita_intervalo_valores_widget_valor_agregado_valores_generales_sensores);
    $("#intervalo_valores_widget_valor_agregado_valores_generales_sensores").change(funcion_habilita_intervalo_valores_widget_valor_agregado_valores_generales_sensores);

    // Habilitación del selector de agregación de comparación de valores generales
    var funcion_habilita_agregacion_widget_valor_agregado_valores_generales_sensores = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_agregaciones = $("select#agregacion_widget_valor_agregado_valores_generales_sensores option").length;
        if (numero_agregaciones <= 1) {
            $("#agregacion_widget_valor_agregado_valores_generales_sensores").attr('disabled', true);
        }
        else {
            $("#agregacion_widget_valor_agregado_valores_generales_sensores").removeAttr('disabled');
        }
    };
    $("#agregacion_widget_valor_agregado_valores_generales_sensores").show(funcion_habilita_agregacion_widget_valor_agregado_valores_generales_sensores);
    $("#agregacion_widget_valor_agregado_valores_generales_sensores").change(funcion_habilita_agregacion_widget_valor_agregado_valores_generales_sensores);

    // Recarga las agregaciones según la clase y el campo en el elemento de plantilla de informe
    var funcion_recarga_agregaciones_widget_valor_agregado_valores_generales_sensores = function() {
        var clase_sensor = $("#clase_sensor_1_widget_valor_agregado_valores_generales_sensores").val();
        var campo = $("#campo_1_widget_valor_agregado_valores_generales_sensores").val();
        var intervalo_valores = $("#intervalo_valores_widget_valor_agregado_valores_generales_sensores").val();
        var agregacion = $("#agregacion_widget_valor_agregado_valores_generales_sensores").val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_NINGUNO:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS: {
                $("#agregacion_widget_valor_agregado_valores_generales_sensores").val(AGREGACION_NINGUNA);
                $("#agregacion_widget_valor_agregado_valores_generales_sensores").attr('disabled', true);
                break;
            }
            default: {
                $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_agregaciones_campo_clase_sensor.php", {
                    clase_sensor: clase_sensor,
                    campo: campo,
                    tipos_agregacion: TIPOS_AGREGACION_SIN_CLASES,
                    agregacion: agregacion
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }
                    $("#agregacion_widget_valor_agregado_valores_generales_sensores").html(resultado.html);
                    $("#agregacion_widget_valor_agregado_valores_generales_sensores").trigger("change");
                });
                break;
            }
        }
    };
    $("#intervalo_valores_widget_valor_agregado_valores_generales_sensores").change(funcion_recarga_agregaciones_widget_valor_agregado_valores_generales_sensores);

    // Habilita y muestra los controles dependientes de utilizar los colores de fondo de valores del widget
    var funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_agregado_valores_generales_sensores = function() {
        funcion_habilita_muestra_controles_utilizar_colores_fondo_widgets_valores_sensor("widget_valor_agregado_valores_generales_sensores");
    };
    $("#utilizar_colores_fondo_widget_valor_agregado_valores_generales_sensores").show(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_agregado_valores_generales_sensores);
    $("#utilizar_colores_fondo_widget_valor_agregado_valores_generales_sensores").change(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_valor_agregado_valores_generales_sensores);

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_valor_agregado_valores_generales_sensores = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_valor_agregado_valores_generales_sensores");
    };
    $("#periodo_tiempo_widget_valor_agregado_valores_generales_sensores").show(funcion_muestra_controles_periodo_tiempo_widget_valor_agregado_valores_generales_sensores);
    $("#periodo_tiempo_widget_valor_agregado_valores_generales_sensores").change(funcion_muestra_controles_periodo_tiempo_widget_valor_agregado_valores_generales_sensores);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_incrementos_totales_sensores = function() {
    $("#clase_sensor_1_widget_grafica_incrementos_totales_sensores").off();
    $("#clase_sensor_2_widget_grafica_incrementos_totales_sensores").off();
    $("#clase_sensor_3_widget_grafica_incrementos_totales_sensores").off();
    $("#campo_1_widget_grafica_incrementos_totales_sensores").off();
    $("#campo_2_widget_grafica_incrementos_totales_sensores").off();
    $("#campo_3_widget_grafica_incrementos_totales_sensores").off();
    $("#intervalo_valores_widget_grafica_incrementos_totales_sensores").off();
    $("#agregacion_widget_grafica_incrementos_totales_sensores").off();
    $("#periodo_tiempo_widget_grafica_incrementos_totales_sensores").off();

    // Mostrar lista doble para las listas dobles de sensores en los widgets de sensores
    $("#contenido_modal").on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var tab = $(e.target);
        var id_pestanya = tab.attr("href");

        if (id_pestanya == "#tab-tipo-grafica-incrementos-totales-sensores-sensores") {
            if ($('#select_sensores_widget_grafica_incrementos_totales_sensores_no_visible').length) {
                $('#select_sensores_widget_grafica_incrementos_totales_sensores_no_visible').attr("id", "select_sensores_widget_grafica_incrementos_totales_sensores_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_sensores_widget_grafica_incrementos_totales_sensores", false);
            }
        }
    });

    // Recarga la lista de sensores del widget
    var funcion_recarga_lista_sensores_tipo_widget_grafica_incrementos_totales_sensores = function() {
        var clases_sensor = [];
        for (var i = 0; i < NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES; i++) {
            var numero_clase_sensor = i + 1;
            var id_lista_clases_sensor = "clase_sensor_" + numero_clase_sensor + "_widget_grafica_incrementos_totales_sensores";
            var clase_sensor = $("#" + id_lista_clases_sensor).val();
            clases_sensor.push(clase_sensor);
        }
        var ids_sensores = [];
        $("#ids_sensores_widget_grafica_incrementos_totales_sensores option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_sensores.push($(this).val());
            }
        });
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores_clases.php", {
			clases_sensor: clases_sensor,
            ids_sensores: ids_sensores,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Recarga de lista doble
            if ($('#select_sensores_widget_grafica_incrementos_totales_sensores_no_visible').length) {
                $("#ids_sensores_widget_grafica_incrementos_totales_sensores").html(resultado.html);
            }
            else {
                // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
                $("#ids_sensores_widget_grafica_incrementos_totales_sensores").multiselect2side('destroy');
                $("#ids_sensores_widget_grafica_incrementos_totales_sensores").html(resultado.html);
                TLNT.Navegacion.convierte_lista_doble("ids_sensores_widget_grafica_incrementos_totales_sensores", true);
            }
        });
    };
    $("#clase_sensor_1_widget_grafica_incrementos_totales_sensores").change(funcion_recarga_lista_sensores_tipo_widget_grafica_incrementos_totales_sensores);
    $("#clase_sensor_2_widget_grafica_incrementos_totales_sensores").change(funcion_recarga_lista_sensores_tipo_widget_grafica_incrementos_totales_sensores);
    $("#clase_sensor_3_widget_grafica_incrementos_totales_sensores").change(funcion_recarga_lista_sensores_tipo_widget_grafica_incrementos_totales_sensores);

    // Habilita el control de campo de sensor del widget
    var funcion_habilita_campo_1_widget_tipo_grafica_incrementos_totales_sensores = function() {
        funcion_habilita_campo_widget("1_widget_grafica_incrementos_totales_sensores");
    };
    $("#campo_1_widget_grafica_incrementos_totales_sensores").show(funcion_habilita_campo_1_widget_tipo_grafica_incrementos_totales_sensores);
    var funcion_habilita_campo_2_widget_tipo_grafica_incrementos_totales_sensores = function() {
        funcion_habilita_campo_widget("2_widget_grafica_incrementos_totales_sensores");
    };
    $("#campo_2_widget_grafica_incrementos_totales_sensores").show(funcion_habilita_campo_2_widget_tipo_grafica_incrementos_totales_sensores);
    var funcion_habilita_campo_3_widget_tipo_grafica_incrementos_totales_sensores = function() {
        funcion_habilita_campo_widget("3_widget_grafica_incrementos_totales_sensores");
    };
    $("#campo_3_widget_grafica_incrementos_totales_sensores").show(funcion_habilita_campo_3_widget_tipo_grafica_incrementos_totales_sensores);

    // Recarga la lista de intervalos de valores del widget
    var funcion_recarga_intervalos_valores_sensor_widget_grafica_incrementos_totales_sensores = function() {
        var clase_sensor = $("#clase_sensor_1_widget_grafica_incrementos_totales_sensores").val();
        var campo = $("#campo_1_widget_grafica_incrementos_totales_sensores").val();
        var intervalo_valores = $("#intervalo_valores_widget_grafica_incrementos_totales_sensores").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_intervalos_valores_informe_incrementos_totales_clase_sensor_campo.php", {
            clase_sensor: clase_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#intervalo_valores_widget_grafica_incrementos_totales_sensores").html(resultado.html);

            // Intervalo de valores por defecto
            if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
                switch (clase_sensor) {
                    case CLASE_SENSOR_CORTES_TENSION: {
                        intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL_LINEAS;
                        break;
                    }
                    default: {
                        intervalo_valores = INTERVALO_VALORES_HORA;
                        break;
                    }
                }
                $("#intervalo_valores_widget_grafica_incrementos_totales_sensores").val(intervalo_valores);
            }

            $("#intervalo_valores_widget_grafica_incrementos_totales_sensores").trigger("change");
        });
    };
    $("#campo_1_widget_grafica_incrementos_totales_sensores").change(funcion_recarga_intervalos_valores_sensor_widget_grafica_incrementos_totales_sensores);

    // Recarga los campos de una clase de sensor del widget
    var funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_incrementos_totales_sensores = function(numero_campo) {
        var clase_sensor = $("#clase_sensor_" + numero_campo + "_widget_grafica_incrementos_totales_sensores").val();
        var intervalo_valores = $("#intervalo_valores_widget_grafica_incrementos_totales_sensores").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_campos_incrementos_clase_sensor_tipo_agrupacion_valores_parametros_extra.php", {
            clase_sensor: clase_sensor,
            intervalo_valores: intervalo_valores,
            campo: ""
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#campo_" + numero_campo + "_widget_grafica_incrementos_totales_sensores").html(resultado.html);

            // Se deshabilita si sólo hay un valor para elegir
            var numero_campos = $("select#campo_" + numero_campo + "_widget_grafica_incrementos_totales_sensores option").length;
            if (numero_campos <= 1) {
                $("#campo_" + numero_campo + "_widget_grafica_incrementos_totales_sensores").attr('disabled', true);
            }
            else {
                $("#campo_" + numero_campo + "_widget_grafica_incrementos_totales_sensores").removeAttr('disabled');
            }

            // Valor seleccionado por defecto
            switch (clase_sensor) {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                case CLASE_SENSOR_AGUA: {
                    $("#campo_" + numero_campo + "_widget_grafica_incrementos_totales_sensores").val(CAMPO_INCREMENTO);
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA: {
                    $("#campo_" + numero_campo + "_widget_grafica_incrementos_totales_sensores").val(CAMPO_CONSUMO_ESTIMADO);
                    break;
                }
                case CLASE_SENSOR_GAS: {
                    $("#campo_" + numero_campo + "_widget_grafica_incrementos_totales_sensores").val(CAMPO_CONSUMO);
                    break;
                }
            }

            $("#campo_" + numero_campo + "_widget_grafica_incrementos_totales_sensores").trigger('change');
        });
    };
    var funcion_recarga_campos_sensor_clase_sensor_1_widget_tipo_grafica_incrementos_totales_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_incrementos_totales_sensores(1);
    };
    $("#clase_sensor_1_widget_grafica_incrementos_totales_sensores").change(funcion_recarga_campos_sensor_clase_sensor_1_widget_tipo_grafica_incrementos_totales_sensores);
    var funcion_recarga_campos_sensor_clase_sensor_2_widget_tipo_grafica_incrementos_totales_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_incrementos_totales_sensores(2);
    };
    $("#clase_sensor_2_widget_grafica_incrementos_totales_sensores").change(funcion_recarga_campos_sensor_clase_sensor_2_widget_tipo_grafica_incrementos_totales_sensores);
    var funcion_recarga_campos_sensor_clase_sensor_3_widget_tipo_grafica_incrementos_totales_sensores = function() {
        funcion_recarga_campos_sensor_clase_sensor_widget_tipo_grafica_incrementos_totales_sensores(3);
    };
    $("#clase_sensor_3_widget_grafica_incrementos_totales_sensores").change(funcion_recarga_campos_sensor_clase_sensor_3_widget_tipo_grafica_incrementos_totales_sensores);

    // Muestra el control de parametros extra de campo de sensor del widget
    var funcion_muestra_control_parametros_extra_campo_1_widget_tipo_grafica_incrementos_totales_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("1_widget_grafica_incrementos_totales_sensores");
    };
    $("#campo_1_widget_grafica_incrementos_totales_sensores").show(funcion_muestra_control_parametros_extra_campo_1_widget_tipo_grafica_incrementos_totales_sensores);
    $("#campo_1_widget_grafica_incrementos_totales_sensores").change(funcion_muestra_control_parametros_extra_campo_1_widget_tipo_grafica_incrementos_totales_sensores);
    var funcion_muestra_control_parametros_extra_campo_2_widget_tipo_grafica_incrementos_totales_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("2_widget_grafica_incrementos_totales_sensores");
    };
    $("#campo_2_widget_grafica_incrementos_totales_sensores").show(funcion_muestra_control_parametros_extra_campo_2_widget_tipo_grafica_incrementos_totales_sensores);
    $("#campo_2_widget_grafica_incrementos_totales_sensores").change(funcion_muestra_control_parametros_extra_campo_2_widget_tipo_grafica_incrementos_totales_sensores);
    var funcion_muestra_control_parametros_extra_campo_3_widget_tipo_grafica_incrementos_totales_sensores = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("3_widget_grafica_incrementos_totales_sensores");
    };
    $("#campo_3_widget_grafica_incrementos_totales_sensores").show(funcion_muestra_control_parametros_extra_campo_3_widget_tipo_grafica_incrementos_totales_sensores);
    $("#campo_3_widget_grafica_incrementos_totales_sensores").change(funcion_muestra_control_parametros_extra_campo_3_widget_tipo_grafica_incrementos_totales_sensores);

    // Habilitación del selector de intervalo de valores de comparación de incrementos totales
    var funcion_habilita_intervalo_valores_widget_grafica_incrementos_totales_sensores = function() {
        funcion_habilita_intervalo_valores_widget("widget_grafica_incrementos_totales_sensores");
    };
    $("#intervalo_valores_widget_grafica_incrementos_totales_sensores").show(funcion_habilita_intervalo_valores_widget_grafica_incrementos_totales_sensores);
    $("#intervalo_valores_widget_grafica_incrementos_totales_sensores").change(funcion_habilita_intervalo_valores_widget_grafica_incrementos_totales_sensores);

    // Habilitación del selector de agregación de comparación de valores generales
    var funcion_habilita_agregacion_widget_grafica_incrementos_totales_sensores = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_agregaciones = $("select#agregacion_widget_grafica_incrementos_totales_sensores option").length;
        if (numero_agregaciones <= 1) {
            $("#agregacion_widget_grafica_incrementos_totales_sensores").attr('disabled', true);
        }
        else {
            $("#agregacion_widget_grafica_incrementos_totales_sensores").removeAttr('disabled');
        }
    };
    $("#agregacion_widget_grafica_incrementos_totales_sensores").show(funcion_habilita_agregacion_widget_grafica_incrementos_totales_sensores);
    $("#agregacion_widget_grafica_incrementos_totales_sensores").change(funcion_habilita_agregacion_widget_grafica_incrementos_totales_sensores);

    // Recarga las agregaciones según la clase y el campo en el elemento de plantilla de informe
    var funcion_recarga_agregaciones_widget_grafica_incrementos_totales_sensores = function() {
        var clase_sensor = $("#clase_sensor_1_widget_grafica_incrementos_totales_sensores").val();
        var campo = $("#campo_1_widget_grafica_incrementos_totales_sensores").val();
        var intervalo_valores = $("#intervalo_valores_widget_grafica_incrementos_totales_sensores").val();
        var agregacion = $("#agregacion_widget_grafica_incrementos_totales_sensores").val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_NINGUNO:
            case INTERVALO_VALORES_TIEMPO_REAL: {
                $("#agregacion_widget_grafica_incrementos_totales_sensores").val(AGREGACION_NINGUNA);
                $("#agregacion_widget_grafica_incrementos_totales_sensores").attr('disabled', true);
                break;
            }
            default: {
                $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_agregaciones_campo_clase_sensor.php", {
                    clase_sensor: clase_sensor,
                    campo: campo,
                    tipos_agregacion: TIPOS_AGREGACION_CON_CLASES,
                    agregacion: agregacion
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }
                    $("#agregacion_widget_grafica_incrementos_totales_sensores").html(resultado.html);
                    $("#agregacion_widget_grafica_incrementos_totales_sensores").trigger("change");
                });
                break;
            }
        }
    };
    $("#intervalo_valores_widget_grafica_incrementos_totales_sensores").change(funcion_recarga_agregaciones_widget_grafica_incrementos_totales_sensores);

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_grafica_incrementos_totales_sensores = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_grafica_incrementos_totales_sensores");
    };
    $("#periodo_tiempo_widget_grafica_incrementos_totales_sensores").show(funcion_muestra_controles_periodo_tiempo_widget_grafica_incrementos_totales_sensores);
    $("#periodo_tiempo_widget_grafica_incrementos_totales_sensores").change(funcion_muestra_controles_periodo_tiempo_widget_grafica_incrementos_totales_sensores);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_informacion_actuador = function() {
    $("#clase_actuador_widget_informacion_actuador").off();
    $("#id_actuador_widget_informacion_actuador").off();

    // Habilita el control de actuador del widget
    var funcion_habilita_actuador_widget_informacion_actuador = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_actuadores = $("select#id_actuador_widget_informacion_actuador option").length;
        if (numero_actuadores <= 1) {
            $("#id_actuador_widget_informacion_actuador").attr('disabled', true);
        }
        else {
            $("#id_actuador_widget_informacion_actuador").removeAttr('disabled');
        }
        $("#id_actuador_widget_informacion_actuador").trigger("chosen:updated");
    };
    $("#id_actuador_widget_informacion_actuador").show(funcion_habilita_actuador_widget_informacion_actuador);

    // Recarga de los actuadores de una clase de actuador de la ventana de anyadir/modificar widget
    var funcion_recarga_actuadores_clase_actuador_widget_informacion_actuador = function() {
        var clase_actuador = $("#clase_actuador_widget_informacion_actuador").val();
        var id_actuador = $("#id_actuador_widget_informacion_actuador").val();
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/dame_lista_ids_destinos_accion.php", {
            clase_actuador: clase_actuador,
            destino: DESTINO_ACCION_ACTUADOR,
            id_destino: id_actuador
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_actuador_widget_informacion_actuador").html(resultado.html);
            $("#id_actuador_widget_informacion_actuador").trigger("chosen:updated");

            // Se habilita el control de actuador
            funcion_habilita_actuador_widget_informacion_actuador();
        });
    };
    $("#clase_actuador_widget_informacion_actuador").change(funcion_recarga_actuadores_clase_actuador_widget_informacion_actuador);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_informacion_grupo_actuadores = function() {
    $("#clase_actuador_widget_informacion_grupo_actuadores").off();
    $("#id_grupo_actuadores_widget_informacion_grupo_actuadores").off();

    // Habilita el control de grupo de actuadores del widget
    var funcion_habilita_grupo_actuadores_widget_informacion_grupo_actuadores = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_grupos_actuadores = $("select#id_grupo_actuadores_widget_informacion_grupo_actuadores option").length;
        if (numero_grupos_actuadores <= 1) {
            $("#id_grupo_actuadores_widget_informacion_grupo_actuadores").attr('disabled', true);
        }
        else {
            $("#id_grupo_actuadores_widget_informacion_grupo_actuadores").removeAttr('disabled');
        }
        $("#id_grupo_actuadores_widget_informacion_grupo_actuadores").trigger("chosen:updated");
    };
    $("#id_grupo_actuadores_widget_informacion_grupo_actuadores").show(funcion_habilita_grupo_actuadores_widget_informacion_grupo_actuadores);

    // Recarga de los grupos de actuadores de una clase de actuador de la ventana de anyadir/modificar widget
    var funcion_recarga_grupos_actuadores_clase_actuador_widget_informacion_grupo_actuadores = function() {
        var clase_actuador = $("#clase_actuador_widget_informacion_grupo_actuadores").val();
        var id_grupo_actuadores = $("#id_grupo_actuadores_widget_informacion_grupo_actuadores").val();
        $.post("./src/modulos/ModulosWeb/ModuloActuadores/dame_lista_ids_destinos_accion.php", {
            clase_actuador: clase_actuador,
            destino: DESTINO_ACCION_GRUPO_ACTUADORES,
            id_destino: id_grupo_actuadores
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_grupo_actuadores_widget_informacion_grupo_actuadores").html(resultado.html);
            $("#id_grupo_actuadores_widget_informacion_grupo_actuadores").trigger("chosen:updated");

            // Se habilita el control de grupo de actuadores
            funcion_habilita_grupo_actuadores_widget_informacion_grupo_actuadores();
        });
    };
    $("#clase_actuador_widget_informacion_grupo_actuadores").change(funcion_recarga_grupos_actuadores_clase_actuador_widget_informacion_grupo_actuadores);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_grafica_consumos_costes_tramos_sensor = function() {
    $("#periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor").off();

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_grafica_consumos_costes_tramos_sensor");
    };
    $("#periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor").show(funcion_muestra_controles_periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor);
    $("#periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor").change(funcion_muestra_controles_periodo_tiempo_widget_grafica_consumos_costes_tramos_sensor);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_coste_factura_sensor = function() {
    $("#medicion_widget_coste_factura_sensor").off();
    $("#id_sensor_widget_coste_factura_sensor").off();
    $("#utilizar_colores_fondo_widget_coste_factura_sensor").off();
    $("#periodo_tiempo_widget_coste_factura_sensor").off();

    // Habilitación de medición
    var funcion_habilita_medicion_widget_coste_factura_sensor = function(id_controles) {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_mediciones = $("select#medicion_widget_coste_factura_sensor option").length;
        if (numero_mediciones <= 1) {
            $("#medicion_widget_coste_factura_sensor").attr('disabled', true);
        }
        else {
            $("#medicion_widget_coste_factura_sensor").removeAttr('disabled');
        }
    };
    $("#medicion_widget_coste_factura_sensor").show(funcion_habilita_medicion_widget_coste_factura_sensor);

    // Realiza acciones al cambiar la medición en el widget de coste de factura de sensor
    var funcion_realiza_acciones_medicion_widget_coste_factura_sensor = function() {
        var medicion = $("#medicion_widget_coste_factura_sensor").val();

        // Se recargan los sensores de la clase correspondiente
        var clase_sensor = null;
        switch (medicion) {
            case MEDICION_ELECTRICIDAD: {
                clase_sensor = CLASE_SENSOR_ENERGIA_ACTIVA;
                break;
            }
            case MEDICION_GAS: {
                clase_sensor = CLASE_SENSOR_GAS;
                break;
            }
            case MEDICION_AGUA: {
                clase_sensor = CLASE_SENSOR_AGUA;
                break;
            }
        }
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores.php", {
            clase_sensor: clase_sensor,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_sensor_widget_coste_factura_sensor").html(resultado.html);
            $("#id_sensor_widget_coste_factura_sensor").trigger("chosen:updated");

            // Se deshabilita si sólo hay un valor para elegir
            var numero_ids = $("select#id_sensor_widget_coste_factura_sensor option").length;
            if (numero_ids <= 1) {
                $("#id_sensor_widget_coste_factura_sensor").attr('disabled', true).trigger("chosen:updated");
            }
            else {
                $("#id_sensor_widget_coste_factura_sensor").removeAttr('disabled').trigger("chosen:updated");
            }
        });

        // Se recargan los conceptos de factura de la medición correspondientes
        var concepto_factura = $("#concepto_factura_widget_coste_factura_sensor").val();
        $.post("./src/lib/modulos/widgets/dame_lista_conceptos_factura_widget_coste_factura_sensor.php", {
            medicion: medicion,
            concepto_factura: concepto_factura
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#concepto_factura_widget_coste_factura_sensor").html(resultado.html);

            // Se deshabilita si sólo hay un valor para elegir
            var numero_conceptos = $("select#concepto_factura_widget_coste_factura_sensor option").length;
            if (numero_conceptos <= 1) {
                $("#concepto_factura_widget_coste_factura_sensor").attr('disabled', true);
            }
            else {
                $("#concepto_factura_widget_coste_factura_sensor").removeAttr('disabled');
            }
        });
    };
    $("#medicion_widget_coste_factura_sensor").change(funcion_realiza_acciones_medicion_widget_coste_factura_sensor);

    // Habilita el control de sensor del widget
    var funcion_habilita_sensor_widget_tipo_coste_factura_sensor = function() {
        funcion_habilita_sensor_widget("widget_coste_factura_sensor");
    };
    $("#id_sensor_widget_coste_factura_sensor").show(funcion_habilita_sensor_widget_tipo_coste_factura_sensor);

    // Habilita y muestra los controles dependientes de utilizar los colores de fondo de valores del widget
    var funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_coste_factura_sensor = function() {
        funcion_habilita_muestra_controles_utilizar_colores_fondo_widgets_valores_sensor("widget_coste_factura_sensor");
    };
    $("#utilizar_colores_fondo_widget_coste_factura_sensor").show(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_coste_factura_sensor);
    $("#utilizar_colores_fondo_widget_coste_factura_sensor").change(funcion_habilita_muestra_controles_utilizar_colores_fondo_widget_coste_factura_sensor);

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_coste_factura_sensor = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_coste_factura_sensor");
    };
    $("#periodo_tiempo_widget_coste_factura_sensor").show(funcion_muestra_controles_periodo_tiempo_widget_coste_factura_sensor);
    $("#periodo_tiempo_widget_coste_factura_sensor").change(funcion_muestra_controles_periodo_tiempo_widget_coste_factura_sensor);
};


establece_eventos_ventanas_modales_modulos_administracion_widget_simulador_linea_base = function() {
    $("#periodo_tiempo_widget_simulador_linea_base").off();

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_simulador_linea_base = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_simulador_linea_base");
    };
    $("#periodo_tiempo_widget_simulador_linea_base").show(funcion_muestra_controles_periodo_tiempo_widget_simulador_linea_base);
    $("#periodo_tiempo_widget_simulador_linea_base").change(funcion_muestra_controles_periodo_tiempo_widget_simulador_linea_base);
};



establece_eventos_ventanas_modales_modulos_administracion_widget_informacion_proyecto = function() {
    // Desactivación de eventos anteriores
    $("#periodo_tiempo_widget_informacion_proyecto").off();

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_widget_informacion_proyecto = function() {
        funcion_muestra_controles_periodo_tiempo_widget("widget_informacion_proyecto");
    };
    $("#periodo_tiempo_widget_informacion_proyecto").show(funcion_muestra_controles_periodo_tiempo_widget_informacion_proyecto);
    $("#periodo_tiempo_widget_informacion_proyecto").change(funcion_muestra_controles_periodo_tiempo_widget_informacion_proyecto);
};


establece_eventos_ventanas_modales_modulos_administracion_informes_automaticos = function() {
    // Desactivación de eventos anteriores
    $("#periodicidad_informe_automatico").off();
    $("#tipo_seleccion_periodo_tiempo_informe_automatico").off();
    $("#periodo_tiempo_informe_automatico").off();

    // Habilita y muestra los controles dependientes de la periodicidad del informe automático
    var funcion_habilita_muestra_controles_periodicidad_informe_automatico = function() {
        var periodicidad_informe_automatico = $("#periodicidad_informe_automatico").val();
        switch (periodicidad_informe_automatico) {
            case PERIODICIDAD_INFORME_AUTOMATICO_SEMANAL: {
                $("#control_dia_semana_informe_automatico").show();
                $("#control_dia_mes_informe_automatico").hide();
                $(".clase_control_periodo_personalizado").hide();
                //$("#control_numero_periodos_personalizado_informe_automatico").hide();
                //$("#control_fecha_primer_envio").hide();
                break;
            }
            case PERIODICIDAD_INFORME_AUTOMATICO_MENSUAL: {
                $("#control_dia_semana_informe_automatico").hide();
                $("#control_dia_mes_informe_automatico").show();
                $(".clase_control_periodo_personalizado").hide();
                //$("#control_numero_periodos_personalizado_informe_automatico").hide();
                //$("#control_fecha_primer_envio").hide();
                break;
            }
            case PERIODICIDAD_INFORME_AUTOMATICO_PERSONALIZADA: {
                $("#control_dia_semana_informe_automatico").hide();
                $("#control_dia_mes_informe_automatico").hide();
                $(".clase_control_periodo_personalizado").show();
                //$("#control_numero_periodos_personalizado_informe_automatico").show();
                //$("#control_fecha_primer_envio").show();
                break;
            }
            default: {
                $("#control_dia_semana_informe_automatico").hide();
                $("#control_dia_mes_informe_automatico").hide();
                $(".clase_control_periodo_personalizado").hide();
                //$("#control_numero_periodos_personalizado_informe_automatico").hide();
                //$("#control_fecha_primer_envio").hide();
                break;
            }
        }
    };
    $("#periodicidad_informe_automatico").show(funcion_habilita_muestra_controles_periodicidad_informe_automatico);
    $("#periodicidad_informe_automatico").change(funcion_habilita_muestra_controles_periodicidad_informe_automatico);

    // Habilita y muestra los controles dependientes del periodo de tiempo
    // - Nota: Se declara antes de la función 'funcion_habilita_muestra_controles_tipo_seleccion_periodo_tiempo_informe_automatico'
    //   porque se llama desde la misma y si no da un error de javascript de función no encontrada
    var funcion_habilita_muestra_controles_periodo_tiempo_informe_automatico = function() {
        var periodo_tiempo_informe_automatico = $("#periodo_tiempo_informe_automatico").val();
        switch (periodo_tiempo_informe_automatico) {
            case PERIODO_TIEMPO_DIA: {
                $("#control_iniciar_comienzo_periodo_tiempo_informe_automatico").hide();
                break;
            }
            default: {
                $("#control_iniciar_comienzo_periodo_tiempo_informe_automatico").show();
                break;
            }
        }
    };
    $("#periodo_tiempo_informe_automatico").change(funcion_habilita_muestra_controles_periodo_tiempo_informe_automatico);

    // Habilita y muestra los controles dependientes del tipo de selección de periodo de tiempo
    var funcion_habilita_muestra_controles_tipo_seleccion_periodo_tiempo_informe_automatico = function() {
        var tipo_seleccion_periodo_tiempo_informe_automatico = $("#tipo_seleccion_periodo_tiempo_informe_automatico").val();
        switch (tipo_seleccion_periodo_tiempo_informe_automatico) {
            case TIPO_SELECCION_PERIODO_TIEMPO_AUTOMATICO: {
                $("#control_periodo_tiempo_informe_automatico").hide();
                $("#control_iniciar_comienzo_periodo_tiempo_informe_automatico").hide();
                break;
            }
            case TIPO_SELECCION_PERIODO_TIEMPO_CONFIGURABLE: {
                $("#control_periodo_tiempo_informe_automatico").show();
                funcion_habilita_muestra_controles_periodo_tiempo_informe_automatico();
                break;
            }
        }
    };
    $("#tipo_seleccion_periodo_tiempo_informe_automatico").show(funcion_habilita_muestra_controles_tipo_seleccion_periodo_tiempo_informe_automatico);
    $("#tipo_seleccion_periodo_tiempo_informe_automatico").change(funcion_habilita_muestra_controles_tipo_seleccion_periodo_tiempo_informe_automatico);
};


establece_eventos_ventanas_modales_modulos_acciones_usuario = function() {
    // Desactivación de eventos anteriores
    $("#observaciones_accion").off();

    // Contador de caracteres de observaciones de acción
    $("#observaciones_accion").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_modulos_administracion_comentarios = function() {
    // Desactivación de eventos anteriores
    $("#tipo_comentarios").off();
    $("#clase_objetos_comentarios").off();
    $("#descripcion_comentario").off();
    $("#descripcion_comentarios").off();

    // Objetos de comentarios
    if ($('#select_objetos_comentarios_no_visible').length) {
        $('#select_objetos_comentarios_no_visible').attr("id", "select_objetos_comentarios_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_objetos_comentarios", true);
    }

    // Recarga de la lista doble de objetos de comentarios
    var funcion_recarga_lista_doble_objetos_comentarios = function() {
        var recargar_lista_doble_objetos_comentarios = false;
        var descripcion_objetos = null;
        var ids_objetos = $("#parametros_ventana_anyadir_comentarios").attr("ids_objetos");
        var ids_sensores = $("#parametros_ventana_anyadir_comentarios").attr("ids_sensores");
        var ids_actuadores = $("#parametros_ventana_anyadir_comentarios").attr("ids_actuadores");
        var ids_grupos_actuadores = $("#parametros_ventana_anyadir_comentarios").attr("ids_grupos_actuadores");
        var origen_comentarios = $("#parametros_ventana_anyadir_comentarios").attr("origen_comentarios");
        var tipo_comentarios_anterior = $("#parametros_ventana_anyadir_comentarios").attr("tipo_comentarios");
        var clase_objetos_anterior = $("#parametros_ventana_anyadir_comentarios").attr("clase_objetos");
        var tipo_comentarios = $("#tipo_comentarios").val();
        var clase_objetos = $("#clase_objetos_comentarios").val();
        if (clase_objetos_anterior != clase_objetos) {
            recargar_lista_doble_objetos_comentarios = true;
        }
        if ((tipo_comentarios_anterior != tipo_comentarios) || (recargar_lista_doble_objetos_comentarios == true)) {
            switch (tipo_comentarios) {
                case TIPO_COMENTARIO_ANOTACION_SENSOR:
                case TIPO_COMENTARIO_INTERVENCION_SENSOR: {
                    switch (tipo_comentarios_anterior) {
                        case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                        case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                        case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                        case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES: {
                            descripcion_objetos = TLNT.Idiomas._("Sensores");
                            recargar_lista_doble_objetos_comentarios = true;
                            ids_objetos = ids_sensores;
                            break;
                        }
                    }
                    break;
                }
                case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                case TIPO_COMENTARIO_INTERVENCION_ACTUADOR: {
                    switch (tipo_comentarios_anterior) {
                        case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                        case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                        case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                        case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES: {
                            descripcion_objetos = TLNT.Idiomas._("Actuadores");
                            recargar_lista_doble_objetos_comentarios = true;
                            ids_objetos = ids_actuadores;
                            break;
                        }
                    }
                    break;
                }
                case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES: {
                    switch (tipo_comentarios_anterior) {
                        case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                        case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                        case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                        case TIPO_COMENTARIO_INTERVENCION_ACTUADOR: {
                            descripcion_objetos = TLNT.Idiomas._("Grupos de actuadores");
                            recargar_lista_doble_objetos_comentarios = true;
                            ids_objetos = ids_grupos_actuadores;
                            break;
                        }
                    }
                    break;
                }
            }
            $("#parametros_ventana_anyadir_comentarios").attr("tipo_comentarios", tipo_comentarios);
            $("#parametros_ventana_anyadir_comentarios").attr("clase_objetos", clase_objetos);
        }
        if (recargar_lista_doble_objetos_comentarios == true) {
            $.post("./src/lib/modulos/Comentarios/dame_lista_objetos_comentarios.php", {
                origen_comentarios: origen_comentarios,
                tipo_comentarios: tipo_comentarios,
                clase_objetos: clase_objetos,
                ids_objetos: ids_objetos
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                // Título de lista doble
                $("#titulo_objetos_comentarios").text(descripcion_objetos);

                // Recarga de lista doble
                // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
                $("#ids_objetos_comentarios").multiselect2side('destroy');
                $("#ids_objetos_comentarios").html(resultado.html);
                TLNT.Navegacion.convierte_lista_doble("ids_objetos_comentarios", true);
            });
        }
    };
    $("#tipo_comentarios").show(funcion_recarga_lista_doble_objetos_comentarios);
    $("#tipo_comentarios").change(funcion_recarga_lista_doble_objetos_comentarios);
    $("#clase_objetos_comentarios").change(funcion_recarga_lista_doble_objetos_comentarios);

    // Contador de caracteres de descripción de comentarios
    $("#descripcion_comentario").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
    $("#descripcion_comentarios").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_modulos_administracion_nodos = function() {
    // Desactivación de eventos anteriores
    $("#id_localizacion_asignacion_localizacion_nodos").off();
    $("#clase_nodo_asignacion_localizacion_nodos").off();
    $("#clase_nodo_asignacion_grupo_nodos").off();
    $("#id_grupo_asignacion_grupo_nodos").off();

    // Mostrar listas dobles para los nodos y grupos de nodos (asignación de localización)
    if ($('#select_nodos_asignacion_localizacion_nodos_no_visible').length) {
        $('#select_nodos_asignacion_localizacion_nodos_no_visible').attr("id", "select_nodos_asignacion_localizacion_nodos_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_nodos_asignacion_localizacion_nodos", true);
    }
    if ($('#select_grupos_nodos_asignacion_localizacion_nodos_no_visible').length) {
        $('#select_grupos_nodos_asignacion_localizacion_nodos_no_visible').attr("id", "select_grupos_nodos_asignacion_localizacion_nodos_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_grupos_nodos_asignacion_localizacion_nodos", true);
    }

    // Mostrar lista doble para los nodos (asignación de grupo)
    if ($('#select_nodos_asignacion_grupo_nodos_no_visible').length) {
        $('#select_nodos_asignacion_grupo_nodos_no_visible').attr("id", "select_nodos_asignacion_grupo_nodos_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_nodos_asignacion_grupo_nodos", true);
    }

    // Recarga las listas de nodos para la asignación de localización
    var funcion_recarga_listas_nodos_asignacion_localizacion = function() {
        var id_localizacion = $("#id_localizacion_asignacion_localizacion_nodos").val();
        var tipo_nodo = $('#parametros_ventana_asignacion_localizacion_nodos').attr("tipo_nodo");
        var clase_nodo = $("#clase_nodo_asignacion_localizacion_nodos").val();
        $.post("./src/lib/modulos/Nodos/administracion/dame_listas_nodos_asignacion_localizacion.php", {
            id_localizacion: id_localizacion,
            tipo_nodo: tipo_nodo,
            clase_nodo: clase_nodo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Recarga de listas dobles
            // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
            $("#ids_nodos_asignacion_localizacion_nodos").multiselect2side('destroy');
            $("#ids_nodos_asignacion_localizacion_nodos").html(resultado.html_lista_nodos);
            TLNT.Navegacion.convierte_lista_doble("ids_nodos_asignacion_localizacion_nodos", true);

            $("#ids_grupos_nodos_asignacion_localizacion_nodos").multiselect2side('destroy');
            $("#ids_grupos_nodos_asignacion_localizacion_nodos").html(resultado.html_lista_grupos_nodos);
            TLNT.Navegacion.convierte_lista_doble("ids_grupos_nodos_asignacion_localizacion_nodos", true);
        });
    };
    $("#clase_nodo_asignacion_localizacion_nodos").change(funcion_recarga_listas_nodos_asignacion_localizacion);

    // Realiza las acciones necesarias al modificar la localización en la ventana de asignación de localización
    var funcion_realiza_acciones_localizacion_asignacion_localizacion = function() {
        var clase_nodo = $("#clase_nodo_asignacion_localizacion_nodos").val();
        if (clase_nodo != CLASE_NINGUNA) {
            funcion_recarga_listas_nodos_asignacion_localizacion();
        }
    };
    $("#id_localizacion_asignacion_localizacion_nodos").change(funcion_realiza_acciones_localizacion_asignacion_localizacion);

    // Recarga la lista de grupos de nodos para la asignación de grupo
    var funcion_recarga_lista_grupos_nodos_asignacion_grupo = function() {
        var clase_nodo = $("#clase_nodo_asignacion_grupo_nodos").val();
        var id_grupo = $("#id_grupo_asignacion_grupo_nodos").val();
        var tipo_nodo = $('#parametros_ventana_asignacion_grupo_nodos').attr("tipo_nodo");
        $.post("./src/lib/modulos/Nodos/administracion/dame_lista_grupos_nodos_asignacion_grupo.php", {
            clase_nodo: clase_nodo,
            id_grupo: id_grupo,
            tipo_nodo: tipo_nodo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_grupo_asignacion_grupo_nodos").html(resultado.html);
            $("#id_grupo_asignacion_grupo_nodos").trigger("chosen:updated");

            funcion_recarga_lista_nodos_asignacion_grupo();
        });
    };
    $("#clase_nodo_asignacion_grupo_nodos").change(funcion_recarga_lista_grupos_nodos_asignacion_grupo);

    // Recarga la lista de nodos para la asignación de grupo
    var funcion_recarga_lista_nodos_asignacion_grupo = function() {
        var clase_nodo = $("#clase_nodo_asignacion_grupo_nodos").val();
        var id_grupo = $("#id_grupo_asignacion_grupo_nodos").val();
        var tipo_nodo = $('#parametros_ventana_asignacion_grupo_nodos').attr("tipo_nodo");
        $.post("./src/lib/modulos/Nodos/administracion/dame_lista_nodos_asignacion_grupo.php", {
            clase_nodo: clase_nodo,
            id_grupo: id_grupo,
            tipo_nodo: tipo_nodo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Recarga de listas dobles
            // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
            $("#ids_nodos_asignacion_grupo_nodos").multiselect2side('destroy');
            $("#ids_nodos_asignacion_grupo_nodos").html(resultado.html);
            TLNT.Navegacion.convierte_lista_doble("ids_nodos_asignacion_grupo_nodos", true);
        });
    };
    $("#id_grupo_asignacion_grupo_nodos").change(funcion_recarga_lista_nodos_asignacion_grupo);
};


//
// Funciones auxiliares (utilizadas en varias funciones)
//


// Muestra el control de ratio
var funcion_muestra_control_ratio_widget = function(id_controles) {
    var clase_sensor = $("#clase_sensor_" + id_controles).val();
    switch (clase_sensor) {
        case CLASE_SENSOR_ENERGIA_ACTIVA:
        case CLASE_SENSOR_ENERGIA_REACTIVA:
        case CLASE_SENSOR_GAS:
        case CLASE_SENSOR_AGUA:
        case CLASE_SENSOR_GENERICA: {
            $("#control_id_ratio_" + id_controles).show();
            break;
        }
        default: {
            $("#control_id_ratio_" + id_controles).hide();
            break;
        }
    }
};


// Habilitación de sensores
funcion_habilita_sensor_widget = function(id_controles) {
    // Se deshabilita si sólo hay un valor para elegir
    var numero_sensores = $("select#id_sensor_" + id_controles + " option").length;
    if (numero_sensores <= 1) {
        $("#id_sensor_" + id_controles).attr('disabled', true).trigger("chosen:updated");
    }
    else {
        $("#id_sensor_" + id_controles).removeAttr('disabled').trigger("chosen:updated");
    }
};


// Recarga de los sensores de una clase de sensor de la ventana de anyadir/modificar widget
funcion_recarga_sensores_clase_sensor_widget = function(id_controles) {
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

        // Habilitación del sensor
        funcion_habilita_sensor_widget(id_controles);

        // Se lanza el evento de 'change'
        $("#id_sensor_" + id_controles).trigger('change');
    });
};


// Habilitación de campos de sensores
funcion_habilita_campo_widget = function(id_controles) {
    // Se deshabilita si sólo hay un valor para elegir
    var numero_campos = $("select#campo_" + id_controles + " option").length;
    if (numero_campos <= 1) {
        $("#campo_" + id_controles).attr('disabled', true);
    }
    else {
        $("#campo_" + id_controles).removeAttr('disabled');
    }
};


// Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar widget
funcion_recarga_campos_sensor_clase_sensor_widget = function(id_controles_widget, id_controles_sensor) {
    var tipo_widget = $("#tipo_widget").val();
    var clase_sensor = $("#clase_sensor_" + id_controles_sensor).val();
    var campo = $("#campo_"  + id_controles_sensor).val();
    var granularidad_valores = $("#granularidad_sensor_" + id_controles_widget).val();
    var intervalo_valores = $("#intervalo_valores_" + id_controles_widget).val();
    $.post("./src/lib/modulos/widgets/dame_lista_campos_sensor_widget.php", {
        tipo_widget: tipo_widget,
        clase_sensor: clase_sensor,
        granularidad_sensor: granularidad_valores,
        intervalo_valores: intervalo_valores,
        campo: campo
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#campo_" + id_controles_sensor).html(resultado.html);
        $("#campo_" + id_controles_sensor).trigger('change');

        // Habilitación del campo de sensor
        funcion_habilita_campo_widget(id_controles_sensor);

        // Campo seleccionado por defecto
        switch (tipo_widget) {
            case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
            case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
            case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR: {
                switch (clase_sensor) {
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    case CLASE_SENSOR_ENERGIA_REACTIVA:
                    case CLASE_SENSOR_AGUA: {
                        $("#campo_" + id_controles_sensor).val(CAMPO_INCREMENTO);
                        break;
                    }
                    case CLASE_SENSOR_COMPRA_ENERGIA: {
                        $("#campo_" + id_controles_sensor).val(CAMPO_CONSUMO_ESTIMADO);
                        break;
                    }
                    case CLASE_SENSOR_GAS: {
                        $("#campo_" + id_controles_sensor).val(CAMPO_CONSUMO);
                        break;
                    }
                }
                break;
            }
        }

        // Habilitación de granularidad
        switch (clase_sensor) {
            case CLASE_NINGUNA: {
                $("#granularidad_sensor_" + id_controles_widget).attr('disabled', true);
                break;
            }
            default: {
                $("#granularidad_sensor_" + id_controles_widget).removeAttr('disabled');
                break;
            }
        }
    });
};


// Habilitación de granularidad de sensor de widgets
funcion_habilita_granularidad_sensor_widget = function(id_controles) {
    // Se deshabilita si sólo hay un valor para elegir
    var numero_granularidades = $("select#granularidad_sensor_" + id_controles + " option").length;
    if (numero_granularidades <= 1) {
        $("#granularidad_sensor_" + id_controles).attr('disabled', true);
    }
    else {
        $("#granularidad_sensor_" + id_controles).removeAttr('disabled');
    }
};


// Recarga la lista de granularidades de una clase de sensor de un widget
funcion_recarga_lista_granularidades_sensor_clase_sensor_widget = function(id_controles) {
    $.post("./src/lib/modulos/widgets/dame_lista_granularidades_sensor_widget.php", {
        clase_sensor: $("#clase_sensor_" + id_controles).val(),
        granularidad_sensor: $("#granularidad_sensor_" + id_controles).val()
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#granularidad_sensor_"  + id_controles).html(resultado.html);
        $("#granularidad_sensor_"  + id_controles).trigger('change');
    });
};


// Habilitación de intervalo de valores de widget
funcion_habilita_intervalo_valores_widget = function(id_controles) {
    // Se deshabilita si sólo hay un valor para elegir
    var numero_intervalos = $("select#intervalo_valores_" + id_controles + " option").length;
    if (numero_intervalos <= 1) {
        $("#intervalo_valores_" + id_controles).attr('disabled', true);
    }
    else {
        $("#intervalo_valores_" + id_controles).removeAttr('disabled');
    }
};


// Recarga la lista de intervalos de valores de widget
funcion_recarga_intervalos_valores_sensor_widget = function(id_controles, id_controles_sensor) {
    var tipo_widget = $("#tipo_widget").val();
    var clase_sensor = $("#clase_sensor_" + id_controles_sensor).val();
    var campo = $("#campo_" + id_controles_sensor).val();
    var periodo_tiempo = $("#periodo_tiempo_" + id_controles).val();
    var intervalo_valores = $("#intervalo_valores_" + id_controles).val();
    $.post("./src/lib/modulos/widgets/dame_lista_intervalos_valores_sensor_widget.php", {
        tipo_widget: tipo_widget,
        clase_sensor: clase_sensor,
        campo: campo,
        periodo_tiempo: periodo_tiempo,
        intervalo_valores: intervalo_valores
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#intervalo_valores_" + id_controles).html(resultado.html);
        $("#intervalo_valores_" + id_controles).removeAttr('disabled');

        // Intervalo de valores por defecto
        if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
            switch (clase_sensor) {
                case CLASE_SENSOR_CORTES_TENSION: {
                    intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL_LINEAS;
                    break;
                }
                default: {
                    intervalo_valores = INTERVALO_VALORES_HORA;
                    break;
                }
            }
            $("#intervalo_valores_" + id_controles).val(intervalo_valores);
        }

        // Notificación de cambio de intervalo de valores (para ejecutar las acciones correspondientes)
        $("#intervalo_valores_"  + id_controles).trigger('change');
    });
};


// Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
funcion_recarga_campos_intervalo_valores_widget = function(id_controles_widget, id_controles_sensor) {
    var tipo_widget = $("#tipo_widget").val();
    var clase_sensor = $("#clase_sensor_" + id_controles_sensor).val();
    var intervalo_valores = $("#intervalo_valores_" + id_controles_widget).val();
    var campo = $("#campo_" + id_controles_sensor).val();
    $.post("./src/lib/modulos/widgets/dame_lista_campos_sensor_widget.php", {
        tipo_widget: tipo_widget,
        clase_sensor: clase_sensor,
        granularidad_sensor: null,
        intervalo_valores: intervalo_valores,
        campo: campo
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#campo_" + id_controles_sensor).html(resultado.html);
    });
};


// Se muestran los controles dependientes del periodo de tiempo del widget
funcion_muestra_controles_periodo_tiempo_widget = function(id_controles_widget) {
    var periodo_tiempo = $("#periodo_tiempo_" + id_controles_widget).val();
    switch (periodo_tiempo) {
        case ID_NINGUNO.toString(): {
            $("#control_iniciar_comienzo_periodo_tiempo_" + id_controles_widget).hide();
            $("#control_fecha_inicio_periodo_tiempo_" + id_controles_widget).hide();
            break;
        }
        case PERIODO_TIEMPO_FECHA_INICIO: {
            $("#control_iniciar_comienzo_periodo_tiempo_" + id_controles_widget).hide();
            $("#control_fecha_inicio_periodo_tiempo_" + id_controles_widget).show();
            break;
        }
        default: {
            $("#control_iniciar_comienzo_periodo_tiempo_" + id_controles_widget).show();
            $("#control_fecha_inicio_periodo_tiempo_" + id_controles_widget).hide();
            break;
        }
    }
};


// Habilita y muestra los controles dependientes de utilizar los colores de fondo de valores de widgets de valores de sensor
funcion_habilita_muestra_controles_utilizar_colores_fondo_widgets_valores_sensor = function(id_controles) {
    var utilizar_colores_fondo = parseInt($("#utilizar_colores_fondo_" + id_controles).val());
    switch (utilizar_colores_fondo) {
        case VALOR_SI: {
            $("#controles_colores_fondo_" + id_controles).show();
            $("#controles_valores_limites_colores_fondo_" + id_controles).show();
            break;
        }
        case VALOR_NO: {
            $("#controles_colores_fondo_" + id_controles).hide();
            $("#controles_valores_limites_colores_fondo_" + id_controles).hide();
            break;
        }
    }
};


// Muestra los controles dependientes de parámetros de mostrar números de página
funcion_muestra_controles_mostrar_numeros_pagina = function(id_controles) {
    var mostrar_numeros_pagina = parseInt($("#mostrar_numeros_pagina_" + id_controles).val());
    switch (mostrar_numeros_pagina) {
        case VALOR_SI: {
            $("#control_numero_numero_pagina_inicial_" + id_controles).show();
            $("#control_mostrar_numero_paginas_totales_" + id_controles).show();
            funcion_muestra_controles_mostrar_numeros_paginas_totales(id_controles);
            break;
        }
        case VALOR_NO: {
            $("#control_numero_numero_pagina_inicial_" + id_controles).hide();
            $("#control_mostrar_numero_paginas_totales_" + id_controles).hide();
            $("#control_numero_paginas_totales_automatico_" + id_controles).hide();
            $("#control_numero_numero_paginas_totales_manual_" + id_controles).hide();
            break;
        }
    }
};


// Muestra los controles dependientes de parámetros de mostrar números de páginas totales
funcion_muestra_controles_mostrar_numeros_paginas_totales = function(id_controles) {
    var mostrar_numero_paginas_totales = parseInt($("#mostrar_numero_paginas_totales_" + id_controles).val());
    var numero_paginas_totales_automatico = parseInt($("#numero_paginas_totales_automatico_" + id_controles).val());
    switch (mostrar_numero_paginas_totales) {
        case VALOR_SI: {
            $("#control_numero_paginas_totales_automatico_" + id_controles).show();
            if (numero_paginas_totales_automatico == VALOR_SI) {
                $("#control_numero_numero_paginas_totales_manual_" + id_controles).hide();
            }
            else {
                $("#control_numero_numero_paginas_totales_manual_" + id_controles).show();
            }
            break;
        }
        case VALOR_NO: {
            $("#control_numero_paginas_totales_automatico_" + id_controles).hide();
            $("#control_numero_numero_paginas_totales_manual_" + id_controles).hide();
            break;
        }
    }
};


//
// Funciones utilizadas en varios módulos
//


// Muestra el control de parámetros extra de la clase y campo especificados en una ventana de administración
funcion_muestra_control_parametros_extra_campo_administracion = function(id_controles) {
    var clase = $("#clase_sensor_" + id_controles).val();
    var campo = $("#campo_" + id_controles).val();
    switch (clase) {
        case CLASE_SENSOR_TEMPERATURA: {
            switch (campo) {
                case CAMPO_GRADOS_HORA_CALEFACCION:
                case CAMPO_GRADOS_HORA_REFRIGERACION:
                case CAMPO_GRADOS_DIA_CALEFACCION:
                case CAMPO_GRADOS_DIA_REFRIGERACION: {
                    $("#etiqueta_parametros_extra_campo_" + id_controles).html(TLNT.Idiomas._("Referencia") + ":");
                    $("#control_parametros_extra_campo_" + id_controles).show();
                    $("#parametros_extra_campo_" + id_controles).addClass('TLNT_input_mandatory TLNT_input_float');
                    break;
                }
                case CAMPO_TEMPERATURA: {
                    $("#control_parametros_extra_campo_" + id_controles).hide();
                    $("#etiqueta_parametros_extra_campo_" + id_controles).html("");
                    $("#parametros_extra_campo_" + id_controles).removeClass('TLNT_input_mandatory TLNT_input_float');
                    $("#parametros_extra_campo_" + id_controles).val("");
                    break;
                }
            }
            break;
        }
        default: {
            $("#control_parametros_extra_campo_" + id_controles).hide();
            $("#etiqueta_parametros_extra_campo_" + id_controles).html("");
            $("#parametros_extra_campo_" + id_controles).removeClass('TLNT_input_mandatory TLNT_input_float');
            $("#parametros_extra_campo_" + id_controles).val("");
            break;
        }
    }
};


// Muestra el control de parámetros extra de la clase y campo especificados en los parámetros de un informe
funcion_muestra_control_parametros_extra_campo_informe = function(id_controles, clase) {
    var campo = $("#campo_" + id_controles).val();
    switch (clase) {
        case CLASE_SENSOR_TEMPERATURA: {
            switch (campo) {
                case CAMPO_GRADOS_HORA_CALEFACCION:
                case CAMPO_GRADOS_HORA_REFRIGERACION:
                case CAMPO_GRADOS_DIA_CALEFACCION:
                case CAMPO_GRADOS_DIA_REFRIGERACION: {
                    $("#etiqueta_parametros_extra_campo_" + id_controles).html(TLNT.Idiomas._("Referencia") + ":");
                    $("#control_parametros_extra_campo_" + id_controles).parent().show();
                    $("#control_parametros_extra_campo_" + id_controles).show();
                    break;
                }
                case CAMPO_TEMPERATURA: {
                    $("#control_parametros_extra_campo_" + id_controles).hide();
                    $("#control_parametros_extra_campo_" + id_controles).parent().hide();
                    $("#etiqueta_parametros_extra_campo_" + id_controles).html("");
                    $("#parametros_extra_campo_" + id_controles).val("");
                    break;
                }
            }
            break;
        }
        default: {
            $("#control_parametros_extra_campo_" + id_controles).hide();
            $("#control_parametros_extra_campo_" + id_controles).parent().hide();
            $("#etiqueta_parametros_extra_campo_" + id_controles).html("");
            $("#parametros_extra_campo_" + id_controles).val("");
            break;
        }
    }
};
