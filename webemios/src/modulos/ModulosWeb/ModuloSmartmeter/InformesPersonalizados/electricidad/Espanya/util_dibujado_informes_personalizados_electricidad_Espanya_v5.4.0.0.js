//
// Funciones para el dibujado de los informes personalizados (SmartMeter) (electricidad - España)
//


// Dibujado del informe de estudio general
function dibuja_informe_smartmeter_estudio_general_electricidad_Espanya(
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
        inicializa_informe_web_smartmeter_estudio_general_electricidad_Espanya(texto_introduccion);
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
            textos_informe = dame_textos_informe_fichero_smartmeter_estudio_general_electricidad_Espanya(parametros_tipo_json);
            break;
        }
    }
    textos_informe.texto_avisos = "";

    // Se dibujan los apartados seleccionados

    // Portada
    dibuja_apartado_portada_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Introducción
    dibuja_apartado_introduccion_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Instalación
    dibuja_apartado_instalacion_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Análisis de consumo
    dibuja_apartado_analisis_consumo_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Resumen de consumo
    dibuja_apartado_resumen_consumo_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Análisis de coste
    dibuja_apartado_analisis_coste_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Resumen de coste
    dibuja_apartado_resumen_coste_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Excesos de potencia
    dibuja_apartado_excesos_potencia_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Excesos de energía reactiva
    dibuja_apartado_excesos_energia_reactiva_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Cortes de tensión
    dibuja_apartado_cortes_tension_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Simulación de factura
    dibuja_apartado_simulacion_factura_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Conclusiones
    dibuja_apartado_conclusiones_smartmeter_estudio_general_electricidad_Espanya(
        parametros,
        datos,
        tipo_informe,
        apartados,
        textos_informe);

    // Avisos
    dibuja_apartado_avisos_smartmeter_estudio_general_electricidad_Espanya(
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
function inicializa_informe_web_smartmeter_estudio_general_electricidad_Espanya() {
    // Portada
    oculta_elemento("apartado_portada_estudio_general");

    // https://stackoverflow.com/questions/15545557/how-to-trigger-an-input-event-with-jquery

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

    // Resumen de consumo
    $("#notas-resumen-consumo-estudio-general").val("");
    $("#notas-resumen-consumo-estudio-general").trigger("input");
    oculta_elemento("apartado-resumen-consumo-estudio-general");

    // Análisis de coste
    $("#notas-analisis-coste-estudio-general").val("");
    $("#notas-analisis-coste-estudio-general").trigger("input");
    oculta_elemento("apartado-analisis-coste-estudio-general");

    // Resumen de coste
    $("#notas-resumen-coste-estudio-general").val("");
    $("#notas-resumen-coste-estudio-general").trigger("input");
    oculta_elemento("apartado-resumen-coste-estudio-general");

    // Excesos de potencia
    $("#notas-excesos-potencia-estudio-general").val("");
    $("#notas-excesos-potencia-estudio-general").trigger("input");
    oculta_elemento("apartado-excesos-potencia-estudio-general");

    // Excesos de energía reactiva
    $("#notas-excesos-energia-reactiva-estudio-general").val("");
    $("#notas-excesos-energia-reactiva-estudio-general").trigger("input");
    oculta_elemento("apartado-excesos-energia-reactiva-estudio-general");

    // Cortes de tensión
    $("#notas-cortes-tension-estudio-general").val("");
    $("#notas-cortes-tension-estudio-general").trigger("input");
    oculta_elemento("apartado-cortes-tension-estudio-general");

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
function dame_textos_informe_fichero_smartmeter_estudio_general_electricidad_Espanya(parametros_tipo_json) {
    var texto_introduccion = parametros_tipo_json["texto_introduccion"];
    var notas_instalacion = "";
    if ("notas_instalacion" in parametros_tipo_json) {
        notas_instalacion = parametros_tipo_json["notas_instalacion"];
    }
    var notas_analisis_consumo = "";
    if ("notas_analisis_consumo" in parametros_tipo_json) {
        notas_analisis_consumo = parametros_tipo_json["notas_analisis_consumo"];
    }
    var notas_resumen_consumo = "";
    if ("notas_resumen_consumo" in parametros_tipo_json) {
        notas_resumen_consumo = parametros_tipo_json["notas_resumen_consumo"];
    }
    var notas_analisis_coste = "";
    if ("notas_analisis_coste" in parametros_tipo_json) {
        notas_analisis_coste = parametros_tipo_json["notas_analisis_coste"];
    }
    var notas_resumen_coste = "";
    if ("notas_resumen_coste" in parametros_tipo_json) {
        notas_resumen_coste = parametros_tipo_json["notas_resumen_coste"];
    }
    var notas_excesos_potencia = "";
    if ("notas_excesos_potencia" in parametros_tipo_json) {
        notas_excesos_potencia = parametros_tipo_json["notas_excesos_potencia"];
    }
    var notas_excesos_energia_reactiva = "";
    if ("notas_excesos_energia_reactiva" in parametros_tipo_json) {
        notas_excesos_energia_reactiva = parametros_tipo_json["notas_excesos_energia_reactiva"];
    }
    var notas_cortes_tension = "";
    if ("notas_cortes_tension" in parametros_tipo_json) {
        notas_cortes_tension = parametros_tipo_json["notas_cortes_tension"];
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
    notas_resumen_consumo = formatea_texto_informe_html(notas_resumen_consumo);
    notas_analisis_coste = formatea_texto_informe_html(notas_analisis_coste);
    notas_resumen_coste = formatea_texto_informe_html(notas_resumen_coste);
    notas_excesos_potencia = formatea_texto_informe_html(notas_excesos_potencia);
    notas_excesos_energia_reactiva = formatea_texto_informe_html(notas_excesos_energia_reactiva);
    notas_cortes_tension = formatea_texto_informe_html(notas_cortes_tension);
    notas_simulacion_factura = formatea_texto_informe_html(notas_simulacion_factura);
    conclusiones = formatea_texto_informe_html(conclusiones);

    var textos_informe_fichero = {
        texto_introduccion: texto_introduccion,
        notas_instalacion: notas_instalacion,
        notas_analisis_consumo: notas_analisis_consumo,
        notas_resumen_consumo: notas_resumen_consumo,
        notas_analisis_coste: notas_analisis_coste,
        notas_resumen_coste: notas_resumen_coste,
        notas_excesos_potencia: notas_excesos_potencia,
        notas_excesos_energia_reactiva: notas_excesos_energia_reactiva,
        notas_cortes_tension: notas_cortes_tension,
        notas_simulacion_factura: notas_simulacion_factura,
        conclusiones: conclusiones};
    return (textos_informe_fichero);
}


//
// Funciones de dibujado de apartados
//


// Portada
function dibuja_apartado_portada_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_ELECTRICIDAD_ESPANYA) > -1) {
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
function dibuja_apartado_introduccion_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_ELECTRICIDAD_ESPANYA) > -1) {
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
function dibuja_apartado_instalacion_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_ELECTRICIDAD_ESPANYA) > -1) {
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
                "contrato-instalacion-estudio-general",
                "titulo-formula-precio-consumo-instalacion-estudio-general",
                "formula-precio-consumo-instalacion-estudio-general",
                "fecha-inicio-instalacion-estudio-general",
                "fecha-fin-instalacion-estudio-general",
                "contenedor-tabla-tramos-tarifa-electrica-instalacion-estudio-general"]);
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
            id_contrato: "contrato-instalacion-estudio-general",
            id_titulo_formula_precio_consumo: "titulo-formula-precio-consumo-instalacion-estudio-general",
            id_formula_precio_consumo: "formula-precio-consumo-instalacion-estudio-general",
            id_contenedor_tabla_tramos_tarifa_electrica: "contenedor-tabla-tramos-tarifa-electrica-instalacion-estudio-general"};
        dibuja_instalacion_electricidad_Espanya(
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
function dibuja_apartado_analisis_consumo_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_analisis_consumo = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_ELECTRICIDAD_ESPANYA) > -1) {
        // Parámetros
        var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;

        // Datos de análisis de consumo
        var datos_analisis_consumo = datos.datos_analisis_consumo;

        // Flags de existencia de datos
        var hay_datos_grafica_consumos = datos_analisis_consumo.hay_datos_grafica_consumos;
        var hay_datos_mapa_calor_consumos = datos_analisis_consumo.hay_datos_mapa_calor_consumos;
        var hay_datos_consumos_periodos = datos_analisis_consumo.hay_datos_consumos_periodos;
        var hay_datos_consumos_tramos = datos_analisis_consumo.hay_datos_consumos_tramos;
        var hay_datos_analisis_consumo = (
            (hay_datos_grafica_consumos == true) ||
            (hay_datos_mapa_calor_consumos == true) ||
            (hay_datos_consumos_periodos == true) ||
            (hay_datos_consumos_tramos == true));

        // Datos del apartado
        if (hay_datos_analisis_consumo == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-analisis-consumo-estudio-general");
                vacia_elementos([
                    "grafica-consumos-analisis-consumo-estudio-general",
                    "grafica-consumos-periodos-analisis-consumo-estudio-general",
                    "contenedor-tabla-evolucion-consumos-tramos-analisis-consumo-estudio-general",
                    "grafica-consumos-tramos-diarios-analisis-consumo-estudio-general",
                    "contenedor-tabla-consumos-tramos-analisis-consumo-estudio-general",
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
                var tabla_evolucion_consumos_tramos = datos_analisis_consumo.tabla_evolucion_consumos_tramos;
            }
            if (hay_datos_consumos_tramos == true) {
                var max_media_consumo_dia_semana = datos_analisis_consumo.max_media_consumo_dia_semana;
                var nombres_tramos = datos_analisis_consumo.nombres_tramos;
                var grafica_medias_consumos_tramos_dias_semana = datos_analisis_consumo.grafica_medias_consumos_tramos_dias_semana;
                var tabla_consumos_tramos = datos_analisis_consumo.tabla_consumos_tramos;
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

            // Nombres de días de la semana
            var nombres_dias_semana = dame_nombres_dias_semana();

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
                muestra_elementos([
                    "grafica-consumos-periodos-analisis-consumo-estudio-general",
                    "contenedor-tabla-evolucion-consumos-tramos-analisis-consumo-estudio-general"]);

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

                // Tabla de evolución de consumos por tramo
                $('#contenedor-tabla-evolucion-consumos-tramos-analisis-consumo-estudio-general').html(tabla_evolucion_consumos_tramos);
                if (anyadir_menus_contextuales == true) {
                    var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                    anyade_menu_contextual('tabla-evolucion-consumos-tramo-comparacion-periodos', info_menu_contextual, TLNT.Idiomas._('Evolución consumo por tramo'));
                }
            }
            else {
                // Se muestra un mensaje de aviso
                cambia_clase_elemento("grafica-consumos-periodos-analisis-consumo-estudio-general", "texto-elemento-no-mostrado-informe");
                $("#" + "grafica-consumos-periodos-analisis-consumo-estudio-general").html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra información de comparación de periodos de consumo (no hay datos disponibles)"));
                oculta_elemento("contenedor-tabla-evolucion-consumos-tramos-analisis-consumo-estudio-general");
            }

            // Datos de consumos por tramos
            if (hay_datos_consumos_tramos == true) {
                // Restauración de clase del elemento y mostrado de elementos
                switch (tipo_informe) {
                    case TIPO_INFORME_WEB_EMIOS: {
                        cambia_clase_elemento("grafica-consumos-tramos-diarios-analisis-consumo-estudio-general", "grafica100");
                        break;
                    }
                    case TIPO_INFORME_FICHERO: {
                        cambia_clase_elemento("grafica-consumos-tramos-diarios-analisis-consumo-estudio-general", "grafica100-informe-fichero");
                        break;
                    }
                }
                muestra_elementos([
                    "grafica-consumos-tramos-diarios-analisis-consumo-estudio-general",
                    "contenedor-tabla-consumos-tramos-analisis-consumo-estudio-general"]);

                // Grafica de consumos por tramo por día de la semana
                muestra_grafica_puntual_barras_valores(
                    "grafica-consumos-tramos-diarios-analisis-consumo-estudio-general",
                    TLNT.Idiomas._("Media de consumos por tramo por día de la semana") + " (" + unidad_medida_consumo + ")",
                    nombres_tramos,
                    grafica_medias_consumos_tramos_dias_semana,
                    nombres_dias_semana, [6, 7],
                    max_media_consumo_dia_semana, true,
                    2, unidad_medida_consumo,
                    true, false,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);

                // Tabla de consumos por tramo
                $('#contenedor-tabla-consumos-tramos-analisis-consumo-estudio-general').html(tabla_consumos_tramos);
                if (anyadir_menus_contextuales == true) {
                    var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                    anyade_menu_contextual('tabla-consumos-tramos-consumos-costes-tramos', info_menu_contextual, TLNT.Idiomas._('Consumos por tramo'));
                }
            }
            else {
                // Se muestra un mensaje de aviso
                cambia_clase_elemento("grafica-consumos-tramos-diarios-analisis-consumo-estudio-general", "texto-elemento-no-mostrado-informe");
                $("#" + "grafica-consumos-tramos-diarios-analisis-consumo-estudio-general").html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra información de consumos por tramo (no hay datos disponibles)"));
                oculta_elemento("contenedor-tabla-consumos-tramos-analisis-consumo-estudio-general");
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
        elimina_elementos([
            "pagina-informe-fichero-estudio-general-analisis-consumo-1",
            "pagina-informe-fichero-estudio-general-analisis-consumo-2"]);
    }
}


// Resumen de consumo
function dibuja_apartado_resumen_consumo_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_resumen_consumo = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_CONSUMO_ELECTRICIDAD_ESPANYA) > -1) {
        // Parámetros
        var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;

        // Datos de resumen de consumo
        var datos_resumen_consumo = datos.datos_resumen_consumo;

        // Flags de existencia de datos
        var hay_datos_grafica_consumos_mapa_calor_consumos = datos_resumen_consumo.hay_datos_grafica_consumos_mapa_calor_consumos;
        var hay_datos_consumos_periodos = datos_resumen_consumo.hay_datos_consumos_periodos;
        var hay_datos_resumen_consumo = (
            (hay_datos_grafica_consumos_mapa_calor_consumos == true) ||
            (hay_datos_consumos_periodos == true));

        // Datos del apartado
        if (hay_datos_resumen_consumo == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-resumen-consumo-estudio-general");
                vacia_elementos([
                    "grafica-consumos-resumen-consumo-estudio-general",
                    "grafica-consumos-periodos-resumen-consumo-estudio-general",
                    "mapa-calor-semanal-consumos-resumen-consumo-estudio-general"]);
            }
            mostrar_resumen_consumo = true;

            // Recuperación de datos del apartado
            if (hay_datos_grafica_consumos_mapa_calor_consumos == true) {
                var max_consumo = datos_resumen_consumo.max_consumo;
                var etiquetas_grafica_consumos = datos_resumen_consumo.etiquetas_grafica_consumos;
                var grafica_consumos = datos_resumen_consumo.grafica_consumos;
                var colores_mapa_calor_consumos = dame_escala_colores_mapa_calor(datos_resumen_consumo.colores_mapa_calor_consumos);
                var dias_mapa_calor_consumos = datos_resumen_consumo.dias_mapa_calor_consumos;
                var datos_mapa_calor_consumos = datos_resumen_consumo.datos_mapa_calor_consumos;
                var unidad_medida_consumo = datos_resumen_consumo.unidad_medida_consumo;
            }
            if (hay_datos_consumos_periodos == true) {
                var max_consumo_periodos = datos_resumen_consumo.max_consumo_periodos;
                var etiquetas_consumos_periodos = datos_resumen_consumo.etiquetas_consumos_periodos;
                var etiquetas_tooltips_consumos_periodos = datos_resumen_consumo.etiquetas_tooltips_consumos_periodos;
                var min_fecha_consumo_periodos = new $.jsDate(datos_resumen_consumo.min_fecha_consumo_periodos);
                var max_fecha_consumo_periodos = new $.jsDate(datos_resumen_consumo.max_fecha_consumo_periodos);
                var grafica_consumos_periodos = datos_resumen_consumo.grafica_consumos_periodos;
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

            // Datos de gráfica de consumos y de mapa de calor de consumos
            if (hay_datos_grafica_consumos_mapa_calor_consumos == true) {
                muestra_elementos([
                    "grafica-consumos-resumen-consumo-estudio-general",
                    "mapa-calor-semanal-consumos-resumen-consumo-estudio-general"]);

                var numero_valores_grafica_consumos = dame_numero_maximo_valores_series_grafica(grafica_consumos);
                var mostrar_indicadores_valores_consumos = (numero_valores_grafica_consumos <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de consumos
                muestra_grafica_temporal_lineas_valores(
                    "grafica-consumos-resumen-consumo-estudio-general",
                    null,
                    TLNT.Idiomas._("Consumo") + " (" + unidad_medida_consumo + ")",
                    etiquetas_grafica_consumos,
                    grafica_consumos, null, INTERVALO_VALORES_HORA,
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

                // Mapa de calor semanal de consumos
                var titulo_mapa_calor_semanal_consumos = TLNT.Idiomas._("Mapa de calor semanal de") + " " + TLNT.Idiomas._("consumo") + " (" + unidad_medida_consumo + ")";
                muestra_grafico_mapa_calor(
                    "mapa-calor-semanal-consumos-resumen-consumo-estudio-general",
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
                oculta_elementos([
                    "grafica-consumos-resumen-consumo-estudio-general",
                    "mapa-calor-semanal-consumos-resumen-consumo-estudio-general"]);
            }

            // Datos de consumos por periodos
            if (hay_datos_consumos_periodos == true) {
                // Restauración de clase del elemento y mostrado de elementos
                switch (tipo_informe) {
                    case TIPO_INFORME_WEB_EMIOS: {
                        cambia_clase_elemento("grafica-consumos-periodos-resumen-consumo-estudio-general", "grafica100");
                        break;
                    }
                    case TIPO_INFORME_FICHERO: {
                        cambia_clase_elemento("grafica-consumos-periodos-resumen-consumo-estudio-general", "grafica100-informe-fichero");
                        break;
                    }
                }
                muestra_elemento("grafica-consumos-periodos-resumen-consumo-estudio-general");

                // Mostrar indicadores de valores
                var numero_valores_grafica_consumos_periodos = dame_numero_maximo_valores_series_grafica(grafica_consumos_periodos);
                var mostrar_indicadores_valores_consumo_periodos = (numero_valores_grafica_consumos_periodos <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Grafica de consumo por periodos
                muestra_grafica_temporal_lineas_valores_fechas_diferentes(
                    "grafica-consumos-periodos-resumen-consumo-estudio-general",
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
                    mostrar_indicadores_valores_consumo_periodos,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);
            }
            else {
                // Se muestra un mensaje de aviso
                cambia_clase_elemento("grafica-consumos-periodos-resumen-consumo-estudio-general", "texto-elemento-no-mostrado-informe");
                $("#" + "grafica-consumos-periodos-resumen-consumo-estudio-general").html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra información de comparación de periodos de consumo (no hay datos disponibles)"));
            }

            // Notas del apartado
            if (tipo_informe == TIPO_INFORME_FICHERO) {
                if (textos_informe.notas_resumen_consumo != "") {
                    $('#notas-resumen-consumo-estudio-general').html(textos_informe.notas_resumen_consumo);
                }
                else {
                    oculta_elemento("notas-resumen-consumo-estudio-general");
                }
            }
        }
        else {
            // Aviso
            textos_informe.texto_avisos += "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No hay datos de resumen de consumo") + "</br>";
        }
    }
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (mostrar_resumen_consumo == false)) {
        elimina_elemento("pagina-informe-fichero-estudio-general-resumen-consumo");
    }
}


// Análisis de coste
function dibuja_apartado_analisis_coste_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_analisis_coste = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_COSTE_ELECTRICIDAD_ESPANYA) > -1) {
        // Parámetros
        var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;

        // Datos de análisis de coste
        var datos_analisis_coste = datos.datos_analisis_coste;

        // Flags de existencia de datos
        var hay_datos_grafica_costes = datos_analisis_coste.hay_datos_grafica_costes;
        var hay_datos_mapa_calor_costes = datos_analisis_coste.hay_datos_mapa_calor_costes;
        var hay_datos_costes_periodos = datos_analisis_coste.hay_datos_costes_periodos;
        var hay_datos_costes_tramos = datos_analisis_coste.hay_datos_costes_tramos;
        var hay_datos_analisis_coste = (
            (hay_datos_grafica_costes == true) ||
            (hay_datos_mapa_calor_costes == true) ||
            (hay_datos_costes_periodos == true) ||
            (hay_datos_costes_tramos == true));

        // Datos del apartado
        if (hay_datos_analisis_coste == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-analisis-coste-estudio-general");
                vacia_elementos([
                    "grafica-costes-analisis-coste-estudio-general",
                    "grafica-costes-periodos-analisis-coste-estudio-general",
                    "contenedor-tabla-evolucion-consumos-costes-analisis-coste-estudio-general",
                    "grafica-costes-tramos-diarios-analisis-coste-estudio-general",
                    "contenedor-tabla-costes-tramos-analisis-coste-estudio-general",
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
            if (hay_datos_costes_tramos == true) {
                var max_media_coste_dia_semana = datos_analisis_coste.max_media_coste_dia_semana;
                var nombres_tramos_coste = datos_analisis_coste.nombres_tramos;
                var grafica_medias_costes_tramos_dias_semana = datos_analisis_coste.grafica_medias_costes_tramos_dias_semana;
                var tabla_costes_tramos = datos_analisis_coste.tabla_costes_tramos;
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

            // Nombres de días de la semana
            var nombres_dias_semana = dame_nombres_dias_semana();

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
                // Se muestra un mensaje de aviso
                cambia_clase_elemento("grafica-costes-periodos-analisis-coste-estudio-general", "texto-elemento-no-mostrado-informe");
                $("#" + "grafica-costes-periodos-analisis-coste-estudio-general").html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra información de comparación de periodos de coste (no hay datos disponibles)"));
                oculta_elemento("contenedor-tabla-evolucion-consumos-costes-analisis-coste-estudio-general");
            }

            // Datos de costes por tramos
            if (hay_datos_costes_tramos == true) {
                // Restauración de clase del elemento y mostrado de elementos
                switch (tipo_informe) {
                    case TIPO_INFORME_WEB_EMIOS: {
                        cambia_clase_elemento("grafica-costes-tramos-diarios-analisis-coste-estudio-general", "grafica100");
                        break;
                    }
                    case TIPO_INFORME_FICHERO: {
                        cambia_clase_elemento("grafica-costes-tramos-diarios-analisis-coste-estudio-general", "grafica100-informe-fichero");
                        break;
                    }
                }
                muestra_elementos([
                    "grafica-costes-tramos-diarios-analisis-coste-estudio-general",
                    "contenedor-tabla-costes-tramos-analisis-coste-estudio-general"]);

                // Gráfica de costes por tramo por día de la semana
                muestra_grafica_puntual_barras_valores(
                    "grafica-costes-tramos-diarios-analisis-coste-estudio-general",
                    TLNT.Idiomas._("Media de costes por tramo por día de la semana") + " (" + unidad_medida_coste + ")",
                    nombres_tramos_coste,
                    grafica_medias_costes_tramos_dias_semana,
                    nombres_dias_semana, [6, 7],
                    max_media_coste_dia_semana, true,
                    2, unidad_medida_coste,
                    true, false,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);

                // Tabla de costes por tramo
                $('#contenedor-tabla-costes-tramos-analisis-coste-estudio-general').html(tabla_costes_tramos);
                if (anyadir_menus_contextuales == true) {
                    var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                    anyade_menu_contextual('tabla-costes-tramos-consumos-costes-tramos', info_menu_contextual, TLNT.Idiomas._('Costes por tramo'));
                }
            }
            else {
                // Se muestra un mensaje de aviso
                cambia_clase_elemento("grafica-costes-tramos-diarios-analisis-coste-estudio-general", "texto-elemento-no-mostrado-informe");
                $("#" + "grafica-costes-tramos-diarios-analisis-coste-estudio-general").html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra información de costes por tramo (no hay datos disponibles)"));
                oculta_elemento("contenedor-tabla-costes-tramos-analisis-coste-estudio-general");
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
        elimina_elementos([
            "pagina-informe-fichero-estudio-general-analisis-coste-1",
            "pagina-informe-fichero-estudio-general-analisis-coste-2"]);
    }
}


// Resumen de coste
function dibuja_apartado_resumen_coste_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_resumen_coste = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_RESUMEN_COSTE_ELECTRICIDAD_ESPANYA) > -1) {
        // Parámetros
        var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;

        // Datos de resumen de coste
        var datos_resumen_coste = datos.datos_resumen_coste;

        // Flags de existencia de datos
        var hay_datos_grafica_costes_mapa_calor_costes = datos_resumen_coste.hay_datos_grafica_costes_mapa_calor_costes;
        var hay_datos_costes_periodos = datos_resumen_coste.hay_datos_costes_periodos;
        var hay_datos_resumen_coste = (
            (hay_datos_grafica_costes_mapa_calor_costes == true) ||
            (hay_datos_costes_periodos == true));

        // Datos del apartado
        if (hay_datos_resumen_coste == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-resumen-coste-estudio-general");
                vacia_elementos([
                    "grafica-costes-resumen-coste-estudio-general",
                    "grafica-costes-periodos-resumen-coste-estudio-general",
                    "mapa-calor-semanal-costes-resumen-coste-estudio-general"]);
            }
            mostrar_resumen_coste = true;

            // Recuperación de datos del apartado
            if (hay_datos_grafica_costes_mapa_calor_costes == true) {
                var max_coste = datos_resumen_coste.max_coste;
                var etiquetas_grafica_costes = datos_resumen_coste.etiquetas_grafica_costes;
                var grafica_costes = datos_resumen_coste.grafica_costes;
                var colores_mapa_calor_costes = dame_escala_colores_mapa_calor(datos_resumen_coste.colores_mapa_calor_costes);
                var dias_mapa_calor_costes = datos_resumen_coste.dias_mapa_calor_costes;
                var datos_mapa_calor_costes = datos_resumen_coste.datos_mapa_calor_costes;
                var unidad_medida_coste = datos_resumen_coste.unidad_medida_coste;
            }
            if (hay_datos_costes_periodos == true) {
                var max_coste_periodos = datos_resumen_coste.max_coste_periodos;
                var etiquetas_costes_periodos = datos_resumen_coste.etiquetas_costes_periodos;
                var etiquetas_tooltips_costes_periodos = datos_resumen_coste.etiquetas_tooltips_costes_periodos;
                var min_fecha_coste_periodos = new $.jsDate(datos_resumen_coste.min_fecha_coste_periodos);
                var max_fecha_coste_periodos = new $.jsDate(datos_resumen_coste.max_fecha_coste_periodos);
                var grafica_costes_periodos = datos_resumen_coste.grafica_costes_periodos;
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

            // Datos de gráfica de costes y de mapa de calor de costes
            if (hay_datos_grafica_costes_mapa_calor_costes == true) {
                muestra_elementos([
                    "grafica-costes-resumen-coste-estudio-general",
                    "mapa-calor-semanal-costes-resumen-coste-estudio-general"]);

                var numero_valores_grafica_costes = dame_numero_maximo_valores_series_grafica(grafica_costes);
                var mostrar_indicadores_valores_costes = (numero_valores_grafica_costes <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de costes
                muestra_grafica_temporal_lineas_valores(
                    "grafica-costes-resumen-coste-estudio-general",
                    null,
                    TLNT.Idiomas._("Coste") + " (" + unidad_medida_coste + ")",
                    etiquetas_grafica_costes,
                    grafica_costes, null, INTERVALO_VALORES_HORA,
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

                // Mapa de calor semanal de costes
                var titulo_mapa_calor_semanal_costes = TLNT.Idiomas._("Mapa de calor semanal de") + " " + TLNT.Idiomas._("coste") + " (" + unidad_medida_coste + ")";
                muestra_grafico_mapa_calor(
                    "mapa-calor-semanal-costes-resumen-coste-estudio-general",
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
                oculta_elementos([
                    "grafica-costes-resumen-coste-estudio-general",
                    "mapa-calor-semanal-costes-resumen-coste-estudio-general"]);
            }

            // Datos de costes por periodos
            if (hay_datos_costes_periodos == true) {
                // Restauración de clase del elemento y mostrado de elementos
                switch (tipo_informe) {
                    case TIPO_INFORME_WEB_EMIOS: {
                        cambia_clase_elemento("grafica-costes-periodos-resumen-coste-estudio-general", "grafica100");
                        break;
                    }
                    case TIPO_INFORME_FICHERO: {
                        cambia_clase_elemento("grafica-costes-periodos-resumen-coste-estudio-general", "grafica100-informe-fichero");
                        break;
                    }
                }
                muestra_elemento("grafica-costes-periodos-resumen-coste-estudio-general");

                // Mostrar indicadores de valores
                var numero_valores_grafica_costes_periodos = dame_numero_maximo_valores_series_grafica(grafica_costes_periodos);
                var mostrar_indicadores_valores_costes_periodos = (numero_valores_grafica_costes_periodos <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de costes por periodos
                muestra_grafica_temporal_lineas_valores_fechas_diferentes(
                    "grafica-costes-periodos-resumen-coste-estudio-general",
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
            }
            else {
                // Se muestra un mensaje de aviso
                cambia_clase_elemento("grafica-costes-periodos-resumen-coste-estudio-general", "texto-elemento-no-mostrado-informe");
                $("#" + "grafica-costes-periodos-resumen-coste-estudio-general").html("<i class='icon-warning-sign color-rojo'></i> " +
                    TLNT.Idiomas._("No se muestra información de comparación de periodos de coste (no hay datos disponibles)"));
            }

            // Notas del apartado
            if (tipo_informe == TIPO_INFORME_FICHERO) {
                if (textos_informe.notas_resumen_coste != "") {
                    $('#notas-resumen-coste-estudio-general').html(textos_informe.notas_resumen_coste);
                }
                else {
                    oculta_elemento("notas-resumen-coste-estudio-general");
                }
            }
        }
        else {
            // Aviso
            textos_informe.texto_avisos += "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No hay datos de resumen de coste") + "</br>";
        }
    }
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (mostrar_resumen_coste == false)) {
        elimina_elemento("pagina-informe-fichero-estudio-general-resumen-coste");
    }
}


// Excesos de potencia
function dibuja_apartado_excesos_potencia_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_excesos_potencia = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA) > -1) {
        // Parámetros
        var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;

        // Datos de excesos de potencia
        var datos_excesos_potencia = datos.datos_excesos_potencia;

        // Flags de existencia de datos
        var hay_datos_excesos_potencia = datos_excesos_potencia.hay_datos_excesos_potencia;
        if (hay_datos_excesos_potencia == true) {
            var hay_datos_mapa_calor_sobrepotencia = datos_excesos_potencia.hay_datos_mapa_calor_sobrepotencia;
            var hay_datos_grafica_sobrepotencias_absolutas_tabla_sobrepotencias_tramos = datos_excesos_potencia.hay_datos_grafica_sobrepotencias_absolutas_tabla_sobrepotencias_tramos;
            hay_datos_excesos_potencia = (
                (hay_datos_mapa_calor_sobrepotencia == true) ||
                (hay_datos_grafica_sobrepotencias_absolutas_tabla_sobrepotencias_tramos == true));
        }

        // Datos del apartado
        if (hay_datos_excesos_potencia == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-excesos-potencia-estudio-general");
                vacia_elementos([
                    "grafica-sobrepotencias-absolutas-excesos-potencia-estudio-general",
                    "contenedor-tabla-sobrepotencias-tramos-excesos-potencia-estudio-general",
                    "mapa-calor-semanal-sobrepotencias-excesos-potencia-estudio-general"]);
            }
            mostrar_excesos_potencia = true;

            // Recuperación de datos del apartado
            if (hay_datos_mapa_calor_sobrepotencia == true) {
                var colores_mapa_calor_sobrepotencia = dame_escala_colores_mapa_calor(datos_excesos_potencia.colores_mapa_calor_sobrepotencia);
                var dias_mapa_calor_sobrepotencia = datos_excesos_potencia.dias_mapa_calor_sobrepotencia;
                var datos_mapa_calor_sobrepotencia = datos_excesos_potencia.datos_mapa_calor_sobrepotencia;
            }
            if (hay_datos_grafica_sobrepotencias_absolutas_tabla_sobrepotencias_tramos == true) {
                var min_sobrepotencia_absoluta = datos_excesos_potencia.min_sobrepotencia_absoluta;
                var max_sobrepotencia_absoluta = datos_excesos_potencia.max_sobrepotencia_absoluta;
                var grafica_sobrepotencias_absolutas = datos_excesos_potencia.grafica_sobrepotencias_absolutas;
                var tabla_sobrepotencias_tramos = datos_excesos_potencia.tabla_sobrepotencias_tramos;
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

            // Datos de mapa de calor de sobrepotencia
            if (hay_datos_mapa_calor_sobrepotencia == true) {
                muestra_elemento("mapa-calor-semanal-sobrepotencias-excesos-potencia-estudio-general");

                // Mapa de calor semanal de sobrepotencia
                var titulo_mapa_calor_semanal_sobrepotencia = TLNT.Idiomas._("Mapa de calor semanal de") + " " + TLNT.Idiomas._("sobrepotencia") + " (" + TLNT.Idiomas._("kW") + ")";
                muestra_grafico_mapa_calor(
                    "mapa-calor-semanal-sobrepotencias-excesos-potencia-estudio-general",
                    TIPO_MAPA_CALOR_SEMANAL,
                    titulo_mapa_calor_semanal_sobrepotencia,
                    dias_mapa_calor_sobrepotencia,
                    null,
                    datos_mapa_calor_sobrepotencia,
                    null,
                    null,
                    true,
                    colores_mapa_calor_sobrepotencia,
                    null,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);
            }
            else {
                oculta_elemento("mapa-calor-semanal-sobrepotencias-excesos-potencia-estudio-general");
            }

            // Datos de gráfica de sobrepotencias absolutas y tabla de sobrepotencias por tramo
            if (hay_datos_grafica_sobrepotencias_absolutas_tabla_sobrepotencias_tramos == true) {
                muestra_elementos([
                    "grafica-sobrepotencias-absolutas-excesos-potencia-estudio-general",
                    "contenedor-tabla-sobrepotencias-tramos-excesos-potencia-estudio-general"]);

                var numero_valores_grafica_sobrepotencias_absolutas = dame_numero_maximo_valores_series_grafica(grafica_sobrepotencias_absolutas);
                var mostrar_indicadores_valores_sobrepotencias_absolutas = (numero_valores_grafica_sobrepotencias_absolutas <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de sobrepotencias absolutas
                var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_SOBREPOTENCIA);
                muestra_grafica_temporal_lineas_valores(
                    "grafica-sobrepotencias-absolutas-excesos-potencia-estudio-general",
                    null,
                    TLNT.Idiomas._("Diferencias respecto a la potencia contratada") + " (" + TLNT.Idiomas._("kW") + ")",
                    null,
                    grafica_sobrepotencias_absolutas, null, INTERVALO_VALORES_CUARTOHORA,
                    null,
                    fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                    min_sobrepotencia_absoluta, true,
                    max_sobrepotencia_absoluta, true,
                    2, TLNT.Idiomas._("kW"),
                    lineas_referencia,
                    true,
                    TIPO_LINEAS_VALORES_ESTANDAR,
                    mostrar_indicadores_valores_sobrepotencias_absolutas,
                    false,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);

                // Tabla de sobrepotencias por tramo
                $('#contenedor-tabla-sobrepotencias-tramos-excesos-potencia-estudio-general').html(tabla_sobrepotencias_tramos);
                if (anyadir_menus_contextuales == true) {
                    var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                    anyade_menu_contextual('tabla-sobrepotencia-tramos-sensor-excesos-potencia', info_menu_contextual, TLNT.Idiomas._('Excesos de potencia por tramo'));
                }
            }
            else {
                oculta_elementos([
                    "grafica-sobrepotencias-absolutas-excesos-potencia-estudio-general",
                    "contenedor-tabla-sobrepotencias-tramos-excesos-potencia-estudio-general"]);
            }

            // Notas del apartado
            if (tipo_informe == TIPO_INFORME_FICHERO) {
                if (textos_informe.notas_excesos_potencia != "") {
                    $('#notas-excesos-potencia-estudio-general').html(textos_informe.notas_excesos_potencia);
                }
                else {
                    oculta_elemento("notas-excesos-potencia-estudio-general");
                }
            }
        }
        else {
            // Aviso
            textos_informe.texto_avisos += "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No hay datos de excesos de potencia") + "</br>";
        }
    }
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (mostrar_excesos_potencia == false)) {
        elimina_elemento("pagina-informe-fichero-estudio-general-excesos-potencia");
    }
}


// Excesos de energía reactiva
function dibuja_apartado_excesos_energia_reactiva_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_excesos_energia_reactiva = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA) > -1) {
        // Parámetros
        var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;

        // Datos de excesos de energía reactiva
        var datos_excesos_energia_reactiva = datos.datos_excesos_energia_reactiva;

        // Flags de existencia de datos
        var hay_datos_excesos_energia_reactiva = datos_excesos_energia_reactiva.hay_datos_excesos_energia_reactiva;
        if (hay_datos_excesos_energia_reactiva == true) {
            var hay_datos_graficas_consumos_energia_coseno_phi_tabla_energia_reactiva_tramos = datos_excesos_energia_reactiva.hay_datos_graficas_consumos_energia_coseno_phi_tabla_energia_reactiva_tramos;
            hay_datos_excesos_energia_reactiva = (
                (hay_datos_graficas_consumos_energia_coseno_phi_tabla_energia_reactiva_tramos == true));
        }

        // Datos del apartado
        if (hay_datos_excesos_energia_reactiva == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-excesos-energia-reactiva-estudio-general");
                vacia_elementos([
                    "grafica-consumos-energia-excesos-energia-reactiva-estudio-general",
                    "grafica-coseno-phi-excesos-energia-reactiva-estudio-general",
                    "contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva-estudio-general"]);
            }
            mostrar_excesos_energia_reactiva = true;

            // Recuperación de datos del apartado
            if (hay_datos_graficas_consumos_energia_coseno_phi_tabla_energia_reactiva_tramos == true) {
                var max_consumo = datos_excesos_energia_reactiva.max_consumo;
                var etiquetas_consumos_energia = datos_excesos_energia_reactiva.etiquetas_consumos_energia;
                var grafica_consumos_energia = datos_excesos_energia_reactiva.grafica_consumos_energia;
                var max_coseno_phi = datos_excesos_energia_reactiva.max_coseno_phi;
                var min_coseno_phi = datos_excesos_energia_reactiva.min_coseno_phi;
                var etiquetas_coseno_phi = datos_excesos_energia_reactiva.etiquetas_coseno_phi;
                var grafica_coseno_phi = datos_excesos_energia_reactiva.grafica_coseno_phi;
                var tabla_energia_reactiva_tramos = datos_excesos_energia_reactiva.tabla_energia_reactiva_tramos;
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

            // Datos de gráficas de consumos de energía y coseno de phi y tabla de costes de energía reactiva por tramo
            if (hay_datos_graficas_consumos_energia_coseno_phi_tabla_energia_reactiva_tramos == true) {
                muestra_elementos([
                    "grafica-consumos-energia-excesos-energia-reactiva-estudio-general",
                    "grafica-coseno-phi-excesos-energia-reactiva-estudio-general",
                    "contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva-estudio-general"]);

                var numero_valores_grafica_consumos_energia = dame_numero_maximo_valores_series_grafica(grafica_consumos_energia);
                var mostrar_indicadores_valores = (numero_valores_grafica_consumos_energia <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de consumos de energía activa y reactiva
                muestra_grafica_temporal_lineas_valores(
                    "grafica-consumos-energia-excesos-energia-reactiva-estudio-general",
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

                // Gráfica de coseno de phi
                var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_COSENO_PHI);
                muestra_grafica_temporal_lineas_valores(
                    "grafica-coseno-phi-excesos-energia-reactiva-estudio-general",
                    null,
                    TLNT.Idiomas._("Coseno de phi") ,
                    null,
                    grafica_coseno_phi, null, INTERVALO_VALORES_HORA,
                    null,
                    fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                    min_coseno_phi, true,
                    max_coseno_phi, true,
                    2, "",
                    lineas_referencia,
                    true,
                    TIPO_LINEAS_VALORES_ESTANDAR,
                    mostrar_indicadores_valores,
                    false,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);

                // Tabla de energía reactiva por tramo
                $('#contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva-estudio-general').html(tabla_energia_reactiva_tramos);
                if (anyadir_menus_contextuales == true) {
                    var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                    anyade_menu_contextual('tabla-costes-energia-reactiva-excesos-energia-reactiva', info_menu_contextual, TLNT.Idiomas._('Energía reactiva por tramo'));
                }
            }
            else {
                oculta_elementos([
                    "grafica-consumos-energia-excesos-energia-reactiva-estudio-general",
                    "grafica-coseno-phi-excesos-energia-reactiva-estudio-general",
                    "contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva-estudio-general"]);
            }

            // Notas del apartado
            if (tipo_informe == TIPO_INFORME_FICHERO) {
                if (textos_informe.notas_excesos_energia_reactiva != "") {
                    $('#notas-excesos-energia-reactiva-estudio-general').html(textos_informe.notas_excesos_energia_reactiva);
                }
                else {
                    oculta_elemento("notas-excesos-energia-reactiva-estudio-general");
                }
            }
        }
        else {
            textos_informe.texto_avisos += "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No hay datos de excesos de energía reactiva") + "</br>";
        }
    }
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (mostrar_excesos_energia_reactiva == false)) {
        elimina_elemento("pagina-informe-fichero-estudio-general-excesos-energia-reactiva");
    }
}


// Cortes de tensión
function dibuja_apartado_cortes_tension_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_cortes_tension = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_CORTES_TENSION_ELECTRICIDAD_ESPANYA) > -1) {
        // Parámetros
        var fecha_hora_inicio_consulta = parametros.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = parametros.fecha_hora_fin_consulta;

        // Datos de cortes de tensión
        var datos_cortes_tension = datos.datos_cortes_tension;

        // Flags de existencia de datos
        var hay_datos_cortes_tension = datos_cortes_tension.hay_datos_cortes_tension;
        if (hay_datos_cortes_tension == true) {
            var hay_datos_grafica_cortes_tension_consumos_tabla_cortes_tension = datos_cortes_tension.hay_datos_grafica_cortes_tension_consumos_tabla_cortes_tension;
            hay_datos_cortes_tension = (
                (hay_datos_grafica_cortes_tension_consumos_tabla_cortes_tension == true));
        }

        // Datos del apartado
        if (hay_datos_cortes_tension == true) {
            // Se muestra el apartado y se borran los datos anteriores
            if (tipo_informe == TIPO_INFORME_WEB_EMIOS) {
                muestra_elemento("apartado-cortes-tension-estudio-general");
                vacia_elementos([
                    "grafica-cortes-tension-consumos-cortes-tension-estudio-general",
                    "contenedor-tabla-cortes-tension-cortes-tension-estudio-general"]);
            }
            mostrar_cortes_tension = true;

            // Recuperación de datos del apartado
            if (hay_datos_grafica_cortes_tension_consumos_tabla_cortes_tension == true) {
                var grafica_consumos_cortes_tension = datos_cortes_tension.grafica_consumos_cortes_tension;
                var tabla_cortes_tension = datos_cortes_tension.tabla_cortes_tension;
                var etiquetas = datos_cortes_tension.etiquetas;
                var etiquetas_unidad = datos_cortes_tension.etiquetas_unidad;
                var max_consumo = datos_cortes_tension.max_consumo;
                var unidades_medida = datos_cortes_tension.unidades_medida;
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

            // Datos de gráfica de consumos y cortes de tensión y tabla de cortes de tensión
            if (hay_datos_grafica_cortes_tension_consumos_tabla_cortes_tension == true) {
                muestra_elementos([
                    "grafica-cortes-tension-consumos-cortes-tension-estudio-general",
                    "contenedor-tabla-cortes-tension-cortes-tension-estudio-general"]);

                var numero_valores_grafica_consumos_cortes_tension = dame_numero_maximo_valores_series_grafica(grafica_consumos_cortes_tension);
                var mostrar_indicadores_valores_cortes_tension_consumo = (numero_valores_grafica_consumos_cortes_tension <= NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE);

                // Gráfica de consumos y de cortes de tensión
                muestra_grafica_temporal_lineas_valores_ejes_diferentes(
                    "grafica-cortes-tension-consumos-cortes-tension-estudio-general",
                    TLNT.Idiomas._("Cortes de tensión y consumo"),
                    etiquetas_unidad,
                    etiquetas,
                    grafica_consumos_cortes_tension, INTERVALO_VALORES_TIEMPO_REAL,
                    false,
                    fecha_hora_inicio_consulta, fecha_hora_fin_consulta, false,
                    [0, 0], true,
                    [1, max_consumo], true,
                    2, unidades_medida,
                    true,
                    [TIPO_LINEAS_VALORES_CUADRADAS, TIPO_LINEAS_VALORES_ESTANDAR],
                    mostrar_indicadores_valores_cortes_tension_consumo,
                    mostrar_animaciones,
                    anyadir_menus_contextuales);

                // Tabla de cortes de tensión
                $('#contenedor-tabla-cortes-tension-cortes-tension-estudio-general').html(tabla_cortes_tension);
                if (anyadir_menus_contextuales == true) {
                    var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                    anyade_menu_contextual('tabla-cortes-tension-cortes-tension', info_menu_contextual, TLNT.Idiomas._('Cortes de tensión'));
                }
            }
            else {
                oculta_elementos([
                    "grafica-cortes-tension-consumos-cortes-tension-estudio-general",
                    "contenedor-tabla-cortes-tension-cortes-tension-estudio-general"]);
            }

            // Notas del apartado
            if (tipo_informe == TIPO_INFORME_FICHERO) {
                if (textos_informe.notas_cortes_tension != "") {
                    $('#notas-cortes-tension-estudio-general').html(textos_informe.notas_cortes_tension);
                }
                else {
                    oculta_elemento("notas-cortes-tension-estudio-general");
                }
            }
        }
        else {
            // Aviso
            textos_informe.texto_avisos += "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No hay datos de cortes de tensión") + "</br>";
        }
    }
    if ((tipo_informe == TIPO_INFORME_FICHERO) && (mostrar_cortes_tension == false)) {
        elimina_elemento("pagina-informe-fichero-estudio-general-cortes-tension");
    }
}


// Simulación de factura
function dibuja_apartado_simulacion_factura_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_simulacion_factura = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_ELECTRICIDAD_ESPANYA) > -1) {
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
                    "contenedor-tabla-energia-activa-simulacion-factura-estudio-general",
                    "contenedor-tabla-energia-activa-consumidor-directo-simulacion-factura-estudio-general",
                    "contenedor-tabla-energia-activa-tarifa-acceso-simulacion-factura-estudio-general",
                    "contenedor-tabla-potencia-simulacion-factura-estudio-general",
                    "contenedor-tabla-potencia-maxima-excesos-potencia-simulacion-factura-estudio-general",
                    "contenedor-tabla-energia-reactiva-simulacion-factura-estudio-general",
                    "contenedor-tabla-otros-conceptos-simulacion-factura-estudio-general"]);
            }
            mostrar_simulacion_factura = true;

            // Recuperación de datos del apartado
            if (hay_datos == true) {
                var tabla_coste_consumo = datos_simulacion_factura.tabla_coste_consumo;
                var tabla_energia_activa = datos_simulacion_factura.tabla_energia_activa;
                var tabla_energia_activa_consumidor_directo = datos_simulacion_factura.tabla_energia_activa_consumidor_directo;
                var tabla_energia_activa_tarifa_acceso = datos_simulacion_factura.tabla_energia_activa_tarifa_acceso;
                var tabla_potencia = datos_simulacion_factura.tabla_potencia;
                var hay_datos_potencia_maxima_excesos_potencia = datos_simulacion_factura.hay_datos_potencia_maxima_excesos_potencia;
                var tabla_potencia_maxima_excesos_potencia = datos_simulacion_factura.tabla_potencia_maxima_excesos_potencia;
                var hay_datos_energia_reactiva = datos_simulacion_factura.hay_datos_energia_reactiva;
                var tabla_energia_reactiva = datos_simulacion_factura.tabla_energia_reactiva;
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
                anyade_menu_contextual('tabla-coste-consumo-simulacion-factura-estudio-general', info_menu_contextual, TLNT.Idiomas._('Coste y consumo'));
            }

            // Se rellena la tabla de energía activa
            $("#contenedor-tabla-energia-activa-simulacion-factura-estudio-general").html(tabla_energia_activa);
            if (anyadir_menus_contextuales == true) {
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual('tabla-energia-activa-simulacion-factura-estudio-general', info_menu_contextual, TLNT.Idiomas._('Energía activa'));
            }
            
            // Se rellena la tabla de energía activa de consumidor directo
            $("#contenedor-tabla-energia-activa-consumidor-directo-simulacion-factura-estudio-general").html(tabla_energia_activa_consumidor_directo);
            if (anyadir_menus_contextuales == true) {
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual('tabla-energia-activa-consumidor-directo-simulacion-factura-estudio-general', info_menu_contextual, TLNT.Idiomas._('Energía activa (consumidor directo)'));
            }
            
            // Se rellena la tabla de energía activa de tarifa de acceso
            $("#contenedor-tabla-energia-activa-tarifa-acceso-simulacion-factura-estudio-general").html(tabla_energia_activa_tarifa_acceso);
            if (anyadir_menus_contextuales == true) {
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual('tabla-energia-activa-tarifa-acceso-simulacion-factura-estudio-general', info_menu_contextual, TLNT.Idiomas._('Energía activa (peaje de acceso)'));
            }

            // Se rellena la tabla de potencia
            $("#contenedor-tabla-potencia-simulacion-factura-estudio-general").html(tabla_potencia);
            if (anyadir_menus_contextuales == true) {
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual('tabla-potencia-simulacion-factura-estudio-general', info_menu_contextual, TLNT.Idiomas._('Potencia'));
            }

            // Tabla de potencia máxima y excesos de potencia
            if (hay_datos_potencia_maxima_excesos_potencia == true) {
                muestra_elemento("contenedor-tabla-potencia-maxima-excesos-potencia-simulacion-factura-estudio-general");

                $("#contenedor-tabla-potencia-maxima-excesos-potencia-simulacion-factura-estudio-general").html(tabla_potencia_maxima_excesos_potencia);
                if (anyadir_menus_contextuales == true) {
                    var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                    anyade_menu_contextual('tabla-potencia-maxima-excesos-potencia-simulacion-factura-estudio-general', info_menu_contextual, TLNT.Idiomas._('Potencia máxima y excesos de potencia'));
                }
            }
            else {
                oculta_elemento("contenedor-tabla-potencia-maxima-excesos-potencia-simulacion-factura-estudio-general");
            }

            // Tabla de energía reactiva
            if (hay_datos_energia_reactiva == true) {
                muestra_elemento("contenedor-tabla-energia-reactiva-simulacion-factura-estudio-general");

                $("#contenedor-tabla-energia-reactiva-simulacion-factura-estudio-general").html(tabla_energia_reactiva);
                if (anyadir_menus_contextuales == true) {
                    var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                    anyade_menu_contextual('tabla-energia-reactiva-simulacion-factura-estudio-general', info_menu_contextual, TLNT.Idiomas._('Energía reactiva'));
                }
            }
            else {
                oculta_elemento("contenedor-tabla-energia-reactiva-simulacion-factura-estudio-general");
            }

            // Tabla de otros conceptos
            $("#contenedor-tabla-otros-conceptos-simulacion-factura-estudio-general").html(tabla_otros_conceptos);
            if (anyadir_menus_contextuales == true) {
                var info_menu_contextual = dame_info_menu_contextual_tablas_datos();
                anyade_menu_contextual('tabla-otros-conceptos-simulacion-factura-estudio-general', info_menu_contextual, TLNT.Idiomas._('Otros conceptos'));
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
function dibuja_apartado_conclusiones_smartmeter_estudio_general_electricidad_Espanya(
    parametros,
    datos,
    tipo_informe,
    apartados,
    textos_informe) {
    var mostrar_conclusiones = false;
    if (apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_ELECTRICIDAD_ESPANYA) > -1) {
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
function dibuja_apartado_avisos_smartmeter_estudio_general_electricidad_Espanya(
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

