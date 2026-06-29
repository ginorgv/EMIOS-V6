// Devuelve los valores de los controles de una acción
function dame_valores_controles_accion(clase_actuador) {
    var id_accion_predefinida = ID_NINGUNO;
    var contenido_accion = "";
    var valor_accion = "";

    // Se recupera el contenido de la acción
    switch (clase_actuador) {
        case CLASE_ACTUADOR_MENSAJE: {
            var titulo = $('#titulo_mensaje').val();
            var contenido = $('#contenido_mensaje').val();
            if (comprueba_longitud_cadena(titulo, NUMERO_MAXIMO_CARACTERES_TITULO_MENSAJE) == false) {
                $('#titulo_mensaje').addClass('data-check-failed');
                return;
            }
            if (comprueba_longitud_cadena(contenido, NUMERO_MAXIMO_CARACTERES_CONTENIDO_MENSAJE) == false) {
                $('#contenido_mensaje').addClass('data-check-failed');
                return;
            }

            // Se codifican el título y el contenido del mensaje en formato json
            var mensaje = {};
            mensaje.titulo = titulo;
            mensaje.contenido = contenido;
            contenido_accion = JSON.stringify(mensaje);
            valor_accion = VALOR_ACCION_ENVIAR_MENSAJE;
            break;
        }
        case CLASE_ACTUADOR_INTERRUPTOR:
        case CLASE_ACTUADOR_TELEPOSTE:
        case CLASE_ACTUADOR_LUZ_GRADUAL_4: {
            var accion_seleccionada = $("input[name=acciones_predefinidas]:checked").val();
            if (accion_seleccionada == null) {
                jAlert(TLNT.Idiomas._("No hay acción seleccionada"));
                return;
            } else {
                id_accion_predefinida = accion_seleccionada.split('__')[1];
            }
            break;
        }
        case CLASE_ACTUADOR_GENERICA: {
            // Tipo de acciones y número de valores de la clase del actuador
            var tipo_acciones_clase_actuador = dame_tipo_acciones_clase_actuador(clase_actuador);
            var numero_valores_clase_actuador = dame_numero_valores_clase_actuador(clase_actuador);

            // Comprobación de datos correctos
            contenido_accion = $('#contenido_accion').val();
            var resultado_contenido_accion = dame_cadena_contenido_accion(
                tipo_acciones_clase_actuador,
                numero_valores_clase_actuador,
                contenido_accion);
            if (resultado_contenido_accion.parametros_correctos == false) {
                jAlert(resultado_contenido_accion.descripcion_error);
                return (null);
            }
            contenido_accion = resultado_contenido_accion.cadena_contenido_accion;
            valor_accion = $('#valor_accion').val();
            if ((parseInt(valor_accion) < VALOR_MINIMO_ACCION_ACTUADOR) ||
                (parseInt(valor_accion) > VALOR_MAXIMO_ACCION_ACTUADOR)) {
                var mensaje_error = TLNT.Idiomas._("El valor de la acción es incorrecto") +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    VALOR_MINIMO_ACCION_ACTUADOR + " - " + VALOR_MAXIMO_ACCION_ACTUADOR + ")";
                jAlert(mensaje_error);
                return (null);
            }
            break;
        }
    }

    // Se devuelven los valores de los controles de la acción
    var valores_controles_accion = [];
    valores_controles_accion["id_accion_predefinida"] = id_accion_predefinida;
    valores_controles_accion["contenido_accion"] = contenido_accion;
    valores_controles_accion["valor_accion"] = valor_accion;
    return (valores_controles_accion);
}

