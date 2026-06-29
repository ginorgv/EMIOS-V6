//
// Funciones de caudales (de SmartMeter) (España)
//


// Descarga la plantilla de fichero de caudales máximas para la optimización manual
function boton_smartmeter_optimizador_caudales_manual_descargar_plantilla_fichero_Espanya() {
    var enlace_descarga = "./rsc/ficheros/plantillas/Espanya/plantilla_fichero_caudales_maximos.csv";
    window.location.href = enlace_descarga;
}


// Muestra el informe de los optimizadores de caudales
function muestra_informe_optimizador_caudales_Espanya(tipo_optimizador_caudales, resultado_script_php_json) {
    // Detección de error en el resultado
    var resultado = dame_resultado_ejecucion_script_php_json(resultado_script_php_json);
    if (resultado == null) {
        return;
    }

    // Comprobación de datos disponibles
    if (resultado.hay_datos == false) {
        jAlert(TLNT.Idiomas._("No hay datos disponibles"));
        return;
    }

    // Sufijo de los controles
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

    // Se muestra el informe
    $("#informe-sin-datos-smartmeter-optimizador-caudales-" + sufijo_controles).hide();
    $("#informe-smartmeter-optimizador-caudales-" + sufijo_controles).show();

    // Se borran los datos anteriores
    vacia_elementos([
        "contenedor-tabla-caudal-diario-optimo-optimizador-caudales-" + sufijo_controles,
        "grafica-caudales-diarios-optimizador-caudales-" + sufijo_controles,
        "grafica-costes-caudales-diarios-optimizador-caudales-" + sufijo_controles]);

    // Se dibuja el informe
    var parametros = {
        id_contenedor_tabla_caudal_diario_optimo: "contenedor-tabla-caudal-diario-optimo-optimizador-caudales-" + sufijo_controles,
        id_grafica_caudales_diarios: "grafica-caudales-diarios-optimizador-caudales-" + sufijo_controles,
        id_grafica_costes_caudales_diarios: "grafica-costes-caudales-diarios-optimizador-caudales-" + sufijo_controles};
    dibuja_informe_smartmeter_optimizador_caudales_Espanya(
        tipo_optimizador_caudales,
        parametros,
        resultado,
        TIPO_INFORME_WEB_EMIOS);
}


// Descarga la plantilla de fichero de caudales diarios máximos para la simulación manual
function boton_smartmeter_simulador_caudales_manual_descargar_plantilla_fichero_Espanya() {
    var enlace_descarga = "./rsc/ficheros/plantillas/Espanya/plantilla_fichero_caudales_maximos.csv";
    window.location.href = enlace_descarga;
}


// Muestra el informe de los simuladores de caudales
function muestra_informe_simulador_caudales_Espanya(tipo_simulador_caudales, resultado_script_php_json) {
    // Detección de error en el resultado
    var resultado = dame_resultado_ejecucion_script_php_json(resultado_script_php_json);
    if (resultado == null) {
        return;
    }

    // Comprobación de datos disponibles
    if (resultado.hay_datos == false) {
        jAlert(TLNT.Idiomas._("No hay datos disponibles"));
        return;
    }

    // Sufijo de los controles
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


    // Se muestra el informe
    $("#informe-sin-datos-smartmeter-simulador-caudales-" + sufijo_controles).hide();
    $("#informe-smartmeter-simulador-caudales-" + sufijo_controles).show();

    // Se borran los datos anteriores
    vacia_elementos([
        "contenedor-tabla-caudal-diario-seleccionado-simulador-caudales-" + sufijo_controles,
        "grafica-caudales-diarios-simulador-caudales-" + sufijo_controles,
        "grafica-costes-caudales-diarios-simulador-caudales-" + sufijo_controles]);

    // Se dibuja el informe
    var parametros = {
        id_contenedor_tabla_caudal_diario_seleccionado: "contenedor-tabla-caudal-diario-seleccionado-simulador-caudales-" + sufijo_controles,
        id_grafica_caudales_diarios: "grafica-caudales-diarios-simulador-caudales-" + sufijo_controles,
        id_grafica_costes_caudales_diarios: "grafica-costes-caudales-diarios-simulador-caudales-" + sufijo_controles};
    dibuja_informe_smartmeter_simulador_caudales_Espanya(
        tipo_simulador_caudales,
        parametros,
        resultado,
        TIPO_INFORME_WEB_EMIOS);
}
