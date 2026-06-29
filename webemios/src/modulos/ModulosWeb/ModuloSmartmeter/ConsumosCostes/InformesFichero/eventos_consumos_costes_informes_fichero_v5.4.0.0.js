//
// Funciones de informes fichero de consumos y costes (de SmartMeter)
//


// Genera el informe fichero de consumos y costes generales
function smartmeter_consumos_costes_generales_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_consumos_costes_generales();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-consumos-costes-generales').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-consumos-costes-generales-consumos",
            "pagina-informe-fichero-consumos-costes-generales-costes",
            "pagina-informe-fichero-consumos-costes-generales-precios"]);
        return;
    }

    // Parámetros del informe
    var medicion = parametros_informe["medicion"];
    var id_ratio = parametros_informe["id_ratio"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var agregacion = parametros_informe["agregacion"];
    var comentarios = parametros_informe["comentarios"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

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
        tipo_informe: TIPO_INFORME_FICHERO
    },
    function (data, status) {
        // Se comprueba si hay error en el resultado del informe
        var error_informe = false;
        var descripcion_error_informe = "";
        var resultado = dame_resultado_ejecucion_script_php_json_usuario_interno(data);
        if (resultado.res == "ERROR") {
            error_informe = true;
            descripcion_error_informe = resultado.msg;
        }

        // Comprobación de datos disponibles
        if (error_informe == false) {
            var hay_datos = resultado.hay_datos;
            if (hay_datos == false) {
                error_informe = true;
                descripcion_error_informe = TLNT.Idiomas._("No hay datos disponibles");
            }
        }

        // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
        if (error_informe == true) {
            $('#mensaje-aviso-informe-fichero-consumos-costes-generales').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-consumos-costes-generales-consumos",
                "pagina-informe-fichero-consumos-costes-generales-costes",
                "pagina-informe-fichero-consumos-costes-generales-precios",
                "pagina-informe-fichero-consumos-costes-generales-comentarios"]);
            return;
        }

        // Se ocultan las páginas de costes y precios si no hay datos de coste
        var hay_datos_costes = resultado.hay_datos_costes;
        if (hay_datos_costes == false) {
            elimina_elementos([
                "pagina-informe-fichero-consumos-costes-generales-costes",
                "pagina-informe-fichero-consumos-costes-generales-precios"]);
        }

        // Se oculta la página de comentarios si no hay comentarios
        var tabla_comentarios = resultado.tabla_comentarios;
        if (tabla_comentarios == null) {
            elimina_elementos([
                "pagina-informe-fichero-consumos-costes-generales-comentarios"]);
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-consumos-costes-generales-consumos').html(TLNT.Idiomas._("Consumos"));
        $('#titulo-informe-fichero-consumos-costes-generales-costes').html(TLNT.Idiomas._("Costes"));
        $('#titulo-informe-fichero-consumos-costes-generales-precios').html(TLNT.Idiomas._("Precios"));
        $('#titulo-informe-fichero-consumos-costes-generales-comentarios').html(TLNT.Idiomas._("Comentarios"));

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
            id_grafica_consumos: "grafica-consumos-consumos-costes-generales",
            id_grafica_consumos_acumulados: "grafica-consumos-acumulados-consumos-costes-generales",
            id_descripciones_sensores: "descripciones-sensores-consumos-costes-generales",
            id_contenedor_tabla_consumos_maximos_minimos: "contenedor-tabla-consumos-maximos-minimos-consumos-costes-generales",
            id_grafica_costes: "grafica-costes-consumos-costes-generales",
            id_grafica_costes_acumulados: "grafica-costes-acumulados-consumos-costes-generales",
            id_contenedor_tabla_costes_maximos_minimos: "contenedor-tabla-costes-maximos-minimos-consumos-costes-generales",
            id_grafica_precios: "grafica-precios-consumos-costes-generales",
            id_contenedor_tabla_precios_maximos_minimos: "contenedor-tabla-precios-maximos-minimos-consumos-costes-generales",
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-consumos-costes-generales"};
        dibuja_informe_smartmeter_consumos_costes_generales(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de consumos y costes totales
function smartmeter_consumos_costes_totales_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_consumos_costes_totales();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-consumos-costes-totales').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-consumos-costes-totales-consumos",
            "pagina-informe-fichero-consumos-costes-totales-costes"]);
        return;
    }

    // Parámetros del informe
    var medicion = parametros_informe["medicion"];
    var id_ratio = parametros_informe["id_ratio"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

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
        tipo_informe: TIPO_INFORME_FICHERO
    },
    function (data, status) {
        // Se comprueba si hay error en el resultado del informe
        var error_informe = false;
        var descripcion_error_informe = "";
        var resultado = dame_resultado_ejecucion_script_php_json_usuario_interno(data);
        if (resultado.res == "ERROR") {
            error_informe = true;
            descripcion_error_informe = resultado.msg;
        }

        // Comprobación de datos disponibles
        if (error_informe == false) {
            var hay_datos = resultado.hay_datos;
            if (hay_datos == false) {
                error_informe = true;
                descripcion_error_informe = TLNT.Idiomas._("No hay datos disponibles");
            }
        }

        // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
        if (error_informe == true) {
            $('#mensaje-aviso-informe-fichero-consumos-costes-totales').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-consumos-costes-totales-consumos",
                "pagina-informe-fichero-consumos-costes-totales-costes"]);
            return;
        }

        // Se oculta la página de costes si no hay datos de coste
        var hay_datos_costes = resultado.hay_datos_costes;
        if (hay_datos_costes == false) {
            elimina_elemento("pagina-informe-fichero-consumos-costes-totales-costes");
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-consumos-costes-totales-consumos').html(TLNT.Idiomas._("Consumos"));
        $('#titulo-informe-fichero-consumos-costes-totales-costes').html(TLNT.Idiomas._("Costes"));

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
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de consumos y costes por tramo
function smartmeter_consumos_costes_tramos_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_consumos_costes_tramos();

    // Se visualiza el informe
    smartmeter_consumos_costes_tramos_ver_informe_fichero_electricidad(parametros_informe);
}


// Genera el informe fichero de excesos de potencia
function smartmeter_excesos_potencia_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_excesos_potencia();

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            smartmeter_excesos_potencia_ver_informe_fichero_electricidad_Espanya(parametros_informe);
            break;
        }
    }
}


// Genera el informe fichero de excesos de energía reactiva
function smartmeter_excesos_energia_reactiva_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_excesos_energia_reactiva();

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            smartmeter_excesos_energia_reactiva_ver_informe_fichero_electricidad_Espanya(parametros_informe);
            break;
        }
    }
}


// Genera el informe fichero de cortes de tensión
function smartmeter_cortes_tension_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_cortes_tension();

    // Se visualiza el informe
    smartmeter_cortes_tension_ver_informe_fichero_electricidad(parametros_informe);
}


// Genera el informe fichero de excesos de caudal
function smartmeter_excesos_caudal_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_excesos_caudal();

    // Selección de país
    switch (pais_tarifas_gas) {
        case PAIS_ESPANYA: {
            smartmeter_excesos_caudal_ver_informe_fichero_gas_Espanya(parametros_informe);
            break;
        }
    }
}


// Genera el informe fichero de comparación de periodos
function smartmeter_comparacion_periodos_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_comparacion_periodos();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-comparacion-periodos').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-comparacion-periodos-consumos-costes-generales",
            "pagina-informe-fichero-comparacion-periodos-consumos-costes-totales"]);
        return;
    }

    // Parámetros del informe
    var medicion = parametros_informe["medicion"];
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
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        tipo_informe: TIPO_INFORME_FICHERO
    },
    function (data, status) {
        // Se comprueba si hay error en el resultado del informe
        var error_informe = false;
        var descripcion_error_informe = "";
        var resultado = dame_resultado_ejecucion_script_php_json_usuario_interno(data);
        if (resultado.res == "ERROR") {
            error_informe = true;
            descripcion_error_informe = resultado.msg;
        }

        // Comprobación de datos disponibles
        if (error_informe == false) {
            var hay_datos = resultado.hay_datos;
            if (hay_datos == false) {
                error_informe = true;
                descripcion_error_informe = TLNT.Idiomas._("No hay datos disponibles");
            }
        }

        // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
        if (error_informe == true) {
            $('#mensaje-aviso-informe-fichero-comparacion-periodos').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-comparacion-periodos-consumos-costes-generales",
                "pagina-informe-fichero-comparacion-periodos-consumos-costes-totales"]);
            return;
        }

        // Títulos de la páginas
        var hay_datos_costes = resultado.hay_datos_costes;
        if (hay_datos_costes == true) {
            $('#titulo-informe-fichero-comparacion-periodos-consumos-costes-generales').html(TLNT.Idiomas._("Consumos y costes generales"));
            $('#titulo-informe-fichero-comparacion-periodos-consumos-costes-totales').html(TLNT.Idiomas._("Consumos y costes totales"));
        }
        else {
            $('#titulo-informe-fichero-comparacion-periodos-consumos-costes-generales').html(TLNT.Idiomas._("Consumos generales"));
            $('#titulo-informe-fichero-comparacion-periodos-consumos-costes-totales').html(TLNT.Idiomas._("Consumos totales"));
        }

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
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de simulador de tarifas
function smartmeter_simulador_tarifas_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_simulador_tarifas();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-simulador-tarifas').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-simulador-tarifas");
        return;
    }

    // Parámetros del informe
    var medicion = parametros_informe["medicion"];
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
        // Se comprueba si hay error en el resultado del informe
        var error_informe = false;
        var descripcion_error_informe = "";
        var resultado = dame_resultado_ejecucion_script_php_json_usuario_interno(data);
        if (resultado.res == "ERROR") {
            error_informe = true;
            descripcion_error_informe = resultado.msg;
        }

        // Comprobación de datos disponibles
        if (error_informe == false) {
            var hay_datos = resultado.hay_datos;
            if (hay_datos == false) {
                error_informe = true;
                descripcion_error_informe = TLNT.Idiomas._("No hay datos disponibles");
            }
        }

        // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
        if (error_informe == true) {
            $('#mensaje-aviso-informe-fichero-simulador-tarifas').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-simulador-tarifas");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-simulador-tarifas').html(TLNT.Idiomas._("Simulador de tarifas"));

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
            TIPO_INFORME_FICHERO);
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero de consumos y costes generales
function dame_parametros_informe_fichero_smartmeter_consumos_costes_generales() {
    // Se recupera la medicion
    var medicion = $("#medicion_smartmeter_informe_fichero_consumos_costes_generales").text();

    // Se recupera el ratio
    var id_ratio = $("#id_ratio_smartmeter_informe_fichero_consumos_costes_generales").text();

    // Se recuperan los identificadores y los nombres de los sensores
    var ids_sensores = [];
    $("#ids_sensores_smartmeter_informe_fichero_consumos_costes_generales li").each(function() {
        ids_sensores.push($(this).text());
    });
    var nombres_sensores = [];
    $("#nombres_sensores_smartmeter_informe_fichero_consumos_costes_generales li").each(function() {
        nombres_sensores.push($(this).text());
    });

    // Intervalo de valores, agregación y comentarios
    var intervalo_valores = $("#intervalo_valores_smartmeter_informe_fichero_consumos_costes_generales").text();
    var agregacion = $("#agregacion_smartmeter_informe_fichero_consumos_costes_generales").text();
    var comentarios = $("#comentarios_smartmeter_informe_fichero_consumos_costes_generales").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_consumos_costes_generales").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_consumos_costes_generales").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_consumos_costes_generales").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["medicion"] = medicion;
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["agregacion"] = agregacion;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_consumos_costes_generales").text();
        var hora_inicio = $("#hora_inicio_smartmeter_informe_fichero_consumos_costes_generales").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_consumos_costes_generales").text();
        var hora_fin = $("#hora_fin_smartmeter_informe_fichero_consumos_costes_generales").text();
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


// Devuelve los parámetros del informe fichero de consumos y costes totales
function dame_parametros_informe_fichero_smartmeter_consumos_costes_totales() {
    // Se recupera la medicion
    var medicion = $("#medicion_smartmeter_informe_fichero_consumos_costes_totales").text();

    // Se recupera el ratio
    var id_ratio = $("#id_ratio_smartmeter_informe_fichero_consumos_costes_totales").text();

    // Se recuperan los identificadores y los nombres de los sensores
    var ids_sensores = [];
    $("#ids_sensores_smartmeter_informe_fichero_consumos_costes_totales li").each(function() {
        ids_sensores.push($(this).text());
    });
    var nombres_sensores = [];
    $("#nombres_sensores_smartmeter_informe_fichero_consumos_costes_totales li").each(function() {
        nombres_sensores.push($(this).text());
    });

    // Intervalo de valores
    var intervalo_valores = $("#intervalo_valores_smartmeter_informe_fichero_consumos_costes_totales").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_consumos_costes_totales").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_consumos_costes_totales").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_consumos_costes_totales").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["medicion"] = medicion;
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_consumos_costes_totales").text();
        var hora_inicio = $("#hora_inicio_smartmeter_informe_fichero_consumos_costes_totales").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_consumos_costes_totales").text();
        var hora_fin = $("#hora_fin_smartmeter_informe_fichero_consumos_costes_totales").text();
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


// Devuelve los parámetros del informe fichero de consumos y costes por tramo
function dame_parametros_informe_fichero_smartmeter_consumos_costes_tramos() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_smartmeter_informe_fichero_consumos_costes_tramos").text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_consumos_costes_tramos").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_consumos_costes_tramos").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_consumos_costes_tramos").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_consumos_costes_tramos").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_consumos_costes_tramos").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_consumos_costes_tramos").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_consumos_costes_tramos").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, null, fecha_fin, null);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
            var fecha_hora_inicio = fecha_inicio + ", " + "00:00:00";
            var fecha_hora_fin = fecha_fin + ", " + "23:59:59";

            parametros_informe["fecha_inicio"] = fecha_inicio;
            parametros_informe["fecha_fin"] = fecha_fin;
            parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
            parametros_informe["fecha_hora_fin"] = fecha_hora_fin;
        }
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe fichero de información de los excesos de potencia de un sensor
function dame_parametros_informe_fichero_smartmeter_excesos_potencia() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_excesos_potencia").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_excesos_potencia").text();

    // Granularidad
    var granularidad = $('#granularidad_smartmeter_informe_fichero_excesos_potencia').text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_excesos_potencia").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_excesos_potencia").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_excesos_potencia").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["granularidad"] = granularidad;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_smartmeter_informe_fichero_excesos_potencia').text();
        var hora_inicio = $('#hora_inicio_smartmeter_informe_fichero_excesos_potencia').text();
        var fecha_fin = $('#fecha_fin_smartmeter_informe_fichero_excesos_potencia').text();
        var hora_fin = $('#hora_fin_smartmeter_informe_fichero_excesos_potencia').text();
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


// Devuelve los parámetros del informe fichero de información de los excesos de energía reactiva de un sensor
function dame_parametros_informe_fichero_smartmeter_excesos_energia_reactiva() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_excesos_energia_reactiva").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_excesos_energia_reactiva").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_excesos_energia_reactiva").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_excesos_energia_reactiva").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_excesos_energia_reactiva").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_smartmeter_informe_fichero_excesos_energia_reactiva').text();
        var hora_inicio = $('#hora_inicio_smartmeter_informe_fichero_excesos_energia_reactiva').text();
        var fecha_fin = $('#fecha_fin_smartmeter_informe_fichero_excesos_energia_reactiva').text();
        var hora_fin = $('#hora_fin_smartmeter_informe_fichero_excesos_energia_reactiva').text();
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


// Devuelve los parámetros del informe fichero de información de los cortes de tensión de un sensor
function dame_parametros_informe_fichero_smartmeter_cortes_tension() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_cortes_tension").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_cortes_tension").text();

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_smartmeter_informe_fichero_cortes_tension').text();
        var hora_inicio = $('#hora_inicio_smartmeter_informe_fichero_cortes_tension').text();
        var fecha_fin = $('#fecha_fin_smartmeter_informe_fichero_cortes_tension').text();
        var hora_fin = $('#hora_fin_smartmeter_informe_fichero_cortes_tension').text();
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


// Devuelve los parámetros del informe fichero de información de los excesos de caudal de un sensor
function dame_parametros_informe_fichero_smartmeter_excesos_caudal() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_excesos_caudal").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_excesos_caudal").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_excesos_caudal").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_excesos_caudal").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_excesos_caudal").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_smartmeter_informe_fichero_excesos_caudal').text();
        var hora_inicio = $('#hora_inicio_smartmeter_informe_fichero_excesos_caudal').text();
        var fecha_fin = $('#fecha_fin_smartmeter_informe_fichero_excesos_caudal').text();
        var hora_fin = $('#hora_fin_smartmeter_informe_fichero_excesos_caudal').text();
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


// Devuelve los parámetros del informe fichero de comparación de periodos
function dame_parametros_informe_fichero_smartmeter_comparacion_periodos() {
    // Se recupera la medicion
    var medicion = $("#medicion_smartmeter_informe_fichero_comparacion_periodos").text();

    // Se recupera el ratio
    var id_ratio = $("#id_ratio_smartmeter_informe_fichero_comparacion_periodos").text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_comparacion_periodos").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_comparacion_periodos").text();

    // Intervalo de valores
    var intervalo_valores = $('#intervalo_valores_smartmeter_informe_fichero_comparacion_periodos').text();

    // Horario semanal y exclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_comparacion_periodos").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_comparacion_periodos").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["medicion"] = medicion;
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Comprobación de fechas y número de días de los periodos
        var fecha_inicio_anterior = $('#fecha_inicio_periodo_anterior_smartmeter_informe_fichero_comparacion_periodos').text();
        var fecha_inicio_posterior = $('#fecha_inicio_periodo_posterior_smartmeter_informe_fichero_comparacion_periodos').text();
        var numero_dias_periodo = parseInt($('#numero_dias_periodo_smartmeter_informe_fichero_comparacion_periodos').text());
        var resultado = comprueba_fechas_numero_dias_periodos_correctos_usuario_interno(fecha_inicio_anterior, fecha_inicio_posterior, numero_dias_periodo);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
            parametros_informe["fecha_inicio_anterior"] = fecha_inicio_anterior;
            parametros_informe["fecha_inicio_posterior"] = fecha_inicio_posterior;
            parametros_informe["numero_dias_periodo"] = numero_dias_periodo;
        }
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe fichero de simulador de tarifas
function dame_parametros_informe_fichero_smartmeter_simulador_tarifas() {
    // Se recupera la medicion
    var medicion = $("#medicion_smartmeter_informe_fichero_simulador_tarifas").text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_simulador_tarifas").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_simulador_tarifas").text();

    // Se recuperan los identificadores y los nombres de las tarifas seleccionadas
    var ids_tarifas = [];
    var nombres_tarifas = [];
    $("#ids_tarifas_smartmeter_informe_fichero_simulador_tarifas li").each(function() {
        ids_tarifas.push($(this).text());
    });
    $("#nombres_tarifas_smartmeter_informe_fichero_simulador_tarifas li").each(function() {
        nombres_tarifas.push($(this).text());
    });

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["medicion"] = medicion;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["ids_tarifas"] = ids_tarifas;
    parametros_informe["nombres_tarifas"] = nombres_tarifas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_smartmeter_informe_fichero_simulador_tarifas').text();
        var hora_inicio = $('#hora_inicio_smartmeter_informe_fichero_simulador_tarifas').text();
        var fecha_fin = $('#fecha_fin_smartmeter_informe_fichero_simulador_tarifas').text();
        var hora_fin = $('#hora_fin_smartmeter_informe_fichero_simulador_tarifas').text();
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

