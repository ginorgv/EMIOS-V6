/* Inicio fichero: 'comun/src/lib/constantes/constantes_v5.4.0.0.js'*/

// Versión del módulo común WEB
var VERSION_COMUN_WEB = "5.4.0.0";

// Web de EnergyMinus
var WEB_ENERGY_MINUS = "www.energy-minus.es";

// Páginas
var PAGINA_LOGIN = "login.php";
var PAGINA_INDEX = "index.php";

// Longitud del identificador de sesión
var LONGITUD_ID_SESION = 8;

// Segundos de timeout de ejecuciones de Ajax
var SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX = 900;
var SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX_SEGUNDO_PLANO = 300;

// Segundos de intervalo de comprobación de sesión correcta
var SEGUNDOS_INTERVALO_COMPROBACION_SESION_CORRECTA = 600;

// Segundos de espera para la recarga de estilos (para evitar 'parpadeo')
var SEGUNDOS_ESPERA_RECARGA_ESTILOS = 1;

// Zonas horarias
var ZONA_HORARIA_UTC = "UTC";

// Formatos de fecha por defecto
var FORMATO_FECHA_LOCAL_DEFECTO = "dd/mm/yyyy";
var FORMATO_DIA_ANYO_LOCAL_DEFECTO = "dd/mm";
var FORMATO_FECHA_LOCAL_JQUERY_UI_DEFECTO = "dd/mm/yy";
var FORMATO_DIA_ANYO_LOCAL_JQUERY_UI_DEFECTO = "dd/mm";

// Formateado de números por defecto
var SEPARADOR_MILES_DEFECTO = ".";
var PUNTO_DECIMAL_DEFECTO = ",";

// Número máximo de reintentos de peticiones 'ajax'
var NUMERO_MAXIMO_REINTENTOS_PETICIONES_AJAX = 2;

// Tiempo de espera para reintentos de peticiones 'ajax'
var MILISEGUNDOS_ESPERA_REINTENTOS_PETICIONES_AJAX = 5000;


/* Fin fichero: 'comun/src/lib/constantes/constantes_v5.4.0.0.js'*/

/* Inicio fichero: 'comun/src/lib/globales/globales_v5.2.0.0.js'*/

// Ficheros WEB concatenados
var ficheros_web_concatenados = false;

// Log de peticiones 'ajax' activado
var log_peticiones_ajax_activado = false;

// Número de reintentos restantes de petición 'ajax' actual
var numero_reintentos_restantes_peticion_ajax = null;
var ultima_peticion_ajax_correcta = null;

// Id de sesión
var id_sesion = "";

// Flags de entrando y saliendo de sesión
var entrando_sesion = false;
var saliendo_sesion = false;

// Error de 'ajax' capturado
var error_ajax_capturado = false;

// Usuario interno
var usuario_interno = false;
var comprobar_usuario_interno = false;

// Comprobación periódica de sesión correcta
var temporizador_comprobacion_sesion_correcta = null;

// Flag de pantalla completa activada
var pantalla_completa_activada = false;

// Colores de tema
var color_tema_oscuro = null;
var color_tema_intermedio = null;
var color_tema_claro = null;
var color_tema_fondo = null;

// Formatos de fecha
var formato_fecha_local = FORMATO_FECHA_LOCAL_DEFECTO;
var formato_dia_anyo_local = FORMATO_DIA_ANYO_LOCAL_DEFECTO;

// Formateado de números
var separador_miles = SEPARADOR_MILES_DEFECTO;
var punto_decimal = PUNTO_DECIMAL_DEFECTO;



/* Fin fichero: 'comun/src/lib/globales/globales_v5.2.0.0.js'*/

/* Inicio fichero: 'comun/src/lib/herramientas/util_cadenas_v5.4.0.0.js'*/

// http://forwebonly.com/capitalize-the-first-letter-of-a-string-in-javascript-the-fast-way/
String.prototype.capitalize = function() {
    return (this.charAt(0).toUpperCase() + this.slice(1));
};


String.prototype.uncapitalize = function() {
    return (this.charAt(0).toLowerCase() + this.slice(1));
};


// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/endsWith
if (!String.prototype.endsWith) {
	String.prototype.endsWith = function(search, this_len) {
		if (this_len === undefined || this_len > this.length) {
			this_len = this.length;
		}
		return this.substring(this_len - search.length, this_len) === search;
	};
}


// http://stackoverflow.com/questions/1144783/replacing-all-occurrences-of-a-string-in-javascript
function escapeRegExp(string) {
    return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}


function replaceAll(string, find, replace) {
    return string.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}


// http://stackoverflow.com/questions/1787322/htmlspecialchars-equivalent-in-javascript
function escapeHtml(text) {
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}


function escapeHtmlXml(text) {
    var map = {
      '&': '%amp;',
      '<': '%lt;',
      '>': '%gt;',
      '"': '%quot;',
      "'": '%#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}


function unescapeHtml(text) {
    return String(text)
        .replace(/&amp;/g, '&')
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&quot;/g, '"')
        .replace(/&#039;/g, "'");
}


// https://gist.github.com/oxguy3/18d92821fe931945c86f
function unescapeHtmlXml(text) {
    return String(text)
        .replace(/%amp;/g, '&')
        .replace(/%lt;/g, '<')
        .replace(/%gt;/g, '>')
        .replace(/%quot;/g, '"')
        .replace(/%#039;/g, "'");
}


// http://stackoverflow.com/questions/30867172/code-not-running-in-ie-11-works-fine-in-chrome
if (!String.prototype.startsWith) {
    String.prototype.startsWith = function(searchString, position) {
        position = position || 0;
        return this.indexOf(searchString, position) === position;
    };
}


// Elimina los espacios en blanco
function elimina_espacios(cadena) {
    return String(cadena)
        .replace(/ /g, "");
}





/* Fin fichero: 'comun/src/lib/herramientas/util_cadenas_v5.4.0.0.js'*/

/* Inicio fichero: 'comun/src/lib/herramientas/util_criptografia_v4.0.0.0.js'*/

var codifica_caracter_peticion_php = function(caracter) {
    var caracter_codificado = "";
    if (caracter == 0) {
        caracter_codificado = '`';
    }
    else {
        caracter_codificado = String.fromCharCode(0x20 + caracter);
    }
    return (caracter_codificado);
};

var codifica_cadena_peticion_php = function(cadena) {
	cadena = unescape(encodeURIComponent(cadena));
	var contador = 0;
	var cadena_codificada = [];
	for (;;) {
		var longitud_cadena = cadena.length - contador;
		if (longitud_cadena > 45) {
			longitud_cadena = 45;
		}
		cadena_codificada.push(codifica_caracter_peticion_php(longitud_cadena));
		var subcadena = cadena.substring(contador, contador + longitud_cadena);
		subcadena += String.fromCharCode(0) + String.fromCharCode(0);
		for (var i = 0; i < longitud_cadena; i += 3) {
            cadena_codificada.push(codifica_caracter_peticion_php(subcadena.charCodeAt(i) >> 2));
			cadena_codificada.push(codifica_caracter_peticion_php(((subcadena.charCodeAt(i) & 3) << 4) + (subcadena.charCodeAt(i + 1) >> 4)));
			cadena_codificada.push(codifica_caracter_peticion_php(((subcadena.charCodeAt(i + 1) & 15) << 2) + (subcadena.charCodeAt(i + 2) >> 6)));
			cadena_codificada.push(codifica_caracter_peticion_php(subcadena.charCodeAt(i + 2) & 0x3f));
		}
		contador += longitud_cadena;
		if (contador == cadena.length) {
			break;
        }
		cadena_codificada.push('\n');
	}
    cadena_codificada = cadena_codificada.join('');
	return (cadena_codificada);
};





/* Fin fichero: 'comun/src/lib/herramientas/util_criptografia_v4.0.0.0.js'*/

/* Inicio fichero: 'comun/src/lib/herramientas/util_html_v5.2.0.0.js'*/

function muestra_elemento(id_elemento) {
    $("#" + id_elemento).show();
}


function muestra_elementos(ids_elementos) {
    for (var i = 0; i < ids_elementos.length; i++) {
        var id_elemento = ids_elementos[i];
        $("#" + id_elemento).show();
    }
}


function oculta_elemento(id_elemento) {
    $("#" + id_elemento).hide();
}


function oculta_elementos(ids_elementos) {
    for (var i = 0; i < ids_elementos.length; i++) {
        var id_elemento = ids_elementos[i];
        $("#" + id_elemento).hide();
    }
}


function vacia_elemento(id_elemento) {
    $("#" + id_elemento).html("");
}


function vacia_elementos(ids_elementos) {
    for (var i = 0; i < ids_elementos.length; i++) {
        var id_elemento = ids_elementos[i];
        $("#" + id_elemento).html("");
    }
}


function elimina_elemento(id_elemento) {
    $("#" + id_elemento).remove();
}


function elimina_elementos(ids_elementos) {
    for (var i = 0; i < ids_elementos.length; i++) {
        var id_elemento = ids_elementos[i];
        $("#" + id_elemento).remove();
    }
}


function anyade_clase_elemento(id_elemento, clase) {
    $("#" + id_elemento).addClass(clase);
}


function elimina_clase_elemento(id_elemento, clase) {
    $("#" + id_elemento).removeClass(clase);
}


function cambia_clase_elemento(id_elemento, clase) {
    $("#" + id_elemento).removeClass();
    $("#" + id_elemento).addClass(clase);
}


function sustituye_clase_elemento(id_elemento, clase_anterior, clase_nueva) {
    $("#" + id_elemento).removeClass(clase_anterior);
    $("#" + id_elemento).addClass(clase_nueva);
}


function dame_elemento_visible(id_elemento) {
    var elemento_visible = true;
    if ($('#' + id_elemento).length) {
        if (($('#' + id_elemento).css('display') == 'none') ||
            ($('#' + id_elemento).css("visibility") == "hidden")) {
            elemento_visible = false;
        }
    }
    else {
        elemento_visible = false;
    }
    return (elemento_visible);
}

/* Fin fichero: 'comun/src/lib/herramientas/util_html_v5.2.0.0.js'*/

/* Inicio fichero: 'comun/src/lib/herramientas/util_matematicas_v5.2.0.0.js'*/

function formatea_numero(numero, decimales, eliminar_ceros_finales) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +      input by: Amirouche
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    // *    example 13: number_format('1 000,50', 2, '.', ' ');
    // *    returns 13: '100 050.00'
    // Strip all characters but numerical ones.
    numero = (numero + '').replace(/[^0-9+\-Ee.]/g, '');

    var n = !isFinite(+numero) ? 0 : +numero;
    var prec = !isFinite(+decimales) ? 0 : Math.abs(decimales);

    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };

    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    var s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split(".");
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, separador_miles);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    var numero_formateado = s.join(punto_decimal);

    // Eliminación de ceros finales
    eliminar_ceros_finales = (eliminar_ceros_finales !== undefined) ? eliminar_ceros_finales : true;
    if (eliminar_ceros_finales == true) {
        if (decimales > 0) {
            while (true) {
                var ultimo_caracter = numero_formateado.charAt(numero_formateado.length - 1);
                if ((ultimo_caracter != '0') && (ultimo_caracter != punto_decimal)) {
                    break;
                }
                if (ultimo_caracter == '0') {
                    numero_formateado = numero_formateado.slice(0, -1);
                }
                if (ultimo_caracter == punto_decimal) {
                    numero_formateado = numero_formateado.slice(0, -1);
                    break;
                }
            }
        }
    }
    return (numero_formateado);
}


function formatea_numero_limite_digitos(numero, decimales, permitir_miles) {
    var sufijo_numero = "";
    if (numero >= 1000000000000000000 || numero <- 1000000000000000000) {
        numero = numero / 1000000000000000000;
        sufijo_numero = "T";
    }
    else {
        if (numero >= 1000000000000 || numero <- 1000000000000) {
            numero = numero / 1000000000000;
            sufijo_numero = "B";
        }
        else {
            if (numero >= 1000000 || numero <- 1000000) {
                numero = numero / 1000000;
                sufijo_numero += "M";
            }
            else {
                if (permitir_miles == false) {
                    if (numero >= 1000 || numero <- 1000) {
                        numero = numero / 1000;
                        sufijo_numero += "k";
                    }
                }
            }
        }
    }
    numero = formatea_numero(numero, decimales);
    numero += sufijo_numero;
    return (numero);
}
/* Fin fichero: 'comun/src/lib/herramientas/util_matematicas_v5.2.0.0.js'*/

/* Inicio fichero: 'comun/src/lib/herramientas/util_pantalla_v4.0.0.0.js'*/

// Función para obtener los píxeles del tamaño actual de letra
// (https://jsfiddle.net/SpYk3/fHgdv/)
(function($){$.getDefaultPx||($.extend({getDefaultPx:function(){var a=Array.prototype.slice.call(arguments),b=$("body"),d=1,e=!1,f=0,c=$("<div />").css({clear:"both",display:"block",height:"1em",position:"absolute",width:"1em","z-index":"-999"});for(x in a)switch(typeof a[x]){case "boolean":e=a[x];break;case "number":d=a[x];break;case "object":a[x]instanceof jQuery?a[x].length&&(b=a[x]):$(a[x]).prop("tagName")?b=$(a[x]):(a[x].element&&("string"==typeof a[x].element?$("body").find($(a[x].element)).length&&
(b=$(a[x].element)):"object"==typeof a[x].element&&(a[x].element instanceof jQuery?b=a[x].element:$(a[x].element).prop("tagName")&&(b=$(a[x].element)))),a[x].multiplier&&(d=parseFloat(a[x].multiplier)),a[x].asObject&&(e=a[x].asObject));break;case "string":$(a[x]).length&&(b=$(a[x]))}if(1==b.length){if("body"==b.prop("tagName").toLowerCase())return parseFloat($("body").css("font-size"))*d;b.append(c);a=c.height()*d;f=!e?a:{"0":a}}else f=!e?[]:{},b.each(function(a){$(this).append(c);f[a]=c.height()*
d});$(document).find(c).length&&c.remove();return f}}),$.fn.extend({getDefaultPx:function(a,b){return $.getDefaultPx($(this),a,b)}}))})(jQuery);



/* Fin fichero: 'comun/src/lib/herramientas/util_pantalla_v4.0.0.0.js'*/

/* Inicio fichero: 'comun/src/lib/herramientas/util_pie_pagina_v5.2.0.0.js'*/

function dame_texto_pie_pagina(texto_adicional) {
    var texto_fijo = $(".texto-fijo-pie-pagina").text();
    var texto_pie_pagina = "<a class='texto-fijo-pie-pagina elemento-no-seleccionable'>" + texto_fijo + "</a>";
    if ((texto_adicional != null) && (texto_adicional != "")) {
        texto_pie_pagina += "<span class='texto-adicional-pie-pagina elemento-no-seleccionable'> [" + texto_adicional + "]</span>";
    }
    return (texto_pie_pagina);
}


function actualiza_texto_pie_pagina(texto_adicional) {
    var texto_pie_pagina = dame_texto_pie_pagina(texto_adicional);
    $('#texto-pie-pagina').html(texto_pie_pagina);
}
/* Fin fichero: 'comun/src/lib/herramientas/util_pie_pagina_v5.2.0.0.js'*/

/* Inicio fichero: 'comun/src/lib/herramientas/util_sistema_v5.2.0.0.js'*/

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

/* Fin fichero: 'comun/src/lib/herramientas/util_sistema_v5.2.0.0.js'*/

/* Inicio fichero: 'comun/src/lib/herramientas/util_tabla_datos_v5.4.0.0.js'*/

function dame_filas_valores_tabla_datos(id_tabla_datos) {
    // Nota: Se ha añadido 'last' porque puede suceder que una tabla tenga otra tabla en los detalles
    // de una fila y también tenga 'valores_xml' (hay que coger siempre el último elemento 'valores_xml').
    var valores_xml = $("#" + id_tabla_datos + " .valores_xml").last().attr("valores");
    if (valores_xml === undefined) {
        return (null);
    }

    var elemento_doc_valores = $.parseXML(valores_xml);
    var doc_valores = $(elemento_doc_valores);

    var filas_valores = [];
    var texto = "";

    var fila_cabecera = [];
    var elemento_columnas = doc_valores.find('columnas');
    elemento_columnas.find('nombre').each(function() {
        texto = unescapeHtmlXml($(this).text());
        fila_cabecera.push(texto);
    });
    filas_valores.push(fila_cabecera);

    doc_valores.find('fila').each(function() {
        var fila_valores = [];
        var elemento_fila = $(this);
        elemento_fila.find('valor').each(function() {
            texto = unescapeHtmlXml($(this).text());
            fila_valores.push(texto);
        });
        filas_valores.push(fila_valores);
    });
    return (filas_valores);
}


/* Fin fichero: 'comun/src/lib/herramientas/util_tabla_datos_v5.4.0.0.js'*/

/* Inicio fichero: 'comun/src/lib/herramientas/util_tiempos_v5.2.0.0.js'*/

function convierte_formato_fecha_hora_a_formato_fecha_hora_python(formato_fecha_hora) {
    var formato_fecha_hora_python = formato_fecha_hora;
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "d", "%d");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "m", "%m");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "y", "%y");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "Y", "%Y");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "H", "%H");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "M", "%M");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "S", "%S");
    return (formato_fecha_hora_python);
}


// http://stackoverflow.com/questions/5619202/converting-string-to-date-in-js
// http://stackoverflow.com/questions/6177975/how-to-validate-date-with-format-mm-dd-yyyy-in-javascript
function dame_fecha_valida(cadena_fecha, formato_fecha) {
    // Recupera el día, mes y año de la fecha (utilizando el formato de fecha)
    var delimitador = dame_delimitador_formato_fecha(formato_fecha);
    var formato_fecha = formato_fecha.toLowerCase();
    var elementos_formato = formato_fecha.split(delimitador);
    var elementos_fecha = cadena_fecha.split(delimitador);
    if (elementos_fecha.length != 3) {
        return (false);
    }
    var indice_dia = elementos_formato.indexOf("dd");
    var indice_mes = elementos_formato.indexOf("mm");
    var indice_anyo = elementos_formato.indexOf("yyyy");
    var cadena_dia = elementos_fecha[indice_dia];
    var cadena_mes = elementos_fecha[indice_mes];
    var cadena_anyo = elementos_fecha[indice_anyo];
    if ((PATRON_NUMERO_ENTERO.test(cadena_dia) == false) ||
        (PATRON_NUMERO_ENTERO.test(cadena_mes) == false) ||
        (PATRON_NUMERO_ENTERO.test(cadena_anyo) == false)) {
        return (false);
    }
    var dia = parseInt(cadena_dia);
    var mes = parseInt(cadena_mes);
    var anyo = parseInt(cadena_anyo);

    // Comprobación de rangos de años y meses
    if ((anyo < 1000) || (anyo > 3000) || (mes == 0) || (mes > 12)) {
        return (false);
    }

    // Comprobación de rangos de días
    var longitud_meses = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if ((anyo % 400 == 0) || ((anyo % 100 != 0) && (anyo % 4 == 0))) {
        longitud_meses[1] = 29;
    }
    if ((dia < 0) || (dia > longitud_meses[mes - 1])) {
        return (false);
    }

    // Comprobaciones correctas
    return (true);
}


// Nota: La cadena de hora es sin segundos
function dame_hora_valida(cadena_hora) {
    // La hora es opcional, si es 'null' es válida
    if (cadena_hora == null) {
        return (true);
    }

    // Recupera la hora, minuto y segundo de la cadena
    var delimitador = ":";
    var elementos_hora = cadena_hora.split(delimitador);
    var cadena_hora = elementos_hora[0];
    var cadena_minuto = elementos_hora[1];
    if ((PATRON_NUMERO_ENTERO.test(cadena_hora) == false) ||
        (PATRON_NUMERO_ENTERO.test(cadena_minuto) == false)) {
        return (false);
    }
    var hora = parseInt(cadena_hora);
    var minuto = parseInt(cadena_minuto);

    // Comprobación de rangos de horas y minutis
    if ((hora < 0) || (hora > 23)) {
        return (false);
    }
    if ((minuto < 0) || (minuto > 59)) {
        return (false);
    }

    // Comprobaciones correctas
    return (true);
}


function dame_dia_anyo_valido(cadena_dia_anyo, formato_dia_anyo) {
    // Recupera el día y mes de la cadena (utilizando el formato de dia / mes)
    var delimitador = dame_delimitador_formato_fecha(formato_dia_anyo);
    var formato_dia_anyo = formato_dia_anyo.toLowerCase();
    var elementos_formato = formato_dia_anyo.split(delimitador);
    var elementos_dia_anyo = cadena_dia_anyo.split(delimitador);
    if (elementos_dia_anyo.length != 2) {
        return (false);
    }
    var indice_dia = elementos_formato.indexOf("dd");
    var indice_mes = elementos_formato.indexOf("mm");
    var cadena_dia = elementos_dia_anyo[indice_dia];
    var cadena_mes = elementos_dia_anyo[indice_mes];
    if ((PATRON_NUMERO_ENTERO.test(cadena_dia) == false) ||
        (PATRON_NUMERO_ENTERO.test(cadena_mes) == false)) {
        return (false);
    }
    var dia = parseInt(cadena_dia);
    var mes = parseInt(cadena_mes);

    // Comprobación de rangos de meses
    if ((mes == 0) || (mes > 12)) {
        return (false);
    }

    // Comprobación de rangos de días
    var longitud_meses = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if ((dia < 0) || (dia > longitud_meses[mes - 1])) {
        return (false);
    }

    // Comprobaciones correctas
    return (true);
}


function convierte_formato_fecha(cadena_fecha, formato_origen, formato_destino) {
    var fecha = $.datepicker.parseDate(formato_origen, cadena_fecha);
    var cadena_fecha_destino = $.datepicker.formatDate(formato_destino, fecha);
    return (cadena_fecha_destino);
}


function convierte_formato_dia_anyo(cadena_dia_anyo, formato_origen, formato_destino) {
    // Recupera el día y mes de la cadena (utilizando el formato origen)
    var delimitador_origen = dame_delimitador_formato_fecha(formato_origen);
    var formato_origen = formato_origen.toLowerCase();
    var elementos_formato_origen = formato_origen.split(delimitador_origen);
    var elementos_dia_anyo = cadena_dia_anyo.split(delimitador_origen);
    if (elementos_dia_anyo.length != 2) {
        return (false);
    }
    var indice_dia_origen = elementos_formato_origen.indexOf("dd");
    var indice_mes_origen = elementos_formato_origen.indexOf("mm");
    var cadena_dia = elementos_dia_anyo[indice_dia_origen];
    if (cadena_dia.length == 1) {
        cadena_dia = "0" + cadena_dia;
    }
    var cadena_mes = elementos_dia_anyo[indice_mes_origen];
    if (cadena_mes.length == 1) {
        cadena_mes = "0" + cadena_mes;
    }

    // Se crea la cadena del día anual (utilizando el formato destino)
    var delimitador_destino = dame_delimitador_formato_fecha(formato_destino);
    var elementos_formato_destino = formato_destino.split(delimitador_destino);
    var indice_dia_destino = elementos_formato_destino.indexOf("dd");
    var indice_mes_destino = elementos_formato_destino.indexOf("mm");
    var cadena_dia_anyo_destino = null;
    if (indice_dia_destino < indice_mes_destino) {
        cadena_dia_anyo_destino = cadena_dia + delimitador_destino + cadena_mes;
    }
    else {
        cadena_dia_anyo_destino = cadena_mes + delimitador_destino + cadena_dia;
    }
    return (cadena_dia_anyo_destino);
}


function dame_dia_mes(cadena_dia_anyo, formato_dia_anyo) {
    // Recupera el día y mes de la cadena
    var delimitador = dame_delimitador_formato_fecha(formato_dia_anyo);
    var formato_dia_anyo = formato_dia_anyo.toLowerCase();
    var elementos_formato = formato_dia_anyo.split(delimitador);
    var elementos_fecha = cadena_dia_anyo.split(delimitador);
    var indice_dia = elementos_formato.indexOf("dd");
    var indice_mes = elementos_formato.indexOf("mm");
    var cadena_dia = elementos_fecha[indice_dia];
    var cadena_mes = elementos_fecha[indice_mes];
    var dia = parseInt(cadena_dia);
    var mes = parseInt(cadena_mes);
    return ([dia, mes]);
}


function dame_fecha_hora(cadena_fecha, cadena_hora) {
    var delimitador = ":";
    var elementos_hora = cadena_hora.split(delimitador);
    var hora = elementos_hora[0];
    var minuto = elementos_hora[1];
    var segundo = elementos_hora[2];
    var fecha_hora = $.datepicker.parseDate(formato_fecha_local_jquery_ui, cadena_fecha);
    fecha_hora.setHours(hora, minuto, segundo);
    return (fecha_hora);
}


function convierte_fecha_a_cadena(fecha, formato_fecha) {
    var cadena_fecha = $.datepicker.formatDate(formato_fecha, fecha);
    return (cadena_fecha);
}


function dame_cadena_hora(fecha) {
    var cadena_hora = anyade_cero_hora_minuto_segundo(fecha.getHours()) + ":" +
        anyade_cero_hora_minuto_segundo(fecha.getMinutes()) + ":" +
        anyade_cero_hora_minuto_segundo(fecha.getSeconds());
    return (cadena_hora);
}


function dame_dias_diferencia_fechas(cadena_fecha_inicio, cadena_fecha_fin) {
    var fecha_inicio = $.datepicker.parseDate(formato_fecha_local_jquery_ui, cadena_fecha_inicio);
    var fecha_fin = $.datepicker.parseDate(formato_fecha_local_jquery_ui, cadena_fecha_fin);
    var dias_diferencia_fechas = Math.floor(fecha_fin - fecha_inicio) / 86400000;
    return (dias_diferencia_fechas);
}


//
// Funciones de descripciones
//


// Devuelve los nombre de los días de la semana
function dame_nombres_dias_semana() {
    var nombres_dias_semana = [
        TLNT.Idiomas._("Lunes"),
        TLNT.Idiomas._("Martes"),
        TLNT.Idiomas._("Miércoles"),
        TLNT.Idiomas._("Jueves"),
        TLNT.Idiomas._("Viernes"),
        TLNT.Idiomas._("Sábado"),
        TLNT.Idiomas._("Domingo")
    ];
    return (nombres_dias_semana);
}


// Devuelve los nombres de los meses
function dame_nombres_meses() {
    var nombres_meses = [
        TLNT.Idiomas._("Enero"),
        TLNT.Idiomas._("Febrero"),
        TLNT.Idiomas._("Marzo"),
        TLNT.Idiomas._("Abril"),
        TLNT.Idiomas._("Mayo"),
        TLNT.Idiomas._("Junio"),
        TLNT.Idiomas._("Julio"),
        TLNT.Idiomas._("Agosto"),
        TLNT.Idiomas._("Septiembre"),
        TLNT.Idiomas._("Octubre"),
        TLNT.Idiomas._("Noviembre"),
        TLNT.Idiomas._("Diciembre")
    ];
    return (nombres_meses);
}


//
// Funciones auxiliares
//


function dame_delimitador_formato_fecha(formato_fecha) {
    var delimitador = null;
    var caracter = null;
    for (var i = 0; i < formato_fecha.length; i++) {
        caracter = formato_fecha[i];
        if (((caracter < "a") || (caracter > 'z')) &&
            ((caracter < "A") || (caracter > 'Z'))) {
            delimitador = caracter;
            break;
        }
    }
    return (delimitador);
}

/* Fin fichero: 'comun/src/lib/herramientas/util_tiempos_v5.2.0.0.js'*/

/* Inicio fichero: 'comun/TLNT/TLNT_v5.2.0.0_R2.js'*/

/*
 *  Marco de desarrollo Web de Telnet
 */


// Establecimiento de navegación al cargar la página
$(document).ready(function() {
    TLNT.Navegacion.muestra_barra_progreso();
    TLNT.Navegacion.establece_navegacion();
    TLNT.Navegacion.oculta_barra_progreso();
});


// Definimos el espacio de trabajo.
var TLNT = {};


/*
 * Check
 *
 * Agrupa funciones para la validación de cadenas de texto, o para la
 * validación automática de formularios.
 *
 * Validación automática de formularios:
 *
 * Añadir como clase al input las clases correspondientespara que se ejecute el
 * validador asociado. Nota: La mayoría de los validadores son excluyentes entre sí.
 *
 * Si se crea un div con el mismo id que el del input a validar, pero concatenando el texto
 * _TLNT_input_error, al fallar una validación se rellenará automaticamente
 * este div con el mensaje de error asociado al validador fallido.
 * ejemplo si tenemos el <input id="lala" class="TLNT_input_mac" />
 * habría que crear el elemento <div id="lala_TLNT_input_error" />
 * para que el validador rellene la información de error asociada.
 *
 * Validadores de cadenas de texto.
 *
 * Bajo el método validate_functions es posible llamar desde javascript
 * cualquier de los validadores definidos, pasando como argumento una
 * cadena de texto.
 *
 * De este modo es posible validar cadenas de texto que no están asociadas
 * a un elemento input.
 *
 */
TLNT.Check = {
	inputs: function(id) {
		// Itera por los inputs de un id dado o si no hay un id definido por todos.
		// Busca los tags de clase y aplica las reglas de validación
		var error_detected = false;
		var selector = '';
		if (id != null) {
			selector = '#' + id;
		}
		this.clear_alerts(id);
		var that = this;
		$(selector + ' input').each(function(i) {
			error_detected = (that.test_element(this) || error_detected);
		});
        $(selector + ' textarea').each(function(i) {
			error_detected = (that.test_element(this) || error_detected);
		});

		return (error_detected);
	},


	// Borra cualquier mensaje de error y las clases que colorean los input erróneos.
	clear_alerts: function(id) {
		// Borra las alertas activas en un id o en todo el documento si no se proporciona un id.
		var selector='';
		if (id != null) {
			selector='#' + id;
		}
		$(selector + ' input').each(function(i) {
			$(this).removeClass('data-check-failed');
			$('#' + $(this).attr('id') + '_TLNT_input_error').html('');
		});
        $(selector + ' textarea').each(function(i) {
			$(this).removeClass('data-check-failed');
			$('#' + $(this).attr('id') + '_TLNT_input_error').html('');
		});
	},


	// Cadenas de texto asociadas al error. Por defecto en inglés, pero se podría extender facilmente.
	help_str: {
		'TLNT_input_mandatory': 'This field is required',
		'TLNT_input_numerical': 'Only numerical characters are allowed',
		'TLNT_input_text': 'Only text are allowed',
		'TLNT_input_alnum': 'Only alphanumerical values are allowed',
        'TLNT_input_integer': 'Only integer values are allowed',
		'TLNT_input_float': 'Only float in decimal format are allowed',
		'TLNT_input_ip': 'Only IP address is allowed',
		'TLNT_input_host': 'Only IP address or hostname is allowed',
		'TLNT_input_url': 'Only URL is allowed',
		'TLNT_input_mac': 'Only MAC address is allowed',
		'TLNT_input_subnet': 'Only IP or subnet is allowed',
		'TLNT_input_email': 'Only email address is allowed',
        'TLNT_input_login': 'Only alphanumerical values or dash are allowed',
		'TLNT_input_hex': 'Only hexadecimal values are allowed',
        'TLNT_input_hex_color': 'Only hex color is allowed',
        'TLNT_input_valid_characters': 'Characters not allowed'
	},


	// Establece las clases css de error para in id e intenta rellenar su campo de error con el mensaje asociado.
	set_alert: function(id, message) {
		$('#' + id).addClass('data-check-failed');

		$('#' + id + '_TLNT_input_error').html('* ' + this.help_str[message]);
		$('#' + id + '_TLNT_input_error').addClass('TLNT_input_error');

		TLNT.Debug.log("TLNT.check.set_alert(" + id + ", \"" + this.help_str[message] + "\");");
	},


	// Dado un elemento Jquery ejecuta las comprobaciones de validación en función de sus clases CSS.
	test_element: function(el) {
		var error_detected = false;
		if ($(el).attr('id')) {
			var that = this;
			var value = $(el).val();
			var len = value.length;
			var classList = $(el).prop('class').split(/\s+/);
			$.each(classList, function(index, item) {
				switch (item) {
					case "TLNT_input_numerical": {
						if (len && !that.validate_functions.is_numerical(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_numerical');
						}
						break;
                    }
					case "TLNT_input_text": {
						if (len && !that.validate_functions.is_text(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_text');
						}
						break;
                    }
					case "TLNT_input_alnum": {
						if (len && !that.validate_functions.is_alphanumerical(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_alnum');
						}
						break;
                    }
                    case "TLNT_input_integer": {
						if (len && !that.validate_functions.is_integer(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_integer');
						}
						break;
                    }
					case "TLNT_input_float": {
						if (len && !that.validate_functions.is_float(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_float');
						}
						break;
                    }
					case "TLNT_input_ip": {
						if (len && !that.validate_functions.is_ip(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_ip');
						}
						break;
                    }
					case "TLNT_input_host": {
						if (len && !that.validate_functions.is_host(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_host');
						}
						break;
                    }
					case "TLNT_input_url": {
						if (len && !that.validate_functions.is_url(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_url');
						}
						break;
                    }
					case "TLNT_input_mac": {
						if (len && !that.validate_functions.is_mac(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_mac');
						}
						break;
                    }
					case "TLNT_input_subnet": {
						if (len && !that.validate_functions.is_subnet(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_subnet');
						}
						break;
                    }
					case "TLNT_input_email": {
						if(len && !that.validate_functions.is_email(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_email');
						}
						break;
                    }
                    case "TLNT_input_login": {
						if(len && !that.validate_functions.is_login(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_login');
						}
						break;
                    }
					case "TLNT_input_hex": {
						if (len && !that.validate_functions.is_hex(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_hex');
						}
						break;
                    }
                    case "TLNT_input_hex_color": {
						if (len && !that.validate_functions.is_hex_color(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_hex_color');
						}
						break;
                    }
                    case "TLNT_input_valid_characters": {
						if (!that.validate_functions.is_valid_characters(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_valid_characters');
						}
						break;
                    }
					// Esta comprobación debe ser siempre la última
					case "TLNT_input_mandatory": {
                        if (!that.validate_functions.is_mandatory(value)) {
							error_detected = true;
							that.set_alert($(el).attr('id'), 'TLNT_input_mandatory');
						}
						break;
                    }
				}
			});
		}
		return (error_detected);
	},


	// Conjunto de funciones de validación. Autoexplicativas.
	validate_functions: {
		is_mandatory: function(str) {
			return (str.length);
		},
		is_numerical: function(str) {
			var pattern_number = /^\d+$/;
			return (pattern_number.test(str));
		},
		is_text: function(str) {
		   var pattern_text = /^[a-zA-Z ]+$/;
		   return (pattern_text.test(str));
		},
		is_alphanumerical: function(str) {
			var pattern_alpha = /^\w+$/;
			return (pattern_alpha.test(str));
		},
        is_integer: function(str) {
			var pattern_number = /^-?\d+$/;
			return (pattern_number.test(str));
		},
		is_float: function(str) {
			// Just on decimal format. No other float formats like scientific notation.
			var pattern_decimal = /^\-?\d+(\.\d+)?$/;
			return (pattern_decimal.test(str));
		},
		is_ip: function(str) {
			var pattern_decimal = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
			return (pattern_decimal.test(str));
		},
		is_host: function(str) {
			// IP or host value.
			var pattern_host = /^(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?$|(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
			return (pattern_host.test(str));
		},
		is_url: function(str) {
			// Url with optional parameters.
			var pattern_url = /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;
			return (pattern_url.test(str));
		},
		is_mac: function(str) {
			var pattern_mac = /^([0-9A-F]{2}[:]){5}[0-9A-F]{2}$/i;
			return (pattern_mac.test(str));
		},
		is_subnet: function(str) {
			// Check for subnet values like 192.168.1.0/24
			var pattern_subnet = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(?:\/(?:3[0-2]|[1-2]?[0-9]))?$/;
			return (pattern_subnet.test(str));
		},
		is_email: function(str) {
			// Two simple validators provided too if needed.(not RFC822 compilant)
			// var pattern_email = /^\w+[\+\.\w-]*@([\w-]+\.)*\w+[\w-]*\.([a-z]{2,4}|\d+)$/i;
			// var pattern_email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

			// RFC822 compliant email address matcher translated to javascript from the php function of Cal Henderson.
			// Source code of the php function is licensed under a Creative Commons Attribution-ShareAlike 2.5 License by Cal Henderson.
			// http://www.iamcal.com/publish/articles/php/parsing_email
			var pattern_email = /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*/;
			return (pattern_email.test(str));
		},
        is_login: function(str) {
		   var pattern_text = /^[-_A-Za-z0-9]+$/;
		   return (pattern_text.test(str));
		},
		is_hex: function(str) {
		   var pattern_text = /^[0-9A-E]+$/;
		   return (pattern_text.test(str));
		},
        is_hex_color: function(str) {
		   var pattern_text = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
		   return (pattern_text.test(str));
		},
        is_valid_characters: function(str) {
            // Nota: Se permiten todos los caracteres excepto "@", "<" o ">"
            var pattern_text = /^[^@<>]*$/;
            return (pattern_text.test(str));
		}
	}
};


/*
 * URLQuery
 *
 * Permite extraer información de la URL de la página
 *
 * get_base_host devuelve el string que identifica el servidor
 *
 * get_parameter con argumento un parámetro. Si en la URL está definido
 * el parámetro, devuelve su valor, si no está definido devuelve null.
 *
*/
TLNT.URLQuery = {
    get_parameter: function(paramName) {
        var searchString = window.location.search.substring(1),
            i, val, params = searchString.split("&");

        for (i = 0; i < params.length; i++) {
            val = params[i].split("=");
            if (val[0] == paramName) {
                return (unescape(val[1]));
            }
        }
        return (null);
    },


    get_base_host: function() {
        var host = window.location.host.split(":");
        return (host[0]);
    },


    get_hashes: function() {
        var hashesString = window.location.hash.substring(1);
        var hashes = hashesString.split('#');
        return (hashes);
    }
};


/*
 * JSON
 *
 * El método encode itera sobre los input contenidos en un elemento (form_id)
 * o todo el documento si no se proporciona un form_id, recogiendo los
 * valores de los campos y generando una cadena con formato JSON que devuelve.
 *
 * Requiere la extensión a JQuery jquery.json-1.3.min.js
 *
*/
TLNT.JSON = {
    encode: function(form_id) {
        var fields = new Object();
        var selector='';
        if (form_id != null) {
            selector = '#' + form_id;
        }
        $(selector + ":input").each(function() {
            if ($(this).attr("type") == "checkbox") {
                if (this.checked) {
                    fields[$(this).attr("name")] = $(this).val();
                }
            }
            else {
                if ($(this).attr("type") == "radio") {
                    if(this.checked) {
                        fields[$(this).attr("name")] = $(this).val();
                    }
                }
                else {
                    if ($(this).val() != '') {
                        fields[$(this).attr("name")] = $(this).val();
                    }
                }
            }
        });

        TLNT.Debug.log(fields);
        return ($.toJSON(fields));
    }
};


/*
 * Debug
 *
 * Implementa un sistema de mensajes de log que sólo se muestran si está
 * la propiedad enable con valor true.
 *
 *
*/
TLNT.Debug = {
    enable: true,


    log: function(obj) {
        if (this.enable) {
            console.log(obj);
            return (true);
        }
        return (false);
    }
};


/*
 * Mutex
 *
 * Implementación muy sencilla de un sistema mutex en Javascript (no utilizado).
 *
 *
*/
TLNT.Mutex = {
    // Vector de mutex
    mutex_pool: [],


    // Test devuelve true si existe un lock en la key, false si no existe.
    test: function(key) {
        return (this.mutex_pool[key] == 1);
    },


    // Establece un lock en una key.
    // No comprueba si ya existe un lock previo ya que no tiene sentido, para eso usar test_and_lock
    lock: function(key) {
        this.mutex_pool[key] = 1;
        return (true);
    },


    // Libera un lock asociado a una key
    // No comprueba si existe el lock previamente ya que no es necesario
    free: function(key) {
        delete this.mutex_pool[key];
        return (true);
    },


    // Comprueba si existe un lock sobre una key:
    // - Si no existe lo establece y devuelve true
    // - Si ya existe no hace nada y devuelve false
    test_and_set: function (key) {
        if(this.mutex_pool[key] != 1) {
            this.mutex_pool[key] = 1;
            return (true);
        }
        return (false);
    }
};


/*
 * Idiomas
*/
TLNT.Idiomas = {
	// Idioma por defecto
	idioma_defecto: "es_ES",

    // Idioma seleccionado
	idioma: "",

	// Idioma seleccionado (formato corto)
	idioma_corto: "",

	// Cadenas con las traducciones de todos los idiomas
	cadenas: {},


    // Carga el fichero de idiomas
    // (Nota: Se han intentado obtener los ficheros de idiomas de alguna otra forma para que se pudieran comprimir o guardar en 'cache'
    //  pero no se ha encontrado ninguna solución aceptable)
	carga_idiomas: function () {
        if (Object.keys(TLNT.Idiomas.cadenas).length == 0) {
            // Nota: Las llamadas Ajax son síncronas porque es necesario cargar los idiomas antes de continuar
            $.ajax({
                url: "./comun/rsc/idiomas/idiomas.json",
                type: "POST",
                async: false,
                data: new FormData(),
                processData: false,
                contentType: false,
                success: function(data) {
                    var cadenas_idiomas_comun;
                    var cadenas_idiomas_web;

                    // Nota:
                    // - Con servidor Apache 2.2, la cabecera manda 'text/plain',
                    // - Con servidor Apache 2.4, la cabecera manda 'application/json'
                    if (typeof data == "string") {
                        cadenas_idiomas_comun = jQuery.parseJSON(data);
                    } else {
                        cadenas_idiomas_comun = data;
                    }

                    $.ajax({
                        url: "./rsc/idiomas/idiomas.json",
                        type: "POST",
                        async: false,
                        data: new FormData(),
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (typeof data == "string") {
                                cadenas_idiomas_web = jQuery.parseJSON(data);
                            } else {
                                cadenas_idiomas_web = data;
                            }

                            TLNT.Idiomas.cadenas = cadenas_idiomas_comun;
                            jQuery.extend(TLNT.Idiomas.cadenas, cadenas_idiomas_web);
                        }
                    });
                }
            });
        }
    },


    // Recupera el idioma actual
	recupera_idioma_actual: function(idioma) {
        if (TLNT.Idiomas.idioma == "") {
            TLNT.Idiomas.idioma = idioma;
            if (TLNT.Idiomas.idioma == "") {
                TLNT.Idiomas.idioma = TLNT.Idiomas.idioma_defecto;
            }

            // Se calcula el formato corto
            TLNT.Idiomas.idioma_corto = TLNT.Idiomas.idioma.substring(0, 2);
        }
    },


    // Traduce la cadena argumento al idioma actual (si no hay idioma actual, la traduce al inglés)
	_: function (cadena_original) {
		var cadena_traducida;

        // Si el idioma es el idioma por defecto, la cadena traducida es la misma que la original
        if (TLNT.Idiomas.idioma == TLNT.Idiomas.idioma_defecto) {
            cadena_traducida = cadena_original;
        }
        else {
            // Se recupera la cadena traducida del idioma actual:
            // - Si no existe se añade un prefijo a la cadena para indicar que no existe la traducción
            var traducciones_cadena = TLNT.Idiomas.cadenas[cadena_original];
            if (traducciones_cadena) {
                cadena_traducida = traducciones_cadena[TLNT.Idiomas.idioma];
                if (cadena_traducida == null) {
                    cadena_traducida = '_' + cadena_original;
                }
            }
            else {
                cadena_traducida = '_' + cadena_original;
            }
        }

		return (escapeHtml(cadena_traducida));
    }
};


/*
 * Navegación
*/
TLNT.Navegacion = {
    // Perfil actual
    perfil_actual: "",

	// Modulo por defecto
	modulo_defecto: "",

	// Seccion por defecto
	seccion_defecto: "",

    // Flag de hashes modificados manualmente
    hashes_modificados_manualmente: false,

    // Modulo y sección anteriores
	modulo_anterior: "",
    seccion_anterior: "",

	// Modulo y sección actual
	modulo_actual: "",
    seccion_actual: "",

	// Titulo del sitio web
	titulo: "",


    // Acciones al visualizar los controles
    realiza_acciones_visualizacion_controles: function(controles) {
        controles.forEach(function(e) {
            if ($(e['selector']).length) {
                e['funcion']();
            }
        });
    },


    // Establecimiento de funciones (eventos) a los botones
    establece_eventos_botones: function(botones) {
        botones.forEach(function(e) {
            $(e['selector']).unbind('click');
            $(e['selector']).click(e['funcion']);
        });
    },


    // Establecimiento de evento de mostrar u ocultar el contenido de tablas de datos
    // (Nota: No funciona correctamente si hay cabeceras desplegables)
    establece_eventos_mostrar_ocultar_contenido_tablas_datos: function() {
        $('.titulo-tabla-datos-contenido-desplegable').unbind('click');
		$('.titulo-tabla-datos-contenido-desplegable').click(function() {
            var contenido_tabla_datos = $(this).siblings().children();
            var contenido_visible_antes = null;
            if ((contenido_tabla_datos.css('display') == 'none') ||
                (contenido_tabla_datos.css("visibility") == "hidden")) {
                contenido_visible_antes = false;
            }
            else {
                contenido_visible_antes = true;
            }
            var that = this;
            contenido_tabla_datos.slideToggle(100, function() {
                if (contenido_visible_antes == true) {
                    $(that).find(".opcion-desplegar-contenido").removeClass("icon-caret-up");
                    $(that).find(".opcion-desplegar-contenido").addClass("icon-caret-down");
                    $(that).addClass("titulo-tabla-datos-contenido-oculto");
                    $(that).attr("desplegado", VALOR_NO);
                }
            });
            if (contenido_visible_antes == false) {
                $(this).find(".opcion-desplegar-contenido").removeClass("icon-caret-down");
                $(this).find(".opcion-desplegar-contenido").addClass("icon-caret-up");
                $(this).removeClass("titulo-tabla-datos-contenido-oculto");
                $(this).attr("desplegado", VALOR_SI);
            }
        });
	},


    // Establecimiento de evento de mostrar u ocultar los elementos desplegables de cabeceras de tablas de datos
    establece_eventos_mostrar_ocultar_elementos_desplegables_cabeceras_tablas_datos: function() {
        $('.cabecera-tabla-datos-elementos-desplegables').unbind('click');
		$('.cabecera-tabla-datos-elementos-desplegables').click(function() {
            // Se muestran u ocultan los elementos desplegables y se establece el icono de la opción correspondiente
            var ids_elementos_desplegables = $("#" + this.id + " .ids_elementos_desplegables").attr("ids_elementos_desplegables").split(",");
            var elementos_visibles_antes = dame_elemento_visible(ids_elementos_desplegables[0]);
            for (var i = 0; i < ids_elementos_desplegables.length; i++) {
                $('#' + ids_elementos_desplegables[i]).slideToggle(100);
            }
            if (elementos_visibles_antes == false) {
                $("#" + this.id + " .opcion-desplegar-elementos-desplegables").removeClass("icon-caret-down");
                $("#" + this.id + " .opcion-desplegar-elementos-desplegables").addClass("icon-caret-up");
                $("#" + this.id).attr("desplegado", VALOR_SI);

                // Nota: Al desplegar un elemento con 'textarea' dentro, hay que redimensionar los 'textarea'
                for (var i = 0; i < ids_elementos_desplegables.length; i++) {
                    $('#' + ids_elementos_desplegables[i]).find("textarea").each(function(i) {
                        TLNT.Navegacion.redimensiona_textarea("#" + this.id);
                    });
                };
            }
            else {
                $("#" + this.id + " .opcion-desplegar-elementos-desplegables").removeClass("icon-caret-up");
                $("#" + this.id + " .opcion-desplegar-elementos-desplegables").addClass("icon-caret-down");
                $("#" + this.id).attr("desplegado", VALOR_NO);
            }
        });
	},


    // Establecimiento de evento para mostrar o esconder los detalles de una fila de una tabla de datos
	establece_eventos_mostrar_ocultar_detalles_tablas_datos: function() {
        $('.dato-fila-tabla-datos').unbind('click');
		$('.dato-fila-tabla-datos').click(function() {
            var text_seleccionado = window.getSelection();
            if (text_seleccionado.toString().length === 0) {
                var dato_fila_tabla_datos = this;
                var fila_datos = $(dato_fila_tabla_datos).parent().parent();
                var id_datos = fila_datos.parent().attr('id');
                if (fila_datos.siblings('.detalle-tabla-datos').is(':hidden')) {
                    $.post("./comun/src/lib/modulos/dame_detalles_tabla.php", {
                        id_datos: id_datos
                    },
                    function (data, status) {
                        var resultado = dame_resultado_ejecucion_script_php_json(data);
                        if (resultado == null) {
                            return;
                        }

                        // Se muestran los detalles recuperados con una animación
                        $('#' + id_datos + " .detalle-tabla-datos").html(resultado.html);
                        fila_datos.siblings('.detalle-tabla-datos').slideToggle(100);

                        // Para activar los eventos de los posibles controles mostrados en el detalle
                        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
                        TLNT.Navegacion.establece_eventos_detalles_tablas_datos();

                        // Acciones 'extra' a realizar en los detalles de la tabla de datos
                        TLNT.Navegacion.realiza_acciones_mostrado_detalle_tabla_datos(resultado);
                    });
                }
                else {
                    fila_datos.siblings('.detalle-tabla-datos').slideToggle(100);
                }
            }
		});
	},


    // Comportamiento de la navegación
	establece_navegacion: function() {
        // Título de la página Web
        TLNT.Navegacion.titulo = TITULO_WEB;

        // Carga de los idiomas
        TLNT.Idiomas.carga_idiomas();

        // Recupera información de las preferencias y local
        TLNT.Navegacion.recupera_informacion_preferencias_local();

        // Timeout de ejecuciones de Ajax (en milisegundos)
        $.ajaxSetup({
            timeout: SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX * 1000
        });

        // Acciones a realizar al inicio y finalización (correcta) de ejecuciones ajax
        // - Bloqueo y muestra barra de progreso durante peticiones ajax (post - get) (http://www.malsup.com/jquery/block/#page)
        // - Activación de timer para comprobación de sesión correcta
        // * Nota: Estos eventos ('ajaxStart' y 'ajaxEnd') sólo saltan en la primera petición 'ajax' (si en el procesado de la respuesta,
        //   se envía otra petición 'ajax' no se vuelve a llamar')
        if (usuario_interno == false) {
            $(document).ajaxStart(function() {
                if (log_peticiones_ajax_activado == true) {
                    console.log("ajaxStart");
                }
                TLNT.Navegacion.muestra_barra_progreso();
            });
            $(document).ajaxStop(function() {
                if (log_peticiones_ajax_activado == true) {
                    console.log("ajaxStop");
                }
                TLNT.Navegacion.oculta_barra_progreso();

                // Activación de comprobación de sesión correcta
                if (id_sesion != "") {
                    if (temporizador_comprobacion_sesion_correcta != null) {
                        clearTimeout(temporizador_comprobacion_sesion_correcta);
                    }
                    temporizador_comprobacion_sesion_correcta = setTimeout(
                        TLNT.Navegacion.expiracion_timeout_comprobacion_sesion_correcta,
                        SEGUNDOS_INTERVALO_COMPROBACION_SESION_CORRECTA * 1000);
                }
            });
        }

        // Acciones a realizar al enviar una petición Ajax
        // - Adición de parámetros de sesión
        // - Número de reintentos
        if (usuario_interno == false) {
            $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
                if (log_peticiones_ajax_activado == true) {
                    console.log("ajaxPrefilter (página destino: '" + options.url + "')");
                }

                // Se añaden los parámetros de sesión
                var parametros_sesion = dame_parametros_comprobacion_sesion();
                if (options.data instanceof FormData == true) {
                    options.data.append("id_sesion", id_sesion);
                    options.data.append("parametros_sesion", JSON.stringify(parametros_sesion));
                }
                else {
                    options.data += options.data? "&": "";
                    options.data +=
                        "id_sesion" + "=" + id_sesion + "&" +
                        "parametros_sesion" + "=" + JSON.stringify(parametros_sesion);
                }

                // Reintentos en peticiones 'ajax'
                // (https://stackoverflow.com/questions/10024469/whats-the-best-way-to-retry-an-ajax-request-on-failure-using-jquery)

                // Número de reintentos (pendientes)
                numero_reintentos_restantes_peticion_ajax = options.pendingRetriesCount;
                if (numero_reintentos_restantes_peticion_ajax === undefined) {
                    numero_reintentos_restantes_peticion_ajax = NUMERO_MAXIMO_REINTENTOS_PETICIONES_AJAX + 1;
                }

                // Objeto 'diferido' propio para los 'callbacks' de éxito / error
                // (Nota: 'let' no está soportado en 'WebKit')
                var dfd = $.Deferred();

                // Estado de la petición 'ajax'
                jqXHR.done(dfd.resolve);
                jqXHR.fail(function(xhr, textStatus, errorThrown) {
                    console.log("Error 'ajax' capturado: '" + JSON.stringify(xhr) + "', textStatus: '" + textStatus + "', errorThrown: '" + errorThrown + "'");
                    if ((xhr && xhr.readyState === 0) && (xhr.status === 0) && (xhr.statusText === "error")) {
                        numero_reintentos_restantes_peticion_ajax--;
                        console.log("Numero reintentos restantes: '" + numero_reintentos_restantes_peticion_ajax + "'");
                        if (numero_reintentos_restantes_peticion_ajax > 0) {
                            console.log("Reintento despues de espera ('" + MILISEGUNDOS_ESPERA_REINTENTOS_PETICIONES_AJAX + "' ms) ...");
                            setTimeout(function() {
                                // Reintento con una copia de opciones con contador de reintentos restantes
                                var newOpts = $.extend({}, originalOptions, {
                                    pendingRetriesCount: numero_reintentos_restantes_peticion_ajax
                                });
                                $.ajax(newOpts).done(dfd.resolve);
                            }, MILISEGUNDOS_ESPERA_REINTENTOS_PETICIONES_AJAX);
                        }
                        else {
                            console.log("Numero máximo de reintentos AJAX alcanzado");
                        }
                    }
                    else {
                        numero_reintentos_restantes_peticion_ajax = 0;
                        console.log("Error de 'ajax' sin reintentos");
                    }
                });
                return dfd.promise(jqXHR);
            });
        }

        // Cierre de ventana modal
        $('.modal').unbind('hide');
    	$('.modal').bind('hide', TLNT.Navegacion.vacia_ventana_modal);

        // Error en ejecuciones de Ajax (http://api.jquery.com/ajaxerror)
        $(document).ajaxSend(function(event, jqXHR, settings) {
            if (log_peticiones_ajax_activado == true) {
                console.log("ajaxSend (página destino: '" + settings.url + "')");
            }
        });
        $(document).ajaxSuccess(function(event, jqXHR, settings, error) {
            if (log_peticiones_ajax_activado == true) {
                console.log("ajaxSuccess (página destino: '" + settings.url + "')");
            }
            ultima_peticion_ajax_correcta = true;
        });
        $(document).ajaxError(function(event, jqXHR, settings, error) {
            if (log_peticiones_ajax_activado == true) {
                console.log("ajaxError (página destino: '" + settings.url + "')");
            }
            ultima_peticion_ajax_correcta = false;
            if (settings.error_procesado != true) {
                if (error_ajax_capturado == false) {
                    try {
                        // Si hay intentos restantes de la petición 'ajax', se ignora el error
                        console.log("Numero reintentos restantes (error capturado): '" + numero_reintentos_restantes_peticion_ajax + "'");
                        if (numero_reintentos_restantes_peticion_ajax > 0) {
                            return;
                        }

                        // Se oculta la barra de progreso
                        // (sólo se oculta si ya no hay más reintentos)
                        TLNT.Navegacion.oculta_barra_progreso();

                        // Mensaje de aviso
                        var escribir_log_externo = true;
                        if (usuario_interno == false) {
                            var mensaje_error = TLNT.Idiomas._("Se ha producido un error");
                            if (error != "") {
                                mensaje_error += " [" + error + "]";
                            }
                            else {
                                mensaje_error += " [?]";
                            }

                            // Se procesa el mensaje de error, sólo se muestra si es necesario
                            // (puede ser un error de desconexión y que no haga falta mostrarlo)
                            var mostrar_mensaje_error = TLNT.Navegacion.procesa_error_ajax(error);
                            if (mostrar_mensaje_error == true) {
                                jAlert(mensaje_error);
                            }
                            else {
                                escribir_log_externo = false;
                            }
                        }

                        // Log externo
                        if (escribir_log_externo == true) {
                            var mensaje_error = "";
                            if (error != "") {
                                mensaje_error = "Error Ajax: '" + error + "' (página destino: '" + settings.url + "', página origen: '" + event.target.location + "')";
                            }
                            else {
                                var cadena_event = JSON.stringify(event);
                                var cadena_request = JSON.stringify(jqXHR);
                                var cadena_settings = JSON.stringify(settings);
                                mensaje_error = "Error Ajax desconocido (página destino: '" + settings.url + "', página origen: '" + event.target.location +
                                    "', event: '" + cadena_event + "', request: '" + cadena_request + "', settings: '" + cadena_settings + "')";
                            }
                            escribe_log_externo("ERROR", mensaje_error);
                        }
                    } catch (err) {}
                }
                else {
                    // Se desactiva el flag de error ajax capturado
                    error_ajax_capturado = false;

                    // Se oculta la barra de progreso
                    TLNT.Navegacion.oculta_barra_progreso();
                }
            }
            settings.error_procesado = true;
        });

        // http://stackoverflow.com/questions/951791/javascript-global-error-handling
        window.onerror = function(msg, url, line, col, error) {
            try {
                // Mensaje de aviso
                // Nota: Se recarga la página porque dejan de funcionar los 'callbacks' de 'ajaxStart' y 'ajaxStop'
                if (usuario_interno == false) {
                    var mensaje_error = TLNT.Idiomas._("Se ha producido un error");
                    if (error != "") {
                        mensaje_error += " [" + error + "]";
                    }
                    else {
                        mensaje_error += " [?]";
                    }
                    jAlert(mensaje_error, TLNT.Idiomas._("Aviso"), function(res) {
                        // http://stackoverflow.com/questions/25522276/jquery-ajaxstop-event-stop-firing-after-runtime-error-in-any-ajax-callback
                        TLNT.Navegacion.oculta_barra_progreso();
                        jQuery.active = 0;
                    });
                }

                // Log externo
                var mensaje_error = "";
                if (error != "") {
                    mensaje_error = "Error JavaScript: '" + error + "'";
                }
                else {
                    mensaje_error = "Error JavaScript desconocido";
                }
                if ((url != "") && (line != "")) {
                    mensaje_error += " (url: '" + url + "', línea: '" + line + "')";
                }
                escribe_log_externo("ERROR", mensaje_error);
            } catch (err) {}

            var suppressErrorAlert = true;
            return (suppressErrorAlert);
        };
	},


    // Se recupera la información de preferencias
    // - Idioma actual
    // - Información del tema actual
    // - Información 'extra' de preferencias actuales
    // Se recupera la información local
    recupera_informacion_preferencias_local: function() {
        // Nota: La llamada Ajax es síncrona porque es necesario cargar la información del tema antes de continuar
        $.ajax({
            url: "./comun/src/lib/herramientas/dame_informacion_preferencias_local.php",
            type: "POST",
            async: false,
            data: new FormData(),
            processData: false,
            contentType: false,
            success: function(data) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                TLNT.Navegacion.recupera_informacion_preferencias_resultado(resultado);
                TLNT.Navegacion.recupera_informacion_local_resultado(resultado);
            }
        });
    },


    recupera_informacion_preferencias: function() {
        // Nota: La llamada Ajax es síncrona porque es necesario cargar la información del tema antes de continuar
        $.ajax({
            url: "./comun/src/lib/herramientas/dame_informacion_preferencias.php",
            type: "POST",
            async: false,
            data: new FormData(),
            processData: false,
            contentType: false,
            success: function(data) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                TLNT.Navegacion.recupera_informacion_preferencias_resultado(resultado);
            }
        });
    },


    recupera_informacion_preferencias_resultado: function(resultado) {
        // Ficheros WEB concatenados
        ficheros_web_concatenados = resultado.ficheros_web_concatenados;

        // Idioma
        TLNT.Idiomas.recupera_idioma_actual(resultado.idioma);

        // Tema actual
        TLNT.Navegacion.recupera_informacion_tema_actual_resultado(resultado);

        // Se recupera la informacion 'extra' de preferencias actuales específica de la aplicación del resultado
        TLNT.Navegacion.recupera_informacion_extra_preferencias_actuales_resultado(resultado);
    },


    recupera_informacion_extra_preferencias_actuales: function() {
        // Nota: La llamada Ajax es síncrona porque es necesario cargar la paleta de colores antes de continuar
        $.ajax({
            url: "./src/lib/herramientas/dame_informacion_extra_preferencias_actuales.php",
            type: "POST",
            async: false,
            data: new FormData(),
            processData: false,
            contentType: false,
            success: function(data) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                TLNT.Navegacion.recupera_informacion_extra_preferencias_actuales_resultado(resultado);
            }
        });
    },


    recupera_informacion_tema_actual: function() {
        // Nota: La llamada Ajax es síncrona porque es necesario cargar la información del tema antes de continuar
        $.ajax({
            url: "./comun/src/lib/herramientas/dame_informacion_tema_actual.php",
            type: "POST",
            async: false,
            data: new FormData(),
            processData: false,
            contentType: false,
            success: function(data) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                TLNT.Navegacion.recupera_informacion_tema_actual_resultado(resultado);
            }
        });
    },


    recupera_informacion_tema_actual_resultado: function(resultado) {
        // Colores de tema
        color_tema_oscuro = resultado.color_tema_oscuro;
        color_tema_intermedio = resultado.color_tema_intermedio;
        color_tema_claro = resultado.color_tema_claro;
        color_tema_fondo = resultado.color_tema_fondo;
    },


    recupera_informacion_local: function() {
        // Nota: La llamada Ajax es síncrona porque es necesario cargar la información local antes de continuar
        $.ajax({
            url: "./comun/src/lib/herramientas/dame_informacion_local.php",
            type: "POST",
            async: false,
            data: new FormData(),
            processData: false,
            contentType: false,
            success: function(data) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                TLNT.Navegacion.recupera_informacion_local_resultado(resultado);
            }
        });
    },


    recupera_informacion_local_resultado: function(resultado) {
        // Formatos de fecha
        formato_fecha_local = resultado.formato_fecha_local;
        formato_dia_anyo_local = resultado.formato_dia_anyo_local;

        // Formatos de números
        separador_miles = resultado.separador_miles;
        punto_decimal = resultado.punto_decimal;

        // Se recupera la informacion 'extra' local específica de la aplicación del resultado
        TLNT.Navegacion.recupera_informacion_extra_local_resultado(resultado);
    },


    muestra_barra_progreso: function() {
        $('.blockUI, .blockOverlay').remove();
        if ($('#popup_overlay').length > 0 && $('#popup_container').length == 0) {
            $('#popup_overlay').remove();
        }
        $.blockUI({
            baseZ: 9999,
            message: '<div class="spinner-modal"><div class="spinner-circle"></div></div>'
        });
    },


    oculta_barra_progreso: function() {
        if ((entrando_sesion == false) && (saliendo_sesion == false)) {
            $.unblockUI();
        }
        else {
            // Se desactivan los flags
            entrando_sesion = false;
            saliendo_sesion = false;
        }
        // Limpieza de overlays residuales de blockUI
        $('.blockUI').remove();
        $('.blockOverlay').remove();
        // Si hay un popup_overlay huerfano (sin popup_container visible), se elimina
        if ($('#popup_overlay').length > 0 && $('#popup_container:visible').length == 0) {
            $('#popup_overlay').remove();
        }
    },


    expiracion_timeout_comprobacion_sesion_correcta: function() {
        TLNT.Navegacion.comprueba_sesion_correcta();
        temporizador_comprobacion_sesion_correcta = setTimeout(
            TLNT.Navegacion.expiracion_timeout_comprobacion_sesion_correcta,
            SEGUNDOS_INTERVALO_COMPROBACION_SESION_CORRECTA * 1000);
    },


    actualiza_menu_modulos: function(html_menu_modulos) {
        $('#menu-modulos').html(html_menu_modulos);
        $('.menu-modulos-opcion-salir').click(boton_logout);
    },


    // Carga los contenidos del menú de secciones y del contenido de la sección
    carga_contenido: function() {
        // Si los hashes se han modificado manualmente no se carga el contenido
        if (TLNT.Navegacion.hashes_modificados_manualmente == true) {
            TLNT.Navegacion.hashes_modificados_manualmente = false;
            return;
        }

        // Hashes
        var hashes = TLNT.URLQuery.get_hashes();
		var modulo = "";
		var seccion = "";

        // Se guarda el id de sesión (con fallback al campo oculto si el pushState lo pierde por el base href)
        id_sesion = TLNT.URLQuery.get_parameter("sesion");
        if (id_sesion == null) {
            var hiddenField = document.getElementById('id_sesion_oculto');
            if (hiddenField) {
                id_sesion = hiddenField.value;
            }
        }

        // Nota: Si se intenta abrir un pestaña en una pestaña nueva del navegador
        var primer_hash = hashes[0];
        if (primer_hash.startsWith("tab-") == true) {
            jAlert(TLNT.Idiomas._("No se puede abrir una pestaña en una nueva ventana del navegador (se redirigirá a la página inicial)"), TLNT.Idiomas._("Aviso"), function(res) {
                window.location.href = "./index.php?id_sesion=" + id_sesion;
            });
            return;
        }

        // Se recupera el perfil del usuario actual
        var perfil = $('#perfil_usuario_actual').attr('perfil');
        TLNT.Navegacion.perfil_actual = perfil;

        // Flag de carga de contenido inicial
        var carga_contenido_inicial = false;
        if (hashes[0] == "") {
            carga_contenido_inicial = true;
        }

        // Pantalla completa desactivada por defecto
        pantalla_completa_activada = false;

        // Se recupera el módulo y sección a mostrar
        if (carga_contenido_inicial == true) {
            if ($('#error-menu-modulos').length > 0) {
                jAlert(TLNT.Idiomas._("Ha ocurrido un error al iniciar sesión"), TLNT.Idiomas._("Aviso"), function(res) {
                    window.location.href = "./login.php";
                });
                return;
            }

            if ($('#modulo_seccion_defecto').length == 0) {
                $('#menu-secciones').hide();
                jAlert(TLNT.Idiomas._("Este usuario no tiene ningún módulo disponible"), TLNT.Idiomas._("Aviso"), function(res) {
                    window.location.href = "./login.php";
                });
                return;
            }

            // Acciones 'extra' al cargar el contenido inicial
            TLNT.Navegacion.realiza_acciones_carga_contenido_inicial();

            // Nota: si es la primera sección mostrada, no se muestra la barra de progreso, se muestra "manualmente"
            TLNT.Navegacion.muestra_barra_progreso();

            var modulo_defecto = $('#modulo_seccion_defecto').attr('modulo');
			modulo = modulo_defecto;
		} else {
			modulo = hashes[0];
		}

        if (hashes[1] == null) {
            var seccion_defecto = $('#modulo_seccion_defecto').attr('seccion');
            seccion = seccion_defecto;
		} else {
			seccion = hashes[1];
		}

        // Procesa los parámetros de la sección (en los hashes)
        TLNT.Navegacion.procesa_parametros_seccion(hashes);

        // Se guardan el módulo anterior y la sección anterior
        TLNT.Navegacion.modulo_anterior = TLNT.Navegacion.modulo_actual;
        TLNT.Navegacion.seccion_anterior = TLNT.Navegacion.seccion_actual;

        // Enlace a la sección actual
        if (carga_contenido_inicial == true) {
            var enlace_seccion = "#" + modulo + "#" + seccion;
            history.pushState(null, null, enlace_seccion);
        }

        // Si es el mismo módulo, se establece la sección actual
        // Si es un módulo diferente, se carga el menú de secciones
        if (TLNT.Navegacion.modulo_actual == modulo) {
            // Sección actual
            $('#contenido .menu-secciones').removeClass('seccion-actual');
            $('#seccion-' + seccion).addClass('seccion-actual');
            TLNT.Navegacion.seccion_actual = seccion;

            // Evento de recarga de contenido de sección actual
            $('.menu-secciones').off("click");
            $('.seccion-actual').click(TLNT.Navegacion.recarga_contenido_seccion_actual);

            // Se carga el contenido de la sección
            TLNT.Navegacion.carga_contenido_seccion(modulo, seccion, carga_contenido_inicial);
        }
        else {
            // Módulo actual
            $('#menu-modulos .menu-modulos-opcion-modulo').removeClass('modulo-actual');
            $('#modulo-' + modulo).addClass('modulo-actual');
            TLNT.Navegacion.modulo_actual = modulo;
            TLNT.Navegacion.actualiza_titulo_documento();

            // Menú de secciones
            var res = TLNT.Navegacion.carga_menu_secciones(modulo, seccion, carga_contenido_inicial);
            if (res == false) {
                return;
            }
		}

        // Se actualiza el pie de página (y la fecha actual)
        actualiza_pie_pagina();
        TLNT.Navegacion.actualiza_pie_pagina_fecha_actual();

        // Botón de pantalla completa
        $('#boton-pantalla-completa').off();
        $('#boton-pantalla-completa').click(TLNT.Navegacion.activa_desactiva_pantalla_completa);
    },


    // Procesa los parámetros de la sección (si los hay)
    procesa_parametros_seccion: function(hashes) {
        if (hashes.length > 2) {
            var modulo = hashes[0];
            var seccion = hashes[1];
            var parametros_seccion = [];
            var cadenas_parametros_seccion = hashes[2].split("&");
            for (var i = 0; i < cadenas_parametros_seccion.length; i++) {
                var nombre_valor = cadenas_parametros_seccion[i].split("=");
                var parametro_seccion = {
                    "nombre": nombre_valor[0],
                    "valor": nombre_valor[1]};
                parametros_seccion.push(parametro_seccion);
            }

            // Establece variables globales dependientes de parámetros de la sección
            TLNT.Navegacion.establece_variables_globales_parametros_seccion(modulo, seccion, parametros_seccion);
        }
    },


    // Actualiza el título del documento
    actualiza_titulo_documento: function() {
        var titulo_documento = "";
        if (TLNT.Navegacion.titulo != "") {
            titulo_documento = TLNT.Navegacion.titulo + " - ";
        }
        titulo_documento += $('#' + "modulo-" + TLNT.Navegacion.modulo_actual + ' a').html();
        document.title = titulo_documento;
    },


    // Comprueba si la sesión es correcta
    comprueba_sesion_correcta: function() {
        var datos_formulario = new FormData();

        // Llamada 'ajax' POST (con global a false para que no utilice los 'callbacks' de inicio y fin de ajax)
        $.ajax({
            url: "./comun/src/lib/herramientas/dame_sesion_correcta.php",
            type: "POST",
            data: datos_formulario,
            processData: false,
            contentType: false,
            global: false,
            success: function(result) {
                var resultado = dame_resultado_ejecucion_script_php_json(result);
                if (resultado == null) {
                    return;
                }
            }
        });
    },


    // Carga el menú de secciones de un módulo
    carga_menu_secciones: function(modulo, seccion, carga_contenido_inicial) {
        // Parámetros extra de la sección
        var parametros_extra = dame_parametros_extra_modulo_seccion(modulo, seccion);

        // Se recupera el menú de secciones del módulo
        $.post("./comun/src/modulos/dame_menu_secciones_modulo.php", {
            modulo: modulo,
            parametros_extra: parametros_extra
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                res = false;
                return;
            }

            // Se establece el menú de secciones
            $('#menu-secciones').show();
            $('#menu-secciones').html(resultado.html);

            // Sección actual
            $('#seccion-' + seccion).addClass('seccion-actual');
            TLNT.Navegacion.seccion_actual = seccion;

            // Muestra las secciones del menú
            TLNT.Navegacion.muestra_secciones_menu(resultado.secciones_menu, null);

            // Actualización de sección actual
            $('.menu-secciones').off("click");
            $('.seccion-actual').click(TLNT.Navegacion.recarga_contenido_seccion_actual);

            // Se muestra el contenido
            $('#contenido').show();

            // Se actualiza el pie de página
            actualiza_pie_pagina();

            // Se carga el contenido de la sección
            TLNT.Navegacion.carga_contenido_seccion(modulo, seccion, carga_contenido_inicial);
        });
    },


    // Actualiza el menú de secciones de un módulo
    actualiza_menu_secciones: function() {
        var modulo_actual = TLNT.Navegacion.modulo_actual;
        var seccion_actual = TLNT.Navegacion.seccion_actual;

        var parametros_extra = dame_parametros_extra_modulo_seccion(modulo_actual, seccion_actual);
        if (parametros_extra == null) {
            $('.menu-secciones').show();
        }
        else {
            // Se recupera el menú de secciones del módulo
            $.post("./comun/src/modulos/dame_secciones_menu_modulo.php", {
                modulo: modulo_actual,
                parametros_extra: parametros_extra
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                // Muestra las secciones del menú
                TLNT.Navegacion.muestra_secciones_menu(
                    resultado.secciones_menu,
                    resultado.enlaces_secciones_menu);

                // Se carga el contenido de la sección
                TLNT.Navegacion.carga_contenido_seccion(modulo_actual, seccion_actual, false);
            });
        }
    },


    // Muestra (u oculta) las secciones del menú (y actualiza los enlaces de las secciones si es necesario)
    muestra_secciones_menu: function(secciones_visibles, enlaces_secciones_visibles) {
        // Se recuperan las secciones visibles
        var secciones_menu_visibles = secciones_visibles;
        var enlaces_secciones_menu_visibles = enlaces_secciones_visibles;

        var ids_secciones_menu_visibles = [];
        for (var i = 0; i < secciones_menu_visibles.length; i++) {
            var id_seccion_menu_visible = "seccion-" + secciones_menu_visibles[i]["id"];
            ids_secciones_menu_visibles.push(id_seccion_menu_visible);
        }

        // Se recorren las secciones del menú y se muestran sólo las devueltas por el script PHP
        var hashes = TLNT.URLQuery.get_hashes();
        $('.menu-secciones').each(function () {
            var id_seccion_menu = this.id;

            var indice_seccion_menu_visible = $.inArray(id_seccion_menu, ids_secciones_menu_visibles);
            if (indice_seccion_menu_visible != -1) {
                $("#" + id_seccion_menu).show();
                if (enlaces_secciones_visibles != null) {
                    var enlace_seccion_menu_visible = enlaces_secciones_menu_visibles[indice_seccion_menu_visible];
                    $("#" + id_seccion_menu).attr("href", enlace_seccion_menu_visible);

                    // Se actualiza el hash de la sección actual (pueden haber cambiado la sección y los parámetros extra)
                    var hashes_seccion_menu_visible = enlace_seccion_menu_visible.split('#');
                    if ((hashes[0] == hashes_seccion_menu_visible[1]) &&
                        ((hashes[1] == hashes_seccion_menu_visible[2]) || (TLNT.Navegacion.seccion_actual == hashes_seccion_menu_visible[2]))) {
                        TLNT.Navegacion.hashes_modificados_manualmente = true;
                        window.location.hash = enlace_seccion_menu_visible;

                        // Se establece el estilo de la sección actual (por si ha cambiado)
                        $('#contenido .menu-secciones').removeClass('seccion-actual');
                        $('#seccion-' + TLNT.Navegacion.seccion_actual).addClass('seccion-actual');
                    }
                }
            }
            else {
                $("#" + id_seccion_menu).hide();
            }
        });
    },


    // Carga el contenido de una sección de un módulo
    carga_contenido_seccion: function(modulo, seccion, carga_contenido_inicial) {
        var parametros_sesion = dame_parametros_comprobacion_sesion();
        var parametros_extra = dame_parametros_extra_modulo_seccion(modulo, seccion);
        $.post("./comun/src/modulos/dame_contenido_seccion_modulo.php", {
            id_sesion: id_sesion,
            parametros_sesion: parametros_sesion,
            modulo: modulo,
            seccion: seccion,
            parametros_extra: parametros_extra
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                $('#contenido-seccion').html("");
                return;
            }

            // Se comprueba si hay mensaje de error
            if ("msg_error" in resultado) {
                jAlert(resultado.msg_error);
            }

            // Se establece el contenido de la sección
            $('#contenido-seccion').html(resultado.html);

            // Se muestra el contenido
            $('#contenido').show();

            // Actualiza el pie de página con la fecha actual
            TLNT.Navegacion.actualiza_pie_pagina_fecha_actual();

            // Establecimiento de eventos
            TLNT.Navegacion.establece_eventos_secciones();
            TLNT.Navegacion.establece_eventos_tablas_datos();
            TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();

            // Acciones 'extra' al cargar el contenido de la sección
            TLNT.Navegacion.realiza_acciones_carga_contenido_seccion(modulo, seccion, carga_contenido_inicial);
        });
    },


    // Recarga el contenido de la sección actual
    recarga_contenido_seccion_actual: function() {
        // Se recuperan el módulo y sección actual
        var hashes = TLNT.URLQuery.get_hashes();
		var modulo = hashes[0];
		var seccion = hashes[1];

        // Se carga el contenido de la sección
        TLNT.Navegacion.carga_contenido_seccion(modulo, seccion, false);
    },


    actualiza_pie_pagina_fecha_actual: function() {
        var fecha_actual = new Date();
        var cadena_fecha_actual = convierte_fecha_a_cadena(fecha_actual, formato_fecha_local_jquery_ui);
        cadena_fecha_actual += ", " + dame_cadena_hora(fecha_actual);
        var texto_actualizado_hora_actual = TLNT.Idiomas._("hora de actualización") + ": " + cadena_fecha_actual;
        actualiza_texto_pie_pagina(texto_actualizado_hora_actual);
    },


    // Activa o desactiva la pantalla completa (el contenido de la sección)
    activa_desactiva_pantalla_completa: function() {
        if (pantalla_completa_activada == false) {
            $('#banner').hide();
            $('#menu-modulos').hide();
            $('#contenedor-menu-secciones').hide();
            $('#contenedor-contenido-seccion').addClass("contenido-seccion-pantalla-completa");

            pantalla_completa_activada = true;
        }
        else {
            $('#banner').show();
            $('#menu-modulos').show();
            $('#contenedor-menu-secciones').show();
            $('#contenedor-contenido-seccion').removeClass("contenido-seccion-pantalla-completa");

            pantalla_completa_activada = false;
        }

        // Acciones a realizar según la pantlla completa
        TLNT.Navegacion.realiza_acciones_pantalla_completa_seccion();
    },


    // Carga una ventana modal
    carga_ventana_modal: function(titulo, body, footer) {
        $('#ventana_modal .modal-body').removeClass('mostrar-todos-elementos-y');
        $('#ventana_modal .modal-body').addClass('mostrar-barra-desplazamiento-y');

        $("#ventana_modal" + " .modal-header h3").html(titulo);
        $("#ventana_modal" + " .modal-body").html(body);
        $("#ventana_modal" + " .modal-footer").html(footer);

        // Evento de pestaña mostrada (si no no se activa el evento de pestaña mostrada al mostrar las pestañas inicialmente)
        TLNT.Navegacion.lanza_evento_pestanya_inicial_visible_ventana_modal();
    },


    // Vacia una ventana modal
    vacia_ventana_modal: function() {
        $("#contenido_modal").off();

        $("#boton_ayuda_ventana_modal").hide("");
        $("#boton_ayuda_ventana_modal").removeClass();
        $("#boton_ayuda_ventana_modal").addClass("ayuda-ventana-modal icon-question-sign color-blanco");

        $("#ventana_modal" + " .modal-header h3").html("");
        $("#ventana_modal" + " .modal-body").html("");
        $("#ventana_modal" + " .modal-footer").html("");
    },


    // Evita que se propague el evento
	// http://javascript.info/tutorial/bubbling-and-capturing
	detiene_propagacion_evento: function(event) {
		event.stopPropagation? event.stopPropagation(): (event.cancelBubble=true);
	},


    //
    // Funciones a sobreescribir en TLNT_Configuracion
    //


    // Realiza acciones 'extra' al cargar el contenido inicial
    realiza_acciones_carga_contenido_inicial: function() {},


    // Establece variables globales dependientes de parámetros de la sección
    establece_variables_globales_parametros_seccion: function(modulo, seccion, parametros_seccion) {},


    // Realiza acciones 'extra' al cargar el contenido de una sección
    realiza_acciones_carga_contenido_seccion: function() {},


    // Realiza acciones 'extra' al salir de sesion
    realiza_acciones_salida_sesion: function() {},


    // Realiza acciones al modificar la pantalla completa dependiendo de la sección actual
    realiza_acciones_pantalla_completa_seccion: function() {},


    // Procesado de error 'ajax'
    procesa_error_ajax: function(error) {},


    // Recupera información 'extra' local
    recupera_informacion_extra_local_resultado: function(resultado) {},


    // Recupera información 'extra' de las preferencias actuales (del resultado)
    recupera_informacion_extra_preferencias_actuales_resultado: function(resultado) {},


    // Establece los eventos de los controles de las secciones
    establece_eventos_secciones: function() {},


    // Establece los eventos del contenido de informes
    establece_eventos_contenido_informes: function () {},


    // Establece los eventos de los controles de las tablas de datos (por módulo)
    establece_eventos_tablas_datos: function() {},


    // Establece los eventos de los controles de los detalles de las tablas de datos
    establece_eventos_detalles_tablas_datos: function() {},


    // Acciones 'extra' a realizar en los detalles de la tabla de datos
    realiza_acciones_mostrado_detalle_tabla_datos: function() {},


    // Establece los eventos de los controles de las ventanas modales
    establece_eventos_ventanas_modales: function() {},


    //
    // Funciones auxiliares
    //


    // Establece los formatos de controles de días y horas
    establece_formatos_fecha_hora: function() {
        // Establecimiento de formatos de fecha y hora
        $('.datepicker').datepicker({
            format: formato_fecha_local,
            weekStart: 1,
            language: TLNT.Idiomas.idioma_corto,
            todayHighlight: true,
            todayBtn: true,
            disableFocus: true
        });
        $('.timepicker').timepicker({
            minuteStep: 1,
            showInputs: false,
            disableFocus: true,
            showMeridian: false
        });
        $('.monthdaypicker').datepicker({
            format: formato_dia_anyo_local,
            weekStart: 1,
            language: TLNT.Idiomas.idioma_corto,
            todayHighlight: false,
            todayBtn: false,
            disableFocus: true
        });
    },


    // Recarga los estilos y muestra el mensaje correspondiente
    recarga_estilos: function(tipo_mensaje, mensaje) {
        TLNT.Navegacion.muestra_barra_progreso();

        // Se oculta el contenido
        $("#contenedor").hide();

        // Se ocultan los menús contextuales (al cambiar los estilos se muestran los textos)
        $(".menu-contextual").hide();

        // Se oculta la ventana modal (si es visible)
        var ventana_modal_visible = $('#ventana_modal').is(":visible");
        if (ventana_modal_visible == true) {
            $("#ventana_modal").hide();
        }

        // Se recargan los estilos
        // - http://stackoverflow.com/questions/2024486/is-there-an-easy-way-to-reload-css-without-reloading-the-page
        var queryString = '?reload=' + new Date().getTime();
        $('link[rel="stylesheet"]').each(function() {
            this.href = this.href.replace(/\?.*|$/, queryString);
        });

        $.post("./comun/src/lib/herramientas/duerme.php", {
            segundos: SEGUNDOS_ESPERA_RECARGA_ESTILOS
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Se muestra el contenido
            $("#contenedor").show();

            // Se muestra la ventana modal (si era visible)
            if (ventana_modal_visible == true) {
                $("#ventana_modal").show();
            }

            // Si hay mensaje, se muestra
            if (mensaje !== null) {
                switch (tipo_mensaje) {
                    case TIPO_MENSAJE_INFORMACION: {
                        jInfo(mensaje);
                        break;
                    }
                    case TIPO_MENSAJE_AVISO: {
                        jAlert(mensaje);
                        break;
                    }
                }
            }
        });
    },


    lanza_evento_pestanya_inicial_visible: function() {
        $('a[data-toggle="tab"]:first').trigger("shown.bs.tab");
    },


    lanza_evento_pestanya_inicial_visible_ventana_modal: function() {
        $('.modal-body a[data-toggle="tab"]:first').trigger("shown.bs.tab");
    }
};


/* Fin fichero: 'comun/TLNT/TLNT_v5.2.0.0_R2.js'*/

/* Inicio fichero: 'comun/src/login/login_v5.0.0.0.js'*/

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

/* Fin fichero: 'comun/src/login/login_v5.0.0.0.js'*/

