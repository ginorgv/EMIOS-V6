// Devuelve los identificadores y los nombres de las tablas de la sección correspondiente
function dame_ids_nombres_tablas_seccion_modulo_actuadores(seccion) {
    var ids_nombres_tablas = [];
    switch (seccion) {
        case SECCION_ACTUADORES_PRINCIPAL: {
            ids_nombres_tablas.push(["tabla" + TIPO_NODO_ACTUADOR, TLNT.Idiomas._("Actuadores")]);
            ids_nombres_tablas.push(["tabla" + TIPO_NODO_GRUPO_ACTUADORES, TLNT.Idiomas._("Grupos de actuadores")]);
            break;
        }
        case SECCION_ACTUADORES_REGLAS: {
            ids_nombres_tablas.push(["tablaReglas", TLNT.Idiomas._("Reglas")]);
            ids_nombres_tablas.push(["tablaHistoricoReglas", TLNT.Idiomas._("Histórico de reglas")]);
            break;
        }
        case SECCION_ACTUADORES_PROGRAMACIONES: {
            ids_nombres_tablas.push(["tablaProgramaciones", TLNT.Idiomas._("Programaciones")]);
            break;
        }
    }
    return (ids_nombres_tablas);
}

