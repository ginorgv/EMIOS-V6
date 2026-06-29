// Carga el contenido de la página actual
$(document).ready(function () {
    TLNT.Navegacion.carga_contenido();
});


// Comportamiento para el cambio de página ('hashes')
// P.e.: para que funcione el "Adelante-Atras" del navegador
window.onhashchange = function() {
    // Se carga el contenido
    TLNT.Navegacion.carga_contenido();

    // Se desactivan los temporizadores
    if (temporizador_actualizacion_pagina !== null) {
        clearTimeout(temporizador_actualizacion_pagina);
        temporizador_actualizacion_pagina = null;
    }
    if (temporizador_actualizacion_fecha_hora_pestanya_widgets !== null) {
        clearTimeout(temporizador_actualizacion_fecha_hora_pestanya_widgets);
        temporizador_actualizacion_fecha_hora_pestanya_widgets = null;
    }
};


// Se oculta el menú contextual si se hace click en otra parte del documento que no sea ese menú
$(document).bind("mousedown", function(e) {
    if (!$(e.target).parents(".menu-contextual").length > 0) {
        $(".menu-contextual").hide(100);
    }
});
