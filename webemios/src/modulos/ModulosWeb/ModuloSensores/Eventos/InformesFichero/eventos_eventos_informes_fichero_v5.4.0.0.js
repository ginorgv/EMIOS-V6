//
// Funciones de informes fichero de eventos (de Sensores)
//


// Genera el informe fichero de activaciones de eventos
function sensores_activaciones_eventos_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_activaciones_eventos();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-activaciones-eventos').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-activaciones-eventos");
        return;
    }

    // Parámetros del informe
    var clase_sensor = parametros_informe["clase_sensor"];
    var origen_evento = parametros_informe["origen_evento"];
    var id_origen_evento = parametros_informe["id_origen_evento"];
    var nombre_origen_evento = parametros_informe["nombre_origen_evento"];
    var granularidad_evento = parametros_informe["granularidad_evento"];
    var ids_eventos = parametros_informe["ids_eventos"];
    var nombres_eventos = parametros_informe["nombres_eventos"];
    var campo = parametros_informe["campo"];
    var nombre_campo = parametros_informe["nombre_campo"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
	$.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_activaciones_eventos.php", {
        clase_sensor: clase_sensor,
        origen_evento: origen_evento,
        id_origen_evento: id_origen_evento,
        nombre_origen_evento: nombre_origen_evento,
        granularidad_evento: granularidad_evento,
        ids_eventos: ids_eventos,
        nombres_eventos: nombres_eventos,
		fecha_hora_inicio: fecha_hora_inicio,
		fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        campo: campo,
        nombre_campo: nombre_campo
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

        // Nota: No se comprueba si hay datos porque siempre se muestran al menos las gráficas de activaciones de eventos
        // (aunque no haya activaciones ...)

        // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
        if (error_informe == true) {
            $('#mensaje-aviso-informe-fichero-activaciones-eventos').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-activaciones-eventos");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-activaciones-eventos').html(TLNT.Idiomas._("Activaciones de eventos"));

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            clase_sensor: clase_sensor,
            origen_evento: origen_evento,
            campo: campo,
            id_grafica_valores_sensor: "grafica-valores-sensor-activaciones-eventos",
            id_grafica_valores_acumulados_sensor: "grafica-valores-acumulados-sensor-activaciones-eventos",
            id_salto_pagina_graficas_valores_sensor_activaciones_eventos: "salto-pagina-graficas-valores-sensor-activaciones-eventos-activaciones-eventos",
            id_graficas_activaciones_eventos: "grafica-activaciones-evento-activaciones-eventos",
            id_contenedores_tablas_activaciones_eventos: "contenedor-tabla-activaciones-evento-activaciones-eventos"};
        dibuja_informe_sensores_activaciones_eventos(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero de activaciones de eventos
function dame_parametros_informe_fichero_sensores_activaciones_eventos() {
    // Identificador y nombre de sensor
    var clase_sensor = $("#clase_sensor_sensores_informe_fichero_activaciones_eventos").text();
    var origen_evento = $("#origen_evento_sensores_informe_fichero_activaciones_eventos").text();
    var id_origen_evento = $("#id_origen_evento_sensores_informe_fichero_activaciones_eventos").text();
    var nombre_origen_evento = $("#nombre_origen_evento_sensores_informe_fichero_activaciones_eventos").text();
    var granularidad_evento = $("#granularidad_evento_sensores_informe_fichero_activaciones_eventos").text();

    // Se recuperan los identificadores y los nombres de los eventos seleccionados
    var ids_eventos = [];
    var nombres_eventos = [];
    $("#ids_eventos_sensores_informe_fichero_activaciones_eventos li").each(function() {
        ids_eventos.push($(this).text());
    });
    $("#nombres_eventos_sensores_informe_fichero_activaciones_eventos li").each(function() {
        nombres_eventos.push($(this).text());
    });

    // Campo de sensor
    var campo = $('#campo_sensores_informe_fichero_activaciones_eventos').text();
    var nombre_campo = $('#nombre_campo_sensores_informe_fichero_activaciones_eventos').text();

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["origen_evento"] = origen_evento;
    parametros_informe["id_origen_evento"] = id_origen_evento;
    parametros_informe["nombre_origen_evento"] = nombre_origen_evento;
    parametros_informe["granularidad_evento"] = granularidad_evento;
    parametros_informe["ids_eventos"] = ids_eventos;
    parametros_informe["nombres_eventos"] = nombres_eventos;
    parametros_informe["campo"] = campo;
    parametros_informe["nombre_campo"] = nombre_campo;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_activaciones_eventos").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_activaciones_eventos").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_activaciones_eventos").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_activaciones_eventos").text();
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
