//
// Funciones de potencias (de SmartMeter) (España)
//


// Descarga la plantilla de fichero de potencias máximas para la optimización manual
function boton_smartmeter_optimizador_potencias_manual_descargar_plantilla_fichero_Espanya() {
    var enlace_descarga = "./rsc/ficheros/plantillas/Espanya/plantilla_fichero_potencias_maximas.csv";
    window.location.href = enlace_descarga;
}


// Muestra el informe de los optimizadores de potencias
function muestra_informe_optimizador_potencias_Espanya(tipo_optimizador_potencias, resultado_script_php_json) {
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

    // Se muestra el informe
    $("#informe-sin-datos-smartmeter-optimizador-potencias-" + sufijo_controles).hide();
    $("#informe-smartmeter-optimizador-potencias-" + sufijo_controles).show();

    // Se borran los datos anteriores
    vacia_elementos([
        "contenedor-tabla-potencias-optimas-tramos-optimizador-potencias-" + sufijo_controles,
        "grafica-potencias-optimizador-potencias-" + sufijo_controles]);
    for (var i = 1; i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; i++) {
        vacia_elemento("grafica-costes-potencias-tramos-optimizador-potencias-" + sufijo_controles + "-" + i);
    }

    // Se dibuja el informe
    var parametros = {
        id_contenedor_tabla_potencias_optimas_tramos: "contenedor-tabla-potencias-optimas-tramos-optimizador-potencias-" + sufijo_controles,
        id_grafica_potencias: "grafica-potencias-optimizador-potencias-" + sufijo_controles,
        id_graficas_costes_potencias_tramos: "grafica-costes-potencias-tramos-optimizador-potencias-" + sufijo_controles};
    dibuja_informe_smartmeter_optimizador_potencias_Espanya(
        tipo_optimizador_potencias,
        parametros,
        resultado,
        TIPO_INFORME_WEB_EMIOS);
}


// Descarga la plantilla de fichero de potencias máximas para la simulación manual
function boton_smartmeter_simulador_potencias_manual_descargar_plantilla_fichero_Espanya() {
    var enlace_descarga = "./rsc/ficheros/plantillas/Espanya/plantilla_fichero_potencias_maximas.csv";
    window.location.href = enlace_descarga;
}


// Muestra el informe de los simuladores de potencias
function muestra_informe_simulador_potencias_Espanya(tipo_simulador_potencias, resultado_script_php_json) {
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

    // Se muestra el informe
    $("#informe-sin-datos-smartmeter-simulador-potencias-" + sufijo_controles).hide();
    $("#informe-smartmeter-simulador-potencias-" + sufijo_controles).show();

    // Se borran los datos anteriores
    vacia_elementos([
        "contenedor-tabla-potencias-seleccionadas-tramos-simulador-potencias-" + sufijo_controles,
        "grafica-potencias-simulador-potencias-" + sufijo_controles]);
    for (var i = 1; i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; i++) {
        vacia_elemento("grafica-costes-potencias-tramos-simulador-potencias-" + sufijo_controles + "-" + i);
    }

    // Se dibuja el informe
    var parametros = {
        id_contenedor_tabla_potencias_seleccionadas_tramos: "contenedor-tabla-potencias-seleccionadas-tramos-simulador-potencias-" + sufijo_controles,
        id_grafica_potencias: "grafica-potencias-simulador-potencias-" + sufijo_controles,
        id_graficas_costes_potencias_tramos: "grafica-costes-potencias-tramos-simulador-potencias-" + sufijo_controles};
    dibuja_informe_smartmeter_simulador_potencias_Espanya(
        tipo_simulador_potencias,
        parametros,
        resultado,
        TIPO_INFORME_WEB_EMIOS);
}
