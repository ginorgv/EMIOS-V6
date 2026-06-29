/*
 * Funciones de validaciones de parámetros de administración de nodos
 *
 */


//
// Calibraciones de valores
//


// Devuelve la cadena de calibración de valores de sensor
function dame_cadena_calibracion_valores_sensor(tipo_sensor, numero_valores_clase_sensor) {
    var cadena_calibracion_valores = "";
    switch (tipo_sensor) {
        case TIPO_SENSOR_REAL: {
            cadena_calibracion_valores = $('#calibracion_interfaz_sensor').val();
            break;
        }
        case TIPO_SENSOR_PROCESADO: {
            cadena_calibracion_valores = $('#calibracion_procesado_sensor').val();
            break;
        }
        case TIPO_SENSOR_EXTERNO: {
            cadena_calibracion_valores = $('#calibracion_externo_sensor').val();
            break;
        }
    }
    var resultado = comprueba_cadena_calibracion_valores(cadena_calibracion_valores, numero_valores_clase_sensor);
    if (resultado.parametros_correctos == false) {
        switch (tipo_sensor) {
            case TIPO_SENSOR_REAL: {
                cadena_calibracion_valores = $('#calibracion_interfaz_sensor').addClass('data-check-failed');
                break;
            }
            case TIPO_SENSOR_PROCESADO: {
                cadena_calibracion_valores = $('#calibracion_procesado_sensor').addClass('data-check-failed');
                break;
            }
            case TIPO_SENSOR_EXTERNO: {
                cadena_calibracion_valores = $('#calibracion_externo_sensor').addClass('data-check-failed');
                break;
            }
        }
    }
    return (resultado);
}


// Devuelve la cadena de calibración de valores de actuador
function dame_cadena_calibracion_valores_actuador(numero_valores_clase_actuador) {
    var cadena_calibracion_valores = $('#calibracion_interfaz_actuador').val();
    var resultado = comprueba_cadena_calibracion_valores(cadena_calibracion_valores, numero_valores_clase_actuador);
    if (resultado.parametros_correctos == false) {
        $("#calibracion_interfaz_actuador").addClass('data-check-failed');
    }
    return (resultado);
}


// Comprueba si la cadena de calibracion de valores es correcta
function comprueba_cadena_calibracion_valores(cadena_calibracion_valores, numero_valores) {
    var parametros_correctos = true;
    var descripcion_error = "";

    // Comprobación de longitud máxima
    if (comprueba_longitud_cadena(cadena_calibracion_valores, NUMERO_MAXIMO_CARACTERES_CALIBRACION) == false) {
        parametros_correctos = false;
    }

    // Se eliminan los espacios y si hay calibraciones se comprueban
    if (parametros_correctos == true) {
        cadena_calibracion_valores = replaceAll(cadena_calibracion_valores, " ", "");
        if (cadena_calibracion_valores != "") {
            var calibracion_valores = cadena_calibracion_valores.split(SEPARADOR_PARAMETROS_VALORES);

            // Se comprueba el número de valores
            if (calibracion_valores.length != numero_valores) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('El número de calibraciones de valores configurado no coincide con el número de valores') +
                    " (" + numero_valores + ")";
            }

            // Se comprueba cada una de las calibraciones de valores
            if (parametros_correctos == true) {
                for (var i = 0; i < numero_valores; i++) {
                    var calibracion_valor = calibracion_valores[i];
                    if (calibracion_valor == "") {
                        continue;
                    }

                    // Se comprueba cada una de las operaciones
                    var operaciones_calibracion_valor = calibracion_valor.split(SEPARADOR_OPERACIONES_CALIBRACION);
                    for (var j = 0; j < operaciones_calibracion_valor.length; j++) {
                        var operacion_calibracion_valor = operaciones_calibracion_valor[j];
                        var elementos_operacion_calibracion_valor = operacion_calibracion_valor.split(SEPARADOR_ELEMENTOS_OPERACIONES_CALIBRACION);
                        if (elementos_operacion_calibracion_valor.length != 2) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._('El formato de las operaciones de calibración es incorrecto');
                            break;
                        }

                        // Se comprueba el tipo de operación de calibración
                        var operaciones_calibracion_valores = [
                            OPERACION_CALIBRACION_MULTIPLICACION,
                            OPERACION_CALIBRACION_SUMA,
                            OPERACION_CALIBRACION_VALOR_MAXIMO,
                            OPERACION_CALIBRACION_VALOR_MINIMO];
                        var operacion_calibracion_valor = elementos_operacion_calibracion_valor[0];
                        var parametros_operacion_calibracion_valor = elementos_operacion_calibracion_valor[1].split(SEPARADOR_PARAMETROS_OPERACIONES_CALIBRACION);
                        if (operaciones_calibracion_valores.indexOf(operacion_calibracion_valor) == -1) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._('Operación de calibración desconocida') + " (" +
                                operaciones_calibracion_valores.join(", ") + ")";
                            break;
                        }

                        // Se comprueba el número de parámetros de la operación de calibración
                        var numero_parametros_operacion_calibracion = null;
                        switch (operacion_calibracion_valor) {
                            case OPERACION_CALIBRACION_MULTIPLICACION:
                            case OPERACION_CALIBRACION_SUMA:
                            case OPERACION_CALIBRACION_VALOR_MAXIMO:
                            case OPERACION_CALIBRACION_VALOR_MINIMO: {
                                numero_parametros_operacion_calibracion = 1;
                                break;
                            }
                        }
                        if (parametros_operacion_calibracion_valor.length != numero_parametros_operacion_calibracion) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._('El número de parámetros de las operaciones de calibración es incorrecto');
                            break;
                        }

                        // Se comprueban los parámetros de la operación de calibración
                        for (var k = 0; k < parametros_operacion_calibracion_valor.length; k++) {
                            var parametro_operacion_calibracion_valor = parametros_operacion_calibracion_valor[k];
                            if (PATRON_NUMERO_REAL.test(parametro_operacion_calibracion_valor) == false) {
                                parametros_correctos = false;
                                descripcion_error = TLNT.Idiomas._('Los parámetros de las operaciones de calibración deben ser números reales');
                                break;
                            }
                        }
                        if (parametros_correctos == false) {
                            break;
                        }
                    }
                    if (parametros_correctos == false) {
                        break;
                    }
                }
            }
        }
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_calibracion_valores: cadena_calibracion_valores,
        descripcion_error: descripcion_error
    };
    return (resultado);
}
