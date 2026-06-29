// Parsea y devuelve el resultado de la ejecución de un script PHP en formato json
function dame_resultado_ejecucion_script_php_json(datos_json) {
    // Se recupera si es usuario interno
    if (comprobar_usuario_interno == true) {
        // Llamada 'ajax' síncrona y POST (con global a false para que no utilice los 'callbacks' de inicio y fin de ajax)
        var usuario_interno = null;
        $.ajax({
            url: "./comun/src/lib/herramientas/dame_usuario_interno.php",
            type: "POST",
            async: false,
            data: new FormData(),
            processData: false,
            contentType: false,
            global: false,
            success: function(result) {
                var resultado_usuario_interno = jQuery.parseJSON(result);
                usuario_interno = resultado_usuario_interno.usuario_interno;
            }
        });

        // Si es usuario interno se desconecta
        if (usuario_interno == true) {
            var mensaje_error = TLNT.Idiomas._("Usuario desconectado");
            jAlert(mensaje_error, TLNT.Idiomas._("Aviso"), function(res) {
                TLNT.Navegacion.muestra_barra_progreso();
                window.location.href = "./login.php";
            });
            return (null);
        }
    }

    // No es usuario interno, se procesa la respuesta
    try {
        var resultado = jQuery.parseJSON(datos_json);
    } catch (err) {
        var resultado = null;
    }
    if (resultado == null) {
        jAlert(TLNT.Idiomas._("Error desconocido"));
        return (null);
    }
    switch (resultado.res) {
        case "SESION_EXPIRADA":
        case "ID_SESION_INCORRECTO":
        case "PARAMETROS_SESION_INCORRECTOS": {
            var mensaje_error = "";
            if (resultado.res == "SESION_EXPIRADA") {
                mensaje_error = TLNT.Idiomas._("Sesión expirada");
            }
            else {
                if (resultado.res == "ID_SESION_INCORRECTO") {
                    mensaje_error = TLNT.Idiomas._("Usuario desconectado");
                }
                else {
                    if (resultado.res == "PARAMETROS_SESION_INCORRECTOS") {
                        mensaje_error = resultado.msg;
                    }
                }
            }
            if (temporizador_comprobacion_sesion_correcta != null) {
                clearTimeout(temporizador_comprobacion_sesion_correcta);
                temporizador_comprobacion_sesion_correcta = null;
            }
            jAlert(mensaje_error, TLNT.Idiomas._("Aviso"), function(res) {
                TLNT.Navegacion.muestra_barra_progreso();
                window.location.href = "./login.php";
            });
            return (null);
        }
    }
    if (resultado.res == "ERROR") {
        jAlert(resultado.msg);
        return (null);
    }
    return (resultado);
}


// Parsea y devuelve el resultado de la ejecución de un script PHP de un usuario interno en formato json
function dame_resultado_ejecucion_script_php_json_usuario_interno(datos_json) {
    // Se comprueba si hay error en el resultado
    var resultado = jQuery.parseJSON(datos_json);
    if (resultado == null) {
        resultado = {
            "res": "ERROR",
            "msg": TLNT.Idiomas._("Error desconocido")
        };
    }
    else {
        switch (resultado.res) {
            case "SESION_EXPIRADA": {
                resultado.res = "ERROR";
                resultado.msg = TLNT.Idiomas._("Sesión expirada");
                break;
            }
        }
    }
    return (resultado);
}


// Devuelve si el navegador es el navegador de un dispositivo móvil
// http://stackoverflow.com/questions/21741841/detecting-ios-android-operating-system
function dame_navegador_dispositivo_movil() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;

    // Windows Phone must come first because its UA also contains "Android"
    if (/windows phone/i.test(userAgent)) {
        return (true);
    }
    if (/android/i.test(userAgent)) {
        return (true);
    }
    // iOS detection from: http://stackoverflow.com/a/9039885/177710
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        return (true);
    }
    return (false);
}


// Devuelve si el navegador es un navegador qt
function dame_navegador_qt() {
    var userAgent = navigator.userAgent;

    if (/wkhtmltopdf/i.test(userAgent)) {
        return (true);
    }
    if (/QtWeb/i.test(userAgent)) {
        return (true);
    }
    return (false);
}


function escribe_log_externo(nivel_log, mensaje_log) {
    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("nivel", nivel_log);
    datos_formulario.append("mensaje", mensaje_log);

    // Se realiza la petición para escribir el log externo
    $.ajax({
        type: "POST",
        url: "./comun/src/lib/herramientas/escribe_log_externo.php",
        global: false,
        async: false,
        data: datos_formulario,
        processData: false,
        contentType: false,
        error: function(request, status, err) {}
    });
}
