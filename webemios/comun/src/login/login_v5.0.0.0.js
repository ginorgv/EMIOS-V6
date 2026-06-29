$(document).ready(function() {
    if (usuario_interno == false) {
        // Si se está en la ventana de 'login':
        // - Se muestra la barra de progreso hasta que se cargue todo el documento
        // - Título de ventana y eliminación de 'hashes' de la URL
        // - Evento de botón 'login' pulsado
        var pagina_url = window.location.pathname;
        if (pagina_url.indexOf(PAGINA_LOGIN) > -1) {
            // - Título de ventana y eliminación de 'hashes' de la URL
            // (Nota: Si hay 'hashes' es porque se ha desconectado al usuario)
            var titulo_documento = "";
            if (TLNT.Navegacion.titulo != "") {
                titulo_documento = TLNT.Navegacion.titulo + " ";
            }
            titulo_documento += "(Login)";
            document.title = titulo_documento;
            var hashes = TLNT.URLQuery.get_hashes();
            if (hashes.length > 0) {
                history.pushState(null, null, PAGINA_LOGIN);
            }

            // Mostrado y ocultación de barra de progreso al cargar el documento
            TLNT.Navegacion.muestra_barra_progreso();
            if (document.readyState == "complete") {
                realiza_acciones_pagina_login_cargada();
            }
            else {
                $(window).on('load', realiza_acciones_pagina_login_cargada);
            }
        }

        // Evento de botón 'logout' pulsado
        if (pagina_url.indexOf(PAGINA_INDEX) > -1) {
            $('.menu-modulos-opcion-salir').click(boton_logout);
        }
    }
});


// Función a llamar cuando se cargue la página de login
function realiza_acciones_pagina_login_cargada() {
    TLNT.Navegacion.oculta_barra_progreso();

    // Se habilitan los controles de la ventana y se establece el foco en el usuario
    $("#usuario").prop('disabled', false);
    $("#contrasenya").prop('disabled', false);
    $("#boton-login").prop('disabled', false);
    $("#usuario").focus();

    // Evento de botón 'login' pulsado (también al pulsar 'enter')
    $('#boton-login').click(boton_login);
    $('#usuario').keypress(function(e) {
        if (e.which == 13) {
            boton_login();
        }
    });
    $('#contrasenya').keypress(function(e) {
        if (e.which == 13) {
            boton_login();
        }
    });

    // Login automático
    if (($("#usuario").val() != "") && ($("#contrasenya").val() != "")) {
        var login_automatico = parseInt($("#login-automatico").text());
        if (login_automatico == 1) {
            boton_login();
        }
    }
};


// Botón de login
function boton_login() {
    var usuario = $('#usuario').val();
    var contrasenya = $('#contrasenya').val();

    // Comprobación de controles de login
    if (TLNT.Check.inputs('controles-login')) {
        jAlert(TLNT.Idiomas._("Por favor, rellene usuario y contraseña"));
        return;
    }

    // Se eliminan los espacios y se pasa a minúsculas el usuario
    usuario = usuario.trim();
    usuario = usuario.toLowerCase();

    // Comprobación de usuario
    if (usuario.length == 0) {
        jAlert(TLNT.Idiomas._("Por favor, rellene usuario y contraseña"));
        return;
    }

    // Flag de entrando en sesión (para no ocultar la barra de progreso)
    // (así se muestra hasta que se carga la ventana de 'index')
    entrando_sesion = true;

    // Se recupera el "token" del login
    var token_login = $("#token-login").text().trim();

    // Datos del usuario
    var datos_usuario = {
        token: token_login,
        usuario: usuario,
        contrasenya: contrasenya
    };
    var cadena_datos_usuario = JSON.stringify(datos_usuario);
    var cadena_datos_usuario_codificada = codifica_cadena_peticion_php(cadena_datos_usuario);

    // Se autentica al usuario
    $.post("./comun/src/login/autentica_usuario.php", {
        datos_usuario: cadena_datos_usuario_codificada},
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            // Se desactiva el flag de entrando de sesión
            // (así se ocultará la barra de progreso al finalizar el 'post')
            entrando_sesion = false;
            return;
        }

        // Se redirige a la página de index (con el identificador de sesión correspondiente)
        var id_sesion = md5(usuario + token_login).substring(0, LONGITUD_ID_SESION);
        window.location.href = "./index.php?sesion=" + id_sesion;
    });
}


// Botón de logout
function boton_logout() {
    // Flag de saliendo de sesión (para no ocultar la barra de progreso)
    // (así se muestra hasta que se carga la ventana de 'login')
    saliendo_sesion = true;

    // Se sale de sesión
    $.post("./comun/src/login/logout.php", {},
    function(data, status) {
        // Se realizan las acciones 'extra' de salida de sesión
        TLNT.Navegacion.realiza_acciones_salida_sesion();

        // Se ignora el resultado y siempre se vuelve a la ventana de 'login'
        dame_resultado_ejecucion_script_php_json(data);
        window.location.href = "./login.php";
    });
}
