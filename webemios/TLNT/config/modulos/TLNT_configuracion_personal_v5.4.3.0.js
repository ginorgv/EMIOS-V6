// Eventos de los botones (por funcionalidad)
TLNT.Navegacion.botones_secciones_personal = [
    // Plantillas de informe
    {	selector: '#boton_personal_filtro_plantillas_informes_tabla',
		funcion: 	boton_personal_filtro_plantillas_informes_tabla
	},
    {	selector: '#boton_personal_informe_plantilla_informe_ver_informe',
		funcion: 	boton_personal_informe_plantilla_informe_ver_informe
	},
    {	selector: '#boton_personal_informe_plantilla_informe_generar_pdf',
		funcion: 	boton_personal_informe_plantilla_informe_generar_pdf
	},
    {	selector: '#boton_personal_informe_plantilla_informe_anyadir_informe_automatico',
		funcion: 	boton_personal_informe_plantilla_informe_anyadir_informe_automatico
	}
];


TLNT.Navegacion.botones_tablas_datos_personal = [
    // Plantillas de informes
    {	selector: '.boton_personal_mostrar_ventana_anyadir_modificar_plantilla_informe',
		funcion: 	boton_personal_mostrar_ventana_anyadir_modificar_plantilla_informe
	},
    {	selector: '.boton_personal_actualizar_tabla_plantillas_informes',
		funcion: 	boton_personal_actualizar_tabla_plantillas_informes
	},
    {	selector: '.boton_personal_eliminar_plantilla_informe',
		funcion: 	boton_personal_eliminar_plantilla_informe
	},
    // Parámetros de plantillas de informes
    {	selector: '.boton_personal_mostrar_ventana_anyadir_modificar_parametro_plantilla_informe',
		funcion: 	boton_personal_mostrar_ventana_anyadir_modificar_parametro_plantilla_informe
	},
    {	selector: '.boton_personal_actualizar_tabla_parametros_plantilla_informe',
		funcion: 	boton_personal_actualizar_tabla_parametros_plantilla_informe
	},
    // Elementos de plantillas de informes
    {	selector: '.boton_personal_mostrar_ventana_anyadir_modificar_elemento_plantilla_informe',
		funcion: 	boton_personal_mostrar_ventana_anyadir_modificar_elemento_plantilla_informe
	},
    {	selector: '.boton_personal_actualizar_tabla_elementos_plantilla_informe',
		funcion: 	boton_personal_actualizar_tabla_elementos_plantilla_informe
	},
    // Ayuda (tablas)
    {	selector: '.boton_personal_ayuda_tabla_widgets',
		funcion: 	boton_personal_ayuda_tabla_widgets
	},
    {	selector: '.boton_personal_ayuda_tabla_plantillas_informes',
		funcion: 	boton_personal_ayuda_tabla_plantillas_informes
	},
    {	selector: '.boton_personal_ayuda_tabla_informes_automaticos',
		funcion: 	boton_personal_ayuda_tabla_informes_automaticos
	}
];


TLNT.Navegacion.botones_detalles_tablas_datos_personal = [
    // Parámetros de plantillas de informes
    {	selector: '.boton_personal_mostrar_ventana_anyadir_modificar_parametro_plantilla_informe',
		funcion: 	boton_personal_mostrar_ventana_anyadir_modificar_parametro_plantilla_informe
	},
    {	selector: '.boton_personal_actualizar_tabla_parametros_plantilla_informe',
		funcion: 	boton_personal_actualizar_tabla_parametros_plantilla_informe
	},
    {	selector: '.boton_personal_eliminar_parametro_plantilla_informe',
		funcion: 	boton_personal_eliminar_parametro_plantilla_informe
	},
    {	selector: '.boton_personal_mostrar_ventana_modificar_posiciones_parametros_plantilla_informe',
		funcion: 	boton_personal_mostrar_ventana_modificar_posiciones_parametros_plantilla_informe
	},
    // Elementos de plantillas de informes
    {	selector: '.boton_personal_mostrar_ventana_anyadir_modificar_elemento_plantilla_informe',
		funcion: 	boton_personal_mostrar_ventana_anyadir_modificar_elemento_plantilla_informe
	},
    {	selector: '.boton_personal_actualizar_tabla_elementos_plantilla_informe',
		funcion: 	boton_personal_actualizar_tabla_elementos_plantilla_informe
	},
    {	selector: '.boton_personal_eliminar_elemento_plantilla_informe',
		funcion: 	boton_personal_eliminar_elemento_plantilla_informe
	},
    {	selector: '.boton_personal_mostrar_ventana_modificar_posiciones_elementos_plantilla_informe',
		funcion: 	boton_personal_mostrar_ventana_modificar_posiciones_elementos_plantilla_informe
	},
    {	selector: '.boton_personal_mostrar_ventana_eliminar_elementos_plantilla_informe',
		funcion: 	boton_personal_mostrar_ventana_eliminar_elementos_plantilla_informe
	}
];


TLNT.Navegacion.botones_tablas_datos_informes_personal = [
    // Comentarios
    {   selector: '.boton_mostrar_ventana_anyadir_comentarios',
		funcion: 	boton_mostrar_ventana_anyadir_comentarios
	},
    {   selector: '.boton_mostrar_ventana_anyadir_modificar_comentario',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_comentario
	},
    {	selector: '.boton_eliminar_comentario',
		funcion: 	boton_eliminar_comentario
	}
];


TLNT.Navegacion.botones_ventanas_modales_personal = [
    // Plantillas de informes
	{	selector: '.boton_personal_anyadir_modificar_plantilla_informe',
		funcion: 	boton_personal_anyadir_modificar_plantilla_informe
	},
    // Parámetros de plantillas de informes
	{	selector: '.boton_personal_anyadir_modificar_parametro_plantilla_informe',
		funcion: 	boton_personal_anyadir_modificar_parametro_plantilla_informe
	},
    {	selector: '.boton_modificar_posiciones_parametros_plantilla_informe',
		funcion: 	boton_modificar_posiciones_parametros_plantilla_informe
	},
    // Elementos de plantillas de informes
	{	selector: '.boton_personal_anyadir_modificar_elemento_plantilla_informe',
		funcion: 	boton_personal_anyadir_modificar_elemento_plantilla_informe
	},
    {	selector: '.boton_modificar_posiciones_elementos_plantilla_informe',
		funcion: 	boton_modificar_posiciones_elementos_plantilla_informe
	},
    {	selector: '.boton_eliminar_elementos_plantilla_informe',
		funcion: 	boton_eliminar_elementos_plantilla_informe
	},
    // Ayuda (elementos de plantillas de informes)
    {	selector: '#boton_ayuda_agrupaciones_dias_semana_elemento_plantilla_informe_sensores_comparacion_perfil_horario',
		funcion: 	boton_ayuda_agrupaciones_dias_semana
	},
    {	selector: '#boton_ayuda_exclusion_fechas_elemento_plantilla_informe',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_elemento_plantilla_informe',
		funcion: 	boton_ayuda_fechas
	}
];


//
// Funciones de establecimiento de eventos (por funcionalidad)
//


TLNT.Navegacion.establece_eventos_secciones_personal = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_secciones_personal);

    establece_eventos_secciones_personal_informes();
};


TLNT.Navegacion.establece_eventos_contenido_informes_personal = function() {
    establece_eventos_contenido_informes_personal_informes();
};


TLNT.Navegacion.establece_eventos_tablas_datos_personal = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_personal);
};


TLNT.Navegacion.establece_eventos_detalles_tablas_datos_personal = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_detalles_tablas_datos_personal);
};


TLNT.Navegacion.establece_eventos_tablas_datos_informes_personal = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_informes_personal);
};


TLNT.Navegacion.establece_eventos_ventanas_modales_personal = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_ventanas_modales_personal);

    establece_eventos_ventanas_modales_personal();
};


//
// Funciones auxiliares para establecer las acciones de los controles
//


establece_eventos_secciones_personal_informes = function() {
    establece_eventos_secciones_personal_informes_plantillas_informes();
};


establece_eventos_secciones_personal_informes_plantillas_informes = function() {
    $("#cabecera-parametros-plantillas-informes").off();
    $("#parametros-plantillas-informes").off();
    $("#cabecera-subtitulos-portadas-plantillas-informes").off();
    $("#subtitulos-portadas-plantillas-informes").off();
    $("#cabecera-titulos-plantillas-informes").off();
    $("#titulos-plantillas-informes").off();
    $("#cabecera-textos-plantillas-informes").off();
    $("#textos-plantillas-informes").off();
    $("#cabecera-imagenes-plantillas-informes").off();
    $("#imagenes-plantillas-informes").off();
    $("#id_plantilla_informe_personal_informe_plantilla_informe").off();
    $("#mostrar_numeros_pagina_personal_informe_plantilla_informe").off();
    $("#mostrar_numero_paginas_totales_personal_informe_plantilla_informe").off();
    $("#numero_paginas_totales_automatico_personal_informe_plantilla_informe").off();

    // Por defecto se ocultan los controles de plantillas de informes
    $("#cabecera-parametros-plantillas-informes").show(function() {
        $("#cabecera-parametros-plantillas-informes").hide();
    });
    $("#parametros-plantillas-informes").show(function() {
        $("#parametros-plantillas-informes").hide();
    });
    $("#cabecera-subtitulos-portadas-plantillas-informes").show(function() {
        $("#cabecera-subtitulos-portadas-plantillas-informes").hide();
    });
    $("#subtitulos-portadas-plantillas-informes").show(function() {
        $("#subtitulos-portadas-plantillas-informes").hide();
    });
    $("#cabecera-titulos-plantillas-informes").show(function() {
        $("#cabecera-titulos-plantillas-informes").hide();
    });
    $("#titulos-plantillas-informes").show(function() {
        $("#titulos-plantillas-informes").hide();
    });
    $("#cabecera-textos-plantillas-informes").show(function() {
        $("#cabecera-textos-plantillas-informes").hide();
    });
    $("#textos-plantillas-informes").show(function() {
        $("#textos-plantillas-informes").hide();
    });
    $("#cabecera-imagenes-plantillas-informes").show(function() {
        $("#cabecera-imagenes-plantillas-informes").hide();
    });
    $("#imagenes-plantillas-informes").show(function() {
        $("#imagenes-plantillas-informes").hide();
    });

    // Recarga la información del informe de plantilla de informe
    var function_recarga_informacion_informe_plantilla_informe = function() {
        var id_plantilla_informe = $("#id_plantilla_informe_personal_informe_plantilla_informe").val();
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/dame_informacion_informe_plantilla_informe.php", {
            id_plantilla_informe: id_plantilla_informe
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            var fecha_inicio = resultado.fecha_inicio;
            var fecha_fin = resultado.fecha_fin;
            var html_controles_parametros = resultado.html_controles_parametros;
            var html_controles_subtitulos_portadas = resultado.html_controles_subtitulos_portadas;
            var html_controles_titulos = resultado.html_controles_titulos;
            var html_controles_textos = resultado.html_controles_textos;
            var html_controles_imagenes = resultado.html_controles_imagenes;
            var tipo_seleccion_horario_semanal_fechas = resultado.tipo_seleccion_horario_semanal_fechas;

            if ($("#cabecera-subtitulos-portadas-plantillas-informes").attr("desplegado") == true) {
                $("#cabecera-subtitulos-portadas-plantillas-informes").click();
            }
            if ($("#cabecera-titulos-plantillas-informes").attr("desplegado") == true) {
                $("#cabecera-titulos-plantillas-informes").click();
            }
            if ($("#cabecera-textos-plantillas-informes").attr("desplegado") == true) {
                $("#cabecera-textos-plantillas-informes").click();
            }
            if ($("#cabecera-imagenes-plantillas-informes").attr("desplegado") == true) {
                $("#cabecera-imagenes-plantillas-informes").click();
            }

            funcion_recarga_fechas_inicio_fin_informe_plantilla_informe(fecha_inicio, fecha_fin);
            realiza_acciones_recarga_controles_parametros_informe_plantilla_informe(html_controles_parametros);
            realiza_acciones_recarga_controles_subtitulos_portadas_informe_plantilla_informe(html_controles_subtitulos_portadas);
            realiza_acciones_recarga_controles_titulos_informe_plantilla_informe(html_controles_titulos);
            realiza_acciones_recarga_controles_textos_informe_plantilla_informe(html_controles_textos);
            realiza_acciones_recarga_controles_imagenes_informe_plantilla_informe(html_controles_imagenes);
            funcion_muestra_controles_horario_semanal_exclusion_fechas_informe_plantilla_informe(tipo_seleccion_horario_semanal_fechas);
            funcion_muestra_controles_parametros_pie_pagina_informe_plantilla_informe();
        });
    };
    $("#id_plantilla_informe_personal_informe_plantilla_informe").show(function_recarga_informacion_informe_plantilla_informe);
    $("#id_plantilla_informe_personal_informe_plantilla_informe").change(function_recarga_informacion_informe_plantilla_informe);

    // Recarga las fechas de inicio y fin del informe de plantilla de informe
    var funcion_recarga_fechas_inicio_fin_informe_plantilla_informe = function(fecha_inicio, fecha_fin) {
        TLNT.Navegacion.establece_fecha_control("fecha_inicio_personal_informe_plantilla_informe", fecha_inicio);
        TLNT.Navegacion.establece_fecha_control("fecha_fin_personal_informe_plantilla_informe", fecha_fin);
        $("#hora_inicio_personal_informe_plantilla_informe").val("00:00");
        $("#hora_fin_personal_informe_plantilla_informe").val("23:59");
    };

    // Muestra u oculta los controles de horario semanal y exclusión de fechas del informe plantilla de informe
    var funcion_muestra_controles_horario_semanal_exclusion_fechas_informe_plantilla_informe = function(tipo_seleccion_horario_semanal_fechas) {
        // Se muestran u ocultan los controles
        switch (tipo_seleccion_horario_semanal_fechas) {
            case TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_FIJO: {
                $("#cabecera_horario_semanal_personal_informe_plantilla_informe").hide();
                for (var i = 0; i < 7; i++) {
                    $("#horario_semanal_personal_informe_plantilla_informe-" + i).hide();
                }
                $("#cabecera_exclusion_fechas_personal_informe_plantilla_informe").hide();
                $("#exclusion_fechas_personal_informe_plantilla_informe").hide();
                $("#cabecera_inclusion_fechas_personal_informe_plantilla_informe").hide();
                $("#inclusion_fechas_personal_informe_plantilla_informe").hide();
                break;
            }
            case TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_CONFIGURABLE: {
                $("#cabecera_horario_semanal_personal_informe_plantilla_informe").show();
                if ($("#cabecera_horario_semanal_personal_informe_plantilla_informe .opcion-desplegar-elementos-desplegables").hasClass("icon-caret-up") == true) {
                    for (var i = 0; i < 7; i++) {
                        $("#horario_semanal_personal_informe_plantilla_informe-" + i).show();
                    }
                }
                $("#cabecera_exclusion_fechas_personal_informe_plantilla_informe").show();
                if ($("#cabecera_exclusion_fechas_personal_informe_plantilla_informe .opcion-desplegar-elementos-desplegables").hasClass("icon-caret-up") == true) {
                    $("#exclusion_fechas_personal_informe_plantilla_informe").show();
                }
                $("#cabecera_inclusion_fechas_personal_informe_plantilla_informe").show();
                if ($("#cabecera_inclusion_fechas_personal_informe_plantilla_informe .opcion-desplegar-elementos-desplegables").hasClass("icon-caret-up") == true) {
                    $("#inclusion_fechas_personal_informe_plantilla_informe").show();
                }
                break;
            }
        }
    };

    // Muestra u oculta los controles de parámetros de pie de página del informe plantilla de informe
    var funcion_muestra_controles_parametros_pie_pagina_informe_plantilla_informe = function() {
        var id_plantilla_informe = $("#id_plantilla_informe_personal_informe_plantilla_informe").val();
        if (id_plantilla_informe == ID_NINGUNO) {
            $("#cabecera_parametros_pie_pagina_personal_informe_plantilla_informe").hide();
            $("#parametros_numeros_paginas_parametros_pie_pagina_personal_informe_plantilla_informe").hide();
            $("#textos_titulos_parametros_pie_pagina_personal_informe_plantilla_informe").hide();
        }
        else {
            $("#cabecera_parametros_pie_pagina_personal_informe_plantilla_informe").show();
            if ($("#cabecera_parametros_pie_pagina_personal_informe_plantilla_informe .opcion-desplegar-elementos-desplegables").hasClass("icon-caret-up") == true) {
                $("#parametros_numeros_paginas_parametros_pie_pagina_personal_informe_plantilla_informe").show();
                $("#textos_titulos_parametros_pie_pagina_personal_informe_plantilla_informe").show();
            }
        }
    };

    // Muestra controles de números de páginas
    var funcion_muestra_controles_mostrar_numeros_pagina_informe_plantilla_informe = function() {
        funcion_muestra_controles_mostrar_numeros_pagina("personal_informe_plantilla_informe");
    };
    $("#mostrar_numeros_pagina_personal_informe_plantilla_informe").show(funcion_muestra_controles_mostrar_numeros_pagina_informe_plantilla_informe);
    $("#mostrar_numeros_pagina_personal_informe_plantilla_informe").change(funcion_muestra_controles_mostrar_numeros_pagina_informe_plantilla_informe);

    // Muestra controles de mostrar números de páginas totales
    var funcion_muestra_controles_mostrar_numeros_paginas_totales_informe_plantilla_informe = function() {
        funcion_muestra_controles_mostrar_numeros_paginas_totales("personal_informe_plantilla_informe");
    };
    $("#mostrar_numero_paginas_totales_personal_informe_plantilla_informe").show(funcion_muestra_controles_mostrar_numeros_paginas_totales_informe_plantilla_informe);
    $("#mostrar_numero_paginas_totales_personal_informe_plantilla_informe").change(funcion_muestra_controles_mostrar_numeros_paginas_totales_informe_plantilla_informe);
    $("#numero_paginas_totales_automatico_personal_informe_plantilla_informe").change(funcion_muestra_controles_mostrar_numeros_paginas_totales_informe_plantilla_informe);

    // Ajuste de textos de plantillas de informes y evento de contador de caracteres
    $('#pestanyas-plantillas-informes-personal').off('shown.bs.tab');
    $('#pestanyas-plantillas-informes-personal').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var href_pestanya_activa = $('#pestanyas-plantillas-informes-personal .active > a').attr('href');
        var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
        switch (id_pestanya_activa) {
            case "informe-plantilla-informe-personal": {
                TLNT.Navegacion.redimensiona_textarea(".area-texto-informe");
                $(".area-texto-informe").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
                break;
            }
            default: {
                break;
            }
        }
    });
};


establece_eventos_contenido_informes_personal_informes = function() {
    establece_eventos_contenido_informes_personal_informes_plantillas_informes();
};


establece_eventos_contenido_informes_personal_informes_plantillas_informes = function() {
    $("#area-texto-informe").off();

    // Contador de caracteres de notas
    $(".area-texto-informe").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_personal = function() {
    establece_eventos_ventanas_modales_personal_administracion_plantilla_informe();
    establece_eventos_ventanas_modales_personal_administracion_parametros_plantilla_informe();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe();
};


establece_eventos_ventanas_modales_personal_administracion_plantilla_informe = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_plantilla_informe").off();
    $("#tipo_plantilla_informe").off();
    $("#id_red_destino_plantilla_informe").off();

    // Contador de caracteres de descripción de plantilla de informe
    $("#descripcion_plantilla_informe").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Ventana de administración de plantilla de informe
    // - Selección de fichero de logo PDF
    $("#fichero_logo_pdf_plantilla_informe_text").show(function() {
        $('#fichero_logo_pdf_plantilla_informe_file').hide();
    });
    $('#fichero_logo_pdf_plantilla_informe_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_logo_pdf_plantilla_informe_text').val(fichero);
    });
    $('#boton_anyadir_modificar_plantilla_informe_seleccionar_fichero_logo_pdf').click(function() {
        $('#fichero_logo_pdf_plantilla_informe_file').click();
    });

    // Habilita y muestra los controles dependientes de si hay logo personalizado
    var funcion_habilita_muestra_controles_logo_personalizado_plantilla_informe = function() {
        var logo_personalizado = parseInt($("#logo_personalizado_plantilla_informe").val());
        switch (logo_personalizado) {
            case VALOR_NO: {
                $("#nombre_logo_plantilla_informe").removeClass('TLNT_input_mandatory');
                $("#control_nombre_logo_plantilla_informe").hide();
                $("#control_fichero_logo_pdf_plantilla_informe").hide();
                break;
            }
            case VALOR_SI: {
                $("#nombre_logo_plantilla_informe").addClass('TLNT_input_mandatory');
                $("#control_nombre_logo_plantilla_informe").show();
                $("#control_fichero_logo_pdf_plantilla_informe").show();
                break;
            }
        }
    };
    $("#logo_personalizado_plantilla_informe").show(funcion_habilita_muestra_controles_logo_personalizado_plantilla_informe);
    $("#logo_personalizado_plantilla_informe").change(funcion_habilita_muestra_controles_logo_personalizado_plantilla_informe);

    // Muestra u oculta la red destino de la plantilla de informe
    var funcion_muestra_oculta_red_destino_plantilla_informe = function() {
        var tipo = $("#tipo_plantilla_informe").val();
        var numero_ids_redes_destino = $("select#id_red_destino_plantilla_informe option").length;
        if ((tipo == TIPO_PLANTILLA_INFORME_CONFIGURABLE) && (numero_ids_redes_destino > 1)) {
            $("#control_id_red_destino_plantilla_informe").show();
        }
        else {
            $("#control_id_red_destino_plantilla_informe").hide();
        }
    };
    $("#id_red_destino_plantilla_informe").show(funcion_muestra_oculta_red_destino_plantilla_informe);
    $("#tipo_plantilla_informe").change(funcion_muestra_oculta_red_destino_plantilla_informe);

    // Muestra u oculta el usuario destino de la plantilla de informe
    var funcion_muestra_oculta_usuario_destino_plantilla_informe = function() {
        var perfil_usuario = $("#parametros_ventana_anyadir_modificar_plantilla_informe").attr("perfil_usuario");
        if (perfil_usuario != PERFIL_USUARIO_ESTANDAR) {
            var id_red_destino = $("#id_red_destino_plantilla_informe").val();
            var id_red = $("#parametros_ventana_anyadir_modificar_plantilla_informe").attr("id_red");
            if (id_red_destino == id_red) {
                $("#control_id_usuario_destino_plantilla_informe").show();
            }
            else {
                $("#id_usuario_destino_plantilla_informe").val(ID_NINGUNO);
                $("#control_id_usuario_destino_plantilla_informe").hide();
            }
        }
    };
    $("#id_red_destino_plantilla_informe").change(funcion_muestra_oculta_usuario_destino_plantilla_informe);
};


establece_eventos_ventanas_modales_personal_administracion_parametros_plantilla_informe = function() {
    // Desactivación de eventos anteriores
    $("#tipo_parametro_plantilla_informe").off();
    $("#clase_sensor_parametro_plantilla_informe_sensor").off();

    // Botones para subir/bajar los elementos en la lista de posiciones de parámetros y elementos de plantilla de informe
    // http://stackoverflow.com/questions/6713702/move-item-up-and-down-in-select-using-button
    // http://jsfiddle.net/Shef/Aq8s3/
    $('#boton_subir_posicion_parametro').unbind('click');
    $('#boton_subir_posicion_parametro').click(function() {
        var parametro_seleccionado = $('#posicion_parametros option:selected'), $this = $(this);
        parametro_seleccionado.first().prev().before(parametro_seleccionado);
    });
    $('#boton_bajar_posicion_parametro').unbind('click');
    $('#boton_bajar_posicion_parametro').click(function() {
        var elemento_seleccionado = $('#posicion_parametros option:selected'), $this = $(this);
        elemento_seleccionado.last().next().after(elemento_seleccionado);
    });

    // Muestra las pestañas del tipo de parámetro de plantilla de informe correspondiente
    var funcion_muestra_pestanyas_tipo_parametro_plantilla_informe = function() {
        $("#titulo-tab-tipo-sensor").hide();
        $("#titulo-tab-tipo-grupo-sensores").hide();
        $("#titulo-tab-tipo-actuador").hide();
        $("#titulo-tab-tipo-grupo-actuadores").hide();

        var tipo_parametro_plantilla_informe = $("#tipo_parametro_plantilla_informe").val();
        switch (tipo_parametro_plantilla_informe) {
            case TIPO_NINGUNO: {
                break;
            }
            case TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR: {
                $("#titulo-tab-tipo-sensor").show();
                break;
            }
            case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES: {
                $("#titulo-tab-tipo-grupo-sensores").show();
                break;
            }
            case TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR: {
                $("#titulo-tab-tipo-actuador").show();
                break;
            }
            case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES: {
                $("#titulo-tab-tipo-grupo-actuadores").show();
                break;
            }
        }
    };
    $("#tipo_parametro_plantilla_informe").show(funcion_muestra_pestanyas_tipo_parametro_plantilla_informe);
    $("#tipo_parametro_plantilla_informe").change(funcion_muestra_pestanyas_tipo_parametro_plantilla_informe);

    // Muestra controles al mostrar o  modificar la clase de sensor de un parámetro de plantilla de informe de tipo sensor
    var funcion_muestra_controles_clase_sensor_parametro_plantilla_informe_sensor = function() {
        var clase_sensor_parametro = $("#clase_sensor_parametro_plantilla_informe_sensor").val();
        switch (clase_sensor_parametro) {
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_CORTES_TENSION: {
                $("#control_id_parametro_sensor_asociado_parametro_plantilla_informe_sensor").show();

                // Se deshabilita si sólo hay un valor para elegir
                var numero_ids = $("select#id_parametro_sensor_asociado_parametro_plantilla_informe_sensor option").length;
                if (numero_ids <= 1) {
                    $("#id_parametro_sensor_asociado_parametro_plantilla_informe_sensor").attr('disabled', true);
                }
                else {
                    $("#id_parametro_sensor_asociado_parametro_plantilla_informe_sensor").removeAttr('disabled');
                }
                break;
            }
            default: {
                $("#control_id_parametro_sensor_asociado_parametro_plantilla_informe_sensor").hide();
                $("#id_parametro_sensor_asociado_parametro_plantilla_informe_sensor").val(ID_NINGUNO);
                break;
            }
        }
    };
    $("#clase_sensor_parametro_plantilla_informe_sensor").show(funcion_muestra_controles_clase_sensor_parametro_plantilla_informe_sensor);

    // Realiza acciones al mostrar o modificar la clase de sensor de un parámetro de plantilla de informe de tipo sensor
    var funcion_realiza_acciones_clase_sensor_parametro_plantilla_informe_sensor_modificada = function() {
        var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_parametro_plantilla_informe").attr("id_plantilla_informe");
        var clase_sensor = $("#clase_sensor_parametro_plantilla_informe_sensor").val();
        var id_parametro = $("#id_parametro_sensor_asociado_parametro_plantilla_informe_sensor").val();

        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/dame_lista_parametros_sensores_asociados_parametro.php", {
            id_plantilla_informe: id_plantilla_informe,
            clase_sensor: clase_sensor,
            id_parametro: id_parametro
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_parametro_sensor_asociado_parametro_plantilla_informe_sensor").html(resultado.html);

            // Muestra u oculta los controles
            funcion_muestra_controles_clase_sensor_parametro_plantilla_informe_sensor();
        });
    };
    $("#clase_sensor_parametro_plantilla_informe_sensor").change(funcion_realiza_acciones_clase_sensor_parametro_plantilla_informe_sensor_modificada);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe = function() {
    // Desactivación de eventos anteriores
    $("#tipo_elemento_plantilla_informe").off();

    // Mostrar listas dobles
    if ($('#select_elementos_informe_elemento_plantilla_informe_no_visible').length) {
        $('#select_elementos_informe_elemento_plantilla_informe_no_visible').attr("id", "select_elementos_informe_elemento_plantilla_informe_visible");
        TLNT.Navegacion.convierte_lista_doble("elementos_informe_elemento_plantilla_informe", true);
    }
    if ($('#select_parametros_requeridos_elemento_plantilla_informe_no_visible').length) {
        $('#select_parametros_requeridos_elemento_plantilla_informe_no_visible').attr("id", "select_parametros_requeridos_elemento_plantilla_informe_visible");
        TLNT.Navegacion.convierte_lista_doble("parametros_requeridos_elemento_plantilla_informe", true);
    }

    // Botones para subir/bajar los elementos en la lista de posiciones de parámetros y elementos de plantilla de informe
    // http://stackoverflow.com/questions/6713702/move-item-up-and-down-in-select-using-button
    // http://jsfiddle.net/Shef/Aq8s3/
    $('#boton_subir_posicion_elemento').unbind('click');
    $('#boton_subir_posicion_elemento').click(function() {
        var elemento_seleccionado = $('#posicion_elementos option:selected'), $this = $(this);
        elemento_seleccionado.first().prev().before(elemento_seleccionado);
    });
    $('#boton_bajar_posicion_elemento').unbind('click');
    $('#boton_bajar_posicion_elemento').click(function() {
        var elemento_seleccionado = $('#posicion_elementos option:selected'), $this = $(this);
        elemento_seleccionado.last().next().after(elemento_seleccionado);
    });

    // Muestra las pestañas del tipo de elemento de plantilla de informe correspondiente
    var funcion_muestra_pestanyas_tipo_elemento_plantilla_informe = function() {
        $("#titulo-tab-tipo-portada").hide();
        $("#titulo-tab-tipo-titulo").hide();
        $("#titulo-tab-tipo-texto").hide();
        $("#titulo-tab-tipo-notas").hide();
        $("#titulo-tab-tipo-imagen").hide();
        $("#titulo-tab-tipo-comentarios-principal").hide();
        $("#titulo-tab-tipo-comentarios-sensores").hide();
        $("#titulo-tab-tipo-comentarios-actuadores").hide();
        $("#titulo-tab-tipo-comentarios-sensores-oculta").hide();
        $("#titulo-tab-tipo-comentarios-actuadores-oculta").hide();
        $("#titulo-tab-tipo-sensores-activaciones-eventos-principal").hide();
        $("#titulo-tab-tipo-sensores-activaciones-eventos-eventos").hide();
        $("#titulo-tab-tipo-sensores-informacion").hide();
        $("#titulo-tab-tipo-sensores-analisis-horario").hide();
        $("#titulo-tab-tipo-sensores-analisis-diario").hide();
        $("#titulo-tab-tipo-sensores-analisis-comportamiento-principal").hide();
        $("#titulo-tab-tipo-sensores-analisis-comportamiento-sensores").hide();
        $("#titulo-tab-tipo-sensores-comparacion-periodos").hide();
        $("#titulo-tab-tipo-sensores-comparacion-perfil-horario-principal").hide();
        $("#titulo-tab-tipo-sensores-comparacion-perfil-horario-perfil-horario").hide();
        $("#titulo-tab-tipo-sensores-comparacion-campos-iguales-principal").hide();
        $("#titulo-tab-tipo-sensores-comparacion-campos-iguales-sensores-secundarios").hide();
        $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-principal").hide();
        $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-1").hide();
        $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-2").hide();
        $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-3").hide();
        $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-4").hide();
        $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-5").hide();
        $("#titulo-tab-tipo-sensores-analisis-comparativo-principal").hide();
        $("#titulo-tab-tipo-sensores-analisis-comparativo-sensores").hide();
        $("#titulo-tab-tipo-sensores-valores-generales-principal").hide();
        $("#titulo-tab-tipo-sensores-valores-generales-campo-1").hide();
        $("#titulo-tab-tipo-sensores-valores-generales-campo-2").hide();
        $("#titulo-tab-tipo-sensores-valores-generales-campo-3").hide();
        $("#titulo-tab-tipo-sensores-valores-generales-sensores").hide();
        $("#titulo-tab-tipo-sensores-incrementos-totales-principal").hide();
        $("#titulo-tab-tipo-sensores-incrementos-totales-campo-1").hide();
        $("#titulo-tab-tipo-sensores-incrementos-totales-campo-2").hide();
        $("#titulo-tab-tipo-sensores-incrementos-totales-campo-3").hide();
        $("#titulo-tab-tipo-sensores-incrementos-totales-sensores").hide();
        $("#titulo-tab-tipo-sensores-histograma").hide();
        $("#titulo-tab-tipo-sensores-correlacion-principal").hide();
        $("#titulo-tab-tipo-sensores-correlacion-sensor-independiente-1").hide();
        $("#titulo-tab-tipo-sensores-correlacion-sensor-independiente-2").hide();
        $("#titulo-tab-tipo-sensores-correlacion-sensor-independiente-3").hide();
        $("#titulo-tab-tipo-sensores-correlacion-sensor-independiente-4").hide();
        $("#titulo-tab-tipo-sensores-correlacion-sensor-dependiente").hide();
        $("#titulo-tab-tipo-actuadores-informacion-acciones-enviadas-principal").hide();
        $("#titulo-tab-tipo-actuadores-informacion-acciones-enviadas-sensor").hide();
        $("#titulo-tab-tipo-smartmeter-consumos-costes-generales-principal").hide();
        $("#titulo-tab-tipo-smartmeter-consumos-costes-generales-sensores").hide();
        $("#titulo-tab-tipo-smartmeter-consumos-costes-totales-principal").hide();
        $("#titulo-tab-tipo-smartmeter-consumos-costes-totales-sensores").hide();
        $("#titulo-tab-tipo-smartmeter-comparacion-periodos").hide();
        $("#titulo-tab-tipo-smartmeter-simulador-tarifas-principal").hide();
        $("#titulo-tab-tipo-smartmeter-simulador-tarifas-tarifas").hide();
        $("#titulo-tab-tipo-smartmeter-consumos-costes-tramos-electricidad").hide();
        $("#titulo-tab-tipo-smartmeter-cortes-tension-electricidad").hide();
        $("#titulo-tab-tipo-smartmeter-excesos-potencia-electricidad").hide();
        $("#titulo-tab-tipo-smartmeter-excesos-energia-reactiva-electricidad").hide();
        $("#titulo-tab-tipo-smartmeter-excesos-caudal-gas").hide();
        $("#titulo-tab-tipo-smartmeter-desvios-compra-energia").hide();
        $("#titulo-tab-tipo-smartmeter-desvios-ponderados-compra-energia").hide();
        $("#titulo-tab-tipo-smartmeter-simulador-factura-principal").hide();
        $("#titulo-tab-tipo-smartmeter-simulador-factura-reparto-costes").hide();
        $("#titulo-tab-tipo-smartmeter-instalacion").hide();
        $("#titulo-tab-tipo-proyectos-simulador-linea-base").hide();
        $("#titulo-tab-tipo-proyectos-informacion-proyecto").hide();

        var nombre_obligatorio = true;
        var mostrar_pestanya_periodo_tiempo = false;
        var mostrar_pestanya_duracion_separacion_periodos = false;
        var mostrar_pestanya_horario_semanal_fechas = false;
        var mostrar_horario_semanal = true;
        var mostrar_exclusion_fechas = true;
        var mostrar_inclusion_fechas = true;
        var mostrar_pestanya_elementos_informe = false;

        var tipo_elemento_plantilla_informe = $("#tipo_elemento_plantilla_informe").val();
        switch (tipo_elemento_plantilla_informe) {
            case TIPO_NINGUNO: {
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA: {
                nombre_obligatorio = false;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA: {
                nombre_obligatorio = false;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA: {
                $("#titulo-tab-tipo-portada").show();
                nombre_obligatorio = false;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO: {
                $("#titulo-tab-tipo-titulo").show();
                nombre_obligatorio = false;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO: {
                $("#titulo-tab-tipo-texto").show();
                nombre_obligatorio = false;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS: {
                $("#titulo-tab-tipo-notas").show();
                nombre_obligatorio = false;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                $("#titulo-tab-tipo-imagen").show();
                nombre_obligatorio = false;
                break;
            }
            // Elementos de varios módulos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS: {
                $("#titulo-tab-tipo-comentarios-principal").show();
                $("#titulo-tab-tipo-comentarios-sensores").show();
                $("#titulo-tab-tipo-comentarios-actuadores").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                break;
            }
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS: {
                $("#titulo-tab-tipo-sensores-activaciones-eventos-principal").show();
                $("#titulo-tab-tipo-sensores-activaciones-eventos-eventos").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION: {
                $("#titulo-tab-tipo-sensores-informacion").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO: {
                $("#titulo-tab-tipo-sensores-analisis-horario").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO: {
                $("#titulo-tab-tipo-sensores-analisis-diario").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO: {
                $("#titulo-tab-tipo-sensores-analisis-comportamiento-principal").show();
                $("#titulo-tab-tipo-sensores-analisis-comportamiento-sensores").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS: {
                $("#titulo-tab-tipo-sensores-comparacion-periodos").show();
                mostrar_pestanya_duracion_separacion_periodos = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_inclusion_fechas = false;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO: {
                $("#titulo-tab-tipo-sensores-comparacion-perfil-horario-principal").show();
                $("#titulo-tab-tipo-sensores-comparacion-perfil-horario-perfil-horario").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_inclusion_fechas = false;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES: {
                $("#titulo-tab-tipo-sensores-comparacion-campos-iguales-principal").show();
                $("#titulo-tab-tipo-sensores-comparacion-campos-iguales-sensores-secundarios").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES: {
                $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-principal").show();
                $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-1").show();
                $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-2").show();
                $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-3").show();
                $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-4").show();
                $("#titulo-tab-tipo-sensores-comparacion-campos-diferentes-sensor-5").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO: {
                $("#titulo-tab-tipo-sensores-analisis-comparativo-principal").show();
                $("#titulo-tab-tipo-sensores-analisis-comparativo-sensores").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES: {
                $("#titulo-tab-tipo-sensores-valores-generales-principal").show();
                $("#titulo-tab-tipo-sensores-valores-generales-campo-1").show();
                $("#titulo-tab-tipo-sensores-valores-generales-campo-2").show();
                $("#titulo-tab-tipo-sensores-valores-generales-campo-3").show();
                $("#titulo-tab-tipo-sensores-valores-generales-sensores").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES: {
                $("#titulo-tab-tipo-sensores-incrementos-totales-principal").show();
                $("#titulo-tab-tipo-sensores-incrementos-totales-campo-1").show();
                $("#titulo-tab-tipo-sensores-incrementos-totales-campo-2").show();
                $("#titulo-tab-tipo-sensores-incrementos-totales-campo-3").show();
                $("#titulo-tab-tipo-sensores-incrementos-totales-sensores").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA: {
                $("#titulo-tab-tipo-sensores-histograma").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION: {
                $("#titulo-tab-tipo-sensores-correlacion-principal").show();
                $("#titulo-tab-tipo-sensores-correlacion-sensor-independiente-1").show();
                $("#titulo-tab-tipo-sensores-correlacion-sensor-independiente-2").show();
                $("#titulo-tab-tipo-sensores-correlacion-sensor-independiente-3").show();
                $("#titulo-tab-tipo-sensores-correlacion-sensor-independiente-4").show();
                $("#titulo-tab-tipo-sensores-correlacion-sensor-dependiente").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS: {
                $("#titulo-tab-tipo-actuadores-informacion-acciones-enviadas-principal").show();
                $("#titulo-tab-tipo-actuadores-informacion-acciones-enviadas-sensor").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
                $("#titulo-tab-tipo-smartmeter-consumos-costes-generales-principal").show();
                $("#titulo-tab-tipo-smartmeter-consumos-costes-generales-sensores").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES: {
                $("#titulo-tab-tipo-smartmeter-consumos-costes-totales-principal").show();
                $("#titulo-tab-tipo-smartmeter-consumos-costes-totales-sensores").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS: {
                $("#titulo-tab-tipo-smartmeter-comparacion-periodos").show();
                mostrar_pestanya_duracion_separacion_periodos = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                mostrar_inclusion_fechas = false;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS: {
                $("#titulo-tab-tipo-smartmeter-simulador-tarifas-principal").show();
                $("#titulo-tab-tipo-smartmeter-simulador-tarifas-tarifas").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD: {
                $("#titulo-tab-tipo-smartmeter-consumos-costes-tramos-electricidad").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD: {
                $("#titulo-tab-tipo-smartmeter-cortes-tension-electricidad").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD: {
                $("#titulo-tab-tipo-smartmeter-excesos-potencia-electricidad").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD: {
                $("#titulo-tab-tipo-smartmeter-excesos-energia-reactiva-electricidad").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS: {
                $("#titulo-tab-tipo-smartmeter-excesos-caudal-gas").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            // Elementos de SmartMeter (Compra de energía)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA: {
                $("#titulo-tab-tipo-smartmeter-desvios-compra-energia").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA: {
                $("#titulo-tab-tipo-smartmeter-desvios-ponderados-compra-energia").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            // Elementos de SmartMeter (Facturas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA: {
                $("#titulo-tab-tipo-smartmeter-simulador-factura-principal").show();
                $("#titulo-tab-tipo-smartmeter-simulador-factura-reparto-costes").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            // Elementos de SmartMeter (Tarifas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION: {
                $("#titulo-tab-tipo-smartmeter-instalacion").show();
                break;
            }
            // Elementos de proyectos (Líneas base)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE: {
                $("#titulo-tab-tipo-proyectos-simulador-linea-base").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
            // Elementos de proyectos (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO: {
                $("#titulo-tab-tipo-proyectos-informacion-proyecto").show();
                mostrar_pestanya_periodo_tiempo = true;
                mostrar_pestanya_elementos_informe = true;
                break;
            }
        }

        // Nombre obligatorio
        if (nombre_obligatorio == true) {
            $("#nombre_elemento_plantilla_informe").addClass('TLNT_input_mandatory');
        }
        else {
            $("#nombre_elemento_plantilla_informe").removeClass('TLNT_input_mandatory');
        }

        // Pestaña de periodo de tiempo
        if (mostrar_pestanya_periodo_tiempo == true) {
            $("#titulo-tab-periodo-tiempo").show();
        }
        else {
            $("#titulo-tab-periodo-tiempo").hide();
        }

        // Pestaña de duración y separación de periodos
        if (mostrar_pestanya_duracion_separacion_periodos == true) {
            $("#titulo-tab-duracion-separacion-periodos").show();
        }
        else {
            $("#titulo-tab-duracion-separacion-periodos").hide();
        }

        // Pestaña de horario semanal y fechas
        if (mostrar_pestanya_horario_semanal_fechas == true) {
            $("#titulo-tab-horario-semanal-fechas").show();
            if (mostrar_horario_semanal == true) {
                $("#control_horario_semanal_elemento_plantilla_informe").show();
            }
            else {
                $("#control_horario_semanal_elemento_plantilla_informe").hide();
            }
            if (mostrar_exclusion_fechas == true) {
                $("#control_exclusion_fechas_elemento_plantilla_informe").show();
            }
            else {
                $("#control_exclusion_fechas_elemento_plantilla_informe").hide();
            }
            if (mostrar_inclusion_fechas == true) {
                $("#control_inclusion_fechas_elemento_plantilla_informe").show();
            }
            else {
                $("#control_inclusion_fechas_elemento_plantilla_informe").hide();
            }
        }
        else {
            $("#titulo-tab-horario-semanal-fechas").hide();
        }

        // Pestaña de elementos de informe
        if (mostrar_pestanya_elementos_informe == true) {
            $("#titulo-tab-elementos-informe").show();
        }
        else {
            $("#titulo-tab-elementos-informe").hide();
        }
    };
    $("#tipo_elemento_plantilla_informe").show(funcion_muestra_pestanyas_tipo_elemento_plantilla_informe);
    $("#tipo_elemento_plantilla_informe").change(funcion_muestra_pestanyas_tipo_elemento_plantilla_informe);

    // Recarga los elementos del informe al cambiar de tipo de elemento
    $("#tipo_elemento_plantilla_informe").change(funcion_recarga_elementos_informe_elemento_plantilla_informe);

    // Establecimiento de eventos de cada uno de los tipos de elementos de plantillas de informes
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_texto();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_imagen();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_comentarios();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_activaciones_eventos();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_informacion();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_analisis_horario();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_analisis_diario();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_analisis_comportamiento();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_comparacion_periodos();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_comparacion_perfil_horario();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_comparacion_campos_iguales();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_comparacion_campos_diferentes();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_analisis_comparativo();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_valores_generales();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_incrementos_totales();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_histograma();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_correlacion();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_actuadores_informacion_acciones_enviadas();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_consumos_costes_generales();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_consumos_costes_totales();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_comparacion_periodos();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_simulador_tarifas();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_cortes_tension_electricidad();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_excesos_potencia_electricidad();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_excesos_caudal_gas();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_desvios_compra_energia();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_desvios_ponderados_compra_energia();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_simulador_factura();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_instalacion();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_proyectos_simulador_linea_base();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_proyectos_informacion_proyecto();

    // Establecimiento de eventos de pestañas utilizadas en varios tipos de elementos de plantillas de informes
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_periodo_tiempo();
    establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_duracion_separacion_periodos();
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_texto = function() {
    // Desactivación de eventos anteriores
    $("#texto_elemento_plantilla_informe_texto").off();

    // Contador de caracteres de texto
    $("#texto_elemento_plantilla_informe_texto").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_imagen = function() {
    // - Selección de fichero de imagen
    $("#fichero_imagen_elemento_plantilla_informe_imagen_text").show(function() {
        $('#fichero_imagen_elemento_plantilla_informe_imagen_file').hide();
    });
    $('#fichero_imagen_elemento_plantilla_informe_imagen_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_imagen_elemento_plantilla_informe_imagen_text').val(fichero);
    });
    $('#boton_anyadir_modificar_elemento_plantilla_informe_imagen_seleccionar_fichero_imagen').click(function() {
        $('#fichero_imagen_elemento_plantilla_informe_imagen_file').click();
    });
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_comentarios = function() {
    $("#clase_sensor_elemento_plantilla_informe_comentarios").off();
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_comentarios").off();
    $("#clase_actuador_elemento_plantilla_informe_comentarios").off();
    $("#tipo_seleccion_actuadores_elemento_plantilla_informe_comentarios").off();
    $("#tipo_seleccion_grupos_actuadores_elemento_plantilla_informe_comentarios").off();

    // Mostrar listas dobles
    if ($('#select_sensores_elemento_plantilla_informe_comentarios_no_visible').length) {
        $('#select_sensores_elemento_plantilla_informe_comentarios_no_visible').attr("id", "select_sensores_elemento_plantilla_informe_comentarios_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_elemento_plantilla_informe_comentarios", true);
    }
    if ($('#select_actuadores_elemento_plantilla_informe_comentarios_no_visible').length) {
        $('#select_actuadores_elemento_plantilla_informe_comentarios_no_visible').attr("id", "select_actuadores_elemento_plantilla_informe_comentarios_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_actuadores_elemento_plantilla_informe_comentarios", true);
    }
    if ($('#select_grupos_actuadores_elemento_plantilla_informe_comentarios_no_visible').length) {
        $('#select_grupos_actuadores_elemento_plantilla_informe_comentarios_no_visible').attr("id", "select_grupos_actuadores_elemento_plantilla_informe_comentarios_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_grupos_actuadores_elemento_plantilla_informe_comentarios", true);
    }

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_lista_doble_sensores_elemento_plantilla_informe_tipo_comentarios = function() {
        funcion_recarga_lista_doble_sensores_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_comentarios",
            null,
            "tipo_seleccion_sensores_elemento_plantilla_informe_comentarios",
            "ids_sensores_elemento_plantilla_informe_comentarios");
    };
    $("#clase_sensor_elemento_plantilla_informe_comentarios").change(funcion_recarga_lista_doble_sensores_elemento_plantilla_informe_tipo_comentarios);
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_comentarios").change(funcion_recarga_lista_doble_sensores_elemento_plantilla_informe_tipo_comentarios);

    // Recarga los actuadores según los parámetros seleccionados
    var funcion_recarga_lista_doble_actuadores_elemento_plantilla_informe_tipo_comentarios = function() {
        funcion_recarga_lista_doble_actuadores_elemento_plantilla_informe(
            "clase_actuador_elemento_plantilla_informe_comentarios",
            "tipo_seleccion_actuadores_elemento_plantilla_informe_comentarios",
            "ids_actuadores_elemento_plantilla_informe_comentarios");
    };
    $("#clase_actuador_elemento_plantilla_informe_comentarios").change(funcion_recarga_lista_doble_actuadores_elemento_plantilla_informe_tipo_comentarios);
    $("#tipo_seleccion_actuadores_elemento_plantilla_informe_comentarios").change(funcion_recarga_lista_doble_actuadores_elemento_plantilla_informe_tipo_comentarios);

    // Recarga los grupos de actuadores según los parámetros seleccionados
    var funcion_recarga_lista_doble_grupos_actuadores_elemento_plantilla_informe_tipo_comentarios = function() {
        funcion_recarga_lista_doble_grupos_actuadores_elemento_plantilla_informe(
            "clase_actuador_elemento_plantilla_informe_comentarios",
            "tipo_seleccion_grupos_actuadores_elemento_plantilla_informe_comentarios",
            "ids_grupos_actuadores_elemento_plantilla_informe_comentarios");
    };
    $("#clase_actuador_elemento_plantilla_informe_comentarios").change(funcion_recarga_lista_doble_grupos_actuadores_elemento_plantilla_informe_tipo_comentarios);
    $("#tipo_seleccion_grupos_actuadores_elemento_plantilla_informe_comentarios").change(funcion_recarga_lista_doble_grupos_actuadores_elemento_plantilla_informe_tipo_comentarios);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_activaciones_eventos = function() {
    $("#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos").off();
    $("#origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").off();
    $("#id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").off();
    $("#tipo_seleccion_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").off();
    $("#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos").off();

    // Mostrar listas dobles
    if ($('#select_eventos_elemento_plantilla_informe_sensores_activaciones_eventos_no_visible').length) {
        $('#select_eventos_elemento_plantilla_informe_sensores_activaciones_eventos_no_visible').attr("id", "select_eventos_elemento_plantilla_informe_sensores_activaciones_eventos_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_eventos_elemento_plantilla_informe_sensores_activaciones_eventos", true);
    }

    // Habilitación de id de origen de evento
    var funcion_habilita_id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos= function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_ids_origen = $("select#id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos option").length;
        if (numero_ids_origen <= 1) {
            $("#id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").attr('disabled', true);
        }
        else {
            $("#id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").removeAttr('disabled');
        }
    };
    $("#id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").show(funcion_habilita_id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos);

    // Recarga los identificadores de orígenes de evento
    var funcion_recarga_ids_origenes_evento_elemento_plantilla_informe_sensores_activaciones_eventos = function() {
        var origen_evento = $("#origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").val();
        switch (origen_evento) {
            case ORIGEN_EVENTO_SENSOR: {
                var clase_sensor = $("#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos").val();
                if (clase_sensor == CLASE_TODAS) {
                    clase_sensor = CLASE_NINGUNA;
                }
                recuperando_lista_ids_origenes_evento_personal = true;
                funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
                    clase_sensor,
                    null,
                    null,
                    "tipo_seleccion_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos",
                    "id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos");
                break;
            }
            case ORIGEN_EVENTO_GRUPO_SENSORES: {
                var clase_sensor = $("#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos").val();
                if (clase_sensor == CLASE_TODAS) {
                    clase_sensor = CLASE_NINGUNA;
                }
                recuperando_lista_ids_origenes_evento_personal = true;
                funcion_recarga_grupos_sensores_clase_sensor_elemento_plantilla_informe(
                    clase_sensor,
                    null,
                    "tipo_seleccion_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos",
                    "id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos");
                break;
            }
        }
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_ids_origenes_evento_elemento_plantilla_informe_sensores_activaciones_eventos);
    $("#origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_ids_origenes_evento_elemento_plantilla_informe_sensores_activaciones_eventos);
    $("#tipo_seleccion_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_ids_origenes_evento_elemento_plantilla_informe_sensores_activaciones_eventos);

    // Recarga las granularidades de evento
    var funcion_recarga_granularidades_evento_elemento_plantilla_informe_sensores_activaciones_eventos = function() {
        var clase_sensor = $("#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos").val();

        recuperando_lista_granularidades_evento_personal = true;
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_granularidades_evento.php", {
            clase_sensor: clase_sensor,
            granularidad: GRANULARIDAD_TODAS,
            opciones_extra: OPCIONES_EXTRA_LISTA_GRANULARIDADES_EVENTO_TODAS
        },
        function (data, status) {
            recuperando_lista_granularidades_evento_personal = false;
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos").html(resultado.html);
            $("#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos").trigger("change");

            // Se deshabilita si sólo hay un valor para elegir
            var numero_ids = $("select#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos option").length;
            if (numero_ids <= 1) {
                $("#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos").attr('disabled', true);
            }
            else {
                $("#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos").removeAttr('disabled');
            }
        });
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_granularidades_evento_elemento_plantilla_informe_sensores_activaciones_eventos);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_activaciones_eventos = function() {
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_activaciones_eventos");
    };
    $("#campo_elemento_plantilla_informe_sensores_activaciones_eventos").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_activaciones_eventos);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_elemento_plantilla_informe_sensores_activaciones_eventos = function() {
        if ((recuperando_lista_ids_origenes_evento_personal == true) ||
            (recuperando_lista_granularidades_evento_personal == true)) {
            return;
        }

        var clase_sensor = $("#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos").val();
        var origen_evento = $("#origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").val();
        var id_sensor = ID_NINGUNO;
        if (origen_evento == ORIGEN_EVENTO_SENSOR) {
            id_sensor = $("#id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").val();
        }
        var granularidad_evento = $("#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos").val();
        var campo = $("#campo_elemento_plantilla_informe_sensores_activaciones_eventos").val();

        // Campo por defecto
        if (campo == CAMPO_NINGUNO) {
            switch (clase_sensor) {
                case CLASE_NINGUNA: {
                    break;
                }
                case CLASE_SENSOR_TEMPERATURA: {
                    campo = CAMPO_TEMPERATURA;
                    break;
                }
                case CLASE_SENSOR_HUMEDAD: {
                    campo = CAMPO_HUMEDAD;
                    break;
                }
                case CLASE_SENSOR_LUZ_INTERIOR: {
                    campo = CAMPO_ILUMINACION;
                    break;
                }
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_ENERGIA_REACTIVA: {
                    campo = CAMPO_INCREMENTO;
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION: {
                    campo = CAMPO_CORTES;
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA: {
                    campo = CAMPO_CONSUMO_ESTIMADO;
                    break;
                }
                case CLASE_SENSOR_GAS: {
                    campo = CAMPO_INCREMENTO;
                    break;
                }
                case CLASE_SENSOR_AGUA: {
                    campo = CAMPO_INCREMENTO;
                    break;
                }
                case CLASE_SENSOR_GENERICA: {
                    campo = CAMPO_VALOR;
                    break;
                }
            }
        }

        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_campos_sensor_activaciones_eventos.php", {
            clase_sensor: clase_sensor,
            id_sensor: id_sensor,
            granularidad: granularidad_evento,
            campo: campo
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#campo_elemento_plantilla_informe_sensores_activaciones_eventos").html(resultado.html);

            // Se habilita el campo de sensor
            funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_activaciones_eventos");
        });
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_campos_sensor_elemento_plantilla_informe_sensores_activaciones_eventos);
    $("#origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_campos_sensor_elemento_plantilla_informe_sensores_activaciones_eventos);
    $("#id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_campos_sensor_elemento_plantilla_informe_sensores_activaciones_eventos);
    $("#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_campos_sensor_elemento_plantilla_informe_sensores_activaciones_eventos);

    // Muestra los controles de eventos dependiendo del tipo de selección de origen de evento
    var funcion_muestra_controles_eventos_elemento_plantilla_informe_tipo_sensores_activaciones_eventos = function() {
        var tipo_seleccion_origen_evento = $("#tipo_seleccion_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").val();
        switch (tipo_seleccion_origen_evento) {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO: {
                $("#control_eventos_elemento_plantilla_informe_sensores_activaciones_eventos").show();
                $("#control_filtro_nombres_eventos_elemento_plantilla_informe_sensores_activaciones_eventos").hide();
                $("#filtro_nombres_eventos_elemento_plantilla_informe_sensores_activaciones_eventos").removeClass('TLNT_input_mandatory');
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE: {
                $("#control_eventos_elemento_plantilla_informe_sensores_activaciones_eventos").hide();
                $("#control_filtro_nombres_eventos_elemento_plantilla_informe_sensores_activaciones_eventos").show();
                $("#filtro_nombres_eventos_elemento_plantilla_informe_sensores_activaciones_eventos").addClass('TLNT_input_mandatory');
                break;
            }
        }
    };
    $("#tipo_seleccion_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").show(funcion_muestra_controles_eventos_elemento_plantilla_informe_tipo_sensores_activaciones_eventos);
    $("#tipo_seleccion_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_muestra_controles_eventos_elemento_plantilla_informe_tipo_sensores_activaciones_eventos);

    // Recarga de la lista doble de eventos de un elemento de plantilla de informe
    var funcion_recarga_lista_doble_eventos_informe_elemento_plantilla_informe_sensores_activaciones_eventos = function() {
        if ((recuperando_lista_ids_origenes_evento_personal == true) ||
            (recuperando_lista_granularidades_evento_personal == true)) {
            return;
        }

        var ids_eventos = [];
        $("#ids_eventos_elemento_plantilla_informe_sensores_activaciones_eventos option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_eventos.push($(this).val());
            }
        });

        // Si el tipo de selección de origen de evento es configurable, no se carga ningún evento en la lista doble (se ocultará)
        var clase_sensor = $("#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos").val();
        var tipo_seleccion_origen_evento = $("#tipo_seleccion_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").val();
        if (tipo_seleccion_origen_evento == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE) {
            return;
        }

        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_eventos.php", {
            clase_sensor: clase_sensor,
            origen: $("#origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").val(),
            id_origen: $("#id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").val(),
            granularidad: $("#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos").val(),
            ids_eventos: ids_eventos
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Recarga de lista doble
            // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
            $("#ids_eventos_elemento_plantilla_informe_sensores_activaciones_eventos").multiselect2side('destroy');
            $("#ids_eventos_elemento_plantilla_informe_sensores_activaciones_eventos").html(resultado.html);
            TLNT.Navegacion.convierte_lista_doble("ids_eventos_elemento_plantilla_informe_sensores_activaciones_eventos", true);
        });
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_lista_doble_eventos_informe_elemento_plantilla_informe_sensores_activaciones_eventos);
    $("#origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_lista_doble_eventos_informe_elemento_plantilla_informe_sensores_activaciones_eventos);
    $("#id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_lista_doble_eventos_informe_elemento_plantilla_informe_sensores_activaciones_eventos);
    $("#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos").change(funcion_recarga_lista_doble_eventos_informe_elemento_plantilla_informe_sensores_activaciones_eventos);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_informacion = function() {
    $("#clase_sensor_elemento_plantilla_informe_sensores_informacion").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_informacion").off();
    $("#id_sensor_elemento_plantilla_informe_sensores_informacion").off();
    $("#campo_elemento_plantilla_informe_sensores_informacion").off();
    $("#intervalo_valores_elemento_plantilla_informe_sensores_informacion").off();

    // Muestra el control de ratio
    var funcion_muestra_control_ratio_elemento_plantilla_informe_sensores_informacion = function() {
        var id_controles = "elemento_plantilla_informe_sensores_informacion";
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

    // Muestra el control de intervalo de valores
    var funcion_muestra_control_intervalo_valores_elemento_plantilla_informe_sensores_informacion = function() {
        var id_controles = "elemento_plantilla_informe_sensores_informacion";
        var clase_sensor = $("#clase_sensor_" + id_controles).val();
        switch (clase_sensor) {
            case CLASE_NINGUNA: {
                $("#control_intervalo_valores_" + id_controles).hide();
                break;
            }
            default: {
                $("#control_intervalo_valores_" + id_controles).show();
                break;
            }
        }
    };

    // Muestra el control de tipo de mapa de calor
    var funcion_muestra_control_tipo_mapa_calor_elemento_plantilla_informe_sensores_informacion = function() {
        var id_controles = "elemento_plantilla_informe_sensores_informacion";
        var clase_sensor = $("#clase_sensor_" + id_controles).val();
        switch (clase_sensor) {
            case CLASE_NINGUNA: {
                $("#control_tipo_mapa_calor_" + id_controles).hide();
                break;
            }
            default: {
                $("#control_tipo_mapa_calor_" + id_controles).show();
                break;
            }
        }
    };

    // Habilita y muestra los controles dependientes de la clase de sensor
    // - Nota: Si dentro de una función (A), se llama a otra función (B), está última tiene que definirse antes (B antes que A)
    var funcion_muestra_controles_elemento_plantilla_informe_sensores_informacion = function() {
        funcion_muestra_control_ratio_elemento_plantilla_informe_sensores_informacion();
        funcion_muestra_control_intervalo_valores_elemento_plantilla_informe_sensores_informacion();
        funcion_muestra_control_tipo_mapa_calor_elemento_plantilla_informe_sensores_informacion();
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_informacion").show(funcion_muestra_controles_elemento_plantilla_informe_sensores_informacion);
    $("#clase_sensor_elemento_plantilla_informe_sensores_informacion").change(funcion_muestra_controles_elemento_plantilla_informe_sensores_informacion);

    // Recarga los elementos del informe al cambiar de clase
    $("#clase_sensor_elemento_plantilla_informe_sensores_informacion").change(funcion_recarga_elementos_informe_elemento_plantilla_informe);

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_informacion = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_sensores_informacion");
    };
    $("#id_sensor_elemento_plantilla_informe_sensores_informacion").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_informacion);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_informacion = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_elemento_plantilla_informe_sensores_informacion",
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_sensores_informacion",
            "id_sensor_elemento_plantilla_informe_sensores_informacion");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_informacion").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_informacion);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_informacion").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_informacion);

    // Muestra u oculta el control de campo de sensor según la clase
    var funcion_muestra_oculta_campo_elemento_plantilla_informe_tipo_sensores_informacion = function() {
        var id_controles = "elemento_plantilla_informe_sensores_informacion";
        var clase_sensor = $("#clase_sensor_" + id_controles).val();
        switch (clase_sensor) {
            case CLASE_SENSOR_TEMPERATURA:
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_GAS:
            case CLASE_SENSOR_AGUA:
            case CLASE_SENSOR_GENERICA: {
                $("#control_campo_" + id_controles).show();
                break;
            }
            default: {
                $("#control_campo_" + id_controles).hide();
                break;
            }
        }
    };

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_informacion = function() {
        funcion_muestra_oculta_campo_elemento_plantilla_informe_tipo_sensores_informacion();
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_informacion");
    };
    $("#campo_elemento_plantilla_informe_sensores_informacion").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_informacion);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_informacion = function() {
        funcion_muestra_oculta_campo_elemento_plantilla_informe_tipo_sensores_informacion();
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_elemento_plantilla_informe_sensores_informacion",
            null,
            "campo_elemento_plantilla_informe_sensores_informacion");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_informacion").change(funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_informacion);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_informacion = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("elemento_plantilla_informe_sensores_informacion");
    };
    $("#campo_elemento_plantilla_informe_sensores_informacion").show(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_informacion);
    $("#campo_elemento_plantilla_informe_sensores_informacion").change(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_informacion);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_informacion = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_informacion",
            null,
            "campo_elemento_plantilla_informe_sensores_informacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_informacion");
    };
    $("#campo_elemento_plantilla_informe_sensores_informacion").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_informacion);

    // Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
    var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_informacion = function() {
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_informacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_informacion",
            "campo_elemento_plantilla_informe_sensores_informacion");
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_informacion").change(funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_informacion);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_analisis_horario = function() {
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_horario").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_analisis_horario").off();
    $("#id_sensor_elemento_plantilla_informe_sensores_analisis_horario").off();
    $("#campo_elemento_plantilla_informe_sensores_analisis_horario").off();

    // Habilita el control sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_analisis_horario = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_sensores_analisis_horario");
    };
    $("#id_sensor_elemento_plantilla_informe_sensores_analisis_horario").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_analisis_horario);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_horario = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_elemento_plantilla_informe_sensores_analisis_horario",
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_sensores_analisis_horario",
            "id_sensor_elemento_plantilla_informe_sensores_analisis_horario");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_horario").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_horario);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_analisis_horario").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_horario);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_analisis_horario = function() {
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_analisis_horario");
    };
    $("#campo_elemento_plantilla_informe_sensores_analisis_horario").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_analisis_horario);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_analisis_horario = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_elemento_plantilla_informe_sensores_analisis_horario",
            null,
            "campo_elemento_plantilla_informe_sensores_analisis_horario");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_horario").change(funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_analisis_horario);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_horario = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("elemento_plantilla_informe_sensores_analisis_horario");
    };
    $("#campo_elemento_plantilla_informe_sensores_analisis_horario").show(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_horario);
    $("#campo_elemento_plantilla_informe_sensores_analisis_horario").change(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_horario);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_analisis_diario = function() {
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_diario").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_analisis_diario").off();
    $("#id_sensor_elemento_plantilla_informe_sensores_analisis_diario").off();
    $("#campo_elemento_plantilla_informe_sensores_analisis_diario").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_analisis_diario = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_sensores_analisis_diario");
    };
    $("#id_sensor_elemento_plantilla_informe_sensores_analisis_diario").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_analisis_diario);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_diario = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_elemento_plantilla_informe_sensores_analisis_diario",
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_sensores_analisis_diario",
            "id_sensor_elemento_plantilla_informe_sensores_analisis_diario");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_diario").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_diario);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_analisis_diario").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_diario);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_analisis_diario = function() {
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_analisis_diario");
    };
    $("#campo_elemento_plantilla_informe_sensores_analisis_diario").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_analisis_diario);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_analisis_diario = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_elemento_plantilla_informe_sensores_analisis_diario",
            null,
            "campo_elemento_plantilla_informe_sensores_analisis_diario");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_diario").change(funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_analisis_diario);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_diario = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("elemento_plantilla_informe_sensores_analisis_diario");
    };
    $("#campo_elemento_plantilla_informe_sensores_analisis_diario").show(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_diario);
    $("#campo_elemento_plantilla_informe_sensores_analisis_diario").change(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_diario);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_analisis_comportamiento = function() {
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_comportamiento").off();
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento").off();
    $("#campo_elemento_plantilla_informe_sensores_analisis_comportamiento").off();

    // Mostrar listas dobles
    if ($('#select_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento_no_visible').length) {
        $('#select_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento_no_visible').attr("id", "select_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento", true);
    }

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento = function() {
        funcion_recarga_lista_doble_sensores_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_analisis_comportamiento",
            null,
            "tipo_seleccion_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento",
            "ids_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_comportamiento").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento);
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento = function() {
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_analisis_comportamiento");
    };
    $("#campo_elemento_plantilla_informe_sensores_analisis_comportamiento").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_analisis_comportamiento = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_elemento_plantilla_informe_sensores_analisis_comportamiento",
            null,
            "campo_elemento_plantilla_informe_sensores_analisis_comportamiento");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_comportamiento").change(funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_analisis_comportamiento);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("elemento_plantilla_informe_sensores_analisis_comportamiento");
    };
    $("#campo_elemento_plantilla_informe_sensores_analisis_comportamiento").show(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento);
    $("#campo_elemento_plantilla_informe_sensores_analisis_comportamiento").change(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_comparacion_periodos = function() {
    $("#clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_comparacion_periodos").off();
    $("#id_sensor_elemento_plantilla_informe_sensores_comparacion_periodos").off();
    $("#campo_elemento_plantilla_informe_sensores_comparacion_periodos").off();
    $("#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_periodos = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_sensores_comparacion_periodos");
    };
    $("#id_sensor_elemento_plantilla_informe_sensores_comparacion_periodos").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_periodos);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_periodos = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos",
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_sensores_comparacion_periodos",
            "id_sensor_elemento_plantilla_informe_sensores_comparacion_periodos");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_periodos);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_comparacion_periodos").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_periodos);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_comparacion_periodos = function() {
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_comparacion_periodos");
    };
    $("#campo_elemento_plantilla_informe_sensores_comparacion_periodos").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_comparacion_periodos);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos",
            "campo_elemento_plantilla_informe_sensores_comparacion_periodos");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos").change(funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_comparacion_periodos = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("elemento_plantilla_informe_sensores_comparacion_periodos");
    };
    $("#campo_elemento_plantilla_informe_sensores_comparacion_periodos").show(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_comparacion_periodos);
    $("#campo_elemento_plantilla_informe_sensores_comparacion_periodos").change(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_comparacion_periodos);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_comparacion_periodos = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos",
            null,
            "campo_elemento_plantilla_informe_sensores_comparacion_periodos",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos");
    };
    $("#campo_elemento_plantilla_informe_sensores_comparacion_periodos").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_comparacion_periodos);

    // Habilitación de la lista de intervalos de valores
    var funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_comparacion_periodos = function() {
        funcion_habilita_intervalo_valores_elemento_plantilla_informe("intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos", true);
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos").show(funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_comparacion_periodos);

    // Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
    var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos = function() {
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos",
            "campo_elemento_plantilla_informe_sensores_comparacion_periodos");
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos").change(funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_comparacion_perfil_horario = function() {
    $("#clase_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario").off();
    $("#id_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario").off();
    $("#campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario").off();
    $("#tipo_perfil_horario_elemento_plantilla_informe_sensores_comparacion_perfil_horario").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario");
    };
    $("#id_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario",
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario",
            "id_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario = function() {
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_comparacion_perfil_horario");
    };
    $("#campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_perfil_horario",
            "campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario").change(funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("elemento_plantilla_informe_sensores_comparacion_perfil_horario");
    };
    $("#campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario").show(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario);
    $("#campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario").change(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario);

    // Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
    var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_comparacion_perfil_horario = function() {
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_perfil_horario",
            "campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario");
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_perfil_horario").change(funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_comparacion_perfil_horario);

    // Habilitación de agrupaciones de días en comparación con perfil horario
    var funcion_habilita_agrupaciones_dias_elemento_plantilla_informe_sensores_comparacion_perfil_horario = function() {
        var tipo_perfil_horario = $("#tipo_perfil_horario_elemento_plantilla_informe_sensores_comparacion_perfil_horario").val();
        switch (tipo_perfil_horario) {
            case TIPO_PERFIL_HORARIO_CONFIGURABLE: {
                if ($("#cadena_agrupaciones_dias_semana_elemento_plantilla_informe_sensores_comparacion_perfil_horario").val() == "") {
                    $("#cadena_agrupaciones_dias_semana_elemento_plantilla_informe_sensores_comparacion_perfil_horario").val("1-2-3-4-5, 6-7");
                }
                $("#control_agrupaciones_dias_semana_elemento_plantilla_informe_sensores_comparacion_perfil_horario").show();
                break;
            }
            default: {
                $("#cadena_agrupaciones_dias_semana_elemento_plantilla_informe_sensores_comparacion_perfil_horario").val("");
                $("#control_agrupaciones_dias_semana_elemento_plantilla_informe_sensores_comparacion_perfil_horario").hide();
                break;
            }
        }
    };
    $("#tipo_perfil_horario_elemento_plantilla_informe_sensores_comparacion_perfil_horario").show(funcion_habilita_agrupaciones_dias_elemento_plantilla_informe_sensores_comparacion_perfil_horario);
    $("#tipo_perfil_horario_elemento_plantilla_informe_sensores_comparacion_perfil_horario").change(funcion_habilita_agrupaciones_dias_elemento_plantilla_informe_sensores_comparacion_perfil_horario);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_comparacion_campos_iguales = function() {
    $("#clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales").off();
    $("#tipo_seleccion_sensor_principal_elemento_plantilla_informe_sensores_comparacion_campos_iguales").off();
    $("#campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales").off();
    $("#tipo_seleccion_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales").off();
    $("#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales").off();

    // Mostrar listas dobles
    if ($('#select_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales_no_visible').length) {
        $('#select_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales_no_visible').attr("id", "select_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales", true);
    }

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_principal_elemento_plantilla_informe_sensores_comparacion_campos_iguales");
    };
    $("#id_sensor_principal_elemento_plantilla_informe_sensores_comparacion_campos_iguales").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales",
            null,
            "tipo_seleccion_sensor_principal_elemento_plantilla_informe_sensores_comparacion_campos_iguales",
            "id_sensor_principal_elemento_plantilla_informe_sensores_comparacion_campos_iguales");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales);
    $("#tipo_seleccion_sensor_principal_elemento_plantilla_informe_sensores_comparacion_campos_iguales").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_lista_doble_sensores_secundarios_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales = function() {
        funcion_recarga_lista_doble_sensores_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales",
            null,
            "tipo_seleccion_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales",
            "ids_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales").change(funcion_recarga_lista_doble_sensores_secundarios_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales);
    $("#tipo_seleccion_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales").change(funcion_recarga_lista_doble_sensores_secundarios_clase_sensor_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales = function() {
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_comparacion_campos_iguales");
    };
    $("#campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales",
            "campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales").change(funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("elemento_plantilla_informe_sensores_comparacion_campos_iguales");
    };
    $("#campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales").show(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales);
    $("#campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales").change(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales);

    // Habilitación de la lista de intervalos de valores
    var funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales = function() {
        funcion_habilita_intervalo_valores_elemento_plantilla_informe("intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales", true);
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales").show(funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales",
            null,
            "campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales");
    };
    $("#campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales);

    // Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
    var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales = function() {
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales",
            "campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales");
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales").change(funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_comparacion_campos_diferentes = function() {
    $("#clase_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#clase_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#clase_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#clase_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#clase_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#tipo_seleccion_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#tipo_seleccion_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#tipo_seleccion_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#tipo_seleccion_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#tipo_seleccion_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#id_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#id_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#id_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#id_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#id_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#campo_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#campo_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#campo_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#campo_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#campo_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();
    $("#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_1_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#id_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_habilita_sensor_1_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_habilita_sensor_2_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#id_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_habilita_sensor_2_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_habilita_sensor_3_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#id_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_habilita_sensor_3_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_habilita_sensor_4_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#id_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_habilita_sensor_4_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_habilita_sensor_5_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#id_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_habilita_sensor_5_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_1_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            null,
            "tipo_seleccion_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "id_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#clase_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_clase_sensor_1_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    $("#tipo_seleccion_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_clase_sensor_1_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_recarga_sensores_clase_sensor_2_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            null,
            "tipo_seleccion_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "id_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#clase_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_clase_sensor_2_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    $("#tipo_seleccion_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_clase_sensor_2_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_recarga_sensores_clase_sensor_3_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            null,
            "tipo_seleccion_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "id_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#clase_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_clase_sensor_3_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    $("#tipo_seleccion_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_clase_sensor_3_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_recarga_sensores_clase_sensor_4_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            null,
            "tipo_seleccion_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "id_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#clase_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_clase_sensor_4_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    $("#tipo_seleccion_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_clase_sensor_4_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_recarga_sensores_clase_sensor_5_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            null,
            "tipo_seleccion_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "id_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#clase_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_clase_sensor_5_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    $("#tipo_seleccion_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_clase_sensor_5_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_1_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_habilita_campo_elemento_plantilla_informe("1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#campo_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_habilita_campo_1_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_habilita_campo_2_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_habilita_campo_elemento_plantilla_informe("2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#campo_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_habilita_campo_2_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_habilita_campo_3_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_habilita_campo_elemento_plantilla_informe("3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#campo_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_habilita_campo_3_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_habilita_campo_4_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_habilita_campo_elemento_plantilla_informe("4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#campo_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_habilita_campo_4_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_habilita_campo_5_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_habilita_campo_elemento_plantilla_informe("5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#campo_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_habilita_campo_5_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "campo_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#clase_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_campos_sensor_clase_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes);
    var funcion_recarga_campos_sensor_clase_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "campo_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#clase_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_campos_sensor_clase_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes);
    var funcion_recarga_campos_sensor_clase_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "campo_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#clase_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_campos_sensor_clase_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes);
    var funcion_recarga_campos_sensor_clase_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "campo_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#clase_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_campos_sensor_clase_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes);
    var funcion_recarga_campos_sensor_clase_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "campo_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#clase_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_campos_sensor_clase_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_1_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#campo_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_muestra_control_parametros_extra_campo_1_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    $("#campo_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_muestra_control_parametros_extra_campo_1_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_muestra_control_parametros_extra_campo_2_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#campo_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_muestra_control_parametros_extra_campo_2_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    $("#campo_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_muestra_control_parametros_extra_campo_2_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_muestra_control_parametros_extra_campo_3_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#campo_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_muestra_control_parametros_extra_campo_3_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    $("#campo_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_muestra_control_parametros_extra_campo_3_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_muestra_control_parametros_extra_campo_4_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#campo_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_muestra_control_parametros_extra_campo_4_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    $("#campo_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_muestra_control_parametros_extra_campo_4_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    var funcion_muestra_control_parametros_extra_campo_5_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#campo_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").show(funcion_muestra_control_parametros_extra_campo_5_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);
    $("#campo_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_muestra_control_parametros_extra_campo_5_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes);

    // Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
    var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes = function() {
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "campo_1_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "campo_2_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "campo_3_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "campo_4_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes",
            "campo_5_elemento_plantilla_informe_sensores_comparacion_campos_diferentes");
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes").change(funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_analisis_comparativo = function() {
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo").off();
    $("#campo_elemento_plantilla_informe_sensores_analisis_comparativo").off();
    $("#tipo_seleccion_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo").off();
    $("#tipo_seleccion_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo").off();
    $("#intervalo_valores_elemento_plantilla_informe_sensores_analisis_comparativo").off();
    $("#id_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo").off();

    // Mostrar listas dobles
    if ($('#select_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo_no_visible').length) {
        $('#select_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo_no_visible').attr("id", "select_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo", true);
    }

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_analisis_comparativo = function() {
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_analisis_comparativo");
    };
    $("#campo_elemento_plantilla_informe_sensores_analisis_comparativo").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_analisis_comparativo);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo",
            "intervalo_valores_elemento_plantilla_informe_sensores_analisis_comparativo",
            "campo_elemento_plantilla_informe_sensores_analisis_comparativo");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo").change(funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_comparativo = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("elemento_plantilla_informe_sensores_analisis_comparativo");
    };
    $("#campo_elemento_plantilla_informe_sensores_analisis_comparativo").show(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_comparativo);
    $("#campo_elemento_plantilla_informe_sensores_analisis_comparativo").change(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_analisis_comparativo);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_lista_doble_sensores_agregados_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comparativo = function() {
        funcion_recarga_lista_doble_sensores_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo",
            null,
            "tipo_seleccion_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo",
            "ids_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo").change(funcion_recarga_lista_doble_sensores_agregados_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comparativo);
    $("#tipo_seleccion_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo").change(funcion_recarga_lista_doble_sensores_agregados_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comparativo);

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comparativo = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo");
    };
    $("#id_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comparativo);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comparativo = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo",
            null,
            "tipo_seleccion_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo",
            "id_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comparativo);
    $("#tipo_seleccion_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_analisis_comparativo);

    // Habilitación de la lista de intervalos de valores
    var funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_analisis_comparativo = function() {
        funcion_habilita_intervalo_valores_elemento_plantilla_informe("intervalo_valores_elemento_plantilla_informe_sensores_analisis_comparativo", true);
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_analisis_comparativo").show(funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_analisis_comparativo);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_analisis_comparativo = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo",
            null,
            "campo_elemento_plantilla_informe_sensores_analisis_comparativo",
            "intervalo_valores_elemento_plantilla_informe_sensores_analisis_comparativo");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_analisis_comparativo);

    // Habilitación del selector de tipo de mapa de calor del elemento de plantilla de informe
    var funcion_habilita_tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_comparativo = function() {
        var id_sensor = $("#id_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo").val();
        if (id_sensor == ID_NINGUNO) {
            $("#tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_comparativo").val(TIPO_MAPA_CALOR_NINGUNO);
            $("#tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_comparativo").prop('disabled', 'disabled');
        }
        else {
            $("#tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_comparativo").prop('disabled', false);
        }
    };
    $("#id_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo").show(funcion_habilita_tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_comparativo);
    $("#id_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo").change(funcion_habilita_tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_comparativo);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_valores_generales = function() {
    $("#clase_sensor_1_elemento_plantilla_informe_sensores_valores_generales").off();
    $("#clase_sensor_2_elemento_plantilla_informe_sensores_valores_generales").off();
    $("#clase_sensor_3_elemento_plantilla_informe_sensores_valores_generales").off();
    $("#campo_1_elemento_plantilla_informe_sensores_valores_generales").off();
    $("#campo_2_elemento_plantilla_informe_sensores_valores_generales").off();
    $("#campo_3_elemento_plantilla_informe_sensores_valores_generales").off();
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_sensores_valores_generales").off();
    $("#intervalo_valores_elemento_plantilla_informe_sensores_valores_generales").off();
    $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").off();

    // Mostrar listas dobles
    if ($('#select_sensores_elemento_plantilla_informe_sensores_valores_generales_no_visible').length) {
        $('#select_sensores_elemento_plantilla_informe_sensores_valores_generales_no_visible').attr("id", "select_sensores_elemento_plantilla_informe_sensores_valores_generales_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_elemento_plantilla_informe_sensores_valores_generales", true);
    }

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_valores_generales_clase_modificada = function() {
        var ids_listas_clases_sensor = [];
        ids_listas_clases_sensor.push("clase_sensor_1_elemento_plantilla_informe_sensores_valores_generales");
        ids_listas_clases_sensor.push("clase_sensor_2_elemento_plantilla_informe_sensores_valores_generales");
        ids_listas_clases_sensor.push("clase_sensor_3_elemento_plantilla_informe_sensores_valores_generales");
        funcion_recarga_lista_doble_sensores_clases_elemento_plantilla_informe(
            ids_listas_clases_sensor,
            "tipo_seleccion_sensores_elemento_plantilla_informe_sensores_valores_generales",
            "ids_sensores_elemento_plantilla_informe_sensores_valores_generales",
            true);
    };
    $("#clase_sensor_1_elemento_plantilla_informe_sensores_valores_generales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_valores_generales_clase_modificada);
    $("#clase_sensor_2_elemento_plantilla_informe_sensores_valores_generales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_valores_generales_clase_modificada);
    $("#clase_sensor_3_elemento_plantilla_informe_sensores_valores_generales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_valores_generales_clase_modificada);

    var funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_valores_generales_tipo_seleccion_sensores_modificado = function() {
        var ids_listas_clases_sensor = [];
        ids_listas_clases_sensor.push("clase_sensor_1_elemento_plantilla_informe_sensores_valores_generales");
        ids_listas_clases_sensor.push("clase_sensor_2_elemento_plantilla_informe_sensores_valores_generales");
        ids_listas_clases_sensor.push("clase_sensor_3_elemento_plantilla_informe_sensores_valores_generales");
        funcion_recarga_lista_doble_sensores_clases_elemento_plantilla_informe(
            ids_listas_clases_sensor,
            "tipo_seleccion_sensores_elemento_plantilla_informe_sensores_valores_generales",
            "ids_sensores_elemento_plantilla_informe_sensores_valores_generales",
            false);
    };
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_sensores_valores_generales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_valores_generales_tipo_seleccion_sensores_modificado);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_1_elemento_plantilla_informe_tipo_sensores_valores_generales = function() {
        funcion_habilita_campo_elemento_plantilla_informe("1_elemento_plantilla_informe_sensores_valores_generales");
    };
    $("#campo_1_elemento_plantilla_informe_sensores_valores_generales").show(funcion_habilita_campo_1_elemento_plantilla_informe_tipo_sensores_valores_generales);
    var funcion_habilita_campo_2_elemento_plantilla_informe_tipo_sensores_valores_generales = function() {
        funcion_habilita_campo_elemento_plantilla_informe("2_elemento_plantilla_informe_sensores_valores_generales");
    };
    $("#campo_2_elemento_plantilla_informe_sensores_valores_generales").show(funcion_habilita_campo_2_elemento_plantilla_informe_tipo_sensores_valores_generales);
    var funcion_habilita_campo_3_elemento_plantilla_informe_tipo_sensores_valores_generales = function() {
        funcion_habilita_campo_elemento_plantilla_informe("3_elemento_plantilla_informe_sensores_valores_generales");
    };
    $("#campo_3_elemento_plantilla_informe_sensores_valores_generales").show(funcion_habilita_campo_3_elemento_plantilla_informe_tipo_sensores_valores_generales);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_valores_generales = function(numero_campo) {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_" + numero_campo + "_elemento_plantilla_informe_sensores_valores_generales",
            "intervalo_valores_elemento_plantilla_informe_sensores_valores_generales",
            "campo_" + numero_campo + "_elemento_plantilla_informe_sensores_valores_generales");
    };
    var funcion_recarga_campos_sensor_clase_sensor_1_elemento_plantilla_informe_sensores_valores_generales = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_valores_generales(1);
    };
    $("#clase_sensor_1_elemento_plantilla_informe_sensores_valores_generales").change(funcion_recarga_campos_sensor_clase_sensor_1_elemento_plantilla_informe_sensores_valores_generales);
    var funcion_recarga_campos_sensor_clase_sensor_2_elemento_plantilla_informe_sensores_valores_generales = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_valores_generales(2);
    };
    $("#clase_sensor_2_elemento_plantilla_informe_sensores_valores_generales").change(funcion_recarga_campos_sensor_clase_sensor_2_elemento_plantilla_informe_sensores_valores_generales);
    var funcion_recarga_campos_sensor_clase_sensor_3_elemento_plantilla_informe_sensores_valores_generales = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_valores_generales(3);
    };
    $("#clase_sensor_3_elemento_plantilla_informe_sensores_valores_generales").change(funcion_recarga_campos_sensor_clase_sensor_3_elemento_plantilla_informe_sensores_valores_generales);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_1_elemento_plantilla_informe_tipo_sensores_valores_generales = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("1_elemento_plantilla_informe_sensores_valores_generales");
    };
    $("#campo_1_elemento_plantilla_informe_sensores_valores_generales").show(funcion_muestra_control_parametros_extra_campo_1_elemento_plantilla_informe_tipo_sensores_valores_generales);
    $("#campo_1_elemento_plantilla_informe_sensores_valores_generales").change(funcion_muestra_control_parametros_extra_campo_1_elemento_plantilla_informe_tipo_sensores_valores_generales);
    var funcion_muestra_control_parametros_extra_campo_2_elemento_plantilla_informe_tipo_sensores_valores_generales = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("2_elemento_plantilla_informe_sensores_valores_generales");
    };
    $("#campo_2_elemento_plantilla_informe_sensores_valores_generales").show(funcion_muestra_control_parametros_extra_campo_2_elemento_plantilla_informe_tipo_sensores_valores_generales);
    $("#campo_2_elemento_plantilla_informe_sensores_valores_generales").change(funcion_muestra_control_parametros_extra_campo_2_elemento_plantilla_informe_tipo_sensores_valores_generales);
    var funcion_muestra_control_parametros_extra_campo_3_elemento_plantilla_informe_tipo_sensores_valores_generales = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("3_elemento_plantilla_informe_sensores_valores_generales");
    };
    $("#campo_3_elemento_plantilla_informe_sensores_valores_generales").show(funcion_muestra_control_parametros_extra_campo_3_elemento_plantilla_informe_tipo_sensores_valores_generales);
    $("#campo_3_elemento_plantilla_informe_sensores_valores_generales").change(funcion_muestra_control_parametros_extra_campo_3_elemento_plantilla_informe_tipo_sensores_valores_generales);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_valores_generales = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            "clase_sensor_1_elemento_plantilla_informe_sensores_valores_generales",
            null,
            "campo_1_elemento_plantilla_informe_sensores_valores_generales",
            "intervalo_valores_elemento_plantilla_informe_sensores_valores_generales");
    };
    $("#campo_1_elemento_plantilla_informe_sensores_valores_generales").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_valores_generales);

    // Habilitación del selector de agregación de comparación de valores generales
    var funcion_habilita_agregacion_elemento_plantilla_informe_sensores_valores_generales = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_tipos = $("select#agregacion_elemento_plantilla_informe_sensores_valores_generales option").length;
        if (numero_tipos <= 1) {
            $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").attr('disabled', true);
        }
        else {
            $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").removeAttr('disabled');
        }
    };
    $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").show(funcion_habilita_agregacion_elemento_plantilla_informe_sensores_valores_generales);
    $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").change(funcion_habilita_agregacion_elemento_plantilla_informe_sensores_valores_generales);

    // Recarga las agregaciones según la clase y el campo en el elemento de plantilla de informe
    var funcion_recarga_agregaciones_elemento_plantilla_informe_sensores_valores_generales = function() {
        var clase_sensor = $("#clase_sensor_1_elemento_plantilla_informe_sensores_valores_generales").val();
        var campo = $("#campo_1_elemento_plantilla_informe_sensores_valores_generales").val();
        var intervalo_valores = $("#intervalo_valores_elemento_plantilla_informe_sensores_valores_generales").val();
        var agregacion = $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
                $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").val(AGREGACION_NINGUNA);
                $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").attr('disabled', true);
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
                    $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").html(resultado.html);
                    $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").trigger("change");
                });
                break;
            }
        }
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_valores_generales").change(funcion_recarga_agregaciones_elemento_plantilla_informe_sensores_valores_generales);

    // Habilitación de la lista de intervalos de valores
    var funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_valores_generales = function() {
        var intervalo_valores = $("#intervalo_valores_elemento_plantilla_informe_sensores_valores_generales").val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_NINGUNO:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
                $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").val(AGREGACION_NINGUNA);
                $("#agregacion_elemento_plantilla_informe_sensores_valores_generales").attr('disabled', true);
                break;
            }
        }
        funcion_habilita_intervalo_valores_elemento_plantilla_informe("intervalo_valores_elemento_plantilla_informe_sensores_valores_generales", true);
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_valores_generales").show(funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_valores_generales);

    // Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
    var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_valores_generales = function() {
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_1_elemento_plantilla_informe_sensores_valores_generales",
            "intervalo_valores_elemento_plantilla_informe_sensores_valores_generales",
            "campo_1_elemento_plantilla_informe_sensores_valores_generales");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_2_elemento_plantilla_informe_sensores_valores_generales",
            "intervalo_valores_elemento_plantilla_informe_sensores_valores_generales",
            "campo_2_elemento_plantilla_informe_sensores_valores_generales");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_3_elemento_plantilla_informe_sensores_valores_generales",
            "intervalo_valores_elemento_plantilla_informe_sensores_valores_generales",
            "campo_3_elemento_plantilla_informe_sensores_valores_generales");
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_valores_generales").change(funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_valores_generales);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_incrementos_totales = function() {
    $("#clase_sensor_1_elemento_plantilla_informe_sensores_incrementos_totales").off();
    $("#clase_sensor_2_elemento_plantilla_informe_sensores_incrementos_totales").off();
    $("#clase_sensor_3_elemento_plantilla_informe_sensores_incrementos_totales").off();
    $("#campo_1_elemento_plantilla_informe_sensores_incrementos_totales").off();
    $("#campo_2_elemento_plantilla_informe_sensores_incrementos_totales").off();
    $("#campo_3_elemento_plantilla_informe_sensores_incrementos_totales").off();
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_sensores_incrementos_totales").off();
    $("#intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales").off();
    $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").off();

    // Mostrar listas dobles
    if ($('#select_sensores_elemento_plantilla_informe_sensores_incrementos_totales_no_visible').length) {
        $('#select_sensores_elemento_plantilla_informe_sensores_incrementos_totales_no_visible').attr("id", "select_sensores_elemento_plantilla_informe_sensores_incrementos_totales_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_elemento_plantilla_informe_sensores_incrementos_totales", true);
    }

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_incrementos_totales_clase_modificada = function() {
        var ids_listas_clases_sensor = [];
        ids_listas_clases_sensor.push("clase_sensor_1_elemento_plantilla_informe_sensores_incrementos_totales");
        ids_listas_clases_sensor.push("clase_sensor_2_elemento_plantilla_informe_sensores_incrementos_totales");
        ids_listas_clases_sensor.push("clase_sensor_3_elemento_plantilla_informe_sensores_incrementos_totales");
        funcion_recarga_lista_doble_sensores_clases_elemento_plantilla_informe(
            ids_listas_clases_sensor,
            "tipo_seleccion_sensores_elemento_plantilla_informe_sensores_incrementos_totales",
            "ids_sensores_elemento_plantilla_informe_sensores_incrementos_totales",
            true);
    };
    $("#clase_sensor_1_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_incrementos_totales_clase_modificada);
    $("#clase_sensor_2_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_incrementos_totales_clase_modificada);
    $("#clase_sensor_3_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_incrementos_totales_clase_modificada);

    var funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_incrementos_totales_tipo_seleccion_sensores_modificado = function() {
        var ids_listas_clases_sensor = [];
        ids_listas_clases_sensor.push("clase_sensor_1_elemento_plantilla_informe_sensores_incrementos_totales");
        ids_listas_clases_sensor.push("clase_sensor_2_elemento_plantilla_informe_sensores_incrementos_totales");
        ids_listas_clases_sensor.push("clase_sensor_3_elemento_plantilla_informe_sensores_incrementos_totales");
        funcion_recarga_lista_doble_sensores_clases_elemento_plantilla_informe(
            ids_listas_clases_sensor,
            "tipo_seleccion_sensores_elemento_plantilla_informe_sensores_incrementos_totales",
            "ids_sensores_elemento_plantilla_informe_sensores_incrementos_totales",
            false);
    };
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_incrementos_totales_tipo_seleccion_sensores_modificado);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_1_elemento_plantilla_informe_tipo_sensores_incrementos_totales = function() {
        funcion_habilita_campo_elemento_plantilla_informe("1_elemento_plantilla_informe_sensores_incrementos_totales");
    };
    $("#campo_1_elemento_plantilla_informe_sensores_incrementos_totales").show(funcion_habilita_campo_1_elemento_plantilla_informe_tipo_sensores_incrementos_totales);
    var funcion_habilita_campo_2_elemento_plantilla_informe_tipo_sensores_incrementos_totales = function() {
        funcion_habilita_campo_elemento_plantilla_informe("2_elemento_plantilla_informe_sensores_incrementos_totales");
    };
    $("#campo_2_elemento_plantilla_informe_sensores_incrementos_totales").show(funcion_habilita_campo_2_elemento_plantilla_informe_tipo_sensores_incrementos_totales);
    var funcion_habilita_campo_3_elemento_plantilla_informe_tipo_sensores_incrementos_totales = function() {
        funcion_habilita_campo_elemento_plantilla_informe("3_elemento_plantilla_informe_sensores_incrementos_totales");
    };
    $("#campo_3_elemento_plantilla_informe_sensores_incrementos_totales").show(funcion_habilita_campo_3_elemento_plantilla_informe_tipo_sensores_incrementos_totales);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_incrementos_totales = function(numero_campo) {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_" + numero_campo + "_elemento_plantilla_informe_sensores_incrementos_totales",
            "intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales",
            "campo_" + numero_campo + "_elemento_plantilla_informe_sensores_incrementos_totales");
    };
    var funcion_recarga_campos_sensor_clase_sensor_1_elemento_plantilla_informe_sensores_incrementos_totales = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_incrementos_totales(1);
    };
    $("#clase_sensor_1_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_recarga_campos_sensor_clase_sensor_1_elemento_plantilla_informe_sensores_incrementos_totales);
    var funcion_recarga_campos_sensor_clase_sensor_2_elemento_plantilla_informe_sensores_incrementos_totales = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_incrementos_totales(2);
    };
    $("#clase_sensor_2_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_recarga_campos_sensor_clase_sensor_2_elemento_plantilla_informe_sensores_incrementos_totales);
    var funcion_recarga_campos_sensor_clase_sensor_3_elemento_plantilla_informe_sensores_incrementos_totales = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_incrementos_totales(3);
    };
    $("#clase_sensor_3_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_recarga_campos_sensor_clase_sensor_3_elemento_plantilla_informe_sensores_incrementos_totales);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_1_elemento_plantilla_informe_tipo_sensores_incrementos_totales = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("1_elemento_plantilla_informe_sensores_incrementos_totales");
    };
    $("#campo_1_elemento_plantilla_informe_sensores_incrementos_totales").show(funcion_muestra_control_parametros_extra_campo_1_elemento_plantilla_informe_tipo_sensores_incrementos_totales);
    $("#campo_1_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_muestra_control_parametros_extra_campo_1_elemento_plantilla_informe_tipo_sensores_incrementos_totales);
    var funcion_muestra_control_parametros_extra_campo_2_elemento_plantilla_informe_tipo_sensores_incrementos_totales = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("2_elemento_plantilla_informe_sensores_incrementos_totales");
    };
    $("#campo_2_elemento_plantilla_informe_sensores_incrementos_totales").show(funcion_muestra_control_parametros_extra_campo_2_elemento_plantilla_informe_tipo_sensores_incrementos_totales);
    $("#campo_2_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_muestra_control_parametros_extra_campo_2_elemento_plantilla_informe_tipo_sensores_incrementos_totales);
    var funcion_muestra_control_parametros_extra_campo_3_elemento_plantilla_informe_tipo_sensores_incrementos_totales = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("3_elemento_plantilla_informe_sensores_incrementos_totales");
    };
    $("#campo_3_elemento_plantilla_informe_sensores_incrementos_totales").show(funcion_muestra_control_parametros_extra_campo_3_elemento_plantilla_informe_tipo_sensores_incrementos_totales);
    $("#campo_3_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_muestra_control_parametros_extra_campo_3_elemento_plantilla_informe_tipo_sensores_incrementos_totales);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_incrementos_totales = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            "clase_sensor_1_elemento_plantilla_informe_sensores_incrementos_totales",
            null,
            "campo_1_elemento_plantilla_informe_sensores_incrementos_totales",
            "intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales");
    };
    $("#campo_1_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_incrementos_totales);

    // Habilitación del selector de agregación de comparación de valores generales
    var funcion_habilita_agregacion_elemento_plantilla_informe_sensores_incrementos_totales = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_agregaciones = $("select#agregacion_elemento_plantilla_informe_sensores_incrementos_totales option").length;
        if (numero_agregaciones <= 1) {
            $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").attr('disabled', true);
        }
        else {
            $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").removeAttr('disabled');
        }
    };
    $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").show(funcion_habilita_agregacion_elemento_plantilla_informe_sensores_incrementos_totales);
    $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_habilita_agregacion_elemento_plantilla_informe_sensores_incrementos_totales);

    // Recarga las agregaciones según la clase y el campo en el elemento de plantilla de informe
    var funcion_recarga_agregaciones_elemento_plantilla_informe_sensores_incrementos_totales = function() {
        var clase_sensor = $("#clase_sensor_1_elemento_plantilla_informe_sensores_incrementos_totales").val();
        var campo = $("#campo_1_elemento_plantilla_informe_sensores_incrementos_totales").val();
        var intervalo_valores = $("#intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales").val();
        var agregacion = $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_NINGUNO:
            case INTERVALO_VALORES_TIEMPO_REAL: {
                $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").val(AGREGACION_NINGUNA);
                $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").attr('disabled', true);
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
                    $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").html(resultado.html);
                    $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").trigger("change");
                });
                break;
            }
        }
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_recarga_agregaciones_elemento_plantilla_informe_sensores_incrementos_totales);

    // Habilitación de la lista de intervalos de valores
    var funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_incrementos_totales = function() {
        var intervalo_valores = $("#intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales").val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_NINGUNO:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
                $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").val(AGREGACION_NINGUNA);
                $("#agregacion_elemento_plantilla_informe_sensores_incrementos_totales").attr('disabled', true);
                break;
            }
        }
        funcion_habilita_intervalo_valores_elemento_plantilla_informe("intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales", true);
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales").show(funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_incrementos_totales);

    // Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
    var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales = function() {
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_1_elemento_plantilla_informe_sensores_incrementos_totales",
            "intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales",
            "campo_1_elemento_plantilla_informe_sensores_incrementos_totales");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_2_elemento_plantilla_informe_sensores_incrementos_totales",
            "intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales",
            "campo_2_elemento_plantilla_informe_sensores_incrementos_totales");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_3_elemento_plantilla_informe_sensores_incrementos_totales",
            "intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales",
            "campo_3_elemento_plantilla_informe_sensores_incrementos_totales");
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales").change(funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_histograma = function() {
    $("#clase_sensor_elemento_plantilla_informe_sensores_histograma").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_histograma").off();
    $("#id_sensor_elemento_plantilla_informe_sensores_histograma").off();
    $("#campo_elemento_plantilla_informe_sensores_histograma").off();
    $("#intervalo_valores_elemento_plantilla_informe_sensores_histograma").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_histograma = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_sensores_histograma");
    };
    $("#id_sensor_elemento_plantilla_informe_sensores_histograma").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_sensores_histograma);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_histograma = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_elemento_plantilla_informe_sensores_histograma",
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_sensores_histograma",
            "id_sensor_elemento_plantilla_informe_sensores_histograma");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_histograma").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_histograma);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_histograma").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_sensores_histograma);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_histograma = function() {
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_sensores_histograma");
    };
    $("#campo_elemento_plantilla_informe_sensores_histograma").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_sensores_histograma);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_histograma = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_elemento_plantilla_informe_sensores_histograma",
            "intervalo_valores_elemento_plantilla_informe_sensores_histograma",
            "campo_elemento_plantilla_informe_sensores_histograma");
    };
    $("#clase_sensor_elemento_plantilla_informe_sensores_histograma").change(funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores_histograma);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_histograma = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("elemento_plantilla_informe_sensores_histograma");
    };
    $("#campo_elemento_plantilla_informe_sensores_histograma").show(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_histograma);
    $("#campo_elemento_plantilla_informe_sensores_histograma").change(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_sensores_histograma);

    // Habilitación de la lista de intervalos de valores
    var funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_histograma = function() {
        funcion_habilita_intervalo_valores_elemento_plantilla_informe("intervalo_valores_elemento_plantilla_informe_sensores_histograma", true);
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_histograma").show(funcion_habilita_intervalo_valores_elemento_plantilla_informe_tipo_sensores_histograma);

    // Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
    var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_histograma = function() {
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_histograma",
            "intervalo_valores_elemento_plantilla_informe_sensores_histograma",
            "campo_elemento_plantilla_informe_sensores_histograma");
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_histograma").change(funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_histograma);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_histograma = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_sensores_histograma",
            null,
            "campo_elemento_plantilla_informe_sensores_histograma",
            "intervalo_valores_elemento_plantilla_informe_sensores_histograma");
    };
    $("#campo_elemento_plantilla_informe_sensores_histograma").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_sensores_histograma);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_sensores_correlacion = function() {
    $("#clase_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion").off();
    $("#clase_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion").off();
    $("#clase_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion").off();
    $("#clase_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion").off();
    $("#clase_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion").off();
    $("#tipo_seleccion_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion").off();
    $("#tipo_seleccion_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion").off();
    $("#tipo_seleccion_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion").off();
    $("#tipo_seleccion_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion").off();
    $("#tipo_seleccion_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion").off();
    $("#id_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion").off();
    $("#id_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion").off();
    $("#id_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion").off();
    $("#id_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion").off();
    $("#id_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion").off();
    $("#campo_independiente_1_elemento_plantilla_informe_sensores_correlacion").off();
    $("#campo_independiente_2_elemento_plantilla_informe_sensores_correlacion").off();
    $("#campo_independiente_3_elemento_plantilla_informe_sensores_correlacion").off();
    $("#campo_independiente_4_elemento_plantilla_informe_sensores_correlacion").off();
    $("#campo_dependiente_elemento_plantilla_informe_sensores_correlacion").off();
    $("#intervalo_valores_elemento_plantilla_informe_sensores_correlacion").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_independiente_1_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#id_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion").show(funcion_habilita_sensor_independiente_1_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_habilita_sensor_independiente_2_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_independiente_2_elemento_plantilla_informe_sensores_correlacion").show(funcion_habilita_sensor_independiente_2_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_habilita_sensor_independiente_3_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#id_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion").show(funcion_habilita_sensor_independiente_3_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_habilita_sensor_independiente_4_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("campo_independiente_4_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_independiente_4_elemento_plantilla_informe_sensores_correlacion").show(funcion_habilita_sensor_independiente_4_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_habilita_sensor_dependiente_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#id_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion").show(funcion_habilita_sensor_dependiente_elemento_plantilla_informe_tipo_sensores_correlacion);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_independiente_1_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion",
            null,
            "tipo_seleccion_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion",
            "id_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#clase_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_sensores_clase_sensor_independiente_1_elemento_plantilla_informe_tipo_sensores_correlacion);
    $("#tipo_seleccion_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_sensores_clase_sensor_independiente_1_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_recarga_sensores_clase_sensor_independiente_2_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion",
            null,
            "tipo_seleccion_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion",
            "id_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#clase_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_sensores_clase_sensor_independiente_2_elemento_plantilla_informe_tipo_sensores_correlacion);
    $("#tipo_seleccion_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_sensores_clase_sensor_independiente_2_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_recarga_sensores_clase_sensor_independiente_3_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion",
            null,
            "tipo_seleccion_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion",
            "id_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#clase_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_sensores_clase_sensor_independiente_3_elemento_plantilla_informe_tipo_sensores_correlacion);
    $("#tipo_seleccion_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_sensores_clase_sensor_independiente_3_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_recarga_sensores_clase_sensor_independiente_4_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion",
            null,
            "tipo_seleccion_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion",
            "id_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#clase_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_sensores_clase_sensor_independiente_4_elemento_plantilla_informe_tipo_sensores_correlacion);
    $("#tipo_seleccion_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_sensores_clase_sensor_independiente_4_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_recarga_sensores_clase_sensor_dependiente_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion",
            null,
            "tipo_seleccion_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion",
            "id_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#clase_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_sensores_clase_sensor_dependiente_elemento_plantilla_informe_tipo_sensores_correlacion);
    $("#tipo_seleccion_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_sensores_clase_sensor_dependiente_elemento_plantilla_informe_tipo_sensores_correlacion);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_independiente_1_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_habilita_campo_elemento_plantilla_informe("independiente_1_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_independiente_1_elemento_plantilla_informe_sensores_correlacion").show(funcion_habilita_campo_independiente_1_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_habilita_campo_independiente_2_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_habilita_campo_elemento_plantilla_informe("independiente_2_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_independiente_2_elemento_plantilla_informe_sensores_correlacion").show(funcion_habilita_campo_independiente_2_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_habilita_campo_independiente_3_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_habilita_campo_elemento_plantilla_informe("independiente_3_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_independiente_3_elemento_plantilla_informe_sensores_correlacion").show(funcion_habilita_campo_independiente_3_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_habilita_campo_independiente_4_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_habilita_campo_elemento_plantilla_informe("independiente_4_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_independiente_4_elemento_plantilla_informe_sensores_correlacion").show(funcion_habilita_campo_independiente_4_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_habilita_campo_dependiente_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_habilita_campo_elemento_plantilla_informe("dependiente_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_dependiente_elemento_plantilla_informe_sensores_correlacion").show(funcion_habilita_campo_dependiente_elemento_plantilla_informe_tipo_sensores_correlacion);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_dependiente_clase_sensor_elemento_plantilla_informe_sensores_correlacion = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_correlacion",
            "campo_dependiente_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#clase_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_campos_sensor_dependiente_clase_sensor_elemento_plantilla_informe_sensores_correlacion);
    var funcion_recarga_campos_sensor_independiente_1_clase_sensor_elemento_plantilla_informe_sensores_correlacion = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_correlacion",
            "campo_independiente_1_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#clase_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_campos_sensor_independiente_1_clase_sensor_elemento_plantilla_informe_sensores_correlacion);
    var funcion_recarga_campos_sensor_independiente_2_clase_sensor_elemento_plantilla_informe_sensores_correlacion = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_correlacion",
            "campo_independiente_2_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#clase_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_campos_sensor_independiente_2_clase_sensor_elemento_plantilla_informe_sensores_correlacion);
    var funcion_recarga_campos_sensor_independiente_3_clase_sensor_elemento_plantilla_informe_sensores_correlacion = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_correlacion",
            "campo_independiente_3_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#clase_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_campos_sensor_independiente_3_clase_sensor_elemento_plantilla_informe_sensores_correlacion);
    var funcion_recarga_campos_sensor_independiente_4_clase_sensor_elemento_plantilla_informe_sensores_correlacion = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_correlacion",
            "campo_independiente_4_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#clase_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_campos_sensor_independiente_4_clase_sensor_elemento_plantilla_informe_sensores_correlacion);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_independiente_1_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("independiente_1_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_independiente_1_elemento_plantilla_informe_sensores_correlacion").show(funcion_muestra_control_parametros_extra_campo_independiente_1_elemento_plantilla_informe_tipo_sensores_correlacion);
    $("#campo_independiente_1_elemento_plantilla_informe_sensores_correlacion").change(funcion_muestra_control_parametros_extra_campo_independiente_1_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_muestra_control_parametros_extra_campo_independiente_2_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("independiente_2_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_independiente_2_elemento_plantilla_informe_sensores_correlacion").show(funcion_muestra_control_parametros_extra_campo_independiente_2_elemento_plantilla_informe_tipo_sensores_correlacion);
    $("#campo_independiente_2_elemento_plantilla_informe_sensores_correlacion").change(funcion_muestra_control_parametros_extra_campo_independiente_2_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_muestra_control_parametros_extra_campo_independiente_3_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("independiente_3_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_independiente_3_elemento_plantilla_informe_sensores_correlacion").show(funcion_muestra_control_parametros_extra_campo_independiente_3_elemento_plantilla_informe_tipo_sensores_correlacion);
    $("#campo_independiente_3_elemento_plantilla_informe_sensores_correlacion").change(funcion_muestra_control_parametros_extra_campo_independiente_3_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_muestra_control_parametros_extra_campo_independiente_4_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("independiente_4_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_independiente_4_elemento_plantilla_informe_sensores_correlacion").show(funcion_muestra_control_parametros_extra_campo_independiente_4_elemento_plantilla_informe_tipo_sensores_correlacion);
    $("#campo_independiente_4_elemento_plantilla_informe_sensores_correlacion").change(funcion_muestra_control_parametros_extra_campo_independiente_4_elemento_plantilla_informe_tipo_sensores_correlacion);
    var funcion_muestra_control_parametros_extra_campo_dependiente_elemento_plantilla_informe_tipo_sensores_correlacion = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("dependiente_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#campo_dependiente_elemento_plantilla_informe_sensores_correlacion").show(funcion_muestra_control_parametros_extra_campo_dependiente_elemento_plantilla_informe_tipo_sensores_correlacion);
    $("#campo_dependiente_elemento_plantilla_informe_sensores_correlacion").change(funcion_muestra_control_parametros_extra_campo_dependiente_elemento_plantilla_informe_tipo_sensores_correlacion);

    // Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
    var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_correlacion = function() {
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_independiente_1_elemento_plantilla_informe_sensores_correlacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_correlacion",
            "campo_independiente_1_elemento_plantilla_informe_sensores_correlacion");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_independiente_2_elemento_plantilla_informe_sensores_correlacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_correlacion",
            "campo_independiente_2_elemento_plantilla_informe_sensores_correlacion");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_independiente_3_elemento_plantilla_informe_sensores_correlacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_correlacion",
            "campo_independiente_3_elemento_plantilla_informe_sensores_correlacion");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_independiente_4_elemento_plantilla_informe_sensores_correlacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_correlacion",
            "campo_independiente_4_elemento_plantilla_informe_sensores_correlacion");
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion",
            "intervalo_valores_elemento_plantilla_informe_sensores_correlacion",
            "campo_dependiente_elemento_plantilla_informe_sensores_correlacion");
    };
    $("#intervalo_valores_elemento_plantilla_informe_sensores_correlacion").change(funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_sensores_correlacion);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_actuadores_informacion_acciones_enviadas = function() {
    $("#clase_actuador_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").off();
    $("#destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").off();
    $("#tipo_seleccion_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").off();
    $("#id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").off();
    $("#clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").off();
    $("#id_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").off();
    $("#campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").off();
    $("#intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").off();

    // Habilitación de id de destino de acción
    var funcion_habilita_id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_ids_destino = $("select#id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas option").length;
        if (numero_ids_destino <= 1) {
            $("#id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").attr('disabled', true);
        }
        else {
            $("#id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").removeAttr('disabled');
        }
    };
    $("#id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").show(funcion_habilita_id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas);

    // Recarga los destinos de acción
    var funcion_recarga_destinos_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas = function() {
        var destino_accion = $("#destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").val();
        switch (destino_accion) {
            case DESTINO_ACCION_ACTUADOR: {
                funcion_recarga_actuadores_clase_actuador_elemento_plantilla_informe(
                    null,
                    "clase_actuador_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
                    "tipo_seleccion_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
                    "id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas");
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES: {
                funcion_recarga_grupos_actuadores_clase_actuador_elemento_plantilla_informe(
                    null,
                    "clase_actuador_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
                    "tipo_seleccion_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
                    "id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas");
                break;
            }
        }
    };
    $("#clase_actuador_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").change(funcion_recarga_destinos_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas);
    $("#destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").change(funcion_recarga_destinos_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas);
    $("#tipo_seleccion_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").change(funcion_recarga_destinos_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas);

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas");
    };
    $("#id_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            "clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
            "id_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas");
    };
    $("#clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas);

    // Habilita el control de campo de sensor del elemento de plantilla de informe
    var funcion_habilita_campo_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas = function() {
        funcion_habilita_campo_elemento_plantilla_informe("elemento_plantilla_informe_actuadores_informacion_acciones_enviadas");
    };
    $("#campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").show(funcion_habilita_campo_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas);

    // Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
    var funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas = function() {
        funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores(
            "clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
            "intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
            "campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas");
    };
    $("#clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").change(funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas);

    // Muestra el control de parametros extra de campo de sensor de los diferentes tipos de elementos de plantillas de informes
    var funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas = function() {
        funcion_muestra_control_parametros_extra_campo_administracion("elemento_plantilla_informe_actuadores_informacion_acciones_enviadas");
    };
    $("#campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").show(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas);
    $("#campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").change(funcion_muestra_control_parametros_extra_campo_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas);

    // Habilita los intervalos de valores de sensor de acciones enviadas
    var funcion_habilita_intervalos_valores_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas = function() {
        var clase = $("#clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").val();
        switch (clase) {
            case CLASE_NINGUNA: {
                $("#intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").attr('disabled', true);
                break;
            }
            default: {
                $("#intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").removeAttr('disabled');
                break;
            }
        }
    };
    $("#clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").show(funcion_habilita_intervalos_valores_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas);
    $("#clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").change(funcion_habilita_intervalos_valores_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
            null,
            "campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
            "intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas");
    };
    $("#campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas);

    // Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
    var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas = function() {
        funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe(
            "clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
            "intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas",
            "campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas");
    };
    $("#intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas").change(funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_consumos_costes_generales = function() {
    $("#medicion_elemento_plantilla_informe_smartmeter_consumos_costes_generales").off();
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales").off();

    // Mostrar listas dobles
    if ($('#select_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales_no_visible').length) {
        $('#select_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales_no_visible').attr("id", "select_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales", true);
    }

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_generales = function() {
        funcion_recarga_lista_doble_sensores_elemento_plantilla_informe(
            null,
            "medicion_elemento_plantilla_informe_smartmeter_consumos_costes_generales",
            "tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales",
            "ids_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_consumos_costes_generales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_generales);
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_generales);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_generales = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            null,
            "medicion_elemento_plantilla_informe_smartmeter_consumos_costes_generales",
            null,
            "intervalo_valores_elemento_plantilla_informe_smartmeter_consumos_costes_generales");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_consumos_costes_generales").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_generales);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_consumos_costes_totales = function() {
    $("#medicion_elemento_plantilla_informe_smartmeter_consumos_costes_totales").off();
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales").off();

    // Mostrar listas dobles
    if ($('#select_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales_no_visible').length) {
        $('#select_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales_no_visible').attr("id", "select_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales", true);
    }

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_totales = function() {
        funcion_recarga_lista_doble_sensores_elemento_plantilla_informe(
            null,
            "medicion_elemento_plantilla_informe_smartmeter_consumos_costes_totales",
            "tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales",
            "ids_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_consumos_costes_totales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_totales);
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales").change(funcion_recarga_lista_doble_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_totales);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_totales = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            null,
            "medicion_elemento_plantilla_informe_smartmeter_consumos_costes_totales",
            null,
            "intervalo_valores_elemento_plantilla_informe_smartmeter_consumos_costes_totales");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_consumos_costes_totales").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_totales);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_comparacion_periodos = function() {
    $("#medicion_elemento_plantilla_informe_smartmeter_comparacion_periodos").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_comparacion_periodos = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos");
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_comparacion_periodos);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_comparacion_periodos = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            null,
            "medicion_elemento_plantilla_informe_smartmeter_comparacion_periodos",
            "tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos",
            "id_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_comparacion_periodos").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_comparacion_periodos);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_comparacion_periodos);

    // Recarga la lista de intervalos de valores de elementos de plantillas de informes
    var funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos = function() {
        funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe(
            null,
            "medicion_elemento_plantilla_informe_smartmeter_comparacion_periodos",
            null,
            "intervalo_valores_elemento_plantilla_informe_smartmeter_comparacion_periodos");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_comparacion_periodos").change(funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos);

    // Recarga los elementos del informe al cambiar de medición
    $("#medicion_elemento_plantilla_informe_smartmeter_comparacion_periodos").change(funcion_recarga_elementos_informe_elemento_plantilla_informe);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_simulador_tarifas = function() {
    $("#medicion_elemento_plantilla_informe_smartmeter_simulador_tarifas").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_simulador_tarifas").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_simulador_tarifas").off();

    // Mostrar listas dobles
    if ($('#select_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas_no_visible').length) {
        $('#select_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas_no_visible').attr("id", "select_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas", true);
    }

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_simulador_tarifas = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("medicion_elemento_plantilla_informe_smartmeter_simulador_tarifas");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_simulador_tarifas").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_simulador_tarifas);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_simulador_tarifas = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            null,
            "medicion_elemento_plantilla_informe_smartmeter_simulador_tarifas",
            "tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_simulador_tarifas",
            "id_sensor_elemento_plantilla_informe_smartmeter_simulador_tarifas");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_simulador_tarifas").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_simulador_tarifas);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_simulador_tarifas").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_simulador_tarifas);

    // Recarga de la lista doble de tarifas
    funcion_recarga_lista_doble_tarifas_informe_elemento_plantilla_informe_smartmeter_simulador_tarifas = function() {
        var medicion = $("#medicion_elemento_plantilla_informe_smartmeter_simulador_tarifas").val();
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/dame_lista_tarifas.php", {
            medicion: medicion,
            opciones_extra: OPCIONES_EXTRA_LISTA_TARIFAS_SIN_NINGUNA
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Recarga de lista doble
            // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
            $("#ids_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas").multiselect2side('destroy');
            $("#ids_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas").html(resultado.html);
            TLNT.Navegacion.convierte_lista_doble("ids_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas", true);
        });
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_simulador_tarifas").change(funcion_recarga_lista_doble_tarifas_informe_elemento_plantilla_informe_smartmeter_simulador_tarifas);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad = function() {
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_tramos_electricidad = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad");
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_tramos_electricidad);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_tramos_electricidad = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            CLASE_SENSOR_ENERGIA_ACTIVA,
            null,
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad",
            "id_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad");
    };
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_tramos_electricidad);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_cortes_tension_electricidad = function() {
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_cortes_tension_electricidad = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad");
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_cortes_tension_electricidad);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_cortes_tension_electricidad = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            CLASE_SENSOR_CORTES_TENSION,
            null,
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad",
            "id_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad");
    };
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_cortes_tension_electricidad);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_excesos_potencia_electricidad = function() {
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad");
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            CLASE_SENSOR_ENERGIA_ACTIVA,
            null,
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad",
            "id_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad");
    };
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad = function() {
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad");
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            CLASE_SENSOR_ENERGIA_REACTIVA,
            null,
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad",
            "id_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad");
    };
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_excesos_caudal_gas = function() {
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas");
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            CLASE_SENSOR_GAS,
            null,
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas",
            "id_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas");
    };
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_desvios_compra_energia = function() {
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia");
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            CLASE_SENSOR_COMPRA_ENERGIA,
            null,
            null,
            "tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia",
            "id_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia");
    };
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_desvios_ponderados_compra_energia = function() {
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").off();
    $("#id_sensor_hijo_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia");
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia);
    var funcion_habilita_sensor_hijo_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("id_sensor_hijo_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia");
    };
    $("#id_sensor_hijo_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").show(funcion_habilita_sensor_hijo_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia);

    // Recarga el sensor según los parámetros seleccionados
    var funcion_recarga_sensores_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia = function() {
        realizando_acciones_tipo_seleccion_sensores_desvios_ponderados_compra_energia = true;
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            CLASE_SENSOR_COMPRA_ENERGIA,
            null,
            null,
            "tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia",
            "id_sensor_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia");
    };
    $("#tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").change(funcion_recarga_sensores_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia);

    // Recarga los sensores hijos según los parámetros seleccionados
    var funcion_recarga_sensores_hijos_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia = function() {
        var tipo_seleccion_sensores = $("#tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").val();
        switch (tipo_seleccion_sensores) {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO: {
                var id_sensor = $("#id_sensor_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").val();
                $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores_hijos.php", {
                    clase_sensor: CLASE_SENSOR_COMPRA_ENERGIA,
                    id_sensor_padre: id_sensor,
                    opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    $("#id_sensor_hijo_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").html(resultado.html);
                    $("#id_sensor_hijo_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").trigger("chosen:updated");

                    funcion_habilita_sensor_hijo_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia();
                });
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE: {
                if (realizando_acciones_tipo_seleccion_sensores_desvios_ponderados_compra_energia == true) {
                    funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
                        CLASE_SENSOR_ENERGIA_ACTIVA,
                        null,
                        null,
                        "tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia",
                        "id_sensor_hijo_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia");
                }
                break;
            }
        }
        realizando_acciones_tipo_seleccion_sensores_desvios_ponderados_compra_energia = false;
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia").change(funcion_recarga_sensores_hijos_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_simulador_factura = function() {
    $("#medicion_elemento_plantilla_informe_smartmeter_simulador_factura").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_simulador_factura").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_simulador_factura").off();
    $("#tipo_seleccion_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_simulador_factura = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("medicion_elemento_plantilla_informe_smartmeter_simulador_factura");
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_simulador_factura").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_simulador_factura);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_simulador_factura = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            null,
            "medicion_elemento_plantilla_informe_smartmeter_simulador_factura",
            "tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_simulador_factura",
            "id_sensor_elemento_plantilla_informe_smartmeter_simulador_factura");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_simulador_factura").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_simulador_factura);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_simulador_factura").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_simulador_factura);

    // Recarga las tarifas según la medición seleccionada
    var funcion_recarga_tarifas_medicion_elemento_plantilla_informe_tipo_smartmeter_simulador_factura = function() {
        var medicion = $("#medicion_elemento_plantilla_informe_smartmeter_simulador_factura").val();
        var caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion(medicion);
        var opciones_extra_lista_tarifas = null;
        if (caracteristicas_tarifas["curva_coste"] == true) {
            opciones_extra_lista_tarifas = OPCIONES_EXTRA_LISTA_TARIFAS_TARIFA_VIGENTE_SEGUN_FECHAS;
        }
        else {
            opciones_extra_lista_tarifas = OPCIONES_EXTRA_LISTA_TARIFAS_ACTUAL;
        }
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/dame_lista_tarifas.php", {
            medicion: medicion,
            opciones_extra: opciones_extra_lista_tarifas
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_tarifa_elemento_plantilla_informe_smartmeter_simulador_factura").html(resultado.html);
            $("#id_tarifa_elemento_plantilla_informe_smartmeter_simulador_factura").trigger("chosen:updated");
        });
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_simulador_factura").change(funcion_recarga_tarifas_medicion_elemento_plantilla_informe_tipo_smartmeter_simulador_factura);

    // Mostrar listas dobles
    if ($('#select_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura_no_visible').length) {
        $('#select_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura_no_visible').attr("id", "select_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura", true);
    }

    // Recarga los sensores de reparto de costes según los parámetros seleccionados
    var funcion_recarga_lista_doble_sensores_reparto_costes_elemento_plantilla_informe_tipo_smartmeter_simulador_factura = function() {
        funcion_recarga_lista_doble_sensores_elemento_plantilla_informe(
            null,
            "medicion_elemento_plantilla_informe_smartmeter_simulador_factura",
            "tipo_seleccion_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura",
            "ids_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_simulador_factura").change(funcion_recarga_lista_doble_sensores_reparto_costes_elemento_plantilla_informe_tipo_smartmeter_simulador_factura);
    $("#tipo_seleccion_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura").change(funcion_recarga_lista_doble_sensores_reparto_costes_elemento_plantilla_informe_tipo_smartmeter_simulador_factura);

    // Recarga los elementos del informe al cambiar de medición
    $("#medicion_elemento_plantilla_informe_smartmeter_simulador_factura").change(funcion_recarga_elementos_informe_elemento_plantilla_informe);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_smartmeter_instalacion = function() {
    $("#medicion_elemento_plantilla_informe_smartmeter_instalacion").off();
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_instalacion").off();
    $("#id_sensor_elemento_plantilla_informe_smartmeter_instalacion").off();

    // Habilita el control de sensor del elemento de plantilla de informe
    var funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_instalacion = function() {
        funcion_habilita_sensor_elemento_plantilla_informe("medicion_elemento_plantilla_informe_smartmeter_instalacion");
    };
    $("#id_sensor_elemento_plantilla_informe_smartmeter_instalacion").show(funcion_habilita_sensor_elemento_plantilla_informe_tipo_smartmeter_instalacion);

    // Recarga los sensores según los parámetros seleccionados
    var funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_instalacion = function() {
        funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe(
            null,
            null,
            "medicion_elemento_plantilla_informe_smartmeter_instalacion",
            "tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_instalacion",
            "id_sensor_elemento_plantilla_informe_smartmeter_instalacion");
    };
    $("#medicion_elemento_plantilla_informe_smartmeter_instalacion").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_instalacion);
    $("#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_instalacion").change(funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe_tipo_smartmeter_instalacion);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_proyectos_simulador_linea_base = function() {
    $("#tipo_seleccion_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base").off();
    $("#id_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base").off();

    // Habilita el control de línea base
    var funcion_habilita_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_lineas_base = $("select#id_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base option").length;
        if (numero_lineas_base <= 1) {
            $("#id_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base").attr('disabled', true);
        }
        else {
            $("#id_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base").removeAttr('disabled');
        }
    };
    $("#id_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base").show(funcion_habilita_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base);

    // Recarga de las líneas base de la ventana de anyadir/modificar elemento de plantilla de informe
    funcion_recarga_lineas_base_elemento_plantilla_informe_proyectos_simulador_linea_base = function() {
        var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
        var tipo_seleccion_linea_base = $("#tipo_seleccion_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base").val();
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_lineas_base_elemento.php", {
            id_plantilla_informe: id_plantilla_informe,
            tipo_seleccion_linea_base: tipo_seleccion_linea_base
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base").html(resultado.html);
            $("#id_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base").trigger("chosen:updated");

            // Se habilita el control de línea base
            funcion_habilita_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base();
        });
    };
    $("#tipo_seleccion_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base").change(funcion_recarga_lineas_base_elemento_plantilla_informe_proyectos_simulador_linea_base);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_proyectos_informacion_proyecto = function() {
    $("#tipo_seleccion_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto").off();
    $("#id_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto").off();

    // Habilita el control de proyecto
    var funcion_habilita_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_proyectos = $("select#id_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto option").length;
        if (numero_proyectos <= 1) {
            $("#id_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto").attr('disabled', true);
        }
        else {
            $("#id_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto").removeAttr('disabled');
        }
    };
    $("#id_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto").show(funcion_habilita_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto);

    // Recarga de los proyectos de la ventana de anyadir/modificar elemento de plantilla de informe
    funcion_recarga_proyectos_elemento_plantilla_informe_proyectos_informacion_proyecto = function() {
        var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
        var tipo_seleccion_proyecto = $("#tipo_seleccion_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto").val();
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_proyectos_elemento.php", {
            id_plantilla_informe: id_plantilla_informe,
            tipo_seleccion_proyecto: tipo_seleccion_proyecto
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto").html(resultado.html);
            $("#id_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto").trigger("chosen:updated");

            // Se habilita el control de proyecto
            funcion_habilita_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto();
        });
    };
    $("#tipo_seleccion_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto").change(funcion_recarga_proyectos_elemento_plantilla_informe_proyectos_informacion_proyecto);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_periodo_tiempo = function() {
    $("#modificar_periodo_tiempo_elemento_plantilla_informe").off();
    $("#periodo_tiempo_elemento_plantilla_informe").off();

    // Se muestran los controles dependientes del periodo de tiempo del widget
    var funcion_muestra_controles_periodo_tiempo_elemento_plantilla_informe = function() {
        var modificar_periodo_tiempo = $("#modificar_periodo_tiempo_elemento_plantilla_informe").val();
        if (modificar_periodo_tiempo == VALOR_SI) {
            var periodo_tiempo = $("#periodo_tiempo_elemento_plantilla_informe").val();
            switch (periodo_tiempo) {
                case PERIODO_TIEMPO_FECHA_INICIO: {
                    $("#control_iniciar_comienzo_periodo_tiempo_elemento_plantilla_informe").hide();
                    $("#control_numero_periodos_tiempo_elemento_plantilla_informe").hide();
                    $("#control_fecha_inicio_periodo_tiempo_elemento_plantilla_informe").show();
                    break;
                }
                default: {
                    $("#control_iniciar_comienzo_periodo_tiempo_elemento_plantilla_informe").show();
                    $("#control_numero_periodos_tiempo_elemento_plantilla_informe").show();
                    $("#control_fecha_inicio_periodo_tiempo_elemento_plantilla_informe").hide();
                    break;
                }
            };
        }
    };
    $("#periodo_tiempo_elemento_plantilla_informe").show(funcion_muestra_controles_periodo_tiempo_elemento_plantilla_informe);
    $("#periodo_tiempo_elemento_plantilla_informe").change(funcion_muestra_controles_periodo_tiempo_elemento_plantilla_informe);

    // Muestra u oculta los controles de duración de periodos
    var funcion_muestra_controles_periodo_tiempo = function() {
        var modificar_periodo_tiempo = $("#modificar_periodo_tiempo_elemento_plantilla_informe").val();
        if (modificar_periodo_tiempo == VALOR_SI) {
            $("#control_periodo_tiempo_elemento_plantilla_informe").show();
            funcion_muestra_controles_periodo_tiempo_elemento_plantilla_informe();
        }
        else {
            $("#control_periodo_tiempo_elemento_plantilla_informe").hide();
            $("#control_iniciar_comienzo_periodo_tiempo_elemento_plantilla_informe").hide();
            $("#control_numero_periodos_tiempo_elemento_plantilla_informe").hide();
            $("#control_fecha_inicio_periodo_tiempo_elemento_plantilla_informe").hide();
        }
    };
    $("#modificar_periodo_tiempo_elemento_plantilla_informe").show(funcion_muestra_controles_periodo_tiempo);
    $("#modificar_periodo_tiempo_elemento_plantilla_informe").change(funcion_muestra_controles_periodo_tiempo);
};


establece_eventos_ventanas_modales_personal_administracion_elementos_plantilla_informe_duracion_separacion_periodos = function() {
    $("#modificar_duracion_periodos_elemento_plantilla_informe").off();
    $("#modificar_desplazamiento_periodo_anterior_elemento_plantilla_informe").off();

    // Muestra u oculta los controles de duración de periodos
    var funcion_muestra_controles_duracion_periodos = function() {
        var modificar_duracion_periodos = $("#modificar_duracion_periodos_elemento_plantilla_informe").val();
        if (modificar_duracion_periodos == VALOR_SI) {
            $("#control_periodo_tiempo_duracion_periodos_elemento_plantilla_informe").show();
            $("#control_iniciar_comienzo_periodo_tiempo_duracion_periodos_elemento_plantilla_informe").show();
            $("#control_numero_periodos_tiempo_duracion_periodos_elemento_plantilla_informe").show();
            $("#numero_periodos_tiempo_duracion_periodos_elemento_plantilla_informe").addClass('TLNT_input_mandatory TLNT_input_integer');
            $("#control_duracion_periodos_completos_elemento_plantilla_informe").show();
        }
        else {
            $("#control_periodo_tiempo_duracion_periodos_elemento_plantilla_informe").hide();
            $("#control_iniciar_comienzo_periodo_tiempo_duracion_periodos_elemento_plantilla_informe").hide();
            $("#control_numero_periodos_tiempo_duracion_periodos_elemento_plantilla_informe").hide();
            $("#numero_periodos_tiempo_duracion_periodos_elemento_plantilla_informe").val(0);
            $("#numero_periodos_tiempo_duracion_periodos_elemento_plantilla_informe").removeClass('TLNT_input_mandatory TLNT_input_integer');
            $("#control_duracion_periodos_completos_elemento_plantilla_informe").hide();
        }
    };
    $("#modificar_duracion_periodos_elemento_plantilla_informe").show(funcion_muestra_controles_duracion_periodos);
    $("#modificar_duracion_periodos_elemento_plantilla_informe").change(funcion_muestra_controles_duracion_periodos);

    // Muestra u oculta los controles de desplazamiento del periodo anterior
    var funcion_muestra_controles_desplazamiento_periodo_anterior = function() {
        var modificar_desplazamiento_periodo_anterior = $("#modificar_desplazamiento_periodo_anterior_elemento_plantilla_informe").val();
        if (modificar_desplazamiento_periodo_anterior == VALOR_SI) {
            $("#control_periodo_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe").show();
            $("#control_numero_periodos_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe").show();
            $("#numero_periodos_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe").addClass('TLNT_input_mandatory TLNT_input_integer');
        }
        else {
            $("#control_periodo_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe").hide();
            $("#control_numero_periodos_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe").hide();
            $("#numero_periodos_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe").val(1);
            $("#numero_periodos_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe").removeClass('TLNT_input_mandatory TLNT_input_integer');
        }
    };
    $("#modificar_desplazamiento_periodo_anterior_elemento_plantilla_informe").show(funcion_muestra_controles_desplazamiento_periodo_anterior);
    $("#modificar_desplazamiento_periodo_anterior_elemento_plantilla_informe").change(funcion_muestra_controles_desplazamiento_periodo_anterior);
};


//
// Funciones auxiliares (utilizadas en varias funciones)
//


// Recarga de los elementos de informe de un elemento de plantilla de informe
funcion_recarga_elementos_informe_elemento_plantilla_informe = function() {
    var tipo_elemento = $("#tipo_elemento_plantilla_informe").val();
    var parametros_informe = {};
    switch (tipo_elemento) {
        // Tipos de elementos de plantilla de informe sin elementos
        case TIPO_NINGUNO:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS: {
            return;
        }
        // Tipos de elementos de plantilla de informe con elementos
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION: {
            var clase_sensor = $("#clase_sensor_elemento_plantilla_informe_sensores_informacion").val();
            parametros_informe["clase_sensor"] = clase_sensor;
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
            var medicion = $("#medicion_elemento_plantilla_informe_smartmeter_consumos_costes_generales").val();
            parametros_informe["medicion"] = medicion;
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES: {
            var medicion = $("#medicion_elemento_plantilla_informe_smartmeter_consumos_costes_totales").val();
            parametros_informe["medicion"] = medicion;
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS: {
            var medicion = $("#medicion_elemento_plantilla_informe_smartmeter_comparacion_periodos").val();
            parametros_informe["medicion"] = medicion;
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS: {
            var medicion = $("#medicion_elemento_plantilla_informe_smartmeter_simulador_tarifas").val();
            parametros_informe["medicion"] = medicion;
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA: {
            var medicion = $("#medicion_elemento_plantilla_informe_smartmeter_simulador_factura").val();
            parametros_informe["medicion"] = medicion;
            break;
        }
    }

    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_elementos_informe_elemento.php", {
        tipo_elemento: tipo_elemento,
        parametros_informe: parametros_informe,
        elementos_informe: null
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Recarga de lista doble
        // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
        $("#elementos_informe_elemento_plantilla_informe").multiselect2side('destroy');
        $("#elementos_informe_elemento_plantilla_informe").html(resultado.html);
        TLNT.Navegacion.convierte_lista_doble("elementos_informe_elemento_plantilla_informe", true);
    });
};


// Habilitación de sensor
funcion_habilita_sensor_elemento_plantilla_informe = function(id_lista_sensores) {
    // Se deshabilita si sólo hay un valor para elegir
    var numero_sensores = $("select#" + id_lista_sensores + " option").length;
    if (numero_sensores <= 1) {
        $("#" + id_lista_sensores).attr('disabled', true).trigger("chosen:updated");
    }
    else {
        $("#" + id_lista_sensores).removeAttr('disabled').trigger("chosen:updated");
    }
};


// Recarga de los sensores de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
funcion_recarga_sensores_clase_sensor_elemento_plantilla_informe = function(
    clase_sensor,
    id_lista_clase_sensor,
    id_lista_medicion,
    id_lista_tipo_seleccion_sensor,
    id_lista_sensores) {
    var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
    var tipo_elemento_plantilla_informe = $("#tipo_elemento_plantilla_informe").val();
    var opciones_extra = null;
    switch (tipo_elemento_plantilla_informe) {
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS: {
            opciones_extra = OPCIONES_EXTRA_LISTA_NODOS_TODOS;
            break;
        }
        default: {
            opciones_extra = OPCIONES_EXTRA_LISTA_NODOS_NINGUNO;
            break;
        }
    }
    if (id_lista_clase_sensor != null) {
        clase_sensor = $("#" + id_lista_clase_sensor).val();
    }
    if (id_lista_medicion != null) {
        var medicion = $("#" + id_lista_medicion).val();
        clase_sensor = dame_clase_sensor_medicion(medicion);
    }
    var tipo_seleccion_sensor = $("#" + id_lista_tipo_seleccion_sensor).val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_sensores_elemento.php", {
        id_plantilla_informe: id_plantilla_informe,
        tipo_seleccion_sensor: tipo_seleccion_sensor,
        clase_sensor: clase_sensor,
        opciones_extra: opciones_extra
    },
    function (data, status) {
        recuperando_lista_ids_origenes_evento_personal = false;
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#" + id_lista_sensores).html(resultado.html);
        $("#" + id_lista_sensores).trigger("chosen:updated");

        // Habilitación del sensor
        funcion_habilita_sensor_elemento_plantilla_informe(id_lista_sensores);

        // Se lanza el evento de 'change'
        $("#" + id_lista_sensores).trigger('change');
    });
};


// Habilitación de grupo de sensores
funcion_habilita_grupo_sensores_elemento_plantilla_informe = function(id_lista_grupos_sensores) {
    // Se deshabilita si sólo hay un valor para elegir
    var numero_grupos_sensores = $("select#" + id_lista_grupos_sensores + " option").length;
    if (numero_grupos_sensores <= 1) {
        $("#" + id_lista_grupos_sensores).attr('disabled', true).trigger("chosen:updated");
    }
    else {
        $("#" + id_lista_grupos_sensores).removeAttr('disabled').trigger("chosen:updated");
    }
};


// Recarga de los grupos de sensores de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
funcion_recarga_grupos_sensores_clase_sensor_elemento_plantilla_informe = function(
    clase_sensor,
    id_lista_clase_sensor,
    id_lista_tipo_seleccion_grupo_sensores,
    id_lista_grupos_sensores) {
    var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
    var tipo_elemento_plantilla_informe = $("#tipo_elemento_plantilla_informe").val();
    var opciones_extra = null;
    switch (tipo_elemento_plantilla_informe) {
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS: {
            opciones_extra = OPCIONES_EXTRA_LISTA_NODOS_TODOS;
            break;
        }
        default: {
            opciones_extra = OPCIONES_EXTRA_LISTA_NODOS_NINGUNO;
            break;
        }
    }
    if (id_lista_clase_sensor != null) {
        clase_sensor = $("#" + id_lista_clase_sensor).val();
    }
    var tipo_seleccion_grupo_sensores = $("#" + id_lista_tipo_seleccion_grupo_sensores).val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_grupos_sensores_elemento.php", {
        id_plantilla_informe: id_plantilla_informe,
        tipo_seleccion_grupo_sensores: tipo_seleccion_grupo_sensores,
        clase_sensor: clase_sensor,
        opciones_extra: opciones_extra
    },
    function (data, status) {
        recuperando_lista_ids_origenes_evento_personal = false;
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#" + id_lista_grupos_sensores).html(resultado.html);
        $("#" + id_lista_grupos_sensores).trigger("chosen:updated");

        // Habilitación del sensor
        funcion_habilita_grupo_sensores_elemento_plantilla_informe(id_lista_grupos_sensores);

        // Se lanza el evento de 'change'
        $("#" + id_lista_grupos_sensores).trigger('change');
    });
};


// Recarga de la lista doble de sensores de un elemento de plantilla de informe
funcion_recarga_lista_doble_sensores_elemento_plantilla_informe = function(
    id_lista_clase_sensor,
    id_lista_medicion,
    id_lista_tipo_seleccion_sensores,
    id_lista_doble_sensores) {
    var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
    var clase_sensor = CLASE_TODAS;
    if (id_lista_clase_sensor != null) {
        clase_sensor = $("#" + id_lista_clase_sensor).val();
    }
    if (id_lista_medicion != null) {
        var medicion = $("#" + id_lista_medicion).val();
        clase_sensor = dame_clase_sensor_medicion(medicion);
    }
    var tipo_seleccion_sensor = $("#" + id_lista_tipo_seleccion_sensores).val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_sensores_elemento.php", {
        id_plantilla_informe: id_plantilla_informe,
        tipo_seleccion_sensor: tipo_seleccion_sensor,
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
        $("#" + id_lista_doble_sensores).multiselect2side('destroy');
        $("#" + id_lista_doble_sensores).html(resultado.html);
        TLNT.Navegacion.convierte_lista_doble(id_lista_doble_sensores, true);
    });
};


// Recarga de la lista doble de sensores de un elemento de plantilla de informe (de múltiples clases)
funcion_recarga_lista_doble_sensores_clases_elemento_plantilla_informe = function(
    ids_listas_clases_sensor,
    id_lista_tipo_seleccion_sensores,
    id_lista_doble_sensores,
    mantener_sensores_seleccionados) {
    var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
    var clases_sensor = [];
    for (var i = 0; i < ids_listas_clases_sensor.length; i++) {
        var id_lista_clase_sensor = ids_listas_clases_sensor[i];
        var clase_sensor = $("#" + id_lista_clase_sensor).val();
        clases_sensor.push(clase_sensor);
    }
    var tipo_seleccion_sensor = $("#" + id_lista_tipo_seleccion_sensores).val();
    var ids_sensores = [];
    if (mantener_sensores_seleccionados == true) {
        $("#" + id_lista_doble_sensores + " option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_sensores.push($(this).val());
            }
        });
    }
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_sensores_clases_elemento.php", {
        id_plantilla_informe: id_plantilla_informe,
        tipo_seleccion_sensor: tipo_seleccion_sensor,
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
        // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
        $("#" + id_lista_doble_sensores).multiselect2side('destroy');
        $("#" + id_lista_doble_sensores).html(resultado.html);
        TLNT.Navegacion.convierte_lista_doble(id_lista_doble_sensores, true);
    });
};


// Recarga de la lista doble de actuadores de un elemento de plantilla de informe
funcion_recarga_lista_doble_actuadores_elemento_plantilla_informe = function(
    id_lista_clase_actuador,
    id_lista_tipo_seleccion_actuadores,
    id_lista_doble_actuadores) {
    var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
    var clase_actuador = CLASE_TODAS;
    if (id_lista_clase_actuador != null) {
        clase_actuador = $("#" + id_lista_clase_actuador).val();
    }
    var tipo_seleccion_actuador = $("#" + id_lista_tipo_seleccion_actuadores).val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_actuadores_elemento.php", {
        id_plantilla_informe: id_plantilla_informe,
        tipo_seleccion_actuador: tipo_seleccion_actuador,
        clase_actuador: clase_actuador,
        opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Recarga de lista doble
        // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
        $("#" + id_lista_doble_actuadores).multiselect2side('destroy');
        $("#" + id_lista_doble_actuadores).html(resultado.html);
        TLNT.Navegacion.convierte_lista_doble(id_lista_doble_actuadores, true);
    });
};


// Recarga de la lista doble de grupos de actuadores de un elemento de plantilla de informe
funcion_recarga_lista_doble_grupos_actuadores_elemento_plantilla_informe = function(
    id_lista_clase_actuador,
    id_lista_tipo_seleccion_grupos_actuadores,
    id_lista_doble_grupos_actuadores) {
    var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
    var clase_actuador = CLASE_TODAS;
    if (id_lista_clase_actuador != null) {
        clase_actuador = $("#" + id_lista_clase_actuador).val();
    }
    var tipo_seleccion_grupo_actuadores = $("#" + id_lista_tipo_seleccion_grupos_actuadores).val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_grupos_actuadores_elemento.php", {
        id_plantilla_informe: id_plantilla_informe,
        tipo_seleccion_grupo_actuadores: tipo_seleccion_grupo_actuadores,
        clase_actuador: clase_actuador,
        opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Recarga de lista doble
        // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
        $("#" + id_lista_doble_grupos_actuadores).multiselect2side('destroy');
        $("#" + id_lista_doble_grupos_actuadores).html(resultado.html);
        TLNT.Navegacion.convierte_lista_doble(id_lista_doble_grupos_actuadores, true);
    });
};


// Habilitación de campos de sensores
funcion_habilita_campo_elemento_plantilla_informe = function(id_controles) {
    // Se deshabilita si sólo hay un valor para elegir
    var numero_campos = $("select#campo_" + id_controles + " option").length;
    if (numero_campos <= 1) {
        $("#campo_" + id_controles).attr('disabled', true);
    }
    else {
        $("#campo_" + id_controles).removeAttr('disabled');
    }
};


// Recarga de los campos de una clase de sensor de la ventana de anyadir/modificar elemento de plantilla de informe
funcion_recarga_campos_sensor_clase_sensor_elemento_plantilla_informe_sensores = function(
    id_lista_clase_sensor,
    id_lista_intervalo_valores,
    id_lista_campo) {
    var tipo_elemento = $("#tipo_elemento_plantilla_informe").val();
    var clase_sensor = $("#" + id_lista_clase_sensor).val();
    var intervalo_valores = null;
    if (id_lista_intervalo_valores != null) {
        intervalo_valores = $("#" + id_lista_intervalo_valores).val();
    }
    var campo = $("#" + id_lista_campo).val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_campos_sensor_elemento.php", {
        tipo_elemento: tipo_elemento,
        clase_sensor: clase_sensor,
        intervalo_valores: intervalo_valores,
        campo: campo
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se actualiza la lista de campos de sensores
        $("#" + id_lista_campo).html(resultado.html);

        // Campo seleccionado por defecto
        switch (clase_sensor) {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_GAS:
            case CLASE_SENSOR_AGUA: {
                $("#" + id_lista_campo).val(CAMPO_INCREMENTO);
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA: {
                $("#" + id_lista_campo).val(CAMPO_CONSUMO_ESTIMADO);
                break;
            }
        }

        // Habilitación del campo de sensor
        var numero_campos = $("select#" + id_lista_campo + " option").length;
        if (numero_campos <= 1) {
            $("#" + id_lista_campo).attr('disabled', true);
        }
        else {
            $("#" + id_lista_campo).removeAttr('disabled');
        }

        // Se lanza el evento de 'change' (después de seleccionar el campo por defecto)
        $("#" + id_lista_campo).trigger('change');
    });
};


// Habilitación de intervalos de valores
funcion_habilita_intervalo_valores_elemento_plantilla_informe = function(id_lista_intervalo_valores, habilitar_lista_intervalo_valores) {
    // Se deshabilita si sólo hay un valor para elegir o hay que deshabilitar el intervalo
    var numero_intervalos = $("select#" + id_lista_intervalo_valores + " option").length;
    if ((numero_intervalos <= 1) || (habilitar_lista_intervalo_valores == false)) {
        $("#" + id_lista_intervalo_valores).attr('disabled', true);
    }
    else {
        $("#" + id_lista_intervalo_valores).removeAttr('disabled');
    }
};


// Recarga los intervalos de valores de sensor del elemento
funcion_recarga_intervalos_valores_sensor_elemento_plantilla_informe = function(
    id_lista_clase_sensor,
    id_lista_medicion,
    id_lista_campo,
    id_lista_intervalo_valores) {
    var tipo_elemento = $("#tipo_elemento_plantilla_informe").val();
    var clase_sensor = null;
    if (id_lista_clase_sensor != null) {
        clase_sensor = $("#" + id_lista_clase_sensor).val();
    }
    if (id_lista_medicion != null) {
        var medicion = $("#" + id_lista_medicion).val();
        clase_sensor = dame_clase_sensor_medicion(medicion);
    }
    var campo = CAMPO_NINGUNO;
    if (id_lista_campo != null) {
        campo = $("#" + id_lista_campo).val();
    }
    var intervalo_valores = $("#" + id_lista_intervalo_valores).val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_intervalos_valores_sensor_elemento.php", {
        tipo_elemento: tipo_elemento,
        clase_sensor: clase_sensor,
        campo: campo,
        intervalo_valores: intervalo_valores
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#" + id_lista_intervalo_valores).html(resultado.html);

        // Habilitar la lista de intervalos de valores
        var habilitar_lista_intervalo_valores = true;

        // Intervalo seleccionado por defecto
        switch (tipo_elemento) {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS: {
                switch (clase_sensor) {
                    case CLASE_NINGUNA: {
                        intervalo_valores = INTERVALO_VALORES_NINGUNO;
                        habilitar_lista_intervalo_valores = false;
                        break;
                    }
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    case CLASE_SENSOR_ENERGIA_REACTIVA:
                    case CLASE_SENSOR_COMPRA_ENERGIA:
                    case CLASE_SENSOR_GAS:
                    case CLASE_SENSOR_AGUA: {
                        intervalo_valores = INTERVALO_VALORES_HORA;
                        break;
                    }
                    case CLASE_SENSOR_GENERICA: {
                        break;
                    }
                    default: {
                        intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL_LINEAS;
                        break;
                    }
                }
                $("#" + id_lista_intervalo_valores).val(intervalo_valores);
                break;
            }
            default: {
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
                    $("#" + id_lista_intervalo_valores).val(intervalo_valores);
                }
                break;
            }
        }
        $("#" + id_lista_intervalo_valores).trigger("change");

        // Habilitación del intervalo de valores
        funcion_habilita_intervalo_valores_elemento_plantilla_informe(id_lista_intervalo_valores, habilitar_lista_intervalo_valores);
    });
};


// Recarga de los actuadores de una clase de actuador de la ventana de anyadir/modificar elemento de plantilla de informe
funcion_recarga_actuadores_clase_actuador_elemento_plantilla_informe = function(
    clase_actuador,
    id_lista_clase_actuador,
    id_lista_tipo_seleccion_actuador,
    id_lista_actuadores) {
    var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
    var opciones_extra = OPCIONES_EXTRA_LISTA_NODOS_NINGUNO;
    if (id_lista_clase_actuador != null) {
        clase_actuador = $("#" + id_lista_clase_actuador).val();
    }
    var tipo_seleccion_actuador = $("#" + id_lista_tipo_seleccion_actuador).val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_actuadores_elemento.php", {
        id_plantilla_informe: id_plantilla_informe,
        tipo_seleccion_actuador: tipo_seleccion_actuador,
        clase_actuador: clase_actuador,
        opciones_extra: opciones_extra
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#" + id_lista_actuadores).html(resultado.html);
        $("#" + id_lista_actuadores).trigger("chosen:updated");

        // Se deshabilita si sólo hay un valor para elegir
        var numero_ids = $("select#" + id_lista_actuadores + " option").length;
        if (numero_ids <= 1) {
            $("#" + id_lista_actuadores).attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#" + id_lista_actuadores).removeAttr('disabled').trigger("chosen:updated");
        }
    });
};


// Recarga de los grupos de actuadores de una clase de actuador de la ventana de anyadir/modificar elemento de plantilla de informe
funcion_recarga_grupos_actuadores_clase_actuador_elemento_plantilla_informe = function(
    clase_actuador,
    id_lista_clase_actuador,
    id_lista_tipo_seleccion_grupo_actuadores,
    id_lista_grupos_actuadores) {
    var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
    var opciones_extra = OPCIONES_EXTRA_LISTA_NODOS_NINGUNO;
    if (id_lista_clase_actuador != null) {
        clase_actuador = $("#" + id_lista_clase_actuador).val();
    }
    var tipo_seleccion_grupo_actuadores = $("#" + id_lista_tipo_seleccion_grupo_actuadores).val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_grupos_actuadores_elemento.php", {
        id_plantilla_informe: id_plantilla_informe,
        tipo_seleccion_grupo_actuadores: tipo_seleccion_grupo_actuadores,
        clase_actuador: clase_actuador,
        opciones_extra: opciones_extra
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#" + id_lista_grupos_actuadores).html(resultado.html);
        $("#" + id_lista_grupos_actuadores).trigger("chosen:updated");

        // Se deshabilita si sólo hay un valor para elegir
        var numero_ids = $("select#" + id_lista_grupos_actuadores + " option").length;
        if (numero_ids <= 1) {
            $("#" + id_lista_grupos_actuadores).attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#" + id_lista_grupos_actuadores).removeAttr('disabled').trigger("chosen:updated");
        }
    });
};


// Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
var funcion_recarga_campos_sensor_intervalo_valores_elemento_plantilla_informe = function(
    id_lista_clase_sensor,
    id_lista_intervalo_valores,
    id_lista_campo) {
    var clase_sensor = $("#" + id_lista_clase_sensor).val();
    if (clase_sensor != CLASE_SENSOR_GENERICA) {
        return;
    }
    var tipo_elemento = $("#tipo_elemento_plantilla_informe").val();
    var intervalo_valores = $("#" + id_lista_intervalo_valores).val();
    var campo = $("#" + id_lista_campo).val();
    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_lista_campos_sensor_elemento.php", {
        tipo_elemento: tipo_elemento,
        clase_sensor: clase_sensor,
        intervalo_valores: intervalo_valores,
        campo: campo
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#" + id_lista_campo).html(resultado.html);

        // Se deshabilita si sólo hay un valor para elegir
        var numero_ids = $("select#" + id_lista_campo + " option").length;
        if (numero_ids <= 1) {
            $("#" + id_lista_campo).attr('disabled', true);
        }
        else {
            $("#" + id_lista_campo).removeAttr('disabled');
        }
    });
};


//
// Funciones auxiliares de informes de plantillas de informes
//


var funcion_realiza_acciones_valor_parametro_plantilla_informe_sensor_modificada = function() {
    var cadena_ids_parametros_asociados = this.getAttribute("ids_parametros_asociados");
    if (cadena_ids_parametros_asociados != "") {
        var tipo = this.getAttribute("tipo");
        var cadena_parametros_tipo = this.getAttribute("parametros_tipo");
        var id_valor_parametro = this.value;
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/dame_ids_valores_parametros_asociados_valor_parametro.php", {
            tipo: tipo,
            cadena_parametros_tipo: cadena_parametros_tipo,
            id_valor_parametro: id_valor_parametro,
            cadena_ids_parametros_asociados: cadena_ids_parametros_asociados
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            var ids_valores_parametros_asociados = resultado.ids_valores_parametros_asociados;
            for (var i = 0; i < ids_valores_parametros_asociados.length; i++) {
                var id_parametro = ids_valores_parametros_asociados[i]["id"];
                var valor_parametro = ids_valores_parametros_asociados[i]["valor"];

                $("#valor_parametro_plantilla_informe_" + id_parametro).val(valor_parametro);
                if ($("#valor_parametro_plantilla_informe_" + id_parametro).val() === undefined) {
                    $("#valor_parametro_plantilla_informe_" + id_parametro).val(ID_NINGUNO);
                }
                $("#valor_parametro_plantilla_informe_" + id_parametro).trigger("chosen:updated");
            }
        });
    }
};


var realiza_acciones_recarga_controles_parametros_informe_plantilla_informe = function(html_controles) {
    if (html_controles != "") {
        $("#parametros-plantillas-informes").html(html_controles);
        $("#cabecera-parametros-plantillas-informes").show();
        $("#parametros-plantillas-informes").show();
        convierte_desplegables_chosen();

        // Identificadores de parametros actuales
        var cadena_ids_parametros_actuales = $("#parametros_informe_plantilla_informe").attr("ids_parametros");
        var ids_parametros_actuales = cadena_ids_parametros_actuales.split(SEPARADOR_PARAMETROS_SIMPLES);

        // Nota: Si se asigna una función al evento 'change' de un 'select' que es un 'chosen' no funciona correctamente (si se hace por clase asignada al 'select')
        // // - Se asigna el evento directamente al identificador del 'select' original (sin el sufijo '_chosen')
        for (var i = 0; i < ids_parametros_actuales.length; i++) {
            var id_parametro = ids_parametros_actuales[i];
            $("#valor_parametro_plantilla_informe_" + id_parametro).change(funcion_realiza_acciones_valor_parametro_plantilla_informe_sensor_modificada);
        }
    }
    else {
        $("#cabecera-parametros-plantillas-informes").hide();
        $("#parametros-plantillas-informes").hide();
        $("#parametros-plantillas-informes").html("");
    }
};


var realiza_acciones_recarga_controles_subtitulos_portadas_informe_plantilla_informe = function(html_controles) {
    if (html_controles != "") {
        $("#subtitulos-portadas-plantillas-informes").html(html_controles);
        $("#cabecera-subtitulos-portadas-plantillas-informes").show();
        if ($("#cabecera-subtitulos-portadas-plantillas-informes").attr("desplegado") == true) {
            $("#subtitulos-portadas-plantillas-informes").show();
        }
    }
    else {
        $("#cabecera-subtitulos-portadas-plantillas-informes").hide();
        $("#subtitulos-portadas-plantillas-informes").hide();
        $("#subtitulos-portadas-plantillas-informes").html("");
    }
};


var realiza_acciones_recarga_controles_titulos_informe_plantilla_informe = function(html_controles) {
    if (html_controles != "") {
        $("#titulos-plantillas-informes").html(html_controles);
        $("#cabecera-titulos-plantillas-informes").show();
        if ($("#cabecera-titulos-plantillas-informes").attr("desplegado") == true) {
            $("#titulos-plantillas-informes").show();
        }
    }
    else {
        $("#cabecera-titulos-plantillas-informes").hide();
        $("#titulos-plantillas-informes").hide();
        $("#titulos-plantillas-informes").html("");
    }
};


var realiza_acciones_recarga_controles_textos_informe_plantilla_informe = function(html_controles) {
    if (html_controles != "") {
        $("#textos-plantillas-informes").html(html_controles);
        $("#cabecera-textos-plantillas-informes").show();
        if ($("#cabecera-textos-plantillas-informes").attr("desplegado") == true) {
            $("#textos-plantillas-informes").show();
        }

        // Ajuste de textos y evento de contador de caracteres
        TLNT.Navegacion.redimensiona_textarea(".area-texto-informe");
        $(".area-texto-informe").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
    }
    else {
        $("#cabecera-textos-plantillas-informes").hide();
        $("#textos-plantillas-informes").hide();
        $("#textos-plantillas-informes").html("");
    }
};


var realiza_acciones_recarga_controles_imagenes_informe_plantilla_informe = function(html_controles) {
    if (html_controles != "") {
        $("#imagenes-plantillas-informes").html(html_controles);
        $("#cabecera-imagenes-plantillas-informes").show();
        if ($("#cabecera-imagenes-plantillas-informes").attr("desplegado") == true) {
            $("#imagenes-plantillas-informes").show();
        }

        $('#imagenes-personal-plantilla-informe').children().each(function () {
            // - Selección de fichero de imagen
            var params = this.id.split('__');
            var id_elemento_imagen = params[1];

            $('#fichero_imagen_plantilla_informe_text_' + id_elemento_imagen).show(function() {
                $('#fichero_imagen_plantilla_informe_file_' + id_elemento_imagen).hide();
            });
            $('#fichero_imagen_plantilla_informe_file_' + id_elemento_imagen).change(function() {
                var fichero = $(this).val().split('\\').pop();
                $('#fichero_imagen_plantilla_informe_text_' + id_elemento_imagen).val(fichero);
                $('#nombre_imagen_plantilla_informe_' + id_elemento_imagen).hide();
                $('#fichero_imagen_plantilla_informe_text_' + id_elemento_imagen).show();
                $('#boton_imagen_plantilla_informe_deseleccionar_fichero_imagen_' + id_elemento_imagen).show();
            });
            $('#boton_imagen_plantilla_informe_seleccionar_fichero_imagen_' + id_elemento_imagen).click(function() {
                $('#fichero_imagen_plantilla_informe_file_' + id_elemento_imagen).val("");
                $('#fichero_imagen_plantilla_informe_file_' + id_elemento_imagen).click();
            });
            $('#boton_imagen_plantilla_informe_deseleccionar_fichero_imagen_' + id_elemento_imagen).click(function() {
                $('#fichero_imagen_plantilla_informe_text_' + id_elemento_imagen).val("");
                $('#fichero_imagen_plantilla_informe_text_' + id_elemento_imagen).hide();
                $("#nombre_imagen_plantilla_informe_" + id_elemento_imagen).removeClass('data-check-failed');
                $('#nombre_imagen_plantilla_informe_' + id_elemento_imagen).show();
                $('#boton_imagen_plantilla_informe_deseleccionar_fichero_imagen_' + id_elemento_imagen).hide();
            });
            var nombre_imagen = $('#nombre_imagen_plantilla_informe_' + id_elemento_imagen).val();
            if (nombre_imagen != "") {
                $('#fichero_imagen_plantilla_informe_text_' + id_elemento_imagen).hide();
                $('#boton_imagen_plantilla_informe_deseleccionar_fichero_imagen_' + id_elemento_imagen).hide();
            }
        });
    }
    else {
        $("#cabecera-imagenes-plantillas-informes").hide();
        $("#imagenes-plantillas-informes").hide();
        $("#imagenes-plantillas-informes").html("");
    }
};
