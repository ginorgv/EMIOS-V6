// Comprobación de que la fecha están dentro del rango de las fechas de inicio y fin
function comprueba_fecha_dentro_rango_fechas(fecha, hora, fecha_inicio, hora_inicio, fecha_fin, hora_fin) {
    if ($.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha) < $.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_inicio)) {
        jAlert(TLNT.Idiomas._("La fecha es menor que la fecha de inicio"));
        return (false);
    }
    else {
        if (+$.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha) == +$.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_inicio)) {
            if (hora < hora_inicio) {
                jAlert(TLNT.Idiomas._("La hora es menor que la hora de inicio"));
                return (false);
            }
        }
    }
    if ($.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha) > $.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_fin)) {
        jAlert(TLNT.Idiomas._("La fecha es mayor que la fecha de fin"));
        return (false);
    }
    else {
        if (+$.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha) == +$.datepicker.parseDate(formato_fecha_local_jquery_ui, fecha_fin)) {
            if (hora > hora_fin) {
                jAlert(TLNT.Idiomas._("La hora es mayor que la hora de fin"));
                return (false);
            }
        }
    }
    return (true);
}
