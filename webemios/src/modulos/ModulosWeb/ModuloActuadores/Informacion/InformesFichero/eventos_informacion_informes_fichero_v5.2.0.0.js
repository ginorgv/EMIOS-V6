//
// Funciones de informes fichero de información (de Actuadores)
//


// Genera el informe fichero de acciones enviadas
function actuadores_informacion_acciones_enviadas_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_actuadores_informacion_acciones_enviadas();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-acciones-enviadas').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-acciones-enviadas");
        return;
    }

    // Parámetros del informe
    var clase_actuador = parametros_informe["clase_actuador"];
    var nombre_clase_actuador = parametros_informe["nombre_clase_actuador"];
    var destino_accion = parametros_informe["destino_accion"];
    var id_destino_accion = parametros_informe["id_destino_accion"];
    var nombre_destino_accion = parametros_informe["nombre_destino_accion"];
    var origen_acciones = parametros_informe["origen_acciones"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var nombre_campo = parametros_informe["nombre_campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
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
	$.post("./src/modulos/ModulosWeb/ModuloActuadores/Informacion/dame_informacion_acciones_enviadas.php", {
        clase_actuador: clase_actuador,
        nombre_clase_actuador: nombre_clase_actuador,
        destino_accion: destino_accion,
        id_destino_accion: id_destino_accion,
        nombre_destino_accion: nombre_destino_accion,
        origen_acciones: origen_acciones,
        clase_sensor: clase_sensor,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        nombre_campo: nombre_campo,
        parametros_extra_campo: parametros_extra_campo,
        fecha_hora_inicio: fecha_hora_inicio,
		fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        comentarios: comentarios,
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
            $('#mensaje-aviso-informe-fichero-acciones-enviadas').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-acciones-enviadas");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-acciones-enviadas').html(TLNT.Idiomas._("Acciones enviadas"));

        // Se dibuja el informe
        var parametros = {
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            hora_inicio: hora_inicio,
            hora_fin: hora_fin,
            clase_actuador: clase_actuador,
            clase_sensor: clase_sensor,
            campo: campo,
            id_sensor: id_sensor,
            intervalo_valores: intervalo_valores,
            id_grafica_valores_sensor: "grafica-valores-sensor-informacion-acciones-enviadas",
            id_grafica_valores_acumulados_sensor: "grafica-valores-acumulados-sensor-informacion-acciones-enviadas",
            id_grafica_acciones_enviadas: "grafica-acciones-enviadas-informacion-acciones-enviadas",
            id_descripcion_destino: "descripcion-destino-informacion-acciones-enviadas",
            id_contenedor_tabla_acciones_enviadas: "contenedor-tabla-acciones-enviadas-informacion-acciones-enviadas",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-actuador-informacion-acciones-enviadas"};
        dibuja_informe_actuadores_informacion_acciones_enviadas(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero de acciones enviadas
function dame_parametros_informe_fichero_actuadores_informacion_acciones_enviadas() {
    // Filtro de acciones
    var clase_actuador = $("#clase_actuador_actuadores_informe_fichero_acciones_enviadas").text();
    var nombre_clase_actuador = $("#nombre_clase_actuador_actuadores_informe_fichero_acciones_enviadas").text();
    var destino_accion = $("#destino_accion_actuadores_informe_fichero_acciones_enviadas").text();
    var id_destino_accion = $("#id_destino_accion_actuadores_informe_fichero_acciones_enviadas").text();
    var nombre_destino_accion = $("#nombre_destino_accion_actuadores_informe_fichero_acciones_enviadas").text();
    var origen_acciones = $("#origen_acciones_actuadores_informe_fichero_acciones_enviadas").text();

    // Sensor
    var clase_sensor = $("#clase_sensor_actuadores_informe_fichero_acciones_enviadas").text();
    var id_sensor = $("#id_sensor_actuadores_informe_fichero_acciones_enviadas").text();
    var nombre_sensor = $("#nombre_sensor_actuadores_informe_fichero_acciones_enviadas").text();
    var campo = $("#campo_actuadores_informe_fichero_acciones_enviadas").text();
    var nombre_campo = $("#nombre_campo_actuadores_informe_fichero_acciones_enviadas").text();
    var parametros_extra_campo = $('#parametros_extra_campo_actuadores_informe_fichero_acciones_enviadas').text();

    // Intervalo de valores de sensor y comentarios
    var intervalo_valores = $("#intervalo_valores_actuadores_informe_fichero_acciones_enviadas").text();
    var comentarios = $("#comentarios_actuadores_informe_fichero_acciones_enviadas").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_actuadores_informe_fichero_acciones_enviadas").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_actuadores_informe_fichero_acciones_enviadas").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_actuadores_informe_fichero_acciones_enviadas").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["clase_actuador"] = clase_actuador;
    parametros_informe["nombre_clase_actuador"] = nombre_clase_actuador;
    parametros_informe["destino_accion"] = destino_accion;
    parametros_informe["id_destino_accion"] = id_destino_accion;
    parametros_informe["nombre_destino_accion"] = nombre_destino_accion;
    parametros_informe["origen_acciones"] = origen_acciones;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["nombre_campo"] = nombre_campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_actuadores_informe_fichero_acciones_enviadas").text();
        var hora_inicio = $("#hora_inicio_actuadores_informe_fichero_acciones_enviadas").text();
        var fecha_fin = $("#fecha_fin_actuadores_informe_fichero_acciones_enviadas").text();
        var hora_fin = $("#hora_fin_actuadores_informe_fichero_acciones_enviadas").text();
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


