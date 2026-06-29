/*
 * Módulo Red
 *
 */


// Botones de envío de acciones de herramientas del módulo
function boton_red_envia_accion_herramientas_red() {
	$.post("./src/modulos/ModulosWeb/ModuloRed/envia_accion_herramientas_red.php", {
		boton: this.id
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		jInfo(resultado.msg);
	});
}


// Ventana de borrado de valores de la red
function boton_red_mostrar_ventana_borrado_valores_red() {
    $.post("./src/modulos/ModulosWeb/ModuloRed/muestra_ventana_borrado_valores_red.php", {},
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		// Se muestra la ventana modal
        $('#ventana_modal').modal('show');
		TLNT.Navegacion.carga_ventana_modal(
			resultado.titulo,
			resultado.contenido,
			resultado.pie);

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales();
	});
}


// Botones de envío de acciones de herramientas de un dispositivo
function boton_red_envia_accion_herramientas_dispositivo() {
    var params = this.id.split('__');
    var boton = params[0];
    var id_dispositivo = params[1];
    var tipo_accion = params[2];
       // DEMO BYE RADON
       if (boton == "boton_actualiza") {
        
                TLNT.Navegacion.detiene_propagacion_evento(event);


                $.post("./src/lib/modulos/Nodos/administracion/muestra_ventana_anyadir_modificar_version_radon.php", {
	            	tipo_accion: tipo_accion,
                    id_dispositivo: id_dispositivo
	            },
	            function(data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

		        // Se muestra la ventana modal
                $('#ventana_modal').modal('show');
		        TLNT.Navegacion.carga_ventana_modal(
			        resultado.titulo,
			        resultado.contenido,
			        resultado.pie);

	            // Eventos de ventanas modales
	        	TLNT.Navegacion.establece_eventos_ventanas_modales();
	            });
            }

    /*
    else if (boton == "boton_envia_comando") {
        jPrompt(TLNT.Idiomas._("Comando a enviar:"), "", TLNT.Idiomas._("Pregunta"), function(res){
            if (res == true){
                jInfo("Comando enviado");
            }
        });
    }*/
    else if (boton == "boton_test") {
        jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que quiere enviar un test al dispositivo?"), TLNT.Idiomas._("Pregunta"), function(res){
            if (res == true){
                jInfo("Test enviado");
                setTimeout(function(){
                    var nombre_contenedor_control_detalles_dispositivo = '#contenedor_control_datos_test__' + id_dispositivo;
                    $(nombre_contenedor_control_detalles_dispositivo).show();
                },5000)
                
            }
        });
    }
    else if (boton == "boton_reiniciar") {
        jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea reiniciar el dispositivo?"), TLNT.Idiomas._("Pregunta"), function(res) {
            if (res == true) {
                $.post("./src/modulos/ModulosWeb/ModuloRed/envia_accion_herramientas_dispositivo.php", {
                    boton: boton,
                    id_dispositivo: id_dispositivo
                },
                function(data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    jInfo(resultado.msg);
                });
            }
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloRed/envia_accion_herramientas_dispositivo.php", {
            boton: boton,
            id_dispositivo: id_dispositivo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
        });
    }
}


// Botones de envío de acciones de herramientas de un axón
function boton_red_envia_accion_herramientas_axon() {
    var params = this.id.split('__');
    var boton = params[0];
    var id_axon = params[1];

	$.post("./src/modulos/ModulosWeb/ModuloRed/envia_accion_herramientas_axon.php", {
		boton: boton,
		id_axon: id_axon
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		jInfo(resultado.msg);
	});
}


// Actualiza la información de la red y las tablas de dispositivos y axones
function boton_red_actualizar_informacion_red() {
    actualiza_informacion_red();
    actualiza_tabla_nodos(TIPO_NODO_DISPOSITIVO);
    actualiza_tabla_nodos(TIPO_NODO_AXON);
}


// Actualiza la información de la red
function actualiza_informacion_red() {
    $.post("./src/modulos/ModulosWeb/ModuloRed/dame_info_red.php", {},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		$('#tab-red').html(resultado.html);

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_secciones();
        TLNT.Navegacion.establece_eventos_tablas_datos();
	});
}


// Realiza el filtrado de alarmas de la red
function boton_red_filtro_alarmas() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_red_filtro_alarmas').val();
    var hora_inicio = $('#hora_inicio_red_filtro_alarmas').val();
    var fecha_fin = $('#fecha_fin_red_filtro_alarmas').val();
    var hora_fin = $('#hora_fin_red_filtro_alarmas').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/lib/modulos/Alarmas/dame_tabla_alarmas.php", {
        modulo: MODULO_RED,
        filtro: $('#filtro_red_filtro_alarmas').val(),
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#tablaAlarmas").html(resultado.html);
        if (resultado.limite_elementos_tabla_superado == true) {
            jAlert(TLNT.Idiomas._("Número máximo de alarmas superado (se muestran las más recientes)"));
        }

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Realiza el filtrado de acciones de usuario
function boton_red_filtro_acciones_usuario() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_red_filtro_acciones_usuario').val();
    var hora_inicio = $('#hora_inicio_red_filtro_acciones_usuario').val();
    var fecha_fin = $('#fecha_fin_red_filtro_acciones_usuario').val();
    var hora_fin = $('#hora_fin_red_filtro_acciones_usuario').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/lib/modulos/AccionesUsuario/dame_tabla_acciones.php", {
        modulo: MODULO_RED,
        filtro: $('#filtro_red_filtro_acciones_usuario').val(),
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#tablaAccionesUsuario").html(resultado.html);
        if (resultado.limite_elementos_tabla_superado == true) {
            jAlert(TLNT.Idiomas._("Número máximo de acciones superado (se muestran las más recientes)"));
        }

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Realiza la exportacion de acciones de usuario
function boton_red_exportar_acciones_usuario() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_red_filtro_acciones_usuario').val();
    var hora_inicio = $('#hora_inicio_red_filtro_acciones_usuario').val();
    var fecha_fin = $('#fecha_fin_red_filtro_acciones_usuario').val();
    var hora_fin = $('#hora_fin_red_filtro_acciones_usuario').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/lib/modulos/AccionesUsuario/exporta_acciones.php", {
        modulo: MODULO_RED,
        filtro: $('#filtro_red_filtro_acciones_usuario').val(),
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se guarda el fichero de las acciones exportadas
        var ruta_fichero_acciones_exportadas = resultado.ruta_fichero_acciones_exportadas;
        if (ruta_fichero_acciones_exportadas != "") {
            jInfo(resultado.msg);
            window.location.href = ruta_fichero_acciones_exportadas;
        }
        else {
            jAlert(resultado.msg);
        }
	});
}


// Realiza el filtrado de comentarios
function boton_red_filtro_comentarios() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_red_filtro_comentarios').val();
    var hora_inicio = $('#hora_inicio_red_filtro_comentarios').val();
    var fecha_fin = $('#fecha_fin_red_filtro_comentarios').val();
    var hora_fin = $('#hora_fin_red_filtro_comentarios').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    $.post("./src/lib/modulos/Comentarios/dame_tabla_comentarios.php", {
        modulo: MODULO_RED,
        filtro: $('#filtro_red_filtro_comentarios').val(),
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#tablaComentarios").html(resultado.html);
        if (resultado.limite_elementos_tabla_superado == true) {
            jAlert(TLNT.Idiomas._("Número máximo de comentarios superado (se muestran las más recientes)"));
        }

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


// Realiza el filtrado de sensores y actuadores en la topología de red
function boton_red_filtro_topologia_red() {
    boton_red_actualizar_topologia_red();
}


// Actualiza la topología de red
function boton_red_actualizar_topologia_red() {
    // Se recuperan la clase de sensor y actuador
    var clase_sensor = $('#clase_sensor_red_filtro_topologia_red').val();
    var clase_actuador = $('#clase_actuador_red_filtro_topologia_red').val();

    // Se recupera la información de la topología de red
    $.post("./src/modulos/ModulosWeb/ModuloRed/dame_info_topologia_red.php", {
        clase_sensor: clase_sensor,
        clase_actuador: clase_actuador
    },
	function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se borran los datos anteriores
        vacia_elemento("grafico-topologia-red");

        // Nodo de la red
        var nodo_red = JSON.parse(resultado.info);

        // Se dibuja la topología de árbol y se establece el menú contextual
        var id_topologia_arbol = "grafico-topologia-red";
        dibuja_topologia_arbol(
            id_topologia_arbol,
            NUMERO_NIVELES_NODOS_TOPOLOGIA_ARBOL_RED,
            0,
            nodo_red);
        var info_menu_contextual = {
            "tipo_origen": TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_TOPOLOGIA_ARBOL,
            "opciones": [OPCION_MENU_CONTEXTUAL_GUARDAR_IMAGEN]};
        anyade_menu_contextual(id_topologia_arbol, info_menu_contextual, TLNT.Idiomas._("Topología de red"));
	});
}
