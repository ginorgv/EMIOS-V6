//
// Funciones de consumos y costes (de SmartMeter) (electricidad)
//


// Muestra información de consumos y costes por tramo
function boton_smartmeter_consumos_costes_tramos_ver_informe_electricidad(parametros_informe) {
    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/dame_consumos_costes_sensor_tramos_electricidad.php", {
        id_ratio: id_ratio,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        valor: ID_TODOS,
        agrupacion_valores: ID_TODOS,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        minutos_desfase_utc: minutos_desfase_utc,
        mostrar_tablas_tramos: true,
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
        $("#informe-sin-datos-smartmeter-consumos-costes-tramos").hide();
        $("#informe-smartmeter-consumos-costes-tramos").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-consumos-tramos-horarios-consumos-costes-tramos",
            "grafica-consumos-tramos-diarios-consumos-costes-tramos",
            "grafica-consumos-tramos-dias-semana-consumos-costes-tramos",
            "contenedor-tabla-consumos-tramos-consumos-costes-tramos",
            "grafica-costes-tramos-horarios-consumos-costes-tramos",
            "grafica-costes-tramos-diarios-consumos-costes-tramos",
            "grafica-costes-tramos-dias-semana-consumos-costes-tramos",
            "contenedor-tabla-costes-tramos-consumos-costes-tramos"]);

        // Se dibuja el informe
        var parametros = {
            id_grafica_consumos_tramos_horarios: "grafica-consumos-tramos-horarios-consumos-costes-tramos",
            id_grafica_consumos_tramos_diarios: "grafica-consumos-tramos-diarios-consumos-costes-tramos",
            id_grafica_medias_consumos_tramos_dias_semana: "grafica-consumos-tramos-dias-semana-consumos-costes-tramos",
            id_contenedor_tabla_consumos_tramos: "contenedor-tabla-consumos-tramos-consumos-costes-tramos",
            id_grafica_costes_tramos_horarios: "grafica-costes-tramos-horarios-consumos-costes-tramos",
            id_grafica_costes_tramos_diarios: "grafica-costes-tramos-diarios-consumos-costes-tramos",
            id_grafica_medias_costes_tramos_dias_semana: "grafica-costes-tramos-dias-semana-consumos-costes-tramos",
            id_contenedor_tabla_costes_tramos: "contenedor-tabla-costes-tramos-consumos-costes-tramos"};
        dibuja_informe_smartmeter_consumos_costes_tramos_electricidad(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra información sobre los cortes de tensión de un sensor
function boton_smartmeter_cortes_tension_ver_informe_electricidad(parametros_informe) {
    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/dame_cortes_tension_sensor_electricidad.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
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
        $("#informe-sin-datos-smartmeter-cortes-tension").hide();
        $("#informe-smartmeter-cortes-tension").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-cortes-tension-consumos-cortes-tension",
            "contenedor-tabla-cortes-tension-cortes-tension"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_grafica_consumos_cortes_tension: "grafica-cortes-tension-consumos-cortes-tension",
            id_contenedor_tabla_cortes_tension: "contenedor-tabla-cortes-tension-cortes-tension"};
        dibuja_informe_smartmeter_cortes_tension_electricidad(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
	});
}