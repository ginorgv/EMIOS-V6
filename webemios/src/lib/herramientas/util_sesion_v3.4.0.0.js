function dame_parametros_comprobacion_sesion() {
    // Red actual
    var id_red_actual = $('#id_red_actual').attr('id_red');
    var parametros_sesion = {
        id_red_actual: id_red_actual
    };
    return (parametros_sesion);
}

