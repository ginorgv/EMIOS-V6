// Dibuja el elemento de una plantilla de informe (Comentarios)
function dibuja_elemento_plantilla_informe_comentarios(
    info_elemento,
    datos_elemento,
    elementos_informe_elemento,
    parametros_elemento,
    tipo_informe) {
    // Información del elemento
    var numero_elemento = info_elemento["numero_elemento"];

    // Comprobación de error
    var hay_error = (datos_elemento.res == "ERROR");
    if (hay_error == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).html(
            "<i class='icon-warning-sign color-rojo'></i> " + datos_elemento.msg);
        $("#elemento-error-datos-elemento" + numero_elemento).show();
        $("#elemento-sin-objetos-seleccionados-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de objetos seleccionados
    var sin_objetos_seleccionados = datos_elemento.sin_objetos_seleccionados;
    if (sin_objetos_seleccionados == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-objetos-seleccionados-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-destino-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Contenedor de la tabla de comentarios
    var id_contenedor_tabla_comentarios = prefijo_elemento + "contenedor-tabla-comentarios";

    // Datos del resultado
    var tabla_comentarios = datos_elemento.tabla_comentarios;

    // Se dibuja el elemento
    muestra_elemento(id_contenedor_tabla_comentarios);
    $("#" + id_contenedor_tabla_comentarios).html(tabla_comentarios);
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        var id_tabla_comentarios = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_comentarios);
        var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
        anyade_menu_contextual(id_tabla_comentarios, info_menu_contextual, TLNT.Idiomas._('Comentarios'));
        TLNT.Navegacion.establece_eventos_tablas_datos_informes_personal();
    }
}