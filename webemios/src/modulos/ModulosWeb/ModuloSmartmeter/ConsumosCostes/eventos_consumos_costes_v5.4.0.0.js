//
// Funciones de consumos y costes (de SmartMeter)
//


// Muestra información de consumos y costes generales
function boton_smartmeter_consumos_costes_generales_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_consumos_costes_generales(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var agregacion = parametros_informe["agregacion"];
    var comentarios = parametros_informe["comentarios"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/dame_consumos_costes_sensores_generales.php", {
        medicion: medicion,
        id_ratio: id_ratio,
        ids_sensores: ids_sensores,
		nombres_sensores: nombres_sensores,
		fecha_hora_inicio: fecha_hora_inicio,
		fecha_hora_fin: fecha_hora_fin,
        intervalo_valores: intervalo_valores,
        agregacion: agregacion,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        minutos_desfase_utc: minutos_desfase_utc,
        tipo_informe: TIPO_INFORME_WEB_EMIOS
	},
	function (data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Comprobación de datos disponibles
        var hay_datos = resultado.hay_datos;
        if (hay_datos == false) {
            jAlert(TLNT.Idiomas._("No hay datos disponibles"));
            return;
        }

        // Se muestra el informe
        $("#informe-sin-datos-smartmeter-consumos-costes-generales").hide();
        $("#informe-smartmeter-consumos-costes-generales").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-consumos-consumos-costes-generales",
            "grafica-consumos-acumulados-consumos-costes-generales",
            "descripciones-sensores-consumos-costes-generales",
            "contenedor-tabla-consumos-maximos-minimos-consumos-costes-generales",
            "grafica-costes-consumos-costes-generales",
            "grafica-costes-acumulados-consumos-costes-generales",
            "contenedor-tabla-costes-maximos-minimos-consumos-costes-generales",
            "grafica-precios-consumos-costes-generales",
            "contenedor-tabla-precios-maximos-minimos-consumos-costes-generales",
            "contenedor-tabla-comentarios-consumos-costes-generales"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            ids_sensores: ids_sensores,
            intervalo_valores: intervalo_valores,
            agregacion: agregacion,
            id_parametros_resultado_informe: "parametros-resultado-informe-consumos-costes-generales",
            id_grafica_consumos: "grafica-consumos-consumos-costes-generales",
            id_grafica_consumos_acumulados: "grafica-consumos-acumulados-consumos-costes-generales",
            id_descripciones_sensores: "descripciones-sensores-consumos-costes-generales",
            id_contenedor_tabla_consumos_maximos_minimos: "contenedor-tabla-consumos-maximos-minimos-consumos-costes-generales",
            id_grafica_costes: "grafica-costes-consumos-costes-generales",
            id_grafica_costes_acumulados: "grafica-costes-acumulados-consumos-costes-generales",
            id_contenedor_tabla_costes_maximos_minimos: "contenedor-tabla-costes-maximos-minimos-consumos-costes-generales",
            id_grafica_precios: "grafica-precios-consumos-costes-generales",
            id_contenedor_tabla_precios_maximos_minimos: "contenedor-tabla-precios-maximos-minimos-consumos-costes-generales",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-consumos-costes-generales"};
        dibuja_informe_smartmeter_consumos_costes_generales(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
	});
}


// Muestra información de consumos y costes totales
function boton_smartmeter_consumos_costes_totales_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_consumos_costes_totales(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Se recuperan los datos para el informe
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/dame_consumos_costes_sensores_totales.php", {
        medicion: medicion,
        id_ratio: id_ratio,
        ids_sensores: ids_sensores,
		nombres_sensores: nombres_sensores,
		fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        intervalo_valores: intervalo_valores,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
	},
	function (data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Comprobación de datos disponibles
        var hay_datos = resultado.hay_datos;
        if (hay_datos == false) {
            jAlert(TLNT.Idiomas._("No hay datos disponibles"));
            return;
        }

        // Se muestra el informe
        $("#informe-sin-datos-smartmeter-consumos-costes-totales").hide();
        $("#informe-smartmeter-consumos-costes-totales").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-consumos-totales-consumos-costes-totales",
            "grafica-porcentajes-consumos-consumos-costes-totales",
            "contenedor-tabla-consumos-consumos-costes-totales",
            "grafica-costes-totales-consumos-costes-totales",
            "grafica-porcentajes-costes-consumos-costes-totales",
            "grafica-precios-medios-consumos-costes-totales",
            "contenedor-tabla-costes-consumos-costes-totales"]);

        // Se dibuja el informe
        var parametros = {
            id_grafica_consumos_totales: "grafica-consumos-totales-consumos-costes-totales",
            id_grafica_porcentajes_consumos: "grafica-porcentajes-consumos-consumos-costes-totales",
            id_contenedor_tabla_consumos: "contenedor-tabla-consumos-consumos-costes-totales",
            id_grafica_costes_totales: "grafica-costes-totales-consumos-costes-totales",
            id_grafica_porcentajes_costes: "grafica-porcentajes-costes-consumos-costes-totales",
            id_grafica_precios_medios: "grafica-precios-medios-consumos-costes-totales",
            id_contenedor_tabla_costes: "contenedor-tabla-costes-consumos-costes-totales"};
        dibuja_informe_smartmeter_consumos_costes_totales(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra información de consumos y costes por tramo
function boton_smartmeter_consumos_costes_tramos_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_consumos_costes_tramos(false);
    if (parametros_informe == null) {
        return;
    }

    // Se visualiza el informe
    boton_smartmeter_consumos_costes_tramos_ver_informe_electricidad(parametros_informe);
}


// Muestra información sobre los excesos de potencia de un sensor
function boton_smartmeter_excesos_potencia_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_excesos_potencia(false);
    if (parametros_informe == null) {
        return;
    }

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_excesos_potencia_ver_informe_electricidad_Espanya(parametros_informe);
            break;
        }
    }
}


// Muestra información de los excesos de energía reactiva de un sensor
function boton_smartmeter_excesos_energia_reactiva_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_excesos_energia_reactiva(false);
    if (parametros_informe == null) {
        return;
    }

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_excesos_energia_reactiva_ver_informe_electricidad_Espanya(parametros_informe);
            break;
        }
    }
}


// Muestra información de los cortes de tensión de un sensor
function boton_smartmeter_cortes_tension_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_cortes_tension(false);
    if (parametros_informe == null) {
        return;
    }

    // Se visualiza el informe
    boton_smartmeter_cortes_tension_ver_informe_electricidad(parametros_informe);
}


// Muestra información sobre los excesos de caudal de un sensor
function boton_smartmeter_excesos_caudal_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_excesos_caudal(false);
    if (parametros_informe == null) {
        return;
    }

    // Selección de país
    switch (pais_tarifas_gas) {
        case PAIS_ESPANYA: {
            boton_smartmeter_excesos_caudal_ver_informe_gas_Espanya(parametros_informe);
            break;
        }
    }
}


// Muestra una comparación de consumos y costes de un sensor en diferentes periodos
function boton_smartmeter_comparacion_periodos_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_comparacion_periodos(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var fecha_inicio_anterior = parametros_informe["fecha_inicio_anterior"];
    var fecha_inicio_posterior = parametros_informe["fecha_inicio_posterior"];
    var numero_dias_periodo = parametros_informe["numero_dias_periodo"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos del informe
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/dame_consumos_costes_sensor_periodos.php", {
        medicion: medicion,
        id_ratio: id_ratio,
        id_sensor: id_sensor,
		nombre_sensor: nombre_sensor,
		fecha_inicio_anterior: fecha_inicio_anterior,
		fecha_inicio_posterior: fecha_inicio_posterior,
		numero_dias_periodo: numero_dias_periodo,
        minutos_desfase_utc: minutos_desfase_utc,
		intervalo_valores: intervalo_valores,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas)
	},
	function (data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Comprobación de datos disponibles
        var hay_datos = resultado.hay_datos;
        if (hay_datos == false) {
            jAlert(TLNT.Idiomas._("No hay datos disponibles"));
            return;
        }

        // Se muestra el informe
        $("#informe-sin-datos-smartmeter-comparacion-periodos").hide();
        $("#informe-smartmeter-comparacion-periodos").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-consumos-comparacion-periodos",
            "grafica-costes-comparacion-periodos",
            "contenedor-tabla-evolucion-consumos-costes-comparacion-periodos",
            "contenedor-tabla-evolucion-consumos-tramos-comparacion-periodos",
            "grafica-consumos-totales-comparacion-periodos",
            "grafica-costes-totales-comparacion-periodos",
            "grafica-precios-medios-comparacion-periodos",
            "contenedor-tabla-evolucion-precios-medios-comparacion-periodos"]);

        // Se dibuja el informe
        var parametros = {
            intervalo_valores: intervalo_valores,
            id_grafica_consumos: "grafica-consumos-comparacion-periodos",
            id_grafica_costes: "grafica-costes-comparacion-periodos",
            id_contenedor_tabla_evolucion_consumos_costes: "contenedor-tabla-evolucion-consumos-costes-comparacion-periodos",
            id_contenedor_tabla_evolucion_consumos_tramos: "contenedor-tabla-evolucion-consumos-tramos-comparacion-periodos",
            id_grafica_consumos_totales: "grafica-consumos-totales-comparacion-periodos",
            id_grafica_costes_totales: "grafica-costes-totales-comparacion-periodos",
            id_grafica_precios_medios: "grafica-precios-medios-comparacion-periodos",
            id_contenedor_tabla_evolucion_precios_medios: "contenedor-tabla-evolucion-precios-medios-comparacion-periodos"};
        dibuja_informe_smartmeter_comparacion_periodos(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
	});
}


// Realiza la comparación de costes de un sensor con las tarifas seleccionadas
function boton_smartmeter_simulador_tarifas_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_tarifas(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var ids_tarifas = parametros_informe["ids_tarifas"];
    var nombres_tarifas = parametros_informe["nombres_tarifas"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos del informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/dame_costes_consumo_sensor_tarifas.php", {
        medicion: medicion,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        ids_tarifas: ids_tarifas,
        nombres_tarifas: nombres_tarifas,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Comprobación de datos disponibles
        var hay_datos = resultado.hay_datos;
        if (hay_datos == false) {
            jAlert(TLNT.Idiomas._("No hay datos disponibles"));
            return;
        }

        // Se muestra el informe
        $("#informe-sin-datos-smartmeter-simulador-tarifas").hide();
        $("#informe-smartmeter-simulador-tarifas").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-costes-simulador-tarifas",
            "grafica-costes-totales-simulador-tarifas",
            "contenedor-tabla-comparacion-coste-actual-simulador-tarifas",
            "contenedor-tabla-comparacion-mejor-opcion-simulador-tarifas"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_grafica_costes: "grafica-costes-simulador-tarifas",
            id_grafica_costes_totales: "grafica-costes-totales-simulador-tarifas",
            id_contenedor_tabla_comparacion_coste_actual: "contenedor-tabla-comparacion-coste-actual-simulador-tarifas",
            id_contenedor_tabla_comparacion_mejor_opcion: "contenedor-tabla-comparacion-mejor-opcion-simulador-tarifas"};
        dibuja_informe_smartmeter_simulador_tarifas(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra el mapa de consumos y costes de los sensores
function boton_smartmeter_mapa_consumos_costes_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_mapa_consumos_costes(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Se recuperan los datos del informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/dame_info_mapa_consumos_costes.php", {
        medicion: medicion,
        id_ratio: id_ratio,
        ids_sensores: ids_sensores,
        nombres_sensores: nombres_sensores,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas)
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Comprobación de datos disponibles
        var hay_datos = resultado.hay_datos;
        if (hay_datos == false) {
            jAlert(TLNT.Idiomas._("No hay datos disponibles"));
            return;
        }

        // Actualización de mapa
        var actualizacion_mapa = ($('#mapa-consumos-costes').length > 0);

        // Si el mapa no está creado
        if (actualizacion_mapa == false) {
            // Se inicializa el contenedor del mapa
            $("#contenedor-mapa-consumos-costes").html("<div id='mapa-consumos-costes' class='mapa'></div>");

            // Se establece la altura del mapa de consumos y costes
            var altura_mapa = ($(window).height() - MARGEN_ALTURA_MAPA_CONSUMOS_COSTES);
            if (altura_mapa < MIN_ALTURA_MAPA_CONSUMOS_COSTES) {
                altura_mapa = MIN_ALTURA_MAPA_CONSUMOS_COSTES;
            }
            $('#mapa-consumos-costes').height(altura_mapa + "px");
        }

        // Información de las capas de elementos
        var info_capas_elementos = [];
        info_capas_elementos.push({
            titulo: TLNT.Idiomas._("Sensores"),
            info_elementos: resultado.info_mapa_sensores,
            activada: true});

        // Información de las capas de calor (para el mapa de consumos y costes)
        var info_capas_calor = [];
        info_capas_calor.push({
            titulo: TLNT.Idiomas._("Consumo") + " (" + resultado.unidad_medida_consumo + ")",
            nombre_dato: "consumo",
            activada: true});
        if (resultado.curva_coste == true) {
            info_capas_calor.push({
                titulo: TLNT.Idiomas._("Coste") + " (" + resultado.unidad_medida_coste + ")",
                nombre_dato: "coste",
                activada: true});
        }

        // Parámetros del mapa
        var parametros_mapa = {};
        parametros_mapa["actualizacion_mapa"] = actualizacion_mapa;
        parametros_mapa["info_capas_elementos"] = info_capas_elementos;
        parametros_mapa["info_capas_calor"] = info_capas_calor;
        parametros_mapa["multiplicador_distancia_cluster"] = null;

        // Origen del mapa
        var origen_mapa = ORIGEN_MAPA_SECCION;
        var parametros_origen_mapa = {
            "modulo": $('#modulo').attr('name')
        };

        // Se recuperan la posición y zoom del mapa
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
            }

            // Se guardan la posición y el zoom del mapa por defecto en variables globales
            latitud_mapa_defecto = resultado.latitud_mapa_defecto;
            longitud_mapa_defecto = resultado.longitud_mapa_defecto;
            zoom_mapa_defecto = resultado.zoom_mapa_defecto;

            // Se crea el mapa
            if (actualizacion_mapa == false) {
                crear_mapa("mapa-consumos-costes", opciones_mapa, mostrar_mapa_personalizado, parametros_mapa);
            }
            else {
                mostrar_mapa_personalizado(parametros_mapa);
            }
        });
    });
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de información de consumos y costes generales
function dame_parametros_informe_smartmeter_consumos_costes_generales(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Se recupera el ratio seleccionado (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Se recuperan los identificadores y nombres de los sensores seleccionados
    var ids_sensores = [];
    var nombres_sensores = [];
    $("#ids_sensores_smartmeter_consumos_costes_generales option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores.push($(this).val());
            nombres_sensores.push($(this).text());
        }
    });
    if (ids_sensores.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
		return (null);
	}

    // Intervalo de valores, gregación y comentarios
    var intervalo_valores = $('#intervalo_valores_smartmeter_consumos_costes_generales').val();
    var agregacion = $('#agregacion_smartmeter_consumos_costes_generales').val();
    var comentarios = $('#comentarios_smartmeter_consumos_costes_generales').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_consumos_costes_generales", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_consumos_costes_generales");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_consumos_costes_generales");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["agregacion"] = agregacion;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_consumos_costes_generales').val();
        var hora_inicio = $('#hora_inicio_smartmeter_consumos_costes_generales').val();
        var fecha_fin = $('#fecha_fin_smartmeter_consumos_costes_generales').val();
        var hora_fin = $('#hora_fin_smartmeter_consumos_costes_generales').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de información de consumos y costes totales
function dame_parametros_informe_smartmeter_consumos_costes_totales(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Se recupera el ratio seleccionado (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Se recuperan los identificadores y nombres de los sensores seleccionados
    var ids_sensores = [];
    var nombres_sensores = [];
    $("#ids_sensores_smartmeter_consumos_costes_totales option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores.push($(this).val());
            nombres_sensores.push($(this).text());
        }
    });
    if (ids_sensores.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
		return (null);
	}

    // Intervalo de valores
    var intervalo_valores = $('#intervalo_valores_smartmeter_consumos_costes_totales').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_consumos_costes_totales", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_consumos_costes_totales");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_consumos_costes_totales");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_consumos_costes_totales').val();
        var hora_inicio = $('#hora_inicio_smartmeter_consumos_costes_totales').val();
        var fecha_fin = $('#fecha_fin_smartmeter_consumos_costes_totales').val();
        var hora_fin = $('#hora_fin_smartmeter_consumos_costes_totales').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de información de consumos y costes por tramo
function dame_parametros_informe_smartmeter_consumos_costes_tramos(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_consumos_costes_tramos').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_smartmeter_consumos_costes_tramos :selected').text();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_consumos_costes_tramos", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_consumos_costes_tramos");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_consumos_costes_tramos");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_consumos_costes_tramos').val();
        var fecha_fin = $('#fecha_fin_smartmeter_consumos_costes_tramos').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
        if (fechas_correctas == false) {
            return (null);
        }
        var fecha_hora_inicio = fecha_inicio + ", " + "00:00:00";
        var fecha_hora_fin = fecha_fin + ", " + "23:59:59";

        parametros_informe["fecha_inicio"] = fecha_inicio;
        parametros_informe["fecha_fin"] = fecha_fin;
        parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
        parametros_informe["fecha_hora_fin"] = fecha_hora_fin;
    }

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de información de los excesos de potencia de un sensor
function dame_parametros_informe_smartmeter_excesos_potencia(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_excesos_potencia').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_smartmeter_excesos_potencia :selected').text();

    // Granularidad
    var granularidad = $('#granularidad_smartmeter_excesos_potencia').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_excesos_potencia", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_excesos_potencia");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_excesos_potencia");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["granularidad"] = granularidad;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_excesos_potencia').val();
        var hora_inicio = $('#hora_inicio_smartmeter_excesos_potencia').val();
        var fecha_fin = $('#fecha_fin_smartmeter_excesos_potencia').val();
        var hora_fin = $('#hora_fin_smartmeter_excesos_potencia').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return;
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de información de los excesos de energía reactiva de un sensor
function dame_parametros_informe_smartmeter_excesos_energia_reactiva(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_excesos_energia_reactiva').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_smartmeter_excesos_energia_reactiva :selected').text();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_excesos_energia_reactiva", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_excesos_energia_reactiva");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_excesos_energia_reactiva");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_excesos_energia_reactiva').val();
        var hora_inicio = $('#hora_inicio_smartmeter_excesos_energia_reactiva').val();
        var fecha_fin = $('#fecha_fin_smartmeter_excesos_energia_reactiva').val();
        var hora_fin = $('#hora_fin_smartmeter_excesos_energia_reactiva').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de información de los cortes de tensión de un sensor
function dame_parametros_informe_smartmeter_cortes_tension(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_cortes_tension').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_smartmeter_cortes_tension :selected').text();

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_cortes_tension').val();
        var hora_inicio = $('#hora_inicio_smartmeter_cortes_tension').val();
        var fecha_fin = $('#fecha_fin_smartmeter_cortes_tension').val();
        var hora_fin = $('#hora_fin_smartmeter_cortes_tension').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de información de los excesos de caudal de un sensor
function dame_parametros_informe_smartmeter_excesos_caudal(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_excesos_caudal').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_smartmeter_excesos_caudal :selected').text();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_excesos_caudal", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_excesos_caudal");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_excesos_caudal");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_excesos_caudal').val();
        var hora_inicio = $('#hora_inicio_smartmeter_excesos_caudal').val();
        var fecha_fin = $('#fecha_fin_smartmeter_excesos_caudal').val();
        var hora_fin = $('#hora_fin_smartmeter_excesos_caudal').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return;
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de información de comparación de consumos y costes de un sensor en diferentes periodos
function dame_parametros_informe_smartmeter_comparacion_periodos(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio
    var id_ratio = dame_id_ratio_seleccionado();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_comparacion_periodos').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
	var nombre_sensor = $('#id_sensor_smartmeter_comparacion_periodos :selected').text();

    // Intervalo de valores
    var intervalo_valores = $('#intervalo_valores_smartmeter_comparacion_periodos :selected').val();

    // Horario semanal y exclusión de fechas
    var horario_semanal = dame_horario_semanal_controles("smartmeter_comparacion_periodos", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_comparacion_periodos");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        // Comprobación de fechas y número de días de los periodos
        var fecha_inicio_anterior = $('#fecha_inicio_anterior_smartmeter_comparacion_periodos').val();
        var fecha_inicio_posterior = $('#fecha_inicio_posterior_smartmeter_comparacion_periodos').val();
        var numero_dias_periodo = $('#numero_dias_smartmeter_comparacion_periodos').val();
        var periodos_correctos = comprueba_fechas_numero_dias_periodos_correctos(fecha_inicio_anterior, fecha_inicio_posterior, numero_dias_periodo);
        if (periodos_correctos == false) {
            return (null);
        }

        parametros_informe["fecha_inicio_anterior"] = fecha_inicio_anterior;
        parametros_informe["fecha_inicio_posterior"] = fecha_inicio_posterior;
        parametros_informe["numero_dias_periodo"] = numero_dias_periodo;
    }

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de simulación de tarifas
function dame_parametros_informe_smartmeter_simulador_tarifas(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_simulador_tarifas').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_smartmeter_simulador_tarifas :selected').text();

    // Si no hay tarifas eléctricas en la lista, es que el sensor no tiene tarifa eléctrica asignada
    if ($('#ids_tarifas_smartmeter_simulador_tarifas option').length == 0) {
        jAlert(TLNT.Idiomas._("El sensor no tiene tarifa asignada"));
        return (null);
    }

    // Identificadores y los nombres de tarifas
    var ids_tarifas = [];
    var nombres_tarifas = [];
    $("#ids_tarifas_smartmeter_simulador_tarifas option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_tarifas.push($(this).val());
            nombres_tarifas.push($(this).text());
        }
    });
    if (ids_tarifas.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos una tarifa"));
        return (null);
	}

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["ids_tarifas"] = ids_tarifas;
    parametros_informe["nombres_tarifas"] = nombres_tarifas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_simulador_tarifas').val();
        var hora_inicio = $('#hora_inicio_smartmeter_simulador_tarifas').val();
        var fecha_fin = $('#fecha_fin_smartmeter_simulador_tarifas').val();
        var hora_fin = $('#hora_fin_smartmeter_simulador_tarifas').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de simulación de tarifas
function dame_parametros_informe_smartmeter_mapa_consumos_costes(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Identificadores y nombres de sensores
    var ids_sensores = [];
    var nombres_sensores = [];
    $("#ids_sensores_smartmeter_mapa_consumos_costes option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores.push($(this).val());
            nombres_sensores.push($(this).text());
        }
    });
    if (ids_sensores.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
        return (null);
	}

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_mapa_consumos_costes", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_mapa_consumos_costes");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_mapa_consumos_costes");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_mapa_consumos_costes').val();
        var hora_inicio = $('#hora_inicio_smartmeter_mapa_consumos_costes').val();
        var fecha_fin = $('#fecha_fin_smartmeter_mapa_consumos_costes').val();
        var hora_fin = $('#hora_fin_smartmeter_mapa_consumos_costes').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}
