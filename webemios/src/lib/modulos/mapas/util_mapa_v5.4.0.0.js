//
// Funciones de mapa
//


function crear_mapa(id_mapa, opciones_mapa, funcion_mostrar_mapa, parametros_mostrar_mapa) {
    // Se guardan las opciones del mapa
    tipo_mapa = opciones_mapa.tipo_mapa;
    nombre_mapa = opciones_mapa.nombre_mapa;
    ruta_fichero_imagen_mapa_local = opciones_mapa.ruta_fichero_imagen_mapa_local;
    anchura_imagen_mapa_local = opciones_mapa.anchura_imagen_mapa_local;
    altura_imagen_mapa_local = opciones_mapa.altura_imagen_mapa_local;
    factor_reduccion_imagen_mapa_local = opciones_mapa.factor_reduccion_imagen_mapa_local;

    // Si ya existe el mapa se elimina
    if (mapa_global != null) {
        mapa_global.off();
        mapa_global.remove();
        mapa_global = null;
    }

    // Se crea el mapa
    var onShowTooltipCallback = TLNT.Navegacion.establece_eventos_contenido_tooltips_mapa;
    switch (tipo_mapa) {
        case TIPO_MAPA_LOCAL: {
            var zoom_minimo = ZOOM_MINIMO_MAPA_LOCAL;
            var zoom_maximo = dame_zoom_maximo_mapa_local();
            mapa_global = L.map(id_mapa, {
                attributionControl: false,
                minZoom: zoom_minimo,
                maxZoom: zoom_maximo,
                crs: L.CRS.Simple,
                onShowTooltipCallback: onShowTooltipCallback
            });
            break;
        }
        case TIPO_MAPA_INTERNET: {
            mapa_global = L.map(id_mapa, {
                attributionControl: false,
                onShowTooltipCallback: onShowTooltipCallback
            });
            break;
        }
    }

    // Se muestra el mapa
    funcion_mostrar_mapa(parametros_mostrar_mapa);

    // Flag de pantalla completa en la última actualización o centrado del mapa
    pantalla_completa_activada_ultima_actualizacion_centrado_mapa = pantalla_completa_activada;
}


function dame_zoom_maximo_mapa_local() {
    var zoom_maximo = ZOOM_MAXIMO_MAPA_LOCAL;
    if (factor_reduccion_imagen_mapa_local > 1) {
        var incremento_zoom_maximo = parseInt(
            Math.log(factor_reduccion_imagen_mapa_local) / Math.log(2));
        zoom_maximo += incremento_zoom_maximo;
    }
    return (zoom_maximo);
}


function dame_capas_base_mapa() {
    var capas_base = {};
    switch (tipo_mapa) {
        case TIPO_MAPA_LOCAL: {
            // https://leafletjs.com/examples/crs-simple/crs-simple.html
            var ruta_fichero_imagen = ruta_fichero_imagen_mapa_local;
            var anchura_imagen = Math.round(anchura_imagen_mapa_local / factor_reduccion_imagen_mapa_local);
            var altura_imagen = Math.round(altura_imagen_mapa_local / factor_reduccion_imagen_mapa_local);
            var rectangulo_imagen = [[0, 0], [altura_imagen, anchura_imagen]];

            // https://stackoverflow.com/questions/46808814/leaflet-put-image-overlay-below-the-map
            mapa_global.createPane("local");
            mapa_global.getPane("local").style.zIndex = 50;
            capas_base[nombre_mapa] = L.imageOverlay(ruta_fichero_imagen, rectangulo_imagen, {pane: "local"});
            capas_base[nombre_mapa].addTo(mapa_global);
            break
        }
        case TIPO_MAPA_INTERNET: {
            // Mapas de internet disponibles
            capas_base[TLNT.Idiomas._("General")] = L.tileLayer.provider('OpenStreetMap.Mapnik');
            capas_base[TLNT.Idiomas._("Urbano")] = L.tileLayer.provider('Esri.WorldStreetMap');
            capas_base[TLNT.Idiomas._("Terreno")] = L.tileLayer.provider('Esri.WorldTopoMap');
            capas_base[TLNT.Idiomas._("Aéreo")] = L.tileLayer.provider('Esri.WorldImagery');
            capas_base[TLNT.Idiomas._("B/N")] = L.tileLayer.provider('Stamen.Toner');

            // Se establece la capa seleccionada (si no existe se establece la capa 'general')
            var nombre_capa_mapa = nombre_mapa;
            if (typeof capas_base[nombre_capa_mapa] == "undefined") {
                nombre_capa_mapa = TLNT.Idiomas._("General");
            }
            capas_base[nombre_capa_mapa].addTo(mapa_global);
            break;
        }
    }
    return (capas_base);
}


function mostrar_mapa_seccion(actualizacion_mapa) {
    // Multiplicador del cluster del mapa
    multiplicador_distancia_cluster_mapa = MULTIPLICADOR_DISTANCIA_CLUSTER_MAPA_SECCION;

    // Nota: Al crear el mapa no se pasa el parámetro 'actualizacion_mapa' (se establece a 'false')
    if (actualizacion_mapa === undefined) {
        actualizacion_mapa = false;
    }

    // Actualización de mapa
    if (actualizacion_mapa == false) {
        // Se establecen la posición y zoom del mapa por defecto
        establece_posicion_zoom_mapa(
            latitud_mapa_defecto,
            longitud_mapa_defecto,
            zoom_mapa_defecto);
    }
    else {
        // Se guardan los nombres de las capas activadas
        // (para añadir en la actualización sólo las capas que estaban activadas)
        var nombres_capas_activadas = [];
        for (var nombre_capa in capas_mapa_globales) {
            if (mapa_global.hasLayer(capas_mapa_globales[nombre_capa]) == true) {
                nombres_capas_activadas.push(nombre_capa);
            }
        }
    }

    // Filtro de mapa
    var res_actualiza_filtro_mapa = actualiza_filtro_mapa();
    var filtro_mapa_modificado = res_actualiza_filtro_mapa.filtro_mapa_modificado;
    var parametros_filtro_mapa = res_actualiza_filtro_mapa.parametros_filtro_mapa;

    // Se recuperan las capas a mostrar en el mapa
    $.post("./src/lib/modulos/mapas/dame_capas_mapa.php", {
        modulo: $("#modulo").attr("name"),
        parametros_filtro_mapa: parametros_filtro_mapa
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // - Si se muestra el mapa por primera vez, se crea el vector de capas globales y se guarda el mapa
        // - Si es una actualización de mapa, se eliminan las capas globales y los controles de selección de capas
        if (actualizacion_mapa == true) {
            // Se eliminan las capas actuales del mapa
            for (var key in capas_mapa_globales) {
                mapa_global.removeLayer(capas_mapa_globales[key]);
            }

            // Se borran los controles de selección de capas (si no se duplican en cada actualización)
            $(".leaflet-control-layers").remove();
        }
        capas_mapa_globales = {};

        // Se crean las capas del mapa
        var capas_overlay = {};
        var info_capas = jQuery.parseJSON(resultado.capas);
        for (var i = 0; i < info_capas.length; i++) {
            // Se crea y rellena la capa del mapa
            var info_capa = info_capas[i];
            var capa = new L.layerGroup();
            rellena_capa_mapa(capa, info_capa);

            // Se añade la capa al mapa y al vector de capas global
            capas_overlay[info_capa.nombre] = capa;
            capas_mapa_globales[info_capa.nombre] = capa;
        }

        // Se añaden las capas al mapa
        var capas_base = dame_capas_base_mapa();
        L.control.layers(capas_base, capas_overlay, {
            collapsed: false
        }).addTo(mapa_global);

        // Se añaden las capas activadas al mapa (https://github.com/Leaflet/Leaflet/issues/3199#event-230611315)
        // - Si es una actualización del mapa y no se ha modificado el filtro, sólo se añaden las capas que ya estaban activadas
        for (var nombre_capa in capas_overlay) {
            if ((actualizacion_mapa == true) && (filtro_mapa_modificado == false)) {
                if (nombres_capas_activadas.indexOf(nombre_capa) == -1) {
                    continue;
                }
            }
            var capa = capas_overlay[nombre_capa];
            mapa_global.addLayer(capa);
        }

        // Se muestra la fecha de actualización del mapa
        actualiza_fecha_actualizacion_mapa();
    });
}


// Filtro de mapa
function actualiza_filtro_mapa(actualizacion_mapa) {
    var modulo = $('#modulo').attr('name');
    var filtro = "";
    var tipo = "";
    var clase = "";
    var id_grupo = "";
    var estado = "";
    var id_ratio = "";
    switch (modulo) {
        case MODULO_LOCALIZACIONES: {
            filtro = $('#filtro_localizaciones_filtro_localizaciones_mapa').val();
            break;
        }
        case MODULO_SENSORES: {
            filtro = $('#filtro_sensores_filtro_sensores_mapa').val();
            tipo = $('#tipo_sensor_sensores_filtro_sensores_mapa').val();
            clase = $('#clase_sensor_sensores_filtro_sensores_mapa').val();
            id_grupo = $('#id_grupo_sensores_sensores_filtro_sensores_mapa').val();
            estado = $('#estado_sensor_sensores_filtro_sensores_mapa').val();
            id_ratio = dame_id_ratio_seleccionado();
            break;
        }
        case MODULO_ACTUADORES: {
            filtro = $('#filtro_actuadores_filtro_actuadores_mapa').val();
            tipo = $('#tipo_actuador_actuadores_filtro_actuadores_mapa').val();
            clase = $('#clase_actuador_actuadores_filtro_actuadores_mapa').val();
            id_grupo = $('#id_grupo_actuadores_actuadores_filtro_actuadores_mapa').val();
            estado = $('#estado_actuador_actuadores_filtro_actuadores_mapa').val();
            break;
        }
    }
    var filtro_mapa_modificado = false;
    if (actualizacion_mapa == true) {
        if ((filtro_filtro_mapa != filtro) ||
            (tipo_filtro_mapa != tipo) ||
            (clase_filtro_mapa != clase) ||
            (id_grupo_filtro_mapa != id_grupo) ||
            (estado_filtro_mapa != estado) ||
            (id_ratio_filtro_mapa != id_ratio)) {
            filtro_mapa_modificado = true;
        }
    }
    filtro_filtro_mapa = filtro;
    tipo_filtro_mapa = tipo;
    clase_filtro_mapa = clase;
    id_grupo_filtro_mapa = id_grupo;
    estado_filtro_mapa = estado;
    id_ratio_filtro_mapa = id_ratio;

    // Parámetros del filtro del mapa
    var parametros_filtro_mapa = {
        filtro: filtro,
        tipo: tipo,
        clase: clase,
        id_grupo: id_grupo,
        estado: estado,
        id_ratio: id_ratio};

    // Resultado de la actualización del filtro del mapa
    var res = {
        filtro_mapa_modificado: filtro_mapa_modificado,
        parametros_filtro_mapa: parametros_filtro_mapa}
    return (res);
}


function rellena_capa_mapa(capa, info_capa) {
    // Se añaden los elementos al mapa
    var cluster = new L.markerClusterGroup({
        spiderfyDistanceMultiplier: multiplicador_distancia_cluster_mapa,
        maxClusterRadius: MAX_RADIO_CLUSTER_MAPAS
    });
    for (var i = 0; i < info_capa.info_elementos.length; i++) {
        var info_elemento = info_capa.info_elementos[i];
        if ((info_elemento.latitud == null) || (info_elemento.longitud == null)) {
            continue;
        }

        // Icono y marcador
        var icono = new L.icon({
            iconUrl: info_elemento.icono,
            iconSize: new L.Point(info_elemento.anchura_icono, info_elemento.altura_icono),
            iconAnchor: new L.Point(info_elemento.anchura_icono / 2, info_elemento.altura_icono),
            shadowUrl: "./rsc/lib/leaflet/dist/images/marker-shadow.png",
            shadowAnchor: new L.Point(ANCHURA_ICONO_SOMBRA / 2 + CORRECION_CENTRADO_HORIZONTAL_ICONO_SOMBRA, ALTURA_ICONO_SOMBRA),
            shadowSize: new L.Point(ANCHURA_ICONO_SOMBRA, ALTURA_ICONO_SOMBRA),
            popupAnchor: new L.Point(0, -info_elemento.altura_icono)
        });
        var latitud = info_elemento.latitud;
        var longitud = info_elemento.longitud;
        switch (tipo_mapa) {
            case TIPO_MAPA_LOCAL: {
                if (factor_reduccion_imagen_mapa_local > 1) {
                    latitud = Math.round(latitud / factor_reduccion_imagen_mapa_local);
                    longitud = Math.round(longitud / factor_reduccion_imagen_mapa_local);
                }
                break;
            }
        }
        var marker = new L.Marker(
            new L.LatLng(latitud, longitud), {
                title: info_elemento.nombre,
                icon: icono
        });
        marker.bindPopup(info_elemento.tooltip);
        cluster.addLayer(marker);
    }
    capa.addLayer(cluster);
}


function mostrar_localizador_mapa(sufijo_controles) {
    // Se recuperan los parámetros iniciales del mapa
    var latitud_mapa = $('#latitud_mapa' + sufijo_controles).val();
	var longitud_mapa = $('#longitud_mapa' + sufijo_controles).val();
	var zoom_mapa = $('#zoom_mapa' + sufijo_controles).val();

    // Se establecen la posición y zoom del mapa
    establece_posicion_zoom_mapa(
        latitud_mapa,
        longitud_mapa,
        zoom_mapa);

    // Capas del mapa
    var capas_base = dame_capas_base_mapa(mapa_global);
    var numero_capas_mapa = 0;
    for (var key in capas_base) {
        if (capas_base.hasOwnProperty(key)) {
            numero_capas_mapa++;
        }
    }
    if (numero_capas_mapa > 0) {
        L.control.layers(capas_base, [], {
            collapsed: false
        }).addTo(mapa_global);
    }

	// Se guarda y se muestra la localización inicial
	$('#latitud_mapa' + sufijo_controles).val(latitud_mapa);
	$('#longitud_mapa' + sufijo_controles).val(longitud_mapa);
    $('#zoom_mapa' + sufijo_controles).val(zoom_mapa);
    switch (tipo_mapa) {
        case TIPO_MAPA_LOCAL: {
            if (factor_reduccion_imagen_mapa_local > 1) {
                latitud_mapa = Math.round(latitud_mapa / factor_reduccion_imagen_mapa_local);
                longitud_mapa = Math.round(longitud_mapa / factor_reduccion_imagen_mapa_local);
            }
            break;
        }
    }
    var posicion_mapa = new L.LatLng(latitud_mapa, longitud_mapa);
	var marker = new L.marker(posicion_mapa, {
		title: posicion_mapa.toString()
	});
	mapa_global.addLayer(marker);

	// Evento de click para guardar la posición del mapa
	function onMapClick(e) {
		// Se calculan las coordenadas del punto con respecto al mapa
        // (http://www.javascripter.net/faq/mouseclickeventcoordinates.htm)
        var evento_original = e.originalEvent;
        var offset_mapa = $('#localizador_mapa' + sufijo_controles).offset();
        var origen_x = evento_original.pageX - offset_mapa.left;
        var origen_y = evento_original.pageY - offset_mapa.top;

        // Se calcula la posición del mapa
        var posicion_mapa = mapa_global.containerPointToLatLng([origen_x, origen_y]);

        // Se guarda la posición en los controles
        var latitud_mapa = posicion_mapa.lat;
        var longitud_mapa = posicion_mapa.lng;
        switch (tipo_mapa) {
            case TIPO_MAPA_LOCAL: {
                if (factor_reduccion_imagen_mapa_local > 1) {
                    latitud_mapa = Math.round(latitud_mapa * factor_reduccion_imagen_mapa_local);
                    longitud_mapa = Math.round(longitud_mapa * factor_reduccion_imagen_mapa_local);
                }
                break;
            }
        }
		$('#latitud_mapa' + sufijo_controles).val(latitud_mapa);
		$('#longitud_mapa' + sufijo_controles).val(longitud_mapa);

        // Se cambia la localización seleccionada en el mapa
		mapa_global.removeLayer(marker);
		marker = new L.marker(posicion_mapa, {
			title: posicion_mapa.toString()
		});
		mapa_global.addLayer(marker);
	}
    mapa_global.off('click');
	mapa_global.on('click', onMapClick);

    // http://sajjad.in/2011/12/zoom-level-based-marker-interaction-using-leaflet-js/
    function onZoomend() {
        // Se guarda el zoom
		$('#zoom_mapa' + sufijo_controles).val(mapa_global.getZoom());
    };
    mapa_global.on('zoomend', onZoomend);

    // Nota: Actualiza la posición del marcador en el mapa al cambiar manualmente la latitud y la longitud
    // - http://stackoverflow.com/questions/6153047/detect-changed-input-text-box
    function actualiza_posicion_marcador_mapa() {
        // Si la latitud o longitud son incorrectas no se hace nada
        var latitud_mapa = $('#latitud_mapa' + sufijo_controles).val();
        var longitud_mapa = $('#longitud_mapa' + sufijo_controles).val();
        if (isNaN(latitud_mapa) || isNaN(longitud_mapa)) {
            return;
        }

        // Se recupera la posición del mapa
        switch (tipo_mapa) {
            case TIPO_MAPA_LOCAL: {
                if (factor_reduccion_imagen_mapa_local > 1) {
                    latitud_mapa = Math.round(latitud_mapa / factor_reduccion_imagen_mapa_local);
                    longitud_mapa = Math.round(longitud_mapa / factor_reduccion_imagen_mapa_local);
                }
                break;
            }
        }
        var posicion_mapa = new L.LatLng(latitud_mapa, longitud_mapa);

        // Se cambia la localización seleccionada en el mapa
		mapa_global.removeLayer(marker);
		marker = new L.marker(posicion_mapa, {
			title: posicion_mapa.toString()
		});
		mapa_global.addLayer(marker);
    }
    $("#latitud_mapa").off('input');
    $("#longitud_mapa").off('input');
    $("#latitud_mapa").on('input', actualiza_posicion_marcador_mapa);
    $("#longitud_mapa").on('input', actualiza_posicion_marcador_mapa);
}


function mostrar_mapa_personalizado(parametros_mapa) {
    // Parámetros del mapa
    var actualizacion_mapa = parametros_mapa.actualizacion_mapa;
    var info_capas_elementos = parametros_mapa.info_capas_elementos;
    var info_capas_calor = parametros_mapa.info_capas_calor;
    multiplicador_distancia_cluster_mapa = parametros_mapa.multiplicador_distancia_cluster;
    if (multiplicador_distancia_cluster_mapa == null) {
        multiplicador_distancia_cluster_mapa = MULTIPLICADOR_DISTANCIA_CLUSTER_MAPA_SECCION;
    }

    // Se establecen la posición y zoom del mapa por defecto
    if (actualizacion_mapa == false) {
        establece_posicion_zoom_mapa(
            latitud_mapa_defecto,
            longitud_mapa_defecto,
            zoom_mapa_defecto);
    }
    else {
        // Se guardan los nombres de las capas activadas
        // (para añadir en la actualización sólo las capas que estaban activadas)
        var nombres_capas_activadas = [];
        for (var nombre_capa in capas_mapa_globales) {
            if (mapa_global.hasLayer(capas_mapa_globales[nombre_capa]) == true) {
                nombres_capas_activadas.push(nombre_capa);
            }
        }
    }

    // Opciones de las capas de calor
    var opciones_capas_calor = {
        radius: 30,
        maxOpacity: 0.5,
        scaleRadius: false,
        useLocalExtrema: true
    };

    // Zoom del mapa
    switch (tipo_mapa) {
        case TIPO_MAPA_LOCAL: {
            var zoom_minimo = ZOOM_MINIMO_MAPA_LOCAL;
            var zoom_maximo = dame_zoom_maximo_mapa_local();
            opciones_capas_calor["minZoom"] = zoom_minimo;
            opciones_capas_calor["maxZoom"] = zoom_maximo;
            break;
        }
    };

    // - Si se muestra el mapa por primera vez, se crea el vector de capas globales y se guarda el mapa
    // - Si es una actualización de mapa, se eliminan las capas globales y los controles de selección de capas
    if (actualizacion_mapa == true) {
        // Se eliminan las capas actuales del mapa
        for (var key in capas_mapa_globales) {
            mapa_global.removeLayer(capas_mapa_globales[key]);
        }

        // Se borran los controles de selección de capas (si no se duplican en cada actualización)
        $(".leaflet-control-layers").remove();
    }
    capas_mapa_globales = {};

    // Se crean las capas de calor
    var capas_calor = [];
    var datos_capas_calor = [];
    for (var i = 0; i < info_capas_calor.length; i++) {
        // Nota: Se utiliza una configuración para cada capa (si no sólo funciona la última capa)
        var opciones_mapa_calor_copia = JSON.parse(JSON.stringify(opciones_capas_calor));
        var capa_calor = new HeatmapOverlay(opciones_mapa_calor_copia);
        var datos_capa_calor = [];
        capas_calor.push(capa_calor);
        datos_capas_calor.push(datos_capa_calor);
    }

    // Se crean las capas de los elementos
    var clusters_capas_elementos = [];
    for (var i = 0; i < info_capas_elementos.length; i++) {
        var cluster_capa_elementos = new L.markerClusterGroup({
            spiderfyDistanceMultiplier: multiplicador_distancia_cluster_mapa,
            maxClusterRadius: MAX_RADIO_CLUSTER_MAPAS
        });
        clusters_capas_elementos.push(cluster_capa_elementos);

        // Información de la capa de elementos
        var info_capa_elementos = info_capas_elementos[i];
        var titulo_capa_elementos = info_capa_elementos["titulo"];
        var info_elementos = info_capa_elementos["info_elementos"];

        // Se añaden los elementos al mapa
        for (var j = 0; j < info_elementos.length; j++) {
            var info_elemento = info_elementos[j];
            if ((info_elemento.latitud == null) || (info_elemento.longitud == null)) {
                continue;
            }

            // Icono y marcador
            var icono = new L.icon({
                iconUrl: info_elemento.icono,
                iconSize: new L.Point(info_elemento.anchura_icono, info_elemento.altura_icono),
                iconAnchor: new L.Point(info_elemento.anchura_icono / 2, info_elemento.altura_icono),
                shadowUrl: "./rsc/lib/leaflet/dist/images/marker-shadow.png",
                shadowAnchor: new L.Point(ANCHURA_ICONO_SOMBRA / 2 + CORRECION_CENTRADO_HORIZONTAL_ICONO_SOMBRA, ALTURA_ICONO_SOMBRA),
                shadowSize: new L.Point(ANCHURA_ICONO_SOMBRA, ALTURA_ICONO_SOMBRA),
                popupAnchor: new L.Point(0, -info_elemento.altura_icono)
            });
            var latitud = info_elemento.latitud;
            var longitud = info_elemento.longitud;
            switch (tipo_mapa) {
                case TIPO_MAPA_LOCAL: {
                    if (factor_reduccion_imagen_mapa_local > 1) {
                        latitud = Math.round(latitud / factor_reduccion_imagen_mapa_local);
                        longitud = Math.round(longitud / factor_reduccion_imagen_mapa_local);
                    }
                    break;
                }
            }
            var marker = new L.Marker(
                new L.LatLng(latitud, longitud), {
                    title: info_elemento.id,
                    icon: icono
            });
            marker.bindPopup(info_elemento.tooltip);
            cluster_capa_elementos.addLayer(marker);

            // Se añade la información de calor a cada una de las capas de calor
            for (var k = 0; k < info_capas_calor.length; k++) {
                var nombre_dato_capa_calor = info_capas_calor[k]["nombre_dato"];
                var valor = info_elemento[nombre_dato_capa_calor];
                datos_capas_calor[k].push({
                    lat: latitud,
                    lng: longitud,
                    value: valor});
            }
        }
    }
    for (var i = 0; i < info_capas_calor.length; i++) {
        capas_calor[i].setData({data: datos_capas_calor[i]});
    }

    // Se añaden las capas al mapa
    var capas_base = dame_capas_base_mapa(mapa_global);
    var capas_overlay = {};
    for (var i = 0; i < info_capas_elementos.length; i++) {
        var titulo_capa_elementos = info_capas_elementos[i]["titulo"];
        capas_overlay[titulo_capa_elementos] = clusters_capas_elementos[i];
        capas_mapa_globales[titulo_capa_elementos] = clusters_capas_elementos[i];
    }
    for (var i = 0; i < info_capas_calor.length; i++) {
        var titulo_capa_calor = info_capas_calor[i]["titulo"];
        capas_overlay[titulo_capa_calor] = capas_calor[i];
        capas_mapa_globales[titulo_capa_calor] = capas_calor[i];
    }
    L.control.layers(capas_base, capas_overlay, {
        collapsed: false
    }).addTo(mapa_global);

    // Se añaden las capas al mapa para que se muestren 'activadas' por defecto
    // (https://github.com/Leaflet/Leaflet/issues/3199#event-230611315)
    for (var i = 0; i < info_capas_elementos.length; i++) {
        var titulo_capa_elementos = info_capas_elementos[i]["titulo"];
        if (actualizacion_mapa == true) {
            if (nombres_capas_activadas.indexOf(titulo_capa_elementos) == -1) {
                continue;
            }
        }
        var capa_elementos_activada = info_capas_elementos[i]["activada"];
        if ((capa_elementos_activada == true) || (actualizacion_mapa == true)) {
            mapa_global.addLayer(clusters_capas_elementos[i]);
        }
    }
    for (var i = 0; i < info_capas_calor.length; i++) {
        var titulo_capa_calor = info_capas_calor[i]["titulo"];
        if (actualizacion_mapa == true) {
            if (nombres_capas_activadas.indexOf(titulo_capa_calor) == -1) {
                continue;
            }
        }
        var capa_calor_activada = info_capas_calor[i]["activada"];
        if ((capa_calor_activada == true) || (actualizacion_mapa == true)) {
            mapa_global.addLayer(capas_calor[i]);

            // Nota: Se elimina y se vuelve a añadir porque si no se muestran las capas pero como si sólo hubiera una
            mapa_global.removeLayer(capas_calor[i]);
            mapa_global.addLayer(capas_calor[i]);
        }
    };
}


function establece_posicion_zoom_mapa(latitud, longitud, zoom) {
    switch (tipo_mapa) {
        case TIPO_MAPA_LOCAL: {
            if (factor_reduccion_imagen_mapa_local > 1) {
                latitud = Math.round(latitud / factor_reduccion_imagen_mapa_local);
                longitud = Math.round(longitud / factor_reduccion_imagen_mapa_local);
            }
            break;
        }
    }
    mapa_global.setView([latitud, longitud], zoom);
}


function actualiza_fecha_actualizacion_mapa() {
    var fecha_actual = new Date();
    var cadena_fecha_actual = convierte_fecha_a_cadena(fecha_actual, formato_fecha_local_jquery_ui);
    cadena_fecha_actual += ", " + dame_cadena_hora(fecha_actual);
    var texto_actualizado_hora_actual = TLNT.Idiomas._("hora de actualización del mapa") + ": " + cadena_fecha_actual;
    actualiza_texto_pie_pagina(texto_actualizado_hora_actual);
}

