//
// Funciones de informes fichero de caudales (de SmartMeter) (España)
//


// Genera el informe fichero de optimizador de caudales (España)
function muestra_informe_fichero_optimizador_caudales_Espanya(
    tipo_optimizador_caudales,
    parametros_informe,
    resultado_script_php_json) {
    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-optimizador-caudales-' + sufijo_controles).html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-optimizador-caudales-" + sufijo_controles);
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
        if (resultado.hay_datos == false) {
            error_informe = true;
            descripcion_error_informe = TLNT.Idiomas._("No hay datos disponibles");
        }
    }

    // Sufijo de controles
    var sufijo_controles = "";
    switch (tipo_optimizador_caudales) {
        case TIPO_OPTIMIZADOR_SIMULADOR_CAUDALES_AUTOMATICO: {
            sufijo_controles = "automatico";
            break;
        }
        case TIPO_OPTIMIZADOR_SIMULADOR_CAUDALES_MANUAL: {
            sufijo_controles = "manual";
            break;
        }
    }

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_informe == true) {
        $('#mensaje-aviso-informe-fichero-optimizador-caudales-' + sufijo_controles).html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-optimizador-caudales-" + sufijo_controles);
        return;
    }

    // Títulos de las páginas
    $('#titulo-informe-fichero-optimizador-caudales-' + sufijo_controles).html(TLNT.Idiomas._("Optimización de caudales"));

    // Se dibuja el informe
    var parametros = {
        id_contenedor_tabla_caudal_diario_optimo: "contenedor-tabla-caudal-diario-optimo-optimizador-caudales-" + sufijo_controles,
        id_grafica_caudales_diarios: "grafica-caudales-diarios-optimizador-caudales-" + sufijo_controles,
        id_grafica_costes_caudales_diarios: "grafica-costes-caudales-diarios-optimizador-caudales-" + sufijo_controles};
    dibuja_informe_smartmeter_optimizador_caudales_Espanya(
        tipo_optimizador_caudales,
        parametros,
        resultado,
        TIPO_INFORME_FICHERO);
}


// Genera el informe fichero de simulador de caudales (España)
function muestra_informe_fichero_simulador_caudales_Espanya(
    tipo_simulador_caudales,
    parametros_informe,
    resultado_script_php_json) {
    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-simulador-caudales-' + sufijo_controles).html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-simulador-caudales-" + sufijo_controles);
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
        if (resultado.hay_datos == false) {
            error_informe = true;
            descripcion_error_informe = TLNT.Idiomas._("No hay datos disponibles");
        }
    }

    // Sufijo de controles
    var sufijo_controles = "";
    switch (tipo_simulador_caudales) {
        case TIPO_OPTIMIZADOR_SIMULADOR_CAUDALES_AUTOMATICO: {
            sufijo_controles = "automatico";
            break;
        }
        case TIPO_OPTIMIZADOR_SIMULADOR_CAUDALES_MANUAL: {
            sufijo_controles = "manual";
            break;
        }
    }

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_informe == true) {
        $('#mensaje-aviso-informe-fichero-simulador-caudales-' + sufijo_controles).html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-simulador-caudales-" + sufijo_controles);
        return;
    }

    // Títulos de las páginas
    $('#titulo-informe-fichero-simulador-caudales-' + sufijo_controles).html(TLNT.Idiomas._("Simulación de caudales"));

    // Se dibuja el informe
    var parametros = {
        id_contenedor_tabla_caudal_diario_seleccionado: "contenedor-tabla-caudal-diario-seleccionado-simulador-caudales-" + sufijo_controles,
        id_grafica_caudales_diarios: "grafica-caudales-diarios-simulador-caudales-" + sufijo_controles,
        id_grafica_costes_caudales_diarios: "grafica-costes-caudales-diarios-simulador-caudales-" + sufijo_controles};
    dibuja_informe_smartmeter_simulador_caudales_Espanya(
        tipo_simulador_caudales,
        parametros,
        resultado,
        TIPO_INFORME_FICHERO);
}
