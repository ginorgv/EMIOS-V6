//
// Funciones de informes fichero de información (de Proyectos)
//


// Genera el informe fichero de información de proyecto
function proyectos_informacion_proyecto_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_proyectos_informacion_proyecto();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-proyecto').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-informacion-proyecto");
        return;
    }

    // Parámetros del informe
    var id_proyecto = parametros_informe["id_proyecto"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
	$.post("./src/modulos/ModulosWeb/ModuloProyectos/Informacion/dame_informacion_proyecto.php", {
        id_proyecto: id_proyecto,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc
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
            $('#mensaje-aviso-informe-fichero-informacion-proyecto').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-informacion-proyecto");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-informacion-proyecto-1').html(TLNT.Idiomas._("Información de proyecto") + " (I)");
        $('#titulo-informe-fichero-informacion-proyecto-2').html(TLNT.Idiomas._("Información de proyecto") + " (II)");

        // Se dibuja el informe
        var parametros = {
            id_contenedor_tabla_parametros_proyecto: "contenedor-tabla-parametros-proyecto-informacion-proyecto",
            id_contenedor_tabla_informacion_proyecto: "contenedor-tabla-informacion-proyecto-informacion-proyecto",
            id_grafica_valores: "grafica-valores-informacion-proyecto",
            id_grafica_diferencias: "grafica-diferencias-informacion-proyecto",
            id_grafica_diferencias_acumuladas: "grafica-diferencias-acumuladas-informacion-proyecto",
            id_contenedor_tabla_valores_adicionales_proyecto: "contenedor-tabla-valores-adicionales-proyecto-informacion-proyecto",
            id_contenedor_tabla_error_coeficientes_linea_base: "contenedor-tabla-error-coeficientes-linea-base-informacion-proyecto",
            id_contenedor_tabla_errores_coeficientes_lineas_base_excepciones: "contenedor-tabla-errores-coeficientes-lineas-base-excepciones-informacion-proyecto"};
        dibuja_informe_proyectos_informacion_proyecto(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero de información de proyecto
function dame_parametros_informe_fichero_proyectos_informacion_proyecto() {
    // Identificador de proyecto
    var id_proyecto = $("#id_proyecto_proyectos_informe_fichero_informacion_proyecto").text();

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_proyecto"] = id_proyecto;

    // Se recuperan las fechas
    var fecha_inicio = $("#fecha_inicio_proyectos_informe_fichero_informacion_proyecto").text();
    var fecha_fin = $("#fecha_fin_proyectos_informe_fichero_informacion_proyecto").text();
    var hora_inicio = "00:00:00";
    var hora_fin = "23:59:59";
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
    var fecha_hora_fin = fecha_fin + ", " + hora_fin;

    parametros_informe["fecha_inicio"] = fecha_fin;
    parametros_informe["fecha_fin"] = fecha_fin;
    parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
    parametros_informe["fecha_hora_fin"] = fecha_hora_fin;

    // Información de error en los parámetros
    parametros_informe["error_parametros"] = false;
    parametros_informe["descripcion_error_parametros"] = "";

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}



