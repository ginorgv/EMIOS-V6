//
// Funciones de informes fichero de comparación (de Sensores)
//


// Genera el informe fichero de comparación de periodos
function sensores_comparacion_periodos_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_comparacion_periodos();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-comparacion-periodos').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-comparacion-periodos-1",
            "pagina-informe-fichero-comparacion-periodos-2"]);
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
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var fecha_inicio_anterior = parametros_informe["fecha_inicio_anterior"];
    var fecha_inicio_posterior = parametros_informe["fecha_inicio_posterior"];
    var numero_dias_periodo = parametros_informe["numero_dias_periodo"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recupera la información de comparación de periodos
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_comparacion_valores_sensor_periodos.php", {
        id_ratio: id_ratio,
        clase_sensor: clase_sensor,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        parametros_extra_campo: parametros_extra_campo,
        fecha_inicio_anterior: fecha_inicio_anterior,
		fecha_inicio_posterior: fecha_inicio_posterior,
		numero_dias_periodo: numero_dias_periodo,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        horario_semanal: JSON.stringify(horario_semanal),
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
            $('#mensaje-aviso-informe-fichero-comparacion-periodos').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-comparacion-periodos-1",
                "pagina-informe-fichero-comparacion-periodos-2"]);
            return;
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-comparacion-periodos-1').html(TLNT.Idiomas._("Comparación de valores de periodos"));
        $('#titulo-informe-fichero-comparacion-periodos-2').html(TLNT.Idiomas._("Mapa de calor de diferencias"));

        // Ocultación del mapa de calor de diferencias
        var datos_mapa_calor_diferencias = resultado.datos_mapa_calor_diferencias;
        if (datos_mapa_calor_diferencias.length == 0) {
            elimina_elemento("pagina-informe-fichero-comparacion-periodos-2");
        }

        // Se dibuja el informe
        var parametros = {
            clase_sensor: clase_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-valores-comparacion-periodos",
            id_contenedor_tabla_evolucion_valores: "contenedor-tabla-evolucion-valores-comparacion-periodos",
            id_grafica_diferencias: "grafica-diferencias-comparacion-periodos",
            id_grafica_diferencias_acumuladas: "grafica-diferencias-acumuladas-comparacion-periodos",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_diferencias: "mapa-calor-diferencias-comparacion-periodos",
            altura_maxima_mapa_calor_diferencias: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO};
        dibuja_informe_sensores_comparacion_periodos(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de comparación con perfil horario
function sensores_comparacion_perfil_horario_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_comparacion_perfil_horario();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-comparacion-perfil-horario').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-comparacion-perfil-horario-1",
            "pagina-informe-fichero-comparacion-perfil-horario-2"]);
        return;
    }

    // Parámetros del informe
    var clase_sensor = parametros_informe["clase_sensor"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var fecha_inicio_perfil_horario = parametros_informe["fecha_inicio_perfil_horario"];
    var fecha_fin_perfil_horario = parametros_informe["fecha_fin_perfil_horario"];
    var tipo_perfil_horario = parametros_informe["tipo_perfil_horario"];
    var agrupaciones_dias_semana = parametros_informe["agrupaciones_dias_semana"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recupera la información de comparación de periodos
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_comparacion_valores_sensor_perfil_horario.php", {
        clase_sensor: clase_sensor,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        parametros_extra_campo: parametros_extra_campo,
        fecha_hora_inicio: fecha_hora_inicio,
		fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        fecha_inicio_perfil_horario: fecha_inicio_perfil_horario,
		fecha_fin_perfil_horario: fecha_fin_perfil_horario,
        tipo_perfil_horario: tipo_perfil_horario,
        agrupaciones_dias_semana: JSON.stringify(agrupaciones_dias_semana),
        horario_semanal: JSON.stringify(horario_semanal),
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
            $('#mensaje-aviso-informe-fichero-comparacion-perfil-horario').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-comparacion-perfil-horario-1",
                "pagina-informe-fichero-comparacion-perfil-horario-2"]);
            return;
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-comparacion-perfil-horario-1').html(TLNT.Idiomas._("Comparación de valores con perfil horario"));
        $('#titulo-informe-fichero-comparacion-perfil-horario-2').html(TLNT.Idiomas._("Mapa de calor de diferencias"));

        // Ocultación del mapa de calor de diferencias
        var datos_mapa_calor_diferencias = resultado.datos_mapa_calor_diferencias;
        if (datos_mapa_calor_diferencias.length == 0) {
            elimina_elemento("pagina-informe-fichero-comparacion-perfil-horario-2");
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            clase_sensor: clase_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-valores-comparacion-perfil-horario",
            id_grafica_diferencias: "grafica-diferencias-comparacion-perfil-horario",
            id_grafica_diferencias_acumuladas: "grafica-diferencias-acumuladas-comparacion-perfil-horario",
            id_grafica_valores_perfil_horario: "grafica-valores-perfil-horario-comparacion-perfil-horario",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_diferencias: "mapa-calor-diferencias-comparacion-perfil-horario"};
        dibuja_informe_sensores_comparacion_perfil_horario(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de comparación de campos iguales
function sensores_comparacion_campos_iguales_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_comparacion_campos_iguales();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-comparacion-campos-iguales').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-comparacion-campos-iguales-1");
        for (var i = 0; i < NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; i++) {
            var numero_pagina_mapa_calor = i + 2;
            elimina_elemento("pagina-informe-fichero-comparacion-campos-iguales-" + numero_pagina_mapa_calor);
        }
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recupera la información de comparación de valores de campos iguales
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_comparacion_valores_campos_iguales_sensores.php", {
        id_ratio: id_ratio,
        clase_sensor: clase_sensor,
        ids_sensores: ids_sensores,
        nombres_sensores: nombres_sensores,
        campo: campo,
        parametros_extra_campo: parametros_extra_campo,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
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
            $('#mensaje-aviso-informe-fichero-comparacion-campos-iguales').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-comparacion-campos-iguales-1");
            for (var i = 0; i < NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; i++) {
                var numero_pagina_mapa_calor = i + 2;
                elimina_elemento("pagina-informe-fichero-comparacion-campos-iguales-" + numero_pagina_mapa_calor);
            }
            return;
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-comparacion-campos-iguales-1').html(TLNT.Idiomas._("Comparación de valores de campos iguales"));
        for (var i = 0; i < NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; i++) {
            var numero_pagina_mapa_calor = i + 2;
            var numero_mapa_calor = i + 1;
            $('#titulo-informe-fichero-comparacion-campos-iguales-' + numero_pagina_mapa_calor).html(TLNT.Idiomas._("Mapa de calor de diferencias") + " (" + numero_mapa_calor + ")");
        }

        // Localización de los mapas de calor de diferencias
        var datos_mapas_calor_diferencias = resultado.datos_mapas_calor_diferencias;
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var numero_mapas_calor = datos_mapas_calor_diferencias.length;
            for (var i = numero_mapas_calor; i < NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; i++) {
                var numero_pagina_mapa_calor = i + 2;
                elimina_elemento("pagina-informe-fichero-comparacion-campos-iguales-" + numero_pagina_mapa_calor);
            }
        }
        else {
            for (var i = 0; i < NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; i++) {
                var numero_pagina_mapa_calor = i + 2;
                elimina_elemento("pagina-informe-fichero-comparacion-campos-iguales-" + numero_pagina_mapa_calor);
            }
        }

        // Se dibuja el informe
        var parametros = {
            clase_sensor: clase_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-valores-comparacion-campos-iguales",
            id_contenedor_tabla_diferencias_valores: "contenedor-tabla-diferencias-valores-comparacion-campos-iguales",
            id_grafica_diferencias: "grafica-diferencias-comparacion-campos-iguales",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapas_calor_diferencias: "mapa-calor-diferencias-comparacion-campos-iguales",
            altura_maxima_mapas_calor_diferencias: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO};
        dibuja_informe_sensores_comparacion_campos_iguales(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de cmoparación de campos diferentes
function sensores_comparacion_campos_diferentes_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_comparacion_campos_diferentes();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-comparacion-campos-diferentes').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-comparacion-campos-diferentes");
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var clases_sensores = parametros_informe["clases_sensores"];
    var campos = parametros_informe["campos"];
    var parametros_extra_campos = parametros_informe["parametros_extra_campos"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var unificar_escalas = parametros_informe["unificar_escalas"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recupera la información de comparación de valores de campos diferentes
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_comparacion_valores_campos_diferentes_sensores.php", {
        id_ratio: id_ratio,
        clases_sensores: clases_sensores,
        ids_sensores: ids_sensores,
        nombres_sensores: nombres_sensores,
        campos: campos,
        parametros_extra_campos: parametros_extra_campos,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        unificar_escalas: unificar_escalas,
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
            $('#mensaje-aviso-informe-fichero-comparacion-campos-diferentes').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-comparacion-campos-diferentes");
            return;
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-comparacion-campos-diferentes').html(TLNT.Idiomas._("Comparación de valores de campos diferentes"));

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-valores-comparacion-campos-diferentes"};
        dibuja_informe_sensores_comparacion_campos_diferentes(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de análisis comparativo
function sensores_analisis_comparativo_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_analisis_comparativo();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-analisis-comparativo').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-analisis-comparativo-1");
        elimina_elemento("pagina-informe-fichero-analisis-comparativo-2");
        elimina_elemento("pagina-informe-fichero-analisis-comparativo-3");
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var ids_sensores_agregados = parametros_informe["ids_sensores_agregados"];
    var nombres_sensores_agregados = parametros_informe["nombres_sensores_agregados"];
    var id_sensor_destacado = parametros_informe["id_sensor_destacado"];
    var nombre_sensor_destacado = parametros_informe["nombre_sensor_destacado"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recupera la información de análisis comparativo
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_analisis_comparativo_sensores.php", {
        id_ratio: id_ratio,
        clase_sensor: clase_sensor,
        campo: campo,
        parametros_extra_campo: parametros_extra_campo,
        ids_sensores_agregados: ids_sensores_agregados,
        nombres_sensores_agregados: nombres_sensores_agregados,
        id_sensor_destacado: id_sensor_destacado,
        nombre_sensor_destacado: nombre_sensor_destacado,
		fecha_hora_inicio: fecha_hora_inicio,
		fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
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
            $('#mensaje-aviso-informe-fichero-analisis-comparativo').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-analisis-comparativo-1");
            elimina_elemento("pagina-informe-fichero-analisis-comparativo-2");
            elimina_elemento("pagina-informe-fichero-analisis-comparativo-3");
            return;
        }

        // Títulos de las páginas
        $('#titulo-informe-fichero-analisis-comparativo-1').html(TLNT.Idiomas._("Análisis comparativo"));
        $('#titulo-informe-fichero-analisis-comparativo-2').html(TLNT.Idiomas._("Pareto"));
        $('#titulo-informe-fichero-analisis-comparativo-3').html(TLNT.Idiomas._("Mapa de calor"));

        // Localización del mapas de calor de diferencias
        var datos_mapa_calor_diferencias = resultado.datos_mapa_calor_diferencias;
        if ((tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO) || (datos_mapa_calor_diferencias.length == 0)) {
            elimina_elemento("pagina-informe-fichero-analisis-comparativo-3");
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            clase_sensor: clase_sensor,
            campo: campo,
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-valores-analisis-comparativo",
            id_grafica_valores_acumulados: "grafica-valores-acumulados-analisis-comparativo",
            id_contenedor_tabla_valores_maximos_minimos: "contenedor-tabla-valores-maximos-minimos-analisis-comparativo",
            id_grafica_pareto: "grafica-pareto-analisis-comparativo",
            id_contenedor_tabla_valores_pareto: "contenedor-tabla-valores-pareto-analisis-comparativo",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_diferencias: "mapa-calor-diferencias-media-analisis-comparativo",
            altura_maxima_mapas_calor_diferencias: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO
        };
        dibuja_informe_sensores_analisis_comparativo(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de valores generales
function sensores_valores_generales_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_valores_generales();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-valores-generales').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-valores-generales");
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clases_sensor = parametros_informe["clases_sensor"];
    var campos = parametros_informe["campos"];
    var parametros_extra_campos = parametros_informe["parametros_extra_campos"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var agregacion = parametros_informe["agregacion"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
	$.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_valores_generales_sensores.php", {
        id_ratio: id_ratio,
        clases_sensor: clases_sensor,
        campos: campos,
        parametros_extra_campos: parametros_extra_campos,
        ids_sensores: ids_sensores,
        nombres_sensores: nombres_sensores,
		fecha_hora_inicio: fecha_hora_inicio,
		fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        agregacion: agregacion,
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
            $('#mensaje-aviso-informe-fichero-valores-generales').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-valores-generales");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-valores-generales').html(TLNT.Idiomas._("Incrementos"));

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Primera clase de sensor y campo
        var primera_clase_sensor = clases_sensor[0];
        var primer_campo = campos[0];

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            primera_clase_sensor: primera_clase_sensor,
            primer_campo: primer_campo,
            intervalo_valores: intervalo_valores,
            agregacion: agregacion,
            id_grafica_valores: "grafica-valores-valores-generales",
            id_grafica_valores_acumulados: "grafica-valores-acumulados-valores-generales",
            id_contenedor_tabla_valores_maximos_minimos: "contenedor-tabla-valores-maximos-minimos-valores-generales"};
        dibuja_informe_sensores_valores_generales(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de incrementos totales
function sensores_incrementos_totales_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_incrementos_totales();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-incrementos-totales').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elemento("pagina-informe-fichero-incrementos-totales");
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clases_sensor = parametros_informe["clases_sensor"];
    var campos = parametros_informe["campos"];
    var parametros_extra_campos = parametros_informe["parametros_extra_campos"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var agregacion = parametros_informe["agregacion"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];

    // Se recuperan los datos para el informe
	$.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_incrementos_totales_sensores.php", {
        id_ratio: id_ratio,
        clases_sensor: clases_sensor,
        campos: campos,
        parametros_extra_campos: parametros_extra_campos,
        ids_sensores: ids_sensores,
        nombres_sensores: nombres_sensores,
		fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        intervalo_valores: intervalo_valores,
        agregacion: agregacion,
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
            $('#mensaje-aviso-informe-fichero-incrementos-totales').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elemento("pagina-informe-fichero-incrementos-totales");
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-incrementos-totales').html(TLNT.Idiomas._("Incrementos"));

        // Se dibuja el informe
        var parametros = {
            intervalo_valores: intervalo_valores,
            id_grafica_incrementos_totales: "grafica-incrementos-totales-incrementos-totales",
            id_grafica_porcentajes_incrementos: "grafica-porcentajes-incrementos-incrementos-totales",
            id_grafica_incrementos: "grafica-incrementos-incrementos-totales",
            id_grafica_incrementos_acumulados: "grafica-incrementos-acumulados-incrementos-totales",
            id_contenedor_tabla_incrementos: "contenedor-tabla-incrementos-incrementos-totales"};
        dibuja_informe_sensores_incrementos_totales(
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero de comparación de periodos
function dame_parametros_informe_fichero_sensores_comparacion_periodos() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_comparacion_periodos").text();

    // Se recuperan la clase de sensor, campo y parámetros extra
    var clase_sensor = $("#clase_sensor_sensores_informe_fichero_comparacion_periodos").text();
    var campo = $("#campo_sensores_informe_fichero_comparacion_periodos").text();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_informe_fichero_comparacion_periodos').text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_comparacion_periodos").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_comparacion_periodos").text();

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_comparacion_periodos").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_comparacion_periodos").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_comparacion_periodos").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_comparacion_periodos").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Comprobación de fechas y número de días de los periodos
        var fecha_inicio_anterior = $("#fecha_inicio_periodo_anterior_sensores_informe_fichero_comparacion_periodos").text();
        var fecha_inicio_posterior = $("#fecha_inicio_periodo_posterior_sensores_informe_fichero_comparacion_periodos").text();
        var numero_dias_periodo = parseInt($("#numero_dias_periodo_sensores_informe_fichero_comparacion_periodos").text());
        var resultado = comprueba_fechas_numero_dias_periodos_correctos_usuario_interno(fecha_inicio_anterior, fecha_inicio_posterior, numero_dias_periodo);
        if (resultado.res == "ERROR") {
            error_parametros = true;
            descripcion_error_parametros = resultado.msg;
        }
        else {
            parametros_informe["fecha_inicio_anterior"] = fecha_inicio_anterior;
            parametros_informe["fecha_inicio_posterior"] = fecha_inicio_posterior;
            parametros_informe["numero_dias_periodo"] = numero_dias_periodo;
        }
    }
    parametros_informe["error_parametros"] = error_parametros;
    parametros_informe["descripcion_error_parametros"] = descripcion_error_parametros;

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe fichero de comparación con perfil horario
function dame_parametros_informe_fichero_sensores_comparacion_perfil_horario() {
    // Se recuperan la clase de sensor, campo y parámetros extra
    var clase_sensor = $("#clase_sensor_sensores_informe_fichero_comparacion_perfil_horario").text();
    var campo = $("#campo_sensores_informe_fichero_comparacion_perfil_horario").text();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_informe_fichero_comparacion_perfil_horario').text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_comparacion_perfil_horario").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_comparacion_perfil_horario").text();

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_comparacion_perfil_horario").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_comparacion_perfil_horario").text();

    // Fechas de inicio y fin de perfil horario
    var fecha_inicio_perfil_horario = $('#fecha_inicio_perfil_horario_sensores_informe_fichero_comparacion_perfil_horario').text();
    var fecha_fin_perfil_horario = $('#fecha_fin_perfil_horario_sensores_informe_fichero_comparacion_perfil_horario').text();

    // Tipo de perfil horario y agrupaciones de días
    var tipo_perfil_horario = $('#tipo_perfil_horario_sensores_informe_fichero_comparacion_perfil_horario').text();
    var cadena_agrupaciones_dias_semana = $('#agrupaciones_dias_semana_sensores_informe_fichero_comparacion_perfil_horario').text();
    var agrupaciones_dias_semana = dame_agrupaciones_dias_semana(cadena_agrupaciones_dias_semana);

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_comparacion_perfil_horario").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_comparacion_perfil_horario").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["fecha_inicio_perfil_horario"] = fecha_inicio_perfil_horario;
    parametros_informe["fecha_fin_perfil_horario"] = fecha_fin_perfil_horario;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["tipo_perfil_horario"] = tipo_perfil_horario;
    parametros_informe["agrupaciones_dias_semana"] = agrupaciones_dias_semana;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_comparacion_perfil_horario").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_comparacion_perfil_horario").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_comparacion_perfil_horario").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_comparacion_perfil_horario").text();
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


// Devuelve los parámetros del informe fichero de comparación de campos iguales
function dame_parametros_informe_fichero_sensores_comparacion_campos_iguales() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_comparacion_campos_iguales").text();

    // Se recuperan la clase de sensor, campo y parámetros extra
    var clase_sensor = $("#clase_sensor_sensores_informe_fichero_comparacion_campos_iguales").text();
    var campo = $("#campo_sensores_informe_fichero_comparacion_campos_iguales").text();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_informe_fichero_comparacion_campos_iguales').text();

    // Se recuperan los identificadores y los nombres de los sensores
    var ids_sensores = [];
    $("#ids_sensores_sensores_informe_fichero_comparacion_campos_iguales li").each(function() {
        ids_sensores.push($(this).text());
    });
    var nombres_sensores = [];
    $("#nombres_sensores_sensores_informe_fichero_comparacion_campos_iguales li").each(function() {
        nombres_sensores.push($(this).text());
    });

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_comparacion_campos_iguales").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_comparacion_campos_iguales").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_comparacion_campos_iguales").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_comparacion_campos_iguales").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_comparacion_campos_iguales").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_comparacion_campos_iguales").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_comparacion_campos_iguales").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_comparacion_campos_iguales").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_comparacion_campos_iguales").text();
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


// Devuelve los parámetros del informe fichero de comparación de campos diferentes
function dame_parametros_informe_fichero_sensores_comparacion_campos_diferentes() {
    // Se recupera el ratio seleccionado (si lo hay)
    var id_ratio = $("#id_ratio_sensores_informe_fichero_comparacion_campos_diferentes").text();

    // Se recuperan los identificadores y los nombres de los sensores
    var ids_sensores = [];
    $("#ids_sensores_sensores_informe_fichero_comparacion_campos_diferentes li").each(function() {
        ids_sensores.push($(this).text());
    });
    var nombres_sensores = [];
    $("#nombres_sensores_sensores_informe_fichero_comparacion_campos_diferentes li").each(function() {
        nombres_sensores.push($(this).text());
    });

    // Se recuperan las clases de los sensores
    var clases_sensores = [];
    $("#clases_sensores_sensores_informe_fichero_comparacion_campos_diferentes li").each(function() {
        clases_sensores.push($(this).text());
    });

    // Se recuperan los campos de los sensores
    var campos = [];
    $("#campos_informe_fichero_comparacion_campos_diferentes li").each(function() {
        campos.push($(this).text());
    });

    // Se recuperan los parámetros extra de los campos de los sensores
    var parametros_extra_campos = [];
    $("#parametros_extra_campos_informe_fichero_comparacion_campos_diferentes li").each(function() {
        parametros_extra_campos.push($(this).text());
    });

    // Intervalo de valores y unificar escalas
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_comparacion_campos_diferentes").text();
    var unificar_escalas = $("#unificar_escalas_sensores_informe_fichero_comparacion_campos_diferentes").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_comparacion_campos_diferentes").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_comparacion_campos_diferentes").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_comparacion_campos_diferentes").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["clases_sensores"] = clases_sensores;
    parametros_informe["campos"] = campos;
    parametros_informe["parametros_extra_campos"] = parametros_extra_campos;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["unificar_escalas"] = unificar_escalas;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_comparacion_campos_diferentes").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_comparacion_campos_diferentes").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_comparacion_campos_diferentes").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_comparacion_campos_diferentes").text();
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


// Devuelve los parámetros del informe fichero de análisis comparativo
function dame_parametros_informe_fichero_sensores_analisis_comparativo() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_analisis_comparativo").text();

    // Se recuperan la clase de sensor, campo y parámetros extra
    var clase_sensor = $("#clase_sensor_sensores_informe_fichero_analisis_comparativo").text();
    var campo = $("#campo_sensores_informe_fichero_analisis_comparativo").text();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_informe_fichero_analisis_comparativo').text();

    // Se recuperan los identificadores y los nombres de los sensores agregados
    var ids_sensores_agregados = [];
    $("#ids_sensores_agregados_sensores_informe_fichero_analisis_comparativo li").each(function() {
        ids_sensores_agregados.push($(this).text());
    });
    var nombres_sensores_agregados = [];
    $("#nombres_sensores_agregados_sensores_informe_fichero_analisis_comparativo li").each(function() {
        nombres_sensores_agregados.push($(this).text());
    });

    // Identificador y nombre de sensor destacado
    var id_sensor_destacado = $("#id_sensor_destacado_sensores_informe_fichero_analisis_comparativo").text();
    var nombre_sensor_destacado = $("#nombre_sensor_destacado_sensores_informe_fichero_analisis_comparativo").text();

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_analisis_comparativo").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_analisis_comparativo").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_analisis_comparativo").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_analisis_comparativo").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_analisis_comparativo").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["ids_sensores_agregados"] = ids_sensores_agregados;
    parametros_informe["nombres_sensores_agregados"] = nombres_sensores_agregados;
    parametros_informe["id_sensor_destacado"] = id_sensor_destacado;
    parametros_informe["nombre_sensor_destacado"] = nombre_sensor_destacado;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_analisis_comparativo").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_analisis_comparativo").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_analisis_comparativo").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_analisis_comparativo").text();
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


// Devuelve los parámetros del informe fichero de valores generales
function dame_parametros_informe_fichero_sensores_valores_generales() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_valores_generales").text();

    // Se recuperan las clases de sensor, campos y parámetros extra
    var clases_sensor = [];
    $("#clases_sensor_sensores_informe_fichero_valores_generales li").each(function() {
        clases_sensor.push($(this).text());
    });
    var campos = [];
    $("#campos_sensores_informe_fichero_valores_generales li").each(function() {
        campos.push($(this).text());
    });
    var parametros_extra_campos = [];
    $("#parametros_extra_campos_sensores_informe_fichero_valores_generales li").each(function() {
        parametros_extra_campos.push($(this).text());
    });

    // Se recuperan los identificadores y los nombresde los sensores
    var ids_sensores = [];
    $("#ids_sensores_sensores_informe_fichero_valores_generales li").each(function() {
        ids_sensores.push($(this).text());
    });
    var nombres_sensores = [];
    $("#nombres_sensores_sensores_informe_fichero_valores_generales li").each(function() {
        nombres_sensores.push($(this).text());
    });

    // Intervalo de valores y agregación
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_valores_generales").text();
    var agregacion = $("#agregacion_sensores_informe_fichero_valores_generales").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_valores_generales").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_valores_generales").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_valores_generales").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clases_sensor"] = clases_sensor;
    parametros_informe["campos"] = campos;
    parametros_informe["parametros_extra_campos"] = parametros_extra_campos;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["agregacion"] = agregacion;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_valores_generales").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_valores_generales").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_valores_generales").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_valores_generales").text();
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


// Devuelve los parámetros del informe fichero de incrementos totales
function dame_parametros_informe_fichero_sensores_incrementos_totales() {
    // Se recupera el ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_incrementos_totales").text();

    // Se recuperan las clases de sensor, campos y parámetros extra
    var clases_sensor = [];
    $("#clases_sensor_sensores_informe_fichero_incrementos_totales li").each(function() {
        clases_sensor.push($(this).text());
    });
    var campos = [];
    $("#campos_sensores_informe_fichero_incrementos_totales li").each(function() {
        campos.push($(this).text());
    });
    var parametros_extra_campos = [];
    $("#parametros_extra_campos_sensores_informe_fichero_incrementos_totales li").each(function() {
        parametros_extra_campos.push($(this).text());
    });

    // Se recuperan los identificadores y los nombresde los sensores
    var ids_sensores = [];
    $("#ids_sensores_sensores_informe_fichero_incrementos_totales li").each(function() {
        ids_sensores.push($(this).text());
    });
    var nombres_sensores = [];
    $("#nombres_sensores_sensores_informe_fichero_incrementos_totales li").each(function() {
        nombres_sensores.push($(this).text());
    });

    // Intervalo de valores y agregación
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_incrementos_totales").text();
    var agregacion = $("#agregacion_sensores_informe_fichero_incrementos_totales").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_incrementos_totales").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_incrementos_totales").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_incrementos_totales").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clases_sensor"] = clases_sensor;
    parametros_informe["campos"] = campos;
    parametros_informe["parametros_extra_campos"] = parametros_extra_campos;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["agregacion"] = agregacion;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_incrementos_totales").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_incrementos_totales").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_incrementos_totales").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_incrementos_totales").text();
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