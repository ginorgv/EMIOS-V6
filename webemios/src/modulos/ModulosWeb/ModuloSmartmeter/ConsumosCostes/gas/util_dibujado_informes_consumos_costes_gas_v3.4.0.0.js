//
// Funciones para el dibujado de los informes de consumos y costes (SmartMeter) (gas)
//


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Smartmeter - Excesos de caudal)
function dibuja_elemento_plantilla_informe_smartmeter_excesos_caudal_gas(
    info_elemento,
    datos_elemento,
    elementos_informe_elemento,
    parametros_elemento,
    tipo_informe) {
    // Selección de país
    switch (pais_tarifas_gas) {
        case PAIS_ESPANYA: {
            dibuja_elemento_plantilla_informe_smartmeter_excesos_caudal_gas_Espanya(
                info_elemento,
                datos_elemento,
                elementos_informe_elemento,
                parametros_elemento,
                tipo_informe);
            break;
        }
    }
}
