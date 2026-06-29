//
// Funciones de análisis (de Sensores)
//


// Muestra el informe de análisis horario
function boton_sensores_analisis_horario_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_horario(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
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

    // Se recuperan los datos de análisis horario de valores
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Analisis/dame_analisis_horario_valores_sensor.php", {
        id_ratio: id_ratio,
        clase_sensor: clase_sensor,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        parametros_extra_campo: parametros_extra_campo,
        tipo_mapa_calor: tipo_mapa_calor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
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

        // Se muestra el informe
        $("#informe-sin-datos-sensores-analisis-horario").hide();
        $("#informe-sensores-analisis-horario").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-valores-analisis-horario",
            "mapa-calor-valores-analisis-horario",
            "grafica-medias-valores-analisis-horario",
            "grafica-coeficientes-variacion-valores-analisis-horario",
            "grafica-lorenz-valores-analisis-horario",
            "contenedor-tabla-percentiles-valores-analisis-horario",
            "grafica-porcentajes-valores-analisis-horario"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            clase_sensor: clase_sensor,
            campo: campo,
            id_grafica_valores: "grafica-valores-analisis-horario",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: "mapa-calor-valores-analisis-horario",
            id_grafica_medias_valores: "grafica-medias-valores-analisis-horario",
            id_grafica_coeficientes_variacion_valores: "grafica-coeficientes-variacion-valores-analisis-horario",
            id_grafica_lorenz_valores: "grafica-lorenz-valores-analisis-horario",
            id_contenedor_tabla_percentiles_valores: "contenedor-tabla-percentiles-valores-analisis-horario",
            id_grafica_porcentajes_valores: "grafica-porcentajes-valores-analisis-horario"};
        dibuja_informe_sensores_analisis_horario(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra el informe de análisis diario
function boton_sensores_analisis_diario_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_diario(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Analisis/dame_analisis_diario_valores_sensor.php", {
        id_ratio: id_ratio,
        clase_sensor: clase_sensor,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        parametros_extra_campo: parametros_extra_campo,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        tipo_mapa_calor: tipo_mapa_calor,
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

        // Se muestra el informe
        $("#informe-sin-datos-sensores-analisis-diario").hide();
        $("#informe-sensores-analisis-diario").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-valores-analisis-diario",
            "mapa-calor-valores-analisis-diario",
            "grafica-medias-valores-analisis-diario",
            "grafica-coeficientes-variacion-valores-analisis-diario",
            "grafica-sumas-valores-analisis-diario",
            "grafica-valores-medias-maximos-minimos-analisis-diario",
            "contenedor-tabla-maximos-minimos-medias-medidas-analisis-diario",
            "contenedor-tabla-valores-dia-analisis-diario"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            clase_sensor: clase_sensor,
            campo: campo,
            id_grafica_valores: "grafica-valores-analisis-diario",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: "mapa-calor-valores-analisis-diario",
            id_grafica_medias_valores: "grafica-medias-valores-analisis-diario",
            id_grafica_coeficientes_variacion_valores: "grafica-coeficientes-variacion-valores-analisis-diario",
            id_grafica_sumas_valores: "grafica-sumas-valores-analisis-diario",
            id_grafica_valores_medias_maximos_minimos: "grafica-valores-medias-maximos-minimos-analisis-diario",
            id_contenedor_tabla_maximos_minimos_medias_medidas: "contenedor-tabla-maximos-minimos-medias-medidas-analisis-diario",
            id_contenedor_tabla_valores_dia: "contenedor-tabla-valores-dia-analisis-diario"};
        dibuja_informe_sensores_analisis_diario(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra el informe de análisis de comportamiento
function boton_sensores_analisis_comportamiento_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_comportamiento(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Se recuperan los datos del análisis de comportamiento
	$.post("./src/modulos/ModulosWeb/ModuloSensores/Analisis/dame_analisis_comportamiento_valores_sensores.php", {
        id_ratio: id_ratio,
        clase_sensor: clase_sensor,
        campo: campo,
        parametros_extra_campo: parametros_extra_campo,
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

        // Se muestra el informe
        $("#informe-sin-datos-sensores-analisis-comportamiento").hide();
        $("#informe-sensores-analisis-comportamiento").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-coeficientes-estabilidad-valores-analisis-comportamiento",
            "texto-explicacion-coeficiente-estabilidad-analisis-comportamiento",
            "grafica-amplitudes-valores-analisis-comportamiento",
            "texto-explicacion-amplitud-analisis-comportamiento",
            "grafica-alturas-relativas-valores-maximos-analisis-comportamiento",
            "texto-explicacion-altura-relativa-maxima-analisis-comportamiento"]);

        // Se dibuja el informe
        var parametros = {
            id_grafica_coeficientes_estabilidad_valores: "grafica-coeficientes-estabilidad-valores-analisis-comportamiento",
            id_texto_explicacion_coeficiente_estabilidad: "texto-explicacion-coeficiente-estabilidad-analisis-comportamiento",
            id_grafica_amplitudes_valores: "grafica-amplitudes-valores-analisis-comportamiento",
            id_texto_explicacion_amplitud: "texto-explicacion-amplitud-analisis-comportamiento",
            id_grafica_alturas_relativas_valores_maximos: "grafica-alturas-relativas-valores-maximos-analisis-comportamiento",
            id_texto_explicacion_altura_relativa_maxima: "texto-explicacion-altura-relativa-maxima-analisis-comportamiento"};
        dibuja_informe_sensores_analisis_comportamiento(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
	});
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de análisis horario
function dame_parametros_informe_sensores_analisis_horario(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Clase de sensor
    var clase_sensor = $('#clase_sensor_sensores_analisis_horario').val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return (null);
    }

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_analisis_horario').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_analisis_horario :selected').text();

    // Campo y parámetros extra
    var campo = $('#campo_sensores_analisis_horario').val();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_analisis_horario').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return (null);
    }

    // Tipo de mapa de calor
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_analisis_horario').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_analisis_horario", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_analisis_horario");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_analisis_horario");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_analisis_horario').val();
        var fecha_fin = $('#fecha_fin_sensores_analisis_horario').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
        if (fechas_correctas == false) {
            return (null);
        }
        var hora_inicio = "00:00:00";
        var hora_fin = "23:59:59";
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


// Devuelve los parámetros del informe de análisis diario
function dame_parametros_informe_sensores_analisis_diario(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Clase de sensor
    var clase_sensor = $('#clase_sensor_sensores_analisis_diario').val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return (null);
    }

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_analisis_diario').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_analisis_diario :selected').text();

    // Campo y parámetros extra
    var campo = $('#campo_sensores_analisis_diario').val();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_analisis_diario').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return (null);
    }

    // Tipo de mapa de calor
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_analisis_diario').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_analisis_diario", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_analisis_diario");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_analisis_diario");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_analisis_diario').val();
        var fecha_fin = $('#fecha_fin_sensores_analisis_diario').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
        if (fechas_correctas == false) {
            return (null);
        }
        var hora_inicio = "00:00:00";
        var hora_fin = "23:59:59";
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


// Devuelve los parámetros del informe de análisis de comportamiento
function dame_parametros_informe_sensores_analisis_comportamiento(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Se recupera el ratio seleccionado (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Se comprueba si hay clase seleccionada
    var clase_sensor = $('#clase_sensor_sensores_analisis_comportamiento').val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return (null);
    }

    // Se recuperan los identificadores y los nombres de los sensores seleccionados
    var ids_sensores = [];
    var nombres_sensores = [];
    $("#ids_sensores_sensores_analisis_comportamiento option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores.push($(this).val());
            nombres_sensores.push($(this).text());
        }
    });
    if (ids_sensores.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
		return (null);
	}

    // Campo y parámetros extra
    var campo = $('#campo_sensores_analisis_comportamiento').val();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_analisis_comportamiento').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return (null);
    }

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_analisis_comportamiento", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_analisis_comportamiento");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_analisis_comportamiento");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_analisis_comportamiento').val();
        var fecha_fin = $('#fecha_fin_sensores_analisis_comportamiento').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
        if (fechas_correctas == false) {
            return (null);
        }
        var hora_inicio = "00:00:00";
        var hora_fin = "23:59:59";
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
