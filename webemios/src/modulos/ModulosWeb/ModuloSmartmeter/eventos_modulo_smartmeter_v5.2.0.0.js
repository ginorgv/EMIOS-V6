/*
 * Módulo SmartMeter
 *
 */


// Botones de medición
function boton_smartmeter_medicion() {
    var params = this.id.split('__');
	var medicion_seleccionada = params[1];
    var seccion_medicion_seleccionada = params[2];

    // Si es la misma medición no se hace nada
    if (medicion_seleccionada == medicion) {
        jInfo(TLNT.Idiomas._("La medición seleccionada ya es la medición actual"));
        return;
    }

    // Se guarda la medición (en la variable global)
    medicion = medicion_seleccionada;

    // Si hay sección en la medición seleccionada, es que hay que cambiar de sección
    // (no está disponible la sección actual en la medición seleccionada, se cambia la sección actual)
    if (seccion_medicion_seleccionada !== undefined) {
        TLNT.Navegacion.seccion_actual = seccion_medicion_seleccionada;
    }

    // Se establece el botón de medición seleccionada (para que el 'feedback' al usuario sea inmediato)
    $(".btn-medicion-seleccionada").removeClass("btn-medicion-seleccionada").addClass("btn-medicion-no-seleccionada");
    $(this).addClass("btn-medicion-seleccionada");

    // Se recarga el menú de secciones
    // (y si es necesario se recarga la sección actual al modificar la URL con la nueva medición)
    TLNT.Navegacion.actualiza_menu_secciones();
}
