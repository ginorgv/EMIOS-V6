//
// Funciones de energía reactiva (de SmartMeter) (España)
//


// Muestra el informe de simulación de batería de condensadores
function boton_smartmeter_simulador_bateria_condensadores_ver_informe_Espanya(parametros_informe) {
    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var diferencia_capacidad = parametros_informe["diferencia_capacidad"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/dame_simulacion_bateria_condensadores_sensor.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        diferencia_capacidad: diferencia_capacidad,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
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
        $("#informe-sin-datos-smartmeter-simulador-bateria-condensadores").hide();
        $("#informe-smartmeter-simulador-bateria-condensadores").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "contenedor-tabla-coste-energia-reactiva-simulador-bateria-condensadores",
            "grafica-consumos-energia-simulador-bateria-condensadores",
            "grafica-cosenos-phi-simulador-bateria-condensadores",
            "grafica-penalizable-simulador-bateria-condensadores"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_contenedor_tabla_energia_reactiva_tramos: "contenedor-tabla-coste-energia-reactiva-simulador-bateria-condensadores",
            id_grafica_consumos_energia: "grafica-consumos-energia-simulador-bateria-condensadores",
            id_grafica_coseno_phi: "grafica-cosenos-phi-simulador-bateria-condensadores",
            id_grafica_penalizable: "grafica-penalizable-simulador-bateria-condensadores"};
        dibuja_informe_smartmeter_simulador_bateria_condensadores_Espanya(
            parametros,
            resultado,
            TIPO_INFORME_WEB_EMIOS);
	});
}

