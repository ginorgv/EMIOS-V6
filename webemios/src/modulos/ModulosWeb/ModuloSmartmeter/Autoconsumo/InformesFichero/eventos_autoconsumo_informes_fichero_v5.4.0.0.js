//
// Funciones de informes fichero de autoconsumo
//


// Muestra el informe de simulación de autoconsumo
function smartmeter_simulador_autoconsumo_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_smartmeter_simulador_autoconsumo();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-aimulador-autoconsumo').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-simulador-autoconsumo"]);
        return;
    }

    // Parámetros del informe
    var medicion = parametros_informe["medicion"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var id_sensor_generacion = parametros_informe["id_sensor_generacion"];
    var id_tarifa = parametros_informe["id_tarifa"];
    var nombre_sensor_generacion = parametros_informe["nombre_sensor_generacion"];
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
        id_tarifa: id_tarifa,
        nombre_sensor_generacion: nombre_sensor_generacion,
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
            $('#mensaje-aviso-informe-fichero-simulador-autoconsumo').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-simulador-autoconsumo-consumos",
                "pagina-informe-fichero-simulador-autoconsumo-costes"]);
            return;
        }

        // Se oculta la página de costes
        var hay_datos_costes = resultado.hay_datos_costes;
        if (hay_datos_costes == false) {
            elimina_elementos([
                "pagina-informe-fichero-simulador-autoconsumo-costes"]);
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-simulador-autoconsumo-consumos').html(TLNT.Idiomas._("Consumos"));
        $('#titulo-informe-fichero-simulador-autoconsumo-costes').html(TLNT.Idiomas._("Costes"));

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
            TIPO_INFORME_FICHERO);
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe de simulación de autoconsumo
function dame_parametros_informe_fichero_smartmeter_simulador_autoconsumo() {
    // Se recupera la medicion
    var medicion = $("#medicion_smartmeter_informe_fichero_simulador_autoconsumo").text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_smartmeter_informe_fichero_simulador_autoconsumo").text();
    var nombre_sensor = $("#nombre_sensor_smartmeter_informe_fichero_simulador_autoconsumo").text();

    // Identificador y nombre de sensor de autoconsumo
    var id_sensor_generacion = $("#id_sensor_generacion_smartmeter_informe_fichero_simulador_autoconsumo").text();
    var nombre_sensor_generacion = $("#nombre_sensor_generacion_smartmeter_informe_fichero_simulador_autoconsumo").text();

    // Se recupera la tarifa
    var id_tarifa = $("#id_tarifa_smartmeter_informe_fichero_simulador_autoconsumo").text();

    // Tipo de autoconsumo, capacidad de acumulación y factor de multiplicación de generación
    var tipo_autoconsumo = $("#tipo_autoconsumo_smartmeter_informe_fichero_simulador_autoconsumo").text();
    var capacidad_acumulacion = $("#capacidad_acumulacion_smartmeter_informe_fichero_simulador_autoconsumo").text();
    var factor_multiplicacion_generacion = $("#factor_multiplicacion_generacion_smartmeter_informe_fichero_simulador_autoconsumo").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_smartmeter_informe_fichero_simulador_autoconsumo").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_smartmeter_informe_fichero_simulador_autoconsumo").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_smartmeter_informe_fichero_simulador_autoconsumo").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["medicion"] = medicion;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["id_sensor_generacion"] = id_sensor_generacion;
    parametros_informe["id_tarifa"] = id_tarifa;
    parametros_informe["nombre_sensor_generacion"] = nombre_sensor_generacion;
    parametros_informe["tipo_autoconsumo"] = tipo_autoconsumo;
    parametros_informe["capacidad_acumulacion"] = capacidad_acumulacion;
    parametros_informe["factor_multiplicacion_generacion"] = factor_multiplicacion_generacion;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_smartmeter_informe_fichero_simulador_autoconsumo").text();
        var hora_inicio = $("#hora_inicio_smartmeter_informe_fichero_simulador_autoconsumo").text();
        var fecha_fin = $("#fecha_fin_smartmeter_informe_fichero_simulador_autoconsumo").text();
        var hora_fin = $("#hora_fin_smartmeter_informe_fichero_simulador_autoconsumo").text();
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
