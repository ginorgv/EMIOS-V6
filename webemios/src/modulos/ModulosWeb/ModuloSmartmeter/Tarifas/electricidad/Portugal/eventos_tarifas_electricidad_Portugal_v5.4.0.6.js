//
// Eventos de tarifas (electricidad - España)
//



function boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Portugal(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_tarifa_electrica = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/muestra_ventana_anyadir_modificar_tarifa_electricidad_Portugal.php", {
		id_tarifa_electrica: id_tarifa_electrica,
        tipo_operacion_administracion: tipo_operacion_administracion
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




function boton_smartmeter_mostrar_ventana_modificar_tarifas_electricidad_Portugal() {
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/muestra_ventana_modificar_tarifas_electricidad_Portugal.php", {},
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

function boton_smartmeter_anyadir_modificar_tarifa_electricidad_Portugal() {

    // Recuperamos todos los valores de la ventana
    var nombre_tarifa_electrica = $('#nombre_tarifa_electrica').val();
    var descripcion = $('#descripcion_tarifa_electrica').val();
    var tipo = $('#tipo_tarifa_electrica').val();
    var ciclo = $('#ciclo_tarifa_electrica').val();
    var region = $('#region_tarifa_electrica').val();
    var id_grupo = $('#id_grupo_tarifa').val();
    var expiracion = $('#expiracion_tarifa').val();
    var fecha_expiracion = $("#fecha_expiracion_tarifa").val();
    var numero_dias_preaviso_expiracion = $("#numero_dias_preaviso_expiracion_tarifa").val();

    var precio_consumo_ponta = $("#precio_consumo_tramo_tarifa_electrica_ponta").val();
    var precio_consumo_cheia = $("#precio_consumo_tramo_tarifa_electrica_cheia").val();
    var precio_consumo_vazio_normal = $("#precio_consumo_tramo_tarifa_electrica_vazio_normal").val();
    var precio_consumo_super_vazio = $("#precio_consumo_tramo_tarifa_electrica_super_vazio").val();

    var precio_acceso_ponta = $("#precio_acceso_tramo_tarifa_electrica_ponta").val();
    var precio_acceso_cheia = $("#precio_acceso_tramo_tarifa_electrica_cheia").val();
    var precio_acceso_vazio_normal = $("#precio_acceso_tramo_tarifa_electrica_vazio_normal").val();
    var precio_acceso_super_vazio = $("#precio_acceso_tramo_tarifa_electrica_super_vazio").val();

    var precio_potencia_contratada = $("#precio_potencia_contratada_tarifa_electrica").val();
    var potencia_contratada = $("#potencia_contratada_tarifa_electrica").val();
    var precio_potencia_ponta = $("#precio_potencia_ponta_tarifa_electrica").val();

    var energia_reactiva_inductiva = $("#precio_energia_reactiva_inductiva_tarifa_electrica").val();
    var energia_reactiva_capacitiva = $("#precio_energia_reactiva_capacitiva_tarifa_electrica").val();

    var impuesto_electrico = $('#impuesto_electrico_tarifa_electrica').val();
    var iva = $('#iva_tarifa_electrica').val();
    var contribucion_audiovisual = $('#contribucion_audiovisual_tarifa_electrica').val();
    var iva_reducido = $('#iva_reducido_tarifa_electrica').val();

    // Parámetros de la ventana
    var anyadir_tarifa_electrica = $("#parametros_ventana_anyadir_modificar_tarifa_electrica").attr("anyadir_tarifa_electrica");
		var id_tarifa_electrica = $("#parametros_ventana_anyadir_modificar_tarifa_electrica").attr("id_tarifa_electrica");

    // Comprobaciones

    // Comprobación de tipo seleccionado
    if (tipo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo seleccionado'));
        return;
    }

    // Comprobación del ciclo seleccionado
    if (ciclo == CICLO_TARIFA_ELECTRICA_PORTUGAL_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay ciclo seleccionado'));
        return;
    }

    // Comprobación de la región seleccionada
    if (region == REGION_TARIFA_ELECTRICA_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay región seleccionada'));
        return;
    }

    // Comprobación de expiración seleccionada
    if (expiracion == EXPIRACION_TARIFA_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay expiración seleccionada'));
        return;
    }

    // Nombre y descripción
    if (comprueba_longitud_cadena(nombre_tarifa_electrica, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_tarifa_electrica").addClass('data-check-failed');
        return;
    }
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_tarifa_electrica").addClass('data-check-failed');
        return;
    }

    // Fecha y número de días de preaviso de expiración
    if (expiracion == EXPIRACION_TARIFA_SI) {
        if (parseInt(numero_dias_preaviso_expiracion) < 0) {
            var descripcion_error = TLNT.Idiomas._('El número de días de preaviso de expiración debe ser mayor que 0');
            jAlert(descripcion_error);
            return;
        }
    }
    var ids_pestanyas_visibles = ["tab-principal"];
    if ($("#titulo-tab-precios_consumo").css("display") != "none") {
        ids_pestanyas_visibles.push("titulo-tab-precios_consumo");
    }
    if ($("#titulo-tab-precios-consumo-tarifa-acceso-tramos").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-precios-consumo-tarifa-acceso-tramos");
    }
    if ($("#titulo-tab-precios-potencias-tramos").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-precios-potencias-tramos");
    }
    if ($("#titulo-tab-energia-reactiva").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-energia-reactiva");
    }
    if ($("#titulo-tab-factura").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-factura");
    }
    var datos_correctos = true;
    for (var i = 0; i < ids_pestanyas_visibles.length; i++) {
        if (TLNT.Check.inputs(ids_pestanyas_visibles[i])) {
            datos_correctos = false;
        }
    }
    if (datos_correctos == false) {
        jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
        return;
    }

    // Se añade o modifica la tarifa eléctrica
    if (anyadir_tarifa_electrica == true) {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/anyade_tarifa_electricidad_Portugal.php", {
            nombre_tarifa_electrica : nombre_tarifa_electrica,
            descripcion : descripcion,
            tipo : tipo,
            ciclo : ciclo,
            region : region,
            id_grupo : id_grupo,
            expiracion : expiracion,
            fecha_expiracion : fecha_expiracion,
            numero_dias_preaviso_expiracion : numero_dias_preaviso_expiracion,
            precio_consumo_ponta : precio_consumo_ponta,
            precio_consumo_cheia : precio_consumo_cheia,
            precio_consumo_vazio_normal : precio_consumo_vazio_normal,
            precio_consumo_super_vazio : precio_consumo_super_vazio,
            precio_acceso_ponta : precio_acceso_ponta,
            precio_acceso_cheia : precio_acceso_cheia,
            precio_acceso_vazio_normal : precio_acceso_vazio_normal,
            precio_acceso_super_vazio : precio_acceso_super_vazio,
            potencia_contratada : potencia_contratada,
            precio_potencia_contratada : precio_potencia_contratada,
            precio_potencia_ponta : precio_potencia_ponta,
            energia_reactiva_inductiva  : energia_reactiva_inductiva,
            energia_reactiva_capacitiva : energia_reactiva_capacitiva,
            impuesto_electrico : impuesto_electrico,
            iva : iva,
            contribucion_audiovisual : contribucion_audiovisual,
            iva_reducido : iva_reducido,
            id_tarifa_electrica_anterior : id_tarifa_electrica
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_tarifas_electricidad_Portugal();
            actualiza_tabla_grupos_tarifas();
        });
    }
    else {
				$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/modifica_tarifa_electricidad_Portugal.php", {
						nombre_tarifa_electrica : nombre_tarifa_electrica,
						descripcion : descripcion,
						tipo : tipo,
						ciclo : ciclo,
						region : region,
						id_grupo : id_grupo,
						expiracion : expiracion,
						fecha_expiracion : fecha_expiracion,
						numero_dias_preaviso_expiracion : numero_dias_preaviso_expiracion,
						precio_consumo_ponta : precio_consumo_ponta,
						precio_consumo_cheia : precio_consumo_cheia,
						precio_consumo_vazio_normal : precio_consumo_vazio_normal,
						precio_consumo_super_vazio : precio_consumo_super_vazio,
						precio_acceso_ponta : precio_acceso_ponta,
						precio_acceso_cheia : precio_acceso_cheia,
						precio_acceso_vazio_normal : precio_acceso_vazio_normal,
						precio_acceso_super_vazio : precio_acceso_super_vazio,
						potencia_contratada : potencia_contratada,
						precio_potencia_contratada : precio_potencia_contratada,
						precio_potencia_ponta : precio_potencia_ponta,
						energia_reactiva_inductiva  : energia_reactiva_inductiva,
						energia_reactiva_capacitiva : energia_reactiva_capacitiva,
						impuesto_electrico : impuesto_electrico,
						iva : iva,
						contribucion_audiovisual : contribucion_audiovisual,
						iva_reducido : iva_reducido,
						id_tarifa_electrica : id_tarifa_electrica
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_tarifa_detalles(id_tarifa_electrica);
            actualiza_tabla_grupos_tarifas();
            $('#ventana_modal').modal('hide');
        });
    }
}

function actualiza_tabla_tarifas_electricidad_Portugal() {
    var filtro = $('#filtro_smartmeter_filtro_tarifas_tabla_electricidad_Portugal').val();
    var tipo = $('#tipo_tarifa_electrica_smartmeter_filtro_tarifas_tabla_electricidad_Portugal').val();
    var ciclo = $('#ciclo_tarifa_electrica_smartmeter_filtro_tarifas_tabla_electricidad_Portugal').val();
    var id_grupo = $('#id_grupo_tarifas_electricas_smartmeter_filtro_tarifas_tabla_electricidad_Portugal').val();
    var estado = $('#estado_tarifa_smartmeter_filtro_tarifas_tabla_electricidad_Portugal').val();

    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/dame_tabla_tarifas_electricidad_Portugal.php", {
        filtro: filtro,
        tipo: tipo,
        ciclo: ciclo,
        id_grupo: id_grupo,
        estado: estado
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaTarifasElectricas').html(resultado.html);

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
    });
}



function boton_smartmeter_actualizar_tabla_tarifas_electricidad_Portugal() {
    actualiza_tabla_tarifas_electricidad_Portugal();
}


function boton_smartmeter_filtro_tarifas_tabla_electricidad_Portugal() {
    boton_smartmeter_actualizar_tabla_tarifas_electricidad_Portugal();
}

function boton_smartmeter_eliminar_tarifa_electricidad_Portugal(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_tarifa_electrica = params[1];
    var nombre_tarifa_electrica = $(this).attr('nombre_tarifa_electrica');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la tarifa?") + "\n(" + escapeHtml(nombre_tarifa_electrica) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/elimina_tarifa_electricidad_Portugal.php", {
				id_tarifa_electrica: id_tarifa_electrica
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
								actualiza_tabla_tarifas_electricidad_Portugal();
                actualiza_tabla_grupos_tarifas();
			});
		}
	});
}
