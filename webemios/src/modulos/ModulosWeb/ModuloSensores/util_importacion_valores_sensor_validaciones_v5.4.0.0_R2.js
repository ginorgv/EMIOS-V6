// Devuelve la cadena de opciones de fichero para la importación de valores de un sensor
function dame_cadena_opciones_fichero_importacion_valores_sensor() {
    var parametros_correctos = true;
    var cadena_opciones_fichero = "";
    var descripcion_error = "";

    // Formato de fichero
    if (parametros_correctos == true) {
        var formato_fichero_valores = $("#formato_fichero_valores_importacion_valores_sensor").val();
    }

    // Carácter separador
    if (parametros_correctos == true) {
        var caracter_separador = $("#caracter_separador_importacion_valores_sensor").val();
        if (caracter_separador.length != 1) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El carácter separador debe ser un solo carácter");
        }
        else {
            // Sustituto separador
            if (caracter_separador == SEPARADOR_PARAMETROS_SIMPLES) {
                caracter_separador = SUSTITUTO_SEPARADOR;
            }
        }
    }

    // Punto decimal
    if (parametros_correctos == true) {
        var id_punto_decimal = $("#id_punto_decimal_importacion_valores_sensor").val();
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
        var numero_lineas_cabeceras = $("#numero_lineas_cabeceras_importacion_valores_sensor").val();
        if (numero_lineas_cabeceras == 0) {
            cabeceras = FICHERO_CSV_SIN_CABECERAS;
        }
        else {
            cabeceras = FICHERO_CSV_CON_CABECERAS;
        }
    }

    // Columna de fecha
    if (parametros_correctos == true) {
        var columna_fecha = $("#columna_fecha_importacion_valores_sensor").val();
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
        var formato_fecha = $("#formato_fecha_importacion_valores_sensor").val();
        formato_fecha = replaceAll(formato_fecha, SEPARADOR_PARAMETROS_SIMPLES, SUSTITUTO_SEPARADOR);
        var formato_fecha_python = convierte_formato_fecha_hora_a_formato_fecha_hora_python(formato_fecha);
    }

    // Hora:
    // - Columna de hora
    // - Formato de hora
    if (parametros_correctos == true) {
        var hora_columna_independiente = $("#hora_columna_independiente_importacion_valores_sensor").val();
        var columna_hora = "";
        var formato_hora = "";
        var formato_hora_python = "";
        if (hora_columna_independiente == VALOR_SI) {
            columna_hora = $("#columna_hora_importacion_valores_sensor").val();
            if (parseInt(columna_hora) < 1) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("La columna de hora debe ser mayor que 0");
            }
            else {
                columna_hora -= 1;
            }
            if (parametros_correctos == true) {
                formato_hora = $("#formato_hora_importacion_valores_sensor").val();
                formato_hora = replaceAll(formato_hora, SEPARADOR_PARAMETROS_SIMPLES, SUSTITUTO_SEPARADOR);
                formato_hora_python = convierte_formato_fecha_hora_a_formato_fecha_hora_python(formato_hora);
            }
        }
    }

    // Zona horaria y horario de verano
    if (parametros_correctos == true) {
        var zona_horaria = $("#zona_horaria_importacion_valores_sensor").val();
        var columna_horario_verano = $("#columna_horario_verano_importacion_valores_sensor").val();
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
        var numero_valores = $("#numero_valores_importacion_valores_sensor").val();
        if (parseInt(numero_valores) < 1) {
            parametros_correctos = false;
            descripcion_error = TLNT.Idiomas._("El número de valores debe ser mayor que 0");
        }
    }

    // Tipo de valores y segundos de incrementos
    if (parametros_correctos == true) {
        var tipo_valores = $("#tipo_valores_sensor_importacion_valores_sensor").val();
        if (tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES) {
            var tipo_incrementos = $("#tipo_incrementos_importacion_valores_sensor").val();
            var tipo_horas_incrementos = $("#tipo_horas_incrementos_importacion_valores_sensor").val();
            var horas_incrementos = $("#horas_incrementos_importacion_valores_sensor").val();
            var segundos_incrementos = Math.round(horas_incrementos * 3600);
            if (horas_incrementos > VALOR_MAXIMO_HORAS_INCREMENTOS_IMPORTACION_VALORES_SENSOR) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._("El número de horas de incrementos es incorrecto") +
                    " (" + TLNT.Idiomas._("valor máximo") + ": " + VALOR_MAXIMO_HORAS_INCREMENTOS_IMPORTACION_VALORES_SENSOR + ")" + "\n" +
                    " (" + TLNT.Idiomas._("el tiempo de incrementos es en horas y anteriormente era en segundos") + ")";
            }
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

    // Se crea la cadena de opciones de fichero con los parámetros de configuración
    if (parametros_correctos == true) {
        var parametros_opciones_fichero = [
            formato_fichero_valores,
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
            numero_valores];
        if (tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES) {
            var tipo_incrementos = $("#tipo_incrementos_importacion_valores_sensor").val();
            parametros_opciones_fichero.push(segundos_incrementos);
            parametros_opciones_fichero.push(tipo_incrementos);
        }
        cadena_opciones_fichero = parametros_opciones_fichero.join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_fichero: cadena_opciones_fichero,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


// Devuelve la cadena de opciones de valores de fichero para la importación de valores de un sensor
function dame_cadena_opciones_valores_fichero_importacion_valores_sensor(numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var cadena_opciones_valores_fichero = "";
    var descripcion_error = "";

    // Número de valores
    var numero_valores = $("#numero_valores_importacion_valores_sensor").val();

    // Se comprueba el número de valores
    if (numero_valores != numero_valores_clase_sensor) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._("El número de valores configurado no coincide con el número de valores del sensor") +
            " (" + numero_valores_clase_sensor + ")";
    }

    // Columnas de valores
    if (parametros_correctos == true) {
        var cadena_opciones_valores = $("#columnas_valores_importacion_valores_sensor").val();
        var opciones_valores = cadena_opciones_valores.split(SEPARADOR_PARAMETROS_VALORES);
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
                var cadena_columna_valor = opciones_valor[0];
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

                // Nota: El índice de elemento de valor se corresponde al bit inicial (con número de bits a 1)
                var cadena_opciones_valor = (columna_valor - 1);
                if (opciones_valor.length == 2) {
                    var cadena_indice_elemento_valor = opciones_valor[1];
                    if (PATRON_NUMERO_NATURAL.test(cadena_indice_elemento_valor) == false) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._("Los índices de elementos de valores deben ser valores numéricos");
                        break;
                    }
                    var indice_elemento_valor = parseInt(cadena_indice_elemento_valor);
                    if (indice_elemento_valor <= 0) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._("Los índices de elementos de valores deben ser mayores que 0");
                        break;
                    }
                    cadena_opciones_valor += SEPARADOR_PARAMETROS_SIMPLES + (indice_elemento_valor - 1);
                }
                cadena_opciones_valores += cadena_opciones_valor;
            }
        }
    }

    // Se crea la cadena de opciones de valores de fichero con los parámetros de configuración
    if (parametros_correctos == true) {
        cadena_opciones_valores_fichero = cadena_opciones_valores;
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_opciones_valores_fichero: cadena_opciones_valores_fichero,
        descripcion_error: descripcion_error
    };
    return (resultado);
}
