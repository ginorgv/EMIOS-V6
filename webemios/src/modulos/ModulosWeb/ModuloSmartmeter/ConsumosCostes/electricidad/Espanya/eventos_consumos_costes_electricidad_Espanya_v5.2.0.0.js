//
// Funciones de consumos y costes (de SmartMeter) (electricidad - España)
//


// Muestra información de los excesos de potencia de un sensor (España)
function boton_smartmeter_excesos_potencia_ver_informe_electricidad_Espanya(parametros_informe) {
    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var granularidad = parametros_informe["granularidad"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/dame_sobrepotencias_sensor_electricidad.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        minutos_desfase_utc: minutos_desfase_utc,
        granularidad: granularidad,
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
        $("#informe-sin-datos-smartmeter-excesos-potencia").hide();
        $("#informe-smartmeter-excesos-potencia").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-potencias-potencias-contratadas-excesos-potencia",
            "grafica-sobrepotencias-absolutas-excesos-potencia",
            "contenedor-tabla-sobrepotencias-tramos-excesos-potencia"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            granularidad: granularidad,
            id_grafica_potencias_potencias_contratadas: "grafica-potencias-potencias-contratadas-excesos-potencia",
            id_grafica_sobrepotencias_absolutas: "grafica-sobrepotencias-absolutas-excesos-potencia",
            id_contenedor_tabla_sobrepotencias_tramos: "contenedor-tabla-sobrepotencias-tramos-excesos-potencia"};
        dibuja_informe_smartmeter_excesos_potencia_electricidad_Espanya(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra información sobre los excesos de energía reactiva de un sensor (España)
function boton_smartmeter_excesos_energia_reactiva_ver_informe_electricidad_Espanya(parametros_informe) {
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
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/dame_excesos_energia_reactiva_sensor_electricidad.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
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
        $("#informe-sin-datos-smartmeter-excesos-energia-reactiva").hide();
        $("#informe-smartmeter-excesos-energia-reactiva").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-consumos-energia-excesos-energia-reactiva",
            "grafica-coseno-phi-excesos-energia-reactiva",
            "grafica-penalizable-excesos-energia-reactiva",
            "contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_grafica_consumos_energia: "grafica-consumos-energia-excesos-energia-reactiva",
            id_grafica_coseno_phi: "grafica-coseno-phi-excesos-energia-reactiva",
            id_grafica_penalizable: "grafica-penalizable-excesos-energia-reactiva",
            id_contenedor_tabla_energia_reactiva_tramos: "contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva"};
        dibuja_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
	});
}
