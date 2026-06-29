//
// Funciones de informes personalizados (de SmartMeter) (gas - España)
//


// Genera y muestra el informe de estudio general
function boton_smartmeter_estudio_general_ver_informe_gas_Espanya(parametros_informe) {
    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var apartados = parametros_informe["apartados"];
    var texto_introduccion = parametros_informe["texto_introduccion"];
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
        apartados: apartados,
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

        // Se muestra el informe
        $("#informe-sin-datos-smartmeter-estudio-general").hide();
        $("#informe-smartmeter-estudio-general").show();

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
            texto_introduccion: texto_introduccion,
            parametros_tipo_json: null};
        dibuja_informe_smartmeter_estudio_general_gas_Espanya(
            parametros,
            resultado,
            TIPO_INFORME_WEB_EMIOS);

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_contenido_informes();
    });
}


// Añade los textos del informe del estudio general
function anyade_textos_informe_estudio_general_gas_Espanya(apartados, textos_estudio_general) {
    var notas_instalacion = $("#notas-instalacion-estudio-general").val();
    var notas_analisis_consumo = $("#notas-analisis-consumo-estudio-general").val();
    var notas_analisis_coste = $("#notas-analisis-coste-estudio-general").val();
    var notas_excesos_caudal = $("#notas-excesos-caudal-estudio-general").val();
    var notas_simulacion_factura = $("#notas-simulacion-factura-estudio-general").val();
    var conclusiones = $("#conclusiones-estudio-general").val();

    if (notas_instalacion != "") {
        if (comprueba_longitud_cadena(notas_instalacion, NUMERO_MAXIMO_CARACTERES_NOTAS) == false) {
            $("#notas-instalacion-estudio-general").addClass('data-check-failed');
            return (false);
        }
        textos_estudio_general["notas_instalacion"] = notas_instalacion;
    }
    if (notas_analisis_consumo != "") {
        if (comprueba_longitud_cadena(notas_analisis_consumo, NUMERO_MAXIMO_CARACTERES_NOTAS) == false) {
            $("#notas-analisis-consumo-estudio-general").addClass('data-check-failed');
            return (false);
        }
        textos_estudio_general["notas_analisis_consumo"] = notas_analisis_consumo;
    }
    if (notas_analisis_coste != "") {
        if (comprueba_longitud_cadena(notas_analisis_coste, NUMERO_MAXIMO_CARACTERES_NOTAS) == false) {
            $("#notas-analisis-coste-estudio-general").addClass('data-check-failed');
            return (false);
        }
        textos_estudio_general["notas_analisis_coste"] = notas_analisis_coste;
    }
    if (notas_excesos_caudal != "") {
        if (comprueba_longitud_cadena(notas_excesos_caudal, NUMERO_MAXIMO_CARACTERES_NOTAS) == false) {
            $("#notas-excesos-caudal-estudio-general").addClass('data-check-failed');
            return (false);
        }
        textos_estudio_general["notas_excesos_caudal"] = notas_excesos_caudal;
    }
    if (notas_simulacion_factura != "") {
        if (comprueba_longitud_cadena(notas_simulacion_factura, NUMERO_MAXIMO_CARACTERES_NOTAS) == false) {
            $("#notas-simulacion-factura-estudio-general").addClass('data-check-failed');
            return (false);
        }
        textos_estudio_general["notas_simulacion_factura"] = notas_simulacion_factura;
    }
    if (conclusiones != "") {
        if (comprueba_longitud_cadena(conclusiones, NUMERO_MAXIMO_CARACTERES_NOTAS) == false) {
            $("#conclusiones-estudio-general").addClass('data-check-failed');
            return (false);
        }
        textos_estudio_general["conclusiones"] = conclusiones;
    }
    if ((apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_GAS_ESPANYA) > -1) && (conclusiones == "")) {
        jAlert(TLNT.Idiomas._("Rellene las conclusiones"));
        return (false);
    }
    return (true);
}
