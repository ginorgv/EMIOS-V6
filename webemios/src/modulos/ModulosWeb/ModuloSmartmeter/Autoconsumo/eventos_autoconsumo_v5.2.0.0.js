//
// Funciones de autoconsumo (de SmartMeter)
//


// Muestra el informe de simulación de autoconsumo
function boton_smartmeter_simulador_autoconsumo_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_simulador_autoconsumo();
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var id_sensor_generacion = parametros_informe["id_sensor_generacion"];
    var nombre_sensor_generacion = parametros_informe["nombre_sensor_generacion"];
    var id_tarifa = parametros_informe["id_tarifa"];
    var tipo_autoconsumo = parametros_informe["tipo_autoconsumo"];
    var capacidad_acumulacion = parametros_informe["capacidad_acumulacion"];
    var factor_multiplicacion_generacion = parametros_informe["factor_multiplicacion_generacion"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Autoconsumo/dame_simulacion_autoconsumo_sensor.php", {
        medicion: medicion,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        id_sensor_generacion: id_sensor_generacion,
        nombre_sensor_generacion: nombre_sensor_generacion,
        id_tarifa: id_tarifa,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        tipo_autoconsumo: tipo_autoconsumo,
        capacidad_acumulacion: capacidad_acumulacion,
        factor_multiplicacion_generacion: factor_multiplicacion_generacion,
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
        $("#informe-sin-datos-smartmeter-simulador-autoconsumo").hide();
        $("#informe-smartmeter-simulador-autoconsumo").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-consumos-simulador-autoconsumo",
            "grafica-consumos-acumulados-simulador-autoconsumo",
            "contenedor-tabla-consumos-simulador-autoconsumo",
            "grafica-costes-simulador-autoconsumo",
            "grafica-costes-acumulados-simulador-autoconsumo",
            "contenedor-tabla-costes-simulador-autoconsumo"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_grafica_consumos: "grafica-consumos-simulador-autoconsumo",
            id_grafica_consumos_acumulados: "grafica-consumos-acumulados-simulador-autoconsumo",
            id_contenedor_tabla_consumos: "contenedor-tabla-consumos-simulador-autoconsumo",
            id_grafica_costes: "grafica-costes-simulador-autoconsumo",
            id_grafica_costes_acumulados: "grafica-costes-acumulados-simulador-autoconsumo",
            id_contenedor_tabla_costes: "contenedor-tabla-costes-simulador-autoconsumo"};
        dibuja_informe_smartmeter_simulador_autoconsumo(
            parametros,
            resultado,
            TIPO_INFORME_WEB_EMIOS);
    });
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de simulación de autoconsumo
function dame_parametros_informe_smartmeter_simulador_autoconsumo() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Se comprueba si hay sensores seleccionados
    var id_sensor = $('#id_sensor_smartmeter_simulador_autoconsumo').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var id_sensor_generacion = $('#id_sensor_generacion_smartmeter_simulador_autoconsumo').val();
    if (id_sensor_generacion == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor de generación seleccionado"));
        return (null);
	}

    // Se recuperan los nombres de los sensores seleccionados
    var nombre_sensor = $('#id_sensor_smartmeter_simulador_autoconsumo :selected').text();
    var nombre_sensor_generacion = $('#id_sensor_generacion_smartmeter_simulador_autoconsumo :selected').text();

    // Se recupera la tarifa seleccionada
    var id_tarifa = $('#id_tarifa_smartmeter_simulador_autoconsumo').val();

    // Tipo de autoconsumo, capacidad de acumulación y factor de multiplicación de generación
    var tipo_autoconsumo = $('#tipo_autoconsumo_smartmeter_simulador_autoconsumo').val();
    var capacidad_acumulacion = $('#numero_capacidad_acumulacion_smartmeter_simulador_autoconsumo').val();
    if (tipo_autoconsumo == TIPO_AUTOCONSUMO_CON_ACUMULACION) {
        if (PATRON_NUMERO_ENTERO.test(capacidad_acumulacion) == false) {
            jAlert(TLNT.Idiomas._("La capacidad de acumulación debe ser un valor numérico"));
            return (null);
        }
        if (parseInt(capacidad_acumulacion) <= 0) {
            jAlert(TLNT.Idiomas._('La capacidad de acumulación debe ser mayor que 0'));
            return (null);
        }
    }
    var factor_multiplicacion_generacion = $('#numero_factor_multiplicacion_generacion_smartmeter_simulador_autoconsumo').val();
    if (PATRON_NUMERO_REAL.test(factor_multiplicacion_generacion) == false) {
        jAlert(TLNT.Idiomas._("El factor de multiplicación debe ser un valor numérico"));
        return (null);
    }
    if (parseFloat(factor_multiplicacion_generacion) < 0) {
        jAlert(TLNT.Idiomas._('El factor de multiplicación de generación debe ser mayor o igual que 0'));
        return (null);
    }

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("smartmeter_simulador_autoconsumo", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_smartmeter_simulador_autoconsumo");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_smartmeter_simulador_autoconsumo");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["id_sensor_generacion"] = id_sensor_generacion;
    parametros_informe["nombre_sensor_generacion"] = nombre_sensor_generacion;
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["tipo_autoconsumo"] = tipo_autoconsumo;
    parametros_informe["capacidad_acumulacion"] = capacidad_acumulacion;
    parametros_informe["factor_multiplicacion_generacion"] = factor_multiplicacion_generacion;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var fecha_inicio = $('#fecha_inicio_smartmeter_simulador_autoconsumo').val();
    var fecha_fin = $('#fecha_fin_smartmeter_simulador_autoconsumo').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
    if (fechas_correctas == false) {
        return (null);
    }
    var hora_inicio = "00:00:00";
    var hora_fin = "23:59:59";
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
    var fecha_hora_fin = fecha_fin + ", " + hora_fin;

    parametros_informe["fecha_inicio"] = fecha_inicio;
    parametros_informe["fecha_fin"] = fecha_fin;
    parametros_informe["hora_inicio"] = hora_inicio;
    parametros_informe["hora_fin"] = hora_fin;
    parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
    parametros_informe["fecha_hora_fin"] = fecha_hora_fin;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}

