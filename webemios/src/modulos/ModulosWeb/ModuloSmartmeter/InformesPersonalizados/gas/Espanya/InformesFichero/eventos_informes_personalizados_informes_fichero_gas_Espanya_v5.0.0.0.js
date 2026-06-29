//
// Funciones de informes fichero de informes personalizados (gas - España)
//


// Genera el informe fichero de estudio general
function smartmeter_estudio_general_ver_informe_fichero_gas_Espanya(parametros_informe) {
    // Se recuperan los parámetros del informe
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-estudio-general').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-estudio-general-portada",
            "pagina-introduccion-informe-fichero-estudio-general",
            "pagina-informe-fichero-estudio-general-instalacion",
            "pagina-informe-fichero-estudio-general-analisis-consumo",
            "pagina-informe-fichero-estudio-general-analisis-coste",
            "pagina-informe-fichero-estudio-general-excesos-caudal",
            "pagina-informe-fichero-estudio-general-simulacion-factura",
            "pagina-informe-fichero-estudio-general-conclusiones",
            "pagina-informe-fichero-estudio-general-avisos"]);
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var apartados = parametros_informe["apartados"];
    var parametros_tipo_json = parametros_informe["parametros_tipo_json"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/dame_estudio_general_sensor.php", {
        medicion: MEDICION_GAS,
        id_ratio: id_ratio,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        apartados: apartados,
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

        // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
        if (error_informe == true) {
            $('#mensaje-aviso-informe-fichero-estudio-general').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-estudio-general-portada",
                "pagina-introduccion-informe-fichero-estudio-general",
                "pagina-informe-fichero-estudio-general-instalacion",
                "pagina-informe-fichero-estudio-general-analisis-consumo",
                "pagina-informe-fichero-estudio-general-analisis-coste",
                "pagina-informe-fichero-estudio-general-excesos-caudal",
                "pagina-informe-fichero-estudio-general-simulacion-factura",
                "pagina-informe-fichero-estudio-general-conclusiones",
                "pagina-informe-fichero-estudio-general-avisos"]);
            return;
        }
        else {
            elimina_elemento("pagina-parametros-informe-fichero-estudio-general");
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-estudio-general-introduccion').html(TLNT.Idiomas._("Introducción"));
        $('#titulo-informe-fichero-estudio-general-instalacion').html(TLNT.Idiomas._("Instalación"));
        $('#titulo-informe-fichero-estudio-general-analisis-consumo').html(TLNT.Idiomas._("Análisis de consumo"));
        $('#titulo-informe-fichero-estudio-general-analisis-coste').html(TLNT.Idiomas._("Análisis de coste"));
        $('#titulo-informe-fichero-estudio-general-excesos-caudal').html(TLNT.Idiomas._("Excesos de caudal"));
        $('#titulo-informe-fichero-estudio-general-simulacion-factura').html(TLNT.Idiomas._("Simulación de factura"));
        $('#titulo-informe-fichero-estudio-general-conclusiones').html(TLNT.Idiomas._("Conclusiones"));
        $('#titulo-informe-fichero-estudio-general-avisos').html(TLNT.Idiomas._("Avisos"));

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            apartados: apartados,
            texto_introduccion: "",
            parametros_tipo_json: parametros_tipo_json};
        dibuja_informe_smartmeter_estudio_general_gas_Espanya(
            parametros,
            resultado,
            TIPO_INFORME_FICHERO);
    });
}
