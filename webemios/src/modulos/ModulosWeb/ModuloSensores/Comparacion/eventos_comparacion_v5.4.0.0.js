//
// Funciones de comparación (de valores de sensores)
//


// Muestra la comparación del valor de un sensor en dos periodos diferentes
function boton_sensores_comparacion_periodos_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_periodos(false);
    if (parametros_informe == null) {
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
        $("#informe-sin-datos-sensores-comparacion-periodos").hide();
        $("#informe-sensores-comparacion-periodos").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-valores-comparacion-periodos",
            "contenedor-tabla-evolucion-valores-comparacion-periodos",
            "grafica-diferencias-comparacion-periodos",
            "mapa-calor-diferencias-comparacion-periodos"]);

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
            id_mapa_calor_diferencias: "mapa-calor-diferencias-comparacion-periodos"};
        dibuja_informe_sensores_comparacion_periodos(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra la comparación del valor de un sensor con su perfil horario
function boton_sensores_comparacion_perfil_horario_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_perfil_horario(false);
    if (parametros_informe == null) {
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

    // Se recupera la información de comparación con perfil horario
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
        $("#informe-sin-datos-sensores-comparacion-perfil-horario").hide();
        $("#informe-sensores-comparacion-perfil-horario").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-valores-comparacion-perfil-horario",
            "grafica-diferencias-comparacion-perfil-horario",
            "grafica-diferencias-acumuladas-comparacion-perfil-horario",
            "grafica-valores-perfil-horario-comparacion-perfil-horario",
            "mapa-calor-diferencias-comparacion-perfil-horario-periodos"]);

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
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra la comparación del valor del mismo campo de varios sensores en un periodo de tiempo
function boton_sensores_comparacion_campos_iguales_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_campos_iguales(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clase_sensor = parametros_informe["clase_sensor"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

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
        $("#informe-sin-datos-sensores-comparacion-campos-iguales").hide();
        $("#informe-sensores-comparacion-campos-iguales").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-valores-comparacion-campos-iguales",
            "tabla-diferencias-valores-comparacion-campos-iguales",
            "grafica-diferencias-comparacion-campos-iguales"]);
        for (var i = 1; i <= NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; i++) {
            vacia_elemento("mapa-calor-diferencias-comparacion-campos-iguales-" + i);
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
            id_mapas_calor_diferencias: "mapa-calor-diferencias-comparacion-campos-iguales"};
        dibuja_informe_sensores_comparacion_campos_iguales(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra la comparación del valor de diferentes campos de sensores en un periodo de tiempo
function boton_sensores_comparacion_campos_diferentes_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_comparacion_campos_diferentes(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var clases_sensores = parametros_informe["clases_sensores"];
    var ids_sensores = parametros_informe["ids_sensores"];
    var nombres_sensores = parametros_informe["nombres_sensores"];
    var campos = parametros_informe["campos"];
    var parametros_extra_campos = parametros_informe["parametros_extra_campos"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var unificar_escalas = parametros_informe["unificar_escalas"];
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

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
        $("#informe-sin-datos-sensores-comparacion-campos-diferentes").hide();
        $("#informe-sensores-comparacion-campos-diferentes").show();

        // Se borran los datos anteriores
        vacia_elemento("grafica-valores-comparacion-campos-diferentes");

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
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra la comparación de análisis comparativo
function boton_sensores_analisis_comparativo_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_analisis_comparativo(false);
    if (parametros_informe == null) {
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
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Desfase horario respecto a la hora UTC
    var minutos_desfase_utc = (new Date().getTimezoneOffset() * -1);

    // Se recuperan los datos para el informe
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
        $("#informe-sin-datos-sensores-analisis-comparativo").hide();
        $("#informe-sensores-analisis-comparativo").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-valores-analisis-comparativo",
            "grafica-valores-acumulados-analisis-comparativo",
            "grafica-pareto-analisis-comparativo",
            "contenedor-tabla-valores-maximos-minimos-analisis-comparativo",
            "mapa-calor-diferencias-media-analisis-comparativo"]);

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
            id_mapa_calor_diferencias: "mapa-calor-diferencias-media-analisis-comparativo"
        };
        dibuja_informe_sensores_analisis_comparativo(
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
	});
}


// Muestra la comparación de valores generales
function boton_sensores_valores_generales_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_valores_generales(false);
    if (parametros_informe == null) {
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
    var fecha_inicio = parametros_informe["fecha_inicio"];
    var fecha_fin = parametros_informe["fecha_fin"];
    var hora_inicio = parametros_informe["hora_inicio"];
    var hora_fin = parametros_informe["hora_fin"];
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

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
        $("#informe-sin-datos-sensores-valores-generales").hide();
        $("#informe-sensores-valores-generales").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-valores-valores-generales",
            "grafica-valores-acumulados-valores-generales",
            "contenedor-tabla-valores-maximos-minimos-valores-generales"]);

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
            TIPO_INFORME_WEB_EMIOS);
	});
}


// Muestra la comparación de incrementos totales
function boton_sensores_incrementos_totales_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_incrementos_totales(false);
    if (parametros_informe == null) {
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
    var fecha_hora_inicio = parametros_informe["fecha_hora_inicio"];
    var fecha_hora_fin = parametros_informe["fecha_hora_fin"];
    var horario_semanal = parametros_informe["horario_semanal"];
    var exclusion_fechas = parametros_informe["exclusion_fechas"];
    var inclusion_fechas = parametros_informe["inclusion_fechas"];

    // Se recuperan los datos para el informe
	$.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_incrementos_totales_sensores.php", {
        id_ratio: id_ratio,
        clases_sensor: clases_sensor,
        campos: campos,
        parametros_extra_campos: parametros_extra_campos,
        ids_sensores: ids_sensores,
        nombres_sensores: nombres_sensores,
        intervalo_valores: intervalo_valores,
        agregacion: agregacion,
		fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas)
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
        $("#informe-sin-datos-sensores-incrementos-totales").hide();
        $("#informe-sensores-incrementos-totales").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-incrementos-totales-incrementos-totales",
            "grafica-porcentajes-incrementos-incrementos-totales",
            "grafica-incrementos-incrementos-totales",
            "grafica-incrementos-acumulados-incrementos-totales",
            "contenedor-tabla-incrementos-incrementos-totales"]);

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
            TIPO_INFORME_WEB_EMIOS);
    });
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de comparación de periodos de valores de un sensor
function dame_parametros_informe_sensores_comparacion_periodos(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Clase de sensor
    var clase_sensor = $('#clase_sensor_sensores_comparacion_periodos').val();
    if (clase_sensor == CLASE_NINGUNA) {
		jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
        return (null);
	}

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_comparacion_periodos').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_comparacion_periodos :selected').text();

    // Campo y parámetros extra
    var campo = $('#campo_sensores_comparacion_periodos').val();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_comparacion_periodos').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return (null);
    }

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = $('#intervalo_valores_sensores_comparacion_periodos').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_comparacion_periodos').val();

    // Horario semanal y exclusión de fechas
    var horario_semanal = dame_horario_semanal_controles("sensores_comparacion_periodos", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_comparacion_periodos");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }

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

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        // Comprobación de fechas y número de días de los periodos
        var fecha_inicio_anterior = $('#fecha_inicio_anterior_sensores_comparacion_periodos').val();
        var fecha_inicio_posterior = $('#fecha_inicio_posterior_sensores_comparacion_periodos').val();
        var numero_dias_periodo = $('#numero_dias_sensores_comparacion_periodos').val();
        var periodos_correctos = comprueba_fechas_numero_dias_periodos_correctos(fecha_inicio_anterior, fecha_inicio_posterior, numero_dias_periodo);
        if (periodos_correctos == false) {
            return (null);
        }

        parametros_informe["fecha_inicio_anterior"] = fecha_inicio_anterior;
        parametros_informe["fecha_inicio_posterior"] = fecha_inicio_posterior;
        parametros_informe["numero_dias_periodo"] = numero_dias_periodo;
    }

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de comparación de valores de un sensor con su perfil horario
function dame_parametros_informe_sensores_comparacion_perfil_horario(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Clase de sensor
    var clase_sensor = $('#clase_sensor_sensores_comparacion_perfil_horario').val();
    if (clase_sensor == CLASE_NINGUNA) {
		jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
        return (null);
	}

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_comparacion_perfil_horario').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_comparacion_perfil_horario :selected').text();

    // Campo y parámetros extra
    var campo = $('#campo_sensores_comparacion_perfil_horario').val();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_comparacion_perfil_horario').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return (null);
    }

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = $('#intervalo_valores_sensores_comparacion_perfil_horario').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_comparacion_perfil_horario').val();

    // Fechas de inicio y fin de perfil horario
    var fecha_inicio_perfil_horario = $('#fecha_inicio_perfil_horario_sensores_comparacion_perfil_horario').val();
    var fecha_fin_perfil_horario = $('#fecha_fin_perfil_horario_sensores_comparacion_perfil_horario').val();
    var fechas_perfil_horario_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio_perfil_horario, null, fecha_fin_perfil_horario, null);
    if (fechas_perfil_horario_correctas == false) {
        return (null);
    }

    // Tipo de perfil horario y agrupaciones de días de la semana
    var tipo_perfil_horario = $('#tipo_perfil_horario_sensores_comparacion_perfil_horario').val();
    var agrupaciones_dias_semana = dame_agrupaciones_dias_semana_control("cadena_agrupaciones_dias_semana_sensores_comparacion_perfil_horario");
    if (agrupaciones_dias_semana.correcto == false) {
        return (null);
    }
    if (tipo_perfil_horario == TIPO_PERFIL_HORARIO_CONFIGURABLE) {
        if (agrupaciones_dias_semana.agrupaciones_dias.length == 0) {
            jAlert(TLNT.Idiomas._("No hay agrupaciones de días de la semana"));
            return (null);
        }
    }

    // Horario semanal y exclusión de fechas
    var horario_semanal = dame_horario_semanal_controles("sensores_comparacion_perfil_horario", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_comparacion_perfil_horario");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }

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
    parametros_informe["tipo_perfil_horario"] = tipo_perfil_horario;
    parametros_informe["agrupaciones_dias_semana"] = agrupaciones_dias_semana;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_sensores_comparacion_perfil_horario').val();
        var hora_inicio = $('#hora_inicio_sensores_comparacion_perfil_horario').val();
        var fecha_fin = $('#fecha_fin_sensores_comparacion_perfil_horario').val();
        var hora_fin = $('#hora_fin_sensores_comparacion_perfil_horario').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de comparación del valor de varios sensores en un periodo de tiempo
function dame_parametros_informe_sensores_comparacion_campos_iguales(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Clase de sensor
    var clase_sensor = $('#clase_sensor_sensores_comparacion_campos_iguales').val();
    if (clase_sensor == CLASE_NINGUNA) {
		jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
        return (null);
	}

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_comparacion_campos_iguales').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_comparacion_campos_iguales :selected').text();

    // Campo y parámetros extra
    var campo = $('#campo_sensores_comparacion_campos_iguales').val();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_comparacion_campos_iguales').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return (null);
    }

    // Identificadores y nombres de sensores
    var ids_sensores = [id_sensor];
    var nombres_sensores = [nombre_sensor];
    $("#ids_sensores_sensores_comparacion_campos_iguales option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores.push($(this).val());
            nombres_sensores.push($(this).text());
        }
    });
    if (ids_sensores.length == 1) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
		return (null);
	}

    // No se permite que el sensor principal sea el mismo que algún sensor secundario
    for (var i = 1; i < ids_sensores.length; i++) {
        if (nombres_sensores[0] == nombres_sensores[i]) {
            jAlert(TLNT.Idiomas._("El sensor principal coincide con algún sensor secundario"));
            return (null);
        }
    }

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = $('#intervalo_valores_sensores_comparacion_campos_iguales').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_comparacion_campos_iguales').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_comparacion_campos_iguales", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_comparacion_campos_iguales");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_comparacion_campos_iguales");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clase_sensor"] = clase_sensor;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_sensores_comparacion_campos_iguales').val();
        var hora_inicio = $('#hora_inicio_sensores_comparacion_campos_iguales').val();
        var fecha_fin = $('#fecha_fin_sensores_comparacion_campos_iguales').val();
        var hora_fin = $('#hora_fin_sensores_comparacion_campos_iguales').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de comparación de diferentes valores de sensores en un periodo de tiempo
function dame_parametros_informe_sensores_comparacion_campos_diferentes(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Parámetros de sensores
    var clases_sensores = [];
    var ids_sensores = [];
    var nombres_sensores = [];
    var campos = [];
    var parametros_extra_campos = [];
    var salir_funcion = false;
    for (var i = 0; i < NUMERO_SENSORES_COMPARACION_CAMPOS_DIFERENTES; i++) {
        var numero_sensor = i + 1;
        var id_lista_clases_sensores = "clase_sensor_" + numero_sensor + "_sensores_comparacion_campos_diferentes";
        var id_lista_sensores = "id_sensor_" + numero_sensor + "_sensores_comparacion_campos_diferentes";
        var id_lista_campos = "campo_" + numero_sensor + "_sensores_comparacion_campos_diferentes";
        var id_lista_parametros_extra_campo = "parametros_extra_campo_" + numero_sensor + "_sensores_comparacion_campos_diferentes";

        var clase_sensor = $('#' + id_lista_clases_sensores).val();
        if (clase_sensor != CLASE_NINGUNA) {
            clases_sensores.push(clase_sensor);

            // Identificador y nombre de sensor
            var id_sensor = $('#' + id_lista_sensores).val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                salir_funcion = true;
                return;
            }
            var nombre_sensor = $('#' + id_lista_sensores + ' :selected').text();
            ids_sensores.push(id_sensor);
            nombres_sensores.push(nombre_sensor);

            // Campo y parámetros extra de sensor
            var campo = $('#' + id_lista_campos).val();
            var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                salir_funcion = true;
                return;
            }
            campos.push(campo);
            parametros_extra_campos.push(parametros_extra_campo);
        };
    }
    if (salir_funcion == true) {
        return (null);
    }
    if (clases_sensores.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
        return (null);
	}

    // Intervalo de valores y unificación de escalas
    var intervalo_valores = $('#intervalo_valores_sensores_comparacion_campos_diferentes').val();
    var unificar_escalas = $('#unificar_escalas_sensores_comparacion_campos_diferentes').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_comparacion_campos_diferentes", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_comparacion_campos_diferentes");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_comparacion_campos_diferentes");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["clases_sensores"] = clases_sensores;
    parametros_informe["ids_sensores"] = ids_sensores;
    parametros_informe["nombres_sensores"] = nombres_sensores;
    parametros_informe["campos"] = campos;
    parametros_informe["parametros_extra_campos"] = parametros_extra_campos;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["unificar_escalas"] = unificar_escalas;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_sensores_comparacion_campos_diferentes').val();
        var hora_inicio = $('#hora_inicio_sensores_comparacion_campos_diferentes').val();
        var fecha_fin = $('#fecha_fin_sensores_comparacion_campos_diferentes').val();
        var hora_fin = $('#hora_fin_sensores_comparacion_campos_diferentes').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de análisis comparativo
function dame_parametros_informe_sensores_analisis_comparativo(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Se recupera el ratio seleccionado (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Se comprueba si hay clase seleccionada
    var clase_sensor = $('#clase_sensor_sensores_analisis_comparativo').val();
    if (clase_sensor == CLASE_NINGUNA) {
		jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
        return (null);
	}

    // Campo y parámetros extra
    var campo = $('#campo_sensores_analisis_comparativo').val();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_analisis_comparativo').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return (null);
    }

    // Se recuperan los identificadores y nombres de los sensores agregados
    var ids_sensores_agregados = [];
    var nombres_sensores_agregados = [];
    $("#ids_sensores_sensores_analisis_comparativo option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores_agregados.push($(this).val());
            nombres_sensores_agregados.push($(this).text());
        }
    });
    if (nombres_sensores_agregados.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor agregado"));
		return (null);
	}

    // Sensor destacado
    var id_sensor_destacado = $('#id_sensor_sensores_analisis_comparativo').val();
    var nombre_sensor_destacado = $('#id_sensor_sensores_analisis_comparativo :selected').text();

    // Intervalo de valores y tipo de mapa de calor
    var intervalo_valores = $('#intervalo_valores_sensores_analisis_comparativo').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_analisis_comparativo').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_analisis_comparativo", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_analisis_comparativo");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_analisis_comparativo");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

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

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_sensores_analisis_comparativo').val();
        var hora_inicio = $('#hora_inicio_sensores_analisis_comparativo').val();
        var fecha_fin = $('#fecha_fin_sensores_analisis_comparativo').val();
        var hora_fin = $('#hora_fin_sensores_analisis_comparativo').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de valores generales
function dame_parametros_informe_sensores_valores_generales(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Se recupera el ratio seleccionado (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Se recuperan los parámetros de las clases de sensor seleccionadas
    var clases_sensor = [];
    var campos = [];
    var parametros_extra_campos = [];
    var salir_funcion = false;
    for (var i = 0; i < NUMERO_CLASES_SENSOR_VALORES_GENERALES; i++) {
        var numero_clase_sensor = i + 1;
        var id_lista_clases_sensor = "clase_sensor_" + numero_clase_sensor + "_sensores_valores_generales";
        var id_lista_campos = "campo_" + numero_clase_sensor + "_sensores_valores_generales";
        var id_lista_parametros_extra_campo = "parametros_extra_campo_" + numero_clase_sensor + "_sensores_valores_generales";

        var clase_sensor = $('#' + id_lista_clases_sensor).val();
        if (clase_sensor != CLASE_NINGUNA) {
            clases_sensor.push(clase_sensor);

            // Se recupera el campo y los parámetros extra
            var campo = $('#' + id_lista_campos).val();
            var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                salir_funcion = true;
                return;
            }
            campos.push(campo);
            parametros_extra_campos.push(parametros_extra_campo);
        }
        else {
            // Nota: La primera clase debe estar seleccionada (es la que determina el intervalo y la agregación)
            if (i == 0) {
                jAlert(TLNT.Idiomas._("Debe seleccionar la primera clase de sensor"));
                salir_funcion = true;
                return;
            }
        }
    }
    if (salir_funcion == true) {
        return (null);
    }
    if (clases_sensor.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos una clase de sensor"));
        return (null);
	}

    // Se recuperan los identificadores y nombres de los sensores seleccionados
    var ids_sensores = [];
    var nombres_sensores = [];
    $("#ids_sensores_sensores_valores_generales option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores.push($(this).val());
            nombres_sensores.push($(this).text());
        }
    });
    if (ids_sensores.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
		return (null);
	}

    // Intervalo de valores y agregación
    var intervalo_valores = $('#intervalo_valores_sensores_valores_generales').val();
    var agregacion = $('#agregacion_sensores_valores_generales').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_valores_generales", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_valores_generales");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_valores_generales");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

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

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_sensores_valores_generales').val();
        var hora_inicio = $('#hora_inicio_sensores_valores_generales').val();
        var fecha_fin = $('#fecha_fin_sensores_valores_generales').val();
        var hora_fin = $('#hora_fin_sensores_valores_generales').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}


// Devuelve los parámetros del informe de incrementos totales
function dame_parametros_informe_sensores_incrementos_totales(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Se recupera el ratio seleccionado (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Se recuperan los parámetros de las clases de sensor seleccionadas
    var clases_sensor = [];
    var campos = [];
    var parametros_extra_campos = [];
    var salir_funcion = false;
    for (var i = 0; i < NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES; i++) {
        var numero_clase_sensor = i + 1;
        var id_lista_clases_sensor = "clase_sensor_" + numero_clase_sensor + "_sensores_incrementos_totales";
        var id_lista_campos = "campo_" + numero_clase_sensor + "_sensores_incrementos_totales";
        var id_lista_parametros_extra_campo = "parametros_extra_campo_" + numero_clase_sensor + "_sensores_incrementos_totales";

        var clase_sensor = $('#' + id_lista_clases_sensor).val();
        if (clase_sensor != CLASE_NINGUNA) {
            clases_sensor.push(clase_sensor);

            // Se recupera el campo y los parámetros extra
            var campo = $('#' + id_lista_campos).val();
            var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                salir_funcion = true;
                return;
            }
            campos.push(campo);
            parametros_extra_campos.push(parametros_extra_campo);
        }
        else {
            // Nota: La primera clase debe estar seleccionada (es la que determina el intervalo y la agregación)
            if (i == 0) {
                jAlert(TLNT.Idiomas._("Debe seleccionar la primera clase de sensor"));
                salir_funcion = true;
                return;
            }
        }
    }
    if (salir_funcion == true) {
        return (null);
    }
    if (clases_sensor.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos una clase de sensor"));
        return (null);
	}

    // Se recuperan los identificadores y nombres de los sensores seleccionados
    var ids_sensores = [];
    var nombres_sensores = [];
    $("#ids_sensores_sensores_incrementos_totales option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores.push($(this).val());
            nombres_sensores.push($(this).text());
        }
    });
    if (ids_sensores.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
		return (null);
	}

    // Intervalo de valores y agregación
    var intervalo_valores = $('#intervalo_valores_sensores_incrementos_totales').val();
    var agregacion = $('#agregacion_sensores_incrementos_totales').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_incrementos_totales", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_incrementos_totales");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_incrementos_totales");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

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

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $('#fecha_inicio_sensores_incrementos_totales').val();
        var hora_inicio = $('#hora_inicio_sensores_incrementos_totales').val();
        var fecha_fin = $('#fecha_fin_sensores_incrementos_totales').val();
        var hora_fin = $('#hora_fin_sensores_incrementos_totales').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
        if (fechas_correctas == false) {
            return (null);
        }
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

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}

