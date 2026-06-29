//
// Eventos de tarifas (electricidad - España)
//


function boton_smartmeter_mostrar_ventana_modificar_tarifas_electricidad_Espanya() {
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/muestra_ventana_modificar_tarifas_electricidad_Espanya.php", {},
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


function boton_smartmeter_modificar_tarifas_electricidad_Espanya() {
    // Tipo
    var tipo = $('#tipo_tarifa_electrica').val();

    // Expiración seleccionada
    var expiracion_tarifa = $('#expiracion_tarifa').val();

    // Valores de listas desplegables a comprobar según las pestañas visibles. Inicialmente poner valores por defecto
    var tipo_alquiler_contador = $('#tipo_alquiler_contador_tarifa_electrica').val();
    var id_indicador_omie_pass_pool = $('#id_indicador_omie_pass_pool_tarifa_electrica').val();
    var tipo_calculo_coste_pass_pool = TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO;
    var dia_calculo_coste_automatico_pass_pool = $('#dia_calculo_coste_automatico_pass_pool_tarifa_electrica').val();
    var formula_precio_consumo_pass_through = "";
    var fecha_inicio_contrato_cierre = $('#fecha_inicio_contrato_cierre').val();

    // Comprobación de datos correctos de las pestañas visibles
    var ids_pestanyas_visibles = ["tab-principal"];
    switch (contrato) {
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO: {
            ids_pestanyas_visibles.push("tab-contrato-fijo");
            break;
        }
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL: {
            id_indicador_omie_pass_pool = $('#id_indicador_omie_pass_pool_tarifa_electrica').val();
            tipo_calculo_coste_pass_pool = $('#tipo_calculo_coste_pass_pool_tarifa_electrica').val();

            if (id_indicador_omie_pass_pool == ID_INDICADOR_OMIE_TARIFA_ELECTRICA_NINGUNO) {
                jAlert(TLNT.Idiomas._('No hay OMIE seleccionado'));
                return;
            }
            if (tipo_calculo_coste_pass_pool == TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO) {
                jAlert(TLNT.Idiomas._('No hay tipo de cálculo de coste seleccionado'));
                return;
            }
            
            if ((parseInt(dia_calculo_coste_automatico_pass_pool) < VALOR_MINIMO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL) ||
                (parseInt(dia_calculo_coste_automatico_pass_pool) > VALOR_MAXIMO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL)) {
                var descripcion_error = TLNT.Idiomas._('El día de cálculo de coste automático es incorrecto') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    VALOR_MINIMO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL + " - " + VALOR_MAXIMO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL + ")";
                jAlert(descripcion_error);
                return;
            }

            ids_pestanyas_visibles.push("tab-contrato-pass-pool");
            break;
        }
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH: {
            formula_precio_consumo_pass_through = $('#formula_precio_consumo_pass_through_tarifa_electrica').val();

            if (comprueba_longitud_cadena(formula_precio_consumo_pass_through, NUMERO_MAXIMO_CARACTERES_FORMULA_PRECIO_CONSUMO) == false) {
                $("#formula_precio_consumo_pass_through_tarifa_electrica").addClass('data-check-failed');
                return;
            }

            ids_pestanyas_visibles.push("tab-contrato-pass-through");
            break;
        }
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE: {
            // Parámetros 'cierre', se asignan los valores a las variables ya existentes de pass-pool y pass-through para que se almacenen en BD
            id_indicador_omie_pass_pool = $('#id_indicador_omie_cierre_tarifa_electrica').val();
            formula_precio_consumo_pass_through = $('#formula_precio_consumo_cierre_tarifa_electrica').val();

            if (id_indicador_omie_pass_pool == ID_INDICADOR_OMIE_TARIFA_ELECTRICA_NINGUNO) {
                jAlert(TLNT.Idiomas._('No hay OMIE seleccionado'));
                return;
            }

            //TODO: poner comprobaciones para la fecha inicio de contrato, y comprobar si es un campo obligatorio

            if (comprueba_longitud_cadena(formula_precio_consumo_pass_through, NUMERO_MAXIMO_CARACTERES_FORMULA_PRECIO_CONSUMO) == false) {
                $("#formula_precio_consumo_cierre_tarifa_electrica").addClass('data-check-failed');
                return;
            }

            ids_pestanyas_visibles.push("tab-contrato-cierre");
            break;
        }
    }

    // Se recuperan los identificadores de las tarifas eléctricas seleccionadas
    var ids_tarifas_electricas = [];
    $("#ids_tarifas_electricas_tarifa_electrica option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_tarifas_electricas.push($(this).val());
        }
    });
    if (ids_tarifas_electricas.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos una tarifa"));
        return;
	}

    // Comprobación de datos correctos de las pestañas visibles
    var ids_pestanyas_visibles = ["tab-principal"];
    var contrato = $('#contrato_tarifa_electrica').val();
    switch (contrato) {
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO: {
            ids_pestanyas_visibles.push("tab-contrato-fijo");
            break;
        }
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL: {
            ids_pestanyas_visibles.push("tab-contrato-pass-pool");
            break;
        }
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH: {
            ids_pestanyas_visibles.push("tab-contrato-pass-through");
            break;
        }
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE: {
            ids_pestanyas_visibles.push("tab-contrato-cierre");
            break;
        }
    }
    if ($("#titulo-tab-precios-consumo-tarifa-acceso-tramos").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-precios-consumo-tarifa-acceso-tramos");
    }
    if ($("#titulo-tab-precios-potencias-tramos").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-precios-potencias-tramos");
    }
    if ($("#titulo-tab-potencias-tramos").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-potencias-tramos");
    }
    if ($("#titulo-tab-excesos-potencia-maximos-mensuales").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-excesos-potencia-maximos-mensuales");
    }
    if ($("#titulo-tab-medida-datos-facturacion").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-medida-datos-facturacion");
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

    // Características de tipo de tarifa eléctrica
    var caracteristicas_tipo_tarifa_electrica = dame_caracteristicas_tipo_tarifa_electrica_Espanya(tipo);

    // Número de tramos y tipo de cálculo de coste de potencias
    var numero_tramos = caracteristicas_tipo_tarifa_electrica["numero_tramos"];
    var tipo_calculo_coste_potencias = caracteristicas_tipo_tarifa_electrica["tipo_calculo_coste_potencias"];

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

    // Bonificación 85 %
    var bonificacion_85 = null;
    switch (tipo_calculo_coste_potencias) {
        case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES: {
            bonificacion_85 = $("#bonificacion_85_tarifa_electrica").val();
            break;
        }
        default: {
            bonificacion_85 = "";
            break;
        }
    }

    // Parámetros de medida de datos de facturación
    var tipo_medida = $('#tipo_medida_tarifa_electrica').val();
    var potencia_nominal_transformador = null;
    switch (tipo_medida) {
        case TIPO_MEDIDA_TARIFA_ELECTRICA_BAJA_TENSION: {
            potencia_nominal_transformador = $('#potencia_nominal_transformador_tarifa_electrica').val();
            if (parseFloat(potencia_nominal_transformador) < 0) {
                jAlert(TLNT.Idiomas._('La potencia nominal del transformador debe ser mayor o igual que 0'));
                return;
            }
            break;
        }
        default: {
            potencia_nominal_transformador = "";
        }
    }


    // Tramos con potencias iguales
    var tramos_potencias_iguales = caracteristicas_tipo_tarifa_electrica["tramos_potencias_iguales"];
    var numero_tramos_potencias = null;
    if (tramos_potencias_iguales == null) {
        numero_tramos_potencias = numero_tramos;
    }
    else {
        numero_tramos_potencias = tramos_potencias_iguales.length;
    }

    // Se recupera la información de potencias de los tramos
    // (si hay tramos con potencias iguales, luego se "desagrupan" para añadirlos a base de datos)
    var precios_potencias_tramos = [];
    var potencias_tramos = [];
    for (var i = 1; i <= numero_tramos_potencias; i++) {
        precios_potencias_tramos.push($("#precio_potencia_tramo_tarifa_electrica__" + i).val());
        potencias_tramos.push($("#potencia_tramo_tarifa_electrica__" + i).val());
    }

    // Se "desagrupa" la información de potencias (si es necesario)
    if (tramos_potencias_iguales != null) {
        var precios_potencias_tramos_agrupados = precios_potencias_tramos;
        var potencias_tramos_agrupadas = potencias_tramos;
        precios_potencias_tramos = [];
        potencias_tramos = [];
        for (var i = 0; i < tramos_potencias_iguales.length; i++) {
            var precio_potencia_tramo_agrupado = precios_potencias_tramos_agrupados[i];
            var potencia_tramo_agrupado = potencias_tramos_agrupadas[i];
            var tramos_potencia_igual = tramos_potencias_iguales[i];
            for (var j = 0; j < tramos_potencia_igual.length; j++) {
                var numero_tramo = tramos_potencia_igual[j];
                if (precio_potencia_tramo_agrupado == "") {
                    precios_potencias_tramos[numero_tramo - 1] = "";
                }
                else {
                    precios_potencias_tramos[numero_tramo - 1] = precio_potencia_tramo_agrupado / tramos_potencia_igual.length;
                }
                if (potencia_tramo_agrupado == "") {
                    potencias_tramos[numero_tramo - 1] = "";
                }
                else {
                    potencias_tramos[numero_tramo - 1] = potencia_tramo_agrupado;
                }
            }
        }
    }

    // Se recupera la información de los tramos (con los parámetros generales y los específicos de 'pass-pool')
    var info_tramos = [];
    for (var i = 1; i <= numero_tramos; i++) {
        var info_tramo = {};
        info_tramo["numero_tramo"] = i;
        info_tramo["precio_consumo"] = $("#precio_consumo_tramo_tarifa_electrica__" + i).val();
        info_tramo["coeficiente_a_precio_consumo_pass_pool"] = $("#coeficiente_a_precio_consumo_pass_pool_tarifa_electrica__" + i).val();
        info_tramo["coeficiente_b_precio_consumo_pass_pool"] = $("#coeficiente_b_precio_consumo_pass_pool_tarifa_electrica__" + i).val();
        info_tramo["precio_consumo_tarifa_acceso"] = $("#precio_consumo_tarifa_acceso_tramo_tarifa_electrica__" + i).val();
        info_tramo["precio_potencia"] = precios_potencias_tramos[i - 1];
        info_tramo["potencia"] = potencias_tramos[i - 1];
        info_tramos.push(info_tramo);
    }

    // Se comprueba que las potencias de los tramos son correctas
    var potencias_tramos_correctas = comprueba_potencias_tramos_tarifa_electrica_correctas(tipo, potencias_tramos);
    if (potencias_tramos_correctas == false) {
        return;
    }

    // Parámetros de factura eléctrica
    var impuesto_electrico = $('#impuesto_electrico_tarifa_electrica').val();
    var alquiler_contador = $('#alquiler_contador_tarifa_electrica').val();
    var iva = $('#iva_tarifa_electrica').val();
    var igic_reducido = $('#igic_reducido_tarifa_electrica').val();
    var igic_normal = $('#igic_normal_tarifa_electrica').val();
    var prorrateo = $('#prorrateo_tarifa').val();

    // Se modifican las tarifas eléctricas
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/modifica_tarifas_electricidad_Espanya.php", {
        ids_tarifas_electricas: ids_tarifas_electricas,
        tipo: tipo,
        contrato: contrato,
        expiracion: expiracion_tarifa,
        fecha_expiracion: fecha_expiracion_tarifa,
        numero_dias_preaviso_expiracion: numero_dias_preaviso_expiracion_tarifa,
        bonificacion_85: bonificacion_85,
        tipo_medida: tipo_medida,
        potencia_nominal_transformador: potencia_nominal_transformador,
        id_indicador_omie_pass_pool: id_indicador_omie_pass_pool,
        tipo_calculo_coste_pass_pool: tipo_calculo_coste_pass_pool,
        dia_calculo_coste_automatico_pass_pool: dia_calculo_coste_automatico_pass_pool,
        formula_precio_consumo_pass_through: formula_precio_consumo_pass_through,
        fecha_inicio_contrato_cierre: fecha_inicio_contrato_cierre,
        impuesto_electrico: impuesto_electrico,
        tipo_alquiler_contador: tipo_alquiler_contador,
        alquiler_contador: alquiler_contador,
        iva: iva,
        igic_reducido: igic_reducido,
        igic_normal: igic_normal,
        prorrateo: prorrateo,
        info_tramos: info_tramos
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
        actualiza_tabla_tarifas_electricidad_Espanya();
    });
}


function boton_smartmeter_filtro_tarifas_tabla_electricidad_Espanya() {
    boton_smartmeter_actualizar_tabla_tarifas_electricidad_Espanya();
}


function boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Espanya(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_tarifa_electrica = params[1];
    var tipo_operacion_administracion = params[2];

    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/muestra_ventana_anyadir_modificar_tarifa_electricidad_Espanya.php", {
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


function boton_smartmeter_eliminar_tarifa_electricidad_Espanya(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_tarifa_electrica = params[1];
    var nombre_tarifa_electrica = $(this).attr('nombre_tarifa_electrica');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar la tarifa?") + "\n(" + escapeHtml(nombre_tarifa_electrica) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/elimina_tarifa_electricidad_Espanya.php", {
				id_tarifa_electrica: id_tarifa_electrica
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
				actualiza_tabla_tarifas_electricidad_Espanya();
                actualiza_tabla_grupos_tarifas();
			});
		}
	});
}


function boton_smartmeter_anyadir_modificar_tarifa_electricidad_Espanya() {
    // Comprobación de tipo seleccionado
    var tipo = $('#tipo_tarifa_electrica').val();
    if (tipo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo seleccionado'));
        return;
    }

    // Comprobación de contrato seleccionado
    var contrato = $('#contrato_tarifa_electrica').val();
    if (contrato == CONTRATO_TARIFA_ELECTRICA_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay contrato seleccionado'));
        return;
    }

    // Comprobación de expiración seleccionada
    var expiracion = $('#expiracion_tarifa').val();
    if (expiracion == EXPIRACION_TARIFA_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay expiración seleccionada'));
        return;
    }

    // Grupo de tarifas eléctricas
    var id_grupo = $('#id_grupo_tarifa').val();

    // Valores de listas desplegables a comprobar según las pestañas visibles. Inicialmente poner valores por defecto
    var tipo_alquiler_contador = $('#tipo_alquiler_contador_tarifa_electrica').val();
    var id_indicador_omie_pass_pool = $('#id_indicador_omie_pass_pool_tarifa_electrica').val();
    var tipo_calculo_coste_pass_pool = TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO;
    var dia_calculo_coste_automatico_pass_pool = $('#dia_calculo_coste_automatico_pass_pool_tarifa_electrica').val();
    var formula_precio_consumo_pass_through = "";
    var fecha_inicio_contrato_cierre = $('#fecha_inicio_contrato_cierre').val();

    // Comprobación de datos correctos de las pestañas visibles
    var ids_pestanyas_visibles = ["tab-principal"];
    switch (contrato) {
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO: {
            ids_pestanyas_visibles.push("tab-contrato-fijo");
            break;
        }
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL: {
            id_indicador_omie_pass_pool = $('#id_indicador_omie_pass_pool_tarifa_electrica').val();
            tipo_calculo_coste_pass_pool = $('#tipo_calculo_coste_pass_pool_tarifa_electrica').val();

            if (id_indicador_omie_pass_pool == ID_INDICADOR_OMIE_TARIFA_ELECTRICA_NINGUNO) {
                jAlert(TLNT.Idiomas._('No hay OMIE seleccionado'));
                return;
            }
            if (tipo_calculo_coste_pass_pool == TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO) {
                jAlert(TLNT.Idiomas._('No hay tipo de cálculo de coste seleccionado'));
                return;
            }
            
            if ((parseInt(dia_calculo_coste_automatico_pass_pool) < VALOR_MINIMO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL) ||
                (parseInt(dia_calculo_coste_automatico_pass_pool) > VALOR_MAXIMO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL)) {
                var descripcion_error = TLNT.Idiomas._('El día de cálculo de coste automático es incorrecto') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    VALOR_MINIMO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL + " - " + VALOR_MAXIMO_DIA_CALCULO_COSTE_AUTOMATICO_PASS_POOL + ")";
                jAlert(descripcion_error);
                return;
            }

            ids_pestanyas_visibles.push("tab-contrato-pass-pool");
            break;
        }
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH: {
            formula_precio_consumo_pass_through = $('#formula_precio_consumo_pass_through_tarifa_electrica').val();

            if (comprueba_longitud_cadena(formula_precio_consumo_pass_through, NUMERO_MAXIMO_CARACTERES_FORMULA_PRECIO_CONSUMO) == false) {
                $("#formula_precio_consumo_pass_through_tarifa_electrica").addClass('data-check-failed');
                return;
            }

            ids_pestanyas_visibles.push("tab-contrato-pass-through");
            break;
        }
        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE: {
            // Parámetros 'cierre', se asignan los valores a las variables ya existentes de pass-pool y pass-through para que se almacenen en BD
            id_indicador_omie_pass_pool = $('#id_indicador_omie_cierre_tarifa_electrica').val();
            formula_precio_consumo_pass_through = $('#formula_precio_consumo_cierre_tarifa_electrica').val();

            if (id_indicador_omie_pass_pool == ID_INDICADOR_OMIE_TARIFA_ELECTRICA_NINGUNO) {
                jAlert(TLNT.Idiomas._('No hay OMIE seleccionado'));
                return;
            }

            //TODO: poner comprobaciones para la fecha inicio de contrato, y comprobar si es un campo obligatorio
            
            if (comprueba_longitud_cadena(formula_precio_consumo_pass_through, NUMERO_MAXIMO_CARACTERES_FORMULA_PRECIO_CONSUMO) == false) {
                $("#formula_precio_consumo_cierre_tarifa_electrica").addClass('data-check-failed');
                return;
            }

            ids_pestanyas_visibles.push("tab-contrato-cierre");
            break;
        }
    }

    if ($("#titulo-tab-precios-consumo-tarifa-acceso-tramos").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-precios-consumo-tarifa-acceso-tramos");
    }
    if ($("#titulo-tab-precios-potencias-tramos").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-precios-potencias-tramos");
    }
    if ($("#titulo-tab-potencias-tramos").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-potencias-tramos");
    }
    if ($("#titulo-tab-excesos-potencia-maximos-mensuales").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-excesos-potencia-maximos-mensuales");
    }
    if ($("#titulo-tab-medida-datos-facturacion").css("display") != "none") {
        ids_pestanyas_visibles.push("tab-medida-datos-facturacion");
    }
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
    var anyadir_tarifa_electrica = $("#parametros_ventana_anyadir_modificar_tarifa_electrica").attr("anyadir_tarifa_electrica");
	var id_tarifa_electrica = $("#parametros_ventana_anyadir_modificar_tarifa_electrica").attr("id_tarifa_electrica");

    // Características de tipo de tarifa eléctrica
    var caracteristicas_tipo_tarifa_electrica = dame_caracteristicas_tipo_tarifa_electrica_Espanya(tipo);

    // Número de tramos y tipo de cálculo de coste de potencias
    var numero_tramos = caracteristicas_tipo_tarifa_electrica["numero_tramos"];
    var tipo_calculo_coste_potencias = caracteristicas_tipo_tarifa_electrica["tipo_calculo_coste_potencias"];

    // Nombre y descripción
    var nombre = $('#nombre_tarifa_electrica').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_tarifa_electrica").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_tarifa_electrica').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_tarifa_electrica").addClass('data-check-failed');
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

    // Bonificación 85 %
    var bonificacion_85 = null;
    switch (tipo_calculo_coste_potencias) {
        case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES: {
            bonificacion_85 = $("#bonificacion_85_tarifa_electrica").val();
            break;
        }
        default: {
            bonificacion_85 = "";
            break;
        }
    }

    // Parámetros de medida de datos de facturación
    var tipo_medida = $('#tipo_medida_tarifa_electrica').val();
    var potencia_nominal_transformador = null;
    switch (tipo_medida) {
        case TIPO_MEDIDA_TARIFA_ELECTRICA_BAJA_TENSION: {
            potencia_nominal_transformador = $('#potencia_nominal_transformador_tarifa_electrica').val();
            if (parseFloat(potencia_nominal_transformador) < 0) {
                jAlert(TLNT.Idiomas._('La potencia nominal del transformador debe ser mayor o igual que 0'));
                return;
            }
            break;
        }
        default: {
            potencia_nominal_transformador = "";
        }
    }

    // Tramos con potencias iguales
    var tramos_potencias_iguales = caracteristicas_tipo_tarifa_electrica["tramos_potencias_iguales"];
    var numero_tramos_potencias = null;
    if (tramos_potencias_iguales == null) {
        numero_tramos_potencias = numero_tramos;
    }
    else {
        numero_tramos_potencias = tramos_potencias_iguales.length;
    }

    // Se recupera la información de potencias de los tramos
    // (si hay tramos con potencias iguales, luego se "desagrupan" para añadirlos a base de datos)
    var precios_potencias_tramos = [];
    var potencias_tramos = [];
    for (var i = 1; i <= numero_tramos_potencias; i++) {
        precios_potencias_tramos.push($("#precio_potencia_tramo_tarifa_electrica__" + i).val());
        potencias_tramos.push($("#potencia_tramo_tarifa_electrica__" + i).val());
    }

    // Se "desagrupa" la información de potencias (si es necesario)
    if (tramos_potencias_iguales != null) {
        var precios_potencias_tramos_agrupados = precios_potencias_tramos;
        var potencias_tramos_agrupadas = potencias_tramos;
        precios_potencias_tramos = [];
        potencias_tramos = [];
        for (var i = 0; i < tramos_potencias_iguales.length; i++) {
            var precio_potencia_tramo_agrupado = precios_potencias_tramos_agrupados[i];
            var potencia_tramo_agrupado = potencias_tramos_agrupadas[i];
            var tramos_potencia_igual = tramos_potencias_iguales[i];
            for (var j = 0; j < tramos_potencia_igual.length; j++) {
                var numero_tramo = tramos_potencia_igual[j];
                precios_potencias_tramos[numero_tramo - 1] = precio_potencia_tramo_agrupado / tramos_potencia_igual.length;
                potencias_tramos[numero_tramo - 1] = potencia_tramo_agrupado;
            }
        }
    }

    // Se recupera la información de los tramos (con los parámetros generales y los específicos de 'pass-pool')
    var info_tramos = [];
    for (var i = 1; i <= numero_tramos; i++) {
        var info_tramo = {};
        info_tramo["numero_tramo"] = i;
        info_tramo["precio_consumo"] = $("#precio_consumo_tramo_tarifa_electrica__" + i).val();
        info_tramo["coeficiente_a_precio_consumo_pass_pool"] = $("#coeficiente_a_precio_consumo_pass_pool_tarifa_electrica__" + i).val();
        info_tramo["coeficiente_b_precio_consumo_pass_pool"] = $("#coeficiente_b_precio_consumo_pass_pool_tarifa_electrica__" + i).val();
        info_tramo["precio_consumo_tarifa_acceso"] = $("#precio_consumo_tarifa_acceso_tramo_tarifa_electrica__" + i).val();
        info_tramo["precio_potencia"] = precios_potencias_tramos[i - 1];
        info_tramo["potencia"] = potencias_tramos[i - 1];
        info_tramos.push(info_tramo);
    }

    // Se comprueba que las potencias de los tramos son correctas
    var potencias_tramos_correctas = comprueba_potencias_tramos_tarifa_electrica_correctas(tipo, potencias_tramos);
    if (potencias_tramos_correctas == false) {
        return;
    }

    // Parámetros de factura eléctrica
    var impuesto_electrico = $('#impuesto_electrico_tarifa_electrica').val();
    var alquiler_contador = $('#alquiler_contador_tarifa_electrica').val();
    var iva = $('#iva_tarifa_electrica').val();
    var igic_reducido = $('#igic_reducido_tarifa_electrica').val();
    var igic_normal = $('#igic_normal_tarifa_electrica').val();
    var prorrateo = $('#prorrateo_tarifa').val();

    // Se añade o modifica la tarifa eléctrica
    if (anyadir_tarifa_electrica == true) {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/anyade_tarifa_electricidad_Espanya.php", {
            nombre: nombre,
            descripcion: descripcion,
            tipo: tipo,
            contrato: contrato,
            id_grupo: id_grupo,
            expiracion: expiracion,
            fecha_expiracion: fecha_expiracion,
            numero_dias_preaviso_expiracion: numero_dias_preaviso_expiracion,
            bonificacion_85: bonificacion_85,
            tipo_medida: tipo_medida,
            potencia_nominal_transformador: potencia_nominal_transformador,
            id_indicador_omie_pass_pool: id_indicador_omie_pass_pool,
            tipo_calculo_coste_pass_pool: tipo_calculo_coste_pass_pool,
            dia_calculo_coste_automatico_pass_pool: dia_calculo_coste_automatico_pass_pool,
            fecha_inicio_contrato_cierre: fecha_inicio_contrato_cierre,
            formula_precio_consumo_pass_through: formula_precio_consumo_pass_through,
            impuesto_electrico: impuesto_electrico,
            tipo_alquiler_contador: tipo_alquiler_contador,
            alquiler_contador: alquiler_contador,
            iva: iva,
            igic_reducido: igic_reducido,
            igic_normal: igic_normal,
            prorrateo: prorrateo,
            info_tramos: info_tramos,            
            id_tarifa_electrica_anterior: id_tarifa_electrica
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_tarifas_electricidad_Espanya();
            actualiza_tabla_grupos_tarifas();
        });
    }
    else {
        //TODO: actualizar también aquí los cambios
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/modifica_tarifa_electricidad_Espanya.php", {
            id_tarifa_electrica: id_tarifa_electrica,
            nombre: nombre,
            descripcion: descripcion,
            tipo: tipo,
            contrato: contrato,
            id_grupo: id_grupo,
            expiracion: expiracion,
            fecha_expiracion: fecha_expiracion,
            numero_dias_preaviso_expiracion: numero_dias_preaviso_expiracion,
            bonificacion_85: bonificacion_85,
            tipo_medida: tipo_medida,
            potencia_nominal_transformador: potencia_nominal_transformador,
            id_indicador_omie_pass_pool: id_indicador_omie_pass_pool,
            tipo_calculo_coste_pass_pool: tipo_calculo_coste_pass_pool,
            dia_calculo_coste_automatico_pass_pool: dia_calculo_coste_automatico_pass_pool,
            formula_precio_consumo_pass_through: formula_precio_consumo_pass_through,
            fecha_inicio_contrato_cierre: fecha_inicio_contrato_cierre,
            impuesto_electrico: impuesto_electrico,
            tipo_alquiler_contador: tipo_alquiler_contador,
            alquiler_contador: alquiler_contador,
            iva: iva,
            igic_reducido: igic_reducido,
            igic_normal: igic_normal,
            prorrateo: prorrateo,
            info_tramos: info_tramos
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


function boton_smartmeter_actualizar_tabla_tarifas_electricidad_Espanya() {
	actualiza_tabla_tarifas_electricidad_Espanya();
}


function actualiza_tabla_tarifas_electricidad_Espanya() {
    var filtro = $('#filtro_smartmeter_filtro_tarifas_tabla_electricidad_Espanya').val();
    var tipo = $('#tipo_tarifa_electrica_smartmeter_filtro_tarifas_tabla_electricidad_Espanya').val();
    var contrato = $('#contrato_tarifa_electrica_smartmeter_filtro_tarifas_tabla_electricidad_Espanya').val();
    var id_grupo = $('#id_grupo_tarifas_electricas_smartmeter_filtro_tarifas_tabla_electricidad_Espanya').val();
    var estado = $('#estado_tarifa_smartmeter_filtro_tarifas_tabla_electricidad_Espanya').val();

	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/dame_tabla_tarifas_electricidad_Espanya.php", {
	    filtro: filtro,
        tipo: tipo,
        contrato: contrato,
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


//
// Funciones de periodos de cálculo de costes 'pass-pool' de las tarifas eléctricas
//


function boton_smartmeter_mostrar_ventana_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_tarifa_electrica = params[1];
	var id_periodo_calculo_costes = params[2];

	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/muestra_ventana_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya.php", {
        id_tarifa_electrica: id_tarifa_electrica,
        id_periodo_calculo_costes: id_periodo_calculo_costes
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


function boton_smartmeter_eliminar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_tarifa_electrica = params[1];
	var id_periodo_calculo_costes = params[2];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el periodo de cálculo de costes?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/elimina_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya.php", {
                id_tarifa_electrica: id_tarifa_electrica,
                id_periodo_calculo_costes: id_periodo_calculo_costes
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
                actualiza_tabla_periodos_calculo_costes_pass_pool_tarifa_electricidad_Espanya(id_tarifa_electrica);
			});
		}
	});
}


function boton_smartmeter_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_periodo = $("#parametros_ventana_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electrica").attr("anyadir_periodo_calculo_costes");
    var id_tarifa_electrica = $("#parametros_ventana_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electrica").attr("id_tarifa_electrica");
    var id_periodo_calculo_costes = $("#parametros_ventana_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electrica").attr("id_periodo_calculo_costes");

    // Fechas de inicio y fin
    var fecha_inicio = $("#fecha_inicio_periodo_calculo_costes_pass_pool_tarifa_electrica").val();
    var fecha_fin = $("#fecha_fin_periodo_calculo_costes_pass_pool_tarifa_electrica").val();

    // Se añade o modifica el periodo de cálculo de costes
    if (anyadir_periodo == true) {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/anyade_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya.php", {
            id_tarifa_electrica: id_tarifa_electrica,
			fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_periodos_calculo_costes_pass_pool_tarifa_electricidad_Espanya(id_tarifa_electrica);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/modifica_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya.php", {
            id_periodo_calculo_costes: id_periodo_calculo_costes,
            id_tarifa_electrica: id_tarifa_electrica,
			fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_periodos_calculo_costes_pass_pool_tarifa_electricidad_Espanya(id_tarifa_electrica);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_smartmeter_actualizar_tabla_periodos_calculo_costes_pass_pool_tarifa_electricidad_Espanya() {
    var params = this.id.split('__');
    var id_tarifa_electrica = params[1];

    actualiza_tabla_periodos_calculo_costes_pass_pool_tarifa_electricidad_Espanya(id_tarifa_electrica);
}


function actualiza_tabla_periodos_calculo_costes_pass_pool_tarifa_electricidad_Espanya(id_tarifa_electrica) {
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/dame_tabla_periodos_calculo_costes_pass_pool_tarifa_electricidad_Espanya.php", {
		id_tarifa_electrica: id_tarifa_electrica
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_periodos_calculo_costes_pass_pool_tarifa_electrica = "periodos_calculo_costes_pass_pool" + id_tarifa_electrica;
        $('#' + id_elemento_periodos_calculo_costes_pass_pool_tarifa_electrica).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


//
// Funciones de conceptos de coste 'pass-through' de las tarifas eléctricas
//


function boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_tarifa_electrica = params[1];
	var id_concepto_coste = params[2];

	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/muestra_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya.php", {
        id_tarifa_electrica: id_tarifa_electrica,
        id_concepto_coste: id_concepto_coste
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


function boton_smartmeter_eliminar_concepto_coste_pass_through_tarifa_electricidad_Espanya(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_tarifa_electrica = params[1];
	var id_concepto_coste = params[2];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el concepto de coste?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/elimina_concepto_coste_pass_through_tarifa_electricidad_Espanya.php", {
                id_tarifa_electrica: id_tarifa_electrica,
                id_concepto_coste: id_concepto_coste
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
                actualiza_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya(id_tarifa_electrica);
			});
		}
	});
}


function boton_smartmeter_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_concepto_coste = $("#parametros_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electrica").attr("anyadir_concepto_coste");
    var id_tarifa_electrica = $("#parametros_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electrica").attr("id_tarifa_electrica");
    var id_concepto_coste = $("#parametros_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electrica").attr("id_concepto_coste");

    // Nombre y fórmula de precio de consumo
    var nombre = $("#nombre_concepto_coste_pass_through_tarifa_electrica").val();
    var formula_precio_consumo = $("#formula_precio_consumo_concepto_coste_pass_through_tarifa_electrica").val();

    // Se añade o modifica el concepto de coste
    if (anyadir_concepto_coste == true) {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/anyade_concepto_coste_pass_through_tarifa_electricidad_Espanya.php", {
            id_tarifa_electrica: id_tarifa_electrica,
			nombre: nombre,
            formula_precio_consumo: formula_precio_consumo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya(id_tarifa_electrica);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/modifica_concepto_coste_pass_through_tarifa_electricidad_Espanya.php", {
            id_concepto_coste: id_concepto_coste,
            id_tarifa_electrica: id_tarifa_electrica,
			nombre: nombre,
            formula_precio_consumo: formula_precio_consumo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya(id_tarifa_electrica);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_smartmeter_actualizar_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya() {
    var params = this.id.split('__');
    var id_tarifa_electrica = params[1];

    actualiza_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya(id_tarifa_electrica);
}


function actualiza_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya(id_tarifa_electrica) {
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/dame_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya.php", {
		id_tarifa_electrica: id_tarifa_electrica
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_conceptos_coste_pass_through_tarifa_electrica = "conceptos_coste_pass_through" + id_tarifa_electrica;
        $('#' + id_elemento_conceptos_coste_pass_through_tarifa_electrica).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


//
// Funciones de conceptos de coste 'cierre' de las tarifas eléctricas
//


function boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_coste_cierre_tarifa_electricidad_Espanya(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_tarifa_electrica = params[1];
	var id_concepto_coste = params[2];

	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/muestra_ventana_anyadir_modificar_concepto_coste_cierre_tarifa_electricidad_Espanya.php", {
        id_tarifa_electrica: id_tarifa_electrica,
        id_concepto_coste: id_concepto_coste
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


function boton_smartmeter_eliminar_concepto_coste_cierre_tarifa_electricidad_Espanya(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_tarifa_electrica = params[1];
	var id_concepto_coste = params[2];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el concepto de coste?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/elimina_concepto_coste_cierre_tarifa_electricidad_Espanya.php", {
                id_tarifa_electrica: id_tarifa_electrica,
                id_concepto_coste: id_concepto_coste
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
                actualiza_tabla_conceptos_coste_cierre_tarifa_electricidad_Espanya(id_tarifa_electrica);
			});
		}
	});
}


//TODO: comprobar cuándo se ejecuta esto y para qué sirve
function boton_smartmeter_anyadir_modificar_concepto_coste_cierre_tarifa_electricidad_Espanya() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_concepto_coste = $("#parametros_ventana_anyadir_modificar_concepto_coste_cierre_tarifa_electrica").attr("anyadir_concepto_coste");
    var id_tarifa_electrica = $("#parametros_ventana_anyadir_modificar_concepto_coste_cierre_tarifa_electrica").attr("id_tarifa_electrica");
    var id_concepto_coste = $("#parametros_ventana_anyadir_modificar_concepto_coste_cierre_tarifa_electrica").attr("id_concepto_coste");

    // Nombre y fórmula de precio de consumo
    var nombre = $("#nombre_concepto_coste_cierre_tarifa_electrica").val();
    var formula_precio_consumo = $("#formula_precio_consumo_concepto_coste_cierre_tarifa_electrica").val();

    // Se añade o modifica el concepto de coste
    if (anyadir_concepto_coste == true) {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/anyade_concepto_coste_cierre_tarifa_electricidad_Espanya.php", {
            id_tarifa_electrica: id_tarifa_electrica,
			nombre: nombre,
            formula_precio_consumo: formula_precio_consumo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_conceptos_coste_cierre_tarifa_electricidad_Espanya(id_tarifa_electrica);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/modifica_concepto_coste_cierre_tarifa_electricidad_Espanya.php", {
            id_concepto_coste: id_concepto_coste,
            id_tarifa_electrica: id_tarifa_electrica,
			nombre: nombre,
            formula_precio_consumo: formula_precio_consumo
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_conceptos_coste_cierre_tarifa_electricidad_Espanya(id_tarifa_electrica);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_smartmeter_actualizar_tabla_conceptos_coste_cierre_tarifa_electricidad_Espanya() {
    var params = this.id.split('__');
    var id_tarifa_electrica = params[1];

    actualiza_tabla_conceptos_coste_cierre_tarifa_electricidad_Espanya(id_tarifa_electrica);
}


function actualiza_tabla_conceptos_coste_cierre_tarifa_electricidad_Espanya(id_tarifa_electrica) {
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/dame_tabla_conceptos_coste_cierre_tarifa_electricidad_Espanya.php", {
		id_tarifa_electrica: id_tarifa_electrica
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_conceptos_coste_cierre_tarifa_electrica = "conceptos_coste_cierre" + id_tarifa_electrica;
        $('#' + id_elemento_conceptos_coste_cierre_tarifa_electrica).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


//
// Funciones de parámetros de energía eléctrica
//


function boton_smartmeter_mostrar_ventana_exportacion_valores_parametros_energia_electrica_Espanya(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/muestra_ventana_exportacion_valores_parametros_energia_electrica_Espanya.php", {},
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


function boton_smartmeter_exportar_valores_parametros_energia_electrica_Espanya() {
    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_exportacion_valores_parametros_energia_electrica').val();
    var hora_inicio = $('#hora_inicio_exportacion_valores_parametros_energia_electrica').val();
    var fecha_fin = $('#fecha_fin_exportacion_valores_parametros_energia_electrica').val();
    var hora_fin = $('#hora_fin_exportacion_valores_parametros_energia_electrica').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, hora_inicio, fecha_fin, hora_fin);
    if (fechas_correctas == false) {
        return;
    }
    var fecha_hora_inicio = fecha_inicio + ", " + hora_inicio + ":00";
    var fecha_hora_fin = fecha_fin + ", " + hora_fin + ":59";

    // Punto decimal
    var id_punto_decimal = $("#id_punto_decimal_exportacion_valores_parametros_energia_electrica").val();
    var punto_decimal = null;
    switch (id_punto_decimal) {
        case ID_PUNTO_DECIMAL_PUNTO: {
            punto_decimal = ".";
            break;
        }
        case ID_PUNTO_DECIMAL_COMA: {
            punto_decimal = ",";
            break;
        }
    }

    // Se exportan los valores del sensor
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/exporta_valores_parametros_energia_electrica_Espanya.php", {
        fecha_hora_inicio: fecha_hora_inicio,
        fecha_hora_fin: fecha_hora_fin,
        punto_decimal: punto_decimal
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);

        // Se guardan los ficheros de los valores exportados
        if (resultado.rutas_ficheros_valores_exportados.length == 1) {
            var ruta_fichero_valores_exportados = resultado.rutas_ficheros_valores_exportados[0];
            window.location.href = ruta_fichero_valores_exportados;
        }
        else {
            for (var i = 0; i < resultado.rutas_ficheros_valores_exportados.length; i++) {
                var ruta_fichero_valores_exportados = resultado.rutas_ficheros_valores_exportados[i];
                window.open(ruta_fichero_valores_exportados);
            }
        }
    });
}


function boton_smartmeter_actualizar_informacion_parametros_energia_electricidad_Espanya() {
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/dame_info_parametros_energia_electricidad_Espanya.php", {},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

		$('#tab-parametros-energia-electrica').html(resultado.html);

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_secciones();
	});
}


//
// Funciones de costes de conceptos
//


function boton_smartmeter_mostrar_ventana_exportacion_costes_conceptos_consumo_sensor_electricidad_Espanya(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/muestra_ventana_exportacion_costes_conceptos_consumo_sensor_electricidad_Espanya.php", {},
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


function boton_smartmeter_exportar_costes_conceptos_consumo_sensor_electricidad_Espanya() {
    // Identificador y nombre de sensor
    var id_sensor = $('#id_sensor_exportacion_costes_conceptos_consumo_sensor').val();
    if (id_sensor == ID_NINGUNO) {
		jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
        return;
	}
    var nombre_sensor = $('#id_sensor_exportacion_costes_conceptos_consumo_sensor :selected').text();

    // Fechas de inicio y fin
    var fecha_inicio = $('#fecha_inicio_exportacion_costes_conceptos_consumo_sensor').val();
    var fecha_fin = $('#fecha_fin_exportacion_costes_conceptos_consumo_sensor').val();
    var fechas_correctas = comprueba_fechas_inicio_fin_correctas(fecha_inicio, null, fecha_fin, null);
    if (fechas_correctas == false) {
        return;
    }

    // Punto decimal
    var id_punto_decimal = $("#id_punto_decimal_exportacion_costes_conceptos_consumo_sensor").val();
    var punto_decimal = null;
    switch (id_punto_decimal) {
        case ID_PUNTO_DECIMAL_PUNTO: {
            punto_decimal = ".";
            break;
        }
        case ID_PUNTO_DECIMAL_COMA: {
            punto_decimal = ",";
            break;
        }
    }

    // Se exportan los valores del sensor
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/exporta_costes_conceptos_consumo_sensor_electricidad_Espanya.php", {
        nombre_sensor: nombre_sensor,
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin,
        punto_decimal: punto_decimal
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);

        // Se guarda el ficheros de los costes exportados
        var ruta_fichero_costes_exportados = resultado.ruta_fichero_costes_exportados;
        if (ruta_fichero_costes_exportados != null) {
            window.location.href = ruta_fichero_costes_exportados;
        }
    });
}
