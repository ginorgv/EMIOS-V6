//
// Funciones de informes fichero de información (de Sensores)
//


// Genera el informe fichero de información de temperatura
function sensores_informacion_temperatura_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_informacion_temperatura();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-temperatura').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-informacion-temperatura-1",
            "pagina-informe-fichero-informacion-temperatura-2"]);
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
            $('#mensaje-aviso-informe-fichero-informacion-temperatura').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-informacion-temperatura-1",
                "pagina-informe-fichero-informacion-temperatura-2"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-informacion-temperatura-1').html(TLNT.Idiomas._("Temperatura"));
        $('#titulo-informe-fichero-informacion-temperatura-2').html(TLNT.Idiomas._("Mapa de calor de temperatura"));

        // Localización del mapa de calor de temperatura
        var numero_comentarios = resultado.numero_comentarios;
        var dias_mapa_calor_temperatura = resultado.dias_mapa_calor_temperatura;
        var id_mapa_calor_temperatura = null;
        var altura_maxima_mapa_calor_temperatura = null;
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var id_mapa_calor_temperatura = null;
            var altura_maxima_mapa_calor_temperatura = null;
            var mostrar_comentarios = ((numero_comentarios > 0) && (comentarios == COMENTARIOS_GRAFICA_TABLA));
            if ((mostrar_comentarios == true) || (dias_mapa_calor_temperatura.length > NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO)) {
                id_mapa_calor_temperatura = "mapa-calor-informacion-temperatura-2";
                altura_maxima_mapa_calor_temperatura = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
                oculta_elemento("mapa-calor-informacion-temperatura-1");
            }
            else {
                id_mapa_calor_temperatura = "mapa-calor-informacion-temperatura-1";
                altura_maxima_mapa_calor_temperatura = ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR;
                elimina_elemento("pagina-informe-fichero-informacion-temperatura-2");
            };
        }
        else {
            oculta_elemento("mapa-calor-informacion-temperatura-1");
            elimina_elemento("pagina-informe-fichero-informacion-temperatura-2");
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            campo: campo,
            intervalo_valores: intervalo_valores,
            id_grafica_temperatura: "grafica-informacion-temperatura",
            id_descripcion_sensor: "descripcion-sensor-informacion-temperatura",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-temperatura",
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-temperatura",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_temperatura: id_mapa_calor_temperatura,
            altura_maxima_mapa_calor_temperatura: altura_maxima_mapa_calor_temperatura};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_TEMPERATURA,
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de información de humedad
function sensores_informacion_humedad_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_informacion_humedad();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-humedad').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-informacion-humedad-1",
            "pagina-informe-fichero-informacion-humedad-2"]);
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
            $('#mensaje-aviso-informe-fichero-informacion-humedad').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-informacion-humedad-1",
                "pagina-informe-fichero-informacion-humedad-2"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-informacion-humedad-1').html(TLNT.Idiomas._("Humedad"));
        $('#titulo-informe-fichero-informacion-humedad-2').html(TLNT.Idiomas._("Mapa de calor de humedad"));

        // Localización del mapa de calor de humedad
        var numero_comentarios = resultado.numero_comentarios;
        var dias_mapa_calor_humedad = resultado.dias_mapa_calor_humedad;
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var id_mapa_calor_humedad = null;
            var altura_maxima_mapa_calor_humedad = null;
            var mostrar_comentarios = ((numero_comentarios > 0) && (comentarios == COMENTARIOS_GRAFICA_TABLA));
            if ((mostrar_comentarios == true) || (dias_mapa_calor_humedad.length > NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO)) {
                id_mapa_calor_humedad = "mapa-calor-informacion-humedad-2";
                altura_maxima_mapa_calor_humedad = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
                oculta_elemento("mapa-calor-informacion-humedad-1");
            }
            else {
                id_mapa_calor_humedad = "mapa-calor-informacion-humedad-1";
                altura_maxima_mapa_calor_humedad = ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR;
                elimina_elemento("pagina-informe-fichero-informacion-humedad-2");
            };
        }
        else {
            oculta_elemento("mapa-calor-informacion-humedad-1");
            elimina_elemento("pagina-informe-fichero-informacion-humedad-2");
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            intervalo_valores: intervalo_valores,
            id_grafica_humedad: "grafica-informacion-humedad",
            id_descripcion_sensor: "descripcion-sensor-informacion-humedad",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-humedad",
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-humedad",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_humedad: id_mapa_calor_humedad,
            altura_maxima_mapa_calor_humedad: altura_maxima_mapa_calor_humedad};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_HUMEDAD,
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de información de luz interior
function sensores_informacion_luz_interior_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_informacion_luz_interior();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-luz-interior').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-informacion-luz-interior-1",
            "pagina-informe-fichero-informacion-luz-interior-2",
            "pagina-informe-fichero-informacion-luz-interior-3"]);
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
            $('#mensaje-aviso-informe-fichero-informacion-luz-interior').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-informacion-luz-interior-1",
                "pagina-informe-fichero-informacion-luz-interior-2",
                "pagina-informe-fichero-informacion-luz-interior-3"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-informacion-luz-interior-1').html(TLNT.Idiomas._("Luz interior"));
        $('#titulo-informe-fichero-informacion-luz-interior-2').html(TLNT.Idiomas._("Mapa de calor de iluminación"));
        $('#titulo-informe-fichero-informacion-luz-interior-3').html(TLNT.Idiomas._("Mapa de calor de luz artificial"));

        // Se ocultan las páginas de los mapas de calor (si es necesario)
        if (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO) {
            elimina_elementos([
                "pagina-informe-fichero-informacion-luz-interior-2",
                "pagina-informe-fichero-informacion-luz-interior-3"]);
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            intervalo_valores: intervalo_valores,
            id_grafica_luz: "grafica-luz-informacion-luz-interior",
            id_grafica_luz_artificial: "grafica-luz-artificial-informacion-luz-interior",
            id_descripcion_sensor: "descripcion-sensor-informacion-luz-interior",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-luz-interior",
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-luz-interior",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_luz: "mapa-calor-luz-informacion-luz-interior",
            altura_maxima_mapa_calor_luz: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO,
            id_mapa_calor_luz_artificial: "mapa-calor-luz-artificial-informacion-luz-interior",
            altura_maxima_mapa_calor_luz_artificial: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_LUZ_INTERIOR,
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de información de viento
function sensores_informacion_viento_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_informacion_viento();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-viento').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-informacion-viento-1",
            "pagina-informe-fichero-informacion-viento-2",
            "pagina-informe-fichero-informacion-viento-3",
            "pagina-informe-fichero-informacion-viento-4"]);
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
            $('#mensaje-aviso-informe-fichero-informacion-viento').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-informacion-viento-1",
                "pagina-informe-fichero-informacion-viento-2",
                "pagina-informe-fichero-informacion-viento-3",
                "pagina-informe-fichero-informacion-viento-4"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-informacion-viento-1').html(TLNT.Idiomas._("Viento"));
        $('#titulo-informe-fichero-informacion-viento-2').html(TLNT.Idiomas._("Gráficos de viento"));
        $('#titulo-informe-fichero-informacion-viento-3').html(TLNT.Idiomas._("Mapa de calor de velocidad del viento"));
        $('#titulo-informe-fichero-informacion-viento-4').html(TLNT.Idiomas._("Mapa de calor de dirección del viento"));

        // Se ocultan las páginas de los mapas de calor (si es necesario)
        if (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO) {
            elimina_elementos([
                "pagina-informe-fichero-informacion-viento-3",
                "pagina-informe-fichero-informacion-viento-4"]);
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            intervalo_valores: intervalo_valores,
            id_grafica_velocidad_viento: "grafica-velocidad-informacion-viento",
            id_grafica_direccion_viento: "grafica-direccion-informacion-viento",
            id_descripcion_sensor: "descripcion-sensor-informacion-viento",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-viento",
            id_grafico_frecuencia_viento: "grafico-frecuencia-informacion-viento",
            id_grafico_velocidad_viento: "grafico-velocidad-informacion-viento",
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-viento",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_velocidad_viento: "mapa-calor-velocidad-informacion-viento",
            altura_maxima_mapa_calor_velocidad_viento: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO,
            id_mapa_calor_direccion_viento: "mapa-calor-direccion-informacion-viento",
            altura_maxima_mapa_calor_direccion_viento: ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_VIENTO,
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de información de energía
function sensores_informacion_energia_ver_informe_fichero(tipo_informe) {
    // Clase de sensor y sufijo de controles de tipo de energía
    var clase_sensor = null;
    var sufijo_tipo_energia = null;
    switch (tipo_informe) {
        case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA: {
            clase_sensor = CLASE_SENSOR_ENERGIA_ACTIVA;
            sufijo_tipo_energia = "activa";
            break;
        }
        case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA: {
            clase_sensor = CLASE_SENSOR_ENERGIA_REACTIVA;
            sufijo_tipo_energia = "reactiva";
            break;
        }
    }

    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_informacion_energia(sufijo_tipo_energia);
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-energia-' + sufijo_tipo_energia).html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-informacion-energia-" + sufijo_tipo_energia + "-1",
            "pagina-informe-fichero-informacion-energia-" + sufijo_tipo_energia + "-2"]);
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_energia.php", {
        id_ratio: id_ratio,
        clase_sensor: clase_sensor,
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
            $('#mensaje-aviso-informe-fichero-informacion-energia-' + sufijo_tipo_energia).html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-informacion-energia-" + sufijo_tipo_energia + "-1",
                "pagina-informe-fichero-informacion-energia-" + sufijo_tipo_energia + "-2"]);
            return;
        }

        // Títulos de la páginas
        switch (clase_sensor) {
            case CLASE_SENSOR_ENERGIA_ACTIVA: {
                $('#titulo-informe-fichero-informacion-energia-' + sufijo_tipo_energia + '-1').html(TLNT.Idiomas._("Energía activa"));
                $('#titulo-informe-fichero-informacion-energia-' + sufijo_tipo_energia + '-2').html(TLNT.Idiomas._("Mapa de calor de energía activa"));
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA: {
                $('#titulo-informe-fichero-informacion-energia-' + sufijo_tipo_energia + '-1').html(TLNT.Idiomas._("Energía reactiva"));
                $('#titulo-informe-fichero-informacion-energia-' + sufijo_tipo_energia + '-2').html(TLNT.Idiomas._("Mapa de calor de energía reactiva"));
                break;
            }
        }

        // Identificadores de controles
        var id_parametros_resultado_informe = "parametros-resultado-informe-informacion-energia-" + sufijo_tipo_energia;
        var id_grafica_valores = "grafica-informacion-energia-" + sufijo_tipo_energia;
        var id_grafica_valores_acumulados = "grafica-informacion-energia-" + sufijo_tipo_energia + "-acumulado";
        var id_descripcion_sensor = "descripcion-sensor-informacion-energia-" + sufijo_tipo_energia;
        var id_texto_informacion_datos = "texto-informacion-datos-informacion-energia-" + sufijo_tipo_energia;
        var id_contenedor_tabla_comentarios = "contenedor-tabla-comentarios-informacion-energia-" + sufijo_tipo_energia;

        // Localización del mapa de calor de valores
        var numero_comentarios = resultado.numero_comentarios;
        var dias_mapa_calor_valores = resultado.dias_mapa_calor_valores;
        var mostrar_grafica_valores_acumulados = (resultado.campo_incremental == true);
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var id_mapa_calor_valores = null;
            var altura_maxima_mapa_calor_valores = null;
            var numero_maximo_dias_mapa_calor_valores = null;
            if (mostrar_grafica_valores_acumulados == true) {
                numero_maximo_dias_mapa_calor_valores = Math.floor(NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO / 2);
            }
            else {
                numero_maximo_dias_mapa_calor_valores = NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO;
            }
            var mostrar_comentarios = ((numero_comentarios > 0) && (comentarios == COMENTARIOS_GRAFICA_TABLA));
            if ((mostrar_comentarios == true) || (dias_mapa_calor_valores.length > numero_maximo_dias_mapa_calor_valores)) {
                id_mapa_calor_valores = "mapa-calor-informacion-energia-" + sufijo_tipo_energia + "-2";
                altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
                oculta_elemento("mapa-calor-informacion-energia-" + sufijo_tipo_energia + "-1");
            }
            else {
                id_mapa_calor_valores = "mapa-calor-informacion-energia-" + sufijo_tipo_energia + "-1";
                if (mostrar_grafica_valores_acumulados == true) {
                    altura_maxima_mapa_calor_valores = Math.floor(ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR / 2);
                }
                else {
                    altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR;
                }
                elimina_elemento("pagina-informe-fichero-informacion-energia-" + sufijo_tipo_energia + "-2");
            };
        }
        else {
            oculta_elemento("mapa-calor-informacion-energia-" + sufijo_tipo_energia + "-1");
            elimina_elemento("pagina-informe-fichero-informacion-energia-" + sufijo_tipo_energia + "-2");
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
            id_parametros_resultado_informe: id_parametros_resultado_informe,
            intervalo_valores: intervalo_valores,
            id_grafica_valores: id_grafica_valores,
            id_grafica_valores_acumulados: id_grafica_valores_acumulados,
            id_descripcion_sensor: id_descripcion_sensor,
            id_texto_informacion_datos: id_texto_informacion_datos,
            id_contenedor_tabla_comentarios: id_contenedor_tabla_comentarios,
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: id_mapa_calor_valores,
            altura_maxima_mapa_calor_valores: altura_maxima_mapa_calor_valores};
        dibuja_informe_sensores_informacion(
            clase_sensor,
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de cortes de tensión
function sensores_informacion_cortes_tension_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_informacion_cortes_tension();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-cortes-tension').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-informacion-cortes-tension-1",
            "pagina-informe-fichero-informacion-cortes-tension-2"]);
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
            $('#mensaje-aviso-informe-fichero-informacion-cortes-tension').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-informacion-cortes-tension-1",
                "pagina-informe-fichero-informacion-cortes-tension-2"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-informacion-cortes-tension-1').html(TLNT.Idiomas._("Cortes de tensión"));
        $('#titulo-informe-fichero-informacion-cortes-tension-2').html(TLNT.Idiomas._("Mapa de calor de cortes de tensión"));

        // Localización del mapa de calor de cortes de tensión
        var numero_comentarios = resultado.numero_comentarios;
        var dias_mapa_calor_cortes_tension = resultado.dias_mapa_calor_cortes_tension;
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var id_mapa_calor_cortes_tension = null;
            var altura_maxima_mapa_calor_cortes_tension = null;
            var mostrar_comentarios = ((numero_comentarios > 0) && (comentarios == COMENTARIOS_GRAFICA_TABLA));
            var numero_maximo_dias_mapa_calor_cortes_tension = Math.floor(NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO / 2);
            if ((mostrar_comentarios == true) || (dias_mapa_calor_cortes_tension.length > numero_maximo_dias_mapa_calor_cortes_tension)) {
                id_mapa_calor_cortes_tension = "mapa-calor-informacion-cortes-tension-cortes-2";
                altura_maxima_mapa_calor_cortes_tension = Math.floor(ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO / 2);
                oculta_elemento("mapa-calor-informacion-cortes-tension-cortes-1");
            }
            else {
                id_mapa_calor_cortes_tension = "mapa-calor-informacion-cortes-tension-cortes-1";
                altura_maxima_mapa_calor_cortes_tension = ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR;
                elimina_elemento("pagina-informe-fichero-informacion-cortes-tension-2");
            };
        }
        else {
            oculta_elemento("mapa-calor-informacion-cortes-tension-cortes-1");
            elimina_elemento("pagina-informe-fichero-informacion-cortes-tension-2");
        }

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
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-cortes-tension",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_cortes_tension: id_mapa_calor_cortes_tension,
            altura_maxima_mapa_calor_cortes_tension: altura_maxima_mapa_calor_cortes_tension};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_CORTES_TENSION,
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de información de compra de energía
function sensores_informacion_compra_energia_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_informacion_compra_energia();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-compra-energia').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-informacion-compra-energia-1",
            "pagina-informe-fichero-informacion-compra-energia-2"]);
        return;
    }

    // Parámetros del informe
    var id_sensor = parametros_informe["id_sensor"];
    var nombre_sensor = parametros_informe["nombre_sensor"];
    var campo = parametros_informe["campo"];
    var intervalo_valores = parametros_informe["intervalo_valores"];
    var tipo_mapa_calor = parametros_informe["tipo_mapa_calor"];
    var comentarios = parametros_informe["comentarios"];
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
    $.post("./src/modulos/ModulosWeb/ModuloSensores/Informacion/dame_informacion_sensor_compra_energia.php", {
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
            $('#mensaje-aviso-informe-fichero-informacion-compra-energia').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-informacion-compra-energia-1",
                "pagina-informe-fichero-informacion-compra-energia-2"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-informacion-compra-energia-1').html(TLNT.Idiomas._("Compra de energía"));
        $('#titulo-informe-fichero-informacion-compra-energia-2').html(TLNT.Idiomas._("Mapa de calor de compra de energía"));

        // Localización del mapa de calor de valores
        var numero_comentarios = resultado.numero_comentarios;
        var dias_mapa_calor_valores = resultado.dias_mapa_calor_valores;
        var mostrar_grafica_valores_acumulados = (resultado.campo_incremental == true);
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var id_mapa_calor_valores = null;
            var altura_maxima_mapa_calor_valores = null;
            var numero_maximo_dias_mapa_calor_valores = null;
            var mostrar_comentarios = ((numero_comentarios > 0) && (comentarios == COMENTARIOS_GRAFICA_TABLA));
            if (mostrar_grafica_valores_acumulados == true) {
                numero_maximo_dias_mapa_calor_valores = Math.floor(NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO / 2);
            }
            else {
                numero_maximo_dias_mapa_calor_valores = NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO;
            }
            if ((mostrar_comentarios == true) || (dias_mapa_calor_valores.length > numero_maximo_dias_mapa_calor_valores)) {
                id_mapa_calor_valores = "mapa-calor-informacion-compra-energia-2";
                altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
                oculta_elemento("mapa-calor-informacion-compra-energia-1");
            }
            else {
                id_mapa_calor_valores = "mapa-calor-informacion-compra-energia-1";
                if (mostrar_grafica_valores_acumulados == true) {
                    altura_maxima_mapa_calor_valores = Math.floor(ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR / 2);
                }
                else {
                    altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR;
                }
                elimina_elemento("pagina-informe-fichero-informacion-compra-energia-2");
            };
        }
        else {
            oculta_elemento("mapa-calor-informacion-compra-energia-1");
            elimina_elemento("pagina-informe-fichero-informacion-compra-energia-2");
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-compra-energia",
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-informacion-compra-energia",
            id_grafica_valores_acumulados: "grafica-informacion-compra-energia-acumulado",
            id_descripcion_sensor: "descripcion-sensor-informacion-compra-energia",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-compra-energia",
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-compra-energia",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: id_mapa_calor_valores,
            altura_maxima_mapa_calor_valores: altura_maxima_mapa_calor_valores};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_COMPRA_ENERGIA,
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de información de gas
function sensores_informacion_gas_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_informacion_gas();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-gas').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-informacion-gas-1",
            "pagina-informe-fichero-informacion-gas-2"]);
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
            $('#mensaje-aviso-informe-fichero-informacion-gas').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-informacion-gas-1",
                "pagina-informe-fichero-informacion-gas-2"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-informacion-gas-1').html(TLNT.Idiomas._("Gas"));
        $('#titulo-informe-fichero-informacion-gas-2').html(TLNT.Idiomas._("Mapa de calor de gas"));

        // Localización del mapa de calor de valores
        var numero_comentarios = resultado.numero_comentarios;
        var dias_mapa_calor_valores = resultado.dias_mapa_calor_valores;
        var mostrar_grafica_valores_acumulados = (resultado.campo_incremental == true);
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var id_mapa_calor_valores = null;
            var altura_maxima_mapa_calor_valores = null;
            var numero_maximo_dias_mapa_calor_valores = null;
            var mostrar_comentarios = ((numero_comentarios > 0) && (comentarios == COMENTARIOS_GRAFICA_TABLA));
            if (mostrar_grafica_valores_acumulados == true) {
                numero_maximo_dias_mapa_calor_valores = Math.floor(NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO / 2);
            }
            else {
                numero_maximo_dias_mapa_calor_valores = NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO;
            }
            if ((mostrar_comentarios == true) || (dias_mapa_calor_valores.length > numero_maximo_dias_mapa_calor_valores)) {
                id_mapa_calor_valores = "mapa-calor-informacion-gas-2";
                altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
                oculta_elemento("mapa-calor-informacion-gas-1");
            }
            else {
                id_mapa_calor_valores = "mapa-calor-informacion-gas-1";
                if (mostrar_grafica_valores_acumulados == true) {
                    altura_maxima_mapa_calor_valores = Math.floor(ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR / 2);
                }
                else {
                    altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR;
                }
                elimina_elemento("pagina-informe-fichero-informacion-gas-2");
            };
        }
        else {
            oculta_elemento("mapa-calor-informacion-gas-1");
            elimina_elemento("pagina-informe-fichero-informacion-gas-2");
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-gas",
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-informacion-gas",
            id_grafica_valores_acumulados: "grafica-informacion-gas-acumulado",
            id_descripcion_sensor: "descripcion-sensor-informacion-gas",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-gas",
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-gas",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: id_mapa_calor_valores,
            altura_maxima_mapa_calor_valores: altura_maxima_mapa_calor_valores};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_GAS,
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de información de agua
function sensores_informacion_agua_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_informacion_agua();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-agua').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-informacion-agua-1",
            "pagina-informe-fichero-informacion-agua-2"]);
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
            $('#mensaje-aviso-informe-fichero-informacion-agua').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-informacion-agua-1",
                "pagina-informe-fichero-informacion-agua-2"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-informacion-agua-1').html(TLNT.Idiomas._("Agua"));
        $('#titulo-informe-fichero-informacion-agua-2').html(TLNT.Idiomas._("Mapa de calor de agua"));

        // Localización del mapa de calor de valores
        var numero_comentarios = resultado.numero_comentarios;
        var dias_mapa_calor_valores = resultado.dias_mapa_calor_valores;
        var mostrar_grafica_valores_acumulados = (resultado.campo_incremental == true);
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var id_mapa_calor_valores = null;
            var altura_maxima_mapa_calor_valores = null;
            var numero_maximo_dias_mapa_calor_valores = null;
            var mostrar_comentarios = ((numero_comentarios > 0) && (comentarios == COMENTARIOS_GRAFICA_TABLA));
            if (mostrar_grafica_valores_acumulados == true) {
                numero_maximo_dias_mapa_calor_valores = Math.floor(NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO / 2);
            }
            else {
                numero_maximo_dias_mapa_calor_valores = NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO;
            }
            if ((mostrar_comentarios == true) || (dias_mapa_calor_valores.length > numero_maximo_dias_mapa_calor_valores)) {
                id_mapa_calor_valores = "mapa-calor-informacion-agua-2";
                altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
                oculta_elemento("mapa-calor-informacion-gas-1");
            }
            else {
                id_mapa_calor_valores = "mapa-calor-informacion-agua-1";
                if (mostrar_grafica_valores_acumulados == true) {
                    altura_maxima_mapa_calor_valores = Math.floor(ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR / 2);
                }
                else {
                    altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR;
                }
                elimina_elemento("pagina-informe-fichero-informacion-agua-2");
            };
        }
        else {
            oculta_elemento("mapa-calor-informacion-agua-1");
            elimina_elemento("pagina-informe-fichero-informacion-agua-2");
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-agua",
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-informacion-agua",
            id_grafica_valores_acumulados: "grafica-informacion-agua-acumulado",
            id_descripcion_sensor: "descripcion-sensor-informacion-agua",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-agua",
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-agua",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: id_mapa_calor_valores,
            altura_maxima_mapa_calor_valores: altura_maxima_mapa_calor_valores};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_AGUA,
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


// Genera el informe fichero de información genérica
function sensores_informacion_generica_ver_informe_fichero() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_fichero_sensores_informacion_generica();
    var error_parametros = parametros_informe["error_parametros"];
    var descripcion_error_parametros = parametros_informe["descripcion_error_parametros"];

    // Si hay error en el informe se muestra un mensaje de aviso y se eliminan las páginas del informe
    if (error_parametros == true) {
        $('#mensaje-aviso-informe-fichero-informacion-generica').html(
            "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Parámetros del informe incorrectos") + " (" + descripcion_error_parametros.toLowerCase() + ")");
        elimina_elementos([
            "pagina-informe-fichero-informacion-generica-1",
            "pagina-informe-fichero-informacion-generica-2"]);
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
            $('#mensaje-aviso-informe-fichero-informacion-generica').html(
                "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("No se ha podido generar el informe") + " (" + descripcion_error_informe.toLowerCase() + ")");
            elimina_elementos([
                "pagina-informe-fichero-informacion-generica-1",
                "pagina-informe-fichero-informacion-generica-2"]);
            return;
        }

        // Títulos de la páginas
        $('#titulo-informe-fichero-informacion-generica-1').html(TLNT.Idiomas._("Datos genéricos"));
        $('#titulo-informe-fichero-informacion-generica-2').html(TLNT.Idiomas._("Mapa de calor de datos genéricos"));

        // Localización del mapa de calor de valores
        var numero_comentarios = resultado.numero_comentarios;
        var dias_mapa_calor_valores = resultado.dias_mapa_calor_valores;
        var mostrar_grafica_valores_acumulados = (resultado.campo_incremental == true);
        if (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) {
            var id_mapa_calor_valores = null;
            var altura_maxima_mapa_calor_valores = null;
            var numero_maximo_dias_mapa_calor_valores = null;
            var mostrar_comentarios = ((numero_comentarios > 0) && (comentarios == COMENTARIOS_GRAFICA_TABLA));
            if (mostrar_grafica_valores_acumulados == true) {
                numero_maximo_dias_mapa_calor_valores = Math.floor(NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO / 2);
            }
            else {
                numero_maximo_dias_mapa_calor_valores = NUMERO_MAXIMO_DIAS_MAPA_CALOR_SENSORES_INFORMACION_INFORMES_FICHERO;
            }
            if ((mostrar_comentarios == true) || (dias_mapa_calor_valores.length > numero_maximo_dias_mapa_calor_valores)) {
                id_mapa_calor_valores = "mapa-calor-informacion-generica-2";
                altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_PAGINA_INFORME_FICHERO;
                oculta_elemento("mapa-calor-informacion-generica-1");
            }
            else {
                id_mapa_calor_valores = "mapa-calor-informacion-generica-1";
                if (mostrar_grafica_valores_acumulados == true) {
                    altura_maxima_mapa_calor_valores = Math.floor(ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR / 2);
                }
                else {
                    altura_maxima_mapa_calor_valores = ALTURA_MAXIMA_MAPA_CALOR_INFORMACION_SENSOR;
                }
                elimina_elemento("pagina-informe-fichero-informacion-generica-2");
            };
        }
        else {
            oculta_elemento("mapa-calor-informacion-generica-1");
            elimina_elemento("pagina-informe-fichero-informacion-generica-2");
        }

        // Fechas de inicio y fin de consulta
        var fecha_hora_inicio_consulta = dame_fecha_hora(fecha_inicio, hora_inicio);
        var fecha_hora_fin_consulta = dame_fecha_hora(fecha_fin, hora_fin);

        // Se dibuja el informe
        var parametros = {
            fecha_hora_inicio_consulta: fecha_hora_inicio_consulta,
            fecha_hora_fin_consulta: fecha_hora_fin_consulta,
            id_parametros_resultado_informe: "parametros-resultado-informe-informacion-generica",
            intervalo_valores: intervalo_valores,
            id_grafica_valores: "grafica-informacion-generica",
            id_grafica_valores_acumulados: "grafica-informacion-generica-acumulado",
            id_descripcion_sensor: "descripcion-sensor-informacion-generica",
            id_texto_informacion_datos: "texto-informacion-datos-informacion-generica",
            id_contenedor_tabla_comentarios: "contenedor-tabla-comentarios-informacion-generica",
            tipo_mapa_calor: tipo_mapa_calor,
            id_mapa_calor_valores: id_mapa_calor_valores,
            altura_maxima_mapa_calor_valores: altura_maxima_mapa_calor_valores};
        dibuja_informe_sensores_informacion(
            CLASE_SENSOR_GENERICA,
            parametros,
            resultado,
            null,
            TIPO_INFORME_FICHERO);
    });
}


//
// Funciones de recuperación de parámetros de informes fichero
//


// Devuelve los parámetros del informe fichero de información de temperatura de un sensor
function dame_parametros_informe_fichero_sensores_informacion_temperatura() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_informacion_temperatura").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_informacion_temperatura").text();

    // Campo y parámetros extra
    var campo = $('#campo_sensores_informe_fichero_informacion_temperatura').text();
    var parametros_extra_campo = $('#parametros_extra_campo_sensores_informe_fichero_informacion_temperatura').text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_informacion_temperatura").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_informacion_temperatura").text();
    var comentarios = $("#comentarios_sensores_informe_fichero_informacion_temperatura").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_informacion_temperatura").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_informacion_temperatura").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_informacion_temperatura").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

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

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_informacion_temperatura").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_informacion_temperatura").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_informacion_temperatura").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_informacion_temperatura").text();
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


// Devuelve los parámetros del informe fichero de información de humedad de un sensor
function dame_parametros_informe_fichero_sensores_informacion_humedad() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_informacion_humedad").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_informacion_humedad").text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_informacion_humedad").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_informacion_humedad").text();
    var comentarios = $("#comentarios_sensores_informe_fichero_informacion_humedad").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_informacion_humedad").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_informacion_humedad").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_informacion_humedad").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

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

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_informacion_humedad").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_informacion_humedad").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_informacion_humedad").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_informacion_humedad").text();
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


// Devuelve los parámetros del informe fichero de información de luz interior de un sensor
function dame_parametros_informe_fichero_sensores_informacion_luz_interior() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_informacion_luz_interior").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_informacion_luz_interior").text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_informacion_luz_interior").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_informacion_luz_interior").text();
    var comentarios = $("#comentarios_sensores_informe_fichero_informacion_luz_interior").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_informacion_luz_interior").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_informacion_luz_interior").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_informacion_luz_interior").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

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

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_informacion_luz_interior").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_informacion_luz_interior").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_informacion_luz_interior").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_informacion_luz_interior").text();
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


// Devuelve los parámetros del informe fichero de información de viento de un sensor
function dame_parametros_informe_fichero_sensores_informacion_viento() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_informacion_viento").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_informacion_viento").text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_informacion_viento").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_informacion_viento").text();
    var comentarios = $("#comentarios_sensores_informe_fichero_informacion_viento").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_informacion_viento").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_informacion_viento").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_informacion_viento").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

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

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_informacion_viento").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_informacion_viento").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_informacion_viento").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_informacion_viento").text();
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


// Devuelve los parámetros del informe fichero de información de energía de un sensor
function dame_parametros_informe_fichero_sensores_informacion_energia(tipo_energia) {
    // Ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();

    // Campo
    var campo = $("#campo_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();
    var comentarios = $("#comentarios_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Se recuperan las fechas
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

    // Se comprueban los parámetros
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_informacion_energia_" + tipo_energia).text();
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


// Devuelve los parámetros del informe fichero de información de cortes de tensión de un sensor
function dame_parametros_informe_fichero_sensores_informacion_cortes_tension() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_informacion_cortes_tension").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_informacion_cortes_tension").text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_informacion_cortes_tension").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_informacion_cortes_tension").text();
    var comentarios = $("#comentarios_sensores_informe_fichero_informacion_cortes_tension").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_informacion_cortes_tension").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_informacion_cortes_tension").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_informacion_cortes_tension").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

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

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_informacion_cortes_tension").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_informacion_cortes_tension").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_informacion_cortes_tension").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_informacion_cortes_tension").text();
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


// Devuelve los parámetros del informe fichero de información de compra de energía de un sensor
function dame_parametros_informe_fichero_sensores_informacion_compra_energia() {
    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_informacion_compra_energia").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_informacion_compra_energia").text();

    // Campo
    var campo = $("#campo_sensores_informe_fichero_informacion_compra_energia").text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_informacion_compra_energia").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_informacion_compra_energia").text();
    var comentarios = $("#comentarios_sensores_informe_fichero_informacion_compra_energia").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_informacion_compra_energia").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_informacion_compra_energia").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_informacion_compra_energia").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["campo"] = campo;
    parametros_informe["intervalo_valores"] = intervalo_valores;
    parametros_informe["tipo_mapa_calor"] = tipo_mapa_calor;
    parametros_informe["comentarios"] = comentarios;
    parametros_informe["horario_semanal"] = horario_semanal;
    parametros_informe["exclusion_fechas"] = exclusion_fechas;
    parametros_informe["inclusion_fechas"] = inclusion_fechas;

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_informacion_compra_energia").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_informacion_compra_energia").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_informacion_compra_energia").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_informacion_compra_energia").text();
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


// Devuelve los parámetros del informe fichero de información de gas de un sensor
function dame_parametros_informe_fichero_sensores_informacion_gas() {
    // Ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_informacion_gas").text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_informacion_gas").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_informacion_gas").text();

    // Campo
    var campo = $("#campo_sensores_informe_fichero_informacion_gas").text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_informacion_gas").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_informacion_gas").text();
    var comentarios = $("#comentarios_sensores_informe_fichero_informacion_gas").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_informacion_gas").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_informacion_gas").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_informacion_gas").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

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

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_informacion_gas").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_informacion_gas").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_informacion_gas").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_informacion_gas").text();
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


// Devuelve los parámetros del informe fichero de información de agua de un sensor
function dame_parametros_informe_fichero_sensores_informacion_agua() {
    // Ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_informacion_agua").text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_informacion_agua").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_informacion_agua").text();

    // Campo
    var campo = $("#campo_sensores_informe_fichero_informacion_agua").text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_informacion_agua").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_informacion_agua").text();
    var comentarios = $("#comentarios_sensores_informe_fichero_informacion_agua").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_informacion_agua").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_informacion_agua").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_informacion_agua").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

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

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_informacion_agua").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_informacion_agua").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_informacion_agua").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_informacion_agua").text();
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


// Devuelve los parámetros del informe fichero de información genérica de un sensor
function dame_parametros_informe_fichero_sensores_informacion_generica() {
    // Ratio
    var id_ratio = $("#id_ratio_sensores_informe_fichero_informacion_generica").text();

    // Identificador y nombre de sensor
    var id_sensor = $("#id_sensor_sensores_informe_fichero_informacion_generica").text();
    var nombre_sensor = $("#nombre_sensor_sensores_informe_fichero_informacion_generica").text();

    // Campo
    var campo = $("#campo_sensores_informe_fichero_informacion_generica").text();

    // Intervalo de valores, tipo de mapa de calor y comentarios
    var intervalo_valores = $("#intervalo_valores_sensores_informe_fichero_informacion_generica").text();
    var tipo_mapa_calor = $("#tipo_mapa_calor_sensores_informe_fichero_informacion_generica").text();
    var comentarios = $("#comentarios_sensores_informe_fichero_informacion_generica").text();

    // Horario semanal, exclusión e inclusión de fechas
    var cadena_horario_semanal = $("#horario_semanal_sensores_informe_fichero_informacion_generica").text();
    var cadena_exclusion_fechas = $("#exclusion_fechas_sensores_informe_fichero_informacion_generica").text();
    var cadena_inclusion_fechas = $("#inclusion_fechas_sensores_informe_fichero_informacion_generica").text();
    var horario_semanal = dame_horario_semanal(cadena_horario_semanal);
    var exclusion_fechas = dame_fechas(cadena_exclusion_fechas);
    var inclusion_fechas = dame_fechas(cadena_inclusion_fechas);

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

    // Se recuperan las fechas
    var error_parametros = false;
    var descripcion_error_parametros = "";
    if (error_parametros == false) {
        // Fechas de inicio y fin
        var fecha_inicio = $("#fecha_inicio_sensores_informe_fichero_informacion_generica").text();
        var hora_inicio = $("#hora_inicio_sensores_informe_fichero_informacion_generica").text();
        var fecha_fin = $("#fecha_fin_sensores_informe_fichero_informacion_generica").text();
        var hora_fin = $("#hora_fin_sensores_informe_fichero_informacion_generica").text();
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
