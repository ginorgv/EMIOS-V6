//
// Funciones de parámetros 'complejos'
//


// Devuelve la cadena de horario semanal
function dame_cadena_horario_semanal(horario_semanal) {
    if (horario_semanal == null) {
        return ("");
    }

    // Formato de cadena (sin espacios):
    // P.e. Lunes de 00:00 a 01:00 y 02:00 a 03:00 y martes de 00:23:59 ->
    // "1|1|0|0|0|0|0, 00:00:00-00:59:59|02:00:00-02:59:59, 00:00:00-00:23:59,,,,,"
    var cadena_selecciones_dias_semana = horario_semanal.selecciones_dias_semana.join(SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES);
    var cadenas_periodos_dias_semana = [];
    for (var i = 0; i < 7; i++) {
        var cadena_periodos_dia_semana = "";
        for (var j = 0; j < horario_semanal.periodos_dias_semana[i].length; j++) {
            if (j > 0) {
                cadena_periodos_dia_semana += SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES;
            }
            var periodo_dia_semana = horario_semanal.periodos_dias_semana[i][j];
            cadena_periodos_dia_semana += periodo_dia_semana[0] + SEPARADOR_HORAS + periodo_dia_semana[1];
        }
        cadenas_periodos_dias_semana.push(cadena_periodos_dia_semana);
    }
    var cadena_horario_semanal = cadena_selecciones_dias_semana + SEPARADOR_PARAMETROS_SIMPLES +
        cadenas_periodos_dias_semana.join(SEPARADOR_PARAMETROS_SIMPLES);
    return (cadena_horario_semanal);
}


// Devuelve el horario semanal
function dame_horario_semanal(cadena_horario_semanal) {
    if (cadena_horario_semanal == "") {
        return (null);
    }

    var cadenas_elementos_horario_semanal = cadena_horario_semanal.split(SEPARADOR_PARAMETROS_SIMPLES);
    var cadena_selecciones_dias_semana = cadenas_elementos_horario_semanal[0];
    var selecciones_dias_semana = cadena_selecciones_dias_semana.split(SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES);
    selecciones_dias_semana = selecciones_dias_semana.map(function(i) {
        return parseInt(i);
    });
    var periodos_dias_semana = [];
    for (var i = 0; i < 7; i++) {
        var periodos_dia_semana = [];
        var cadena_periodos_dia_semana = cadenas_elementos_horario_semanal[i + 1];
        var cadenas_periodos_dia_semana = cadena_periodos_dia_semana.split(SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES);
        for (var j = 0; j < cadenas_periodos_dia_semana.length; j++) {
            var elementos_periodo_dia_semana = cadenas_periodos_dia_semana[j].split(SEPARADOR_HORAS);
            periodos_dia_semana.push(elementos_periodo_dia_semana);
        }
        periodos_dias_semana.push(periodos_dia_semana);
    }
    var horario_semanal = {
        correcto: true,
        selecciones_dias_semana: selecciones_dias_semana,
        periodos_dias_semana: periodos_dias_semana};
    return (horario_semanal);
}


// Recupera el horario semanal de los controles especificados
function dame_horario_semanal_controles(id_controles, permitir_ningun_dia_seleccionado) {
    var horario_semanal = {
        "correcto": true,
        "selecciones_dias_semana": [],
        "periodos_dias_semana": []
    };
    var dias_semana = ["lunes", "martes", "miercoles", "jueves", "viernes", "sabado", "domingo"];
    var algun_dia_semana_seleccionado = false;
    for (var i = 0; i < dias_semana.length; i++) {
        var dia_semana_seleccionado = ($('#' + dias_semana[i] + '_' + id_controles + ':checked').length > 0);
        if (dia_semana_seleccionado == true) {
            algun_dia_semana_seleccionado = true;
            horario_semanal.selecciones_dias_semana.push(VALOR_SI);
        }
        else {
            horario_semanal.selecciones_dias_semana.push(VALOR_NO);
        }
        var periodos_dia_semana = [];
        var cadena_periodos_dia_semana = $('#periodos_' + dias_semana[i] + '_' + id_controles).val();
        if (comprueba_longitud_cadena(cadena_periodos_dia_semana, NUMERO_MAXIMO_CARACTERES_PERIODOS_DIA_SEMANA_HORARIO_SEMANAL) == false) {
            $('#periodos_' + dias_semana[i] + '_' + id_controles).addClass('data-check-failed');
            horario_semanal.correcto = false;
            return (horario_semanal);
        }
        var cadenas_periodos_dia_semana = cadena_periodos_dia_semana.split(",");
        for (var j = 0; j < cadenas_periodos_dia_semana.length; j++) {
            var horas_periodo_dia_semana = cadenas_periodos_dia_semana[j].split("-");
            if (horas_periodo_dia_semana.length != 2) {
                jAlert(TLNT.Idiomas._("Horario semanal incorrecto"));
                horario_semanal.correcto = false;
                return (horario_semanal);
            }
            var hora_inicio_periodo_dia_semana = horas_periodo_dia_semana[0].trim();
            var hora_fin_periodo_dia_semana = horas_periodo_dia_semana[1].trim();
            var hora_inicio_periodo_dia_semana_valida = (PATRON_HORA_MINUTO.test(hora_inicio_periodo_dia_semana));
            var hora_fin_periodo_dia_semana_valida = (PATRON_HORA_MINUTO.test(hora_fin_periodo_dia_semana));
            if ((hora_inicio_periodo_dia_semana_valida == false) || (hora_fin_periodo_dia_semana_valida == false)) {
                jAlert(TLNT.Idiomas._("Horario semanal incorrecto"));
                horario_semanal.correcto = false;
                return (horario_semanal);
            }
            if (hora_inicio_periodo_dia_semana > hora_fin_periodo_dia_semana) {
                jAlert(TLNT.Idiomas._("Los rangos de horas en horario semanal son incorrectos"));
                horario_semanal.correcto = false;
                return (horario_semanal);
            }
            var periodo_dia_semana = [hora_inicio_periodo_dia_semana + ":00", hora_fin_periodo_dia_semana + ":59"];
            periodos_dia_semana.push(periodo_dia_semana);
        }
        horario_semanal.periodos_dias_semana.push(periodos_dia_semana);
    }
    if ((permitir_ningun_dia_seleccionado == false) && (algun_dia_semana_seleccionado == false)) {
        jAlert(TLNT.Idiomas._("Debe seleccionar al menos un día de la semana"));
        horario_semanal.correcto = false;
    }
    return (horario_semanal);
}


// Devuelve la cadena de fechas
function dame_cadena_fechas(fechas) {
    if (fechas == null) {
        return ("");
    }

    // Formato de cadena (sin espacios)
    // (formatos de fecha de base de datos - para poder cambiar el formato de fecha de una red):
    // P.e. 01/01/2016 y del 06/01/2016 al 10/01/2016 (seleccionado) ->
    // "1, 2016-01-01|2016-01-06_2016-01-10"
    var cadena_seleccion = fechas.seleccion;
    var cadena_periodos_fechas = "";
    for (var i = 0; i < fechas.periodos_fechas.length; i++) {
        if (i > 0) {
            cadena_periodos_fechas += SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES;
        }
        var periodo_fechas = fechas.periodos_fechas[i];
        var cadena_periodo_fechas = null;
        if (periodo_fechas[0] == periodo_fechas[1]) {
            cadena_periodo_fechas = periodo_fechas[0];
        }
        else {
            cadena_periodo_fechas = periodo_fechas[0] + SEPARADOR_FECHAS + periodo_fechas[1];
        }
        cadena_periodos_fechas += cadena_periodo_fechas;
    }
    var cadena_periodos_dias_anyo = "";
    for (var i = 0; i < fechas.periodos_dias_anyo.length; i++) {
        if (i > 0) {
            cadena_periodos_dias_anyo += SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES;
        }
        var periodo_dias_anyo = fechas.periodos_dias_anyo[i];
        var cadena_periodo_dias_anyo = null;
        if (periodo_dias_anyo[0] == periodo_dias_anyo[1]) {
            cadena_periodo_dias_anyo = periodo_dias_anyo[0];
        }
        else {
            cadena_periodo_dias_anyo = periodo_dias_anyo[0] + SEPARADOR_FECHAS + periodo_dias_anyo[1];
        }
        cadena_periodos_dias_anyo += cadena_periodo_dias_anyo;
    }
    var cadena_fechas = [
        cadena_seleccion,
        cadena_periodos_fechas,
        cadena_periodos_dias_anyo].join(SEPARADOR_PARAMETROS_SIMPLES);
    return (cadena_fechas);
}


// Devuelve las fechas
function dame_fechas(cadena_fechas) {
    if (cadena_fechas == "") {
        return (null);
    }

    var cadenas_elementos_fechas = cadena_fechas.split(SEPARADOR_PARAMETROS_SIMPLES);
    var cadena_seleccion = cadenas_elementos_fechas[0];
    var cadena_periodos_fechas = cadenas_elementos_fechas[1];
    var cadena_periodos_dias_anyo = cadenas_elementos_fechas[2];
    var seleccion = parseInt(cadena_seleccion);
    var periodos_fechas = [];
    if (cadena_periodos_fechas != "") {
        var cadenas_periodos_fechas = cadena_periodos_fechas.split(SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES);
        for (var i = 0; i < cadenas_periodos_fechas.length; i++) {
            var elementos_periodo = cadenas_periodos_fechas[i].split(SEPARADOR_FECHAS);
            if (elementos_periodo.length == 1) {
                elementos_periodo.push(elementos_periodo[0]);
            }
            periodos_fechas.push(elementos_periodo);
        }
    }
    var periodos_dias_anyo = [];
    if (cadena_periodos_dias_anyo != "") {
        var cadenas_periodos_dias_anyo = cadena_periodos_dias_anyo.split(SEPARADOR_ELEMENTOS_PARAMETROS_SIMPLES);
        for (var i = 0; i < cadenas_periodos_dias_anyo.length; i++) {
            var elementos_periodo = cadenas_periodos_dias_anyo[i].split(SEPARADOR_FECHAS);
            if (elementos_periodo.length == 1) {
                elementos_periodo.push(elementos_periodo[0]);
            }
            periodos_dias_anyo.push(elementos_periodo);
        }
    }
    var fechas = {
        correcto: true,
        seleccion: seleccion,
        periodos_fechas: periodos_fechas,
        periodos_dias_anyo: periodos_dias_anyo};
    return (fechas);
}


// Recupera las fechas de los controles especificados
function dame_fechas_controles(id_controles){
    var fechas = {
        "correcto": true,
        "seleccion": null,
        "periodos_fechas": [],
        "periodos_dias_anyo": []
    };

    var fechas_seleccionado = ($('#' + id_controles + ':checked').length == 1);
    if (fechas_seleccionado == true) {
        fechas.seleccion = VALOR_SI;
    }
    else {
        fechas.seleccion = VALOR_NO;
    }
    var cadena_periodos = $('#periodos_' + id_controles).val();
    if (comprueba_longitud_cadena(cadena_periodos, NUMERO_MAXIMO_CARACTERES_PERIODOS_FECHAS) == false) {
        $('#periodos_' + id_controles).addClass('data-check-failed');
        fechas.correcto = false;
        return (fechas);
    }
    if (cadena_periodos != "") {
        var cadenas_periodos = cadena_periodos.split(',');
        for (var i = 0; i < cadenas_periodos.length; i++) {
            var fechas_periodo_fechas = cadenas_periodos[i].split("-");
            var numero_fechas_periodo_fechas = fechas_periodo_fechas.length;
            if ((numero_fechas_periodo_fechas != 1) && (numero_fechas_periodo_fechas != 2)) {
                jAlert(TLNT.Idiomas._("Fechas incorrectas"));
                fechas.correcto = false;
                return (fechas);
            }
            var fecha_hora_inicio_periodo = fechas_periodo_fechas[0].trim().split(" ");
            var fecha_inicio_periodo = fecha_hora_inicio_periodo[0].trim();
            var hora_inicio_periodo = null;
            if (fecha_hora_inicio_periodo.length > 1) {
                hora_inicio_periodo = fecha_hora_inicio_periodo[1].trim();
            }
            var fecha_fin_periodo = null;
            var hora_fin_periodo = null;
            if (numero_fechas_periodo_fechas == 1) {
                fecha_fin_periodo = fecha_inicio_periodo;
                hora_fin_periodo = hora_inicio_periodo;
            }
            else {
                var fecha_hora_fin_periodo = fechas_periodo_fechas[1].trim().split(" ");
                fecha_fin_periodo = fecha_hora_fin_periodo[0].trim();
                var hora_fin_periodo = null;
                if (fecha_hora_fin_periodo.length > 1) {
                    hora_fin_periodo = fecha_hora_fin_periodo[1].trim();
                }
            }

            var fechas_validas = false;
            var dias_anyo_validos = false;

            fechas_validas = (((dame_fecha_valida(fecha_inicio_periodo, formato_fecha_local) == true) && (dame_fecha_valida(fecha_fin_periodo, formato_fecha_local) == true)) &&
                ((dame_hora_valida(hora_inicio_periodo) == true) && (dame_hora_valida(hora_fin_periodo) == true)));
            if (fechas_validas == false) {
                dias_anyo_validos = ((dame_dia_anyo_valido(fecha_inicio_periodo, formato_dia_anyo_local) == true) && (dame_dia_anyo_valido(fecha_fin_periodo, formato_dia_anyo_local) == true));
            }
            if ((fechas_validas == false) && (dias_anyo_validos == false)) {
                jAlert(TLNT.Idiomas._("Fechas incorrectas"));
                fechas.correcto = false;
                return (fechas);
            }

            if (fechas_validas == true) {
                var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio_periodo, hora_inicio_periodo, fecha_fin_periodo, hora_fin_periodo);
                if (fechas_correctas == false) {
                    fechas.correcto = false;
                    return (fechas);
                }

                // Nota: El formato de fechas es el formato de base de datos
                var fecha_inicio_periodo_base_datos = convierte_formato_fecha(fecha_inicio_periodo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);
                var fecha_hora_inicio_periodo_base_datos = fecha_inicio_periodo_base_datos;
                if (hora_inicio_periodo != null) {
                    fecha_hora_inicio_periodo_base_datos += " " + hora_inicio_periodo;
                }
                var fecha_fin_periodo_base_datos = convierte_formato_fecha(fecha_fin_periodo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);
                var fecha_hora_fin_periodo_base_datos = fecha_fin_periodo_base_datos;
                if (hora_fin_periodo != null) {
                    fecha_hora_fin_periodo_base_datos += " " + hora_fin_periodo;
                }
                var periodo = [fecha_hora_inicio_periodo_base_datos, fecha_hora_fin_periodo_base_datos];
                fechas.periodos_fechas.push(periodo);
            }
            if (dias_anyo_validos == true) {
                var dias_anyo_correctos = comprueba_dias_anyo_inicio_fin_correctos(fecha_inicio_periodo, fecha_fin_periodo);
                if (dias_anyo_correctos == false) {
                    fechas.correcto = false;
                    return (fechas);
                }

                // Nota: El formato de días anuales es el formato de base de datos
                // (cas cadenas con el formato de base de datos se pueden comparar para saber cual es anterior (primero mes y luego día))
                var dia_anyo_inicio_periodo_base_datos = convierte_formato_dia_anyo(fecha_inicio_periodo, formato_dia_anyo_local_jquery_ui, FORMATO_DIA_ANYO_BASE_DATOS_JQUERY_UI);
                var dia_anyo_fin_periodo_base_datos = convierte_formato_dia_anyo(fecha_fin_periodo, formato_dia_anyo_local_jquery_ui, FORMATO_DIA_ANYO_BASE_DATOS_JQUERY_UI);
                if (dia_anyo_inicio_periodo_base_datos > dia_anyo_fin_periodo_base_datos) {
                    jAlert(TLNT.Idiomas._("Los rangos de fechas son incorrectos"));
                    fechas.correcto = false;
                    return (fechas);
                }

                // Se añade el periodo
                var periodo = [dia_anyo_inicio_periodo_base_datos, dia_anyo_fin_periodo_base_datos];
                fechas.periodos_dias_anyo.push(periodo);
            }
        }
    }
    return (fechas);
}


// Devuelve la cadena de agrupaciones de días de la semana
function dame_cadena_agrupaciones_dias_semana(agrupaciones_dias_semana) {
    // Formato de cadena (sin espacios)
    // "1-2-3-4-5,6-7"
    var cadena_agrupaciones_dias_semana = "";
    for (var i = 0; i < agrupaciones_dias_semana.agrupaciones_dias.length; i++) {
        if (i > 0) {
            cadena_agrupaciones_dias_semana += SEPARADOR_PARAMETROS_SIMPLES;
        }
        var agrupacion_dias = agrupaciones_dias_semana.agrupaciones_dias[i];
        for (var j = 0; j < agrupacion_dias.length; j++) {
            if (j > 0) {
                cadena_agrupaciones_dias_semana += SEPARADOR_DIAS_SEMANA;
            }
            cadena_agrupaciones_dias_semana += agrupacion_dias[j];
        }
    }
    return (cadena_agrupaciones_dias_semana);
}


// Devuelve las agrupaciones de días de la semana
function dame_agrupaciones_dias_semana(cadena_agrupaciones_dias_semana) {
    var agrupaciones_dias = [];
    if (cadena_agrupaciones_dias_semana != "") {
        var cadenas_agrupaciones_dias = cadena_agrupaciones_dias_semana.split(SEPARADOR_PARAMETROS_SIMPLES);
        for (var i = 0; i < cadenas_agrupaciones_dias.length; i++) {
            var dias = cadenas_agrupaciones_dias[i].split(SEPARADOR_DIAS_SEMANA);
            agrupaciones_dias.push(dias);
        }
    }
    var agrupaciones_dias_semana = {
        correcto: true,
        agrupaciones_dias: agrupaciones_dias};
    return (agrupaciones_dias_semana);
}


// Recupera las agrupaciones de días de semana del control especificado
function dame_agrupaciones_dias_semana_control(id_control){
    var agrupaciones_dias_semana = {
        "correcto": true,
        "agrupaciones_dias": []
    };

    var dias_utilizados = [];
    var cadena_agrupaciones_dias_semana = $('#' + id_control).val();
    cadena_agrupaciones_dias_semana = cadena_agrupaciones_dias_semana.replace(" ", "");
    if (cadena_agrupaciones_dias_semana != "") {
        var cadenas_agrupaciones_dias = cadena_agrupaciones_dias_semana.split(SEPARADOR_PARAMETROS_SIMPLES);
        for (var i = 0; i < cadenas_agrupaciones_dias.length; i++) {
            var agrupacion_dias = cadenas_agrupaciones_dias[i].split(SEPARADOR_DIAS_SEMANA);
            for (var j = 0; j < agrupacion_dias.length; j++) {
                var dia = agrupacion_dias[j].trim();
                if (dias_utilizados.indexOf(dia) != -1) {
                    jAlert(TLNT.Idiomas._("Agrupaciones de días de la semana incorrectos"));
                    agrupaciones_dias_semana.correcto = false;
                    return (agrupaciones_dias_semana);
                }
                dias_utilizados.push(dia);
            }
            agrupaciones_dias_semana.agrupaciones_dias.push(agrupacion_dias);
        }
        if (dias_utilizados.length != 7) {
            jAlert(TLNT.Idiomas._("Agrupaciones de días de la semana incorrectos"));
            agrupaciones_dias_semana.correcto = false;
            return (agrupaciones_dias_semana);
        }
    }
    return (agrupaciones_dias_semana);
}


//
// Funciones de parámetros extra para la carga de secciones de los módulos
//


function dame_parametros_extra_modulo_seccion(modulo, seccion) {
    var parametros_extra = {};

    // Selección de localización actual desplegada
    // (sólo si es cambio de sección en el mismo módulo, para mantener el estado de selección de localización)
    if (TLNT.Navegacion.modulo_anterior == TLNT.Navegacion.modulo_actual) {
        if ($('#tabla-seleccion-localizacion-actual').length > 0) {
            var seleccion_localizacion_actual_desplegada = $('#tabla-seleccion-localizacion-actual .titulo-tabla-datos').attr('desplegado');
            parametros_extra["seleccion_localizacion_actual_desplegada"] = seleccion_localizacion_actual_desplegada;
        }
    }

    // Parámetros extra específicos de cada módulo
    switch (modulo) {
        case MODULO_SMARTMETER: {
            parametros_extra["medicion"] = medicion;
            break;
        }
        default: {
            break;
        }
    }
    return (parametros_extra);
}


//
// Funciones de comprobaciones
//


function comprueba_longitud_cadena(cadena, longitud_maxima_cadena) {
    var longitud_cadena = cadena.length;
    if (longitud_cadena > longitud_maxima_cadena) {
        jAlert(TLNT.Idiomas._("Número de caracteres máximo excedido") + ": " + longitud_cadena +
            " (" + TLNT.Idiomas._("máximo permitido") + ": " + longitud_maxima_cadena + ")");
        return (false);
    }
    return (true);
}


//
// Funciones auxiliares
//


function formatea_funcion_valores(funcion_valores) {
    funcion_valores = replaceAll(funcion_valores, " ", "");
    funcion_valores = replaceAll(funcion_valores, "+", " + ");
    funcion_valores = replaceAll(funcion_valores, "-", " - ");
    funcion_valores = replaceAll(funcion_valores, "*", " * ");
    funcion_valores = replaceAll(funcion_valores, "*  *", "**");
    funcion_valores = replaceAll(funcion_valores, "/", " / ");
    funcion_valores = replaceAll(funcion_valores, "if", " if ");
    funcion_valores = replaceAll(funcion_valores, "else", " else ");
    funcion_valores = funcion_valores.trim();
    return (funcion_valores);
}







