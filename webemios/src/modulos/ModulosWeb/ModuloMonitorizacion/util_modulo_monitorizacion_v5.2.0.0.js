// Devuelve los identificadores y los nombres de las tablas de la sección correspondiente
function dame_ids_nombres_tablas_seccion_modulo_monitorizacion(seccion) {
    var ids_nombres_tablas = [];
    switch (seccion) {
        case SECCION_MONITORIZACION_PROCESADO: {
            ids_nombres_tablas.push(["tablaHistoricoProcesado", TLNT.Idiomas._("Histórico de procesado de datos")]);
            anyade_nombres_tablas_procesado_datos_sensores(ids_nombres_tablas);
            break;
        }
        case SECCION_MONITORIZACION_ALARMAS: {
            ids_nombres_tablas.push(["tablaAlarmas", TLNT.Idiomas._("Alarmas")]);
            break;
        }
    }
    return (ids_nombres_tablas);
}
