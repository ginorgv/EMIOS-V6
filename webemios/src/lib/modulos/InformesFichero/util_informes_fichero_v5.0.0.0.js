// Función que genera el informe y lo muestra en una ventana
function genera_informe_fichero(parametros_informe) {
    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("parametros_informe", JSON.stringify(parametros_informe));

    // Llamada 'ajax' POST
    $.ajax({
        url: "./src/lib/modulos/InformesFichero/genera_informe_fichero.php",
        type: "POST",
        data: datos_formulario,
        processData: false,
        contentType: false,
        timeout: SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX_GENERACION_INFORME_FICHERO * 1000,
        success: function(result) {
            // Se procesa el resultado de la generación del informe
            procesa_resultado_generacion_informe_fichero(parametros_informe, result);
        },
        error: function(request, status, err) {
            if (status == "timeout") {
                error_ajax_capturado = true;

                jInfo(TLNT.Idiomas._("El informe PDF tarda demasiado en generarse") +
                    "\n(" + TLNT.Idiomas._("divida el informe en varios informes más pequeños o utilice informes configurables") + ")");
            }
        }
    });
}


// Función que genera el informe y lo muestra en una ventana (con parámetro de control de selección de fichero)
function genera_informe_fichero_control_seleccion_fichero(
    parametros_informe,
    nombre_parametro_control_seleccion_fichero,
    control_texto_seleccion_fichero) {
    // Fichero seleccionado
    var control_seleccion_fichero = parametros_informe[nombre_parametro_control_seleccion_fichero];
    delete parametros_informe[nombre_parametro_control_seleccion_fichero];

    // Se crean los datos del formulario
    var datos_formulario = new FormData();
    datos_formulario.append("parametros_informe", JSON.stringify(parametros_informe));
    datos_formulario.append("fichero", control_seleccion_fichero.files[0]);

    // Llamada 'ajax' POST
    $.ajax({
        url: "./src/lib/modulos/InformesFichero/genera_informe_fichero.php",
        type: "POST",
        data: datos_formulario,
        processData: false,
        contentType: false,
        timeout: SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX_GENERACION_INFORME_FICHERO * 1000,
        success: function(result) {
            // Se procesa el resultado de la generación del informe
            procesa_resultado_generacion_informe_fichero(parametros_informe, result);
        },
        error: function(request, status, err) {
            if (status == "timeout") {
                error_ajax_capturado = true;

                jInfo(TLNT.Idiomas._("El informe PDF tarda demasiado en generarse") +
                    "\n(" + TLNT.Idiomas._("divida el informe en varios informes más pequeños o utilice informes configurables") + ")");
            }
        }
    });
}


// Procesa el resultado de la generación del informe fichero
function procesa_resultado_generacion_informe_fichero(parametros_informe, resultado_script_php_json) {
    var resultado = dame_resultado_ejecucion_script_php_json(resultado_script_php_json);
    if (resultado == null) {
        return;
    }

    // Se muestra una nueva ventana con el fichero PDF
    var ventana = window.open(
        resultado.enlace_descarga,
        '_blank'
      );
    if (ventana == null) {
        jAlert(TLNT.Idiomas._("No se ha podido mostrar el PDF (asegúrese de que la visualización de ventanas emergentes ('pop-ups') no está bloqueada en el navegador)"));
        return;
    }
}


// Devuelve la escala de reducción del gráfico para ajustarlo a la altura máxima
function dame_escala_reduccion_grafico_altura_maxima(id_grafico, altura_maxima) {
    // 1. Se recuperan la anchura y altura del gráfico
    // 2. Si la altura es mayor que la altura máxima
    // 3. Se devuelve la escala de reducción para redibujar el gráfico correspondiente
    var elemento_svg = $("#" + id_grafico).find('svg');
    var altura_actual = elemento_svg.height();
    var escala_reduccion = null;
    if (altura_actual > altura_maxima) {
        escala_reduccion = altura_maxima / altura_actual;
    }
    else {
        escala_reduccion = 1;
    }
    return (escala_reduccion);
}
