// Devuelve los identificadores y los nombres de las tablas de la sección correspondiente
function dame_ids_nombres_tablas_seccion_modulo_sensores(seccion) {
    var ids_nombres_tablas = [];
    switch (seccion) {
        case SECCION_SENSORES_PRINCIPAL: {
            ids_nombres_tablas.push(["tabla" + TIPO_NODO_SENSOR, TLNT.Idiomas._("Sensores")]);
            ids_nombres_tablas.push(["tabla" + TIPO_NODO_GRUPO_SENSORES, TLNT.Idiomas._("Grupos de sensores")]);
            anyade_nombres_tablas_procesado_datos_sensores(ids_nombres_tablas);
            break;
        }
        case SECCION_SENSORES_EVENTOS: {
            ids_nombres_tablas.push(["tablaEventos", TLNT.Idiomas._("Eventos")]);
            ids_nombres_tablas.push(["tablaHistoricoEventos", TLNT.Idiomas._("Histórico de eventos")]);
            break;
        }
    }
    return (ids_nombres_tablas);
}

