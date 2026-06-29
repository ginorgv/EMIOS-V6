//
// Funciones de información (de actuadores)
//


// Muestra la información de acciones enviadas a un actuador o grupo de actuadores
function boton_actuadores_informacion_acciones_enviadas_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_actuadores_informacion_acciones_enviadas(false);
    if (parametros_informe == null) {
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
        $("#informe-sin-datos-actuadores-informacion-acciones-enviadas").hide();
        $("#informe-actuadores-informacion-acciones-enviadas").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-valores-sensor-informacion-acciones-enviadas",
            "grafica-valores-acumulados-sensor-informacion-acciones-enviadas",
            "grafica-acciones-enviadas-informacion-acciones-enviadas",
            "descripcion-destino-informacion-acciones-enviadas",
            "contenedor-tabla-acciones-enviadas-informacion-acciones-enviadas",
            "contenedor-tabla-comentarios-actuador-informacion-acciones-enviadas"]);

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
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-acciones-enviadas",
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
            TIPO_INFORME_WEB_EMIOS);
    });
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de información de acciones enviadas a un actuador o grupo de actuadores
function dame_parametros_informe_actuadores_informacion_acciones_enviadas(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Filtro de acciones
    var clase_actuador = $('#clase_actuador_actuadores_informacion_acciones_enviadas').val();
    if (clase_actuador == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase de actuador seleccionada'));
        return (null);
    }
    var nombre_clase_actuador = $('#clase_actuador_actuadores_informacion_acciones_enviadas :selected').text();
    var destino_accion = $('#destino_accion_actuadores_informacion_acciones_enviadas').val();
    var id_destino_accion = $('#id_destino_accion_actuadores_informacion_acciones_enviadas').val();
    if (id_destino_accion == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay destino seleccionado"));
		return (null);
	}
    var nombre_destino_accion = $('#id_destino_accion_actuadores_informacion_acciones_enviadas :selected').text();
    var origen_acciones = $('#origen_acciones_actuadores_informacion_acciones_enviadas').val();

    // Sensor
    var clase_sensor = $('#clase_sensor_actuadores_informacion_acciones_enviadas').val();
    var id_sensor = $('#id_sensor_actuadores_informacion_acciones_enviadas').val();
    var nombre_sensor = $('#id_sensor_actuadores_informacion_acciones_enviadas :selected').text();
    if ((clase_sensor != CLASE_NINGUNA) && (id_sensor == ID_NINGUNO)) {
        jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
		return (null);
    }

    // Campo y parámetros extra de sensor
    var campo = $('#campo_actuadores_informacion_acciones_enviadas').val();
    var nombre_campo = $('#campo_actuadores_informacion_acciones_enviadas :selected').text();
    var parametros_extra_campo = $('#parametros_extra_campo_actuadores_informacion_acciones_enviadas').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return (null);
    }

    // Intervalo de valores de sensor
    var intervalo_valores = $('#intervalo_valores_actuadores_informacion_acciones_enviadas').val();
    if ((id_sensor != ID_NINGUNO) && (intervalo_valores == INTERVALO_VALORES_NINGUNO)) {
        jAlert(TLNT.Idiomas._("No hay intervalo de valores de sensor seleccionado"));
		return (null);
    }

    // Comentarios
    var comentarios = $('#comentarios_actuadores_informacion_acciones_enviadas').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("actuadores_informacion_acciones_enviadas", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_actuadores_informacion_acciones_enviadas");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_actuadores_informacion_acciones_enviadas");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

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

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_actuadores_informacion_acciones_enviadas').val();
        var hora_inicio = $('#hora_inicio_actuadores_informacion_acciones_enviadas').val();
        var fecha_fin = $('#fecha_fin_actuadores_informacion_acciones_enviadas').val();
        var hora_fin = $('#hora_fin_actuadores_informacion_acciones_enviadas').val();
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


