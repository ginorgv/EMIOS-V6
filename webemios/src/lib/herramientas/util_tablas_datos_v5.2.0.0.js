/*
 * Funciones de tablas de datos
 *
 */


// Devuelve información del menú contextual de las tablas de datos
function dame_info_menu_contextual_tablas_datos() {
    var exportacion_valores = exportacion_valores_sensores;
    var opciones_menu_contextual_graficas = [OPCION_MENU_CONTEXTUAL_GUARDAR_IMAGEN];
    if (exportacion_valores == true) {
        opciones_menu_contextual_graficas.push(OPCION_MENU_CONTEXTUAL_EXPORTAR_VALORES);
    }
    var info_menu_contextual_tablas_datos = {
        "tipo_origen": TIPO_ORIGEN_MENU_CONTEXTUAL_TABLA_DATOS,
        "opciones": opciones_menu_contextual_graficas};
    return (info_menu_contextual_tablas_datos);
}
