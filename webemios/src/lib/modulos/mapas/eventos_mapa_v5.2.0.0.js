//
// Eventos de mapa
//


function mapa_seccion_visible() {
    // Origen del mapa
    var origen_mapa = ORIGEN_MAPA_SECCION;
    var parametros_origen_mapa = {
        "modulo": $('#modulo').attr('name')
    };

    // Identificador del mapa
    var id_mapa = "mapa_seccion";

    // Altura del mapa
    var altura_mapa = ($(window).height() - MARGEN_ALTURA_MAPA_SECCION);
    if (altura_mapa < MIN_ALTURA_MAPA_SECCION) {
        altura_mapa = MIN_ALTURA_MAPA_SECCION;
    }
    $('#' + id_mapa).height(altura_mapa + "px");

    // Se recupera información del mapa
    $.post("./src/lib/modulos/mapas/dame_info_mapa.php", {
        origen_mapa: origen_mapa,
        id_origen_mapa: parametros_origen_mapa
    },
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var opciones_mapa = {
            tipo_mapa: resultado.tipo_mapa,
            nombre_mapa: resultado.nombre_mapa,
            ruta_fichero_imagen_mapa_local: null,
            anchura_imagen_mapa_local: null,
            altura_imagen_mapa_local: null,
            factor_reduccion_imagen_mapa_local: null
        };
        switch (resultado.tipo_mapa) {
            case TIPO_MAPA_LOCAL: {
                // Origen de la imagen del mapa
                var origen_imagen = resultado.origen_imagen;
                var id_origen_imagen = resultado.id_origen_imagen;

                // Se carga la imagen del mapa local
                var res_carga_imagen = carga_imagen_base_datos(origen_imagen, id_origen_imagen, null);
                var imagen_cargada_correcta = res_carga_imagen.imagen_cargada_correcta;
                if (imagen_cargada_correcta == false) {
                    return;
                }
                opciones_mapa.ruta_fichero_imagen_mapa_local = res_carga_imagen.ruta_fichero_imagen;
                opciones_mapa.anchura_imagen_mapa_local = res_carga_imagen.anchura_imagen;
                opciones_mapa.altura_imagen_mapa_local = res_carga_imagen.altura_imagen;
                opciones_mapa.factor_reduccion_imagen_mapa_local = resultado.factor_reduccion_imagen_mapa_local;
                break;
            }
        }

        // Se guardan la posición y el zoom del mapa por defecto en variables globales
        latitud_mapa_defecto = resultado.latitud_mapa_defecto;
        longitud_mapa_defecto = resultado.longitud_mapa_defecto;
        zoom_mapa_defecto = resultado.zoom_mapa_defecto;

        // Se crea el mapa
        crear_mapa(id_mapa, opciones_mapa, mostrar_mapa_seccion, false);
    });
}


function boton_etiquetas_mapa() {
    // Se cambian las etiquetas
    $.post("./src/lib/modulos/mapas/cambia_etiquetas_mapa.php", {},
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se actualiza el mapa
        boton_actualizar_mapa();
    });
}


function boton_centrar_mapa() {
    // Si antes no habia pantalla completa y ahora sí, se invalida el tamaño del mapa
    // (http://stackoverflow.com/questions/21433109/refresh-reload-div-tag-container-after-size-change)
    if ((pantalla_completa_activada != pantalla_completa_activada_ultima_actualizacion_centrado_mapa)) {
        mapa_global.invalidateSize(false);
    }

    // Flag de pantalla completa en la última actualización o centrado del mapa
    pantalla_completa_activada_ultima_actualizacion_centrado_mapa = pantalla_completa_activada;

    // Se establecen la posición y zoom del mapa por defecto
    establece_posicion_zoom_mapa(
        latitud_mapa_defecto,
        longitud_mapa_defecto,
        zoom_mapa_defecto);
}


function boton_actualizar_mapa() {
    // Si antes no habia pantalla completa y ahora sí, se invalida el tamaño del mapa
    // (http://stackoverflow.com/questions/21433109/refresh-reload-div-tag-container-after-size-change)
    if ((pantalla_completa_activada != pantalla_completa_activada_ultima_actualizacion_centrado_mapa)) {
        mapa_global.invalidateSize(false);
    }

    // Flag de pantalla completa en la última actualización o centrado del mapa
    pantalla_completa_activada_ultima_actualizacion_centrado_mapa = pantalla_completa_activada;

    // Se actualiza el mapa
    mostrar_mapa_seccion(true);
}


function boton_actualizacion_periodica_mapa() {
    inicia_actualizacion_periodica_mapa();
}


function inicia_actualizacion_periodica_mapa() {
    // Se activa o desactiva la actualización periódica del mapa
    if (temporizador_actualizacion_pagina == null) {
        jPrompt(TLNT.Idiomas._("Intervalo de actualización periódica de mapa") + " (" + TLNT.Idiomas._("segundos") + ")",
            SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_MAPA_DEFECTO,
            TLNT.Idiomas._("Pregunta"),
            function(valor) {
                if (valor != null) {
                    if ((isNaN(valor) == true) ||
                        ((valor < MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_MAPA_DEFECTO) ||
                         (valor > MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_MAPA_DEFECTO))) {
                        var mensaje_aviso = TLNT.Idiomas._("Intervalo de actualización periódica de mapa no válido") +
                            " (" + MIN_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_MAPA_DEFECTO + " - " + MAX_SEGUNDOS_INTERVALO_ACTUALIZACION_PERIODICA_MAPA_DEFECTO + ")";
                        jAlert(mensaje_aviso, TLNT.Idiomas._("Aviso"), function(res) {
                            inicia_actualizacion_periodica_mapa();
                        });
                    }
                    else {
                        segundos_intervalo_actualizacion_mapa = valor;
                        temporizador_actualizacion_pagina = setTimeout(
                            expiracion_timeout_actualizacion_periodica_mapa,
                            segundos_intervalo_actualizacion_mapa * 1000);
                        jInfo(TLNT.Idiomas._("Actualización periódica de mapa activada"));

                        // Actualizar el icono de actualización periódica
                        $('#boton_actualizacion_periodica_mapa').removeClass("icon-play");
                        $('#boton_actualizacion_periodica_mapa').addClass("icon-pause");

                        // Se actualiza el mapa
                        boton_actualizar_mapa();
                    }
                }
            }
        );
    }
    else {
        clearTimeout(temporizador_actualizacion_pagina);
        temporizador_actualizacion_pagina = null;

        jInfo(TLNT.Idiomas._("Actualización periódica de mapa desactivada"));

        // Actualizar el icono de actualización periódica
        $('#boton_actualizacion_periodica_mapa').removeClass("icon-pause");
        $('#boton_actualizacion_periodica_mapa').addClass("icon-play");
    }
}


function expiracion_timeout_actualizacion_periodica_mapa() {
    boton_actualizar_mapa();
    temporizador_actualizacion_pagina = setTimeout(
        expiracion_timeout_actualizacion_periodica_mapa,
        segundos_intervalo_actualizacion_mapa * 1000);
}


function localizador_mapa_visible(sufijo_controles) {
    // Origen del mapa
    var origen_mapa = $('#origen_mapa' + sufijo_controles).val();
    var id_origen_mapa = JSON.parse($('#id_origen_mapa' + sufijo_controles).val());

    // Parámetros de origen de mapa
    var parametros_origen_mapa = id_origen_mapa;
    var modulo = parametros_origen_mapa["modulo"];
    switch (modulo) {
        case MODULO_LOCALIZACIONES: {
            var tipo_elemento_mapa = parametros_origen_mapa["tipo_elemento_mapa"];
            switch (tipo_elemento_mapa) {
                case TIPO_ELEMENTO_MAPA_INSTALACION: {
                    var id_localizacion = $('#id_localizacion_instalacion').val();
                    parametros_origen_mapa["id_localizacion"] = id_localizacion;
                    break;
                }
                case TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION: {
                    var id_instalacion = $("#parametros_ventana_anyadir_modificar_equipo_instalacion").attr("id_instalacion");
                    parametros_origen_mapa["id_instalacion"] = id_instalacion;
                    break;
                }
            }
            break;
        }
    }
    id_origen_mapa = parametros_origen_mapa;

    // Se recuperan información del mapa
    $.post("./src/lib/modulos/mapas/dame_info_mapa.php", {
        origen_mapa: origen_mapa,
        id_origen_mapa: id_origen_mapa
    },
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var opciones_mapa = {
            tipo_mapa: resultado.tipo_mapa,
            nombre_mapa: resultado.nombre_mapa,
            ruta_fichero_imagen_mapa_local: null,
            anchura_imagen_mapa_local: null,
            altura_imagen_mapa_local: null,
            factor_reduccion_imagen_mapa_local: null
        };
        switch (resultado.tipo_mapa) {
            case TIPO_MAPA_LOCAL: {
                $('#titulo_latitud_longitud_localizador_mapa' + sufijo_controles).text(TLNT.Idiomas._("Coordenadas (y, x)") + ":");

                // Origen de la imagen del mapa
                var origen_imagen = resultado.origen_imagen;
                var id_origen_imagen = resultado.id_origen_imagen;

                // Se carga la imagen del mapa local de la red
                var res_carga_imagen = carga_imagen_base_datos(origen_imagen, id_origen_imagen, null);
                var imagen_cargada_correcta = res_carga_imagen.imagen_cargada_correcta;
                if (imagen_cargada_correcta == false) {
                    return;
                }
                opciones_mapa.ruta_fichero_imagen_mapa_local = res_carga_imagen.ruta_fichero_imagen;
                opciones_mapa.anchura_imagen_mapa_local = res_carga_imagen.anchura_imagen;
                opciones_mapa.altura_imagen_mapa_local = res_carga_imagen.altura_imagen;
                opciones_mapa.factor_reduccion_imagen_mapa_local = resultado.factor_reduccion_imagen_mapa_local;
                break;
            }
            case TIPO_MAPA_INTERNET: {
                $('#titulo_latitud_longitud_localizador_mapa' + sufijo_controles).text(TLNT.Idiomas._("Coordenadas geográficas (latitud, longitud)") + ":");
                break;
            }
        }
        $('#controles_latitud_longitud_mapa' + sufijo_controles).show();

        // Se guardan la posición y el zoom del mapa por defecto en variables globales
        latitud_mapa_defecto = resultado.latitud_mapa_defecto;
        longitud_mapa_defecto = resultado.longitud_mapa_defecto;
        zoom_mapa_defecto = resultado.zoom_mapa_defecto;

        // Se crea el mapa
        crear_mapa("localizador_mapa" + sufijo_controles, opciones_mapa, mostrar_localizador_mapa, sufijo_controles);
    });
}
