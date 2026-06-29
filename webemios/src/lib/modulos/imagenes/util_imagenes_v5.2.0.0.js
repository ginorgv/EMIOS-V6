//
// Funciones de imágenes
//


function comprueba_imagen_correcta(origen, id_control_fichero_imagen) {
    // Se comprueba el tamaño del fichero
    var tamanyo_fichero_imagen_bytes = document.getElementById(id_control_fichero_imagen).files[0].size;
    var tamanyo_fichero_imagen_kbs = (tamanyo_fichero_imagen_bytes / 1024);
    if (tamanyo_fichero_imagen_kbs > TAMANYO_MAXIMO_FICHERO_IMAGEN_KBS) {
        jAlert(TLNT.Idiomas._("El tamaño del fichero de imagen es demasiado grande") + "\n" +
            "(" + TLNT.Idiomas._("tamaño máximo") + ": " + formatea_numero(TAMANYO_MAXIMO_FICHERO_IMAGEN_KBS, 0) + " " + TLNT.Idiomas._("KBs") + ") " +
            "(" + TLNT.Idiomas._("tamaño actual") + ": " + formatea_numero(tamanyo_fichero_imagen_kbs, 0) + " " + TLNT.Idiomas._("KBs") + ")");
        return (false);
    }

    // http://stackoverflow.com/questions/4069982/document-getelementbyid-vs-jquery
    var control_seleccion_fichero_imagen = $('#' + id_control_fichero_imagen)[0];

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("origen", origen);
    datos_formulario.append("fichero_imagen", control_seleccion_fichero_imagen.files[0]);

    // Llamada 'ajax' POST
    var imagen_correcta = false;
    $.ajax({
        url: "./src/lib/modulos/imagenes/comprueba_imagen_correcta.php",
        type: "POST",
        async: false,
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            var resultado = dame_resultado_ejecucion_script_php_json(result);
            if (resultado == null) {
                return;
            }

            imagen_correcta = true;
        },
        error: function(request, status, err) {
            jAlert(TLNT.Idiomas._("Ha ocurrido un error al comprobar si la imagen es correcta"));
        }
    });
    return (imagen_correcta);
}


//
// Funciones de imágenes de servidor
//


function guarda_imagen_servidor_fichero_imagen(control_fichero_imagen) {
    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("fichero_imagen", control_fichero_imagen.files[0]);

    // Llamada 'ajax' POST
    var ruta_fichero_imagen = null;
    $.ajax({
        url: "./src/lib/modulos/imagenes/guarda_imagen_servidor_fichero_imagen.php",
        type: "POST",
        async: false,
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            var resultado = dame_resultado_ejecucion_script_php_json(result);
            if (resultado == null) {
                return;
            }

            ruta_fichero_imagen = resultado.ruta_fichero_imagen;
        },
        error: function(request, status, err) {
            jAlert(TLNT.Idiomas._("Ha ocurrido un error al guardar la imagen"));
        }
    });
    return (ruta_fichero_imagen);
}


//
// Funciones de imágenes de base de datos
//


function guarda_imagen_base_datos_fichero_imagen(origen, id_origen, control_fichero_imagen) {
    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("origen", origen);
    datos_formulario.append("id_origen", id_origen);
    datos_formulario.append("fichero_imagen", control_fichero_imagen.files[0]);

    // Llamada 'ajax' POST
    var imagen_guardada_correcta = false;
    $.ajax({
        url: "./src/lib/modulos/imagenes/guarda_imagen_base_datos_fichero_imagen.php",
        type: "POST",
        async: false,
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            var resultado = dame_resultado_ejecucion_script_php_json(result);
            if (resultado == null) {
                return;
            }

            imagen_guardada_correcta = true;
        },
        error: function(request, status, err) {
            jAlert(TLNT.Idiomas._("Ha ocurrido un error al guardar la imagen"));
        }
    });
    return (imagen_guardada_correcta);
}


function duplica_imagen_base_datos(origen, id_origen_anterior, id_origen) {
    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("origen", origen);
    datos_formulario.append("id_origen_anterior", id_origen_anterior);
    datos_formulario.append("id_origen", id_origen);

    // Llamada 'ajax' POST
    var imagen_duplicada_correcta = false;
    $.ajax({
        url: "./src/lib/modulos/imagenes/duplica_imagen_base_datos.php",
        type: "POST",
        async: false,
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            var resultado = dame_resultado_ejecucion_script_php_json(result);
            if (resultado == null) {
                return;
            }

            imagen_duplicada_correcta = true;
        },
        error: function(request, status, err) {
            jAlert(TLNT.Idiomas._("Ha ocurrido un error al duplicar la imagen"));
        }
    });
    return (imagen_duplicada_correcta);
}


function carga_imagen_base_datos(origen, id_origen, nombre_fichero_imagen) {
    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("origen", origen);
    datos_formulario.append("id_origen", id_origen);

    // Nota: Si un parámetro es 'null' y se añade a los datos del formulario, se añade la cadena 'null'
    // (si no se añade, al recuperar desde 'php' se recupera 'null' que es lo que se quiere)
    if (nombre_fichero_imagen != null) {
        datos_formulario.append("nombre_fichero_imagen", nombre_fichero_imagen);
    }

    // Llamada 'ajax' POST
    var imagen_cargada_correcta = false;
    var anchura_imagen = null;
    var altura_imagen = null;
    var ruta_fichero_imagen = null;
    $.ajax({
        url: "./src/lib/modulos/imagenes/carga_imagen_base_datos.php",
        type: "POST",
        async: false,
        data: datos_formulario,
        processData: false,
        contentType: false,
        success: function(result) {
            var resultado = dame_resultado_ejecucion_script_php_json(result);
            if (resultado == null) {
                return;
            }

            imagen_cargada_correcta = true;
            ruta_fichero_imagen = resultado.ruta_fichero_imagen;
            anchura_imagen = parseInt(resultado.anchura_imagen);
            altura_imagen = parseInt(resultado.altura_imagen);
        },
        error: function(request, status, err) {
            jAlert(TLNT.Idiomas._("Ha ocurrido un error al cargar la imagen"));
        }
    });
    var res = {
        imagen_cargada_correcta: imagen_cargada_correcta,
        ruta_fichero_imagen: ruta_fichero_imagen,
        anchura_imagen: anchura_imagen,
        altura_imagen: altura_imagen};
    return (res);
}


function boton_mostrar_imagen_base_datos_ventana() {
    var origen = $(this).attr('origen');
    var id_origen = $(this).attr('id_origen');
    var nombre_ventana = $(this).attr('nombre_ventana');

    muestra_imagen_base_datos_ventana(origen, id_origen, nombre_ventana);
}


function muestra_imagen_base_datos_ventana(origen, id_origen, nombre_ventana) {
    // Nombre del fichero de imagen
    var nombre_fichero_imagen = nombre_ventana;

    // Se carga la imagen
    var res_carga_imagen = carga_imagen_base_datos(origen, id_origen, nombre_fichero_imagen);
    var imagen_cargada_correcta = res_carga_imagen.imagen_cargada_correcta;
    if (imagen_cargada_correcta == false) {
        return;
    }

    try {
        // Se muestra la imagen en una ventana emergente
        var anchura_ventana = res_carga_imagen.anchura_imagen + MARGEN_DERECHA_VENTANA_PNG;
        var altura_ventana = res_carga_imagen.altura_imagen + MARGEN_ABAJO_VENTANA_PNG;

        // Se añade la variable 'refresh_timestamp' para que se actualice la imagen y no muestre la que tenga el navegador guardada en cache (ocurre en IExplorer):
        // - http://stackoverflow.com/questions/2104949/how-to-reload-refresh-an-elementimage-in-jquery
        // - http://stackoverflow.com/questions/16822831/what-is-the-best-way-to-force-an-image-refresh-on-a-webpage
        var ventana_imagen = window.open("", "_blank", "width=" + anchura_ventana + ", height=" + altura_ventana + ", location=0, menubar=0, status=0, toolbar=0");

        // Nota: Aquí no funciona la clase CSS
        // https://stackoverflow.com/questions/20424279/canvas-todataurl-securityerror
        ventana_imagen.document.write('<html><head><title>' + escapeHtml(nombre_ventana) + '</title></head><body>' +
            '<img style="border: 1px solid #777777" crossOrigin="anonymous" src="' + res_carga_imagen.ruta_fichero_imagen + '?refresh_timestamp=' + new Date().getTime() + '"></img></body></html>');
        ventana_imagen.document.close();
    }
    catch (err) {
        // Mensaje de error al log
        var mensaje_error = "Error JavaScript: '" + err.message + "'";
        if ((err.filename != "") && (err.lineNumber != "")) {
            mensaje_error += " (url: '" + err.filename + "', línea: '" + err.lineNumber + "')";
        }
        escribe_log_externo("ERROR", mensaje_error);

        // Mensaje de aviso
        jAlert(TLNT.Idiomas._("No se ha podido mostrar la imagen (asegúrese de que la visualización de ventanas emergentes ('pop-ups') no está bloqueada en el navegador)"));
    }
}


/*
 * Funciones de colores
 *
 */


// Devuelve el rgb de un color hexadecimal
function dame_rgb_color_hexadecimal(color_hexadecimal) {
    color_hexadecimal = color_hexadecimal.replace('#', '');
    var r = parseInt(color_hexadecimal.substring(0, 2), 16);
    var g = parseInt(color_hexadecimal.substring(2, 4), 16);
    var b = parseInt(color_hexadecimal.substring(4, 6), 16);
    var rgb = [r, g, b];
    return (rgb);
}


// Convierte un color de hexadecimal a RGB (con transparencia si existe)
function convierte_color_hexadecimal_rgb(color_hexadecimal, transparencia) {
    var rgb = dame_rgb_color_hexadecimal(color_hexadecimal);
    if (transparencia == null) {
        var color_rgb = "rgb(" +
            rgb[0] + "," +
            rgb[1] + "," +
            rgb[2] + ")";
        return (color_rgb);
    }
    else {
        var color_rgba = "rgba(" +
            rgb[0] + "," +
            rgb[1] + "," +
            rgb[2] + "," +
            (1 - transparencia) + ")";
        return (color_rgba);
    }
}


// Cambia el color de la imagen del elemento especificado (con id)
// (https://stackoverflow.com/questions/16228048/replace-a-specific-color-by-another-in-an-image-sprite)
function cambia_color_imagen(id, color_anterior, color_nuevo) {
    // Componentes 'rgb' de los colores
    var rgb_anterior = dame_rgb_color_hexadecimal(color_anterior);
    var rgb_nuevo = dame_rgb_color_hexadecimal(color_nuevo);
    var rojo_anterior = rgb_anterior[0];
    var verde_anterior = rgb_anterior[1];
    var azul_anterior = rgb_anterior[2];
    var rojo_nuevo = rgb_nuevo[0];
    var verde_nuevo = rgb_nuevo[1];
    var azul_nuevo = rgb_nuevo[2];

    // Se recupera la imagen y se crea un 'canvas' para cambiar los colores
    var image = document.getElementById(id);
    var anchura = image.width;
    var altura = image.height;
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext("2d");
    canvas.width = anchura;
    canvas.height = altura;

    // Se dibuja la imagen en el canvas y se recuperan los datos de la imagen
    ctx.drawImage(image, 0, 0, anchura, altura);
    var image_data = ctx.getImageData(0, 0, anchura, altura);

    // Se recorre cada pixel y se cambia el color especificado
    for (var i = 0; i < image_data.data.length; i += 4) {
        // is this pixel the old rgb?
        if ((image_data.data[i] == rojo_anterior) &&
            (image_data.data[i + 1] == verde_anterior) &&
            (image_data.data[i + 2] == azul_anterior)) {
            image_data.data[i] = rojo_nuevo;
            image_data.data[i + 1] = verde_nuevo;
            image_data.data[i + 2] = azul_nuevo;
        }
    }

    // Se escribe la imagen modificada en el canvas y después en el elemento
    ctx.putImageData(image_data, 0, 0);
    image.src = canvas.toDataURL('image/png');
}