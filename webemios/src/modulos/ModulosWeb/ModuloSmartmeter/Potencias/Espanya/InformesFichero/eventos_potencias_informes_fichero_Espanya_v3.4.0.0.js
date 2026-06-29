//
// Funciones de informes fichero de potencias (de SmartMeter) (España)
//


// Genera el informe fichero de optimizador de potencias (España)
function muestra_informe_fichero_optimizador_potencias_Espanya(
    tipo_optimizador_potencias,
    parametros_informe,
    resultado_script_php_json) {
    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-optimizador-potencias-' + sufijo_controles).html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-optimizador-potencias-" + sufijo_controles);
        return;
    }

    // Se comprueba si hay error en el resultado del informe
    var error_informe = false;
    var descripcion_error_informe = "";
    var resultado = dame_resultado_ejecucion_script_php_json_usuario_interno(resultado_script_php_json);
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

    // Sufijo de controles
    var sufijo_controles = "";
    switch (tipo_optimizador_potencias) {
        case TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_AUTOMATICO: {
            sufijo_controles = "automatico";
            break;
        }
        case TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_MANUAL: {
            sufijo_controles = "manual";
            break;
        }
    }

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_informe == true) {
        $('#mensaje-aviso-informe-fichero-optimizador-potencias-' + sufijo_controles).html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-optimizador-potencias-" + sufijo_controles);
        return;
    }

    // Títulos de las páginas
    $('#titulo-informe-fichero-optimizador-potencias-' + sufijo_controles).html(TLNT.Idiomas._("Optimización de potencias"));

    // Se dibuja el informe
    var parametros = {
        id_contenedor_tabla_potencias_optimas_tramos: "contenedor-tabla-potencias-optimas-tramos-optimizador-potencias-" + sufijo_controles,
        id_grafica_potencias: "grafica-potencias-optimizador-potencias-" + sufijo_controles,
        id_graficas_costes_potencias_tramos: "grafica-costes-potencias-tramos-optimizador-potencias-" + sufijo_controles};
    dibuja_informe_smartmeter_optimizador_potencias_Espanya(
        tipo_optimizador_potencias,
        parametros,
        resultado,
        TIPO_INFORME_FICHERO);
}


// Genera el informe fichero de simulador de potencias (España)
function muestra_informe_fichero_simulador_potencias_Espanya(
    tipo_simulador_potencias,
    parametros_informe,
    resultado_script_php_json) {
    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-simulador-potencias-' + sufijo_controles).html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-simulador-potencias-" + sufijo_controles);
        return;
    }

    // Se comprueba si hay error en el resultado del informe
    var error_informe = false;
    var descripcion_error_informe = "";
    var resultado = dame_resultado_ejecucion_script_php_json_usuario_interno(resultado_script_php_json);
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

    // Sufijo de controles
    var sufijo_controles = "";
    switch (tipo_simulador_potencias) {
        case TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_AUTOMATICO: {
            sufijo_controles = "automatico";
            break;
        }
        case TIPO_OPTIMIZADOR_SIMULADOR_POTENCIAS_MANUAL: {
            sufijo_controles = "manual";
            break;
        }
    }

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_informe == true) {
        $('#mensaje-aviso-informe-fichero-simulador-potencias-' + sufijo_controles).html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-simulador-potencias-" + sufijo_controles);
        return;
    }

    // Títulos de las páginas
    $('#titulo-informe-fichero-simulador-potencias-' + sufijo_controles).html(TLNT.Idiomas._("Simulación de potencias"));

    // Se dibuja el informe
    var parametros = {
        id_contenedor_tabla_potencias_seleccionadas_tramos: "contenedor-tabla-potencias-seleccionadas-tramos-simulador-potencias-" + sufijo_controles,
        id_grafica_potencias: "grafica-potencias-simulador-potencias-" + sufijo_controles,
        id_graficas_costes_potencias_tramos: "grafica-costes-potencias-tramos-simulador-potencias-" + sufijo_controles};
    dibuja_informe_smartmeter_simulador_potencias_Espanya(
        tipo_simulador_potencias,
        parametros,
        resultado,
        TIPO_INFORME_FICHERO);
}
