//
// Funciones para el dibujado de "elementos" de tarifas
//


// Dibuja la instalación (Smartmeter)
function dibuja_instalacion_gas_Espanya(
    parametros,
    datos,
    tipo_informe) {
    // Datos del resultado
    var cups = datos.cups;
    var hay_informacion_tarifa_gas = datos.hay_informacion_tarifa_gas;
    if (hay_informacion_tarifa_gas == true) {
        var descripcion = datos.descripcion;
        var descripcion_tipo = datos.descripcion_tipo;
        var tabla_parametros_tarifa_gas = datos.tabla_parametros_tarifa_gas;
    }

    // Parámetros del elemento
    var fecha_inicio = parametros.fecha_inicio;
    var fecha_fin = parametros.fecha_fin;
    var id_cups = parametros.id_cups;
    var id_fecha_inicio = parametros.id_fecha_inicio;
    var id_fecha_fin = parametros.id_fecha_fin;
    var id_descripcion = parametros.id_descripcion;
    var id_tipo = parametros.id_tipo;
    var id_contenedor_tabla_parametros_tarifa_gas = parametros.id_contenedor_tabla_parametros_tarifa_gas;

    // Flags según el tipo de informe
    var mostrar_animaciones = null;
    var anyadir_menus_contextuales = null;
    switch (tipo_informe) {
        case TIPO_INFORME_WEB_EMIOS: {
            mostrar_animaciones = true;
            anyadir_menus_contextuales = true;
            break;
        }
        case TIPO_INFORME_FICHERO: {
            mostrar_animaciones = false;
            anyadir_menus_contextuales = false;
            break;
        }
    }

    // Se dibuja el elemento
    $("#" + id_cups).html(cups);
    $("#" + id_fecha_inicio).html(fecha_inicio);
    $("#" + id_fecha_fin).html(fecha_fin);
    if (hay_informacion_tarifa_gas == true) {
        $("#" + id_descripcion).html(descripcion);
        $("#" + id_tipo).html(descripcion_tipo);
        muestra_elemento(id_contenedor_tabla_parametros_tarifa_gas);
        $("#" + id_contenedor_tabla_parametros_tarifa_gas).html(tabla_parametros_tarifa_gas);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_parametros_tarifa_gas = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_parametros_tarifa_gas);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_parametros_tarifa_gas, info_menu_contextual, TLNT.Idiomas._('Caudales y precios'));
        }
    }
    else {
        $("#" + id_descripcion).html(TLNT.Idiomas._("ND"));
        $("#" + id_tipo).html(TLNT.Idiomas._("ND"));
        oculta_elemento(id_contenedor_tabla_parametros_tarifa_gas);
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Smartmeter - Instalación)
function dibuja_elemento_plantilla_informe_smartmeter_instalacion_gas_Espanya(
    info_elemento,
    datos_elemento,
    parametros_elemento,
    tipo_informe) {
    // Información del elemento
    var numero_elemento = info_elemento["numero_elemento"];
    var parametros_tipo = info_elemento["parametros_tipo"];

    // Comprobación de error
    var hay_error = (datos_elemento.res == "ERROR");
    if (hay_error == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).html(
            "<i class='icon-warning-sign color-rojo'></i> " + datos_elemento.msg);
        $("#elemento-error-datos-elemento" + numero_elemento).show();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de sensor seleccionado
    var sin_sensor_seleccionado = datos_elemento.sin_sensor_seleccionado;
    if (sin_sensor_seleccionado == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Parámetros del elemento
    var fecha_inicio = parametros_elemento.fecha_inicio;
    var fecha_fin = parametros_elemento.fecha_fin;

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_cups = prefijo_elemento + "cups-instalacion";
    var id_fecha_inicio = prefijo_elemento + "fecha-inicio-instalacion";
    var id_fecha_fin = prefijo_elemento + "fecha-fin-instalacion";
    var id_descripcion = prefijo_elemento + "descripcion-instalacion";
    var id_tipo = prefijo_elemento + "tipo-instalacion";
    var id_contenedor_tabla_parametros_tarifa_gas = prefijo_elemento + "contenedor-tabla-parametros-tarifa-gas-instalacion";

    var parametros = {
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin,
        id_cups: id_cups,
        id_fecha_inicio: id_fecha_inicio,
        id_fecha_fin: id_fecha_fin,
        id_descripcion: id_descripcion,
        id_tipo: id_tipo,
        id_contenedor_tabla_parametros_tarifa_gas: id_contenedor_tabla_parametros_tarifa_gas};
    dibuja_instalacion_gas_Espanya(
        parametros,
        datos_elemento,
        tipo_informe);
}
