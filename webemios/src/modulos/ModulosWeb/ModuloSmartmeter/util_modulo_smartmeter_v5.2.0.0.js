// Devuelve los identificadores y los nombres de las tablas de la sección correspondiente
function dame_ids_nombres_tablas_seccion_modulo_smartmeter(seccion) {
    var ids_nombres_tablas = [];
    switch (seccion) {
        case SECCION_SMARTMETER_TARIFAS: {
            ids_nombres_tablas.push(["tablaTarifasElectricas", TLNT.Idiomas._("Tarifas")]);
            ids_nombres_tablas.push(["tablaTarifasGas", TLNT.Idiomas._("Tarifas")]);
            ids_nombres_tablas.push(["tablaGruposTarifas", TLNT.Idiomas._("Grupos de tarifas")]);
            break;
        }
        case SECCION_SMARTMETER_FACTURAS: {
            ids_nombres_tablas.push(["tablaValidacionesFacturas", TLNT.Idiomas._("Validaciones de facturas y cierres")]);
            break;
        }
    }
    return (ids_nombres_tablas);
}


//
// Funciones de comprobación
//


// Comprueba que los valores de las potencias de los tramos son correctos según el tipo de la tarifa eléctrica especificada
function comprueba_potencias_tramos_tarifa_electrica_correctas(tipo_tarifa_electrica, potencias_tramos) {
    var tarifas_electricas_correctas = null;
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            tarifas_electricas_correctas = comprueba_potencias_tramos_tarifa_electrica_correctas_Espanya(
                tipo_tarifa_electrica,
                potencias_tramos);
            break;
        }
        default: {
            tarifas_electricas_correctas = false;
        }
    }
    return (tarifas_electricas_correctas);
}


// Comprueba que los valores de las potencias de los tramos son correctos según el tipo de la tarifa eléctrica especificada
function comprueba_potencias_tramos_tarifa_electrica_correctas_Espanya(tipo_tarifa_electrica, potencias_tramos) {
    // Características del tipo de tarifa eléctrica
    var caracteristicas_tipo_tarifa_electrica = dame_caracteristicas_tipo_tarifa_electrica_Espanya(tipo_tarifa_electrica);
    var numero_tramos = caracteristicas_tipo_tarifa_electrica["numero_tramos"];

    // Se comprueba que si hay alguna potencias rellena, estén todas rellenas (sólo para modificación de varias tarifas eléctricas)
    var numero_potencias_rellenas = 0;
    for (var i = 0; i < numero_tramos; i++) {
        if (potencias_tramos[i] != "") {
            // Los valores de las potencias deben ser mayor que 0
            if (potencias_tramos[i] <= 0) {
                jAlert(TLNT.Idiomas._("Las potencias contratadas deben ser mayores que 0"));
                return (false);
            }
            numero_potencias_rellenas += 1;
        }
    }
    if (numero_potencias_rellenas == 0) {
        return;
    }
    else {
        if (numero_potencias_rellenas < numero_tramos) {
            jAlert(TLNT.Idiomas._('Por favor, rellene las potencias contratadas en todos los tramos'));
            return (false);
        }
    }

    // Se comprueban las potencias mínimas y máximas permitidas para cada tipo de tarifa eléctrica
    var potencia_minima = POTENCIA_MINIMA_DEFECTO_TARIFAS_ELECTRICAS;
    var potencia_maxima = caracteristicas_tipo_tarifa_electrica["potencia_maxima_tramos"];
    for (var i = 0; i < numero_tramos; i++) {
        if (parseFloat(potencias_tramos[i]) < potencia_minima) {
            jAlert(TLNT.Idiomas._('Por favor, compruebe que las potencias contratadas en todos los tramos son iguales o superiores a') + " " + potencia_minima + " " + TLNT.Idiomas._("kWh"));
            return (false);
        }
        if (potencia_maxima != null) {
            if (parseFloat(potencias_tramos[i]) > potencia_maxima) {
                jAlert(TLNT.Idiomas._('Por favor, compruebe que las potencias contratadas en todos los tramos son iguales o inferiores a') + " " + potencia_maxima + " " + TLNT.Idiomas._("kWh"));
                return (false);
            }
        }
    }

    // Se comprueba que al menos un tramo tenga una potencia mínima
    var potencia_minima_algun_tramo = caracteristicas_tipo_tarifa_electrica["potencia_minima_algun_tramo"];
    if (potencia_minima_algun_tramo != null) {
        var potencia_minima_alcanzada = false;
        for (var i = 0; i < numero_tramos; i++) {
            if (potencias_tramos[i] >= potencia_minima_algun_tramo) {
                potencia_minima_alcanzada = true;
            }
        }
        if (potencia_minima_alcanzada == false) {
            jAlert(TLNT.Idiomas._('La potencia de algún tramo debe ser igual o mayor que') + " " + potencia_minima_algun_tramo + " " + TLNT.Idiomas._("kWh"));
            return (false);
        }
    }

    // Se comprueba que la potencia contratada para un tramo es igual o mayor que la potencia del anterior
    var potencias_tramos_crecientes = caracteristicas_tipo_tarifa_electrica["potencias_tramos_crecientes"];
    if (potencias_tramos_crecientes == true) {
        for (var i = 1; i < numero_tramos; i++) {
            if (parseFloat(potencias_tramos[i]) < parseFloat(potencias_tramos[i - 1])) {
                jAlert(TLNT.Idiomas._('Por favor, compruebe que la potencia en cada tramo sea igual o superior a la del tramo anterior'));
                return (false);
            }
        }
    }

    return (true);
}


//
// Funciones de características de tarifas
//


// Devuelve las características de las tarifas del país de la medición especificada
function dame_caracteristicas_tarifas_pais_medicion(medicion)
{
    switch (medicion) {
        case MEDICION_ELECTRICIDAD: {
            caracteristicas_tarifas = dame_caracteristicas_tarifas_electricas_pais();
            break;
        }
        case MEDICION_GAS: {
            caracteristicas_tarifas = dame_caracteristicas_tarifas_gas_pais();
            break;
        }
        case MEDICION_AGUA: {
            caracteristicas_tarifas = dame_caracteristicas_tarifas_agua_pais();
            break;
        }
    }
    return (caracteristicas_tarifas);
}


// Devuelve las características de las tarifas eléctricas según el país
function dame_caracteristicas_tarifas_electricas_pais() {
    var tarifas = false;
    var tramos = false;
    var autoconsumo = false;
    var potencias = false;
    var energia_reactiva = false;
    var cortes_tension = false;
    var facturas = false;
    var validacion_facturas = false;
    var informe_estudio_general = false;
    var curva_coste = false;
    var conceptos_adicionales_factura_consumo = false;
    var impuesto_conceptos_adicionales_factura = false;

    // Selección de país
    switch (pais_tarifas_electricas) {
        case PAIS_ESPANYA: {
            tarifas = true;
            tramos = true;
            autoconsumo = true;
            potencias = true;
            energia_reactiva = true;
            cortes_tension = true;
            facturas = true;
            validacion_facturas = true;
            informe_estudio_general = true;
            curva_coste = true;
            break;
        }
        case PAIS_NINGUNO: {
            break;
        }
    }

    var caracteristicas_tarifas_electricas = {
        "tarifas": tarifas,
        "tramos": tramos,
        "autoconsumo": autoconsumo,
        "potencias": potencias,
        "energia_reactiva": energia_reactiva,
        "cortes_tension": cortes_tension,
        "facturas": facturas,
        "validacion_facturas": validacion_facturas,
        "informe_estudio_general": informe_estudio_general,
        "curva_coste": curva_coste,
        "conceptos_adicionales_factura_consumo": conceptos_adicionales_factura_consumo,
        "impuesto_conceptos_adicionales_factura": impuesto_conceptos_adicionales_factura
    };
    return (caracteristicas_tarifas_electricas);
}


// Devuelve las características de las tarifas de gas según el país
function dame_caracteristicas_tarifas_gas_pais() {
    var tarifas = false;
    var tramos = false;
    var autoconsumo = false;
    var caudales = false;
    var facturas = false;
    var validacion_facturas = false;
    var informe_estudio_general = false;
    var curva_coste = false;
    var conceptos_adicionales_factura_consumo = false;
    var impuesto_conceptos_adicionales_factura = false;

    // Selección de país
    switch (pais_tarifas_gas) {
        case PAIS_ESPANYA: {
            tarifas = true;
            autoconsumo = true;
            caudales = true;
            facturas = true;
            informe_estudio_general = true;
            curva_coste = true;
            break;
        }
        case PAIS_NINGUNO:{
            break;
        }
    }

    var caracteristicas_tarifas_gas = {
        "tarifas": tarifas,
        "tramos": tramos,
        "autoconsumo": autoconsumo,
        "caudales": caudales,
        "facturas": facturas,
        "validacion_facturas": validacion_facturas,
        "informe_estudio_general": informe_estudio_general,
        "curva_coste": curva_coste,
        "conceptos_adicionales_factura_consumo": conceptos_adicionales_factura_consumo,
        "impuesto_conceptos_adicionales_factura": impuesto_conceptos_adicionales_factura
    };
    return (caracteristicas_tarifas_gas);
}


// Devuelve las características de las tarifas de agua según el país
function dame_caracteristicas_tarifas_agua_pais() {
    var tarifas = false;
    var autoconsumo = false;
    var facturas = false;
    var validacion_facturas = false;
    var informe_estudio_general = false;
    var curva_coste = false;
    var conceptos_adicionales_factura_consumo = false;
    var impuesto_conceptos_adicionales_factura = false;

    // Selección de país
    switch (pais_tarifas_agua) {
        case PAIS_ESPANYA: {
            tarifas = true;
            autoconsumo = true;
            facturas = true;
            informe_estudio_general = true;
            conceptos_adicionales_factura_consumo = true;
            impuesto_conceptos_adicionales_factura = true;
            break;
        }
        case PAIS_NINGUNO: {
            break;
        }
    }

    var caracteristicas_tarifas_agua = {
        "tarifas": tarifas,
        "autoconsumo": autoconsumo,
        "facturas": facturas,
        "validacion_facturas": validacion_facturas,
        "informe_estudio_general": informe_estudio_general,
        "curva_coste": curva_coste,
        "conceptos_adicionales_factura_consumo": conceptos_adicionales_factura_consumo,
        "impuesto_conceptos_adicionales_factura": impuesto_conceptos_adicionales_factura
    };
    return (caracteristicas_tarifas_agua);
}
// Se hace lo mismo que con el boton de importar valores.
// Se oculta el input type file, se le asocia un botón para que
// al hacer click sobre él se llame al input
function asocia_boton_html_input_file(){
    // Ventana de importación de valores (selección de fichero de valores)
    $("#fichero_importacion_valores_sensor_text").show(function() {
        $('#fichero_importacion_valores_sensor_file').hide();
    });
    $('#fichero_importacion_valores_sensor_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_importacion_valores_sensor_text').val(fichero);
    });
    $('#boton_importacion_valores_sensor_seleccionar_fichero').click(function() {
        $('#fichero_importacion_valores_sensor_file').click();
    });
}