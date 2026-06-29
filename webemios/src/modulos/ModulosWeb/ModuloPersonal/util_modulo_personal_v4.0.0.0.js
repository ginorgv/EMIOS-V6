// Devuelve los identificadores y los nombres de las tablas de la sección correspondiente
function dame_ids_nombres_tablas_seccion_modulo_personal(seccion) {
    var ids_nombres_tablas = [];
    switch (seccion) {
        case SECCION_PERSONAL_PLANTILLAS_INFORMES: {
            ids_nombres_tablas.push(["tablaPlantillasInformes", TLNT.Idiomas._("Plantillas de informes")]);
            break;
        }
        case SECCION_PERSONAL_INFORMES_AUTOMATICOS: {
            ids_nombres_tablas.push(["tablaInformesAutomaticos", TLNT.Idiomas._("Informes automáticos")]);
            break;
        }
    }
    return (ids_nombres_tablas);
}
