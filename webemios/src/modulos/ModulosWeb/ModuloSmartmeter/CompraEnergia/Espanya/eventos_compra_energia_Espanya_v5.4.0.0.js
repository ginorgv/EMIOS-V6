//
// Funciones de compra de energía (de SmartMeter) (España)
//


// Muestra el informe de previsión de compra de energía
function boton_smartmeter_prevision_compra_energia_ver_informe_Espanya(parametros_informe) {
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

    // Se recupera la información de previsión de compra de energía
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
        $("#informe-sin-datos-smartmeter-prevision-compra-energia").hide();
        $("#informe-smartmeter-prevision-compra-energia").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-consumos-estimados-prevision-compra-energia",
            "mapa-calor-consumos-estimados-prevision-compra-energia",
            "grafica-consumos-perfil-horario-prevision-compra-energia",
            "mapa-calor-consumos-perfil-horario-semanales-prevision-compra-energia",
            "mapa-calor-consumos-perfil-horario-prevision-compra-energia"]);

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
            TIPO_INFORME_WEB_EMIOS);

        // Se habilita el botón de exportar e importar valores diarios
        $("#boton_smartmeter_prevision_compra_energia_exportar_importar_valores_diarios").prop('disabled', false);
    });
}


// Exporta los valores a fichero del informe de previsión de compra de energía e importa estos valores al sensor de compra de energía
function boton_smartmeter_prevision_compra_energia_exportar_importar_valores_diarios_Espanya() {
    // Pregunta de confirmación
    jConfirmAcceptCancelAlert(TLNT.Idiomas._("Los valores guardados del sensor en la fecha a importar se borrarán. ¿Está seguro?"), TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            // Nombre del fichero CSV de valores exportados
            var nombre_sensor = $('#id_sensor_smartmeter_prevision_compra_energia :selected').text();
            var cadena_fecha_inicio_local = $('#fecha_inicio_smartmeter_prevision_compra_energia').val();
            var cadena_fecha_fin_local = $('#fecha_fin_smartmeter_prevision_compra_energia').val();
            var cadena_fecha_inicio_fichero = convierte_formato_fecha(cadena_fecha_inicio_local, formato_fecha_local_jquery_ui, FORMATO_FECHA_FICHERO_JQUERY_UI);
            var cadena_fecha_fin_fichero = convierte_formato_fecha(cadena_fecha_fin_local, formato_fecha_local_jquery_ui, FORMATO_FECHA_FICHERO_JQUERY_UI);
            var nombre_fichero_csv = "prevision_compra_energia_" + nombre_sensor + "_" + cadena_fecha_inicio_fichero + "_" + cadena_fecha_fin_fichero;

            // Se guardan los valores del mapa de calor (utilizando la misma función que la opción de menú contextual de exportar valores)
            var id_mapa_calor = "mapa-calor-consumos-estimados-prevision-compra-energia";
            var tipo_origen = TIPO_ORIGEN_MENU_CONTEXTUAL_INFORME;
            guarda_valores_mapa_calor(
                id_mapa_calor,
                tipo_origen,
                nombre_fichero_csv,
                importa_valores_diarios_prevision_compra_energia_Espanya);
        }
    });
}


// Importa los valores diarios desde el informe de previsión de compra de energía
function importa_valores_diarios_prevision_compra_energia_Espanya(ruta_fichero_valores_diarios) {
    // Nombre de sensor
    var nombre_sensor = $('#id_sensor_smartmeter_prevision_compra_energia :selected').text();

    // Se importan los valores diarios
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/importa_valores_diarios_compra_energia_sensor.php", {
        nombre_sensor: nombre_sensor,
        ruta_fichero_valores_diarios: ruta_fichero_valores_diarios,
        origen_importacion_valores: ORIGEN_IMPORTACION_VALORES_DIARIOS_COMPRA_ENERGIA_SENSOR_INFORME
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);

        // Se guarda el fichero de valores diarios
        window.location.href = ruta_fichero_valores_diarios;
    });
}


// Muestra el informe de desvíos de compra de energía
function boton_smartmeter_desvios_compra_energia_ver_informe_Espanya(parametros_informe) {
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

    // Se recupera la información de previsión de compra de energía
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
        $("#informe-sin-datos-smartmeter-desvios-compra-energia").hide();
        $("#informe-smartmeter-desvios-compra-energia").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "contenedor-tabla-consumos-desvios-totales-desvios-compra-energia",
            "grafica-consumos-desvios-compra-energia",
            "grafica-consumos-acumulados-desvios-compra-energia",
            "grafica-desvios-consumo-desvios-compra-energia",
            "grafica-desvios-consumo-acumulados-desvios-compra-energia",
            "mapa-calor-desvios-consumo-desvios-compra-energia",
            "grafica-costes-desvios-desvios-compra-energia",
            "grafica-costes-desvios-acumulados-desvios-compra-energia",
            "mapa-calor-costes-desvios-desvios-compra-energia"]);

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
            id_mapa_calor_desvios_consumo: "mapa-calor-desvios-consumo-desvios-compra-energia",
            id_grafica_costes_desvios: "grafica-costes-desvios-desvios-compra-energia",
            id_grafica_costes_desvios_acumulados: "grafica-costes-desvios-acumulados-desvios-compra-energia",
            id_mapa_calor_costes_desvios: "mapa-calor-costes-desvios-desvios-compra-energia"};
        dibuja_informe_smartmeter_desvios_compra_energia_Espanya(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra el informe de desvíos ponderados de compra de energía
function boton_smartmeter_desvios_ponderados_compra_energia_ver_informe_Espanya(parametros_informe) {
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

    // Se recupera la información de previsión de compra de energía
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
        $("#informe-sin-datos-smartmeter-desvios-ponderados-compra-energia").hide();
        $("#informe-smartmeter-desvios-ponderados-compra-energia").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "contenedor-tabla-consumos-coste-desvio-ponderado-desvios-ponderados-compra-energia",
            "grafica-consumos-desvios-ponderados-compra-energia",
            "grafica-consumos-acumulados-desvios-ponderados-compra-energia",
            "grafica-costes-desvios-ponderados-desvios-ponderados-compra-energia",
            "grafica-costes-desvios-ponderados-acumulados-desvios-ponderados-compra-energia",
            "mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia"]);

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
            id_mapa_calor_costes_desvios_ponderados: "mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia"};
        dibuja_informe_smartmeter_desvios_ponderados_compra_energia_Espanya(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}
