//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Smartmeter - Instalación)
function dibuja_elemento_plantilla_informe_smartmeter_instalacion(
    info_elemento,
    datos_elemento,
    parametros_elemento,
    tipo_informe) {
    // Medición
    var parametros_tipo = info_elemento["parametros_tipo"];
    var medicion = parametros_tipo["medicion"];

    // Selección de medición y país
    switch (medicion) {
        case MEDICION_ELECTRICIDAD: {
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    dibuja_elemento_plantilla_informe_smartmeter_instalacion_electricidad_Espanya(
                        info_elemento,
                        datos_elemento,
                        parametros_elemento,
                        tipo_informe);
                    break;
                }
            }
            break;
        }
        case MEDICION_GAS: {
            switch (pais_tarifas_gas) {
                case PAIS_ESPANYA: {
                    dibuja_elemento_plantilla_informe_smartmeter_instalacion_gas_Espanya(
                        info_elemento,
                        datos_elemento,
                        parametros_elemento,
                        tipo_informe);
                    break;
                }
            }
            break;
        }
        case MEDICION_AGUA: {
            switch (pais_tarifas_agua) {
                case PAIS_ESPANYA: {
                    dibuja_elemento_plantilla_informe_smartmeter_instalacion_agua_Espanya(
                        info_elemento,
                        datos_elemento,
                        parametros_elemento,
                        tipo_informe);
                    break;
                }
            }
            break;
        }
    }
}
