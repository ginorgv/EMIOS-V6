//
// Funciones de información (de proyectos)
//


// Muestra la información de un proyecto
function boton_proyectos_informacion_proyecto_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_proyectos_informacion_proyecto();
    if (parametros_informe == null) {
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
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Comprobación de datos disponibles
        var hay_datos = resultado.hay_datos;
        if (hay_datos == false) {
            jAlert(TLNT.Idiomas._("No hay datos disponibles"));
            return;
        }

        // Se muestra el informe
        $("#informe-sin-datos-proyectos-informacion-proyecto").hide();
        $("#informe-proyectos-informacion-proyecto").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "contenedor-tabla-parametros-proyecto-informacion-proyecto",
            "contenedor-tabla-informacion-proyecto-informacion-proyecto",
            "grafica-valores-informacion-proyecto",
            "grafica-diferencias-informacion-proyecto",
            "grafica-diferencias-acumuladas-informacion-proyecto",
            "contenedor-tabla-valores-adicionales-proyecto-informacion-proyecto",
            "contenedor-tabla-error-coeficientes-linea-base-informacion-proyecto",
            "contenedor-tabla-errores-coeficientes-lineas-base-excepciones-informacion-proyecto"]);

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
            TIPO_INFORME_WEB_EMIOS);
    });
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de información de proyecto
function dame_parametros_informe_proyectos_informacion_proyecto() {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Se comprueba si hay proyecto seleccionado
    var id_proyecto = $('#id_proyecto_proyectos_informacion_proyecto').val();
    if (id_proyecto == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay proyecto seleccionado"));
        return (null);
	}

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_proyecto"] = id_proyecto;

    // Se recuperan las fechas
    var fecha_inicio = $('#fecha_inicio_proyectos_informacion_proyecto').val();
    var fecha_fin = $('#fecha_fin_proyectos_informacion_proyecto').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
    if (fechas_correctas == false) {
        return (null);
    }
    var hora_inicio = "00:00:00";
    var hora_fin = "23:59:59";
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio;
    var fecha_hora_fin = fecha_fin + ", " + hora_fin;

    parametros_informe["fecha_inicio"] = fecha_inicio;
    parametros_informe["fecha_fin"] = fecha_fin;
    parametros_informe["hora_inicio"] = hora_inicio;
    parametros_informe["hora_fin"] = hora_fin;
    parametros_informe["fecha_hora_inicio"] = fecha_hora_inicio;
    parametros_informe["fecha_hora_fin"] = fecha_hora_fin;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}