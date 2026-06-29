// Añade los nombres de tablas de procesado de datos de sensores
function anyade_nombres_tablas_procesado_datos_sensores(ids_nombres_tablas) {
    ids_nombres_tablas.push(["tablaOperacionesDatosSensores", TLNT.Idiomas._("Operaciones de datos de sensores (en ejecución)")]);
    ids_nombres_tablas.push(["tablaImportacionesValoresSensoresPendientes", TLNT.Idiomas._("Importaciones de valores de sensores pendientes")]);
    ids_nombres_tablas.push(["tablaHistoricoImportacionesValoresSensores", TLNT.Idiomas._("Histórico de importaciones de valores de sensores")]);
    ids_nombres_tablas.push(["tablaRecalculosValoresClaseHorarios", TLNT.Idiomas._("Recálculos de valores de clase") + " (" +  TLNT.Idiomas._("cuartohorarios") + ")"]);
    ids_nombres_tablas.push(["tablaRecalculosValoresClaseCuartohorarios", TLNT.Idiomas._("Recálculos de valores de clase") + " (" +  TLNT.Idiomas._("horarios") + ")"]);
    ids_nombres_tablas.push(["tablaSensoresProcesadoValoresAntiguosHorarios", TLNT.Idiomas._("Sensores de procesado") + " (" +  TLNT.Idiomas._("cuartohorarios") + ")"]);
    ids_nombres_tablas.push(["tablaSensoresProcesadoValoresAntiguosCuartohorarios", TLNT.Idiomas._("Sensores de procesado") + " (" +  TLNT.Idiomas._("horarios") + ")"]);
}
