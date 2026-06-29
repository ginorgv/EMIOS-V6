/*
 * Funciones de validaciones de parámetros de administración de sensores
 *
 */


//
// Ubicaciones de interfaces de sensores
//


// Devuelve la cadena de ubicación de interfaz de la clase de interfaz especificada
function dame_cadena_ubicacion_interfaz_clase_interfaz_sensor(clase_interfaz_sensor) {
    var resultado = {};
    switch (clase_interfaz_sensor) {
        case CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE: {
            resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_asincrono_serie();
            break;
        }
        case CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM: {
            resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_http_abbodincem();
            break;
        }
        case CLASE_INTERFAZ_SENSOR_IEC102_SERIE: {
            resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_iec102_serie();
            break;
        }
        case CLASE_INTERFAZ_SENSOR_MODBUS_SERIE: {
            resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_modbus_serie();
            break;
        }
        case CLASE_INTERFAZ_SENSOR_MODBUS_IP: {
            resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_modbus_ip();
            break;
        }
        case CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS: {
            resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_valores_aleatorios();
            break;
        }
        case CLASE_INTERFAZ_SENSOR_VALORES_FIJOS: {
            resultado = dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_valores_fijos();
            break;
        }
    }
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un sensor real con la clase de interfaz 'Asíncrono serie'
function dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_asincrono_serie() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se crea la cadena de ubicación de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_ubicacion_interfaz = [
            $("#tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor").val(),
            $("#velocidad_clase_interfaz_asincrono_serie_sensor").val(),
            $("#numero_bits_parada_clase_interfaz_asincrono_serie_sensor").val(),
            $("#paridad_clase_interfaz_asincrono_serie_sensor").val(),
            $("#protocolo_clase_interfaz_asincrono_serie_sensor").val()].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un sensor real con la clase de interfaz 'Abbodincem'
function dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_http_abbodincem() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se crea la cadena de ubicación de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_ubicacion_interfaz = $("#direccion_ip_clase_interfaz_http_abbodincem_sensor").val();
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un sensor real con la clase de interfaz 'IEC 102 serie'
function dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_iec102_serie() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Dirección de enlace
    if (parametros_correctos == true) {
        var direccion_enlace = $("#direccion_enlace_clase_interfaz_iec102_serie_sensor").val();
        if ((parseInt(direccion_enlace) < VALOR_MINIMO_DIRECCION_ENLACE_CLASE_INTERFAZ_IEC102_SERIE_SENSOR) ||
            (parseInt(direccion_enlace) > VALOR_MAXIMO_DIRECCION_ENLACE_CLASE_INTERFAZ_IEC102_SERIE_SENSOR)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._('La dirección de enlace es incorrecta') +
                " (" + TLNT.Idiomas._('rango de valores') + ": " +
                VALOR_MINIMO_DIRECCION_ENLACE_CLASE_INTERFAZ_IEC102_SERIE_SENSOR + " - " + VALOR_MAXIMO_DIRECCION_ENLACE_CLASE_INTERFAZ_IEC102_SERIE_SENSOR + ")";
        }
    }

    // Punto de medida
    if (parametros_correctos == true) {
        var punto_medida = $("#punto_medida_clase_interfaz_iec102_serie_sensor").val();
        if ((parseInt(punto_medida) < VALOR_MINIMO_PUNTO_MEDIDA_CLASE_INTERFAZ_IEC102_SERIE_SENSOR) ||
            (parseInt(punto_medida) > VALOR_MAXIMO_PUNTO_MEDIDA_CLASE_INTERFAZ_IEC102_SERIE_SENSOR)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._('El punto de medida es incorrecto') +
                " (" + TLNT.Idiomas._('rango de valores') + ": " +
                VALOR_MINIMO_PUNTO_MEDIDA_CLASE_INTERFAZ_IEC102_SERIE_SENSOR + " - " + VALOR_MAXIMO_PUNTO_MEDIDA_CLASE_INTERFAZ_IEC102_SERIE_SENSOR + ")";
        }
    }

    // Clave
    if (parametros_correctos == true) {
        var clave =  $("#clave_clase_interfaz_iec102_serie_sensor").val();
        if ((parseInt(clave) < VALOR_MINIMO_CLAVE_CLASE_INTERFAZ_IEC102_SERIE_SENSOR) ||
            (parseInt(clave) > VALOR_MAXIMO_CLAVE_CLASE_INTERFAZ_IEC102_SERIE_SENSOR)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._('La clave es incorrecta') +
                " (" + TLNT.Idiomas._('rango de valores') + ": " +
                VALOR_MINIMO_CLAVE_CLASE_INTERFAZ_IEC102_SERIE_SENSOR + " - " + VALOR_MAXIMO_CLAVE_CLASE_INTERFAZ_IEC102_SERIE_SENSOR + ")";
        }
    }

    // Comandos de enlace
    var comandos_enlace = $("#comandos_enlace_clase_interfaz_iec102_serie_sensor").val();

    // Se crea la cadena de ubicación de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_ubicacion_interfaz = [
            $("#velocidad_clase_interfaz_iec102_serie_sensor").val(),
            $("#numero_bits_parada_clase_interfaz_iec102_serie_sensor").val(),
            $("#paridad_clase_interfaz_iec102_serie_sensor").val(),
            direccion_enlace,
            punto_medida,
            clave,
            comandos_enlace].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un sensor real con la clase de interfaz 'Modbus serie'
function dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_modbus_serie() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Velocidad
    if (parametros_correctos == true) {
        var velocidad =  $("#velocidad_clase_interfaz_modbus_serie_sensor").val();
        if ((parseInt(velocidad) < VALOR_MINIMO_VELOCIDAD_MODBUS_SERIE) ||
            (parseInt(velocidad) > VALOR_MAXIMO_VELOCIDAD_MODBUS_SERIE)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("La velocidad es incorrecta") +
                " (" + TLNT.Idiomas._("rango de valores") + ": " +
                VALOR_MINIMO_VELOCIDAD_MODBUS_SERIE + " - " + VALOR_MAXIMO_VELOCIDAD_MODBUS_SERIE + ")";
        }
    }

    // Se crea la cadena de ubicación de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_ubicacion_interfaz = [
            $("#encapsulado_clase_interfaz_modbus_serie_sensor").val(),
            velocidad,
            $("#numero_bits_parada_clase_interfaz_modbus_serie_sensor").val(),
            $("#paridad_clase_interfaz_modbus_serie_sensor").val()].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un sensor real con la clase de interfaz 'Modbus IP'
function dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_modbus_ip() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se crea la cadena de ubicación de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_ubicacion_interfaz = [
            $("#encapsulado_clase_interfaz_modbus_ip_sensor").val(),
            $("#protocolo_clase_interfaz_modbus_ip_sensor").val(),
            $("#direccion_ip_clase_interfaz_modbus_ip_sensor").val(),
            $("#puerto_clase_interfaz_modbus_ip_sensor").val()].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un sensor real con la clase de interfaz 'Valores aleatorios'
function dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_valores_aleatorios() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de ubicación de interfaz de un sensor real con la clase de interfaz 'Valores fijos'
function dame_cadena_ubicacion_interfaz_clase_interfaz_sensor_valores_fijos() {
    var parametros_correctos = true;
    var cadena_ubicacion_interfaz = "";
    var descripcion_error = "";

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_ubicacion_interfaz: cadena_ubicacion_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


//
// Opciones de interfaces de sensores
//


// Devuelve la cadena de opciones de interfaz de la clase de interfaz especificada
function dame_cadena_opciones_interfaz_clase_interfaz_sensor(clase_interfaz_sensor, numero_valores_clase_sensor) {
    var resultado = {};
    switch (clase_interfaz_sensor) {
        case CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE: {
            resultado = dame_cadena_opciones_interfaz_clase_interfaz_sensor_asincrono_serie(numero_valores_clase_sensor);
            break;
        }
        case CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM: {
            resultado = dame_cadena_opciones_interfaz_clase_interfaz_sensor_http_abbodincem(numero_valores_clase_sensor);
            break;
        }
        case CLASE_INTERFAZ_SENSOR_IEC102_SERIE: {
            resultado = dame_cadena_opciones_interfaz_clase_interfaz_sensor_iec102_serie(numero_valores_clase_sensor);
            break;
        }
        case CLASE_INTERFAZ_SENSOR_MODBUS_SERIE:
        case CLASE_INTERFAZ_SENSOR_MODBUS_IP: {
            resultado = dame_cadena_opciones_sensor_modbus("clase_interfaz_modbus_sensor", numero_valores_clase_sensor);
            resultado.cadena_opciones_interfaz = resultado.cadena_opciones;
            break;
        }
        case CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS: {
            resultado = dame_cadena_opciones_interfaz_clase_interfaz_sensor_valores_aleatorios(numero_valores_clase_sensor);
            break;
        }
        case CLASE_INTERFAZ_SENSOR_VALORES_FIJOS: {
            resultado = dame_cadena_opciones_interfaz_clase_interfaz_sensor_valores_fijos(numero_valores_clase_sensor);
            break;
        }
    }
    return (resultado);
}


// Devuelve la cadena de opciones de interfaz de un sensor real con la clase de interfaz 'Asíncrono serie'
function dame_cadena_opciones_interfaz_clase_interfaz_sensor_asincrono_serie(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_interfaz = "";
    var descripcion_error = "";

    // Caducidad
    if (parametros_correctos == true) {
        var caducidad = $("#caducidad_clase_interfaz_asincrono_serie_sensor").val();
        if (parseInt(caducidad) < 1) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("La caducidad debe ser mayor que 0");
        }
    }

    // Se crea la cadena de opciones de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_interfaz = [
            $("#numero_registro_clase_interfaz_asincrono_serie_sensor").val(),
            caducidad].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_interfaz: cadena_opciones_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de interfaz de un sensor real con la clase de interfaz 'Abbodincem'
function dame_cadena_opciones_interfaz_clase_interfaz_sensor_http_abbodincem(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_interfaz = "";
    var descripcion_error = "";

    // Se comprueba el número de valores
    if (numero_valores_clase_sensor != 1) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._("El número de valores del interfaz no coincide con el número de valores del sensor") +
            " (" + TLNT.Idiomas._("número de valores del sensor") + ": " + numero_valores_clase_sensor + ")";
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_interfaz: cadena_opciones_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de interfaz de un sensor real con la clase de interfaz 'IEC 102 serie'
function dame_cadena_opciones_interfaz_clase_interfaz_sensor_iec102_serie(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_interfaz = "";
    var descripcion_error = "";

    // Se comprueba el número de valores
    if (numero_valores_clase_sensor != 1) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._("El número de valores del interfaz no coincide con el número de valores del sensor") +
            " (" + TLNT.Idiomas._("número de valores del sensor") + ": " + numero_valores_clase_sensor + ")";
    }

    // Caducidad
    if (parametros_correctos == true) {
        var caducidad = $("#caducidad_clase_interfaz_iec102_serie_sensor").val();
        if (parseInt(caducidad) < 1) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("La caducidad debe ser mayor que 0");
        }
    }

    // Se crea la cadena de opciones de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_interfaz = [
            $("#id_valor_clase_interfaz_iec102_serie_sensor").val(),
            caducidad].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_interfaz: cadena_opciones_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de interfaz de un sensor real con la clase de interfaz 'Valores aleatorios'
function dame_cadena_opciones_interfaz_clase_interfaz_sensor_valores_aleatorios(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_interfaz = "";
    var descripcion_error = "";

    // Parámetros de valores
    var cadena_parametros_valores_aleatorios = replaceAll($("#valores_clase_interfaz_valores_aleatorios_sensor").val(), " ", "");
    var parametros_valores_aleatorios = cadena_parametros_valores_aleatorios.split(SEPARADOR_PARAMETROS_VALORES);

    // Se comprueba el número de valores
    if (parametros_valores_aleatorios.length != numero_valores_clase_sensor) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._("El número de valores configurado no coincide con el número de valores del sensor") +
            " (" + TLNT.Idiomas._("número de valores del sensor") + ": " + numero_valores_clase_sensor + ")";
    }

    // Valores aleatorios
    if (parametros_correctos == true) {
        for (var i = 0; i < parametros_valores_aleatorios.length; i++) {
            var parametros_valor_aleatorio = parametros_valores_aleatorios[i].split(',');
            if (parametros_valor_aleatorio.length != NUMERO_PARAMETROS_VALORES_ALEATORIOS) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Cada valor aleatorio debe tener cuatro parámetros") + " (" +
                    TLNT.Idiomas._("tipo de valor") + ", " +
                    TLNT.Idiomas._("valor inicial") + ", " +
                    TLNT.Idiomas._("valor mínimo") + ", " +
                    TLNT.Idiomas._("valor máximo") + ")";
                break;
            }

            // Parámetros del valor aleatorio
            var tipo_valor_aleatorio = parametros_valor_aleatorio[0];
            var valor_inicial = parametros_valor_aleatorio[1];
            var valor_minimo = parametros_valor_aleatorio[2];
            var valor_maximo = parametros_valor_aleatorio[3];

            // Tipo de valor aleatorio
            var tipos_valor_aleatorio = [
                TIPO_VALOR_ALEATORIO_PUNTUAL,
                TIPO_VALOR_ALEATORIO_INCREMENTAL];
            if (tipos_valor_aleatorio.indexOf(tipo_valor_aleatorio) == -1) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("El tipo de valor es incorrecto") +
                    " (" + TLNT.Idiomas._('valores disponibles') + ": " +
                    tipos_valor_aleatorio.join(", ") + ")";
                break;
            }

            // Valor inicial
            if (PATRON_NUMERO_ENTERO.test(valor_inicial) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("El valor inicial debe ser numérico");
                break;
            }

            // Valor mínimo
            if (PATRON_NUMERO_ENTERO.test(valor_minimo) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("El valor mínimo debe ser numérico");
                break;
            }

            // Valor máximo
            if (PATRON_NUMERO_ENTERO.test(valor_maximo) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("El valor máximo debe ser numérico");
                break;
            }
            if (parseInt(valor_maximo) < parseInt(valor_minimo)) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("El valor máximo debe ser igual o mayor que el valor mínimo");
                break;
            }
        }
    }

    // Se crea la cadena de opciones de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_interfaz = cadena_parametros_valores_aleatorios;
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_interfaz: cadena_opciones_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de interfaz de un sensor real con la clase de interfaz 'Valores fijos'
function dame_cadena_opciones_interfaz_clase_interfaz_sensor_valores_fijos(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_interfaz = "";
    var descripcion_error = "";

    // Parámetros de valores
    var cadena_parametros_valores_fijos = replaceAll($("#valores_clase_interfaz_valores_fijos_sensor").val(), " ", "");
    var parametros_valores_fijos = cadena_parametros_valores_fijos.split(SEPARADOR_PARAMETROS_VALORES);

    // Se comprueba el número de valores
    if (parametros_valores_fijos.length != numero_valores_clase_sensor) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._("El número de valores configurado no coincide con el número de valores del sensor") +
            " (" + TLNT.Idiomas._("número de valores del sensor") + ": " +numero_valores_clase_sensor + ")";
    }

    // Valores fijos
    // Nota: Hacer en el futuro que puedan ser valores reales
    var numero_valores = null;
    if (parametros_correctos == true) {
        for (var i = 0; i <parametros_valores_fijos.length; i++) {
            var valores = parametros_valores_fijos[i].split(SEPARADOR_PARAMETROS_SIMPLES);
            for (var j = 0; j < valores.length; j++) {
                // Valor
                var valor = valores[j];
                if (PATRON_NUMERO_ENTERO.test(valor) == false) {
                    parametros_correctos = false;
                    descripcion_error = TLNT.Idiomas._("Los valores fijos deben ser numéricos");
                    break;
                }
            }
            if (parametros_correctos == false) {
                break;
            }

            // Número de valores igual para cada valor del sensor
            if (numero_valores == null) {
                numero_valores = valores.length;
            }
            else {
                if (numero_valores != valores.length) {
                    parametros_correctos = false;
                    descripcion_error = TLNT.Idiomas._("El número de valores fijos debe ser el mismo para cada valor del sensor");
                    break;
                }
            }
        }
    }

    // Se crea la cadena de opciones de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_interfaz = cadena_parametros_valores_fijos;
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_interfaz: cadena_opciones_interfaz,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


//
// Sensores externos
//


// Devuelve la cadena de opciones generales de la clase de sensor externo que se le pasa como parámetro
function dame_cadena_opciones_generales_sensor_externo(clase_sensor_externo, numero_valores_clase_sensor, tipo_valores_sensor) {
    var resultado = {};
    switch (clase_sensor_externo) {
        case CLASE_SENSOR_EXTERNO_NINGUNA: {
            resultado = dame_cadena_opciones_generales_clase_sensor_externo_ninguna();
            break;
        }
        case CLASE_SENSOR_EXTERNO_FICHEROS_CSV: {
            resultado = dame_cadena_opciones_generales_clase_sensor_externo_ficheros_csv(tipo_valores_sensor);
            break;
        }
        case CLASE_SENSOR_EXTERNO_HTTP_EMIOS: {
            resultado = dame_cadena_opciones_generales_clase_sensor_externo_http_emios(tipo_valores_sensor);
            break;
        }
        case CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO: {
            resultado = dame_cadena_opciones_generales_clase_sensor_externo_http_xml_powerstudio(tipo_valores_sensor);
            break;
        }
        case CLASE_SENSOR_EXTERNO_MODBUS_IP: {
            resultado = dame_cadena_opciones_generales_clase_sensor_externo_modbus_ip(tipo_valores_sensor);
            break;
        }
        case CLASE_SENSOR_EXTERNO_WIBEEE: {
            resultado = dame_cadena_opciones_generales_clase_sensor_externo_wibeee(tipo_valores_sensor);
            break;
        }
        case CLASE_SENSOR_EXTERNO_API: {
            //resultado = dame_cadena_opciones_generales_clase_sensor_externo_api(tipo_valores_sensor);
            break;
        }
    }
    return (resultado);
}


// Devuelve la cadena de opciones generales de la clase de sensor externo 'Ninguna'
function dame_cadena_opciones_generales_clase_sensor_externo_ninguna() {
    var resultado = {
        parametros_correctos: true,
        cadena_opciones_valores: ""
    };
    return (resultado);
}


// Devuelve la cadena de opciones generales de la clase de sensor externo 'Ficheros CSV'
function dame_cadena_opciones_generales_clase_sensor_externo_ficheros_csv(tipo_valores_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_generales = "";
    var descripcion_error = "";

    // Prefijo de fichero
    // si es de tipo datadis el prefijo
    // se construye automaticamente
    if ($("#formato_fichero_valores_clase_externo_ficheros_csv_sensor").val() != "datadis"){
        if (parametros_correctos == true) {
            var prefijo_fichero = $("#prefijo_fichero_clase_externo_ficheros_csv_sensor").val();
            if (comprueba_longitud_cadena(prefijo_fichero, NUMERO_MAXIMO_CARACTERES_PREFIJOS_FICHEROS) == false) {
                $('#prefijo_fichero_clase_externo_ficheros_csv_sensor').addClass('data-check-failed');
                parametros_correctos = false;
                descripcion_error = "";
            }
            else {
                prefijo_fichero = replaceAll(prefijo_fichero, SEPARADOR_PARAMETROS_SIMPLES, SUSTITUTO_SEPARADOR);
            }
        }
    }

    // Formato de fichero
    if (parametros_correctos == true) {
        var formato_fichero = $("#formato_fichero_valores_clase_externo_ficheros_csv_sensor").val();
    }

    // Carácter separador
    if (parametros_correctos == true) {
        var caracter_separador = $("#caracter_separador_clase_externo_ficheros_csv_sensor").val();
        if (caracter_separador.length != 1) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El carácter separador debe ser un solo carácter");
        }
        else {
            caracter_separador = replaceAll(caracter_separador, SEPARADOR_PARAMETROS_SIMPLES, SUSTITUTO_SEPARADOR);
        }
    }

    // Punto decimal
    if (parametros_correctos == true) {
        var id_punto_decimal = $("#id_punto_decimal_clase_externo_ficheros_csv_sensor").val();
        var punto_decimal = null;
        switch (id_punto_decimal) {
            case ID_PUNTO_DECIMAL_PUNTO: {
                punto_decimal = ".";
                break;
            }
            case ID_PUNTO_DECIMAL_COMA: {
                punto_decimal = ",";
                break;
            }
        }

        // Sustituto separador
        if (punto_decimal == SEPARADOR_PARAMETROS_SIMPLES) {
            punto_decimal = SUSTITUTO_SEPARADOR;
        }

        // El caracter separador debe ser diferente al punto decimal
        if (caracter_separador == punto_decimal) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El carácter separador y el punto decimal deben ser diferentes");
        }
    }

    // Número de líneas de cabecera
    if (parametros_correctos == true) {
        var cabeceras = "";
        var numero_lineas_cabeceras = $("#numero_lineas_cabeceras_clase_externo_ficheros_csv_sensor").val();
        if (numero_lineas_cabeceras == 0) {
            cabeceras = "sin_cabeceras";
        }
        else {
            cabeceras = "con_cabeceras";
        }
    }

    // Columna de fecha
    if (parametros_correctos == true) {
        var columna_fecha = $("#columna_fecha_clase_externo_ficheros_csv_sensor").val();
        if (parseInt(columna_fecha) < 1) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("La columna de fecha debe ser mayor que 0");
        }
        else {
            columna_fecha -= 1;
        }
    }

    // Formato de fecha
    if (parametros_correctos == true) {
        var formato_fecha = $("#formato_fecha_clase_externo_ficheros_csv_sensor").val();
        formato_fecha = replaceAll(formato_fecha, SEPARADOR_PARAMETROS_SIMPLES, SUSTITUTO_SEPARADOR);
        var formato_fecha_python = convierte_formato_fecha_hora_a_formato_fecha_hora_python(formato_fecha);
    }

    // Hora:
    // - Columna de hora
    // - Formato de hora
    if (parametros_correctos == true) {
        var hora_columna_independiente = $("#hora_columna_independiente_clase_externo_ficheros_csv_sensor").val();
        var columna_hora = "";
        var formato_hora = "";
        var formato_hora_python = "";
        if (hora_columna_independiente == VALOR_SI) {
            columna_hora = $("#columna_hora_clase_externo_ficheros_csv_sensor").val();
            if (parseInt(columna_hora) < 1) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("La columna de hora debe ser mayor que 0");
            }
            else {
                columna_hora -= 1;
            }
            if (parametros_correctos == true) {
                formato_hora = $("#formato_hora_clase_externo_ficheros_csv_sensor").val();
                formato_hora = replaceAll(formato_hora, SEPARADOR_PARAMETROS_SIMPLES, SUSTITUTO_SEPARADOR);
                formato_hora_python = convierte_formato_fecha_hora_a_formato_fecha_hora_python(formato_hora);
            }
        }
    }

    // Zona horaria y horario de verano
    if (parametros_correctos == true) {
        var zona_horaria = $("#zona_horaria_clase_externo_ficheros_csv_sensor").val();
        var columna_horario_verano = $("#columna_horario_verano_clase_externo_ficheros_csv_sensor").val();
        if (columna_horario_verano != "") {
            if (parseInt(columna_horario_verano) < 1) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("La columna de horario de verano debe ser mayor que 0");
            }
            else {
                columna_horario_verano -= 1;
            }
        }
    }

    // Número de valores
    if (parametros_correctos == true) {
        var numero_valores = $("#numero_valores_clase_externo_ficheros_csv_sensor").val();
        if (parseInt(numero_valores) < 1) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El número de valores debe ser mayor que 0");
        }
    }

    // Tipo de valores
    if (parametros_correctos == true) {
        var tipo_valores = $("#tipo_valores_sensor_clase_externo_ficheros_csv_sensor").val();
        if (tipo_valores == TIPO_NINGUNO) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("No hay tipo de valores de sensor externo seleccionado");
        }
        else {
            if (tipo_valores != tipo_valores_sensor) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("El tipo de valores del sensor externo debe ser igual que el tipo de valores del sensor");
            }
        }
    }

    // Tipo de valores y segundos de incrementos
    if (parametros_correctos == true) {
        if (tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES) {
            var tipo_incrementos = $("#tipo_incrementos_clase_externo_ficheros_csv_sensor").val();
            var tipo_horas_incrementos = $("#tipo_horas_incrementos_clase_externo_ficheros_csv_sensor").val();
            var horas_incrementos = $("#horas_incrementos_clase_externo_ficheros_csv_sensor").val();
            var segundos_incrementos = Math.round(horas_incrementos * 3600);
            if (parametros_correctos == true) {
                switch (tipo_horas_incrementos) {
                    case TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO: {
                        if (segundos_incrementos <= 0) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._("El número de horas de incrementos debe ser mayor que 0");
                        }
                        break;
                    }
                }
            }
            if (parametros_correctos == true) {
                switch (tipo_incrementos) {
                    case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL: {
                        if (segundos_incrementos == 0) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._("El tipo de incrementos debe ser fecha final si las horas de incrementos son variables");
                        }
                        break;
                    }
                }
            }
        }
    }
    if ($("#formato_fichero_valores_clase_externo_ficheros_csv_sensor").val() == "datadis"){

        var cups = $("#cups_datadis_clase_externo_api").val();
        var prefijo_fichero = cups + "_";
        var distributorCode = $("#distributor_code_datadis_clase_externo_api").val();
        var measurementType = $("#measurement_type_clase_externo_datadis_sensor").val();
        var pointType = $("#point_type_clase_externo_api").val();
        var authorizedNif = $("#authorized_nif_clase_externo_api").val();
    }
    // Anyadido C.V. 10-10-2023 para evitar error "RefenceError: cadena_parametros_datadis not defined" si hay un error en la definición del sensor
    cadena_parametros_datadis = null;
    // Se crea la cadena de opciones generales con los parámetros de configuración
    if (parametros_correctos == true) {
        var parametros_opciones_generales = [
            prefijo_fichero,
            formato_fichero,
            caracter_separador,
            punto_decimal,
            cabeceras,
            numero_lineas_cabeceras,
            columna_fecha,
            formato_fecha_python,
            columna_hora,
            formato_hora_python,
            zona_horaria,
            columna_horario_verano,
            numero_valores,
            tipo_valores];
        if (tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES) {
            parametros_opciones_generales.push(segundos_incrementos);
            parametros_opciones_generales.push(tipo_incrementos);
        }
        if ($("#formato_fichero_valores_clase_externo_ficheros_csv_sensor").val() == "datadis"){

            var parametros_datadis = [
                cups,
                distributorCode,
                measurementType,
                pointType,
                authorizedNif
            ];
            cadena_parametros_datadis = parametros_datadis.join(SEPARADOR_PARAMETROS_SIMPLES);
        } else {
            cadena_parametros_datadis = null;
        }
        cadena_opciones_generales = parametros_opciones_generales.join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_generales: cadena_opciones_generales,
        cadena_parametros_datadis: cadena_parametros_datadis,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones generales de la clase de sensor externo 'HTTP Emios'
function dame_cadena_opciones_generales_clase_sensor_externo_http_emios(tipo_valores_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_generales = "";
    var descripcion_error = "";

    // Tipo de valores
    if (parametros_correctos == true) {
        if (tipo_valores_sensor != TIPO_VALORES_SENSOR_PUNTUALES) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El tipo de valores del sensor debe ser puntual");
        }
    }

    // Tiempo de muestreo
    if (parametros_correctos == true) {
        var tiempo_muestreo = $("#tiempo_muestreo_clase_externo_http_emios_sensor").val();
        if ((parseInt(tiempo_muestreo) < VALOR_MINIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_HTTP_EMIOS) ||
            (parseInt(tiempo_muestreo) > VALOR_MAXIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_HTTP_EMIOS)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El tiempo de muestreo es incorrecto") +
                " (" + TLNT.Idiomas._("rango de valores") + ": " +
                VALOR_MINIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_HTTP_EMIOS + " - " + VALOR_MAXIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_HTTP_EMIOS + ")";
        }
    }

    // Se crea la cadena de opciones generales con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_generales = [
            tiempo_muestreo,
            TIPO_VALORES_SENSOR_PUNTUALES].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_generales: cadena_opciones_generales,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones generales de la clase de sensor externo 'HTTP XML PowerStudio'
function dame_cadena_opciones_generales_clase_sensor_externo_http_xml_powerstudio(tipo_valores_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_generales = "";
    var descripcion_error = "";

    // Tiempo de muestreo
    if (parametros_correctos == true) {
        var tiempo_muestreo = $("#tiempo_muestreo_clase_externo_http_xml_powerstudio_sensor").val();
        if ((parseInt(tiempo_muestreo) < VALOR_MINIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO) ||
            (parseInt(tiempo_muestreo) > VALOR_MAXIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El tiempo de muestreo es incorrecto") +
                " (" + TLNT.Idiomas._("rango de valores") + ": " +
                VALOR_MINIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO + " - " + VALOR_MAXIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO + ")";
        }
    }

    // Tipo de valores
    if (parametros_correctos == true) {
        var tipo_valores = $("#tipo_valores_sensor_clase_externo_http_xml_powerstudio_sensor").val();
        if (tipo_valores == TIPO_NINGUNO) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("No hay tipo de valores de sensor externo seleccionado");
        }
        else {
            if (tipo_valores != tipo_valores_sensor) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("El tipo de valores del sensor externo debe ser igual que el tipo de valores del sensor");
            }
        }
    }

    // Tipo y segundos de incrementos
    if (parametros_correctos == true) {
        if (tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES) {
            var tipo_incrementos = $("#tipo_incrementos_clase_externo_http_xml_powerstudio_sensor").val();
            var tipo_horas_incrementos = $("#tipo_horas_incrementos_clase_externo_http_xml_powerstudio_sensor").val();
            var horas_incrementos = $("#horas_incrementos_clase_externo_http_xml_powerstudio_sensor").val();
            var segundos_incrementos = Math.round(horas_incrementos * 3600);
            if (parametros_correctos == true) {
                switch (tipo_horas_incrementos) {
                    case TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO: {
                        if (segundos_incrementos <= 0) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._("El número de horas de incrementos debe ser mayor que 0");
                        }
                        break;
                    }
                }
            }
            if (parametros_correctos == true) {
                switch (tipo_incrementos) {
                    case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL: {
                        if (segundos_incrementos == 0) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._("El tipo de incrementos debe ser fecha final si las horas de incrementos son variables");
                        }
                        break;
                    }
                }
            }
        }
    }

    // Se crea la cadena de opciones generales con los parámetros de configuración
    if (parametros_correctos == true) {
        var parametros_opciones_generales = [
            tiempo_muestreo,
            tipo_valores];
        if (tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES) {
            parametros_opciones_generales.push(segundos_incrementos);
            parametros_opciones_generales.push(tipo_incrementos);
        }
        cadena_opciones_generales = parametros_opciones_generales.join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_generales: cadena_opciones_generales,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones generales de la clase de sensor externo 'Modbus IP'
function dame_cadena_opciones_generales_clase_sensor_externo_modbus_ip(tipo_valores_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_generales = "";
    var descripcion_error = "";

    // Tiempo de muestreo
    if (parametros_correctos == true) {
        var tiempo_muestreo = $("#tiempo_muestreo_clase_externo_modbus_ip_sensor").val();
        if ((parseInt(tiempo_muestreo) < VALOR_MINIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_MODBUS_IP) ||
            (parseInt(tiempo_muestreo) > VALOR_MAXIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_MODBUS_IP)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El tiempo de muestreo es incorrecto") +
                " (" + TLNT.Idiomas._("rango de valores") + ": " +
                VALOR_MINIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_MODBUS_IP + " - " + VALOR_MAXIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_MODBUS_IP + ")";
        }
    }

    // Tipo de valores
    if (parametros_correctos == true) {
        var tipo_valores = $("#tipo_valores_sensor_clase_externo_modbus_ip_sensor").val();
        if (tipo_valores != tipo_valores_sensor) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El tipo de valores del sensor externo debe ser igual que el tipo de valores del sensor");
        }
    }

    // Tipo y segundos de incrementos
    if (parametros_correctos == true) {
        if (tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES) {
            var tipo_incrementos = $("#tipo_incrementos_clase_externo_modbus_ip_sensor").val();
            var tipo_horas_incrementos = $("#tipo_horas_incrementos_clase_externo_modbus_ip_sensor").val();
            var horas_incrementos = $("#horas_incrementos_clase_externo_modbus_ip_sensor").val();
            var segundos_incrementos = Math.round(horas_incrementos * 3600);
            if (parametros_correctos == true) {
                switch (tipo_horas_incrementos) {
                    case TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO: {
                        if (segundos_incrementos <= 0) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._("El número de horas de incrementos debe ser mayor que 0");
                        }
                        break;
                    }
                }
            }
            if (parametros_correctos == true) {
                switch (tipo_incrementos) {
                    case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL: {
                        if (segundos_incrementos == 0) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._("El tipo de incrementos debe ser fecha final si las horas de incrementos son variables");
                        }
                        break;
                    }
                }
            }
        }
    }

    // Se crea la cadena de opciones generales con los parámetros de configuración
    if (parametros_correctos == true) {
        var parametros_opciones_generales = [
            $("#id_encapsulado_clase_externo_modbus_ip_sensor").val(),
            $("#protocolo_clase_externo_modbus_ip_sensor").val(),
            $("#direccion_ip_clase_externo_modbus_ip_sensor").val(),
            $("#puerto_clase_externo_modbus_ip_sensor").val(),
            tiempo_muestreo,
            tipo_valores];
        if (tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES) {
            parametros_opciones_generales.push(segundos_incrementos);
            parametros_opciones_generales.push(tipo_incrementos);
        }
        cadena_opciones_generales = parametros_opciones_generales.join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_generales: cadena_opciones_generales,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones generales de la clase de sensor externo 'Wibeee'
function dame_cadena_opciones_generales_clase_sensor_externo_wibeee(tipo_valores_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_generales = "";
    var descripcion_error = "";

    // Tipo de valores
    if (parametros_correctos == true) {
        if (tipo_valores_sensor != TIPO_VALORES_SENSOR_PUNTUALES) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El tipo de valores del sensor debe ser puntual");
        }
    }

    // Dirección MAC
    if (parametros_correctos == true) {
        var direccion_mac = $("#direccion_mac_clase_externo_wibeee_sensor").val();
    }

    // Se crea la cadena de opciones generales con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_generales = direccion_mac;
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_generales: cadena_opciones_generales,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones generales de la clase de sensor externo 'API'
//function dame_cadena_opciones_generales_clase_sensor_externo_api(tipo_valores_sensor) {
//    var parametros_correctos = true;
//    var cadena_opciones_generales = "";
//    var descripcion_error = "";
//
//    // Comprueba que el campo introducido en 'Tiempo de muestreo' está comprendido entre los valores establecidos
//    if (parametros_correctos == true) {
//        var tiempo_muestreo = $("#tiempo_muestreo_clase_externo_api_sensor").val();
//        if ((parseInt(tiempo_muestreo) < VALOR_MINIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_API) ||
//            (parseInt(tiempo_muestreo) > VALOR_MAXIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_API)) {
//            parametros_correctos = false;
//            descripcion_error = TLNT.Idiomas._("El tiempo de muestreo es incorrecto") +
//                " (" + TLNT.Idiomas._("rango de valores") + ": " +
//                VALOR_MINIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_API + " - " + VALOR_MAXIMO_TIEMPO_MUESTREO_SENSOR_EXTERNO_API + ")";
//        }
//    }
//
//    // Tipo de valores
//    if (parametros_correctos == true) {
//        var tipo_valores = $("#tipo_valores_sensor_clase_externo_api_sensor").val();
//        if (tipo_valores == TIPO_NINGUNO) {
//            parametros_correctos = false;
//            descripcion_error = TLNT.Idiomas._("No hay tipo de valores de sensor externo seleccionado");
//        }
//        else {
//            if (tipo_valores != tipo_valores_sensor) {
//                parametros_correctos = false;
//                descripcion_error = TLNT.Idiomas._("El tipo de valores del sensor externo debe ser igual que el tipo de valores del sensor");
//            }
//        }
//    }
//
//    // Tipo y segundos de incrementos
//    if (parametros_correctos == true) {
//        if (tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES) {
//            var tipo_incrementos = $("#tipo_incrementos_clase_externo_api_sensor").val();
//            var tipo_horas_incrementos = $("#tipo_horas_incrementos_clase_externo_api_sensor").val();
//            var horas_incrementos = $("#horas_incrementos_clase_externo_api_sensor").val();
//            var segundos_incrementos = Math.round(horas_incrementos * 3600);
//            if (parametros_correctos == true) {
//                switch (tipo_horas_incrementos) {
//                    case TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO: {
//                        if (segundos_incrementos <= 0) {
//                            parametros_correctos = false;
//                            descripcion_error = TLNT.Idiomas._("El número de horas de incrementos debe ser mayor que 0");
//                        }
//                        break;
//                    }
//                }
//            }
//            if (parametros_correctos == true) {
//                switch (tipo_incrementos) {
//                    case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL: {
//                        if (segundos_incrementos == 0) {
//                            parametros_correctos = false;
//                            descripcion_error = TLNT.Idiomas._("El tipo de incrementos debe ser fecha final si las horas de incrementos son variables");
//                        }
//                        break;
//                    }
//                }
//            }
//        }
//    }
//
//    // Se crea la cadena de opciones generales con los parámetros de configuración
//    if (parametros_correctos == true) {
//        var parametros_opciones_generales = [
//            tiempo_muestreo,
//            tipo_valores];
//        if (tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES) {
//            parametros_opciones_generales.push(segundos_incrementos);
//            parametros_opciones_generales.push(tipo_incrementos);
//        }
//        cadena_opciones_generales = parametros_opciones_generales.join(SEPARADOR_PARAMETROS_SIMPLES);
//    }
//
//    // Se devuelve el resultado
//    var resultado = {
//        parametros_correctos: parametros_correctos,
//        cadena_opciones_generales: cadena_opciones_generales,
//        descripcion_error: descripcion_error
//    };
//    return (resultado);
//}





// Devuelve la cadena de opciones de valores de la clase de sensor externo que se le pasa como parámetro
function dame_cadena_opciones_valores_sensor_externo(clase_sensor_externo, numero_valores_clase_sensor) {
    var resultado = {};
    switch (clase_sensor_externo) {
        case CLASE_SENSOR_EXTERNO_NINGUNA: {
            resultado = dame_cadena_opciones_valores_clase_sensor_externo_ninguna(numero_valores_clase_sensor);
            break;
        }
        case CLASE_SENSOR_EXTERNO_FICHEROS_CSV: {
            resultado = dame_cadena_opciones_valores_clase_sensor_externo_ficheros_csv(numero_valores_clase_sensor);
            break;
        }
        case CLASE_SENSOR_EXTERNO_HTTP_EMIOS: {
            resultado = dame_cadena_opciones_valores_clase_sensor_externo_http_emios(numero_valores_clase_sensor);
            break;
        }
        case CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO: {
            resultado = dame_cadena_opciones_valores_clase_sensor_externo_http_xml_powerstudio(numero_valores_clase_sensor);
            break;
        }
        case CLASE_SENSOR_EXTERNO_MODBUS_IP: {
            resultado = dame_cadena_opciones_sensor_modbus("clase_externo_modbus_ip_sensor", numero_valores_clase_sensor);
            resultado.cadena_opciones_valores = resultado.cadena_opciones;
            break;
        }
        case CLASE_SENSOR_EXTERNO_WIBEEE: {
            resultado = dame_cadena_opciones_valores_clase_sensor_externo_wibeee(numero_valores_clase_sensor);
            break;
        }
        case CLASE_SENSOR_EXTERNO_API: {
            resultado = dame_cadena_opciones_valores_clase_sensor_externo_api(numero_valores_clase_sensor);
            break;
        }
    }
    return (resultado);
}


// Devuelve la cadena de opciones de valores de la clase de sensor externo 'Ninguna'
function dame_cadena_opciones_valores_clase_sensor_externo_ninguna(numero_valores_clase_sensor) {
    var resultado = {
        parametros_correctos: true,
        cadena_opciones_valores: ""
    };
    return (resultado);
}


// Devuelve la cadena de opciones de valores de la clase de sensor externo 'Ficheros CSV'
function dame_cadena_opciones_valores_clase_sensor_externo_ficheros_csv(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_valores = "";
    var descripcion_error = "";

    // Columnas de valores
    if (parametros_correctos == true) {
        var cadena_opciones_valores = $("#columnas_valores_clase_externo_ficheros_csv_sensor").val();
        var opciones_valores = cadena_opciones_valores.split(SEPARADOR_PARAMETROS_VALORES);
        var numero_valores = $("#numero_valores_clase_externo_ficheros_csv_sensor").val();
        if (opciones_valores.length != numero_valores) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El número de columnas de valores debe ser igual que el número de valores");
        }

        if (parametros_correctos == true) {
            cadena_opciones_valores = "";
            for (var i = 0; i < opciones_valores.length; i++) {
                if (i > 0) {
                    cadena_opciones_valores += SEPARADOR_PARAMETROS_VALORES;
                }
                var opciones_valor = opciones_valores[i].split(SEPARADOR_PARAMETROS_SIMPLES);
                var cadena_columna_valor = opciones_valor[0].trim();
                if (PATRON_NUMERO_NATURAL.test(cadena_columna_valor) == false) {
                    parametros_correctos = false;
                    descripcion_error = TLNT.Idiomas._("Las columnas de valores deben ser valores numéricos");
                    break;
                }
                var columna_valor = parseInt(cadena_columna_valor);
                if (columna_valor <= 0) {
                    parametros_correctos = false;
                    descripcion_error = TLNT.Idiomas._("Las columnas de valores deben ser mayores que 0");
                    break;
                }
                var cadena_opciones_valor = (columna_valor - 1);
                if (opciones_valor.length > 1) {
                    var cadena_bit_inicial_valor = opciones_valor[1].trim();
                    if (PATRON_NUMERO_NATURAL.test(cadena_bit_inicial_valor) == false) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._("Los bits iniciales de valores deben ser valores numéricos");
                        break;
                    }
                    var bit_inicial_valor = parseInt(cadena_bit_inicial_valor);
                    if (bit_inicial_valor <= 0) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._("Los bits iniciales de valores deben ser mayores que 0");
                        break;
                    }
                    cadena_opciones_valor += SEPARADOR_PARAMETROS_SIMPLES + (bit_inicial_valor - 1);
                }
                if (opciones_valor.length == 3) {
                    var cadena_numero_bits_valor = opciones_valor[2].trim();
                    if (PATRON_NUMERO_NATURAL.test(cadena_numero_bits_valor) == false) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._("Los números de bits de valores deben ser valores numéricos");
                        break;
                    }
                    var numero_bits_valor = parseInt(cadena_numero_bits_valor);
                    if (numero_bits_valor <= 0) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._("Los números de bits de valores deben ser mayores que 0");
                        break;
                    }
                    if (numero_bits_valor > bit_inicial_valor) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._("Los números de bits de valores no pueden ser mayores que los bits iniciales de valores");
                        break;
                    }

                    cadena_opciones_valor += SEPARADOR_PARAMETROS_SIMPLES + numero_bits_valor;
                }
                cadena_opciones_valores += cadena_opciones_valor;
            }
        }

        // Se comprueba el número de valores
        if (numero_valores != numero_valores_clase_sensor) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El número de valores configurado no coincide con el número de valores del sensor") +
                " (" + TLNT.Idiomas._("número de valores del sensor") + ": " + numero_valores_clase_sensor + ")";
        }
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_valores: cadena_opciones_valores,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de valores de la clase de sensor externo 'HTTP Emios'
function dame_cadena_opciones_valores_clase_sensor_externo_http_emios(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_valores = "";
    var descripcion_error = "";

    // Se crea la cadena de opciones de valores con los parámetros de configuración
    var tipo_clase_externo_http_emios_sensor = $("#tipo_clase_externo_http_emios_sensor").val();
    switch (tipo_clase_externo_http_emios_sensor) {
        case TIPO_SENSOR_SOFTWARE_METEOROLOGICO: {
            // Tipo de información, clave y localización
            var proveedor = $("#proveedor_clase_externo_http_emios_tipo_meteorologico_sensor").val();
            var tipo_informacion = $("#tipo_informacion_clase_externo_http_emios_tipo_meteorologico_sensor").val();
            var modo_localizacion = $("#modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").val();

            var parametros_opciones_valores = [
                "tipo_sensor=" + tipo_clase_externo_http_emios_sensor + "",
                "proveedor=" + proveedor,
                "tipo_informacion=" + tipo_informacion];
            switch (modo_localizacion) {
                case MODO_LOCALIZACION_COORDENADAS_GEOGRAFICAS: {
                    var latitud = $("#latitud_clase_externo_http_emios_tipo_meteorologico_sensor").val();
                    var longitud = $("#longitud_clase_externo_http_emios_tipo_meteorologico_sensor").val();
                    parametros_opciones_valores.push("latitud=" + latitud);
                    parametros_opciones_valores.push("longitud=" + longitud);
                    break;
                }
                case MODO_LOCALIZACION_LOCALIDAD: {
                    var localidad = $("#localidad_clase_externo_http_emios_tipo_meteorologico_sensor").val();
                    var pais = $("#pais_clase_externo_http_emios_tipo_meteorologico_sensor").val();
                    parametros_opciones_valores.push("localidad=" + localidad);
                    parametros_opciones_valores.push("codigo_pais=" + pais);
                    break;
                }
                case MODO_LOCALIZACION_IDEMA: {
                    var idema = $("#idema_clase_externo_http_emios_tipo_meteorologico_sensor").val();
                    parametros_opciones_valores.push("idema=" + idema);
                    break;
                }
            }
            cadena_opciones_valores = parametros_opciones_valores.join(SEPARADOR_PARAMETROS_SIMPLES);
            break;
        }
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_valores: cadena_opciones_valores,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de valores de la clase de sensor externo 'HTTP XML PowerStudio'
function dame_cadena_opciones_valores_clase_sensor_externo_http_xml_powerstudio(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_valores = "";
    var descripcion_error = "";

    // Se comprueba el número de valores
    if (numero_valores_clase_sensor != 1) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._("El número de valores del tipo de sensor externo no coincide con el número de valores del sensor");
            " (" + TLNT.Idiomas._("número de valores del sensor") + ": " + numero_valores_clase_sensor + ")";
    }

    // Dirección IP
    var direccion_ip = $("#direccion_ip_clase_externo_http_xml_powerstudio_sensor").val();

    // Puerto
    var puerto = $("#puerto_clase_externo_http_xml_powerstudio_sensor").val();

    // Nombre de dispositivo
    var nombre_dispositivo = $("#nombre_dispositivo_clase_externo_http_xml_powerstudio_sensor").val();

    // Nombre de variable
    var nombre_variable = $("#nombre_variable_clase_externo_http_xml_powerstudio_sensor").val();

    // Se crea la cadena de opciones de valores con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_valores = [
            direccion_ip,
            puerto,
            nombre_dispositivo,
            nombre_variable].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_valores: cadena_opciones_valores,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de valores de sensor ModBus
function dame_cadena_opciones_sensor_modbus(id_controles, numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones = "";
    var descripcion_error = "";

    // Parámetros de valores
    var cadena_tipos_registros = replaceAll($("#tipos_registros_" + id_controles).val(), " ", "");
    var cadena_direcciones_dispositivos = replaceAll($("#direcciones_dispositivos_" + id_controles).val(), " ", "");
    var cadena_direcciones_registros = replaceAll($("#direcciones_registros_" + id_controles).val(), " ", "");
    var cadena_numeros_elementos = replaceAll($("#numeros_elementos_" + id_controles).val(), " ", "");
    var cadena_reversos_bytes = replaceAll($("#reversos_bytes_" + id_controles).val(), " ", "");
    var cadena_reversos_registros = replaceAll($("#reversos_registros_" + id_controles).val(), " ", "");
    var cadena_tipos_datos = replaceAll($("#tipos_datos_" + id_controles).val(), " ", "");

    var tipos_registros = cadena_tipos_registros.split(SEPARADOR_PARAMETROS_VALORES);
    var direcciones_dispositivos = cadena_direcciones_dispositivos.split(SEPARADOR_PARAMETROS_VALORES);
    var direcciones_registros = cadena_direcciones_registros.split(SEPARADOR_PARAMETROS_VALORES);
    var numeros_elementos = cadena_numeros_elementos.split(SEPARADOR_PARAMETROS_VALORES);
    var reversos_bytes = cadena_reversos_bytes.split(SEPARADOR_PARAMETROS_VALORES);
    var reversos_registros = cadena_reversos_registros.split(SEPARADOR_PARAMETROS_VALORES);
    var tipos_datos = cadena_tipos_datos.split(SEPARADOR_PARAMETROS_VALORES);

    // Se comprueba el número de valores
    if ((tipos_registros.length != numero_valores_clase_sensor) ||
        (direcciones_dispositivos.length != numero_valores_clase_sensor) ||
        (direcciones_registros.length != numero_valores_clase_sensor) ||
        (numeros_elementos.length != numero_valores_clase_sensor) ||
        (reversos_bytes.length != numero_valores_clase_sensor) ||
        (reversos_registros.length != numero_valores_clase_sensor) ||
        (tipos_datos.length != numero_valores_clase_sensor)) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._("El número de valores configurado no coincide con el número de valores del sensor") +
            " (" + TLNT.Idiomas._("número de valores del sensor") + ": " + numero_valores_clase_sensor + ")";
    }

    // Se comprueban los parámetros de cada uno de los valores
    var cadenas_parametros_valores = [];
    for (var i = 0; i <numero_valores_clase_sensor; i++) {
        if (parametros_correctos == true) {
            var tipos_registro_modbus = [
                TIPO_REGISTRO_MODBUS_COILS,
                TIPO_REGISTRO_MODBUS_HOLDING_REGISTERS,
                TIPO_REGISTRO_MODBUS_INPUT_REGISTERS,
                TIPO_REGISTRO_MODBUS_DISCRETE_INPUTS,
                TIPO_REGISTRO_MODBUS_AUTO_BYTES,
                TIPO_REGISTRO_MODBUS_AUTO_BITS];
            var tipo_registro = tipos_registros[i];
            if (tipos_registro_modbus.indexOf(tipo_registro) == -1) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Los tipos de registros son incorrectos") +
                    " (" + TLNT.Idiomas._("valores disponibles") + ": " +
                    tipos_registro_modbus.join(", ") + ")";
                break;
            }
        }

        // Dirección del dispositivo
        if (parametros_correctos == true) {
            var direccion_dispositivo = direcciones_dispositivos[i];
            if (PATRON_NUMERO_NATURAL.test(direccion_dispositivo) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Las direcciones de los dispositivos deben ser valores numéricos");
                break;
            }
            if ((parseInt(direccion_dispositivo) < VALOR_MINIMO_DIRECCION_DISPOSITIVO_MODBUS) ||
                (parseInt(direccion_dispositivo) > VALOR_MAXIMO_DIRECCION_DISPOSITIVO_MODBUS)) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Las direcciones de los dispositivos son incorrectas") +
                    " (" + TLNT.Idiomas._("rango de valores") + ": " +
                    VALOR_MINIMO_DIRECCION_DISPOSITIVO_MODBUS + " - " + VALOR_MAXIMO_DIRECCION_DISPOSITIVO_MODBUS + ")";
                break;
            }
        }

        // Dirección del registro (inicial)
        if (parametros_correctos == true) {
            var direccion_registro = direcciones_registros[i];
            if (PATRON_NUMERO_NATURAL.test(direccion_registro) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Las direcciones de los registros deben ser valores numéricos");
                break;
            }
            if ((parseInt(direccion_registro) < VALOR_MINIMO_DIRECCION_REGISTRO_MODBUS) ||
                (parseInt(direccion_registro) > VALOR_MAXIMO_DIRECCION_REGISTRO_MODBUS)) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Las direcciones de los registros son incorrectas") +
                    " (" + TLNT.Idiomas._("rango de valores") + ": " +
                    VALOR_MINIMO_DIRECCION_REGISTRO_MODBUS + " - " + VALOR_MAXIMO_DIRECCION_REGISTRO_MODBUS + ")";
                break;
            }
        }

        // Número de elementos
        if (parametros_correctos == true) {
            var numero_elementos = numeros_elementos[i];
            if (PATRON_NUMERO_NATURAL.test(numero_elementos) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Los números de elementos deben ser valores numéricos");
                break;
            }
            if ((parseInt(numero_elementos) < VALOR_MINIMO_NUMERO_ELEMENTOS_MODBUS) ||
                (parseInt(numero_elementos) > VALOR_MAXIMO_NUMERO_ELEMENTOS_MODBUS)) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Los números de elementos son incorrectos") +
                    " (" + TLNT.Idiomas._("rango de valores") + ": " +
                    VALOR_MINIMO_NUMERO_ELEMENTOS_MODBUS + " - " + VALOR_MAXIMO_NUMERO_ELEMENTOS_MODBUS + ")";
                break;
            }
        }

        // Reverso de bytes
        if (parametros_correctos == true) {
            var reverso_bytes = reversos_bytes[i];
            if (PATRON_NUMEROS_0_1.test(reverso_bytes) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Los reversos de bytes deben ser 0 (desactivado) o 1 (activado)");
                break;
            }
        }

        // Reverso de registros
        if (parametros_correctos == true) {
            var reverso_registros = reversos_registros[i];
            if (PATRON_NUMEROS_0_1.test(reverso_registros) == false) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Los reversos de registros deben ser 0 (desactivado) o 1 (activado)");
                break;
            }
        }

        // Tipo de dato
        if (parametros_correctos == true) {
            var tipos_dato_modbus = [
                TIPO_DATO_MODBUS_8BIT_INT,
                TIPO_DATO_MODBUS_8BIT_UINT,
                TIPO_DATO_MODBUS_16BIT_INT,
                TIPO_DATO_MODBUS_16BIT_UINT,
                TIPO_DATO_MODBUS_32BIT_INT,
                TIPO_DATO_MODBUS_32BIT_UINT,
                TIPO_DATO_MODBUS_32BIT_FLOAT,
                TIPO_DATO_MODBUS_64BIT_INT,
                TIPO_DATO_MODBUS_64BIT_UINT,
                TIPO_DATO_MODBUS_64BIT_FLOAT,
                TIPO_DATO_MODBUS_BITS];
            var tipo_dato = tipos_datos[i];
            if (tipos_dato_modbus.indexOf(tipo_dato) == -1) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("Los tipos de datos son incorrectos") +
                    " (" + TLNT.Idiomas._("valores disponibles") + ": " +
                    tipos_dato_modbus.join(", ") + ")";
                break;
            }
        }

        // Comprobación de tipo de registros y tipo de datos correcto
        if (parametros_correctos == true) {
            switch (tipo_registro) {
                case TIPO_REGISTRO_MODBUS_COILS:
                case TIPO_REGISTRO_MODBUS_DISCRETE_INPUTS: {
                    parametros_correctos = (tipo_dato == TIPO_DATO_MODBUS_BITS);
                    break;
                }
            }
            if (parametros_correctos == false) {
                descripcion_error = TLNT.Idiomas._("Los tipos de registros y los tipos de datos no coinciden");
                break;
            }
        }

        // Comprobación de número de elementos y tipo de dato correcto
        if (parametros_correctos == true) {
            switch (tipo_dato) {
                case TIPO_DATO_MODBUS_8BIT_INT:
                case TIPO_DATO_MODBUS_8BIT_UINT:
                case TIPO_DATO_MODBUS_16BIT_INT:
                case TIPO_DATO_MODBUS_16BIT_UINT: {
                    parametros_correctos = (numero_elementos == 1);
                    break;
                }
                case TIPO_DATO_MODBUS_32BIT_INT:
                case TIPO_DATO_MODBUS_32BIT_UINT:
                case TIPO_DATO_MODBUS_32BIT_FLOAT: {
                    parametros_correctos = (numero_elementos == 2);
                    break;
                }
                case TIPO_DATO_MODBUS_64BIT_INT:
                case TIPO_DATO_MODBUS_64BIT_UINT:
                case TIPO_DATO_MODBUS_64BIT_FLOAT: {
                    parametros_correctos = (numero_elementos == 4);
                    break;
                }
                case TIPO_DATO_MODBUS_BITS: {
                    parametros_correctos = (numero_elementos == 1);
                    break;
                }
            }
            if (parametros_correctos == false) {
                descripcion_error = TLNT.Idiomas._("Los números de elementos y los tipos de datos no coinciden");
                break;
            }
        }

        // Parámetros del valor
        var parametros_valor = [
            tipo_registro,
            direccion_dispositivo,
            direccion_registro,
            numero_elementos,
            reverso_bytes,
            reverso_registros,
            tipo_dato];
        var cadena_parametros_valor = parametros_valor.join(SEPARADOR_PARAMETROS_SIMPLES);
        cadenas_parametros_valores.push(cadena_parametros_valor);
    }

    // Se crea la cadena de opciones de interfaz con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones = cadenas_parametros_valores.join(SEPARADOR_PARAMETROS_VALORES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones: cadena_opciones,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de valores de la clase de sensor externo 'Wibeee'
function dame_cadena_opciones_valores_clase_sensor_externo_wibeee(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_valores = "";
    var descripcion_error = "";

    // Se comprueba el número de valores
    if (numero_valores_clase_sensor != 1) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._("El número de valores del tipo de sensor externo no coincide con el número de valores del sensor") +
            " (" + TLNT.Idiomas._("número de valores del sensor") + ": " + numero_valores_clase_sensor + ")";
    }

    // Se crea la cadena de opciones de valores con los parámetros de configuración
    var id_tipo_dato_clase_externo_wibeee_sensor = $("#id_tipo_dato_clase_externo_wibeee_sensor").val();
    cadena_opciones_valores = id_tipo_dato_clase_externo_wibeee_sensor;

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_valores: cadena_opciones_valores,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de valores de la clase de sensor externo 'API'
function dame_cadena_opciones_valores_clase_sensor_externo_api(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_valores = "";
    var descripcion_error = "";

    // Se comprueba el número de valores
    if (numero_valores_clase_sensor != 1) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._("El número de valores del tipo de sensor externo no coincide con el número de valores del sensor");
            " (" + TLNT.Idiomas._("número de valores del sensor") + ": " + numero_valores_clase_sensor + ")";
    }

    //Api seleccionada
    var api_seleccionada = $("#api_seleccionada_sensor_externo_apis").val();



    //Se compara el la API seleccionada, en función de ello se devolverá una serie de valores u otros. El valor de api seleccionada es necesario para conocerla en el backend
    switch (api_seleccionada) {
        case 'API_AXONTIME':
            {
                var cups_id = $("#cups_id_clase_externo_api").val();
                var tipo_curva = $("#tipo_curva_clase_externo_axon_time_sensor").val();
                var tipo_energia = $("#tipo_energia_clase_externo_axon_time_sensor").val();
                // Se crea la cadena de opciones de valores con los parámetros de configuración
                if (parametros_correctos == true) {
                    cadena_opciones_valores = [
                        api_seleccionada,
                        cups_id,
                        tipo_curva,
                        tipo_energia].join(SEPARADOR_PARAMETROS_SIMPLES);
                }
                break;
            }

        case 'API_SGCLIMA':
            {

                var usuario = $("#usuario_api_clase_externo_api").val();
                var password = $("#password_api_clase_externo_api").val();
                var id_localizacion = $("#id_localizacion_clase_externo_api").val();
                var id_parametro = $("#id_parametro_clase_externo_api").val();
                // Se crea la cadena de opciones de valores con los parámetros de configuración
                if (parametros_correctos == true) {
                    cadena_opciones_valores = [
                        api_seleccionada,
                        usuario,
                        password,
                        id_localizacion,
                        id_parametro].join(SEPARADOR_PARAMETROS_SIMPLES);
                }
                break;
            }
            
        //Si no se conoce la api seleccionada se devuelve un error
        default:
            {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("La API seleccionada es desconocida");
            }

    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_valores: cadena_opciones_valores,
        descripcion_error: descripcion_error
    };
    return (resultado);
}

//
// Parámetros de funciones de hijos de sensores de procesado
//


// Devuelve la cadena de parámetros de función de hijo de sensor de procesado
function dame_cadena_parametros_funcion_hijo_sensor_procesado(funcion_hijo_sensor_procesado) {
    var resultado = {};
    switch (funcion_hijo_sensor_procesado) {
        case FUNCION_HIJO_SENSOR_PROCESADO_IDENTIDAD: {
            resultado = dame_cadena_parametros_funcion_hijo_sensor_procesado_identidad();
            break;
        }
        case FUNCION_HIJO_SENSOR_PROCESADO_MEDIA: {
            resultado = dame_cadena_parametros_funcion_hijo_sensor_procesado_media();
            break;
        }
        case FUNCION_HIJO_SENSOR_PROCESADO_DESVIACION_ESTANDAR: {
            resultado = dame_cadena_parametros_funcion_hijo_sensor_procesado_desviacion_estandar();
            break;
        }
        case FUNCION_HIJO_SENSOR_PROCESADO_ACUMULADO: {
            resultado = dame_cadena_parametros_funcion_hijo_sensor_procesado_acumulado();
            break;
        }
        case FUNCION_HIJO_SENSOR_PROCESADO_INCREMENTO: {
            resultado = dame_cadena_parametros_funcion_hijo_sensor_procesado_incremento();
            break;
        }
    }
    return (resultado);
}


// Devuelve la cadena de parámetros de función de hijo de sensor de procesado 'Identidad'
function dame_cadena_parametros_funcion_hijo_sensor_procesado_identidad() {
    var parametros_correctos = true;
    var cadena_parametros_funcion = "";
    var descripcion_error = "";

    // Se crea la cadena de parámetros de la función
    if (parametros_correctos == true) {
        cadena_parametros_funcion = "";
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_parametros_funcion: cadena_parametros_funcion,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de parámetros de función de hijo de sensor de procesado 'Media'
function dame_cadena_parametros_funcion_hijo_sensor_procesado_media() {
    var parametros_correctos = true;
    var cadena_parametros_funcion = "";
    var descripcion_error = "";

    // Dirección de enlace
    if (parametros_correctos == true) {
        var numero_horas = $("#numero_horas_funcion_hijo_sensor_procesado_media").val();
        if ((parseInt(numero_horas) < VALOR_MINIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO) ||
            (parseInt(numero_horas) > VALOR_MAXIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El número de horas de la función es incorrecto") +
                " (" + TLNT.Idiomas._("rango de valores") + ": " +
                VALOR_MINIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO + " - " + VALOR_MAXIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO + ")";
        }
    }

    // Se crea la cadena de parámetros de la función
    if (parametros_correctos == true) {
        cadena_parametros_funcion = [
            numero_horas].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_parametros_funcion: cadena_parametros_funcion,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de parámetros de función de hijo de sensor de procesado 'Desviación estándar'
function dame_cadena_parametros_funcion_hijo_sensor_procesado_desviacion_estandar() {
    var parametros_correctos = true;
    var cadena_parametros_funcion = "";
    var descripcion_error = "";

    // Dirección de enlace
    if (parametros_correctos == true) {
        var numero_horas = $("#numero_horas_funcion_hijo_sensor_procesado_desviacion_estandar").val();
        if ((parseInt(numero_horas) < VALOR_MINIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO) ||
            (parseInt(numero_horas) > VALOR_MAXIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El número de horas de la función es incorrecto") +
                " (" + TLNT.Idiomas._("rango de valores") + ": " +
                VALOR_MINIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO + " - " + VALOR_MAXIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO + ")";
        }
    }

    // Se crea la cadena de parámetros de la función
    if (parametros_correctos == true) {
        cadena_parametros_funcion = [
            numero_horas].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_parametros_funcion: cadena_parametros_funcion,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de parámetros de función de hijo de sensor de procesado 'Acumulado'
function dame_cadena_parametros_funcion_hijo_sensor_procesado_acumulado() {
    var parametros_correctos = true;
    var cadena_parametros_funcion = "";
    var descripcion_error = "";

    // Dirección de enlace
    if (parametros_correctos == true) {
        var numero_horas = $("#numero_horas_funcion_hijo_sensor_procesado_acumulado").val();
        if ((parseInt(numero_horas) < VALOR_MINIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO) ||
            (parseInt(numero_horas) > VALOR_MAXIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO)) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El número de horas de la función es incorrecto") +
                " (" + TLNT.Idiomas._("rango de valores") + ": " +
                VALOR_MINIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO + " - " + VALOR_MAXIMO_NUMERO_HORAS_FUNCION_HIJO_SENSOR_PROCESADO + ")";
        }
    }

    // Se crea la cadena de parámetros de la función
    if (parametros_correctos == true) {
        cadena_parametros_funcion = [
            numero_horas].join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_parametros_funcion: cadena_parametros_funcion,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de parámetros de función de hijo de sensor de procesado 'Incremento'
function dame_cadena_parametros_funcion_hijo_sensor_procesado_incremento() {
    var parametros_correctos = true;
    var cadena_parametros_funcion = "";
    var descripcion_error = "";

    // Se crea la cadena de parámetros de la función
    if (parametros_correctos == true) {
        cadena_parametros_funcion = "";
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_parametros_funcion: cadena_parametros_funcion,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


//
// Funciones auxiliares
//


// TODO SERGIO: Recuperar de las características de clase del sensor?
function dame_numero_valores_clase_sensor(clase_sensor) {
    var numero_valores_clase_sensor = null;
    switch (clase_sensor) {
        case CLASE_SENSOR_TEMPERATURA: {
            numero_valores_clase_sensor = 1;
            break;
        }
        case CLASE_SENSOR_HUMEDAD: {
            numero_valores_clase_sensor = 1;
            break;
        }
        case CLASE_SENSOR_LUZ_INTERIOR: {
            numero_valores_clase_sensor = 2;
            break;
        }
        case CLASE_SENSOR_VIENTO: {
            numero_valores_clase_sensor = 2;
            break;
        }
        case CLASE_SENSOR_ENERGIA_ACTIVA:
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            numero_valores_clase_sensor = 1;
            break;
        }
        case CLASE_SENSOR_CORTES_TENSION: {
            numero_valores_clase_sensor = 1;
            break;
        }
        case CLASE_SENSOR_COMPRA_ENERGIA: {
            numero_valores_clase_sensor = 1;
            break;
        }
        case CLASE_SENSOR_GAS: {
            numero_valores_clase_sensor = 1;
            break;
        }
        case CLASE_SENSOR_AGUA: {
            numero_valores_clase_sensor = 1;
            break;
        }
        case CLASE_SENSOR_GENERICA: {
            numero_valores_clase_sensor = 1;
            break;
        }
    }
    return (numero_valores_clase_sensor);
}


// TODO SERGIO: Recuperar de las características de clase del sensor?
function dame_numero_valores_clase_clase_sensor(clase_sensor) {
    var numero_valores_clase_clase_sensor = null;
    switch (clase_sensor) {
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    numero_valores_clase_clase_sensor = 4;
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    numero_valores_clase_clase_sensor = 4;
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_COMPRA_ENERGIA: {
             switch (pais_tarifas_gas) {
                case PAIS_ESPANYA: {
                    numero_valores_clase_clase_sensor = 4;
                    break;
                }
            }
            break;
        }
        case CLASE_SENSOR_GAS: {
            switch (pais_tarifas_gas) {
                case PAIS_ESPANYA: {
                    numero_valores_clase_clase_sensor = 3;
                    break;
                }
            }
            break;
        }
        // Nota: Aquí se cuentan el valor y el incremento, aunque realmente no sean valores de clase de clase, son casos 'especiales'
        case CLASE_SENSOR_AGUA:
        case CLASE_SENSOR_GENERICA: {
            numero_valores_clase_clase_sensor = 2;
            break;
        }
        default: {
            numero_valores_clase_clase_sensor = dame_numero_valores_clase_sensor(clase_sensor);
            break;
        }
    }
    return (numero_valores_clase_clase_sensor);
}
