//
// Funciones sobreescritas
//


// Realiza acciones 'extra' al cargar el contenido inicial
TLNT.Navegacion.realiza_acciones_carga_contenido_inicial = (function() {
    // Aviso de módulos con licencias desactivadas
    if ($('#licencias_desactivadas_modulos').length > 0) {
        jAlert(TLNT.Idiomas._("Existen módulos con licencias desactivadas"));
    }

    // Pantalla completa al inicio
    if (pantalla_completa_inicio == true) {
        TLNT.Navegacion.activa_desactiva_pantalla_completa();
    }
});


// Establece variables globales dependientes de parámetros de la sección
TLNT.Navegacion.establece_variables_globales_parametros_seccion = (function(modulo, seccion, parametros_seccion) {
    // Se busca la medición (si existe) y se establece la variable global
    for (var i = 0; i < parametros_seccion.length; i++) {
        var parametro_seccion = parametros_seccion[i];
        if (parametro_seccion["nombre"] == "medicion") {
            medicion = parametro_seccion["valor"];
        }
    }
});


// Realiza acciones 'extra' al cargar el contenido de una sección
TLNT.Navegacion.realiza_acciones_carga_contenido_seccion = (function(modulo, seccion, carga_contenido_inicial) {
    // Borrado de variables de gráficas
    elimina_graficas_globales();
    elimina_parametros_graficas_globales();

    // Se añaden los menús contextuales a las tablas de datos correspondientes
    var ids_nombres_tablas = [];
    switch (modulo) {
        case MODULO_ADMINISTRACION: {
            ids_nombres_tablas = dame_ids_nombres_tablas_seccion_modulo_administracion(seccion);
            break;
        }
        case MODULO_MONITORIZACION: {
            ids_nombres_tablas = dame_ids_nombres_tablas_seccion_modulo_monitorizacion(seccion);
            break;
        }
        case MODULO_PERSONAL: {
            ids_nombres_tablas = dame_ids_nombres_tablas_seccion_modulo_personal(seccion);
            break;
        }
        case MODULO_RED: {
            ids_nombres_tablas = dame_ids_nombres_tablas_seccion_modulo_red(seccion);
            break;
        }
        case MODULO_LOCALIZACIONES: {
            ids_nombres_tablas = dame_ids_nombres_tablas_seccion_modulo_localizaciones(seccion);
            break;
        }
        case MODULO_SENSORES: {
            ids_nombres_tablas = dame_ids_nombres_tablas_seccion_modulo_sensores(seccion);
            break;
        }
        case MODULO_ACTUADORES: {
            ids_nombres_tablas = dame_ids_nombres_tablas_seccion_modulo_actuadores(seccion);
            break;
        }
        case MODULO_SMARTMETER: {
            ids_nombres_tablas = dame_ids_nombres_tablas_seccion_modulo_smartmeter(seccion);
            break;
        }
        case MODULO_PROYECTOS: {
            ids_nombres_tablas = dame_ids_nombres_tablas_seccion_modulo_proyectos(seccion);
            break;
        }
    }
    anyade_menus_contextuales_tablas_datos(ids_nombres_tablas);

    // Procesado de acción inicial en la sesión
    if (carga_contenido_inicial == true) {
        $.ajax({
            url: "./comun/src/lib/herramientas/dame_elimina_accion_inicial_sesion.php",
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

                // Se recupera la acción inicial
                var accion_inicial = resultado.accion_inicial;
                if (accion_inicial == null) {
                    return;
                }

                // Se ejecuta la acción inicial
                var parametros_accion_inicial = resultado.parametros_accion_inicial;
                switch (accion_inicial) {
                    case ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS: {
                        if ((modulo == MODULO_PERSONAL) && (seccion == SECCION_PERSONAL_WIDGETS)) {
                            // Parámetros de la actualización periódica de widgets
                            var pantalla_completa_activada_accion_inicial = (
                                (parametros_accion_inicial[INDICE_PARAMETRO_ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS_PANTALLA_COMPLETA_ACTIVADA] == true) ||
                                (parametros_accion_inicial[INDICE_PARAMETRO_ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS_PANTALLA_COMPLETA_ACTIVADA] == "true"));
                            var id_pestanya_accion_inicial = parametros_accion_inicial[INDICE_PARAMETRO_ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS_ID_PESTANYA];
                            var segundos_intervalo_actualizacion_widgets_accion_inicial = parametros_accion_inicial[INDICE_PARAMETRO_ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS_SEGUNDOS];

                            // Se restaura la pantalla completa y se activa la actualización periódica de widgets
                            // - Si ya estaba la pantalla completa activada, hay que realizar las acciones de pantalla completa de la sección
                            if (pantalla_completa_activada_accion_inicial != pantalla_completa_activada) {
                                TLNT.Navegacion.activa_desactiva_pantalla_completa();
                            }
                            else {
                                if (pantalla_completa_activada_accion_inicial == true) {
                                    TLNT.Navegacion.realiza_acciones_pantalla_completa_seccion();
                                }
                            }
                            activa_actualizacion_periodica_cuadricula_widgets(
                                id_pestanya_accion_inicial,
                                segundos_intervalo_actualizacion_widgets_accion_inicial,
                                false);
                        }
                        break;
                    }
                }
            }
        });
    }
});


// Realiza acciones 'extra' al salir de sesion
TLNT.Navegacion.realiza_acciones_salida_sesion = (function() {
    // Borrado de variables de gráficas
    elimina_graficas_globales();
    elimina_parametros_graficas_globales();
});


// Realiza acciones al modificar la pantalla completa dependiendo de la sección actual
TLNT.Navegacion.realiza_acciones_pantalla_completa_seccion = (function() {
    var modulo_actual = TLNT.Navegacion.modulo_actual;
    var seccion_actual = TLNT.Navegacion.seccion_actual;

    switch (modulo_actual) {
        case MODULO_PERSONAL: {
            switch (seccion_actual) {
                case SECCION_PERSONAL_WIDGETS: {
                    if (pantalla_completa_activada == true) {
                        // Se oculta la tabla de selección de localización
                        $('#tabla-seleccion-localizacion-actual').hide();

                        // Se recupera la pestaña activa
                        var href_pestanya = $('#tabs-pestanyas-widgets .active > a').attr("href");
                        if (href_pestanya !== undefined) {
                            var id_pestanya = href_pestanya.split("__")[1];

                            // Se crean los datos del formulario
                            var datos_formulario = new FormData();
                            datos_formulario.append("id_pestanya", id_pestanya);

                            // Se recuperan las opciones de pantalla completa de pestañas de widgets
                            $.ajax({
                                url: "./src/lib/modulos/widgets/dame_opciones_pantalla_completa_pestanya_widgets.php",
                                type: "POST",
                                async: false,
                                data: datos_formulario,
                                processData: false,
                                contentType: false,
                                success: function(data) {
                                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                                    if (resultado == null) {
                                        return;
                                    }

                                    // Opciones de pantalla completa de pestañas de widgets
                                    var opciones_pantalla_completa = resultado.opciones_pantalla_completa;
                                    var modificar = parseInt(opciones_pantalla_completa.modificar);
                                    var mostrar_opciones = parseInt(opciones_pantalla_completa.mostrar_opciones);
                                    var estilo_fuente_titulo = opciones_pantalla_completa.estilo_fuente_titulo;
                                    var color = opciones_pantalla_completa.color;
                                    var color_fondo = opciones_pantalla_completa.color_fondo;
                                    var mostrar_pie_pagina = parseInt(opciones_pantalla_completa.mostrar_pie_pagina);

                                    // Si no hay que modificar, no se hace nada
                                    if (modificar == VALOR_NO) {
                                        return;
                                    }

                                    // Se ocultan las pestañas de widgets
                                    // Nota: Si no se modifican las opciones de pantalla completa no se ocultan las pestañas de widgets
                                    $('#tabs-pestanyas-widgets').hide();

                                    // Color de título y color de fondo
                                    $('.titulo-tabla-datos').css('color', color);
                                    $('.titulo-tabla-datos').css('background-color', color_fondo);
                                    $('.titulo-tabla-datos').css('border', 'solid 1px ' + color);
                                    $('.contenedor-datos-tabla-datos-sin-margenes').css('border-left', 'solid 1px ' + color);
                                    $('.contenedor-datos-tabla-datos-sin-margenes').css('border-right', 'solid 1px ' + color);

                                    $('.contenedor-datos-tabla-datos-sin-margenes').css('border-bottom', 'solid 1px ' + color);
                                    document.body.style.backgroundColor = color_fondo;
                                    $("#contenedor").css('background-color', color_fondo);

                                    // Estilo del texto del título de la pestaña de widgets
                                    if (estilo_fuente_titulo == ESTILO_FUENTE_NORMAL) {
                                        $('.titulo-tabla-datos').css('font-weight', 'normal');
                                    }

                                    // Pie de página
                                    if (mostrar_pie_pagina == VALOR_NO) {
                                        $('#descripcion-perfil').hide();
                                        $('#texto-pie-pagina').hide();
                                        $('#boton-pantalla-completa').css('margin-top', '-1em');
                                        $('#boton-pantalla-completa').css('margin-right', '1em');
                                        $('#boton-pantalla-completa').css('float', 'right');
                                        cambia_color_imagen("boton-pantalla-completa", COLOR_BLANCO, color);
                                        $('#pie-pagina').css('background-color', color_fondo);
                                    }

                                    // Opciones
                                    if (mostrar_opciones == VALOR_SI) {
                                        $('.boton_personal_ayuda_tabla_widgets').css('color', color);
                                        $('.boton_mostrar_ventana_anyadir_widget').css('color', color);
                                        $('.boton_mostrar_ventana_anyadir_modificar_pestanya_widgets').css('color', color);
                                        $('.boton_eliminar_pestanya_widgets').css('color', color);
                                        $('.boton_actualizar_cuadricula_widgets').css('color', color);
                                        $('.boton_actualizacion_periodica_cuadricula_widgets').css('color', color);
                                    }
                                    else {
                                        $('.boton_personal_ayuda_tabla_widgets').hide();
                                        $('.boton_mostrar_ventana_anyadir_widget').hide();
                                        $('.boton_mostrar_ventana_anyadir_modificar_pestanya_widgets').hide();
                                        $('.boton_eliminar_pestanya_widgets').hide();
                                        $('.boton_actualizar_cuadricula_widgets').css('color', color);
                                        $('.boton_actualizacion_periodica_cuadricula_widgets').css('color', color);
                                    }
                                }
                            });
                        }
                    }
                    else {
                        // Se muestra la tabla de selección de localización
                        $('#tabla-seleccion-localizacion-actual').show();

                        // Se restauran las opciones que se han podido cambiar (sin pantalla completa)
                        $('#tabs-pestanyas-widgets').show();

                        // Color de título y colores de fondo
                        $('.titulo-tabla-datos').css('color', COLOR_BLANCO);
                        $('.titulo-tabla-datos').css('background-color', color_tema_oscuro);
                        $('.titulo-tabla-datos').css('border', 'solid 1px ' + color_tema_oscuro);
                        $('.contenedor-datos-tabla-datos-sin-margenes').css('border-left', 'solid 1px ' + color_tema_oscuro);
                        $('.contenedor-datos-tabla-datos-sin-margenes').css('border-right', 'solid 1px ' + color_tema_oscuro);
                        $('.contenedor-datos-tabla-datos-sin-margenes').css('border-bottom', 'solid 1px ' + color_tema_oscuro);
                        document.body.style.backgroundColor = COLOR_GRIS_FONDO;
                        $("#contenedor").css('background-color', color_tema_fondo);

                        // Estilo del texto del título de la pestaña de widgets
                        $('.titulo-tabla-datos').css('font-weight', 'bold');

                        // Pie de página
                        $('#descripcion-perfil').show();
                        $('#texto-pie-pagina').show();
                        $('#boton-pantalla-completa').css('margin-top', '0em');
                        $('#boton-pantalla-completa').css('margin-right', '0em');
                        $('#boton-pantalla-completa').css('float', 'none');
                        document.getElementById("boton-pantalla-completa").src = "./comun/rsc/imagenes/pantalla_completa.png";
                        actualiza_pie_pagina();

                        // Botones (de opciones) de la pestaña de widgets
                        $('.boton_personal_ayuda_tabla_widgets').css('color', COLOR_BLANCO);
                        $('.boton_mostrar_ventana_anyadir_widget').css('color', COLOR_BLANCO);
                        $('.boton_mostrar_ventana_anyadir_modificar_pestanya_widgets').css('color', COLOR_BLANCO);
                        $('.boton_eliminar_pestanya_widgets').css('color', COLOR_BLANCO);
                        $('.boton_actualizar_cuadricula_widgets').css('color', COLOR_BLANCO);
                        $('.boton_actualizacion_periodica_cuadricula_widgets').css('color', COLOR_BLANCO);
                        $('.boton_personal_ayuda_tabla_widgets').show();
                        $('.boton_mostrar_ventana_anyadir_widget').show();
                        $('.boton_mostrar_ventana_anyadir_modificar_pestanya_widgets').show();
                        $('.boton_eliminar_pestanya_widgets').show();
                    }
                    break;
                }
            }
            break;
        }
        case MODULO_RED: {
            switch (seccion_actual) {
                case SECCION_RED_MAPA: {
                    if (pantalla_completa_activada == true) {
                        var altura_mapa = ($(window).height() - MARGEN_ALTURA_MAPA_SECCION_PANTALLA_COMPLETA);
                        if (altura_mapa < MIN_ALTURA_MAPA_SECCION) {
                            altura_mapa = MIN_ALTURA_MAPA_SECCION;
                        }
                        $('#mapa').height(altura_mapa + "px");
                    }
                    else {
                        var altura_mapa = ($(window).height() - MARGEN_ALTURA_MAPA_SECCION);
                        if (altura_mapa < MIN_ALTURA_MAPA_SECCION) {
                            altura_mapa = MIN_ALTURA_MAPA_SECCION;
                        }
                        $('#mapa').height(altura_mapa + "px");
                    }
                    break;
                }
            }
            break;
        }
        case MODULO_SENSORES: {
            switch (seccion_actual) {
                case SECCION_SENSORES_MAPA: {
                    if (pantalla_completa_activada == true) {
                        $("#tabla-seleccion-localizacion-actual").hide();
                        $("#tabla-sensores-filtro-sensores-mapa").hide();

                        var altura_mapa = ($(window).height() - MARGEN_ALTURA_MAPA_SECCION_PANTALLA_COMPLETA);
                        if (altura_mapa < MIN_ALTURA_MAPA_SECCION) {
                            altura_mapa = MIN_ALTURA_MAPA_SECCION;
                        }
                        $('#mapa').height(altura_mapa + "px");
                    }
                    else {
                        $("#tabla-seleccion-localizacion-actual").show();
                        $("#tabla-sensores-filtro-sensores-mapa").show();

                        var altura_mapa = ($(window).height() - MARGEN_ALTURA_MAPA_SECCION);
                        if (altura_mapa < MIN_ALTURA_MAPA_SECCION) {
                            altura_mapa = MIN_ALTURA_MAPA_SECCION;
                        }
                        $('#mapa').height(altura_mapa + "px");
                    }
                    break;
                }
            }
            break;
        }
        case MODULO_ACTUADORES: {
            switch (seccion_actual) {
                case SECCION_ACTUADORES_MAPA: {
                    if (pantalla_completa_activada == true) {
                        $("#tabla-seleccion-localizacion-actual").hide();
                        $("#tabla-actuadores-filtro-actuadores-mapa").hide();

                        var altura_mapa = ($(window).height() - MARGEN_ALTURA_MAPA_SECCION_PANTALLA_COMPLETA);
                        if (altura_mapa < MIN_ALTURA_MAPA_SECCION) {
                            altura_mapa = MIN_ALTURA_MAPA_SECCION;
                        }
                        $('#mapa').height(altura_mapa + "px");
                    }
                    else {
                        $("#tabla-seleccion-localizacion-actual").show();
                        $("#tabla-actuadores-filtro-actuadores-mapa").show();

                        var altura_mapa = ($(window).height() - MARGEN_ALTURA_MAPA_SECCION);
                        if (altura_mapa < MIN_ALTURA_MAPA_SECCION) {
                            altura_mapa = MIN_ALTURA_MAPA_SECCION;
                        }
                        $('#mapa').height(altura_mapa + "px");
                    }
                    break;
                }
            }
            break;
        }
    }
});


// Procesa un error de ajax
TLNT.Navegacion.procesa_error_ajax = (function(error) {
    var mostrar_mensaje_error = true;
    if (error == "") {
        // Si el error es desconocido (probablemente sin conexión a internet)
        // (https://stackoverflow.com/questions/15433598/detecting-error-106-neterr-internet-disconnected?rq=1)
        // - Si hay actualización automática de widgets, no se muestra el error y seguira la actualización normalmente
        //   (salvo que no se actualizará la pestaña que ha dado el error en la iteración actual)
        if ((TLNT.Navegacion.modulo_actual == MODULO_PERSONAL) && (TLNT.Navegacion.seccion_actual == SECCION_PERSONAL_WIDGETS)) {
            if (temporizador_actualizacion_pagina != null) {
                mostrar_mensaje_error = false;
            }
        }
    };
    return (mostrar_mensaje_error);
});


//
// Funciones de información local y de preferencias
//


// Recupera información 'extra' local
TLNT.Navegacion.recupera_informacion_extra_local_resultado = (function(resultado) {
    // Formatos de fecha
    formato_fecha_local_jqplot = resultado.formato_fecha_local_jqplot;
    formato_fecha_local_jquery_ui = resultado.formato_fecha_local_jquery_ui;
    formato_dia_anyo_local_jquery_ui = resultado.formato_dia_anyo_local_jquery_ui;
    formato_dia_anyo_local_jquery_ui = resultado.formato_dia_anyo_local_jquery_ui;

    // Unidades de medida
    moneda = resultado.moneda;
    unidad_medida_temperatura = resultado.unidad_medida_temperatura;
    unidad_medida_velocidad = resultado.unidad_medida_velocidad;

    // Países de tarifas
    pais_tarifas_electricas = resultado.pais_tarifas_electricas;
    pais_tarifas_gas = resultado.pais_tarifas_gas;
    pais_tarifas_agua = resultado.pais_tarifas_agua;

    // Medición por defecto
    medicion = resultado.medicion_defecto;
});


// Recupera información 'extra' de las preferencias actuales
TLNT.Navegacion.recupera_informacion_extra_preferencias_actuales_resultado = (function(resultado) {
    TLNT.Navegacion.titulo = resultado.titulo_web;

    pantalla_completa_inicio = resultado.pantalla_completa_inicio;
    exportacion_valores_sensores = resultado.exportacion_valores_sensores;
    administracion_comentarios_sensores = resultado.administracion_comentarios_sensores;
    administracion_comentarios_actuadores = resultado.administracion_comentarios_actuadores;

    var paleta_colores_graficas = resultado.paleta_colores_graficas;
    switch (paleta_colores_graficas) {
        case PALETA_COLORES_GRAFICAS_DEFECTO: {
            colores_graficas_jqplot = COLORES_GRAFICAS_JQPLOT_DEFECTO;
            break;
        }
        case PALETA_COLORES_GRAFICAS_ORIGINAL: {
            colores_graficas_jqplot = COLORES_GRAFICAS_JQPLOT_ORIGINAL;
            break;
        }
        case PALETA_COLORES_GRAFICAS_ALTO_CONTRASTE: {
            colores_graficas_jqplot = COLORES_GRAFICAS_JQPLOT_ALTO_CONTRASTE;
            break;
        }
        case PALETA_COLORES_GRAFICAS_EJENER: {
            colores_graficas_jqplot = COLORES_GRAFICAS_JQPLOT_EJENER;
            break;
        }
    };
});


// Establece los eventos de los controles de las secciones (por módulo)
TLNT.Navegacion.establece_eventos_secciones = (function() {
    var modulo = $('#modulo').attr('name');
    switch (modulo) {
        case MODULO_ACTUADORES: {
            TLNT.Navegacion.establece_eventos_secciones_actuadores();
            break;
        }
        case MODULO_ADMINISTRACION: {
            TLNT.Navegacion.establece_eventos_secciones_administracion();
            break;
        }
        case MODULO_LOCALIZACIONES: {
            TLNT.Navegacion.establece_eventos_secciones_localizaciones();
            break;
        }
        case MODULO_MONITORIZACION: {
            TLNT.Navegacion.establece_eventos_secciones_monitorizacion();
            break;
        }
        case MODULO_PERSONAL: {
            TLNT.Navegacion.establece_eventos_secciones_personal();
            break;
        }
        case MODULO_RED: {
            TLNT.Navegacion.establece_eventos_secciones_red();
            break;
        }
        case MODULO_SENSORES: {
            TLNT.Navegacion.establece_eventos_secciones_sensores();
            break;
        }
        case MODULO_SMARTMETER: {
            TLNT.Navegacion.establece_eventos_secciones_smartmeter();
            break;
        }
        case MODULO_PROYECTOS: {
            TLNT.Navegacion.establece_eventos_secciones_proyectos();
            break;
        }
        default: {
            return;
        }
    }

    TLNT.Navegacion.establece_eventos_secciones_modulos();
});


// Establece los eventos de los controles del contenido de los informes (por módulo)
TLNT.Navegacion.establece_eventos_contenido_informes = (function() {
    var modulo = $('#modulo').attr('name');
    switch (modulo) {
        case MODULO_PERSONAL: {
            TLNT.Navegacion.establece_eventos_contenido_informes_personal();
            break;
        }
        case MODULO_SMARTMETER: {
            TLNT.Navegacion.establece_eventos_contenido_informes_smartmeter();
            break;
        }
        default: {
            return;
        }
    }
});


// Establece los eventos de los controles de las tablas de datos (por módulo)
TLNT.Navegacion.establece_eventos_tablas_datos = (function() {
    var modulo = $('#modulo').attr('name');
    switch (modulo) {
        case MODULO_ACTUADORES: {
            TLNT.Navegacion.establece_eventos_tablas_datos_actuadores();
            break;
        }
        case MODULO_ADMINISTRACION: {
            TLNT.Navegacion.establece_eventos_tablas_datos_administracion();
            break;
        }
        case MODULO_LOCALIZACIONES: {
            TLNT.Navegacion.establece_eventos_tablas_datos_localizaciones();
            break;
        }
        case MODULO_MONITORIZACION: {
            TLNT.Navegacion.establece_eventos_tablas_datos_monitorizacion();
            break;
        }
        case MODULO_PERSONAL: {
            TLNT.Navegacion.establece_eventos_tablas_datos_personal();
            break;
        }
        case MODULO_RED: {
            TLNT.Navegacion.establece_eventos_tablas_datos_red();
            break;
        }
        case MODULO_SENSORES: {
            TLNT.Navegacion.establece_eventos_tablas_datos_sensores();
            break;
        }
        case MODULO_SMARTMETER: {
            TLNT.Navegacion.establece_eventos_tablas_datos_smartmeter();
            break;
        }
        case MODULO_PROYECTOS: {
            TLNT.Navegacion.establece_eventos_tablas_datos_proyectos();
            break;
        }
        default: {
            return;
        }
    }

    TLNT.Navegacion.establece_eventos_tablas_datos_modulos();
});


// Establece los eventos de los controles de los detalles de las tablas de datos (por módulo)
TLNT.Navegacion.establece_eventos_detalles_tablas_datos = (function() {
    var modulo = $('#modulo').attr('name');
    switch (modulo) {
        case MODULO_ACTUADORES: {
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos_actuadores();
            break;
        }
        case MODULO_ADMINISTRACION: {
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos_administracion();
            break;
        }
        case MODULO_LOCALIZACIONES: {
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos_localizaciones();
            break;
        }
        case MODULO_MONITORIZACION: {
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos_monitorizacion();
            break;
        }
        case MODULO_PERSONAL: {
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos_personal();
            break;
        }
        case MODULO_RED: {
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos_red();
            break;
        }
        case MODULO_SENSORES: {
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos_sensores();
            break;
        }
        case MODULO_SMARTMETER: {
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos_smartmeter();
            break;
        }
        case MODULO_PROYECTOS: {
            TLNT.Navegacion.establece_eventos_detalles_tablas_datos_proyectos();
            break;
        }
        default: {
            return;
        }
    }

    TLNT.Navegacion.establece_eventos_detalles_tablas_datos_modulos();
});


// Acciones 'extra' a realizar en los detalles de la tabla de datos
TLNT.Navegacion.realiza_acciones_mostrado_detalle_tabla_datos = (function(resultado) {
    var ids_nombres_tablas = resultado.ids_nombres_tablas;
    anyade_menus_contextuales_tablas_datos(ids_nombres_tablas);
});


// Establece los eventos de los controles de las ventanas modales (por módulo)
TLNT.Navegacion.establece_eventos_ventanas_modales = (function() {
    var modulo = $('#modulo').attr('name');
    switch (modulo) {
        case MODULO_ACTUADORES: {
            TLNT.Navegacion.establece_eventos_ventanas_modales_actuadores();
            break;
        }
        case MODULO_ADMINISTRACION: {
            TLNT.Navegacion.establece_eventos_ventanas_modales_administracion();
            break;
        }
        case MODULO_LOCALIZACIONES: {
            TLNT.Navegacion.establece_eventos_ventanas_modales_localizaciones();
            break;
        }
        case MODULO_MONITORIZACION: {
            TLNT.Navegacion.establece_eventos_ventanas_modales_monitorizacion();
            break;
        }
        case MODULO_PERSONAL: {
            TLNT.Navegacion.establece_eventos_ventanas_modales_personal();
            break;
        }
        case MODULO_RED: {
            TLNT.Navegacion.establece_eventos_ventanas_modales_red();
            break;
        }
        case MODULO_SENSORES: {
            TLNT.Navegacion.establece_eventos_ventanas_modales_sensores();
            break;
        }
        case MODULO_SMARTMETER: {
            TLNT.Navegacion.establece_eventos_ventanas_modales_smartmeter();
            break;
        }
        case MODULO_PROYECTOS: {
            TLNT.Navegacion.establece_eventos_ventanas_modales_proyectos();
            break;
        }
        default: {
            return;
        }
    }

    TLNT.Navegacion.establece_eventos_ventanas_modales_modulos();
});


//
// Funciones auxiliares
//


// Conversión a lista doble
TLNT.Navegacion.convierte_lista_doble = (function(ids_elementos_lista, mostrar_filtro) {
    // Nota: Si el navegador es de un dispositivo móvil las listas dobles no se ven correctamente, no se hace nada
    if (dame_navegador_dispositivo_movil() == true) {
        return;
    }

    var filtro = false;
    if (mostrar_filtro == true) {
        filtro = TLNT.Idiomas._('Filtro') + ": ";
    }
    var max_selected = null;
    var altura_lista_doble = null;
    if ($('#' + ids_elementos_lista).attr("max_selected")) {
        max_selected = $('#' + ids_elementos_lista).attr("max_selected");
        altura_lista_doble = (max_selected * 1.5);
    }
    else {
        max_selected = -1;
        altura_lista_doble = ALTURA_MINIMA_LISTA_DOBLE;
    }
    if ((altura_lista_doble < 0) || (altura_lista_doble > ALTURA_MAXIMA_LISTA_DOBLE)) {
        altura_lista_doble = ALTURA_MAXIMA_LISTA_DOBLE;
    }
    else {
        if (altura_lista_doble < ALTURA_MINIMA_LISTA_DOBLE) {
            altura_lista_doble = ALTURA_MINIMA_LISTA_DOBLE;
        }
    }
    // Espacio para las barras horizontales de scroll
    altura_lista_doble += 1;
    $('#' + ids_elementos_lista).attr("size", altura_lista_doble);
    $('#' + ids_elementos_lista).multiselect2side({
        selectedPosition: 'right',
        moveOptions: false,
        labelsx: '',
        labeldx: ' ',
        autoSort: true,
        autoSortAvailable: true,
        maxSelected: max_selected,
        search: filtro
    });
    $('#' + ids_elementos_lista + 'ms2side__sx').css('height', altura_lista_doble + "em");
    $('#' + ids_elementos_lista + 'ms2side__dx').css('height', altura_lista_doble + "em");

    // Función para mostrar tooltips con el texto completo cuando no quepa entero en la lista
    // (Nota: No funciona en 'Internet Explorer' (sí en Chrome, FireFox y Edge))
    // (https://stackoverflow.com/questions/5474871/html-how-can-i-show-tooltip-only-when-ellipsis-is-activated)
    $.fn.tooltipOnOverflow = function(options) {
        $(this).on("mouseenter", function() {
            if (this.offsetWidth < this.scrollWidth) {
                $('#tooltip_general').text($(this).text());
                $('#tooltip_general').offset($(this).offset());
                $('#tooltip_general').css("z-index", 9999);
                $('#tooltip_general').css("opacity", 1);
            }
        });
        $(this).on("mouseleave", function() {
            if (this.offsetWidth < this.scrollWidth) {
                $('#tooltip_general').css("opacity", 0);
                $('#tooltip_general').css("z-index", 0);
            }
        });
    };
    $('.ms2side__select option').tooltipOnOverflow();

    // Reestablecimiento de eventos al modificar los elementos de las listas
    $('#' + ids_elementos_lista + 'ms2side__sx').change(function() {
        $('.ms2side__select option').tooltipOnOverflow();
    });
    $('#' + ids_elementos_lista + 'ms2side__dx').change(function() {
        $('.ms2side__select option').tooltipOnOverflow();
    });

    // Ocultar el tooltip al pulsar cualquier botón del ratón o teclado
    $(document).click(function() {
        $('#tooltip_general').css("opacity", 0);
    });
    $(document).keypress(function() {
        $('#tooltip_general').css("opacity", 0);
    });
});


// Redimensión automática de un 'textarea'
TLNT.Navegacion.redimensiona_textarea = (function(comodin_elementos_textarea) {
    // Nota: Si el navegador es de un navegador Qt no funciona la librería 'autosize'
    // (se establecen 5 filas de altura)
    if (dame_navegador_qt() == true) {
        $(comodin_elementos_textarea).attr('rows', '5');
    }
    else {
        // http://www.jacklmoore.com/autosize/
        autosize.destroy($(comodin_elementos_textarea));
        autosize($(comodin_elementos_textarea));
    }
});


// Actualiza el contador de caracteres máximos de un 'textarea'
TLNT.Navegacion.actualiza_contador_caracteres_textarea = (function(e) {
    var elemento_textarea = $("#" + e.target.id);
    var elemento_contador_caracteres_textarea = $("#" + e.target.id).siblings(".contador-caracteres-textarea");
    var numero_caracteres_actuales_textarea = elemento_textarea.val().length;
    var numero_maximo_caracteres_textarea = elemento_contador_caracteres_textarea.attr("numero_maximo_caracteres");
    elemento_contador_caracteres_textarea.html(" (" + numero_caracteres_actuales_textarea + " / " + numero_maximo_caracteres_textarea + ")");
});


// Nota: El formato de fecha es "Y-m-d"
TLNT.Navegacion.establece_fecha_control = (function(id_control, cadena_fecha) {
    // Nota: Se establece diferente si el navegador es Qt
    var navegador_qt = dame_navegador_qt();
    if (navegador_qt == true) {
        var elementos_fecha = cadena_fecha.split("-");
        var anyo_fecha = parseInt(elementos_fecha[0]);
        var mes_fecha = parseInt(elementos_fecha[1]);
        var dia_fecha = parseInt(elementos_fecha[2]);
        $("#" + id_control).datepicker("update", new Date(anyo_fecha, mes_fecha - 1, dia_fecha + 1));
    }
    else {
        $("#" + id_control).datepicker("update", new Date(cadena_fecha));
    }
});
