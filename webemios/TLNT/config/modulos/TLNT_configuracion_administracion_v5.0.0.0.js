// Eventos de los botones (por funcionalidad)
TLNT.Navegacion.botones_secciones_administracion = [
    {	selector: '#boton_administracion_filtro_redes_tabla',
		funcion: 	boton_administracion_filtro_redes_tabla
	},
    {	selector: '#boton_administracion_filtro_usuarios_tabla',
		funcion: 	boton_administracion_filtro_usuarios_tabla
	},
    {	selector: '#boton_administracion_seleccionar_red_actual',
		funcion: 	boton_administracion_seleccionar_red_actual
	}
];


TLNT.Navegacion.botones_tablas_datos_administracion = [
    // Tabla de clientes
    {	selector: '.boton_administracion_mostrar_ventana_anyadir_modificar_cliente',
		funcion: 	boton_administracion_mostrar_ventana_anyadir_modificar_cliente
	},
    {	selector: '.boton_administracion_actualizar_tabla_clientes',
		funcion: 	boton_administracion_actualizar_tabla_clientes
	},
    {	selector: '.boton_administracion_eliminar_cliente',
		funcion: 	boton_administracion_eliminar_cliente
	},
    // Tabla de licencias de módulos
    {	selector: '.boton_administracion_mostrar_ventana_anyadir_modificar_licencia',
		funcion: 	boton_administracion_mostrar_ventana_anyadir_modificar_licencia
	},
    {	selector: '.boton_administracion_actualizar_tabla_licencias',
		funcion: 	boton_administracion_actualizar_tabla_licencias
	},
    {	selector: '.boton_administracion_eliminar_licencia',
		funcion: 	boton_administracion_eliminar_licencia
	},
    // Tabla de preferencias
    {	selector: '.boton_administracion_mostrar_ventana_anyadir_modificar_preferencias',
		funcion: 	boton_administracion_mostrar_ventana_anyadir_modificar_preferencias
	},
    {	selector: '.boton_administracion_actualizar_tabla_preferencias',
		funcion: 	boton_administracion_actualizar_tabla_preferencias
	},
    {	selector: '.boton_administracion_eliminar_preferencias',
		funcion: 	boton_administracion_eliminar_preferencias
	},
    // Tabla de usuarios
    {	selector: '.boton_administracion_mostrar_ventana_anyadir_modificar_usuario',
		funcion: 	boton_administracion_mostrar_ventana_anyadir_modificar_usuario
	},
    {	selector: '.boton_administracion_actualizar_tabla_usuarios',
		funcion: 	boton_administracion_actualizar_tabla_usuarios
	},
    {	selector: '.boton_administracion_eliminar_usuario',
		funcion: 	boton_administracion_eliminar_usuario
	},
    // Ayuda (tablas)
    {	selector: '.boton_administracion_ayuda_tabla_usuarios',
		funcion: 	boton_administracion_ayuda_tabla_usuarios
	}
];


TLNT.Navegacion.botones_ventanas_modales_administracion = [
    // Clientes
    {	selector: '.boton_administracion_anyadir_modificar_cliente',
		funcion: 	boton_administracion_anyadir_modificar_cliente
	},
    // Redes
    {	selector: '.boton_modificar_red_parcial',
		funcion: 	boton_modificar_red_parcial
	},
    // Licencias de módulos
    {	selector: '.boton_administracion_anyadir_modificar_licencia',
		funcion: 	boton_administracion_anyadir_modificar_licencia
	},
    // Preferencias
    {	selector: '.boton_administracion_anyadir_modificar_preferencias',
		funcion: 	boton_administracion_anyadir_modificar_preferencias
	},
    // Usuarios
    {	selector: '.boton_administracion_anyadir_modificar_usuario',
		funcion: 	boton_administracion_anyadir_modificar_usuario
	},
    // Ayuda (administración de usuarios)
    {	selector: '#boton_administracion_ayuda_parametros_accion_inicial_usuario',
		funcion: 	boton_administracion_ayuda_parametros_accion_inicial_usuario
	}
];


//
// Funciones de establecimiento de eventos (por funcionalidad)
//


TLNT.Navegacion.establece_eventos_secciones_administracion = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_secciones_administracion);
};


TLNT.Navegacion.establece_eventos_tablas_datos_administracion = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_administracion);
};


TLNT.Navegacion.establece_eventos_detalles_tablas_datos_administracion = function() {};


TLNT.Navegacion.establece_eventos_ventanas_modales_administracion = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_ventanas_modales_administracion);

    establece_eventos_ventanas_modales_administracion_redes();
    establece_eventos_ventanas_modales_administracion_usuarios();
    establece_eventos_ventanas_modales_administracion_preferencias();
};


//
// Funciones auxiliares para establecer las acciones de los controles
//


establece_eventos_ventanas_modales_administracion_redes = function() {
    // Desactivación de eventos anteriores
    $("#logo_personalizado_red").off();
    $("#tipo_mapa_red").off();

    // Ventana de administración de red:
    // - Selección de fichero de logo
    $("#fichero_logo_red_text").show(function() {
        $('#fichero_logo_red_file').hide();
    });
    $('#fichero_logo_red_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_logo_red_text').val(fichero);
    });
    $('#boton_anyadir_modificar_red_seleccionar_fichero_logo').click(function() {
        $('#fichero_logo_red_file').click();
    });
    // - Selección de fichero de logo PDF
    $("#fichero_logo_pdf_red_text").show(function() {
        $('#fichero_logo_pdf_red_file').hide();
    });
    $('#fichero_logo_pdf_red_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_logo_pdf_red_text').val(fichero);
    });
    $('#boton_anyadir_modificar_red_seleccionar_fichero_logo_pdf').click(function() {
        $('#fichero_logo_pdf_red_file').click();
    });
    // - Selección de fichero de imagen de mapa local
    $("#fichero_imagen_mapa_red_text").show(function() {
        $('#fichero_imagen_mapa_red_file').hide();
    });
    $('#fichero_imagen_mapa_red_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_imagen_mapa_red_text').val(fichero);
    });
    $('#boton_anyadir_modificar_red_seleccionar_fichero_imagen_mapa').click(function() {
        $('#fichero_imagen_mapa_red_file').click();
    });

    // Habilita y muestra los controles dependientes de si hay logo personalizado
    var funcion_habilita_muestra_controles_logo_personalizado_red = function() {
        var logo_personalizado = parseInt($("#logo_personalizado_red").val());
        switch (logo_personalizado) {
            case VALOR_NO: {
                $("#nombre_logo_red").removeClass('TLNT_input_mandatory');
                $("#control_nombre_logo_red").hide();
                $("#control_fichero_logo_red").hide();
                $("#control_fichero_logo_pdf_red").hide();
                $("#control_url_logo_red").hide();
                break;
            }
            case VALOR_SI: {
                $("#nombre_logo_red").addClass('TLNT_input_mandatory');
                $("#control_nombre_logo_red").show();
                $("#control_fichero_logo_red").show();
                $("#control_fichero_logo_pdf_red").show();
                $("#control_url_logo_red").show();
                break;
            }
        }
    };
    $("#logo_personalizado_red").show(funcion_habilita_muestra_controles_logo_personalizado_red);
    $("#logo_personalizado_red").change(funcion_habilita_muestra_controles_logo_personalizado_red);

    // Habilita y muestra los controles dependientes del tipo de mapa de red
    var funcion_habilita_muestra_controles_tipo_mapa_red = function() {
        var tipo_mapa = $("#tipo_mapa_red").val();
        switch (tipo_mapa) {
            case TIPO_MAPA_INTERNET: {
                $("#nombre_mapa_red").removeClass('TLNT_input_mandatory');
                $("#control_fichero_imagen_mapa_red").hide();
                $("#factor_reduccion_imagen_mapa_local_red").removeClass('TLNT_input_mandatory');
                $("#control_factor_reduccion_imagen_mapa_local_red").hide();
                break;
            }
            case TIPO_MAPA_LOCAL: {
                $("#nombre_mapa_red").addClass('TLNT_input_mandatory');
                $("#control_fichero_imagen_mapa_red").show();
                $("#factor_reduccion_imagen_mapa_local_red").addClass('TLNT_input_mandatory');
                $("#control_factor_reduccion_imagen_mapa_local_red").show();
                break;
            }
        }
    };
    $("#tipo_mapa_red").show(funcion_habilita_muestra_controles_tipo_mapa_red);
    $("#tipo_mapa_red").change(funcion_habilita_muestra_controles_tipo_mapa_red);
};


establece_eventos_ventanas_modales_administracion_usuarios = function() {
    // Desactivación de eventos anteriores
    $("#perfil_usuario").off();
    $("#modulo_licencia").off();
    $("#modulo_defecto_usuario").off();
    $("#seccion_defecto_usuario").off();
    $("#accion_inicial_usuario").off();

    // Mostrar lista doble en la ventana de administración de usuarios
    $("#contenido_modal").on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var tab = $(e.target);
        var id_pestanya = tab.attr("href");

        // Redes del usuario
        if (id_pestanya == "#tab-redes-usuario") {
            if ($('#select_redes_usuario_no_visible').length) {
                $('#select_redes_usuario_no_visible').attr("id", "select_redes_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_redes_usuario", true);
            }
        }

        // Módulos del usuario
        if (id_pestanya == "#tab-licencias-usuario") {
            if ($('#select_licencias_usuario_no_visible').length) {
                $('#select_licencias_usuario_no_visible').attr("id", "select_licencias_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_licencias_usuario", false);
            }
        }

        // Secciones de los módulos
        if (id_pestanya == "#tab-secciones-usuario") {
            if ($('#select_secciones_personal_usuario_no_visible').length) {
                $('#select_secciones_personal_usuario_no_visible').attr("id", "select_secciones_personal_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_secciones_personal_usuario", false);
            }
            if ($('#select_secciones_red_usuario_no_visible').length) {
                $('#select_secciones_red_usuario_no_visible').attr("id", "select_secciones_red_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_secciones_red_usuario", false);
            }
            if ($('#select_secciones_localizaciones_usuario_no_visible').length) {
                $('#select_secciones_localizaciones_usuario_no_visible').attr("id", "select_secciones_localizaciones_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_secciones_localizaciones_usuario", false);
            }
            if ($('#select_secciones_sensores_usuario_no_visible').length) {
                $('#select_secciones_sensores_usuario_no_visible').attr("id", "select_secciones_sensores_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_secciones_sensores_usuario", false);
            }
            if ($('#select_secciones_actuadores_usuario_no_visible').length) {
                $('#select_secciones_actuadores_usuario_no_visible').attr("id", "select_secciones_actuadores_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_secciones_actuadores_usuario", false);
            }
            if ($('#select_secciones_smartmeter_usuario_no_visible').length) {
                $('#select_secciones_smartmeter_usuario_no_visible').attr("id", "select_secciones_smartmeter_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_secciones_smartmeter_usuario", false);
            }
            if ($('#select_secciones_proyectos_usuario_no_visible').length) {
                $('#select_secciones_proyectos_usuario_no_visible').attr("id", "select_secciones_proyectos_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_secciones_proyectos_usuario", false);
            }
        }

        // Localizaciones del usuario
        if (id_pestanya == "#tab-localizaciones-usuario") {
            if ($('#select_localizaciones_usuario_no_visible').length) {
                $('#select_localizaciones_usuario_no_visible').attr("id", "select_localizaciones_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_localizaciones_usuario", true);
            }
        }

        // Sensores y grupos de sensores del usuario
        if (id_pestanya == "#tab-sensores-usuario") {
            if ($('#select_sensores_usuario_no_visible').length) {
                $('#select_sensores_usuario_no_visible').attr("id", "select_sensores_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_sensores_usuario", true);
            }
            if ($('#select_grupos_sensores_usuario_no_visible').length) {
                $('#select_grupos_sensores_usuario_no_visible').attr("id", "select_grupos_sensores_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_grupos_sensores_usuario", true);
            }
        }

        // Actuadores y grupos de actuadores del usuario
        if (id_pestanya == "#tab-actuadores-usuario") {
            if ($('#select_actuadores_usuario_no_visible').length) {
                $('#select_actuadores_usuario_no_visible').attr("id", "select_actuadores_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_actuadores_usuario", true);
            }
            if ($('#select_grupos_actuadores_usuario_no_visible').length) {
                $('#select_grupos_actuadores_usuario_no_visible').attr("id", "select_grupos_actuadores_usuario_visible");
                TLNT.Navegacion.convierte_lista_doble("ids_grupos_actuadores_usuario", true);
            }
        }
    });

    // Habilita y muestra los controles dependientes del perfil de usuario
    var funcion_habilita_muestra_controles_perfil_usuario = function() {
        var perfil_usuario = $("#perfil_usuario").val();
        var perfil_actual = TLNT.Navegacion.perfil_actual;
        var id_red_actual = $("#parametros_ventana_anyadir_modificar_usuario").attr("id_red_actual");
        var anyadir_usuario = $("#parametros_ventana_anyadir_modificar_usuario").attr("anyadir_usuario");
        if (anyadir_usuario == true) {
            switch (perfil_actual) {
                case PERFIL_USUARIO_ADMINISTRADOR: {
                    $("#perfil_usuario").prop('disabled', 'disabled');
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR: {
                    $("#perfil_usuario").prop('disabled', 'disabled');
                    break;
                }
            }
        }
        else {
            var id_usuario_habilitado = true;
            switch (perfil_actual) {
                case PERFIL_USUARIO_ESTANDAR:
                case PERFIL_USUARIO_ADMINISTRADOR: {
                    if (perfil_usuario == perfil_actual) {
                        id_usuario_habilitado = false;
                    }
                    break;
                }
            }
            if (id_usuario_habilitado == false) {
                $("#id_usuario").prop('disabled', 'disabled');
           }
        }

        switch (perfil_usuario) {
            case PERFIL_USUARIO_ESTANDAR: {
                $("#control_api_http_usuario").show();
                if (perfil_actual == PERFIL_USUARIO_ESTANDAR) {
                    $("#titulo-tab-redes-usuario").hide();
                    $("#titulo-tab-licencias-usuario").hide();
                    $("#titulo-tab-secciones-usuario").hide();
                    $("#titulo-tab-personal-usuario").hide();
                    $("#titulo-tab-localizaciones-usuario").hide();
                    $("#titulo-tab-sensores-usuario").hide();
                    $("#titulo-tab-actuadores-usuario").hide();
                    $("#api_http_usuario").prop('disabled', 'disabled');
                }
                else {
                    if (id_red_actual == ID_NINGUNO) {
                        $("#titulo-tab-redes-usuario").show();
                        $("#titulo-tab-licencias-usuario").hide();
                        $("#titulo-tab-secciones-usuario").hide();
                        $("#titulo-tab-personal-usuario").hide();
                        $("#titulo-tab-localizaciones-usuario").hide();
                        $("#titulo-tab-sensores-usuario").hide();
                        $("#titulo-tab-actuadores-usuario").hide();
                    }
                    else {
                        $("#titulo-tab-redes-usuario").hide();
                        $("#titulo-tab-licencias-usuario").show();
                        $("#titulo-tab-secciones-usuario").show();
                        if ($("#titulo-tab-personal-usuario").hasClass(MODULO_DISPONIBLE) == true) {
                            $("#titulo-tab-personal-usuario").show();
                        }
                        else {
                            $("#titulo-tab-personal-usuario").hide();
                        }
                        if ($("#titulo-tab-localizaciones-usuario").hasClass(MODULO_DISPONIBLE) == true) {
                            $("#titulo-tab-localizaciones-usuario").show();
                        }
                        else {
                            $("#titulo-tab-localizaciones-usuario").hide();
                        }
                        if ($("#titulo-tab-sensores-usuario").hasClass(MODULO_DISPONIBLE) == true) {
                            $("#titulo-tab-sensores-usuario").show();
                        }
                        else {
                            $("#titulo-tab-sensores-usuario").hide();
                        }
                        if ($("#titulo-tab-actuadores-usuario").hasClass(MODULO_DISPONIBLE) == true) {
                            $("#titulo-tab-actuadores-usuario").show();
                        }
                        else {
                            $("#titulo-tab-actuadores-usuario").hide();
                        }
                    }
                }
                break;
            }
            case PERFIL_USUARIO_ADMINISTRADOR: {
                $("#control_api_http_usuario").hide();
                if ((perfil_actual == PERFIL_USUARIO_SUPERADMINISTRADOR) && (id_red_actual == ID_NINGUNO)) {
                    $("#titulo-tab-redes-usuario").show();
                }
                else {
                    $("#titulo-tab-redes-usuario").hide();
                }
                $("#titulo-tab-licencias-usuario").hide();
                $("#titulo-tab-secciones-usuario").hide();
                $("#titulo-tab-personal-usuario").hide();
                $("#titulo-tab-localizaciones-usuario").hide();
                $("#titulo-tab-sensores-usuario").hide();
                $("#titulo-tab-actuadores-usuario").hide();
                break;
            }
            case PERFIL_USUARIO_SUPERADMINISTRADOR: {
                $("#control_api_http_usuario").hide();
                $("#titulo-tab-redes-usuario").hide();
                $("#titulo-tab-licencias-usuario").hide();
                $("#titulo-tab-secciones-usuario").hide();
                $("#titulo-tab-personal-usuario").hide();
                $("#titulo-tab-localizaciones-usuario").hide();
                $("#titulo-tab-sensores-usuario").hide();
                $("#titulo-tab-actuadores-usuario").hide();
                break;
            }
        }

        // Si el usuario es estándar
        if (perfil_actual == PERFIL_USUARIO_ESTANDAR) {
            // Se oculta la pestaña de parámetros de módulos si no hay controles visibles
            var control_modo_seleccion_localizacion_visible = parseInt($("#parametros_ventana_anyadir_modificar_usuario").attr("control_modo_seleccion_localizacion_visible"));
            var mostrar_parametros_usuario_unica_red = parseInt($("#parametros_ventana_anyadir_modificar_usuario").attr("mostrar_parametros_usuario_unica_red"));
            if ((control_modo_seleccion_localizacion_visible == VALOR_NO) && (mostrar_parametros_usuario_unica_red == VALOR_NO)) {
                $("#titulo-tab-preferencias-modulos-usuario").hide();
            }

            // Se oculta la pestña de HTTP API si es usuario estándar y no tiene permisos de HTTP API
            var api_http_usuario_habilitado = $("#api_http_usuario").val();
            if (api_http_usuario_habilitado == VALOR_NO) {
                $("#titulo-tab-api-usuario").hide();
            }
        }
    };
    $("#perfil_usuario").show(funcion_habilita_muestra_controles_perfil_usuario);
    $("#perfil_usuario").change(funcion_habilita_muestra_controles_perfil_usuario);

    // Habilita y muestra los controles dependientes del módulo de licencia
    var funcion_habilita_muestra_controles_modulo_licencia = function() {
        var modulo_licencia = $("#modulo_licencia").val();
        switch (modulo_licencia) {
            case ID_NINGUNO.toString(): {
                $("#control_numero_maximo_elementos_licencia").hide();
                break;
            }
            case MODULO_PERSONAL:
            case MODULO_LOCALIZACIONES:
            case MODULO_SMARTMETER: {
                $("#control_numero_maximo_elementos_licencia").hide();
                $("#numero_maximo_elementos_licencia").val("0");
                break;
            }
            case MODULO_RED:
            case MODULO_SENSORES:
            case MODULO_ACTUADORES:
            case MODULO_PROYECTOS:
            {
                $("#control_numero_maximo_elementos_licencia").show();
                break;
            }
        }
    };
    $("#modulo_licencia").show(funcion_habilita_muestra_controles_modulo_licencia);
    $("#modulo_licencia").change(funcion_habilita_muestra_controles_modulo_licencia);

    // Habilita y muestra los controles dependientes del permiso de todas las localizaciones
    var funcion_habilita_muestra_controles_permiso_todas_localizaciones = function() {
        var permiso_todas_localizaciones = $("#permiso_todas_localizaciones_usuario").val();
        if (permiso_todas_localizaciones == VALOR_SI) {
            $("#control_ids_localizaciones_usuario").hide();
        }
        else {
            $("#control_ids_localizaciones_usuario").show();
        }
    };
    $("#permiso_todas_localizaciones_usuario").show(funcion_habilita_muestra_controles_permiso_todas_localizaciones);
    $("#permiso_todas_localizaciones_usuario").change(funcion_habilita_muestra_controles_permiso_todas_localizaciones);

    // Habilita y muestra los controles dependientes del permiso de todos los sensores
    var funcion_habilita_muestra_controles_permiso_todos_sensores = function() {
        var permiso_todos_sensores = $("#permiso_todos_sensores_usuario").val();
        if (permiso_todos_sensores == VALOR_SI) {
            $("#control_ids_sensores_usuario").hide();
            $("#control_ids_grupos_sensores_usuario").hide();
        }
        else {
            $("#control_ids_sensores_usuario").show();
            $("#control_ids_grupos_sensores_usuario").show();
        }
    };
    $("#permiso_todos_sensores_usuario").show(funcion_habilita_muestra_controles_permiso_todos_sensores);
    $("#permiso_todos_sensores_usuario").change(funcion_habilita_muestra_controles_permiso_todos_sensores);

    // Habilita y muestra los controles dependientes de administracion de sensores
    var funcion_habilita_muestra_controles_administracion_sensores = function() {
        var administracion_sensores = $("#administracion_sensores_usuario").val();
        if (administracion_sensores == VALOR_SI) {
            $("#control_administracion_comentarios_sensores_usuario").hide();
            $("#control_lectura_sensores_usuario").hide();
            $("#control_exportacion_sensores_usuario").hide();
            $("#control_administracion_eventos_usuario").hide();
            $("#control_envio_valores_manuales_sensores_usuario").hide();
        }
        else {
            $("#control_administracion_comentarios_sensores_usuario").show();
            $("#control_lectura_sensores_usuario").show();
            $("#control_exportacion_sensores_usuario").show();
            $("#control_administracion_eventos_usuario").show();
            $("#control_envio_valores_manuales_sensores_usuario").show();
        }
    };
    $("#administracion_sensores_usuario").show(funcion_habilita_muestra_controles_administracion_sensores);
    $("#administracion_sensores_usuario").change(funcion_habilita_muestra_controles_administracion_sensores);

    // Habilita y muestra los controles dependientes de todos los actuadores
    var funcion_habilita_muestra_controles_permiso_todos_actuadores = function() {
        var permiso_todos_actuadores = $("#permiso_todos_actuadores_usuario").val();
        if (permiso_todos_actuadores == VALOR_SI) {
            $("#control_ids_actuadores_usuario").hide();
            $("#control_ids_grupos_actuadores_usuario").hide();
        }
        else {
            $("#control_ids_actuadores_usuario").show();
            $("#control_ids_grupos_actuadores_usuario").show();
        }
    };
    $("#permiso_todos_actuadores_usuario").show(funcion_habilita_muestra_controles_permiso_todos_actuadores);
    $("#permiso_todos_actuadores_usuario").change(funcion_habilita_muestra_controles_permiso_todos_actuadores);

    // Habilita y muestra los controles dependientes de administracion de actuadores
    var funcion_habilita_muestra_controles_administracion_actuadores = function() {
        var administracion_actuadores = $("#administracion_actuadores_usuario").val();
        if (administracion_actuadores == VALOR_SI) {
            $("#control_administracion_comentarios_actuadores_usuario").hide();
            $("#control_acciones_actuadores_usuario").hide();
            $("#control_administracion_programaciones_usuario").hide();
            $("#control_administracion_reglas_usuario").hide();
        }
        else {
            $("#control_administracion_comentarios_actuadores_usuario").show();
            $("#control_acciones_actuadores_usuario").show();
            $("#control_administracion_programaciones_usuario").show();
            $("#control_administracion_reglas_usuario").show();
        }
    };
    $("#administracion_actuadores_usuario").show(funcion_habilita_muestra_controles_administracion_actuadores);
    $("#administracion_actuadores_usuario").change(funcion_habilita_muestra_controles_administracion_actuadores);

    // Habilitación de sección por defecto
    var funcion_habilita_lista_secciones_defecto = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_secciones = $("select#seccion_defecto_usuario" + " option").length;
        if (numero_secciones <= 1) {
            $("#seccion_defecto_usuario").attr('disabled', true);
        }
        else {
            $("#seccion_defecto_usuario").removeAttr('disabled');
        }
    };
    $("#seccion_defecto_usuario").show(funcion_habilita_lista_acciones_iniciales);

    // Recarga de las secciones del módulo por defecto
    var funcion_recarga_secciones_modulo_defecto = function() {
        var id_usuario = $("#parametros_ventana_anyadir_modificar_usuario").attr("id_usuario");
        var perfil_usuario = $("#parametros_ventana_anyadir_modificar_usuario").attr("perfil_usuario");
        var id_red_usuario = $("#parametros_ventana_anyadir_modificar_usuario").attr("id_red_usuario");
        var modulo_defecto = $("#modulo_defecto_usuario").val();
        $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/dame_lista_secciones_modulo_defecto_usuario.php", {
            id_usuario: id_usuario,
            perfil_usuario: perfil_usuario,
            id_red: id_red_usuario,
            modulo: modulo_defecto
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#seccion_defecto_usuario").html(resultado.html);
            $("#seccion_defecto_usuario").trigger('change');
            funcion_habilita_lista_secciones_defecto();
        });
    };
    $("#modulo_defecto_usuario").change(funcion_recarga_secciones_modulo_defecto);

    // Habilitación de accion inicial
    var funcion_habilita_lista_acciones_iniciales = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_acciones = $("select#accion_inicial_usuario" + " option").length;
        if (numero_acciones <= 1) {
            $("#accion_inicial_usuario").attr('disabled', true);
        }
        else {
            $("#accion_inicial_usuario").removeAttr('disabled');
        }
    };
    $("#accion_inicial_usuario").show(funcion_habilita_lista_acciones_iniciales);

    // Recarga de las acciones iniciales del módulo y acción por defecto
    var funcion_recarga_acciones_iniciales = function() {
        var modulo_defecto = $("#modulo_defecto_usuario").val();
        var seccion_defecto = $("#seccion_defecto_usuario").val();
        $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/dame_lista_acciones_iniciales_modulo_seccion.php", {
            modulo: modulo_defecto,
            seccion: seccion_defecto
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#accion_inicial_usuario").html(resultado.html);
            $("#accion_inicial_usuario").trigger('change');
            funcion_habilita_lista_acciones_iniciales();
        });
    };
    $("#seccion_defecto_usuario").change(funcion_recarga_acciones_iniciales);

    // Habilita y muestra los controles dependientes de la acción inicial
    var funcion_habilita_muestra_controles_accion_inicial = function() {
        var accion_inicial = $("#accion_inicial_usuario").val();
        if (accion_inicial == ID_NINGUNO) {
            $("#control_parametros_accion_inicial_usuario").hide();
        }
        else {
            $("#control_parametros_accion_inicial_usuario").show();
        }
    };
    $("#accion_inicial_usuario").show(funcion_habilita_muestra_controles_accion_inicial);
    $("#accion_inicial_usuario").change(funcion_habilita_muestra_controles_accion_inicial);
};


establece_eventos_ventanas_modales_administracion_preferencias = function() {
    // Desactivación de eventos anteriores
    $("#logo_personalizado_preferencias").off();

    // Ventana de administración de preferencias
    // - Selección de fichero de logo
    $("#fichero_logo_preferencias_text").show(function() {
        $('#fichero_logo_preferencias_file').hide();
    });
    $('#fichero_logo_preferencias_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_logo_preferencias_text').val(fichero);
    });
    $('#boton_anyadir_modificar_preferencias_seleccionar_fichero_logo').click(function() {
        $('#fichero_logo_preferencias_file').click();
    });
    // - Selección de fichero de logo PDF
    $("#fichero_logo_pdf_preferencias_text").show(function() {
        $('#fichero_logo_pdf_preferencias_file').hide();
    });
    $('#fichero_logo_pdf_preferencias_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_logo_pdf_preferencias_text').val(fichero);
    });
    $('#boton_anyadir_modificar_preferencias_seleccionar_fichero_logo_pdf').click(function() {
        $('#fichero_logo_pdf_preferencias_file').click();
    });

    // Habilita y muestra los controles dependientes de si hay logo personalizado
    var funcion_habilita_muestra_controles_logo_personalizado_preferencias = function() {
        var logo_personalizado = parseInt($("#logo_personalizado_preferencias").val());
        switch (logo_personalizado) {
            case VALOR_NO: {
                $("#nombre_logo_preferencias").removeClass('TLNT_input_mandatory');
                $("#control_nombre_logo_preferencias").hide();
                $("#control_fichero_logo_preferencias").hide();
                $("#control_fichero_logo_pdf_preferencias").hide();
                $("#control_url_logo_preferencias").hide();
                break;
            }
            case VALOR_SI: {
                $("#nombre_logo_preferencias").addClass('TLNT_input_mandatory');
                $("#control_nombre_logo_preferencias").show();
                $("#control_fichero_logo_preferencias").show();
                $("#control_fichero_logo_pdf_preferencias").show();
                $("#control_url_logo_preferencias").show();
                break;
            }
        }
    };
    $("#logo_personalizado_preferencias").show(funcion_habilita_muestra_controles_logo_personalizado_preferencias);
    $("#logo_personalizado_preferencias").change(funcion_habilita_muestra_controles_logo_personalizado_preferencias);
};



