function dame_filas_valores_tabla_datos(id_tabla_datos) {
    // Nota: Se ha añadido 'last' porque puede suceder que una tabla tenga otra tabla en los detalles
    // de una fila y también tenga 'valores_xml' (hay que coger siempre el último elemento 'valores_xml').
    var valores_xml = $("#" + id_tabla_datos + " .valores_xml").last().attr("valores");
    if (valores_xml === undefined) {
        return (null);
    }

    var elemento_doc_valores = $.parseXML(valores_xml);
    var doc_valores = $(elemento_doc_valores);

    var filas_valores = [];
    var texto = "";

    var fila_cabecera = [];
    var elemento_columnas = doc_valores.find('columnas');
    elemento_columnas.find('nombre').each(function() {
        texto = unescapeHtmlXml($(this).text());
        fila_cabecera.push(texto);
    });
    filas_valores.push(fila_cabecera);

    doc_valores.find('fila').each(function() {
        var fila_valores = [];
        var elemento_fila = $(this);
        elemento_fila.find('valor').each(function() {
            texto = unescapeHtmlXml($(this).text());
            fila_valores.push(texto);
        });
        filas_valores.push(fila_valores);
    });
    return (filas_valores);
}

