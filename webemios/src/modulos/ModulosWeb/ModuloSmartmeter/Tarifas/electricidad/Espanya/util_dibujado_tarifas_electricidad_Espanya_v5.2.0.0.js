//
// Funciones para el dibujado de "elementos" de tarifas
//


// Dibuja la instalación (Smartmeter)
function dibuja_instalacion_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe) {
    // Datos del resultado
    var cups = datos.cups;
    var hay_informacion_tarifa_electrica = datos.hay_informacion_tarifa_electrica;
    if (hay_informacion_tarifa_electrica == true) {
        var descripcion = datos.descripcion;
        var contrato = datos.contrato;
        var descripcion_tipo = datos.descripcion_tipo;
        var descripcion_contrato = datos.descripcion_contrato;
        var formula_precio_consumo = datos.formula_precio_consumo;
        var tabla_tramos_tarifa_electrica = datos.tabla_tramos_tarifa_electrica;
    }

    // Parámetros del elemento
    var fecha_inicio = parametros.fecha_inicio;
    var fecha_fin = parametros.fecha_fin;
    var id_cups = parametros.id_cups;
    var id_fecha_inicio = parametros.id_fecha_inicio;
    var id_fecha_fin = parametros.id_fecha_fin;
    var id_tipo = parametros.id_tipo;
    var id_descripcion = parametros.id_descripcion;
    var id_contrato = parametros.id_contrato;
    var id_titulo_formula_precio_consumo = parametros.id_titulo_formula_precio_consumo;
    var id_formula_precio_consumo = parametros.id_formula_precio_consumo;
    var id_contenedor_tabla_tramos_tarifa_electrica = parametros.id_contenedor_tabla_tramos_tarifa_electrica;

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
    if (hay_informacion_tarifa_electrica == true) {
        $("#" + id_descripcion).html(descripcion);
        $("#" + id_tipo).html(descripcion_tipo);
        $("#" + id_contrato).html(descripcion_contrato);
        if (contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH) {
            muestra_elementos([
                id_titulo_formula_precio_consumo,
                id_formula_precio_consumo]);
            $("#" + id_formula_precio_consumo).html(formula_precio_consumo + " (" + moneda + "/" + TLNT.Idiomas._("MWh") + ")");
        }
        else {
            oculta_elementos([
                id_titulo_formula_precio_consumo,
                id_formula_precio_consumo]);
        }
        muestra_elemento(id_contenedor_tabla_tramos_tarifa_electrica);
        $("#" + id_contenedor_tabla_tramos_tarifa_electrica).html(tabla_tramos_tarifa_electrica);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_tramos_tarifa_electrica = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_tramos_tarifa_electrica);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_tramos_tarifa_electrica, info_menu_contextual, TLNT.Idiomas._('Tramos de tarifa eléctrica'));
        }
    }
    else {
        $("#" + id_descripcion).html(TLNT.Idiomas._("ND"));
        $("#" + id_tipo).html(TLNT.Idiomas._("ND"));
        $("#" + id_contrato).html(TLNT.Idiomas._("ND"));
        oculta_elementos([
            id_titulo_formula_precio_consumo,
            id_formula_precio_consumo,
            id_contenedor_tabla_tramos_tarifa_electrica]);
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Smartmeter - Instalación)
function dibuja_elemento_plantilla_informe_smartmeter_instalacion_electricidad_Espanya(
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
    var id_contrato = prefijo_elemento + "contrato-instalacion";
    var id_titulo_formula_precio_consumo = prefijo_elemento + "titulo-formula-precio-consumo-instalacion";
    var id_formula_precio_consumo = prefijo_elemento + "formula-precio-consumo-instalacion";
    var id_contenedor_tabla_tramos_tarifa_electrica = prefijo_elemento + "contenedor-tabla-tramos-tarifa-electrica-instalacion";

    var parametros = {
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin,
        id_cups: id_cups,
        id_fecha_inicio: id_fecha_inicio,
        id_fecha_fin: id_fecha_fin,
        id_descripcion: id_descripcion,
        id_tipo: id_tipo,
        id_contrato: id_contrato,
        id_titulo_formula_precio_consumo: id_titulo_formula_precio_consumo,
        id_formula_precio_consumo: id_formula_precio_consumo,
        id_contenedor_tabla_tramos_tarifa_electrica: id_contenedor_tabla_tramos_tarifa_electrica};
    dibuja_instalacion_electricidad_Espanya(
        parametros,
        datos_elemento,
        tipo_informe);
}
