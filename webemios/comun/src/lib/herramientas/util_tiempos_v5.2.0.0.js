function convierte_formato_fecha_hora_a_formato_fecha_hora_python(formato_fecha_hora) {
    var formato_fecha_hora_python = formato_fecha_hora;
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "d", "%d");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "m", "%m");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "y", "%y");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "Y", "%Y");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "H", "%H");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "M", "%M");
    formato_fecha_hora_python = replaceAll(formato_fecha_hora_python, "S", "%S");
    return (formato_fecha_hora_python);
}


// http://stackoverflow.com/questions/5619202/converting-string-to-date-in-js
// http://stackoverflow.com/questions/6177975/how-to-validate-date-with-format-mm-dd-yyyy-in-javascript
function dame_fecha_valida(cadena_fecha, formato_fecha) {
    // Recupera el día, mes y año de la fecha (utilizando el formato de fecha)
    var delimitador = dame_delimitador_formato_fecha(formato_fecha);
    var formato_fecha = formato_fecha.toLowerCase();
    var elementos_formato = formato_fecha.split(delimitador);
    var elementos_fecha = cadena_fecha.split(delimitador);
    if (elementos_fecha.length != 3) {
        return (false);
    }
    var indice_dia = elementos_formato.indexOf("dd");
    var indice_mes = elementos_formato.indexOf("mm");
    var indice_anyo = elementos_formato.indexOf("yyyy");
    var cadena_dia = elementos_fecha[indice_dia];
    var cadena_mes = elementos_fecha[indice_mes];
    var cadena_anyo = elementos_fecha[indice_anyo];
    if ((PATRON_NUMERO_ENTERO.test(cadena_dia) == false) ||
        (PATRON_NUMERO_ENTERO.test(cadena_mes) == false) ||
        (PATRON_NUMERO_ENTERO.test(cadena_anyo) == false)) {
        return (false);
    }
    var dia = parseInt(cadena_dia);
    var mes = parseInt(cadena_mes);
    var anyo = parseInt(cadena_anyo);

    // Comprobación de rangos de años y meses
    if ((anyo < 1000) || (anyo > 3000) || (mes == 0) || (mes > 12)) {
        return (false);
    }

    // Comprobación de rangos de días
    var longitud_meses = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if ((anyo % 400 == 0) || ((anyo % 100 != 0) && (anyo % 4 == 0))) {
        longitud_meses[1] = 29;
    }
    if ((dia < 0) || (dia > longitud_meses[mes - 1])) {
        return (false);
    }

    // Comprobaciones correctas
    return (true);
}


// Nota: La cadena de hora es sin segundos
function dame_hora_valida(cadena_hora) {
    // La hora es opcional, si es 'null' es válida
    if (cadena_hora == null) {
        return (true);
    }

    // Recupera la hora, minuto y segundo de la cadena
    var delimitador = ":";
    var elementos_hora = cadena_hora.split(delimitador);
    var cadena_hora = elementos_hora[0];
    var cadena_minuto = elementos_hora[1];
    if ((PATRON_NUMERO_ENTERO.test(cadena_hora) == false) ||
        (PATRON_NUMERO_ENTERO.test(cadena_minuto) == false)) {
        return (false);
    }
    var hora = parseInt(cadena_hora);
    var minuto = parseInt(cadena_minuto);

    // Comprobación de rangos de horas y minutis
    if ((hora < 0) || (hora > 23)) {
        return (false);
    }
    if ((minuto < 0) || (minuto > 59)) {
        return (false);
    }

    // Comprobaciones correctas
    return (true);
}


function dame_dia_anyo_valido(cadena_dia_anyo, formato_dia_anyo) {
    // Recupera el día y mes de la cadena (utilizando el formato de dia / mes)
    var delimitador = dame_delimitador_formato_fecha(formato_dia_anyo);
    var formato_dia_anyo = formato_dia_anyo.toLowerCase();
    var elementos_formato = formato_dia_anyo.split(delimitador);
    var elementos_dia_anyo = cadena_dia_anyo.split(delimitador);
    if (elementos_dia_anyo.length != 2) {
        return (false);
    }
    var indice_dia = elementos_formato.indexOf("dd");
    var indice_mes = elementos_formato.indexOf("mm");
    var cadena_dia = elementos_dia_anyo[indice_dia];
    var cadena_mes = elementos_dia_anyo[indice_mes];
    if ((PATRON_NUMERO_ENTERO.test(cadena_dia) == false) ||
        (PATRON_NUMERO_ENTERO.test(cadena_mes) == false)) {
        return (false);
    }
    var dia = parseInt(cadena_dia);
    var mes = parseInt(cadena_mes);

    // Comprobación de rangos de meses
    if ((mes == 0) || (mes > 12)) {
        return (false);
    }

    // Comprobación de rangos de días
    var longitud_meses = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if ((dia < 0) || (dia > longitud_meses[mes - 1])) {
        return (false);
    }

    // Comprobaciones correctas
    return (true);
}


function convierte_formato_fecha(cadena_fecha, formato_origen, formato_destino) {
    var fecha = $.datepicker.parseDate(formato_origen, cadena_fecha);
    var cadena_fecha_destino = $.datepicker.formatDate(formato_destino, fecha);
    return (cadena_fecha_destino);
}


function convierte_formato_dia_anyo(cadena_dia_anyo, formato_origen, formato_destino) {
    // Recupera el día y mes de la cadena (utilizando el formato origen)
    var delimitador_origen = dame_delimitador_formato_fecha(formato_origen);
    var formato_origen = formato_origen.toLowerCase();
    var elementos_formato_origen = formato_origen.split(delimitador_origen);
    var elementos_dia_anyo = cadena_dia_anyo.split(delimitador_origen);
    if (elementos_dia_anyo.length != 2) {
        return (false);
    }
    var indice_dia_origen = elementos_formato_origen.indexOf("dd");
    var indice_mes_origen = elementos_formato_origen.indexOf("mm");
    var cadena_dia = elementos_dia_anyo[indice_dia_origen];
    if (cadena_dia.length == 1) {
        cadena_dia = "0" + cadena_dia;
    }
    var cadena_mes = elementos_dia_anyo[indice_mes_origen];
    if (cadena_mes.length == 1) {
        cadena_mes = "0" + cadena_mes;
    }

    // Se crea la cadena del día anual (utilizando el formato destino)
    var delimitador_destino = dame_delimitador_formato_fecha(formato_destino);
    var elementos_formato_destino = formato_destino.split(delimitador_destino);
    var indice_dia_destino = elementos_formato_destino.indexOf("dd");
    var indice_mes_destino = elementos_formato_destino.indexOf("mm");
    var cadena_dia_anyo_destino = null;
    if (indice_dia_destino < indice_mes_destino) {
        cadena_dia_anyo_destino = cadena_dia + delimitador_destino + cadena_mes;
    }
    else {
        cadena_dia_anyo_destino = cadena_mes + delimitador_destino + cadena_dia;
    }
    return (cadena_dia_anyo_destino);
}


function dame_dia_mes(cadena_dia_anyo, formato_dia_anyo) {
    // Recupera el día y mes de la cadena
    var delimitador = dame_delimitador_formato_fecha(formato_dia_anyo);
    var formato_dia_anyo = formato_dia_anyo.toLowerCase();
    var elementos_formato = formato_dia_anyo.split(delimitador);
    var elementos_fecha = cadena_dia_anyo.split(delimitador);
    var indice_dia = elementos_formato.indexOf("dd");
    var indice_mes = elementos_formato.indexOf("mm");
    var cadena_dia = elementos_fecha[indice_dia];
    var cadena_mes = elementos_fecha[indice_mes];
    var dia = parseInt(cadena_dia);
    var mes = parseInt(cadena_mes);
    return ([dia, mes]);
}


function dame_fecha_hora(cadena_fecha, cadena_hora) {
    var delimitador = ":";
    var elementos_hora = cadena_hora.split(delimitador);
    var hora = elementos_hora[0];
    var minuto = elementos_hora[1];
    var segundo = elementos_hora[2];
    var fecha_hora = $.datepicker.parseDate(formato_fecha_local_jquery_ui, cadena_fecha);
    fecha_hora.setHours(hora, minuto, segundo);
    return (fecha_hora);
}


function convierte_fecha_a_cadena(fecha, formato_fecha) {
    var cadena_fecha = $.datepicker.formatDate(formato_fecha, fecha);
    return (cadena_fecha);
}


function dame_cadena_hora(fecha) {
    var cadena_hora = anyade_cero_hora_minuto_segundo(fecha.getHours()) + ":" +
        anyade_cero_hora_minuto_segundo(fecha.getMinutes()) + ":" +
        anyade_cero_hora_minuto_segundo(fecha.getSeconds());
    return (cadena_hora);
}


function dame_dias_diferencia_fechas(cadena_fecha_inicio, cadena_fecha_fin) {
    var fecha_inicio = $.datepicker.parseDate(formato_fecha_local_jquery_ui, cadena_fecha_inicio);
    var fecha_fin = $.datepicker.parseDate(formato_fecha_local_jquery_ui, cadena_fecha_fin);
    var dias_diferencia_fechas = Math.floor(fecha_fin - fecha_inicio) / 86400000;
    return (dias_diferencia_fechas);
}


//
// Funciones de descripciones
//


// Devuelve los nombre de los días de la semana
function dame_nombres_dias_semana() {
    var nombres_dias_semana = [
        TLNT.Idiomas._("Lunes"),
        TLNT.Idiomas._("Martes"),
        TLNT.Idiomas._("Miércoles"),
        TLNT.Idiomas._("Jueves"),
        TLNT.Idiomas._("Viernes"),
        TLNT.Idiomas._("Sábado"),
        TLNT.Idiomas._("Domingo")
    ];
    return (nombres_dias_semana);
}


// Devuelve los nombres de los meses
function dame_nombres_meses() {
    var nombres_meses = [
        TLNT.Idiomas._("Enero"),
        TLNT.Idiomas._("Febrero"),
        TLNT.Idiomas._("Marzo"),
        TLNT.Idiomas._("Abril"),
        TLNT.Idiomas._("Mayo"),
        TLNT.Idiomas._("Junio"),
        TLNT.Idiomas._("Julio"),
        TLNT.Idiomas._("Agosto"),
        TLNT.Idiomas._("Septiembre"),
        TLNT.Idiomas._("Octubre"),
        TLNT.Idiomas._("Noviembre"),
        TLNT.Idiomas._("Diciembre")
    ];
    return (nombres_meses);
}


//
// Funciones auxiliares
//


function dame_delimitador_formato_fecha(formato_fecha) {
    var delimitador = null;
    var caracter = null;
    for (var i = 0; i < formato_fecha.length; i++) {
        caracter = formato_fecha[i];
        if (((caracter < "a") || (caracter > 'z')) &&
            ((caracter < "A") || (caracter > 'Z'))) {
            delimitador = caracter;
            break;
        }
    }
    return (delimitador);
}
