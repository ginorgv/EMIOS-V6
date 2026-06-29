// Devuelve los identificadores y los nombres de las tablas de la sección correspondiente
function dame_ids_nombres_tablas_seccion_modulo_administracion(seccion) {
    var ids_nombres_tablas = [];
    switch (seccion) {
        case SECCION_ADMINISTRACION_REDES: {
            ids_nombres_tablas.push(["tablaClientes", TLNT.Idiomas._("Clientes")]);
            ids_nombres_tablas.push(["tabla" + TIPO_NODO_RED, TLNT.Idiomas._("Redes")]);
            break;
        }
        case SECCION_ADMINISTRACION_USUARIOS: {
            ids_nombres_tablas.push(["tablaLicencias", TLNT.Idiomas._("Licencias")]);
            ids_nombres_tablas.push(["tablaUsuarios", TLNT.Idiomas._("Usuarios")]);
            break;
        }
        case SECCION_ADMINISTRACION_PREFERENCIAS: {
            ids_nombres_tablas.push(["tablaPreferencias", TLNT.Idiomas._("Preferencias")]);
            break;
        }
    }
    return (ids_nombres_tablas);
}
