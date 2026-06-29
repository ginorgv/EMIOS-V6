/*
 * Módulo Administración
 *
 */


// Selecciona la red actual
function boton_administracion_seleccionar_red_actual() {
	$.post("./src/modulos/ModulosWeb/ModuloAdministracion/selecciona_red_actual.php", {
        id_red: $('#id_red_actual_administracion_seleccion_red').val()
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se actualizan la descripción del usuario, el logo Web y el texto del pie de página
        $("#descripcion-usuario").html(resultado.html_descripcion_usuario);
        $("#logo-web").html(resultado.html_logo);
        $("#texto-pie-pagina").html(resultado.texto_pie_pagina);

        // Se actualiza el menú de módulos
        TLNT.Navegacion.actualiza_menu_modulos(resultado.html_menu_modulos);

        // Se establece el título de la Web
        TLNT.Navegacion.titulo = resultado.titulo_web;
        TLNT.Navegacion.actualiza_titulo_documento();

        // Si hay licencias "desactivadas" de módulos se añade al mensaje
        var tipo_mensaje = TIPO_MENSAJE_INFORMACION;
        if ($('#licencias_desactivadas_modulos').length > 0) {
            tipo_mensaje = TIPO_MENSAJE_AVISO;
            resultado.msg += "\n" + "(" + TLNT.Idiomas._("existen módulos con licencias desactivadas") + ")";
        }

        // Si se ha modificado la paleta de colores, se recarga la paleta de colores actual
        if (resultado.paleta_colores_graficas_modificada == true) {
            TLNT.Navegacion.recupera_informacion_extra_preferencias_actuales_resultado(resultado);
        }

        // - Si no se ha modificado el tema, se muestra el mensaje
        // - Si se ha modificado el tema, se modifican los estilos y se muestra el mensaje
        if (resultado.tema_modificado == false) {
            switch (tipo_mensaje) {
                case TIPO_MENSAJE_INFORMACION: {
                    jInfo(resultado.msg);
                    break;
                }
                case TIPO_MENSAJE_AVISO: {
                    jAlert(resultado.msg);
                    break;
                }
            }
        }
        else {
            // Tema actual
            TLNT.Navegacion.recupera_informacion_tema_actual_resultado(resultado);

            // Se recargan los estilos
            TLNT.Navegacion.recarga_estilos(tipo_mensaje, resultado.msg);

            // Se actualiza el color de fondo del menú de módulos y del pie de página
            if (TLNT.Navegacion.perfil_actual == PERFIL_USUARIO_ESTANDAR) {
                var color_fondo = color_tema_oscuro;
                $("#menu-modulos").css('background-color', color_fondo);
                $("#pie-pagina").css('background-color', color_fondo);
            }
        }

        // Se recupera la información local
        TLNT.Navegacion.recupera_informacion_local_resultado(resultado);

        // Establecimiento de formatos de fecha y hora
        TLNT.Navegacion.establece_formatos_fecha_hora();
	});
}


// Actualiza la red actual
function actualiza_red_actual() {
	$.post("./src/modulos/ModulosWeb/ModuloAdministracion/actualiza_red_actual.php", {},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se actualiza el menú de módulos
        TLNT.Navegacion.actualiza_menu_modulos(resultado.html_menu_modulos);

        // Se actualizan la descripción del usuario, el logo Web y el texto del pie de página
        $("#descripcion-usuario").html(resultado.html_descripcion_usuario);
        $("#logo-web").html(resultado.html_logo);
        $("#texto-pie-pagina").html(resultado.texto_pie_pagina);

        // Se establece el título de la Web
        TLNT.Navegacion.titulo = resultado.titulo_web;
        TLNT.Navegacion.actualiza_titulo_documento();

        // Si se ha modificado la paleta de colores, se recarga la paleta de colores actual
        if (resultado.paleta_colores_graficas_modificada == true) {
            TLNT.Navegacion.recupera_informacion_extra_preferencias_actuales_resultado(resultado);
        }

        // - Si no se ha modificado el tema, se muestra el mensaje
        // - Si se ha modificado el tema, se modifican los estilos y se muestra el mensaje
        if (resultado.tema_modificado == false) {
            jInfo(resultado.msg);
        }
        else {
            // Tema actual
            TLNT.Navegacion.recupera_informacion_tema_actual_resultado(resultado);

            // Se recargan los estilos
            TLNT.Navegacion.recarga_estilos(TIPO_MENSAJE_INFORMACION, resultado.msg);

            // Se actualiza el color de fondo del menú de módulos y del pie de página
            if (TLNT.Navegacion.perfil_actual == PERFIL_USUARIO_ESTANDAR) {
                var color_fondo = color_tema_oscuro;
                $("#menu-modulos").css('background-color', color_fondo);
                $("#pie-pagina").css('background-color', color_fondo);
            }
        }

        // Se recupera la información local
        TLNT.Navegacion.recupera_informacion_local_resultado(resultado);

        // Establecimiento de formatos de fecha y hora
        TLNT.Navegacion.establece_formatos_fecha_hora();
	});
}


// Actualiza el usuario actual
function actualiza_usuario_actual(idioma_usuario, tamanyo_letra_usuario, preferencias_modulos) {
	$.post("./src/modulos/ModulosWeb/ModuloAdministracion/actualiza_usuario_actual.php", {
        idioma: idioma_usuario,
        tamanyo_letra: tamanyo_letra_usuario,
        preferencias_modulos: preferencias_modulos
    },
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se actualiza la descripción del usuario
        $("#descripcion-usuario").html(resultado.html_descripcion_usuario);

        // - Si se ha modificado el idioma se recarga la página (también se recargarán los estilos)
        // - Si se ha modificado el tamanyo de letra, se modifican los estilos y se muestra el mensaje
        if (resultado.idioma_modificado == true) {
            jInfo(resultado.msg, TLNT.Idiomas._("Información"), function(res) {
                // Se muestra la barra de progreso
                TLNT.Navegacion.muestra_barra_progreso();

                // Se actualiza el id de sesión en la página y se recarga (así también se actualiza el idioma y el tamañp de letra)
                window.location.href = "./index.php?id_sesion=" + id_sesion + "#administracion#usuarios";
                window.location.reload();
            });
        }
        else {
            if (resultado.tamanyo_letra_modificado == true) {
                TLNT.Navegacion.recarga_estilos(TIPO_MENSAJE_INFORMACION, resultado.msg);
            }
            else {
                jInfo(resultado.msg);
            }
        }
	});
}


// Muestra la tabla de redes aplicando el filtro
function boton_administracion_filtro_redes_tabla() {
    actualiza_tabla_nodos(TIPO_NODO_RED);
}
