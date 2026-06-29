//
// Funciones de informes fichero de compra de energía (de SmartMeter) (España)
//


// Genera el informe fichero de previsión de compra de energía
function smartmeter_prevision_compra_energia_ver_informe_fichero_Espanya(parametros_informe) {
    // Se recuperan los parámetros del informe
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en los parámetros se muestra un mensaje de aviso y se ocultan las páginas del resultado del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-prevision-compra-energia').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-prevision-compra-energia");
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var fecha_inicio_perfil_horario = parametros_informe["fecha_inicio_perfil_horario"];
    var fecha_fin_perfil_horario = parametros_informe["fecha_fin_perfil_horario"];
    var tipo_perfil_horario = parametros_informe["tipo_perfil_horario"];
    var agrupaciones_dias_semana = parametros_informe["agrupaciones_dias_semana"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/dame_prevision_compra_energia_sensor.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
		fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        fecha_inicio_perfil_horario: fecha_inicio_perfil_horario,
		fecha_fin_perfil_horario: fecha_fin_perfil_horario,
        tipo_perfil_horario: tipo_perfil_horario,
        agrupaciones_dias_semana: JSON.stringify(agrupaciones_dias_semana),
        exclusion_fechas: JSON.stringify(exclusion_fechas)
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
            $('#mensaje-aviso-informe-fichero-prevision-compra-energia').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-prevision-compra-energia");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-prevision-compra-energia-1').html(TLNT.Idiomas._("Consumo estimado"));
        $('#titulo-informe-fichero-prevision-compra-energia-2').html(TLNT.Idiomas._("Consumos anteriores"));

        // Nota: No se ajustan los tamaños de los mapas de calor (aunque ocupen varias páginas)
        // (los valores del mapa de calor deben ser visibles, no como en otros informes)

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_grafica_consumos_estimados: "grafica-consumos-estimados-prevision-compra-energia",
            id_mapa_calor_consumos_estimados: "mapa-calor-consumos-estimados-prevision-compra-energia",
            id_grafica_consumos_perfil_horario: "grafica-consumos-perfil-horario-prevision-compra-energia",
            id_mapa_calor_consumos_perfil_horario_semanales: "mapa-calor-consumos-perfil-horario-semanales-prevision-compra-energia",
            id_mapa_calor_consumos_perfil_horario: "mapa-calor-consumos-perfil-horario-prevision-compra-energia"};
        dibuja_informe_smartmeter_prevision_compra_energia_Espanya(
            parametros,
            resultado,
            TIPO_INFORME_FICHERO);
	});
}


// Genera el informe fichero de desvíos de compra de energía
function smartmeter_desvios_compra_energia_ver_informe_fichero_Espanya(parametros_informe) {
    // Se recuperan los parámetros del informe
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en los parámetros se muestra un mensaje de aviso y se ocultan las páginas del resultado del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-desvios-compra-energia').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-desvios-compra-energia");
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/dame_desvios_compra_energia_sensor.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
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
            $('#mensaje-aviso-informe-fichero-desvios-compra-energia').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-desvios-compra-energia-1",
                "pagina-informe-fichero-desvios-compra-energia-2",
                "pagina-informe-fichero-desvios-compra-energia-3",
                "pagina-informe-fichero-desvios-compra-energia-4"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-desvios-compra-energia-1').html(TLNT.Idiomas._("Totales y consumos"));
        $('#titulo-informe-fichero-desvios-compra-energia-2').html(TLNT.Idiomas._("Desvíos de consumo"));
        $('#titulo-informe-fichero-desvios-compra-energia-3').html(TLNT.Idiomas._("Mapa de calor de desvíos de consumo"));
        $('#titulo-informe-fichero-desvios-compra-energia-4').html(TLNT.Idiomas._("Costes de desvíos"));
        $('#titulo-informe-fichero-desvios-compra-energia-5').html(TLNT.Idiomas._("Mapa de calor de costes de desvíos"));

        // Localización de los mapas de calor
        var dias_mapas_calor = resultado.dias_mapas_calor;
        var id_mapa_calor_desvios_consumo = null;
        var id_mapa_calor_costes_desvios = null;
        var altura_maxima_mapa_calor_desvios_consumo = null;
        var altura_maxima_mapa_calor_costes_desvios = null;
        if (dias_mapas_calor.length > NUMERO_MAXIMO_DIAS_MAPA_CALOR_SMARTMETER_DESVIOS_COMPRA_ENERGIA_DESVIOS_CONSUMO) {
            id_mapa_calor_desvios_consumo = "mapa-calor-desvios-consumo-desvios-compra-energia-2";
            altura_maxima_mapa_calor_desvios_consumo = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
            oculta_elemento("mapa-calor-desvios-consumo-desvios-compra-energia-1");
        }
        else {
            id_mapa_calor_desvios_consumo = "mapa-calor-desvios-consumo-desvios-compra-energia-1";
            altura_maxima_mapa_calor_desvios_consumo = ALTURA_MAXIMA_MAPA_CALOR_DESVIOS_CONSUMO;
            elimina_elemento("pagina-informe-fichero-desvios-compra-energia-3");
        };
        if (dias_mapas_calor.length > NUMERO_MAXIMO_DIAS_MAPA_CALOR_SMARTMETER_DESVIOS_COMPRA_ENERGIA_COSTES_DESVIOS) {
            id_mapa_calor_costes_desvios = "mapa-calor-costes-desvios-desvios-compra-energia-2";
            altura_maxima_mapa_calor_costes_desvios = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
            oculta_elemento("mapa-calor-costes-desvios-desvios-compra-energia-1");
        }
        else {
            id_mapa_calor_costes_desvios = "mapa-calor-costes-desvios-desvios-compra-energia-1";
            altura_maxima_mapa_calor_costes_desvios = ALTURA_MAXIMA_MAPA_CALOR_COSTES_DESVIOS;
            elimina_elemento("pagina-informe-fichero-desvios-compra-energia-5");
        };

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_contenedor_tabla_consumos_desvios_totales: "contenedor-tabla-consumos-desvios-totales-desvios-compra-energia",
            id_grafica_consumos: "grafica-consumos-desvios-compra-energia",
            id_grafica_consumos_acumulados: "grafica-consumos-acumulados-desvios-compra-energia",
            id_grafica_desvios_consumo: "grafica-desvios-consumo-desvios-compra-energia",
            id_grafica_desvios_consumo_acumulados: "grafica-desvios-consumo-acumulados-desvios-compra-energia",
            id_mapa_calor_desvios_consumo: id_mapa_calor_desvios_consumo,
            altura_maxima_mapa_calor_desvios_consumo: altura_maxima_mapa_calor_desvios_consumo,
            id_grafica_costes_desvios: "grafica-costes-desvios-desvios-compra-energia",
            id_grafica_costes_desvios_acumulados: "grafica-costes-desvios-acumulados-desvios-compra-energia",
            id_mapa_calor_costes_desvios: id_mapa_calor_costes_desvios,
            altura_maxima_mapa_calor_costes_desvios: altura_maxima_mapa_calor_costes_desvios};
        dibuja_informe_smartmeter_desvios_compra_energia_Espanya(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
	});
}


// Genera el informe fichero de desvíos ponderados de compra de energía
function smartmeter_desvios_ponderados_compra_energia_ver_informe_fichero_Espanya(parametros_informe) {
    // Se recuperan los parámetros del informe
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en los parámetros se muestra un mensaje de aviso y se ocultan las páginas del resultado del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-desvios-ponderados-compra-energia').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-desvios-ponderados-compra-energia");
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var id_sensor_hijo = parametros_informe["id_sensor_hijo"];
    var nombre_sensor_hijo = parametros_informe["nombre_sensor_hijo"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/dame_desvios_ponderados_compra_energia_sensor.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        id_sensor_hijo: id_sensor_hijo,
        nombre_sensor_hijo: nombre_sensor_hijo,
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
            $('#mensaje-aviso-informe-fichero-desvios-ponderados-compra-energia').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-desvios-ponderados-compra-energia-1",
                "pagina-informe-fichero-desvios-ponderados-compra-energia-2",
                "pagina-informe-fichero-desvios-ponderados-compra-energia-3"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-desvios-ponderados-compra-energia-1').html(TLNT.Idiomas._("Totales y consumos"));
        $('#titulo-informe-fichero-desvios-ponderados-compra-energia-2').html(TLNT.Idiomas._("Costes de desvíos ponderados"));
        $('#titulo-informe-fichero-desvios-ponderados-compra-energia-3').html(TLNT.Idiomas._("Mapa de calor de costes de desvíos ponderados"));

        // Localización del mapa de calor
        var dias_mapa_calor_costes_desvios_ponderados = resultado.dias_mapa_calor_costes_desvios_ponderados;
        var id_mapa_calor_costes_desvios_ponderados = null;
        var altura_maxima_mapa_calor_costes_desvios_ponderados = null;
        if (dias_mapa_calor_costes_desvios_ponderados.length > NUMERO_MAXIMO_DIAS_MAPA_CALOR_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_COSTES_DESVIOS_PONDERADOS) {
            id_mapa_calor_costes_desvios_ponderados = "mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia-2";
            altura_maxima_mapa_calor_costes_desvios_ponderados = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
            oculta_elemento("mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia-1");
        }
        else {
            id_mapa_calor_costes_desvios_ponderados = "mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia-1";
            altura_maxima_mapa_calor_costes_desvios_ponderados = ALTURA_MAXIMA_MAPA_CALOR_COSTES_DESVIOS_PONDERADOS;
            elimina_elemento("pagina-informe-fichero-desvios-ponderados-compra-energia-3");
        };

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_contenedor_tabla_consumos_coste_desvio_ponderado_totales: "contenedor-tabla-consumos-coste-desvio-ponderado-totales-desvios-ponderados-compra-energia",
            id_grafica_consumos: "grafica-consumos-desvios-ponderados-compra-energia",
            id_grafica_consumos_acumulados: "grafica-consumos-acumulados-desvios-ponderados-compra-energia",
            id_grafica_costes_desvios_ponderados: "grafica-costes-desvios-ponderados-desvios-ponderados-compra-energia",
            id_grafica_costes_desvios_ponderados_acumulados: "grafica-costes-desvios-ponderados-acumulados-desvios-ponderados-compra-energia",
            id_mapa_calor_costes_desvios_ponderados: id_mapa_calor_costes_desvios_ponderados};
        dibuja_informe_smartmeter_desvios_ponderados_compra_energia_Espanya(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
	});
}
