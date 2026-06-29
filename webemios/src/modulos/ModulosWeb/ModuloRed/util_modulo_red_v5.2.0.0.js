// Devuelve los identificadores y los nombres de las tablas de la sección correspondiente
function dame_ids_nombres_tablas_seccion_modulo_red(seccion) {
    var ids_nombres_tablas = [];
    switch (seccion) {
        case SECCION_RED_PRINCIPAL: {
            ids_nombres_tablas.push(["tabla" + TIPO_NODO_DISPOSITIVO, TLNT.Idiomas._("Dispositivos")]);
            ids_nombres_tablas.push(["tabla" + TIPO_NODO_AXON, TLNT.Idiomas._("Axones")]);
            break;
        }
        case SECCION_RED_ALARMAS: {
            ids_nombres_tablas.push(["tablaAlarmas", TLNT.Idiomas._("Alarmas")]);
            break;
        }
        case SECCION_RED_ACCIONES_USUARIO: {
            ids_nombres_tablas.push(["tablaAccionesUsuario", TLNT.Idiomas._("Acciones")]);
            break;
        }
        case SECCION_RED_COMENTARIOS: {
            ids_nombres_tablas.push(["tablaComentarios", TLNT.Idiomas._("Comentarios")]);
            break;
        }
    }
    return (ids_nombres_tablas);
}
