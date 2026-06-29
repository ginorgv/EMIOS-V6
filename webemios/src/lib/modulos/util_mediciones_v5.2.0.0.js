//
// Funciones de mediciones
//


// Devuelve la clase de sensor correspondiente a la medición
function dame_clase_sensor_medicion(medicion) {
    var clase_sensor = null;
    switch (medicion) {
        case MEDICION_ELECTRICIDAD: {
            clase_sensor = CLASE_SENSOR_ENERGIA_ACTIVA;
            break;
        }
        case MEDICION_GAS: {
            clase_sensor = CLASE_SENSOR_GAS;
            break;
        }
        case MEDICION_AGUA: {
            clase_sensor = CLASE_SENSOR_AGUA;
            break;
        }
        case MEDICION_NINGUNA: {
            clase_sensor = CLASE_NINGUNA;
            break;
        }
    }
    return (clase_sensor);
}