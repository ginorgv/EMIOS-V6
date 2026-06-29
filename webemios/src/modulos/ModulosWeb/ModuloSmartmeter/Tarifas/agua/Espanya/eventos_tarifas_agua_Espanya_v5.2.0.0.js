//
// Eventos de tarifas (agua - España)
//


function boton_smartmeter_mostrar_ventana_modificar_tarifas_agua_Espanya() {
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/muestra_ventana_modificar_tarifas_agua_Espanya.php", {},
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


function boton_smartmeter_modificar_tarifas_agua_Espanya() {
    // Tipo de tarifa de agua
    var tipo_tarifa_agua = $('#tipo_tarifa_agua').val();

    // Tipo de límites de consumo por tramo seleccionado
    var tipo_limites_consumo_tramos = $('#tipo_limites_consumo_tramos_tarifa_agua').val();

    // Expiración seleccionada
    var expiracion_tarifa = $('#expiracion_tarifa').val();

    // Valores de listas desplegables a comprobar según las pestañas visibles
    var tipo_alquiler_contador = $('#tipo_alquiler_contador_tarifa_agua').val();

    // Se recuperan los identificadores de las tarifas de agua seleccionadas
    var ids_tarifas_agua = [];
    $("#ids_tarifas_agua_tarifa_agua option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_tarifas_agua.push($(this).val());
        }
    });
    if (ids_tarifas_agua.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos una tarifa"));
        return;
	}

    // Comprobación de datos correctos de las pestañas visibles
    var ids_pestanyas_visibles = ["tab-principal"];
    if ($("#titulo-tab-precios-consumo").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-precios-consumo");
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

    // Límites y precios de consumo por tramos de tarifa de agua
    var cadena_limites_consumo_tramos = elimina_espacios($('#limites_consumo_tramos_tarifa_agua').val());
    var cadena_precios_consumo_tramos = elimina_espacios($('#precios_consumo_tramos_tarifa_agua').val());
    if ((cadena_limites_consumo_tramos != "") || (cadena_precios_consumo_tramos != "")) {
        var limites_consumo_tramos = null;
        var precios_consumo_tramos = null;
        if (cadena_limites_consumo_tramos == "") {
            limites_consumo_tramos = [];
        }
        else {
            limites_consumo_tramos = cadena_limites_consumo_tramos.split(",");
        }
        if (cadena_precios_consumo_tramos == "") {
            precios_consumo_tramos = [];
        }
        else {
            precios_consumo_tramos = cadena_precios_consumo_tramos.split(",");
        }
        if (precios_consumo_tramos > NUMERO_MAXIMO_TRAMOS_TARIFA_AGUA_ESPANYA) {
            var descripcion_error = TLNT.Idiomas._('El número de tramos de consumo supera el máximo permitido') +
                " (" + NUMERO_MAXIMO_TRAMOS_TARIFA_AGUA_ESPANYA + ")";
            jAlert(descripcion_error);
            return;
        }
        if (precios_consumo_tramos.length != (limites_consumo_tramos.length + 1)) {
            var descripcion_error = TLNT.Idiomas._('Los números de límites y de precios de consumo por tramo son incorrectos');
            jAlert(descripcion_error);
            return;
        }
        for (var i = 0; i < limites_consumo_tramos.length; i++) {
            var limite_consumo_tramo_tarifa_agua = limites_consumo_tramos[i];
            if (PATRON_NUMERO_REAL.test(limite_consumo_tramo_tarifa_agua) == false) {
                jAlert(TLNT.Idiomas._('Los límites de consumo por tramo deben ser numéricos'));
                return;
            }
        }
        for (var i = 0; i < precios_consumo_tramos.length; i++) {
            var precio_consumo_tramo_tarifa_agua = precios_consumo_tramos[i];
            if (PATRON_NUMERO_REAL.test(precio_consumo_tramo_tarifa_agua) == false) {
                jAlert(TLNT.Idiomas._('Los precios de consumo por tramo deben ser numéricos'));
                return;
            }
        }
        cadena_limites_consumo_tramos = limites_consumo_tramos.join(SEPARADOR_PARAMETROS_SIMPLES);
        cadena_precios_consumo_tramos = precios_consumo_tramos.join(SEPARADOR_PARAMETROS_SIMPLES);
    }

    // Parámetros de factura de agua
    var alquiler_contador = $('#alquiler_contador_tarifa_agua').val();
    var iva_consumo = $('#iva_consumo_tarifa_agua').val();
    var igic_consumo = $('#igic_consumo_tarifa_agua').val();
    var iva_alquiler_contador = $('#iva_alquiler_contador_tarifa_agua').val();
    var igic_alquiler_contador = $('#igic_alquiler_contador_tarifa_agua').val();

    // Se modifican las tarifas de agua
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/modifica_tarifas_agua_Espanya.php", {
        ids_tarifas_agua: ids_tarifas_agua,
        tipo: tipo_tarifa_agua,
        expiracion: expiracion_tarifa,
        fecha_expiracion: fecha_expiracion_tarifa,
        numero_dias_preaviso_expiracion: numero_dias_preaviso_expiracion_tarifa,
        tipo_limites_consumo_tramos: tipo_limites_consumo_tramos,
        limites_consumo_tramos: cadena_limites_consumo_tramos,
        precios_consumo_tramos: cadena_precios_consumo_tramos,
        tipo_alquiler_contador: tipo_alquiler_contador,
        alquiler_contador: alquiler_contador,
        iva_consumo: iva_consumo,
        igic_consumo: igic_consumo,
        iva_alquiler_contador: iva_alquiler_contador,
        igic_alquiler_contador: igic_alquiler_contador
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
        actualiza_tabla_tarifas_agua_Espanya();
    });
}


function boton_smartmeter_filtro_tarifas_tabla_agua_Espanya() {
    boton_smartmeter_actualizar_tabla_tarifas_agua_Espanya();
}


function boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_agua_Espanya(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_tarifa_agua = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/muestra_ventana_anyadir_modificar_tarifa_agua_Espanya.php", {
		id_tarifa_agua: id_tarifa_agua,
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


function boton_smartmeter_eliminar_tarifa_agua_Espanya(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_tarifa_agua = params[1];
    var nombre_tarifa_agua = $(this).attr('nombre_tarifa_agua');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la tarifa?") + "\n(" + escapeHtml(nombre_tarifa_agua) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/elimina_tarifa_agua_Espanya.php", {
				id_tarifa_agua: id_tarifa_agua
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
				actualiza_tabla_tarifas_agua_Espanya();
                actualiza_tabla_grupos_tarifas();
			});
		}
	});
}


function boton_smartmeter_anyadir_modificar_tarifa_agua_Espanya() {
    // Comprobación de tipo seleccionado
    var tipo = $('#tipo_tarifa_agua').val();
    if (tipo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo seleccionado'));
        return;
    }

    // Comprobación de tipo de límites de consumo por tramo seleccionado
    var tipo_limites_consumo_tramos = $('#tipo_limites_consumo_tramos_tarifa_agua').val();
    if (tipo_limites_consumo_tramos == TIPO_LIMITES_CONSUMO_TRAMOS_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo de límites de consumo por tramo seleccionado'));
        return;
    }

    // Comprobación de expiración seleccionada
    var expiracion = $('#expiracion_tarifa').val();
    if (expiracion == EXPIRACION_TARIFA_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay expiración seleccionada'));
        return;
    }

    // Grupo de tarifas de agua
    var id_grupo = $('#id_grupo_tarifa').val();

    // Valores de listas desplegables a comprobar según las pestañas visibles
    var tipo_alquiler_contador = $('#tipo_alquiler_contador_tarifa_agua').val();

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
    var anyadir_tarifa_agua = $("#parametros_ventana_anyadir_modificar_tarifa_agua").attr("anyadir_tarifa_agua");
	var id_tarifa_agua = $("#parametros_ventana_anyadir_modificar_tarifa_agua").attr("id_tarifa_agua");

    // Nombre y descripción
    var nombre = $('#nombre_tarifa_agua').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_tarifa_agua").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_tarifa_agua').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_tarifa_agua").addClass('data-check-failed');
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

    // Precios de consumo por tramo de tarifa de agua
    var cadena_limites_consumo_tramos = elimina_espacios($('#limites_consumo_tramos_tarifa_agua').val());
    var cadena_precios_consumo_tramos = elimina_espacios($('#precios_consumo_tramos_tarifa_agua').val());
    var limites_consumo_tramos = null;
    var precios_consumo_tramos = null;
    if (cadena_limites_consumo_tramos == "") {
        limites_consumo_tramos = [];
    }
    else {
        limites_consumo_tramos = cadena_limites_consumo_tramos.split(",");
    }
    if (cadena_precios_consumo_tramos == "") {
        precios_consumo_tramos = [];
    }
    else {
        precios_consumo_tramos = cadena_precios_consumo_tramos.split(",");
    }
    if (precios_consumo_tramos > NUMERO_MAXIMO_TRAMOS_TARIFA_AGUA_ESPANYA) {
        var descripcion_error = TLNT.Idiomas._('El número de tramos de consumo supera el máximo permitido') +
            " (" + NUMERO_MAXIMO_TRAMOS_TARIFA_AGUA_ESPANYA + ")";
        jAlert(descripcion_error);
        return;
    }
    if (precios_consumo_tramos.length != (limites_consumo_tramos.length + 1)) {
        var descripcion_error = TLNT.Idiomas._('Los números de límites y de precios de consumo por tramo son incorrectos');
        jAlert(descripcion_error);
        return;
    }
    for (var i = 0; i < limites_consumo_tramos.length; i++) {
        var limite_consumo_tramo_tarifa_agua = limites_consumo_tramos[i];
        if (PATRON_NUMERO_REAL.test(limite_consumo_tramo_tarifa_agua) == false) {
            jAlert(TLNT.Idiomas._('Los límites de consumo por tramo deben ser numéricos'));
            return;
        }
    }
    for (var i = 0; i < precios_consumo_tramos.length; i++) {
        var precio_consumo_tramo_tarifa_agua = precios_consumo_tramos[i];
        if (PATRON_NUMERO_REAL.test(precio_consumo_tramo_tarifa_agua) == false) {
            jAlert(TLNT.Idiomas._('Los precios de consumo por tramo deben ser numéricos'));
            return;
        }
    }
    cadena_limites_consumo_tramos = limites_consumo_tramos.join(SEPARADOR_PARAMETROS_SIMPLES);
    cadena_precios_consumo_tramos = precios_consumo_tramos.join(SEPARADOR_PARAMETROS_SIMPLES);

    // Parámetros de factura de agua
    var alquiler_contador = $('#alquiler_contador_tarifa_agua').val();
    var iva_consumo = $('#iva_consumo_tarifa_agua').val();
    var igic_consumo = $('#igic_consumo_tarifa_agua').val();
    var iva_alquiler_contador = $('#iva_alquiler_contador_tarifa_agua').val();
    var igic_alquiler_contador = $('#igic_alquiler_contador_tarifa_agua').val();

    // Se añade o modifica la tarifa de agua
    if (anyadir_tarifa_agua == true) {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/anyade_tarifa_agua_Espanya.php", {
            nombre: nombre,
            descripcion: descripcion,
            tipo: tipo,
            id_grupo: id_grupo,
            expiracion: expiracion,
            fecha_expiracion: fecha_expiracion,
            numero_dias_preaviso_expiracion: numero_dias_preaviso_expiracion,
            tipo_limites_consumo_tramos: tipo_limites_consumo_tramos,
            limites_consumo_tramos: cadena_limites_consumo_tramos,
            precios_consumo_tramos: cadena_precios_consumo_tramos,
            tipo_alquiler_contador: tipo_alquiler_contador,
            alquiler_contador: alquiler_contador,
            iva_consumo: iva_consumo,
            igic_consumo: igic_consumo,
            iva_alquiler_contador: iva_alquiler_contador,
            igic_alquiler_contador: igic_alquiler_contador,
            id_tarifa_agua_anterior: id_tarifa_agua
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_tarifas_agua_Espanya();
            actualiza_tabla_grupos_tarifas();
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/modifica_tarifa_agua_Espanya.php", {
            id_tarifa_agua: id_tarifa_agua,
            nombre: nombre,
            descripcion: descripcion,
            tipo: tipo,
            id_grupo: id_grupo,
            expiracion: expiracion,
            fecha_expiracion: fecha_expiracion,
            numero_dias_preaviso_expiracion: numero_dias_preaviso_expiracion,
            tipo_limites_consumo_tramos: tipo_limites_consumo_tramos,
            limites_consumo_tramos: cadena_limites_consumo_tramos,
            precios_consumo_tramos: cadena_precios_consumo_tramos,
            tipo_alquiler_contador: tipo_alquiler_contador,
            alquiler_contador: alquiler_contador,
            iva_consumo: iva_consumo,
            igic_consumo: igic_consumo,
            iva_alquiler_contador: iva_alquiler_contador,
            igic_alquiler_contador: igic_alquiler_contador
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_tarifa_detalles(id_tarifa_agua);
            actualiza_tabla_grupos_tarifas();
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_smartmeter_actualizar_tabla_tarifas_agua_Espanya() {
	actualiza_tabla_tarifas_agua_Espanya();
}


function actualiza_tabla_tarifas_agua_Espanya() {
    var filtro = $('#filtro_smartmeter_filtro_tarifas_tabla_agua_Espanya').val();
    var tipo = $('#tipo_tarifa_agua_smartmeter_filtro_tarifas_tabla_agua_Espanya').val();
    var id_grupo = $('#id_grupo_tarifas_agua_smartmeter_filtro_tarifas_tabla_agua_Espanya').val();
    var estado = $('#estado_tarifa_smartmeter_filtro_tarifas_tabla_agua_Espanya').val();

	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/dame_tabla_tarifas_agua_Espanya.php", {
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

        $('#tablaTarifasAgua').html(resultado.html);

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}
