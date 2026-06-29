//
// Funciones de información (de valores de sensores)
//


// Muestra la información de temperatura de un sensor
function boton_sensores_informacion_temperatura_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_temperatura(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var parametros_extra_campo = parametros_informe["parametros_extra_campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_temperatura.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        parametros_extra_campo: parametros_extra_campo,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-sensores-informacion-temperatura").hide();
        $("#informe-sensores-informacion-temperatura").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-informacion-temperatura",
            "descripcion-sensor-informacion-temperatura",
            "texto-informacion-datos-informacion-temperatura",
            "contenedor-tabla-comentarios-informacion-temperatura",
            "mapa-calor-informacion-temperatura"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            campo: campo,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-temperatura",
            intervalo_valores: intervalo_valores,
            id_grafica_temperatura: "grafica-informacion-temperatura",
            id_descripcion_sensor: "descripcion-sensor-informacion-temperatura",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-temperatura",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-temperatura",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_temperatura: "mapa-calor-informacion-temperatura"};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_TEMPERATURA,
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra la información de humedad de un sensor
function boton_sensores_informacion_humedad_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_humedad(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_humedad.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-sensores-informacion-humedad").hide();
        $("#informe-sensores-informacion-humedad").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-informacion-humedad",
            "descripcion-sensor-informacion-humedad",
            "texto-informacion-datos-informacion-humedad",
            "contenedor-tabla-comentarios-informacion-humedad",
            "mapa-calor-informacion-humedad"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-humedad",
            intervalo_valores: intervalo_valores,
            id_grafica_humedad: "grafica-informacion-humedad",
            id_descripcion_sensor: "descripcion-sensor-informacion-humedad",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-humedad",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-humedad",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_humedad: "mapa-calor-informacion-humedad"};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_HUMEDAD,
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra la información de luz interior de un sensor
function boton_sensores_informacion_luz_interior_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_luz_interior(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_luz_interior.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-sensores-informacion-luz-interior").hide();
        $("#informe-sensores-informacion-luz-interior").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-luz-informacion-luz-interior",
            "grafica-luz-artificial-informacion-luz-interior",
            "descripcion-sensor-informacion-luz-interior",
            "texto-informacion-datos-informacion-luz-interior",
            "contenedor-tabla-comentarios-informacion-luz-interior",
            "mapa-calor-luz-informacion-luz-interior",
            "mapa-calor-luz-artificial-informacion-luz-interior"]);
        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-luz-interior",
            intervalo_valores: intervalo_valores,
            id_grafica_luz: "grafica-luz-informacion-luz-interior",
            id_grafica_luz_artificial: "grafica-luz-artificial-informacion-luz-interior",
            id_descripcion_sensor: "descripcion-sensor-informacion-luz-interior",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-luz-interior",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-luz-interior",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_luz: "mapa-calor-luz-informacion-luz-interior",
            id_mapa_calor_luz_artificial: "mapa-calor-luz-artificial-informacion-luz-interior"};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_LUZ_INTERIOR,
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra el viento de un sensor
function boton_sensores_informacion_viento_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_viento(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_viento.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-sensores-informacion-viento").hide();
        $("#informe-sensores-informacion-viento").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-velocidad-informacion-viento",
            "grafica-direccion-informacion-viento",
            "descripcion-sensor-informacion-viento",
            "texto-informacion-datos-informacion-viento",
            "contenedor-tabla-comentarios-informacion-viento",
            "grafico-velocidad-informacion-viento",
            "grafico-frecuencia-informacion-viento",
            "mapa-calor-velocidad-informacion-viento",
            "mapa-calor-direccion-informacion-viento"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-viento",
            intervalo_valores: intervalo_valores,
            id_grafica_velocidad_viento: "grafica-velocidad-informacion-viento",
            id_grafica_direccion_viento: "grafica-direccion-informacion-viento",
            id_descripcion_sensor: "descripcion-sensor-informacion-viento",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-viento",
            id_grafico_frecuencia_viento: "grafico-frecuencia-informacion-viento",
            id_grafico_velocidad_viento: "grafico-velocidad-informacion-viento",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-viento",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_velocidad_viento: "mapa-calor-velocidad-informacion-viento",
            id_mapa_calor_direccion_viento: "mapa-calor-direccion-informacion-viento"};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_VIENTO,
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra información de energía activa de un sensor
function boton_sensores_informacion_energia_activa_ver_informe() {
    boton_sensores_informacion_energia_ver_informe(CLASE_SENSOR_ENERGIA_ACTIVA);
}


// Muestra información de energía reactiva de un sensor
function boton_sensores_informacion_energia_reactiva_ver_informe() {
    boton_sensores_informacion_energia_ver_informe(CLASE_SENSOR_ENERGIA_REACTIVA);
}


// Muestra información de energia de un sensor
function boton_sensores_informacion_energia_ver_informe(clase_sensor) {
    // Sufijo de controles de tipo de energía
    var sufijo_tipo_energia = null;
    switch (clase_sensor) {
        case CLASE_SENSOR_ENERGIA_ACTIVA: {
            sufijo_tipo_energia = "activa";
            break;
        }
        case CLASE_SENSOR_ENERGIA_REACTIVA: {
            sufijo_tipo_energia = "reactiva";
            break;
        }
    }

    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_energia(sufijo_tipo_energia, false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_energia.php", {
        id_ratio: id_ratio,
        id_sensor: id_sensor,
        clase_sensor: clase_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-sensores-informacion-energia-" + sufijo_tipo_energia).hide();
        $("#informe-sensores-informacion-energia-" + sufijo_tipo_energia).show();

        // Identificadores de controles
        var id_parametros_resultado_informe = "parametros-resultado-informe-informacion-energia-" + sufijo_tipo_energia;
        var id_grafica_valores = "grafica-informacion-energia-" + sufijo_tipo_energia;
        var id_grafica_valores_acumulados = "grafica-informacion-energia-" + sufijo_tipo_energia + "-acumulado";
        var id_descripcion_sensor = "descripcion-sensor-informacion-energia-" + sufijo_tipo_energia;
        var id_texto_informacion_datos = "texto-informacion-datos-informacion-energia-" + sufijo_tipo_energia;
        var id_contenedor_tabla_comentarios = "contenedor-tabla-comentarios-informacion-energia-" + sufijo_tipo_energia;
        var id_mapa_calor_valores = "mapa-calor-informacion-energia-" + sufijo_tipo_energia;

        // Se borran los datos anteriores
        vacia_elementos([
            id_grafica_valores,
            id_grafica_valores_acumulados,
            id_descripcion_sensor,
            id_texto_informacion_datos,
            id_contenedor_tabla_comentarios,
            id_mapa_calor_valores]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            clase_sensor: clase_sensor,
            campo: campo,
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: id_parametros_resultado_informe,
            intervalo_valores: intervalo_valores,
            id_grafica_valores: id_grafica_valores,
            id_grafica_valores_acumulados: id_grafica_valores_acumulados,
            id_descripcion_sensor: id_descripcion_sensor,
            id_texto_informacion_datos: id_texto_informacion_datos,
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: id_mapa_calor_valores};
        dibuja_informe_sensores_informacion(
            clase_sensor,
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra información de cortes de tensión de un sensor
function boton_sensores_informacion_cortes_tension_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_cortes_tension(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_cortes_tension.php", {
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-sensores-informacion-cortes-tension").hide();
        $("#informe-sensores-informacion-cortes-tension").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-informacion-cortes-tension-cortes",
            "grafica-informacion-cortes-tension-cortes-acumulados",
            "descripcion-sensor-informacion-cortes-tension",
            "texto-informacion-datos-informacion-cortes-tension",
            "contenedor-tabla-comentarios-informacion-cortes-tension",
            "mapa-calor-informacion-cortes-tension-cortes"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-cortes-tension",
            intervalo_valores: intervalo_valores,
            id_grafica_cortes_tension: "grafica-informacion-cortes-tension-cortes",
            id_grafica_cortes_tension_acumulados: "grafica-informacion-cortes-tension-cortes-acumulados",
            id_descripcion_sensor: "descripcion-sensor-informacion-cortes-tension",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-cortes-tension",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-cortes-tension",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_cortes_tension: "mapa-calor-informacion-cortes-tension-cortes"};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_CORTES_TENSION,
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra información de compra de energía de un sensor
function boton_sensores_informacion_compra_energia_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_compra_energia(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_compra_energia.php", {
        id_ratio: id_ratio,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-sensores-informacion-compra-energia").hide();
        $("#informe-sensores-informacion-compra-energia").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-informacion-compra-energia",
            "grafica-informacion-compra-energia-acumulado",
            "descripcion-sensor-informacion-compra-energia",
            "texto-informacion-datos-informacion-compra-energia",
            "contenedor-tabla-comentarios-informacion-compra-energia",
            "mapa-calor-informacion-compra-energia"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            campo: campo,
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-compra-energia",
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-informacion-compra-energia",
            id_grafica_valores_acumulados: "grafica-informacion-compra-energia-acumulado",
            id_descripcion_sensor: "descripcion-sensor-informacion-compra-energia",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-compra-energia",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-compra-energia",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: "mapa-calor-informacion-compra-energia"};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_COMPRA_ENERGIA,
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra información de gas de un sensor
function boton_sensores_informacion_gas_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_gas(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_gas.php", {
        id_ratio: id_ratio,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-sensores-informacion-gas").hide();
        $("#informe-sensores-informacion-gas").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-informacion-gas",
            "grafica-informacion-gas-acumulado",
            "descripcion-sensor-informacion-gas",
            "texto-informacion-datos-informacion-gas",
            "contenedor-tabla-comentarios-informacion-gas",
            "mapa-calor-informacion-gas"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            campo: campo,
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-gas",
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-informacion-gas",
            id_grafica_valores_acumulados: "grafica-informacion-gas-acumulado",
            id_descripcion_sensor: "descripcion-sensor-informacion-gas",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-gas",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-gas",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: "mapa-calor-informacion-gas"};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_GAS,
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra información de agua de un sensor
function boton_sensores_informacion_agua_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_agua(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_agua.php", {
        id_ratio: id_ratio,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-sensores-informacion-agua").hide();
        $("#informe-sensores-informacion-agua").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-informacion-agua",
            "grafica-informacion-agua-acumulado",
            "descripcion-sensor-informacion-agua",
            "texto-informacion-datos-informacion-agua",
            "contenedor-tabla-comentarios-informacion-agua",
            "mapa-calor-informacion-agua"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            campo: campo,
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-agua",
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-informacion-agua",
            id_grafica_valores_acumulados: "grafica-informacion-agua-acumulado",
            id_descripcion_sensor: "descripcion-sensor-informacion-agua",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-agua",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-agua",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: "mapa-calor-informacion-agua"};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_AGUA,
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


// Muestra información genérica de un sensor
function boton_sensores_informacion_generica_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_sensores_informacion_generica(false);
    if (parametros_informe == null) {
        return;
    }

    // Parámetros del informe
    var id_ratio = parametros_informe["id_ratio"];
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_generico.php", {
        id_ratio: id_ratio,
        id_sensor: id_sensor,
        nombre_sensor: nombre_sensor,
        campo: campo,
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        minutos_desfase_utc: minutos_desfase_utc,
        intervalo_valores: intervalo_valores,
        tipo_mapa_calor: tipo_mapa_calor,
        comentarios: comentarios,
        horario_semanal: JSON.stringify(horario_semanal),
        exclusion_fechas: JSON.stringify(exclusion_fechas),
        inclusion_fechas: JSON.stringify(inclusion_fechas),
        tipo_informe: TIPO_INFORME_WEB_EMIOS
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
        $("#informe-sin-datos-sensores-informacion-generica").hide();
        $("#informe-sensores-informacion-generica").show();

        // Se borran los datos anteriores
        vacia_elementos([
            "grafica-informacion-generica",
            "grafica-informacion-generica-acumulado",
            "descripcion-sensor-informacion-generica",
            "texto-informacion-datos-informacion-generica",
            "contenedor-tabla-comentarios-informacion-generica",
            "mapa-calor-informacion-generica"]);

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            campo: campo,
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-generica",
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-informacion-generica",
            id_grafica_valores_acumulados: "grafica-informacion-generica-acumulado",
            id_descripcion_sensor: "descripcion-sensor-informacion-generica",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-generica",
            comentarios: comentarios,
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-generica",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: "mapa-calor-informacion-generica"};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_GENERICA,
            parametros,
            resultado,
            null,
            TIPO_INFORME_WEB_EMIOS);
    });
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de información de temperatura de un sensor
function dame_parametros_informe_sensores_informacion_temperatura(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_informacion_temperatura').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_informacion_temperatura :selected').text();

    // Campo y parámetros extra
    var campo = $('#campo_sensores_informacion_temperatura').val();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_informacion_temperatura').val();
    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(CLASE_SENSOR_TEMPERATURA, campo, parametros_extra_campo);
    if (parametros_extra_campo_correctos == false) {
        return (null);
    }

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $('#intervalo_valores_sensores_informacion_temperatura').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_informacion_temperatura').val();
    var comentarios = $('#comentarios_sensores_informacion_temperatura').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_informacion_temperatura", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_informacion_temperatura");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_informacion_temperatura");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["parametros_extra_campo"] = parametros_extra_campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_informacion_temperatura').val();
        var hora_inicio = $('#hora_inicio_sensores_informacion_temperatura').val();
        var fecha_fin = $('#fecha_fin_sensores_informacion_temperatura').val();
        var hora_fin = $('#hora_fin_sensores_informacion_temperatura').val();
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


// Devuelve los parámetros del informe de información de humedad de un sensor
function dame_parametros_informe_sensores_informacion_humedad(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_informacion_humedad').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_informacion_humedad :selected').text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $('#intervalo_valores_sensores_informacion_humedad').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_informacion_humedad').val();
    var comentarios = $('#comentarios_sensores_informacion_humedad').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_informacion_humedad", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_informacion_humedad");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_informacion_humedad");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_informacion_humedad').val();
        var hora_inicio = $('#hora_inicio_sensores_informacion_humedad').val();
        var fecha_fin = $('#fecha_fin_sensores_informacion_humedad').val();
        var hora_fin = $('#hora_fin_sensores_informacion_humedad').val();
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


// Devuelve los parámetros del informe de información de luz interior de un sensor
function dame_parametros_informe_sensores_informacion_luz_interior(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_informacion_luz_interior').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_informacion_luz_interior :selected').text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $('#intervalo_valores_sensores_informacion_luz_interior').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_informacion_luz_interior').val();
    var comentarios = $('#comentarios_sensores_informacion_luz_interior').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_informacion_luz_interior", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_informacion_luz_interior");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_informacion_luz_interior");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_informacion_luz_interior').val();
        var hora_inicio = $('#hora_inicio_sensores_informacion_luz_interior').val();
        var fecha_fin = $('#fecha_fin_sensores_informacion_luz_interior').val();
        var hora_fin = $('#hora_fin_sensores_informacion_luz_interior').val();
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


// Devuelve los parámetros del informe de información de viento de un sensor
function dame_parametros_informe_sensores_informacion_viento(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_informacion_viento').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_informacion_viento :selected').text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $('#intervalo_valores_sensores_informacion_viento').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_informacion_viento').val();
    var comentarios = $('#comentarios_sensores_informacion_viento').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_informacion_viento", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_informacion_viento");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_informacion_viento");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_informacion_viento').val();
        var hora_inicio = $('#hora_inicio_sensores_informacion_viento').val();
        var fecha_fin = $('#fecha_fin_sensores_informacion_viento').val();
        var hora_fin = $('#hora_fin_sensores_informacion_viento').val();
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


// Devuelve los parámetros del informe de información de energía de un sensor
function dame_parametros_informe_sensores_informacion_energia(sufijo_tipo_energia, informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio
    var id_ratio = dame_id_ratio_seleccionado();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_informacion_energia_' + sufijo_tipo_energia).val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_informacion_energia_' + sufijo_tipo_energia + ' :selected').text();

    // Campo
    var campo = $('#campo_sensores_informacion_energia_' + sufijo_tipo_energia).val();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $('#intervalo_valores_sensores_informacion_energia_' + sufijo_tipo_energia).val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_informacion_energia_' + sufijo_tipo_energia).val();
    var comentarios = $('#comentarios_sensores_informacion_energia_' + sufijo_tipo_energia).val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_informacion_energia_" + sufijo_tipo_energia, false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_informacion_energia_" + sufijo_tipo_energia);
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_informacion_energia_" + sufijo_tipo_energia);
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_informacion_energia_' + sufijo_tipo_energia).val();
        var hora_inicio = $('#hora_inicio_sensores_informacion_energia_' + sufijo_tipo_energia).val();
        var fecha_fin = $('#fecha_fin_sensores_informacion_energia_' + sufijo_tipo_energia).val();
        var hora_fin = $('#hora_fin_sensores_informacion_energia_' + sufijo_tipo_energia).val();
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


// Devuelve los parámetros del informe de cortes de tension de un sensor
function dame_parametros_informe_sensores_informacion_cortes_tension(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_informacion_cortes_tension').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_informacion_cortes_tension' + ' :selected').text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $('#intervalo_valores_sensores_informacion_cortes_tension').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_informacion_cortes_tension').val();
    var comentarios = $('#comentarios_sensores_informacion_cortes_tension').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_informacion_cortes_tension", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_informacion_cortes_tension");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_informacion_cortes_tension");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_informacion_cortes_tension').val();
        var hora_inicio = $('#hora_inicio_sensores_informacion_cortes_tension').val();
        var fecha_fin = $('#fecha_fin_sensores_informacion_cortes_tension').val();
        var hora_fin = $('#hora_fin_sensores_informacion_cortes_tension').val();
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


// Devuelve los parámetros del informe de información de compra de energía de un sensor
function dame_parametros_informe_sensores_informacion_compra_energia(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio
    var id_ratio = dame_id_ratio_seleccionado();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_informacion_compra_energia').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_informacion_compra_energia' + ' :selected').text();

    // Campo
    var campo = $('#campo_sensores_informacion_compra_energia').val();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $('#intervalo_valores_sensores_informacion_compra_energia').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_informacion_compra_energia').val();
    var comentarios = $('#comentarios_sensores_informacion_compra_energia').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_informacion_compra_energia", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_informacion_compra_energia");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_informacion_compra_energia");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_informacion_compra_energia').val();
        var hora_inicio = $('#hora_inicio_sensores_informacion_compra_energia').val();
        var fecha_fin = $('#fecha_fin_sensores_informacion_compra_energia').val();
        var hora_fin = $('#hora_fin_sensores_informacion_compra_energia').val();
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


// Devuelve los parámetros del informe de información de gas de un sensor
function dame_parametros_informe_sensores_informacion_gas(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio
    var id_ratio = dame_id_ratio_seleccionado();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_informacion_gas').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_informacion_gas' + ' :selected').text();

    // Campo
    var campo = $('#campo_sensores_informacion_gas').val();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $('#intervalo_valores_sensores_informacion_gas').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_informacion_gas').val();
    var comentarios = $('#comentarios_sensores_informacion_gas').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_informacion_gas", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_informacion_gas");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_informacion_gas");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_informacion_gas').val();
        var hora_inicio = $('#hora_inicio_sensores_informacion_gas').val();
        var fecha_fin = $('#fecha_fin_sensores_informacion_gas').val();
        var hora_fin = $('#hora_fin_sensores_informacion_gas').val();
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


// Devuelve los parámetros del informe de información de agua de un sensor
function dame_parametros_informe_sensores_informacion_agua(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio
    var id_ratio = dame_id_ratio_seleccionado();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_informacion_agua').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_informacion_agua' + ' :selected').text();

    // Campo
    var campo = $('#campo_sensores_informacion_agua').val();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $('#intervalo_valores_sensores_informacion_agua').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_informacion_agua').val();
    var comentarios = $('#comentarios_sensores_informacion_agua').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_informacion_agua", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_informacion_agua");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_informacion_agua");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_informacion_agua').val();
        var hora_inicio = $('#hora_inicio_sensores_informacion_agua').val();
        var fecha_fin = $('#fecha_fin_sensores_informacion_agua').val();
        var hora_fin = $('#hora_fin_sensores_informacion_agua').val();
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


// Devuelve los parámetros del informe de información genérica de un sensor
function dame_parametros_informe_sensores_informacion_generica(informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio
    var id_ratio = dame_id_ratio_seleccionado();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_sensores_informacion_generica').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
	}
    var nombre_sensor = $('#id_sensor_sensores_informacion_generica' + ' :selected').text();

    // Campo
    var campo = $('#campo_sensores_informacion_generica').val();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $('#intervalo_valores_sensores_informacion_generica').val();
    var tipo_mapa_calor = $('#tipo_mapa_calor_sensores_informacion_generica').val();
    var comentarios = $('#comentarios_sensores_informacion_generica').val();

    // Horario semanal
    var horario_semanal = dame_horario_semanal_controles("sensores_informacion_generica", false);
    if (horario_semanal.correcto == false) {
        return (null);
    }

    // Exclusión e inclusión de fechas
    var exclusion_fechas = dame_fechas_controles("exclusion_fechas_sensores_informacion_generica");
    if (exclusion_fechas.correcto == false) {
        return (null);
    }
    var inclusion_fechas = dame_fechas_controles("inclusion_fechas_sensores_informacion_generica");
    if (inclusion_fechas.correcto == false) {
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_sensores_informacion_generica').val();
        var hora_inicio = $('#hora_inicio_sensores_informacion_generica').val();
        var fecha_fin = $('#fecha_fin_sensores_informacion_generica').val();
        var hora_fin = $('#hora_fin_sensores_informacion_generica').val();
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
