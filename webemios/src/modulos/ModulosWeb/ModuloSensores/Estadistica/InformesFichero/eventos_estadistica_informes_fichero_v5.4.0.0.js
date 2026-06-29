//
// Funciones de informes fichero de estadística (de Sensores)
//


// Genera el informe fichero de histograma
function sensores_histograma_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_histograma();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-histograma').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-estadistica-informe-fichero-histograma");
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var detalle = parametros_informe["detalle"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Se recupera la información del histograma
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Estadistica/dame_histograma_valores_sensor.php", {
        id_ratio: id_ratio,
        clase_sensor: clase_sensor,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        parametros_extra_campo: parametros_extra_campo,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        intervalo_valores: intervalo_valores,
        detalle: detalle,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas)
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
            $('#mensaje-aviso-informe-fichero-histograma').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-estadistica-informe-fichero-histograma");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-histograma').html(TLNT.Idiomas._("Histograma de valores"));

        // Se dibuja el informe
        var parametros = {
            id_grafica_histograma: "grafica-histograma",
            id_contenedor_tabla_medidas_estadisticas: "contenedor-tabla-medidas-estadisticas-histograma",
            id_contenedor_tabla_percentiles: "contenedor-tabla-percentiles-histograma"};
        dibuja_informe_sensores_histograma(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de correlación
function sensores_correlacion_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_correlacion();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-correlacion').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-estadistica-informe-fichero-correlacion");
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clases_sensores_independientes = parametros_informe["clases_sensores_independientes"];
    var ids_sensores_independientes = parametros_informe["ids_sensores_independientes"];
    var nombres_sensores_independientes = parametros_informe["nombres_sensores_independientes"];
    var campos_independientes = parametros_informe["campos_independientes"];
    var parametros_extra_campos_independientes = parametros_informe["parametros_extra_campos_independientes"];
    var clase_sensor_dependiente = parametros_informe["clase_sensor_dependiente"];
    var id_sensor_dependiente = parametros_informe["id_sensor_dependiente"];
    var nombre_sensor_dependiente = parametros_informe["nombre_sensor_dependiente"];
    var campo_dependiente = parametros_informe["campo_dependiente"];
    var parametros_extra_campo_dependiente = parametros_informe["parametros_extra_campo_dependiente"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var funcion_correlacion = parametros_informe["funcion_correlacion"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Se recupera la información de la correlación
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Estadistica/dame_correlacion_valores_sensores.php", {
        id_ratio: id_ratio,
        clases_sensores_independientes: clases_sensores_independientes,
        ids_sensores_independientes: ids_sensores_independientes,
        nombres_sensores_independientes: nombres_sensores_independientes,
        campos_independientes: campos_independientes,
        parametros_extra_campos_independientes: parametros_extra_campos_independientes,
        clase_sensor_dependiente: clase_sensor_dependiente,
        id_sensor_dependiente: id_sensor_dependiente,
        nombre_sensor_dependiente: nombre_sensor_dependiente,
        campo_dependiente: campo_dependiente,
        parametros_extra_campo_dependiente: parametros_extra_campo_dependiente,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        intervalo_valores: intervalo_valores,
        funcion_correlacion: funcion_correlacion,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas)
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
            $('#mensaje-aviso-informe-fichero-correlacion').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-estadistica-informe-fichero-correlacion");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-correlacion').html(TLNT.Idiomas._("Correlación de valores"));

        // Se dibuja el informe
        var parametros = {
            id_grafica_correlacion: "grafica-correlacion",
            id_contenedor_tabla_funcion_correlacion: "contenedor-tabla-funcion-correlacion"};
        dibuja_informe_sensores_correlacion(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero de histograma
function dame_parametros_informe_fichero_sensores_histograma() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_histograma").text();

    // Se recuperan la clase de sensor, campo y parámetros extra
    var clase_sensor = $("#clase_sensor_sensores_informe_fichero_histograma").text();
    var campo = $("#campo_sensores_informe_fichero_histograma").text();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_informe_fichero_histograma').text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_histograma").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_histograma").text();

    // Intervalo de valores y detalle del histograma
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_histograma").text();
    var detalle = $("#detalle_sensores_informe_fichero_histograma").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_histograma").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_histograma").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_histograma").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["detalle"] = detalle;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_histograma").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_histograma").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_histograma").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_histograma").text();
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


// Devuelve los parámetros del informe fichero de correlacion
function dame_parametros_informe_fichero_sensores_correlacion() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_correlacion").text();

    // Se recuperan los identificadores y los nombres de los sensores independientes
    var ids_sensores_independientes = [];
    $("#ids_sensores_independientes_sensores_informe_fichero_correlacion li").each(function() {
        ids_sensores_independientes.push($(this).text());
    });
    var nombres_sensores_independientes = [];
    $("#nombres_sensores_independientes_sensores_informe_fichero_correlacion li").each(function() {
        nombres_sensores_independientes.push($(this).text());
    });

    // Se recuperan las clases de los sensores independientes
    var clases_sensores_independientes = [];
    $("#clases_sensores_independientes_sensores_informe_fichero_correlacion li").each(function() {
        clases_sensores_independientes.push($(this).text());
    });

    // Se recuperan los campos de los sensores independientes
    var campos_independientes = [];
    $("#campos_independientes_sensores_informe_fichero_correlacion li").each(function() {
        campos_independientes.push($(this).text());
    });

    // Se recuperan los parámetros extra de los campos de los sensores independientes
    var parametros_extra_campos_independientes = [];
    $("#parametros_extra_campos_independientes_sensores_informe_fichero_correlacion li").each(function() {
        parametros_extra_campos_independientes.push($(this).text());
    });

    // Se recupera la información del sensor dependiente
    var id_sensor_dependiente = $("#id_sensor_dependiente_sensores_informe_fichero_correlacion").text();
    var nombre_sensor_dependiente = $("#nombre_sensor_dependiente_sensores_informe_fichero_correlacion").text();
    var clase_sensor_dependiente = $("#clase_sensor_dependiente_sensores_informe_fichero_correlacion").text();
    var campo_dependiente = $("#campo_dependiente_sensores_informe_fichero_correlacion").text();
    var parametros_extra_campo_dependiente = $("#parametros_extra_campo_dependiente_sensores_informe_fichero_correlacion").text();

    // Intervalo de valores y función de correlación
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_correlacion").text();
    var funcion_correlacion = $("#funcion_correlacion_sensores_informe_fichero_correlacion").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_correlacion").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_correlacion").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_correlacion").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clases_sensores_independientes"] = clases_sensores_independientes;
    parametros_informe["ids_sensores_independientes"] = ids_sensores_independientes;
    parametros_informe["nombres_sensores_independientes"] = nombres_sensores_independientes;
    parametros_informe["campos_independientes"] = campos_independientes;
    parametros_informe["parametros_extra_campos_independientes"] = parametros_extra_campos_independientes;
    parametros_informe["clase_sensor_dependiente"] = clase_sensor_dependiente;
    parametros_informe["id_sensor_dependiente"] = id_sensor_dependiente;
    parametros_informe["nombre_sensor_dependiente"] = nombre_sensor_dependiente;
    parametros_informe["campo_dependiente"] = campo_dependiente;
    parametros_informe["parametros_extra_campo_dependiente"] = parametros_extra_campo_dependiente;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["funcion_correlacion"] = funcion_correlacion;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_correlacion").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_correlacion").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_correlacion").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_correlacion").text();
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
