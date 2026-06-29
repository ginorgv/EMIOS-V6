// Devuelve los identificadores y los nombres de las tablas de la sección correspondiente
function dame_ids_nombres_tablas_seccion_modulo_localizaciones(seccion) {
    var ids_nombres_tablas = [];
    switch (seccion) {
        case SECCION_LOCALIZACIONES_PRINCIPAL: {
            ids_nombres_tablas.push(["tablaLocalizaciones", TLNT.Idiomas._("Localizaciones")]);
            ids_nombres_tablas.push(["tabla" + TIPO_NODO_AXON, TLNT.Idiomas._("Axones")]);
            break;
        }
        case SECCION_LOCALIZACIONES_INSTALACIONES: {
            ids_nombres_tablas.push(["tablaInstalaciones", TLNT.Idiomas._("Instalaciones")]);
            break;
        }
        case SECCION_LOCALIZACIONES_RATIOS: {
            ids_nombres_tablas.push(["tablaRatios", TLNT.Idiomas._("Ratios")]);
            break;
        }
    }
    return (ids_nombres_tablas);
}
