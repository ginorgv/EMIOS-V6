/*
 * Funciones de validaciones de contenido de acciones
 *
 */


// Devuelve la cadena del contenido de la acción
function dame_cadena_contenido_accion(tipo_accion_clase_actuador, numero_valores_clase_actuador, cadena_contenido_accion) {
    var parametros_correctos = true;
    var descripcion_error = "";

    // Se eliminan los espacios y se comprueban los parámetros
    cadena_contenido_accion = replaceAll(cadena_contenido_accion, " ", "");
    var contenido_accion_valores = cadena_contenido_accion.split(SEPARADOR_PARAMETROS_VALORES);

    // Se comprueba el número de valores
    if (contenido_accion_valores.length != numero_valores_clase_actuador) {
        parametros_correctos = false;
        descripcion_error = TLNT.Idiomas._('El número de acciones configurado no coincide con el número de valores del actuador') +
            " (" + TLNT.Idiomas._('número de valores del actuador') + ": " + numero_valores_clase_actuador + ")";
    }

    // Se comprueba cada una de las acciones de los valores
    for (var i = 0; i < numero_valores_clase_actuador; i++) {
        switch (tipo_accion_clase_actuador) {
            case TIPO_ACCIONES_VALORES_UNICOS: {
                break;
            }
            case TIPO_ACCIONES_VALORES_INICIAL_FINAL:
            case TIPO_ACCIONES_VALORES_FIJOS_GRADUALES: {
                // Nota: Pendiente de implementar (actualmente no se utiliza ya que sólo se envían acciones predefinidas de estos tipos)
                break;
            }
        }
    }

    // Se devuelve el resultado
    var resultado = {
        parametros_correctos: parametros_correctos,
        cadena_contenido_accion: cadena_contenido_accion,
        descripcion_error: descripcion_error
    };
    return (resultado);
}


//
// Funciones auxiliares
//


function dame_tipo_acciones_clase_actuador(clase_actuador) {
    var tipo_acciones = null;
    switch (clase_actuador) {
        case CLASE_ACTUADOR_MENSAJE: {
            tipo_acciones = TIPO_ACCIONES_VALORES_UNICOS;
            break;
        }
        case CLASE_ACTUADOR_INTERRUPTOR: {
            tipo_acciones = TIPO_ACCIONES_VALORES_INICIAL_FINAL;
            break;
        }
        case CLASE_ACTUADOR_TELEPOSTE: {
            tipo_acciones = TIPO_ACCIONES_VALORES_FIJOS_GRADUALES;
            break;
        }
        case CLASE_ACTUADOR_LUZ_GRADUAL_4: {
            tipo_acciones = TIPO_ACCIONES_VALORES_INICIAL_FINAL;
            break;
        }
        case CLASE_ACTUADOR_GENERICA: {
            tipo_acciones = TIPO_ACCIONES_VALORES_UNICOS;
            break;
        }
        default: {
            break;
        }
    }
    return (tipo_acciones);
}
