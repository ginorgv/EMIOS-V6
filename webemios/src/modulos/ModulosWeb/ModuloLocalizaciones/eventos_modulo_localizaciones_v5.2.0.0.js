/*
 * Módulo Localizaciones
 *
 */


//
// Funciones de topología de localización
//


// Muestra la topología de la localización seleccionada (al mostrarse la topología)
function boton_localizaciones_mostrar_topologia_localizacion_seleccionada() {
    // Se recupera la localización seleccionada
    var id_localizacion = $('#id_localizacion_localizaciones_seleccion_topologia_localizacion').val();
    if (id_localizacion == ID_NINGUNO) {
        // Se muestra el texto de que no hay localización seleccionada y se oculta el gráfico de la topología
        muestra_elemento("texto-topologia-localizacion");
        oculta_elemento("grafico-topologia-localizacion");
    }
    else {
        boton_localizaciones_actualizar_topologia_localizacion();
    }
}


// Realiza la selección de localización en la topología de localización
function boton_localizaciones_seleccion_topologia_localizacion() {
    boton_localizaciones_actualizar_topologia_localizacion();
}


// Actualiza la topología de localización
function boton_localizaciones_actualizar_topologia_localizacion() {
    // Se recupera la localización seleccionada
    var id_localizacion = $('#id_localizacion_localizaciones_seleccion_topologia_localizacion').val();
    if (id_localizacion == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._("No hay localización seleccionada"));
        return;
    }

    // Se oculta el texto de que no hay localización seleccionada y se muestra el gráfico de la topología
    oculta_elemento("texto-topologia-localizacion");
    muestra_elemento("grafico-topologia-localizacion");
    vacia_elemento("grafico-topologia-localizacion");

    // Se recupera la información de la topología de la localización seleccionada
    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/dame_info_topologia_localizacion.php", {
        id_localizacion: id_localizacion
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Nodo de la localización
        var nodo_localizacion = JSON.parse(resultado.info);

        // Se dibuja la topología de árbol y se establece el menú contextual
        var id_topologia_arbol = "grafico-topologia-localizacion";
        dibuja_topologia_arbol(
            id_topologia_arbol,
            NUMERO_NIVELES_NODOS_TOPOLOGIA_ARBOL_LOCALIZACION,
            0,
            nodo_localizacion);
        var info_menu_contextual = {
            "tipo_origen": TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_TOPOLOGIA_ARBOL,
            "opciones": [OPCION_MENU_CONTEXTUAL_GUARDAR_IMAGEN]};
        anyade_menu_contextual(id_topologia_arbol, info_menu_contextual, TLNT.Idiomas._("Topología de localización"));
    });
}


//
// Funciones de topología de instalación
//


// Muestra la topología de la instalación seleccionada (al mostrarse la topología)
function boton_localizaciones_mostrar_topologia_instalacion_seleccionada() {
    // Se recupera la instalación seleccionada
    var id_instalacion = $('#id_instalacion_localizaciones_seleccion_topologia_instalacion').val();
    if (id_instalacion == ID_NINGUNO) {
        // Se muestra el texto de que no hay instalación seleccionada y se oculta el gráfico de la topología
        muestra_elemento("texto-topologia-instalacion");
        oculta_elemento("grafico-topologia-instalacion");
    }
    else {
        boton_localizaciones_actualizar_topologia_instalacion();
    }
}


// Realiza la selección de instalación en la topología de instalación
function boton_localizaciones_seleccion_topologia_instalacion() {
    boton_localizaciones_actualizar_topologia_instalacion();
}


// Actualiza la topología de instalación
function boton_localizaciones_actualizar_topologia_instalacion() {
    // Se recupera la instalación seleccionada
    var id_instalacion = $('#id_instalacion_localizaciones_seleccion_topologia_instalacion').val();
    if (id_instalacion == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._("No hay instalación seleccionada"));
        return;
    }

    // Se oculta el texto de que no hay instalación seleccionada y se muestra el gráfico de la topología
    oculta_elemento("texto-topologia-instalacion");
    muestra_elemento("grafico-topologia-instalacion");
    vacia_elemento("grafico-topologia-instalacion");

    // Se recupera la información de la topología de la instalación seleccionada
    $.post("./src/modulos/ModulosWeb/ModuloLocalizaciones/dame_info_topologia_instalacion.php", {
        id_instalacion: id_instalacion
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Nodo de la instalación
        var nodo_instalacion = JSON.parse(resultado.info);

        // Se dibuja la topología de árbol y se establece el menú contextual
        var id_topologia_arbol = "grafico-topologia-instalacion";
        var numero_niveles_nodos = NUMERO_NIVELES_NODOS_TOPOLOGIA_ARBOL_INSTALACION;
        var numero_niveles_extra_nodos = 0;
        if (nodo_instalacion.numero_niveles_nodos > numero_niveles_nodos) {
            numero_niveles_extra_nodos = (nodo_instalacion.numero_niveles_nodos - numero_niveles_nodos);
        }
        dibuja_topologia_arbol(
            id_topologia_arbol,
            numero_niveles_nodos,
            numero_niveles_extra_nodos,
            nodo_instalacion);
        var info_menu_contextual = {
            "tipo_origen": TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_TOPOLOGIA_ARBOL,
            "opciones": [OPCION_MENU_CONTEXTUAL_GUARDAR_IMAGEN]};
        anyade_menu_contextual(id_topologia_arbol, info_menu_contextual, TLNT.Idiomas._("Topología de instalación"));
    });
}


//
// Funciones de mapa
//


// Muestra el mapa de localizaciones aplicando el filtro
function boton_localizaciones_filtro_localizaciones_mapa() {
    boton_actualizar_mapa();
}
