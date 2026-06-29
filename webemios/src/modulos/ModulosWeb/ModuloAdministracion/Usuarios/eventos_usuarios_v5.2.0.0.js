//
// Funciones de usuarios
//


function boton_administracion_filtro_usuarios_tabla() {
    boton_administracion_actualizar_tabla_usuarios();
}


function boton_administracion_mostrar_ventana_anyadir_modificar_usuario(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_usuario = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/muestra_ventana_anyadir_modificar_usuario.php", {
		id_usuario: id_usuario,
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


function boton_administracion_eliminar_usuario(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_usuario = params[1];

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el usuario?") + "\n(" + escapeHtml(id_usuario) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/elimina_usuario.php", {
				id_usuario: id_usuario
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
				actualiza_tabla_usuarios();
			});
		}
	});
}


function boton_administracion_anyadir_modificar_usuario() {
    // Comprobación de datos correctos de las pestañas visibles
    var perfil_actual = TLNT.Navegacion.perfil_actual;
    var id_red_actual = $("#parametros_ventana_anyadir_modificar_usuario").attr("id_red_actual");
    var perfil = $('#perfil_usuario').val();
    var ids_pestanyas_visibles = ["tab-principal", "tab-preferencias"];
    switch (perfil) {
        case PERFIL_USUARIO_ESTANDAR: {
            if (perfil_actual != PERFIL_USUARIO_ESTANDAR) {
                if (id_red_actual != ID_NINGUNO) {
                    ids_pestanyas_visibles.push("tab-licencias-usuario");
                    ids_pestanyas_visibles.push("tab-secciones-usuario");
                    if ($("#titulo-tab-personal-usuario").hasClass(MODULO_DISPONIBLE) == true) {
                        ids_pestanyas_visibles.push("tab-personal-usuario");
                    };
                    if ($("#titulo-tab-localizaciones-usuario").hasClass(MODULO_DISPONIBLE) == true) {
                        ids_pestanyas_visibles.push("tab-localizaciones-usuario");
                    };
                    if ($("#titulo-tab-sensores-usuario").hasClass(MODULO_DISPONIBLE) == true) {
                        ids_pestanyas_visibles.push("tab-sensores-usuario");
                    };
                    if ($("#titulo-tab-actuadores-usuario").hasClass(MODULO_DISPONIBLE) == true) {
                        ids_pestanyas_visibles.push("tab-actuadores-usuario");
                    };
                }
                else {
                    ids_pestanyas_visibles.push("tab-redes-usuario");
                }
            }
            break;
        }
        case PERFIL_USUARIO_ADMINISTRADOR: {
            ids_pestanyas_visibles.push("tab-redes-usuario");
            break;
        }
        case PERFIL_USUARIO_SUPERADMINISTRADOR: {
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

    // Parámetros de la ventana
    var anyadir_usuario = $("#parametros_ventana_anyadir_modificar_usuario").attr("anyadir_usuario");
    var id_usuario_anterior = $("#parametros_ventana_anyadir_modificar_usuario").attr("id_usuario");
    var cadena_redes_anteriores = $("#parametros_ventana_anyadir_modificar_usuario").attr("cadena_redes_usuario");
    var cadena_parametros_modulo_personal_anteriores = $("#parametros_ventana_anyadir_modificar_usuario").attr("cadena_parametros_modulo_personal_usuario");
    var cadena_parametros_modulo_localizaciones_anteriores = $("#parametros_ventana_anyadir_modificar_usuario").attr("cadena_parametros_modulo_localizaciones_usuario");
    var cadena_parametros_modulo_sensores_anteriores = $("#parametros_ventana_anyadir_modificar_usuario").attr("cadena_parametros_modulo_sensores_usuario");
    var cadena_parametros_modulo_actuadores_anteriores = $("#parametros_ventana_anyadir_modificar_usuario").attr("cadena_parametros_modulo_actuadores_usuario");

    // Se comprueba que las contraseñas introducidas sean iguales
    var contrasenya = $('#contrasenya_usuario').val();
    var comprobacion_contrasenya = $('#comprobacion_contrasenya_usuario').val();
    if (contrasenya != comprobacion_contrasenya) {
        jAlert(TLNT.Idiomas._("Las contraseñas introducidas no coinciden"));
        return;
    }

    // Se recuperan los identificadores de redes seleccionadas
    var ids_redes = [];
    if (ids_pestanyas_visibles.indexOf("tab-redes-usuario") != -1) {
        $("#ids_redes_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_redes.push($(this).val());
            }
        });
        if ((perfil == PERFIL_USUARIO_ESTANDAR) && (ids_redes.length == 0)) {
            jAlert(TLNT.Idiomas._("Los usuarios estándar deben tener al menos una red asignada"));
            return;
        }
    }

    // Se recuperan los identificadores de licencias seleccionadas
    var ids_licencias = [];
    if (ids_pestanyas_visibles.indexOf("tab-licencias-usuario") != -1) {
        $("#ids_licencias_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_licencias.push($(this).val());
            }
        });
    }

    // Se recuperan los identificadores de secciones seleccionadas
    var ids_secciones = {};
    if (ids_pestanyas_visibles.indexOf("tab-secciones-usuario") != -1) {
        var ids_secciones_personal = [];
        $("#ids_secciones_personal_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_secciones_personal.push($(this).val());
            }
        });
        if (ids_secciones_personal.length > 0) {
            ids_secciones[MODULO_PERSONAL] = ids_secciones_personal;
        }
        var ids_secciones_red = [];
        $("#ids_secciones_red_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_secciones_red.push($(this).val());
            }
        });
        if (ids_secciones_red.length > 0) {
            ids_secciones[MODULO_RED] = ids_secciones_red;
        }
        var ids_secciones_localizaciones = [];
        $("#ids_secciones_localizaciones_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_secciones_localizaciones.push($(this).val());
            }
        });
        if (ids_secciones_localizaciones.length > 0) {
            ids_secciones[MODULO_LOCALIZACIONES] = ids_secciones_localizaciones;
        }
        var ids_secciones_sensores = [];
        $("#ids_secciones_sensores_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_secciones_sensores.push($(this).val());
            }
        });
        if (ids_secciones_sensores.length > 0) {
            ids_secciones[MODULO_SENSORES] = ids_secciones_sensores;
        }
        var ids_secciones_actuadores = [];
        $("#ids_secciones_actuadores_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_secciones_actuadores.push($(this).val());
            }
        });
        if (ids_secciones_actuadores.length > 0) {
            ids_secciones[MODULO_ACTUADORES] = ids_secciones_actuadores;
        }
        var ids_secciones_smartmeter = [];
        $("#ids_secciones_smartmeter_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_secciones_smartmeter.push($(this).val());
            }
        });
        if (ids_secciones_smartmeter.length > 0) {
            ids_secciones[MODULO_SMARTMETER] = ids_secciones_smartmeter;
        }
        var ids_secciones_proyectos = [];
        $("#ids_secciones_proyectos_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_secciones_proyectos.push($(this).val());
            }
        });
        if (ids_secciones_proyectos.length > 0) {
            ids_secciones[MODULO_PROYECTOS] = ids_secciones_proyectos;
        }
    }

    // Se recuperan los parámetros del módulo Personal
    var parametros_modulo_personal = {};
    if (ids_pestanyas_visibles.indexOf("tab-personal-usuario") != -1) {
        var numero_maximo_informes_automaticos_personal = $('#numero_maximo_informes_automaticos_personal_usuario').val();
        if (PATRON_NUMERO_ENTERO.test(numero_maximo_informes_automaticos_personal) == false) {
            jAlert(TLNT.Idiomas._('El número máximo de informes automáticos debe ser -1, 0 o mayor que 0'));
            return;
        }
        var administracion_widgets = $('#administracion_widgets_usuario').val();
        var administracion_plantillas_informes = $('#administracion_plantillas_informes_usuario').val();
        var administracion_informes_automaticos = $('#administracion_informes_automaticos_usuario').val();
        var mostrar_otros_modulos = $('#mostrar_otros_modulos_usuario').val();
        parametros_modulo_personal["numero_maximo_informes_automaticos"] = numero_maximo_informes_automaticos_personal;
        parametros_modulo_personal["administracion_widgets"] = administracion_widgets;
        parametros_modulo_personal["administracion_plantillas_informes"] = administracion_plantillas_informes;
        parametros_modulo_personal["administracion_informes_automaticos"] = administracion_informes_automaticos;
        parametros_modulo_personal["mostrar_otros_modulos"] = mostrar_otros_modulos;
    }

    // Se recuperan los parámetros del módulo Localizaciones
    var parametros_modulo_localizaciones = {};
    if (ids_pestanyas_visibles.indexOf("tab-localizaciones-usuario") != -1) {
        var permiso_todas_localizaciones = $('#permiso_todas_localizaciones_usuario').val();
        var ids_localizaciones = [];
        $("#ids_localizaciones_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_localizaciones.push($(this).val());
            }
        });
        var administracion_localizaciones = $('#administracion_localizaciones_usuario').val();
        var administracion_instalaciones = $('#administracion_instalaciones_usuario').val();
        parametros_modulo_localizaciones["permiso_todas_localizaciones"] = permiso_todas_localizaciones;
        parametros_modulo_localizaciones["ids_localizaciones"] = ids_localizaciones;
        parametros_modulo_localizaciones["administracion_localizaciones"] = administracion_localizaciones;
        parametros_modulo_localizaciones["administracion_instalaciones"] = administracion_instalaciones;
    }

    // Se recuperan los parámetros del módulo Sensores
    var parametros_modulo_sensores = {};
    if (ids_pestanyas_visibles.indexOf("tab-sensores-usuario") != -1) {
        var permiso_todos_sensores = $('#permiso_todos_sensores_usuario').val();
        var ids_sensores = [];
        $("#ids_sensores_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_sensores.push($(this).val());
            }
        });
        var ids_grupos_sensores = [];
        $("#ids_grupos_sensores_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_grupos_sensores.push($(this).val());
            }
        });
        var administracion_sensores = $('#administracion_sensores_usuario').val();
        var administracion_comentarios_sensores = $('#administracion_comentarios_sensores_usuario').val();
        var lectura_sensores = $('#lectura_sensores_usuario').val();
        var exportacion_sensores = $('#exportacion_sensores_usuario').val();
        var administracion_eventos = $('#administracion_eventos_usuario').val();
        var envio_valores_manuales_sensores = $('#envio_valores_manuales_sensores_usuario').val();
        parametros_modulo_sensores["permiso_todos_sensores"] = permiso_todos_sensores;
        parametros_modulo_sensores["ids_sensores"] = ids_sensores;
        parametros_modulo_sensores["ids_grupos_sensores"] = ids_grupos_sensores;
        parametros_modulo_sensores["administracion_sensores"] = administracion_sensores;
        parametros_modulo_sensores["administracion_comentarios_sensores"] = administracion_comentarios_sensores;
        parametros_modulo_sensores["lectura_sensores"] = lectura_sensores;
        parametros_modulo_sensores["exportacion_sensores"] = exportacion_sensores;
        parametros_modulo_sensores["administracion_eventos"] = administracion_eventos;
        parametros_modulo_sensores["envio_valores_manuales_sensores"] = envio_valores_manuales_sensores;
    }

    // Se recuperan los parámetros del módulo Actuadores
    var parametros_modulo_actuadores = {};
    if (ids_pestanyas_visibles.indexOf("tab-actuadores-usuario") != -1) {
        var permiso_todos_actuadores = $('#permiso_todos_actuadores_usuario').val();
        var ids_actuadores = [];
        $("#ids_actuadores_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_actuadores.push($(this).val());
            }
        });
        var ids_grupos_actuadores = [];
        $("#ids_grupos_actuadores_usuario option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_grupos_actuadores.push($(this).val());
            }
        });
        var administracion_actuadores = $('#administracion_actuadores_usuario').val();
        var administracion_comentarios_actuadores = $('#administracion_comentarios_actuadores_usuario').val();
        var acciones_actuadores = $('#acciones_actuadores_usuario').val();
        var administracion_programaciones = $('#administracion_programaciones_usuario').val();
        var administracion_reglas = $('#administracion_reglas_usuario').val();
        parametros_modulo_actuadores["permiso_todos_actuadores"] = permiso_todos_actuadores;
        parametros_modulo_actuadores["ids_actuadores"] = ids_actuadores;
        parametros_modulo_actuadores["ids_grupos_actuadores"] = ids_grupos_actuadores;
        parametros_modulo_actuadores["administracion_actuadores"] = administracion_actuadores;
        parametros_modulo_actuadores["administracion_comentarios_actuadores"] = administracion_comentarios_actuadores;
        parametros_modulo_actuadores["acciones_actuadores"] = acciones_actuadores;
        parametros_modulo_actuadores["administracion_programaciones"] = administracion_programaciones;
        parametros_modulo_actuadores["administracion_reglas"] = administracion_reglas;
    }

    // Identificador y nombre del usuario
    var id_usuario = $('#id_usuario').val();
    if ((id_usuario[0] == "_") || (id_usuario[id_usuario.length - 1] == "_")) {
        jAlert(TLNT.Idiomas._("El identificador de usuario no puede empezar o terminar con el caracter '_'"));
        return;
    }
    if (id_usuario.indexOf("__") != -1) {
        jAlert(TLNT.Idiomas._("El identificador de usuario no puede contener los caracteres '__'"));
        return;
    }
    var nombre = $('#nombre_usuario').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_usuario").addClass('data-check-failed');
        return;
    }

    // Preferencias del usuario
    var idioma = $('#idioma_usuario').val();
    var tamanyo_letra = $('#tamanyo_letra_usuario').val();
    var pantalla_completa_inicio = $('#pantalla_completa_inicio_usuario').val();

    // Parametro
    var modo_seleccion_localizacion_actual = $('#modo_seleccion_localizacion_actual_usuario').val();
    var modulo_defecto = $('#modulo_defecto_usuario').val();
    var seccion_defecto = $('#seccion_defecto_usuario').val();
    var accion_inicial = $('#accion_inicial_usuario').val();
    var cadena_parametros_accion_inicial = $('#parametros_accion_inicial_usuario').val();
    var parametros_accion_inicial = cadena_parametros_accion_inicial.split(SEPARADOR_PARAMETROS_SIMPLES);
    switch (accion_inicial) {
        case ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS: {
            if ((cadena_parametros_accion_inicial == 0) || (parametros_accion_inicial.length != 1)) {
                jAlert(TLNT.Idiomas._("El número de parámetros es incorrecto"));
                return;
            }
            else {
                var numero_segundos_intervalo_actualizacion = parseInt(parametros_accion_inicial[0]);
                if ((isNaN(numero_segundos_intervalo_actualizacion) == true) || (
                    (numero_segundos_intervalo_actualizacion < MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_WIDGETS_DEFECTO) ||
                    (numero_segundos_intervalo_actualizacion > MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_WIDGETS_DEFECTO))) {
                    jAlert(TLNT.Idiomas._("Intervalo de actualización periódica de widgets no válido") +
                        " (" + MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_WIDGETS_DEFECTO + " - " + MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_WIDGETS_DEFECTO + ")");
                    return;
                }
            }
        }
    }
    var preferencias_modulos = [
        modo_seleccion_localizacion_actual,
        modulo_defecto,
        seccion_defecto,
        accion_inicial,
        cadena_parametros_accion_inicial].join(SEPARADOR_PARAMETROS_COMPUESTOS);

    // API HTTP
    var api_http = null;
    switch (perfil) {
        case PERFIL_USUARIO_ESTANDAR: {
            api_http = $('#api_http_usuario').val();
            break;
        }
        case PERFIL_USUARIO_ADMINISTRADOR:
        case PERFIL_USUARIO_SUPERADMINISTRADOR: {
            api_http = VALOR_SI;
            break;
        }
    }

    // Datos de la pestaña principal
    var datos_principal = {
        id_usuario: id_usuario,
        contrasenya: contrasenya,
        nombre: nombre,
        perfil: perfil
    };
    var cadena_datos_principal = JSON.stringify(datos_principal);
    var cadena_datos_principal_codificada = codifica_cadena_peticion_php(cadena_datos_principal);

    // Se añade o modifica el usuario
    if (anyadir_usuario == true) {
        $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/anyade_usuario.php", {
            datos_principal: cadena_datos_principal_codificada,
            ids_redes: ids_redes,
            licencias: ids_licencias,
            secciones: ids_secciones,
            parametros_modulo_personal: parametros_modulo_personal,
            parametros_modulo_localizaciones: parametros_modulo_localizaciones,
            parametros_modulo_sensores: parametros_modulo_sensores,
            parametros_modulo_actuadores: parametros_modulo_actuadores,
            idioma: idioma,
            tamanyo_letra: tamanyo_letra,
            pantalla_completa_inicio: pantalla_completa_inicio,
            preferencias_modulos: preferencias_modulos,
            api_http: api_http,
            // Parámetros del usuario anterior
            id_usuario_anterior: id_usuario_anterior,
            cadena_redes_anteriores: cadena_redes_anteriores,
            cadena_parametros_modulo_personal_anteriores: cadena_parametros_modulo_personal_anteriores,
            cadena_parametros_modulo_localizaciones_anteriores: cadena_parametros_modulo_localizaciones_anteriores,
            cadena_parametros_modulo_sensores_anteriores: cadena_parametros_modulo_sensores_anteriores,
            cadena_parametros_modulo_actuadores_anteriores: cadena_parametros_modulo_actuadores_anteriores
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_usuarios();
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/modifica_usuario.php", {
            datos_principal: cadena_datos_principal_codificada,
            ids_redes: ids_redes,
            licencias: ids_licencias,
            secciones: ids_secciones,
            parametros_modulo_personal: parametros_modulo_personal,
            parametros_modulo_localizaciones: parametros_modulo_localizaciones,
            parametros_modulo_sensores: parametros_modulo_sensores,
            parametros_modulo_actuadores: parametros_modulo_actuadores,
            idioma: idioma,
            tamanyo_letra: tamanyo_letra,
            pantalla_completa_inicio: pantalla_completa_inicio,
            preferencias_modulos: preferencias_modulos,
            api_http: api_http,
            // Parámetros del usuario anterior
            id_usuario_anterior: id_usuario_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            if (resultado.usuario_actual == 0) {
                jInfo(resultado.msg);
                actualiza_tabla_usuarios();
            }
            else {
                actualiza_usuario_actual(
                    idioma,
                    tamanyo_letra,
                    preferencias_modulos);
            }
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_administracion_actualizar_tabla_usuarios() {
	actualiza_tabla_usuarios();
}


function actualiza_tabla_usuarios() {
	$.post("./src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/dame_tabla_usuarios.php", {
		filtro: $('#filtro_administracion_filtro_usuarios_tabla').val(),
        perfil: $('#perfil_usuario_administracion_filtro_usuarios_tabla').val()
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaUsuarios').html(resultado.html);

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}
