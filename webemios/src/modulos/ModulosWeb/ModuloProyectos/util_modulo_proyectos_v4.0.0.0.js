// Devuelve los identificadores y los nombres de las tablas de la sección correspondiente
function dame_ids_nombres_tablas_seccion_modulo_proyectos(seccion) {
    var ids_nombres_tablas = [];
    switch (seccion) {
        case SECCION_PROYECTOS_PRINCIPAL: {
            ids_nombres_tablas.push(["tablaProyectos", TLNT.Idiomas._("Proyectos")]);
            break;
        }
        case SECCION_PROYECTOS_LINEAS_BASE: {
            ids_nombres_tablas.push(["tablaLineasBase", TLNT.Idiomas._("Líneas base")]);
        }
    }
    return (ids_nombres_tablas);
}

