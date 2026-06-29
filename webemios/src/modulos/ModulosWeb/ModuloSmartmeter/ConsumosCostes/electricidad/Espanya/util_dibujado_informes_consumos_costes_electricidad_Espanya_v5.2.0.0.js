//
// Funciones para el dibujado de los informes de consumos y costes (SmartMeter) (electricidad - España)
//


// Dibujado del informe de excesos de potencia
function dibuja_informe_smartmeter_excesos_potencia_electricidad_Espanya(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var grafica_sobrepotencias_absolutas = datos.grafica_sobrepotencias_absolutas;
    var max_potencia = datos.max_potencia;
    var grafica_potencias_potencias_contratadas = datos.grafica_potencias_potencias_contratadas;
    var min_sobrepotencia_absoluta = datos.min_sobrepotencia_absoluta;
    var max_sobrepotencia_absoluta = datos.max_sobrepotencia_absoluta;
    var tabla_sobrepotencias_tramos = datos.tabla_sobrepotencias_tramos;
    var nombre_sensor = datos.nombre_sensor;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var granularidad = parametros.granularidad;
    var id_grafica_potencias_potencias_contratadas = parametros.id_grafica_potencias_potencias_contratadas;
    var id_grafica_sobrepotencias_absolutas = parametros.id_grafica_sobrepotencias_absolutas;
    var id_contenedor_tabla_sobrepotencias_tramos = parametros.id_contenedor_tabla_sobrepotencias_tramos;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes_electricidad_Espanya(
        TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA,
        null);

    // Parámetros (flags de mostrar elementos)
    var mostrar_grafica_potencias_potencias_contratadas = parametros.mostrar_grafica_potencias_potencias_contratadas;
    var mostrar_grafica_sobrepotencias_absolutas = parametros.mostrar_grafica_sobrepotencias_absolutas;
    var mostrar_tabla_sobrepotencias_tramos = parametros.mostrar_tabla_sobrepotencias_tramos;

    // Se muestran los elementos visibles
    if (mostrar_grafica_potencias_potencias_contratadas == true) {
        muestra_elemento(id_grafica_potencias_potencias_contratadas);
    }
    if (mostrar_grafica_sobrepotencias_absolutas == true) {
        muestra_elemento(id_grafica_sobrepotencias_absolutas);
    }
    if (mostrar_tabla_sobrepotencias_tramos == true) {
        muestra_elemento(id_contenedor_tabla_sobrepotencias_tramos);
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

    // Intervalo de valores
    var intervalo_valores = null;
    switch (granularidad) {
        case GRANULARIDAD_CUARTOHORARIA: {
            intervalo_valores = INTERVALO_VALORES_CUARTOHORA;
            break;
        }
        case GRANULARIDAD_HORARIA: {
            intervalo_valores = INTERVALO_VALORES_HORA;
            break;
        }
    }

    // Mostrar indicadores de valores
    var numero_valores_grafica_potencia_sobrepotencia = dame_numero_maximo_valores_series_grafica(grafica_potencias_potencias_contratadas);
    var mostrar_indicadores_valores = (numero_valores_grafica_potencia_sobrepotencia <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de potencias y potencias contratadas
    if (mostrar_grafica_potencias_potencias_contratadas == true) {
        muestra_grafica_temporal_lineas_valores(
            id_grafica_potencias_potencias_contratadas,
            null,
            TLNT.Idiomas._("Potencias y potencias contratadas") + " (" + TLNT.Idiomas._("kW") + ")",
            [TLNT.Idiomas._("Potencias") + " (" + nombre_sensor + ")", TLNT.Idiomas._("Potencias contratadas")  + " (" + nombre_sensor + ")"],
            grafica_potencias_potencias_contratadas, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_potencia, true,
            2, TLNT.Idiomas._("kW"),
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            true,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de sobrepotencias absolutas
    if (mostrar_grafica_sobrepotencias_absolutas == true) {
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_SOBREPOTENCIA);
        muestra_grafica_temporal_lineas_valores(
            id_grafica_sobrepotencias_absolutas,
            null,
            TLNT.Idiomas._("Diferencias respecto a la potencia contratada") + " (" + TLNT.Idiomas._("kW") + ")",
            null,
            grafica_sobrepotencias_absolutas, null, intervalo_valores,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            min_sobrepotencia_absoluta, true,
            max_sobrepotencia_absoluta, true,
            2, TLNT.Idiomas._("kW"),
            lineas_referencia,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            true,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Tabla de sobrepotencias por tramo
    if (mostrar_tabla_sobrepotencias_tramos == true) {
        $("#" + id_contenedor_tabla_sobrepotencias_tramos).html(tabla_sobrepotencias_tramos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_sobrepotencias_tramos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_sobrepotencias_tramos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_sobrepotencias_tramos, info_menu_contextual, TLNT.Idiomas._('Excesos de potencia por tramo'));
        }
    }
}


// Dibujado del informe de excesos de energía reactiva
function dibuja_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var tipo_energia_reactiva = datos.tipo_energia_reactiva;
    var grafica_consumos_energia = datos.grafica_consumos_energia;
    var grafica_coseno_phi = datos.grafica_coseno_phi;
    var grafica_penalizable = datos.grafica_penalizable;
    var tabla_energia_reactiva_tramos = datos.tabla_energia_reactiva_tramos;
    var etiquetas_consumos_energia = datos.etiquetas_consumos_energia;
    var max_consumo = datos.max_consumo;
    var min_coseno_phi = datos.min_coseno_phi;
    var max_coseno_phi = datos.max_coseno_phi;

    // Parámetros
    var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
    var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;
    var id_grafica_consumos_energia = parametros.id_grafica_consumos_energia;
    var id_grafica_coseno_phi = parametros.id_grafica_coseno_phi;
    var id_grafica_penalizable = parametros.id_grafica_penalizable;
    var id_contenedor_tabla_energia_reactiva_tramos = parametros.id_contenedor_tabla_energia_reactiva_tramos;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes_electricidad_Espanya(
        TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_grafica_consumos_energia = parametros.mostrar_grafica_consumos_energia;
    var mostrar_grafica_coseno_phi = parametros.mostrar_grafica_coseno_phi;
    var mostrar_grafica_penalizable = parametros.mostrar_grafica_penalizable;
    var mostrar_tabla_energia_reactiva_tramos = parametros.mostrar_tabla_energia_reactiva_tramos;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles
    if (mostrar_grafica_consumos_energia == true) {
        muestra_elemento(id_grafica_consumos_energia);
    }
    if (mostrar_grafica_coseno_phi == true) {
        muestra_elemento(id_grafica_coseno_phi);
    }
    if (mostrar_grafica_penalizable == true) {
        muestra_elemento(id_grafica_penalizable);
    }
    if (mostrar_tabla_energia_reactiva_tramos == true) {
        muestra_elemento(id_contenedor_tabla_energia_reactiva_tramos);
    }

    // Comprobación de datos disponibles de penalizable (sólo hay una serie)
    var numero_valores_grafica_penalizable = dame_numero_maximo_valores_series_grafica(grafica_penalizable);
    var hay_datos_penalizable = (numero_valores_grafica_penalizable > 0);

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
    var numero_valores_grafica_consumos_energia = dame_numero_maximo_valores_series_grafica(grafica_consumos_energia);
    var mostrar_indicadores_valores = (numero_valores_grafica_consumos_energia <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

    // Gráfica de consumos de energía activa y reactiva
    if (mostrar_grafica_consumos_energia == true) {
        muestra_grafica_temporal_lineas_valores(
            id_grafica_consumos_energia,
            null,
            TLNT.Idiomas._("Consumos"),
            etiquetas_consumos_energia,
            grafica_consumos_energia, null, INTERVALO_VALORES_HORA,
            null,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
            0, false,
            max_consumo, true,
            2, "",
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            false,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Gráfica de coseno de phi
    if (mostrar_grafica_coseno_phi == true) {
        if(tipo_energia_reactiva == TIPO_ENERGIA_REACTIVA_Q1){
            var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_COSENO_PHI);
            muestra_grafica_temporal_lineas_valores(
                id_grafica_coseno_phi,
                null,
                TLNT.Idiomas._("Coseno de phi") ,
                null,
                grafica_coseno_phi, null, INTERVALO_VALORES_HORA,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                min_coseno_phi, true,
                max_coseno_phi, true,
                3, "",
                lineas_referencia,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        if(tipo_energia_reactiva == TIPO_ENERGIA_REACTIVA_Q4){
            var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_COSENO_PHI_CAPACITIVA);
            muestra_grafica_temporal_lineas_valores(
                id_grafica_coseno_phi,
                null,
                TLNT.Idiomas._("Coseno de phi") ,
                null,
                grafica_coseno_phi, null, INTERVALO_VALORES_HORA,
                null,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                min_coseno_phi, true,
                max_coseno_phi, true,
                3, "",
                lineas_referencia,
                true,
                TIPO_LINEAS_VALORES_ESTANDAR,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
                
    }

    // Gráfica de penalizable
    if (mostrar_grafica_penalizable == true) {
        if (hay_datos_penalizable == true) {
            // Se dibuja la gráfica
            muestra_grafica_temporal_valores_si_no(
                id_grafica_penalizable,
                TLNT.Idiomas._("Penalizable"),
                null,
                grafica_penalizable, INTERVALO_VALORES_HORA,
                fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                null,
                true,
                TIPO_LINEAS_VALORES_CUADRADAS,
                mostrar_indicadores_valores,
                false,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_penalizable);
            }
            else {
                cambia_clase_elemento(id_grafica_penalizable, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_penalizable).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de penalizable (no hay datos de penalizable)"));
            }
        }
    }

    // Tabla de energía reactiva (por tramo)
    if (mostrar_tabla_energia_reactiva_tramos == true) {
        if (hay_datos_penalizable == true) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_energia_reactiva_tramos).html(tabla_energia_reactiva_tramos);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_energia_reactiva_tramos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_energia_reactiva_tramos);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_energia_reactiva_tramos, info_menu_contextual, TLNT.Idiomas._('Energía reactiva por tramo'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_energia_reactiva_tramos);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_energia_reactiva_tramos, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_energia_reactiva_tramos).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de costes de energía reactiva por tramo (no hay datos de coste)"));
            }
        }
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_smartmeter_consumos_costes_electricidad_Espanya(
    tipo_informe_smartmeter_consumos_costes,
    elementos_informe,
    parametros,
    datos) {
    switch (tipo_informe_smartmeter_consumos_costes) {
        case TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA: {
            var mostrar_grafica_potencias_potencias_contratadas = true;
            var mostrar_grafica_sobrepotencias_absolutas = true;
            var mostrar_tabla_sobrepotencias_tramos = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_GRAFICA_POTENCIAS_POTENCIAS_CONTRATADAS) == -1) {
                    mostrar_grafica_potencias_potencias_contratadas = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_GRAFICA_SOBREPOTENCIAS_ABSOLUTAS) == -1) {
                    mostrar_grafica_sobrepotencias_absolutas = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_TABLA_SOBREPOTENCIAS_TRAMOS) == -1) {
                    mostrar_tabla_sobrepotencias_tramos = false;
                }
            }
            parametros.mostrar_grafica_potencias_potencias_contratadas = mostrar_grafica_potencias_potencias_contratadas;
            parametros.mostrar_grafica_sobrepotencias_absolutas = mostrar_grafica_sobrepotencias_absolutas;
            parametros.mostrar_tabla_sobrepotencias_tramos = mostrar_tabla_sobrepotencias_tramos;
            break;
        }
        case TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA: {
            var mostrar_grafica_consumos_energia = true;
            var mostrar_grafica_coseno_phi = true;
            var mostrar_grafica_penalizable = true;
            var mostrar_tabla_energia_reactiva_tramos = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_CONSUMOS_ENERGIA) == -1) {
                    mostrar_grafica_consumos_energia = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_COSENO_PHI) == -1) {
                    mostrar_grafica_coseno_phi = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_PENALIZABLE) == -1) {
                    mostrar_grafica_penalizable = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_TABLA_COSTES_ENERGIA_REACTIVA_TRAMOS) == -1) {
                    mostrar_tabla_energia_reactiva_tramos = false;
                }
            }
            parametros.mostrar_grafica_consumos_energia = mostrar_grafica_consumos_energia;
            parametros.mostrar_grafica_coseno_phi = mostrar_grafica_coseno_phi;
            parametros.mostrar_grafica_penalizable = mostrar_grafica_penalizable;
            parametros.mostrar_tabla_energia_reactiva_tramos = mostrar_tabla_energia_reactiva_tramos;

            // Indica si hay elementos visibles
            var grafica_penalizable = datos.grafica_penalizable;
            var numero_valores_grafica_penalizable = dame_numero_maximo_valores_series_grafica(grafica_penalizable);
            var hay_datos_penalizable = (numero_valores_grafica_penalizable > 0);
            var hay_elementos_visibles =
                (mostrar_grafica_consumos_energia == true) ||
                (mostrar_grafica_coseno_phi == true) ||
                ((mostrar_grafica_penalizable == true) && (hay_datos_penalizable == true)) ||
                ((mostrar_tabla_energia_reactiva_tramos == true) && (hay_datos_penalizable == true));
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Smartmeter - Excesos de potencia)
function dibuja_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad_Espanya(
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

    // Granularidad
    var granularidad = parametros_tipo.granularidad;

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_grafica_potencias_potencias_contratadas = prefijo_elemento + "grafica-potencias-potencias-contratadas-excesos-potencia";
    var id_grafica_sobrepotencias_absolutas = prefijo_elemento + "grafica-sobrepotencias-absolutas-excesos-potencia";
    var id_contenedor_tabla_sobrepotencias_tramos = prefijo_elemento + "contenedor-tabla-sobrepotencias-tramos-excesos-potencia";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        granularidad: granularidad,
        id_grafica_potencias_potencias_contratadas: id_grafica_potencias_potencias_contratadas,
        id_grafica_sobrepotencias_absolutas: id_grafica_sobrepotencias_absolutas,
        id_contenedor_tabla_sobrepotencias_tramos: id_contenedor_tabla_sobrepotencias_tramos};
    dibuja_informe_smartmeter_excesos_potencia_electricidad_Espanya(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}


// Dibuja el elemento de una plantilla de informe (Smartmeter - Consumos y costes totales)
function dibuja_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
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
    var id_grafica_consumos_energia = prefijo_elemento + "grafica-consumos-energia-excesos-energia-reactiva";
    var id_grafica_coseno_phi = prefijo_elemento + "grafica-coseno-phi-excesos-energia-reactiva";
    var id_grafica_penalizable = prefijo_elemento + "grafica-penalizable-excesos-energia-reactiva";
    var id_contenedor_tabla_energia_reactiva_tramos = prefijo_elemento + "contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva";

    var parametros = {
        fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
        fecha_hora_fin_consulta: fecha_hora_fin_consulta,
        id_grafica_consumos_energia: id_grafica_consumos_energia,
        id_grafica_coseno_phi: id_grafica_coseno_phi,
        id_grafica_penalizable: id_grafica_penalizable,
        id_contenedor_tabla_energia_reactiva_tramos: id_contenedor_tabla_energia_reactiva_tramos};
    dibuja_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}
