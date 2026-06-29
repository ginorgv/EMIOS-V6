//
// Funciones de facturas (electricidad - España)
//


// Simulación de factura
function boton_smartmeter_simulador_factura_ver_informe_electricidad_Espanya(parametros_informe) {
    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var id_tarifa = parametros_informe["id_tarifa"];
    var ids_sensores_reparto_costes = parametros_informe["ids_sensores_reparto_costes"];
    var nombres_sensores_reparto_costes = parametros_informe["nombres_sensores_reparto_costes"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/dame_simulacion_factura_sensor_tarifa.php", {
        medicion: medicion,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        id_tarifa: id_tarifa,
        ids_sensores_reparto_costes: ids_sensores_reparto_costes,
        nombres_sensores_reparto_costes: nombres_sensores_reparto_costes,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        exclusion_fechas: JSON.stringify(exclusion_fechas)
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Comprobación de datos disponibles
        if (resultado.hay_datos == false) {
            jAlert(TLNT.Idiomas._("No hay datos disponibles"));
            return;
        }

        // Se muestra el informe
        $("#informe-sin-datos-smartmeter-simulador-factura").hide();
        $("#informe-smartmeter-simulador-factura").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "titulo-datos-simulador-factura",
            "contenedor-tabla-datos-simulador-factura",
            "titulo-resumen-simulador-factura",
            "contenedor-tabla-coste-consumo-simulador-factura",
            "titulo-detalles-simulador-factura",
            "contenedor-tabla-energia-activa-simulador-factura",
            "contenedor-tabla-energia-activa-directo-simulador-factura",
            "contenedor-tabla-energia-activa-tarifa-acceso-simulador-factura",
            "contenedor-tabla-potencia-simulador-factura",
            "contenedor-tabla-potencia-maxima-excesos-potencia-simulador-factura",
            "contenedor-tabla-energia-reactiva-simulador-factura",
            "contenedor-tabla-otros-conceptos-simulador-factura",
            "grafica-porcentajes-costes-conceptos-simulador-factura",
            "titulo-reparto-costes-simulador-factura",
            "contenedor-tabla-reparto-costes-simulador-factura",
            "grafica-porcentajes-reparto-costes-simulador-factura"]);

        // Se dibuja el informe
        var parametros = {
            id_titulo_datos: "titulo-datos-simulador-factura",
            id_contenedor_tabla_datos: "contenedor-tabla-datos-simulador-factura",
            id_titulo_resumen: "titulo-resumen-simulador-factura",
            id_contenedor_tabla_coste_consumo: "contenedor-tabla-coste-consumo-simulador-factura",
            id_titulo_detalles: "titulo-detalles-simulador-factura",
            id_contenedor_tabla_energia_activa: "contenedor-tabla-energia-activa-simulador-factura",
            id_contenedor_tabla_energia_activa_directo: "contenedor-tabla-energia-activa-directo-simulador-factura",
            id_contenedor_tabla_energia_activa_tarifa_acceso: "contenedor-tabla-energia-activa-tarifa-acceso-simulador-factura",
            id_contenedor_tabla_potencia: "contenedor-tabla-potencia-simulador-factura",
            id_contenedor_tabla_potencia_maxima_excesos_potencia: "contenedor-tabla-potencia-maxima-excesos-potencia-simulador-factura",
            id_contenedor_tabla_energia_reactiva: "contenedor-tabla-energia-reactiva-simulador-factura",
            id_contenedor_tabla_otros_conceptos: "contenedor-tabla-otros-conceptos-simulador-factura",
            id_grafica_porcentajes_costes_conceptos: "grafica-porcentajes-costes-conceptos-simulador-factura",
            id_titulo_reparto_costes: "titulo-reparto-costes-simulador-factura",
            id_contenedor_tabla_reparto_costes: "contenedor-tabla-reparto-costes-simulador-factura",
            id_grafica_porcentajes_reparto_costes: "grafica-porcentajes-reparto-costes-simulador-factura"};
        dibuja_informe_smartmeter_simulador_factura_electricidad_Espanya(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}
