function actualiza_pie_pagina() {
    // Color de fondo y descripción de perfil (excepto en perfil estándar)
    var color_fondo = null;
    var descripcion_perfil = null;
    switch (TLNT.Navegacion.perfil_actual) {
        case PERFIL_USUARIO_ESTANDAR: {
            color_fondo = color_tema_oscuro;
            descripcion_perfil = "";
            break;
        }
        case PERFIL_USUARIO_ADMINISTRADOR: {
            color_fondo = COLOR_PIE_PAGINA_ADMINISTRADOR;
            descripcion_perfil = "[" + TLNT.Idiomas._("administrador") + "]";
            break;
        }
        case PERFIL_USUARIO_SUPERADMINISTRADOR: {
            color_fondo = COLOR_PIE_PAGINA_SUPERADMINISTRADOR;
            descripcion_perfil = "[" + TLNT.Idiomas._("superadministrador") + "]";
            break;
        }
    }
    $("#pie-pagina").css('background-color', color_fondo);
    $('#descripcion-perfil').html(descripcion_perfil);

    // Se actualiza también el el color de fondo del menú de módulos y se muestra
    // (al inicio oculto y se muestra ahora para que no se muestra con diferentes colores)
    $("#menu-modulos").css('background-color', color_fondo);
    if (pantalla_completa_activada == false) {
        $('#menu-modulos').show();
    }

    // Se muestra el pie de página
    $("#pie-pagina").show();
}

