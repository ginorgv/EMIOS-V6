//
// Funciones de informes fichero de facturas (gas - España)
//


// Genera el informe fichero de simulación de factura
function smartmeter_simulador_factura_ver_informe_fichero_gas_Espanya(parametros_informe) {
    // Se recuperan los parámetros del informe
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-simulador-factura').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-simulador-factura");
        return;
    }

    // Parámetros del informe
    var medicion = parametros_informe["medicion"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var id_tarifa = parametros_informe["id_tarifa"];
    var ids_sensores_reparto_costes = parametros_informe["ids_sensores_reparto_costes"];
    var nombres_sensores_reparto_costes = parametros_informe["nombres_sensores_reparto_costes"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

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
        // Se comprueba si hay error en el resultado del informe
        var error_informe = false;
        var descripcion_error_informe = "";
        var resultado = dame_resultado_ejecucion_script_php_json_usuario_interno(data);
        if (resultado.res == "ERROR") {
            error_informe = true;
            descripcion_error_informe = resultado.msg;
        }

        // Comprobación de datos disponibles
        if (error_informe == false) {
            var hay_datos = resultado.hay_datos;
            if (hay_datos == false) {
                error_informe = true;
                descripcion_error_informe = TLNT.Idiomas._("No hay datos disponibles");
            }
        }

        // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
        if (error_informe == true) {
            $('#mensaje-aviso-informe-fichero-simulador-factura').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-simulador-factura-1");
            elimina_elemento("pagina-informe-fichero-simulador-factura-2");
            return;
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-simulador-factura-1').html(TLNT.Idiomas._("Simulación de factura"));
        $('#titulo-informe-fichero-simulador-factura-2').html(TLNT.Idiomas._("Reparto de costes"));

        // Reparto de costes
        if (resultado.hay_datos_reparto_costes == false) {
            elimina_elemento("pagina-informe-fichero-simulador-factura-2");
        }

        // Se dibuja el informe
        var parametros = {
            id_titulo_datos: "titulo-datos-simulador-factura",
            id_contenedor_tabla_datos: "contenedor-tabla-datos-simulador-factura",
            id_titulo_resumen: "titulo-resumen-simulador-factura",
            id_contenedor_tabla_coste_consumo: "contenedor-tabla-coste-consumo-simulador-factura",
            id_titulo_detalles: "titulo-detalles-simulador-factura",
            id_contenedor_tabla_consumo: "contenedor-tabla-consumo-simulador-factura",
            id_contenedor_tabla_termino_fijo: "contenedor-tabla-termino-fijo-simulador-factura",
            id_contenedor_tabla_otros_conceptos: "contenedor-tabla-otros-conceptos-simulador-factura",
            id_grafica_porcentajes_costes_conceptos: "grafica-porcentajes-costes-conceptos-simulador-factura",
            id_titulo_reparto_costes: "titulo-reparto-costes-simulador-factura",
            id_contenedor_tabla_reparto_costes: "contenedor-tabla-reparto-costes-simulador-factura",
            id_grafica_porcentajes_reparto_costes: "grafica-porcentajes-reparto-costes-simulador-factura"};
        dibuja_informe_smartmeter_simulador_factura_gas_Espanya(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}
