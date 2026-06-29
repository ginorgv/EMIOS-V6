//
// Funciones de informes fichero de consumos y costes (de SmartMeter) (electricidad - España)
//


// Genera el informe fichero de excesos de caudal de un sensor (España)
function smartmeter_excesos_caudal_ver_informe_fichero_gas_Espanya(parametros_informe) {
    // Se recuperan los parámetros del informe
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-excesos-caudal').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-excesos-caudal-excesos-caudal");
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
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/gas/dame_sobrecaudales_sensor_gas.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
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
            $('#mensaje-aviso-informe-fichero-excesos-caudal').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-excesos-caudal-excesos-caudal");
            return;
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-excesos-caudal-excesos-caudal').html(TLNT.Idiomas._("Excesos de caudal"));

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_grafica_caudales_sobrecaudales: "grafica-caudales-sobrecaudales-excesos-caudal",
            id_contenedor_tabla_sobrecaudales: "contenedor-tabla-sobrecaudales-excesos-caudal"};
        dibuja_informe_smartmeter_excesos_caudal_gas_Espanya(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}
