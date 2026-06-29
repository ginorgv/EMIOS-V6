//
// Eventos de tarifas (gas - España)
//


function boton_smartmeter_mostrar_ventana_modificar_tarifas_gas_Espanya() {
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/muestra_ventana_modificar_tarifas_gas_Espanya.php", {},
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


function boton_smartmeter_modificar_tarifas_gas_Espanya() {
    // Tipo de tarifa de gas
    var tipo_tarifa_gas = $('#tipo_tarifa_gas').val();

    // Expiración seleccionada
    var expiracion_tarifa = $('#expiracion_tarifa').val();

    // Valores de listas desplegables a comprobar según las pestañas visibles
    var tipo_alquiler_contador_tarifa_gas = $('#tipo_alquiler_contador_tarifa_gas').val();

    // Se recuperan los identificadores de las tarifas de gas seleccionadas
    var ids_tarifas_gas = [];
    $("#ids_tarifas_gas_tarifa_gas option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_tarifas_gas.push($(this).val());
        }
    });
    if (ids_tarifas_gas.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos una tarifa"));
        return;
	}

    // Comprobación de datos correctos de las pestañas visibles
    var ids_pestanyas_visibles = ["tab-principal"];
    if ($("#titulo-tab-parametros").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-parametros");
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

    // Fecha y número de días de preaviso de expiración
    var fecha_expiracion_tarifa = $("#fecha_expiracion_tarifa").val();
    var numero_dias_preaviso_expiracion_tarifa = $("#numero_dias_preaviso_expiracion_tarifa").val();
    if (expiracion_tarifa == EXPIRACION_TARIFA_SI) {
        if (parseInt(numero_dias_preaviso_expiracion_tarifa) < 0) {
            var descripcion_error = TLNT.Idiomas._('El número de días de preaviso de expiración debe ser mayor que 0');
            jAlert(descripcion_error);
            return;
        }
    }

    // Parámetros de tarifa de gas
    var factor_conversion_tarifa_gas = $('#factor_conversion_tarifa_gas').val();
    var precio_consumo_tarifa_gas = $('#precio_consumo_tarifa_gas').val();
    var precio_caudal_diario_tarifa_gas = $('#precio_caudal_diario_tarifa_gas').val();
    var caudal_diario_tarifa_gas = $('#caudal_diario_tarifa_gas').val();
    var precio_termino_fijo_diario_tarifa_gas = $('#precio_termino_fijo_diario_tarifa_gas').val();

    // Parámetros de factura de gas
    var impuesto_gas_tarifa_gas = $('#impuesto_gas_tarifa_gas').val();
    var alquiler_contador_tarifa_gas = $('#alquiler_contador_tarifa_gas').val();
    var iva_tarifa_gas = $('#iva_tarifa_gas').val();

    // Se modifican las tarifas de gas
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/modifica_tarifas_gas_Espanya.php", {
        ids_tarifas_gas: ids_tarifas_gas,
        tipo: tipo_tarifa_gas,
        expiracion: expiracion_tarifa,
        fecha_expiracion: fecha_expiracion_tarifa,
        numero_dias_preaviso_expiracion: numero_dias_preaviso_expiracion_tarifa,
        factor_conversion: factor_conversion_tarifa_gas,
        precio_consumo: precio_consumo_tarifa_gas,
        precio_caudal_diario: precio_caudal_diario_tarifa_gas,
        caudal_diario: caudal_diario_tarifa_gas,
        precio_termino_fijo_diario: precio_termino_fijo_diario_tarifa_gas,
        impuesto_gas: impuesto_gas_tarifa_gas,
        tipo_alquiler_contador: tipo_alquiler_contador_tarifa_gas,
        alquiler_contador: alquiler_contador_tarifa_gas,
        iva: iva_tarifa_gas
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        switch (resultado.tipo_mensaje) {
            case TIPO_MENSAJE_INFORMACION: {
                jInfo(resultado.msg);
                break;
            }
            case TIPO_MENSAJE_AVISO: {
                jAlert(resultado.msg);
                break;
            }
        }
        actualiza_tabla_tarifas_gas_Espanya();
    });
}


function boton_smartmeter_filtro_tarifas_tabla_gas_Espanya() {
    boton_smartmeter_actualizar_tabla_tarifas_gas_Espanya();
}


function boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_gas_Espanya(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_tarifa_gas = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/muestra_ventana_anyadir_modificar_tarifa_gas_Espanya.php", {
		id_tarifa_gas: id_tarifa_gas,
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


function boton_smartmeter_eliminar_tarifa_gas_Espanya(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_tarifa_gas = params[1];
    var nombre_tarifa_gas = $(this).attr('nombre_tarifa_gas');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la tarifa?") + "\n(" + escapeHtml(nombre_tarifa_gas) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/elimina_tarifa_gas_Espanya.php", {
				id_tarifa_gas: id_tarifa_gas
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
				actualiza_tabla_tarifas_gas_Espanya();
                actualiza_tabla_grupos_tarifas();
			});
		}
	});
}


function boton_smartmeter_anyadir_modificar_tarifa_gas_Espanya() {
    // Comprobación de tipo seleccionado
    var tipo = $('#tipo_tarifa_gas').val();
    if (tipo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo seleccionado'));
        return;
    }

    // Comprobación de expiración seleccionada
    var expiracion = $('#expiracion_tarifa').val();
    if (expiracion == EXPIRACION_TARIFA_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay expiración seleccionada'));
        return;
    }

    // Grupo de tarifas de gas
    var id_grupo = $('#id_grupo_tarifa').val();

    // Valores de listas desplegables a comprobar según las pestañas visibles
    var tipo_alquiler_contador = $('#tipo_alquiler_contador_tarifa_gas').val();

    // Comprobación de datos correctos de las pestañas visibles
    var ids_pestanyas_visibles = [
        "tab-principal",
        "tab-parametros"];
    if ($("#titulo-tab-factura").css("display") != "none") {
        if (tipo_alquiler_contador == TIPO_NINGUNO) {
            jAlert(TLNT.Idiomas._('No hay tipo de alquiler de contador seleccionado'));
            return;
        }
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

    // Parámetros de la ventana
    var anyadir_tarifa_gas = $("#parametros_ventana_anyadir_modificar_tarifa_gas").attr("anyadir_tarifa_gas");
	var id_tarifa_gas = $("#parametros_ventana_anyadir_modificar_tarifa_gas").attr("id_tarifa_gas");

    // Nombre y descripción
    var nombre = $('#nombre_tarifa_gas').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_tarifa_gas").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_tarifa_gas').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_tarifa_gas").addClass('data-check-failed');
        return;
    }

    // Fecha y número de días de preaviso de expiración
    var fecha_expiracion = $("#fecha_expiracion_tarifa").val();
    var numero_dias_preaviso_expiracion = $("#numero_dias_preaviso_expiracion_tarifa").val();
    if (expiracion == EXPIRACION_TARIFA_SI) {
        if (parseInt(numero_dias_preaviso_expiracion) < 0) {
            var descripcion_error = TLNT.Idiomas._('El número de días de preaviso de expiración debe ser mayor que 0');
            jAlert(descripcion_error);
            return;
        }
    }

    // Parámetros de tarifa de gas
    var factor_conversion = $('#factor_conversion_tarifa_gas').val();
    var precio_consumo = $('#precio_consumo_tarifa_gas').val();
    var precio_caudal_diario = $('#precio_caudal_diario_tarifa_gas').val();
    var caudal_diario = $('#caudal_diario_tarifa_gas').val();
    var precio_termino_fijo_diario = $('#precio_termino_fijo_diario_tarifa_gas').val();

    // Tarifas 2021
    var capacidad_contratada = $('#capacidad_contratada_tarifa_gas').val();
    var termino_fijo = $('#termino_fijo_tarifa_gas').val();
    var termino_fijo_por_cliente = $('#termino_fijo_tarifa_gas_por_cliente').val();
    var termino_variable = $('#termino_variable_tarifa_gas').val();
    var exceso_caudal = $('#exceso_demanda_tarifa_gas').val();

    // Parámetros de factura de gas
    var impuesto_gas = $('#impuesto_gas_tarifa_gas').val();
    var alquiler_contador = $('#alquiler_contador_tarifa_gas').val();
    var iva = $('#iva_tarifa_gas').val();

    // Se añade o modifica la tarifa de gas
    if (anyadir_tarifa_gas == true) {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/anyade_tarifa_gas_Espanya.php", {
            nombre: nombre,
            descripcion: descripcion,
            tipo: tipo,
            id_grupo: id_grupo,
            expiracion: expiracion,
            fecha_expiracion: fecha_expiracion,
            numero_dias_preaviso_expiracion: numero_dias_preaviso_expiracion,
            factor_conversion: factor_conversion,
            precio_consumo: precio_consumo,
            precio_caudal_diario: precio_caudal_diario,
            caudal_diario: caudal_diario,
            precio_termino_fijo_diario: precio_termino_fijo_diario,
            capacidad_contratada : capacidad_contratada ,
            termino_fijo : termino_fijo,
            termino_fijo_por_cliente : termino_fijo_por_cliente,
            termino_variable : termino_variable,
            exceso_caudal : exceso_caudal,
            impuesto_gas: impuesto_gas,
            tipo_alquiler_contador: tipo_alquiler_contador,
            alquiler_contador: alquiler_contador,
            iva: iva,
            id_tarifa_gas_anterior: id_tarifa_gas
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_tarifas_gas_Espanya();
            actualiza_tabla_grupos_tarifas();
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/modifica_tarifa_gas_Espanya.php", {
            id_tarifa_gas: id_tarifa_gas,
            nombre: nombre,
            descripcion: descripcion,
            tipo: tipo,
            id_grupo: id_grupo,
            expiracion: expiracion,
            fecha_expiracion: fecha_expiracion,
            numero_dias_preaviso_expiracion: numero_dias_preaviso_expiracion,
            factor_conversion: factor_conversion,
            precio_consumo: precio_consumo,
            precio_caudal_diario: precio_caudal_diario,
            caudal_diario: caudal_diario,
            precio_termino_fijo_diario: precio_termino_fijo_diario,
            capacidad_contratada : capacidad_contratada ,
            termino_fijo : termino_fijo,
            termino_fijo_por_cliente : termino_fijo_por_cliente,
            termino_variable : termino_variable,
            exceso_caudal : exceso_caudal,
            impuesto_gas: impuesto_gas,
            tipo_alquiler_contador: tipo_alquiler_contador,
            alquiler_contador: alquiler_contador,
            iva: iva
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_tarifa_detalles(id_tarifa_gas);
            actualiza_tabla_grupos_tarifas();
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_smartmeter_actualizar_tabla_tarifas_gas_Espanya() {
	actualiza_tabla_tarifas_gas_Espanya();
}


function actualiza_tabla_tarifas_gas_Espanya() {
    var filtro = $('#filtro_smartmeter_filtro_tarifas_tabla_gas_Espanya').val();
    var tipo = $('#tipo_tarifa_gas_smartmeter_filtro_tarifas_tabla_gas_Espanya').val();
    var id_grupo = $('#id_grupo_tarifas_gas_smartmeter_filtro_tarifas_tabla_gas_Espanya').val();
    var estado = $('#estado_tarifa_smartmeter_filtro_tarifas_tabla_gas_Espanya').val();

	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/dame_tabla_tarifas_gas_Espanya.php", {
	    filtro: filtro,
        tipo: tipo,
        id_grupo: id_grupo,
        estado: estado
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaTarifasGas').html(resultado.html);

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}
