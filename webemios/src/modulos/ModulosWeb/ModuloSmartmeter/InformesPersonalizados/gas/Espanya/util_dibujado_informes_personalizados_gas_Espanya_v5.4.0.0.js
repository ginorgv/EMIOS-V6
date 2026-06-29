//
// Funciones para el dibujado de los informes personalizados (SmartMeter) (gas - España)
//


// Dibujado del informe de estudio general
function dibuja_informe_smartmeter_estudio_general_gas_Espanya(
    parametros,
    datos,
    tipo_informe) {
    // Parámetros
    var apartados = parametros.apartados;
    var texto_introduccion = parametros.texto_introduccion;
    var parametros_tipo_json = parametros.parametros_tipo_json;

    // Información de dibujado del informe
    establece_informacion_dibujado_informe(
        MODULO_SMARTMETER,
        TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL,
        null);

    // Inicializa el informe Web
    if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
        inicializa_informe_web_smartmeter_estudio_general_gas_Espanya(texto_introduccion);
    }

    // Textos del informe
    var textos_informe = null;
    switch (tipo_informe) {
        case TIPO_INFORME_WEB_EMIOS: {
            textos_informe = {
                texto_introduccion: texto_introduccion
            };
            break;
        }
        case TIPO_INFORME_FICHERO: {
            textos_informe = dame_textos_informe_fichero_smartmeter_estudio_general_gas_Espanya(parametros_tipo_json);
            break;
        }
    }
    textos_informe.texto_avisos = "";

    // Se dibujan los apartados seleccionados

    // Portada
    dibuja_apartado_portada_smartmeter_estudio_general_gas_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Introducción
    dibuja_apartado_introduccion_smartmeter_estudio_general_gas_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Instalación
    dibuja_apartado_instalacion_smartmeter_estudio_general_gas_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Análisis de consumo
    dibuja_apartado_analisis_consumo_smartmeter_estudio_general_gas_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Análisis de coste
    dibuja_apartado_analisis_coste_smartmeter_estudio_general_gas_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Excesos de caudal
    dibuja_apartado_excesos_caudal_smartmeter_estudio_general_gas_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Simulación de factura
    dibuja_apartado_simulacion_factura_smartmeter_estudio_general_gas_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Conclusiones
    dibuja_apartado_conclusiones_smartmeter_estudio_general_gas_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Avisos
    dibuja_apartado_avisos_smartmeter_estudio_general_gas_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);
}


//
// Funciones auxiliares
//


// Inicialización de informe Web
function inicializa_informe_web_smartmeter_estudio_general_gas_Espanya() {
    // Portada
    oculta_elemento("apartado_portada_estudio_general");

    // Introducción
    $("#introduccion-estudio-general").val("");
    $("#introduccion-estudio-general").trigger("input");
    oculta_elemento("apartado-introduccion-estudio-general");

    // Instalación
    $("#notas-instalacion-estudio-general").val("");
    $("#notas-instalacion-estudio-general").trigger("input");
    oculta_elemento("apartado-instalacion-estudio-general");

    // Análisis de consumo
    $("#notas-analisis-consumo-estudio-general").val("");
    $("#notas-analisis-consumo-estudio-general").trigger("input");
    oculta_elemento("apartado-analisis-consumo-estudio-general");

    // Análisis de coste
    $("#notas-analisis-coste-estudio-general").val("");
    $("#notas-analisis-coste-estudio-general").trigger("input");
    oculta_elemento("apartado-analisis-coste-estudio-general");

    // Excesos de caudal
    $("#notas-excesos-caudal-estudio-general").val("");
    $("#notas-excesos-caudal-estudio-general").trigger("input");
    oculta_elemento("apartado-excesos-caudal-estudio-general");

    // Simulación de factura
    $("#notas-simulacion-factura-estudio-general").val("");
    $("#notas-simulacion-factura-estudio-general").trigger("input");
    oculta_elemento("apartado-simulacion-factura-estudio-general");

    // Conclusiones
    $("#conclusiones-estudio-general").val("");
    $("#conclusiones-estudio-general").trigger("input");
    oculta_elemento("apartado-conclusiones-estudio-general");

    // Avisos
    oculta_elemento("apartado-avisos-estudio-general");
}


// Recuperación de textos del informe fichero
function dame_textos_informe_fichero_smartmeter_estudio_general_gas_Espanya(parametros_tipo_json) {
    var texto_introduccion = parametros_tipo_json["texto_introduccion"];
    var notas_instalacion = "";
    if ("notas_instalacion" in parametros_tipo_json) {
        notas_instalacion = parametros_tipo_json["notas_instalacion"];
    }
    var notas_analisis_consumo = "";
    if ("notas_analisis_consumo" in parametros_tipo_json) {
        notas_analisis_consumo = parametros_tipo_json["notas_analisis_consumo"];
    }
    var notas_analisis_coste = "";
    if ("notas_analisis_coste" in parametros_tipo_json) {
        notas_analisis_coste = parametros_tipo_json["notas_analisis_coste"];
    }
    var notas_excesos_caudal = "";
    if ("notas_excesos_caudal" in parametros_tipo_json) {
        notas_excesos_caudal = parametros_tipo_json["notas_excesos_caudal"];
    }
    var notas_simulacion_factura = "";
    if ("notas_simulacion_factura" in parametros_tipo_json) {
        notas_simulacion_factura = parametros_tipo_json["notas_simulacion_factura"];
    }
    var conclusiones = "";
    if ("conclusiones" in parametros_tipo_json) {
        conclusiones = parametros_tipo_json["conclusiones"];
    }

    // Formateado de textos a HTML
    texto_introduccion = formatea_texto_informe_html(texto_introduccion);
    notas_instalacion = formatea_texto_informe_html(notas_instalacion);
    notas_analisis_consumo = formatea_texto_informe_html(notas_analisis_consumo);
    notas_analisis_coste = formatea_texto_informe_html(notas_analisis_coste);
    notas_excesos_caudal = formatea_texto_informe_html(notas_excesos_caudal);
    notas_simulacion_factura = formatea_texto_informe_html(notas_simulacion_factura);
    conclusiones = formatea_texto_informe_html(conclusiones);

    var textos_informe_fichero = {
        texto_introduccion: texto_introduccion,
        notas_instalacion: notas_instalacion,
        notas_analisis_consumo: notas_analisis_consumo,
        notas_analisis_coste: notas_analisis_coste,
        notas_excesos_caudal: notas_excesos_caudal,
        notas_simulacion_factura: notas_simulacion_factura,
        conclusiones: conclusiones};
    return (textos_informe_fichero);
}


//
// Funciones de dibujado de apartados
//


// Portada
function dibuja_apartado_portada_smartmeter_estudio_general_gas_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_GAS_ESPANYA) > -1) {
        // Se muestra el apartado
        if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
            muestra_elemento("apartado_portada_estudio_general");
        }

        // Datos de portada
        var datos_portada = datos.datos_portada;

        // Recuperación de datos del apartado
        var nombre_red = datos_portada.nombre_red;
        var cadena_fechas = datos_portada.cadena_fechas;
        var descripcion_sensor = datos_portada.descripcion_sensor;

        // Se rellenan los datos
        $("#nombre-red-portada-estudio-general").html(nombre_red);
        $("#fechas-portada-estudio-general").html(cadena_fechas);
        $("#descripcion-sensor-portada-estudio-general").html(descripcion_sensor);
    }
    else {
        // Se ocultan las páginas del apartado
        if (tipo_informe == TIPO_INFORME_FICHERO) {
            elimina_elemento("pagina-informe-fichero-estudio-general-portada");
        }
    }
}


// Introducción
function dibuja_apartado_introduccion_smartmeter_estudio_general_gas_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_GAS_ESPANYA) > -1) {
        // Se muestra el apartado
        if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
            muestra_elemento("apartado-introduccion-estudio-general");
        }

        // Introducción
        switch (tipo_informe) {
            case TIPO_INFORME_WEB_EMIOS: {
                $("#introduccion-estudio-general").val(textos_informe.texto_introduccion);
                TLNT.Navegacion.redimensiona_textarea("#introduccion-estudio-general");
                break;
            }
            case TIPO_INFORME_FICHERO: {
                $("#introduccion-estudio-general").html(textos_informe.texto_introduccion);
                break;
            }
        }
    }
    else {
        if (tipo_informe == TIPO_INFORME_FICHERO) {
            elimina_elemento("pagina-introduccion-informe-fichero-estudio-general");
        }
    }
}


// Instalación
function dibuja_apartado_instalacion_smartmeter_estudio_general_gas_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_GAS_ESPANYA) > -1) {
        // Parámetros
        var fecha_inicio = parametros.fecha_inicio;
        var fecha_fin = parametros.fecha_fin;

        // Se muestra el apartado y se borran los datos anteriores
        if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
            muestra_elemento("apartado-instalacion-estudio-general");
            vacia_elementos([
                "cups-instalacion-estudio-general",
                "descripcion-instalacion-estudio-general",
                "tipo-instalacion-estudio-general",
                "fecha-inicio-instalacion-estudio-general",
                "fecha-fin-instalacion-estudio-general",
                "contenedor-tabla-parametros-tarifa-gas-instalacion-estudio-general"]);
        }

        // Datos de instalación
        var datos_instalacion = datos.datos_instalacion;

        // Se dibuja la instalación
        var parametros = {
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            id_cups: "cups-instalacion-estudio-general",
            id_fecha_inicio: "fecha-inicio-instalacion-estudio-general",
            id_fecha_fin: "fecha-fin-instalacion-estudio-general",
            id_descripcion: "descripcion-instalacion-estudio-general",
            id_tipo: "tipo-instalacion-estudio-general",
            id_contenedor_tabla_parametros_tarifa_gas: "contenedor-tabla-parametros-tarifa-gas-instalacion-estudio-general"};
        dibuja_instalacion_gas_Espanya(
            parametros,
            datos_instalacion,
            tipo_informe);

        // Notas del apartado
        if (tipo_informe == TIPO_INFORME_FICHERO) {
            if (textos_informe.notas_instalacion != "") {
                $('#notas-instalacion-estudio-general').html(textos_informe.notas_instalacion);
            }
            else {
                oculta_elemento("notas-instalacion-estudio-general");
            }
        }
    }
    else {
        if (tipo_informe == TIPO_INFORME_FICHERO) {
            elimina_elemento("pagina-informe-fichero-estudio-general-instalacion");
        }
    }
}


// Análisis de consumo
function dibuja_apartado_analisis_consumo_smartmeter_estudio_general_gas_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_analisis_consumo = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_GAS_ESPANYA) > -1) {
        // Parámetros
        var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;

        // Datos de análisis de consumo
        var datos_analisis_consumo = datos.datos_analisis_consumo;

        // Flags de existencia de datos
        var hay_datos_grafica_consumos = datos_analisis_consumo.hay_datos_grafica_consumos;
        var hay_datos_mapa_calor_consumos = datos_analisis_consumo.hay_datos_mapa_calor_consumos;
        var hay_datos_consumos_periodos = datos_analisis_consumo.hay_datos_consumos_periodos;
        var hay_datos_analisis_consumo = (
            (hay_datos_grafica_consumos == true) ||
            (hay_datos_mapa_calor_consumos == true) ||
            (hay_datos_consumos_periodos == true));

        // Datos del apartado
        if (hay_datos_analisis_consumo == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-analisis-consumo-estudio-general");
                vacia_elementos([
                    "grafica-consumos-analisis-consumo-estudio-general",
                    "grafica-consumos-periodos-analisis-consumo-estudio-general",
                    "mapa-calor-semanal-consumos-analisis-consumo-estudio-general"]);
            }
            mostrar_analisis_consumo = true;

            // Recuperación de datos del apartado
            if (hay_datos_grafica_consumos == true) {
                var max_consumo = datos_analisis_consumo.max_consumo;
                var etiquetas_grafica_consumos = datos_analisis_consumo.etiquetas_grafica_consumos;
                var grafica_consumos = datos_analisis_consumo.grafica_consumos;
            }
            if (hay_datos_mapa_calor_consumos == true) {
                var colores_mapa_calor_consumos = dame_escala_colores_mapa_calor(datos_analisis_consumo.colores_mapa_calor_consumos);
                var dias_mapa_calor_consumos = datos_analisis_consumo.dias_mapa_calor_consumos;
                var datos_mapa_calor_consumos = datos_analisis_consumo.datos_mapa_calor_consumos;
                var unidad_medida_consumo = datos_analisis_consumo.unidad_medida_consumo;
            }
            if (hay_datos_consumos_periodos == true) {
                var max_consumo_periodos = datos_analisis_consumo.max_consumo_periodos;
                var etiquetas_consumos_periodos = datos_analisis_consumo.etiquetas_consumos_periodos;
                var etiquetas_tooltips_consumos_periodos = datos_analisis_consumo.etiquetas_tooltips_consumos_periodos;
                var min_fecha_consumo_periodos = new $.jsDate(datos_analisis_consumo.min_fecha_consumo_periodos);
                var max_fecha_consumo_periodos = new $.jsDate(datos_analisis_consumo.max_fecha_consumo_periodos);
                var grafica_consumos_periodos = datos_analisis_consumo.grafica_consumos_periodos;
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

            // Datos de gráfica de consumos
            if (hay_datos_grafica_consumos == true) {
                muestra_elemento("grafica-consumos-analisis-consumo-estudio-general");

                var numero_valores_grafica_consumos = dame_numero_maximo_valores_series_grafica(grafica_consumos);
                var mostrar_indicadores_valores_consumos = (numero_valores_grafica_consumos <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de consumos
                muestra_grafica_temporal_lineas_valores(
                    "grafica-consumos-analisis-consumo-estudio-general",
                    null,
                    TLNT.Idiomas._("Consumo") + " (" + unidad_medida_consumo + ")",
                    etiquetas_grafica_consumos,
                    grafica_consumos, null, INTERVALO_VALORES_DIA,
                    null,
                    fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                    0, false,
                    max_consumo, true,
                    2, unidad_medida_consumo,
                    null,
                    true,
                    TIPO_LINEAS_VALORES_ESTANDAR,
                    mostrar_indicadores_valores_consumos,
                    false,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);
            }
            else {
                oculta_elemento("grafica-consumos-analisis-consumo-estudio-general");
            }

            // Datos de mapa de calor de consumos
            if (hay_datos_mapa_calor_consumos == true) {
                muestra_elemento("mapa-calor-semanal-consumos-analisis-consumo-estudio-general");

                // Mapa de calor semanal de consumos
                var titulo_mapa_calor_semanal_consumos = TLNT.Idiomas._("Mapa de calor semanal de") + " " + TLNT.Idiomas._("consumo") + " (" + unidad_medida_consumo + ")";
                muestra_grafico_mapa_calor(
                    "mapa-calor-semanal-consumos-analisis-consumo-estudio-general",
                    TIPO_MAPA_CALOR_SEMANAL,
                    titulo_mapa_calor_semanal_consumos,
                    dias_mapa_calor_consumos,
                    null,
                    datos_mapa_calor_consumos,
                    null,
                    null,
                    true,
                    colores_mapa_calor_consumos,
                    null,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);
            }
            else {
                oculta_elemento("mapa-calor-semanal-consumos-analisis-consumo-estudio-general");
            }

            // Datos de consumos por periodos
            if (hay_datos_consumos_periodos == true) {
                // Restauración de clase del elemento y mostrado de elementos
                switch (tipo_informe) {
                    case TIPO_INFORME_WEB_EMIOS: {
                        cambia_clase_elemento("grafica-consumos-periodos-analisis-consumo-estudio-general", "grafica100");
                        break;
                    }
                    case TIPO_INFORME_FICHERO: {
                        cambia_clase_elemento("grafica-consumos-periodos-analisis-consumo-estudio-general", "grafica100-informe-fichero");
                        break;
                    }
                }
                muestra_elemento("grafica-consumos-periodos-analisis-consumo-estudio-general");

                // Mostrar indicadores de valores
                var numero_valores_grafica_consumos_periodos = dame_numero_maximo_valores_series_grafica(grafica_consumos_periodos);
                var mostrar_indicadores_valores_consumos_periodos = (numero_valores_grafica_consumos_periodos <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de consumo por periodos
                muestra_grafica_temporal_lineas_valores_fechas_diferentes(
                    "grafica-consumos-periodos-analisis-consumo-estudio-general",
                    TLNT.Idiomas._("Consumo") + " (" + TLNT.Idiomas._("comparación de periodos") + ") (" + unidad_medida_consumo + ")",
                    etiquetas_consumos_periodos,
                    etiquetas_tooltips_consumos_periodos,
                    grafica_consumos_periodos, INTERVALO_VALORES_DIA,
                    min_fecha_consumo_periodos, max_fecha_consumo_periodos, false,
                    0, false,
                    max_consumo_periodos, true,
                    2, unidad_medida_consumo,
                    null,
                    true,
                    TIPO_LINEAS_VALORES_ESTANDAR,
                    mostrar_indicadores_valores_consumos_periodos,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);
            }
            else {
                cambia_clase_elemento("grafica-consumos-periodos-analisis-consumo-estudio-general", "texto-elemento-no-mostrado-informe");
                $("#" + "grafica-consumos-periodos-analisis-consumo-estudio-general").html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra información de comparación de periodos de consumo (no hay datos disponibles)"));
            }

            // Notas del apartado
            if (tipo_informe == TIPO_INFORME_FICHERO) {
                if (textos_informe.notas_analisis_consumo != "") {
                    $('#notas-analisis-consumo-estudio-general').html(textos_informe.notas_analisis_consumo);
                }
                else {
                    oculta_elemento("notas-analisis-consumo-estudio-general");
                }
            }
        }
        else {
            // Aviso
            textos_informe.texto_avisos += "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No hay datos de análisis de consumo") + "</br>";
        }
    }
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (mostrar_analisis_consumo == false)) {
        elimina_elemento("pagina-informe-fichero-estudio-general-analisis-consumo");
    }
}


// Análisis de coste
function dibuja_apartado_analisis_coste_smartmeter_estudio_general_gas_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_analisis_coste = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_GAS_ESPANYA) > -1) {
        // Parámetros
        var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;

        // Datos de análisis de coste
        var datos_analisis_coste = datos.datos_analisis_coste;

        // Flags de existencia de datos
        var hay_datos_grafica_costes = datos_analisis_coste.hay_datos_grafica_costes;
        var hay_datos_mapa_calor_costes = datos_analisis_coste.hay_datos_mapa_calor_costes;
        var hay_datos_costes_periodos = datos_analisis_coste.hay_datos_costes_periodos;
        var hay_datos_analisis_coste = (
            (hay_datos_grafica_costes == true) ||
            (hay_datos_mapa_calor_costes == true) ||
            (hay_datos_costes_periodos == true));

        // Datos del apartado
        if (hay_datos_analisis_coste == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-analisis-coste-estudio-general");
                vacia_elementos([
                    "grafica-costes-analisis-coste-estudio-general",
                    "grafica-costes-periodos-analisis-coste-estudio-general",
                    "contenedor-tabla-evolucion-consumos-costes-analisis-coste-estudio-general",
                    "mapa-calor-semanal-costes-analisis-coste-estudio-general"]);
            }
            mostrar_analisis_coste = true;

            // Recuperación de datos del apartado
            if (hay_datos_grafica_costes == true) {
                var max_coste = datos_analisis_coste.max_coste;
                var etiquetas_grafica_costes = datos_analisis_coste.etiquetas_grafica_costes;
                var grafica_costes = datos_analisis_coste.grafica_costes;
            }
            if (hay_datos_mapa_calor_costes == true) {
                var colores_mapa_calor_costes = dame_escala_colores_mapa_calor(datos_analisis_coste.colores_mapa_calor_costes);
                var dias_mapa_calor_costes = datos_analisis_coste.dias_mapa_calor_costes;
                var datos_mapa_calor_costes = datos_analisis_coste.datos_mapa_calor_costes;
                var unidad_medida_coste = datos_analisis_coste.unidad_medida_coste;
            }
            if (hay_datos_costes_periodos == true) {
                var max_coste_periodos = datos_analisis_coste.max_coste_periodos;
                var etiquetas_costes_periodos = datos_analisis_coste.etiquetas_costes_periodos;
                var etiquetas_tooltips_costes_periodos = datos_analisis_coste.etiquetas_tooltips_costes_periodos;
                var min_fecha_coste_periodos = new $.jsDate(datos_analisis_coste.min_fecha_coste_periodos);
                var max_fecha_coste_periodos = new $.jsDate(datos_analisis_coste.max_fecha_coste_periodos);
                var grafica_costes_periodos = datos_analisis_coste.grafica_costes_periodos;
                var tabla_evolucion_consumos_costes = datos_analisis_coste.tabla_evolucion_consumos_costes;
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

            // Datos de gráfica de costes
            if (hay_datos_grafica_costes == true) {
                muestra_elemento("grafica-costes-analisis-coste-estudio-general");

                var numero_valores_grafica_costes = dame_numero_maximo_valores_series_grafica(grafica_costes);
                var mostrar_indicadores_valores_costes = (numero_valores_grafica_costes <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de costes
                muestra_grafica_temporal_lineas_valores(
                    "grafica-costes-analisis-coste-estudio-general",
                    null,
                    TLNT.Idiomas._("Coste") + " (" + unidad_medida_coste + ")",
                    etiquetas_grafica_costes,
                    grafica_costes, null, INTERVALO_VALORES_DIA,
                    null,
                    fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                    0, false,
                    max_coste, true,
                    2, unidad_medida_coste,
                    null,
                    true,
                    TIPO_LINEAS_VALORES_ESTANDAR,
                    mostrar_indicadores_valores_costes,
                    false,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);
            }
            else {
                oculta_elemento("grafica-costes-analisis-coste-estudio-general");
            }

            // Datos de mapa de calor de costes
            if (hay_datos_mapa_calor_costes == true) {
                muestra_elemento("mapa-calor-semanal-costes-analisis-coste-estudio-general");

                // Mapa de calor semanal de costes
                var titulo_mapa_calor_semanal_costes = TLNT.Idiomas._("Mapa de calor semanal de") + " " + TLNT.Idiomas._("coste") + " (" + unidad_medida_coste + ")";
                muestra_grafico_mapa_calor(
                    "mapa-calor-semanal-costes-analisis-coste-estudio-general",
                    TIPO_MAPA_CALOR_SEMANAL,
                    titulo_mapa_calor_semanal_costes,
                    dias_mapa_calor_costes,
                    null,
                    datos_mapa_calor_costes,
                    null,
                    null,
                    true,
                    colores_mapa_calor_costes,
                    null,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);
            }
            else {
                oculta_elemento("mapa-calor-semanal-costes-analisis-coste-estudio-general");
            }

            // Datos de costes por periodos
            if (hay_datos_costes_periodos == true) {
                // Restauración de clase del elemento y mostrado de elementos
                switch (tipo_informe) {
                    case TIPO_INFORME_WEB_EMIOS: {
                        cambia_clase_elemento("grafica-costes-periodos-analisis-coste-estudio-general", "grafica100");
                        break;
                    }
                    case TIPO_INFORME_FICHERO: {
                        cambia_clase_elemento("grafica-costes-periodos-analisis-coste-estudio-general", "grafica100-informe-fichero");
                        break;
                    }
                }
                muestra_elementos([
                    "grafica-costes-periodos-analisis-coste-estudio-general",
                    "contenedor-tabla-evolucion-consumos-costes-analisis-coste-estudio-general"]);

                // Mostrar indicadores de valores
                var numero_valores_grafica_costes_periodos = dame_numero_maximo_valores_series_grafica(grafica_costes_periodos);
                var mostrar_indicadores_valores_costes_periodos = (numero_valores_grafica_costes_periodos <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de coste por periodos
                muestra_grafica_temporal_lineas_valores_fechas_diferentes(
                    "grafica-costes-periodos-analisis-coste-estudio-general",
                    TLNT.Idiomas._("Coste") + " (" + TLNT.Idiomas._("comparación de periodos") + ") (" + unidad_medida_coste + ")",
                    etiquetas_costes_periodos,
                    etiquetas_tooltips_costes_periodos,
                    grafica_costes_periodos, INTERVALO_VALORES_DIA,
                    min_fecha_coste_periodos, max_fecha_coste_periodos, false,
                    0, false,
                    max_coste_periodos, true,
                    2, unidad_medida_coste,
                    null,
                    true,
                    TIPO_LINEAS_VALORES_ESTANDAR,
                    mostrar_indicadores_valores_costes_periodos,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);

                // Tabla de evolución de consumos y costes
                $('#contenedor-tabla-evolucion-consumos-costes-analisis-coste-estudio-general').html(tabla_evolucion_consumos_costes);
                if (anyadir_menus_contextuales == true) {
                    var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                    anyade_menu_contextual('tabla-evolucion-consumos-costes-comparacion-periodos', info_menu_contextual, TLNT.Idiomas._('Consumos y costes'));
                }
            }
            else {
                cambia_clase_elemento("grafica-costes-periodos-analisis-coste-estudio-general", "texto-elemento-no-mostrado-informe");
                $("#" + "grafica-costes-periodos-analisis-coste-estudio-general").html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra información de comparación de periodos de coste (no hay datos disponibles)"));
                oculta_elemento("contenedor-tabla-evolucion-consumos-costes-analisis-coste-estudio-general");
            }

            // Notas del apartado
            if (tipo_informe == TIPO_INFORME_FICHERO) {
                if (textos_informe.notas_analisis_coste != "") {
                    $('#notas-analisis-coste-estudio-general').html(textos_informe.notas_analisis_coste);
                }
                else {
                    oculta_elemento("notas-analisis-coste-estudio-general");
                }
            }
        }
        else {
            // Aviso
            textos_informe.texto_avisos += "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No hay datos de análisis de coste") + "</br>";
        }
    }
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (mostrar_analisis_coste == false)) {
        elimina_elemento("pagina-informe-fichero-estudio-general-analisis-coste");
    }
}


// Excesos de caudal
function dibuja_apartado_excesos_caudal_smartmeter_estudio_general_gas_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_excesos_caudal = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_CAUDAL_GAS_ESPANYA) > -1) {
        // Parámetros
        var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;

        // Datos de excesos de caudal
        var datos_excesos_caudal = datos.datos_excesos_caudal;

        // Flags de existencia de datos
        var hay_datos_excesos_caudal = datos_excesos_caudal.hay_datos_excesos_caudal;
        if (hay_datos_excesos_caudal == true) {
            var hay_datos_grafica_caudales_sobrecaudales_tabla_sobrecaudales = datos_excesos_caudal.hay_datos_grafica_caudales_sobrecaudales_tabla_sobrecaudales;
            hay_datos_excesos_caudal = (
                (hay_datos_grafica_caudales_sobrecaudales_tabla_sobrecaudales == true));
        }

        // Datos del apartado
        if (hay_datos_excesos_caudal == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-excesos-caudal-estudio-general");
                vacia_elementos([
                    "grafica-caudales-sobrecaudales-excesos-caudal-estudio-general",
                    "contenedor-tabla-sobrecaudales-excesos-caudal-estudio-general"]);
            }
            mostrar_excesos_caudal = true;

            // Recuperación de datos del apartado
            if (hay_datos_grafica_caudales_sobrecaudales_tabla_sobrecaudales == true) {
                var grafica_caudales_sobrecaudales = datos_excesos_caudal.grafica_caudales_sobrecaudales;
                var max_caudal = datos_excesos_caudal.max_caudal;
                var caudal_diario_contratado = datos_excesos_caudal.caudal_diario_contratado;
                var tabla_sobrecaudales = datos_excesos_caudal.tabla_sobrecaudales;
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

            // Datos de gráfica de caudales y sobrecaudales y tabla de sobrecaudales
            if (hay_datos_grafica_caudales_sobrecaudales_tabla_sobrecaudales == true) {
                muestra_elementos([
                    "grafica-caudales-sobrecaudales-excesos-caudal-estudio-general",
                    "contenedor-tabla-sobrecaudales-excesos-caudal-estudio-general"]);

                var numero_valores_grafica_caudales_sobrecaudales = dame_numero_maximo_valores_series_grafica(grafica_caudales_sobrecaudales);
                var mostrar_indicadores_valores_caudales_sobrecaudales = (numero_valores_grafica_caudales_sobrecaudales <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de caudales y sobrecaudales
                var lineas_referencia = [dame_linea_referencia_grafica_valores(
                    caudal_diario_contratado,
                    TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA,
                    "rgba(255, 150, 150, 0.5)",
                    TLNT.Idiomas._("Caudal diario contratado"))];
                muestra_grafica_temporal_lineas_puntos_valores(
                    "grafica-caudales-sobrecaudales-excesos-caudal-estudio-general",
                    TLNT.Idiomas._("Caudales diarios y excesos de caudal diario") + " (" + TLNT.Idiomas._("kWh") + ")",
                    [TLNT.Idiomas._("Caudales"), TLNT.Idiomas._("Excesos de caudal")],
                    grafica_caudales_sobrecaudales, INTERVALO_VALORES_DIA,
                    fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                    0, false,
                    max_caudal, true,
                    2, TLNT.Idiomas._("kWh"),
                    lineas_referencia,
                    mostrar_indicadores_valores_caudales_sobrecaudales,
                    false,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);

                // Tabla de sobrecaudales
                $('#contenedor-tabla-sobrecaudales-excesos-caudal-estudio-general').html(tabla_sobrecaudales);
                if (anyadir_menus_contextuales == true) {
                    var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                    anyade_menu_contextual('tabla-sobrecaudal-sensor-excesos-caudal', info_menu_contextual, TLNT.Idiomas._('Excesos de caudal'));
                }
            }
            else {
                oculta_elementos([
                    "grafica-caudales-sobrecaudales-excesos-caudal-estudio-general",
                    "contenedor-tabla-sobrecaudales-excesos-caudal-estudio-general"]);
            }

            // Notas del apartado
            if (tipo_informe == TIPO_INFORME_FICHERO) {
                if (textos_informe.notas_excesos_caudal != "") {
                    $('#notas-excesos-caudal-estudio-general').html(textos_informe.notas_excesos_caudal);
                }
                else {
                    oculta_elemento("notas-excesos-caudal-estudio-general");
                }
            }
        }
        else {
            // Aviso
            textos_informe.texto_avisos += "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No hay datos de excesos de caudal") + "</br>";
        }
    }
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (mostrar_excesos_caudal == false)) {
        elimina_elemento("pagina-informe-fichero-estudio-general-excesos-caudal");
    }
}


// Simulación de factura
function dibuja_apartado_simulacion_factura_smartmeter_estudio_general_gas_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_simulacion_factura = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_GAS_ESPANYA) > -1) {
        // Datos de simulación de factura
        var datos_simulacion_factura = datos.datos_simulacion_factura;

        // Flags de existencia de datos
        var hay_datos_simulacion_factura = datos_simulacion_factura.hay_datos_simulacion_factura;
        if (hay_datos_simulacion_factura == true) {
            var hay_datos = datos_simulacion_factura.hay_datos;
            hay_datos_simulacion_factura = (
                (hay_datos == true));
        }

        // Datos del apartado
        if (hay_datos_simulacion_factura == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-simulacion-factura-estudio-general");
                vacia_elementos([
                    "contenedor-tabla-coste-consumo-simulacion-factura-estudio-general",
                    "contenedor-tabla-consumo-simulacion-factura-estudio-general",
                    "contenedor-tabla-termino-fijo-simulacion-factura-estudio-general",
                    "contenedor-tabla-otros-conceptos-simulacion-factura-estudio-general"]);
            }
            mostrar_simulacion_factura = true;

            // Recuperación de datos del apartado
            if (hay_datos == true) {
                var tabla_coste_consumo = datos_simulacion_factura.tabla_coste_consumo;
                var tabla_consumo = datos_simulacion_factura.tabla_consumo;
                var tabla_termino_fijo = datos_simulacion_factura.tabla_termino_fijo;
                var tabla_otros_conceptos = datos_simulacion_factura.tabla_otros_conceptos;
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

            // Títulos
            $("#titulo-resumen-simulacion-factura-estudio-general").html(TLNT.Idiomas._("Resumen de factura"));
            $("#titulo-detalles-simulacion-factura-estudio-general").html(TLNT.Idiomas._("Detalles de factura"));

            // Tablas de simulación de factura

            // Se rellena la tabla de coste y consumo (total y diario)
            $("#contenedor-tabla-coste-consumo-simulacion-factura-estudio-general").html(tabla_coste_consumo);
            if (anyadir_menus_contextuales == true) {
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual('tabla-coste-consumo-simulador-factura-gas', info_menu_contextual, TLNT.Idiomas._('Coste y consumo'));
            }

            // Se rellena la tabla de consumo
            $("#contenedor-tabla-consumo-simulacion-factura-estudio-general").html(tabla_consumo);
            if (anyadir_menus_contextuales == true) {
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual('tabla-consumo-simulador-factura-gas', info_menu_contextual, TLNT.Idiomas._('Consumo'));
            }

            // Se rellena la tabla de término fijo
            $("#contenedor-tabla-termino-fijo-simulacion-factura-estudio-general").html(tabla_termino_fijo);
            if (anyadir_menus_contextuales == true) {
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual('tabla-termino-fijo-simulador-factura-gas', info_menu_contextual, TLNT.Idiomas._('Término fijo'));
            }

            // Tabla de otros conceptos
            $("#contenedor-tabla-otros-conceptos-simulacion-factura-estudio-general").html(tabla_otros_conceptos);
            if (anyadir_menus_contextuales == true) {
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual('tabla-otros-conceptos-simulador-factura-gas', info_menu_contextual, TLNT.Idiomas._('Otros conceptos'));
            }

            // Notas del apartado
            if (tipo_informe == TIPO_INFORME_FICHERO) {
                if (textos_informe.notas_simulacion_factura != "") {
                    $('#notas-simulacion-factura-estudio-general').html(textos_informe.notas_simulacion_factura);
                }
                else {
                    oculta_elemento("notas-simulacion-factura-estudio-general");
                }
            }
        }
        else {
            textos_informe.texto_avisos += "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No hay datos de simulación de factura") + "</br>";
        }
    }
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (mostrar_simulacion_factura == false)) {
        elimina_elemento("pagina-informe-fichero-estudio-general-simulacion-factura");
    }
}


// Conclusiones
function dibuja_apartado_conclusiones_smartmeter_estudio_general_gas_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_conclusiones = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_GAS_ESPANYA) > -1) {
        // Se muestra el apartado
        if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
            muestra_elemento("apartado-conclusiones-estudio-general");
        }

        // Conclusiones
        if (tipo_informe == TIPO_INFORME_FICHERO) {
            if (textos_informe.conclusiones != "") {
                mostrar_conclusiones = true;
                $('#conclusiones-estudio-general').html(textos_informe.conclusiones);
            }
        }
    }
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (mostrar_conclusiones == false)) {
        elimina_elemento("pagina-informe-fichero-estudio-general-conclusiones");
    }
}

// Avisos
function dibuja_apartado_avisos_smartmeter_estudio_general_gas_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    // Avisos
    if (textos_informe.texto_avisos != "") {
        // Se muestra el apartado
        if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
            muestra_elemento("apartado-avisos-estudio-general");
        }

        // Texto de avisos
        $("#texto-avisos-estudio-general").html(textos_informe.texto_avisos);
    }
    else {
        if (tipo_informe == TIPO_INFORME_FICHERO) {
            elimina_elemento("pagina-informe-fichero-estudio-general-avisos");
        }
    }
}
