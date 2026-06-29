/*
 * Funciones de localizaciones (de varios módulos)
 *
 */


// Recupera el identificador del ratio seleccionado
function dame_id_ratio_seleccionado() {
    var id_ratio_seleccionado = ID_NINGUNO;
    if (($('#id_ratio_seleccion_localizacion_actual').length > 0) &&
        ($('#control_id_ratio_seleccion_localizacion_actual').is(':visible') == true))
    {
        id_ratio_seleccionado = $('#id_ratio_seleccion_localizacion_actual').val();
    }
    return (id_ratio_seleccionado);
}


// Recupera el nombre del ratio seleccionado
function dame_nombre_ratio_seleccionado() {
    var nombre_ratio_seleccionado = "";
    var id_ratio_seleccionado = dame_id_ratio_seleccionado();
    if (id_ratio_seleccionado != ID_NINGUNO) {
        nombre_ratio_seleccionado = $('#id_ratio_seleccion_localizacion_actual :selected').text();
    }
    return (nombre_ratio_seleccionado);
}
