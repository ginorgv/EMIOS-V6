function muestra_elemento(id_elemento) {
    $("#" + id_elemento).show();
}


function muestra_elementos(ids_elementos) {
    for (var i = 0; i < ids_elementos.length; i++) {
        var id_elemento = ids_elementos[i];
        $("#" + id_elemento).show();
    }
}


function oculta_elemento(id_elemento) {
    $("#" + id_elemento).hide();
}


function oculta_elementos(ids_elementos) {
    for (var i = 0; i < ids_elementos.length; i++) {
        var id_elemento = ids_elementos[i];
        $("#" + id_elemento).hide();
    }
}


function vacia_elemento(id_elemento) {
    $("#" + id_elemento).html("");
}


function vacia_elementos(ids_elementos) {
    for (var i = 0; i < ids_elementos.length; i++) {
        var id_elemento = ids_elementos[i];
        $("#" + id_elemento).html("");
    }
}


function elimina_elemento(id_elemento) {
    $("#" + id_elemento).remove();
}


function elimina_elementos(ids_elementos) {
    for (var i = 0; i < ids_elementos.length; i++) {
        var id_elemento = ids_elementos[i];
        $("#" + id_elemento).remove();
    }
}


function anyade_clase_elemento(id_elemento, clase) {
    $("#" + id_elemento).addClass(clase);
}


function elimina_clase_elemento(id_elemento, clase) {
    $("#" + id_elemento).removeClass(clase);
}


function cambia_clase_elemento(id_elemento, clase) {
    $("#" + id_elemento).removeClass();
    $("#" + id_elemento).addClass(clase);
}


function sustituye_clase_elemento(id_elemento, clase_anterior, clase_nueva) {
    $("#" + id_elemento).removeClass(clase_anterior);
    $("#" + id_elemento).addClass(clase_nueva);
}


function dame_elemento_visible(id_elemento) {
    var elemento_visible = true;
    if ($('#' + id_elemento).length) {
        if (($('#' + id_elemento).css('display') == 'none') ||
            ($('#' + id_elemento).css("visibility") == "hidden")) {
            elemento_visible = false;
        }
    }
    else {
        elemento_visible = false;
    }
    return (elemento_visible);
}
