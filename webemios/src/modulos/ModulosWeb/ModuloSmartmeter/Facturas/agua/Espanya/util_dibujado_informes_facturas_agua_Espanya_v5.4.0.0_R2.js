//
// Funciones para el dibujado de los informes de facturas (SmartMeter) (agua - España)
//


// Dibujado del informe de simulación de factura
function dibuja_informe_smartmeter_simulador_factura_agua_Espanya(
    parametros,
    datos,
    elementos_informe,
    tipo_informe) {
    // Datos del resultado
    var msg_aviso = datos.msg_aviso;
    var tabla_datos = datos.tabla_datos;
    var tabla_coste_consumo = datos.tabla_coste_consumo;
    var tabla_consumo = datos.tabla_consumo;
    var tabla_otros_conceptos = datos.tabla_otros_conceptos;
    var grafica_porcentajes_costes_conceptos = datos.grafica_porcentajes_costes_conceptos;
    var etiquetas_conceptos = datos.etiquetas_conceptos;
    var hay_datos_reparto_costes = datos.hay_datos_reparto_costes;
    var tabla_reparto_costes = datos.tabla_reparto_costes;
    var grafica_porcentajes_reparto_costes = datos.grafica_porcentajes_reparto_costes;
    var etiquetas_sensores_reparto_costes = datos.etiquetas_sensores_reparto_costes;
    var unidad_medida_coste = datos.unidad_medida_coste;

    // Parámetros
    var id_titulo_datos = parametros.id_titulo_datos;
    var id_contenedor_tabla_datos = parametros.id_contenedor_tabla_datos;
    var id_titulo_resumen = parametros.id_titulo_resumen;
    var id_contenedor_tabla_coste_consumo = parametros.id_contenedor_tabla_coste_consumo;
    var id_titulo_detalles = parametros.id_titulo_detalles;
    var id_contenedor_tabla_consumo = parametros.id_contenedor_tabla_consumo;
    var id_contenedor_tabla_otros_conceptos = parametros.id_contenedor_tabla_otros_conceptos;
    var id_grafica_porcentajes_costes_conceptos = parametros.id_grafica_porcentajes_costes_conceptos;
    var id_titulo_reparto_costes = parametros.id_titulo_reparto_costes;
    var id_contenedor_tabla_reparto_costes = parametros.id_contenedor_tabla_reparto_costes;
    var id_grafica_porcentajes_reparto_costes = parametros.id_grafica_porcentajes_reparto_costes;

    // Añade a los parámetros los flags de elementos visibles
    anyade_parametros_elementos_visibles_informe_smartmeter_facturas_agua_Espanya(
        TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA,
        elementos_informe,
        parametros,
        datos);

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA,
        null);

    // Parámetros (flags de mostrar elementos y elementos visibles)
    var mostrar_titulo_datos = parametros.mostrar_titulo_datos;
    var mostrar_tabla_datos = parametros.mostrar_tabla_datos;
    var mostrar_titulo_resumen = parametros.mostrar_titulo_resumen;
    var mostrar_tabla_coste_consumo = parametros.mostrar_tabla_coste_consumo;
    var mostrar_titulo_detalles = parametros.mostrar_titulo_detalles;
    var mostrar_tabla_consumo = parametros.mostrar_tabla_consumo;
    var mostrar_tabla_otros_conceptos = parametros.mostrar_tabla_otros_conceptos;
    var mostrar_grafica_porcentajes_costes_conceptos = parametros.mostrar_grafica_porcentajes_costes_conceptos;
    var mostrar_titulo_reparto_costes = parametros.mostrar_titulo_reparto_costes;
    var mostrar_tabla_reparto_costes = parametros.mostrar_tabla_reparto_costes;
    var mostrar_grafica_porcentajes_reparto_costes = parametros.mostrar_grafica_porcentajes_reparto_costes;
    var hay_elementos_visibles = parametros.hay_elementos_visibles;

    // Se muestran los elementos visibles
    if (mostrar_titulo_datos == true) {
        muestra_elemento(id_titulo_datos);
    }
    if (mostrar_tabla_datos == true) {
        muestra_elemento(id_contenedor_tabla_datos);
    }
    if (mostrar_titulo_resumen == true) {
        muestra_elemento(id_titulo_resumen);
    }
    if (mostrar_tabla_coste_consumo == true) {
        muestra_elemento(id_contenedor_tabla_coste_consumo);
    }
    if (mostrar_titulo_detalles == true) {
        muestra_elemento(id_titulo_detalles);
    }
    if (mostrar_tabla_consumo == true) {
        muestra_elemento(id_contenedor_tabla_consumo);
    }
    if (mostrar_tabla_otros_conceptos == true) {
        muestra_elemento(id_contenedor_tabla_otros_conceptos);
    }
    if (mostrar_grafica_porcentajes_costes_conceptos == true) {
        muestra_elemento(id_grafica_porcentajes_costes_conceptos);
    }
    if (mostrar_titulo_reparto_costes == true) {
        muestra_elemento(id_titulo_reparto_costes);
    }
    if (mostrar_tabla_reparto_costes == true) {
        muestra_elemento(id_contenedor_tabla_reparto_costes);
    }
    if (mostrar_grafica_porcentajes_reparto_costes == true) {
        muestra_elemento(id_grafica_porcentajes_reparto_costes);
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

    // Aviso
    if ((tipo_informe == TIPO_INFORME_WEB_EMIOS) && (elementos_informe == null)) {
        if (msg_aviso != "") {
            jAlert(msg_aviso);
        }
    }

    // Título de datos
    if (mostrar_titulo_datos == true) {
        $("#" + id_titulo_datos).html(TLNT.Idiomas._("Datos de factura"));
    }

    // Tabla de datos
    if (mostrar_tabla_datos == true) {
        $("#" + id_contenedor_tabla_datos).html(tabla_datos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_datos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_datos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_datos, info_menu_contextual, TLNT.Idiomas._('Datos de factura'));
        }
    }

    // Título de resumen
    if (mostrar_titulo_resumen == true) {
        $("#" + id_titulo_resumen).html(TLNT.Idiomas._("Resumen de factura"));
    }

    // Tabla de coste y consumo
    if (mostrar_tabla_coste_consumo == true) {
        $("#" + id_contenedor_tabla_coste_consumo).html(tabla_coste_consumo);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_coste_consumo = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_coste_consumo);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_coste_consumo, info_menu_contextual, TLNT.Idiomas._('Coste y consumo'));
        }
    }

    // Título de detalles
    if (mostrar_titulo_detalles == true) {
        $("#" + id_titulo_detalles).html(TLNT.Idiomas._("Detalles de factura"));
    }

    // Tabla de consumo
    if (mostrar_tabla_consumo == true) {
        $("#" + id_contenedor_tabla_consumo).html(tabla_consumo);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_consumo = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_consumo);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_consumo, info_menu_contextual, TLNT.Idiomas._('Consumo'));
        }
    }

    // Tabla de otros conceptos
    if (mostrar_tabla_otros_conceptos == true) {
        // Se rellena la tabla y se añade el menú contextual
        $("#" + id_contenedor_tabla_otros_conceptos).html(tabla_otros_conceptos);
        if (anyadir_menus_contextuales == true) {
            var id_tabla_otros_conceptos = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_otros_conceptos);
            var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
            anyade_menu_contextual(id_tabla_otros_conceptos, info_menu_contextual, TLNT.Idiomas._('Otros conceptos'));
        }
    }

    // Gráfica de porcentajes de costes por concepto
    if (mostrar_grafica_porcentajes_costes_conceptos == true) {
        var titulo_grafica_porcentajes_costes_conceptos = TLNT.Idiomas._("Porcentajes de costes por concepto") + " (" + unidad_medida_coste + ")";
        muestra_grafica_tarta_valores(
            id_grafica_porcentajes_costes_conceptos,
            titulo_grafica_porcentajes_costes_conceptos,
            TLNT.Idiomas._("Conceptos"),
            etiquetas_conceptos,
            grafica_porcentajes_costes_conceptos,
            2, unidad_medida_coste,
            mostrar_animaciones,
            anyadir_menus_contextuales);
    }

    // Título de reparto de costes
    if (mostrar_titulo_reparto_costes == true) {
        if (hay_datos_reparto_costes == true) {
            $("#" + id_titulo_reparto_costes).html(TLNT.Idiomas._("Reparto de costes"));
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_titulo_reparto_costes);
            }
            else {
                cambia_clase_elemento(id_titulo_reparto_costes, "texto-elemento-no-mostrado-informe");
                $("#" + id_titulo_reparto_costes).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra el título de reparto de costes (no hay sensores seleccionados)"));
            }
        }
    }

    // Tabla de reparto de costes
    if (mostrar_tabla_reparto_costes == true) {
        if (hay_datos_reparto_costes == true) {
            // Se rellena la tabla y se añade el menú contextual
            $("#" + id_contenedor_tabla_reparto_costes).html(tabla_reparto_costes);
            if (anyadir_menus_contextuales == true) {
                var id_tabla_reparto_costes = anyade_id_tabla_id_contenedor_tabla(id_contenedor_tabla_reparto_costes);
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual(id_tabla_reparto_costes, info_menu_contextual, TLNT.Idiomas._('Reparto de costes'));
            }
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_contenedor_tabla_reparto_costes);
            }
            else {
                cambia_clase_elemento(id_contenedor_tabla_reparto_costes, "texto-elemento-no-mostrado-informe");
                $("#" + id_contenedor_tabla_reparto_costes).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la tabla de reparto de costes (no hay sensores seleccionados)"));
            }
        }
    }

    // Gráfica de porcentajes de reparto de costes
    if (mostrar_grafica_porcentajes_reparto_costes == true) {
        if (hay_datos_reparto_costes == true) {
            var titulo_grafica_porcentajes_reparto_costes = TLNT.Idiomas._("Porcentajes de reparto de costes") + " (" + unidad_medida_coste + ")";
            muestra_grafica_tarta_valores(
                id_grafica_porcentajes_reparto_costes,
                titulo_grafica_porcentajes_reparto_costes,
                TLNT.Idiomas._("Sensores"),
                etiquetas_sensores_reparto_costes,
                grafica_porcentajes_reparto_costes,
                2, unidad_medida_coste,
                mostrar_animaciones,
                anyadir_menus_contextuales);
        }
        else {
            // Si hay elementos visibles se oculta el elemento, si no se muestra un mensaje de aviso
            if (hay_elementos_visibles == true) {
                oculta_elemento(id_grafica_porcentajes_reparto_costes);
            }
            else {
                cambia_clase_elemento(id_grafica_porcentajes_reparto_costes, "texto-elemento-no-mostrado-informe");
                $("#" + id_grafica_porcentajes_reparto_costes).html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra la gráfica de porcentajes de reparto de costes (no hay sensores seleccionados)"));
            }
        }
    }
}


// Añade a los parámetros los flags de elementos visibles
function anyade_parametros_elementos_visibles_informe_smartmeter_facturas_agua_Espanya(
    tipo_informe_smartmeter_facturas,
    elementos_informe,
    parametros,
    datos) {
    switch (tipo_informe_smartmeter_facturas) {
        case TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA: {
            var mostrar_titulo_datos = true;
            var mostrar_tabla_datos = true;
            var mostrar_titulo_resumen = true;
            var mostrar_tabla_coste_consumo = true;
            var mostrar_titulo_detalles = true;
            var mostrar_tabla_consumo = true;
            var mostrar_tabla_otros_conceptos = true;
            var mostrar_grafica_porcentajes_costes_conceptos = true;
            var mostrar_titulo_reparto_costes = true;
            var mostrar_tabla_reparto_costes = true;
            var mostrar_grafica_porcentajes_reparto_costes = true;
            if (elementos_informe != null) {
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TITULO_DATOS) == -1) {
                    mostrar_titulo_datos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_DATOS) == -1) {
                    mostrar_tabla_datos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TITULO_RESUMEN) == -1) {
                    mostrar_titulo_resumen = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_COSTE_CONSUMO) == -1) {
                    mostrar_tabla_coste_consumo = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TITULO_DETALLES) == -1) {
                    mostrar_titulo_detalles = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_CONSUMO) == -1) {
                    mostrar_tabla_consumo = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_OTROS_CONCEPTOS) == -1) {
                    mostrar_tabla_otros_conceptos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_GRAFICA_PORCENTAJES_COSTES_CONCEPTOS) == -1) {
                    mostrar_grafica_porcentajes_costes_conceptos = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TITULO_REPARTO_COSTES) == -1) {
                    mostrar_titulo_reparto_costes = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_REPARTO_COSTES) == -1) {
                    mostrar_tabla_reparto_costes = false;
                }
                if (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES) == -1) {
                    mostrar_grafica_porcentajes_reparto_costes = false;
                }
            }
            parametros.mostrar_titulo_datos = mostrar_titulo_datos;
            parametros.mostrar_tabla_datos = mostrar_tabla_datos;
            parametros.mostrar_titulo_resumen = mostrar_titulo_resumen;
            parametros.mostrar_tabla_coste_consumo = mostrar_tabla_coste_consumo;
            parametros.mostrar_titulo_detalles = mostrar_titulo_detalles;
            parametros.mostrar_tabla_consumo = mostrar_tabla_consumo;
            parametros.mostrar_tabla_otros_conceptos = mostrar_tabla_otros_conceptos;
            parametros.mostrar_grafica_porcentajes_costes_conceptos = mostrar_grafica_porcentajes_costes_conceptos;
            parametros.mostrar_titulo_reparto_costes = mostrar_titulo_reparto_costes;
            parametros.mostrar_tabla_reparto_costes = mostrar_tabla_reparto_costes;
            parametros.mostrar_grafica_porcentajes_reparto_costes = mostrar_grafica_porcentajes_reparto_costes;

            // Indica si hay elementos visibles
            var hay_datos_reparto_costes = datos.hay_datos_reparto_costes;
            var hay_elementos_visibles =
                (mostrar_titulo_datos == true) ||
                (mostrar_tabla_datos == true) ||
                (mostrar_titulo_resumen == true) ||
                (mostrar_tabla_coste_consumo == true) ||
                (mostrar_titulo_detalles == true) ||
                (mostrar_tabla_consumo == true) ||
                (mostrar_tabla_consumo == true) ||
                (mostrar_tabla_otros_conceptos == true) ||
                (mostrar_grafica_porcentajes_costes_conceptos == true) ||
                ((mostrar_titulo_reparto_costes == true) && (hay_datos_reparto_costes == true)) ||
                ((mostrar_tabla_reparto_costes == true) && (hay_datos_reparto_costes == true)) ||
                ((mostrar_grafica_porcentajes_reparto_costes == true) && (hay_datos_reparto_costes == true));
            parametros.hay_elementos_visibles = hay_elementos_visibles;
            break;
        }
    }
}


//
// Funciones de plantillas de informes
//


// Dibuja el elemento de una plantilla de informe (Smartmeter - Simulación de factura)
function dibuja_elemento_plantilla_informe_smartmeter_simulador_factura_agua_Espanya(
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
        $("#elemento-sin-tarifa-asignada-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de sensor seleccionado
    var sin_sensor_seleccionado = datos_elemento.sin_sensor_seleccionado;
    if (sin_sensor_seleccionado == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).show();
        $("#elemento-sin-tarifa-asignada-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de tarifa asignada
    var sin_tarifa_asignada = datos_elemento.sin_tarifa_asignada;
    if (sin_tarifa_asignada == true) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-tarifa-asignada-elemento" + numero_elemento).show();
        $("#elemento-sin-datos-elemento" + numero_elemento).hide();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Comprobación de datos disponibles
    var hay_datos = datos_elemento.hay_datos;
    if (hay_datos == false) {
        $("#elemento-error-datos-elemento" + numero_elemento).hide();
        $("#elemento-sin-sensor-seleccionado-elemento" + numero_elemento).hide();
        $("#elemento-sin-tarifa-asignada-elemento" + numero_elemento).hide();
        $("#elemento-sin-datos-elemento" + numero_elemento).show();
        $("#contenido-elemento" + numero_elemento).hide();
        return;
    }

    // Prefijo de elemento para los controles
    var prefijo_elemento = "elemento" + numero_elemento + "-";

    // Se dibuja el elemento
    var id_titulo_datos = prefijo_elemento + "titulo-datos-simulador-factura";
    var id_contenedor_tabla_datos = prefijo_elemento + "contenedor-tabla-datos-simulador-factura";
    var id_titulo_resumen = prefijo_elemento + "titulo-resumen-simulador-factura";
    var id_contenedor_tabla_coste_consumo = prefijo_elemento + "contenedor-tabla-coste-consumo-simulador-factura";
    var id_titulo_detalles = prefijo_elemento + "titulo-detalles-simulador-factura";
    var id_contenedor_tabla_consumo = prefijo_elemento + "contenedor-tabla-consumo-simulador-factura";
    var id_contenedor_tabla_otros_conceptos = prefijo_elemento + "contenedor-tabla-otros-conceptos-simulador-factura";
    var id_grafica_porcentajes_costes_conceptos = prefijo_elemento + "grafica-porcentajes-costes-conceptos-simulador-factura";

    var parametros = {
        id_titulo_datos: id_titulo_datos,
        id_contenedor_tabla_datos: id_contenedor_tabla_datos,
        id_titulo_resumen: id_titulo_resumen,
        id_contenedor_tabla_coste_consumo: id_contenedor_tabla_coste_consumo,
        id_titulo_detalles: id_titulo_detalles,
        id_contenedor_tabla_consumo: id_contenedor_tabla_consumo,
        id_contenedor_tabla_otros_conceptos: id_contenedor_tabla_otros_conceptos,
        id_grafica_porcentajes_costes_conceptos: id_grafica_porcentajes_costes_conceptos};
    dibuja_informe_smartmeter_simulador_factura_agua_Espanya(
        parametros,
        datos_elemento,
        elementos_informe_elemento,
        tipo_informe);
}
