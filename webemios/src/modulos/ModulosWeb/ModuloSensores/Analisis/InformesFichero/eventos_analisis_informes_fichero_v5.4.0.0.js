//
// Funciones de informes fichero de análisis (de Sensores)
//


// Genera el informe fichero de análisis horario
function sensores_analisis_horario_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_analisis_horario();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-analisis-horario').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-analisis-informe-fichero-analisis-horario-1",
            "pagina-analisis-informe-fichero-analisis-horario-2",
            "pagina-analisis-informe-fichero-analisis-horario-3"]);
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
            $('#mensaje-aviso-informe-fichero-analisis-horario').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-analisis-informe-fichero-analisis-horario-1",
                "pagina-analisis-informe-fichero-analisis-horario-2",
                "pagina-analisis-informe-fichero-analisis-horario-3"]);
            return;
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-analisis-horario-1').html(TLNT.Idiomas._("Valores"));
        $('#titulo-informe-fichero-analisis-horario-2').html(TLNT.Idiomas._("Mapa de calor de valores"));
        $('#titulo-informe-fichero-analisis-horario-3').html(TLNT.Idiomas._("Análisis horario"));

        // Localización del mapa de calor de valores
        var dias_mapa_calor_valores = resultado.dias_mapa_calor_valores;
        var id_mapa_calor_valores = null;
        var altura_maxima_mapa_calor_valores = null;
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var id_mapa_calor_valores = null;
            var altura_maxima_mapa_calor_valores = null;
            if (dias_mapa_calor_valores.length > NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_ANALISIS_INFORMES_FICHERO) {
                id_mapa_calor_valores = "mapa-calor-valores-analisis-horario-2";
                altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
                oculta_elemento("mapa-calor-valores-analisis-horario-1");
            }
            else {
                id_mapa_calor_valores = "mapa-calor-valores-analisis-horario-1";
                altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_ANALISIS_HORARIO_INFORME_FICHERO;
                elimina_elemento("pagina-analisis-informe-fichero-analisis-horario-2");
            };
        }
        else {
            oculta_elemento("mapa-calor-valores-analisis-horario-1");
            elimina_elemento("pagina-analisis-informe-fichero-analisis-horario-2");
        }

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
            id_mapa_calor_valores: id_mapa_calor_valores,
            altura_maxima_mapa_calor_valores: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO,
            id_grafica_medias_valores: "grafica-medias-valores-analisis-horario",
            id_grafica_coeficientes_variacion_valores: "grafica-coeficientes-variacion-valores-analisis-horario",
            id_grafica_lorenz_valores: "grafica-lorenz-valores-analisis-horario",
            id_contenedor_tabla_percentiles_valores: "contenedor-tabla-percentiles-valores-analisis-horario",
            id_grafica_porcentajes_valores: "grafica-porcentajes-valores-analisis-horario"};
        dibuja_informe_sensores_analisis_horario(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de análisis diario
function sensores_analisis_diario_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_analisis_diario();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-analisis-diario').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-analisis-informe-fichero-analisis-diario-1",
            "pagina-analisis-informe-fichero-analisis-diario-2",
            "pagina-analisis-informe-fichero-analisis-diario-3",
            "pagina-analisis-informe-fichero-analisis-diario-4"]);
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
            $('#mensaje-aviso-informe-fichero-analisis-diario').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-analisis-informe-fichero-analisis-diario-1",
                "pagina-analisis-informe-fichero-analisis-diario-2",
                "pagina-analisis-informe-fichero-analisis-diario-3",
                "pagina-analisis-informe-fichero-analisis-diario-4"]);
            return;
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-analisis-diario-1').html(TLNT.Idiomas._("Valores"));
        $('#titulo-informe-fichero-analisis-diario-2').html(TLNT.Idiomas._("Mapa de calor de valores"));
        $('#titulo-informe-fichero-analisis-diario-3').html(TLNT.Idiomas._("Análisis diario") + " (I)");
        $('#titulo-informe-fichero-analisis-diario-4').html(TLNT.Idiomas._("Análisis diario") + " (II)");
        $('#titulo-informe-fichero-analisis-diario-5').html(TLNT.Idiomas._("Valores diarios"));

        // Localización del mapa de calor de valores
        var dias_mapa_calor_valores = resultado.dias_mapa_calor_valores;
        var id_mapa_calor_valores = null;
        var altura_maxima_mapa_calor_valores = null;
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var id_mapa_calor_valores = null;
            var altura_maxima_mapa_calor_valores = null;
            if (dias_mapa_calor_valores.length > NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_ANALISIS_INFORMES_FICHERO) {
                id_mapa_calor_valores = "mapa-calor-valores-analisis-diario-2";
                altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
                oculta_elemento("mapa-calor-valores-analisis-diario-1");
            }
            else {
                id_mapa_calor_valores = "mapa-calor-valores-analisis-diario-1";
                altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_ANALISIS_HORARIO_INFORME_FICHERO;
                elimina_elemento("pagina-analisis-informe-fichero-analisis-diario-2");
            };
        }
        else {
            oculta_elemento("mapa-calor-valores-analisis-horario-1");
            elimina_elemento("pagina-analisis-informe-fichero-analisis-diario-2");
        }

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
            id_mapa_calor_valores: id_mapa_calor_valores,
            altura_maxima_mapa_calor_valores: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO,
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
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de análisis de comportamiento
function sensores_analisis_comportamiento_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_analisis_comportamiento();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-analisis-comportamiento').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-analisis-informe-fichero-analisis-comportamiento");
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Se recuperan los datos para el informe
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
            $('#mensaje-aviso-informe-fichero-analisis-comportamiento').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-analisis-informe-fichero-analisis-comportamiento");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-analisis-comportamiento').html(TLNT.Idiomas._("Análisis de comportamiento"));

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
            TIPO_INFORME_FICHERO);
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero de información de análisis horario
function dame_parametros_informe_fichero_sensores_analisis_horario() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_analisis_horario").text();

    // Se recupera la información del sensor
    var clase_sensor = $("#clase_sensor_sensores_informe_fichero_analisis_horario").text();
    var campo = $('#campo_sensores_informe_fichero_analisis_horario').text();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_informe_fichero_analisis_horario').text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_analisis_horario").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_analisis_horario").text();

    // Tipo de mapa de calor
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_analisis_horario").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_analisis_horario").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_analisis_horario").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_analisis_horario").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

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

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_analisis_horario").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_analisis_horario").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, null, fecha_fin, null);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
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
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe fichero de información de análisis diario
function dame_parametros_informe_fichero_sensores_analisis_diario() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_analisis_diario").text();

    // Se recupera la información del sensor
    var clase_sensor = $("#clase_sensor_sensores_informe_fichero_analisis_diario").text();
    var campo = $('#campo_sensores_informe_fichero_analisis_diario').text();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_informe_fichero_analisis_diario').text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_analisis_diario").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_analisis_diario").text();

    // Tipo de mapa de calor
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_analisis_diario").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_analisis_diario").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_analisis_diario").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_analisis_diario").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

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

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_analisis_diario").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_analisis_diario").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, null, fecha_fin, null);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
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
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe fichero de información de análisis de comportamiento
function dame_parametros_informe_fichero_sensores_analisis_comportamiento() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_analisis_comportamiento").text();

    // Se recuperan la clase de sensor, campo y parámetros extra
    var clase_sensor = $("#clase_sensor_sensores_informe_fichero_analisis_comportamiento").text();
    var campo = $("#campo_sensores_informe_fichero_analisis_comportamiento").text();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_informe_fichero_analisis_comportamiento').text();

    // Se recuperan los identificadores y los nombres de los sensores
    var ids_sensores = [];
    $("#ids_sensores_sensores_informe_fichero_analisis_comportamiento li").each(function() {
        ids_sensores.push($(this).text());
    });
    var nombres_sensores = [];
    $("#nombres_sensores_sensores_informe_fichero_analisis_comportamiento li").each(function() {
        nombres_sensores.push($(this).text());
    });

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_analisis_comportamiento").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_analisis_comportamiento").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_analisis_comportamiento").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_analisis_comportamiento").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_analisis_comportamiento").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, null, fecha_fin, null);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
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
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}
