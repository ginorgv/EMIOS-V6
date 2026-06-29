//
// Funciones de estadística (de valores de sensores)
//


// Muestra el histograma del valor de un sensor
function boton_sensores_histograma_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_histograma(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var detalle = parametros_informe["detalle"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Se recupera la información del histograma
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Estadistica/dame_histograma_valores_sensor.php", {
        id_ratio: id_ratio,
        clase_sensor: clase_sensor,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        parametros_extra_campo: parametros_extra_campo,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        intervalo_valores: intervalo_valores,
        detalle: detalle,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas)
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Comprobación de datos disponibles
        var hay_datos = resultado.hay_datos;
        if (hay_datos == false) {
            jAlert(TLNT.Idiomas._("No hay datos disponibles"));
            return;
        }

        // Se muestra el informe
        $("#informe-sin-datos-sensores-histograma").hide();
        $("#informe-sensores-histograma").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-histograma",
            "contenedor-tabla-medidas-estadisticas-histograma",
            "tabla-percentiles-histograma"]);

        // Se dibuja el informe
        var parametros = {
            id_grafica_histograma: "grafica-histograma",
            id_contenedor_tabla_medidas_estadisticas: "contenedor-tabla-medidas-estadisticas-histograma",
            id_contenedor_tabla_percentiles: "contenedor-tabla-percentiles-histograma"};
        dibuja_informe_sensores_histograma(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra la correlación entre valores de sensores
function boton_sensores_correlacion_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_correlacion(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clases_sensores_independientes = parametros_informe["clases_sensores_independientes"];
    var ids_sensores_independientes = parametros_informe["ids_sensores_independientes"];
    var nombres_sensores_independientes = parametros_informe["nombres_sensores_independientes"];
    var campos_independientes = parametros_informe["campos_independientes"];
    var parametros_extra_campos_independientes = parametros_informe["parametros_extra_campos_independientes"];
    var clase_sensor_dependiente = parametros_informe["clase_sensor_dependiente"];
    var id_sensor_dependiente = parametros_informe["id_sensor_dependiente"];
    var nombre_sensor_dependiente = parametros_informe["nombre_sensor_dependiente"];
    var campo_dependiente = parametros_informe["campo_dependiente"];
    var parametros_extra_campo_dependiente = parametros_informe["parametros_extra_campo_dependiente"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var funcion_correlacion = parametros_informe["funcion_correlacion"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Se recupera la información de la correlación
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Estadistica/dame_correlacion_valores_sensores.php", {
        id_ratio: id_ratio,
        clases_sensores_independientes: clases_sensores_independientes,
        ids_sensores_independientes: ids_sensores_independientes,
        nombres_sensores_independientes: nombres_sensores_independientes,
        campos_independientes: campos_independientes,
        parametros_extra_campos_independientes: parametros_extra_campos_independientes,
        clase_sensor_dependiente: clase_sensor_dependiente,
        id_sensor_dependiente: id_sensor_dependiente,
        nombre_sensor_dependiente: nombre_sensor_dependiente,
        campo_dependiente: campo_dependiente,
        parametros_extra_campo_dependiente: parametros_extra_campo_dependiente,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        intervalo_valores: intervalo_valores,
        funcion_correlacion: funcion_correlacion,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas)
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Comprobación de datos disponibles
        var hay_datos = resultado.hay_datos;
        if (hay_datos == false) {
            jAlert(TLNT.Idiomas._("No hay datos disponibles"));
            return;
        }

        // Se muestra el informe
        $("#informe-sin-datos-sensores-correlacion").hide();
        $("#informe-sensores-correlacion").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-correlacion",
            "contenedor-tabla-funcion-correlacion"]);

        // Se guarda la función de correlación, el texto de error estándar y los coeficientes de variación y correlación
        // (para la generación de línea base desde la correlación)
        var cadena_funcion_correlacion = resultado.cadena_funcion_correlacion;
        var error_estandar = resultado.error_estandar;
        var coeficiente_variacion = resultado.coeficiente_variacion;
        var coeficiente_correlacion = resultado.coeficiente_correlacion;
        $("#parametros_resultado_correlacion").attr("cadena_funcion_correlacion", cadena_funcion_correlacion);
        $("#parametros_resultado_correlacion").attr("error_estandar", error_estandar);
        $("#parametros_resultado_correlacion").attr("coeficiente_variacion", coeficiente_variacion);
        $("#parametros_resultado_correlacion").attr("coeficiente_correlacion", coeficiente_correlacion);

        // Se dibuja el informe
        var parametros = {
            id_grafica_correlacion: "grafica-correlacion",
            id_contenedor_tabla_funcion_correlacion: "contenedor-tabla-funcion-correlacion"};
        dibuja_informe_sensores_correlacion(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);

        // Se habilita el botón para añadir una línea base
        $('#boton_sensores_correlacion_mostrar_ventana_anyadir_linea_base').removeAttr('disabled');
    });
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del histograma del valor de un sensor
function dame_parametros_informe_sensores_histograma(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Clase de sensor
    var clase_sensor = $('#clase_sensor_sensores_histograma').val();
    if (clase_sensor == CLASE_NINGUNA) {
        jAlert(TLNT.Idiomas._('No hay clase seleccionada'));
        return (null);
    }

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_histograma').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_histograma :selected').text();

    // Campo y parámetros extra
    var campo = $('#campo_sensores_histograma').val();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_histograma').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return (null);
    }

    // Intervalo de valores y detalle
    var intervalo_valores = $('#intervalo_valores_sensores_histograma').val();
    var detalle = $('#detalle_sensores_histograma').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_histograma", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_histograma");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_histograma");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["detalle"] = detalle;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_histograma').val();
        var hora_inicio = $('#hora_inicio_sensores_histograma').val();
        var fecha_fin = $('#fecha_fin_sensores_histograma').val();
        var hora_fin = $('#hora_fin_sensores_histograma').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
        if (fechas_correctas == false) {
            return (null);
        }
        hora_inicio += ":00";
        hora_fin += ":59";
        var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
        var fecha_hora_fin = fecha_fin + ", " + hora_fin;

        parametros_informe["fecha_inicio"] = fecha_inicio;
        parametros_informe["fecha_fin"] = fecha_fin;
        parametros_informe["hora_inicio"] = hora_inicio;
        parametros_informe["hora_fin"] = hora_fin;
        parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
        parametros_informe["fecha_hora_fin"] = fecha_hora_fin;
    }

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros de la correlación entre valores de sensores
function dame_parametros_informe_sensores_correlacion(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Parámetros de sensores independientes
    var clases_sensores_independientes = [];
    var ids_sensores_independientes = [];
    var nombres_sensores_independientes = [];
    var campos_independientes = [];
    var parametros_extra_campos_independientes = [];
    var salir_funcion = false;
    for (var i = 0; i < NUMERO_SENSORES_INDEPENDIENTES_CORRELACION; i++) {
        var numero_sensor = i + 1;
        var id_lista_clases_sensor = "clase_sensor_independiente_" + numero_sensor + "_sensores_correlacion";
        var id_lista_sensores = "id_sensor_independiente_" + numero_sensor + "_sensores_correlacion";
        var id_lista_campos = "campo_independiente_" + numero_sensor + "_sensores_correlacion";
        var id_lista_parametros_extra_campo = "parametros_extra_campo_independiente_" + numero_sensor + "_sensores_correlacion";

        var clase_sensor = $('#' + id_lista_clases_sensor).val();
        if (clase_sensor != CLASE_NINGUNA) {
            clases_sensores_independientes.push(clase_sensor);

            // Identificador y nombre de sensor
            var id_sensor = $('#' + id_lista_sensores).val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor independiente seleccionado"));
                salir_funcion = true;
                return;
            }
            var nombre_sensor = $('#' + id_lista_sensores + ' :selected').text();
            ids_sensores_independientes.push(id_sensor);
            nombres_sensores_independientes.push(nombre_sensor);

            // Campo y parámetros extra
            var campo = $('#' + id_lista_campos).val();
            var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                salir_funcion = true;
                return;
            }
            campos_independientes.push(campo);
            parametros_extra_campos_independientes.push(parametros_extra_campo);
        };
    }
    if (salir_funcion == true) {
        return (null);
    }
    if (clases_sensores_independientes.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor independiente"));
        return (null);
	}

    // Clase de sensor dependiente
    var clase_sensor_dependiente = $('#clase_sensor_dependiente_sensores_correlacion').val();
    if ((clase_sensor_dependiente == CLASE_NINGUNA)) {
		jAlert(TLNT.Idiomas._("No hay clase dependiente seleccionada"));
        return (null);
	}

    // Identificador y nombre de sensor dependiente
    var id_sensor_dependiente = $('#id_sensor_dependiente_sensores_correlacion').val();
    if (id_sensor_dependiente == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor dependiente seleccionado"));
        return (null);
	}
    var nombre_sensor_dependiente = $('#id_sensor_dependiente_sensores_correlacion :selected').text();

    // Campo y parámetros extra de sensor dependiente
    var campo_dependiente = $('#campo_dependiente_sensores_correlacion').val();
    var parametros_extra_campo_dependiente = $('#parametros_extra_campo_dependiente_sensores_correlacion').val();
    var parametros_extra_campo_dependiente_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor_dependiente, campo_dependiente, parametros_extra_campo_dependiente);
    if (parametros_extra_campo_dependiente_correctos == false) {
        return (null);
    }

    // Comprobación de campos diferentes
    for (var i = 0; i < NUMERO_SENSORES_INDEPENDIENTES_CORRELACION; i++) {
        if ((clases_sensores_independientes[i] == clase_sensor_dependiente) &&
            (campos_independientes[i] == campo_dependiente) &&
            (nombres_sensores_independientes[i] == nombre_sensor_dependiente) &&
            (campos_independientes[i] == campo_dependiente) &&
            (parametros_extra_campos_independientes[i] == parametros_extra_campo_dependiente))
        {
            jAlert(TLNT.Idiomas._("Los valores y sensores seleccionados deben ser diferentes"));
            return (null);
        }
    }

    // Intervalo de valores y función de correlación
    var intervalo_valores = $('#intervalo_valores_sensores_correlacion').val();
    var funcion_correlacion = $('#funcion_correlacion_sensores_correlacion').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_correlacion", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_correlacion");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_correlacion");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Si hay más de un sensor independiente seleccionado se comprueba la función seleccionada
    if (ids_sensores_independientes.length > 1) {
        switch (funcion_correlacion) {
            case FUNCION_CORRELACION_AUTOMATICA:
            case FUNCION_CORRELACION_LINEAL: {
                break;
            }
            default: {
                jAlert(TLNT.Idiomas._("Función de correlación no disponible en correlación multivariable"));
                return (null);
            }
        }
    }

    // Función de correlación
    switch (funcion_correlacion) {
        case FUNCION_CORRELACION_AUTOMATICA: {
            funcion_correlacion = "";
            break;
        }
        case FUNCION_CORRELACION_LINEAL: {
            if (ids_sensores_independientes.length > 1) {
                funcion_correlacion = FUNCION_CORRELACION_MULTIVARIABLE_LINEAL;
            }
            break;
        }
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clases_sensores_independientes"] = clases_sensores_independientes;
    parametros_informe["ids_sensores_independientes"] = ids_sensores_independientes;
    parametros_informe["nombres_sensores_independientes"] = nombres_sensores_independientes;
    parametros_informe["campos_independientes"] = campos_independientes;
    parametros_informe["parametros_extra_campos_independientes"] = parametros_extra_campos_independientes;
    parametros_informe["clase_sensor_dependiente"] = clase_sensor_dependiente;
    parametros_informe["id_sensor_dependiente"] = id_sensor_dependiente;
    parametros_informe["nombre_sensor_dependiente"] = nombre_sensor_dependiente;
    parametros_informe["campo_dependiente"] = campo_dependiente;
    parametros_informe["parametros_extra_campo_dependiente"] = parametros_extra_campo_dependiente;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["funcion_correlacion"] = funcion_correlacion;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_correlacion').val();
        var hora_inicio = $('#hora_inicio_sensores_correlacion').val();
        var fecha_fin = $('#fecha_fin_sensores_correlacion').val();
        var hora_fin = $('#hora_fin_sensores_correlacion').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
        if (fechas_correctas == false) {
            return (null);
        }
        hora_inicio += ":00";
        hora_fin += ":59";
        var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
        var fecha_hora_fin = fecha_fin + ", " + hora_fin;

        parametros_informe["fecha_inicio"] = fecha_inicio;
        parametros_informe["fecha_fin"] = fecha_fin;
        parametros_informe["hora_inicio"] = hora_inicio;
        parametros_informe["hora_fin"] = hora_fin;
        parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
        parametros_informe["fecha_hora_fin"] = fecha_hora_fin;
    }

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


//
// Adición de líneas base desde el informe de correlación
//


// Muestra la ventana para añadir una línea base con los datos de la correlación
function boton_sensores_correlacion_mostrar_ventana_anyadir_linea_base(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    $.post("./src/modulos/ModulosWeb/ModuloSensores/Estadistica/muestra_ventana_anyadir_linea_base_correlacion.php", {},
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se muestra la ventana modal
        $('#ventana_modal').modal('show');
        TLNT.Navegacion.carga_ventana_modal(
            resultado.titulo,
            resultado.contenido,
            resultado.pie);

        // Eventos de ventanas modales
        TLNT.Navegacion.establece_eventos_ventanas_modales();
    });
}


// Muestra la correlación entre valores de sensores
function boton_sensores_correlacion_anyadir_linea_base() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Se recupera el nombre de la línea base
    var nombre_linea_base = $('#nombre_linea_base').val();
    if (comprueba_longitud_cadena(nombre_linea_base, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_linea_base").addClass('data-check-failed');
        return;
    }

    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_correlacion(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var clases_sensores_independientes = parametros_informe["clases_sensores_independientes"];
    var ids_sensores_independientes = parametros_informe["ids_sensores_independientes"];
    var nombres_sensores_independientes = parametros_informe["nombres_sensores_independientes"];
    var campos_independientes = parametros_informe["campos_independientes"];
    var parametros_extra_campos_independientes = parametros_informe["parametros_extra_campos_independientes"];
    var clase_sensor_dependiente = parametros_informe["clase_sensor_dependiente"];
    var id_sensor_dependiente = parametros_informe["id_sensor_dependiente"];
    var nombre_sensor_dependiente = parametros_informe["nombre_sensor_dependiente"];
    var campo_dependiente = parametros_informe["campo_dependiente"];
    var parametros_extra_campo_dependiente = parametros_informe["parametros_extra_campo_dependiente"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];

    // Horario semanal y exclusión de fechas (para añadir la línea base no se utiliza la inclusión de fechas)
    var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);
    var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);

    // Se recuperan la cadena de la función de correlación, el texto de error estandar y
    // los coeficientes de variación y correlación del resultado de la correlación
    var cadena_funcion_correlacion = $('#parametros_resultado_correlacion').attr("cadena_funcion_correlacion");
    var error_estandar = $('#parametros_resultado_correlacion').attr("error_estandar");
    var coeficiente_variacion = $('#parametros_resultado_correlacion').attr("coeficiente_variacion");
    var coeficiente_correlacion = $('#parametros_resultado_correlacion').attr("coeficiente_correlacion");

    // Se recupera la información de la correlación
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Estadistica/anyade_linea_base_correlacion.php", {
        nombre_linea_base: nombre_linea_base,
        clases_sensores_independientes: clases_sensores_independientes,
        ids_sensores_independientes: ids_sensores_independientes,
        nombres_sensores_independientes: nombres_sensores_independientes,
        campos_independientes: campos_independientes,
        parametros_extra_campos_independientes: parametros_extra_campos_independientes,
        clase_sensor_dependiente: clase_sensor_dependiente,
        id_sensor_dependiente: id_sensor_dependiente,
        nombre_sensor_dependiente: nombre_sensor_dependiente,
        campo_dependiente: campo_dependiente,
        parametros_extra_campo_dependiente: parametros_extra_campo_dependiente,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        intervalo_valores: intervalo_valores,
        cadena_horario_semanal: cadena_horario_semanal,
        cadena_exclusion_fechas: cadena_exclusion_fechas,
        cadena_funcion_correlacion: cadena_funcion_correlacion,
        error_estandar: error_estandar,
        coeficiente_variacion: coeficiente_variacion,
        coeficiente_correlacion: coeficiente_correlacion
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
    });
}
