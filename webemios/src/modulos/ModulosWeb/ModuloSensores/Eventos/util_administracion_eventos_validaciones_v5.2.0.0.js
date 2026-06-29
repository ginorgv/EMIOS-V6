/*
 * Funciones de validaciones de parámetros de eventos
 *
 */


// Devuelve la cadena de parámetros del evento
function dame_cadena_parametros_evento(tipo_evento, numero_valores_clase_sensor) {
    var parametros_correctos = true;
    var descripcion_error = "";

    // Se recuperan el campo del evento
    var campo_evento = $('#campo_evento').val();

    // Flag para ignorar el campo del evento para comprobar los parámetros
    var ignorar_campo_evento_comprobacion_parametros = false;
    switch (tipo_evento) {
        case TIPO_EVENTO_LINEA_BASE:
        case TIPO_EVENTO_PERFIL_HORARIO: {
            ignorar_campo_evento_comprobacion_parametros = true;
            break;
        }
    }

    // Se recupera la cadena de los parámetros del evento y se eliminan los espacios en blanco
    var cadena_parametros_evento = $('#parametros_evento').val();
    if (comprueba_longitud_cadena(cadena_parametros_evento, NUMERO_MAXIMO_CARACTERES_PARAMETROS_EVENTO) == false) {
        $("#parametros_evento").addClass('data-check-failed');
        parametros_correctos = false;
    }
    cadena_parametros_evento = replaceAll(cadena_parametros_evento, " ", "");

    // Parámetros del evento
    if (parametros_correctos == true) {
        var parametros_evento_valores = cadena_parametros_evento.split(SEPARADOR_PARAMETROS_VALORES);
        var numero_valores_comprobacion_parametros_evento = numero_valores_clase_sensor;
        if ((campo_evento == CAMPO_TODOS) && (ignorar_campo_evento_comprobacion_parametros == false)) {
            // Se comprueba el número de parámetros del evento
            if (parametros_evento_valores.length != numero_valores_clase_sensor) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('El número de parámetros configurado no coincide con el número de valores del sensor') +
                    " (" + TLNT.Idiomas._('número de valores del sensor') + ": " + numero_valores_clase_sensor + ")";
            }
        }
        else {
            if (parametros_evento_valores.length != 1) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('El número de parámetros configurado es incorrecto');
            }

            // Selección de tipo de evento
            if (parametros_correctos == true) {
                switch (tipo_evento) {
                    case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL:
                    case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO: {
                        var periodo_tiempo_evento = $('#periodo_tiempo_evento_incremento_acumulado_maximo').val();
                        var parametros_evento_valores = [];
                        parametros_evento_valores.push(cadena_parametros_evento);
                        cadena_parametros_evento = [
                            campo_evento,
                            periodo_tiempo_evento,
                            cadena_parametros_evento].join(SEPARADOR_PARAMETROS_COMPUESTOS);
                        numero_valores_comprobacion_parametros_evento = 1;
                        break;
                    }
                    case TIPO_EVENTO_LINEA_BASE: {
                        var id_linea_base_evento = $('#id_linea_base_evento_linea_base').val();
                        if (id_linea_base_evento == ID_NINGUNO) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._("No hay línea base seleccionada");
                        }
                        if (parametros_correctos == true) {
                            var parametros_evento_valores = [];
                            parametros_evento_valores.push(cadena_parametros_evento);
                            cadena_parametros_evento = [
                                id_linea_base_evento,
                                cadena_parametros_evento].join(SEPARADOR_PARAMETROS_COMPUESTOS);
                            numero_valores_comprobacion_parametros_evento = 1;
                        }
                        break;
                    }
                    case TIPO_EVENTO_PERFIL_HORARIO: {
                        var intervalo_valores = $('#intervalo_valores_evento_perfil_horario').val();
                        var numero_dias_perfil_horario = $('#numero_dias_perfil_horario_evento_perfil_horario').val();
                        var tipo_perfil_horario = $('#tipo_perfil_horario_evento_perfil_horario').val();
                        var agrupaciones_dias_semana = dame_agrupaciones_dias_semana_control("cadena_agrupaciones_dias_semana_evento_perfil_horario");
                        if (agrupaciones_dias_semana.correcto == false) {
                            parametros_correctos = false;
                            descripcion_error = "";
                        }
                        var cadena_agrupaciones_dias_semana = dame_cadena_agrupaciones_dias_semana(agrupaciones_dias_semana);
                        if (parametros_correctos == true) {
                            if (tipo_perfil_horario == TIPO_PERFIL_HORARIO_CONFIGURABLE) {
                                if (agrupaciones_dias_semana.agrupaciones_dias.length == 0) {
                                    parametros_correctos = false;
                                    descripcion_error = TLNT.Idiomas._("No hay agrupaciones de días de la semana");
                                }
                            }
                        }
                        if (parametros_correctos == true) {
                            var horario_semanal = dame_horario_semanal_controles("evento", false);
                            if (horario_semanal.correcto == false) {
                                parametros_correctos = false;
                                descripcion_error = "";
                            }
                            var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
                        }
                        if (parametros_correctos == true) {
                            var exclusion_fechas = dame_fechas_controles("exclusion_fechas_evento");
                            if (exclusion_fechas.correcto == false) {
                                parametros_correctos = false;
                                descripcion_error = "";
                            }
                            var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
                        }
                        if (parametros_correctos == true) {
                            var parametros_evento_valores = [];
                            parametros_evento_valores.push(cadena_parametros_evento);
                            cadena_parametros_evento = [
                                campo_evento,
                                intervalo_valores,
                                numero_dias_perfil_horario,
                                tipo_perfil_horario,
                                cadena_agrupaciones_dias_semana,
                                cadena_horario_semanal,
                                cadena_exclusion_fechas,
                                cadena_parametros_evento].join(SEPARADOR_PARAMETROS_COMPUESTOS);
                            numero_valores_comprobacion_parametros_evento = 1;
                        }
                        break;
                    }
                    default: {
                        // Se crea la lista de parámetros del evento (con los parámetros de campo seleccionado en el lugar correspondiente)
                        if (numero_valores_clase_sensor > 1) {
                            var parametros_evento_valores = [];
                            var parametros_evento_campo = cadena_parametros_evento;
                            var indice_campo_seleccionado = $('#campo_evento').prop('selectedIndex') - 1;
                            for (var i = 0; i < numero_valores_clase_sensor; i++) {
                                if (indice_campo_seleccionado == i) {
                                    parametros_evento_valores.push(parametros_evento_campo);
                                }
                                else {
                                    parametros_evento_valores.push("");
                                }
                            }
                            cadena_parametros_evento = parametros_evento_valores.join(SEPARADOR_PARAMETROS_VALORES);
                        }
                    }
                }
            }
        }
    }

    // Se comprueba cada uno de los parámetros de los valores
    if (parametros_correctos == true) {
        for (var i = 0; i < numero_valores_comprobacion_parametros_evento; i++) {
            var cadena_parametros_evento_valor = parametros_evento_valores[i];
            if (cadena_parametros_evento_valor == "") {
                continue;
            }
            var parametros_evento_valor = cadena_parametros_evento_valor.split(SEPARADOR_PARAMETROS_SIMPLES);

            // Se recuperan el número de parámetros del evento y si tienen carácter 'extra' (de valor incluído)
            var numero_parametros_evento = null;
            var tipos_parametros_evento = null;
            var parametros_evento_opcion_valor_incluido = null;
            switch (tipo_evento) {
                case TIPO_EVENTO_INCREMENTO_TEMPORAL_MINIMO:
                case TIPO_EVENTO_INCREMENTO_TEMPORAL_MAXIMO: {
                    numero_parametros_evento = 2;
                    tipos_parametros_evento = [TIPO_PARAMETRO_NUMERO_REAL, TIPO_PARAMETRO_NUMERO_ENTERO_POSITIVO];
                    parametros_evento_opcion_valor_incluido = [0];
                    break;
                }
                case TIPO_EVENTO_VALOR_MINIMO:
                case TIPO_EVENTO_VALOR_MAXIMO: {
                    numero_parametros_evento = 2;
                    tipos_parametros_evento = [TIPO_PARAMETRO_NUMERO_REAL, TIPO_PARAMETRO_NUMERO_REAL];
                    parametros_evento_opcion_valor_incluido = [0];
                    break;
                }
                case TIPO_EVENTO_VALORES_MINIMO_MAXIMO:
                case TIPO_EVENTO_INTERVALO_VALORES: {
                    numero_parametros_evento = 3;
                    tipos_parametros_evento = [TIPO_PARAMETRO_NUMERO_REAL, TIPO_PARAMETRO_NUMERO_REAL, TIPO_PARAMETRO_NUMERO_REAL];
                    parametros_evento_opcion_valor_incluido = [0, 1];
                    break;
                }
                case TIPO_EVENTO_VALOR_EXACTO:
                case TIPO_EVENTO_VALOR_DIFERENTE: {
                    numero_parametros_evento = 1;
                    tipos_parametros_evento = [TIPO_PARAMETRO_NUMERO_REAL];
                    parametros_evento_opcion_valor_incluido = [];
                    break;
                }
                case TIPO_EVENTO_VALOR_EXACTO_BITS:
                case TIPO_EVENTO_VALOR_DIFERENTE_BITS: {
                    numero_parametros_evento = 3;
                    tipos_parametros_evento = [TIPO_PARAMETRO_NUMERO_ENTERO_NO_NEGATIVO, TIPO_PARAMETRO_NUMERO_ENTERO_POSITIVO, TIPO_PARAMETRO_NUMERO_ENTERO_POSITIVO];
                    parametros_evento_opcion_valor_incluido = [];
                    break;
                }
                case TIPO_EVENTO_VALOR_REPETIDO: {
                    numero_parametros_evento = 1;
                    tipos_parametros_evento = [TIPO_PARAMETRO_NUMERO_ENTERO_POSITIVO];
                    parametros_evento_opcion_valor_incluido = [];
                    break;
                }
                case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL: {
                    numero_parametros_evento = 2;
                    tipos_parametros_evento = [TIPO_PARAMETRO_NUMERO_ENTERO_NO_NEGATIVO, TIPO_PARAMETRO_NUMERO_REAL];
                    parametros_evento_opcion_valor_incluido = [1];
                    break;
                }
                case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO: {
                    numero_parametros_evento = 2;
                    tipos_parametros_evento = [TIPO_PARAMETRO_NUMERO_ENTERO_POSITIVO, TIPO_PARAMETRO_NUMERO_REAL];
                    parametros_evento_opcion_valor_incluido = [1];
                    break;
                }
                case TIPO_EVENTO_LINEA_BASE:
                case TIPO_EVENTO_PERFIL_HORARIO: {
                    numero_parametros_evento = 1;
                    tipos_parametros_evento = [TIPO_PARAMETRO_NUMERO_REAL];
                    parametros_evento_opcion_valor_incluido = [];
                    break;
                }
            }

            // Parámetros del evento
            if (parametros_evento_valor.length != numero_parametros_evento) {
                parametros_correctos = false;
                descripcion_error = TLNT.Idiomas._('El número de parámetros del evento es incorrecto') +
                    " (" + TLNT.Idiomas._('número de parámetros') + ": " + numero_parametros_evento + ")";
                break;
            }

            // Se comprueban los parámetros del evento
            for (var j = 0; j < parametros_evento_valor.length; j++) {
                var parametro_evento_valor = parametros_evento_valor[j];

                // Comprobación de '*' en el último caracter del valor del parámetro
                if (parametros_evento_opcion_valor_incluido.indexOf(j) > -1) {
                    if (parametro_evento_valor.slice(-1) == "*") {
                        parametro_evento_valor = parametro_evento_valor.slice(0, -1);
                    }
                }

                // Comprobación del valor del parámetro
                var tipo_parametro_evento = tipos_parametros_evento[j];
                switch (tipo_parametro_evento) {
                    case TIPO_PARAMETRO_NUMERO_REAL: {
                        if (PATRON_NUMERO_REAL.test(parametro_evento_valor) == false) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._('El parámetro del evento debe ser un número real') +
                                " (" + TLNT.Idiomas._('número de parámetro') + ": " + (j + 1) + ")";
                        }
                        break;
                    }
                    case TIPO_PARAMETRO_NUMERO_ENTERO_POSITIVO: {
                        if (PATRON_NUMERO_NATURAL.test(parametro_evento_valor) == false) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._('El parámetro del evento debe ser númerico') +
                                " (" + TLNT.Idiomas._('número de parámetro') + ": " + (j + 1) + ")";
                        }
                        if (parseInt(parametro_evento_valor) < 1) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._('El parámetro del evento debe ser mayor que 0') +
                                " (" + TLNT.Idiomas._('número de parámetro') + ": " + (j + 1) + ")";
                        }
                        break;
                    }
                    case TIPO_PARAMETRO_NUMERO_ENTERO_NO_NEGATIVO: {
                        if (PATRON_NUMERO_NATURAL.test(parametro_evento_valor) == false) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._('El parámetro del evento debe ser númerico') +
                                " (" + TLNT.Idiomas._('número de parámetro') + ": " + (j + 1) + ")";
                        }
                        if (parseInt(parametro_evento_valor) < 0) {
                            parametros_correctos = false;
                            descripcion_error = TLNT.Idiomas._('El parámetro del evento debe ser 0 o mayor que 0') +
                                " (" + TLNT.Idiomas._('número de parámetro') + ": " + (j + 1) + ")";
                        }
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

            // Comprobaciones específicas de los tipos de evento
            switch (tipo_evento) {
                case TIPO_EVENTO_VALORES_MINIMO_MAXIMO:
                case TIPO_EVENTO_INTERVALO_VALORES: {
                    if (parseFloat(parametros_evento_valor[0]) >= parseFloat(parametros_evento_valor[1])) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._('El valor mínimo debe ser menor que el valor máximo');
                    }
                    break;
                }
                case TIPO_EVENTO_VALOR_EXACTO_BITS:
                case TIPO_EVENTO_VALOR_DIFERENTE_BITS: {
                    if (parseFloat(parametros_evento_valor[2]) > parseFloat(parametros_evento_valor[1])) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._('El número de bits no puede ser mayor que el bit inicial');
                    }
                    break;
                }
                case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL: {
                    if ((parseInt(parametros_evento_valor[0]) < 0) || (parseInt(parametros_evento_valor[0]) > 23)) {
                        parametros_correctos = false;
                        descripcion_error = TLNT.Idiomas._('La hora de inicio debe ser de 0 a 23');
                    }
                    break;
                }
            }
        }
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_parametros_evento: cadena_parametros_evento,
        descripcion_error: descripcion_error
    };
    return (resultado);
}
