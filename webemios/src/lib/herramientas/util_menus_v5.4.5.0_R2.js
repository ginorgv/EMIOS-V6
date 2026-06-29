// Añade un menú contextual
function anyade_menu_contextual(id, info, nombre_ventana) {
    // Existen las siguientes opciones en los menús contextuales:
    // - Generar imagen: Se puede generar una imagen de los siguientes elementos
    //   (la imagen se muestra en una nueva ventana del navegador):
    // - Exportar valores: Se pueden exportar a fichero los datos de las gráficas y de las tablas de datos (de tipo fila)

    // Información del menú contextual
    var tipo_origen = info["tipo_origen"];
    var opciones = info["opciones"];

    // Se crea en el documento el menú contextual para el elemento que tiene el id que se pasa como parámetro
    // (si ya existe, se elimina del documento y se vuelve a añadir)

    // Se genera el menú
    var menu_contextual = $("<ul></ul>")
        .addClass("menu-contextual elemento-no-seleccionable")
        .attr({'id': 'menu-contextual-' + id});
    if (opciones.indexOf(OPCION_MENU_CONTEXTUAL_GUARDAR_IMAGEN) != -1) {
        menu_contextual.append($('<li></li>')
            .attr({'data-action': 'guardar_imagen_' + tipo_origen + '_' + id})
            .text(TLNT.Idiomas._('Generar imagen')));
    }
    if (opciones.indexOf(OPCION_MENU_CONTEXTUAL_EXPORTAR_VALORES) != -1) {
        menu_contextual.append($('<li></li>')
            .attr({'data-action': 'exportar_valores_' + tipo_origen + '_' + id})
            .text(TLNT.Idiomas._('Exportar valores')));
    }
    if (opciones.indexOf(OPCION_MENU_CONTEXTUAL_ANYADIR_COMENTARIO) != -1) {
        menu_contextual.append($('<li></li>')
            .attr({'data-action': 'anyadir_comentario_' + tipo_origen + '_' + id})
            .attr({'modulo_informe': modulo_informe_dibujado})
            .attr({'tipo_informe': tipo_informe_dibujado})
            .attr({'informacion_extra_informe': informacion_extra_informe_dibujado})
            .attr({'numero_elemento_plantilla_informe': numero_elemento_plantilla_informe_dibujado})
            .text(TLNT.Idiomas._('Añadir comentario')));
    }
    if (opciones.indexOf(OPCION_MENU_CONTEXTUAL_ANYADIR_COMENTARIOS) != -1) {
        menu_contextual.append($('<li></li>')
            .attr({'data-action': 'anyadir_comentarios_' + tipo_origen + '_' + id})
            .attr({'modulo_informe': modulo_informe_dibujado})
            .attr({'tipo_informe': tipo_informe_dibujado})
            .attr({'informacion_extra_informe': informacion_extra_informe_dibujado})
            .attr({'numero_elemento_plantilla_informe': numero_elemento_plantilla_informe_dibujado})
            .text(TLNT.Idiomas._('Añadir comentarios')));
    }
    if ($('#menu-contextual-' + id).length > 0) {
        $('#menu-contextual-' + id).remove();
    }
    $(document.body).append(menu_contextual);

    // Función que gestiona la pulsación sobre las opciones del menú contextual personalizado
    // (se muestra la barra de progreso para que se muestre mientras realiza el procesado en 'javascript')
    $('#menu-contextual-' + id + ' li').click(function() {
        // Se comprueba sobre que opción del menú se ha pulsado
        switch ($(this).attr('data-action')) {
            case 'guardar_imagen_' + tipo_origen + '_' + id: {
                switch (tipo_origen) {
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_JQPLOT:
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_JQPLOT_WIDGET: {
                        // Vacía el tooltip de las gráficas
                        vacia_tooltip_graficas();

                        var imagen_png_base64 = $('#' + id).jqplotToImageStr({});
                        var anchura_ventana = $('#' + id).width();
                        var altura_ventana = $('#' + id).height();

                        muestra_imagen_png(
                            id + ".png",
                            imagen_png_base64,
                            CODIFICACION_BASE_64,
                            nombre_ventana,
                            anchura_ventana,
                            altura_ventana);
                        break;
                    }
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_TOPOLOGIA_ARBOL:
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_MAPA_CALOR:
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_MAPA_CALOR_WIDGET:
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_GRAFICA_VIENTO: {
                        // Se recupera la hoja de estilos correspondiente
                        var estilos_css = "";
                        if (ficheros_web_concatenados == true) {
                            estilos_css = dame_estilos_hoja_css("web_rsc.php");
                        }
                        else {
                            // Se recuperan los estilos correspondientes al elemento 'svg'
                            switch (tipo_origen) {
                                case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_TOPOLOGIA_ARBOL: {
                                    estilos_css = dame_estilos_hoja_css("topologiaarbol.php");
                                    break;
                                }
                                case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_MAPA_CALOR:
                                case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_MAPA_CALOR_WIDGET: {
                                    estilos_css = dame_estilos_hoja_css("heatmap.php");
                                    break;
                                }
                                case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_GRAFICA_VIENTO: {
                                    estilos_css = dame_estilos_hoja_css("windhistory.php");
                                    break;
                                }
                            }
                        }
                        var estilos = "<![CDATA[ " + estilos_css + " ]]>";
                        var definiciones_estilos = $("<defs></defs>").append($("<style></style>")
                            .attr({'type': 'text/css'})
                            .html(estilos));

                        // Se añaden los estilos al elemento 'svg'
                        // Nota: Se trabaja con una copia del elemento 'svg'
                        // (si no al insertar el elemento 'defs' (con los estilos) se añade al elemento 'svg' de la página cada vez que se guarda la imagen)
                        var copia_elemento_svg = $("#" + id + " svg").clone().get(0);
                        copia_elemento_svg.insertBefore(definiciones_estilos.get(0), copia_elemento_svg.firstChild);

                        // Se convierte el elemento 'svg' a imagen 'png'
                        // (se utiliza la librería 'canvg' para generar la imagen. El modo "canvas" nativo no funciona en IE debido a un bug:
                        // https://connect.microsoft.com/IE/feedback/details/828416/cavas-todataurl-method-doesnt-work-after-draw-svg-file-to-canvas)
                        copia_elemento_svg.toDataURL("image/png", {
                            renderer: "canvg",
                            callback: function(data) {
                                var imagen_png_base64 = data;
                                var anchura_ventana = parseInt(copia_elemento_svg.getAttribute("width"));
                                var altura_ventana = parseInt(copia_elemento_svg.getAttribute("height"));

                                TLNT.Navegacion.muestra_barra_progreso();
                                muestra_imagen_png(
                                    id + ".png",
                                    imagen_png_base64,
                                    CODIFICACION_BASE_64,
                                    nombre_ventana,
                                    anchura_ventana,
                                    altura_ventana);
                            }
                        });
                        break;
                    }
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_TABLA_DATOS: {
                        var elemento = document.getElementById(id);

                        var elementos_id = id.split("__");
                        var id_tabla = elementos_id[elementos_id.length - 1];
                        var nombre_imagen = id_tabla;

                        // Conversión de tablas
                        var libreria_dom_to_image_soportada = dame_libreria_dom_to_image_soportada();
                        if (libreria_dom_to_image_soportada == true) {
                            domtoimage.toPng(elemento)
                                .then(function (dataUrl) {
                                    var imagen_png_base64 = dataUrl;
                                    var anchura_ventana = parseInt($(elemento).width());
                                    var altura_ventana = parseInt($(elemento).height());

                                    muestra_imagen_png(
                                        nombre_imagen + ".png",
                                        imagen_png_base64,
                                        CODIFICACION_BASE_64,
                                        nombre_ventana,
                                        anchura_ventana,
                                        altura_ventana);
                                })
                                .catch(function (error) {
                                    jAlert(TLNT.Idiomas._("Ha ocurrido un error al generar la imagen"));
                                });
                        }
                        else {
                            html2canvas(elemento, {
                                onrendered: function(canvas) {
                                    var imagen_png_base64 = canvas.toDataURL("image/png");
                                    var anchura_ventana = parseInt(canvas.getAttribute("width"));
                                    var altura_ventana = parseInt(canvas.getAttribute("height"));

                                    TLNT.Navegacion.muestra_barra_progreso();
                                    muestra_imagen_png(
                                        nombre_imagen + ".png",
                                        imagen_png_base64,
                                        CODIFICACION_BASE_64,
                                        nombre_ventana,
                                        anchura_ventana,
                                        altura_ventana);
                                }
                            });
                        }
                        break;
                    }
                }
                break;
            }
            case 'exportar_valores_' + tipo_origen + '_' + id: {
                switch (tipo_origen) {
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_JQPLOT:
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_JQPLOT_WIDGET: {
                        // Se recuperan los nombres y las filas de valores de las series de valores de la gráfica
                        // y se guardan los ficheros CSV (comprimidos en un fichero 'zip')
                        var nombres_series_valores = dame_nombres_series_valores_grafica(id);
                        var numero_series_valores = nombres_series_valores.length;
                        var nombre_grafica = id;
                        // Si el origen es un widget se elimina el id del nombre de la gráfica
                        if (tipo_origen == TIPO_ORIGEN_MENU_CONTEXTUAL_JQPLOT_WIDGET) {
                            nombre_grafica = nombre_grafica.split("__")[0];
                        }
                        var nombre_fichero_zip = nombre_grafica;
                        var nombres_ficheros_csv = [];
                        var filas_valores_series_valores = [];
                        for (var i = 0; i < numero_series_valores; i++) {
                            // Nota: Si no hay nombre de serie, se establece el nombre del id
                            var nombre_fichero_csv = null;
                            var numero_fichero_csv = i + 1;
                            if (nombres_series_valores[i] == null) {
                                nombre_fichero_csv = nombre_grafica;
                                if (numero_series_valores > 1) {
                                    nombre_fichero_csv += "_" + numero_fichero_csv;
                                }
                            }
                            else {
                                nombre_fichero_csv = nombres_series_valores[i];
                            }
                            var filas_valores_serie_valores = dame_filas_valores_serie_valores_grafica(id, i);
                            nombres_ficheros_csv.push(nombre_fichero_csv);
                            filas_valores_series_valores.push(filas_valores_serie_valores);
                        }

                        TLNT.Navegacion.muestra_barra_progreso();
                        guarda_valores_csv(
                            nombre_fichero_zip,
                            nombres_ficheros_csv,
                            filas_valores_series_valores,
                            true,
                            null);
                        break;
                    }
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_TABLA_DATOS: {
                        // Se obtienen las filas de valores de la tabla de datos y se guarda el fichero CSV (sin comprimir)
                        var filas_valores_tabla_datos = dame_filas_valores_tabla_datos(id);
                        if (filas_valores_tabla_datos == null) {
                            jAlert(TLNT.Idiomas._("No se han podido exportar los valores"));
                        }
                        else {
                            var elementos_id = id.split("__");
                            var id_tabla = elementos_id[elementos_id.length - 1];
                            var nombre_fichero_csv = id_tabla;

                            TLNT.Navegacion.muestra_barra_progreso();
                            guarda_valores_csv(
                                null,
                                [nombre_fichero_csv],
                                [filas_valores_tabla_datos],
                                true,
                                null);
                        }
                        break;
                    }
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_MAPA_CALOR:
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_MAPA_CALOR_WIDGET: {
                        // Se guardan los valores del mapa de calor
                        guarda_valores_mapa_calor(id, tipo_origen, null, null);
                        break;
                    }
                }
                break;
            }
            case 'anyadir_comentario_' + tipo_origen + '_' + id: {
                var modulo_informe = $(this).attr('modulo_informe');
                var tipo_informe = $(this).attr('tipo_informe');
                var informacion_extra_informe = $(this).attr('informacion_extra_informe');
                var numero_elemento_plantilla_informe = $(this).attr('numero_elemento_plantilla_informe');
                switch (tipo_origen) {
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_JQPLOT: {
                        muestra_ventana_anyadir_comentario_grafica(
                            tipo_origen,
                            id,
                            modulo_informe,
                            tipo_informe,
                            informacion_extra_informe,
                            numero_elemento_plantilla_informe);
                        break;
                    }
                }
                break;
            }
            case 'anyadir_comentarios_' + tipo_origen + '_' + id: {
                var modulo_informe = $(this).attr('modulo_informe');
                var tipo_informe = $(this).attr('tipo_informe');
                var informacion_extra_informe = $(this).attr('informacion_extra_informe');
                var numero_elemento_plantilla_informe = $(this).attr('numero_elemento_plantilla_informe');
                switch (tipo_origen) {
                    case TIPO_ORIGEN_MENU_CONTEXTUAL_JQPLOT: {
                        muestra_ventana_anyadir_comentarios_grafica(
                            tipo_origen,
                            id,
                            modulo_informe,
                            tipo_informe,
                            informacion_extra_informe,
                            numero_elemento_plantilla_informe);
                        break;
                    }
                }
                break;
            }
        }

        // Se oculta el menú contextual
        $('#menu-contextual-' + id).hide();
    });

    // Se ejecuta esta acción cuando se vaya a mostrar el menú contextual:
    // - Se desactiva el comportamiento por defecto (menú contextual del navegador)
    // - Se detiene la propagación del evento (para que en la tablas de detalles no se muestre también el menú de la tabla 'contenedora' detrás)
    // - Se muestra el menú contextual
    $('#' + id).unbind('contextmenu');
    $('#' + id).bind('contextmenu', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $('#menu-contextual-' + id).toggle(100).
            css({
                top: event.pageY + 'px',
                left: event.pageX + 'px'
            });
    });
}


//
// Funciones de opciones de menús contextuales
//


// Guarda la imagen PNG y la muestra en una ventana independiente
function muestra_imagen_png(
    nombre_fichero,
    imagen_png_base64,
    codificacion_imagen,
    nombre_ventana,
    anchura,
    altura) {
    var imagen_png_base64_sin_cabecera = imagen_png_base64.replace(/^data:image\/(png|jpg);base64,/, "");

    $.post("./src/lib/modulos/imagenes/guarda_imagen_servidor_datos_imagen.php", {
        nombre_fichero: nombre_fichero,
        datos_imagen: imagen_png_base64_sin_cabecera,
        codificacion_imagen: codificacion_imagen
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        try {
            // Se muestra la imagen en una ventana emergente
            var anchura_ventana = anchura + MARGEN_DERECHA_VENTANA_PNG;
            var altura_ventana = altura + MARGEN_ABAJO_VENTANA_PNG;

            // Se añade la variable 'refresh_timestamp' para que se actualice la imagen y no muestre la que tenga el navegador guardada en cache (ocurre en IExplorer):
            // - http://stackoverflow.com/questions/2104949/how-to-reload-refresh-an-elementimage-in-jquery
            // - http://stackoverflow.com/questions/16822831/what-is-the-best-way-to-force-an-image-refresh-on-a-webpage
            var ventana_imagen = window.open("", "_blank", "width=" + anchura_ventana + ", height=" + altura_ventana + ", location=0, menubar=0, status=0, toolbar=0");

            // https://stackoverflow.com/questions/20424279/canvas-todataurl-securityerror
            ventana_imagen.document.write('<html><head><title>' + escapeHtml(nombre_ventana) + '</title></head><body>' +
                '<img crossOrigin="anonymous" src="' + resultado.ruta_fichero_imagen + '?refresh_timestamp=' + new Date().getTime() + '"></img></body></html>');
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
    });
}


// Guarda los valores en ficheros CSV (si es más de 1 se comprimen en un fichero 'zip')
function guarda_valores_csv(
    nombre_fichero_zip,
    nombres_ficheros_csv,
    filas_valores_ficheros_csv,
    mostrar_mensaje_informacion,
    funcion_procesa_fichero_valores) {
    // Nota: Si no se pasan los valores codificados en una cadena de json, sólo se reciben hasta 500 elementos del primer vector de valores en PHP
    // Hay que comprobar el tamano de esta cadena, para que no exceda el límite de PHP (la cadena se pasa url-encoded)
    var filas_valores_ficheros_csv_string = nombre_fichero_zip + nombres_ficheros_csv + JSON.stringify(filas_valores_ficheros_csv);
    var filas_valores_ficheros_csv_size = encodeURIComponent(filas_valores_ficheros_csv_string).length;
    if (filas_valores_ficheros_csv_size > MAX_POST_SIZE_IN_BYTES) {
        jAlert(TLNT.Idiomas._("La solicitud excede el límite de datos permitido. Por favor, reduzca el rango de fechas e intente de nuevo."));
        TLNT.Navegacion.oculta_barra_progreso();
        return;
    }
    $.post("./src/lib/modulos/guarda_valores_csv.php", {
        nombre_fichero_zip: nombre_fichero_zip,
        nombres_ficheros_csv: nombres_ficheros_csv,
        filas_valores_ficheros_csv: JSON.stringify(filas_valores_ficheros_csv)
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }
	if (resultado.ruta_fichero_valores == null) {
        return;
	}

        if (mostrar_mensaje_informacion == true) {
            jInfo(resultado.msg);
        }

        // Si existe la función que procesa el fichero de valores, se ejecuta
        if (funcion_procesa_fichero_valores != null) {
            funcion_procesa_fichero_valores(resultado.ruta_fichero_valores);
        }
        else {
            // Se guarda el fichero de valores exportados
            window.location.href = resultado.ruta_fichero_valores;
        }
    });
}


// Guarda los valores de un mapa de calor
function guarda_valores_mapa_calor(
    id,
    tipo_origen,
    nombre_fichero_csv_personalizado,
    funcion_procesa_fichero_valores) {
    // Se obtienen las filas de valores del mapa de calor y se guarda el fichero CSV (sin comprimir)
    var filas_valores_mapa_calor = dame_filas_valores_mapa_calor(id);
    if (filas_valores_mapa_calor == null) {
        jAlert(TLNT.Idiomas._("No se han podido exportar los valores"));
    }
    else {
        // Nombre del fichero CSV y flag para mostrar el mensaje de información
        // - Si el origen es un widget se elimina el id del nombre del mapa de calor
        // - Si el origen es de un informe, tiene un nombre personalizado
        var nombre_fichero_csv = null;
        var mostrar_mensaje_informacion = true;
        switch (tipo_origen) {
            case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_MAPA_CALOR: {
                nombre_fichero_csv = id;
                break;
            }
            case TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_MAPA_CALOR_WIDGET: {
                nombre_fichero_csv = id.split("__")[0];
                break;
            }
            case TIPO_ORIGEN_MENU_CONTEXTUAL_INFORME: {
                nombre_fichero_csv = nombre_fichero_csv_personalizado;
                mostrar_mensaje_informacion = false;
                break;
            }
        }

        // Se exportan los valores
        TLNT.Navegacion.muestra_barra_progreso();
        guarda_valores_csv(
            null,
            [nombre_fichero_csv],
            [filas_valores_mapa_calor],
            mostrar_mensaje_informacion,
            funcion_procesa_fichero_valores);
    }
}


// Muestra la ventana de añadir comentario (desde una gráfica)
function muestra_ventana_anyadir_comentario_grafica(
    tipo_origen,
    id,
    modulo_informe,
    tipo_informe,
    informacion_extra_informe,
    numero_elemento_plantilla_informe) {
    // Se recupera la fecha y hora del comentario (si existen)
    var cadena_fecha_hora_local = null;
    if (fecha_hora_grafica_boton_derecho != null) {
        cadena_fecha_hora_local = convierte_fecha_a_cadena(fecha_hora_grafica_boton_derecho, formato_fecha_local_jquery_ui);
        cadena_fecha_hora_local += ", " + dame_cadena_hora(fecha_hora_grafica_boton_derecho);
    }
    else {
        jAlert(TLNT.Idiomas._('Pulse en el interior de la gráfica para añadir el comentario'));
        return;
    }

    // Se calculan el origen y los parámetros del comentario y se muestra la ventana de añadir comentario
    var modulo = null;
    var objeto = null;
    var origen_comentarios = null;
    var parametros_origen_comentarios = null;
    switch (tipo_informe) {
        case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA:
        case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD:
        case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR:
        case TIPO_INFORME_SENSORES_INFORMACION_VIENTO:
        case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
        case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
        case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION:
        case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA:
        case TIPO_INFORME_SENSORES_INFORMACION_GAS:
        case TIPO_INFORME_SENSORES_INFORMACION_AGUA:
        case TIPO_INFORME_SENSORES_INFORMACION_GENERICA: {
            objeto = nombre_primera_serie_grafica_boton_derecho;
            if (numero_elemento_plantilla_informe == null) {
                modulo = MODULO_SENSORES;
                origen_comentarios = ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION;
                parametros_origen_comentarios = tipo_informe;
            }
            else {
                modulo = MODULO_PERSONAL;
                origen_comentarios = ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                parametros_origen_comentarios = tipo_informe + "," + numero_elemento_plantilla_informe;
            }
            break;
        }
        case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS: {
            objeto = nombre_primera_serie_grafica_boton_derecho;
            var destino_accion = informacion_extra_informe;
            if (numero_elemento_plantilla_informe == null) {
                modulo = MODULO_ACTUADORES;
                switch (destino_accion) {
                    case DESTINO_ACCION_ACTUADOR: {
                        origen_comentarios = ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR;
                        break;
                    }
                    case DESTINO_ACCION_GRUPO_ACTUADORES: {
                        origen_comentarios = ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES;
                        break;
                    }
                }
                parametros_origen_comentarios = tipo_informe;
            }
            else {
                modulo = MODULO_PERSONAL;
                switch (destino_accion) {
                    case DESTINO_ACCION_ACTUADOR: {
                        origen_comentarios = ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR;
                        break;
                    }
                    case DESTINO_ACCION_GRUPO_ACTUADORES: {
                        origen_comentarios = ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES;
                        break;
                    }
                }
                parametros_origen_comentarios = tipo_informe + "," + numero_elemento_plantilla_informe;
            }
            break;
        }
        case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE: {
            var nombre_sensor = informacion_extra_informe;
            objeto = nombre_sensor;
            if (numero_elemento_plantilla_informe == null) {
                modulo = MODULO_PROYECTOS;
                origen_comentarios = ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE;
                parametros_origen_comentarios = null;
            }
            else {
                modulo = MODULO_PERSONAL;
                origen_comentarios = ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME;
                parametros_origen_comentarios = numero_elemento_plantilla_informe;
            }
            break;
        }
        default: {
            // Nota: Aquí no debería llegar
            return;
        }
    }
    if (origen_comentarios != null) {
        muestra_ventana_anyadir_modificar_comentario(
            modulo,
            ID_NINGUNO,
            TIPO_NINGUNO,
            VISIBILIDAD_PUBLICA,
            origen_comentarios,
            parametros_origen_comentarios,
            cadena_fecha_hora_local,
            objeto);
    }
}


// Muestra la ventana de añadir comentarios (desde una gráfica)
function muestra_ventana_anyadir_comentarios_grafica(
    tipo_origen,
    id,
    modulo_informe,
    tipo_informe,
    informacion_extra_informe,
    numero_elemento_plantilla_informe) {
    // Se recupera la fecha y hora del comentario (si existen)
    var cadena_fecha_hora_local = null;
    if (fecha_hora_grafica_boton_derecho != null) {
        cadena_fecha_hora_local = convierte_fecha_a_cadena(fecha_hora_grafica_boton_derecho, formato_fecha_local_jquery_ui);
        cadena_fecha_hora_local += ", " + dame_cadena_hora(fecha_hora_grafica_boton_derecho);
    }
    else {
        jAlert(TLNT.Idiomas._('Pulse en el interior de la gráfica para añadir los comentarios'));
        return;
    }

    // Se calculan el origen y los parámetros del comentario y se muestra la ventana de añadir comentario
    var modulo = null;
    var ids_objetos = null;
    var objeto = null;
    var origen_comentarios = null;
    var parametros_origen_comentarios = null;
    switch (tipo_informe) {
        case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
            objeto = nombre_serie_grafica_boton_derecho;
            ids_objetos = informacion_extra_informe;
            if (numero_elemento_plantilla_informe == null) {
                modulo = MODULO_SMARTMETER;
                origen_comentarios = ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES;
                parametros_origen_comentarios = null;
            }
            else {
                modulo = MODULO_PERSONAL;
                origen_comentarios = ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME;
                parametros_origen_comentarios = numero_elemento_plantilla_informe;
            }
        }
    }
    if (origen_comentarios != null) {
        muestra_ventana_anyadir_comentarios(
            modulo,
            ids_objetos,
            null,
            null,
            null,
            origen_comentarios,
            parametros_origen_comentarios,
            cadena_fecha_hora_local,
            objeto);
    }
}


//
// Funciones auxiliares
//


// Devuelve en una cadena los estilos definidos en un fichero css
function dame_estilos_hoja_css(nombre_hoja_estilos_css) {
    var estilos_usados = "";
    var hojas_estilo = document.styleSheets;
    for (var i = 0; i < hojas_estilo.length; i++) {
        if (hojas_estilo[i].href != null) {
            var partes_href = hojas_estilo[i].href.split("/");
            var nombre_hoja_estilo = partes_href[partes_href.length - 1];
            if (nombre_hoja_estilo.startsWith(nombre_hoja_estilos_css) == true) {
                var reglas = hojas_estilo[i].cssRules;
                for (var j = 0; j < reglas.length; j++) {
                    var regla = reglas[j];
                    if (typeof(regla.style) != "undefined") {
                        var elementos = document.querySelectorAll(regla.selectorText);
                        if (elementos.length > 0) {
                            estilos_usados += regla.selectorText + " { " + regla.style.cssText + " }\n";
                        }
                    }
                }
                break;
            }
        }
    }

    return (estilos_usados);
}


// Añade menús contextuales de las tablas de datos especificadas
function anyade_menus_contextuales_tablas_datos(ids_nombres_tablas) {
    for (var i = 0; i < ids_nombres_tablas.length; i++) {
        var id_nombre_tabla = ids_nombres_tablas[i];
        var id_tabla = id_nombre_tabla[0];
        var nombre_tabla = id_nombre_tabla[1];
        var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
        anyade_menu_contextual(id_tabla, info_menu_contextual, nombre_tabla);
    }
}
