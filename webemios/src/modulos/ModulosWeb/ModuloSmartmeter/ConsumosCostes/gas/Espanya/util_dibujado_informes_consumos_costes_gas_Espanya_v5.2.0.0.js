//
// Funciones para el dibujado de los informes de consumos y costes (SmartMeter) (gas - España)
//


// Dibujado del informe de excesos de caudal
function dibuja_informe_smartmeter_excesos_caudal_gas_Espanya(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var grafica_caudales_sobrecaudales = datos.grafica_caudales_sobrecaudales;
    var max_caudal = datos.max_caudal;
    var caudal_diario_contratado = datos.caudal_diario_contratado;
    var tabla_sobrecaudales = datos.tabla_sobrecaudales;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_caudales_sobrecaudales = parametros.id_grafica_caudales_sobrecaudales;
    var id_contenedor_tabla_sobrecaudales = parametros.id_contenedor_tabla_sobrecaudales;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes_gas_Espanya(
        TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL,
        elementos_informe,
        parametros);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_caudales_sobrecaudales = parametros.mostrar_grafica_caudales_sobrecaudales;
    var mostrar_tabla_sobrecaudales = parametros.mostrar_tabla_sobrecaudales;

    // Se muestran los elementos visibles
    if (mostrar_grafica_caudales_sobrecaudales == true) {
        muestra_elemento(id_grafica_caudales_sobrecaudales);
    }
    if (mostrar_tabla_sobrecaudales == true) {
        muestra_elemento(id_contenedor_tabla_sobrecaudales);
    }

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

    // Mostrar indicadores de valores
    var numero_valores_grafica_caudales_sobrecaudales = dame_numero_maximo_valores_series_grafica(grafica_caudales_sobrecaudales);
    var mostrar_indicadores_valores = (numero_valores_grafica_caudales_sobrecaudales <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de caudales y sobrecaudales
    if (mostrar_grafica_caudales_sobrecaudales == true) {
        var lineas_referencia = [dame_linea_referencia_grafica_valores(
            caudal_diario_contratado,
            TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA,
            "rgba(255, 150, 150, 0.5)",
            TLNT.Idiomas._("Caudal diario contratado"))];
        muestra_grafica_temporal_lineas_puntos_valores(
            id_grafica_caudales_sobrecaudales,
            TLNT.Idiomas._("Caudales diarios y excesos de caudal diario") + " (" + TLNT.Idiomas._("kWh") + ")",
            [TLNT.Idiomas._("Caudales"), TLNT.Idiomas._("Excesos de caudal")],
            grafica_caudales_sobrecaudales, INTERVALO_VALORES_DIA,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_caudal, true,
            2, TLNT.Idiomas._("kWh"),
            lineas_referencia,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de sobrecaudales
    if (mostrar_tabla_sobrecaudales == true) {
        $("#" + id_contenedor_tabla_sobrecaudales).html(tabla_sobrecaudales);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_sobrecaudales = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_sobrecaudales);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_sobrecaudales, info_menu_contextual, TLNT.Idiomas._('Excesos de caudal'));
        }
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes_gas_Espanya(
    tipo_informe_smartmeter_consumos_costes_gas,
    elementos_informe,
    parametros) {
    switch (tipo_informe_smartmeter_consumos_costes_gas) {
        case TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL: {
            var mostrar_grafica_caudales_sobrecaudales = true;
            var mostrar_tabla_sobrecaudales = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ESPANYA_GRAFICA_CAUDALES_SOBRECAUDALES) == -1) {
                    mostrar_grafica_caudales_sobrecaudales = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ESPANYA_TABLA_SOBRECAUDALES) == -1) {
                    mostrar_tabla_sobrecaudales = false;
                }
            }
            parametros.mostrar_grafica_caudales_sobrecaudales = mostrar_grafica_caudales_sobrecaudales;
            parametros.mostrar_tabla_sobrecaudales = mostrar_tabla_sobrecaudales;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Smartmeter - Excesos de caudal)
function dibuja_elemento_plantilla_informe_smartmeter_excesos_caudal_gas_Espanya(
    info_elemento,
    datos_elemento,
    elementos_informe_elemento,
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
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de sensor seleccionado
    var sin_sensor_seleccionado = datos_elemento.sin_sensor_seleccionado;
    if (sin_sensor_seleccionado == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Parámetros del elemento
    var fecha_hora_inicio_consulta = parametros_elemento.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros_elemento.fecha_hora_fin_consulta;

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_caudales_sobrecaudales = prefijo_elemento + "grafica-caudales-sobrecaudales-excesos-caudal";
    var id_contenedor_tabla_sobrecaudales = prefijo_elemento + "contenedor-tabla-sobrecaudales-excesos-caudal";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        id_grafica_caudales_sobrecaudales: id_grafica_caudales_sobrecaudales,
        id_contenedor_tabla_sobrecaudales: id_contenedor_tabla_sobrecaudales};
    dibuja_informe_smartmeter_excesos_caudal_gas_Espanya(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


