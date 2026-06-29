//
// Funciones de informes fichero de plantillas de informes
//


// Genera el informe fichero del informe de plantilla de informe
function personal_informe_plantilla_informe_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_personal_informe_plantilla_informe();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informe-plantilla-informe').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        return;
    }

    // Parámetros del informe
    var id_plantilla_informe = parametros_informe["id_plantilla_informe"];
    var ids_parametros = parametros_informe["ids_parametros"];
    var valores_parametros = parametros_informe["valores_parametros"];
    var ids_elementos_portada = parametros_informe["ids_elementos_portada"];
    var ids_elementos_titulo = parametros_informe["ids_elementos_titulo"];
    var ids_elementos_texto = parametros_informe["ids_elementos_texto"];
    var ids_elementos_notas = parametros_informe["ids_elementos_notas"];
    var ids_elementos_imagen = parametros_informe["ids_elementos_imagen"];
    var parametros_tipo_json = parametros_informe["parametros_tipo_json"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Subtítulos de portadas y títulos
    var subtitulos_elementos_portada = [];
    for (var i = 0; i < ids_elementos_portada.length; i++) {
        var id_elemento_portada = ids_elementos_portada[i];
        var subtitulo_elemento_portada = parametros_tipo_json["subtitulo_elemento_portada_" + id_elemento_portada];
        subtitulos_elementos_portada.push(subtitulo_elemento_portada);
    }
    var titulos_elementos_titulo = [];
    for (var i = 0; i < ids_elementos_titulo.length; i++) {
        var id_elemento_titulo = ids_elementos_titulo[i];
        var titulo_elemento_titulo = parametros_tipo_json["titulo_elemento_titulo_" + id_elemento_titulo];
        titulos_elementos_titulo.push(titulo_elemento_titulo);
    }

    // Textos y notas del informe
    var textos_elementos_texto = [];
    for (var i = 0; i < ids_elementos_texto.length; i++) {
        var id_elemento_texto = ids_elementos_texto[i];
        var texto_elemento_texto = parametros_tipo_json["texto_elemento_texto_" + id_elemento_texto];
        textos_elementos_texto.push(texto_elemento_texto);
    }
    var textos_elementos_notas = [];
    for (var i = 0; i < ids_elementos_notas.length; i++) {
        var id_elemento_notas = ids_elementos_notas[i];
        var texto_elemento_notas = parametros_tipo_json["texto_elemento_notas_" + id_elemento_notas];
        textos_elementos_notas.push(texto_elemento_notas);
    }

    // Rutas de ficheros de las imágenes
    var rutas_ficheros_imagenes_elementos_imagen = [];
    for (var i = 0; i < ids_elementos_imagen.length; i++) {
        var id_elemento_imagen = ids_elementos_imagen[i];
        var ruta_fichero_imagen_elemento_imagen = parametros_tipo_json["ruta_fichero_imagen_elemento_imagen_" + id_elemento_imagen];
        rutas_ficheros_imagenes_elementos_imagen.push(ruta_fichero_imagen_elemento_imagen);
    }

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("id_plantilla_informe", id_plantilla_informe);
    datos_formulario.append("ids_parametros", JSON.stringify(ids_parametros));
    datos_formulario.append("valores_parametros", JSON.stringify(valores_parametros));
    datos_formulario.append("ids_elementos_portada", JSON.stringify(ids_elementos_portada));
    datos_formulario.append("subtitulos_elementos_portada", JSON.stringify(subtitulos_elementos_portada));
    datos_formulario.append("ids_elementos_titulo", JSON.stringify(ids_elementos_titulo));
    datos_formulario.append("titulos_elementos_titulo", JSON.stringify(titulos_elementos_titulo));
    datos_formulario.append("ids_elementos_texto", JSON.stringify(ids_elementos_texto));
    datos_formulario.append("textos_elementos_texto", JSON.stringify(textos_elementos_texto));
    datos_formulario.append("ids_elementos_notas", JSON.stringify(ids_elementos_notas));
    datos_formulario.append("textos_elementos_notas", JSON.stringify(textos_elementos_notas));
    datos_formulario.append("ids_elementos_imagen", JSON.stringify(ids_elementos_imagen));
    datos_formulario.append("rutas_ficheros_imagenes_elementos_imagen", JSON.stringify(rutas_ficheros_imagenes_elementos_imagen));
    datos_formulario.append("fecha_hora_inicio", fecha_hora_inicio);
    datos_formulario.append("fecha_hora_fin", fecha_hora_fin);
    datos_formulario.append("horario_semanal", JSON.stringify(horario_semanal));
    datos_formulario.append("exclusion_fechas", JSON.stringify(exclusion_fechas));
    datos_formulario.append("inclusion_fechas", JSON.stringify(inclusion_fechas));
    datos_formulario.append("minutos_desfase_utc", minutos_desfase_utc);
    datos_formulario.append("tipo_informe", TIPO_INFORME_FICHERO);

    // Llamada 'ajax' POST (se recuperan los datos para el informe)
    $.ajax({
        url: "./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/dame_datos_informe_plantilla_informe.php",
        type: "POST",
        data: datos_formulario,
        processData: false,
        contentType: false,
        timeout: SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX_GENERACION_INFORME_PLANTILLA_INFORME * 1000,
        success: function(result) {
            // Se comprueba si hay error en el resultado del informe
            var error_informe = false;
            var descripcion_error_informe = "";
            var resultado = dame_resultado_ejecucion_script_php_json_usuario_interno(result);
            if (resultado.res == "ERROR") {
                error_informe = true;
                descripcion_error_informe = resultado.msg;
            }

            // Comprobación de elementos en la plantilla de informe
            if (error_informe == false) {
                var hay_elementos = resultado.hay_elementos;
                if (hay_elementos == false) {
                    error_informe = true;
                    descripcion_error_informe = TLNT.Idiomas._("No hay elementos en la plantilla de informe");
                }
            }

            // Comprobación de elementos visibles en la plantilla de informe
            if (error_informe == false) {
                var hay_elementos_visibles = resultado.hay_elementos_visibles;
                if (hay_elementos_visibles == false) {
                    error_informe = true;
                    descripcion_error_informe = TLNT.Idiomas._("No hay elementos visibles en el informe");
                }
            }

            // Si hay error en el informe se muestra un mensaje de aviso (no se ocultan páginas porque no hay ninguna página creada aún)
            if (error_informe == true) {
                $('#mensaje-aviso-informe-fichero-informe-plantilla-informe').html(
                    "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
                return;
            }

            // Dibujado del informe:
            // - 1. Establecer los controles (id: 'informe-personal-informe-plantilla-informe')
            // - 2. Dibujar cada uno de los elementos utilizando funciones de dibujado específicas
            //   (se utilizará la misma función para dibujar que en los informes en los cuales está cada uno de los elementos y se le pasará el tipo de informe: WebEmios o fichero)

            // Recuperación de datos del resultado
            var info_elementos = resultado.info_elementos;
            var html_elementos = resultado.html_elementos;
            var datos_elementos = resultado.datos_elementos;
            var claves_datos_elementos = resultado.claves_datos_elementos;
            var elementos_informes_elementos = resultado.elementos_informes_elementos;

            // Se establece el html del informe
            $("#contenido-informe-fichero-informe-plantilla-informe").html(html_elementos);

            for (var i = 0; i < info_elementos.length; i++) {
                var info_elemento = info_elementos[i];
                var clave_datos_elemento = claves_datos_elementos[i];
                var datos_elemento = datos_elementos[clave_datos_elemento];
                var elementos_informe_elemento = elementos_informes_elementos[i];

                // Fechas de inicio y fin de consulta
                // (la fecha de inicio puede haberse modificado en el elemento por los parámetros de periodo de tiempo)
                var fecha_inicio_elemento = fecha_inicio;
                var hora_inicio_elemento = hora_inicio;
                if (datos_elemento["fecha_inicio"] !== undefined) {
                    fecha_inicio_elemento = datos_elemento["fecha_inicio"];
                }
                if (datos_elemento["hora_inicio"] !== undefined) {
                    hora_inicio_elemento = datos_elemento["hora_inicio"];
                }
                var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio_elemento, hora_inicio_elemento);
                var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

                // Parámetros del elemento
                var parametros_elemento = {
                    fecha_inicio: fecha_inicio_elemento,
                    fecha_fin: fecha_fin,
                    hora_inicio: hora_inicio_elemento,
                    hora_fin: hora_fin,
                    fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
                    fecha_hora_fin_consulta: fecha_hora_fin_consulta};

                // Se dibuja el elemento
                dibuja_elemento_plantilla_informe(
                   info_elemento,
                   datos_elemento,
                   elementos_informe_elemento,
                   parametros_elemento,
                   TIPO_INFORME_FICHERO);
            }
        }
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero del informe de plantilla de informe
function dame_parametros_informe_fichero_personal_informe_plantilla_informe() {
    // Identificador de plantilla de informe
    var id_plantilla_informe = $("#id_plantilla_informe_personal_informe_fichero_informe_plantilla_informe").text();

    // Parámetros tipo json
    var cadena_parametros_tipo_json = $("#parametros_tipo_json_personal_informe_fichero_informe_plantilla_informe").text();
    var parametros_tipo_json = jQuery.parseJSON(cadena_parametros_tipo_json);

    // Identificadores y valores de los parámetros
    var ids_parametros = [];
    var valores_parametros = [];
    $("#ids_parametros_personal_informe_fichero_informe_plantilla_informe li").each(function() {
        ids_parametros.push($(this).text());
    });
    $("#valores_parametros_personal_informe_fichero_informe_plantilla_informe li").each(function() {
        valores_parametros.push($(this).text());
    });

    // Identificadores de los elementos de tipo portada (los subtitulos están en los parámetros de tipo json)
    var ids_elementos_portada = [];
    $("#ids_elementos_portada_personal_informe_fichero_informe_plantilla_informe li").each(function() {
        ids_elementos_portada.push($(this).text());
    });

    // Identificadores de los elementos de tipo título (los títulos están en los parámetros de tipo json)
    var ids_elementos_titulo = [];
    $("#ids_elementos_titulo_personal_informe_fichero_informe_plantilla_informe li").each(function() {
        ids_elementos_titulo.push($(this).text());
    });

    // Identificadores de los elementos de tipo texto (los textos están en los parámetros de tipo json)
    var ids_elementos_texto = [];
    $("#ids_elementos_texto_personal_informe_fichero_informe_plantilla_informe li").each(function() {
        ids_elementos_texto.push($(this).text());
    });

    // Identificadores de los elementos de tipo notas (sólo aquellos elementos con texto introducido)
    // (los textos están en los parámetros de tipo json)
    var ids_elementos_notas = [];
    $("#ids_elementos_notas_personal_informe_fichero_informe_plantilla_informe li").each(function() {
        ids_elementos_notas.push($(this).text());
    });

    // Identificadores de los elementos de tipo imagen
    // (las rutas de los ficheros de las imágenes están en los parámetros de tipo json)
    var ids_elementos_imagen = [];
    $("#ids_elementos_imagen_personal_informe_fichero_informe_plantilla_informe li").each(function() {
        ids_elementos_imagen.push($(this).text());
    });

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_personal_informe_fichero_informe_plantilla_informe").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_personal_informe_fichero_informe_plantilla_informe").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_personal_informe_fichero_informe_plantilla_informe").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_plantilla_informe"] = id_plantilla_informe;
    parametros_informe["ids_parametros"] = ids_parametros;
    parametros_informe["valores_parametros"] = valores_parametros;
    parametros_informe["ids_elementos_portada"] = ids_elementos_portada;
    parametros_informe["ids_elementos_titulo"] = ids_elementos_titulo;
    parametros_informe["ids_elementos_texto"] = ids_elementos_texto;
    parametros_informe["ids_elementos_notas"] = ids_elementos_notas;
    parametros_informe["ids_elementos_imagen"] = ids_elementos_imagen;
    parametros_informe["parametros_tipo_json"] = parametros_tipo_json;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_personal_informe_fichero_informe_plantilla_informe").text();
        var hora_inicio = $("#hora_inicio_personal_informe_fichero_informe_plantilla_informe").text();
        var fecha_fin = $("#fecha_fin_personal_informe_fichero_informe_plantilla_informe").text();
        var hora_fin = $("#hora_fin_personal_informe_fichero_informe_plantilla_informe").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
            hora_inicio += ":00";
            hora_fin += ":59";
            var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
            var fecha_hora_fin = fecha_fin + ", " + hora_fin;

            parametros_informe["fecha_inicio"] = fecha_inicio;
            parametros_informe["fecha_fin"] = fecha_fin;
            parametros_informe["hora_inicio"] = hora_inicio;
            parametros_informe["hora_fin"] = hora_fin;
            parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
            parametros_informe["fecha_hora_fin"] = fecha_hora_fin;
        }
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}



