//
// Eventos de tarifas
//


function boton_smartmeter_mostrar_ventana_recalculo_datos() {
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/muestra_ventana_recalculo_datos.php", {
        medicion: medicion
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


function boton_smartmeter_recalcular_datos() {
    // Fecha
    var fecha = $('#fecha_inicio_recalculo_datos').val();

    // Se recuperan los identificadores de las tarifas seleccionadas
    var ids_tarifas = [];
    $("#ids_tarifas_recalculo_datos option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_tarifas.push($(this).val());
        }
    });

    // Se recuperan los identificadores de los grupos de tarifas seleccionados
    var ids_grupos_tarifas = [];
    $("#ids_grupos_tarifas_recalculo_datos option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_grupos_tarifas.push($(this).val());
        }
    });

    // Comprobación de tarifas y grupos seleccionados
    if ((ids_tarifas.length == 0) && (ids_grupos_tarifas.length == 0)) {
		jAlert(TLNT.Idiomas._("Seleccione al menos una tarifa o grupo de tarifas"));
        return;
	}

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("Los datos posteriores a la fecha seleccionada se recalcularán con la información actual de las tarifas seleccionadas. ¿Está seguro?"), TLNT.Idiomas._("Pregunta"), function(res) {
        if (res == true) {
            var fecha_hora = fecha + ", " + "00:00:00";
            $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/guarda_fecha_recalculo_datos.php", {
                medicion: medicion,
                ids_tarifas: ids_tarifas,
                ids_grupos_tarifas: ids_grupos_tarifas,
                fecha_hora: fecha_hora
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
            });
        }
	});
}


function boton_smartmeter_mostrar_ventana_asignacion_tarifa_grupo_tarifas_sensores() {
    boton_smartmeter_mostrar_ventana_asignacion_tarifa_grupo_tarifas_sensores_general(ID_NINGUNO, ID_NINGUNO);
}


function boton_smartmeter_mostrar_ventana_asignacion_tarifa_sensores() {
    var params = this.id.split('__');
	var id_tarifa = params[1];

    boton_smartmeter_mostrar_ventana_asignacion_tarifa_grupo_tarifas_sensores_general(id_tarifa, ID_NINGUNO);
}


function actualiza_tabla_tarifas() {
    switch (medicion) {
        case MEDICION_ELECTRICIDAD: {
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    actualiza_tabla_tarifas_electricidad_Espanya();
                    break;
                }
            }
            break;
        }
    }
}


function boton_smartmeter_refrescar_tabla_tarifa() {
    var params = this.id.split('__');
	var id_tarifa = params[1];

    actualiza_tabla_tarifa_detalles(id_tarifa);
}


function actualiza_tabla_tarifa_detalles(id_tarifa) {
    // Se actualiza la información de la fila de la tabla
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/dame_informacion_fila_tabla_tarifa.php", {
        medicion: medicion,
        id_tarifa: id_tarifa
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_datos = resultado.id_datos;
        var fila = resultado.fila;

        $("#fila_" + id_datos).html(fila);

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();

        // Se actualiza la información detallada (si está visible)
        var detalles_tabla_visibles = dame_elemento_visible(id_datos + " .detalle-tabla-datos");
        if (detalles_tabla_visibles == true) {
            $.post("./comun/src/lib/modulos/dame_detalles_tabla.php", {
                id_datos: id_datos
            },
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                $('#' + id_datos + " .detalle-tabla-datos").html(resultado.html);

                // Establecimiento de eventos
                TLNT.Navegacion.establece_eventos_tablas_datos();
                TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
                TLNT.Navegacion.establece_eventos_detalles_tablas_datos();

                // Acciones 'extra' a realizar en los detalles de la tabla de datos
                TLNT.Navegacion.realiza_acciones_mostrado_detalle_tabla_datos(resultado);
            });
        }
	});
}


//
// Funciones de conceptos adicionales de tarifas
//


function boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_adicional_factura_tarifa(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_tarifa = params[1];
	var id_concepto_adicional = params[2];

	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/muestra_ventana_anyadir_modificar_concepto_adicional_factura_tarifa.php", {
        medicion: medicion,
        id_tarifa: id_tarifa,
        id_concepto_adicional: id_concepto_adicional
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


function boton_smartmeter_eliminar_concepto_adicional_factura_tarifa(event) {
    TLNT.Navegacion.detiene_propagacion_evento(event);

	var params = this.id.split('__');
	var id_tarifa = params[1];
	var id_concepto_adicional = params[2];

	jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el concepto adicional de factura?"), TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true)
		{
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/elimina_concepto_adicional_factura_tarifa.php", {
                medicion: medicion,
                id_tarifa: id_tarifa,
                id_concepto_adicional: id_concepto_adicional
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

				jInfo(resultado.msg);
                actualiza_tabla_conceptos_adicionales_factura_tarifa(id_tarifa);
			});
		}
	});
}


function boton_smartmeter_anyadir_modificar_concepto_adicional_factura_tarifa() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_concepto_adicional = $("#parametros_ventana_anyadir_modificar_concepto_adicional_factura_tarifa").attr("anyadir_concepto_adicional");
    var id_tarifa = $("#parametros_ventana_anyadir_modificar_concepto_adicional_factura_tarifa").attr("id_tarifa");
    var id_concepto_adicional = $("#parametros_ventana_anyadir_modificar_concepto_adicional_factura_tarifa").attr("id_concepto_adicional");

    // Nombre
    var nombre = $("#nombre_concepto_adicional_factura_tarifa").val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_concepto_adicional_factura_tarifa").addClass('data-check-failed');
        return;
    }

    // Tipo
    var tipo = $("#tipo_concepto_adicional_factura_tarifa").val();

    // Coste (y límites de consumo)
    var coste = $("#coste_concepto_adicional_factura_tarifa").val();
    var cadena_coste = null;
    var cadena_limites_consumo_tramos = null;
    switch (tipo) {
        case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_FIJO:
        case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_DIARIO: {
            if (PATRON_NUMERO_REAL.test(coste) == false) {
                jAlert(TLNT.Idiomas._('El coste debe ser numérico'));
                return;
            }
            cadena_coste = coste;
            break;
        }
        case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_ABSOLUTO:
        case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO: {
            var cadena_costes_consumo_tramos = elimina_espacios(coste);
            var cadena_limites_consumo_tramos = elimina_espacios($('#limites_consumo_tramos_concepto_adicional_factura_tarifa').val());
            var costes_consumo_tramos = cadena_costes_consumo_tramos.split(",");
            var limites_consumo_tramos = null;
            if (cadena_limites_consumo_tramos == "") {
                limites_consumo_tramos = [];
            }
            else {
                limites_consumo_tramos = cadena_limites_consumo_tramos.split(",");
            }
            if (costes_consumo_tramos > NUMERO_MAXIMO_TRAMOS_CONCEPTOS_ADICIONALES_CONSUMO_FACTURA_TARIFA) {
                var descripcion_error = TLNT.Idiomas._('El número de tramos de consumo supera el máximo permitido') +
                    " (" + NUMERO_MAXIMO_TRAMOS_CONCEPTOS_ADICIONALES_CONSUMO_FACTURA_TARIFA + ")";
                jAlert(descripcion_error);
                return;
            }
            if (costes_consumo_tramos.length != (limites_consumo_tramos.length + 1)) {
                var descripcion_error = TLNT.Idiomas._('Los números de límites y de precios de consumo por tramo son incorrectos');
                jAlert(descripcion_error);
                return;
            }
            for (var i = 0; i < costes_consumo_tramos.length; i++) {
                var coste_consumo_tramo = costes_consumo_tramos[i];
                if (PATRON_NUMERO_REAL.test(coste_consumo_tramo) == false) {
                    jAlert(TLNT.Idiomas._('Los precios de consumo por tramo deben ser numéricos'));
                    return;
                }
            }
            for (var i = 0; i < limites_consumo_tramos.length; i++) {
                var limite_consumo_tramo = limites_consumo_tramos[i];
                if (PATRON_NUMERO_REAL.test(limite_consumo_tramo) == false) {
                    jAlert(TLNT.Idiomas._('Los límites de consumo por tramo deben ser numéricos'));
                    return;
                }
            }
            cadena_coste = costes_consumo_tramos.join(SEPARADOR_PARAMETROS_SIMPLES);
            cadena_limites_consumo_tramos = limites_consumo_tramos.join(SEPARADOR_PARAMETROS_SIMPLES);
            break;
        }
    }

    // Impuesto
    var impuesto = $("#impuesto_concepto_adicional_factura_tarifa").val();

    // Se añade o modifica el concepto adicional
    if (anyadir_concepto_adicional == true) {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/anyade_concepto_adicional_factura_tarifa.php", {
            medicion: medicion,
            id_tarifa: id_tarifa,
			nombre: nombre,
            tipo: tipo,
            coste: cadena_coste,
            limites_consumo_tramos: cadena_limites_consumo_tramos,
            impuesto: impuesto
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_conceptos_adicionales_factura_tarifa(id_tarifa);
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/modifica_concepto_adicional_factura_tarifa.php", {
            medicion: medicion,
            id_concepto_adicional: id_concepto_adicional,
            id_tarifa: id_tarifa,
			nombre: nombre,
            tipo: tipo,
            coste: cadena_coste,
            limites_consumo_tramos: cadena_limites_consumo_tramos,
            impuesto: impuesto
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_tabla_conceptos_adicionales_factura_tarifa(id_tarifa);
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_smartmeter_actualizar_tabla_conceptos_adicionales_factura_tarifa() {
    var params = this.id.split('__');
    var id_tarifa = params[1];

    actualiza_tabla_conceptos_adicionales_factura_tarifa(id_tarifa);
}


function actualiza_tabla_conceptos_adicionales_factura_tarifa(id_tarifa) {
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/dame_tabla_conceptos_adicionales_factura_tarifa.php", {
        medicion: medicion,
		id_tarifa: id_tarifa
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_conceptos_adicionales_factura_tarifa = "conceptos-adicionales-factura-tarifa" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_tarifa;
        $('#' + id_elemento_conceptos_adicionales_factura_tarifa).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


//
// Eventos de grupos de tarifas
//


function boton_smartmeter_mostrar_ventana_asignacion_grupo_tarifas_sensores() {
    var params = this.id.split('__');
	var id_grupo_tarifas = params[1];

    boton_smartmeter_mostrar_ventana_asignacion_tarifa_grupo_tarifas_sensores_general(ID_NINGUNO, id_grupo_tarifas);
}


function boton_smartmeter_filtro_grupos_tarifas_tabla() {
    boton_smartmeter_actualizar_tabla_grupos_tarifas();
}


function boton_smartmeter_mostrar_ventana_anyadir_modificar_grupo_tarifas(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_grupo_tarifas = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/muestra_ventana_anyadir_modificar_grupo_tarifas.php", {
        medicion: medicion,
		id_grupo_tarifas: id_grupo_tarifas
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


function boton_smartmeter_eliminar_grupo_tarifas(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
    var id_grupo_tarifas = params[1];
    var nombre_grupo_tarifas = $(this).attr('nombre_grupo_tarifas');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el grupo?") + "\n(" + escapeHtml(nombre_grupo_tarifas) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/elimina_grupo_tarifas.php", {
                medicion: medicion,
				id_grupo_tarifas: id_grupo_tarifas
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
                actualiza_lista_grupos_tarifas_filtro();
				actualiza_tabla_grupos_tarifas();
			});
		}
	});
}


function boton_smartmeter_anyadir_modificar_grupo_tarifas() {
    if (TLNT.Check.inputs('contenido_modal')) {
		jAlert(TLNT.Idiomas._('Por favor, compruebe que los datos son correctos'));
		return;
	}

    // Parámetros de la ventana
    var anyadir_grupo_tarifas = $("#parametros_ventana_anyadir_modificar_grupo_tarifas").attr("anyadir_grupo_tarifas");
    var id_grupo_tarifas = $("#parametros_ventana_anyadir_modificar_grupo_tarifas").attr("id_grupo_tarifas");

    // Nombre y descripción
    var nombre = $('#nombre_grupo_tarifas').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_grupo_tarifas").addClass('data-check-failed');
        return;
    }
    var descripcion = $('#descripcion_grupo_tarifas').val();
    if (comprueba_longitud_cadena(descripcion, NUMERO_MAXIMO_CARACTERES_DESCRIPCION) == false) {
        $("#descripcion_grupo_tarifas").addClass('data-check-failed');
        return;
    }

    // Se añade o modifica el grupo de tarifas
    if (anyadir_grupo_tarifas == true) {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/anyade_grupo_tarifas.php", {
            medicion: medicion,
            nombre: nombre,
            descripcion: descripcion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_lista_grupos_tarifas_filtro();
            actualiza_tabla_grupos_tarifas();
        });
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/modifica_grupo_tarifas.php", {
            medicion: medicion,
            id_grupo_tarifas: id_grupo_tarifas,
            nombre: nombre,
            descripcion: descripcion
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            jInfo(resultado.msg);
            actualiza_lista_grupos_tarifas_filtro();
            actualiza_tabla_grupos_tarifas();
            switch (medicion) {
                case MEDICION_ELECTRICIDAD: {
                    switch (pais_tarifas_electricas) {
                        case PAIS_ESPANYA: {
                            actualiza_tabla_tarifas_electricidad_Espanya();
                            break;
                        }
                    }
                    break;
                }
                case MEDICION_GAS: {
                    switch (pais_tarifas_gas) {
                        case PAIS_ESPANYA: {
                            actualiza_tabla_tarifas_gas_Espanya();
                            break;
                        }
                    }
                    break;
                }
                case MEDICION_AGUA: {
                    switch (pais_tarifas_agua) {
                        case PAIS_ESPANYA: {
                            actualiza_tabla_tarifas_agua_Espanya();
                            break;
                        }
                    }
                    break;
                }
            }
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_smartmeter_actualizar_tabla_grupos_tarifas() {
    actualiza_tabla_grupos_tarifas();
}


function actualiza_tabla_grupos_tarifas() {
    var filtro = $('#filtro_smartmeter_filtro_grupos_tarifas_tabla').val();
    var estado = $('#estado_tarifa_smartmeter_filtro_grupos_tarifas_tabla').val();

	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/dame_tabla_grupos_tarifas.php", {
        medicion: medicion,
	    filtro: filtro,
        estado: estado
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $('#tablaGruposTarifas').html(resultado.html);

        // Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


function boton_smartmeter_refrescar_tabla_grupo_tarifas() {
    var params = this.id.split('__');
	var id_grupo_tarifas = params[1];

    actualiza_tabla_grupo_tarifas_detalles(id_grupo_tarifas);
}


function actualiza_tabla_grupo_tarifas_detalles(id_grupo_tarifas) {
    // Se actualiza la información de la fila de la tabla
	$.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/dame_informacion_fila_tabla_grupo_tarifas.php", {
        medicion: medicion,
        id_grupo_tarifas: id_grupo_tarifas
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_datos = "datosGrupoTarifas__" + medicion + "__" + id_grupo_tarifas;
        $("#fila_" + id_datos).html(resultado.fila);

        // Establecimiento de eventos
        TLNT.Navegacion.establece_eventos_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();

        // Se actualiza la información detallada (si está visible)
        var detalles_tabla_visibles = dame_elemento_visible(id_datos + " .detalle-tabla-datos");
        if (detalles_tabla_visibles == true) {
            $.post("./comun/src/lib/modulos/dame_detalles_tabla.php", {
                id_datos: id_datos
            },
            function(data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                $('#' + id_datos + " .detalle-tabla-datos").html(resultado.html);

                // Establecimiento de eventos
                TLNT.Navegacion.establece_eventos_tablas_datos();
                TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
                TLNT.Navegacion.establece_eventos_detalles_tablas_datos();

                // Acciones 'extra' a realizar en los detalles de la tabla de datos
                TLNT.Navegacion.realiza_acciones_mostrado_detalle_tabla_datos(resultado);
            });
        }
	});
}


function actualiza_lista_grupos_tarifas_filtro() {
	var id_lista = "id_grupo_tarifas_smartmeter_filtro_tarifas_tabla";
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/dame_lista_grupos_tarifas.php", {
        medicion: medicion,
        id_grupo_seleccionado: $("#" + id_lista).val(),
        opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#" + id_lista).html(resultado.html);
    });
}


//
// Eventos comunes a tarifas y grupos de tarifas
//


function boton_smartmeter_mostrar_ventana_asignacion_tarifa_grupo_tarifas_sensores_general(id_tarifa, id_grupo_tarifas) {
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/muestra_ventana_asignacion_tarifa_grupo_tarifas_sensores.php", {
        medicion: medicion,
        id_tarifa: id_tarifa,
        id_grupo_tarifas: id_grupo_tarifas
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


function boton_smartmeter_asignar_tarifa_grupo_tarifas_sensores() {
    // Se recuperan la tarifa y el grupo de tarifas
    var id_tarifa = $('#id_tarifa_asignacion_tarifa_grupo_tarifas_sensores').val();
    var id_grupo_tarifas = $('#id_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores').val();
    if ((id_tarifa == ID_NINGUNO) && (id_grupo_tarifas == ID_NINGUNO)) {
        jAlert(TLNT.Idiomas._("No hay tarifa ni grupo de tarifas seleccionado"));
        return;
    }

    // Se recuperan los identificadores de los sensores seleccionados
    var ids_sensores = [];
    $("#ids_sensores_asignacion_tarifa_grupo_tarifas_sensores option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores.push($(this).val());
        }
    });
    if (ids_sensores.length == 0) {
		jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
        return;
	}

    // Se asigna la tarifa o el grupo de tarifas a los sensores especificados
    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/asigna_tarifa_grupo_tarifas_sensores.php", {
        medicion: medicion,
        id_tarifa: id_tarifa,
        id_grupo_tarifas: id_grupo_tarifas,
        ids_sensores: ids_sensores
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
        actualiza_tabla_tarifas();
        actualiza_tabla_grupos_tarifas();
        $('#ventana_modal').modal('hide');
    });
}
