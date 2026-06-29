//
// Funciones de informes fichero de líneas base (de Proyectos)
//


// Genera el informe fichero de simulación de línea base
function proyectos_simulador_linea_base_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_proyectos_simulador_linea_base();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-simulador-linea-base').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-simulador-linea-base");
        return;
    }

    // Parámetros del informe
    var id_linea_base = parametros_informe["id_linea_base"];
    var comentarios = parametros_informe["comentarios"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
    $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_simulacion_linea_base.php", {
        id_linea_base: id_linea_base,
        comentarios: comentarios,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        tipo_informe: TIPO_INFORME_FICHERO
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
            $('#mensaje-aviso-informe-fichero-simulador-linea-base').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-simulador-linea-base");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-simulador-linea-base').html(TLNT.Idiomas._("Simulación de línea base"));

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_grafica_valores: "grafica-valores-simulador-linea-base",
            id_grafica_diferencias: "grafica-diferencias-simulador-linea-base",
            id_grafica_diferencias_acumuladas: "grafica-diferencias-acumuladas-simulador-linea-base",
            id_descripcion_sensor: "descripcion-sensor-simulador-linea-base",
            id_contenedor_tabla_error_coeficientes_linea_base: "contenedor-tabla-error-coeficientes-linea-base-simulador-linea-base",
            id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones: "contenedor-tabla-errores-coeficientes-lineas-base-excepciones-simulador-linea-base",
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-simulador-linea-base"};
        dibuja_informe_proyectos_simulador_linea_base(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero de simulación de línea base
function dame_parametros_informe_fichero_proyectos_simulador_linea_base() {
    // Identificador de línea base
    var id_linea_base = $("#id_linea_base_proyectos_informe_fichero_simulador_linea_base").text();

    // Comentarios
    var comentarios = $("#comentarios_proyectos_informe_fichero_simulador_linea_base").text();

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_linea_base"] = id_linea_base;
    parametros_informe["comentarios"] = comentarios;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_proyectos_informe_fichero_simulador_linea_base").text();
        var hora_inicio = $("#hora_inicio_proyectos_informe_fichero_simulador_linea_base").text();
        var fecha_fin = $("#fecha_fin_proyectos_informe_fichero_simulador_linea_base").text();
        var hora_fin = $("#hora_fin_proyectos_informe_fichero_simulador_linea_base").text();
        var resultado = comprueba_fechas_inicio_fin_correctas_usuario_interno(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
            hora_inicio += ":00";
            hora_fin += ":59";
            var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
            var fecha_hora_fin = fecha_fin + ", " + hora_fin;

            parametros_informe["fecha_inicio"] = fecha_inicio;
            parametros_informe["fecha_fin"] = fecha_fin;
            parametros_informe["hora_inicio"] = hora_inicio;
            parametros_informe["hora_fin"] = hora_fin;
            parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
            parametros_informe["fecha_hora_fin"] = fecha_hora_fin;
        }
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}



