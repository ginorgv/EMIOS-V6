//
// Funciones de informes personalizados (de SmartMeter)
//


// Genera y muestra el informe de estudio general
function boton_smartmeter_estudio_general_ver_informe() {
    // Se recuperan los parámetros del informe
    var parametros_informe = dame_parametros_informe_smartmeter_estudio_general(false, false);
    if (parametros_informe == null) {
        return;
    }

    // Selección de medición y país
    switch (medicion) {
        case MEDICION_ELECTRICIDAD: {
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    boton_smartmeter_estudio_general_ver_informe_electricidad_Espanya(parametros_informe);
                    break;
                }
            }
            break;
        }
        case MEDICION_GAS: {
            switch (pais_tarifas_gas) {
                case PAIS_ESPANYA: {
                    boton_smartmeter_estudio_general_ver_informe_gas_Espanya(parametros_informe);
                    break;
                }
            }
            break;
        }
        case MEDICION_AGUA: {
            switch (pais_tarifas_agua) {
                case PAIS_ESPANYA: {
                    boton_smartmeter_estudio_general_ver_informe_agua_Espanya(parametros_informe);
                    break;
                }
            }
            break;
        }
    }
}


//
// Funciones de recuperación de parámetros de informes
//


// Devuelve los parámetros del informe de estudio general
function dame_parametros_informe_smartmeter_estudio_general(informe_fichero, informe_automatico) {
    // Se quitan las alertas
    TLNT.Check.clear_alerts();

    // Ratio (si lo hay)
    var id_ratio = dame_id_ratio_seleccionado();

    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_smartmeter_estudio_general').val();
    if (id_sensor == ID_NINGUNO) {
        jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return (null);
    }
    var nombre_sensor = $('#id_sensor_smartmeter_estudio_general :selected').text();

    // Apartados
    var apartados = [];
    $("#ids_apartados_smartmeter_estudio_general option").each(function () {
        if (typeof ($(this).attr("selected")) !== "undefined") {
            apartados.push($(this).val());
        }
    });
    if (apartados.length == 0) {
        jAlert(TLNT.Idiomas._("Seleccione al menos un apartado"));
        return (null);
    }

    // Texto de introducción
    var texto_introduccion = $("#texto-introduccion-estudio-general").val();
    if (comprueba_longitud_cadena(texto_introduccion, NUMERO_MAXIMO_CARACTERES_TEXTO) == false) {
        $("#texto-introduccion-estudio-general").addClass('data-check-failed');
        return (null);
    }
    if ((apartados.indexOf(APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_ELECTRICIDAD_ESPANYA) > -1) && (texto_introduccion == "")) {
        jAlert(TLNT.Idiomas._("Rellene la introducción"));
        return (null);
    }

    // Parámetros recuperados
    var parametros_informe = {};
    parametros_informe["id_ratio"] = id_ratio;
    parametros_informe["id_sensor"] = id_sensor;
    parametros_informe["nombre_sensor"] = nombre_sensor;
    parametros_informe["apartados"] = apartados;
    parametros_informe["texto_introduccion"] = texto_introduccion;

    // Se recuperan las fechas si no es informe automático
    if (informe_automatico == false) {
        var fecha_inicio = $('#fecha_inicio_smartmeter_estudio_general').val();
        var fecha_fin = $('#fecha_fin_smartmeter_estudio_general').val();
        var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, hora_fin);
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
    }

    // Conversión de parámetros de tipo cadena a json (si es informe fichero o informe automático)
    if ((informe_fichero == true) || (informe_automatico == true)) {
        var textos_estudio_general = {
            "texto_introduccion": parametros_informe["texto_introduccion"]
        };
        delete parametros_informe["texto_introduccion"];

        // Se recuperan los textos del contenido del informe si es informe fichero
        if (informe_fichero == true) {
            // Selección de medición y país
            switch (medicion) {
                case MEDICION_ELECTRICIDAD: {
                    switch (pais_tarifas_electricas) {
                        case PAIS_ESPANYA: {
                            var resultado_adicion_textos = anyade_textos_informe_estudio_general_electricidad_Espanya(apartados, textos_estudio_general);
                            if (resultado_adicion_textos == false) {
                                return (null);
                            }
                            break;
                        }
                    }
                    break;
                }
                case MEDICION_GAS: {
                    switch (pais_tarifas_gas) {
                        case PAIS_ESPANYA: {
                            var resultado_adicion_textos = anyade_textos_informe_estudio_general_gas_Espanya(apartados, textos_estudio_general);
                            if (resultado_adicion_textos == false) {
                                return (null);
                            }
                            break;
                        }
                    }
                    break;
                }
                case MEDICION_AGUA: {
                    switch (pais_tarifas_agua) {
                        case PAIS_ESPANYA: {
                            var resultado_adicion_textos = anyade_textos_informe_estudio_general_agua_Espanya(apartados, textos_estudio_general);
                            if (resultado_adicion_textos == false) {
                                return (null);
                            }
                            break;
                        }
                    }
                    break;
                }
            }
        }

        var parametros_tipo_json = JSON.stringify(textos_estudio_general);
        parametros_informe["parametros_tipo_json"] = parametros_tipo_json;
    };

    // Se devuelven los parámetros recuperados
    return (parametros_informe);
}



//
// Funciones de subir ficheros de resultados mensuales (YOIBA)
//

// Subir un fichero 
function boton_smartmeter_resultados_mensuales_subir_fichero() {
    
    // Se recuperan los parámetros del fichero a subir
   
    // Nombre del fichero
    var alias_fichero = $("#nombre_fichero_excel_resultados_mensuales").val();
    
    // Fichero 
    var nombre_fichero = $("#fichero_importacion_valores_sensor_text").val();
    //var control_seleccion_fichero_importacion = $('#fichero_importacion_valores_sensor_file').val();
    var control_seleccion_fichero_importacion = $('#fichero_importacion_valores_sensor_file')[0];
    
    // Sensor
    var id_sensor = $("#id_sensor_smartmeter_resultados_mensuales_subir_fichero").val();

    if (id_sensor == -1) {
        jAlert(TLNT.Idiomas._("Seleccione un sensor"));
        return (null);
    }
    if ((alias_fichero == null) || (alias_fichero == '')) {
        jAlert(TLNT.Idiomas._("Escriba un nombre para el fichero"));
        return (null);
    }
    if ((nombre_fichero == null) || (nombre_fichero == '')) {
        jAlert(TLNT.Idiomas._("Seleccione un fichero"));
        return (null);
    }
    
    var datos_formulario = new FormData();
    datos_formulario.append("fichero_valores", control_seleccion_fichero_importacion.files[0]);
    datos_formulario.append("nombre_fichero", nombre_fichero);
    datos_formulario.append("alias_fichero", alias_fichero);
    datos_formulario.append("sensor_asociado", id_sensor);

    
    // Se envian los datos para subir el fichero
    // Llamada 'ajax' POST
            $.ajax({
                url: "./src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/sube_fichero_excel.php",
                type: "POST",
                data: datos_formulario,
                processData: false,
                contentType: false,
                timeout: SEGUNDOS_TIMEOUT_EJECUCIONES_AJAX_SEGUNDO_PLANO * 1000,
                success: function(result) {
                    var resultado = dame_resultado_ejecucion_script_php_json(result);
                    if (resultado == null) {
                        return;
                    }

                    jInfo(resultado.msg);
                    
                    // Actualizamos la tabla de ficheros
                    actualiza_tabla_ficheros_excel()
                }
            });



}


// Elimina una validación de factura
function boton_smartmeter_eliminar_fichero_excel(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_fichero = params[1];
    var nombre_fichero = $(this).attr('nombre_fichero');
    

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el fichero?") + "\n(" +
        TLNT.Idiomas._("Fichero") + ": " + nombre_fichero + ")",
        TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/elimina_fichero_excel.php", {
                            "id_fichero": id_fichero,
                            "nombre_fichero": nombre_fichero
			},
		function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
        actualiza_tabla_ficheros_excel();
		});
            }
	});
}

// Elimina una validación de factura
function boton_smartmeter_descargar_fichero_excel(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    //var id_fichero = params[1];
    var nombre_fichero = $(this).attr('nombre_fichero');
    
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/descarga_fichero_excel.php", {
            "nombre_fichero": nombre_fichero
	},
	function(data, status) {
	var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }
            jInfo(resultado.msg);

        // Se guardan los ficheros de los valores exportados
        
        var ruta_fichero_valores_exportados = resultado.rutas_ficheros_valores_exportados;
        window.location.href = ruta_fichero_valores_exportados;
	});
    
}

// Funcion a la que llama el boton para actualizar la tabla
function boton_smartmeter_actualizar_tabla_ficheros_excel(event){
    actualiza_tabla_ficheros_excel();
}

// Funcion de actualizar a la que llamamos desde el boton y desde las funciones de borrar e insertar 
function actualiza_tabla_ficheros_excel(){
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/dame_tabla_resultados_mensuales.php", {medicion: medicion},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaFicherosXLS').html(resultado);
        TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();

	});
}