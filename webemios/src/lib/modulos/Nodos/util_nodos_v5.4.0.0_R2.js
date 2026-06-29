//
// Funciones de nodos
//


// Devuelve la cadena de horario semanal
function dame_caracteristicas_clase_sensor(clase_sensor) {
    var caracteristicas_clase_sensor = [];

    // Clase de sensor
    switch (clase_sensor) {
        case CLASE_SENSOR_TEMPERATURA: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_PUNTUAL;
            caracteristicas_clase_sensor["numero_valores"] = 1;
            caracteristicas_clase_sensor["campos_puntuales"] = [CAMPO_TEMPERATURA];
            caracteristicas_clase_sensor["campos_incrementos"] = [];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = null;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_TIEMPO_REAL;
            break;
        }
        case CLASE_SENSOR_HUMEDAD: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_PUNTUAL;
            caracteristicas_clase_sensor["numero_valores"] = 1;
            caracteristicas_clase_sensor["campos_puntuales"] = [CAMPO_HUMEDAD];
            caracteristicas_clase_sensor["campos_incrementos"] = [];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = null;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_TIEMPO_REAL;
            break;
        }
        case CLASE_SENSOR_LUZ_INTERIOR: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_PUNTUAL;
            caracteristicas_clase_sensor["numero_valores"] = 2;
            caracteristicas_clase_sensor["campos_puntuales"] = [CAMPO_ILUMINACION, CAMPO_LUZ_ARTIFICIAL];
            caracteristicas_clase_sensor["campos_incrementos"] = [];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = null;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_TIEMPO_REAL;
            break;
        }
        case CLASE_SENSOR_VIENTO: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_PUNTUAL;
            caracteristicas_clase_sensor["numero_valores"] = 2;
            caracteristicas_clase_sensor["campos_puntuales"] = [CAMPO_VELOCIDAD, CAMPO_DIRECCION];
            caracteristicas_clase_sensor["campos_incrementos"] = [];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = null;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_TIEMPO_REAL;
            break;
        }
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
            caracteristicas_clase_sensor["numero_valores"] = 1;
            caracteristicas_clase_sensor["campos_puntuales"] = [CAMPO_ABSOLUTO];
            caracteristicas_clase_sensor["campos_incrementos"] = [CAMPO_INCREMENTO];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = true;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = VALOR_SI;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_CUARTOHORARIA;
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
            caracteristicas_clase_sensor["numero_valores"] = 1;
            caracteristicas_clase_sensor["campos_puntuales"] = [CAMPO_ABSOLUTO];
            caracteristicas_clase_sensor["campos_incrementos"] = [CAMPO_INCREMENTO];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = true;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = VALOR_NO;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_HORARIA;
            break;
        }
        case CLASE_SENSOR_CORTES_TENSION: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
            caracteristicas_clase_sensor["numero_valores"] = 1;
            caracteristicas_clase_sensor["campos_puntuales"] = [];
            caracteristicas_clase_sensor["campos_incrementos"] = [CAMPO_CORTES];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = null;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_TIEMPO_REAL;
            break;
        }
        case CLASE_SENSOR_COMPRA_ENERGIA: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
            caracteristicas_clase_sensor["numero_valores"] = 1;
            caracteristicas_clase_sensor["campos_puntuales"] = [];
            caracteristicas_clase_sensor["campos_incrementos"] = [CAMPO_CONSUMO_ESTIMADO];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = VALOR_NO;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_HORARIA;
            break;
        }
        case CLASE_SENSOR_GAS: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
            caracteristicas_clase_sensor["numero_valores"] = 1;
            caracteristicas_clase_sensor["campos_puntuales"] = [CAMPO_ABSOLUTO];
            caracteristicas_clase_sensor["campos_incrementos"] = [CAMPO_INCREMENTO];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = true;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = VALOR_NO;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_HORARIA;
            break;
        }
        case CLASE_SENSOR_AGUA: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
            caracteristicas_clase_sensor["numero_valores"] = 1;
            caracteristicas_clase_sensor["campos_puntuales"] = [CAMPO_ABSOLUTO];
            caracteristicas_clase_sensor["campos_incrementos"] = [CAMPO_INCREMENTO];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = true;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = VALOR_NO;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_HORARIA;
            break;
        }
        case CLASE_SENSOR_GENERICA: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
            caracteristicas_clase_sensor["numero_valores"] = 1;
            caracteristicas_clase_sensor["campos_puntuales"] = [CAMPO_VALOR];
            caracteristicas_clase_sensor["campos_incrementos"] = [CAMPO_INCREMENTO];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = true;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = VALOR_NO;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_HORARIA;
            break;
        }
        // Ninguna
        case CLASE_NINGUNA: {
            caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_PUNTUAL;
            caracteristicas_clase_sensor["numero_valores"] = 0;
            caracteristicas_clase_sensor["campos_puntuales"] = [];
            caracteristicas_clase_sensor["campos_incrementos"] = [];
            caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
            caracteristicas_clase_sensor["granularidad_cuartohoraria_defecto"] = null;
            caracteristicas_clase_sensor["granularidad_eventos_defecto"] = GRANULARIDAD_NINGUNA;
            break;
        }
    }
    return (caracteristicas_clase_sensor);
}